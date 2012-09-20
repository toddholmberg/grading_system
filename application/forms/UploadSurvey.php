<?php

class Application_Form_UploadSurvey extends Zend_Form
{

	public function init()
	{

		// Upload Type 
		$type = new Zend_Form_Element_Select('type');
		$type->setLabel('Reviewer Type');
		$type->addMultiOptions(array('', 'Please select...', '1' => 'Student', '2' => 'Faculty'));
		$type->setRequired();	

		$unid1 = new Zend_Form_Element_Text('unid1');
		$unid1->setLabel('Presenter 1');
		$unid1->class = 'presenter';
		$seminarDate1 = new Zend_Form_Element_Text('seminarDate1');
		$seminarDate1->setLabel('Seminar Date 1');


		$unid2 = new Zend_Form_Element_Text('unid2');
		$unid2->setLabel('Presenter 2');
		$seminarDate2 = new Zend_Form_Element_Text('seminarDate2');
		$seminarDate2->setLabel('Seminar Date 2');


		$unid3 = new Zend_Form_Element_Text('unid3');
		$unid3->setLabel('Presenter 3');
		$seminarDate3 = new Zend_Form_Element_Text('seminarDate3');
		$seminarDate3->setLabel('Seminar Date 3');


		$unid4 = new Zend_Form_Element_Text('unid4');
		$unid4->setLabel('Presenter 4');
		$seminarDate4 = new Zend_Form_Element_Text('seminarDate4');
		$seminarDate4->setLabel('Seminar Date 4');


		$unid5 = new Zend_Form_Element_Text('unid5');
		$unid5->setLabel('Presenter 5');
		$seminarDate5 = new Zend_Form_Element_Text('seminarDate5');
		$seminarDate5->setLabel('Seminar Date 5');


		$unid6 = new Zend_Form_Element_Text('unid6');
		$unid6->setLabel('Presenter 6');
		$seminarDate6 = new Zend_Form_Element_Text('seminarDate6');
		$seminarDate6->setLabel('Seminar Date 6');


		$unid7 = new Zend_Form_Element_Text('unid7');
		$unid7->setLabel('Presenter 7');
		$seminarDate7 = new Zend_Form_Element_Text('seminarDate7');
		$seminarDate7->setLabel('Seminar Date 7');


		$unid8 = new Zend_Form_Element_Text('unid8');
		$unid8->setLabel('Presenter 8');
		$seminarDate8 = new Zend_Form_Element_Text('seminarDate8');
		$seminarDate8->setLabel('Seminar Date 8');


		$unid9 = new Zend_Form_Element_Text('unid9');
		$unid9->setLabel('Presenter 9');
		$seminarDate9 = new Zend_Form_Element_Text('seminarDate9');
		$seminarDate9->setLabel('Seminar Date 9');


		$unid10 = new Zend_Form_Element_Text('unid10');
		$unid10->setLabel('Presenter 10');
		$seminarDate10 = new Zend_Form_Element_Text('seminarDate10');
		$seminarDate10->setLabel('Seminar Date 10');


		/*
		// P-Year
		$pYearData = new Application_Model_DbTable_PYears();
		$pYear = new Zend_Form_Element_Select('p_year');
		$pYear->setLabel('Current P-Year');
		$pYear->addMultiOption('', 'Please select...');
		foreach($pYearData->fetchAll() as $pYearItem) {
			if(in_array($pYearItem['p'], array(3,4))) {
				$pYear->addMultiOption($pYearItem['id'], $pYearItem['p']);
			}
		}

		//Current Section
		$sectionData = new Application_Model_DbTable_Sections();
		$section = new Zend_Form_Element_Select('section');
		$section->setLabel('Current Section');
		$section->addMultiOption('', 'Please select...');
		foreach($sectionData->fetchAll() as $sectionItem) {
			$section->addMultiOption($sectionItem['id'], $sectionItem['number']);
		}
*/

		$config = Zend_Registry::get('config');
		$uploadPath = $config->upload->path . "/survey";
		$this->setName('upload_survey');
		$this->setAttrib('enctype', 'multipart/form-data');	
		$upload = new Zend_Form_Element_File('survey');
		$upload->setLabel('Upload survey spreadsheet:')
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


//		$this->addDisplayGroup(array($type,$upload, $submit), 'upload_tools_top');
		$this->addDisplayGroup(array($type,$upload), 'upload_tools_top');
		$this->addDisplayGroup(array($unid1, $seminarDate1, $unid2, $seminarDate2,$unid3, $seminarDate3,$unid4, $seminarDate4,$unid5, $seminarDate5), 'presenter_left');
		//$this->addDisplayGroup(array($unid6, $seminarDate6, $unid7, $seminarDate7, $unid8, $seminarDate8, $unid9, $seminarDate9, $unid10, $seminarDate10), 'presenter_right');
		$this->addDisplayGroup(array($submit), 'upload_tools_bottom');
	}


}

