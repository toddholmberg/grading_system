<?php

include 'PHPExcel/IOFactory.php';

class Application_Model_UploadMapper
{
	const LENGTH = 10000;
	const DELIMITER = ",";
	const SKIPROWS = 2;

	private $_filename;
	private $_skipRows;

	public function __construct($filename, $skipRows = null)
	{
		$this->_filename = (string) $filename;
		$this->_skipRows = !empty($skipRows) ? $skipRows : self::SKIPROWS;
	}


	public function parse($skipHeader = self::SKIPROWS)
	{
		try {
			$inputFileType = PHPExcel_IOFactory::identify($this->_filename);
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			$objPHPExcel = $objReader->load($this->_filename);
			//  Get worksheet dimensions
			$sheet = $objPHPExcel->getSheet(0); 
			$highestRow = $sheet->getHighestRow(); 
			$highestColumn = $sheet->getHighestColumn();
			
			$data = array();

			$firstDataRow = $skipHeader + 1;

			//error_log("FIRST DATA ROW: " . $firstDataRow);

			//  Loop through each row of the worksheet in turn
			for ($row = $firstDataRow; $row <= $highestRow; $row++){ 
				//  Read a row of data into an array
				$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
						NULL,
						TRUE,
						FALSE);
				//  Insert row data array into your database of choice here
				if(!empty($rowData)) {
					$data[] = $rowData;	
				}
				
			}	

			return json_encode($data);
		} catch(Exception $e) {
			die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
		}

	}



	public function parse_old()
	{
		$uploadData = array();
		$fh = fopen($this->_filename, "r");
		$row = 1;
		while ( ( $data = fgetcsv( $fh, self::LENGTH, self::DELIMITER ) ) !== FALSE ) {
			echo $row;
			if($row > $this->_skipRows) {
				$uploadData[] = $data;
			}
			$row++;
		}
		fclose($fh);
		Zend_Debug::dump($uploadData);
		return json_encode($uploadData);

	}

}

