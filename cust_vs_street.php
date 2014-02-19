<script language="JavaScript" src="includes/prototype/prototype.js"></script>
<script language="JavaScript" src="includes/js/ajax_tbx.js"></script>
<script language ="Javascript">
<!--
function procx(args) {

	var argval;
	argval=args.split("^");
	//alert(args);
	//return false;
	
	var progressbar;
	progressbar = 'Getting supporting data.<br><br><img src="images/loading-bar.gif" border="0">';
	document.getElementById(argval[0]).innerHTML=progressbar; 
	
	AjaxRequest.get(
			{
				'url':'cust_vs_street_ajx.php?spotdate='+ argval[0] + '&symbol=' + argval[1]
				,'onSuccess':function(req){ 
																		parse_req(req.responseText, argval[0]);
																	}
				,'onError':function(req){ document.getElementById('notify').innerHTML='Program Error! Please contact Technical Support.';}
			}
		);
}

function parse_req(response, divid) {
		document.getElementById(divid).innerHTML=response; 
	//alert($response);
}

function noenter() {
  return !(window.event && window.event.keyCode == 13); }
-->
</script>

<script language="JavaScript" src="includes/wz/wz_tooltip.js" type="text/javascript"></script>
<style>
.sdata {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 11px;
	text-transform: capitalize;
}
.headsub {
	background-color: #0000CC;
	font-weight: bold;
	font-size: 11px;
	color: #99FFFF;
}
.light2 {
	background-color: #FFFFFF;
}
.dark2 {
	background-color: #EFEFEF;
}
.light2o {
	background-color: #FFFFFF;
	font-weight: bold;
	color: #FF6600;
}
.dark2o {
	background-color: #EFEFEF;
	font-weight: bold;
	color: #FF6600;
}
</style>
<?
//*********************************************************************************************
//Create Lookup Array of Client Code / Client Name

	$qry_clients = "select * from int_clnt_clients";
	$result_clients = mysql_query($qry_clients) or die (tdw_mysql_error($qry_clients));
	$arr_clients = array();
	while ( $row_clients = mysql_fetch_array($result_clients) ) 
	{
		$arr_clients[$row_clients["clnt_code"]] = $row_clients["clnt_name"];
	}
	
	//temporary MUST CHANGE THIS LATER
	function look_up_client($clnt) {
		global $arr_clients;
		if ($arr_clients[$clnt] == '') {
		   return $clnt;
		} else {
		   return $arr_clients[$clnt];
		}
	}
//*********************************************************************************************

function previous_bizday ($dateval=NULL) {

	if ($dateval==NULL) {
		$working_dateval = date('Y-m-d');
	} else {
		$working_dateval = $dateval;
	}
	
	$i = 1;
	while ($i < 7) {
		 if (date("w",strtotime($working_dateval)-(60*60*24*$i)) > 0 AND
				 date("w",strtotime($working_dateval)-(60*60*24*$i)) < 6 AND
				 check_holiday(date("Y-m-d", strtotime($working_dateval)-(60*60*24*$i))) == 0 ) {
				$val_pbd = date("Y-m-d",strtotime($working_dateval)-(60*60*24*$i));
			 return $val_pbd;
		 } else {
				$i = $i + 1;
		 }
	}
}

//get the oldest report date.
 $query_min_date = "SELECT min(msrv_trade_date) as min_date FROM mgmt_reports_creation";
 $result_min_date = mysql_query($query_min_date) or die(tdw_mysql_error($query_min_date));
	while ($row_min_date = mysql_fetch_array($result_min_date)) {
	$min_date =	$row_min_date["min_date"];
 }

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

if ($x or $_POST) { // form submitted //format_date_ymd_to_mdy($trade_date_to_process)
		
	//print_r($_POST);
	$sel_datefrom = $datefrom;
	$sel_dateto = $dateto;
		 
	$datefrom = format_date_mdy_to_ymd($datefrom);
	$dateto = format_date_mdy_to_ymd($dateto);

} else {

	$sel_datefrom = format_date_ymd_to_mdy(business_day_backward(strtotime(date('Y-m-d')),10));
	$sel_dateto = format_date_ymd_to_mdy(previous_business_day());

	$datefrom = business_day_backward(strtotime(date('Y-m-d')),10);
	$dateto = previous_business_day();
}

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//declare some arrays
$arr_cust = array();
$qry_cust =  "SELECT 
								trad_advisor_code,
								trad_symbol,
								trad_buy_sell,
								DATE_FORMAT(trad_trade_date,'%m/%d/%Y') as trad_trade_date,
								trad_trade_date as val_trad_trade_date,
								max(trad_advisor_name) as trad_advisor_name,
								FORMAT(sum(trad_quantity),0) as trad_quantity,
								sum(trad_quantity) as for_sum_trad_quantity,
								FORMAT(max(trade_price),2) as trade_price,
								FORMAT(sum(trad_commission),2) as trad_commission,
								sum(trad_commission) as for_sum_trad_commission,
								FORMAT(avg(trad_cents_per_share),3) as trad_cents_per_share,
								trad_rr 
							FROM mry_comm_rr_trades 
							WHERE trad_is_cancelled = 0 
							AND trad_trade_date between '".$datefrom."' AND '".$dateto."'". 
							" GROUP BY trad_advisor_code, trad_rr, trad_symbol, trad_buy_sell, trad_trade_date 
							ORDER BY  trad_trade_date desc, trad_symbol, trad_buy_sell, trad_advisor_name";
$result_cust = mysql_query($qry_cust) or die(tdw_mysql_error($qry_cust));
while ($row = mysql_fetch_array($result_cust)) {
	$arr_cust[] =	$row["val_trad_trade_date"]."^".$row["trad_advisor_code"]."^".$row["trad_symbol"]."^".$row["trad_buy_sell"]."^".$row["for_sum_trad_quantity"]."^".$row["trade_price"];
}

$arr_street = array();
$qry_street = "SELECT
								Ticker,
								Quantity,
								fill_price,
								buy_sell,
								Exchange,
								count_Exchange,
								customer_id,
								trade_date,
								exec_broker,
								count_exec_broker,
								min_manual_time,
								max_manual_time,
								parent_id,
								count_parent_id
							FROM tradeware_trades_consolidated 
							where trade_date between  '".$datefrom."' and '".$dateto."'
							ORDER BY trade_date desc, Ticker";

//xdebug("qry_street",$qry_street);
$result_street = mysql_query($qry_street) or die(tdw_mysql_error($qry_street));
while ($row = mysql_fetch_array($result_street)) {
	$arr_street[] =	$row["trade_date"]."^".$row["customer_id"]."^".$row["Ticker"]."^".$row["buy_sell"]."^".$row["Quantity"]."^".$row["fill_price"];
}

//show_array($arr_cust);
//show_array($arr_street);
//exit;

?>

<script type="text/javascript" src="includes/javascript/calendar/calendar.js"></script>
<script type="text/javascript" src="includes/javascript/calendar/lang/calendar-en.js"></script>
<!-- helper script that uses the calendar -->
<script type="text/javascript" src="includes/javascript/calendar/helper.js"></script>
<link rel="alternate stylesheet" type="text/css" media="all" href="includes/javascript/calendar/calendar-win2k-2.css" title="win2k-2" />
<script type="text/javascript">setActiveStyleSheet(document.getElementById("defaultTheme"), "win2k-2");</script>

<STYLE>
<!--
blink {color: red}
-->
</STYLE>
<SCRIPT>
<!--
function doBlink() {
	var blink = document.all.tags("BLINK")
	for (var i=0; i<blink.length; i++)
		blink[i].style.visibility = blink[i].style.visibility == "" ? "hidden" : "" 
}

function startBlink() {
	if (document.all)
		setInterval("doBlink()",300)
}
window.onload = startBlink;
// -->
</SCRIPT>

<?
//===========================================================================================================
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//Process to get all report types and all who viewed them for the selected time interval
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//===========================================================================================================


//xdebug("min_date",$min_date);
// Get the dates (Business Days in the past 30 days)
//for ($i=1; $i<10; $i++) {
//$arr_report_dates[$i] = previous_bizday($arr_report_dates[$i-1]);
//}

$arr_report_dates = array();
$arr_report_dates[0] = $dateto;
for ($i=1; $i<120; $i++) {
	if (strtotime($arr_report_dates[$i-1]) > strtotime($datefrom)) {
		$arr_report_dates[$i] = previous_bizday($arr_report_dates[$i-1]);
	} else {
	  //nothing
	}
}
//print_r($arr_report_dates);


?>

<? tsp(100, "Customer vs. Streetside"); ?>

																<table width="100%" cellpadding="0" cellspacing="0">
														<form name="clnt_activity" id="idclnt_activity" action="" method="get">
															<tr>
																<SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
																<SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
																	<SCRIPT LANGUAGE="JavaScript">
																	var calfrom = new CalendarPopup("divfrom");
																	calfrom.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
																	var calto = new CalendarPopup("divto");
																	calto.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
																	
																	</SCRIPT>						
																<td width="5">&nbsp;</td>
																<td width="130" class="ilt" align="right">Trade Date From: </td>
																<td width="10"><input type="text" id="iddatefrom" class="Text1" name="datefrom" size="14" maxlength="12" value="<?=$sel_datefrom?>"></td>
																<td width="20" align="center"><A HREF="#" onClick="calfrom.select(document.forms['clnt_activity'].datefrom,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
																<td width="5">&nbsp;</td>
																<td width="10" class="ilt">To: </td>
																<td width="10"><input type="text" id="iddateto" class="Text1" name="dateto" size="14" maxlength="12" value="<?=$sel_dateto?>"></td>
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

				
		
<!--		<a class="ilt" href="rep_viewed_mgmt_print.php" target="_blank">PRINT</a>
-->		<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
					<!--TABLE 2 START-->
					<table width="100%"  border="0" cellspacing="1" cellpadding="1">
						<tr class="lf11b">
						  <td width="110">Trade Date</td>
							<td width="500">Customer Vs. Street Side</td>
							<td width="500">&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						
						<? 
						$count_row = 0;
						foreach ( $arr_report_dates as $key => $report_date ) {
											if ($count_row%2 == 0) {
													$rowclass = " class=\"trdark\"";
											} else {
													$rowclass = " class=\"trlight\"";
											}
											
												?>
                        <tr <?=$rowclass?>>						
                          <td valign="top"><?=format_date_ymd_to_mdy($report_date)?></td>
                          <td>
                          		<table class="sdata">
																<tr class="headsub"><td colspan="5">Customer</td><td width="25">&nbsp;</td><td colspan="3">Street</td></tr>
															<?
															//$row["val_trad_trade_date"]."^".$row["trad_advisor_code"]."^".$row["trad_symbol"]."^".$row["trad_buy_sell"]."^".$row["trad_quantity"]."^".$row["trade_price"];

															$count_sub = 0;
															foreach ($arr_cust as $indx=>$valstr) {

																$arr_temp_cust = explode("^",$valstr);
																if ($arr_temp_cust[0] == $report_date) {

																		//$row["trade_date"]."^".$row["customer_id"]."^".$row["Ticker"]."^".$row["buy_sell"]."^".$row["Quantity"]."^".$row["fill_price"];
																		$postval = 0;
																		$mval_1 = "";
																		$mval_4 = "";
																		$mval_5 = "";
																		foreach ($arr_street as $sindx=>$svalstr) {
																			$arr_temp_street = explode("^",$svalstr);
																			if ($arr_temp_street[0] == $report_date) {
																					if ($arr_temp_street[2] == $arr_temp_cust[2] && $arr_temp_street[4] == $arr_temp_cust[4]) {

                                          		if ($postval == 0) {
                                                  $mval_1 = $arr_temp_street[1];
                                                  $mval_4 = $arr_temp_street[4];
                                                  $mval_5 = $arr_temp_street[5];
                                                  $postval = 1;
                                              }

																					}
																			}	
																		}

																	if ($count_sub%2 == 0) {
																			if ($postval != 1) {
																				$rowclasssub = " class=\"dark2o\"";
																			} else {
																				$rowclasssub = " class=\"dark2\"";
																			}
																	} else {
																			if ($postval != 1) {
																				$rowclasssub = " class=\"light2o\"";
																			} else {
																				$rowclasssub = " class=\"light2\"";
																			}
																	}
	
																	
																?>
                                
                                
                                
																	<tr <?=$rowclasssub?> >
                                  	<td><?=$arr_temp_cust[2]?></td>
                                    <td><?=look_up_client(trim($arr_temp_cust[1]))?></td>
                                    <td><?=$arr_temp_cust[3]?></td>
                                    <td align="right"><?=number_format($arr_temp_cust[4],0,"",",")?></td>
                                    <td align="right"><?=$arr_temp_cust[5]?></td>
                                    
                                    <td>&nbsp;</td>
                                    <?
																		if ($postval == 1) {
																		?>
                                    	<td><?=$mval_1?></td>
																			<td align="right"><?=number_format($mval_4,0,"",",")?></td>
																			<td align="right"><?=number_format($mval_5,2,".",",")?></td>
																		<?
                                    } else {
																		?>
	                                    <td colspan="3"><font color="ff0000"><a href="#" onclick="procx('<?=$report_date."^".$arr_temp_cust[2]?>'); return false;">[?]</a></font></td>
																		<?
                                    }
																		?>
                                   </tr>
																<?
																}															
															$count_sub++;
															}
															?>
                              </table>
                          </td>
													<td valign="top"><div id="<?=$report_date?>"></div></td>
													<td>&nbsp;</td>
												</tr>
												<?
						$count_row = $count_row + 1;
						}
						?>
					</table>
				</td>
			</tr>
		</table>
	
		<? tep(); ?>
		<?
		echo "</center>";
/////////////////////////////////////////////////END OF DELETE SECTION/////////////////////////////////////////////////
?>
	<DIV ID="divfrom" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
	<DIV ID="divto" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>			