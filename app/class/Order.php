<?php
class Order
{
	private $orderId;
	private $customerId;
	private $deliveryAddress;
	private $creationDate;
	private $productId;
	private $quantity;
	private $total;
	private $dateSince;
	private $dateTo;

	function __construct()
	{
		$this->orderId = null;
		$this->customerId = null;
		$this->deliveryAddress = '';
		$this->creationDate = date("Ymd");
		$this->productId = null;
		$this->quantity = null;
		$this->total = null;
		$this->dateSince = null;
		$this->dateTo = null;
	}
	
	function getOrderById()
	{
		if($this->orderId > 0)
		{
			include_once($_SERVER['DOCUMENT_ROOT'] . '/ext/in/db.php');

			$data = new dbAppAlex();
			$data->setQueryString('SELECT order_id, customer_id, creation_date, total FROM `order` WHERE order_id = ?');
			$data->setQueryVars(array($this->orderId));
			$temp = $data->executeQuery();
			$this->orderId = isset($temp[0]['product_id']) ? $temp[0]['product_id'] : null;
			$this->customerId = isset($temp[0]['name']) ? $temp[0]['name'] : null;
			$this->creationDate = isset($temp[0]['product_description']) ? $temp[0]['product_description'] : null;
			$this->total = isset($temp[0]['total']) ? $temp[0]['total'] : null;
		}
	}

	function addOrder()
	{
		if($this->customerId > 0 && $this->productId > 0)
		{
			include_once($_SERVER['DOCUMENT_ROOT'] . '/ext/in/db.php');
			include_once($_SERVER['DOCUMENT_ROOT'] . '/app/class/Product.php');
			
			/*check for product availability*/
			$CP = new Product();
			$CP->setCustomerId($this->customerId);
			$CP->setProductId($this->productId);
			$isAvailable = $CP->getProductByCustomer();
			
			if($isAvailable == '0')
			{
				return array(0 => array('response' => 'product not available'));
			}

			/* check for 5 products*/
			$data = new dbAppAlex();
			$data->setQueryString('SELECT r.order_id, COUNT(od.order_id) AS details FROM `order` r LEFT JOIN order_detail od ON r.order_id = od.order_id WHERE r.customer_id = ? AND r.creation_date = \'' . $this->creationDate . '\' GROUP BY r.order_id');
			$data->setQueryVars( array($this->customerId) );
			$temp = $data->executeQuery('order');

			$this->orderId = isset($temp[0]['order_id']) ? $temp[0]['order_id'] : 0;
			$productDay = isset($temp[0]['details']) ? $temp[0]['details'] : 0;

			unset($data);

			if($productDay >= 5)
			{
				return array(0 => array('response' => 'you already have 5 products'));
			}
			
			$P = new Product();
			$P->setProductId($this->productId);
			$P->getProductById();
			$productPrice = $P->getPrice();
			$productDescription = $P->getProductDescription();

			if($this->orderId == 0)
			{
				$data = new dbAppAlex();
				$data->setQueryString('INSERT INTO `order` (customer_id, creation_date, total) VALUES (?, ?, ?)');
				$data->setQueryVars( array($this->customerId, $this->creationDate, $productPrice) );
				error_log($data->executeQuery('order'));
				unset($data);

				$data = new dbAppAlex();
				$data->setQueryString('SELECT MAX(order_id) As order_id FROM `order`');
				$temp = $data->executeQuery('order');
				$this->orderId = $temp[0]['order_id'];
				unset($data);
			}

			
			
			$data = new dbAppAlex();
			$data->setQueryString('INSERT INTO order_detail (order_id, product_description, price, quantity) VALUES (?, ?, ?, ?)');
			$data->setQueryVars( array($this->orderId, $productDescription, $productPrice, '1') );
			$data->executeQuery();
			unset($data);
			
			$this->getOrderById();

			$data = new dbAppAlex();
			$data->setQueryString('UPDATE `order` SET total = ? WHERE order_id = ?');
			$data->setQueryVars( array(($this->getTotal() + $productPrice), $this->orderId) );
			$data->executeQuery();
			unset($data);

			return array(0 => array('response' => 'order saved'));
		}
		else
		{
			return array(0 => array('response' => 'no data received'));
		}
	}

	function getOrdersByCustomerAndDate()
	{
		if($this->customerId > 0)
		{
			include_once($_SERVER['DOCUMENT_ROOT'] . '/ext/in/db.php');

			$data = new dbAppAlex();
			$data->setQueryString('SELECT om.order_id, om.delivery_address, om.creation_date, SUM(od.quantity * od.price) AS total FROM `order` om INNER JOIN order_detail od ON om.order_id = od.order_id WHERE om.customer_id = ? AND om.creation_date BETWEEN \'' . $this->dateSince . '\' AND \'' . $this->dateTo . '\' GROUP BY om.order_id, om.delivery_address, om.creation_date');
			$data->setQueryVars( array( $this->customerId ) );
			$temp = $data->executeQuery();
			unset($data);

			$orderIds = array();
			foreach($temp as $te)
			{
				array_push($orderIds, $te['order_id']);
			}

			$data = new dbAppAlex();
			$data->setQueryString('SELECT order_id, quantity, product_description FROM order_detail WHERE order_id IN (' . implode(',', $orderIds) . ') ORDER BY product_description ASC');
			$data->setQueryVars( array() );
			$tempDetail = $data->executeQuery();
			unset($data);

			for($i = 0, $tlen = count($temp); $i < $tlen; $i++)
			{
				$temp[$i]['product'] = '';
				foreach($tempDetail as $td)
				{
					if($temp[$i]['order_id'] == $td['order_id'])
					{
						$temp[$i]['product'] .= $td['quantity'] . ' x ' . $td['product_description'] . '<br>';
					}
				}
			}

			if(count($temp) == 0)
			{
				return array(0 => array('response' => 'no orders'));
			}
			else
			{
				return $temp;
			}
		}
		else
		{
			return array(0 => array('response' => 'no orders'));
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
	function setDeliveryAddress($deliveryAddress){$this->deliveryAddress = $deliveryAddress;}
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
	function setQuantity($quantity)
	{
		if(is_numeric($quantity) && $quantity > 0)
		{
			$this->quantity = $quantity;
		}
		else
		{
			$this->quantity = null;
		}
	}
	function setPrice($price)
	{
		if(is_numeric($price) && $price > 0)
		{
			$this->price = $price;
		}
		else
		{
			$this->price = null;
		}
	}
	function setDateSince($dateSince){$this->dateSince = $dateSince;}
	function setDateTo($dateTo){$this->dateTo = $dateTo;}
	
	
	function getTotal(){return $this->total;}
}
?>