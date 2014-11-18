<?php

class Application_Form_AddAcademicYear extends Zend_Form
{

    public function init()
    {

		// Academic year
		$academicYear = new Zend_Form_Element_Text('newYear');
		$academicYear->setLabel('Enter a new year (example: 2014):');
		$academicYear->setRequired(true)->addValidators(array(
			array('NotEmpty', true),
			array('Digits')
			));
		
		$yearValidator = new Cop_Validator_Year();
		$academicYear->addValidator($yearValidator);

		// Submit button
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Submit');

		$this->addElements(array($academicYear, $submit));
	}


}

