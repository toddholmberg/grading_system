<?php

class Application_Form_GetSectionConfiguration extends Zend_Form
{

	protected $_academicYearId = null;

    public function init()
    {

    	// get basic section data

    	// get section id from aps table
    	$sectionMapper = new Application_Model_SectionMapper();
    	$sectionData = $sectionMapper->getSectionsByAcademicYearId($this->_academicYearId);
        
        //error_log(print_r($sectionData, true));
        
        $sectionFormElements = array();

    	// build section config form
        foreach ($sectionData as $key => $section) {

            $sectionConfigData = $sectionMapper->getSectionConfig($section['id']);
            //error_log(print_r($sectionConfigData, true));
            $element = new Zend_Form_Element_Text($section['section_id']);
            $element->setLabel('Section ' . $section['section_id']);
            $element->class = 'section';
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

        $this->setAction('/seminars/section/save-section-configuration');

    }

    public function setAcademicYearId($academicYearId)
    {
    	$this->_academicYearId = $academicYearId;
    }


}