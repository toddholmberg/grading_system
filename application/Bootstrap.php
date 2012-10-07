<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	private $_view;

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

	protected function _initJQuery()
	{
		$this->_view->addHelperPath(
			'ZendX/JQuery/View/Helper', 
			'ZendX_JQuery_View_Helper'
		);
	}

	protected function _initRoutes()
	{
		$ctrl = Zend_Controller_Front::getInstance();
		$router = $ctrl->getRouter();
		$router->addRoute(
				'download-report',
				new Zend_Controller_Router_Route(
					'/grading/download-report/:filename',
					array('controller' => 'grading',
						'action' => 'download-report')
				)); 


	}
	
}

