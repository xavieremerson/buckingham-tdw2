<?
if ($fsweep) {
$str_fsweep = "&fsweep=1";
} else {
$str_fsweep = "";
}
?>
<br />
<table><tr><td>&nbsp;&nbsp;&nbsp;<a href="bcm_trend_v3_citta_excel.php?c_datefrom=<?=$c_datefrom?>&c_dateto=<?=$c_dateto?><?=$str_fsweep?>" target="_blank"><img src="images/lf_v1/exp2excel.png" border="0" /></a>
</td><td>&nbsp;&nbsp;&nbsp;<font style="font-family:Arial; font-size:11px; color:#0000CC">&nbsp;&nbsp;&nbsp;[Exports the data shown below to Excel.]</font>
</td></tr></table>
<br />

<?
function emp_clnt($str) {
	if ($str == "") {
		return "---";
	} else if ($str == "BCM-Client") {
		return "Client";
	} else {
		return "Employee";
	}
}

?>
    <table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
<?
//if ($_POST) { print_r($_POST); }
?>
					<!--TABLE 2 START-->
					<script language="JavaScript" src="includes/javascript/sorttable.js" type="text/javascript"></script>

					<table class="sortable" preserve_style="cell" width="100%"  border="0" cellspacing="1" cellpadding="1">
					<tr>
							<? if ($fsweep) { ?>
              <td>Matched</td>
              <? } ?>
              <td>Trade Date</td>
              <td>Broker</td>
              <td>Buy/Sell</td>
              <td>Symbol</td>
              <td>Quantity</td>
              <td>Price</td>
              <td>Commission</td>
              <td>Net</td>
              <td>Trade Time</td>
              <td>PM</td>
              <td>Empl. / Client</td>
              <td>Trade TS</td>
              <td>First Exec.</td>
              <td>Last Exec.</td>
							<td>&nbsp;</td>
					</tr>
<?
//Array ( [c_datefrom] => 08/12/2011 [c_dateto] => 09/14/2011 [proc_citta] => C [x] => 11 [y] => 9 ) 
$f_date = format_date_mdy_to_ymd($c_datefrom);
$t_date = format_date_mdy_to_ymd($c_dateto);

//first get the list of symbols in the active date range
$query_symbol = "SELECT distinct(citta_company_symbol) as citta_symbol
								 FROM citta_list 
								 where (
								 					(citta_date_received <= '" . $f_date . "')
												or
													(citta_date_received >= '" . $f_date . "' and citta_date_received <= '" . $t_date . "')
											 )
									 and citta_isactive = 1 order by citta_company_symbol";
//xdebug("query_symbol",$query_symbol);
$arr_symbols = array();
$result_symbol = mysql_query($query_symbol) or die(tdw_mysql_error($query_symbol));
while($row = mysql_fetch_array($result_symbol)) {
		$arr_symbols[] = $row["citta_symbol"];
}

//show_array($arr_symbols);
$str_symbols = " ('".implode("','", $arr_symbols)."') ";
//xdebug("str_symbols",$str_symbols);

if ($fsweep) {
  $qry_string_add = "";
} else {
	$qry_string_add = " oth_symbol in ".$str_symbols." and ";
}


$query_trds = "SELECT  
									auto_id,
									oth_trade_date,
									oth_process_date,
									oth_original_trade_id,
									oth_broker,
									oth_buysell,
									oth_symbol,
									oth_quantity,
									oth_price,
									oth_commission,
									oth_net_money,
									oth_trade_time,
									oth_pm_code,
									oth_emp_client,
									oth_emp_alloc,
									oth_trade_id,
									DATE_FORMAT(oth_trade_ts, '%l:%i:%s%p') as oth_trade_ts,
									DATE_FORMAT(oth_first_exec, '%l:%i:%s%p') as oth_first_exec,
									DATE_FORMAT(oth_last_exec, '%l:%i:%s%p') as oth_last_exec
							 FROM `oth_other_trades` 
							 where ". $qry_string_add . " 
							 oth_isactive = 1 
							 and oth_trade_date between '".$f_date."' and '".$t_date."'";
								 //oth_symbol in ".$str_symbols." and 
//xdebug("query_trds",$query_trds);
//exit;
$result_trds = mysql_query($query_trds) or die(tdw_mysql_error($query_trds));

$count_row = 0;
while($row = mysql_fetch_array($result_trds)) {
		if ($count_row%2 == 0) {
			$rowclass = " class=\"trlight\"";
		} else {
			$rowclass = " class=\"trdark\"";
		}
		?>
		<tr <?=$rowclass?>>
    	<?
			if ($fsweep) {
				if (in_array($row["oth_symbol"], $arr_symbols)) {
					echo '<td>&nbsp;&nbsp;<font color="red">YES</font></td>';
				} else {
					echo '<td>&nbsp;</td>';
				}
			}
			?>
			<td>&nbsp;&nbsp;<?=format_date_ymd_to_mdy($row["oth_trade_date"])?></td>
			<td>&nbsp;&nbsp;<?=$row["oth_broker"]?></td>
			<td nowrap="nowrap">&nbsp;&nbsp;<?=$row["oth_buysell"]?></td>
			<td>&nbsp;&nbsp;<?=$row["oth_symbol"]?></td>
			<td align="right"><?=number_format($row["oth_quantity"],0,"",",")?>&nbsp;&nbsp;</td>
			<td align="right"><?=number_format($row["oth_price"],2,".",",")?>&nbsp;&nbsp;</td>
			<td align="right"><?=number_format($row["oth_commission"],2,".",",")?>&nbsp;&nbsp;</td>
			<td align="right" nowrap="nowrap"><?=number_format($row["oth_net_money"],2,".",",")?>&nbsp;&nbsp;</td>
			<td align="right"><?=$row["oth_trade_time"]?>&nbsp;&nbsp;</td>
			<td nowrap="nowrap">&nbsp;&nbsp;<?=$row["oth_pm_code"]?></td>
			<td>&nbsp;&nbsp;<?=emp_clnt($row["oth_emp_client"])?></td>
			<td align="right"><?=$row["oth_trade_ts"]?>&nbsp;&nbsp;</td>
			<td align="right" nowrap="nowrap"><?=$row["oth_first_exec"]?>&nbsp;&nbsp;</td>
			<td align="right" nowrap="nowrap"><?=$row["oth_last_exec"]?>&nbsp;&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<?
		$count_row++;
	}
?>
      </td>
    </tr>
  </table>
  	</td>
  </tr>
</table>

