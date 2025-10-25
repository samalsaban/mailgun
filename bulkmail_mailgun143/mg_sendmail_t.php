<?php

/**
 * Include external classes
**/
require 'vendor/autoload.php';
use Mailgun\Mailgun;

require("../db/db.php");
global $dbc;

//local function
function encodeEmail($email) {
	$email = strtoupper($email);
	$email_length = strlen($email);
	$conv_code = "";
	$newstring = "";

	for ($i = 0; $i < $email_length; $i++) {
	  $conv_code =   ord(substr($email,$i,1));
	  $newstring .= chr($conv_code+2);
	} 
	// Replacements to shorten the String
	$newstring = str_replace('0EQO','c',$newstring);
	$newstring = str_replace('0PGV','n',$newstring);
	$newstring = str_replace('BIOCKN','g',$newstring);
	$newstring = str_replace('BDKIRQPF','d',$newstring);
	$newstring = str_replace('BJQVOCKN','h',$newstring);
	$newstring = str_replace('BQRVWUPGV','o',$newstring);
	$newstring = str_replace('BCQN','a',$newstring);
	$newstring = str_replace('B[CJQQ','y',$newstring);
	$newstring = str_replace('BQWVNQQM','l',$newstring);
	$newstring = str_replace('BNKXG','v',$newstring);
	$newstring = str_replace('BOUP','m',$newstring);
  
    return $newstring;
}


$token_t = $argv[1];
$normal_mailguns_id = $argv[2];



// get content
$query_content = "select mszc from normal_mailgunc where token=$token_t limit 1";
		  
$result_content = mysqli_query($dbc,$query_content)
          or die ("Error while retrieving content record");
$rowContent = mysqli_fetch_array($result_content);
$mszc = 	array_shift($rowContent);		  

// get list
$query = "select id,sub,f_mail,f_name,receipent,mail_format,domain,unique_smtp_password,mode
          from normal_mailguns where id = $normal_mailguns_id";
$result = mysqli_query($dbc,$query)
          or die ("Error while retrieving records");


while ($row = mysqli_fetch_array($result)) {
      $id = $row['id'];
	  
	  //Temporarily lock the row to prevent re-pickup
	  $query_templock = "update normal_mailguns set confirmation=3 where id = $id";  
      $result_templock = mysqli_query($dbc,$query_templock)
             or die ("Error while updating record for lock script_status");
	  
	  //
      $sub = $row['sub'];
      $f_mail = $row['f_mail'];
      $f_name = $row['f_name']; 
      $mailto = $row['receipent'];
      $mail_format = $row['mail_format'];
      $domain = $row['domain'];	  
      $pvtkey = $row['unique_smtp_password'];
	  $mode = $row['mode'];
	  //$email->SMTPDebug = 2;

      //Clickid handling
      $clickid = encodeEmail($mailto);
      $mszc = str_replace('varx_clickid',$clickid,$mszc);
      //end...Clickid handling
	  
	  //Code for mailgun
	  
	   $mgClient = new Mailgun($pvtkey);
	   if ($mail_format == 'h') {
			 $result = $mgClient->sendMessage($domain, array(
				"from" => "$f_name <$f_mail>",
				"to" => "$mailto",
				"sub" => "$sub",
				"html" => "$mszc",
				));
	   } else {
			 $result = $mgClient->sendMessage($domain, array(
				"from" => "$f_name <$f_mail>",
				"to" => "$mailto",
				"sub" => "$sub",
				"text" => "$mszc",
				));
	   } 	

	   $query1 = "update normal_mailguns set confirmation=1 where id = $id";  
       $result1 = mysqli_query($dbc,$query1)
             or die ("Error while updating record");	   

/*	  
      $email = new PHPMailer();
      $email->isSMTP();
      $email->Host = $server;
      $email->Port = $port;
      $email->SMTPAuth = true;
      $email->Username = $usr;
      $email->Password = $passwd;
      $email->SMTPSecure = 'tls';
      $email->CharSet = 'UTF-8';
      if ($mail_format == 'h') {
	       $email->isHTML(true);
      }  
      $email->setFrom("$f_mail","$f_name");
      $email->sub = "$sub";
      $email->addAddress("$mailto");
      $email->Body = "$mszc";
  
     if ($email->send()) {
	   $errorflag = 0;
	   $query1 = "update msgq_list set confirmation=1 where id = $id";  
       $result1 = mysqli_query($dbc,$query1)
             or die ("Error while updating record");
	   //echo "Mail sent";
     } else {
	  $erralert = $email->ErrorInfo;
      echo "Error occurred in id: $id .".$erralert;
     }
	 
*/	 

  }

  mysqli_close($dbc);
  
?>
	  


