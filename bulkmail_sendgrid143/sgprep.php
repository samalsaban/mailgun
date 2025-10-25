<?php
/**
 * Include external routines
**/
require("../db/db.php");
global $dbc;
// Variables to receive form data
$mode       = $_POST["mode"];
$sub    = $_POST["sub"];
$f_mail = $_POST["f_mail"];
$repl = $_POST["repl"];
$f_name  = $_POST["f_name"]; 
$receipent    = $_POST["receipent"];
$mail_format      = $_POST["mail_format"];
$mszc   = $_POST["mszc"];
$token     = $_POST["token"];
$limit      = $_POST["limit"];
$unique_smtp_password     = $_POST["unique_smtp_password"];
$runmode    = $_POST["runmode"];
$tts        = $_POST["tts"];
$xm = $_POST["xm"];
$set_mid = $_POST["set_mid"];
$m_mail = $_POST["m_mail"];
$encrypting = $_POST["encrypting"]; // values are 'Y' or 'N'
$lts        = $_POST["lts"];

// Additional variables
$response = "";
$errorflag = 0;
$erralert = "";
$response = "";


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

// Find/initialize script_sendgrids script_unique_id
$query = "select IFNULL(max(script_unique_id),100) max_script_unique_id from script_sendgrids";
$result = mysqli_query($dbc,$query);
$rowsId = mysqli_fetch_array($result);
$sg_script_unique_id = array_shift($rowsId);
// end..Find/initialize script_sendgrids script_unique_id

// Get mailcount if mode='b'
if ($mode=="bulk"){
$query = "select IFNULL(count(id),0) mailcount from triunique where mail_sent=0 and token = $token";
$result = mysqli_query($dbc,$query);
$rowsId = mysqli_fetch_array($result);
$btoken_count = array_shift($rowsId);
} 
// end...Get mailcount if mode='b'  


// Insert records in to script_sendgrids and script_sendgridc
$sg_script_unique_id++;
if ($mode=="test"){
   // script_sendgrids
   $query = "insert into script_sendgrids (script_unique_id,count_seconds,count_mail_limit,time_ideal,script_status) ".
            "values (".$sg_script_unique_id.",".$tts.",".$lts.",".time().",'".'READY'."')"; 
   $result = mysqli_query($dbc,$query) or die ("Error while inserting record into script_sendgrids for test"); 
      
   // script_sendgridc
   $query = "insert into script_sendgridc (sub,f_mail,repl,f_name,receipent,mail_format,unique_smtp_password,xm,set_mid,m_mail,encrypting,mode,mszc,script_unique_id) ".
            "values ('".$sub."','".$f_mail."','".$repl."','".$f_name."','".$receipent."','".$lmail_format."','".
			 $aplikey."','".$xm."','".$set_mid."','".$m_mail."','".$encrypting."','".$lmode."','".$mszc."',".$sg_script_unique_id.")";        
   $result = mysqli_query($dbc,$query) or die ("Error while inserting record into script_sendgridc for test"); 

 } else {
   // script_sendgrids
   $query = "insert into script_sendgrids (script_unique_id,token,count_seconds,count_mail_limit,btoken_count,token_update_limit,recent_count,time_ideal,script_status) ".
            "values (".$sg_script_unique_id.",".$token.",".$tts.",".$lts.",".$btoken_count.",".$limit.",".'0'.",".time().",'".'READY'."')"; 
   $result = mysqli_query($dbc,$query) or die ("Error while inserting record into script_sendgrids for bulk"); 
   
   // script_sendgridc
   $query = "insert into script_sendgridc (token,sub,f_mail,repl,f_name,receipent,mail_format,unique_smtp_password,xm,set_mid,m_mail,encrypting,mode,mszc,script_unique_id) ".
            "values (".$token.",'".$sub."','".$f_mail."','".$repl."','".$f_name."','".$receipent."','".$lmail_format."','".
			 $unique_smtp_password."','".$xm."','".$set_mid."','".$m_mail."','".$encrypting."','".$lmode."','".$mszc."',".$sg_script_unique_id.")";      
   $result = mysqli_query($dbc,$query) or die ("Error while inserting record into script_sendgridc for bulk"); 	
 }	


mysqli_close($dbc);	

if ($errorflag == 0) {
 echo "<h2>Run Id: ".$sg_script_unique_id."</h2>";
} else {
  echo "Error occurred !";
  echo $response;
}	

?>
	  


