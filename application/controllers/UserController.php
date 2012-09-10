<?php

class UserController extends Zend_Controller_Action
{

	public function init()
	{
		/* Initialize action controller here */
	}

	public function indexAction()
	{
		// action body
		$user = new Application_Model_UserMapper();
		$this->view->users = $user->fetchAllJson();

	}

	public function loadusersAction()
	{
		// disable view
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();

		// get users

		$offset = $this->getRequest()->getParam('start');
		$limit = $this->getRequest()->getParam('count');
		$sort = $this->getRequest()->getParam('sort');
		if (isset($sort)) {
			$tempsort = $sort;
			if(substr($tempsort,0,1) == '-') {
				$sort = substr($tempsort,1,strlen($tempsort)-1) . ' DESC';
			} else {
				$sort = $sort . ' ASC';	
			}
		}

		$user = new Application_Model_DbTable_User();
		$query = $user->select()->order($sort);
		$users = $user->fetchAll($query);
		$this->view->paginator = Zend_Paginator::factory($users);
		$this->view->paginator->setItemCountPerPage($limit)
			->setCurrentPageNumber($offset);


		//$data = new Zend_Dojo_Data('id', $paginator, 'id');
		$this->view->paginationControl($paginator, 'Sliding','pagination.phtml');

		Zend_Debug::dump($this->view->paginator);
		//		exit;

		echo $data->toJson();
	}

	public function createUserAction()
	{
		$form = new Application_Form_CreateUser();

		if ($this->_request->isPost()) {

			$formData = $this->_request->getPost();


			if ($form->isValid($formData)) {

				$userMapper = new Application_Model_UserMapper();
				$newUser = $userMapper->save($formData);
				$this->view->newUser = $newUser;

			} else {

				$form->populate($formData);

			}
		}

		$this->view->createUserForm = $form;
	}

	public function editUserAction()
	{

		$form = new Application_Form_EditUser();
		$userId = $this->getRequest()->getParam('user_id');


		if (isset($userId)) {

			if ($this->_request->isPost()) {

				$formData = $this->_request->getPost();
				if ($form->isValid($formData)) {

					$userMapper = new Application_Model_UserMapper();
					$updateUser = $userMapper->save($formData);
					$userArray = $updateUser[0]->toArray();

					$this->view->updatedUser = $userMapper->find($userArray['id']);
				} else {

					$form->populate($formData);

				}
			} else {
				$userMapper = new Application_Model_UserMapper();
				$user = $userMapper->find($userId);
				$form->populate($user);
			}
		}
		$this->view->editUserForm = $form;
		
	}


}





