<?php

/**
 * Include external classes
**/
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';
require("../db/db.php");
global $dbc;

//local function

$token_t = $argv[1];

// get content
$query_content = "select mszc from normal_smtpc where token=$token_t limit 1";
		  
$result_content = mysqli_query($dbc,$query_content)
          or die ("Error while retrieving content record");
$rowContent = mysqli_fetch_array($result_content);
$mszc = 	array_shift($rowContent);		  

// get list
$query = "select id,sub,f_mail,f_name,receipent,mail_format,server,port,usr,passwd,tls,mode
          from normal_smtps where token=$token_t";
$result = mysqli_query($dbc,$query)
          or die ("Error while retrieving records");


while ($row = mysqli_fetch_array($result)) {
      $id = $row['id'];
	  //Temporarily lock the row to prevent re-pickup
	  $query_templock = "update normal_smtps set confirmation=3 where id = $id";  
      $result_templock = mysqli_query($dbc,$query_templock)
             or die ("Error while updating normal_smtps record for lock script_status");
	  
	  //
      $subject = $row['sub'];
      $f_mail = $row['f_mail'];
      $f_name = $row['f_name']; 
      $mailto = $row['receipent'];
      $mail_format = $row['mail_format'];
      $server = $row['server'];
      $port = $row['port'];
      $usr = $row['usr'];
      $passwd = $row['passwd'];
      $tls = $row['tls'];	  
      $mode = $row['mode'];

      $email = new PHPMailer();
      //$email->SMTPDebug = 2;
      $email->isSMTP();
      $email->Host = $server;
      $email->Port = $port;
      $email->SMTPAuth = true;
      $email->Username = $usr;
      $email->Password = $passwd;
	  /*
	  if ($tls == 'Y') {
	    $email->SMTPSecure = 'tls';
      }
	  */
	  $email->SMTPAutoTLS = false; 
      $email->CharSet = 'UTF-8';
      if ($mail_format == 'h') {
	       $email->isHTML(true);
      }  
      $email->setFrom("$f_mail","$f_name");
      $email->Subject = "$subject";	  
      $email->addAddress("$mailto");
	  
      //Clickid handling
      
      //end...Clickid handling
	  
      $email->Body = "$mszc";	  

     if ($email->send()) {
	   $errorflag = 0;
	   $query1 = "update normal_smtps set confirmation=1 where id = $id";  
       $result1 = mysqli_query($dbc,$query1)
             or die ("Error while updating record");
	   //echo "Mail sent";
     } else {
	  $erralert = $email->ErrorInfo;
      echo "Error occurred in id: $id .".$erralert;
     }

  }

  mysqli_close($dbc);
  
?>
	  


