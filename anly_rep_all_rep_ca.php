<?
//===================================================================
// Check Performance 
$str_timedebug = "";
$str_timecount = 0;
$arr_timedebug = array();
$arr_timedebug[$str_timecount]=getmicrotime();
//===================================================================

//show_array($_POST);

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

function extract_client ($str) {
	return substr($str,strpos($str,"[")+1,4); //Code is always 4 characters long.
}

if ($x or $val_client or $_POST) { // form submitted //format_date_ymd_to_mdy($trade_date_to_process)
		
		//print_r($_POST);
		
		if($_POST["val_client"] == 'Enter Client' or $_POST["val_client"] == '') {
			$show_client = "Show All";
		} else {
			$show_client = extract_client($_POST["val_client"]);
		}
		if($_POST["sel_rep"] == '^ALL^') {
			$show_rep = "Show All";
		} else {
			$arr_repinfo = split('\^',$_POST["sel_rep"]);
			$show_rep = $arr_repinfo[0];
			$rep_id = $arr_repinfo[1];
		}
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
				
				$string_heading = "Selection: RR #: ".$show_rep." Client(s): ".$show_client. "&nbsp;&nbsp;&nbsp;&nbsp;Symbol(s): ".$show_symbol. "&nbsp;&nbsp;&nbsp;&nbsp;Date From: ".$_POST["datefrom"]. "&nbsp;&nbsp;&nbsp;&nbsp;Date To: ".$_POST["dateto"];

			} else {

				$sel_datefrom = $datefrom;
				$sel_dateto = $dateto;

				// ^ caused problems, had to escape it				
				$arr_split_input = split("\^", $sel_month);
				$arr_dates = get_commission_month_dates($arr_split_input[0],$arr_split_input[1]);
				$datefrom = $arr_dates[0];
				$dateto = $arr_dates[1];
				
				$string_heading = "Selection: RR #: ".$show_rep." Client(s): ".$show_client. "&nbsp;&nbsp;&nbsp;&nbsp;Symbol(s): ".$show_symbol. "&nbsp;&nbsp;&nbsp;&nbsp;Date From: ".format_date_ymd_to_mdy($datefrom). "&nbsp;&nbsp;&nbsp;&nbsp;Date To: ".format_date_ymd_to_mdy($dateto);
				

			}

} else {

			$sel_datefrom = format_date_ymd_to_mdy($trade_date_to_process);
			$sel_dateto = format_date_ymd_to_mdy($trade_date_to_process);

			$string_heading = "";
			$show_rep = "Show All";
			$show_client = "Show All";
			$show_symbol = "Show All";
			$datefrom = previous_business_day();
			$dateto = previous_business_day();
}


$str_timedebug .= "<br>Headers: " .sprintf("%01.4f",((getmicrotime()-$arr_timedebug[$str_timecount])/1000))." s.";
$str_timecount++;
$arr_timedebug[$str_timecount]=getmicrotime();
?>
<?
tsp(100, "CLIENT ACTIVITY");
?> 		
								<!-- START TABLE 4 -->
								<!-- class="tablewithdata" -->
												<table width="100%" bgcolor="#FFFFFF">
													<tr>
														<td>
														<table width="100%" cellpadding="0" cellspacing="0">
														<form name="clnt_activity" id="idclnt_activity" action="" method="post">
															<tr>
<!--																<td width="100">
																<select class="Text2" name="sel_rep" size="1" >
																<option value="^ALL^">&nbsp;REGISTERED REPS.&nbsp;(ALL)</option>
																<option value="^ALL^">____________</option>
																<?
																//get reps from query  on table mry_comm_rr_trades and join on users
																
																//*************************************************************************
																//This query with join on mry_comm_rr_trades was taking too long, altered
																//to just show reps.
																//*************************************************************************
																
																$qry_get_reps = "SELECT
																									a.ID, a.rr_num, concat(a.Firstname, ' ', a.Lastname ) as rep_name, a.rr_num as trad_rr 
																									from users a
																								WHERE a.rr_num like '0%'
																								AND a.Role > 2
																								AND a.Role < 5
																								ORDER BY a.Firstname";
																
																$result_get_reps = mysql_query($qry_get_reps) or die (tdw_mysql_error($qry_get_reps));
																while($row_get_reps = mysql_fetch_array($result_get_reps))
																{
																//for trades for shared rep, do a reverse lookup in the users table to get the id and then the shared reps
																?>
																	<option value="<?=$row_get_reps["trad_rr"]."^".$row_get_reps["ID"]?>"><?=str_pad($row_get_reps["rep_name"], 20, ".")?>(<?=$row_get_reps["rr_num"]?>)</option>
																<?
																}
																$str_timedebug .= "<br>Reps: " .sprintf("%01.4f",((getmicrotime()-$arr_timedebug[$str_timecount])/1000))." s.";
																$str_timecount++;
																$arr_timedebug[$str_timecount]=getmicrotime();
																?>
																</select>
																</td>
-->																<td width="5">&nbsp;</td>
																<td width="205">
																		<script language="javascript" type="text/javascript" src="includes/actb/actb.js"></script>
																		<script language="javascript" type="text/javascript" src="includes/actb/common.js"></script>
																		<script>
																		var clientarray=new Array(
																			<?
																			$query_sel_client = "SELECT comm_advisor_code, trim(comm_advisor_name) as comm_advisor_name 
																														FROM lkup_clients
																														ORDER BY comm_advisor_name, comm_advisor_code";
																			$result_sel_client = mysql_query($query_sel_client) or die(mysql_error());
																			?>																
																			<?
																			$count_row_client = 0;
																			while($row_sel_client = mysql_fetch_array($result_sel_client))
																			{
																				if ($row_sel_client["comm_advisor_name"] == '') {
																				$display_val_client = $row_sel_client["comm_advisor_code"];
																				} else {
																				$display_val_client = str_replace("'","\\'",$row_sel_client["comm_advisor_name"]);
																				}
																				echo "'". $display_val_client . "  [" .$row_sel_client["comm_advisor_code"]."]',"; //."\n"
																			}
																			?>
																			'');
																		
																		function set_val_null(str_id) {
																			if (document.getElementById(str_id).value == 'Enter Client') {
																				document.getElementById(str_id).value = ""; 
																			}
																		}
																		</script>
																		<input type='text' name="val_client" style='font-family:verdana;width:250px;font-size:12px' id='tb' value='Enter Client' onFocus="set_val_null('tb')" /> 
																		<script>
																		//var obj = actb(document.getElementById('tb'),clientarray);
																		</script>
																</td>
																<td width="5">&nbsp;</td>
																<td width="100">
																		<script>
																		function set_sym_null(str_id) {
																			if (document.getElementById(str_id).value == 'Enter Symbol') {
																				document.getElementById(str_id).value = ""; 
																			}
																		}
																		</script>
																		<input type='text' name="val_symbol" style='font-family:verdana;width:100px;font-size:12px' id='ts' value='Enter Symbol' onFocus="set_sym_null('ts')"  /> 
																		<script>
																			obj = new actb(document.getElementById('tb'),clientarray);
																		</script>
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
																<td width="5">&nbsp;</td>
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
$str_timedebug .= "<br>Top Data: " .sprintf("%01.4f",((getmicrotime()-$arr_timedebug[$str_timecount])/1000))." s.";
$str_timecount++;
$arr_timedebug[$str_timecount]=getmicrotime();

														include('anly_rep_all_rep_ca_inc_trade.php');	
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
