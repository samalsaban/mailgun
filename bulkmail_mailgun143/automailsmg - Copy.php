<?php

/**
 * Include external classes
**/
echo "Run id: ".$argv[1];

require 'vendor/autoload.php';
use Mailgun\Mailgun;

require("../db/db.php");
global $dbc;

$runid = $argv[1];

// get script_mailguns record for current runid
$query1 = "select token,count_seconds,count_mail_limit,mail_holdl,btoken_count,token_update_limit,script_status
          from script_mailguns where runid = $runid";
		  
$result1 = mysqli_query($dbc,$query1)
          or die ("Error while retrieving records from script_mailguns");
$rowContent1 = mysqli_fetch_array($result1);

// get script_mailgunc record for current runid
$query2 = "select id,sub,f_mail,f_name,receipent,mail_format,domain,unique_smtp_password,mode,mszc
          from script_mailgunc where runid = $runid";
		  
$result2 = mysqli_query($dbc,$query2)
          or die ("Error while retrieving records from script_mailgunc");
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
  $f_name     = $rowContent2['f_name']; 
  $receipent       = $rowContent2['receipent'];
  $mail_format         = $rowContent2['mail_format'];
  $domain        = $rowContent2['domain'];	  
  $unique_smtp_password        = $rowContent2['unique_smtp_password'];
  $mode          = $rowContent2['mode'];
  $mszc      = $rowContent2['mszc'];  
  
  // Create record for runscript_mailun if script_status='READY' else update
  if ($script_status == 'READY') {
     $query = "insert into runscript_mailun (runid,runsignal) values (".$runid.",'1')"; 
  }
  else {
	 $query = "update runscript_mailun set runsignal = '1' where runid = $runid";
  }
  $result = mysqli_query($dbc,$query) or die ("Error while inserting/updating record in runscript_mailun"); 
  
  function get_runsignal (int $runid){
	 global $dbc;
     $query_signal = "select runsignal from runscript_mailun where runid = $runid";
	 $result_signal = mysqli_query($dbc,$query_signal) or die ("Error while retrieving records from runscript_mailun");
     $row_signal = mysqli_fetch_array($result_signal);	
     return ($row_signal['runsignal']);	 
  }  

  function send_test_mails(){
	global $receipent;
    global $domain;
    global $unique_smtp_password;
	global $mail_format;
	global $f_mail;
	global $f_name;
	global $sub;
	global $mszc;  
	if (trim($receipent) != "") {
       //$email->SMTPDebug = 2;
	   $mail_list = explode(',',trim($receipent));
	   foreach ( $mail_list as $mailto) {
          $mgClient = new Mailgun($unique_smtp_password);
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
	      } // end of 	($mail_format == 'h')
	   } // end of foreach
    }  // end of if (trim($receipent) != "")
  }  // end of function send_test_mails()

  // Send only test mails if mode='t'
  if ($mode=="t") {
    send_test_mails();
  }
 
  // Code for bulk mails
  if ($mode=="b") {
	  
	  //Update time_beginning in autoscript_status_smtp								 
	  $query_updscript_status = "update script_mailguns 
						  set time_beginning = ".time().
						  ",script_status = 'RUNNING' where runid = $runid";  
	  $result_updscript_status = mysqli_query($dbc,$query_updscript_status)
						 or die ("Error while updating script_mailguns");  
	  
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
		if (get_runsignal($runid) == 1) 
		{

		 try {	  
		 	 //Temporarily lock the row to prevent re-pickup from triunique
			 $query_templock = "update triunique set mail_sent=2 where id = $id";  
			 $result_templock = mysqli_query($dbc,$query_templock)
			                    or die ("Error while updating triunique record for lock script_status");
	  
	          $mgClient = new Mailgun($unique_smtp_password);
	   
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
								 
	          //Update script_mailguns								 
							 
			  $query_updscript_status = "update script_mailguns 
								  set recent_count = recent_count + 1,
									  script_status = 'RUNNING',
									  time_end_sent = ".time().
								  " where runid = $runid";  
			  $result_updscript_status = mysqli_query($dbc,$query_updscript_status)
								  or die ("Error while updating autoscript_status_smtp");								 
		   
         }	catch (Exception $e) {
               echo "Err id: $id ";
		       echo $e->getMessage();
         }							   
		} else {
			break;
        }	

	  } // end of while
	  
	  if ($sess_mailcount == $limit) {
		  //Update script_mailguns								 
		  $query_updscript_status = "update script_mailguns 
							  set script_status = 'COMPLETE' where runid = $runid";  
		  $result_updscript_status = mysqli_query($dbc,$query_updscript_status)
							  or die ("Error while updating script_status in script_mailguns");		  
      }
  } // end of if ($mode=="b")
  

mysqli_close($dbc);
 
?>