<?php

class Zend_View_Helper_SurveyAverages extends Zend_View_Helper_Abstract
{
	public function surveyAverages($surveys, $role_id)
	{
		$averages = $this->_averageArray();
		foreach($surveys as $survey) {
			if($survey['reviewer']['role_id'] != $role_id) {
				continue;
			}	

			foreach($survey as $key => $value) {
				if((strpos($key, 'ps_') === 0) && !(strpos($key, 'comments') > 0)){
					$averages['ps']['count']++;
					$averages['ps']['total'] += $value;
				}

				if((strpos($key, 'im_') === 0) && !(strpos($key, 'comments') > 0)){
					$averages['im']['count']++;
					$averages['im']['total'] += $value;
				}	

				if((strpos($key, 'op_') === 0) && !(strpos($key, 'comments') > 0)){
					$averages['op']['count']++;
					$averages['op']['total'] += $value;
				}

				if((strpos($key, 'cd_') === 0) && !(strpos($key, 'comments') > 0)){
					$averages['cd']['count']++;
					$averages['cd']['total'] += $value;
				}


				if((strpos($key, 'cc_') === 0) && !(strpos($key, 'comments') > 0)){
					$averages['cc']['count']++;
					$averages['cc']['total'] += $value;
				}


				if((strpos($key, 'qa_') === 0) && !(strpos($key, 'comments') > 0)){
					$averages['qa']['count']++;
					$averages['qa']['total'] += $value;
				}


				if((strpos($key, 'ok_') === 0) && !(strpos($key, 'comments') > 0)){
					$averages['ok']['count']++;
					$averages['ok']['total'] += $value;
				}

			}
		}

		$averages = $this->_calculateAverages($averages);

		return $averages;
	}

	private function _calculateAverages($averages)
	{
		foreach($averages as $field => $data){
			// only calculate if count != 0
			if($data['count'] != 0) {
				$averages[$field]['average'] = round(($data['total']/$data['count']), 3);
			} else {
				$averages[$field]['average'] = 0;
			}
		}
		return $averages;
	}


	private function _averageArray()
	{
		$averageArray = array(

			'ps' => array(
				'total' => 0,
				'count' => 0
			),
			'im' => array(
				'total' => 0,
				'count' => 0
			),
			'op' => array(
				'total' => 0,
				'count' => 0
			),
			'cd' => array(
				'total' => 0,
				'count' => 0
			),
			'cc' => array(
				'total' => 0,
				'count' => 0
			),
			'qa' => array(
				'total' => 0,
				'count' => 0
			),
			'ok' => array(
				'total' => 0,
				'count' => 0
			)
		
		);
		
		return $averageArray;
	}
}
