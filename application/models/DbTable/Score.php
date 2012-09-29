<?php

class Application_Model_DbTable_Score extends Zend_Db_Table_Row_Abstract
{

    protected $_name = 'score';
	protected $_tableClass = 'Application_Model_DbTable_Scores';

	protected $_referenceMap = array(
		'Seminar' => array(
			'columns' => array('seminar_id'),
			'refTableClass' => 'Application_Model_DbTable_Seminar',
			'refColumns' => array('id')
		),
		'Grader' => array(
			'columns' => array('grader_user_id'),
			'refTableClass' => 'Application_Model_DbTable_User',
			'refColumns' => array('id')
		)
	);

	protected $_id;
	protected $_seminar_id;
	protected $_grader_user_id;
	protected $_prep;
	protected $_prof;

}

