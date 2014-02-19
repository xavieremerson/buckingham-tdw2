<?php

  session_start();
  session_register('user');
  session_register('pass');
	session_register('userfullname');
	
	if ($user == '')
	{
	Header("Location: testindex.php?frompage=testredirect.php");
	exit;
	}

  include('includes/dbconnect.php');
  include('includes/global.php'); 
	
	//Tocqueville Company Logo color #21427B
	 
?>

<HTML>
<BODY>
<a href="http://www.ibm.com">Go to IBM</a>


</BODY>
</HTML>
