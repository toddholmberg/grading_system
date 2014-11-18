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
		$role = new Zend_Form_Element_Select('role_id');
		$role->setLabel('Role');
		$role->addMultiOption('', 'Please select...');
		foreach($roleData->fetchAll() as $roleItem) {
			$role->addMultiOption($roleItem['id'], $roleItem['title']);
		}

		// P-Year
		$pYearData = new Application_Model_DbTable_PYears();
		$pYear = new Zend_Form_Element_Select('p_year_id');
		$pYear->setLabel('Current P-Year');
		$pYear->addMultiOption('', 'Please select...');
		foreach($pYearData->fetchAll() as $pYearItem) {
			if(in_array($pYearItem['p'], array(3,4))) {
				$pYear->addMultiOption($pYearItem['id'], $pYearItem['p']);
			}
		}

		//Current Section
		$sectionData = new Application_Model_DbTable_Sections();
		$section = new Zend_Form_Element_Select('section_id');
		$section->setLabel('Current Section');
		$section->addMultiOption('', 'Please select...');
		foreach($sectionData->fetchAll() as $sectionItem) {
			$section->addMultiOption($sectionItem['id'], $sectionItem['number']);
		}

		// Set as grader
		$grader = new Zend_Form_Element_Checkbox('is_grader');
        $grader->setLabel('Grader')
                 ->setAttrib('id','is_grader');

		// Submit button
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Save');

		$this->addElements(array($unid, $firstName, $lastName, $email, $role, $pYear, $section, $grader, $submit));

	}
}

