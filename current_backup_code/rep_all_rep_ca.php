<?
if ($datefilterval) {
//xdebug('datefilterval',$datefilterval);
$trade_date_to_process = format_date_mdy_to_ymd($datefilterval);
//xdebug('trade_date_to_process',$trade_date_to_process);
} else {
$trade_date_to_process = previous_business_day();
//xdebug('trade_date_to_process',$trade_date_to_process);
}
//$rep_to_process = '028';
$rep_to_process = $rr_num;
?>

<?
if ($x) { // form submitted //format_date_ymd_to_mdy($trade_date_to_process)
		if($_POST["sel_client"] == '^ALL^') {
			$show_client = "Show All";
		} else {
			$show_client = $_POST["sel_client"];
		}
		if($_POST["sel_rep"] == '^ALL^') {
			$show_rep = "Show All";
		} else {
			$show_rep = $_POST["sel_rep"];
		}
		if($_POST["sel_symbol"] == '^ALL^') {
			$show_symbol = "Show All";
		} else {
			$show_symbol = $_POST["sel_symbol"];
		}
		$string_heading = "Selection: RR #: ".$show_rep." Client(s): ".$show_client. "&nbsp;&nbsp;&nbsp;&nbsp;Symbol(s): ".$show_symbol. "&nbsp;&nbsp;&nbsp;&nbsp;Date From: ".$_POST["datefrom"]. "&nbsp;&nbsp;&nbsp;&nbsp;Date To: ".$_POST["dateto"];

			$datefrom = format_date_mdy_to_ymd($datefrom);
			$dateto = format_date_mdy_to_ymd($dateto);

} else {
			$string_heading = "";
			$show_rep = "Show All";
			$show_client = "Show All";
			$show_symbol = "Show All";
			$datefrom = previous_business_day();
			$dateto = previous_business_day();
}
?>

<table width="100%" cellpadding="1" cellspacing="1">
		<tr>
		<td>
			<table width="100%" border="0" cellpadding="0" cellspacing="0" class="test">
						<tr> 
							<td>
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
									<tr> 
										<td height="20" valign="middle" background="images/tables3/header_bk.jpg">
										&nbsp;&nbsp;<a class="table_heading_text">Sales Rep : CLIENT ACTIVITY</a>
										</td>
									</tr>
									<tr> 
										<td valign="middle">			

				<!-- START TABLE 3 -->
					<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
						<tr>
							<td valign="top">
								<!-- START TABLE 4 -->
								<!-- class="tablewithdata" -->
												<table width="100%" bgcolor="#FFFFFF">
													<tr>
														<td>
														<table width="100%" cellpadding="0" cellspacing="0">
														<form name="clnt_activity" id="idclnt_activity" action="" method="post">
															<tr>
																<td>&nbsp;</td>
																<td width="100">
																<select class="Text1" name="sel_rep" size="1" >
																<option value="^ALL^">&nbsp;REGISTERED REPS.&nbsp;(ALL)</option>
																<option value="^ALL^">____________</option>
																<?
																$query_sel_rrep = "SELECT ID , concat( Lastname, ', ', FirstName ) AS repname, rr_num
																											FROM users 
																											WHERE Role =3
																											ORDER BY Lastname";
																$result_sel_rrep = mysql_query($query_sel_rrep) or die(mysql_error());
																while($row_sel_rrep = mysql_fetch_array($result_sel_rrep))
																{
																//for tradesfor shared rep, do a reverse lookup in the users table to get the id and then the shared reps
																?>
																<option value="<?=$row_sel_rrep["rr_num"]?>"><?=$row_sel_rrep["repname"]?>&nbsp;&nbsp;</option>
																<?
																}
																?>
																</select>
																</td>

																<td width="100">
																<select class="Text1" name="sel_client" size="1" >
																<option value="^ALL^">&nbsp;CLIENTS&nbsp;(ALL)</option>
																<option value="^ALL^">____________</option>
																<?
																$query_sel_client = "SELECT comm_advisor_code, max( comm_advisor_name ) as comm_advisor_name 
																											FROM rep_comm_rr_level_a
																											GROUP BY comm_advisor_code
																											ORDER BY comm_advisor_name";
																$result_sel_client = mysql_query($query_sel_client) or die(mysql_error());
																while($row_sel_client = mysql_fetch_array($result_sel_client))
																{
																	if ($row_sel_client["comm_advisor_name"] == '') {
																	$display_val_client = $row_sel_client["comm_advisor_code"];
																	} else {
																	$display_val_client = $row_sel_client["comm_advisor_name"];
																	}
																?>
																
																<option value="<?=$row_sel_client["comm_advisor_code"]?>"><?=$display_val_client?>&nbsp;&nbsp;</option>
																<?
																}
																?>
																</select>
																</td>
																<td width="100">																
																<select class="Text1" name="sel_symbol" size="1" >
																<option value="^ALL^">&nbsp;SYMBOLS&nbsp;(ALL)&nbsp;&nbsp;</option>
																<option value="^ALL^">_____________</option>
																<?
																$query_sel_symbol = "SELECT DISTINCT(trad_symbol)
																											FROM rep_comm_rr_trades 
																											ORDER BY trad_symbol";
																$result_sel_symbol = mysql_query($query_sel_symbol) or die(mysql_error());
																while($row_sel_symbol = mysql_fetch_array($result_sel_symbol))
																{
																?>
																<option value="<?=$row_sel_symbol["trad_symbol"]?>"><?=$row_sel_symbol["trad_symbol"]?></option>
																<?
																}
																?>
																</select>
																</td>
																<SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
																	<SCRIPT LANGUAGE="JavaScript">
																	var calfrom = new CalendarPopup();
																	calfrom.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
																	var calto = new CalendarPopup();
																	calto.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
																	
																	</SCRIPT>																
																<td width="10">From:</td>
																<td width="10"><input type="text" id="iddatefrom" class="Text1" name="datefrom" readonly size="12" maxlength="12" value="<?=format_date_ymd_to_mdy($trade_date_to_process)?>"></td>
																<td width="20" align="center"><A HREF="#" onClick="calfrom.select(document.forms['clnt_activity'].datefrom,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
																<td width="10">To:</td>
																<td width="10"><input type="text" id="iddateto" class="Text1" name="dateto" readonly size="12" maxlength="12" value="<?=format_date_ymd_to_mdy($trade_date_to_process)?>"></td>
																<td width="20" align="center"><A HREF="#" onClick="calto.select(document.forms['clnt_activity'].dateto,'anchor2','MM/dd/yyyy'); return false;" NAME="anchor2" ID="anchor2"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
																<td width="10"><input type="image" src="images/lf_v1/form_submit.png"></td>
																<td width="10" align="center">&nbsp;</td>
																<td width="10" align="center">&nbsp;</td>
															</tr>
														</form>															
														</table>
														</td> 
													</tr>
												</table>
												<!-- END TABLE 4 -->
              </td>
						</tr>
						</table>
						<!-- END TABLE 3 -->
				</td>
			</tr>
			<tr id="pbd"> <!--  style="display=none; visibility=hidden" -->
				<td>
					<?
					include('rep_all_rep_ca_inc_trade.php');	
					?>
				</td>
			</tr>

		</table>
		</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>