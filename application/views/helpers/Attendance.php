<?php

class Zend_View_Helper_Attendance extends Zend_View_Helper_Abstract
{
	public function attendance()
	{
		return $this;		
	}

	public function attended($presenterUserSectionId)
	{
		try{
			$db = Zend_Db_Table::getDefaultAdapter();
			$sql = 'SELECT count(id) FROM survey WHERE reviewer_user_section_id = :presenterUserSectionId';
			$sth = $db->prepare($sql);
			$sth->bindParam(':presenterUserSectionId', $presenterUserSectionId);
			if($sth->execute()) {
				return $sth->rowCount();
			} else {
				echo Zend_Debug::dump($this->errorInfo());
			}	
		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}

	public function score($presenterUserSectionId)
	{
		try {
			// find all surveys where reviewer_user_section_id = $presenterUserSectionId
			$db = Zend_Db_Table::getDefaultAdapter();
			//$sql = 'SELECT count(id) FROM survey WHERE reviewer_user_section_id = :presenterUserSectionId';
			$sql = "SELECT count(*) as survey_count FROM survey WHERE reviewer_user_section_id = $presenterUserSectionId";
//			echo $sql; exit;
			$sth = $db->prepare($sql);
			//$sth->bindParam(':presenterUserSectionId', $presenterUserSectionId);
			if($sth->execute()) {
				$result = $sth->fetch(PDO::FETCH_ASSOC);	
				return $result['survey_count'];
				//return $sth->rowCount();
			} else {
				echo Zend_Debug::dump($this->errorInfo());
			}
		} catch(Exception $e) {
			echo $e->errorMessage();
		}
		
	}
}
