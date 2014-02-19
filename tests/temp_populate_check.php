<?

include('includes/functions.php');
include('includes/dbconnect.php');
include('includes/global.php');

$qry = "select distinct(chek_advisor) as chek_advisor from chk_chek_payments_etc";
$result = mysql_query($qry) or die(tdw_mysql_error($qry));
while ( $row = mysql_fetch_array($result) ) 
{
	xdebug("chek_advisor",$row["chek_advisor"]);

	$qry1 = "select * from int_clnt_clients where clnt_code = '".trim($row["chek_advisor"])."'";
	$result1 = mysql_query($qry1) or die(tdw_mysql_error($qry1));
	while ( $row1 = mysql_fetch_array($result1) ) 
	{
		echo "<br>[".$row1["clnt_rr1"]."#".$row1["clnt_rr2"]."]";
		$qry2 = "update chk_chek_payments_etc set chek_reps_and = '".$row1["clnt_rr1"]."#".$row1["clnt_rr2"]."' where chek_advisor = '".$row["chek_advisor"]."'";
		$result2 = mysql_query($qry2) or die(tdw_mysql_error($qry2));
	}
}


?>