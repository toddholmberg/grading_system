<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	protected function _initDoctype()
	{
		$this->bootstrap('view');
		$view = $this->getResource('view');
		$view->doctype('HTML5');
	}

	protected function _initConfig()
	{

		$config = new Zend_Config($this->getOptions());

		Zend_Registry::set('config', $config);

		return $config;

	}

	protected function _initDatabaseRegistry()
	{

		$this->bootstrap("db");

		$db = $this->getResource("db");

		Zend_Registry::set('db', $db);

		return $db;

	}


}

