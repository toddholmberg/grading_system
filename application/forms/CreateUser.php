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

		// Access control role
		$roleData = new Application_Model_DbTable_Roles();
		$role = new Zend_Form_Element_Select('role');
		$role->setLabel('Role');
		$role->addMultiOption(0, 'Please select...');
		foreach($roleData->fetchAll() as $roleItem) {
			$role->addMultiOption($roleItem['id'], $roleItem['title']);
		}

		// P-Year
		$pYearData = new Application_Model_DbTable_PYears();
		$pYear = new Zend_Form_Element_Select('p_year');
		$pYear->setLabel('P-Year');
		$pYear->addMultiOption(0, 'Please select...');
		foreach($pYearData->fetchAll() as $pYearItem) {
			$pYear->addMultiOption($pYearItem['id'], $pYearItem['p']);
		}

		//Current Section
		$sectionData = new Application_Model_DbTable_Sections();
		$section = new Zend_Form_Element_Select('section');
		$section->setLabel('Section');
		$section->addMultiOption(0, 'Please select...');
		foreach($sectionData->fetchAll() as $sectionItem) {
			$section->addMultiOption($sectionItem['id'], $sectionItem['number']);
		}

		// Archive user
		$archive = new Zend_Form_Element_Checkbox('archive');
        $archive->setLabel('Archived')
                 ->setAttrib('id','archive'); 

		// Submit button
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Save');

		$this->addElements(array($unid, $firstName, $lastName, $email, $role, $pYear, $section, $archive, $user_id, $submit));

	}
}

