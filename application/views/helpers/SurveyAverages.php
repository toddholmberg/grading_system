<?php

class Zend_View_Helper_SurveyAverages extends Zend_View_Helper_Abstract
{
	public function surveyAverages()
	{
		return $this;
	}

	
	public function averageOne($survey)
	{
		$averages = $this->_averageArray();

		foreach($survey as $key => $value) {
			if((strpos($key, 'ps_') === 0) && !(strpos($key, 'comments') > 0)){
				$averages['ps']['values'][] = $value;
				$averages['ps']['total'] += $value;
				$averages['ps']['qValues'][$key][] = $value;
			}

			if((strpos($key, 'im_') === 0) && !(strpos($key, 'comments') > 0)){
				$averages['im']['values'][] = $value;
				$averages['im']['total'] += $value;
				$averages['im']['qValues'][$key][] = $value;
			}	

			if((strpos($key, 'op_') === 0) && !(strpos($key, 'comments') > 0)){
				$averages['op']['values'][] = $value;
				$averages['op']['total'] += $value;
				$averages['op']['qValues'][$key][] = $value;
			}

			if((strpos($key, 'cd_') === 0) && !(strpos($key, 'comments') > 0)){
				$averages['cd']['values'][] = $value;
				$averages['cd']['total'] += $value;
				$averages['cd']['qValues'][$key][] = $value;
			}


			if((strpos($key, 'cc_') === 0) && !(strpos($key, 'comments') > 0)){
				$averages['cc']['values'][] = $value;
				$averages['cc']['total'] += $value;
				$averages['cc']['qValues'][$key][] = $value;
			}


			if((strpos($key, 'qa_') === 0) && !(strpos($key, 'comments') > 0)){
				$averages['qa']['values'][] = $value;
				$averages['qa']['total'] += $value;
				$averages['qa']['qValues'][$key][] = $value;
			}


			if((strpos($key, 'ok_') === 0) && !(strpos($key, 'comments') > 0)){
				$averages['ok']['values'][] = $value;
				$averages['ok']['total'] += $value;
				$averages['ok']['qValues'][$key][] = $value;
			}

		}

		$averages = $this->_calculateAverages($averages);

		return $averages;
	}


	public function averageAll($surveys, $role_id)
	{
		$averages = $this->_averageArray();
		foreach($surveys as $survey) {
			
			if(isset($role_id) && ($survey['reviewer']['role_id'] != $role_id)) {
				continue;
			}	

			foreach($survey as $key => $value) {
				if((strpos($key, 'ps_') === 0) && !(strpos($key, 'comments') > 0)){
					$averages['ps']['values'][] = $value;
					$averages['ps']['total'] += $value;
					$averages['ps']['qValues'][$key]['values'][] = $value;
					$averages['ps']['qValues'][$key]['counts'] = array_count_values($averages['ps']['qValues'][$key]['values']);
				}

				if((strpos($key, 'im_') === 0) && !(strpos($key, 'comments') > 0)){
					$averages['im']['values'][] = $value;
					$averages['im']['total'] += $value;
					$averages['im']['qValues'][$key]['values'][] = $value;
					$averages['im']['qValues'][$key]['counts'] = array_count_values($averages['im']['qValues'][$key]['values']);
				}	

				if((strpos($key, 'op_') === 0) && !(strpos($key, 'comments') > 0)){
					$averages['op']['values'][] = $value;
					$averages['op']['total'] += $value;
					$averages['op']['qValues'][$key]['values'][] = $value;
					$averages['op']['qValues'][$key]['counts'] = array_count_values($averages['op']['qValues'][$key]['values']);
				}

				if((strpos($key, 'cd_') === 0) && !(strpos($key, 'comments') > 0)){
					$averages['cd']['values'][] = $value;
					$averages['cd']['total'] += $value;
					$averages['cd']['qValues'][$key]['values'][] = $value;
					if($value != 0) {
						$averages['cd']['qValues'][$key]['counts'] = array_count_values($averages['cd']['qValues'][$key]['values']);
					} 
				}


				if((strpos($key, 'cc_') === 0) && !(strpos($key, 'comments') > 0)){
					$averages['cc']['values'][] = $value;
					$averages['cc']['total'] += $value;
					$averages['cc']['qValues'][$key]['values'][] = $value;
					$averages['cc']['qValues'][$key]['counts'] = array_count_values($averages['cc']['qValues'][$key]['values']);
				}


				if((strpos($key, 'qa_') === 0) && !(strpos($key, 'comments') > 0)){
					$averages['qa']['values'][] = $value;
					$averages['qa']['total'] += $value;
					$averages['qa']['qValues'][$key]['values'][] = $value;
					$averages['qa']['qValues'][$key]['counts'] = array_count_values($averages['qa']['qValues'][$key]['values']);
				}


				if((strpos($key, 'ok_') === 0) && !(strpos($key, 'comments') > 0)){
					$averages['ok']['values'][] = $value;
					$averages['ok']['total'] += $value;
					$averages['ok']['qValues'][$key]['values'][] = $value;
					$averages['ok']['qValues'][$key]['counts'] = array_count_values($averages['ok']['qValues'][$key]['values']);
				}

			}
		}
		$averages = $this->_calculateAverages($averages);
		return $averages;
	}

	private function _countQuestionValues($averages)
	{
			
	}

	private function _calculateAverages($averages)
	{
		foreach($averages as $field => $data){
			// adjust count to exclude NA(0) values where NA = true.
			$values = $data['values'];
			foreach($values as $index => $value) {
				if($value == 0) {
					unset($values[$index]);
				}
			}
			// only calculate if count > 0
			if(count($values) > 0) {
				//$averages[$field]['average'] = round(array_sum($data['values'])/count($data['values']), 2);
				$averages[$field]['average'] = round(array_sum($values)/count($values), 2);
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
				'values' => array(),
				'weight' => 0.05,
				'qValues' => array(),
				'NA' => false

			),
			'im' => array(
				'total' => 0,
				'values' => array(),
				'weight' => 0.1,
				'qValues' => array(),
				'NA' => true
			),
			'op' => array(
				'total' => 0,
				'values' => array(),
				'weight' => 0.1,
				'qValues' => array(),
				'NA' => false
			),
			'cd' => array(
				'total' => 0,
				'values' => array(),
				'weight' => 0.2,
				'qValues' => array(),
				'NA' => true
			),
			'cc' => array(
				'total' => 0,
				'values' => array(),
				'weight' => 0.2,
				'qValues' => array(),
				'NA' => false
			),
			'qa' => array(
				'total' => 0,
				'values' => array(),
				'weight' => 0.15,
				'qValues' => array(),
				'NA' => false
			),
			'ok' => array(
				'total' => 0,
				'values' => array(),
				'weight' => 0.2,
				'qValues' => array(),
				'NA' => false
			)
		
		);
		
		return $averageArray;
	}
}
