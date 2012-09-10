<?php

class Zend_View_Helper_Section extends Zend_View_Helper_Abstract
{
	private $_sectionMapper;

	public function section($data = null)
	{
		$this->_sectionMapper = new Application_Model_SectionMapper();	
		return $this;
	}

	public function buildSections($academicYearId)
	{
		$sectionMapper = new Application_Model_SectionMapper();
		$sections = $sectionMapper->buildSections($academicYearId);
		return $sections;
	
	}

	public function getSectionsByAcademicYearId($academicYearId)
	{
		return $this->_sectionMapper->getSectionsByAcademicYearId($academicYearId);
	}

}
