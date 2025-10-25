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
function encodeEmail($email) {
	$email = strtoupper($email);
	$email_length = strlen($email);
	$conv_code = "";
	$newstring = "";

	for ($i = 0; $i < $email_length; $i++) {
	  $conv_code =   ord(substr($email,$i,1));
	  $newstring .= chr($conv_code+2);
	} 
	// Replacements to shorten the String
	$newstring = str_replace('0EQO','c',$newstring);
	$newstring = str_replace('0PGV','n',$newstring);
	$newstring = str_replace('BIOCKN','g',$newstring);
	$newstring = str_replace('BDKIRQPF','d',$newstring);
	$newstring = str_replace('BJQVOCKN','h',$newstring);
	$newstring = str_replace('BQRVWUPGV','o',$newstring);
	$newstring = str_replace('BCQN','a',$newstring);
	$newstring = str_replace('B[CJQQ','y',$newstring);
	$newstring = str_replace('BQWVNQQM','l',$newstring);
	$newstring = str_replace('BNKXG','v',$newstring);
	$newstring = str_replace('BOUP','m',$newstring);
  
    return $newstring;
}



$token_t = $argv[1];


// For Sendgrid
$server = "smtp.sendgrid.net";
$port = "587";
$usr = "apikey";
//$passwd = $unique_smtp_password;

// get content
$query_content = "select mszc from normal_sendgridc where token=$token_t limit 1";
		  
$result_content = mysqli_query($dbc,$query_content)
          or die ("Error while retrieving content record");
$rowContent = mysqli_fetch_array($result_content);
$mszc = 	array_shift($rowContent);		  

// get list
$query = "select id,sub,f_mail,f_name,receipent,mail_format,set_mid,m_mail,xm,encrypting,unique_smtp_password,mode
          from normal_sendgrids where token=$token_t";
$result = mysqli_query($dbc,$query)
          or die ("Error while retrieving records");


while ($row = mysqli_fetch_array($result)) {
      $id = $row['id'];
	  
	  //Temporarily lock the row to prevent re-pickup
	  $query_templock = "update normal_sendgrids set confirmation=3 where id = $id";  
      $result_templock = mysqli_query($dbc,$query_templock)
             or die ("Error while updating record for lock script_status");
	  
	  //
      $sub = $row['sub'];
      $f_mail = $row['f_mail'];
      $f_name = $row['f_name']; 
      $mailto = $row['receipent'];
      $set_mid=$row['set_mid'];
      $messageid = base64_encode($row['receipent']);
$m_mail=$row['m_mail'];
      $mail_format = $row['mail_format'];
      $passwd = $row['unique_smtp_password'];
      $encrypting = $row['encrypting'];
	  $xm = $row['xm'];
	  $mode = $row['mode'];
      $email = new PHPMailer();
      //$email->SMTPDebug = 2;
      $email->isSMTP();
      $email->Host = $server;
      $email->Port = $port;
      $email->SMTPAuth = true;
      $email->Username = $usr;
      $email->Password = $passwd;
      $email->SMTPSecure = 'tls';
      $email->CharSet = 'UTF-8';
      if ($encrypting == 'B') {
	       $email->Encoding = 'base64';
      }
      if ($encrypting == 'D')
      {
      $email->Encoding = 'quoted-printable';
      }
      if ($encrypting == 'S')
      {
      $email->Encoding = '16bit';
      }
      if ($encrypting == 'E')
      {
      $email->Encoding = '8bit';
      }
if ($xm == 'N') {
               $email->XMailer = ' ';
      }  
      if ($mail_format == 'h') {
	       $email->isHTML(true);
      } 
      if ($set_mid == 'Y') {
      $email->MessageID = '<'.$messageid.$f_mail.'>';
      }
$email->addCustomHeader("List-Unsubscribe: <mailto:$f_mail?sub=Unsubscribe");
                   $email->addCustomHeader("List-Unsubscribe-Post: List-Unsubscribe=One-Click");
      $email->setFrom("$f_mail","$f_name");
      $email->Subject = "$sub";
      $email->addAddress("$mailto");


      //Clickid handling
      $clickid = encodeEmail($mailto);
      $mszc = str_replace('varx_clickid',$clickid,$mszc);
      //end...Clickid handling

      $email->Body = "$mszc";
  
     if ($email->send()) {
	   $errorflag = 0;
	   $query1 = "update normal_sendgrids set confirmation=1 where id = $id";  
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
	  


