<?
//echo "test";
//exit;
//Since this is a AJAX requested page, all inputs to this page should be passed with the param string
//Also, all the relevant includes should be a part of this page including css, etc
include('includes/global.php');
include('includes/dbconnect.php');
include('includes/functions.php');
?>
		<link href="includes/styles.css" rel="stylesheet" type="text/css" />
		<table width="100%" height="100%" cellpadding="0", cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
					<table width="100%"  border="0" cellspacing="0" cellpadding="1">
						<?
						$str_sql_select = "SELECT 
																	trad_order_reference_number,
																	trad_trade_date,
																	TIME_FORMAT(trad_order_time, '%l:%i:%s %p' ) as trad_order_time,
																	TIME_FORMAT(trad_exec_time, '%l:%i:%s %p' ) as trad_exec_time,
																	trad_advisor_code,trad_advisor_name,trad_account_name,trad_account_number,trad_symbol,trad_buy_sell,trad_quantity,trade_price,trad_commission,trad_cents_per_share 
																FROM emp_employee_trades
																WHERE trad_is_cancelled = 0
																AND trad_order_reference_number = '".$oref."' 
																ORDER BY trad_auto_id DESC";
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
 							<td width="100">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=format_date_ymd_to_mdy($row_select["trad_trade_date"])?></td> 
							<td width="150">&nbsp;&nbsp;<?=$row_select["trad_account_name"]?></td>
							<td width="200">&nbsp;&nbsp;&nbsp;<?=$row_select["trad_account_number"] . "  (".trim($row_select["trad_advisor_code"]).")"?></td>
							<td width="60">&nbsp;&nbsp;&nbsp;<?=$row_select["trad_symbol"]?></td>
							<td width="100">&nbsp;&nbsp;<?=offset_buy_sell($row_select["trad_buy_sell"])?></td>
							<td width="100" align="right"><?=number_format($row_select["trad_quantity"],0,'',',')?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
							<td width="100" align="right">&nbsp;&nbsp;<?=number_format($row_select["trade_price"],2,'.',',')?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
							<td width="150" align="right">&nbsp;&nbsp;<?=$row_select["trad_order_time"]?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
							<td width="150" align="right">&nbsp;&nbsp;<?=$row_select["trad_exec_time"]?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<?php
						$count_row_select = $count_row_select + 1;
						}
						?>
					</table>
				</td>
			</tr>
		</table>