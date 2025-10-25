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
		echo "Run id $runid is in 'COMPLETE' script_status";
	} elseif ($row['script_status'] == "READY") {
		echo "Run id $runid has not started yet";
	}
	else {
	    // Update record in runscript_mailgun
        $query = "update runscript_mailgun set runsignal = '0' where runid = $runid";
	    $result = mysqli_query($dbc,$query) 
		          or die ("Error while inserting/updating record in runscript_mailgun"); 
		$query_updscript_status = "update script_mailguns 
							set script_status = 'STOPPED'
							where runid = $runid";  
		$result_updscript_status = mysqli_query($dbc,$query_updscript_status)
							or die ("Error while updating script_mailguns");		
		echo "Auto run for runid $runid has been stopped.";
	}

} else {
   echo "Invalid run id.";
   echo "---------------";
}   	

mysqli_close($dbc);


?>


