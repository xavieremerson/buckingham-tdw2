<?php

  session_start();

  session_register('user');

  session_register('pass');



  include('includes/dbconnect.php');


  $check = mysql_query("SELECT Username, Password FROM Users WHERE Username = '$user' AND Password = '$pass'") or die (mysql_error());

  if (mysql_num_rows($check) >= 1) {

    $yyyymmdd = date("Ymd");

    $addLogin = mysql_query("UPDATE Users SET LastLogin = '$yyyymmdd' WHERE Username = '$user'") or die (mysql_error());
		
					$getuserdata = mysql_query("SELECT Fullname FROM Users WHERE Username = '$user'") or die (mysql_error());
					
					while ( $row = mysql_fetch_array($getuserdata) ) {
			
					$userfullname = $row["Fullname"];
			
					}
				
		session_register('userfullname');

    Header("Location: $frompage");

    exit;

  } else {

    Header("Location: index.php?login=n");

    exit;

  }

?>
