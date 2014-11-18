<?php

class SectionController extends Zend_Controller_Action
{

    public function init()
    {
		$this->_config = Zend_Registry::get('config');
        $this->view->identity = Zend_Auth::getInstance()->getIdentity();
        $this->view->currentAcademicYear = $this->_helper->currentAcademicYear();
    }

    public function indexAction()
    {
        // action body
    }

    public function buildAction()
    {
	}

	public function initSectionsAction()
	{
		$initSectionsForm = new Application_Form_InitSections();

		if ($this->_request->isPost()) {

			$formData = $this->_request->getPost();
			if($initSectionsForm->isValid($formData)) {

				if(is_numeric($formData['acadmicYearId'])) {

					$this->view->message = '';

					// if the year was submitted, init the sections
					if(isset($formData['acadmicYearId'])) {
						$sectionMapper = new Application_Model_SectionMapper();
						$sections = $sectionMapper->buildSections($formData['acadmicYearId']);

						if(isset($sections->error)) {
							$this->view->message = sprintf("<p>%s</p>", $sections->error);
						} else {
							$this->view->message = '<p>New sections successfully created.</p>';
						}

					}


				}
			} else {
				$initSectionsForm->populate($formData);
			}	

		}

		$this->view->initSectionsForm = $initSectionsForm;

	}

	public function configureSectionsAction()
	{

		$academicYearMapper = new Application_Model_AcademicYearMapper();
    	$this->view->currentAcademicYear = $academicYearMapper->getCurrentAcademicYear();

		$configureSectionsForm = new Application_Form_ConfigureSections();

		if ($this->_request->isPost()) {

			$formData = $this->_request->getPost();
			
			if($configureSectionsForm->isValid($formData)) {

				if(!empty($formData['attendance']) && !empty($formData['sectionData'])) {
					try {
						$sectionMapper = new Application_Model_SectionMapper();
						$sectionMapper->saveSectionConfiguration(
							json_decode($formData['sectionData']),
							$formData['attendance']
							);
						$this->view->message = 'Section configuration saved.';
					} catch(Exception $e) {
						echo $e;
					}
				}

			} else {
				$configureSectionsForm->populate($formData);
			}	

		}

		$this->view->configureSectionsForm = $configureSectionsForm;

	}

	public function saveSectionConfigurationAction()
	{

		$formData = $this->_request->getPost();

		if(!empty($formData['attendance']) && !empty($formData['sectionData'])) {
			try {
				$sectionMapper = new Application_Model_SectionMapper();
				$sectionMapper->saveSectionConfiguration(
					json_decode($formData['sectionData']),
					$formData['attendance']
					);
				echo 'Section configuration saved.';
			} catch(Exception $e) {
				echo $e;
			}
		}


	}

	public function getSectionConfigurationAction()
	{
		$this->_helper->layout()->disableLayout();

		$formData = $this->_request->getPost();
		if(isset($formData['academicYearId'])) {
			$sectionConfigurationForm = new Application_Form_GetSectionConfiguration($formData);
			$this->view->sectionConfigurationForm = $sectionConfigurationForm;
		}
	}

	public function academicYearSettingsAction()
	{
		// init the set academic year form
		$this->view->setAcademicYearForm = $this->_getSetAcademicYearForm();
		$this->view->setAcademicYearForm->setAction($this->getRequest()->getBaseUrl() . '/section/set-academic-year');		


		// init the add new academic year form
		$this->view->addAcademicYearForm = $this->_getAddAcademicYearForm();
		$this->view->addAcademicYearForm->setAction($this->getRequest()->getBaseUrl() . '/section/add-academic-year');		
		
	}

	private function _getSetAcademicYearForm()
	{
		if (!Zend_Registry::isRegistered('form_setAcademicYear')){           
			require_once (APPLICATION_PATH . '/forms/SetAcademicYear.php');
			$this->_setAcademicYearForm = new Application_Form_SetAcademicYear();
			Zend_Registry::set('form_setAcademicYear', $this->_setAcademicYearForm);
		}else{           
			$this->_setAcademicYearForm = Zend_Registry::get('form_setAcademicYear');
		}
		return $this->_setAcademicYearForm;
	}

	private function _getAddAcademicYearForm()
	{
		if (!Zend_Registry::isRegistered('form_addAcademicYear')){           
			require_once (APPLICATION_PATH . '/forms/AddAcademicYear.php');
			$this->_addAcademicYearForm = new Application_Form_AddAcademicYear();
			Zend_Registry::set('form_addAcademicYear', $this->_addAcademicYearForm);
		}else{           
			$this->_addAcademicYearForm = Zend_Registry::get('form_addAcademicYear');
		}
		return $this->_addAcademicYearForm;
	}



	public function addAcademicYearAction()
	{
		$academicYearMapper = new Application_Model_AcademicYearMapper();

		$form = $this->_getAddAcademicYearForm();

		if ($this->_request->isPost()) {
			try{

				$formData = $this->_request->getPost();

				if ($form->isValid($formData)) {

					$insertAcademicYear = $academicYearMapper->addAcademicYear($formData);
					if(isset($insertAcademicYeat['error']['message'])) {
						throw new Exception($insertAcademicYear['error']['message']);
					} else {
						$this->view->message = 'success';

						$redirector = $this->_helper->getHelper('Redirector');
						$redirector->gotoSimple('academic-year-settings', 'section');			

					}
				} else {
					$form->populate($formData);
					return $this->_forward('academic-year-settings');
				}
			} catch (Exception $e) {
				$this->view->error = $e;
			}
		}

		$this->view->addAcademicYearForm = $form;		
	}




	public function setAcademicYearAction()
	{
		$academicYearMapper = new Application_Model_AcademicYearMapper();

		$this->view->currentAcademicYear = $academicYearMapper->getCurrentAcademicYear();

		$form = $this->_getSetAcademicYearForm();

		if ($this->_request->isPost()) {
			try{

				$formData = $this->_request->getPost();

				if ($form->isValid($formData)) {

					$updateAcademicYear = $academicYearMapper->setCurrentAcademicYear($formData);
					if(isset($updateAcademicYeat['error']['message'])) {
						throw new Exception($updateAcademicYear['error']['message']);
					} else {
						$this->view->message = 'success';

						$this->view->currentAcademicYear = $academicYearMapper->getCurrentAcademicYear();
					}
			
					$redirector = $this->_helper->getHelper('Redirector');
					$redirector->gotoSimple('academic-year-settings', 'section');			

				} else {
					$form->populate($formData);
					return $this->_forward('academic-year-settings');
				}
			} catch (Exception $e) {
				$this->view->error = $e;
			}
		}

		$this->view->setAcademicYearForm = $form;		
	}




}





