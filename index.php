<?PHP
//gets the POST request and creates a text file of the data to work with
//// the name of the file you're writing to
$part = time();
$myFile = "./mail/".$part."email.txt";

// opens the file for appending (file must already exist)
$fh = fopen($myFile, 'a');

// Makes a CSV list of your post data
$comma_delmited_list = file_get_contents("php://input");

// Write to the file
fwrite($fh, $comma_delmited_list);

// You're done
fclose($fh);

$url = './archive.php?f='.$myFile;
$ch = curl_init($url);
 
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
 
$response = curl_exec($ch);
curl_close($ch);
?>
