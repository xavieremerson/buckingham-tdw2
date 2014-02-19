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

	//Get all data from map into an array
	$result1 = mysql_query("SELECT map_old, map_new from tmp_map") or die (mysql_error());
	
	$i = 0;
	$arr_map = array();
	
	while ( $row = mysql_fetch_array($result1) ) 
	{
		$arr_map[$row["map_old"]] = $row["map_new"];
		$i = $i+1;
	}
	
    //Get all subaccounts data
	$result_tmp_subacct = mysql_query("SELECT * from tmp_subacct") or die (mysql_error());
	while ( $row_tmp_subacct = mysql_fetch_array($result_tmp_subacct) ) 
	{
		if ($arr_map["470".$row_tmp_subacct["acct_number"]] != '') {
		echo "new acct number = ". $arr_map["470".$row_tmp_subacct["acct_number"]] . "<br>";
		//update table
	    $result_update_subacct = mysql_query("UPDATE tmp_subacct set acct_new_number = '".$arr_map["470".$row_tmp_subacct["acct_number"]]."' where auto_id = '".$row_tmp_subacct["auto_id"]."'") or die (mysql_error());
		} else {
		echo "not found<br>";
		
		}
		
	}
	
	
			
?>