<?php

class Application_Form_EditUser extends Zend_Form
{


	public function init()
	{

		$this->setName('edit_user');

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
			->setMultiOptions(array(1 => 'Admin', 2 =>'Student', 3 => 'Faculty'))
			->setRequired(true)->addValidator('NotEmpty', true);

		// Current Year
		$p_year = new Zend_Form_Element_Select('p_year');
		$p_year->setLabel('Year')
			->setMultiOptions(array(1 => 'Admin', 2 =>'Faculty', 3 => 'Student'))
			->setRequired(true)->addValidator('NotEmpty', true);

		//Current Section
		$section = new Zend_Form_Element_Select('section');
		$section->setLabel('Section')
			->setMultiOptions(array(1 => 'Admin', 2 =>'Faculty', 3 => 'Student'))
			->setRequired(true)->addValidator('NotEmpty', true);

		$archive = new Zend_Form_Element_Checkbox('archive');
        $archive->setLabel('Archived')
                 ->setAttrib('id','archive'); 

		$user_id = new Zend_Form_Element_Hidden('id');
		$user_id->setDisableLoadDefaultDecorators(true);
		$user_id->addDecorator('ViewHelper');
		$user_id->removeDecorator('DtDdWrapper');
		$user_id->removeDecorator('HtmlTag');
		$user_id->removeDecorator('Label');	

		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Save');
		$this->addElements(array($unid, $firstName, $lastName, $email, $role, $p_year, $section, $archive, $submit));


	}

}

