<?
//Since this is a IFRAME requested page, all inputs to this page should be passed with the param string
//Also, all the relevant includes should be a part of this page including css, etc
include('includes/global.php');
include('includes/dbconnect.php');
include('includes/functions.php');
?>
<?
//*********************************************************************************************
//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^ 

		////
		// Create percentage with appropriate format
		function mkpercent($a, $b) {
			if ($a == 0) { return 0; }
		  elseif ($b == 0) { return '---'; }
			else { return number_format(($a*100/$b),0,'',','); }		
		}

    ////
		// Get date in previous year (input and output format: yyyy-mm-dd)
		function get_date_previous_year($dateval) {
		$arr_date = explode("-",$dateval);
		$retval = $arr_date[0]-1 . "-". $arr_date[1] . "-". $arr_date[2];
		return $retval;
		}
		
		////
		// Get data in previous year (input and output format: yyyy-mm-dd)
		function get_previous_yr_data($clntval) {
		global $arr_prev_year;
			 if ($arr_prev_year[$clntval] == "") {
					$pyc = "";
					return $pyc;
			 } else {
					$pyc = $arr_prev_year[$clntval];
					return $pyc;
			 }
		 }	
		
		$previous_year_date = get_date_previous_year($trade_date_to_process);
		//Get all data from table into an array
		$qry_prev_year = "SELECT yrt_advisor_code, yrt_commission 
											FROM yrt_yearly_total_lookup
											WHERE yrt_rr  = '".$rep_to_process."'
											AND yrt_year = EXTRACT(YEAR FROM '".$previous_year_date."')
											GROUP BY yrt_advisor_code
											ORDER BY yrt_advisor_code";
		//xdebug('qry_prev_year',$qry_prev_year);
		$result_prev_year = mysql_query($qry_prev_year) or die (tdw_mysql_error($qry_prev_year));
		$arr_prev_year = array();
		while ( $row_prev_year = mysql_fetch_array($result_prev_year) ) 
		{
			$arr_prev_year[$row_prev_year["yrt_advisor_code"]] = $row_prev_year["yrt_commission"];
		}
		
		//print_r($arr_prev_year);

//*********************************************************************************************
//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
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



				<!-- START TABLE 3 -->
										<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
											<tr>
												<td valign="top">		
                        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
													
													<?
													///\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\
													//Get an array of relevant data for advisors by way of check payments.
													//get all advisors for the selected rr as of the trade date selected (do not show advisors of the future)
													
													$arr_check_data_shrd = array();
													
													$query_adv_checks_shrd = "SELECT DISTINCT (nadd_advisor) AS advisor_code, b.srep_rrnum AS shared_rep_num
																														FROM mry_nfs_nadd a, sls_sales_reps b
																														WHERE a.nadd_rr_owning_rep = b.srep_rrnum
																														AND b.srep_user_id = '".$rep_id."'
																														AND nadd_advisor != 'XXXX'
																														AND nadd_advisor not like '&%'
																														AND b.srep_isactive = 1 
																														GROUP BY a.nadd_advisor, b.srep_rrnum
																														ORDER BY a.nadd_advisor";
																								
													$result_adv_checks_shrd = mysql_query($query_adv_checks_shrd) or die(tdw_mysql_error($query_adv_checks_shrd));
													while($row_adv_checks_shrd = mysql_fetch_array($result_adv_checks_shrd))
															{
																$process_advisor_shrd = $row_adv_checks_shrd["advisor_code"];
															
																$query_get_adv_checks_shrd = "SELECT max(chk_check_date) as chk_check_date 
																													FROM chk_totals_level_a 
																													WHERE chk_advisor_code = '".$process_advisor_shrd."'";
																$result_get_adv_checks_shrd = mysql_query($query_get_adv_checks_shrd) or die(tdw_mysql_error($query_get_adv_checks_shrd));
		
																		while($row_get_adv_checks_shrd = mysql_fetch_array($result_get_adv_checks_shrd))
																		{
																			$adv_date_val_shrd = $row_get_adv_checks_shrd["chk_check_date"];
																		}
																		
																//get data from chk_totals_level_a
																//fields are chk_check_date  chk_advisor_code  chk_advisor_name  chk_total  chk_mtd  chk_qtd  chk_ytd  chk_isactive
																if ($adv_date_val_shrd == $trade_date_to_process) { //data available for trade_date_to_process
																		$query_level_a_shrd = "SELECT * 
																											FROM chk_totals_level_a
																											WHERE chk_check_date = '".$adv_date_val_shrd."'
																											AND chk_advisor_code = '".$process_advisor_shrd."'";
																		$result_level_a_shrd = mysql_query($query_level_a_shrd) or die(mysql_error());
																		while($row_level_a_shrd = mysql_fetch_array($result_level_a_shrd)) 
																		{
																			$show_check_mtd_shrd = $row_level_a_shrd["chk_mtd"];
																			$show_check_qtd_shrd = $row_level_a_shrd["chk_qtd"];
																			$show_check_ytd_shrd = $row_level_a_shrd["chk_ytd"];
																			$str_adv_data_shrd = $process_advisor_shrd."#".$show_check_mtd_shrd."#".$show_check_qtd_shrd."#".$show_check_ytd_shrd;
																			$arr_check_data_shrd[$process_advisor_shrd] = $str_adv_data_shrd;
																		}

																} elseif ($adv_date_val_shrd != $trade_date_to_process AND $adv_date_val_shrd != '') { //data not available for trade_date_to_process
																			$query_level_ae_shrd = "SELECT * 
																											FROM chk_totals_level_a
																											WHERE chk_check_date = '".$adv_date_val_shrd."'
																											AND chk_advisor_code = '".$process_advisor_shrd."'";
																			$result_level_ae_shrd = mysql_query($query_level_ae_shrd) or die(mysql_error());
																			while($row_level_ae_shrd = mysql_fetch_array($result_level_ae_shrd)) 
																			{
																				$is_same_year = samebrokyear($adv_date_val_shrd,$trade_date_to_process);
																				$is_same_month = samebrokmonth($adv_date_val_shrd,$trade_date_to_process);
																				$is_same_qtr = samebrokqtr($adv_date_val_shrd,$trade_date_to_process);
																				if ($is_same_month == 1) {
																								$show_check_mtd_shrd = $row_level_ae_shrd["chk_mtd"];
																				} else {
																								$show_check_mtd_shrd = 0;
																				}
																				
																				if ($is_same_qtr == 1) {
																								$show_check_qtd_shrd = $row_level_ae_shrd["chk_qtd"];
																				} else {
																								$show_check_qtd_shrd = 0;
																				}
																				
																				if ($is_same_year == 1) {
																								$show_check_ytd_shrd = $row_level_ae_shrd["chk_ytd"];
																				} else {
																								$show_check_ytd_shrd = 0;
																				}
																				
																			$str_adv_data_shrd = $process_advisor_shrd."#".$show_check_mtd_shrd."#".$show_check_qtd_shrd."#".$show_check_ytd_shrd;
																			$arr_check_data_shrd[$process_advisor_shrd] = $str_adv_data_shrd;
																			}
																		} else { //no data exists for this client
																			$str_adv_data_shrd = $process_advisor_shrd."#0#0#0";
																			$arr_check_data_shrd[$process_advisor_shrd] = $str_adv_data_shrd;
																		}

													}
													///\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\
													//set the running totals for this section
														$running_total_shrd_comm = 0;
														$running_total_shrd_mtd = 0;
														$running_total_shrd_qtd = 0;
														$running_total_shrd_ytd = 0;

													//get all advisors for the selected rr
													//changed the query above to reflect shared rep clients based on nfs_nadd
													$query_level_a_shared_reps_adv = "SELECT DISTINCT (
																														nadd_advisor
																														) AS comm_advisor_code, b.srep_rrnum AS shared_rep_num
																														FROM mry_nfs_nadd a, sls_sales_reps b
																														WHERE a.nadd_rr_owning_rep = b.srep_rrnum
																														AND b.srep_user_id = '".$rep_id."'
																														AND b.srep_isactive = 1 
																														AND nadd_advisor != 'XXXX'
																														AND nadd_advisor not like '&%'
																														GROUP BY a.nadd_advisor, b.srep_rrnum
																														ORDER BY a.nadd_advisor";		
																																									
													$result_level_a_shared_reps_adv = mysql_query($query_level_a_shared_reps_adv) or die(mysql_error());

													$level_a_shrd_count = 1000; //for css style

													while($row_level_a_shared_reps_adv = mysql_fetch_array($result_level_a_shared_reps_adv))
													{
														$comm_advisor_shrd_code = $row_level_a_shared_reps_adv["comm_advisor_code"];
														if ($arr_clients[$comm_advisor_shrd_code]){
															$comm_advisor_shrd_name = $arr_clients[$comm_advisor_shrd_code];
														} else {
															$comm_advisor_shrd_name = $comm_advisor_shrd_code;
														}
														//also getting the shared rep number
														$comm_shared_rep_num = $row_level_a_shared_reps_adv["shared_rep_num"];
														//get data for advisor
														$query_level_a_adv_shrd_date = "SELECT max(comm_trade_date) as comm_trade_date
																														FROM mry_comm_rr_level_a
																														WHERE comm_trade_date <= '".$trade_date_to_process."'
																														AND comm_rr = '".$comm_shared_rep_num."'  
																														AND comm_advisor_code = '".$comm_advisor_shrd_code."'";
																														
														$result_level_a_adv_shrd_date = mysql_query($query_level_a_adv_shrd_date) or die(tdw_mysql_error($query_level_a_adv_shrd_date));
														while($row_level_a_adv_shrd_date = mysql_fetch_array($result_level_a_adv_shrd_date))
														{
															$adv_shrd_date_val = $row_level_a_adv_shrd_date["comm_trade_date"];
														}
														
														//get data from rep_coom_level_a
														//fields are comm_rr  comm_trade_date  comm_advisor_code  comm_advisor_name  comm_total  comm_mtd  comm_qtd  comm_ytd 
														//xdebug("adv_date_val",$adv_date_val);
														//xdebug("trade_date_to_process",$trade_date_to_process);
														if ($adv_shrd_date_val == $trade_date_to_process) { //data available for trade_date_to_process
																$query_level_a_shrd  =  "SELECT * 
																												FROM mry_comm_rr_level_a
																												WHERE comm_advisor_code = '".$comm_advisor_shrd_code."'
																												AND comm_rr = '".$comm_shared_rep_num."' 
																												AND comm_trade_date = '".$adv_shrd_date_val."'";
																//xdebug("query_level_a_shrd",$query_level_a_shrd);
																$result_level_a_shrd = mysql_query($query_level_a_shrd) or die(mysql_error());
																while($row_level_a_shrd = mysql_fetch_array($result_level_a_shrd)) 
																{

																	if ($row_level_a_shrd["comm_advisor_name"] == '') {
																	$show_shrd_advisor_name = $comm_advisor_shrd_code;
																	} else {
																	$show_shrd_advisor_name = $row_level_a_shrd["comm_advisor_name"];
																	}
																	$show_shrd_rr = $comm_shared_rep_num; //$row_level_a_shrd["comm_rr"];
																	$show_shrd_previous_day_comm = $row_level_a_shrd["comm_total"];
																	$show_shrd_mtd = $row_level_a_shrd["comm_mtd"];
																	$show_shrd_qtd = $row_level_a_shrd["comm_qtd"];
																	$show_shrd_ytd = $row_level_a_shrd["comm_ytd"];
																	
																	$running_total_shrd_comm = $running_total_shrd_comm + $row_level_a_shrd["comm_total"];
																	$running_total_shrd_mtd = $running_total_shrd_mtd + $row_level_a_shrd["comm_mtd"];
																	$running_total_shrd_qtd = $running_total_shrd_qtd + $row_level_a_shrd["comm_qtd"];
																	$running_total_shrd_ytd = $running_total_shrd_ytd + $row_level_a_shrd["comm_ytd"];
	
																	//xdebug("case 1: running_total_shrd_mtd",$running_total_shrd_mtd);

																}
														} elseif ($adv_shrd_date_val != $trade_date_to_process and $adv_shrd_date_val != '') { //data not available for trade_date_to_process
																$query_level_ae_shrd = "SELECT * 
																												FROM mry_comm_rr_level_a
																												WHERE comm_advisor_code = '".$comm_advisor_shrd_code."'
																												AND comm_rr = '".$comm_shared_rep_num."' 
																												AND comm_trade_date = '".$adv_shrd_date_val."'";
																//xdebug("query_level_ae_shrd",$query_level_ae_shrd);
																$result_level_ae_shrd = mysql_query($query_level_ae_shrd) or die(mysql_error());
																//xdebug("countval",mysql_num_rows($result_level_ae_shrd));
																while($row_level_ae_shrd = mysql_fetch_array($result_level_ae_shrd)) 
																{
																	//xdebug("comm_qtd",$row_level_ae_shrd["comm_qtd"]);
																	if ($row_level_ae_shrd["comm_advisor_name"] == '') {
																	$show_shrd_advisor_name = $comm_advisor_shrd_code;
																	} else {
																	$show_shrd_advisor_name = $row_level_ae_shrd["comm_advisor_name"];
																	}
																	$show_shrd_rr = $row_level_ae_shrd["comm_rr"];
																	$show_shrd_previous_day_comm = 0;
																	
																	//$show_shrd_qtd = number_format($row_level_ae_shrd["comm_qtd"],2,'.',",");
																	//$show_shrd_ytd = number_format($row_level_ae_shrd["comm_ytd"],2,'.',",");

																	$running_total_shrd_comm = $running_total_shrd_comm + 0;
																	
																	$is_same_year = samebrokyear($adv_shrd_date_val,$trade_date_to_process);
																	$is_same_month = samebrokmonth($adv_shrd_date_val,$trade_date_to_process);
																	$is_same_qtr = samebrokqtr($adv_shrd_date_val,$trade_date_to_process);
																	
																	if ($is_same_month == 1) { 
																					//xdebug("case 2a: row_level_ae_shrd['comm_mtd']",$row_level_ae_shrd["comm_mtd"]);
																					$show_shrd_mtd = $row_level_ae_shrd["comm_mtd"];
																					//xdebug("case 2a: show_shrd_mtd",$show_shrd_mtd);
																					$running_total_shrd_mtd = $running_total_shrd_mtd + $row_level_ae_shrd["comm_mtd"];
																	} else {
																					//xdebug("case 2a: running_total_shrd_mtd",$running_total_shrd_mtd);
																					$show_shrd_mtd = 0;
																					$running_total_shrd_mtd = $running_total_shrd_mtd;
																	}
																	//xdebug("case 2: running_total_shrd_mtd",$running_total_shrd_mtd);  
																	
																	if ($is_same_qtr == 1) {
																					$running_total_shrd_qtd = $running_total_shrd_qtd + $row_level_ae_shrd["comm_qtd"];
																					$show_shrd_qtd = $row_level_ae_shrd["comm_qtd"];
																					//xdebug("case if: running_total_shrd_qtd",$running_total_shrd_qtd);  
																	} else {
																					$running_total_shrd_qtd = $running_total_shrd_qtd;
																					$show_shrd_qtd = 0;
																					//xdebug("case else: running_total_shrd_qtd",$running_total_shrd_qtd);  
																	}
																	
																	if ($is_same_year == 1) {
																					$running_total_shrd_ytd = $running_total_shrd_ytd + $row_level_ae_shrd["comm_ytd"];
																					$show_shrd_ytd = $row_level_ae_shrd["comm_ytd"];
																	} else {
																					$running_total_shrd_ytd = $running_total_shrd_ytd;
																					$show_shrd_ytd = 0;
																	}
																
																}
														} else { //no data available yet for the clients			
														//!!!!!!!!!!!!!!!!!!!
																	//xdebug("case 3: adv_shrd_date_val",$adv_shrd_date_val);
																					$show_shrd_advisor_name = $comm_advisor_shrd_name;
																					$zero_string = 0;
																					$show_shrd_rr = $comm_shared_rep_num;
																					$show_shrd_previous_day_comm = $zero_string;
																					$show_shrd_mtd = $zero_string;
																					$show_shrd_qtd = $zero_string;
																					$show_shrd_ytd = $zero_string;
														}
													
														if ($level_a_shrd_count % 2) { 
																$class_row = "trdark";
														} else { 
																$class_row = "trlight"; 
														} 
														
															//This gets the previous year data
															$pytotal = get_previous_yr_data($comm_advisor_shrd_code);
														

															?>
																<tr class="<?=$class_row?>" onDblClick="javascript:showhidedetail(<?=$level_a_shrd_count?>)"> 
																	<td valign="middle">&nbsp;&nbsp;&nbsp;&nbsp;
																	<!--<a href="javascript:showhidedetail(<?=$level_a_shrd_count?>)"><img id="img<?=$level_a_shrd_count?>" src="images/lf_v1/expand.png" border="0"></a>--> 
																	<?=$show_shrd_advisor_name?>&nbsp;<?=$show_shrd_rr?></td>
																	<td align="right"><?=show_numbers($show_shrd_previous_day_comm)?>&nbsp;&nbsp;</td>
																	<td align="right"><?=show_numbers($show_shrd_mtd)?>&nbsp;&nbsp;</td>
																	<td align="right"><?=show_numbers($show_shrd_qtd)?>&nbsp;&nbsp;</td>
																	<td align="right"><?=show_numbers($show_shrd_ytd)?>&nbsp;&nbsp;</td>
																	<td align="right"><?=show_numbers(get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 1))?>&nbsp;&nbsp;</td>
																	<td align="right"><?=show_numbers(get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 2))?>&nbsp;&nbsp;</td>
																	<td align="right"><?=show_numbers(get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 3))?>&nbsp;&nbsp;</td>
																	<td align="right"><?=show_numbers($show_shrd_mtd + get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 1))?>&nbsp;&nbsp;</td>
																	<td align="right"><?=show_numbers($show_shrd_qtd + get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 2))?>&nbsp;&nbsp;</td>
																	<td align="right"><?=show_numbers($show_shrd_ytd + get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 3))?>&nbsp;&nbsp;</td>
																	<td align="right"><?=number_format($pytotal,0,'',',')?>&nbsp;&nbsp;</td>
																	<td align="right"><?=show_numbers(mkpercent($show_shrd_ytd + get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 3), $pytotal))?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
																	<td align="right">&nbsp;</td>
																</tr>
																<?
																$total_pbd_shrd = $total_pbd_shrd + $show_shrd_previous_day_comm;
																$total_mtd_shrd = $total_mtd_shrd + $show_shrd_mtd;
																$total_qtd_shrd = $total_qtd_shrd + $show_shrd_qtd;
																$total_ytd_shrd = $total_ytd_shrd + $show_shrd_ytd;
																$total_cmtd_shrd = $total_cmtd_shrd + get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 1); 
																$total_cqtd_shrd = $total_cqtd_shrd + get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 2); 
																$total_cytd_shrd = $total_cytd_shrd + get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 3); 
																$total_grand_mtd_shrd = $total_grand_mtd_shrd + $show_shrd_mtd + get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 1); 
																$total_grand_qtd_shrd = $total_grand_qtd_shrd + $show_shrd_qtd + get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 2); 
																$total_grand_ytd_shrd = $total_grand_ytd_shrd + $show_shrd_ytd + get_checks_data ($comm_advisor_shrd_code, $arr_check_data_shrd, 3); 
																?>
																
																<tr class="trlight" id="<?=$level_a_shrd_count?>" style="display=none; visibility=hidden"> 
																	<td colspan="16"> 
																	<?
																	$process_shared_advisor_code_subacct = $comm_advisor_shrd_code;
																	//xdebug("process_shared_advisor_code_subacct",$process_shared_advisor_code_subacct);
																	include('rep_src_inc_shared_subacct.php');
																	?> 
																	</td>
																</tr>
															<?
																$level_a_shrd_count = $level_a_shrd_count + 1;															
														}
													?>
												   <tr class="display_totals"> 
                            <td width="240" align="left">&nbsp;&nbsp;TOTALS:</td>
                            <td width="70" align="right"><?=show_numbers($total_pbd_shrd)?>&nbsp;&nbsp;</td>
                            <td width="100" align="right"><?=show_numbers($total_mtd_shrd)?>&nbsp;&nbsp;</td>
                            <td width="100" align="right"><?=show_numbers($total_qtd_shrd)?>&nbsp;&nbsp;</td>
                            <td width="100" align="right"><?=show_numbers($total_ytd_shrd)?>&nbsp;&nbsp;</td>
                            <td width="70" align="right"><?=show_numbers($total_cmtd_shrd)?>&nbsp;&nbsp;</td>
                            <td width="70" align="right"><?=show_numbers($total_cqtd_shrd)?>&nbsp;&nbsp;</td>
                            <td width="70" align="right"><?=show_numbers($total_cytd_shrd)?>&nbsp;&nbsp;</td>
 														<td width="70" align="right"><?=show_numbers($total_grand_mtd_shrd)?>&nbsp;&nbsp;</td>
                            <td width="70" align="right"><?=show_numbers($total_grand_qtd_shrd)?>&nbsp;&nbsp;</td>
                            <td width="80" align="right"><?=show_numbers($total_grand_ytd_shrd)?>&nbsp;&nbsp;</td>
                            <td width="80" align="right">&nbsp;</td>
                            <td width="50" align="right">&nbsp;</td>
                            <td>&nbsp;</td>                          
														</tr>
												</table>
									<!-- END TABLE 4 -->
								</td>
							</tr>
						</table>
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