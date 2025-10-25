<?php 

session_start();

if (isset($_SESSION['id']) && isset($_SESSION['user_name'])) {


 ?><!DOmail_format html>
<html lang="en">

<head>

    <title>Bulk Email Sending</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="node_modules/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="node_modules/bootstrap-social/bootstrap-social.css">
    <link rel="stylesheet" type="text/css" href="CSS/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" />
    
    
    <script>
      function displayHTML() {
	    var content = document.getElementById("mszc").value;
	    popupWin = window.open('','popup','toolbar = no, status = no, width=500, height=600');
	    popupWin.document.writeln(content);
	  }
      function displayHTMLinline() {
	    //document.getElementById('mw').style.display = "block";
		document.getElementById('test_iframe').style.display = "block";
		document.getElementById('close_prv').style.display = "inline-block";
	    var content = document.getElementById("mszc").value;
		var iframe = document.getElementById('test_iframe');
		var iframedoc = iframe.document;
        if (iframe.contentDocument)
           iframedoc = iframe.contentDocument;
        else if (iframe.contentWindow)
           iframedoc = iframe.contentWindow.document;

        if (iframedoc) {
          // Put the content in the iframe
          iframedoc.open();
          iframedoc.writeln(content);
          iframedoc.close();
        } else {
          //just in case of browsers that don't support the above 3 properties.
          //fortunately we don't come across such case so far.
          alert('Cannot inject dynamic contents into iframe.');
        }
	    //popupWin = window.open('','popup','toolbar = no, status = no, width=500, height=600');
	    //popupWin.document.writeln(content);
	  }	  
	</script>
<style> 
 
  
 input[type=file] {
  background-color: #4CAF50;
  border: none;
  color: white;
border-radius: .2em;
border: 2px solid #6c5ce7;
  text-decoration: none;
  margin: 4px 2px;
  cursor: pointer;
}
input[type=file]::file-selector-button {
  border: 2px solid #6c5ce7;
  padding: .2em .4em;
  border-radius: .2em;
  background-color: #1eed07;
  transition: 1s;
  border-radius: 10px;
}

input[type=file]::file-selector-button:hover {
  background-color: #ecffe8;
  border: 2px solid #00cec9;
  border-radius: 10px;
}
input[value="Insert Text"]  {
  background-color: #4CAF50;
  border: none;
  color: white;
text-decoration: none;
  margin: 4px 2px;
  cursor: pointer;
}


a:link {
  color: green;
  background-color: transparent;
  text-decoration: none;
}
a:visited {
  color: pink;
  background-color: transparent;
  text-decoration: none;
}
a:hover {
  color: red;
  background-color: transparent;
  text-decoration: underline;
}
a:active {
  color: yellow;
  background-color: transparent;
  text-decoration: underline;
</style>
</head>

<body>
    <nav class="navbar navbar-inverse navbar-toggleable-sm fixed-top">
        <div class="container justify-content-center">
            <a class="navbar-brand" href="#"><span class="fa fa-envelope fa-lg ml-3">
</span>&nbspWelcome <?php echo $_SESSION['name']; ?> Bulk Mail Application</a>&nbsp&nbsp&nbsp&nbsp<a href="logout.php"><i class="fa fa-sign-out" aria-hidden="true"></i>Logout</a>
        </div> 
    </nav>

    <div>        
        <div class="row font">
            <div class="col-sm-6">
                  <div class="card w-100 h-100">                  
                  
                  <h6 class="card-header bg-primary text-white">Message Body</h6>
                  
                  <h6 class="text-center">Type &nbsp&nbsp&nbsp&nbsp<input name="mail_format" id="mail_format" type="radio" value="plain" class="rad">
                     Normal <input name="mail_format" id="mail_format" type="radio" value="html" class="rad">
                     Html&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                     
                    <!-- <button type="button" value=" Preview " class="btn btn-success btn-rounded onClick="displayHTML()"><i class="fa fa-camera fa-lg pr-2" aria-hidden="true"></i>Preview</button> -->
                  <button class="btn btn-success"> <i class="fa fa-camera fa-lg pr-2" aria-hidden="true">  <input type="button" value=" Preview " onClick="displayHTML()" class="btn btn-success btn-rounded"></i></button>
                     
                     </h6>
                     <div>
                      &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                      <textarea  name="mszc" id="mszc" cols="77" rows="20"></textarea>
                      </div></div>                       
              </div>
              <div class="col-sm-6">
                <div class="card h-85">
                    <h6 class="card-header bg-primary text-white">Message Credential</h6>
                    <div class="card-block">
                        <dl class="row">
                            <dt class="col-4">Subject Line</dt>
                         <dd class="col-8"><input type="text" name="sub" class="w-100" id="sub" size="30"></dd>
                            <dt class="col-4">From Email</dt>
                            <dd class="col-8"><input type="text" name="f_mail" class="w-100" id="f_mail" size="30"></dd>
                            <dt class="col-4">CC</dt>
                            <dd class="col-8"><input type="text" name="cc_email" class="w-100" id="cc_email" size="30"></dd>
                            <dt class="col-4">BCC</dt>
                            <dd class="col-8"><input type="text" name="cc_email" class="w-100" id="cc_email" size="30"></dd>
                            <dt class="col-4">From Name</dt>
                            <dd class="col-8"><input type="text" name="f_name" class="w-100" id="f_name" size="30"></dd>
                            <dt class="col-4">Host</dt>
                            <dd class="col-8"><input name="server" id="server" class="w-100" size="30" type="text" value=""></dd>
                            <dt class="col-4">User</dt>
                            <dd class="col-8"><input name="usr" id="usr" class="w-100" type="text" value=""></dd>
                            <dt class="col-4">Password</dt>
                            <dd class="col-8"><input name="passwd" id="passwd" class="w-100"type="text" value=""></dd>
                            <dt class="col-4">Port</dt>
                            <dd class="col-8"><input name="port" id="port" class="w-100" type="text" value=""></dd>
                            <dt class="col-4">Test Id</dt>
                            <dd class="col-8"><textarea name="receipent" id="receipent" class="w-100" cols="29" rows="2"></textarea></dd>
                            <dt class="col-4">Authy Req</dt>
                            <dd class="col-8">
                            <input name="tls" id="tls" type="radio" value="N" checked="checked"> No
	                        <input name="tls" id="tls" type="radio" value="Y"> Yes
	                        </dd>
	                       <dt class="col-12"><form action="uploadfile.php" method="post" enctype="multipart/form-data">
    <strong> Text File : </strong><input type="file" name="tempfile" id="tempfile">
<i class="fa fa-upload fa-lg pr-2" aria-hidden="true"></i>
<input type="submit" class="btn btn-warning" value="&#xf00d; Insert Text" />

</form></dt>

<dt class="col-4"><strong>Mode</strong></dt>
                            <dd class="col-8">
                            <input name="mode" id="mode" type="radio" value="test">&nbsp;Test(mail)
                  <input name="mode" id="mode" type="radio" value="bulk">&nbsp;Bulk(mail)
                                </dd>
<dt class="col-6"><strong><i>Token</i></strong>&nbsp;&nbsp;<input type="text" name="token" id="token" size="20">
    </dt><dd class="col-6">              <strong><i>Limit</i></strong>&nbsp;&nbsp;<input type="text" name="limit" id="limit" size="20">
</dd> 
<dt> 
<table align="center"  cellpadding="10" cellspacing="0" border="0">
                            <tr>
                                    <td colspan="2" align="center" style="padding:5px;">
                                           <span id="msg"></span>
                                        </td>
                                  </tr>
                                  <tr id="mw">
                                     <td colspan="2" align="center">
                                     <iframe id="test_iframe" src="about:blank"  style="display: none;"></iframe>
                                         <button name="close_prv" id="close_prv" style="display: none;"> Close </button>
                                         </td>
                                  </tr>
                                </table></dt>
</dl>
                        
                    </div>
                 </div>
                                   
              </div>
            </div>
            </div>
            <div class="row" align="center">
            <div name="sendNormal" id="sendNormal" style="display:inline-block;" class="text-center">
				 <table cellpadding="10" cellspacing="0" width="500" align="center" border="0">
						 <tr align="center">
						   <td width="20%"></td>
						   <td align="center" style="padding-left:6px;">
						   <button class="btn btn-primary"><i class="fa fa-envelope fa-lg pr-2" aria-hidden="true"><input type="submit" value="Send" name="sendmail" id="sendmail" class="btn-primary"></i></button>
						   </td>
						 </tr>
				 </table>
</div>
			 
			 <div name="sendAuto" id="sendAuto" style="display: none;" class="text-center">
				 <table cellpadding="10" cellspacing="0" width="500" align="center" border="0">
						 <tr align="center">
						   <td width="20%"></td>
						   <td align="center" style="padding-left:6px;">
						   <button name="setautorun" id="setautorun" style="width: 160px; height: 35px;" class="btn btn-primary"> <i class="fa fa-envelope fa-lg"></i>Set Auto Run </button>
						   </td>						   
						 </tr>
				 </table>
			 </div>	
              <div class="col-sm">
                  <div class="card h-10">
                  <dl class="row">
                      
                  <dt> <p>
                  <strong><i>Run Mode</strong></i>&nbsp;&nbsp;&nbsp<input name="runmode" id="runmode" type="radio" value="normal" class="rad" checked="checked" onchange="hideParams()";>&nbsp;Default&nbsp;
                  <input name="runmode" id="runmode" type="radio" value="auto" class="rad" onchange="showParams();">&nbsp;Script&nbsp;&nbsp;
                  
          

                  </p></dt>
                
				</dl><!--
				<dl class="row">
				<div name="autoparams" id="autoparams" style="display:none">
				<dt class="col-2">Time<input name="tts" id="tts" type="text" value="" size="10" placeholder="(Second)">
				Limit<input name="lts" id="lts" type="text" value="" size="10" placeholder="(Number)">
				BCT<input name="test_mail_break" id="test_mail_break" type="text" value="" size="10"></dt></dl>
				</div>-->
				<div name="autoparams" id="autoparams" style="display:none">
				<table align="center">
				<tr>
				<td>
				<b>Time:</b>
				<input name="tts" id="tts" type="text" value="" size="10">
				<b>Cap:</b>
				<input name="lts" id="lts" type="text" value="" size="10">
				<b>BCT:</b>
				<input name="test_mail_break" id="test_mail_break" type="text" value="" size="10"></td>
				</tr>
				</table>
				</div>

				</div></br>				
              </div>
              </div>
<!--<table align="center"  cellpadding="10" cellspacing="0" border="0">
                            <tr>
                        	    <td colspan="2" align="center" style="padding:5px;">
                        		   <span id="msg"></span>
                        		</td>
                        	  </tr>	  
                        	  <tr id="mw">
                        	     <td colspan="2" align="center">
                        	     <iframe id="test_iframe" src="about:blank" width="800" height="700" style="display: none;"></iframe>
                        		 <button name="close_prv" id="close_prv" style="display: none;"> Close </button>
                        		 </td>
                        	  </tr>	  
                        	</table>

            
        -->
       <!-- <div class="text-center">
            <button  class="btn btn-primary">
    <i class="fa fa-envelope fa-lg"></i> Send Mail.   
</button>
</div>-->
        <!--<div name="sendAuto" id="sendAuto" class="text-center"><button class ="button1 btn btn-primary" name="setautorun" id="setautorun"> <i class="fa fa-envelope fa-lg">Auto Run</i></button></div>-->
       
       
       
       
       
       </div>
    </div>
            <script>

     function getCheckedRadioVal(rname) {
       var radioButtons = document.getElementsByName(rname);
       for (var x = 0; x < radioButtons.length; x ++) {
      if (radioButtons[x].checked) {
	     return radioButtons[x].value
        }
       }
     }
  
     function validateForm(){

	   var server = document.getElementById("server").value;
       if (server == null || server == ""){
          alert("Please enter Server address");
          return false;
       }	 
	   var port = document.getElementById("port").value;
       if (port == null || port == ""){
          alert("Please enter Port value");
          return false;
       }	 
	   var usr = document.getElementById("usr").value;
       if (usr == null || usr == ""){
          alert("Please enter User Name");
          return false;
       }	 
	   var passwd = document.getElementById("passwd").value;
       if (passwd == null || passwd == ""){
          alert("Please enter Password");
          return false;
       }	
	   var tls = document.getElementById("tls").value;
       if (tls == null || tls == ""){
          alert("Please select a value for TLS");
          return false;
       }
	   
	   var runmode = getCheckedRadioVal("runmode");
	   if (runmode == "auto") {
	       var tts = document.getElementById("tts").value;
			 if (tts == null || tts == ""){
				alert("Please enter a value for Time to send");
				return false;
			 }		   
		   var lts = document.getElementById("lts").value;
			 if (lts == null || lts == ""){
				alert("Please enter a value for Limit to send");
				return false;
			 }	
		   var test_mail_break = document.getElementById("test_mail_break").value;
			 if (test_mail_break == null || test_mail_break == ""){
				alert("Please enter a value for bulk count for test mail");
				return false;
			 }				 
	   }	   
	   
       var mode = getCheckedRadioVal("mode");
       if (mode == null || mode == ""){
          alert("Please select a mode...");
          return false;
       }
	   var sub = document.getElementById("sub").value;
       if (sub == null || sub == ""){
          alert("Please enter the sub");
          return false;
       }
	   var f_mail = document.getElementById("f_mail").value;
       if (f_mail == null || f_mail == ""){
          alert("Please enter From(Email)");
          return false;
       }
	   var f_name = document.getElementById("f_name").value;
       if (f_name == null || f_name == ""){
          alert("Please enter From(Name)");
          return false;
       }	
       if (getCheckedRadioVal("mode")=="test") {
	     var receipent = (document.getElementById("receipent").value).trim();
 
		 //var receipent = document.forms["form1"]["receipent"].value;
         if (receipent == null || receipent == ""){
            alert("Please enter atleast 1 Test Email Recepient");
            return false;
         }
       }
       var mail_format = getCheckedRadioVal("mail_format");
       if (mail_format == null || mail_format == ""){
          alert("Please select content type...");
          return false;
       }	
	   var mszc = document.getElementById("mszc").value;
       if (mszc == null || mszc == ""){
          alert("Please enter Email Body");
          return false;
       }	
	   
       if (getCheckedRadioVal("mode")=="bulk" && runmode == "normal") {
	     var limit = document.getElementById("limit").value;
         if (limit == null || limit == "" || limit < 1 || limit > 100000){
            alert("Please enter a valid limit in the range 1 to 100000");
            return false;
         }
       }
	   
	   
	   if (getCheckedRadioVal("mode")=="bulk") {
	     var token = document.getElementById("token").value;
         if (token == null || token == ""){
            alert("Please enter a valid File Id");
            return false;
         }
       }	   
       // If validation passes, return true	   
	   return true;
     }
  

 
     function processMails() {
	  if (validateForm()){
	    //displayHTMLinline();
        var target =  document.getElementById("msg");
		var mode = getCheckedRadioVal("mode");
		var runmode = getCheckedRadioVal("runmode");
		var tts = document.getElementById("tts").value;
		var lts = document.getElementById("lts").value;		
		var sub = document.getElementById("sub").value;
		var f_mail = document.getElementById("f_mail").value;	
		var f_name = document.getElementById("f_name").value;
        var receipent = document.getElementById("receipent").value;	
        var mail_format = getCheckedRadioVal("mail_format");	
        var mszc = document.getElementById("mszc").value;
		var limit = document.getElementById("limit").value;
		var token = document.getElementById("token").value;
		var server = document.getElementById("server").value;
		var port = document.getElementById("port").value;
		var usr = document.getElementById("usr").value;
		var passwd = document.getElementById("passwd").value;
		var tls = document.getElementById("tls").value;		
		var test_mail_break = document.getElementById("test_mail_break").value;		
		
		if (limit == null || limit == ""){
          limit = 1;
        }
		
		receipent = receipent.replace(/\n/g, ",");
		
		var post = "mode=" + encodeURIComponent(unescape(mode)) 
		           + "&sub=" + encodeURIComponent(unescape(sub))
				   + "&f_mail=" + encodeURIComponent(unescape(f_mail))
                   + "&f_name=" + encodeURIComponent(unescape(f_name))				   
				   + "&receipent=" + encodeURIComponent(unescape(receipent))
				   + "&mail_format=" + encodeURIComponent(unescape(mail_format))
				   + "&mszc=" + encodeURIComponent(unescape(mszc))
				   + "&limit=" + encodeURIComponent(unescape(limit))
				   + "&token=" + encodeURIComponent(unescape(token))
				   + "&server=" + encodeURIComponent(unescape(server))
				   + "&port=" + encodeURIComponent(unescape(port))
				   + "&usr=" + encodeURIComponent(unescape(usr))
				   + "&passwd=" + encodeURIComponent(unescape(passwd))
				   + "&tls=" + encodeURIComponent(unescape(tls))
				   + "&runmode=" + encodeURIComponent(unescape(runmode))				   
				   + "&tts=" + encodeURIComponent(unescape(tts))
				   + "&lts=" + encodeURIComponent(unescape(lts))
				   + "&test_mail_break=" + encodeURIComponent(unescape(test_mail_break));
	     
		var xhr = new XMLHttpRequest();	
        if (runmode == "auto") {
		   xhr.open("POST", "smtpprep.php", true);
		   //document.getElementById("setautorun").disabled = true;
        } else {		
           xhr.open("POST", "qmails.php", true);
		}
		xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.setRequestHeader("Content-length", post.length);
        xhr.onreadystatechange = function () {
          if(xhr.readyState == 2) {
             target.innerHTML = 'Processing...';
          }
          if(xhr.readyState == 4 && xhr.status == 200) {
             target.innerHTML = xhr.responseText;
          }
        }
        xhr.send(post);		
      }
	}

   function closePrv() {
     	document.getElementById('test_iframe').style.display = "none";
		document.getElementById('close_prv').style.display = "none";
		//document.getElementById('mw').style.display = "none";
   }
   
   function hideParams(){
     	document.getElementById('autoparams').style.display = "none";
		document.getElementById('sendNormal').style.display = "inline-block";
		document.getElementById('sendAuto').style.display = "none";		
   }  

   function showParams(){
     	document.getElementById('autoparams').style.display = "block";
		document.getElementById('sendNormal').style.display = "none";		
		document.getElementById('sendAuto').style.display = "inline-block";
   }    
 
   var button1 = document.getElementById("sendmail");
   button1.addEventListener("click", processMails);

   var button2 = document.getElementById("setautorun");
   button2.addEventListener("click", processMails);    

   var button_prv = document.getElementById("close_prv");
   button_prv.addEventListener("click", closePrv);
   </script>
    <!-- jQuery first, then Tether, then Bootstrap JS. -->
    <script src="node_modules/jquery/dist/jquery.min.js"></script>
    <script src="node_modules/tether/dist/js/tether.min.js"></script>
    <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
<?php 
}else{
     header("Location: index.php");
     exit();
}
 ?>
</body>

</html>
