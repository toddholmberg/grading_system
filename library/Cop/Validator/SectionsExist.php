<?php

/**
 * Checks if setions exist for submitted academic year.
 */
class Cop_Validator_SectionsExist extends Zend_Validate_Abstract
{

	const SECTIONS_EXIST = 'sectionsExist';

	protected $_messageTemplates = array(
		self::SECTIONS_EXIST => "Sections already exist for that academic year"
	);

	public function isValid($value)
	{
		$this->_setValue($value);

		$sectionMapper = new Application_Model_SectionMapper();
		$sections = $sectionMapper->getSectionsByAcademicYearId($value);

		if(!empty($sections)) {
			$this->_error(self::SECTIONS_EXIST);
			return false;
		}
		
		return true;

	}

}
