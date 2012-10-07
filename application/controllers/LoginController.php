<?php

class LoginController extends Zend_Controller_Action
{

    public function init()
    {
		$this->_config = Zend_Registry::get('config');
		$this->_dbh = Zend_Db_Table::getDefaultAdapter();
		
    }

    public function indexAction()
    {
		// redirect to CAS form
		//$options = new Zend_Config_Ini(APPLICATION_PATH . '/configs/cas.ini');
		//$adapter = new Zend_Auth_Adapter_Cas($options->cas->toArray());
 		$auth    = Zend_Auth::getInstance();
        //$result  = $auth->authenticate($adapter);

		//if (! $result->isValid()) {
		//	$this->_redirect($adapter->getLoginUrl());
		//} else {
			// process unid
			// 2nd auth level
			//$unid = 'u0615627';
			$unid = 'u07687676';
			$dbAuth = Zend_Auth::getInstance();
			$dbAuthAdapter = new Zend_Auth_Adapter_DbTable($this->_dbh, 'user', 'unid', 'unid');
			$dbAuthAdapter->setIdentity($unid)->setCredential($unid);
			$dbAuthResult = $auth->authenticate($dbAuthAdapter);
			Zend_Debug::dump($dbAuthResult);
			$authenticated = true;


		//}

		if($authenticated) {
			$this->_helper->redirector('index', 'index');
		}

    }

    public function preDispatch() 
	{
        if (Zend_Auth::getInstance()->hasIdentity()) {
            // If the user is logged in, we don't want to show the login form;
            // however, the logout action should still be available
            if ('logout' != $this->getRequest()->getActionName()) {
                $this->_helper->redirector('index', 'index');
            }
        } else {
            // If they aren't, they can't logout, so that action should 
            // redirect to the login form
            if ('logout' == $this->getRequest()->getActionName()) {
                $this->_helper->redirector('index');
            }
        }
    }

	public function logoutAction()
	{
		Zend_Auth::getInstance()->clearIdentity();
		$this->_helper->redirector('login'); // back to login page
	}
    
}

