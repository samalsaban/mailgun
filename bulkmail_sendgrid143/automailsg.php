<?php

/**
 * Include external classes
**/

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';
require("../db/db.php");
global $dbc;

$script_unique_id = $argv[1];

// get script_sendgrids record for current script_unique_id
$query1 = "select token,count_seconds,count_mail_limit,mail_holdl,btoken_count,token_update_limit,script_status
          from script_sendgrids where script_unique_id = $script_unique_id";
		  
$result1 = mysqli_query($dbc,$query1)
          or die ("Error while retrieving records from script_sendgrids");
$rowContent1 = mysqli_fetch_array($result1);

// get script_sendgridc record for current script_unique_id
$query2 = "select id,sub,f_mail,repl,f_name,receipent,mail_format,unique_smtp_password,xm,encrypting,m_mail,set_mid,mode,mszc
          from script_sendgridc where script_unique_id = $script_unique_id";
		  
$result2 = mysqli_query($dbc,$query2)
          or die ("Error while retrieving records from script_sendgridc");
$rowContent2 = mysqli_fetch_array($result2);

//Get DB values into variables
  $token        = $rowContent1['token'];
  $count_seconds    = $rowContent1['count_seconds'];
  $count_mail_limit   = $rowContent1['count_mail_limit']; 
//$mail_holdl  = $rowContent1['mail_holdl'];
  $btoken_count = $rowContent1['btoken_count'];
  $token_update_limit      = $rowContent1['token_update_limit'];
  $script_status        = $rowContent1['script_status'];	  

  $sub       = $rowContent2['sub'];
  $f_mail    = $rowContent2['f_mail'];
  $repl          = $rowContent2['repl'];
  $f_name     = $rowContent2['f_name']; 
  $receipent       = $rowContent2['receipent'];
  $mail_format         = $rowContent2['mail_format'];
  $unique_smtp_password        = $rowContent2['unique_smtp_password'];	  
  $mode          = $rowContent2['mode'];
  $mszc      = $rowContent2['mszc']; 
  $messageid     = base64_encode($rowContent2['receipent']);
  $set_mid    = $rowContent2['set_mid'];
  $m_mail   = $rowContent2['m_mail'];
  $encrypting       = $rowContent2['encrypting'];
  $xm            = $rowContent2['xm'];
  // For Sendgrid
  $server = "smtp.sendgrid.net";
  $port   = "587";
  $usr    = "unique_smtp_password";  
  
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
date_default_timezone_set('Asia/Kolkata');
$date_prefix = 'd'.date('d');	
$clickid_prefix  = $date_prefix.$usercode.$offerid;    
  
  // Create record for runscript_sendgrid if script_status='READY' else update
  if ($script_status == 'READY') {
     $query = "insert into runscript_sendgrid (script_unique_id,runsignal) values (".$script_unique_id.",'1')"; 
  }
  else {
	 $query = "update runscript_sendgrid set runsignal = '1' where script_unique_id = $script_unique_id";
  }
  $result = mysqli_query($dbc,$query) or die ("Error while inserting/updating record in runscript_sendgrid"); 
  
  function get_runsignal (int $script_unique_id){
	 global $dbc;
     $query_signal = "select runsignal from runscript_sendgrid where script_unique_id = $script_unique_id";
	 $result_signal = mysqli_query($dbc,$query_signal) or die ("Error while retrieving records from runscript_sendgrid");
     $row_signal = mysqli_fetch_array($result_signal);	
     return ($row_signal['runsignal']);	 
  }
  
  function send_test_mails(){
	global $receipent;
	global $unique_smtp_password;
	global $server;
	global $port;
	global $usr;
	global $mail_format;
	global $f_mail;
	global $f_name;
	global $sub;
	global $mszc;
	
	if (trim($receipent) != "") {
     //$email->SMTPDebug = 2;
	 $mail_list = explode(',',trim($receipent));
	 foreach ( $mail_list as $mailto) {
		 $email = new PHPMailer();
		 $email->isSMTP();
		 $email->Host = $server;
		 $email->Port = $port;
		 $email->SMTPAuth = true;
		 $email->Username = $usr;
		 $email->Password = $unique_smtp_password;
		 $email->CharSet = 'UTF-8';
		 if ($encrypting == 'B') {
	       $email->Encoding = 'base64';
          }
          if ($encrypting == 'D')
          {
          $email->Encoding = 'quoted-printable';
          }
          if ($encrypting == 'S')
          {
          $email->Encoding = '16bit';
          }
          if ($encrypting == 'E')
          {
          $email->Encoding = '8bit';
          }
		 if ($mail_format == 'h') {
			$email->isHTML(true);
		 }  
		 //one-click Unsubscribe changes
					if ($set_mid == 'Y') {
      $email->MessageID = '<'.$messageid.$f_mail.'>';
      }
$email->addCustomHeader("List-Unsubscribe: <mailto:$f_mail?sub=Unsubscribe");
                   $email->addCustomHeader("List-Unsubscribe-Post: List-Unsubscribe=One-Click");
		 $email->setFrom("$f_mail","$f_name");
		 $email->Subject = "$sub";	
		 if (trim($mailto) != ""){
			$email->addAddress("$mailto");
		 }
		 $email->Body = "$mszc";
		 
		 if ($email->send()) {
	        $errorflag = 0;
         } else {
            $errorflag = 1;
	        $erralert = $email->ErrorInfo;
         }
	 } // end ..... foreach 
    }  // end of if (trim($receipent) != "")
  }  // end of function send_test_mails()

  // Send only test mails if mode='t'
  if ($mode=="t") {
    send_test_mails();
  }

	  
  // Code for bulk mails
  if ($mode=="b") {

	  //Update time_beginning in script_sendgrids								 
	  $query_updscript_status = "update script_sendgrids 
						  set time_beginning = ".time().
						  ",script_status = 'RUNNING' where script_unique_id = $script_unique_id";  
	  $result_updscript_status = mysqli_query($dbc,$query_updscript_status)
						 or die ("Error while updating script_sendgrids");  
	  
	  // Send test mails 
	  send_test_mails();
	  
	  $cycle_time_beginning = time();
	  $cycle_endts = 0;
	  $timetowait = 0;
	  
	  // Get target list size
	  $limit = min($btoken_count,$token_update_limit); 
	  // end..Get target list size 
	  $query_list = "select id,email from triunique 
                     where mail_sent=0 and token = $token 
		             order by id ASC 
					 limit $limit";
      $result_list = mysqli_query($dbc,$query_list) or die ("Error while retrieving records from triunique");
	  
	  $sess_mailcount = 0;
	  $cyclemailcount = 0;
	  
	  while ($row = mysqli_fetch_array($result_list)) {
		if (get_runsignal($script_unique_id) == 1) 
		{					   
				try {	  
					$id = $row['id'];
					//Temporarily lock the row to prevent re-pickup from triunique
					$query_templock = "update triunique set mail_sent=2 where id = $id";  
					$result_templock = mysqli_query($dbc,$query_templock)
									   or die ("Error while updating triunique record for lock script_status"); 

					$email = new PHPMailer();
					$email->isSMTP();
					$email->Host = $server;
					$email->Port = $port;
					$email->SMTPAuth = true;
					$email->Username = $usr;
					$email->Password = $unique_smtp_password;
					$email->CharSet = 'UTF-8';
					if ($encrypting == 'B') {
	       $email->Encoding = 'base64';
          }
          if ($encrypting == 'D')
          {
          $email->Encoding = 'quoted-printable';
          }
          if ($encrypting == 'S')
          {
          $email->Encoding = '16bit';
          }
          if ($encrypting == 'E')
          {
          $email->Encoding = '8bit';
          }
					if ($mail_format == 'h') {
						 $email->isHTML(true);
					}  //one-click Unsubscribe changes
					if ($set_mid == 'Y') {
      $email->MessageID = '<'.$messageid.$f_mail.'>';
      }
$email->addCustomHeader("List-Unsubscribe: <mailto:$f_mail?sub=Unsubscribe");
                   $email->addCustomHeader("List-Unsubscribe-Post: List-Unsubscribe=One-Click");
					$email->setFrom("$f_mail","$f_name");
					$email->Subject = "$sub";
					$mailto = $row['email'];	
					if (trim($mailto) != ""){
					   $email->addAddress("$mailto");
					}

				    $clickid = $clickid_prefix.base64_encode($mailto);
					$mszc = str_replace('varx_clickid',$clickid,$mszc);
					
					$email->Body = "$mszc";
					
										
				 
					if ($email->send()) {
					   $errorflag = 0;
					} else {
					   $errorflag = 1;
					   $erralert = $email->ErrorInfo;
					   echo "$erralert";
					}
					
					$sess_mailcount++;	

					$cyclemailcount++;
					
					if ($cyclemailcount == $count_mail_limit) {
						// Send test mails 
						send_test_mails();
						// Feed delay if needed.
						$cycle_endts = time();
						$timetowait = $count_seconds - ($cycle_endts - $cycle_time_beginning);
						if ($timetowait > 0) {
							sleep($timetowait);
						}
						$cyclemailcount = 0;
						$cycle_time_beginning  = time();
					}
					
					//Update triunique
					$query_updtriunique = "update triunique set mail_sent=1 where id = $id";  
					$result_updtriunique = mysqli_query($dbc,$query_updtriunique)
									   or die ("Error while updating triunique");
										 
					//Update autoscript_status_smtp								 
					$query_updscript_status = "update script_sendgrids 
										  set recent_count = recent_count + 1,
											  script_status = 'RUNNING',
											  time_end_sent = ".time().
										  " where script_unique_id = $script_unique_id";  
					$result_updscript_status = mysqli_query($dbc,$query_updscript_status)
										or die ("Error while updating script_sendgrids");								 
				 }	catch (Exception $e) {
					   echo "Err id: $id ";
					   echo $e->getMessage();
				 }
        } else {
			break;
        }			
	  } // end of while
	  
	  if ($sess_mailcount >= $limit) {
		  //Update autoscript_status_mg								 
		  $query_updscript_status = "update script_sendgrids 
							  set script_status = 'COMPLETE' where script_unique_id = $script_unique_id";  
		  $result_updscript_status = mysqli_query($dbc,$query_updscript_status)
							  or die ("Error while updating script_status in script_sendgrids");		  
      }

  } // end of if ($mode=="b")


mysqli_close($dbc);
  
?>
