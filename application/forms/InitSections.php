<?php

class Application_Form_InitSections extends Zend_Form
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

        $sectionValidator = new Cop_Validator_SectionsExist();
		$academicYearHidden->addValidator($sectionValidator, true);

		// Submit button
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Initialize the sections for ' . $currentAcademicYear['year']);

		$this->addElements(array($academicYearHidden, $submit));

	}


}

