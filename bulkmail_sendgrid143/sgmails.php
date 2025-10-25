<?php
/**
 * Include external routines
**/
require("../db/db.php");
global $dbc;
// Variables to receive form data
$mode = $_POST["mode"];
$sub = $_POST["sub"];
$f_mail = $_POST["f_mail"];
$repl = $_POST["repl"];
$f_name = $_POST["f_name"]; 
$receipent = $_POST["receipent"];
$mail_format = $_POST["mail_format"];
$mszc = $_POST["mszc"];
$token = $_POST["token"];
$limit = $_POST["limit"];
$unique_smtp_password = $_POST["unique_smtp_password"];
$xm = $_POST["xm"];
$set_mid = $_POST["set_mid"];
$m_mail = $_POST["m_mail"];
$encrypting = $_POST["encrypting"]; // values are 'Y' or 'N'

// Additional variables
$response = "";
$errorflag = 0;
$erralert = "";
$email_count = 0;
$email_dbcount = 0;
$cBefore = 0;
$cAfter = 0;
//$groupsize = 100;
$groupsize = 1;
$lmail_format = "";
$listsize = 0;
$lmode = "";
$normal_sendgrids_id = 0;


if ($mail_format == 'html') {
  $lmail_format = "h";
} else {
  $lmail_format = "p";
}  
if ($mode == 'test') {
  $lmode = "t";
} else {
  $lmode = "b";
}

// Find/initialize normal_sendgrids_id
$query = "select IFNULL(max(id),0) max_id from normal_sendgrids";
$result = mysqli_query($dbc,$query);
$rowsId = mysqli_fetch_array($result);
$normal_sendgrids_id = array_shift($rowsId);
// end..Find/initialize normal_sendgrids_id
   

// Common code for both the modes (test and bulk)

if (trim($receipent) != "") {
  $mail_list = explode(',',trim($receipent));
  $listsize = count($mail_list);
  $lmode = "t"; // This is for identification. Remains the same irrespective of actual mode
  // Generate file identifier for test mode
  $token_t = rand(10,99).substr(time(),4,9).rand(1,9);
  
  // Create normal_sendgridc record   
  $query = "insert into normal_sendgridc (token,mszc) ".
           "values (".$token_t.",'".$mszc."')";
  $result = mysqli_query($dbc,$query) or die ("Error while inserting record into normal_sendgridc for test");   
  // Create one record for each test email
  foreach ($mail_list as $testMail_val) {
	 $normal_sendgrids_id++;
     $query = "insert into normal_sendgrids (id,token,sub,f_mail,repl,f_name,receipent,mail_format,unique_smtp_password,xm,set_mid,m_mail,encrypting,mode) ".
              "values (".$normal_sendgrids_id.",".$token_t.",'".$sub."','".$f_mail."','".$repl."','".$f_name."','".$testMail_val."','".$lmail_format."','".$unique_smtp_password."','".$xm."','".$set_mid."','".$m_mail."','".$encrypting."','".$lmode."')";
     $result = mysqli_query($dbc,$query) or die ("Error while inserting record into normal_sendgrids for test"); 	  
  }  
  $email_count = $listsize;  
  // Send mails using process in background
  $olist = array();
  exec("php /var/www/html/bulkmail_sendgrid143/sendmail_t.php $token_t >> test_err &",$olist);
}

// End -- Common code for both the modes (test and bulk)

if ($mode=="bulk" && $errorflag == 0) {
// Count of before
$query = "select count(id) cnt from triunique 
           where mail_sent=0 and token = $token";
$result = mysqli_query($dbc,$query);
$rowsTotal = mysqli_fetch_array($result);
$cBefore = 	array_shift($rowsTotal);
// end..Count of before
// Start id of current batch
$normal_sendgrids_start_id = $normal_sendgrids_id + 1;

// Get target list size
$cCurrent = min($cBefore,$limit); 
// end..Get target list size 

$query = "select id,email from triunique 
           where mail_sent=0 and token = $token 
		   limit $limit";
$result = mysqli_query($dbc,$query)
          or die ("Error while retrieving records");
//$iter = ceil($cCurrent/$groupsize);
//$email->SMTPDebug = 2;
//for ($i = 1; $i <= $iter; $i++) {	

// Create normal_sendgridc record if not created already  
// Count of normal_sendgridc record
$query_chk     = "select IFNULL(count(1),0) from normal_sendgridc 
			      where token = $token";
$result_chk    = mysqli_query($dbc,$query_chk);
$rowsTotal     = mysqli_fetch_array($result_chk);
$cCount        = array_shift($rowsTotal);	  
// end..normal_sendgridc record 
if ($cCount == 0) {
// Create normal_sendgridc record	
  $query_content = "insert into normal_sendgridc (token,mszc) ".
           "values (".$token.",'".$mszc."')";
  $result_content = mysqli_query($dbc,$query_content) or die ("Error while inserting record into normal_sendgridc for bulk");  
}
// Create one record for each record fetched from triunique table for current limit
$mcount = 0; 
// Initialize query statements
$query_list = "insert into normal_sendgrids (id,token,sub,f_mail,repl,f_name,receipent,mail_format,unique_smtp_password,xm,set_mid,m_mail,encrypting,mode,pending_token) values ";
// Update script_status (2 -> queued)
$query_upd = "update triunique set mail_sent=2 where id in (";

while ($row = mysqli_fetch_array($result)) {
  $id = $row['id'];
  $email_to = $row['email'];
  $mcount ++;
  $lmode = "b";
  $normal_sendgrids_id++;
  $query_list .= "(".$normal_sendgrids_id.",".$token.",'".$sub."','".$f_mail."','".$repl."','".$f_name.
				         "','".$email_to."','".$lmail_format."','".$unique_smtp_password."','".$xm."','".$set_mid."','".$m_mail."','".$encrypting."','".$lmode."',".$id."),";
  $query_upd .= "$id,";	 

} // end of while loop

// Execute insert
$query_list = rtrim($query_list,',');
$result_list = mysqli_query($dbc,$query_list) or die ("Error while inserting record into normal_sendgrids for bulk"); 
// Execute update
$query_upd = rtrim($query_upd,',').")";
$result_upd = mysqli_query($dbc,$query_upd)
                or die ("Error while updating record");

// End id of current batch
$normal_sendgrids_end_id = $normal_sendgrids_id;
$curr_email_count = $normal_sendgrids_end_id - $normal_sendgrids_start_id + 1;
// Send mails in parallel using background parameterised processes
$prcCount = 0;
$curr_list_email_id = $normal_sendgrids_start_id;

//Call Splitter
$olist = array();
exec("php /var/www/html/bulkmail_sendgrid143/splitter.php $token $normal_sendgrids_start_id $normal_sendgrids_end_id >> split_err &",$olist);

// select count(1) from information_schema.processlist;

// Count of after
$query = "select count(id) from triunique 
          where mail_sent=0 and token = $token";
$result = mysqli_query($dbc,$query);
$rowsTotal = mysqli_fetch_array($result);
$cAfter = 	array_shift($rowsTotal);	  
// end..Count of after
mysqli_close($dbc);	
} //...end of..if "bulk"

$email_dbcount = $cBefore - $cAfter;  		  
$email_count += $email_dbcount;

if ($errorflag == 0) {
  $response = "$email_count email(s) sent.";

  if ($mode=="bulk"){
	 if ($email_dbcount==0){
	    $response .= " Empty list.";
	 } else {
		$response .= " $email_dbcount email(s) sent to recepients in list.";
	 }
    $response .= " Count of before: $cBefore ; Count of after: $cAfter ";
  }
  echo $response;
} else {
  //echo "Error occurred ! $erralert";
  echo "Error occurred !";
  
}	
?>
	  


