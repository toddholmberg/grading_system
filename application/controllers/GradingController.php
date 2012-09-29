<?php

class GradingController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body

    }

    public function saveScoresAction()
    {
		// disable view
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
	    $data = $this->_request->getPost();
		
		$seminarMapper = new Application_Model_SeminarMapper();
		$scoreData = $seminarMapper->saveScores($data);	
		echo json_encode($scoreData);
    }


}





