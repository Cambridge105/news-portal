<?php

require_once("db_credentials.php");	
$mysql_conn = new mysqli($db_host, $db_user, $db_pass, 'localnews');
	
$query = mysqli_query($mysql_conn, "SELECT id,audiofilename AS audiofile FROM stories WHERE (audiofilename IS NOT NULL AND audiofilename !='') AND audioimported is null");
$rows = array();
while($r = mysqli_fetch_assoc($query)) {
	$rows[] = $r;
}
header('Access-Control-Allow-Origin: *');										
header('Content-Type: application/json');
print json_encode($rows);
$mysql_conn->close();
?>
