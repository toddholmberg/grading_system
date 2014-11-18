<?php

class Application_Form_UploadUsers extends Zend_Form
{

	public function init()
	{

		// Academic year
		// $academicYearData = new Application_Model_DbTable_AcademicYears();
		// $academicYear = new Zend_Form_Element_Select('year');
		// $academicYear->setLabel('Select an Academic Year:');
		// $academicYear->setRequired(true)->addValidator('NotEmpty', true);
		// $academicYear->addMultiOption('', 'Please select...');
		// foreach($academicYearData->getAcademicYears() as $academicYearItem) {
		// 		$academicYear->addMultiOption($academicYearItem['id'], $academicYearItem['year']);
		// }

		$academicYearMapper = new Application_Model_AcademicYearMapper();
    	$currentAcademicYear = $academicYearMapper->getCurrentAcademicYear();


        $academicYearHidden = new Zend_Form_Element_Hidden('acadmicYearId');
        $academicYearHidden->setValue($currentAcademicYear['id']);
        $academicYearHidden->setDisableLoadDefaultDecorators(true);
        $academicYearHidden->addDecorator('ViewHelper');
        $academicYearHidden->removeDecorator('DtDdWrapper');
        $academicYearHidden->removeDecorator('HtmlTag');
        $academicYearHidden->removeDecorator('Label');

		$config = Zend_Registry::get('config');
		$uploadPath = $config->upload->path . "/user";
		$this->setName('upload_users');
		$this->setAttrib('enctype', 'multipart/form-data');	
		$upload = new Zend_Form_Element_File('users');
		$upload->setLabel('Upload user spreadsheet:')
			->setDestination($uploadPath);
		// ensure only 1 file
		$upload->addValidator('Count', false, 1);
		// limit to 1MB
		$upload->addValidator('Size', false, 1048576);
		// only CSV and XLS
		$upload->addValidator('Extension', false, 'csv,xls');

		// Submit button
		$submit = new Zend_Form_Element_Submit('Submit');
		$submit->setLabel('Submit');

		$this->addDisplayGroup(array($academicYearHidden, $upload, $submit), 'upload_tools_top');
	}


}

