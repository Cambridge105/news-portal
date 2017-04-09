<?php
require_once("basic_auth.php");
?>
<html>
<head>
<title>Cambridge 105 Local News Portal</title>
<link type="text/css" rel="stylesheet" href="news-portal.css">
<script src="jquery-3.2.0.min.js"></script>
<script src="news-portal.js"></script>
<meta name="robots" value="noindex,nofollow">
</head>
<body onLoad="loadIndex();">
<div id="header">
    <div class="button"><a href="index.php">Return to main page</a></div>

  <h1><img src="logo.png" height="50px"><br>Local News Portal</h1>
</div>

<?php
$hasaudio = 0;
$uploadOK = 0;
$audiofilename = "";

require_once("db_credentials.php");											
$mysql_conn = new mysqli($db_host, $db_user, $db_pass, 'localnews');

if ($_POST)
{
	$uploadOK = 1;
}

if (!empty($_FILES["audiofile"]["name"]))
{
	$target_dir = "uploads/";
	$target_file = $target_dir . basename($_FILES["audiofile"]["name"]);
	$hasaudio = 1;
	$isMP3=false;
	$isWAV=false;
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
	// Check if file already exists
	if (file_exists($target_file)) {
		 echo "Sorry, file already exists.";
		 $hasaudio = 0;
	}
	// Check file size
	if ($_FILES["audiofile"]["size"] > 10000000) {
		 echo "Sorry, your file is too large.";
		 $hasaudio = 0;
	}
	// Allow certain file formats
	if($imageFileType == "mp3") {$isMP3 = true;} elseif ($imageFileType=="wav") {$isWAV=true;} else{
		 echo "Sorry, only MP3 and WAV files are allowed.";
		 $hasaudio = 0;
	}
	// Check if $hasaudio is set to 0 by an error
	if ($hasaudio === 0) {
		$uploadOK = 0; // Prevents the db insert later 
		 echo "Sorry, your file was not uploaded."; // if everything is ok, try to upload file
		 } else {
		 if (move_uploaded_file($_FILES["audiofile"]["tmp_name"], $target_file)) {
			 echo "The file ". basename( $_FILES["audiofile"]["name"]). " has been uploaded.";
			 $audiofilename = basename( $_FILES["audiofile"]["name"]);
		 } else {
			 echo "Sorry, there was an error uploading your file.";
		 }
	}
}





if ($uploadOK == 1) 
{
	$embargo = "";
	$embargo_db = "";
	if (!empty($_POST['embargo']))
	{
		$embargo = DateTime::createFromFormat('!Y-m-d\TH:i', mysqli_real_escape_string($mysql_conn, ($_POST['embargo'])));
		$embargo_db = date("Y-m-d H:i:s",$embargo->getTimestamp());
	}
	else{ $embargo_db =  date("Y-m-d H:i:s");}
	
	if (!($stmt = $mysql_conn->prepare("INSERT INTO stories(title, embargo, audiocredit, addedby, addeddate, text, category, audiofilename) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"))) {
				echo "Prepare failed: (" . $mysql_conn->errno . ") " . $mysql_conn->error;
			}
			if (!$stmt->bind_param('ssssssss', mysqli_real_escape_string($mysql_conn, $_POST['title']), $embargo_db, mysqli_real_escape_string($mysql_conn, $_POST['audiocredit']), mysqli_real_escape_string($mysql_conn, $_POST['addedby']), date("Y-m-d H:i:s"), mysqli_real_escape_string($mysql_conn, $_POST['script']), mysqli_real_escape_string($mysql_conn, $_POST['category']), $audiofilename)) {
				echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			}
			$stmt->execute(); 
	echo "<p>Upload successful</p>";
}

$mysql_conn->close();
?>



<h2>Upload</h2>
<form action="upload.php" method="post" id="uploadForm" enctype="multipart/form-data">
<label for="title">Title*:</label> <input type="text" maxlength="25" name="title" required><br>
<label for="embargo">Embargo until:</label> <input type="datetime-local" name="embargo"> <em>Leave blank if for immediate release</em><br>
<label for="category">Category:</label> <select name="category" id="category"><option value="NEWS">News</option><option value="SPORT">Sport</option><option value="SHOWBIZ">Showbiz</option><option value="BUSINESS">Business</option><option value="BBC">BBC</option><option value="PROSPECTS">Prospects</option><option value="PINNED">Pinned</option></select><br>
<label for="addedby">Added by*:</label> <input type="text" maxlength="25" name="addedby" required value="<?php echo $_SERVER['PHP_AUTH_USER'] ?>"><br>
<label for="audiofile">Audio file:</label> <input type="file" name="audiofile"><br>
<label for="audiocredit">Audio credit:</label> <input type="text" maxlength="30" id="audiocredit" name="audiocredit"><br>
<br>
Script*:<br>
<textarea name="script" rows="10" cols="70" required>
</textarea><br>
<input type="submit" value="Upload">
</form>
<hr class="clear">
<script>
$( "#category" ).change(function() {
  var newCategory = $("#category").val();
  if (newCategory == "BBC" && $("#audiocredit").val().length<1)
	{
		$("#audiocredit").val("BBC Radio Cambridgeshire");
	}
	else if (newCategory != "BBC" && $("#audiocredit").val() == "BBC Radio Cambridgeshire")
	{
		$("#audiocredit").val("");
	}
});
</script>
</body>
</html> 