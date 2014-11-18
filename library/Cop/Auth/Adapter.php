<?php

class Cop_Auth_Adapter implements Zend_Auth_Adapter_Interface
{

	public function authenticate()
	{
		try {
			//$code = 'u9090909'; // admin
			//$code = 'u07687676'; // faculty
			//$code = 'u0011967'; // student
			//$code = false;

			// initialize phpCAS
			phpCAS::client( CAS_VERSION_2_0, 'ulogin.utah.edu', 443, 'cas', false ) ;

			// no SSL validation for the CAS server
			phpCAS::setNoCasServerValidation();

			// force CAS authentication
			phpCAS::forceAuthentication();

			// at this step, the user has been authenticated by the CAS server
			// and the user's login name can be read with phpCAS::getUser().
			$casUser = phpCAS::getUser()  ;

			//Zend_Debug::dump($casUser);

			$result = new Zend_Auth_Result(1, $casUser);

			return $result;
		} catch (Exception $e) {
			throw new Zend_Auth_Adapter_Exception($e->getMessage());	
		}

	}
}
