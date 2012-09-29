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
			->addValidator('NotEmpty');

		// prof
		$prof = new Zend_Form_Element_Text('prof');
		$prof->setLabel('Professionalism')
			->setRequired(true)
			->addValidator('NotEmpty');

		// Submit button
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Save');

		$this->addElements(array($prep, $prof));	

    }


}

