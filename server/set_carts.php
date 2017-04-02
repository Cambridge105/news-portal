<?php

// Receives POST form containing serialised array of filenames to cart IDs
// Update db with cart IDs and set audioimported = 1 for each item in array
if ($_POST['stories'])
{
	echo "OK";
	
	require_once("db_credentials.php");											
	$mysql_conn = new mysqli($db_host, $db_user, $db_pass, 'localnews');
	$id = mysqli_real_escape_string($mysql_conn, $_GET['id']);
	
	$posted_data = $_POST['stories'];
	echo "Received";
	print_r($posted_data);
	foreach ($posted_data as $id => $cart)
	{
		echo "Got ID" . $id;
		$id = mysqli_real_escape_string($mysql_conn, $id);
		$cart = mysqli_real_escape_string($mysql_conn, $cart);
		$sql = "UPDATE stories SET `cart`='" . $cart . "',audioimported=1 WHERE `id`=" . $id . ";" ;

		if ($mysql_conn->query($sql) === TRUE) {
			echo "Record updated successfully";
		} else {
			echo "Error updating record: " . $mysql_conn->error . " in response to " . $sql . "<br>";
			print_r($mysql_conn);
		}
	}

	$mysql_conn->close();
}

?>