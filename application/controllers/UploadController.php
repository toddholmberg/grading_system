<?php


class UploadController extends Zend_Controller_Action
{

    public function init()
    {
		$this->_config = Zend_Registry::get('config');
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
						$surveyMapper = new Application_Model_SurveyMapper($this->view->location);
						$this->view->surveyData = $surveyMapper->parse();
					}

				} catch(Exception $e) {
					$this->view->message->error = $e;
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





