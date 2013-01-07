<?php
/**
 * Mad Library for Zend Framework
 */

/**
 * Resource for initializing the Propel ORM
 *
 * @author madman
 */
class Mad_Application_Resource_Propel extends Zend_Application_Resource_ResourceAbstract {

    protected $_propel;

    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return Propel
     */
    public function init() {
        return $this->getPropel();
    }

    public function getPropel() {
        $options = $this->getOptions();

        require_once 'propel/Propel.php';
        Propel::init($options['options']);

        // so we can get the connection from the registry easily
        return Propel::getConnection();
    }

}