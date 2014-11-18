<?php

class IndexController extends Zend_Controller_Action
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
		//$this->_helper->redirector('index', 'user');
    }

	public function downloadTemplateAction()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

		$request = Zend_Controller_Front::getInstance()->getRequest();
        $this->params = $request->getParams();

		$location = $this->_config->template->path . $this->params['filename'];
		header('Content-Type: application/xls');
		header('Content-Disposition: attachment; filename="' . $this->params['filename']  . '"');
		readfile($location);

	}

	public function downloadArchiveAction()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

		$request = Zend_Controller_Front::getInstance()->getRequest();
        $this->params = $request->getParams();

		$location = $this->_config->archive->path . '/' . $this->params['filename'];

    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private",false);
    header("Content-Type: application/zip");
    header("Content-Disposition: attachment; filename=".basename($location));
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".filesize($location));
    ob_clean();
    flush();
    echo readfile("$location");

	}


}



