<?
include('includes/dbconnect.php');  
include('includes/global.php');  
include('includes/functions.php');  
?>
<html>
<head>
<link REL="SHORTCUT ICON" HREF="favicon.ico"> 
<title>SLX</title>


<?
	$query_advisor = "SELECT DISTINCT (
	nadd_short_name
	)
	FROM `nfs_nadd`  
	 WHERE nadd_short_name != ''  and length(nadd_short_name) < 5
	 ORDER BY nadd_short_name";	
	
	$result_advisor = mysql_query($query_advisor) or die (mysql_error());
	while ( $rowx = mysql_fetch_array($result_advisor) ) 
	{
		echo $rowx["nadd_short_name"];
		$result_acct = mysql_query("SELECT distinct(nadd_rr_exec_rep) from nfs_nadd where nadd_short_name = '".$rowx["nadd_short_name"]."' order by nadd_full_account_number, nadd_address_line_1") or die (mysql_error());
		$str_reps = '';
		while ( $rowy = mysql_fetch_array($result_acct) )
		{
				$str_reps .= $rowy["nadd_rr_exec_rep"].";";
		}
		echo ",".$str_reps."\n<br>";
	}
	
						
	?>