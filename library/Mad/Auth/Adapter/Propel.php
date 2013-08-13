<?php

/**
 * Mad library for Zend Framework
 * 
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://devcomm.org.ua/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@devcomm.org.ua so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2011 Devcomm (http://www.devcomm.org.ua)
 * @license    http://devcomm.org.ua/license/new-bsd     New BSD License
 * @version    $Id: $
 */

/**
 * Auth adapter with storage in db using propel
 *
 * @category   Mad
 * @package    Auth
 * @author     madman <madman@devcomm.org.ua>
 */
class Mad_Auth_Adapter_Propel implements Zend_Auth_Adapter_Interface {

    /**
     * Sets username and password for authentication
     * 
     * @param string $username
     * @param string $password 
     * @return void
     */
    public function __construct($username, $password) {
        // TODO:
    }

    /**
     * Performs an authentication attempt
     * 
     * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
     * @return Zend_Auth_Result 
     */
    public function authenticate() {
        $code = Zend_Auth_Result::SUCCESS;
        $identy = null;
        $messages = array();

        try {
            // TODO: will implement feature
            $code = Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
            $messages[] = 'Користувача не знайдено';
            throw new Mad_Exception();
 
            $identy = $this->_username;
        } catch (Mad_Exception $e) {
            
        }

        return new Zend_Auth_Result($code, $identy, $messages);
    }

}