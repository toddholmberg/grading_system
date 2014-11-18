<?php

class Application_Form_UploadSurvey extends Zend_Form
{

	public function __construct($options = null)
    {
        parent::__construct($options);
    }

	public function init()
	{

		$this->setAction('/seminars/upload/survey');
		$this->setMethod('post');
		$this->setName('upload_survey');
		$this->setAttrib('enctype', 'multipart/form-data');	

		$unid1 = new Zend_Form_Element_Text('unid1');
		$unid1->setLabel('Presenter 1');
		$unid1->class = 'presenter';
		$seminarDate1 = new Zend_Form_Element_Text('seminarDate1');
		$seminarDate1->setLabel('Seminar Date 1');
		$seminarDate1->class = 'seminar_date';
		$unid1->setDecorators(array('ViewHelper','Errors'));
		$seminarDate1->setDecorators(array('ViewHelper','Errors'));
		$this->addElement($unid1);
		$this->addElement($seminarDate1);
		

		$unid2 = new Zend_Form_Element_Text('unid2');
		$unid2->setLabel('Presenter 2');
		$seminarDate2 = new Zend_Form_Element_Text('seminarDate2');
		$seminarDate2->setLabel('Seminar Date 2');
		$unid2->setDecorators(array('ViewHelper','Errors'));
		$seminarDate2->setDecorators(array('ViewHelper','Errors'));
		$this->addElement($unid2);
		$this->addElement($seminarDate2);


		$unid3 = new Zend_Form_Element_Text('unid3');
		$unid3->setLabel('Presenter 3');
		$seminarDate3 = new Zend_Form_Element_Text('seminarDate3');
		$seminarDate3->setLabel('Seminar Date 3');
		$unid3->setDecorators(array('ViewHelper','Errors'));
		$seminarDate3->setDecorators(array('ViewHelper','Errors'));
		$this->addElement($unid3);
		$this->addElement($seminarDate3);		
		

		$unid4 = new Zend_Form_Element_Text('unid4');
		$unid4->setLabel('Presenter 4');
		$seminarDate4 = new Zend_Form_Element_Text('seminarDate4');
		$seminarDate4->setLabel('Seminar Date 4');
		$unid4->setDecorators(array('ViewHelper','Errors'));
		$seminarDate4->setDecorators(array('ViewHelper','Errors'));
		$this->addElement($unid4);
		$this->addElement($seminarDate4);		
	

		$unid5 = new Zend_Form_Element_Text('unid5');
		$unid5->setLabel('Presenter 5');
		$seminarDate5 = new Zend_Form_Element_Text('seminarDate5');
		$seminarDate5->setLabel('Seminar Date 5');
		$unid5->setDecorators(array('ViewHelper','Errors'));
		$seminarDate5->setDecorators(array('ViewHelper','Errors'));
		$this->addElement($unid5);
		$this->addElement($seminarDate5);		
		

		$unid6 = new Zend_Form_Element_Text('unid6');
		$unid6->setLabel('Presenter 6');
		$seminarDate6 = new Zend_Form_Element_Text('seminarDate6');
		$seminarDate6->setLabel('Seminar Date 6');
		$unid6->setDecorators(array('ViewHelper','Errors'));
		$seminarDate6->setDecorators(array('ViewHelper','Errors'));
		$this->addElement($unid6);
		$this->addElement($seminarDate6);		
		

		$unid7 = new Zend_Form_Element_Text('unid7');
		$unid7->setLabel('Presenter 7');
		$seminarDate7 = new Zend_Form_Element_Text('seminarDate7');
		$seminarDate7->setLabel('Seminar Date 7');
		$unid7->setDecorators(array('ViewHelper','Errors'));
		$seminarDate7->setDecorators(array('ViewHelper','Errors'));
		$this->addElement($unid7);
		$this->addElement($seminarDate7);		
		

		$unid8 = new Zend_Form_Element_Text('unid8');
		$unid8->setLabel('Presenter 8');
		$seminarDate8 = new Zend_Form_Element_Text('seminarDate8');
		$seminarDate8->setLabel('Seminar Date 8');
		$unid8->setDecorators(array('ViewHelper','Errors'));
		$seminarDate8->setDecorators(array('ViewHelper','Errors'));
		$this->addElement($unid8);
		$this->addElement($seminarDate8);		
		

		$unid9 = new Zend_Form_Element_Text('unid9');
		$unid9->setLabel('Presenter 9');
		$seminarDate9 = new Zend_Form_Element_Text('seminarDate9');
		$seminarDate9->setLabel('Seminar Date 9');
		$unid9->setDecorators(array('ViewHelper','Errors'));
		$seminarDate9->setDecorators(array('ViewHelper','Errors'));
		$this->addElement($unid9);
		$this->addElement($seminarDate9);		
		

		$unid10 = new Zend_Form_Element_Text('unid10');
		$unid10->setLabel('Presenter 10');
		$seminarDate10 = new Zend_Form_Element_Text('seminarDate10');
		$seminarDate10->setLabel('Seminar Date 10');
		$unid10->setDecorators(array('ViewHelper','Errors'));
		$seminarDate10->setDecorators(array('ViewHelper','Errors'));
		$this->addElement($unid10);
		$this->addElement($seminarDate10);		


		$config = Zend_Registry::get('config');
		$uploadPath = $config->upload->path . "/survey";

		$upload = new Zend_Form_Element_File('survey');
		$upload->setLabel('Upload survey spreadsheet:')			
			->addValidator('Count', false, 1)
			->addValidator('Size', false, 1048576)
			->addValidator('Extension', false, 'csv,xls')
			->setDecorators(array('File','Errors'));
		$this->addElement($upload);

//->setDestination($uploadPath)
//
		// Submit button
		$submit = new Zend_Form_Element_Submit('Submit');
		$submit->setLabel('Submit');
		$submit->setDecorators(array('ViewHelper','Errors'));
		$this->addElement($submit);
		
	}

	public function loadDefaultDecorators()
	{
		$this->setDecorators(
			array(
				array('ViewScript', 
					array(
						'viewScript' => 'upload/survey.phtml'
					)
				)
			)
		);
	}


}

