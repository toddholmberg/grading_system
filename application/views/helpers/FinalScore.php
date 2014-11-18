<?php

class Zend_View_Helper_FinalScore extends Zend_View_Helper_Abstract
{
	const PREP_SCORE_MAX = 35;
	const PRECISION = 2;


	public function finalScore($facultyAverages, $studentAverages, $attendanceScore, $facPrepAvg, $facProfAvg)
	{
		$seminarMapper = new Application_Model_SeminarMapper();
		return $seminarMapper->finalScore($facultyAverages, $studentAverages, $attendanceScore, $facPrepAvg, $facProfAvg);
	}	
}
