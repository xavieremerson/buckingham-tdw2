<script language="JavaScript" src="includes/js/ajax_tbx.js"></script>
<script language ="Javascript">
<!--
function popdiv(divid,oref) {
  var trid;
	trid = 'div_'+ divid; 

	if (document.getElementById) { // DOM3 = IE5, NS6 Generally this is what it is

		if (document.getElementById(trid).style.getAttribute("visibility") == "" || document.getElementById(trid).style.getAttribute("visibility") == "hidden" ) {
			document.getElementById(trid).style.visibility = 'visible'; 
			document.getElementById(trid).style.display = 'block'; 
			document.getElementById('img'+ divid).src = 'images/lf_v1/collapse.png';
			if (document.getElementById(trid).innerHTML == "") {

					AjaxRequest.get(
						{
							'url':'mod_emp_trades_inc_trade_fills.php?ifid='+trid+'&oref='+oref
							,'onSuccess':function(req){ document.getElementById(trid).innerHTML=req.responseText; }
							,'onError':function(req){ document.getElementById(trid).innerHTML='Error receiving data.';}
						}
					);

			}
			//alert(document.getElementById(trid).src)
		} else {
			document.getElementById(trid).style.visibility = 'hidden'; 
			document.getElementById(trid).style.display = 'none'; 
			document.getElementById('img'+ divid).src = 'images/lf_v1/expand.png';
		}		
	} 
}
-->
</script>

	<? tsp(100, "Employee Trades"); ?>
  	<script language="JavaScript" src="includes/javascript/sorttable.js" type="text/javascript"></script>
		<table width="100%" height="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
					<table class="sortable" preserve_style="cell" width="100%"  border="0" cellspacing="1" cellpadding="1">
						<tr class="ilt">
							<td ts_type="date" width="100">&nbsp;&nbsp;Trade Date</td>
							<td width="150">&nbsp;&nbsp;Name</td>
							<td width="200">&nbsp;&nbsp;Account</td> 
							<td width="60">&nbsp;&nbsp;Symbol</td>
							<td width="100">&nbsp;&nbsp;Buy/Sell</td>
							<td ts_type="money" width="100" align="right">&nbsp;&nbsp;Quantity</td>
							<td align="right" ts_type="money" width="100">&nbsp;&nbsp;Price</td>
							<td width="150">&nbsp;&nbsp;Order Time</td>
							<td width="150">&nbsp;&nbsp;Exec Time</td>
							<td>&nbsp;</td>
						</tr>
						<?
						if ($sel_emp == '_ALL_') {
							$str_emp = " and trad_account_name like '%' "	;					
						} else {
							$str_emp = " and trad_account_name like '%".$sel_emp."%' ";				
						}
						
						if ($sel_symbol == 'SYMBOL' or trim($sel_symbol) == '') {
							$str_symbol = " and trad_symbol like '%' ";
						} else {
							$str_symbol = " and trad_symbol like '%".trim($sel_symbol)."%' ";
						}
						
						
						$str_sql_select = "SELECT 
																	count(trad_order_reference_number) as tcount,
																	trad_order_reference_number,
																	max(trad_rr) as trad_rr,
																	trad_trade_date,
																	TIME_FORMAT(max(trad_order_time), '%l:%i:%s %p' ) as trad_order_time,
																	TIME_FORMAT(max(trad_exec_time), '%l:%i:%s %p' ) as trad_exec_time,
																	max(trad_advisor_code) as trad_advisor_code,
																	max(trad_advisor_name) as trad_advisor_name,
																	max(trad_account_name) as trad_account_name,
																	max(trad_account_number) as trad_account_number,
																	max(trad_symbol) as trad_symbol,
																	max(trad_buy_sell) as trad_buy_sell,
																	sum(trad_quantity) as trad_quantity,
																	avg(trade_price) as trade_price,
																	sum(trad_commission) as trad_commission,
																	avg(trad_cents_per_share) as trad_cents_per_share 
																FROM emp_employee_trades
																WHERE trad_is_cancelled = 0
																AND trad_trade_date between '".$datefrom."' and '".$dateto."' ".$str_emp.$str_symbol."
																GROUP BY trad_order_reference_number
																ORDER BY trad_auto_id DESC";
						/*
						trad_auto_id  trad_reference_number  trad_rr  trad_trade_date  trad_run_date  
						trad_settle_date  trad_advisor_code  trad_advisor_name  trad_account_name  
						trad_account_number  trad_symbol  trad_buy_sell  trad_quantity  trade_price  
						trad_commission  trad_cents_per_share  trad_is_cancelled  						
						*/
            //xdebug("str_sql_select",$str_sql_select);
						$result_select = mysql_query($str_sql_select) or die(tdw_mysql_error($str_sql_select));

						$count_row_select = 0;
						while ( $row_select = mysql_fetch_array($result_select) ) 
						{
							if ($count_row_select%2) {
										$class_row_select = "trdark";
							} else { 
									$class_row_select = "trlight"; 
							} 
						?>
						<tr class="<?=$class_row_select?>"> 
 							<td>
							<?
							if ($row_select["tcount"] > 1) {
							$rand_id = md5(rand(1,999999999));
							?>
							<!--<a href="javascript:popdiv('<?=$rand_id?>','<?=$row_select["trad_order_reference_number"]?>')">
							<img id="img<?=$rand_id?>" src="images/lf_v1/expand.png" border="0"></a>-->
							&nbsp;&nbsp;<?=format_date_ymd_to_mdy($row_select["trad_trade_date"])?></td>
							<?
							} else {
							?>
							<!--&nbsp;&nbsp;&nbsp;&nbsp;-->&nbsp;&nbsp;<?=format_date_ymd_to_mdy($row_select["trad_trade_date"])?></td>
							<?
							}
							?>
							
							<td>&nbsp;&nbsp;<?=$row_select["trad_account_name"]?></td>
							<td>&nbsp;&nbsp;<?=$row_select["trad_account_number"] . "  (".trim($row_select["trad_advisor_code"]).")"?></td>
							<td>&nbsp;&nbsp;<?=$row_select["trad_symbol"]?></td>
							<td>&nbsp;&nbsp;<?=offset_buy_sell($row_select["trad_buy_sell"])?></td>
							<td align="right"><?=number_format($row_select["trad_quantity"],0,'',',')?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
							<td align="right">&nbsp;&nbsp;<?=number_format($row_select["trade_price"],2,'.',',')?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
							<td align="right">&nbsp;&nbsp;<?=$row_select["trad_order_time"]?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
							<td align="right">&nbsp;&nbsp;<?=$row_select["trad_exec_time"]?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<!--<tr>
							<td colspan="10">
						<?
						if ($row_select["tcount"] > 1) {
						?>
								<div name="div_<?=$rand_id?>" id="div_<?=$rand_id?>"></div>
						<?
						}
						?>
							</td>
						</tr>	-->						
						<?php
						$count_row_select = $count_row_select + 1;
						}
						?>
					</table>
				</td>
			</tr>
		</table>
		<? tep(); ?>