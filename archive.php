<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('./rest_login.php');

$file = file_get_contents($_GET['f']);
//echo $file;
//get POST input from mandrill
$mail_array = str_replace('mandrill_events=', '', urldecode($file));
//var_dump($mail_array);
//json completely decoded!
$mail_array = json_decode($mail_array, true);
//var_dump($mail_array);
$mail_array = $mail_array[0];
//var_dump($mail_array['msg']['to']);

//set variables from mandril POST
$to = $mail_array['msg']['to'][0][0];
$user_email = $mail_array['msg']['from_email'];
$from = $mail_array['msg']['from_email'];
$subject = $mail_array['msg']['subject'];
$text = utf8_encode($mail_array['msg']['text']);
$text = iconv("UTF-8", "ISO-8859-1//IGNORE", $text);
$html = utf8_encode($mail_array['msg']['html']);
$html = iconv("UTF-8", "ISO-8859-1//IGNORE", $html);
//Figure out how to handle attachments eventually
//$attachments = $Parser->getAttachments();
//print_r( $attachments);

//login to REST API
$login_result = call("login", $login_parameters, $url);
//get session id
$session_id = $login_result->id;

$pattern = '/[A-Za-z0-9\'\+._-]+@[A-Za-z0-9._-]+\.([A-Za-z0-9_-][A-Za-z0-9_]+)/'; //regex for pattern of e-mail address
preg_match_all($pattern, $mail_array['msg']['raw_msg'], $matches);
//print_r($matches); 
$emails=array_unique($matches[0]);
$emails = array_reverse($emails);

preg_match_all($pattern, $mail_array['msg']['text'], $matchesText);
$matchesText = $matchesText[0];
$textEmails=array_unique($matchesText);
$emails = array_merge($emails, $textEmails);
$emailsunique=array_unique($emails);
$emails = array_reverse($emailsunique);
//get all the emailaddresses from the message

$i = 0;
$cc = array();
foreach ($emails as &$value) {
  //split up email addresses in forwarded messages
  if($i==0){
    //email was from
    $from = $value;
  }
  else if ( $i == 1 ) {
    //email is to
    $to = $value;
  }
  else
    //email cced to everyone else
    array_push($cc, "$value");
  $email_caps = strtoupper($value);
  $i++;
}

//find out if email was from user to contact
if (strtoupper($from) == strtoupper($user_email)){
  $get_entry_parameters = array(
     //session id
     'session' => $session_id,
     //The name of the module from which to retrieve records
     'module_name' => 'Contacts',
     //The SQL WHERE clause without the word "where".
     'query' => "contacts.id in (select eabr.bean_id 
                 from email_addr_bean_rel eabr join email_addresses ea on eabr.email_address_id = ea.id 
                 where eabr.bean_module = 'Contacts' and ea.email_address = '$to'
                 and eabr.deleted = 0)",
     'order_by' => "",

     //The record offset from which to start.
     'offset' => 0,
     //A list of fields to include in the results.
     'select_fields' => array(
          'id',
     ),
     //The maximum number of results to return.
     'max_results' => 1,
     //If deleted records should be included in results.
     'deleted' => 0,
     //If only records marked as favorites should be returned.
     'favorites' => false,
  );
  $get_contact = call('get_entry_list', $get_entry_parameters, $url); 
  $contact_id = $get_contact->entry_list[0]->id;
}
//the email is from the Contact to the User
else {
  $get_entry_parameters = array(
     //session id
     'session' => $session_id,
     //The name of the module from which to retrieve records
     'module_name' => 'Contacts',
     //The SQL WHERE clause without the word "where".
     'query' => "contacts.id in (select eabr.bean_id 
                 from email_addr_bean_rel eabr join email_addresses ea on eabr.email_address_id = ea.id 
                 where eabr.bean_module = 'Contacts' and ea.email_address = '$from'
                 and eabr.deleted = 0)",
     'order_by' => "",

     //The record offset from which to start.
     'offset' => 0,
     //A list of fields to include in the results.
     'select_fields' => array(
          'id',
     ),
     //The maximum number of results to return.
     'max_results' => 1,
     //If deleted records should be included in results.
     'deleted' => 0,
     //If only records marked as favorites should be returned.
     'favorites' => false,
  );
  $get_contact = call('get_entry_list', $get_entry_parameters, $url); 
  $contact_id = $get_contact->entry_list[0]->id;
}
$get_entry_parameters = array(
     //session id
     'session' => $session_id,
     //The name of the module from which to retrieve records
     'module_name' => 'Users',
     //The SQL WHERE clause without the word "where".
     'query' => "users.id in (select eabr.bean_id 
                 from email_addr_bean_rel eabr join email_addresses ea on eabr.email_address_id = ea.id 
                 where eabr.bean_module = 'Users' and ea.email_address = '$user_email'
                 and eabr.deleted = 0)",
     'order_by' => "",                  
     //The record offset from which to start.
     'offset' => 0,
     //A list of fields to include in the results.
     'select_fields' => array(
          'id',
     ),
     //The maximum number of results to return.
     'max_results' => 1,
     //If deleted records should be included in results.
     'deleted' => 0,
     //If only records marked as favorites should be returned.
     'favorites' => false,
);

$get_user = call('get_entry_list', $get_entry_parameters, $url); 
//echo $get_user->entry_list->id;
$user_id = $get_user->entry_list[0]->id;
print_r($cc);
foreach( $cc as $email ){
	$cclist .= $email.", ";
}
//Fill out all the values for the email
$parameters = array(
    'session' => $session_id,
    'module' => 'Emails',
    'name_value_list' => array(
        array('name' => 'name', 'value' => $subject),
        array('name' => 'status', 'value' => 'archive@crm.marshallreddick.com'),
        array('name' => 'from_addr', 'value' => "$from"),
        array('name' => 'to_addrs', 'value' => "$to"),
        array('name' => 'cc_addrs', 'value' => "$cclist"),
        array('name' => 'date_sent', 'value' => gmdate("Y-m-d H:i:s")),
        array('name' => 'description', 'value' => "$text"),
        array('name' => 'description_html', 'value' => "$html"),
        array('name' => 'parent_type', 'value' => 'Contacts'),
        array('name' => 'parent_id', 'value' => "$contact_id"),
        array('name' => 'created_by', 'value' => "$user_id"),
        array('name' => 'assigned_user_id', 'value' => "$user_id"),
        ),
    );

$create_email = call('set_entry', $parameters, $url); 
//print_r ($create_email);
$file = 'archived_email_log.txt';
// Open the file to get existing content
$current = file_get_contents($file);
// Append a new person to the file
$current .= gmdate("Y-m-d H:i:s")."$user_id logged an email to $contact_id\nTo: $to From: $from \n";
// Write the contents back to the file
file_put_contents($file, $current);

?>  
