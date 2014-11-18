<?php

class Application_Form_Archive extends Zend_Form
{

    public function init()
    {

		// Set current academic year hidden
		$academicYearMapper = new Application_Model_AcademicYearMapper();
    	$currentAcademicYear = $academicYearMapper->getCurrentAcademicYear();


        $academicYearHidden = new Zend_Form_Element_Hidden('acadmicYearId');
        $academicYearHidden->setValue($currentAcademicYear['id']);
        $academicYearHidden->setDisableLoadDefaultDecorators(true);
        $academicYearHidden->addDecorator('ViewHelper');
        $academicYearHidden->removeDecorator('DtDdWrapper');
        $academicYearHidden->removeDecorator('HtmlTag');
        $academicYearHidden->removeDecorator('Label');

		// Submit button
		$submit = new Zend_Form_Element_Submit('Archive');
		$submit->setLabel('Archive all data for ' . $currentAcademicYear['year']);

		$this->addElements(array($academicYearHidden, $submit));

	}




}



