<?
//Since this is a IFRAME requested page, all inputs to this page should be passed with the param string
//Also, all the relevant includes should be a part of this page including css, etc
include('includes/global.php');
include('includes/dbconnect.php');
include('includes/functions.php');
?>
<script language='JavaScript'>
function showhidedetail(divid,ifid) {
	if (document.getElementById) { // DOM3 = IE5, NS6 Generally this is what it is

		if (document.getElementById(divid).style.getAttribute("visibility") == "" || document.getElementById(divid).style.getAttribute("visibility") == "hidden" ) {
		document.getElementById(divid).style.visibility = 'visible'; 
		document.getElementById(divid).style.display = 'block'; 
		document.getElementById('img'+divid).src = 'images/lf_v1/collapse.png';
		autofitIframe(ifid);
		} else {
		document.getElementById(divid).style.visibility = 'hidden'; 
		document.getElementById(divid).style.display = 'none'; 
		document.getElementById('img'+divid).src = 'images/lf_v1/expand.png';
		autofitIframe(ifid);
		}		

	} 
	else { 
		if (document.layers) { // Netscape 4 
			alert("Netscape 4");
			document.AELT.visibility = 'visible'; 
		} 
		else { // IE 4 
			alert("IE 4");
			document.all.AELT.style.visibility = 'visible'; 
		}
	} 
} 
</script>
<?
//show_array($_GET);
if ($_GET) { // form submitted //format_date_ymd_to_mdy($trade_date_to_process)
//assign the variables here
$ifid = $ifid;
$rep_to_process = $rep_num;
$trade_date_to_process = $tdate;
} else {
$ifid = "test";
$rep_to_process = "045";
$trade_date_to_process = previous_business_day();
}
?>

<script language="JavaScript" type="text/JavaScript">
function autofitIframe(id){
	if (!window.opera && !document.mimeType && document.all && document.getElementById){
      parent.document.getElementById(id).style.height=this.document.body.offsetHeight+"px";
    }
    else if(document.getElementById) {
    parent.document.getElementById(id).style.height=this.document.body.scrollHeight+"px"
   }
}
</script>
<link href="includes/styles.css" rel="stylesheet" type="text/css">
<!--onload="autofitIframe('ca_trades')"--> 
<body onload="autofitIframe('<?=$ifid?>')">


<!-- START TABLE 4 -->
<!-- class="tablewithdata" -->
		<table width="100%"  border="0" cellspacing="1" cellpadding="0">
			<?
			//set the running totals for this section
				$running_total_comm = 0;
				$running_total_mtd = 0;
				$running_total_qtd = 0;
				$running_total_ytd = 0;

			
			//get all advisors for the selected rr as of the trade date selected (do not show advisors of the future)
			$query_level_a_adv = "SELECT DISTINCT(nadd_advisor) as comm_advisor_code
														FROM mry_nfs_nadd 
														WHERE nadd_rr_owning_rep = '".$rep_to_process."'
														AND nadd_branch = 'PDY'
														AND nadd_advisor not like '&%' 
														ORDER BY nadd_advisor"; 
														
			//xdebug("query_level_a_adv",$query_level_a_adv);
			//investigate SAC
			$result_level_a_adv = mysql_query($query_level_a_adv) or die(mysql_error());

			$level_a_count = 1; //for css style

			while($row_level_a_adv = mysql_fetch_array($result_level_a_adv))
			{
				
				$comm_advisor_code = $row_level_a_adv["comm_advisor_code"];
				if ($arr_clients[$comm_advisor_code]){
					$comm_advisor_name = $arr_clients[$comm_advisor_code];
				} else {
					$comm_advisor_name = $comm_advisor_code;
				}
				//xdebug("comm_advisor_code",$comm_advisor_code);
				//get data for advisor
				$query_level_a_adv_date = "SELECT max(comm_trade_date) as comm_trade_date
																	FROM mry_comm_rr_level_a
																	WHERE comm_rr = '".$rep_to_process."'
																	AND comm_trade_date <= '".$trade_date_to_process."'
																	AND comm_advisor_code = '".$comm_advisor_code."'";
				//xdebug("query_level_a_adv_date",$query_level_a_adv_date);																											
				$result_level_a_adv_date = mysql_query($query_level_a_adv_date) or die(tdw_mysql_error($query_level_a_adv_date));

						while($row_level_a_adv_date = mysql_fetch_array($result_level_a_adv_date))
						{
							$adv_date_val = $row_level_a_adv_date["comm_trade_date"];
							//xdebug("adv_date_val",$adv_date_val);																											
						}
						//get data from rep_coom_level_a
						//fields are comm_rr  comm_trade_date  comm_advisor_code  comm_advisor_name  comm_total  comm_mtd  comm_qtd  comm_ytd 
						if ($adv_date_val == $trade_date_to_process) { //data available for trade_date_to_process
								$query_level_a = "SELECT * 
																	FROM mry_comm_rr_level_a
																	WHERE comm_rr = '".$rep_to_process."'
																	AND comm_advisor_code = '".$comm_advisor_code."'
																	AND comm_trade_date = '".$adv_date_val."'";
								//xdebug("query_level_a",$query_level_a);
								$result_level_a = mysql_query($query_level_a) or die(mysql_error());
								while($row_level_a = mysql_fetch_array($result_level_a)) 
								{
									if ($row_level_a["comm_advisor_name"] == '') {
										$show_advisor_name = $comm_advisor_code;
									} else {
										$show_advisor_name = $row_level_a["comm_advisor_name"];
									}
									$show_rr = $row_level_a["comm_rr"];
									$show_previous_day_comm = number_format($row_level_a["comm_total"],2,'.',",");
									$show_mtd = number_format($row_level_a["comm_mtd"],2,'.',",");
									$show_qtd = number_format($row_level_a["comm_qtd"],2,'.',",");
									$show_ytd = number_format($row_level_a["comm_ytd"],2,'.',",");
									
									$running_total_comm = $running_total_comm + $row_level_a["comm_total"];
									
									$running_total_mtd = $running_total_mtd + $row_level_a["comm_mtd"];
									$running_total_qtd = $running_total_qtd + $row_level_a["comm_qtd"];
									$running_total_ytd = $running_total_ytd + $row_level_a["comm_ytd"];
									
								}

							} elseif ($adv_date_val != $trade_date_to_process AND $adv_date_val != '') { //data not available for trade_date_to_process
									$query_level_ae = "SELECT * 
																		FROM mry_comm_rr_level_a
																		WHERE comm_rr = '".$rep_to_process."'
																		AND comm_advisor_code = '".$comm_advisor_code."'
																		AND comm_trade_date = '".$adv_date_val."'";
									//xdebug("query_level_ae",$query_level_ae);
									$result_level_ae = mysql_query($query_level_ae) or die(mysql_error());
									while($row_level_ae = mysql_fetch_array($result_level_ae)) 
									{
										if ($row_level_ae["comm_advisor_name"] == '') {
											$show_advisor_name = $comm_advisor_code;
										} else {
											$show_advisor_name = $row_level_ae["comm_advisor_name"];
										}
										$show_rr = $row_level_ae["comm_rr"];
										$show_previous_day_comm = '<a class="display_zero">'."0.00"."</a>";
										
										//$running_total_comm = $running_total_comm + 0;
										
										$is_same_year = sameyear($adv_date_val,$trade_date_to_process);
										$is_same_month = samemonth($adv_date_val,$trade_date_to_process);
										$is_same_qtr = sameqtr($adv_date_val,$trade_date_to_process);
										//xdebug("adv_date_val",$adv_date_val);
										//xdebug("trade_date_to_process",$trade_date_to_process);
										//xdebug("is_same_year",$is_same_year);
										//xdebug("is_same_month",$is_same_month);
										//xdebug("is_same_qtr",$is_same_qtr);
							
										if ($is_same_month == 1) {
														$running_total_mtd = $running_total_mtd + $row_level_ae["comm_mtd"];
														$show_mtd = number_format($row_level_ae["comm_mtd"],2,'.',",");
										} else {
														//$running_total_mtd = $running_total_mtd;
														$show_mtd = '<a class="display_zero">'."0.00"."</a>";
										}
										
										if ($is_same_qtr == 1) {
														$running_total_qtd = $running_total_qtd + $row_level_ae["comm_qtd"];
														$show_qtd = number_format($row_level_ae["comm_qtd"],2,'.',",");
										} else {
														//$running_total_qtd = $running_total_qtd;
														$show_qtd = '<a class="display_zero">'."0.00"."</a>";
										}
										
										if ($is_same_year == 1) {
														$running_total_ytd = $running_total_ytd + $row_level_ae["comm_ytd"];
														$show_ytd = number_format($row_level_ae["comm_ytd"],2,'.',",");
										} else {
														//$running_total_ytd = $running_total_ytd;
														$show_ytd = '<a class="display_zero">'."0.00"."</a>";
														$do_not_show = 1;
										}
						
									}
								} else { //no data exists for this client

											$show_advisor_name = $comm_advisor_name;
											$zero_string = '<a class="display_zero">'."0.00"."</a>";
											$show_rr = $rep_to_process;
											$show_previous_day_comm = $zero_string;
											$show_mtd = $zero_string;
											$show_qtd = $zero_string;
											$show_ytd = $zero_string;
											$do_not_show = 1;
								}

								//}
			
				if ($level_a_count % 2) { 
						$class_row = "trdark";
				} else { 
						$class_row = "trlight"; 
				} 
			
							?>
								<tr class="<?=$class_row?>" onDblClick="javascript:showhidedetail('<?=$level_a_count?>','<?=$ifid?>')"> 
									<td width="344" valign="middle">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:showhidedetail('<?=$level_a_count?>','<?=$ifid?>')"><img id="img<?=$level_a_count?>" src="images/lf_v1/expand.png" border="0"></a> 
									<?=$show_advisor_name?></td>
									<td width="102" align="right"><?=$show_previous_day_comm?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
									<td width="104" align="right"><?=$show_mtd?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
									<td width="104">&nbsp;</td>
									<td width="102" align="right"><?=$show_qtd?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
									<td width="104">&nbsp;</td>
									<td width="104" align="right"><?=$show_ytd?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td align="right">&nbsp;</td>
								</tr>
								<tr class="trlight" id="<?=$level_a_count?>" style="display=none; visibility=hidden"> 
									<td colspan="9"> 
									<?
									$process_advisor_code_subacct = $comm_advisor_code;
									include('rep_bssrc_inc_subacct.php');
									?> 
									</td>
								</tr>
							<?
						$level_a_count = $level_a_count + 1;															
				}
			?>
		</table>
<!-- END TABLE 4 -->
</body>
