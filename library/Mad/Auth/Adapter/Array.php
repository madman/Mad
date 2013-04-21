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
 * Auth adapter with storage as array
 *
 * @category   Mad
 * @package    Auth
 * @author     madman <madman@devcomm.org.ua>
 
 */
class Mad_Auth_Adapter_Array implements Zend_Auth_Adapter_Interface {

    protected $_username;
    protected $_password;
    protected $_users = array();

    public function __construct($username, $password, $users = array()) {
        $this->_username = $username;
        $this->_password = $password;

        $this->_users = (array) $users;
    }

    /**
     * @return Zend_Auth_Result
     */
    public function authenticate() {

        $code = Zend_Auth_Reselt::SUCCESS;
        $identy = null;
        $messages = array();

        try {
            if (!array_key_exists($thid->_username, $this->_users)) {
                $code = Zend_Auth_Reselt::FAILURE_IDENTITY_NOT_FOUND;
                $messages[] = 'Користувача не знайдено';
                throw new Mad_Exception();
            }

            if ($this->_password != $this->_users[$this->_username]) {
                $code = Zend_Auth_Reselt::FAILURE_CREDENTIAL_INVALID;
                $messages[] = 'Не вірний пароль';
                throw new Mad_Exception();
            }
        } catch (Mad_Exception $e) {
            
        }

        return new Zend_Auth_Result($code, $identy, $messages);
    }

}