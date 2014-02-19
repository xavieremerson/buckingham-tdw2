<?php

  include('includes/dbconnect.php');
	include('includes/global.php');
	include('includes/functions.php'); 


  $check = mysql_query("SELECT * FROM users WHERE Email = '$email'") or die (mysql_error());

  if (mysql_num_rows($check) > 0) {

    while ($row = mysql_fetch_array($check)) {

			$pass = password_generator();
			$query_create = "UPDATE users set Password = '".md5($pass)."' WHERE Email = '$email'";
			$result_create = mysql_query($query_create) or die (mysql_error());
			
			$fileattach   = "";
			$mailsubject  = $_app_title. " : Your login information.";
			$emailheading = "TDW Password";
			$mailbody     = '<font color = "#000080" family = "Verdana,Arial,Helvetica">'.$row["Fullname"].': 
											<br>
											<br>Your new password is: <b>'.$pass.'</b><br>
											Please remember to go to TDW Password Changer and change your password to something
											easier for you to remember.<br>';
			$mailbody    .= 'Click on the link to launch <a href="'.$_site_url.'login.php?user='.$row["Username"].'&pass='.$pass.'">'.$_app_name.'</a></font>';
			
			$html_body = zSysMailHeader('test');
			$html_body .= $mailbody;
			$html_body .= zSysMailFooter();
			
			$text_body = $_app_title. " : Your login information.";
			
			//create file attachments
			$attachment = array();
			
			zSysMailer($email, $row["Fullname"], $mailsubject, $html_body, $text_body, $attachment);

      header("Location: index.php?forpass=y");

      exit;

    }

  } else {

    header("Location: index.php?forpass=n");

    exit;

  }

?>