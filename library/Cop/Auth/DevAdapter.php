<?php

class Cop_Auth_DevAdapter implements Zend_Auth_Adapter_Interface
{

	public function authenticate()
	{
		try {

			/**
			 * Add code(unid) of desired role to
			 * circumvent UofU CAS login durin testing 
			 */
			
			$code = 'u9999999'; // admin
			//$code = false;

			$result = new Zend_Auth_Result(1, $code);

			return $result;
		} catch (Exception $e) {
			throw new Zend_Auth_Adapter_Exception($e->getMessage());	
		}

	}
}
