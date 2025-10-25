<?php
// db credentials
require("../db/db.php");
global $dbc;
// Delete records from normal_sendgrids where mail sent successfully
// Delete older records from normal_sendgridc keeping last 20 records

// Find the id of last record
$query = "select IFNULL(max(id),0) max_id from normal_sendgrids";
$result = mysqli_query($dbc,$query);
$row_id = mysqli_fetch_array($result);
$normal_sendgrids_maxid = array_shift($row_id);
// end..Find the id of last record	
// Delete all but last 20 records from normal_sendgridc 
$normal_sendgrids_retain_pt = $normal_sendgrids_maxid - 20;
$query = "delete from normal_sendgrids where id <=  $normal_sendgrids_retain_pt and confirmation in (1, 3)";
$result = mysqli_query($dbc,$query)
          or die ("Error while deleting records from normal_sendgrids");


// Find the id of last record
$query = "select IFNULL(max(seqn),0) max_seqn from normal_sendgridc";
$result = mysqli_query($dbc,$query);
$rowsSeqn = mysqli_fetch_array($result);
$normal_sendgridc_maxseqn = array_shift($rowsSeqn);
// end..Find the id of last record	

// Delete all but last 20 records from normal_sendgridc 
$normal_sendgridc_retain_pt = $normal_sendgridc_maxseqn - 20;
$query = "delete from normal_sendgridc where seqn <=  $normal_sendgridc_retain_pt";
$result = mysqli_query($dbc,$query)
          or die ("Error while deleting records from normal_sendgridc"); 
		  
mysqli_close($dbc);
  
?>
	  


