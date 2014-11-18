<?php

class Application_Model_UserMapper
{

	protected $_dbTable;

	private $_uploadData;

	private $_users;

	private $_errors;

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

	public function saveUpload($data, $year_id)
	{
		$this->_uploadData = json_decode($data);

		$this->_parse();

		//Zend_Debug::dump($this->_users);

		$errors = array();

		foreach($this->_users as $user) {

			// Skip user if entire array is empty
			if($this->_userIsEmpty($user)) {
				continue;
			}
			
			// Add error data to error array if user is invalid.
			$error = $this->_validateUser($user);
			if (!empty($error)) {
				$this->_errors[] = $error;
				continue;
			} else {
				$this->save($user);
				//$this->_errors[] = $error;
			}
		}
		//error_log(print_r($this->_errors, true));

	}

	private function _userIsEmpty($user)
	{
		$notEmpty = 0;
		foreach($user as $key=>$value) {
			if(isset($value) && $value != '') {
				$notEmpty = 1;
			}
		}
		
		if($notEmpty == 0) {
			return true;
		}

	}

	private function _validateUser($user)
	{

		// check if user already exists
		$error = $this->_checkForExistingUser($user);
		if(!empty($error)) {
			return $error;
		}

		// check that all required data is present
		$error = $this->_validateUserData($user);
		if(!empty($error)) {
			return $error;
		}
	}

	private function _validateUserData($user)
	{
		$missingData = array();
		foreach($user as $key=>$value) {
			if(!isset($value)) {
				$missingData[] = $key;
			}
		}
		if(!empty($missingData)) {
			return array(
				'error_type' => 'User data missing',
				'fields' => implode(', ', $missingData),
				'data' => $user
			);
		}
	}

	private function _checkForExistingUser($userData)
	{
		$existingUser = $this->findUserByUnid($userData['unid']);
		if($existingUser['unid'] == $userData['unid']) {
			return array(
				'error_type' => 'User already exists',
				'unid' => $existingUser['unid'],
				'last_name' => $existingUser['last_name'],
				'first_name' => $existingUser['first_name']
			);
		}

	}

	private function _parse()
	{
		$userData = array();
		// get rid of row header if it exists
		if($this->_uploadData[0][0][0] == 'unid') {
			unset($this->_uploadData[0]);
		}

		foreach($this->_uploadData as $row) {
			$this->_users[] = $this->_mapRow($row[0]);
		}	
	}

	public function getErrors()
	{
		return $this->_errors;
	}

	private function _mapRow($row)
	{
		$mappedRow = array();
		for($i=0; $i<count($row); $i++) {
			switch(true) {
				// unid
				case($i == 0):
					if(isset($row[$i])) {
						$mappedRow['unid']=$row[$i];
					} else {
						$mappedRow['unid']=null;
					}
					break;

				// first name
				case($i == 1):
					if(isset($row[$i])) {
						$mappedRow['first_name']=$row[$i];
					} else {
						$mappedRow['first_name']=null;
					}
					break;

				// last name
				case($i == 2):
					if(isset($row[$i])) {
						$mappedRow['last_name']=$row[$i];
					} else {
						$mappedRow['last_name']=null;
					}
					break;

				// email
				case($i == 3):
					if(isset($row[$i])) {
						$mappedRow['email']=$row[$i];
					} else {
						$mappedRow['email']=null;
					}					
					break;

				// role
				case($i == 4):
					if(isset($row[$i])) {
						$roleMapper = new Application_Model_RoleMapper();
						$role = $roleMapper->findByTitle($row[$i]);
						if(strtolower($row[$i]) == strtolower($role['title'])) {	
							$mappedRow['role_id']=$role['id'];
						}
					} else {
						$mappedRow['role_id']=null;
					}
					break;

				// p-year
				case($i == 5):
					if(isset($row[$i])) {
						$pYearMapper = new Application_Model_PYearMapper();
						$pYear = $pYearMapper->findByPYearNumber($row[$i]);
						$mappedRow['p_year_id']=$pYear['id'];
					} else {
						$mappedRow['p_year_id']=null;
					}
					break;

				// section
				case($i == 6):
					if(isset($row[$i])) {
						$sectionMapper = new Application_Model_SectionMapper();
						$section = $sectionMapper->findSectionDataByNumber($row[$i]);
						$mappedRow['section_id']=$row[$i];
					} else {
						$mappedRow['section_id']=null;
					}
					break;

				// is_grader
				case($i == 7):
					switch($row[$i]){
						case 'n':
						case 'N':
							$mappedRow['is_grader'] = 0;
							break;

						case 'y':
						case 'Y':
							$mappedRow['is_grader'] = 1;
							break;
						default:
							$mappedRow['is_grader'] = null;
					}
					break;
			}
		}
		return $mappedRow;
	}

	public function save($data)
	{
		// user vital data
		$userData = array(
				'unid'   => $data['unid'],
				'first_name' => $data['first_name'],
				'last_name' => $data['last_name'],
				'email'   => $data['email'],
				'archive'   => empty($data['archive'])? 0 : $data['archive'],
				'created_date' => date('Y-m-d H:i:s')
				);

		if(isset($data['id'])) {
			$userData['id'] = $data['id'];
		}

		// role id
		$roleData = array(
			'role_id' => $data['role_id']
		);

		$sectionData = array();

		// section id
		if(!empty($data['section_id']) && ($data['role_id'] != 1)) {
			$sectionData = array(
					'p_year_id'=> !empty($data['p_year_id']) ? $data['p_year_id'] : null,
					'section_id' => $data['section_id'],
					'is_grader' => $data['is_grader']
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
				if (!empty($sectionData)) {
				    $updatedUser = $this->update($userData, $roleData, $sectionData);
				} else {
				    $updatedUser = $this->update($userData, $roleData);

				}
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

			// map the section details
			if(!empty($sectionData)) {
				$sectionMapper = new Application_Model_SectionMapper();
				$sectionToBeMapped = $sectionMapper->findAcademicYearPYearSectionId(
						$currentAcademicYear['id'],
						$sectionData['p_year_id'],
						$sectionData['section_id']
						);			
				$sectionMapper->saveUserSection(
					$user_id, 
					$sectionToBeMapped['id'], 
					$sectionData['is_grader'],
					$roleData['role_id']
				);	
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

			//To-Do: allow updated user to add new role
			$currentRole = $userRole->fetchRow($where);

			if($currentRole->role_id !== $roleData['role_id']) {
				$currentRole->role_id = $roleData['role_id'];
				$UserRoleId = $currentRole->save();
			}

			// update section map
			if(!empty($sectionData)) {
				$sectionMapper = new Application_Model_SectionMapper();
				$sectionsToBeMapped = $sectionMapper->findSectionIds(
						$currentAcademicYear['id'],
						$sectionData['p_year_id'],
						$sectionData['section_id'],
						$roleData['role_id']
						);			

				$sectionMapper->updateUserSection(
						$userData['id'],
						$sectionsToBeMapped,
						$sectionData['is_grader'],
						$currentAcademicYear['id']	
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
			if(!empty($roles)) {
				$user['role_id'] = $roles[0]['id'];	
			}

			// get current section
			$academicYears = new Application_Model_DbTable_AcademicYears();
			$currentAcademicYear = $academicYears->getCurrentAcademicYear();
			$sectionMapper = new Application_Model_SectionMapper();
			$section = $sectionMapper->getUserCurrentSectionMapId($user_id, $currentAcademicYear['id']);

			if(!empty($section)) {
				$user['p_year_id'] = $section['p_year_id'];
				$user['section_id'] = $section['section_id'];
		
				$userSectionData = $sectionMapper->getUserSectionData($user_id);
				$user['is_grader'] = $userSectionData['is_grader'];
			}
		
			return $user;
		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		}

	}	


	public function getGraders($sectionNumber, $academicYear)
	{

		try {
			$db = $this->getDbTable()->getDefaultAdapter();
			$sql = 'select * from user 
				left join user__section us on user.id = us.user_id 
				left join academic_year__p_year__section aps on aps.id = us.section_id 
				left join academic_year ay on aps.academic_year_id = ay.id 
				left join section on aps.section_id = section.id 
				left join user__role ur on ur.user_id = user.id 
				where section.number = :sectionNumber 
				and ur.role_id = 3 
				and us.is_grader = 1 
				and ay.current = 1';

			$sth = $db->prepare($sql);
			$sth->bindParam(':sectionNumber', $sectionNumber);
			if($sth->execute()) {
				$results = $sth->fetchAll(PDO::FETCH_ASSOC);
				return $results;
			} else {
				echo $sth->error_Info();
				throw new Exception($sth->errorInfo());
			}		


		} catch (Exception $e) {
			throw new Exception(__CLASS__ . "::"  . __FUNCTION__ . ": " . $e->getMessage());
		}	
	}




	public function getFacultyByPYearSectionIdAcademicYear($pyearName, $sectionNumber, $academicYear)
	{

		// select * from user left join user__section us on user.id = us.user_id left join academic_year__p_year__section aps on aps.id = us.section_id left join p_year on aps.p_year_id = p_year.id left join section on aps.section_id = section.id left join user__role ur on ur.user_id = user.id where section.number = 1 and p_year.p = 3 and ur.role_id = 3;

		try {
			$db = $this->getDbTable()->getDefaultAdapter();
			$sql = 'select * from user left join user__section us on user.id = us.user_id left join academic_year__p_year__section aps on aps.id = us.section_id  left join academic_year ay on aps.academic_year_id = ay.id left join p_year on aps.p_year_id = p_year.id left join section on aps.section_id = section.id left join user__role ur on ur.user_id = user.id where section.number = :sectionNumber and p_year.p = :pyearName and ur.role_id = 3 and us.is_grader = 1 and ay.current = 1';

			$sth = $db->prepare($sql);
			$sth->bindParam(':sectionNumber', $sectionNumber);
			$sth->bindParam(':pyearName', $pyearName);
			if($sth->execute()) {
				$results = $sth->fetchAll(PDO::FETCH_ASSOC);
				return $results;
			} else {
				echo $sth->error_Info();
				throw new Exception($sth->errorInfo());
			}		


		} catch (Exception $e) {
			throw new Exception(__CLASS__ . "::"  . __FUNCTION__ . ": " . $e->getMessage());
		}	
	}

	public function getFacultyByPYearSectionId($pyearName, $sectionNumber)
	{

		// select * from user left join user__section us on user.id = us.user_id left join academic_year__p_year__section aps on aps.id = us.section_id left join p_year on aps.p_year_id = p_year.id left join section on aps.section_id = section.id left join user__role ur on ur.user_id = user.id where section.number = 1 and p_year.p = 3 and ur.role_id = 3;

		try {
			$db = $this->getDbTable()->getDefaultAdapter();
			$sql = 'select * from user left join user__section us on user.id = us.user_id left join academic_year__p_year__section aps on aps.id = us.section_id left join p_year on aps.p_year_id = p_year.id left join section on aps.section_id = section.id left join user__role ur on ur.user_id = user.id where section.number = :sectionNumber and p_year.p = :pyearName and ur.role_id = 3 and us.is_grader = 1';

			$sth = $db->prepare($sql);
			$sth->bindParam(':sectionNumber', $sectionNumber);
			$sth->bindParam(':pyearName', $pyearName);
			if($sth->execute()) {
				$results = $sth->fetchAll(PDO::FETCH_ASSOC);
				return $results;
			} else {
				echo $sth->error_Info();
				throw new Exception($sth->errorInfo());
			}		


		} catch (Exception $e) {
			throw new Exception(__CLASS__ . "::"  . __FUNCTION__ . ": " . $e->getMessage());
		}	
	}
	
	public function getUsersBySectionId($sectionId)
	{
		try {
			$db = $this->getDbTable()->getDefaultAdapter();
			$sql = 'select user.id,
			user.unid, 
			user.last_name, 
			user.first_name, 
			user__section.id
			from user__section 
			left join user on user__section.user_id = user.id 
			where user__section.section_id = :sectionId
			order by user.last_name asc, user.first_name asc';

			$sth = $db->prepare($sql);
			$sth->bindParam(':sectionId', $sectionId);
			if($sth->execute()) {
				$result = $sth->fetchAll(PDO::FETCH_ASSOC);
				return $result;
			} else {
				echo $sth->error_Info();
				throw new Exception($sth->errorInfo());
			}		


		} catch (Exception $e) {
			throw new Exception(__CLASS__ . "::"  . __FUNCTION__ . ": " . $e->getMessage());
		}	
	}

	public function getUsersBySectionIdAndRoleId($sectionId, $roleId)
	{
		try {
			$db = $this->getDbTable()->getDefaultAdapter();
			$sql = 'select user.id,
			user.unid, 
			user.last_name, 
			user.first_name, 
			user__section.id
			from user__section 
			left join user on user__section.user_id = user.id
			left join user__role on user__role.user_id = user.id 
			where user__section.section_id = :sectionId
			and user__role.role_id = :roleId
			order by user.last_name asc, user.first_name asc';

			$sth = $db->prepare($sql);
			$sth->bindParam(':sectionId', $sectionId);
			$sth->bindParam(':roleId', $roleId);
			if($sth->execute()) {
				$result = $sth->fetchAll(PDO::FETCH_ASSOC);
				return $result;
			} else {
				echo $sth->error_Info();
				throw new Exception($sth->errorInfo());
			}		


		} catch (Exception $e) {
			throw new Exception(__CLASS__ . "::"  . __FUNCTION__ . ": " . $e->getMessage());
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
			throw new Exception(__CLASS__ . "::"  . __FUNCTION__ . ": " . $e->getMessage());
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

	public function findUserByEmail($email)
	{
		try {
			$table = $this->getDbTable();
			$select = $table->select()->where('email = ?',$email);
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

