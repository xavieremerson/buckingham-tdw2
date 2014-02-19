<?
include('../includes/dbconnect.php');
include('../includes/global.php'); 
include('../includes/functions.php'); 


$trade_date_to_process = previous_business_day();

//show all trades for all days
//fields are trad_reference_number  trad_rr  trad_trade_date  trad_advisor_code  trad_advisor_name  trad_account_name  trad_account_number  trad_symbol  trad_buy_sell  trad_quantity  trade_price  trad_commission  trad_cents_per_share  trad_is_cancelled 
$trade_str = "TRADEREFNUM,TRADEDATE,ADVISOR,BUYSELL,SYMBOL,PRICE,COMMISSION,COMMPERSHARE,BADTRADE".chr(13) . chr(10);
			
/*
$query_trades = "SELECT * from mry_comm_rr_trades
							   WHERE trad_trade_date = '".$trade_date_to_process."'";
*/
$query_trades = "SELECT * from mry_comm_rr_trades order by trad_trade_date";
								 							
$result_trades= mysql_query($query_trades) or die(mysql_error());
//echo mysql_num_rows($result_trades);
while($row_trades = mysql_fetch_array($result_trades))
{
$trade_str .= $row_trades["trad_reference_number"].",".format_date_ymd_to_mdy($row_trades["trad_trade_date"]).",".$row_trades["trad_advisor_code"].",".$row_trades["trad_buy_sell"].",".$row_trades["trad_symbol"].",".round($row_trades["trade_price"],2).",".$row_trades["trad_commission"].",".$row_trades["trad_cents_per_share"].",".$row_trades["trad_is_cancelled"].chr(13) . chr(10);
}

define("FILE_PATH","C:\\");
$filename = "TRADES.DAT";

$tradeFile = fopen(FILE_PATH.$filename, "w" ) ;

  if ( $tradeFile )
  {
	 fwrite($tradeFile, $trade_str);
   fclose($tradeFile);
	 echo "File ".FILE_PATH.$filename . " written successfully<br>";
  }
  else
  {
   die( "File could not be opened for writing" ) ;
  }

$cmd_string = "copy c:\TRADES.DAT \\\\192.168.20.49\\nfs$\\TRADES.DAT";
//shell_exec($cmd_string);



$advisor_str = "TRADEDATE,ADVISOR,CURDAYCOMM,CURMTD,CURYTD,PREMTD,PREYTD".chr(13) . chr(10);
$qry_distinct_adv = "SELECT distinct(comm_advisor_code) as comm_advisor_code 
										 FROM mry_comm_rr_level_a order by comm_advisor_code";
$result_distinct_adv = mysql_query($qry_distinct_adv) or die(mysql_error());
while($row_distinct_adv = mysql_fetch_array($result_distinct_adv))
{
		$query_mqydate_adv = "SELECT max(comm_trade_date)  AS comm_trade_date
													FROM mry_comm_rr_level_a
													WHERE comm_advisor_code = '".$row_distinct_adv["comm_advisor_code"]."'";
		$result_mqydate_adv= mysql_query($query_mqydate_adv) or die(mysql_error());
		while($row_mqydate_adv = mysql_fetch_array($result_mqydate_adv))
		{
			$query_data = "SELECT * from mry_comm_rr_level_a
														WHERE comm_advisor_code = '".$row_distinct_adv["comm_advisor_code"]."'
														AND comm_trade_date = '".$row_mqydate_adv["comm_trade_date"]."'";
			$result_data= mysql_query($query_data) or die(mysql_error());
			while($row_data = mysql_fetch_array($result_data))
			{
				if ($row_mqydate_adv["comm_trade_date"] == $trade_date_to_process) {
					$advisor_str .= format_date_ymd_to_mdy($trade_date_to_process).",".$row_distinct_adv["comm_advisor_code"].",".$row_data["comm_total"].",".$row_data["comm_mtd"].",".$row_data["comm_ytd"] .","."0" .","."0".chr(13) . chr(10);
				} else {
					$advisor_str .= format_date_ymd_to_mdy($trade_date_to_process).",".$row_distinct_adv["comm_advisor_code"].","."0".",".$row_data["comm_mtd"].",".$row_data["comm_ytd"] .","."0" .","."0".chr(13) . chr(10);
				}
			}
			
		}

}

$filename = "COMM.DAT";

$commFile = fopen(FILE_PATH.$filename, "w" ) ;

  if ( $commFile )
  {
	 fwrite($commFile, $advisor_str);
   fclose($commFile);
	 echo "File ".FILE_PATH.$filename . " written successfully<br>";
  }
  else
  {
   die( "File could not be opened for writing" ) ;
  }

$cmd_string = "copy c:\COMM.DAT \\\\192.168.20.49\\nfs$\\COMM.DAT";
//shell_exec($cmd_string);

?>