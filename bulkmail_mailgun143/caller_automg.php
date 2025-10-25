<?php
require("../db/db.php");
global $dbc;

$runid = $argv[1];



//Fetch script_mailguns record for current runid
$query = "select script_status from script_mailguns 
		   where runid = $runid";
$result = mysqli_query($dbc,$query)
		  or die ("Error while retrieving records");
$row = mysqli_fetch_array($result);		  
if ($row)	{
	if ($row['script_status'] == "COMPLETE") {
		echo "Run id $runid is already in 'COMPLETE' script_status";
	} else {
		//Call automailsmg.php
		$olist = array();
		exec("nohup php /var/www/html/bulkmail_mailgun143/automailsmg.php $runid >> mg_err &",$olist);
		echo "Auto run started..";
	}

} else {
   echo "Invalid run id.";
   echo "---------------";
}   	

mysqli_close($dbc);


?>


