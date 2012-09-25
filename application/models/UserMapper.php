<?php

class Application_Model_UserMapper
{

	protected $_dbTable;

	public function setDbTable($dbTable)
	{
		if (is_string($dbTable)) {
			$dbTable = new $dbTable();
		}
		if (!$dbTable instanceof Zend_Db_Table_Abstract) {
			throw new Exception('Invalid table data gateway provided');
		}
		$this->_dbTable = $dbTable;
		return $this;
	}

	public function getDbTable()
	{
		if (null === $this->_dbTable) {
			$this->setDbTable('Application_Model_DbTable_Users');
		}
		return $this->_dbTable;
	}

	public function save($data)
	{
		// user vital data
		$userData = array(
				'id' => $data['id'],
				'unid'   => $data['unid'],
				'first_name' => $data['first_name'],
				'last_name' => $data['last_name'],
				'email'   => $data['email'],
				'archive'   => empty($data['archive'])? 0 : $data['archive'],
				'created_date' => date('Y-m-d H:i:s')
				);

		// role id
		$roleData = array(
			'role_id' => $data['role']
		);

		// section id
		if(!empty($data['p_year']) && !empty($data['section'])) {
			$sectionData = array(
					'p_year_id'=> $data['p_year'],
					'section_id' => $data['section']
					);
		}
		if (empty($userData['id'])) {
			// insert user	
			try {
				$newUser = $this->insert($userData, $roleData, $sectionData);
				return $newUser;
			} catch(Exception $e) {
				return array('error' => array('message'=> $e->getMessage()));
			}
		} else { 
			// update user	
			try {
				$updatedUser = $this->update($userData, $roleData, $sectionData);
				return $updatedUser;
			} catch(Exception $e) {
				return array('error' => array('message'=> $e->getMessage()));
			}	
		}
	}


	public function insert($userData, $roleData, $sectionData = null)
	{
		try {
			// drop 'id' field from userData array
			unset($userData['id']);

			// begin transaction
			$this->getDbTable()->getDefaultAdapter()->beginTransaction();	

			// insert new user
			$user = new Application_Model_DbTable_Users();
			$user_id = $user->insert($userData);

			// map user role
			$userRole = new Application_Model_DbTable_UserRole();
			$userRole->insert(array(
				'user_id'=>$user_id,
				'role_id'=>$roleData['role_id']
			));

			// map user to current section
			// get current academic year
			$academicYears = new Application_Model_DbTable_AcademicYears();
			$currentAcademicYear = $academicYears->getCurrentAcademicYear();

			if(!empty($sectionData)) {
				$sectionMapper = new Application_Model_SectionMapper();
				$sectionToBeMapped = $sectionMapper->findAcademicYearPYearSectionId(
						$currentAcademicYear['id'],
						$sectionData['p_year_id'],
						$sectionData['section_id']
						);			
				$sectionMapper->saveUserSection($user_id, $sectionToBeMapped['id']);	
			}

			// commit the transaction
			$this->getDbTable()->getDefaultAdapter()->commit();	

			// get saved user data
			$newUser = $user->find($user_id);

			return $newUser;		
		} catch(Exception $e) {
			$this->getDbTable()->getDefaultAdapter()->rollback();
			throw new Exception($e->getMessage());
		}
	}


	public function update($userData, $roleData, $sectionData = null)
	{
		// get current academic year
		$academicYears = new Application_Model_DbTable_AcademicYears();
		$currentAcademicYear = $academicYears->getCurrentAcademicYear();

		try {
			// begin transaction
			$this->getDbTable()->getDefaultAdapter()->beginTransaction();	
			$user = new Application_Model_DbTable_Users();
			$user->update($userData, array('id = ?' => $userData['id']));

			// update role
			$userRole = new Application_Model_DbTable_UserRole();
			$where = array('user_id = ?' => $userData['id']);
			$currentRole = $userRole->fetchRow($where);
			if($currentRole->role_id !== $roleData['role_id']) {
				$currentRole->role_id = $roleData['role_id'];
				$UserRoleId = $currentRole->save();
			}

			if(!empty($sectionData)) {
				$sectionMapper = new Application_Model_SectionMapper();
				$sectionToBeMapped = $sectionMapper->findAcademicYearPYearSectionId(
						$currentAcademicYear['id'],
						$sectionData['p_year_id'],
						$sectionData['section_id']
						);			
				$sectionMapper->updateUserSection(
						$userData['id'],
						$currentAcademicYear['id'],
						$sectionToBeMapped['id']
						);	
			}

			// commit the transaction
			$this->getDbTable()->getDefaultAdapter()->commit();	

			$updatedUser = $this->find($userData['id']);
			return $updatedUser;

		} catch(Exception $e) {
			$this->getDbTable()->getDefaultAdapter()->rollback();
			throw new Exception($e->getMessage());
		}

	}

	public function find($user_id)
	{
		try {
			$result = $this->getDbTable()->find($user_id);
			if (0 == count($result)) {
				throw new Exception("User with id $user_id does not exist.");
			}
			$user = $result->current();

			// get role
			$role = new Application_Model_DbTable_Roles();
			$userRole = new Application_Model_DbTable_UserRole();
			$roles = $user->findManyToManyRowset($role, $userRole)->toArray();

			// add related data
			$user = $user->toArray();
			$user['role'] = $roles[0]['id'];

			// get current section
			$academicYears = new Application_Model_DbTable_AcademicYears();
			$currentAcademicYear = $academicYears->getCurrentAcademicYear();
			$sectionMapper = new Application_Model_SectionMapper();
			$section = $sectionMapper->getUserCurrentSectionMapId($user_id, $currentAcademicYear['id']);
			$user['p_year'] = $section['p_year_id'];
			$user['section'] = $section['section_id'];

			return $user;
		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		}

	}	

	public function getFacultyByPYearSectionId($pyearName, $sectionNumber)
	{

		// select * from user left join user__section us on user.id = us.user_id left join academic_year__p_year__section aps on aps.id = us.section_id left join p_year on aps.p_year_id = p_year.id left join section on aps.section_id = section.id left join user__role ur on ur.user_id = user.id where section.number = 1 and p_year.p = 3 and ur.role_id = 3;

		try {
			$db = $this->getDbTable()->getDefaultAdapter();
			$sql = 'select user.id, user.unid, user.last_name, user.first_name from user__section left join user on user__section.user_id = user.id where user__section.id = :userSectionId';

			$sth = $db->prepare($sql);
			$sth->bindParam(':userSectionId', $userSectionId);
			if($sth->execute()) {
				$result = $sth->fetch(PDO::FETCH_ASSOC);
				return $result;
			} else {
				echo $sth->error_Info();
				throw new Exception($sth->errorInfo());
			}		


		} catch (Exception $e) {
			throw new Exception("userMapper::getUserByUserSectionId(): " . $e->getMessage());
		}	
	}



	
	public function getUserByUserSectionId($userSectionId)
	{
		try {
			$db = $this->getDbTable()->getDefaultAdapter();
			$sql = 'select user.id, user.unid, user.last_name, user.first_name from user__section left join user on user__section.user_id = user.id where user__section.id = :userSectionId';

			$sth = $db->prepare($sql);
			$sth->bindParam(':userSectionId', $userSectionId);
			if($sth->execute()) {
				$result = $sth->fetch(PDO::FETCH_ASSOC);
				return $result;
			} else {
				echo $sth->error_Info();
				throw new Exception($sth->errorInfo());
			}		


		} catch (Exception $e) {
			throw new Exception("userMapper::getUserByUserSectionId(): " . $e->getMessage());
		}	
	}

	public function findUserByUnid($unid)
	{
		try {
			$table = $this->getDbTable();
			$select = $table->select()->where('unid = ?',$unid);
			$row = $table->fetchRow($select);

			if(isset($row)) {
				return $row->toArray();
			} else {
				return false;
			}		
		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		}
	}


	public function fetchAll()
	{
		$resultSet = $this->getDbTable()->fetchAll();
		$users   = array();
		foreach ($resultSet as $user) {
			$role = new Application_Model_DbTable_Roles();
			$userRole = new Application_Model_DbTable_UserRole();
			$roles = $user->findManyToManyRowset($role, $userRole);
			$users[] = $user; 
		}
		return $users;
	}
	
	public function fetchAllJson()
	{
		$resultSet = $this->getDbTable()->fetchAll();
		$users   = array();
		foreach ($resultSet as $user) {
			$role = new Application_Model_DbTable_Roles();
			$userRole = new Application_Model_DbTable_UserRole();
			$roles = $user->findManyToManyRowset($role, $userRole);
	
			// get current section
			$academicYears = new Application_Model_DbTable_AcademicYears();
			$currentAcademicYear = $academicYears->getCurrentAcademicYear();
			$sectionMapper = new Application_Model_SectionMapper();
			$section = $sectionMapper->getUserCurrentSectionMapId($user->id, $currentAcademicYear['id']);
			$userArray = $user->toArray();
			$userArray['roles'] = $roles->toArray();
			$userArray['p_year'] = $section['p_year_id'];
			$userArray['section'] = $section['section_id'];
			$users[] = $userArray;
		}
		return json_encode($users);
	}
}

