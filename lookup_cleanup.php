<?
include('includes/dbconnect.php');  
include('includes/global.php');  
include('includes/functions.php');  
?>
<html>
<head>
<link REL="SHORTCUT ICON" HREF="favicon.ico"> 
<title>Map Accts</title>

<?

	//clean data
	
	$result_tmp_subacct = mysql_query("SELECT distinct(cust_id) from tmp_subacct") or die (mysql_error());
	while ( $row_tmp_subacct = mysql_fetch_array($result_tmp_subacct) ) 
	{
		
		$str_SQL = "SELECT * from tmp_cust where cust_id = '".$row_tmp_subacct["cust_id"]."'";
		$check = mysql_query($str_SQL) or die (mysql_error());
		
		if (mysql_num_rows($check) >= 1) {
		echo "found <br>";
		} else {
		echo "not found <br>";
		$str_delete = "delete from tmp_subacct where cust_id = '".$row_tmp_subacct["cust_id"]."'";
		echo $str_delete. "<br>";
		$result_delete = mysql_query($str_delete) or die (mysql_error());
		}

	}	
	
			
?>