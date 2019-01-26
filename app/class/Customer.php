<?php
class Customer
{
	private $customerId;
	private $name;
	private $email;

	function __construct()
	{
		$this->customerId = null;
		$this->name = null;
		$this->email = null;
	}

	function getCustomerById()
	{
		if($this->customerId > 0)
		{
			include_once($_SERVER['DOCUMENT_ROOT'] . '/ext/in/db.php');

			$data = new dbAppAlex();
			$data->setQueryString('SELECT customer_id, name, email FROM customer WHERE customer_id = ?');
			$data->setQueryVars(array($this->customerId));
			$customerData = $data->executeQuery();

			$this->name = $customerData[0]['name'];
			$this->email = $customerData[0]['email'];
		}
	}

	function getCustomerAll()
	{
		include_once($_SERVER['DOCUMENT_ROOT'] . '/ext/in/db.php');

		$data = new dbAppAlex();
		$data->setQueryString('SELECT * FROM customer ORDER BY name ASC');
		$data->setQueryVars(array());
		return $data->executeQuery();
	}

	function setCustomerId($customerId)
	{
		if(is_numeric($customerId) && $customerId > 0)
		{
			$this->customerId = $customerId;
		}
		else
		{
			$this->customerId = null;
		}
	}

	function getName(){return $this->name;}
	function getEmail(){return $this->email;}
}
?>