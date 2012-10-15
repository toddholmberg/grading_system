<?php

class Cop_Plugin_ACL extends Zend_Controller_Plugin_Abstract
{

	protected $_defaultRole = 'Guest';
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		$auth = Zend_Auth::getInstance();
		$acl = new Cop_Acl();
		$copsession = new Zend_Session_Namespace('copsession');
		//Zend_Debug::dump($auth);
		//Zend_Debug::dump($auth->getIdentity());
		//Zend_Debug::dump($copsession);
		//Zend_Debug::dump($acl);
		error_log(print_r($_SESSION, true));
		
		if($auth->hasIdentity() && !empty($auth->getIdentity()->role_title)) {
	
			$user = $auth->getIdentity();
			//Zend_Debug::dump($user);
			
			if(empty($user->role_title)) {
				$user->role_title = $this->_defaultRole;
			}
			
			if(!$acl->isAllowed($user->role_title, $request->getControllerName() . '::' . $request->getActionName())) {
				
				$copsession->destination_url = $request->getPathInfo();

				return Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')->setGotoUrl('auth/noauth');

			}

		} else {

			if(!$acl->isAllowed($this->_defaultRole, $request->getControllerName() . '::' . $request->getActionName())) {

				$copsession->destination_url = $request->getPathInfo();

				return Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')->setGotoUrl('http://www.pharmacy.utah.edu/');

			}

		}

	}
}
