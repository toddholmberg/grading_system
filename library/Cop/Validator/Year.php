<?php

/**
 * Checks if academic year exists
 */
class Cop_Validator_Year extends Zend_Validate_Abstract
{

	const YEAR = 'year';

	protected $_messageTemplates = array(
		self::YEAR => "'%value%' already exists"
	);

	public function isValid($value)
	{
		$this->_setValue($value);

		$academicYearMapper = new Application_Model_AcademicYearMapper();
		$academicYear = $academicYearMapper->findAcademicYear($value);
		error_log(print_r($academicYear, true));
		if(!empty($academicYear)) {
			$this->_error(self::YEAR);
			return false;
		}
		
		return true;

	}

}
