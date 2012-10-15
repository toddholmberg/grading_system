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
		/*
		Application requires 2 levels of navigation: 
		1.	Successful auth with Ubniv. of Utah CAS.
		2.	Auth with grading system
		*/

		// redirect to CAS form
		//$options = new Zend_Config_Ini(APPLICATION_PATH . '/configs/cas.ini');
		//$adapter = new Zend_Auth_Adapter_Cas($options->cas->toArray());
 		$auth    = Zend_Auth::getInstance();
        //$result  = $auth->authenticate($adapter);
		// unid is returned form CAS if login is successful

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
		if($result->isValid()) {	
			$storage = new Zend_Auth_Storage_Session();
			$storage->write($dbAuthAdapter->getResultRowObject());
			$copsession = new Zend_Session_Namespace('copsession');
			if(isset($copsession->destination_url)) {
				$url = $copsession->destination_url;
				unset($copsession->destination_url);
				$this->_redirect($url);
				$this->_redirect('index/index');
			} else {
				$this->view->errorMessage = "Invalid email or password. Please try again.";
			}
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

