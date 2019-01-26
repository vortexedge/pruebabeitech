<?php
include('./app/controller/main.php');
?>
<!DOCTYPE html>
<html>
<head>
<title>Alex App</title>
<style>
#main{width: 50%;margin-left: auto; margin-right: auto; margin-top: 20px; margin-bottom: 20px; border: 5px solid #4d4d4d; font-family: Arial, Helvetica, sans-serif;}
#main div{margin-left: auto; margin-right: auto;}
h1{text-align: center;}
#customer_header table{margin-left: 10px;}
h2{margin-left: 10px;}
#old_orders table{margin-left: auto; margin-right: auto; margin-bottom: 10px; border-collapse: collapse; border: 1px solid black;}
#old_orders table th{text-transform: capitalize; padding: 5px;}
#old_orders table td{border: 1px solid black; padding: 5px; text-align: center; font-size: 80%;}
#old_orders table tr:nth-of-type(even) { background-color:#E6E6E6; }
</style>
</head>
<body>
<div id="main" >
	<div id="customer_header" >
		<form method="get" >
			<h1>Customer</h1>
			<table>
				<tr>
					<td>Customer:</td>
					<td>
						<select name="customer" >
							<option value="0" >None</option>
<?php
foreach($viewCustomerList as $vcl)
{
?>
							<option value="<?php echo $vcl['customer_id']; ?>" <?php if($viewCustomerListSelection == $vcl['customer_id']){echo 'selected';} ?> ><?php echo $vcl['name']; ?></option>
<?php
}
?>
						</select>
					</td>
					<td>
						<input type="submit" value="Go" >
					</td>
				</tr>
			</table>
		</form>
	</div>
	<h2><?php if($viewCustomerName != ''){ echo $viewCustomerName; } ?></h2>
	<div id="old_orders" ><?php echo $viewOrderByCustomer; ?></div>
</div>
</body>
</html>