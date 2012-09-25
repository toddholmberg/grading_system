<?php

class Application_Model_SeminarMapper
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
			$this->setDbTable('Application_Model_DbTable_Seminars');
		}
		return $this->_dbTable;
	}

	public function save($data)
	{
		$seminarData = array(
				'id' => isset($data['id']) ? $data['id'] : null,
				'date'   => $data['seminarDate'],
				'presenter_user_section_id' => $data['userSectionId']
				);

		if (empty($seminarData['id'])) {
			// insert seminar
			try {
				$newSeminar = $this->insert($seminarData);
				return $newSeminar;
			} catch(Exception $e) {
				return array('error' => array('message'=> $e->getMessage()));
			}
		} else { 
			// update update	
			throw new Exception('Seminars are not yet editable');
		}
	}


	public function insert($seminarData)
	{
		try {
			// drop 'id' field from seminarData array
			unset($seminarData['id']);

			// begin transaction
			$this->getDbTable()->getDefaultAdapter()->beginTransaction();	

			// insert new user
			$seminar = new Application_Model_DbTable_Seminars();
			$seminarId = $seminar->insert($seminarData);

			$this->getDbTable()->getDefaultAdapter()->commit();

			$newSeminar = $seminar->find($seminarId);
					
			return $newSeminar;
	
		} catch(Exception $e) {
			$this->getDbTable()->getDefaultAdapter()->rollback();
			throw new Exception($e->getMessage());
		}
	}

	public function find($seminarId)
	{
		try {
			$result = $this->getDbTable()->find($seminarId);
			if (0 == count($result)) {
				throw new Exception("Seminar with id $seminarId does not exist.");
			}
			$seminar = $result->current();
			return $seminar;
		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		}
	}	

	public function findByDateAndUserSectionId($date, $userSectionId)
	{
		$table = $this->getDbTable();
		$select = $table->select()
			->where('date = ?', $date)
			->where('presenter_user_section_id = ?', $userSectionId);
		$row = $table->fetchRow($select);

		if (!empty($row)) {
			return $row;
		} else {
			return false;
		}
	}

	public function fetchAll()
	{
		$resultSet = $this->getDbTable()->fetchAll();
		return $resultSet->toArray();
	}

	public function findCurrentSeminars($pYearId = null, $sectionId = null)
	{
		// get current academic year
		try {
			$academicYears = new Application_Model_DbTable_AcademicYears();
			$currentAcademicYear = $academicYears->getCurrentAcademicYear();
			$db = Zend_Db_Table::getDefaultAdapter();
			$sql = 'select s.id as seminar_id, s.date as seminar_date, s.presenter_user_section_id, us.user_id as presenter_user_id, user.unid as presenter_unid, user.last_name as presenter_last_name, user.first_name as presenter_first_name from seminar s left join user__section us on s.presenter_user_section_id = us.id left join academic_year__p_year__section aps on aps.id = us.section_id left join user on us.user_id = user.id where aps.academic_year_id = :academic_year_id';
			if(isset($pYearId) && isset($sectionId)) {
				$sql .= ' and aps.p_year_id = :pyear and aps.section_id = :section';
			}

			$sth = $db->prepare($sql);
			$sth->bindParam(':academic_year_id', $currentAcademicYear['id']);
			if(isset($pYearId) && isset($sectionId)) {
				$sth->bindParam(':pyear', $pYearId);
				$sth->bindParam(':section', $sectionId);
			}
			if($sth->execute()) {
				$result = $sth->fetchAll(PDO::FETCH_ASSOC);
				return $result;
			} else {
				throw new Exception($sth->errorInfo());
			}
		} catch(Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	
}

