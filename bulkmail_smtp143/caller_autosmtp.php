<?php
require("../db/db.php");
global $dbc;

$script_unique_id = $argv[1];



//Fetch script_smtps record for current script_unique_id
$query = "select script_status from script_smtps 
		   where script_unique_id = $script_unique_id";
$result = mysqli_query($dbc,$query)
		  or die ("Error while retrieving records");
$row = mysqli_fetch_array($result);		  
if ($row)	{
	if ($row['script_status'] == "COMPLETE") {
		echo "Run id $script_unique_id is already in 'COMPLETE' script_status";
	} elseif ($row['script_status'] == "RUNNING"){
		echo "Run id $script_unique_id is already in 'RUNNING' script_status";
	} else {
		//Call automailsmtp.php
		$olist = array();
		exec("nohup php /var/www/html/bulkmail_smtp143/automailsmtp.php $script_unique_id >> smtp_err &",$olist);
		echo "Auto run started..";
	}

} else {
   echo "Invalid run id.";
   echo "---------------";
}   	

mysqli_close($dbc);


?>


