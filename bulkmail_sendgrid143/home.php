<?php  
session_start(); 
if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) { 
?> 
<!doctype html> 
<html> 
<head> 
  <title>SendGrid Mailer</title>
  <script>
    function displayHTML() { 
      var content = document.getElementById("mszc").value; 
      popupWin = window.open('','popup','toolbar=no,status=no,width=500,height=600'); 
      popupWin.document.writeln(content); 
    } 
  </script>

  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: "Segoe UI", Arial, sans-serif;
      padding: 0;
      margin: 0;
      background-color: #e9edf2;
      color: #333;
    }

    a {
      text-decoration: none;
      color: #0d6efd;
      font-weight: bold;
      margin: 10px;
      display: inline-block;
    }

    .header {
  background-color: #1f2937;
  color: #ffffff;
  padding: 20px 50px;
  position: relative;
  border-bottom: 3px solid #2563eb;
}

.header h3 {
  margin: 0;
  font-size: 20px;
  letter-spacing: 1px;
  color: #ffffff;
  text-align: center;
}

.logout-btn {
  position: absolute;
  top: 50%;
  right: 30px;
  transform: translateY(-50%);
  background-color: #2563eb;
  color: white;
  padding: 8px 16px;
  border-radius: 6px;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  transition: background-color 0.3s ease;
}

.logout-btn:hover {
  background-color: #1e40af;
}

    .row {
      display: flex;
      flex-wrap: wrap;
      padding: 20px;
      gap: 20px;
    }

    .leftcolumn, .rightcolumn {
      background-color: #ffffff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .leftcolumn {
      flex: 1 1 45%;
      min-width: 320px;
    }

    .rightcolumn {
      flex: 1 1 50%;
      min-width: 350px;
    }

    textarea, input[type=text], select {
      width: 100%;
      padding: 10px;
      margin: 5px 0 10px 0;
      border: 1px solid #ccc;
      border-radius: 8px;
      background-color: #f8fafc;
      font-size: 14px;
    }

    input[type=file] {
      width: 100%;
    }

    input[type=file]::file-selector-button {
      background-color: #2563eb;
      color: white;
      border: none;
      padding: 6px 12px;
      border-radius: 6px;
      cursor: pointer;
    }

    input[type=file]::file-selector-button:hover {
      background-color: #1e40af;
    }

    .button1 {
      background-color: #2563eb;
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 8px 16px;
      cursor: pointer;
      transition: 0.3s;
    }

    .button1:hover {
      background-color: #1e40af;
    }

    h3 {
      color: #111827;
      border-bottom: 2px solid #2563eb;
      display: inline-block;
      padding-bottom: 3px;
    }

    #msg {
      color: #2563eb;
      font-weight: bold;
      display: block;
      text-align: center;
      margin-bottom: 10px;
    }

    .footer {
      background-color: #1f2937;
      color: #9ca3af;
      text-align: center;
      padding: 15px;
      margin-top: 30px;
      font-size: 13px;
      border-top: 2px solid #2563eb;
    }

    button {
      cursor: pointer;
    }

    @media screen and (max-width: 768px) {
      .row {
        flex-direction: column;
      }

      .leftcolumn, .rightcolumn {
        width: 100%;
      }

      button {
        width: 100% !important;
        position: static !important;
        margin: 5px 0;
      }
    }
  </style>
</head>

<body>

<div class="header">
  <h3>Welcome <?php echo $_SESSION['name']; ?> to SendGrid Sending Software</h3>
   <a href="logout.php" class="logout-btn">Logout</a>
</div>

<div class="row">
  <!-- Left Side -->
  <div class="leftcolumn">
    <h3>Content</h3>
    <textarea name="mszc" id="mszc" rows="10"></textarea>
    <p>
      <b>Type:</b>
      <input name="mail_format" type="radio" value="plain"> Plain
      <input name="mail_format" type="radio" value="html"> HTML
    </p>

    <p>
      <b>Encrypt:</b>
      <select name="encrypting" id="encrypting">
        <option value="N">Default</option>
        <option value="B">Base-64</option>
        <option value="D">Dimensional</option>
      </select>
      <input type="button" value="Preview" onclick="displayHTML()" class="button1">
    </p>

    <p>
      <b>Message ID:</b>
      <select name="set_mid" id="set_mid">
        <option value="Y">Yes</option>
        <option value="N">No</option>
      </select>

      <b>X-Mail:</b>
      <select name="xm" id="xm">
        <option value="Y">Yes</option>
        <option value="N">No</option>
      </select>
    </p>

    <h3>Test Email</h3>
    <textarea name="receipent" id="receipent" rows="2"></textarea>

    <form action="uploadfile.php" method="post" enctype="multipart/form-data">
      <label><b>Text File:</b></label>
      <input type="file" name="tempfile" id="tempfile">
      <label><b>Token Number:</b></label>
      <input type="submit" value="Upload File" class="button1" name="submit">
    </form>
  </div>

  <!-- Right Side -->
  <div class="rightcolumn">
    <form name="form1" method="POST">
      <h3>Mail Settings</h3>

      <p><b>Mailing Method:</b>
        <input name="mode" type="radio" value="test"> Test
        <input name="mode" type="radio" value="bulk"> Mass
      </p>

      <p><b>Sending Method:</b>
        <input name="runmode" type="radio" value="normal" checked onchange="hideParams();"> Normal
        <input name="runmode" type="radio" value="auto" onchange="showParams();"> Script
      </p>

      <div id="autoparams" style="display:none; margin-bottom:10px;">
        <label>Time:</label> <input name="tts" id="tts" type="text" size="5">
        <label>Cap:</label> <input name="lts" id="lts" type="text" size="5">
        <label>Halt:</label> <input name="plimit" id="plimit" type="text" size="5">
      </div>

      <label>From Name:</label>
      <input type="text" name="f_name" id="f_name">

      <label>From Email:</label>
      <input type="text" name="f_mail" id="f_mail">

      <label>CC:</label>
      <input type="text" name="cc_email" id="cc_email">

      <label>BCC:</label>
      <input type="text" name="bcc_email" id="bcc_email">

      <label>Reply Email:</label>
      <input type="text" name="repl" id="repl">

      <label>Subject Line:</label>
      <input type="text" name="sub" id="sub">

      <label>Message Email:</label>
      <input type="text" name="m_mail" id="m_mail">

      <label>Token Cap:</label>
      <input type="text" name="limit" id="limit">

      <label>Token Number:</label>
      <input type="text" name="token" id="token">

      <label>Sending Key:</label>
      <input type="text" name="unique_smtp_password" id="unique_smtp_password">

    </form>

    <div id="sendNormal" style="margin-top:20px; text-align:center;">
      <button id="sendmail" class="button1" style="width:200px;">PHP Mailer</button>
      <button disabled style="width:100px;">Reset</button>
      <button disabled style="width:100px;">Status</button>
      <button id="sendmail1" class="button1" style="width:200px;">Swift Mailer</button>
    </div>

    <div id="sendAuto" style="display:none; margin-top:20px; text-align:center;">
      <button id="setautorun" class="button1" style="width:200px;">PHP Mailer Script</button>
      <button disabled style="width:100px;">Reset</button>
      <button disabled style="width:100px;">Status</button>
      <button id="setautorun1" class="button1" style="width:200px;">Swift Mailer Script</button>
    </div>
  </div>
</div>

<div class="footer">
  &copy; <?php echo date('Y'); ?> SendGrid Mailer Dashboard
</div>

<script>
  // Keep all your existing JS below unchanged
  // (validateForm, processMails, showParams, hideParams, etc.)
  // Paste your old script section here
</script>

<?php  
}else{ 
  header("Location: index.php"); 
  exit(); 
} 
?>
</body>
</html>
