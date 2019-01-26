<?php
class Product
{
	private $productId;
	private $name;
	private $productDescription;
	private $price;
	private $customerId;

	function __construct()
	{
		$this->productId = null;
		$this->name = null;
		$this->productDescription = null;
		$this->price = null;
		$this->customerId = null;
	}

	function getProductByCustomer()
	{
		if($this->customerId > 0 && $this->productId > 0)
		{
			include_once($_SERVER['DOCUMENT_ROOT'] . '/ext/in/db.php');

			$data = new dbAppAlex();
			$data->setQueryString('SELECT product_id FROM customer_product WHERE customer_id = ? AND product_id = ?');
			$data->setQueryVars(array($this->customerId, $this->productId));
			$temp = $data->executeQuery();
			return isset($temp[0]['product_id']) ? $temp[0]['product_id'] : '0';
		}
	}
	
	function getProductById()
	{
		if($this->productId > 0)
		{
			include_once($_SERVER['DOCUMENT_ROOT'] . '/ext/in/db.php');

			$data = new dbAppAlex();
			$data->setQueryString('SELECT product_id, name, product_description, price FROM product WHERE product_id = ?');
			$data->setQueryVars(array($this->productId));
			$temp = $data->executeQuery();
			$this->productId = isset($temp[0]['product_id']) ? $temp[0]['product_id'] : null;
			$this->name = isset($temp[0]['name']) ? $temp[0]['name'] : null;
			$this->productDescription = isset($temp[0]['product_description']) ? $temp[0]['product_description'] : null;
			$this->price = isset($temp[0]['price']) ? $temp[0]['price'] : null;
		}
	}

	function getAllProducts()
	{
		include_once($_SERVER['DOCUMENT_ROOT'] . '/ext/in/db.php');

		$data = new dbAppAlex();
		$data->setQueryString('SELECT p.product_id, p.name, p.product_description, p.price FROM product p');
		return $data->executeQuery();
	}

	function setProductId($productId)
	{
		if(is_numeric($productId) && $productId > 0)
		{
			$this->productId = $productId;
		}
		else
		{
			$this->productId = null;
		}
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
	
	function getProductDescription(){return $this->productDescription;}
	function getPrice(){return $this->price;}
}
?>