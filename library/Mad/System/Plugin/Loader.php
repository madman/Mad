<?php

/**
 * @package Mad
 * @subpackage System
 * @author madman <madman@devcomm.org.ua>
 */

/**
 * Plugin for set resources autoloads
 *
 * @author madman
 */
class Mad_System_Plugin_Loader extends Zend_Controller_Plugin_Abstract {

    /**
     * Set up autoloader
     * 
     * @param Zend_Controller_Request_Abstract $request 
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request) {

        $front = Zend_Controller_Front::getInstance();

        if (null !== $request) {
            $module = $request->getModuleName();
        }
        if (empty($module)) {
            $module = $front->getDispatcher()->getDefaultModule();
        }

        $controllerDir = $front->getControllerDirectory($module);

        if (is_string($controllerDir)) {
            $moduledir = dirname($controllerDir);

            $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
                        'basePath' => $moduledir,
                        'namespace' => ucfirst($module),
                    ));

            $resourceLoader->addResourceTypes(array(
                'form' => array(
                    'path' => 'forms/',
                    'namespace' => 'Form',
                ),
            ));
        }
        
     }

}