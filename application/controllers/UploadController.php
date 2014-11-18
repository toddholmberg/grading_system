<?php


class UploadController extends Zend_Controller_Action
{
	/**
	 * Initialize relevant values.
	 * @return void
	 */
    public function init()
    {
		$this->view->message = new stdClass();
		$this->_config = Zend_Registry::get('config');
        $this->view->identity = Zend_Auth::getInstance()->getIdentity();
        $this->view->currentAcademicYear = $this->_helper->currentAcademicYear();
    }

    /**
     * Index action.
     * @return void
     */
    public function indexAction()
    {
    }

    /**
     * User action.
     * @return void [description]
     */
    public function usersAction()
	{
		$form = new Application_Form_UploadUsers();
		if ($this->_request->isPost()) {

			$formData = $this->_request->getPost();

			if ($form->isValid($formData)) {
				try {
					if ($form->users->receive()) {
						$this->view->location = $form->users->getFileName();	
						$uploadMapper = new Application_Model_UploadMapper($this->view->location, 1);
						$uploadData = $uploadMapper->parse(1);

						$userMapper = new Application_Model_UserMapper();
						$userMapper->saveUpload($uploadData, $formData['acadmicYearId']);
						$this->view->errors = $userMapper->getErrors();

						if(!empty($this->view->errors)) {
							$this->view->success = 0;	
						} else {
							$this->view->success = 1;
						}
					}
				} catch(Exception $e) {
					$this->view->message->error = $e->getMessage();
				}
			}
		}
		$this->view->userUploadForm = $form;
    }


    public function surveyAction()
	{
		$form = new Application_Form_UploadSurvey();
		if ($this->_request->isPost()) {

			$formData = $this->_request->getPost();

			//Zend_Debug::dump($formData);

			if ($form->isValid($formData)) {
				try {
					$upload = new Zend_File_Transfer_Adapter_Http();
					$upload->setDestination($this->_config->upload->path);
					$upload->receive();

					if ($form->survey->receive()) {

						$this->view->location = $upload->getFileName();

						$uploadMapper = new Application_Model_UploadMapper($this->view->location);
						$uploadData = $uploadMapper->parse();

						$surveyMapper = new Application_Model_SurveyMapper($uploadData, $formData);
						$this->view->errors = $surveyMapper->getErrors();

						if(!empty($this->view->errors)) {
							$this->view->success = 0;	
						} else {
							$this->view->success = 1;
						}

					}

				} catch(Exception $e) {
					$this->view->message->error = $e->getMessage();
				}
			}
		}
		$this->view->surveyUploadForm = $form;
    }

}





