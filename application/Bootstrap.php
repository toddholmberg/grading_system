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

	public function _initViewHelpers()
	{
		$this->bootstrap('layout');
		$this->_layout = $this->getResource('layout');
		$this->_view = $this->_layout->getView();

		/*$this->_view->addHelperPath('Zend/Dojo/View/Helper','Zend_Dojo_View_Helper');

		$this->_view->dojo()
			->enable()
			->setCdnBase(Zend_Dojo::CDN_BASE_GOOGLE)
			->setCdnVersion('1.5.0')
			->setCdnDojoPath(Zend_Dojo::CDN_DOJO_PATH_GOOGLE)
			->addStyleSheetModule('dijit.themes.claro')
			->useCdn();
		*/
	}


}

