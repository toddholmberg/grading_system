<?php

class Zend_View_Helper_CurrentAcademicYear extends Zend_View_Helper_Abstract
{
	public function currentAcademicYear()
	{
		$academicYears = new Application_Model_DbTable_AcademicYears();
		$currentAcademicYear = $academicYears->getCurrentAcademicYear();
		return $currentAcademicYear;
	}
}
