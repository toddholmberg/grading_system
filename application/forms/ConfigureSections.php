<?php

class Application_Form_ConfigureSections extends Zend_Form
{

	protected $_academicYearId = null;

    public function init()
    {

    	$academicYearMapper = new Application_Model_AcademicYearMapper();
    	$currentAcademicYear = $academicYearMapper->getCurrentAcademicYear();

    	if(empty($this->_academicYearId)) {
    		$this->_academicYearId = $currentAcademicYear['id'];
    	}

		$sectionFormElements = array();

		// Academic year
		// $academicYearTable = new Application_Model_DbTable_AcademicYears();
		// $academicYearPicker = new Zend_Form_Element_Select('academicYearId');
		// $academicYearPicker->setLabel('Configure sections for the current academic year:');
		// $academicYearPicker->setRequired(true)->addValidator('NotEmpty', true);
		// $academicYearPicker->addMultiOption('', 'Please select...');
		// $academicYearPicker->setValue($this->_academicYearId);

		// foreach($academicYearTable->getAcademicYears() as $academicYearItem) {
		// 		$academicYearPicker->addMultiOption($academicYearItem['id'], $academicYearItem['year']);
		// }

		// $sectionFormElements[] = $academicYearPicker;


    	// get section id from aps table
    	$sectionMapper = new Application_Model_SectionMapper();
    	$sectionData = $sectionMapper->getSectionsByAcademicYearId($this->_academicYearId);
        

    	// build section config form
        foreach ($sectionData as $key => $section) {

            $sectionConfigData = $sectionMapper->getSectionConfig($section['id']);
            
            $element = new Zend_Form_Element_Text($section['section_id']);
            $element->setLabel('Section ' . $section['section_id']);
            $element->class = 'section';

            $element->setRequired(true)
            ->addValidator('NotEmpty', true)
            ->addValidator(
                new Zend_Validate_Int()
                );
            
            if(!empty($sectionConfigData->attendance_count)) {
                $element->setValue($sectionConfigData->attendance_count);
            }

            $element->setBelongsTo('attendance');
            $sectionFormElements[] = $element;
        }

        // Hidden sectionMapIds element
        $sectionDataHidden = new Zend_Form_Element_Hidden('sectionData');
        // JSON encode the section ids
        $sectionDataHidden->setValue(json_encode($sectionData));

        $sectionDataHidden->setDisableLoadDefaultDecorators(true);
        $sectionDataHidden->addDecorator('ViewHelper');
        $sectionDataHidden->removeDecorator('DtDdWrapper');
        $sectionDataHidden->removeDecorator('HtmlTag');
        $sectionDataHidden->removeDecorator('Label');

        $sectionFormElements[] = $sectionDataHidden;

        // Hidden sectionMapIds element
        $academicYearHidden = new Zend_Form_Element_Hidden('acadmicYearId');
        // JSON encode the section ids
        $academicYearHidden->setValue($this->_academicYearId);

        $academicYearHidden->setDisableLoadDefaultDecorators(true);
        $academicYearHidden->addDecorator('ViewHelper');
        $academicYearHidden->removeDecorator('DtDdWrapper');
        $academicYearHidden->removeDecorator('HtmlTag');
        $academicYearHidden->removeDecorator('Label');

        $sectionFormElements[] = $academicYearHidden;


        // Submit button
        $submit = new Zend_Form_Element_Submit('Save');
        $submit->setLabel('Save');

        $sectionFormElements[] = $submit;

		$this->addElements($sectionFormElements);

	}

    public function setAcademicYearId($academicYearId)
    {
    	$this->_academicYearId = $academicYearId;
    }


}

