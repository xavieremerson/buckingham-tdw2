<?
if ($datefilterval) {
//xdebug('datefilterval',$datefilterval);
$trade_date_to_process = format_date_mdy_to_ymd($datefilterval);
//xdebug('trade_date_to_process',$trade_date_to_process);
} else {
$trade_date_to_process = previous_business_day();
//xdebug('trade_date_to_process',$trade_date_to_process);
}

if ($x) { // form submitted //format_date_ymd_to_mdy($trade_date_to_process)

			$sel_datefrom = $datefrom;
			$sel_dateto = $dateto;
			
			$datefrom = format_date_mdy_to_ymd($datefrom);
			$dateto = format_date_mdy_to_ymd($dateto);
			
			$string_heading = "Selection: &nbsp;Date From: ".$_POST["datefrom"]. "&nbsp;&nbsp;&nbsp;&nbsp;Date To: ".$_POST["dateto"];

} else {

			$sel_datefrom = format_date_ymd_to_mdy($trade_date_to_process);
			$sel_dateto = format_date_ymd_to_mdy($trade_date_to_process);

			$string_heading = "";
			$datefrom = previous_business_day();
			$dateto = previous_business_day();
}
?>
<?
tsp(100,"Employee Trades (Fidelity) : Date Filter");
?>
								<!-- START TABLE 4 -->
								<!-- class="tablewithdata" -->
												<table width="100%" bgcolor="#FFFFFF">
													<tr>
														<td>
														<table width="100%" cellpadding="0" cellspacing="0">
														<form name="clnt_activity" id="idclnt_activity" action="mod_fid_emp_trades_container.php" method="post">
															<tr>
																<td width="10">&nbsp;</td>
																<SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
																<SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
																	<SCRIPT LANGUAGE="JavaScript">
																	var calfrom = new CalendarPopup("divfrom");
																	calfrom.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),5))?>",null);
																	var calto = new CalendarPopup("divto");
																	calto.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),5))?>",null);
																	</SCRIPT>																
																<td width="10">From:</td>
																<td width="10">&nbsp;</td>
																<td width="10"><input type="text" id="iddatefrom" class="Text1" name="datefrom" size="12" maxlength="12" value="<?=$sel_datefrom?>"></td>
																<td width="20" align="center"><A HREF="#" onClick="calfrom.select(document.forms['clnt_activity'].datefrom,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
																<td width="10">&nbsp;</td>
																<td width="10">To:</td>
																<td width="10">&nbsp;</td>
																<td width="10"><input type="text" id="iddateto" class="Text1" name="dateto" size="12" maxlength="12" value="<?=$sel_dateto?>"></td>
																<td width="20" align="center"><A HREF="#" onClick="calto.select(document.forms['clnt_activity'].dateto,'anchor2','MM/dd/yyyy'); return false;" NAME="anchor2" ID="anchor2"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
																<td width="10">&nbsp;</td>
																<td width="50">
																<select name="sel_emp" size="1">
																<option value="_ALL_" selected="selected">ALL EMPLOYEES</option>
																<?
																$str_sql_select = "SELECT 
																                    DISTINCT (CONCAT( first_name,  '^', TRIM( middle_name ) ,  '^', last_name )) as display_name
																										FROM  `fidelity_emp_trades` 
																										ORDER BY first_name";
																$result_select = mysql_query($str_sql_select) or die(tdw_mysql_error($str_sql_select));
																$count_row_select = 0; 
																while ( $row_select = mysql_fetch_array($result_select) ) {
																$arr_name = explode("^", $row_select['display_name']);
																$name_show = str_replace("  "," ",ucwords(strtolower($arr_name[0]." ".$arr_name[1]." ".$arr_name[2])));
																$name_val = $arr_name[0]."ZZZ".$arr_name[1]."ZZZ".$arr_name[2]; 
																?>
																<option value="<?=$name_val?>"><?=$name_show?></option>
																<?
																}
																?>
																</select> 
																</td>
															<td width="10">&nbsp;</td>
																<td width="60">
																<input type="text" size="20" maxlength="20" name="sel_symbol" value="SYMBOL" />
																</td>
																<td width="10">&nbsp;</td>
																<td width="10"><input type="image" src="images/lf_v1/form_submit.png"></td>
																<td width="10" align="center">&nbsp;</td>
																<?
																$passtoexcel = substr(md5(rand(100,999)),0,10).'^'.$datefrom.'^'.$dateto.'^'.$sel_emp.'^'.$sel_symbol;
																//echo $passtoexcel;
																?>
																<td width="80"><a href="mod_fid_emp_trades_exp_trade_excel.php?xl=<?=$passtoexcel?>" target="_blank"><img src="images/lf_v1/exp2excel.png" border="0"></a></td>
																<td>&nbsp;</td>
														</tr>
														</form>															
														</table>
														</td> 
													</tr>
													<tr id="pbd"> <!--  style="display=none; visibility=hidden" -->
														<td>
															<?
															include('mod_fid_emp_trades_inc_trade.php');
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
				