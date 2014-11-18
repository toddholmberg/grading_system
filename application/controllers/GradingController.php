<?php

class GradingController extends Zend_Controller_Action
{

	private $_config;

    public function init()
    {
		$this->_config = Zend_Registry::get('config');
        $this->view->identity = Zend_Auth::getInstance()->getIdentity();
        $this->view->currentAcademicYear = $this->_helper->currentAcademicYear();
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
    }

    public function reportAction()
    {
   		// disable view
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
	    $this->view->params = $this->_request->getPost();
		echo $this->view->render('grading/report.phtml');
	}

	public function rosterAction()
	{
		// disable view
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$this->view->params = $this->_request->getPost();
		echo $this->view->render('grading/roster.phtml');
	}

	public function downloadReportAction()
	{
		// disable view
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

		$request = Zend_Controller_Front::getInstance()->getRequest();
        $this->params = $request->getParams();

		$location = APPLICATION_PATH . '/../data/reports/' . $this->params['filename'];
		header('Content-Type: application/pdf');
		header('Content-Disposition: attachment; filename="' . $this->params['filename']  . '"');
		readfile($location);
	}

}













