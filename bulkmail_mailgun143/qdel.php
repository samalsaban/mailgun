<?php
// db credentials
require("../db/db.php");
global $dbc;
// Delete records from msgq_list where mail sent successfully
// Delete older records from msgq_content keeping last 20 records

// Find the id of last record
$query = "select IFNULL(max(id),0) max_id from normal_mailguns";
$result = mysqli_query($dbc,$query);
$row_id = mysqli_fetch_array($result);
$msgq_list_maxid = array_shift($row_id);
// end..Find the id of last record	
// Delete all but last 20 records from msgq_content 
$msgq_list_retain_pt = $msgq_list_maxid - 20;
$query = "delete from normal_mailguns where id <=  $msgq_list_retain_pt and confirmation in (1, 3)";
$result = mysqli_query($dbc,$query)
          or die ("Error while deleting records from normal_mailguns");


// Find the id of last record
$query = "select IFNULL(max(seqn),0) max_seqn from normal_mailgunc";
$result = mysqli_query($dbc,$query);
$rowsSeqn = mysqli_fetch_array($result);
$msgq_content_maxseqn = array_shift($rowsSeqn);
// end..Find the id of last record	

// Delete all but last 20 records from msgq_content 
$msgq_content_retain_pt = $msgq_content_maxseqn - 20;
$query = "delete from normal_mailgunc where seqn <=  $msgq_content_retain_pt";
$result = mysqli_query($dbc,$query)
          or die ("Error while deleting records normal_mailgunc"); 
		  
mysqli_close($dbc);
  
?>
	  


