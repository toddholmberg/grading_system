<?php

class Application_Form_SetAcademicYear extends Zend_Form
{

    public function init()
    {

		// Academic year
		$academicYearData = new Application_Model_DbTable_AcademicYears();
		$academicYear = new Zend_Form_Element_Select('year');
		$academicYear->setLabel('Select a new Academic Year:');
		$academicYear->setRequired(true)->addValidator('NotEmpty', true);
		$academicYear->addMultiOption('', 'Please select...');
		foreach($academicYearData->getAcademicYears() as $academicYearItem) {
				$academicYear->addMultiOption($academicYearItem['id'], $academicYearItem['year']);
		}

		// Submit button
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setLabel('Submit');

		$this->addElements(array($academicYear, $submit));
	}


}

