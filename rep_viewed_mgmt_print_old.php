<style type="text/css">
<!--
.all_general {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 11px;
}
.headtext {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight:bold;
}
.datatable {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
	color: #000000;
	font-style: normal;
	border-top-color: #000000;
	border-right-color: #0000FF;
	border-bottom-color: #000000;
	border-left-color: #0000FF;
	border-style: solid;
	border-width: 1px;
	border-collapse: collapse;
}
.notetext {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #0000ff;
	font-style: normal;
}
-->
</style>
<?

include('includes/functions.php');
include('includes/global.php');
include('includes/dbconnect.php');
												
$date_start = $xstart;
$date_end   = $xend;


?>
<body onLoad="window.print();">  <!-- -->
<table width="502" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td valign="top" width="100"><img src="images/logo.gif"></td>
		<td valign="top">
		
		<?
		echo "<a class='headtext'>Compliance Review Log<br>Report Created: ".date('m/d/Y h:i a')."<!--Trade Date ".format_date_ymd_to_mdy($date_start)." to ".format_date_ymd_to_mdy($date_end)."--></b></a>";
		?>
		</td>
	</tr>
</table>

<?

//get the oldest report date.
		 $query_min_date = "SELECT min(msrv_trade_date) as min_date FROM mgmt_reports_creation";
     $result_min_date = mysql_query($query_min_date) or die(tdw_mysql_error($query_min_date));
			while ($row_min_date = mysql_fetch_array($result_min_date)) {
			$min_date =	$row_min_date["min_date"];
		 }

//xdebug("min_date",$min_date);
// Get the dates (Business Days in the past 30 days)
$arr_report_dates = array();
for ($i=1; $i<300; $i++) {
$arr_report_dates[$i] = previous_business_day($arr_report_dates[$i-1]);
}
//print_r($arr_report_dates);

		//get_count_notes
 	   $arr_count_notes = array();
		 $query_count_notes = "SELECT msrn_rep_auto_id , count( msrn_notes ) as count_notes
													FROM mgmt_reports_notes 
													where msrn_isopen = 0
													GROUP BY msrn_rep_auto_id";
     $result_count_notes = mysql_query($query_count_notes) or die(tdw_mysql_error($query_count_notes));
			while ($row_count_notes = mysql_fetch_array($result_count_notes)) {
			$arr_count_notes[$row_count_notes["msrn_rep_auto_id"]]=	$row_count_notes["count_notes"];
		 }
		 
		 //show_array($arr_count_notes);

		//get_count_open
 	   $arr_count_open = array();
		 $query_count_open = "SELECT msrn_rep_auto_id , count( msrn_notes ) as count_notes
													FROM mgmt_reports_notes 
													where msrn_isopen = 1
													GROUP BY msrn_rep_auto_id";
     $result_count_open = mysql_query($query_count_open) or die(tdw_mysql_error($query_count_open));
			while ($row_count_open = mysql_fetch_array($result_count_open)) {
			$arr_count_open[$row_count_open["msrn_rep_auto_id"]]=	$row_count_open["count_notes"];
		 }

		 //show_array($arr_count_open);

		//get_count_open by trade date.
 	   $arr_count_open_td = array();
		 $query_count_open_td = "SELECT a.msrv_trade_date, count( b.auto_id ) as count_notes
													FROM mgmt_reports_creation a, mgmt_reports_notes b
													WHERE a.auto_id = b.msrn_rep_auto_id
													AND msrn_isopen =1
													GROUP BY a.msrv_trade_date";
     $result_count_open_td = mysql_query($query_count_open_td) or die(tdw_mysql_error($query_count_open_td));
			while ($row_count_open_td = mysql_fetch_array($result_count_open_td)) {
			$arr_count_open_td[$row_count_open_td["msrv_trade_date"]]=	$row_count_open_td["count_notes"];
		 }
		 

?>
<STYLE>
<!--
blink {color: red}
-->
</STYLE>
			
					<!--TABLE 2 START-->
					<table class="datatable" width="" border="1" cellspacing="0" cellpadding="1">
						<tr class="headtext">
						  <td width="140" valign="top">Report Trade Date</td>
							<td width="220" valign="top">Daily Compliance Activity (v1)<br />Report (Reviewed)</td>
							<td width="220" valign="top">Daily Compliance Activity (v2)<br />Report (Reviewed)</td>
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
												
														if ($arr_count_open_td[$report_date] == '') {
															$str_attention = '';
														} else {
															$str_attention = '&nbsp;&nbsp;&nbsp;<strong>(<BLINK>'.$arr_count_open_td[$report_date].' AP</BLINK>)</strong>';
														}
											  
												?>						
												
												<tr>
													<td><?=format_date_ymd_to_mdy($report_date).$str_attention?></td>
													<td valign="top">
														<?
														//Table Cell for DCAR												
														$qry_reports = "SELECT auto_id, msrv_rep_id, msrv_trade_date, 
																									 msrv_creation_datetime, msrv_rep_file 
																							FROM mgmt_reports_creation
																							WHERE msrv_rep_id = 'DCAR'
																							and msrv_trade_date = '".$report_date."'";
														$result_reports = mysql_query($qry_reports) or die(tdw_mysql_error($qry_reports));
														while ( $row = mysql_fetch_array($result_reports) ) 
														{
																$report_id = $row["auto_id"];
																$rep_file = $row["msrv_rep_file"];
																$rep_trade_date = format_date_ymd_to_mdy($row["msrv_trade_date"]);
		
																$qry_viewers = "SELECT 
																								b.msrv_rep_file, 
																								DATE_FORMAT( b.msrv_view_datetime, '%c/%e/%y' ) as view_time , c.Fullname
																								FROM mgmt_sup_report_views b, Users c
																								WHERE b.msrv_rep_id = 'DCAR'
																								AND b.msrv_user_id = c.ID
																								AND b.msrv_rep_file = '".$rep_file."'
																								ORDER BY b.msrv_rep_file, b.msrv_view_datetime DESC";
																								
																								//'%c/%e/%y %l:%i %p'
																$result_viewers = mysql_query($qry_viewers) or die(mysql_error());
																echo '<table class="all_general">';
																while ( $row_v = mysql_fetch_array($result_viewers) ) 
																{
																	?>
																	<tr>
																		<td><?=$row_v["Fullname"]?></td><td>(<?=$row_v["view_time"]?>)</td>
																	</tr>
																	<?
																}
																echo '</table>';
															
																	if ($arr_count_notes[$report_id] == '') {
																	echo '';
																	} elseif ($arr_count_notes[$report_id] == 1) {
																	echo '<strong>(1 Note) </strong>';
																	} else {
																	echo '<strong>('.$arr_count_notes[$report_id].' Notes) </strong>';
																	}

																	if ($arr_count_open[$report_id] == '') {
																	echo '';
																	} else {
																	echo '<strong>('.$arr_count_open[$report_id].' AP)</strong>';
																	}																	
																
														}
													?>
													</td>
													<td valign="top">
												  <?
													//Table Cell for DCARV2											
													$qry_reports = "SELECT auto_id, msrv_rep_id, msrv_trade_date, 
																								 msrv_creation_datetime, msrv_rep_file 
																						FROM mgmt_reports_creation
																						WHERE msrv_rep_id = 'DCARV2'
																						and msrv_trade_date = '".$report_date."'";
														$result_reports = mysql_query($qry_reports) or die(tdw_mysql_error($qry_reports));
													while ( $row = mysql_fetch_array($result_reports) ) 
													{
															$report_id = $row["auto_id"];
															$rep_file = $row["msrv_rep_file"];
															$rep_trade_date = format_date_ymd_to_mdy($row["msrv_trade_date"]);
																?>
																<?
																	$qry_viewers = "SELECT 
																									b.msrv_rep_file, 
																									DATE_FORMAT( b.msrv_view_datetime, '%c/%e/%y' ) as view_time , c.Fullname
																									FROM mgmt_sup_report_views b, Users c
																									WHERE b.msrv_rep_id = 'DCARV2'
																									AND b.msrv_user_id = c.ID
																									AND b.msrv_rep_file = '".$rep_file."'
																									ORDER BY b.msrv_rep_file, b.msrv_view_datetime DESC";
															    $result_viewers = mysql_query($qry_viewers) or die(mysql_error());
																  echo '<table class="all_general">';
															while ( $row_v = mysql_fetch_array($result_viewers) ) 
															{
															?>
															<tr>
																<td><?=$row_v["Fullname"]?></td><td>(<?=$row_v["view_time"]?>)</td>
															</tr>
															<?
															}
															echo '</table>';

																	if ($arr_count_notes[$report_id] == '') {
																	echo '';
																	} elseif ($arr_count_notes[$report_id] == 1) {
																	echo '<strong>(1 Note) </strong>';
																	} else {
																	echo '<strong>('.$arr_count_notes[$report_id].' Notes) </strong>';
																	}

																	if ($arr_count_open[$report_id] == '') {
																	echo '';
																	} else {
																	echo '<strong>('.$arr_count_open[$report_id].' AP)</strong>';
																	}
													}
													?>
													</td>
												</tr>
												<?
												}
						$count_row = $count_row + 1;
						}
						?>
					</table>