<?php

class Zend_View_Helper_FinalScore extends Zend_View_Helper_Abstract
{
	public function finalScore($facultyAverages, $studentAverages, $attendanceScore, $facPrepAvg, $facProfAvg)
	{
		$presRawScore = $this->_presRawScore($facultyAverages, $studentAverages);
		$presScore = $this->_presScore($presRawScore);
	
		return round((0.5 * $presScore) + (0.35 * $facPrepAvg) + (0.1 * $attendanceScore) + (0.05 * $facProfAvg), 2);
	}	

	private function _presRawScore($facultyAverages, $studentAverages)
	{
		$facWeightedAvg = 0;
		foreach($facultyAverages as $field) {
			$facWeightedAvg += $field['weight'] * $field['average'];	
		}
		
		$studWeightedAvg = 0;
		foreach($studentAverages as $field) {
			$studWeightedAvg += $field['weight'] * $field['average'];	
		}

		$prs = (0.75 * $facWeightedAvg) + (0.25 * $studWeightedAvg);

		return $prs;
	}

	private function _presScore($prs)
	{
		switch(true) {
			case($prs >= 6.5):
				return (93 + (7 * ($prs - 6.5)));
				break;

			case(($prs < 6.5) && ($prs >= 5.5)):
				return (90 + (3 * ($prs - 5.5)));
				break;	

			case(($prs < 5.5) && ($prs >= 4.5)):
				return (87 + (3 * ($prs - 4.5)));
				break;		

			case(($prs < 4.5) && ($prs >= 3.5)):
				return (83 + (4 * ($prs - 3.5)));
				break;

			case(($prs < 3.5) && ($prs >= 2.5)):
				return (80 + (3 * ($prs - 2.5)));
				break;

			case(($prs < 2.5) && ($prs >= 1.5)):
				return (77 + (3 * ($prs - 1.5)));
				break;

			case(($prs < 1.5) && ($prs >= 0.5)):
				return (73 + (4 * ($prs - 0.5)));
				break;
		}
	}

}
