<?

include('../includes/functions.php');
include('../includes/global.php');
include('../includes/dbconnect.php');

ini_set('max_execution_time', 3600);

//get nadd data in array
$arr_nadd = array();
$qry_acctnadd = "SELECT nadd_full_account_number,nadd_advisor from mry_nfs_nadd";
xdebug("qry_acctnadd",$qry_acctnadd);
$result_acctnadd = mysql_query($qry_acctnadd) or die (tdw_mysql_error($qry_acctnadd));
while ( $row_acctnadd = mysql_fetch_array($result_acctnadd) )
{
	$arr_nadd[$row_acctnadd["nadd_full_account_number"]] = $row_acctnadd["nadd_advisor"];
}

//get tradeware data in array
$arr_tradeware = array();
$qry_tradeware = "SELECT code, name from tmp_tradeware";
xdebug("qry_tradeware",$qry_tradeware);
$result_tradeware = mysql_query($qry_tradeware) or die (tdw_mysql_error($qry_tradeware));
while ( $row_tradeware = mysql_fetch_array($result_tradeware) )
{
	$arr_tradeware[$row_tradeware["code"]] = $row_tradeware["code"];
}
//STEP 1
/*
$qry_acct = "SELECT acct from tmp_pdy WHERE advisor IS NULL";
xdebug("qry_acct",$qry_acct);
$result_acct = mysql_query($qry_acct) or die (tdw_mysql_error($qry_acct));
while ( $row_acct = mysql_fetch_array($result_acct) )
	{
		echo $row_acct["acct"]."<br>";
	  //nadd_branch  nadd_account_number  nadd_full_account_number  nadd_advisor 
		$valmatch = str_replace('-','',$row_acct["acct"]);
		$qry_acctu = "update tmp_pdy set advisor = '".$arr_nadd[$valmatch]."' where acct = '".$row_acct["acct"]."'";
		xdebug("qry_acctu",$qry_acctu);
		$result_acctu = mysql_query($qry_acctu) or die (tdw_mysql_error($qry_acctu));
	}
	*/
	
//STEP 2
$qry_acct = "SELECT acct, advisor from tmp_pdy";
xdebug("qry_acct",$qry_acct);
$result_acct = mysql_query($qry_acct) or die (tdw_mysql_error($qry_acct));
while ( $row_acct = mysql_fetch_array($result_acct) )
	{
			echo $row_acct["advisor"]."<br>";
	  	//nadd_branch  nadd_account_number  nadd_full_account_number  nadd_advisor 
			$valmatch = $row_acct["advisor"]."X";
			$qry_acctu = "update tmp_pdy set map_adv = '".$arr_tradeware[$valmatch]."' where acct = '".$row_acct["acct"]."'";
			xdebug("qry_acctu",$qry_acctu);
			$result_acctu = mysql_query($qry_acctu) or die (tdw_mysql_error($qry_acctu));
	}

//PDY-024350
//qry_accta = [SELECT nadd_full_account_number,nadd_advisor from mry_nfs_nadd where nadd_full_account_number ='PDY024350']
//qry_acctu = [update tmp_pdy set advisor = 'GEIC' where acct = 'PDY-024350']

?>
