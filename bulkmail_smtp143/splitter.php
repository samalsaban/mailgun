<?php
/**
 * Include external routines
**/
require("../db/db.php");
global $dbc;

//
$olist = array();	

// Set parallel processing parameters
$process_limit   = 100;
$mail_per_thread = 1;

//Find set variables from command line arguments
$token          = $argv[1];
$batch_start_id  = $argv[2];
$batch_end_id    = $argv[3];

$olist = array();	

$thread_start_id = $batch_start_id;

echo $token."------".$batch_start_id."------".$batch_end_id;

while ($thread_start_id <= $batch_end_id) {
	// Set thread Start id and thread End id
	$remaining = $batch_end_id - $thread_start_id + 1;
	if ($remaining >= $mail_per_thread) {
	  $thread_end_id = $thread_start_id + $mail_per_thread - 1;

	} else {
	   $thread_end_id = $batch_end_id;
	}
	  //process with $thread_start_id & $curr_end_id;
	  // Split start
 
	  while (true) {
			// Count current number of active processes
			$query_prc  = "select count(1) from information_schema.processlist";
			$result_prc = mysqli_query($dbc,$query_prc);
			$prcTotal   = mysqli_fetch_array($result_prc);
			$prcCount   = array_shift($prcTotal);	
			// End..Count current number of active processes
			if ($prcCount >= $process_limit) {
				// Dummy delay
				$cnt = 0;
				for ($d = 1; $d <= 99999; $d++) {	
				 $cnt++;
				}			
			  continue;
			} else {
			  // Call background processes to send mail(s) in parallel
              //echo "--->thread_start_id:".$thread_start_id." : thread_end_id:".$thread_end_id ; 
			  $olist = array();	
			  exec("php /var/www/html/bulkmail_smtp143/sendmail_b.php $token $thread_start_id $thread_end_id >> bulk_err &",$olist);	
              break;			  
			}
	  }
	  // Split end
	  $thread_start_id = $thread_end_id + 1;
}

  
?>

