<?PHP
//system variables you need to fill out, specific to your CRM configuration
$url = "http://crm.example.com/service/v4/rest.php";
$username = 'USERNAME';
$password = 'PASSWORD';
$archive_email_address = 'archive@crm.example.com';

//functions to login with
//function to make cURL request
function call($method, $parameters, $url)
{
    ob_start();
    $curl_request = curl_init();

    curl_setopt($curl_request, CURLOPT_URL, $url);
    curl_setopt($curl_request, CURLOPT_POST, 1);
    curl_setopt($curl_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    curl_setopt($curl_request, CURLOPT_HEADER, 1);
    curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl_request, CURLOPT_FOLLOWLOCATION, 0);

    $jsonEncodedData = json_encode($parameters);

    $post = array(
         "method" => $method,
         "input_type" => "JSON",
         "response_type" => "JSON",
         "rest_data" => $jsonEncodedData
    );

    curl_setopt($curl_request, CURLOPT_POSTFIELDS, $post);
    $result = curl_exec($curl_request);
    curl_close($curl_request);

    $result = explode("\r\n\r\n", $result, 2);
    $response = json_decode($result[1]);
    ob_end_flush();

    return $response;
}

//login --------------------------------------------

$login_parameters = array(
     "user_auth"=>array(
          "user_name"=>$username,
          "password"=>md5($password),
          "version"=>"1"
     ),
     "application_name"=>"ArchiveEmail",
     "name_value_list"=>array(),
);
function get_email_address($input){
        $input = explode('&lt;', $input);
        $output = str_replace('&gt;', '', $input);
        $name = $output[0]; // THE NAME
        $email = $output[1]; // THE EMAIL ADDRESS
        return $email;
};
?>
