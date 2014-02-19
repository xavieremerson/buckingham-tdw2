<?
//===================================================================
// Check Performance 
$str_timedebug = "";
$str_timecount = 0;
$arr_timedebug = array();
$arr_timedebug[$str_timecount]=getmicrotime();
//===================================================================


if ($datefilterval) {
//xdebug('datefilterval',$datefilterval);
$trade_date_to_process = format_date_mdy_to_ymd($datefilterval);
//xdebug('trade_date_to_process',$trade_date_to_process);
} else {
$trade_date_to_process = previous_business_day();
//xdebug('trade_date_to_process',$trade_date_to_process);
}

if ($x or $_POST) { // form submitted //format_date_ymd_to_mdy($trade_date_to_process)
		
		if($_POST["val_symbol"] == 'Enter Symbol' or trim($_POST["val_symbol"]) == '') {
			$show_symbol = "Show All";
		} else {
			$show_symbol = $_POST["val_symbol"];
		}

			$sel_datefrom = $datefrom;
			$sel_dateto = $dateto;

			//if brokerage month is selected use that info to create dateto and datefrom values
			if($_POST["sel_month"] == '') {

				$sel_datefrom = $datefrom;
				$sel_dateto = $dateto;
				
				$datefrom = format_date_mdy_to_ymd($datefrom);
				$dateto = format_date_mdy_to_ymd($dateto);
				
				$string_heading = "Selection: &nbsp;Symbol(s): ".$show_symbol. "&nbsp;&nbsp;&nbsp;&nbsp;Date From: ".$_POST["datefrom"]. "&nbsp;&nbsp;&nbsp;&nbsp;Date To: ".$_POST["dateto"];

			} else {

				$sel_datefrom = $datefrom;
				$sel_dateto = $dateto;

				// ^ caused problems, had to escape it				
				$arr_split_input = split("\^", $sel_month);
				$arr_dates = get_commission_month_dates($arr_split_input[0],$arr_split_input[1]);
				$datefrom = $arr_dates[0];
				$dateto = $arr_dates[1];
				
				$string_heading = "Selection: &nbsp;Symbol(s): ".$show_symbol. "&nbsp;&nbsp;&nbsp;&nbsp;Date From: ".format_date_ymd_to_mdy($datefrom). "&nbsp;&nbsp;&nbsp;&nbsp;Date To: ".format_date_ymd_to_mdy($dateto);
				

			}

} else {

			$sel_datefrom = format_date_ymd_to_mdy($trade_date_to_process);
			$sel_dateto = format_date_ymd_to_mdy($trade_date_to_process);

			$string_heading = "";
			$show_symbol = "Show All";
			$datefrom = previous_business_day();
			$dateto = previous_business_day();
}
?>
<?
tsp(100, "BCM ACTIVITY");
?> 		
								<!-- START TABLE 4 -->
								<!-- class="tablewithdata" -->
												<table width="100%" bgcolor="#FFFFFF">
													<tr>
														<td>
														<table width="100%" cellpadding="0" cellspacing="0" border="0">
														<form name="clnt_activity" id="idclnt_activity" action="" method="post">
															<tr>
																<td width="100">
																		<script>
																		function set_sym_null(str_id) {
																			if (document.getElementById(str_id).value == 'Enter Symbol') {
																				document.getElementById(str_id).value = ""; 
																			}
																		}
																		</script>
																		<input type='text' name="val_symbol" style='font-family:verdana;width:100px;font-size:12px' id='ts' value='Enter Symbol' onFocus="set_sym_null('ts')"  /> 
																</td>
                                <td width="5">&nbsp;</td>
																<td width="100">																
																<select class="Text1" name="sel_month" size="1" >
																<option value="">&nbsp;BROKERAGE MONTH&nbsp;&nbsp;</option>
																<option value="">_______________</option>
																<?
																echo create_commission_month();
																?>
																</select>
																</td>

																<SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
																<SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
																	<SCRIPT LANGUAGE="JavaScript">
																	var calfrom = new CalendarPopup("divfrom");
																	calfrom.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
																	var calto = new CalendarPopup("divto");
																	calto.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
																	
																	</SCRIPT>						
																<td width="15">&nbsp;</td>
																<td width="5"><a class="ilt"> OR </a></td>
																<td width="10">From:</td>
																<td width="10"><input type="text" id="iddatefrom" class="Text1" name="datefrom" size="12" maxlength="12" value="<?=$sel_datefrom?>"></td>
																<td width="20" align="center"><A HREF="#" onClick="calfrom.select(document.forms['clnt_activity'].datefrom,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
																<td width="5">&nbsp;</td>
																<td width="10">To:</td>
																<td width="10"><input type="text" id="iddateto" class="Text1" name="dateto" size="12" maxlength="12" value="<?=$sel_dateto?>"></td>
																<td width="20" align="center"><A HREF="#" onClick="calto.select(document.forms['clnt_activity'].dateto,'anchor2','MM/dd/yyyy'); return false;" NAME="anchor2" ID="anchor2"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
																<td width="5">&nbsp;</td>
																<td width="10"><input type="image" src="images/lf_v1/form_submit.png"></td>
																<td width="10" align="center">&nbsp;</td>
																<td width="100" align="center" valign="bottom">
																<?						
																//Get trades for the default/selected previous trade date	(table : rep_comm_rr_trades)		
																//fields are trad_rr  trad_trade_date  trad_advisor_code  trad_advisor_name  trad_account_name  trad_account_number  
																//trad_symbol  trad_buy_sell  trad_quantity  trade_price  trad_commission  trad_cents_per_share 						
																if ($show_symbol != "Show All") {
																	$qry_string_symbol = " AND oth_symbol = '".$show_symbol."' ";
																} else {
																	$qry_string_symbol = "";
																}
																
													
																$query_trades = "SELECT 
																										* 
																									FROM oth_other_trades  
																									WHERE oth_trade_date between '".$datefrom."' AND '".$dateto."'"
																									. $qry_string_symbol .
																									" ORDER BY oth_trade_time desc";
																																
																$passtoexcel = md5(rand(100,999)).'^'.$datefrom.'^'.$dateto.'^'.$qry_string_symbol;
																						
																echo '<a class="links_temp" href="bcm_activity_excel.php?xl='.$passtoexcel.'" target="_blank"><img src="images/lf_v1/exp2excel.png" border="0"></a>';
                                ?>
                                </td>
																<td width="10" align="center">&nbsp;</td>
																<td>&nbsp;</td>
															</tr>
														</form>			
														</table>
														</td> 
													</tr>
													<tr id="pbd"> <!--  style="display=none; visibility=hidden" -->
														<td>
														<?
														include('bcm_activity_inc_trade.php');	
														?>
														</td>
													</tr>
												</table>
												<!-- END TABLE 4 -->
<?
tep();
?>						
	<DIV ID="divfrom" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
	<DIV ID="divto" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>			
