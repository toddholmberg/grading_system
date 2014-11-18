<?php

class AttendanceController extends Zend_Controller_Action
{

    public function init()
    {
		$this->_config = Zend_Registry::get('config');
        $this->view->identity = Zend_Auth::getInstance()->getIdentity();
        $this->view->currentAcademicYear = $this->_helper->currentAcademicYear();
    }

    public function indexAction()
    {
        // action body
    }


}

