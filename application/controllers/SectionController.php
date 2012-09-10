<?php

class SectionController extends Zend_Controller_Action
{

    public function init()
    {
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

				if($formData['year'] > 0) {
					$this->view->initSectionsFormData = $formData;
				}
			} else {
				$initSectionsForm->populate($formData);
			}	

		}

		$this->view->initSectionsForm = $initSectionsForm;

	}

}





