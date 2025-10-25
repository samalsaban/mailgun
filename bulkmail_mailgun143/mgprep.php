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
$domain     = $_POST["domain"];
$pvtkey     = $_POST["pvtkey"];
$runmode    = $_POST["runmode"];
$tts        = $_POST["tts"];
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

// Find/initialize script_mailguns script_unique_id
$query = "select IFNULL(max(script_unique_id),100) max_script_unique_id from script_mailguns";
$result = mysqli_query($dbc,$query);
$rowsId = mysqli_fetch_array($result);
$mg_script_unique_id = array_shift($rowsId);
// end..Find/initialize script_mailguns script_unique_id

// Get mailcount if mode='b'
if ($mode=="bulk"){
$query = "select IFNULL(count(id),0) mailcount from triunique where mail_sent=0 and token = $token";
$result = mysqli_query($dbc,$query);
$rowsId = mysqli_fetch_array($result);
$btoken_count = array_shift($rowsId);
} 
// end...Get mailcount if mode='b'  


// Insert records in to script_mailguns and script_mailgunc
$mg_script_unique_id++;
if ($mode=="test"){
   // script_mailguns
   $query = "insert into script_mailguns (script_unique_id,count_seconds,count_mail_limit,time_ideal,script_status) ".
            "values (".$mg_script_unique_id.",".$tts.",".$lts.",".time().",'".'READY'."')"; 
   //$result = mysqli_query($dbc,$query) or die ("Error while inserting record into script_mailguns for test"); 
   $result = mysqli_query($dbc,$query) or die ("Error while inserting record into script_mailguns for test."); 
   
   // script_mailgunc
   $query = "insert into script_mailgunc (sub,f_mail,f_name,receipent,mail_format,domain,unique_smtp_password,mode,mszc,script_unique_id) ".
            "values ('".$sub."','".$f_mail."','".$f_name."','".$receipent."','".$lmail_format."','".
			 $domain."','".$pvtkey."','".$lmode."','".$mszc."',".$mg_script_unique_id.")";        
   $result = mysqli_query($dbc,$query) or die ("Error while inserting record into script_mailgunc for test"); 

 } else {
   // script_mailguns
   $query = "insert into script_mailguns (script_unique_id,token,count_seconds,count_mail_limit,btoken_count,token_update_limit,recent_count,time_ideal,script_status) ".
            "values (".$mg_script_unique_id.",".$token.",".$tts.",".$lts.",".$btoken_count.",".$limit.",'0',".time().",'".'READY'."')"; 
   $result = mysqli_query($dbc,$query) or die ("Error while inserting record into script_mailguns for bulk"); 
   
   // script_mailgunc
   $query = "insert into script_mailgunc (token,sub,f_mail,f_name,receipent,mail_format,domain,unique_smtp_password,mode,mszc,script_unique_id) ".
            "values (".$token.",'".$sub."','".$f_mail."','".$f_name."','".$receipent."','".$lmail_format."','".
			 $domain."','".$pvtkey."','".$lmode."','".$mszc."',".$mg_script_unique_id.")";        
   $result = mysqli_query($dbc,$query) or die ("Error while inserting record into script_mailgunc for bulk"); 	
 }	


mysqli_close($dbc);	

if ($errorflag == 0) {
 echo "<h2>Run Id: ".$mg_script_unique_id."</h2>";
} else {
  echo "Error occurred !";
  echo $response;
}	

?>
	  


