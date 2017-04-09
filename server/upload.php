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
	$audiofilename = mysqli_real_escape_string($mysql_conn, $_POST['uploadedAudioFile']);
}

if (!empty($_POST['renameaudio']))
{
	$renameaudio = mysqli_real_escape_string($mysql_conn, $_POST['renameaudio']);
	rename("uploads/" . $audiofilename, "uploads/" . $renameaudio);
	$audiofilename = $renameaudio;
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
<div id="dropArea">Drop the audio file to upload here</div>
<div id="progressBar"><div id="progress"></div></div>
<label for="title">Title*:</label> <input type="text" maxlength="25" name="title" required><br>
<label for="embargo">Embargo until:</label> <input type="datetime-local" name="embargo"> <em>Leave blank if for immediate release</em><br>
<label for="category">Category:</label> <select name="category" id="category"><option value="NEWS">News</option><option value="SPORT">Sport</option><option value="SHOWBIZ">Showbiz</option><option value="BUSINESS">Business</option><option value="BBC">BBC</option><option value="PROSPECTS">Prospects</option><option value="PINNED">Pinned</option></select><br>
<label for="addedby">Added by*:</label> <input type="text" maxlength="25" name="addedby" required value="<?php echo $_SERVER['PHP_AUTH_USER'] ?>"><br>
<label for="renameaudio">Rename audio file:</label> <input type="text" maxlength="30" id="renameaudio" name="renameaudio"><br>
<input id="uploadedAudioFile" type="hidden" value="">
<label for="audiocredit">Audio credit:</label> <input type="text" maxlength="30" id="audiocredit" name="audiocredit"><br>
<br>
Script*:<br>
<textarea name="script" rows="10" cols="70" required>
</textarea><br>
<input type="submit" id="formSubmit" value="Upload">
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

function dragOver(evt) {
    evt.stopPropagation();
    evt.preventDefault();
    return false;
}


function dropEvent(evt) {
    evt.stopPropagation();
    evt.preventDefault();
    
    var droppedFiles = evt.dataTransfer.files;

    var formData = new FormData();
    
    for(var i = 0; i < droppedFiles.length; ++i) {
        var file = droppedFiles[i];
        
        formData.append("audiofile", file);
		$('#uploadedAudioFile').val(file.name);
				
    }
	$('#formSubmit').prop('disabled', true);
    
    xhr = new XMLHttpRequest();
	xhr.upload.addEventListener("progress", function(evt){
      if (evt.lengthComputable) {
        var percentComplete = (evt.loaded / evt.total)*100;
		percentComplete = Math.floor(percentComplete);
        $('#progress').width(percentComplete + "%")
        console.log(percentComplete);
      }
    }, false);
    
    xhr.open("POST", "upload_audio.php");  
    xhr.onreadystatechange = handleResult;
    xhr.send(formData);
   
}

function handleResult() {
    if (xhr.readyState == 4 /* complete */) {
        switch(xhr.status) {
            case 200: /* Success */
                $('#dropArea').css("color","black");
				$('#dropArea').css("font-style","normal");
				var jsonResponse = JSON.parse(xhr.responseText);
				$('#dropArea').html(jsonResponse);
				$('#formSubmit').prop('disabled', false);
				if (jsonResponse.search("Sorry") > -1)
				{
					$('#progress').css("background-color","red");
				}
				else 
				{
					$('#progress').css("background-color","green");
				}
				break;
            default:
                break;
        }
        xhr = null;
    }      
}


var dropArea = document.getElementById('dropArea');
dropArea.addEventListener('drop', dropEvent, false);
dropArea.addEventListener('dragover', dragOver, false);

</script>
</body>
</html> 