<?php
if ($_GET['id'] && is_numeric($_GET['id']))
{
	require_once("db_credentials.php");											
	$mysql_conn = new mysqli($db_host, $db_user, $db_pass, 'localnews');
	$id = mysqli_real_escape_string($mysql_conn, $_GET['id']);
	$sql = "UPDATE stories SET `scriptused`=NOW() WHERE `id`=" . $id . ";" ;

	if ($mysql_conn->query($sql) === TRUE) {
		echo "Record updated successfully";
	} else {
		echo "Error updating record: " . $mysql_conn->error . " in response to " . $sql . "<br>";
		print_r($mysql_conn);
	}

	$mysql_conn->close();
}

?>