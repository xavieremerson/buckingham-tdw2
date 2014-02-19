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

function extract_client ($str) {
	return substr($str,strpos($str,"[")+1,4); //Code is always 4 characters long.
}

if ($x or $val_client) { // form submitted //format_date_ymd_to_mdy($trade_date_to_process)
		
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
		if($_POST["sel_symbol"] == '^ALL^') {
			$show_symbol = "Show All";
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
																<td width="100">
																<select class="Text1" name="sel_rep" size="1" >
																<option value="^ALL^">&nbsp;REGISTERED REPS.&nbsp;(ALL)</option>
																<option value="^ALL^">____________</option>
																<?
																//get reps from query  on table mry_comm_rr_trades and join on users
																$qry_get_reps = "SELECT
																									a.ID, a.rr_num, concat(a.Lastname, ', ', a. Firstname) as rep_name, b.trad_rr 
																									from users a, mry_comm_rr_trades b
																								WHERE a.rr_num = b.trad_rr
																								AND b.trad_rr like '0%'
																								AND a.Role > 2
																								AND a.Role < 5
																								GROUP BY b.trad_rr
																								ORDER BY a.Lastname";
																/*							
																								 SELECT
																									ID, rr_num, rep_name, trad_rr 
																								 FROM lkup_rrep
																								 ORDER BY rep_name
																*/
																$result_get_reps = mysql_query($qry_get_reps) or die (tdw_mysql_error($qry_get_reps));
																while($row_get_reps = mysql_fetch_array($result_get_reps))
																{
																//for tradesfor shared rep, do a reverse lookup in the users table to get the id and then the shared reps
																?>
																					<option value="<?=$row_get_reps["trad_rr"]."^".$row_get_reps["ID"]?>"><?=$row_get_reps["rep_name"]?>&nbsp; &nbsp; (<?=$row_get_reps["rr_num"]?>)</option>
																<?
																}
																?>
																</select>
																</td>
																<td width="5">&nbsp;</td>
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
																																echo "'". $display_val_client . "  [" .$row_sel_client["comm_advisor_code"]."]',"."\n";
																															}
																															?>
																															'');
																		
																		function set_val_null(str_id) {
																			if (document.getElementById(str_id).value == 'Enter Client') {
																				document.getElementById(str_id).value = ""; 
																			}
																		}
																		
																		</script>
																		<input type='text' name="val_client" style='font-family:verdana;width:200px;font-size:12px' id='tb' value='Enter Client' onFocus="set_val_null('tb')" /> 
																		<script>
																		var obj = actb(document.getElementById('tb'),clientarray);
																		</script>
																</td>
																<td width="5">&nbsp;</td>
																<td width="100">
																<select class="Text1" name="sel_client" size="1" >
																<option value="^ALL^">&nbsp;CLIENTS&nbsp;(ALL)</option>
																<option value="^ALL^">____________</option>
																<script type="text/javascript">
																var dc = new Array()
																<?
																
																//MAKE THIS MORE ACCURATE (GO TO MRY_COMM_RR_TRADES TABLE)
																
																$query_sel_client = "SELECT comm_advisor_code, comm_advisor_name 
																											FROM lkup_clients
																											ORDER BY comm_advisor_name, comm_advisor_code";
																/*
																SELECT comm_advisor_code, max( comm_advisor_name ) as comm_advisor_name 
																											FROM rep_comm_rr_level_a
																											WHERE comm_advisor_code NOT LIKE '&%'
																											GROUP BY comm_advisor_code
																											ORDER BY comm_advisor_name, comm_advisor_code
																*/
																$result_sel_client = mysql_query($query_sel_client) or die(mysql_error());
																?>																
																<?
																$count_row_client = 0;
																while($row_sel_client = mysql_fetch_array($result_sel_client))
																{
																	if ($row_sel_client["comm_advisor_name"] == '') {
																	$display_val_client = $row_sel_client["comm_advisor_code"];
																	} else {
																	$display_val_client = $row_sel_client["comm_advisor_name"];
																	}
                                echo 'dc ['.$count_row_client.'] = "'.$row_sel_client["comm_advisor_code"].'^'.
																												trim($display_val_client).'"'.";\n";
																$count_row_client = $count_row_client + 1;
																}
																?>
																
																for (i=0;i<dc.length;i++)
																	{
																	var rowclient_array = new Array() 
																	rowclient_array=dc[i].split("^");
																	document.write("<option value='"+rowclient_array[0]+"'>"+rowclient_array[1]+"</option>");
																	}
																</script>
																</select>
																</td>
																<td width="5">&nbsp;</td>
																<td width="100">																
																<select class="Text1" name="sel_symbol" size="1" >
																<option value="^ALL^">&nbsp;SYMBOLS&nbsp;(ALL)&nbsp;&nbsp;</option>
																<option value="^ALL^">_____________</option>
																<script type="text/javascript">
																var ds
																<?
																$query_sel_symbol = "SELECT trad_symbol
																											FROM lkup_symbols 
																											ORDER BY trad_symbol";
																											/*SELECT DISTINCT(trad_symbol)
																											FROM rep_comm_rr_trades 
																											ORDER BY trad_symbol*/
																$result_sel_symbol = mysql_query($query_sel_symbol) or die(mysql_error());
																while($row_sel_symbol = mysql_fetch_array($result_sel_symbol))
																{
																	echo 'ds = "'.$row_sel_symbol["trad_symbol"].'"'.";\n";	
																}
																?>
																var rowsymbol = new Array()
																rowsymbol=ds.split("^");
																for (i=0;i<rowsymbol.length-1;i++)
																		{
																		document.write("<option value='"+rowsymbol[i]+"'>"+rowsymbol[i]+"</option>");
																		}
																</script>
																
																</select>
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
																<td width="10"><input type="text" id="iddatefrom" class="Text1" name="datefrom" readonly size="12" maxlength="12" value="<?=$sel_datefrom?>"></td>
																<td width="20" align="center"><A HREF="#" onClick="calfrom.select(document.forms['clnt_activity'].datefrom,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
																<td width="5">&nbsp;</td>
																<td width="10">To:</td>
																<td width="10"><input type="text" id="iddateto" class="Text1" name="dateto" readonly size="12" maxlength="12" value="<?=$sel_dateto?>"></td>
																<td width="20" align="center"><A HREF="#" onClick="calto.select(document.forms['clnt_activity'].dateto,'anchor2','MM/dd/yyyy'); return false;" NAME="anchor2" ID="anchor2"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
																<td width="5">&nbsp;</td>
																<td width="10"><input type="image" src="images/lf_v1/form_submit.png"></td>
																<td width="10" align="center">&nbsp;</td>
																<td width="10" align="center">&nbsp;</td>
																<td>&nbsp;
																
																</td>
															</tr>
														</form>			
														</table>
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
												<!-- END TABLE 4 -->
<?
tep();
?>						
	<DIV ID="divfrom" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
	<DIV ID="divto" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>			