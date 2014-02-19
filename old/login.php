<?php
  session_start();
  session_register('user');
  session_register('pass');

  include('includes/dbconnect.php');

  $check = mysql_query("SELECT Username, Password FROM users WHERE Username = '$user' AND Password = '".md5($pass)."'") or die (mysql_error());

  if (mysql_num_rows($check) >= 1) {

    $yyyymmdd = date("Ymd");
    $addLogin = mysql_query("UPDATE users SET LastLogin = '$yyyymmdd' WHERE Username = '$user'") or die (mysql_error());
		
					$getuserdata = mysql_query("SELECT ID, Fullname, Email, is_administrator, (now() < login_expiry) as login_active, DATE_FORMAT(login_expiry,'%b %D, %Y') as login_expiry, DATE_FORMAT(login_expiry,'%l:%i %p') as tval FROM users WHERE Username = '$user'") or die (mysql_error());
					
					while ( $row = mysql_fetch_array($getuserdata) ) {
			
					$userfullname = $row["Fullname"];
					$user_id = $row["ID"];
					$user_email = $row["Email"];
					$user_isadmin = $row["is_administrator"];
					$user_login_active = $row["login_active"];
					$login_expiry = $row["login_expiry"];
					$dval = $row["login_expiry"];
					$tval = $row["tval"];
					}
					
					if ($user_login_active == 1) {
				
						session_register('userfullname');
						session_register('user_id');
						session_register('user_email');
						session_register('user_isadmin');
						session_register('dval');
						session_register('tval');

						Header("Location: main.php");
				
						exit;
						
					} else {

						Header("Location: index.php?login=ae&dval=$dval&tval=$tval");
				
						exit;
					}

  } else {

    Header("Location: index.php?login=n");

    exit;

  }

?>