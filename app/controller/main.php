<?php
include_once('.\app\class\Customer.php');
include_once('.\app\class\Product.php');
include_once('.\app\class\Order.php');

$customer = 0;
foreach($_GET as $key => $val)
{
	${$key} = $val;
}
foreach($_POST as $key => $val)
{
	${$key} = $val;
}

/*sanitation*/
$customer = (is_numeric($customer)) ? $customer : 0;


/*data by customer*/
/*set*/
$viewCustomerName = '';
$viewCustomerEmail = '';

if($customer > 0)
{
	/*customer data*/
	$customerData = new Customer();
	$customerData->setCustomerId($customer);
	$customerData->getCustomerById();
	$viewCustomerName = $customerData->getName();
	$viewCustomerEmail = $customerData->getEmail();
	unset($customerData);
}



/* data always present*/
$customerList = new Customer();
$viewCustomerList = $customerList->getCustomerAll();
$viewCustomerListSelection = $customer;
unset($customerList);



/*consume the rest for orders list*/
function consumeRest($customer = 0, $since = '', $to = '')
{
	include_once('./app/configuration.php');
	
	$url = RESTURL;

	$data = array('list' => $customer, 'since' => $since, 'to' => $to);
	$method = 'POST';

	$curl = curl_init();

	switch ($method)
	{
		case "POST":
			curl_setopt($curl, CURLOPT_POST, 1);

			if ($data)
			{
				curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			}
		break;
		default:
			if ($data)
			{
				$url = sprintf("%s?%s", $url, http_build_query($data));
			}
		break;
	}

	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

	//curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: text/plain', 'Accept: text/xml'));

	$result = curl_exec($curl);

	curl_close($curl);
	
	return $result;
}

/*list of orders*/
$since = time() - (60 * 60 * 24 * 30); //seconds * minutes * hours * days
$since = date("Ymd", $since);
$to = date("Ymd");
$viewOrderByCustomer = consumeRest($customer, $since, $to);
?>