<?php

class Application_Controller_Action_Helper_CurrentAcademicYear extends Zend_Controller_Action_Helper_Abstract
{
	public function direct()
	{
		$academicYears = new Application_Model_DbTable_AcademicYears();
		return $academicYears->getCurrentAcademicYear();
	}

}