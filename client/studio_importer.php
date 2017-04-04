<?php
require_once("config.php.inc");
$file_count = 0;

$temp_dir = tempnam(sys_get_temp_dir(), 'localnews-import');
mkdir($temp_dir, 0755);

$unimported_json = file_get_contents($remote_path . "unimported_audio.php");
$unimported_decoded = json_decode($unimported_json,true);
foreach ($unimported_decoded as $unimported_file_info)
{
	echo "Downloading from " . $remote_path . "uploads/" . $unimported_file_info['audiofile'];
	$downloaded_audio = file_get_contents($remote_path . "uploads/" . $unimported_file_info['audiofile']);
	file_put_contents($temp_dir . "/" . $unimported_file_info['audiofile'],$downloaded_audio);
	$downloaded_audio = ""; // Free memory
	$file_count++;
}

if ($file_count > 0)
{
	sleep(1); // Ensure files written before trying import
	$return_array=array();
	$return_array['stories'] = array();

	foreach ($unimported_decoded as $unimported_file_info)
	{
		exec("rdimport --verbose --delete-source --segue-level=-16 NEWS-LOCAL " . $temp_dir . "/" . $unimported_file_info['audiofile'],$output);
		
		//FOR LOCAL TESTING:
		//$output = array();
		//array_push($output, "FOO");
		//array_push($output, "BAR");
		//array_push($output, "Importing file \"IRN.mp3\" to cart 060601 ... done.");
		//array_push($output, "BAZ");
	
	
		// Looking for something like 
		// Importing file "060108_001.wav" to cart 060601 ... done. 
		// in output
		$thisid = $unimported_file_info['id'];
		foreach ($output as $outputLine)
		{
			if (strpos($outputLine,"to cart"))
			{
				preg_match("/to cart (\d+)/",$outputLine,$matches);
				$return_array['stories'] = array($thisid => $matches[1]);
			}
		}

	}

	
	$result = file_post_contents($remote_path . "set_carts.php",$return_array);
	echo $result;
	
}

rmdir($temp_dir);

function file_post_contents($url, $data, $username = null, $password = null)
{
    $postdata = http_build_query($data);

    $opts = array('http' =>
        array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => $postdata
        )
    );

    if($username && $password)
    {
        $opts['http']['header'] = ("Authorization: Basic " . base64_encode("$username:$password"));
    }

    $context = stream_context_create($opts);
    return file_get_contents($url, false, $context);
}


?>
