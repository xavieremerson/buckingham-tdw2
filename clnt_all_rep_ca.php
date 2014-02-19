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
if ($x or $_POST) {
//show_array($_POST);

 // form submitted //format_date_ymd_to_mdy($trade_date_to_process)
		if($_POST["sel_client"] == '^ALL^') {
			$show_client = "Show All";
		} else {
			$show_client = $_POST["sel_client"];
		}

		if($_POST["tickersinput2"] == 'Enter Symbol' or $_POST["tickersinput2"] == "") {
			$show_symbol = "Show All";
		} else {
			$show_symbol = $_POST["tickersinput2"];
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
														<td><br />
														<table width="100%" cellpadding="0" cellspacing="0">
														<form name="clnt_activity" id="idclnt_activity" action="" method="post">
															<tr>
																<td width="5">&nbsp;</td>
																<td width="100">
																<select class="Text1" name="sel_client" size="1" >
																<option value="^ALL^">&nbsp;CLIENTS&nbsp;(ALL)</option>
																<option value="^ALL^">____________</option>
																<script type="text/javascript">
																var dc = new Array()
																<?
																
																//MAKE THIS MORE ACCURATE (GO TO int_clnt_clients TABLE)
																$query_sel_client = "SELECT clnt_code as comm_advisor_code, clnt_name as comm_advisor_name
																											FROM int_clnt_clients 
																											WHERE clnt_status = 'A'
																											AND clnt_code != '----'
																											AND clnt_code != 'BUCK' and clnt_code != 'FRIS'
																											ORDER BY clnt_name, clnt_code";

																/*$query_sel_client = "SELECT comm_advisor_code, comm_advisor_name 
																											FROM lkup_clients
																											WHERE length(comm_advisor_name) > 4
																											AND comm_advisor_code != 'BUCK' and comm_advisor_code != 'FRIS'
																											ORDER BY comm_advisor_name, comm_advisor_code";
																
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
																<td width="180" valign="top">
																<?
																//analyst coverage list showing
																//get all stocks covered by the analyst
																$qry_symbols = "select acv_symbol from acv_analyst_coverage where acv_email = '".$user_email."'";
																$result_symbols = mysql_query($qry_symbols) or die(tdw_mysql_error($qry_symbols));
																$arr_symbols = array();
																while($row_symbols = mysql_fetch_array($result_symbols)) {
																	$arr_symbols[] = $row_symbols["acv_symbol"];
																}
																
																//print_r($arr_symbols);
																
																$arr_sectors = array();
																foreach($arr_symbols as $k=> $symbol_val) {
																//now get distinct sector from the list for this user
																$sector_val = db_single_val("select industry as single_val from sec_master where symbol = '".$symbol_val."' LIMIT 1");
																$arr_sectors[$sector_val] = $symbol_val;
																echo "<!--".$sector_val."//".$symbol_val."-->\n";		
																}
																
																//print_r($arr_sectors);
																
																
																////
																// Brian Amaturo on 01/28/2012 pointed out seeing other stocks, not of his interest
																
																//OLD===============================================================================================
																//OLD===============================================================================================
																//OLD===============================================================================================
																$arr_final_symbol_list = array();
																foreach($arr_sectors as $sector=>$val) {
																	if($sector != ''){
																	//xdebug("sector",$sector);  
																		$qry_symbols_final = "select symbol, description from sec_master where industry = '".$sector."'";
																		echo "<!--".$qry_symbols_final."-->\n";
																		$result_symbols_final = mysql_query($qry_symbols_final) or die(tdw_mysql_error($qry_symbols_final));
																		while($row_symbols_final = mysql_fetch_array($result_symbols_final)) {
																			$arr_final_symbol_list[$row_symbols_final["symbol"]] = $row_symbols_final["description"];
																			echo "<!--".$row_symbols_final["symbol"]."//".$row_symbols_final["description"]."-->\n";
																		}
																	}
																}	
																//OLD===============================================================================================
																//OLD===============================================================================================
																//OLD===============================================================================================

																//NEW===============================================================================================
																//NEW===============================================================================================
																//NEW===============================================================================================
																
																//xdebug("user_email",$user_email);
																
																$arr_final_symbol_list_2 = array();
																
																$qry_symbols_final_2 = "select acv_symbol from acv_analyst_coverage
																										  where acv_email = '".strtolower($user_email)."'";
																$result_symbols_final_2 = mysql_query($qry_symbols_final_2) or die(tdw_mysql_error($qry_symbols_final_2));
																while($row_symbols_final_2 = mysql_fetch_array($result_symbols_final_2)) {
																	$arr_final_symbol_list_2[$row_symbols_final_2["acv_symbol"]] = $row_symbols_final_2["acv_symbol"];
																}
																
																//NEW===============================================================================================
																//NEW===============================================================================================
																//NEW===============================================================================================
																
																ksort($arr_final_symbol_list);
																//show_array($arr_final_symbol_list);
																//$arr_final_symbols = array_flip($arr_final_symbol_list);
																$arr_final_symbols = array_flip($arr_final_symbol_list_2);
																//show_array($arr_final_symbols);
																?>
																<!--+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++-->
																<link type="text/css" rel="stylesheet" href="includes/yui/build/reset/reset.css">
																<link type="text/css" rel="stylesheet" href="includes/yui/build/fonts/fonts.css">
																<link type="text/css" rel="stylesheet" href="includes/yui/build/logger/assets/logger.css">
																<link type="text/css" rel="stylesheet" href="includes/yui/docs/assets/dpSyntaxHighlighter.css">
																
																<style type="text/css">
																		#tickersmod {position:relative;}
																		#tickersautocomplete, #tickersautocomplete2 {position:absolute;width:8.5em;margin-bottom:1em;}/* set width of widget here*/
																		#tickersautocomplete {z-index:9000} /* for IE z-index of absolute divs inside relative divs issue 404040*/
																		#tickersinput, #tickersinput2 {width:100%;height:1.6em;z-index:0;}
																		#tickerscontainer, #tickerscontainer2 {position:absolute;top:1.6em;width:200%}
																		#tickerscontainer .yui-ac-content, #tickerscontainer2 .yui-ac-content {position:absolute;width:100%;border:1px solid #4040FF;background:#fff;overflow:hidden;z-index:9050;}
																		#tickerscontainer .yui-ac-shadow, #tickerscontainer2 .yui-ac-shadow {position:absolute;margin:.3em;width:100%;background:#5BA1FF;z-index:9049;}
																		#tickerscontainer ul, #tickerscontainer2 ul {padding:5px 0;width:100%;}
																		#tickerscontainer li, #tickerscontainer2 li {padding:0 5px;cursor:default;white-space:nowrap;}
																		#tickerscontainer li.yui-ac-highlight, #tickerscontainer2 li.yui-ac-highlight {background:#B2DEFA;}
																		#tickerscontainer li.yui-ac-prehighlight,#tickerscontainer2 li.yui-ac-prehighlight {background:#CFF2FF;}
																</style>
																
																<div id="bd">
																		<!-- AutoComplete begins -->
																		<div id="tickersmod">
																						<div id="tickersautocomplete2">
																						 &nbsp;&nbsp;&nbsp;<input type="text" id="tickersinput2" name="tickersinput2" onFocus="set_blank()" value="Enter Symbol"><br>
																							 &nbsp;&nbsp;&nbsp;<div id="tickerscontainer2"></div>
																						</div>
																		</div>
																		<!-- AutoComplete ends -->
																</div>
																<!-- Content ends -->
																
																<!-- Libary begins -->
																<script type="text/javascript" src="includes/yui/build/yahoo/yahoo.js"></script>
																<script type="text/javascript" src="includes/yui/build/dom/dom.js"></script>
																<script type="text/javascript" src="includes/yui/build/event/event-debug.js"></script>
																<script type="text/javascript" src="includes/yui/build/animation/animation.js"></script>
																<script type="text/javascript" src="includes/yui/build/autocomplete/autocomplete-debug.js"></script>
																<script type="text/javascript" src="includes/yui/build/logger/logger.js"></script>
																<!-- Library ends -->
																
																
																
																<!-- In-memory JS array begins-->
																<script type="text/javascript">
																var tickersArray = [
																["---","----------------------"]
																<?
																		foreach($arr_final_symbol_list as $s=>$d) {
																			echo ', ["'.$s.'", "'.ucwords(strtolower($d)).'"]'."\n";
																		}
																		//, ["AB", "Alliancebernstein"]
																?>
																];
																</script>
																<!-- In-memory JS array ends-->
																
																
																<script type="text/javascript">
																YAHOO.example.ACJSArray = function() {
																		//var mylogger;
																		var oACDS,oACDS2;
																		var oAutoComp,oAutoComp2;
																		return {
																				init: function() {
																
																						//Logger
																						//mylogger = new YAHOO.widget.LogReader("logger");
																						
																						// Instantiate second JS Array DataSource
																						oACDS2 = new YAHOO.widget.DS_JSArray(tickersArray);
																
																						// Instantiate second AutoComplete
																						oAutoComp2 = new YAHOO.widget.AutoComplete('tickersinput2','tickerscontainer2', oACDS2);
																						oAutoComp2.queryDelay = 0;
																						oAutoComp2.prehighlightClassName = "yui-ac-prehighlight";
																						oAutoComp2.typeAhead = true;
																						oAutoComp2.useShadow = true;
																						oAutoComp2.forceSelection = true;
																						oAutoComp2.formatResult = function(oResultItem, sQuery) {
																								var sMarkup = oResultItem[0] + " (" + oResultItem[1] + ")";
																								return (sMarkup);
																						};
																				},
																
																				validateForm: function() {
																						// Validate form inputs here
																						return false;
																				}
																		};
																}();
																
																YAHOO.util.Event.addListener(this,'load',YAHOO.example.ACJSArray.init);
																</script>
																<script type="text/javascript" src="includes/yui/docs/assets/dpSyntaxHighlighter.js"></script>
																<script type="text/javascript">
																dp.SyntaxHighlighter.HighlightAll('code');
																</script>
																<script type="text/javascript">
																
																function set_blank () {
																	if (document.getElementById('tickersinput2').value == 'Enter Symbol') {
																		document.getElementById('tickersinput2').value = ''; 
																	}
																}
																</script>
																
																<!--++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++-->																
																
															  </td>
																<td width="5">&nbsp;</td>
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
														include('clnt_all_rep_ca_inc_trade.php');	
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
