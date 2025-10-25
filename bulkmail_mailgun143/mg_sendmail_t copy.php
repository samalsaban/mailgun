<?php

/**
 * Include external classes
**/
require 'vendor/autoload.php';
use Mailgun\Mailgun;

require("../db/db.php");
global $dbc;




$token_t = $argv[1];
$normal_mailguns_id = $argv[2];



// get content
$query_content = "select mszc from normal_mailgunc where token=$token_t limit 1";
		  
$result_content = mysqli_query($dbc,$query_content)
          or die ("Error while retrieving content record");
$rowContent = mysqli_fetch_array($result_content);
$mszc = 	array_shift($rowContent);		  

//get values from triuniquec	 
$query_triuniquec =  "select offerid,usercode from triuniquec where token=$token";
$result_triuniquec = mysqli_query($dbc,$query_triuniquec)
          or die ("Error while retrieving triuniquec record");
$row_triuniquec = mysqli_fetch_array($result_triuniquec);
$offerid  = $row_triuniquec['offerid'] + 100;
$usercode = $row_triuniquec['usercode'];
$clickid_prefix  = $usercode.$offerid;

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
      $clickid = $clickid_prefix.$messageid;
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

  }

  mysqli_close($dbc);
  
?>
	  


