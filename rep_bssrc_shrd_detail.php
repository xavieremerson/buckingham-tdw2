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
$srep_user_id = $rep_id;
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
    parent.document.getElementById(id).style.height=this.document.body.scrollHeight+"px";
   }
}
</script>

<script language="JavaScript" type="text/JavaScript">
//pass variables as separate args
function sh_level3s(divid,rrnum,tdate) { //show/hide level2 shared rep
  var trid;
	trid = 'if_'+ divid; 
	if (document.getElementById) { // DOM3 = IE5, NS6 Generally this is what it is

		if (document.getElementById(trid).style.getAttribute("visibility") == "" || document.getElementById(trid).style.getAttribute("visibility") == "hidden" ) {
			document.getElementById(trid).style.visibility = 'visible'; 
			document.getElementById(trid).style.display = 'block'; 
			document.getElementById('img'+ divid).src = 'images/lf_v1/collapse.png';
			if (document.getElementById(trid).src == "") {
			document.getElementById(trid).src='http://192.168.20.63/tdw/rep_bssrc_if_srep.php?ifid='+trid+'&rep_num='+rrnum+'&tdate='+tdate;
			}
			//alert(document.getElementById(trid).src)
		} else {
			document.getElementById(trid).style.visibility = 'hidden'; 
			document.getElementById(trid).style.display = 'none'; 
			document.getElementById('img'+ divid).src = 'images/lf_v1/expand.png';
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

<link href="includes/styles.css" rel="stylesheet" type="text/css">
<!--onload="autofitIframe('ca_trades')"--> 
<body onLoad="autofitIframe('<?=$ifid?>')">

<?
														//get shared rep data (sls_sales_reps)
														//fields are  srep_user_id  srep_rrnum  srep_percent
														$qry_get_shared_reps = "SELECT
																											srep_user_id,srep_rrnum,srep_percent
																											from sls_sales_reps
																										WHERE srep_user_id = '".$srep_user_id."'
																										AND srep_isactive = 1 
																										ORDER BY srep_rrnum";
														$result_get_shared_reps = mysql_query($qry_get_shared_reps) or die (tdw_mysql_error($qry_get_shared_reps));
														while($row_get_shared_reps = mysql_fetch_array($result_get_shared_reps))
														{
															$mk_sid = md5(rand(1000000000,9999999999));
															//for trades for shared rep, do a reverse lookup in the users table to get the id and then the shared reps
															$srep_to_process = $row_get_shared_reps["srep_rrnum"];
																	
															//get data for primary rep
															$query_level_0_rep_date = "SELECT max(comm_trade_date) as comm_trade_date
																												FROM mry_comm_rr_level_0
																												WHERE comm_rr = '".$srep_to_process."'
																												AND comm_trade_date <= '".$trade_date_to_process."'";
															//xdebug("query_level_0_rep_date",$query_level_0_rep_date);																											
															$result_level_0_rep_date = mysql_query($query_level_0_rep_date) or die(tdw_mysql_error($query_level_0_rep_date));
															while($row_level_0_rep_date = mysql_fetch_array($result_level_0_rep_date))
																{
																	$rep_date_val = $row_level_0_rep_date["comm_trade_date"];
																}
																
															//get data from rep_coom_level_0
															//fields are comm_rr  comm_trade_date  comm_total  comm_mtd  comm_qtd  comm_ytd 
															if ($rep_date_val == $trade_date_to_process) { //data available for trade_date_to_process
																	$query_level_0 = "SELECT * 
																										FROM mry_comm_rr_level_0
																										WHERE comm_rr = '".$srep_to_process."'
																										AND comm_trade_date = '".$rep_date_val."'";
																	//xdebug("query_level_0",$query_level_0);
																	$result_level_0 = mysql_query($query_level_0) or die(mysql_error());
																	while($row_level_0 = mysql_fetch_array($result_level_0)) 
																	{
																		$show_rr = $row_level_0["comm_rr"];
																		$show_previous_day_comm = number_format($row_level_0["comm_total"],2,'.',",");
																		$show_mtd = number_format($row_level_0["comm_mtd"],2,'.',",");
																		$show_qtd = number_format($row_level_0["comm_qtd"],2,'.',",");
																		$show_ytd = number_format($row_level_0["comm_ytd"],2,'.',",");
																		
																		
																		
																	}
	
																} elseif ($rep_date_val != $trade_date_to_process AND $rep_date_val != '') { //data not available for trade_date_to_process
																		$query_level_0e = "SELECT * 
																											FROM mry_comm_rr_level_0
																											WHERE comm_rr = '".$srep_to_process."'
																											AND comm_trade_date = '".$rep_date_val."'";
																		//xdebug("query_level_0e",$query_level_0e);
																		$result_level_0e = mysql_query($query_level_0e) or die(mysql_error());
																		while($row_level_0e = mysql_fetch_array($result_level_0e)) 
																		{
																			$show_rr = $row_level_0e["comm_rr"];
																			$show_previous_day_comm = '<a class="display_zero">'."0.00"."</a>";
																			
																			$running_total_comm = $running_total_comm + 0;
																			
																			$is_same_year = samebrokyear($rep_date_val,$trade_date_to_process);
																			$is_same_month = samebrokmonth($rep_date_val,$trade_date_to_process);
																			$is_same_qtr = samebrokqtr($rep_date_val,$trade_date_to_process);
																
																			if ($is_same_month == 1) {
																							$running_total_mtd = $running_total_mtd + $row_level_0e["comm_mtd"]/2;
																							$show_mtd = number_format($row_level_0e["comm_mtd"],2,'.',",");
																			} else {
																							$running_total_mtd = $running_total_mtd;
																							$show_mtd = '<a class="display_zero">'."0.00"."</a>";
																			}
																			
																			if ($is_same_qtr == 1) {
																							$running_total_qtd = $running_total_qtd + $row_level_0e["comm_qtd"]/2;
																							$show_qtd = number_format($row_level_0e["comm_qtd"],2,'.',",");
																			} else {
																							$running_total_qtd = $running_total_qtd;
																							$show_qtd = '<a class="display_zero">'."0.00"."</a>";
																			}
																			
																			if ($is_same_year == 1) {
																							$running_total_ytd = $running_total_ytd + $row_level_0e["comm_ytd"]/2;
																							$show_ytd = number_format($row_level_0e["comm_ytd"],2,'.',",");
																			} else {
																							$running_total_ytd = $running_total_ytd;
																							$show_ytd = '<a class="display_zero">'."0.00"."</a>";
																			}
															
																		}
																	} else { //no data exists for this client
	
																				$zero_string = '<a class="display_zero">'."0.00"."</a>";
																				$show_rr = $rep_to_process;
																				$show_previous_day_comm = $zero_string;
																				$show_mtd = $zero_string;
																				$show_qtd = $zero_string;
																				$show_ytd = $zero_string;
																	}
																	?>
																	<table width="100%">
																		<tr class="trlight" onDblClick="javascript:sh_level3s('<?=$mk_sid?>','<?=$srep_to_process?>','<?=$trade_date_to_process?>')"> 
																			<td width="340" valign="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
																			<a href="javascript:sh_level3s('<?=$mk_sid?>','<?=$srep_to_process?>','<?=$trade_date_to_process?>')">
																			<img id="img<?=$mk_sid?>" src="images/lf_v1/expand.png" border="0"></a> 
																			<?=$row_get_reps["rep_name"]?> (Shrd Rep: <?=$srep_to_process?>)</td>
																			<td width="100" align="right"><?=$show_previous_day_comm?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
																			<td width="100" align="right"><?=$show_mtd?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
																			<td width="100" align="right">&nbsp;&nbsp;&nbsp;&nbsp;</td>
																			<td width="100" align="right"><?=$show_qtd?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
																			<td width="100" align="right">&nbsp;&nbsp;&nbsp;&nbsp;</td>
																			<td width="100" align="right"><?=$show_ytd?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
																			<td width="100" align="right">&nbsp;</td>
																			<td>&nbsp;</td>
																		</tr>
																	</table>
																	<iframe name="if_<?=$mk_sid?>" src="" width="100%" marginwidth="0" marginheight="0" scrolling="no" frameborder="0" style="visibility:hidden; display=none"></iframe>
														  <?
															} // end while looking for shared reps													
?>
</body>