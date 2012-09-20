<?php


class UploadController extends Zend_Controller_Action
{

    public function init()
    {
		$this->_config = Zend_Registry::get('config');
		$this->view->message = new stdClass();
    }

    public function indexAction()
    {
    }

    public function surveyAction()
	{
		$form = new Application_Form_UploadSurvey();
		if ($this->_request->isPost()) {

			$formData = $this->_request->getPost();

			if ($form->isValid($formData)) {
				try {
					if ($form->survey->receive()) {
						$this->view->location = $form->survey->getFileName();	
						$uploadMapper = new Application_Model_UploadMapper($this->view->location);
						$uploadData = $uploadMapper->parse();


						$surveyMapper = new Application_Model_SurveyMapper($uploadData, $formData);
						
//						Zend_Debug::dump($surveyMapper); exit;
	
						//$this->view->surveyData = $surveyMapper->parse();
					}

				} catch(Exception $e) {
					$this->view->message->error = $e->getMessage();
				}
			}

		}
		$this->view->surveyUploadForm = $form;

				/*

		try {
			$form->file->receive();
			//upload complete!
			$file = new Default_Model_File();
			$file->setDisplayFilename($originalFilename['basename'])
				->setActualFilename($newFilename)
				->setMimeType($form->file->getMimeType())
				->setDescription($form->description->getValue());
			$file->save();
		} catch (Exception $e) {
			//error: file couldn't be received, or saved (one of the two)
		}	
		*/

    }

    public function usersAction()
    {
		$uploadPath = $this->_config->upload->path . "/user";
		
        // action body
    }


}





