<?php

class GradingController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
		// process section filter
		if ($this->_request->isPost()) {
			$filterData = $this->_request->getPost();
			$this->view->p_year_id = $filterData['p_year_id'];
			$this->view->section_id = $filterData['section_id'];
		} else {
			$this->view->p_year_id = 3;
			$this->view->section_id = 1;
		}

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

    public function formatSurveyDetailAction()
    {
   		// disable view
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
	    $this->view->data = $this->_request->getPost();
		//echo $this->view->formatSurveyDetail($data);
		echo $this->view->render('grading/format-survey-detail.phtml');
	     // action body
    }

}









