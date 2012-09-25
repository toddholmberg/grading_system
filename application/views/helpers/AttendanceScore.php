<?php

class Zend_View_Helper_AttendanceScore extends Zend_View_Helper_Abstract
{
	public function attendanceScore($presenterUserSectionId)
	{
		try {
			// find all surveys where reviewer_user_section_id = $presenterUserSectionId
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
			echo $e->errorMessage();
		}
		
	}
}
