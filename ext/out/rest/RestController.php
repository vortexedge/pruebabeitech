<?php
$add = 0;
$product = 0;
$list = 0;
$since = '';
$to = '';

foreach($_GET as $key => $val)
{
	${$key} = $val;
}
foreach($_POST as $key => $val)
{
	${$key} = $val;
}

/*sanitation*/
$add = (is_numeric($add)) ? $add : 0;
$product = (is_numeric($product)) ? $product : 0;
$list = (is_numeric($list)) ? $list : 0;
$since = (is_numeric($since)) ? $since : '';
$to = (is_numeric($to)) ? $to : '';

/*add the order in the rest request, add => customer and product */
if($add > 0 && $product > 0)
{
	include_once($_SERVER['DOCUMENT_ROOT'] . '/app/class/Order.php');

	$addOrder = new Order();
	$addOrder->setCustomerId($add);
	$addOrder->setProductId($product);
	$response = $addOrder->addOrder();

	include_once('./restHandler.php');

	$contentType = $_SERVER['HTTP_ACCEPT'];
	if($contentType == '*/*')
	{
		$contentType = 'text/html';
	}

	$handler = new RestHandler();
	$handler->httpHeaders($contentType, 200);
	echo $handler->encodeResponse($contentType, $response);
}
else if($list > 0 && trim($since) != '' )
{
	include_once($_SERVER['DOCUMENT_ROOT'] . '/app/class/Order.php');

	if($to < $since || trim($to) == '')
	{
		$to = date("Ymd");
	}

	$orderList = new Order();
	$orderList->setCustomerId($list);
	$orderList->setDateSince($since);
	$orderList->setDateTo($to);
	$customerOrders = $orderList->getOrdersByCustomerAndDate();

	include_once('./restHandler.php');

	$contentType = $_SERVER['HTTP_ACCEPT'];
	if($contentType == '*/*')
	{
		$contentType = 'text/html';
	}

	$handler = new RestHandler();
	$handler->httpHeaders($contentType, 200);
	echo $handler->encodeResponse($contentType, $customerOrders);
}
?>