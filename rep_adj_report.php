<?php
//BRG
include('inc_header.php');

?>
		<!-- START TABLE 1 -->
		<table width="100%" height="100%" border="0" cellspacing="1" cellpadding="0">
			<tr> 
				<td valign="top">


	<? table_start_percent(100, "Report: Adjustments"); ?>
		
		<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
					<!--TABLE 2 START-->
					<script language="JavaScript" src="includes/javascript/sorttable.js" type="text/javascript"></script>

					<table class="sortable" preserve_style="cell" width="100%"  border="0" cellspacing="1" cellpadding="1">
						<tr>
						  <td width="80">Trd Date</td>
							<td width="30">Client</td>
							<td width="180">Client Name</td>
							<td width="100">Acct. Name</td>
							<td width="50">Symbol</td>
							<td width="20">B/S</td>
							<td width="80">Price</td>
							<td width="80">Commission</td>
							<td width="80">From RR</td>
							<td width="80">To RR</td>
							<td width="100">From Acct. #</td>
							<td width="100">To Acct. #</td>
							<td width="130">Adjusted On</td>
							<td>&nbsp;</td>
						</tr>
						<?
						//trad_reference_number  trad_rr  trad_trade_date  trad_run_date  trad_advisor_code  trad_advisor_name  trad_account_name  
						//trad_account_number  trad_symbol  trad_buy_sell  trad_quantity  
						//trade_price  trad_commission  trad_cents_per_share  trad_adj_rr  trad_adj_changed_by  trad_adj_comment  trad_adj_datetime  trad_is_cancelled  
						
						$query_adj = "SELECT *, date_format(trad_adj_datetime, '%m/%d/%Y') as adjdate
														FROM rep_comm_rr_trades_adj 
														ORDER BY trad_trade_date ASC";
														
						//echo $query_trades;
						$result_adj = mysql_query($query_adj) or die(mysql_error());
						$count_row_adj = 0;
						while ( $row_adj = mysql_fetch_array($result_adj) ) 
						{
							if ($count_row_adj%2) {
										$class_row_adj = "trdark";
							} else { 
									$class_row_adj = "trlight"; 
							} 
						?>
						<tr class="<?=$class_row_adj?>"> 
 							<td nowrap><?=format_date_ymd_to_mdy($row_adj["trad_trade_date"])?></td>
							<td nowrap><?=$row_adj["trad_advisor_code"]?></td>
							<td><?=$row_adj["trad_advisor_name"]?></td>
							<td><?=$row_adj["trad_account_name"]?></td>
							<td><?=trim($row_adj["trad_symbol"])?></td>
							<td><?=offset_buy_sell($row_adj["trad_buy_sell"])?></td>
							<td><?=number_format($row_adj["trade_price"],2)?></td>
							<td><?=number_format($row_adj["trad_commission"],0)?></td>
							<td><?=$row_adj["trad_rr"]?></td>
							<td><?=$row_adj["trad_adj_rr"]?></td>
							<td><?=$row_adj["trad_account_number"]?></td>
							<td><?=$row_adj["trad_adj_account_number"]?></td>
							<td><?=$row_adj["adjdate"]?></td>
							<td></td>
						</tr>
						<?php
						$count_row_adj = $count_row_adj + 1;
						}
						?>
					</table>
				</td>
			</tr>
		</table>
	
		<? table_end_percent(); ?>




				</td>
			</tr>
		</table>
		<!-- END TABLE 1 -->
<?php
include('inc_footer.php'); 
?>

