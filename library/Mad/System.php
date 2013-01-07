<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * System class for creation multiapps
 * from Zend_Application
 *
 * @author madman
 */
class Mad_System {

    /**
     * System environment
     *
     * @var string
     */
    protected $_environment;

    /**
     * Name of application for runnig
     * 
     * @var string
     */
    protected $_appname;

    /**
     * Flattened (lowercase) option keys
     *
     * @var array
     */
    protected $_optionKeys = array();

    /*
     * Це лише обгортка на Zend_Application для загрузки і об"єднання 
     * конфігураційних файлів, також кешування файлів.
     * Конфігурація розділяється на загальносистемну (база даних, сесії,
     * кешування) і окремо для кожного apps. 
     * 
     */

    public function __construct($appname, $environment, $options = null) {
        $this->_appname = (string) $appname;
        $this->_environment = (string) $environment;

        require_once 'Zend/Loader/Autoloader.php';
        $this->_autoloader = Zend_Loader_Autoloader::getInstance();
        if (null !== $options) {
            if (is_string($options)) {
                $options = $this->_loadConfig($options);
            } elseif ($options instanceof Zend_Config) {
                $options = $options->toArray();
            } elseif (!is_array($options)) {
                throw new Zend_Application_Exception('Invalid options provided; must be location of config file, a config object, or an array');
            }
            $this->setOptions($options);
        }
    }

    /**
     * Run application 
     */
    public function run() {
        $options = $this->getApplicationOptions();
        $application = new Zend_Application($this->_environment, $options);
        $application->bootstrap()->run();
    }

    /**
     *
     * @return mixed
     */
    public function getApplicationOptions() {
        $options = array();

        $options = $this->mergeOptions($options, array(
            'resources' => array(
                'frontController' => array(
                    'moduledirectory' => $this->getOption('appsbasepath') . DIRECTORY_SEPARATOR . $this->getAppName() . DIRECTORY_SEPARATOR . 'modules'
                )
            )
        ));
        
        // set constant fro appliacation configs
        defined('APPLICATION_PATH') || define('APPLICATION_PATH', $this->getOption('appsbasepath'). DIRECTORY_SEPARATOR . $this->getAppName());
        
        // merge with system options
        $options = $this->mergeOptions($options, $this->getOptions());

        // load application options and merge with system options
        $appConfigFile = $this->getOption('appsbasepath') . DIRECTORY_SEPARATOR . $this->getAppName() . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'application.ini';
        if (file_exists($appConfigFile)) {
            $options = $this->mergeOptions($options, $this->_loadConfig($appConfigFile));
        }

        // TODO: remove "white-list" feature
        $appOptions = array();
        foreach ($options as $key => &$option) {
            if (in_array($key, array(
                        'phpsettings',
                        'includepaths',
                        'pluginpaths',
                        'autoloadernamespaces',
                        'autoloaderzfpath',
                        'bootstrap',
                        'resourceloader',
                        'resources',
                    ))) {
                $appOptions[$key] = $option;
            }
        }

        return $appOptions;
    }

    /**
     * Retrieve current environment
     *
     * @return string
     */
    public function getEnvironment() {
        return $this->_environment;
    }

    /**
     * Retrieve current application name
     * 
     * @return string
     */
    public function getAppName() {
        return $this->_appname;
    }

    /**
     * Set system options
     *
     * @param  array $options
     * @return Mad_System
     */
    public function setOptions(array $options) {
        if (!empty($options['config'])) {
            if (is_array($options['config'])) {
                $_options = array();
                foreach ($options['config'] as $tmp) {
                    $_options = $this->mergeOptions($_options, $this->_loadConfig($tmp));
                }
                $options = $this->mergeOptions($_options, $options);
            } else {
                $options = $this->mergeOptions($this->_loadConfig($options['config']), $options);
            }
        }

        $this->_options = $options;

        $options = array_change_key_case($options, CASE_LOWER);

        $this->_optionKeys = array_keys($options);
    }

    /**
     * Retrieve application options (for caching)
     *
     * @return array
     */
    public function getOptions() {
        return $this->_options;
    }

    /**
     * Is an option present?
     *
     * @param  string $key
     * @return bool
     */
    public function hasOption($key) {
        return in_array(strtolower($key), $this->_optionKeys);
    }

    /**
     * Retrieve a single option
     *
     * @param  string $key
     * @return mixed
     */
    public function getOption($key) {
        if ($this->hasOption($key)) {
            $options = $this->getOptions();
            $options = array_change_key_case($options, CASE_LOWER);
            return $options[strtolower($key)];
        }
        return null;
    }

    /**
     * Merge options recursively
     *
     * @param  array $array1
     * @param  mixed $array2
     * @return array
     */
    public function mergeOptions(array $array1, $array2 = null) {
        if (is_array($array2)) {
            foreach ($array2 as $key => $val) {
                if (is_array($array2[$key])) {
                    $array1[$key] = (array_key_exists($key, $array1) && is_array($array1[$key])) ? $this->mergeOptions($array1[$key], $array2[$key]) : $array2[$key];
                } else {
                    $array1[$key] = $val;
                }
            }
        }
        return $array1;
    }

    /**
     * Load configuration file of options
     * 
     * @param  string $file
     * @throws Mad_Exception When invalid configuration file is provided 
     * @return array
     */
    protected function _loadConfig($file) {
        $environment = $this->getEnvironment();
        $suffix = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        switch ($suffix) {
            case 'ini':
                $config = new Zend_Config_Ini($file, $environment);
                break;

            case 'xml':
                $config = new Zend_Config_Xml($file, $environment);
                break;

            case 'php':
            case 'inc':
                $config = include $file;
                if (!is_array($config)) {
                    throw new Mad_Exception('Invalid configuration file provided; PHP file does not return array value');
                }
                return $config;
                break;

            default:
                throw new Mad_Exception('Invalid configuration file provided; unknown config type');
        }

        return $config->toArray();
    }

}
