<?php

class AuthController extends Zend_Controller_Action
{

	private $_db;
	private $_casConfig;

	public function init()
	{
		/* Initialize action controller here */
		$this->_casConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/cas.ini');
		$this->_db = Zend_Db_Table::getDefaultAdapter();
	}

	public function indexAction()
	{
		// action body
	}

	public function loginAction()
	{
		/*
		   Application requires 2 levels of navigation: 
		   1.	Successful auth with Ubniv. of Utah CAS.
		   2.	Auth with grading system
		 */

		// redirect to CAS form
		// CAS returns a unid on sucessful login 
		$adapter = new Zend_Auth_Adapter_Cas($this->_casConfig->cas->toArray());
		//$adapter = new Cop_Auth_Adapter(); // testing adapter
		$auth    = Zend_Auth::getInstance();
		$result  = $auth->authenticate($adapter);
		

		if (! $result->isValid()) {
			$this->_redirect($adapter->getLoginUrl());
		} 

		if($auth->hasIdentity()) {
			// process unid
			// 2nd auth level
	
			$unid = $auth->getIdentity();

			$dbAuth = Zend_Auth::getInstance();

			$dbAuthAdapter = new Zend_Auth_Adapter_DbTable($this->_db, 'userAuthData', 'unid', 'unid', 'AND (role_title = "Faculty" OR role_title = "Admin")');

			$dbAuthAdapter->setIdentity($unid)->setCredential($unid);

			$dbAuthResult = $dbAuth->authenticate($dbAuthAdapter);

			if($dbAuthResult->isValid()) {	

				$storage = new Zend_Auth_Storage_Session();

				$result = $dbAuthAdapter->getResultRowObject(array(
							'unid',
							'first_name',
							'last_name',
							'email',
							'role_id',
							'role_title'
							));

				$storage->write($result);

				$copsession = new Zend_Session_Namespace('copsession');

				if(isset($copsession->destination_url)) {
					$url = $copsession->destination_url;
					unset($copsession->destination_url);
					$this->_redirect($url);
				}

				$this->_redirect('index/index');

			} else {

				$this->view->errorMessage = "Your UNID is not registered with this system.";
			}
		}
	}

    public function logoutAction()
    {
		
		$storage = new Zend_Auth_Storage_Session();
		$storage->clear();
		$this->killSession('Zend_Auth');
		$this->killSession('copsession');
		$this->_redirect('index/index');
    }

    public function noauthAction()
    {
        // action body
    }

	public function killSession($sessionName)
	{
		if($this->isStarted())
		{
			if($this->sessionExists())
			{
				$this->namespaceUnset($sessionName);
				$this->forgetMe();
			}
		}

	}


}







