<?

if ($_POST) {
	if ($sel_brok_month != "") {
	$trade_date_to_process = $sel_brok_month;
	} else {
	$trade_date_to_process = format_date_mdy_to_ymd($datefilterval);
	}
	//xdebug('trade_date_to_process',$trade_date_to_process);
	
	$arr_repinfo = explode("^",$sel_rep);
	$rep_id = $arr_repinfo[1];
	//$rep_to_process = $arr_repinfo[0];
	//$rep_name = $arr_repinfo[2];
	//xdebug('rep_id',$rep_id);

	
} else {
  $trade_date_to_process = previous_business_day();
	//$sel_rep = '091^288';
  //$rep_name = 'BRG,    (091)'; 
	//$arr_repinfo = split('\^',$sel_rep);
	//$rep_to_process = $arr_repinfo[0];
	$rep_id = ""; //$arr_repinfo[1];
	
	//xdebug('trade_date_to_process',$trade_date_to_process);
	//xdebug('rep_to_process',$rep_to_process);
	//xdebug('rep_id',$rep_id);
	//xdebug('rep_name',$rep_name);
}
//$rep_to_process = '035'; //'028';

//*********************************************************************************************
//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
	//xdebug('trade_date_to_process',$trade_date_to_process);

?>
<!--<a href="javascript:populate_div_primary('b3ffed8e8c1b98085503659fdfdb5011','041','2013-04-04')">-->
<script src="includes/prototype/prototype.js" language="javascript"></script>
<script language ="Javascript">
<!--
function populate_div(divid,clnts,tdate)
{
 	var trid;
	trid = 'div_'+ divid; 

 	var url = 'http://192.168.20.63/tdw/sales_rev_summ_ajx.php';
	var pars = 'clnts='+clnts;
  pars = pars + '&tdate='+tdate;
  var ran_number= Math.random()*5; 
  pars = pars + '&xrand=' + ran_number;
	
	//alert(url+"?"+pars);
	//return false;

	//var imgval = "img"+divid;
	//alert(imgval);
	
	/*if ($(imgval)) {
		alert("image found");
	} else {
		alert("image not found");
	}*/
	
	/*var youtubeimgsrc = $(imgval).src;
	alert(youtubeimgsrc);*/


	if ($(trid).style.getAttribute("visibility") == "" || $(trid).style.getAttribute("visibility") == "hidden" ) {
		$(trid).style.visibility = 'visible'; 
		$(trid).style.display = 'block'; 
		$('img'+ divid).src = 'images/lf_v1/collapse.png';
				new Ajax.Request
				(
					url,   
					{     
						method:'get', 
						parameters:pars,    
						onSuccess: 
							function(transport){       
								var response = transport.responseText; 
								//alert(response);
								$(trid).innerHTML=response;
								//$(trid).style.visibility = 'visible'; 
								//(trid).style.display = 'block'; 

							},     
						onFailure: 
						function(){ $(trid).innerHTML="ERROR GETTING DATA."; }
					}
				);
		//alert(document.getElementById(trid).src)
	} else {
		$(trid).style.visibility = 'hidden'; 
		$(trid).style.display = 'none'; 
		$('img'+ divid).src = 'images/lf_v1/expand.png';
	}		

}

function populate_divx(divid,clnts,tdate) {
	alert("entered function");
 	var trid;
	trid = 'div_'+ divid; 
	if (document.getElementById) { // DOM3 = IE5, NS6 Generally this is what it is

		if (document.getElementById(trid).style.getAttribute("visibility") == "" || document.getElementById(trid).style.getAttribute("visibility") == "hidden" ) {
			document.getElementById(trid).style.visibility = 'visible'; 
			document.getElementById(trid).style.display = 'block'; 
			document.getElementById('img'+ divid).src = 'images/lf_v1/collapse.png';
			if (document.getElementById(trid).innerHTML == "") {

					AjaxRequest.get(
						{
							'url':'sales_rev_summ_ajx.php?clnts='+rrid+'&tdate='+tdate
							,'onSuccess':function(req){ document.getElementById(trid).innerHTML=req.responseText; }
							,'onError':function(req){ document.getElementById(trid).innerHTML='Error receiving data.';}
						}
					);

			}
			//alert(document.getElementById(trid).src)
		} else {
			document.getElementById(trid).style.visibility = 'hidden'; 
			document.getElementById(trid).style.display = 'none'; 
			document.getElementById('img'+ divid).src = 'images/lf_v1/expand.png';
		}		
	} 

}
-->
</script>
<?
tsp(100,"SALES REVENUE SUMMARY : As of ".format_date_ymd_to_mdy($trade_date_to_process)); //." for ".$rep_name
?>
				<!-- START TABLE 3 -->
					<table width="100%" cellpadding="1", cellspacing="0"> <!-- bgcolor="#CCCCCC"-->
						<tr>
							<td valign="top"> 
								<!-- START TABLE 4 -->
								<!-- class="tablewithdata" -->
												<table width="100%" bgcolor="#FFFFFF">
													<tr>
														<td>
														<table width="100%" cellpadding="0" cellspacing="0">
															<tr>
																<td width="20">&nbsp;</td>
																<td width="200">
																<form name="selectionfilter" id="idselectionfilter" action="" method="post">
																<?
																//get reps from query  on table mry_comm_rr_trades and join on users
																$qry_get_reps = "SELECT
																									a.ID, a.rr_num, concat(a.Firstname, ' ', a.Lastname) as rep_name, a.rr_num as trad_rr, trim(Initials) as Initials 
																									from users a
																								WHERE a.rr_num like '0%'
																								AND a.Role > 2
																								AND a.Role < 5
																								AND a.user_isactive = 1
																								ORDER BY a.Lastname";
																$result_get_reps = mysql_query($qry_get_reps) or die (tdw_mysql_error($qry_get_reps));
																?>
																<select name="sel_rep" class="Text2">
                                	<option value="">Select Rep.</option>
																<?
																while ( $row = mysql_fetch_array($result_get_reps) )
																	{
																		?> 
																		<option value="<?=$row["trad_rr"]."^".$row["ID"]."^".$row["rep_name"]."&nbsp; &nbsp; (".$row["rr_num"].")"."^".$row["Initials"]?>"<? if ($row["ID"] == $rep_id) {echo " selected";} ?>><?=str_pad($row["rep_name"], 20, ".")?>(<?=$row["rr_num"]?>)</option>
																		<?
																	}
																?>
																</select>
																</td>
																<td width="15">&nbsp;</td>
																<td width="125" class="ilt" align="right">Select As Of Date</td>
																<td width="150">
																<SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
																<SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
																	<SCRIPT LANGUAGE="JavaScript">
																	var cal = new CalendarPopup("divfrom");
																	cal.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
																	</SCRIPT>																
																		<input type="text" id="iddatefilterval" class="Text" name="datefilterval" size="12" maxlength="12" value="<?=format_date_ymd_to_mdy($trade_date_to_process)?>">
																		<A HREF="#" onClick="cal.select(document.forms['selectionfilter'].datefilterval,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A>
																</td>
																<td width="15" class="ilt"> OR </td>
																<td width="200" class="ilt">
																<?
																//get reps from query  on table mry_comm_rr_trades and join on users
																$str_brok_month = date('Y-m-d');
																$arr_brok_months = array();
																$qry_brok_months = "select concat(brk_month,' ', brk_year) as str_brok, brk_end_date 
																										 from brk_brokerage_months
																										 where brk_end_date <= '".$str_brok_month."'
																										 order by brk_end_date desc limit 0,12";
																$result_brok_months = mysql_query($qry_brok_months) or die (tdw_mysql_error($qry_brok_months));
																?>
																<select name="sel_brok_month" class="Text">
                                	<option value="">Select Brokerage Month</option>
																<?
																while ( $row = mysql_fetch_array($result_brok_months) ) {
																		?> 
																		<option value="<?=$row["brk_end_date"]?>")<? if ($sel_brok_month == $row["brk_end_date"]) {echo " selected"; } ?>><?=$row["str_brok"]?></option>
																		<?
																	}
																?>
																</select>
																</td>
                                <td width="30" align="left"><input type="image" src="images/lf_v1/form_submit.png" border="0"></td>
																		</form>
																<td width="100" align="right">
																<script language="javascript">

																function go_prntscrn () {
																 document.prntscrn.sel_rep.value = document.selectionfilter.sel_rep.value;
																 document.prntscrn.sel_brok_month.value = document.selectionfilter.sel_brok_month.value;
																 document.prntscrn.datefilterval.value = document.selectionfilter.datefilterval.value;
																 document.prntscrn.info_str.value = '<?=$userfullname?>';
																}
																</script>
																	<form name="prntscrn" action="sales_rev_summ_excel.php" method="get" target="_blank">
																		<input type="image" src="images/lf_v1/exp2excel.png" border="0" alt="Export to Excel."  onclick="go_prntscrn()"/>&nbsp;&nbsp;
																		<input type="hidden" name="sel_rep" value="" />
																		<input type="hidden" name="datefilterval" value="" />
																		<input type="hidden" name="info_str" value="" />
																		<input type="hidden" name="sel_brok_month" value="" />
																	</form>
																</td>
                                <td width="5">&nbsp;</td>
																<td width="140" align="right">
																<script language="javascript">

																function go_excel_detail () {
																 document.exceldetail.sel_rep.value = document.selectionfilter.sel_rep.value;
																 document.exceldetail.sel_brok_month.value = document.selectionfilter.sel_brok_month.value;
																 document.exceldetail.datefilterval.value = document.selectionfilter.datefilterval.value;
																 document.exceldetail.info_str.value = '<?=$userfullname?>';
																}
																</script>
																	<form name="exceldetail" action="sales_rev_summ_excel_detail.php" method="get" target="_blank">
																		<input type="image" src="images/lf_v1/exp2excel_detail.png" border="0" alt="Export to Excel."  onclick="go_excel_detail()"/>&nbsp;&nbsp;
																		<input type="hidden" name="sel_rep" value="" />
																		<input type="hidden" name="datefilterval" value="" />
																		<input type="hidden" name="info_str" value="" />
																		<input type="hidden" name="sel_brok_month" value="" />
																	</form>
																</td>
																<td>&nbsp;</td>
															</tr>
														</table>
														</td> 
													</tr>
												</table>
												<table width="100%" cellpadding="3" cellspacing="1" bgcolor="#CCCCCC" border="0"> 
															<tr>
																<td colspan="5" bgcolor="#ffffff" width="240"><a class="ghm">&nbsp;&nbsp;"Brokerage Month Basis"</a></td>
																<td bgcolor="#222222" colspan="3" align="center"><a class="tblhead_a">Month to Date</a></td>
																<td bgcolor="#888888" colspan="5" align="center"><a class="tblhead_a">Year to Date</a></td>
																<td bgcolor="#222222" colspan="5" align="center"><a class="tblhead_a">Performance</a></td>
																<td bgcolor="#222222">&nbsp;</td>
                              </tr>
															<tr bgcolor="#333333"> 
																<td width="15"><a class="tblhead_a">&nbsp;</a></td>
																<td width="40"><a class="tblhead_a">&nbsp;&nbsp;&nbsp;&nbsp;Tier</a></td>
																<td width="175"><a class="tblhead_a">&nbsp;&nbsp;&nbsp;&nbsp;Sales Rep.</a></td>
																<td width="100"><a class="tblhead_a">&nbsp;&nbsp;&nbsp;&nbsp;Type</a></td>
																<td width="40"><a class="tblhead_a">&nbsp;&nbsp;# Clnts.</a></td>
																<td width="70" align="right"><a class="tblhead_a">CY&nbsp;&nbsp;</a></td>
																<td width="70" align="right"><a class="tblhead_a">PY&nbsp;&nbsp;</a></td>
																<td width="80" align="right"><a class="tblhead_a">% chng.&nbsp;&nbsp;</a></td>
																<td bgcolor="#888888" width="70" align="right"><a class="tblhead_a">CY&nbsp;&nbsp;</a></td>
																<td bgcolor="#888888" width="70" align="right"><a class="tblhead_a">PY&nbsp;&nbsp;</a></td>
																<td bgcolor="#888888" width="70" align="right"><a class="tblhead_a">$ chng.&nbsp;&nbsp;</a></td>
																<td bgcolor="#888888" width="70" align="right"><a class="tblhead_a">% chng.&nbsp;&nbsp;</a></td>
																<td bgcolor="#888888" width="70" align="right"><a class="tblhead_a">CY Annly.&nbsp;&nbsp;</a></td>
																<td width="70" bgcolor="#222222" align="center"><a class="tblhead_a">Target<br>Budget&nbsp;&nbsp;</a></td>
																<td width="70" bgcolor="#555555" align="center"><a class="tblhead_a">Perf. vs.<br />Target Budget</a></td>
																<td width="80" bgcolor="#888888" align="center"><a class="tblhead_a">Prior Full <br />Year</a></td>
																<td width="80" bgcolor="#222222" align="center"><a class="tblhead_a">Perf.<br />vs. PFY</a></td>
																<td width="80" bgcolor="#222222" align="center"><a class="tblhead_a">Prior FY - 1</a></td>
																<td>&nbsp;</td>
															</tr>
                              <?
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
												
//Active Clients.
	$arr_active_clients = array();
	$qry_active_clients = "select distinct(trim(clnt_code)) as active_clients 
												 from int_clnt_clients 
												 where clnt_isactive = 1 and clnt_status = 'A'
												 and clnt_code!= '----' and clnt_code not like 'ADJ %'";
	$result_active_clients = mysql_query($qry_active_clients) or die (tdw_mysql_error($qry_active_clients));
	while ( $row = mysql_fetch_array($result_active_clients) ) {
		$arr_active_clients[] = $row["active_clients"];
	}
	$str_active_clients = implode("','", $arr_active_clients);
	$str_active_clients = "('".$str_active_clients."')";
	//echo $str_active_clients;												
																								
//Client current tier and how many years in that tier and if it went up or down.
	for ($k=1;$k<3;$k++) {
		$arr_years[] = date('Y',strtotime($trade_date_to_process)) - $k;
	}
	$arr_years = array_reverse($arr_years);
	$str_years = implode("','", $arr_years);
	$str_years = "('".$str_years."')";
	//echo $str_years;
	//show_array($arr_years);

	//Yearly Totals for Y-1 and Y-2
	$arr_clnt_yearly_total = array();
	$qry_yearly_total = "select yrt_advisor_code, yrt_year, round(sum(yrt_commission),0) as clnt_revenue
											 from yrt_yearly_total_lookup
											 where yrt_advisor_code in ".$str_active_clients." 
											 and yrt_year in ".$str_years."
											 group by yrt_advisor_code, yrt_year
											 order by yrt_advisor_code, yrt_year";
	$result_yearly_total = mysql_query($qry_yearly_total) or die (tdw_mysql_error($qry_yearly_total));
	while ( $row = mysql_fetch_array($result_yearly_total) ) {
		$arr_clnt_yearly_total[] = $row["yrt_advisor_code"]."^".$row["yrt_year"]."^".$row["clnt_revenue"];
		$arr_clnt_yearly_total_process[$row["yrt_advisor_code"]][$row["yrt_year"]]= $row["clnt_revenue"];
	}
	//show_array($arr_clnt_yearly_total);
		
	//Annualized Current Year
	$qry_cur_yearly_total = "select trad_advisor_code, round(sum(trad_commission),0) as clnt_revenue
													from mry_comm_rr_trades 
													where trad_trade_date between '".date('Y')."-01-01' and '".date('Y')."-12-31' 
													and trad_is_cancelled = 0
													group by trad_advisor_code
													order by trad_advisor_code";
																	
	$result_cur_yearly_total = mysql_query($qry_cur_yearly_total) or die (tdw_mysql_error($qry_cur_yearly_total));
	$arr_cur_yearly_total = array();
	$arr_cur_yearly_total_actual = array();
	while ( $row = mysql_fetch_array($result_cur_yearly_total) ) {
		$annualized_cur_year = round(($row["clnt_revenue"]/date('z'))*365,0);
		$arr_cur_yearly_total_actual[$row["trad_advisor_code"]] = round(($row["clnt_revenue"]/1000),0);
		$arr_cur_yearly_total[$row["trad_advisor_code"]] = $annualized_cur_year; 
	}
	
	//Annualized Current Year Checks
	$qry_cur_yearly_chk_total = "select chek_advisor, round(sum(chek_amount),0) as clnt_revenue
													from chk_chek_payments_etc  
													where chek_date between '".date('Y',strtotime($trade_date_to_process))."-01-01' and '".date('Y',strtotime($trade_date_to_process))."-12-31' 
													and chek_isactive = 1
													group by chek_advisor
													order by chek_advisor";
																	
	$result_cur_yearly_chk_total = mysql_query($qry_cur_yearly_chk_total) or die (tdw_mysql_error($qry_cur_yearly_chk_total));
	$arr_cur_yearly_chk_total = array();
	$arr_cur_yearly_chk_total_actual = array();
	while ( $row = mysql_fetch_array($result_cur_yearly_chk_total) ) {
		$annualized_cur_year = round(($row["clnt_revenue"]/date('z',strtotime($trade_date_to_process)))*365,0);
		$arr_cur_yearly_chk_total_actual[$row["chek_advisor"]] = round(($row["clnt_revenue"]/1000),0);
		$arr_cur_yearly_chk_total[$row["chek_advisor"]] = $annualized_cur_year;
	}
	//show_array($arr_cur_yearly_chk_total);
	
	//Merge Commission and Checks for Current Year.
	$arr_merge_current_year_actual = array();
	$arr_merge_current_year_annualized = array();
	foreach ($arr_active_clients as $zindex=>$ccode) {
		$arr_merge_current_year_actual[$ccode] = $arr_cur_yearly_total_actual[$ccode] + $arr_cur_yearly_chk_total_actual[$ccode];
		$arr_merge_current_year_annualized[$ccode] = $arr_cur_yearly_total[$ccode] + $arr_cur_yearly_chk_total[$ccode];
	}
	//show_array($arr_merge_current_year_actual);
	//($arr_merge_current_year_annualized);


	//Client Rep. List. Containing Rep. Initials.
	$clnt_rep_list = array();
	$qry_clnt_rep_list = "SELECT clnt_code, clnt_rr1, clnt_rr2 from int_clnt_clients  
												where clnt_status ='A'
												and clnt_isactive = 1";
	$result = mysql_query($qry_clnt_rep_list) or die(tdw_mysql_error($qry_clnt_rep_list));
	while ( $row = mysql_fetch_array($result) ) 
	{
		if (trim($row["clnt_rr1"]) != "" 
				&& trim($row["clnt_rr1"])!= "**" 
				&& trim($row["clnt_code"]) != "----"
				&& substr(trim($row["clnt_code"]),0,4) != "ADJ ") {
			$clnt_rep_list[$row["clnt_code"]] = trim($row["clnt_rr1"])."^".trim($row["clnt_rr2"]);
		}
	}
	//show_array($clnt_rep_list);
	//exit;

	//Get userid, rep name, initial from db
	$qry_get_reps = "SELECT a.ID, a.Initials, a.rr_num, trim(concat(a.Firstname,' ', a.Lastname)) AS rep_name, b.trad_rr
										FROM users a, mry_comm_rr_trades b
										WHERE a.rr_num = b.trad_rr
										AND a.user_isactive = 1
										AND b.trad_rr like '0%'
										AND trim(a.Initials) != ''
										GROUP BY b.trad_rr
										ORDER BY a.Firstname";
	
	$arr_reps_list = array();
	$arr_reps_list_array = array();												
	$result_get_reps = mysql_query($qry_get_reps) or die (tdw_mysql_error($qry_get_reps));
	while ( $row = mysql_fetch_array($result_get_reps) ) {
		$arr_reps_list[$row["ID"]] = $row["Initials"]."^".$row["rr_num"]."^".$row["rep_name"];
		$arr_reps_list_array [$row["ID"]] = array($row["Initials"],$row["rr_num"],$row["rep_name"]); 
	}
	//show_array($arr_reps_list_array);
	//exit;
	
	//Array of Client Revenue, Tier, Type and Reps.
	$clnt_rep_revenue_tier = array();
  foreach ($arr_active_clients as $k=>$v) {
		//$str_revenue = $arr_cur_yearly_total[$v] + $arr_cur_yearly_chk_total[$v];
		$str_revenue = $arr_merge_current_year_annualized[$v];
		$str_tier = get_tier($str_revenue);
		$arr_rep_codes = explode("^",$clnt_rep_list[$v]);
		$str_rep1 = trim($arr_rep_codes[0]);
		$str_rep2 = trim($arr_rep_codes[1]);
		$clnt_rep_revenue_tier[$v] = array($str_revenue,$str_tier,$str_rep1,$str_rep2);
		//echo $v. "#" . $str_revenue. "#" . $str_tier ."#" . $str_rep1."#" . $str_rep2."<br>"; 
	}
	//show_array($clnt_rep_revenue_tier);
	
	//$arr_reps_list_array [$row["ID"]] = array($row["Initials"],$row["rr_num"],$row["rep_name"]);
	//$clnt_rep_revenue_tier[$v] = array($str_revenue,$str_tier,$str_rep1,$str_rep2);
	//$clnt_rep_list[$row["clnt_code"]] = trim($row["clnt_rr1"])."^".trim($row["clnt_rr2"]);

	$arr_rep_client_details = array(); // array holds details of client, tier, etc.
	foreach ($clnt_rep_revenue_tier as $ccode=>$aval) {
		if ($aval[2] != "" && $aval[3] == "" ) { //SOLE CLIENT
			foreach ($arr_reps_list_array as $ak=>$av) {
				if ($aval[2]==$av[0]) {
					$arr_rep_client_details[] = array($ak,$ccode,$aval[0],$aval[1],"Sole");
				}
			}
		} else if ($aval[2] != "" && $aval[3] != "" ) { //JOINT PRIMARY AND SECONDARY
			foreach ($arr_reps_list_array as $ak=>$av) {
				if ($aval[2]==$av[0]) {
					$arr_rep_client_details[] = array($ak,$ccode,$aval[0],$aval[1],"JP"); //PRIMARY
				}
				if ($aval[3]==$av[0]) {
					$arr_rep_client_details[] = array($ak,$ccode,$aval[0],$aval[1],"JS"); //SECONDARY
				}
			}
		} else if ($aval[2] == "" && $aval[3] == "" ) {
			$var_dummy = 1;
			//echo "[".$av[0]."]"."[".$aval[2]."]"."[".$aval[3]."]"."[".$ccode."]"."[".$aval[0]."]"."<br>";
		} else {
			//do nothing for now.
			//echo "[".$av[0]."]"."[".$aval[2]."]"."[".$aval[3]."]"."<br>";
			echo "ERROR: UNKNOWN EXCEPTION.<br>"; //
			$var_dummy = 1;
		}
	}
	
	//show_array($arr_rep_client_details);
	//exit;
	
	
	
	/*if ( $arr_reps_list_array [$ck][2] == 'Andrew Sinclair') {
		echo "Processing Andrew Sinclair<br>";
		xdebug("i",$i);
	}*/
	//exit;
	
	//Get Count with Tier for each user.
	$arr_tier_count = array();
	foreach($arr_rep_client_details as $bk=>$bv) {
		//show_array($bv);
		//$arr_tier_count[$bv[0]][$bv[4]][$aval[1]] = array($arr_tier_count[$bv[0]][$bv[4]][$aval[1]][0]+1, $arr_tier_count[$bv[0]][$bv[4]][$aval[1]][1]."','".$bv[1]);
		$arr_tier_count[$bv[0]][$bv[4]][$bv[3]] = array($arr_tier_count[$bv[0]][$bv[4]][$bv[3]][0]+1, $arr_tier_count[$bv[0]][$bv[4]][$bv[3]][1]."','".$bv[1]);
	}
	//show_array($arr_tier_count);	
	//exit;
	//echo ">>>>>>>>>>>>>>>>> [346]['Sole'][4]"."<br>";
	//show_array($arr_tier_count[346]['Sole'][4]);
	//CYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCY
	//CYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCY
	
	//Get CY MTD Commission for Each Client, to be consolidated in a function.
	//xdebug('trade_date_to_process',$trade_date_to_process);
	$cur_month_start_date = db_single_val("SELECT `brk_start_date` as single_val from brk_brokerage_months where brk_start_date <= '".$trade_date_to_process."' and brk_end_date >= '".$trade_date_to_process."'");
	$qry_cymtd = "select trad_advisor_code, round(sum(trad_commission),0) as clnt_revenue
													from mry_comm_rr_trades 
													where trad_trade_date between '".$cur_month_start_date."' and '".$trade_date_to_process."' 
													and trad_is_cancelled = 0
													group by trad_advisor_code
													order by trad_advisor_code";
	//xdebug("qry_cymtd",$qry_cymtd);

	$result_cymtd = mysql_query($qry_cymtd) or die (tdw_mysql_error($qry_cymtd));
	$arr_cymtd = array();
	while ( $row = mysql_fetch_array($result_cymtd) ) {
		$arr_cymtd[$row["trad_advisor_code"]] = $row["clnt_revenue"]; 
	}
	//show_array($arr_cymtd);
	//exit;
	
	// if Brokerage Month is selected then use end of calendar month.
	if ($sel_brok_month != "") {
		$end_date = date("Y-m-t", strtotime($sel_brok_month));
	} else {
		$end_date = $trade_date_to_process;
	}
	//xdebug("end_date",$end_date);
	
	//Get CY MTD Check for Each Client, to be consolidated in a function.
	$qry_cymtd_chk = "select chek_advisor, round(sum(chek_amount),0) as clnt_revenue
													from chk_chek_payments_etc  
													where chek_date between '".date('Y',strtotime($trade_date_to_process))."-".date('m',strtotime($trade_date_to_process))."-01' and '".$end_date."' 
													and chek_isactive = 1
													group by chek_advisor
													order by chek_advisor";
	$result_cymtd_chk = mysql_query($qry_cymtd_chk) or die (tdw_mysql_error($qry_cymtd_chk));
	$arr_cymtd_chk = array();
	while ( $row = mysql_fetch_array($result_cymtd_chk) ) {
		$arr_cymtd_chk[$row["chek_advisor"]] = $row["clnt_revenue"]; 
	}
	//show_array($arr_cymtd_chk);
	
	//Merge Commission and Checks for CY MTD.
	$arr_merge_cymtd = array();
	foreach ($arr_active_clients as $zindex=>$ccode) {
		$arr_merge_cymtd[$ccode] = $arr_cymtd[$ccode] + $arr_cymtd_chk[$ccode];
	}
	//show_array($arr_merge_cymtd);	

	//Get CY YTD for Each Client, to be consolidated in a function.
	$qry_cyytd = "select trad_advisor_code, round(sum(trad_commission),0) as clnt_revenue
													from mry_comm_rr_trades 
													where trad_trade_date between '".date('Y',strtotime($trade_date_to_process))."-01-01' and '".$trade_date_to_process."' 
													and trad_is_cancelled = 0
													group by trad_advisor_code
													order by trad_advisor_code";
																	
	$result_cyytd = mysql_query($qry_cyytd) or die (tdw_mysql_error($qry_cyytd));
	$arr_cyytd = array();
	while ( $row = mysql_fetch_array($result_cyytd) ) {
		$arr_cyytd[$row["trad_advisor_code"]] = $row["clnt_revenue"]; 
	}
	//show_array($arr_cyytd);

	//Get CY YTD Check for Each Client, to be consolidated in a function.
	$qry_cyytd_chk = "select chek_advisor, round(sum(chek_amount),0) as clnt_revenue
													from chk_chek_payments_etc  
													where chek_date between '".date('Y',strtotime($trade_date_to_process))."-01-01' and '".$end_date."' 
													and chek_isactive = 1
													group by chek_advisor
													order by chek_advisor";
	$result_cyytd_chk = mysql_query($qry_cyytd_chk) or die (tdw_mysql_error($qry_cyytd_chk));
	$arr_cyytd_chk = array();
	while ( $row = mysql_fetch_array($result_cyytd_chk) ) {
		$arr_cyytd_chk[$row["chek_advisor"]] = $row["clnt_revenue"]; 
	}
	//show_array($arr_cyytd_chk);

	//Merge Commission and Checks for CY YTD.
	$arr_merge_cyytd = array();
	foreach ($arr_active_clients as $zindex=>$ccode) {
		$arr_merge_cyytd[$ccode] = $arr_cyytd[$ccode] + $arr_cyytd_chk[$ccode];
	}
	//show_array($arr_merge_cyytd);	
	//exit;
	//CYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCY
	//CYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCYCY
	
	//PYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPY	
	//PYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPY	
	
	$py_trade_date_to_process = date('Y-m-d', strtotime("last year", strtotime($trade_date_to_process)));
	//Get PY MTD Commission for Each Client, to be consolidated in a function.
	//xdebug('trade_date_to_process',$trade_date_to_process);
	$py_cur_month_start_date = db_single_val("SELECT `brk_start_date` as single_val from brk_brokerage_months where brk_start_date <= '".$py_trade_date_to_process."' and brk_end_date >= '".$py_trade_date_to_process."'");
	
	$qry_pymtd = "select trad_advisor_code, round(sum(trad_commission),0) as clnt_revenue
													from mry_comm_rr_trades 
													where trad_trade_date between '".$py_cur_month_start_date."' and '".$py_trade_date_to_process."' 
													and trad_is_cancelled = 0
													group by trad_advisor_code
													order by trad_advisor_code";
																	
	$result_pymtd = mysql_query($qry_pymtd) or die (tdw_mysql_error($qry_pymtd));
	$arr_pymtd = array();
	while ( $row = mysql_fetch_array($result_pymtd) ) {
		$arr_pymtd[$row["trad_advisor_code"]] = $row["clnt_revenue"]; 
	}
	//show_array($arr_pymtd);
	//exit;

	// if Brokerage Month is selected then use end of calendar month.
	if ($sel_brok_month != "") {
		$end_date_py = date("Y-m-t", strtotime($py_trade_date_to_process));
	} else {
		$end_date_py = $py_trade_date_to_process;
	}
	//xdebug('end_date_py',$end_date_py);

	//Get PY MTD Check for Each Client, to be consolidated in a function.
	$qry_pymtd_chk = "select chek_advisor, round(sum(chek_amount),0) as clnt_revenue
													from chk_chek_payments_etc  
													where chek_date between '".date('Y',strtotime($py_trade_date_to_process))."-".date('m',strtotime($py_trade_date_to_process))."-01' and '".$end_date_py."' 
													and chek_isactive = 1
													group by chek_advisor
													order by chek_advisor";
	$result_pymtd_chk = mysql_query($qry_pymtd_chk) or die (tdw_mysql_error($qry_pymtd_chk));
	$arr_pymtd_chk = array();
	while ( $row = mysql_fetch_array($result_pymtd_chk) ) {
		$arr_pymtd_chk[$row["chek_advisor"]] = $row["clnt_revenue"]; 
	}
	//show_array($arr_pymtd_chk);
	
	//Merge Commission and Checks for CY MTD.
	$arr_merge_pymtd = array();
	foreach ($arr_active_clients as $zindex=>$ccode) {
		$arr_merge_pymtd[$ccode] = $arr_pymtd[$ccode] + $arr_pymtd_chk[$ccode];
	}
	//show_array($arr_merge_pymtd);	

	//Get CY YTD for Each Client, to be consolidated in a function.
	$qry_pyytd = "select trad_advisor_code, round(sum(trad_commission),0) as clnt_revenue
													from mry_comm_rr_trades 
													where trad_trade_date between '".date('Y',strtotime($py_trade_date_to_process))."-01-01' and '".$py_trade_date_to_process."' 
													and trad_is_cancelled = 0
													group by trad_advisor_code
													order by trad_advisor_code";
																	
	$result_pyytd = mysql_query($qry_pyytd) or die (tdw_mysql_error($qry_pyytd));
	$arr_pyytd = array();
	while ( $row = mysql_fetch_array($result_pyytd) ) {
		$arr_pyytd[$row["trad_advisor_code"]] = $row["clnt_revenue"]; 
	}
	//show_array($arr_pyytd);

	//Get CY YTD Check for Each Client, to be consolidated in a function.
	$qry_pyytd_chk = "select chek_advisor, round(sum(chek_amount),0) as clnt_revenue
													from chk_chek_payments_etc  
													where chek_date between '".date('Y',strtotime($py_trade_date_to_process))."-01-01' and '".$end_date_py."' 
													and chek_isactive = 1
													group by chek_advisor
													order by chek_advisor";
	$result_pyytd_chk = mysql_query($qry_pyytd_chk) or die (tdw_mysql_error($qry_pyytd_chk));
	$arr_pyytd_chk = array();
	while ( $row = mysql_fetch_array($result_pyytd_chk) ) {
		$arr_pyytd_chk[$row["chek_advisor"]] = $row["clnt_revenue"]; 
	}
	//show_array($arr_pyytd_chk);

	//Merge Commission and Checks for CY YTD.
	$arr_merge_pyytd = array();
	foreach ($arr_active_clients as $zindex=>$ccode) {
		$arr_merge_pyytd[$ccode] = $arr_pyytd[$ccode] + $arr_pyytd_chk[$ccode];
	}
	//show_array($arr_merge_pyytd);	
	//exit;

	//PYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPY	
	//PYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPYPY	

	$arr_budget = array();
	$qry_budget = "select a.bdgt_amount, a.bdgt_year, b.clnt_code from
								int_clnt_clients_budget a
								left join int_clnt_clients b on a.clnt_id = b.clnt_auto_id 
								where a.bdgt_year = '".date('Y',strtotime($trade_date_to_process))."'";
	$result_budget = mysql_query($qry_budget) or die (tdw_mysql_error($qry_budget));
	while ( $row = mysql_fetch_array($result_budget) ) {
		$arr_budget[$row["clnt_code"]] = $row["bdgt_amount"]; 
	}
	//echo $qry_budget;
	//show_array($arr_budget);	 

	//Function to provide budget for a group of clients.
	//Inputs are Budget Array and Client Group String
	function budget_group_clients ($arr_budget, $str_clients) {
		//$str = "','KEYA','WEIS','GUGG";
		$str = substr($str_clients,2,10000);
		$str = str_replace("'","",$str);
		$arr_clnts = explode(",",$str);
		$return_rev = 0;
		foreach($arr_clnts as $k=>$v) {
			if (array_key_exists($v,$arr_budget)) {
				$return_rev = $return_rev + $arr_budget[$v];
			}
		}
		return $return_rev;
	}

		
	//Function to provide revenue for a group of clients.
	//Inputs are Revenue Array and Client Group String
	function rev_group_clients ($arr_rev, $str_clients) {
		//$str = "','KEYA','WEIS','GUGG";
		$str = substr($str_clients,2,10000);
		$str = str_replace("'","",$str);
		$arr_clnts = explode(",",$str);
		$return_rev = 0;
		foreach($arr_clnts as $k=>$v) {
			if (array_key_exists($v,$arr_rev)) {
				$return_rev = $return_rev + $arr_rev[$v];
			}
		}
		return $return_rev;
	}

	//Function to provide past years revenue for a group of clients.
	//Inputs are Revenue Array and Client Group String
	//$arr_clnt_yearly_total_process[$row["yrt_advisor_code"]][$row["yrt_year"]]= $row["clnt_revenue"];
	function rev_group_clients_past_years ($arr_clnt_yearly_total_process, $year, $str_clients) {
		//$str = "','KEYA','WEIS','GUGG";
		$str = substr($str_clients,2,10000);
		$str = str_replace("'","",$str);
		$arr_clnts = explode(",",$str);
		$return_rev = 0;
		foreach($arr_clnts as $k=>$v) {
			if ($arr_clnt_yearly_total_process[$v][$year]) {
				$return_rev = $return_rev + $arr_clnt_yearly_total_process[$v][$year];
			}
		}
		return $return_rev;
	}
		
	//echo "THIS IS ". rev_group_clients_past_years ($arr_clnt_yearly_total_process, '2012', "','ASCE");
	//exit;	
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

//111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111
//111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111
//111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111

	//$arr_reps_list_array [$row["ID"]] = array($row["Initials"],$row["rr_num"],$row["rep_name"]); 
	$level_a_count = 0;
	//$arr_sole_joint = array("'Sole'","'JP'","'JS'");
	$arr_sole_joint["Sole"] = "Sole";
	$arr_sole_joint["JP"] = "JP";
	$arr_sole_joint["JS"] = "JS";
	
	$arr_tier_val = array('1','2','3','4');
	//show_array($arr_tier_val);

	//echo ">>>>>>>>>>>>>>>>> [346][Sole][4]"."<br>";
	//show_array($arr_tier_count[346][Sole]['4']);
	//show_array($arr_reps_list_array);

					//echo "Processing ".$i."<br>";
					//echo $ck." ".$dv." ".$ev."<br>"; 
					//for($i=1;$i<5;$i++) {
					//show_array($arr_tier_count[$ck][$dv][$i]);
					
					//if ($arr_tier_count[$ck][$dv][$i]) {
					//	echo $ck." ".$dv." ".$i."<br>";
					//}

	$arr_reps_list_array_final = array();
	if ($rep_id != "") { 
		//$use_rep_id = $ck;
		foreach( $arr_reps_list_array as $k=>$v) {
			if ($rep_id == $k) {
				$arr_reps_list_array_final[$k] = $v;
			}
		}
	} else {
		$arr_reps_list_array_final = $arr_reps_list_array;
	}

	
	foreach ($arr_reps_list_array_final as $use_rep_id=>$cv) {
		foreach($arr_sole_joint as $dk=>$dv) {
			foreach($arr_tier_val as $ek=>$i) {
				
				if ($arr_tier_count[$use_rep_id][$dv][$i]) {
				
							
														
							if ($level_a_count % 2) { 
									$class_row = "trdark";
							} else { 
									$class_row = "trlight"; 
							} 
							$cymtd = rev_group_clients($arr_merge_cymtd, $arr_tier_count[$use_rep_id][$dv][$i][1]);
							$pymtd = rev_group_clients($arr_merge_pymtd, $arr_tier_count[$use_rep_id][$dv][$i][1]);
							if ($pymtd > 0) { 
								$mtd_chng = (($cymtd-$pymtd)/$pymtd)*100; 
							} else { 
								$mtd_chng = '-NA-';
							} 
							if ($mtd_chng < 0) { 
								$str_mtd_chng = "<font color='red'>".number_format($mtd_chng,0,".",",")."%</font>"; 
							} else { 
								$str_mtd_chng = number_format($mtd_chng,0,".",",").'%';
							} 
							$cyytd = rev_group_clients($arr_merge_cyytd, $arr_tier_count[$use_rep_id][$dv][$i][1]);
							$pyytd = rev_group_clients($arr_merge_pyytd, $arr_tier_count[$use_rep_id][$dv][$i][1]);
							if ($pyytd > 0) { 
								$ytd_chng = (($cyytd-$pyytd)/$pyytd)*100; 
							} else { 
								$ytd_chng = '-NA-';
							} 
							if ($ytd_chng < 0) { 
								$str_ytd_chng = "<font color='red'>".number_format($ytd_chng,0,".",",")."%</font>"; 
							} else { 
								$str_ytd_chng = number_format($ytd_chng,0,".",",").'%';
							} 
							$py_full = rev_group_clients_past_years ($arr_clnt_yearly_total_process, date('Y',strtotime($trade_date_to_process))-1, $arr_tier_count[$use_rep_id][$dv][$i][1]);
							$ppy_full = rev_group_clients_past_years ($arr_clnt_yearly_total_process, date('Y',strtotime($trade_date_to_process))-2, $arr_tier_count[$use_rep_id][$dv][$i][1]);
							
							if ($dv == "Sole") { $str_type = "PRIMARY";} elseif ($dv == "JP") { $str_type = "Shared Pri."; } elseif ($dv == "JS") { $str_type = "Shared Sec."; } else { $str_type = "";}
							
							//Create Link Parameters for Detail Data
							//$arr_tier_count[$bv[0]][$bv[4]][$bv[3]] = array($arr_tier_count[$bv[0]][$bv[4]][$bv[3]][0]+1, $arr_tier_count[$bv[0]][$bv[4]][$bv[3]][1]."','".$bv[1]);
							
							$str_clnts = $arr_tier_count[$use_rep_id][$dv][$i][1];
							$str_clnts = substr($str_clnts,2,1000)."'";
							$str_clnts = str_replace("'","",$str_clnts);
							//echo $str_clnts;						
											
							$bdgt_cy = budget_group_clients($arr_budget, $arr_tier_count[$use_rep_id][$dv][$i][1]);
							if ($bdgt_cy > 0) {
								$str_perf_bdgt = round((round(($cyytd/date('z',strtotime($py_trade_date_to_process)))*365,0) / $bdgt_cy)*100,0)."%";
							} else {
								$str_perf_bdgt = '-NA-';
							}
				
							if ($py_full > 0) {
								$str_perf_pyf = round((round(($cyytd/date('z',strtotime($py_trade_date_to_process)))*365,0) / $py_full)*100,0)."%";
							} else {
								$str_perf_pyf = '-NA-';
							}

											
							$id_md5 = md5(rand(1,99999999));							
							?>
              <tr class="<?=$class_row?>"> 
              	<td><a href="#" onclick="javascript:populate_div('<?=$id_md5?>','<?=$str_clnts?>','<?=$trade_date_to_process?>')">
                                <img id="img<?=$id_md5?>" src="images/lf_v1/expand.png" border="0"></a></td>
							<? echo "<td>".$i."</td>"; ?>
                <td><?=$arr_reps_list_array [$use_rep_id][2]?></td>
                <td><?=$str_type?></td>
                <td align="right"><?=$arr_tier_count[$use_rep_id][$dv][$i][0]?></td>
                <td align="right"><?=number_format($cymtd,0,".",",")?></td>
                <td align="right"><?=number_format($pymtd,0,".",",")?></td>
                <td align="right"><?=$str_mtd_chng?></td>
                <td align="right"><?=number_format($cyytd,0,".",",")?></td>
                <td align="right"><?=number_format($pyytd,0,".",",")?></td>
                <td align="right"><?=number_format(($cyytd-$pyytd),0,".",",")?></td>
                <td align="right"><?=$str_ytd_chng?></td>
                <td align="right"><?=number_format(round(($cyytd/date('z',strtotime($py_trade_date_to_process)))*365,0),0,".",",")?></td>
                <td align="right"><?=number_format($bdgt_cy,0,".",",")?></td>
                <td align="right"><?=$str_perf_bdgt?></td>
                <td align="right"><?=number_format($py_full,0,".",",")?></td>
                <td align="right"><?=$str_perf_pyf?></td>
                <td align="right"><?=number_format($ppy_full,0,".",",")?></td>
                <td>&nbsp;</td>
              </tr>
              <tr>
              <!--<td colspan="19" height="0" bgcolor="#FFFFFF"><div id="div_<?=$id_md5?>" style="visibility:hidden; display:block"></div></td>-->
              <td colspan="19" height="0" bgcolor="#FFFFFF" id="div_<?=$id_md5?>" style="visibility:hidden; display:block"></td>
              </tr>
							<?
              $level_a_count++;
				}
			}
		}
	}
	?>
	</table></td></table>
  <DIV ID="divfrom" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
	<?

//111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111
//111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111
//111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

    ////
		// Get date in previous year (input and output format: yyyy-mm-dd)
		function get_tier($amt) {
			if ($amt <= 50000) {
				return 4;
			} elseif ($amt > 50000 && $amt <= 100000) {
				return 3;
			} elseif ($amt > 100000 && $amt <= 200000) {
				return 2;
			} elseif ($amt > 200000) {
				return 1;
			} else {
				return "?";
			}
		}
		
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%												

tep();
?>?>