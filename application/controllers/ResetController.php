<?php

class ResetController extends Zend_Controller_Action
{
	public function init()
	{
		$this->_config = Zend_Registry::get('config');
        $this->view->identity = Zend_Auth::getInstance()->getIdentity();
        $this->view->currentAcademicYear = $this->_helper->currentAcademicYear();
	}

	public function indexAction()
	{
		$this->view->resetForm = new Application_Form_Reset();

		if ($this->_request->isPost()) {

			$formData = $this->_request->getPost();
			if($this->view->resetForm->isValid($formData)) {

				if(is_numeric($formData['acadmicYearId'])) {

					$this->view->message = '';

					// if the year was submitted, init the sections
					if(isset($formData['acadmicYearId'])) {

						if(isset($sections->error)) {
							$this->view->message = sprintf("<p>%s</p>", $sections->error);
						} else {
							$this->view->message = '<p>New sections successfully created.</p>';
						}

					}


				}
			} else {
				$this->view->resetForm->populate($formData);
			}	

		}

	}

}
