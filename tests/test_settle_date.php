<?
ini_set('max_execution_time', 3600);

  include('includes/dbconnect.php');
  include('includes/global.php');
  include('includes/functions.php');


$result_mry_comm_rr_trades_flush =  mysql_query("truncate table mry_comm_rr_trades") or die (mysql_error());
$result_mry_comm_rr_trades_populate = mysql_query("insert into mry_comm_rr_trades select * from rep_comm_rr_trades") or die (mysql_error());
exit;

	$qry_ref = "select distinct(trad_reference_number) as ref_num from mry_comm_rr_trades";
	$result_ref = mysql_query($qry_ref) or tdw_mysql_error($qry_ref);
	$count = 0;
	while ( $row_ref = mysql_fetch_array($result_ref) ) 
	{
		if ($count > 10) {
		 //exit;
		}
		$qry_match = "select trad_trade_reference_number, trad_settle_date from nfs_trades
		              where trad_trade_reference_number = '".$row_ref["ref_num"]."'";
		$result_match = mysql_query($qry_match) or tdw_mysql_error($qry_match);
		$count_match = 0;
		while ( $row_match = mysql_fetch_array($result_match) ) {
			$count_match = count_match + 1;
			//xdebug("ref / settle",$row_match["trad_trade_reference_number"] . "/" . $row_match["trad_settle_date"] );
			$qry = "update rep_comm_rr_trades set trad_settle_date = '".$row_match["trad_settle_date"]."' where trad_reference_number = '".$row_match["trad_trade_reference_number"]."'";
	    $result = mysql_query($qry) or tdw_mysql_error($qry);
		}
		//xdebug("count_match",$count_match);
		ob_flush();
		flush();
		$count = $count + 1;
		echo $count;
	}

echo "DONE>>>>>>>>>>>>>>>>>>>>>>>";
?>
