<?php
$response = "";
if (!empty($_FILES["audiofile"]["name"]))
{
	$target_dir = "uploads/";
	$uploaded_filename = basename($_FILES["audiofile"]["name"]);
	$target_file = $target_dir . $uploaded_filename;
	$hasaudio = 1;
	$isMP3=false;
	$isWAV=false;
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
	// Check if file already exists
	if (file_exists($target_file)) {
		 $response .= "Sorry, file already exists.";
		 $hasaudio = 0;
	}
	// Check file size
	if ($_FILES["audiofile"]["size"] > 10000000) {
		 $response .= "Sorry, your file is too large.";
		 $hasaudio = 0;
	}
	// Allow certain file formats
	if($imageFileType == "mp3") {$isMP3 = true;} elseif ($imageFileType=="wav") {$isWAV=true;} else{
		 $response .= "Sorry, only MP3 and WAV files are allowed.";
		 $hasaudio = 0;
	}
	// Check if $hasaudio is set to 0 by an error
	if ($hasaudio === 0) {
		$uploadOK = 0; // Prevents the db insert later 
		 $response .= "Sorry, your file was not uploaded."; // if everything is ok, try to upload file
		 } else {
		 if (move_uploaded_file($_FILES["audiofile"]["tmp_name"], $target_file)) {
			 $response .= "The file ". $uploaded_filename . " has been uploaded.";
			 $audiofilename = $uploaded_filename;
		 } else {
			 $response .= "Sorry, there was an error uploading your file. Error: " . $_FILES["audiofile"]["error"];
		 }
	}
}
echo json_encode($response);
?>