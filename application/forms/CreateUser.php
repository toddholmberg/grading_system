<?php

class Application_Form_CreateUser extends Zend_Form
{

	public function init()
	{

		$this->setName('create_user');

		$unid = new Zend_Form_Element_Text('unid');
		$unid->setLabel('Unid')
			->setRequired(true)
			->addValidator('NotEmpty');

		$firstName = new Zend_Form_Element_Text('first_name');
		$firstName->setLabel('First name')
			->setRequired(true)
			->addValidator('NotEmpty');

		$lastName = new Zend_Form_Element_Text('last_name');
		$lastName->setLabel('Last name')
			->setRequired(true)
			->addValidator('NotEmpty');

		$email = new Zend_Form_Element_Text('email');
		$email->setLabel('Email address')
			->addFilter('StringToLower')
			->setRequired(true)
			->addValidator('NotEmpty', true)
			->addValidator('EmailAddress'); 

		$role = new Zend_Form_Element_Select('role');
		$role->setLabel('Role')
			->setMultiOptions(array(1 => 'Admin', 2 =>'Faculty', 3 => 'Student'))
			->setRequired(true)->addValidator('NotEmpty', true);

		// Current Year
		$academic_year = new Zend_Form_Element_Select('academic_year');
		$academic_year->setLabel('Year')
			->setMultiOptions(array(1 =>2012, 2 =>2011))
			->setRequired(true)->addValidator('NotEmpty', true);

		// Current Year
		$p_year = new Zend_Form_Element_Select('p_year');
		$p_year->setLabel('P')
			->setMultiOptions(array(1 => 1, 2 => 2, 3 => 3, 4 => 4))
			->setRequired(true)->addValidator('NotEmpty', true);

		//Current Section
		$section = new Zend_Form_Element_Select('section');
		$section->setLabel('Section')
			->setMultiOptions(array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5))
			->setRequired(true)->addValidator('NotEmpty', true);

		$archive = new Zend_Form_Element_Checkbox('archive');
        $archive->setLabel('Archived')
                 ->setAttrib('id','archive'); 

		// Submit button
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Save');
		$this->addElements(array($unid, $firstName, $lastName, $email, $role, $academic_year, $p_year, $section, $archive, $submit));





	}


}

