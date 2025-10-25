<?php
require("../db/db.php");
global $dbc;
$target_dir = "upload/";
$file_name = $_FILES["tempfile"]["name"];
$target_file = $target_dir ."emails.txt";
$uploadOk = 1;
$fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

// Check file size
if ($_FILES["tempfile"]["size"] = 0) {
    echo "Sorry, your file is empty.";
    $uploadOk = 0;
}
if ($_FILES["tempfile"]["size"] > 900000000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}
// Allow certain file formats
if($fileType != "txt") {
    echo "Sorry, only text files are allowed.";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["tempfile"]["tmp_name"], $target_file)) {
        echo "The file ". basename( $_FILES["tempfile"]["name"]). " has been uploaded.</br>";
		// Write to db
           $rcount = 0;
           $wcount = 0;
           $email_str = "";
           $query = "";
	   
           //Read from file and insert records into table.

           $file = fopen("upload/emails.txt", "r") or exit("Unable to open the file!");
           echo "file opened...";
		   // Generate file identifier
		   $token = rand(10,99).substr(time(),4,9).rand(1,9);
		   //end .. Generate file identifier
		   $emails = array();
           while(!feof($file))
           {
           $email_str = trim(fgets($file));
           //echo $email_str;
           if ($email_str != ""){
             $rcount++;
		     $emails[$rcount] = $email_str;
           }
         }
        fclose($file);
		// Write data into db
		$batchsize = 4000;
		$batchcount = 0;
		$wcount = 0;
		$query_prefix = "insert into triunique (token,email,mail_sent) values ";
		$query_ins_append = "";
		$query_ins = "";
		for ($i = 1; $i <= $rcount; $i++) {
			$email = $emails[$i];
			$batchcount ++;
			$query_ins_append .= "($token,'".$email."',0),";
			$wcount ++;
			if ($batchcount == $batchsize ) {
				$query_ins_append = rtrim($query_ins_append, ",");
				$query_ins = $query_prefix.$query_ins_append;
				$result_ins = mysqli_query($dbc,$query_ins) or die ("Error while inserting records into db...");		
				$batchcount = 0;
				$query_ins_append = "";		
			}
		}

		if ($batchcount > 0) {
			$query_ins_append = rtrim($query_ins_append, ",");
			$query_ins = $query_prefix.$query_ins_append;
			$result_ins = mysqli_query($dbc,$query_ins) or die ("Error while inserting records");		
		}

        mysqli_close($dbc);
        echo "$rcount record(s) read from file </br>";
        echo "$wcount record(s) inserted into database table </br>";
		// end writing to db
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
	echo "</br></br>";
	echo "<h1>File Id: $token</h1>";
}

?>



