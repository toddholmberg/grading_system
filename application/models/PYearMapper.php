<?php

class Application_Model_PYearMapper extends Application_Model_MapperAbstract
{
	public function save()
	{
	}

	public function findByPYearNumber($pyear) {
		$table = new Application_Model_DbTable_PYears();
		$where = array('p = ?' => $pyear);
		$row = $table->fetchRow($where);
		if(!empty($row)) {
			return $row->toArray();
		} else {
			return false;
		}
	}

}

