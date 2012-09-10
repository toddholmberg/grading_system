<?php

abstract class Application_Model_MapperAbstract
{
	protected $_dbTable;

	abstract protected function save();

	public function setDbtable()
	{
		if (is_string($dbTable)) {
			$dbTable = new $dbTable();
		}
		if (!$dbTable instanceof Zend_Db_Table_Abstract) {
			throw new Exception('Invalid table data gateway provided');
		}
		$this->_dbTable = $dbTable;
		return $this;
	}

	public function getDbTable($table)
	{
		if (null === $this->_dbTable) {
			$this->setDbTable($table);
		}
		return $this->_dbTable;
	}
}
