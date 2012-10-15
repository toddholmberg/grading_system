<?php

class Cop_Auth_Adapter implements Zend_Auth_Adapter_Interface
{

	public function authenticate()
	{
		try {
			$code = 'u9090909'; // admin
			//$code = 'u07687676'; // faculty
			//$code = 'u0011967'; // student
			//$code = false;
			$result = new Zend_Auth_Result(1, $code);
			return $result;
		} catch (Exception $e) {
			throw new Zend_Auth_Adapter_Exception($e->getMessage());	
		}

	}
}
