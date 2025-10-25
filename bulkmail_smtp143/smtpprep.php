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
$f_name  = $_POST["f_name"]; 
$receipent    = $_POST["receipent"];
$mail_format      = $_POST["mail_format"];
$mszc   = $_POST["mszc"];
$token     = $_POST["token"];
$limit      = $_POST["limit"];
$server     = $_POST["server"];
$port       = $_POST["port"];
$usr        = $_POST["usr"];
$passwd     = $_POST["passwd"];
$tls        = $_POST["tls"];
$runmode    = $_POST["runmode"];
$tts        = $_POST["tts"];
$lts        = $_POST["lts"];
$test_mail_break        = $_POST["test_mail_break"];

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

// Find/initialize script_smtps script_unique_id
$query = "select IFNULL(max(script_unique_id),100000) max_script_unique_id from script_smtps";
$result = mysqli_query($dbc,$query);
$rowsId = mysqli_fetch_array($result);
$smtp_script_unique_id = array_shift($rowsId);
// end..Find/initialize script_smtps script_unique_id

// Get mailcount if mode='b'
if ($mode=="bulk"){
$query = "select IFNULL(count(id),0) mailcount from triunique where mail_sent=0 and token = $token";
$result = mysqli_query($dbc,$query);
$rowsId = mysqli_fetch_array($result);
$btoken_count = array_shift($rowsId);
} 
// end...Get mailcount if mode='b'  


// Insert records in to script_smtps and script_smtpc
$smtp_script_unique_id++;
if ($mode=="test"){
   // script_smtps
   $query = "insert into script_smtps (script_unique_id,count_seconds,count_mail_limit,time_ideal,script_status) ".
            "values (".$smtp_script_unique_id.",".$tts.",".$lts.",".time().",'".'READY'."')"; 
   $result = mysqli_query($dbc,$query) or die ("Error while inserting record into script_smtps for test"); 
      
   // script_smtpc
   $query = "insert into script_smtpc (sub,f_mail,f_name,receipent,mail_format,server,port,usr,passwd,tls,mode,mszc,script_unique_id) ".
            "values ('".$sub."','".$f_mail."','".$f_name."','".$receipent."','".$lmail_format."','".
			 $server."','".$port."','".$usr."','".$passwd."','".$tls."','".$lmode."','".$mszc."',".$smtp_script_unique_id.")";        
   $result = mysqli_query($dbc,$query) or die ("Error while inserting record into script_smtpc for test"); 

 } else {
   // script_smtps
   $query = "insert into script_smtps (script_unique_id,token,count_seconds,count_mail_limit,test_mail_break,btoken_count,token_update_limit,recent_count,time_ideal,script_status) ".
            "values (".$smtp_script_unique_id.",".$token.",".$tts.",".$lts.",".$test_mail_break.",".$btoken_count.",".$limit.",".'0'.",".time().",'".'READY'."')"; 
   $result = mysqli_query($dbc,$query) or die ("Error while inserting record into script_smtps for bulk"); 
   
   // script_smtpc
   $query = "insert into script_smtpc (token,sub,f_mail,f_name,receipent,mail_format,server,port,usr,passwd,tls,mode,mszc,script_unique_id) ".
            "values (".$token.",'".$sub."','".$f_mail."','".$f_name."','".$receipent."','".$lmail_format."','".
			 $server."','".$port."','".$usr."','".$passwd."','".$tls."','".$lmode."','".$mszc."',".$smtp_script_unique_id.")";      
   $result = mysqli_query($dbc,$query) or die ("Error while inserting record into script_smtpc for bulk"); 	
 }	


mysqli_close($dbc);	

if ($errorflag == 0) {
 echo "<h2>Script Number: ".$smtp_script_unique_id."</h2>";
} else {
  echo "Error occurred !";
  echo $response;
}	

?>
	  


