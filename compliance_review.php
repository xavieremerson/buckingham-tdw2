<?php
//BRG
include('inc_header.php');

$qry_rept = "select auto_id 
							from mgmt_reports_creation 
							where msrv_trade_date between '2011-01-25' and '2011-01-27'
							and msrv_rep_id = 'DCARV2'";
$res_rept = mysql_query($qry_rept) or die(tdw_mysql_error($qry_rept));
$arr_rept = array();
while ($row = mysql_fetch_array($res_rept)) {
		$arr_rept[] = $row["auto_id"];
}
$str_rept = " ('".implode("','",$arr_rept)."') ";
//show_array($str_rept);
//echo $str_rept;


$arr_combine_open = array();
$arr_combine_notes = array();

$qry_a = "select pac_rep_id, sum(pac_isopen) as pac_isopen,  count(pac_rep_id) as sum_rep_id from crep_agency_cross	where pac_rep_id in ".$str_rept."	group by pac_rep_id";
$res_a = mysql_query($qry_a) or die(tdw_mysql_error($qry_a));
while ($row = mysql_fetch_array($res_a)) {
		if (array_key_exists($row["pac_rep_id"],$arr_combine_open)) {
			$arr_combine_open[$row["pac_rep_id"]] = $arr_combine_open[$row["pac_rep_id"]] + $row["pac_isopen"];	
		} else {
			$arr_combine_open[$row["pac_rep_id"]] = $row["pac_isopen"];	
		}
		
		if (array_key_exists($row["pac_rep_id"],$arr_combine_notes)) {
			$arr_combine_notes[$row["pac_rep_id"]] = $arr_combine_notes[$row["pac_rep_id"]] + $row["sum_rep_id"];	
		} else {
			$arr_combine_notes[$row["pac_rep_id"]] = $row["sum_rep_id"];	
		}
}

$qry_b = "select emp_rep_id, sum(emp_isopen) as emp_isopen, count(emp_rep_id) as sum_rep_id from crep_emp_trades where emp_rep_id in ".$str_rept."	group by emp_rep_id";
$res_b = mysql_query($qry_b) or die(tdw_mysql_error($qry_b));
while ($row = mysql_fetch_array($res_b)) {
		if (array_key_exists($row["emp_rep_id"],$arr_combine_open)) {
			$arr_combine_open[$row["emp_rep_id"]] = $arr_combine_open[$row["emp_rep_id"]] + $row["emp_isopen"];	
		} else {
			$arr_combine_open[$row["emp_rep_id"]] = $row["emp_isopen"];	
		}
		
		if (array_key_exists($row["emp_rep_id"],$arr_combine_notes)) {
			$arr_combine_notes[$row["emp_rep_id"]] = $arr_combine_notes[$row["emp_rep_id"]] + $row["sum_rep_id"];	
		} else {
			$arr_combine_notes[$row["emp_rep_id"]] = $row["sum_rep_id"];	
		}
}

$qry_c = "select mri_rep_id, sum(mri_isopen) as mri_isopen, count(mri_rep_id) as sum_rep_id from crep_mri_trades where mri_rep_id in ".$str_rept."	group by mri_rep_id";
$res_c = mysql_query($qry_c) or die(tdw_mysql_error($qry_c));
while ($row = mysql_fetch_array($res_c)) {
		if (array_key_exists($row["mri_rep_id"],$arr_combine_open)) {
			$arr_combine_open[$row["mri_rep_id"]] = $arr_combine_open[$row["mri_rep_id"]] + $row["mri_isopen"];	
		} else {
			$arr_combine_open[$row["mri_rep_id"]] = $row["mri_isopen"];	
		}
		
		if (array_key_exists($row["mri_rep_id"],$arr_combine_notes)) {
			$arr_combine_notes[$row["mri_rep_id"]] = $arr_combine_notes[$row["mri_rep_id"]] + $row["sum_rep_id"];	
		} else {
			$arr_combine_notes[$row["mri_rep_id"]] = $row["sum_rep_id"];	
		}
}

$qry_d = "select oth_rep_id, sum(oth_isopen) as oth_isopen, count(oth_rep_id) as sum_rep_id from crep_other_notes where oth_rep_id in ".$str_rept."	group by oth_rep_id";
$res_d = mysql_query($qry_d) or die(tdw_mysql_error($qry_d));
while ($row = mysql_fetch_array($res_d)) {
		if (array_key_exists($row["oth_rep_id"],$arr_combine_open)) {
			$arr_combine_open[$row["oth_rep_id"]] = $arr_combine_open[$row["oth_rep_id"]] + $row["oth_isopen"];	
		} else {
			$arr_combine_open[$row["oth_rep_id"]] = $row["oth_isopen"];	
		}
		
		if (array_key_exists($row["oth_rep_id"],$arr_combine_notes)) {
			$arr_combine_notes[$row["oth_rep_id"]] = $arr_combine_notes[$row["oth_rep_id"]] + $row["sum_rep_id"];	
		} else {
			$arr_combine_notes[$row["oth_rep_id"]] = $row["sum_rep_id"];	
		}
}

$qry_e = "select sra_rep_id, sum(sra_isopen) as sra_isopen, count(sra_rep_id) as sum_rep_id from crep_sra_approval where sra_rep_id in ".$str_rept."	group by sra_rep_id";
$res_e = mysql_query($qry_e) or die(tdw_mysql_error($qry_e));
while ($row = mysql_fetch_array($res_e)) {
		if (array_key_exists($row["sra_rep_id"],$arr_combine_open)) {
			$arr_combine_open[$row["sra_rep_id"]] = $arr_combine_open[$row["sra_rep_id"]] + $row["sra_isopen"];	
		} else {
			$arr_combine_open[$row["sra_rep_id"]] = $row["sra_isopen"];	
		}
		
		if (array_key_exists($row["sra_rep_id"],$arr_combine_notes)) {
			$arr_combine_notes[$row["sra_rep_id"]] = $arr_combine_notes[$row["sra_rep_id"]] + $row["sum_rep_id"];	
		} else {
			$arr_combine_notes[$row["sra_rep_id"]] = $row["sum_rep_id"];	
		}
}

//show_array($arr_combine_open);
//show_array($arr_combine_notes);
//exit;

?>
<script language="JavaScript" src="includes/wz/wz_tooltip.js" type="text/javascript"></script>
<?
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
$min_date = "";
$query_min_date = "SELECT min(msrv_trade_date) as min_date FROM mgmt_reports_creation";
$result_min_date = mysql_query($query_min_date) or die(tdw_mysql_error($query_min_date));
	while ($row_min_date = mysql_fetch_array($result_min_date)) {
	$min_date =	$row_min_date["min_date"];
}

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
$master_emls = array();
$qry_emls = "SELECT eml_trade_date,eml_is_ok
							FROM eml_research_compliance
							WHERE eml_isactive =1";
$result_emls = 	mysql_query($qry_emls) or die (tdw_mysql_error($qry_emls));
while ($row_emls = mysql_fetch_array($result_emls) ) 
{
	$master_emls[$row_emls['eml_trade_date']] = $row_emls['eml_is_ok'];	
}							
//show_array($master_emls);


$master_count_emls = array();
$qry_count_emls = "SELECT eml_trade_date,eml_count 
										FROM eml_research_counts 
										WHERE eml_type = 'Total'
										AND eml_isactive =1";
$result_count_emls = 	mysql_query($qry_count_emls) or die (tdw_mysql_error($qry_count_emls));
while ($row_count_emls = mysql_fetch_array($result_count_emls) ) 
{
		$master_count_emls[$row_count_emls['eml_trade_date']] = $row_count_emls['eml_count'];	
}							
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

if ($x or $_POST) { // form submitted //format_date_ymd_to_mdy($trade_date_to_process)
		
	//print_r($_POST);
	$sel_datefrom = $datefrom;
	$sel_dateto = $dateto;
		 
	$datefrom = format_date_mdy_to_ymd($datefrom);
	$dateto = format_date_mdy_to_ymd($dateto);

} else {

	$sel_datefrom = format_date_ymd_to_mdy(business_day_backward(strtotime(date('Y-m-d')),20));
	$sel_dateto = format_date_ymd_to_mdy(previous_business_day());

	$datefrom = business_day_backward(strtotime(date('Y-m-d')),20);
	$dateto = previous_business_day();
}

//get_count_notes
$arr_count_notes = array();
$query_count_notes = "SELECT a.auto_id, b.msrn_rep_auto_id , count( b.msrn_notes ) as count_notes
										FROM mgmt_reports_creation a, mgmt_reports_notes b
										where a.auto_id = b.msrn_rep_auto_id 
										AND msrn_isopen = 0
										AND a.msrv_trade_date between '".$datefrom."' and '".$dateto."'
										GROUP BY msrn_rep_auto_id";
$result_count_notes = mysql_query($query_count_notes) or die(tdw_mysql_error($query_count_notes));
while ($row_count_notes = mysql_fetch_array($result_count_notes)) {
$arr_count_notes[$row_count_notes["msrn_rep_auto_id"]]=	$row_count_notes["count_notes"];
}

//get_count_open Action Item Pending
$arr_count_open = array();
$query_count_open = "SELECT a.auto_id, b.msrn_rep_auto_id , count( b.msrn_notes ) as count_notes
										FROM mgmt_reports_creation a, mgmt_reports_notes b
										where a.auto_id = b.msrn_rep_auto_id
										AND b.msrn_isopen = 1
										AND a.msrv_trade_date between '".$datefrom."' and '".$dateto."'
										GROUP BY msrn_rep_auto_id";
//xdebug("query_count_open",$query_count_open);
$result_count_open = mysql_query($query_count_open) or die(tdw_mysql_error($query_count_open));
while ($row_count_open = mysql_fetch_array($result_count_open)) {
$arr_count_open[$row_count_open["msrn_rep_auto_id"]]=	$row_count_open["count_notes"];
}

//get_count_open by trade date.
$arr_count_open_td = array();
$query_count_open_td = "SELECT a.msrv_trade_date, count( b.auto_id ) as count_notes
										FROM mgmt_reports_creation a, mgmt_reports_notes b
										WHERE a.auto_id = b.msrn_rep_auto_id
										AND msrn_isopen =1
										GROUP BY a.msrv_trade_date";
//xdebug("query_count_open_td",$query_count_open_td);
$result_count_open_td = mysql_query($query_count_open_td) or die(tdw_mysql_error($query_count_open_td));
while ($row_count_open_td = mysql_fetch_array($result_count_open_td)) {
$arr_count_open_td[$row_count_open_td["msrv_trade_date"]]=	$row_count_open_td["count_notes"];
}


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
$arr_reports = array();
$arr_reports_info = array();
$arr_reports_views = array();

$qry_reports = "SELECT a.auto_id, a.msrv_rep_id, a.msrv_trade_date, a.msrv_creation_datetime, a.msrv_rep_file
								FROM mgmt_reports_creation a
								WHERE a.msrv_trade_date between '".$datefrom."' and '".$dateto."'";
$result_reports = mysql_query($qry_reports) or die(tdw_mysql_error($qry_reports));
while ( $row = mysql_fetch_array($result_reports) ) 
{
	$arr_reports_info[$row["auto_id"]] = $row["msrv_rep_file"];
	$arr_reports[format_date_ymd_to_mdy($row["msrv_trade_date"])][$row["msrv_rep_id"]] = $row["auto_id"]; //$arr_reports_for_date;	
}

//show_array($arr_reports);
//show_array($arr_reports_info);


$qry_reports = "SELECT a.auto_id, a.msrv_rep_id, a.msrv_trade_date, a.msrv_creation_datetime, a.msrv_rep_file,
											 DATE_FORMAT( b.msrv_view_datetime, '%c/%e %l:%i%p' ) as view_time,
											 concat( substr(c.Firstname, 1, 1), '. ', c.Lastname ) as Fullname,
											 c.ID 
								FROM mgmt_reports_creation a, mgmt_sup_report_views b, Users c
								WHERE a.auto_id = b.msrv_rep_auto_id
								AND b.msrv_user_id = c.ID
								and a.msrv_trade_date between '".$datefrom."' and '".$dateto."'";
$result_reports = mysql_query($qry_reports) or die(tdw_mysql_error($qry_reports));
while ( $row = mysql_fetch_array($result_reports) ) 
{
		$arr_reports_views[$row["auto_id"]][] = $row["Fullname"]."^".$row["view_time"];
}

//show_array($arr_reports_views);
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//===========================================================================================================


//xdebug("min_date",$min_date);
// Get the dates (Business Days in the past 30 days)
//for ($i=1; $i<10; $i++) {
//$arr_report_dates[$i] = previous_bizday($arr_report_dates[$i-1]);
//}

$arr_report_dates = array();
$arr_report_dates[0] = $dateto;
for ($i=1; $i<220; $i++) {
	if (strtotime($arr_report_dates[$i-1]) > strtotime($datefrom)) {
		$arr_report_dates[$i] = previous_bizday($arr_report_dates[$i-1]);
	} else {
	  //nothing
	}
}
//print_r($arr_report_dates);


?>

<? tsp(100, "Compliance Review Log"); ?>

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
							<td width="230">Compliance Activity (v1)</td>
							<td width="230">Compliance Activity (v2) <a class="iltr" href="rep_viewed_mgmt_print.php?fd=<?=$datefrom?>&td=<?=$dateto?>&rcode=DCARV2" target="_blank"><img src="images/printer_small.png" border="0" onmouseover="Tip('<img src=images/printer_small.png><br>This link allows you to print all notes for the selected date range. <br>Output is in PDF Format.', WIDTH, 200, PADDING, 6, BGCOLOR, '#cbccff', TITLE, 'Print Report Notes')"/></a></td>
							<td width="80">MRI LkBk</td>
							<td width="200">S & R Approval Review</td>
							<td>&nbsp;</td>
						</tr>
						
						<? 
						$count_row = 0;
						foreach ( $arr_report_dates as $key => $report_date ) {
											if ($count_row%2 == 0) {
													$rowclass = " class=\"trlight\"";
											} else {
													$rowclass = " class=\"trdark\"";
											}
											
					  						//$total_action_pending_for_date = $arr_count_open['DCAR'] + $arr_count_open['DCARV2'];
												
											if (strtotime($report_date) >= strtotime($min_date)) {
												
														/*if ($arr_count_open_td[$report_date] == '') {
															$str_attention = '';
														} else {
															$str_attention = '&nbsp;&nbsp;&nbsp;<strong>(<BLINK>'.$arr_count_open_td[$report_date].' AP</BLINK>)</strong>';
														}*/

														if ($arr_combine_open[$arr_reports[format_date_ymd_to_mdy($report_date)]['DCARV2']] == 0 || $arr_combine_open[$arr_reports[format_date_ymd_to_mdy($report_date)]['DCARV2']] == "") {  
															$str_attention = '';
														} else {
															$str_attention = '&nbsp;&nbsp;&nbsp;<strong>(<BLINK>'.$arr_combine_open[$arr_reports[format_date_ymd_to_mdy($report_date)]['DCARV2']] .' AP</BLINK>)</strong>';
														}

												?>						
												
												<tr <?=$rowclass?>>
													<td>&nbsp;&nbsp;<?=format_date_ymd_to_mdy($report_date)."<br>".$str_attention?></td>
													<td valign="top">
														<?
														//Table Cell for DCAR		
														//See if there is Report, if yes, show.
														if ($arr_reports[format_date_ymd_to_mdy($report_date)]['DCAR'] != '') 
														{
																$report_id = $arr_reports[format_date_ymd_to_mdy($report_date)]['DCAR'];
																if (checkpriv($privileges,"dcar") == 1) 
																{
																				$url = $_site_url."repsvr.php?rep=DCAR&src=".rand(10000000,99999999).str_replace('-','N',$report_date).str_pad($user_id,10,'Q',1).md5("dummy");
														?>
																				<a href="<?=$url?>" target="_blank"><img src="images/b_arrow.gif" border="0" /> Report</a>&nbsp;&nbsp;&nbsp;
																				<a href="javascript:CreateWnd('repsvr_notes.php?rep_auto_id=<?=$report_id?>&user_id=<?=$user_id?>', 600, 600, null);"><img src="images/b_arrow.gif" border="0" /> Notes</a>
														<?
																				if ($arr_count_notes[$report_id] == '') {
																				echo '<br>';
																				} elseif ($arr_count_notes[$report_id] == 1) {
																				echo '<br><strong>(1 Note)</strong>';
																				} else {
																				echo '<br><strong>('.$arr_count_notes[$report_id].' Notes)</strong>';
																				}
			
																				if ($arr_count_open[$report_id] == '') {
																				echo '';
																				} else {
																				echo '&nbsp;&nbsp;<strong>('.$arr_count_open[$report_id].' AP)</strong>';
																				}																	
																
																				if (count($arr_reports_views[$report_id])>0) {
																				echo '<table>';
																				foreach($arr_reports_views[$report_id] as $k=>$v)
																				{
																					$arr_name_time = explode("^",$v);
																					?>
																					<tr <?=$rowclass?>>
																						<td><?=$arr_name_time[0]?></td><td>(<?=$arr_name_time[1]?>)</td>
																					</tr>
																					<?
																				}
																				echo '</table>';
																				}

																}
														}
													?>
													</td>
													<td valign="top">
												  <?
														//Table Cell for DCARV2		
														//See if there is Report, if yes, show.
														if ($arr_reports[format_date_ymd_to_mdy($report_date)]['DCARV2'] != '') 
														{
																$report_id = $arr_reports[format_date_ymd_to_mdy($report_date)]['DCARV2'];
																if (checkpriv($privileges,"dcarv2") == 1) 
																{
																	$url = $_site_url."repsvr.php?rep=DCARV2&src=".rand(10000000,99999999).str_replace('-','N',$report_date).str_pad($user_id,10,'Q',1).md5("dummy");
														?>
																	<a href="<?=$url?>" target="_blank"><img src="images/b_arrow.gif" border="0" /> Report</a>&nbsp;&nbsp;&nbsp;
																	<a href="javascript:showPopWin('rep_compliance_notes.php?rep_auto_id=<?=$report_id?>&user_id=<?=$user_id?>', 800, 600, null);"><img src="images/b_arrow.gif" border="0" /> Notes</a>
														<?
																				/*if ($arr_count_notes[$report_id] == '') {
																				echo '<br>';
																				} elseif ($arr_count_notes[$report_id] == 1) {
																				echo '<br><strong>(1 Note)</strong>';
																				} else {
																				echo '<br><strong>('.$arr_count_notes[$report_id].' Notes)</strong>';
																				}*/
			
														           if ($arr_combine_notes[$report_id] == 0 || $arr_combine_notes[$report_id] == "") {  
																				echo '';
																				} elseif ($arr_combine_notes[$report_id] == 1) {
																				echo ' &nbsp;&nbsp;<strong>(1 Note)</strong>';
																				} else {
																				echo ' &nbsp;&nbsp;<strong>('.$arr_combine_notes[$report_id].' Notes)</strong>';
																				}

																				if ($arr_count_open[$report_id] == '') {
																				echo '';
																				} else {
																				echo '&nbsp;&nbsp;<strong>('.$arr_count_open[$report_id].' AP)</strong>';
																				}
																				
																				if (count($arr_reports_views[$report_id])>0) {
																				echo '<table>';
																				foreach($arr_reports_views[$report_id] as $k=>$v)
																				{
																					$arr_name_time = explode("^",$v);
																					?>
																					<tr <?=$rowclass?>>
																						<td><?=$arr_name_time[0]?></td><td>(<?=$arr_name_time[1]?>)</td>
																					</tr>
																					<?
																				}
																				echo '</table>';
																				}

																}
														}
													?>
													</td>
													<td valign="top"><br />&nbsp;&nbsp;&nbsp;<a href="data/compliance/lookback_<?=$report_date?>.pdf" target="_blank"><img src="images/pdf.png" border="0" /></a></td>
													<td>
													<?
													if ($master_emls[$report_date] == 1) {
													 echo '<img src="images/g_arrow.gif" border="0" /> <a href="serve/emlfile/?vd='.$report_date.'" target="_blank">Email Count = '.$master_count_emls[$report_date].'</a>';
													} elseif ($master_emls[$report_date] == 0 and $master_emls[$report_date] != '') {
													 echo '<img src="images/r_arrow.gif" border="0" /> <a href="serve/emlfile/?vd='.$report_date.'" target="_blank">Email Count = '.$master_count_emls[$report_date].'</a>';
													} else {  
													  echo "&nbsp;";
													}
													?>
													</td>
													<td>&nbsp;</td>
												</tr>
												<?
												}
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


  include('inc_footer.php');
?>
	<DIV ID="divfrom" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
	<DIV ID="divto" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>			