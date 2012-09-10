<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	protected function _initDoctype()
	{
		$this->bootstrap('view');
		$this->_view = $this->getResource('view');
		$this->_view->doctype('HTML5');
		$this->_view->headMeta()
			->setCharset('UTF-8')
			->appendName('viewport','width=device-width,initial-scale=1.0')
			->appendName('description','')
			->appendName('keywords','');
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

		$this->_db = $this->getResource("db");
		Zend_Registry::set('db', $this->_db);

		return $this->_db;

	}

}

