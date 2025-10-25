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
		echo "Run id $script_unique_id is in 'COMPLETE' script_status";
	} elseif ($row['script_status'] == "READY") {
		echo "Run id $script_unique_id has not started yet";
	}
	else {
	    // Update record in runscript_smtp
        $query = "update runscript_smtp set runsignal = '0' where script_unique_id = $script_unique_id";
	    $result = mysqli_query($dbc,$query) 
		          or die ("Error while inserting/updating record in runscript_smtp"); 
		$query_updscript_status = "update script_smtps 
							set script_status = 'STOPPED'
							where script_unique_id = $script_unique_id";  
							
        for ($i = 1; $i <=5 ; $i++) {							
			$result_updscript_status = mysqli_query($dbc,$query_updscript_status)
								or die ("Error while updating script_smtps");
            sleep(2);								
        }
		
		//echo "Auto run for script_unique_id $script_unique_id has been stopped.";
	}

} else {
   echo "Invalid run id.";
   echo "---------------";
}   	

mysqli_close($dbc);


?>


