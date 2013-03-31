<?php

/**
 * @package Mad
 * @subpackage System
 * @author madman <madman@devcomm.org.ua>
 */

/**
 * Auth Plugin for Controller
 *
 * @author madman
 */
class Mad_Controller_Plugin_Auth extends Zend_Controller_Plugin_Abstract {

    protected $_module = 'default';
    protected $_controller = 'auth';
    protected $_action = 'login';

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $module = strtolower($request->getModuleName());
        $controller = strtolower($request->getControllerName());
        $action = strtolower($request->getActionName());

        if (
                $this->_module == $module
                && $this->_controller == $controller
                && $this->_action == $action
        ) {
            return;
        }

        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            return;
        }
        
       if ($request->isGet()) {
            $storage = new Zend_Session_Namespace('Auth');
            $storage->referer = $request->getPathInfo();
        }
 
        $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
        $redirector->gotoSimple($this->_action, $this->_controller, $this->_module);
    }

}