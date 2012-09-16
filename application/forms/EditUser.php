<?php

class Application_Form_EditUser extends Zend_Form
{


	public function init()
	{

		$this->setName('edit_user');

		// unid
		$unid = new Zend_Form_Element_Text('unid');
		$unid->setLabel('Unid')
			->setRequired(true)
			->addValidator('NotEmpty');

		// First Name
		$firstName = new Zend_Form_Element_Text('first_name');
		$firstName->setLabel('First name')
			->setRequired(true)
			->addValidator('NotEmpty');

		// Last Name
		$lastName = new Zend_Form_Element_Text('last_name');
		$lastName->setLabel('Last name')
			->setRequired(true)
			->addValidator('NotEmpty');

		// Email
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
		$role->addMultiOption('','Please select...');
		foreach($roleData->fetchAll() as $roleItem) {
			$role->addMultiOption($roleItem['id'], $roleItem['title']);
		}

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
  

		// Archive user
		$archive = new Zend_Form_Element_Checkbox('archive');
        $archive->setLabel('Archived')
                 ->setAttrib('id','archive'); 

		// Hidden user_id element
		$user_id = new Zend_Form_Element_Hidden('id');
		$user_id->setDisableLoadDefaultDecorators(true);
		$user_id->addDecorator('ViewHelper');
		$user_id->removeDecorator('DtDdWrapper');
		$user_id->removeDecorator('HtmlTag');
		$user_id->removeDecorator('Label');	

		// Submit button
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Save');

		$this->addElements(array($unid, $firstName, $lastName, $email, $role, $pYear, $section, $archive, $user_id, $submit));
	}
}

