<?php

class Application_Form_FacultyScoring extends Zend_Form
{

	public function init()
	{

		$this->setName('');

		// prep
		$prep = new Zend_Form_Element_Text('prep');
		$prep->setLabel('Preparation')
			->setRequired(true)
			->addValidators(array(
				array('NotEmpty', true),
				array('Digits')
			));

		// prof
		$prof = new Zend_Form_Element_Text('prof');
		$prof->setLabel('Professionalism')
			->setRequired(true)
			->addValidators(array(
				array('NotEmpty', true),
				array('Digits')
			));

		// Submit button
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Save');

		$this->addElements(array($prep, $prof));	

    }


}
