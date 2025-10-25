<?php

/**
 * Include external classes
**/
require 'vendor/autoload.php';
use Mailgun\Mailgun;
require("../db/db.php");
global $dbc;




$token             = $argv[1];
$msgq_list_start_id = $argv[2];
$msgq_list_end_id   = $argv[3];

echo "--->token:".$token." : msgq_list_start_id:".$msgq_list_start_id." : msgq_list_end_id:".$msgq_list_end_id ;

// get content
$query_content = "select mszc from normal_mailgunc where token=$token limit 1";
		  
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
/*
$query = "select id,sub,f_mail,f_name,receipent,mail_format,unique_smtp_password,mode,pending_token
          from msgq_list where id=$msgq_list_id";
*/		  
$query = "select id,sub,f_mail,f_name,receipent,mail_format,domain,unique_smtp_password,mode,pending_token		  
		  from normal_mailguns where id BETWEEN $msgq_list_start_id AND $msgq_list_end_id";
$result = mysqli_query($dbc,$query)
          or die ("Error while retrieving records");

		  
		  
while ($row = mysqli_fetch_array($result)) {
      $id = $row['id'];
 try {	  
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
	  $pending_token = $row['pending_token'];
	  

      //Clickid 
      $clickid = $clickid_prefix.$messageid;
      $mszc = str_replace('varx_clickid',$clickid,$mszc);
        
	  
	  //Code for mailgun
	  
	   $mgClient = new Mailgun($pvtkey);
	   
	   if ($mail_format == 'h') {
			 $confirmation = $mgClient->sendMessage($domain, array(
				"from" => "$f_name <$f_mail>",
				"to" => "$mailto",
				"sub" => "$sub",
				"html" => "$mszc",
				));
	   } else {
			 $confirmation = $mgClient->sendMessage($domain, array(
				"from" => "$f_name <$f_mail>",
				"to" => "$mailto",
				"sub" => "$sub",
				"text" => "$mszc",
				));
	   }  	

	   $query1 = "update normal_mailguns set confirmation=1 where id = $id";  
       $result1 = mysqli_query($dbc,$query1)
             or die ("Error while updating record");

 	   $query_updtriunique = "update triunique set mail_sent=1 where id = $pending_token";  
	   $result_updtriunique = mysqli_query($dbc,$query_updtriunique)
		   or die ("Error while updating triunique");
	   
		   
     }	catch (Exception $e) {
         echo "Err id: $id ";
		 echo $e->getMessage();
     }	
  }

  mysqli_close($dbc);
 
  
?>
	  


