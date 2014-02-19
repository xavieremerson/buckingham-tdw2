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

if ($x or $sel_symbol) { // form submitted //format_date_ymd_to_mdy($trade_date_to_process)
		if($_POST["sel_client"] == '^ALL^') {
			$show_client = "Show All";
		} else {
			$show_client = $_POST["sel_client"];
		}

		if(trim($_POST["sel_symbol"]) == '') {
			$show_symbol = "";
		} else {
			$show_symbol = $_POST["sel_symbol"];
		}

			$sel_datefrom = $datefrom;
			$sel_dateto = $dateto;
			
			//if brokerage month is selected use that info to create dateto and datefrom values
			if($_POST["sel_month"] == '') {

				$sel_datefrom = $datefrom;
				$sel_dateto = $dateto;
				
				$datefrom = format_date_mdy_to_ymd($datefrom);
				$dateto = format_date_mdy_to_ymd($dateto);
				
				$string_heading = "Selection: Client(s): ".$show_client. "&nbsp;&nbsp;&nbsp;&nbsp;Symbol(s): ".$show_symbol. "&nbsp;&nbsp;&nbsp;&nbsp;Date From: ".$_POST["datefrom"]. "&nbsp;&nbsp;&nbsp;&nbsp;Date To: ".$_POST["dateto"];

			} else {

				$sel_datefrom = $datefrom;
				$sel_dateto = $dateto;

				// ^ caused problems, had to escape it				
				$arr_split_input = split("\^", $sel_month);
				$arr_dates = get_commission_month_dates($arr_split_input[0],$arr_split_input[1]);
				$datefrom = $arr_dates[0];
				$dateto = $arr_dates[1];
				
				$string_heading = "Selection: Client(s): ".$show_client. "&nbsp;&nbsp;&nbsp;&nbsp;Symbol(s): ".$show_symbol. "&nbsp;&nbsp;&nbsp;&nbsp;Date From: ".format_date_ymd_to_mdy($datefrom). "&nbsp;&nbsp;&nbsp;&nbsp;Date To: ".format_date_ymd_to_mdy($dateto);
				

			}
			

} else {


			$sel_datefrom = format_date_ymd_to_mdy(business_day_backward(strtotime($trade_date_to_process),22));
			$sel_dateto = format_date_ymd_to_mdy(previous_business_day());


			$string_heading = "";
			$show_client = "Show All";
			$show_symbol = "";
			$datefrom = business_day_backward(strtotime($trade_date_to_process),22);
			$dateto = previous_business_day();
}
?>
<?
tsp(100,"BCM Trends Analysis: ".$symbol);
?>								<!-- START TABLE 4 -->
								<!-- class="tablewithdata" -->
												<table width="100%" height="100%" bgcolor="#FFFFFF">
													<tr>
														<td>
														<table width="100%" cellpadding="0" cellspacing="0">
														<form name="clnt_activity" id="idclnt_activity" action="" method="post">
															<tr>
																<td width="10">&nbsp;</td>
																<td width="10">&nbsp;</td>
																<td width="100">																
																<input type="text" width="30" maxlength="30" class="Text1" name="sel_symbol" value="">
																</td>
																<td width="5">&nbsp;</td>
																<td width="10">&nbsp;</td>
																<SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
																<SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
																	<SCRIPT LANGUAGE="JavaScript">
																	var calfrom = new CalendarPopup("divfrom");
																	calfrom.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
																	var calto = new CalendarPopup("divto");
																	calto.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
																	
																	</SCRIPT>																
																<td width="10">From:</td>
																<td width="10">&nbsp;</td>
																
																<td width="10"><input type="text" id="iddatefrom" class="Text1" name="datefrom" readonly size="12" maxlength="12" value="<?=$sel_datefrom?>"></td>
																<td width="20" align="center"><A HREF="#" onClick="calfrom.select(document.forms['clnt_activity'].datefrom,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
																<td width="10">&nbsp;</td>
																<td width="10">To:</td>
																<td width="10">&nbsp;</td>
																<td width="10"><input type="text" id="iddateto" class="Text1" name="dateto" readonly size="12" maxlength="12" value="<?=$sel_dateto?>"></td>
																<td width="20" align="center"><A HREF="#" onClick="calto.select(document.forms['clnt_activity'].dateto,'anchor2','MM/dd/yyyy'); return false;" NAME="anchor2" ID="anchor2"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
																<td width="10">&nbsp;</td>
																<td width="10"><input type="image" src="images/lf_v1/form_submit.png"></td>
																<td width="10" align="center">&nbsp;</td>
																<td>&nbsp;</td>
														</tr>
														</form>															
														</table>
														</td> 
													</tr>
													<tr id="pbd"> <!--  style="display=none; visibility=hidden" -->
														<td height="100%">
															<?
															if ($show_symbol != "") {
																if (strpos($show_symbol,",") > 0 ) {
																  $arr_symbols = explode(",",$show_symbol);
																	foreach ($arr_symbols as $k=>$v) {
																		if (trim(strtoupper($v)) != "") {
																		echo "<br><br>Symbol: ".trim(strtoupper($v))." From: ".format_date_ymd_to_mdy($datefrom). " To: ".format_date_ymd_to_mdy($dateto)."<br><br>";	
																		?>
																		<img src="./bcm_trend/chart_img.php?symbol=<?=strtoupper(trim($v))?>&date_start=<?=$datefrom?>&date_end=<?=$dateto?>" />
																		<?
                                    }
																	}
																} else {
																	echo "<br><br>Symbol: ".trim(strtoupper($show_symbol))." From: ".format_date_ymd_to_mdy($datefrom). " To: ".format_date_ymd_to_mdy($dateto)."<br><br>";	
																	?>
																	<img src="./bcm_trend/chart_img.php?symbol=<?=strtoupper(trim($show_symbol))?>&date_start=<?=$datefrom?>&date_end=<?=$dateto?>" />
																	<br><br><br><br><br><br><br><br><br><br><br><br>
																	<?
																	//echo 'img src="./bcm_trend/chart_img.php?symbol='.strtoupper(trim($show_symbol)).'&date_start='.$datefrom.'&date_end='.$dateto;
																}
															} else {
															  echo "Please enter symbol(s). You can enter multiple values separated by commas. Also, select start and end dates.<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>";
															}
															?>
														</td>
													</tr>
												</table>
												<!-- END TABLE 4 -->
	<DIV ID="divfrom" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
	<DIV ID="divto" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>			
				