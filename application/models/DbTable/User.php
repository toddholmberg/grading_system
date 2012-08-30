<?php

class Application_Model_DbTable_User extends Zend_Db_Table_Abstract
{

	protected $_id;
	protected $_unid;
	protected $_first_name;
	protected $_last_name;
	protected $_created_date;

	public function __construct(array $options = null)
	{
		if (is_array($options)) {
			$this->setOptions($options);
		}
	}

	public function __set($name, $value)
	{
		$method = 'set' . $name;
		if (('mapper' == $name) || !method_exists($this, $method)) {
			throw new Exception('Invalid user property');
		}
		$this->$method($value);
	}

	public function __get($name)
	{
		$method = 'get' . $name;
		if (('mapper' == $name) || !method_exists($this, $method)) {
			throw new Exception('Invalid user property');
		}
		return $this->$method();
	}

	public function setOptions(array $options)
	{
		$methods = get_class_methods($this);
		foreach ($options as $key => $value) {
			$method = 'set' . ucfirst($key);
			if (in_array($method, $methods)) {
				$this->$method($value);
			}
		}
		return $this;
	}

	public function setId($id)
	{
		$this->_id = (int) $id;
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function setUnid($unid)
	{
		$this->_unid = (int) $unid;
		return $this;
	}

	public function getUnid()
	{
		return $this->_unid;
	}

	public function setFirstName($first_name)
	{
		$this->_first_name = (int) $first_name;
		return $this;
	}

	public function getFirstName()
	{
		return $this->_first_name;
	}

	public function setLastName($last_name)
	{
		$this->_last_name = (int) $last_name;
		return $this;
	}

	public function getLastName()
	{
		return $this->_last_name;
	}

	public function setCreatedDate($ts)
	{
		$this->_created_date = $ts;
		return $this;
	}

	public function getCreatedDate()
	{
		return $this->_created_date;
	}


}

