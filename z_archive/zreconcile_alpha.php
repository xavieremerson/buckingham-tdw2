<style type="text/css">
<!--
.all_general {
	font-family: "Courier New", Courier, mono;
	font-size: 12px;
}
-->
</style>
<?

include('includes/functions.php');
include('includes/global.php');
include('includes/dbconnect.php');

//Reconcile data between current DOS ansd NFS

//get data like all rep client activity


//$datefrom = previous_business_day();
//$dateto   = previous_business_day();

$datefrom = '2006-03-21';
$dateto   = '2006-03-21';

echo "DAILY COMMISSIONS RECONCILIATION (Trade Date: ".format_date_ymd_to_mdy($datefrom).")<br>";


?>

<?	
//get all NFS trades
			//Get trades for the default/selected previous trade date	(table : rep_comm_rr_trades)		
			//fields are trad_rr  trad_trade_date  trad_advisor_code  trad_advisor_name  trad_account_name  trad_account_number  
			//trad_symbol  trad_buy_sell  trad_quantity  trade_price  trad_commission  trad_cents_per_share 						
?>
<table border="0" bgcolor="#666666" cellpadding="1" cellspacing="0"><tr><TD>
			<table border="0" cellspacing="1" cellpadding="0">
					<tr>
						<td bgcolor="#ffffff">&nbsp;&nbsp;NFS/TDW</td>
						<td bgcolor="#ffffff"></td>
						<td bgcolor="#ffffff">&nbsp;&nbsp;DOS</td>
					</tr>
<?
//high level numbers by advisor and total commission dollars
			$query_adv_level = "SELECT 
													trad_advisor_code,
													DATE_FORMAT(trad_trade_date,'%m/%d/%Y') as trad_trade_date,
													max(trad_advisor_name) as trad_advisor_name,
													FORMAT(sum(trad_commission),2) as trad_commission,
													sum(trad_commission) as xfor_sum_trad_commission,
													max(trad_rr) as trad_rr 
												FROM mry_comm_rr_trades 
												WHERE trad_is_cancelled = 0 
												AND trad_trade_date between '".$datefrom."' AND '".$dateto."'".
												" GROUP BY trad_advisor_code ORDER BY trad_advisor_code";
												
								
			$result_adv_level = mysql_query($query_adv_level) or die(tdw_mysql_error($query_adv_level));
			while($row_adv_level = mysql_fetch_array($result_adv_level))
			{
			?>
			<tr>
				<td bgcolor="#ffffff" valign="top">
					<table class="all_general">
							<tr>
							<td width="80">Trade Date</td>
							<td width="50">Client</td>
							<td width="30">Rep</td>
							<td width="60">Symbol</td>
							<td width="20">B/S</td>
							<td width="60" align="right">Qty.</td>
							<td width="60" align="right">Price</td>
							<td width="60" align="right">Comm.</td>
							</tr>							
					<?
								$running_total_adv = 0;
								$query_trades = "SELECT 
																	trad_advisor_code,
																	trad_symbol,
																	trad_buy_sell,
																	DATE_FORMAT(trad_trade_date,'%m/%d/%Y') as trad_trade_date,
																	max(trad_advisor_name) as trad_advisor_name,
																	FORMAT(sum(trad_quantity),0) as trad_quantity,
																	FORMAT(max(trade_price),2) as trade_price,
																	FORMAT(sum(trad_commission),2) as trad_commission,
																	sum(trad_commission) as for_sum_trad_commission,
																	FORMAT(avg(trad_cents_per_share),3) as trad_cents_per_share,
																	max(trad_rr) as trad_rr 
																FROM mry_comm_rr_trades 
																WHERE trad_is_cancelled = 0 and trad_advisor_code = '".$row_adv_level["trad_advisor_code"]."'  
																AND trad_trade_date between '".$datefrom."' AND '".$dateto."'".
																" GROUP BY trad_advisor_code, trad_symbol, trad_buy_sell, trad_trade_date 
																ORDER BY trad_advisor_name, sum(trad_commission)";
																//removed from order by clause :  trad_symbol, trad_buy_sell, trad_trade_date
								$result_trades = mysql_query($query_trades) or die(tdw_mysql_error($query_trades));
								while($row_trades = mysql_fetch_array($result_trades)) {
								$running_total_adv = $running_total_adv + $row_trades["for_sum_trad_commission"];
								$running_total_adv_level = $running_total_adv_level + $row_trades["for_sum_trad_commission"];
								
							?>

							<tr>
							<td><?=$row_trades["trad_trade_date"]?></td>
							<td><?=$row_trades["trad_advisor_code"]?></td>
							<td><?=$row_trades["trad_rr"]?></td>
							<td><?=$row_trades["trad_symbol"]?></td>
							<td><?=$row_trades["trad_buy_sell"]?></td>
							<td align="right"><?=$row_trades["trad_quantity"]?></td>
							<td align="right"><?=$row_trades["trade_price"]?></td>
							<td align="right"><?=$row_trades["trad_commission"]?></td>
							</tr>							
							<?
								}
							?>
							<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td align="right"></td>
							<td align="right"></td>
							<td align="right"><strong><?=number_format($running_total_adv ,2)?></strong></td>
							<? $compare_val_nfs = $running_total_adv; ?>
							</tr>							
					</table>
			
				</td>
				<td width="1" bgcolor="#ffffff"></td>
				<td bgcolor="#ffffff" valign="top">
					<table class="all_general">
						<tr>
							<td width="50">Client</td>
							<td width="30">Commission</td>
						</tr>

						<?
						//DOS COMMISSIONS
						$total_dos = 0;
						$query_dos = "SELECT * 
													FROM mry_dos_commission 
													WHERE clnt_code = '".$row_adv_level["trad_advisor_code"]."' order by clnt_commission ";
						$result_dos = mysql_query($query_dos) or die(tdw_mysql_error($query_dos));
						while($row_dos = mysql_fetch_array($result_dos)) {
						$total_dos = $total_dos + $row_dos["clnt_commission"];
						$grand_total_dos = $grand_total_dos + $row_dos["clnt_commission"];
						?>
						<tr>
							<td><?=$row_dos["clnt_code"]?></td>
							<td><?=number_format($row_dos["clnt_commission"],2)?></td>
						</tr>
						<?
						}
						?>
						<tr>
							<td></td>
							<td>
							<?
							$diff_val = abs($total_dos - $compare_val_nfs);
							if ($diff_val < 5) {
							echo '<strong>'.number_format($total_dos,2).'</strong>';
							} else {
							echo '<strong><font color="FF0000"><u>'.number_format($total_dos,2).'</u></font></strong>';
							}
							?>
							</td>
						</tr>
					</table>
				</td>			
			</tr>
			
			<?			
			}
?>
<tr>
<td bgcolor="#ffffff" align="right">
<strong><?=number_format($running_total_adv_level ,2)?></strong></td>
<td width="1" bgcolor="#ffffff"></td>
<td bgcolor="#ffffff" align="right">
<strong><?=number_format($grand_total_dos,2)?></strong></td>
</tr>
</table>
</TD></tr></table>		
<?		
?>
