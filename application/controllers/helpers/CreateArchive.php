<?php

class Application_Controller_Action_Helper_CreateArchive extends Zend_Controller_Action_Helper_Abstract
{
	private $_config;
	private $_sectionUsers;
	private $_archiveName;
	private $_error;
	private $_view = null;

	public function direct($sectionUsers, $archiveName)
	{
		$this->_config = Zend_Registry::get('config');

		$this->_view = $view = Zend_Layout::getMvcInstance()->getView();

		$this->_sectionUsers = $sectionUsers;
		$this->_archiveName = $archiveName;

		$archive = new stdClass();

		$archive->filename = $this->_createZipArchive();

		if(isset($this->_error)) {
			$archive->error = $this->_error;
		}
		return $archive;

	}

	private function _createArchive()
	{
		
		try {
			$filename = sprintf('%s/%s.sql.gz', $this->_config->archive->path, $this->_archiveName);
			$zp = gzopen($filename, "w9");
			$data = $this->_dumpDatabase();
			gzwrite($zp, $data);
			gzclose($zp);

		} catch(Exception $e) {
			$this->_error = $e->getMessage();
		}

	}

	 private function _createZipArchive()
	 {
		$zip = new ZipArchive();
		$filename = sprintf('%s/%s.zip', $this->_config->archive->path, $this->_archiveName);

		try {
			if($zip->open($filename, ZipArchive::CREATE) !== TRUE) {
				throw new Exception("Can't open $filename");
			}

			//$reports = $this->_createSeminarFullReports();

			// create top level directory
			$zip->addEmptyDir($this->_archiveName);
				
			$zip->addFromString($this->_archiveName . "/" . $this->_config->resources->db->params->dbname . "_" . date('Ymd') . ".sql", $this->_dumpDatabase());
			$zipFilename = $zip->filename;
			$zip->close();
			return $zipFilename;

		} catch(Exception $e) {
			$this->_error = $e->getMessage();
		}

	 }

	/**
	 * Dump the database. Use PHP rather than shell commands to better insure portability.
	 * Derived from http://wiki.birth-online.de/snippets/php/dump-mysql-db
	 * @return string Database dump.
	 */
	private function _dumpDatabase()
	{
		try {
		    $db = Zend_Db_Table::getDefaultAdapter();

		 	$sql = '-- Full data dump of ' . $this->_config->resources->db->params->dbname . ' database. ' . PHP_EOL;
		 	$sql .= '-- ' . date('Y-m-d H:i:s');
		    $tables = $db->query( 'SHOW TABLES' );

		    // Drop the database if exists;
		    // $sql .= '-- DROP/CREATE database' . PHP_EOL;
		    // $sql .= 'DROP DATABASE IF EXISTS ' . $this->_config->resources->db->params->dbname . ';' . PHP_EOL;
		    // $sql .= 'CREATE DATABASE ' . $this->_config->resources->db->params->dbname . ';' . PHP_EOL;
		    $sql .= PHP_EOL;
		 	$sql .= PHP_EOL;

		 	// Disable foreign key checks
		 	$sql .= 'SET foreign_key_checks = 0;';

		 	$sql .= PHP_EOL;
			$sql .= PHP_EOL;

		    foreach ( $tables as $table ) {
		    	$tableName = $table['Tables_in_' . $this->_config->resources->db->params->dbname];

		        $create = $db->query( 'SHOW CREATE TABLE `' . $tableName . '`' )->fetch();
		        switch(true) {
		        	case(array_key_exists('Create Table', $create)):
			        	$sql .= '-- TABLE: ' . $tableName . PHP_EOL;
			        	$sql .= 'DROP TABLE IF EXISTS ' . $tableName . ';' . PHP_EOL;
			        	$sql .= $create['Create Table'] . ';' . PHP_EOL;

						$sql .= PHP_EOL;
				 		$sql .= PHP_EOL;

				        $rows = $db->query( 'SELECT * FROM `' . $tableName . '`' );
				        $rows->setFetchMode( PDO::FETCH_ASSOC );
				        foreach ( $rows as $row ) {
				            $row = array_map( array( $db, 'quote' ), $row );
				            $sql .= 'INSERT INTO `' . $tableName . '` (`' . implode( '`, `', array_keys( $row ) ) . '`) VALUES (' . implode( ', ', $row ) . ');' . PHP_EOL;
				        }

		        	break;

		        	case(array_key_exists('Create View', $create)):
		        		$sql .= '-- VIEW: ' . $tableName . PHP_EOL;
			        	$sql .= 'DROP VIEW IF EXISTS ' . $tableName . ';' . PHP_EOL;
			        	$create_view = preg_replace("/CREATE .+ VIEW/s", "CREATE VIEW", $create['Create View'] . ';');
			        	$sql .= $create_view . PHP_EOL;
			        	break;

		        }
		        
		 		
		 		
		 
		        $sql .= PHP_EOL;


		    }

		    
		    $sql .= 'SET foreign_key_checks = 1;';
	
			$sql .= PHP_EOL;

		    //echo "<pre>$sql</pre>";

		    return $sql;
		} catch (Exception $e) {
		    echo 'Damn it! ' . $e->getMessage() . PHP_EOL;
		}
	}

	private function _createSeminarFullReports()
	{

		//$seminarId = $this->params['seminarId'];

		$seminarMapper = new Application_Model_SeminarMapper();

		$surveyMapper = new Application_Model_SurveyMapper();

		//$seminar = $seminarMapper->findById($seminarId);
		$seminars = $seminarMapper->findCurrentSeminars();

		foreach($seminars as $seminar){
			
			$surveys = $surveyMapper->getSeminarSurveys($seminar['seminar_id']);
			
			usort($surveys, array('Application_Controller_Action_Helper_CreateArchive', '_compareReviewerLastNames'));

			$seminar['surveys'] = $surveys;

			$seminar['scores'] = $seminarMapper->getSeminarScores($seminar['seminar_id']);

			$header = '<h3>' . $seminar['presenter_last_name'] . ', ' . $seminar['presenter_first_name'] . '</h3>';

			$header .= '<h4>Seminar Date: ' . $seminar['seminar_date'] . '</h4>';

			$presenterDetails = $this->_view->partial('grading/partials/presenterDetails.phtml', array('data' => $seminar));

			$surveySummaries = $this->_view->partial('grading/partials/reportSurveySummaries.phtml', array('surveys' => $seminar['surveys']));

			$filename = sprintf("%s_%s_%s_full.pdf", trim($seminar['presenter_last_name']), trim($seminar['presenter_first_name']), trim($seminar['seminar_date']));

			$formattedSurveyReport = array();
			for ($count = 0; $count < count($seminar['surveys']); $count++) {
				$formattedSurveyReport[] = $this->_view->surveyDetail()->formatReport($seminar['surveys'][$count], $count, 'full');
			}

			$contents = array(
					'header' => $header,
					'presenterDetails' => $presenterDetails,
					'surveySummaries' => $surveySummaries,
					'formattedSurveyReport' => $formattedSurveyReport
					);

			$reportOutput = $this->_view->partial('grading/partials/reportTemplate.phtml', array('contents' => $contents));

			$this->_view->PDF($reportOutput, $filename);

		}

	}

	private static function _compareReviewerLastNames($a, $b)
	{
		return strnatcmp($a['reviewer']['last_name'], $b['reviewer']['last_name']);
	}




}
