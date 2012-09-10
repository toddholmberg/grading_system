<?php

class Application_Form_InitSections extends Zend_Form
{

    public function init()
    {

		/** 
		 * choose academic year
		 * TO-DO: get options from DB
		 */
		$academicYear = new Zend_Form_Element_Select('year');
		$academicYear->setLabel('Generate sections for this academic year:')
			->setMultiOptions(array(0 =>'Choose a year...', 1 =>2012, 2 =>2011))
			->setRequired(true)->addValidator('NotEmpty', true);


		// Submit button
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Submit');

		$this->addElements(array($academicYear, $currentAcademicYear, $submit));
	}


}

