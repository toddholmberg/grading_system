<?php

class Cop_Acl extends Zend_Acl
{
	public function __construct()
	{
		// Guest is the unauthenticated state
		$this->addRole(new Zend_Acl_Role('Guest'));

		// Student access matches guest
		$this->addRole(new Zend_Acl_Role('Student'), 'Guest');
		// Faculty is the base authenticated access level
		$this->addRole(new Zend_Acl_Role('Faculty'));
		// admin inherits all Faculty access
		$this->addRole(new Zend_Acl_Role('Admin'), 'Faculty');

		// Add some resources in the form controller::action
		$this->add(new Zend_Acl_Resource('index::index'));
		$this->add(new Zend_Acl_Resource('error::error'));
		$this->add(new Zend_Acl_Resource('auth::login'));
		$this->add(new Zend_Acl_Resource('auth::logout'));
		$this->add(new Zend_Acl_Resource('auth::noauth'));
		$this->add(new Zend_Acl_Resource('user::index'));
		$this->add(new Zend_Acl_Resource('grading::index'));
		$this->add(new Zend_Acl_Resource('grading::format-survey-detail'));
		$this->add(new Zend_Acl_Resource('grading::report'));
		$this->add(new Zend_Acl_Resource('grading::save-scores'));
		$this->add(new Zend_Acl_Resource('grading::download-report'));
		$this->add(new Zend_Acl_Resource('user::edit-user'));
		$this->add(new Zend_Acl_Resource('user::create-user'));
		$this->add(new Zend_Acl_Resource('upload::survey'));
		
		// Deny by default
		$this->deny();

		// Set guest access levels
		$this->allow('Guest', 'auth::login');
		$this->allow('Guest', 'auth::logout');
		$this->allow('Guest', 'auth::noauth');
		$this->allow('Faculty', 'auth::login');
        $this->allow('Faculty', 'index::index');
		$this->allow('Faculty', 'auth::logout');
		$this->allow('Faculty', 'auth::noauth');
		$this->allow('Faculty', 'error::error');
        $this->allow('Faculty', 'user::index');
        $this->allow('Faculty', 'grading::index');
        $this->allow('Faculty', 'grading::report');
        $this->allow('Faculty', 'grading::save-scores');
        $this->allow('Faculty', 'grading::download-report');
        $this->allow('Faculty', 'grading::format-survey-detail');

		// Set admin access
        $this->allow('Admin', 'user::edit-user');
        $this->allow('Admin', 'user::create-user');
        $this->allow('Admin', 'upload::survey');
		
	}
}
