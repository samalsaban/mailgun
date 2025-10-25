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

// get script_smtps record for current script_unique_id
$query1 = "select token,count_seconds,count_mail_limit,test_mail_break,mail_holdl,btoken_count,token_update_limit,script_status
          from script_smtps where script_unique_id = $script_unique_id";
		  
$result1 = mysqli_query($dbc,$query1)
          or die ("Error while retrieving records from script_smtps");
$rowContent1 = mysqli_fetch_array($result1);

// get script_smtpc record for current script_unique_id
$query2 = "select id,sub,f_mail,f_name,receipent,mail_format,server,port,usr,passwd,tls,mode,mszc
          from script_smtpc where script_unique_id = $script_unique_id";
		  
$result2 = mysqli_query($dbc,$query2)
          or die ("Error while retrieving records from script_smtpc");
$rowContent2 = mysqli_fetch_array($result2);

//Get DB values into variables
  $token        = $rowContent1['token'];
  $count_seconds    = $rowContent1['count_seconds'];
  $count_mail_limit   = $rowContent1['count_mail_limit']; 
  $test_mail_break           = $rowContent1['test_mail_break']; 
//$mail_holdl  = $rowContent1['mail_holdl'];
  $btoken_count = $rowContent1['btoken_count'];
  $token_update_limit      = $rowContent1['token_update_limit'];
  $script_status        = $rowContent1['script_status'];	  

  $subject       = $rowContent2['sub'];
  $f_mail    = $rowContent2['f_mail'];
  $f_name     = $rowContent2['f_name']; 
  $receipent       = $rowContent2['receipent'];
  $mail_format         = $rowContent2['mail_format'];
  $server        = $rowContent2['server'];	  
  $port          = $rowContent2['port'];
  $usr           = $rowContent2['usr'];
  $passwd        = $rowContent2['passwd'];
  $tls           = $rowContent2['tls'];  
  $mode          = $rowContent2['mode'];
  $mszc      = $rowContent2['mszc']; 
  

	//get values from triuniquec	 
/*	$query_triuniquec =  "select offerid,usercode from triuniquec where token=$token";
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
	$clickid_prefix  = $date_prefix.$usercode.$offerid; */
	date_default_timezone_set('Asia/Kolkata');
  
  // Create record for runscript_smtp if script_status='READY' else update
  if ($script_status == 'READY') {
     $query = "insert into runscript_smtp (script_unique_id,runsignal) values (".$script_unique_id.",'1')"; 
  }
  else {
	 $query = "update runscript_smtp set runsignal = '1' where script_unique_id = $script_unique_id";
  }
  $result = mysqli_query($dbc,$query) or die ("Error while inserting/updating record in runscript_smtp"); 
  
  function get_runsignal (int $script_unique_id){
	 global $dbc;
     $query_signal = "select runsignal from runscript_smtp where script_unique_id = $script_unique_id";
	 $result_signal = mysqli_query($dbc,$query_signal) or die ("Error while retrieving records from runscript_smtp");
     $row_signal = mysqli_fetch_array($result_signal);	
     return ($row_signal['runsignal']);	 
  }
  
  function send_test_mails(){
	global $receipent;
	global $server;
	global $port;
	global $usr;
	global $passwd;
    global $tls;
	global $mail_format;
	global $f_mail;
	global $f_name;
	global $subject;
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
		 $email->setFrom("$f_mail","$f_name");
		 $email->Subject = "$subject";	
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
  }

  // Send only test mails if mode='t'
  if ($mode=="t") {
    send_test_mails();
  }

	  
  // Code for bulk mails
  
  if ($mode=="b") {

	  //Update time_beginning in script_smtps								 
	  $query_updscript_status = "update script_smtps 
						  set time_beginning = ".time().
						  ",script_status = 'RUNNING' where script_unique_id = $script_unique_id";  
	  $result_updscript_status = mysqli_query($dbc,$query_updscript_status)
						 or die ("Error while updating script_smtps");  
	  

	  $cycle_time_beginning = time();
	  $cycle_endts = 0;
	  $timetowait = 0;
	  
	  // Get target list size
	  $limit = min($btoken_count,$token_update_limit); 
	  // end..Get target list size 

	  // Send test mails 
	  send_test_mails();

	  $query_list = "select id,email from triunique 
                     where mail_sent=0 and token = $token 
		             order by id ASC 
					 limit $limit";
      $result_list = mysqli_query($dbc,$query_list) or die ("Error while retrieving records from triunique");
	  
	  $sess_mailcount = 0;
	  $cyclemailcount = 0;
	  
	  $btoken_counter = 0;
  
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
					$email->setFrom("$f_mail","$f_name");
					$email->Subject = "$subject";
					$mailto = $row['email'];	
					if (trim($mailto) != ""){
					   $email->addAddress("$mailto");
					}
					
				    //Clickid handling
					/*
					ob_clean();
					//ob_end_clean();
				    $clickid = $clickid_prefix.base64_encode($mailto);
				    $mszc = str_replace('varx_clickid',$clickid,$mszc);
					//one-click Unsubscribe changes
					$messageid  = base64_encode($mailto);
					/*$email->MessageID = '<'.$messageid.$f_mail.'>';
					$email->addCustomHeader("List-Unsubscribe: <mailto:$f_mail?sub=Unsubscribe");
					$email->addCustomHeader("List-Unsubscribe-Post: List-Unsubscribe=One-Click");		*/			
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

						// Feed delay if needed.
						$cycle_endts = time();
						$timetowait = $count_seconds - ($cycle_endts - $cycle_time_beginning);
						if ($timetowait > 0) {
							sleep($timetowait);
						}
						$cyclemailcount = 0;
						$cycle_time_beginning  = time();
					}

					// Send test mail
					$btoken_counter++;
					if ($btoken_counter == $test_mail_break) {
						send_test_mails();
						$btoken_counter = 0;
					}
					// ... end ..send test mail
					
					//Update triunique
					$query_updtriunique = "update triunique set mail_sent=1 where id = $id";  
					$result_updtriunique = mysqli_query($dbc,$query_updtriunique)
									   or die ("Error while updating triunique");
										 
					//Update script_smtps								 
					$query_updscript_status = "update script_smtps 
										  set recent_count = recent_count + 1,
											  script_status = 'RUNNING',
											  time_end_sent = ".time().
										  " where script_unique_id = $script_unique_id";  
					$result_updscript_status = mysqli_query($dbc,$query_updscript_status)
										or die ("Error while updating script_smtps");								 
				 }	catch (Exception $e) {
					   echo "Err id: $id ";
					   echo $e->getMessage();
				 }
        } else {
			break;
        }			
	  } // end of while
	  
	  if ($sess_mailcount == $limit) {
		  //Update autoscript_status_mg								 
		  $query_updscript_status = "update script_smtps 
							  set script_status = 'COMPLETE' where script_unique_id = $script_unique_id";  
		  $result_updscript_status = mysqli_query($dbc,$query_updscript_status)
							  or die ("Error while updating script_status in script_smtps");		  
      }

  } // end of if ($mode=="b")


mysqli_close($dbc);
  
?>







