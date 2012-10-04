<?php

class Zend_View_Helper_SurveyDetail extends Zend_View_Helper_Abstract
{
	public function surveyDetail()
	{
		return $this;
	}

	public function format($survey)
	{
		$questions = $this->questions();
		$categories = array('ps' => array(), 'im' => array(), 'op' => array(), 'cd' => array(), 'cc' => array(), 'qa' => array(), 'ok' => array(), 'comments' => array());
		foreach($categories as $k => $v) {
			foreach($survey as $key => $value) {
				$arr = explode("_", $key, 2);
				$field_prefix = $arr[0];
				if($field_prefix == $k) {
					$categories[$k][] = $value;
				}
			}
		}	

		$text = '';
		foreach($questions as $q_prefix => $q) {
			$rows = array();
			$text .= '<h4>' . $q['title'] . '</h4>';
			for ($i = 0; $i < count($q['questions']); $i++) {
				if(!empty($categories[$q_prefix][$i])) {
					$rows[$i] = '<tr><td style="width: 50%">' . $q['questions'][$i] . '</td><td>' . $categories[$q_prefix][$i]  . '</td></tr>';
				}
			}
			$text .= "<table class='table table-bordered'>" . implode(' ', $rows) . "</table>";
		}
		return $text;
	}

	public function questions()
	{
		$questions = array(
				"ps" => array(	
				"title" => "Presentation Style",
				"questions" => array(
					"Moderate Pace",
					"Thorough eye contact/ minimal reliance on notes ",
					"Displayed professionalism/ poise/ confidence/ lacked distracting mannerisms",
					"Material presented at the appropriate level for the audience",
					"Additional comments regarding presentation style:"
					)	
				),

				"im" => array(
				"title" => "Instructional Materials", 
				"questions" => array(
					"Slides and handout were clear/easy to read",
					"Slides and handout  are devoid of spelling and grammatical errors",
					"Provided orientation to charts/graphs/pictures/diagrams (if applicable)",
					"Cites appropriate references/correct referencing style and emphasizes primary literature",
					"Additional comments regarding instructional materials:"
					)
				),

				"op" => array(
				"title" => "Overall Presentation Content",
				"questions" => array(
					"Introduction, interest in topic, and outline/objectives described",
					"Defines purpose/controversy of seminar topic clearly",
					"Objectives clear and useful for self assessment ",
					"Appropriate background information was provided ",
					"Well organized presentations and smooth transitions (appropriate 'flow')",
					"Additional comments regarding overall presentation content:"
					)
				),

				"cd" =>  array(
				"title" => "Presentation of Clinical Data",
				"questions" => array(
						"Presented concise objectives, methodology and treatment for each study",
						"Outcome measures were stated and described, and appropriateness was explained",
						"Presented key trial results with corresponding statistical analysis",
						"Student is able to determine if sample size and power is appropriate (if applicable)",
						"Withdrawals and dropouts are accounted for (if applicable)",
						"Provided a detailed & thoughtful analysis of study strengths and limitations",
						"Additional comments regarding presentation of clinical data:",
					)		
				),

				"cc" => array(
				"title" => "Conclusions",
				"questions" => array(
						"Conclusions are supported by data presented in the seminar",
						"Clinical importance and application of the study is discussed",
						"Provided specific recommendations for clinical pharmacy practice",
						"Discussed the role of the pharmacist and/or impact to the profession of pharmacy in regards to the use of the treatment",
						"Additional comments regarding conclusions:"
						)
					),

				"qa" => array(
				"title" => "Question Answer Session",
				"questions" => array(
						"Succinctly, yet thoroughly answered audience questions",
						"Encouraged questions and interaction with the audience",
						"Additional comments regarding the question answer session:"
					)
				),

				"ok" => array(
				"title" => "Overall Knowledge Base",
				"questions" => array(
						"Demonstrated knowledge of subject beyond the facts presented in the seminar",
						"Student is able to distinguish the difference between clinical and statistical significance",
						"Student is able to look beyond the author's conclusions and offer insight into the overall study results",
						"Student is able to discuss conclusions in the context of previous research and in comparison to current practice/therapy",
						"Student is able to think on his/her feet. May theorize if not sure of answer, but identifies answer as such",
						"Additional comments regarding overall knowledge base:"
					)
				),

				"comments" => array(
				"title" => "Comments",
				"questions" => array(
						"Provide one comment on what you liked about this seminar",
						"Provide one comment on what could be improved about this seminar",
						"General Comments"
						)
				)
			);

		return $questions;
	}

}
