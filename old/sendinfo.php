<?php

  include('includes/dbconnect.php');
	include('includes/global.php');
	include('includes/functions.php'); 


  $check = mysql_query("SELECT * FROM Users WHERE Email = '$email'") or die (mysql_error());

  if (mysql_num_rows($check) > 0) {

    while ($row = mysql_fetch_array($check)) {

			$pass = password_generator();
			$query_create = "UPDATE Users set Password = '".md5($pass)."' WHERE Email = '$email'";
			$result_create = mysql_query($query_create) or die (mysql_error());
			
			$fileattach   = "";
			$mailsubject  = "CompSys 2.0 Login Information";
			$emailheading = "CompSys Password";
			$mailbody     = '<font color = "#000080" family = "Verdana,Arial,Helvetica">'.$row["Fullname"].': 
											<br>
											<br>Your new password is: <b>'.$pass.'</b><br><br>';
			$mailbody    .= 'Click on the link to launch <a href="'.$_site_url.'">'.$_app_name.'</a> <Br><br><br>From: </font>';
			$from = "Compliance System <compliance_admin@donotreply.com>";
			html_emails_dynamic($email, $from, $mailsubject, $mailbody, $emailheading, $fileattach, gen_control_number());
		
		

      header("Location: index.php?forpass=y");

      exit;

    }

  } else {

    header("Location: index.php?forpass=n");

    exit;

  }

?>