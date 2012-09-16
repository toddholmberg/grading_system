<?php

class Application_Model_SurveyMapper
{
	const LENGTH = 10000;
	const DELIMITER = ",";
	const SKIPROWS = 2;

	private $_filename;
	private $_skipRows;

	public function __construct($filename, $skipRows = null)
	{
		$this->_filename = (string) $filename;
		$this->_skipRows = self::SKIPROWS;
	}

	public function parse()
	{
		$surveyData = array();
		$fh = fopen($this->_filename, "r");
		$row = 1;
		while ( ( $data = fgetcsv( $fh, self::LENGTH, self::DELIMITER ) ) !== FALSE ) {
			if($row > 2) {
				$surveyData[] = $this->_mapRow($data);	
			}
			$row++;
		}
		fclose($fh);
		return json_encode($surveyData);

	}

	private function _mapRow($row)
	{
		$mappedRow = array();
		for ($i = 0; $i < count($row); $i++) {
			//$row[$i] = addslashes($row[$i]);
			switch(true) {
				case($i == 0):
					$mappedRow['qualtricsId'] = $row[$i];
					break;
				case($i == 2):
					$mappedRow['reviewerName'] = $row[$i];
					break;
				case($i == 3):
					$mappedRow['reviewerUnid'] = $row[$i];
					break;
				case($i == 5):
					$mappedRow['reviewerEmail'] = $row[$i];
					break;
				case($i == 8):
					$mappedRow['surveyDate'] = $row[$i];
					break;
				case($i == 15):
					$mappedRow['presenter'] = $row[$i];
					break;
				case(in_array($i, range(16,20))):
					$mappedRow['q1'][] = $row[$i];
					break;
				case(in_array($i, range(21,25))):
					$mappedRow['q2'][] = $row[$i];
					break;
				case(in_array($i, range(26,31))):
					$mappedRow['q3'][] = $row[$i];
					break;

				case(in_array($i, range(38,32))):
					$mappedRow['q4'][] = $row[$i];
					break;

				case(in_array($i, range(39,43))):
					$mappedRow['q5'][] = $row[$i];
					break;

				case(in_array($i, range(44,46))):
					$mappedRow['q6'][] = $row[$i];
					break;

				case(in_array($i, range(47,52))):
					$mappedRow['q7'][] = $row[$i];
					break;
				case($i == 54):
					$mappedRow['q8'] = $row[$i];
					break;	
				case($i == 55):
					$mappedRow['q9'] = $row[$i];
					break;
				case($i == 56):
					$mappedRow['q10'] = $row[$i];
					break;
			}
		}
		return $mappedRow;
	}

}
