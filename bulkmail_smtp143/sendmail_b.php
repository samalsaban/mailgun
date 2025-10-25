<?php

/**
 * Include external classes
**/
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';
require("../db/db.php");
global $dbc;

//local function
/*function encodeEmail($email) {
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
*/


$token             = $argv[1];
$msgq_list_start_id = $argv[2];
$msgq_list_end_id   = $argv[3];

echo "--->token:".$token." : normal_smtps_start_id:".$msgq_list_start_id." : normal_smtps_end_id:".$msgq_list_end_id ;

// get content
$query_content = "select mszc from normal_smtpc where token=$token limit 1";
		  
$result_content = mysqli_query($dbc,$query_content)
          or die ("Error while retrieving content record");
$rowContent = mysqli_fetch_array($result_content);
$mszc = 	array_shift($rowContent);

//get values from triuniquec	 
$query_triuniquec =  "select offerid,usercode from triuniquec where token=$token";
$result_triuniquec = mysqli_query($dbc,$query_triuniquec)
          or die ("Error while retrieving triuniquec record");
if ($row_triuniquec = mysqli_fetch_array($result_triuniquec)) {
	$offerid  = $row_triuniquec['offerid'] + 100;
	$usercode = $row_triuniquec['usercode'];	
} else {
	$offerid  = '';
	$usercode = '';
}	
$clickid_prefix  = $usercode.$offerid;
	  

// get list
/*
$query = "select id,sub,f_mail,f_name,receipent,mail_format,server,port,usr,passwd,tls,mode,pending_token
          from normal_smtps where id=$msgq_list_id";
*/		  
$query = "select id,sub,f_mail,f_name,receipent,mail_format,server,port,usr,passwd,tls,mode,pending_token		  
		  from normal_smtps where id BETWEEN $msgq_list_start_id AND $msgq_list_end_id";
$result = mysqli_query($dbc,$query)
          or die ("Error while retrieving records");


while ($row = mysqli_fetch_array($result)) {
      $id = $row['id'];
	  
	  //Temporarily lock the row to prevent re-pickup
	  $query_templock = "update normal_smtps set confirmation=3 where id = $id";  
      $result_templock = mysqli_query($dbc,$query_templock)
             or die ("Error while updating record for lock script_status");
	  
	  //
      $subject = $row['sub'];
      $f_mail = $row['f_mail'];
      $f_name = $row['f_name']; 
      $mailto = $row['receipent'];
$messageid =base64_encode($row['receipent']);     
 $mail_format = $row['mail_format'];
      $server = $row['server'];
      $port = $row['port'];
      $usr = $row['usr'];
      $passwd = $row['passwd'];
      $tls = $row['tls'];	  
      $mode = $row['mode'];
	  $pending_token = $row['pending_token'];

      $email = new PHPMailer();
      //$email->SMTPDebug = 2;
      $email->isSMTP();
      $email->Host = $server;
      $email->Port = $port;
      $email->SMTPAuth = true;
      $email->Username = $usr;
      $email->Password = $passwd;
	  /*
	  if ($tls == 'Y') {
	    $email->SMTPSecure = 'tls';
      }
	  */
	  $email->SMTPAutoTLS = false; 
      $email->CharSet = 'UTF-8';
      if ($mail_format == 'h') {
	       $email->isHTML(true);
      }  
/*$email->MessageID = '<'.$messageid.$f_name.'>';
$email->addCustomHeader("List-Unsubscribe: <mailto:$f_mail?sub=Unsubscribe");
                   $email->addCustomHeader("List-Unsubscribe-Post: List-Unsubscribe=One-Click");*/
      $email->setFrom("$f_mail","$f_name");
      $email->Subject = "$subject";	  
      $email->addAddress("$mailto");

      //Clickid handling
	  /*$clickid = $clickid_prefix.$messageid;
      $mszc = str_replace('varx_clickid',$clickid,$mszc);
      //end...Clickid handling	  
	  */
      $email->Body = "$mszc";
  
     if ($email->send()) {
	   $errorflag = 0;
	   $query1 = "update normal_smtps set confirmation=1 where id = $id";  
       $result1 = mysqli_query($dbc,$query1)
                  or die ("Error while updating normal_smtps record for bulk");
	   //echo "Mail sent";

       $query_updtriunique = "update triunique set mail_sent=1 where id = $pending_token";  
	   $result_updtriunique = mysqli_query($dbc,$query_updtriunique)
             or die ("Error while updating triunique");
	   
     } else {

		   // Update triunique table. If error occurred, reset flag
		   $query_updtriunique = "update triunique set mail_sent=0 where id = $pending_token";  
		   $result_updtriunique = mysqli_query($dbc,$query_updtriunique)
             or die ("Error while updating triunique");
	 
	    $erralert = $email->ErrorInfo;
        echo "Error occurred in id: $id .".$erralert;
     }
	

  }

  mysqli_close($dbc);
  
?>
	  


