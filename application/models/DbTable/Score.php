<?php

class Application_Model_DbTable_Score extends Zend_Db_Table_Abstract
{

    protected $_name = 'score';
	protected $_tableClass = 'Application_Model_DbTable_Scores';

	protected $_referenceMap = array(
		'Seminar' => array(
			'columns' => array('seminar_id'),
			'refTableClass' => 'Application_Model_DbTable_Seminar',
			'refColumns' => array('id')
		)
	);

	protected $_id;
	protected $_seminar_id;
	protected $_presentation;
	protected $_professionalism;
	protected $_attendance;

}

