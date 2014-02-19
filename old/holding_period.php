<?
//include('includes/functions.php');
//include('includes/dbconnect.php');

$tdate =  business_day_backward(strtotime("now"), 1);
$hdate =  business_day_backward(strtotime("now"), 21);
$flag1 = 0;

$query = "trdm_account_number = '3123123123131' ";

$qry_acctnames = "SELECT acct_number, acct_name1 FROM Employee_accounts WHERE acct_is_active = 1";
$result_acctnames = mysql_query($qry_acctnames) or die('ERROR HERE');
while($row= mysql_fetch_array($result_acctnames))
{
	$arr_acctnames[$row["acct_number"]] = $row["acct_name1"];
	
	$query = $query . " OR trdm_account_number = '" . $row["acct_number"] . "' ";
}

//GET ALL THE TRADES FROM LAST BUSINESS DAY THAT WERE SELL 
$get_sell_trades = "SELECT * FROM Trades_m WHERE trdm_trade_date = '".$tdate."' AND (trdm_buy_sell = 'sl' OR trdm_buy_sell = 'Sell') AND (".$query.")";
$result_sell_trades = mysql_query($get_sell_trades) or die(mysql_error());
$htmlfilebodydata .= 
'
<tr>
	<td colspan="8"><bR></td>
</tr>
<tr> 
	<td colspan="8">
		<table>
			<tr> 
				<td colspan="2" nowrap><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#000099"><b>Holding Period Violation List</b></td>
			</tr>';
//START WHILE 1
while($row_sell_trades = mysql_fetch_array($result_sell_trades))
{
	$get_buy_trades = "SELECT * FROM Trades_m where trdm_account_number = '".$row_sell_trades["trdm_account_number"]."' AND trdm_symbol = '".$row_sell_trades["trdm_symbol"]."' AND (trdm_buy_sell = 'by' OR trdm_buy_sell = 'Buy') AND trdm_trade_date >= '".$hdate."'";
	$result_buy_trades = mysql_query($get_buy_trades) or die(mysql_error());
	
	//START IF
	if(mysql_num_rows($result_buy_trades) > 0)
	{
		//START WHILE 2
		while($row_buy_trades = mysql_fetch_array($result_buy_trades))
		{
			if($row_buy_trades["trdm_buy_sell"] == 'by')
			{
				$row_buy_trades["trdm_buy_sell"] = 'Buy';
			}
			if($row_sell_trades["trdm_buy_sell"] == 'sl')
			{
				$row_sell_trades["trdm_buy_sell"] = 'Sell';
			}
			$htmlfilebodydata .= 
			'
			<tr><td colspan="2" bgcolor="#000000"></td></tr>
			<tr> 
				<td colspan="2" nowrap align="center"><font face="Courier New, Courier, mono" color="#000000" size="2"><b>'.$row_buy_trades["trdm_account_number"].': '.$arr_acctnames[$row_buy_trades["trdm_account_number"]].'&nbsp;&nbsp;('.$row_buy_trades["trdm_symbol"].' : '.$row_buy_trades["trdm_sec_description"].')</b></font></td>
			</tr>
			<tr><td colspan="2" bgcolor="#000000"></td></tr>
			<tr>
				<td align="left" nowrap><font face="Courier New, Courier, mono" color="#000000" size="2">'.$row_buy_trades["trdm_buy_sell"].'</font></td>
				<td align="left" nowrap><font face="Courier New, Courier, mono" color="#000000" size="2">&nbsp;&nbsp;&nbsp;'.$row_sell_trades["trdm_buy_sell"].'</font></td>
			</tr>
			<tr>	
				<td align="left" nowrap><font face="Courier New, Courier, mono" color="#000000" size="2">Qty:&nbsp;'.$row_buy_trades["trdm_quantity"].'</font></td>
				<td align="left" nowrap><font face="Courier New, Courier, mono" color="#000000" size="2">&nbsp;&nbsp;&nbsp;Qty:&nbsp;'.$row_sell_trades["trdm_quantity"].'</font></td> 
			</tr>
			<tr>	
				<td align="left" nowrap><font face="Courier New, Courier, mono" color="#000000" size="2">Price:&nbsp;'.$row_buy_trades["trdm_price"].'</font></td>
				<td align="left" nowrap><font face="Courier New, Courier, mono" color="#000000" size="2">&nbsp;&nbsp;&nbsp;Price:&nbsp;'.$row_sell_trades["trdm_price"].'</font></td>
			</tr>	
			<tr>	
				<td align="left" nowrap><font face="Courier New, Courier, mono" color="#000000" size="2">Trade Date:&nbsp;'.format_date_ymd_to_mdy($row_buy_trades["trdm_trade_date"]).'</font></td>
				<td align="left" nowrap><font face="Courier New, Courier, mono" color="#000000" size="2">&nbsp;&nbsp;&nbsp;Trade Date:&nbsp;'.format_date_ymd_to_mdy($row_sell_trades["trdm_trade_date"]).'</font></td>
			</tr>
			';
		}//END WHILE 2
	}//END IF
}//END WHILE 1	

$htmlfilebodydata .= '		
		</table>
	</td>
</tr>
';
	
?>

