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
							<td ts_type="date" width="85">&nbsp;&nbsp;Trade Date</td>
							<td width="200">&nbsp;&nbsp;Name</td>
							<td width="100">&nbsp;&nbsp;Account</td> 
							<td width="60">&nbsp;&nbsp;Symbol</td>
							<td width="100">&nbsp;&nbsp;Cusip</td> 
							<td width="320">&nbsp;&nbsp;Sec. Description</td>
							<td width="40">&nbsp;&nbsp;Buy/Sell</td>
							<td ts_type="money" width="70" align="right">&nbsp;&nbsp;Quantity</td>
							<td align="right" ts_type="money" width="100">&nbsp;&nbsp;Price</td>
							<td width="80" align="right">&nbsp;&nbsp;Ord Entry</td>
							<td width="75" align="right">&nbsp;&nbsp;Ord Exec</td>
							<td>&nbsp;</td>
						</tr>
						<?
/*						if ($sel_emp == '_ALL_') {
							$str_emp = " and trad_account_name like '%' "	;					
						} else {
							$str_emp = " and trad_account_name like '%".$sel_emp."%' ";				
						}
*/						
						if ($sel_symbol == 'SYMBOL' or trim($sel_symbol) == '') {
							$str_symbol = " and symbol like '%' ";
						} else {
							$str_symbol = " and symbol like '%".strtoupper(trim($sel_symbol))."%' ";
						}
						
						if ($sel_emp == '_ALL_' or trim($sel_emp) == '') {
							$str_name = " and first_name like '%' ";
						} else {
							$znameval = explode("ZZZ",$sel_emp);
							$str_name = " and first_name = '".str_replace("'","\\'",$znameval[0])."' ";
							$str_name .= " and middle_name = '".str_replace("'","\\'",$znameval[1])."' ";
							$str_name .= " and last_name = '".str_replace("'","\\'",$znameval[2])."' ";
						}
						
						$str_sql_select = "SELECT 
												auto_id,
												acct_num,
												trade_date,
												buy_sell,
												symbol,
												cusip,
												date_format(order_entry_time, '%l:%i%p') as order_entry_time,
												date_format(order_exec_time, '%l:%i%p') as order_exec_time,
												concat(sec_desc_1, ' ', sec_desc_2) as sec_desc,  
												round(sum(quantity),0) as quantity, 
												round(avg(price),2) as price,
												is_active, 
												substring(concat(trim(first_name),' ',trim(middle_name),' ',trim(last_name)),1,20) as Fullname
											FROM fidelity_emp_trades
											WHERE trade_date between '".$datefrom."' and '".$dateto."' ".$str_symbol." ".$str_name."
											AND is_active  = 1
											GROUP BY acct_num, symbol, trade_date, buy_sell";
						
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
 							<td><? echo "&nbsp;&nbsp;".format_date_ymd_to_mdy($row_select["trade_date"])."</td>"; ?>
							<td>&nbsp;&nbsp;<?=$row_select["Fullname"]?></td>
							<td>&nbsp;&nbsp;<?=$row_select["acct_num"]?></td>
							<td>&nbsp;&nbsp;<?=$row_select["symbol"]?></td>
							<td>&nbsp;&nbsp;<?=$row_select["cusip"]?></td>
							<td>&nbsp;&nbsp;<?=$row_select["sec_desc"]?></td>
							<td>&nbsp;&nbsp;<?=offset_buy_sell($row_select["buy_sell"])?></td>
							<td align="right"><?=number_format($row_select["quantity"],0,'',',')?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
							<td align="right">&nbsp;&nbsp;<?=number_format($row_select["price"],2,'.',',')?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
							<td align="right"><?=$row_select["order_entry_time"]?>&nbsp;&nbsp;</td>
							<td align="right"><?=$row_select["order_exec_time"]?>&nbsp;&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<?php
						$count_row_select = $count_row_select + 1;
						}
						?>
					</table>
				<?
				   //xdebug("str_sql_select",$str_sql_select);
				?>
				</td>
			</tr>
		</table>
		<? tep(); ?>