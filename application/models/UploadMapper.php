<?php

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
		$this->_skipRows = self::SKIPROWS;
	}

	public function parse()
	{
		$uploadData = array();
		$fh = fopen($this->_filename, "r");
		$row = 1;
		while ( ( $data = fgetcsv( $fh, self::LENGTH, self::DELIMITER ) ) !== FALSE ) {
			if($row > 2) {
				$uploadData[] = $data;
			}
			$row++;
		}
		fclose($fh);
		return json_encode($uploadData);

	}

}

