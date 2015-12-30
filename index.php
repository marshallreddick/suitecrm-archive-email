<?PHP
//gets the POST request and creates a text file of the data to work with
// the name of the file you're writing to
$part = time();
$myFile = "./mail/".$part."email.txt";

// opens the file for appending (file must already exist)
$fh = fopen($myFile, 'a');

//takes your post into a variable 
$post_data = file_get_contents("php://input");

// Write to the file
fwrite($fh, $post_data);

// You're done
fclose($fh);

//calls the archive script to act on the text file
//if you ever need to rerun an email archive, due to SuiteCRM credential change or misconfiguration in rest_login
//you can re run the file by going to the url like this http://example.com/suitecrm-archive-email/archive.php?f=./mail/1451456232email.txt
$url = './archive.php?f='.$myFile;
$ch = curl_init($url);
 
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
 
$response = curl_exec($ch);
curl_close($ch);
?>
