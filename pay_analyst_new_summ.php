<?
include('pay_analyst_js.php');
include('pay_analyst_css.php');
?>

<script language="JavaScript" src="includes/js/ajax_tbx.js"></script>
<script language ="Javascript">
<!--
function procx(str) {
	var arr_str = new Array();
	arr_str=str.split("^");
	if (arr_str[0]=="b") {
		newStr = "You have chosen to UNLOCK Analyst Allocations for " + arr_str[5] + "\n" + "for Q" + arr_str[2] + " " + arr_str[3] + ".";
	} else {
		newStr = "You have chosen to SEND EMAIL REMINDER to " + arr_str[5] + "\n" + "for completion of Analyst Allocations for period Q" + arr_str[2] + " " + arr_str[3] + ".";
	}
	input_box=confirm( newStr + "\n\n" + "Are you sure?");
	if (input_box==true)	{ 
		AjaxRequest.get(
			{
				'url':'pay_analyst_summ_ajax.php?str='+ str
				,'onSuccess':function(req){ 
																		parse_req(req.responseText);
																	}
				,'onError':function(req){ document.getElementById('notify').innerHTML='Program Error! Please contact Technical Support.';}
			}
		);
	} else {
		return false;
	}
}

function parse_req(response) {
	var arr_response = new Array();
	arr_response=response.split("^");
	//scount is [2] passed back
	if(arr_response[0]=="c") {
		document.getElementById('notify').innerHTML=arr_response[1]; 
		document.getElementById('notify').className="notify_x"; 
		var citemid = 'c' + arr_response[2];
		document.getElementById(citemid).innerHTML="&nbsp;&nbsp;&nbsp;Email Sent";
	} else {
		document.getElementById('notify').innerHTML=arr_response[1]; 
		document.getElementById('notify').className="notify_x";
		var bitemid = 'b' + arr_response[2];
		document.getElementById(bitemid).innerHTML="&nbsp;&nbsp;&nbsp;Unlocked";
		var aitemid = 'a' + arr_response[2];
		document.getElementById(aitemid).innerHTML="&nbsp;&nbsp;&nbsp;<strong><font color='red'>No</font></strong>";
		
	}
	//alert($response);
}

function noenter() {
  return !(window.event && window.event.keyCode == 13); }
-->
</script>

<?

function create_arr ($q, $i=1) {
  $arr_created = array();
	$result = mysql_query($q) or die(tdw_mysql_error($q));
	if ($i == 1) {
		while ( $row = mysql_fetch_array($result) )
		{
			$arr_created[] = $row["v"];
		}
	} else {
		while ( $row = mysql_fetch_array($result) )
		{
			$arr_created[$row["k"]] = $row["v"];
		}
	}
	return $arr_created;
}

tsp(100, "Analyst Allocations Summary and Reporting");
?>
<style type="text/css">
<!--
.notify_x {
	font-family: verdana;
	font-size: 12px;
	font-weight: bold;
	color: #009900;
	text-decoration: none;
	background-color: #E6FFE6;
	border-top-width: 1px;
	border-bottom-width: 1px;
	border-top-style: solid;
	border-right-style: none;
	border-bottom-style: solid;
	border-left-style: none;
	border-top-color: #00CC33;
	border-bottom-color: #00CC33;
}
-->
</style>

<table width="100%" border="0" cellpadding="4" cellspacing="0"> 
	<tr>
		<td>
		<!-- Top Menu -->		
		<form name="frm_criteria" action="<?=$PHP_SELF?>" method="get"><!-- onsubmit="return check_frm_criteria()"-->
		<select name="sel_qtr">
			<option value="">Select Quarter</option>
			<option value="1" <? if ($sel_qtr == 1) { echo "selected";}?>>Qtr. 1</option>
			<option value="2" <? if ($sel_qtr == 2) { echo "selected";}?>>Qtr. 2</option>
			<option value="3" <? if ($sel_qtr == 3) { echo "selected";}?>>Qtr. 3</option>
			<option value="4" <? if ($sel_qtr == 4) { echo "selected";}?>>Qtr. 4</option>
		</select>		
		&nbsp;&nbsp;&nbsp;
		<select name="sel_year">
			<option value="">Select Year</option>
<!--			<option value="2007" <? if ($sel_year == 2007) { echo "selected";}?>>2007</option>
-->			<option value="2008" <? if ($sel_year == 2008) { echo "selected";}?>>2008</option>
			<option value="2009" <? if ($sel_year == 2009) { echo "selected";}?>>2009</option>
			<option value="2010" <? if ($sel_year == 2010) { echo "selected";}?>>2010</option>
		</select>		
		&nbsp;&nbsp;&nbsp;
		<input type="image" src="images/lf_v1/form_submit.png"/>
		&nbsp;&nbsp;&nbsp;
		<?
		if ($sel_qtr != "" AND $sel_year != "") {
		$str_xl = $user_id."^".$rr_num."^".$sel_qtr."^".$sel_year;
		?>
		<!--<a href="pay_analyst_excel.php?xl=<?=$str_xl?>" target="_blank"><img src="images/lf_v1/exp2excel.png" border="0" /></a>-->
		<?
		}
		?>
		</form>
		<!-- End Top Menu -->		
		</td>
	</tr>
</table>
<?

//function get sole rr_num from ID
function get_rr_num ($ID) {
	$rr_num = db_single_val("SELECT rr_num as single_val FROM users WHERE ID = '".$ID."'");   
	return $rr_num;
}

////
//function get user_id from rr_num
function get_userid_for_rr ($rr_num) {
	$user_id = db_single_val("SELECT ID as single_val FROM users WHERE rr_num = '".$rr_num."'");   
	return $user_id;
}

//function get user_id from Initials
function get_userid_for_initials ($Initials) {
	$user_id = db_single_val("SELECT ID as single_val FROM users WHERE Initials = '".$Initials."'");   
	return $user_id;
}

//function get user_id from Initials
function get_rr_num_for_initials ($Initials) {
	$rr_num = db_single_val("SELECT rr_num as single_val FROM users WHERE Initials = '".$Initials."'");   
	return $rr_num;
}

//Create Lookup Array of Client Code / Client Name
$qry_clients = "select clnt_code,
                       clnt_name,
											 trim(clnt_rr1) as clnt_rr1,
											 trim(clnt_rr2) as clnt_rr2
								from int_clnt_clients";
$result_clients = mysql_query($qry_clients) or die (tdw_mysql_error($qry_clients));
$arr_client_rrs = array();
while ( $row_clients = mysql_fetch_array($result_clients) ) 
{
	$arr_client_rrs[$row_clients["clnt_code"]] = $row_clients["clnt_rr1"]."##".$row_clients["clnt_rr2"];
}

function get_rep_for_client ($arr_client_rrs, $client_code) {
  //$initial_a, $initial_b
	$arr_initials = explode('##',	$arr_client_rrs[$client_code]);
	$initial_a = $arr_initials[0];
	$initial_b = $arr_initials[1];
	
	if (strlen($initial_b) > 1 and strlen($initial_a) > 1) { //we are talking about shared reps.
	    //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			$userid_a = get_userid_for_initials($initial_a);
			$userid_b = get_userid_for_initials($initial_b);
			//xdebug("initials/userid_a",$initial_a."/".$userid_a);
			//xdebug("initials/userid_b",$initial_b."/".$userid_b);
			$qry_shared_rr_num = "SELECT trim(srep_rrnum) as srep_rrnum 
														FROM sls_sales_reps
														WHERE srep_user_id ='".$userid_a."'
														AND	srep_isactive = 1 
														AND srep_rrnum
														IN (
														SELECT trim(srep_rrnum) 
														FROM sls_sales_reps
														WHERE 
															srep_isactive = 1 
															AND srep_user_id ='".$userid_b."')";   
			//xdebug("qry_shared_rr_num",$qry_shared_rr_num);
			$result_shared_rr_num = mysql_query($qry_shared_rr_num) or die(tdw_mysql_error($qry_shared_rr_num));
			while($row_shared_rr_num = mysql_fetch_array($result_shared_rr_num)) {
				$shared_rr_num = $row_shared_rr_num["srep_rrnum"];
			}
			return $shared_rr_num;
	    //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	} elseif (strlen($initial_b) == 0 and strlen($initial_a) > 1) {
	    //===============================================================================================
			$prim_rr_num = get_rr_num (get_userid_for_initials ($initial_a));
			return $prim_rr_num;
	    //===============================================================================================
	} else {
	    return "";
	}
}




if ($sel_qtr != "" AND $sel_year != "") {
	//@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@

	//*********************************************************************************************
	//Create Lookup Array of Client Code / Client Name
	
		$qry_clients = "select * from int_clnt_clients";
		$result_clients = mysql_query($qry_clients) or die (tdw_mysql_error($qry_clients));
		$arr_clients = array();
		while ( $row_clients = mysql_fetch_array($result_clients) ) 
		{
			$arr_clients[$row_clients["clnt_code"]] = $row_clients["clnt_name"];
		}
	//*********************************************************************************************
	//get the start and end dates for the selected quarter and year
	
	////
	// function get start and end dates for the selected quarter and year
	function get_quarter_dates ($q, $y, $b="B") { // Brokerage vs Calendar
	
	$arr_qtrs = array(1=>"Jan|Mar",2=>"Apr|Jun",3=>"Jul|Sep",4=>"Oct|Dec"); 
	$arr_qtrs_startmon = array(1=>"01",2=>"04",3=>"07",4=>"10"); 
	$arr_qtrs_endmon =   array(1=>"03",2=>"06",3=>"09",4=>"12"); 
	
	$arr_start_end_months = explode("|",$arr_qtrs[$q]);
	
		if ($b=="B") {
			$result_ = mysql_query("SELECT brk_start_date FROM brk_brokerage_months where brk_month = '".$arr_start_end_months[0]."' and brk_year = '".$y."'") or die (mysql_error());
			while ( $row = mysql_fetch_array($result_) ) {
				$begin_tradedate = $row["brk_start_date"];
			}
	
			$result_ = mysql_query("SELECT brk_end_date FROM brk_brokerage_months where brk_month = '".$arr_start_end_months[1]."' and brk_year = '".$y."'") or die (mysql_error());
			while ( $row = mysql_fetch_array($result_) ) {
				$end_tradedate = $row["brk_end_date"];
			}
	
			$arr_return_dates = array($begin_tradedate,$end_tradedate);
			return $arr_return_dates;
	
		} else {
			//to be programmed
			$sdate = $y."-".$arr_qtrs_startmon[$q]."-01";
			$edate = $y."-".$arr_qtrs_endmon[$q]."-".idate('d', mktime(0, 0, 0, ($arr_qtrs_endmon[$q] + 1), 0, $y));
			return array($sdate,$edate);
		}
	}
	
	//Create Array of all clients to show here
	$arr_quarter_brok_dates = get_quarter_dates ($sel_qtr, $sel_year);
	$arr_quarter_cal_dates = get_quarter_dates ($sel_qtr, $sel_year, "C");
	
	//show_array($arr_quarter_brok_dates);
	//show_array($arr_quarter_cal_dates);

	$arr_clnt_master_qtr = array();
	$qry_clnt_master_qtr = "SELECT distinct(trad_advisor_code) 
													FROM mry_comm_rr_trades 
													WHERE trad_trade_date between '".$arr_quarter_brok_dates[0]."' AND '".$arr_quarter_brok_dates[1]."'
														and trad_is_cancelled = 0
													order by trad_advisor_code";
	//xdebug("qry_clnt_master_qtr",$qry_clnt_master_qtr);
	$result_clnt_master_qtr = mysql_query($qry_clnt_master_qtr) or die (tdw_mysql_error($qry_clnt_master_qtr));
	while ( $row_clnt_master_qtr = mysql_fetch_array($result_clnt_master_qtr) ) 
	{
		$arr_clnt_master_qtr[$row_clnt_master_qtr["trad_advisor_code"]] = $row_clnt_master_qtr["trad_advisor_code"];
	}
	
	$qry_clnt_master_qtr = "SELECT distinct(a.chek_advisor) as chek_advisor
														FROM chk_chek_payments_etc a
														WHERE a.chek_date between '".$arr_quarter_cal_dates[0]."' AND '".$arr_quarter_cal_dates[1]."' 
															AND a.chek_isactive = 1
													 ORDER BY a.chek_advisor";
	//xdebug("qry_clnt_for_rr",$qry_clnt_for_rr);
	$result_clnt_master_qtr = mysql_query($qry_clnt_master_qtr) or die (tdw_mysql_error($qry_clnt_master_qtr));
	while ( $row_clnt_master_qtr = mysql_fetch_array($result_clnt_master_qtr) ) 
	{
		$arr_clnt_master_qtr[$row_clnt_master_qtr["chek_advisor"]] = $row_clnt_master_qtr["chek_advisor"];
	}
	
	ksort($arr_clnt_master_qtr);
	//show_array($arr_clnt_master_qtr);

	//Get array of all initials of users against the clients (RR1 ONLY)
	$str_clients = implode(",",$arr_clnt_master_qtr);
	$str_clients = str_replace(",",'","',$str_clients);
	$str_clients = '"'.$str_clients.'"';
	
	//echo $str_clients;

	$arr_clnt_rr_initials = array();
	$qry_clnt_rr_initials = "select clnt_code, clnt_rr1 from int_clnt_clients where clnt_code in (".$str_clients.")";
	$result_clnt_rr_initials = mysql_query($qry_clnt_rr_initials) or die (tdw_mysql_error($qry_clnt_rr_initials));
	while ( $row_clnt_rr_initials = mysql_fetch_array($result_clnt_rr_initials) ) 
	{
		$arr_clnt_rr_initials[trim($row_clnt_rr_initials["clnt_code"])] = trim($row_clnt_rr_initials["clnt_rr1"]);
	}
	
	//show_array($arr_clnt_rr_initials);
	
	//ARRAY OF JUST INITIALS
	$arr_list_sales = array();
	foreach ($arr_clnt_rr_initials as $k=>$v) {
			if ($v != '') {
				$arr_list_sales[$v] = $v;
			}
	} 
	
	//ARRAY OF INITIALS, ID
	$arr_initials_id = array();
	foreach($arr_list_sales as $k=>$v) {
		$arr_initials_id[$v] = db_single_val("select ID as single_val from users where Initials = '".$v."'");
	}
	//show_array($arr_initials_id);
	
	//ARRAY OF FINALIZED SUBMISSIONS
	$arr_isfinal = array();
	foreach($arr_initials_id as $k=>$v) {
		$count = db_single_val("select count(*) as single_val from pay_analyst_allocations where pay_sales_id = '".$v."' and pay_final = 1 and pay_qtr = '".$sel_qtr."' and pay_year = '".$sel_year."'");
		if ($count > 0) {
			$arr_isfinal[$k] = 'Yes';
		} else {
			$arr_isfinal[$k] = 'No';
		}
	}
	//show_array($arr_isfinal);
	
	$str_sales = implode(",",$arr_list_sales);
	$str_sales = str_replace(",",'","',$str_sales);
	$str_sales = '"'.$str_sales.'"';
	//echo $str_sales;
	//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	//PRIMARY REP DATA

		//Get lookup relevant client codes from client master (internal) for verification
		$qry_primary_clients = "SELECT DISTINCT (a.clnt_code) as sole_client
																			FROM int_clnt_clients a
																		WHERE (
																							(
																							trim(a.clnt_rr1) != ''
																							AND trim(a.clnt_rr2) = ''
																							)
																					)
																		ORDER BY a.clnt_name";
		//xdebug("qry_primary_clients",$qry_primary_clients);
		$result_primary_clients = mysql_query($qry_primary_clients) or die (tdw_mysql_error($qry_primary_clients));
		$arr_sole_clnts = array();
		while ( $row_sole_clients = mysql_fetch_array($result_primary_clients) ) 
		{
			$arr_sole_clnts[$row_sole_clients["sole_client"]] = $row_sole_clients["sole_client"];
		}
	
		
		$arr_sole_clnts_more = array();		
		$qry_primary_clients_more = "SELECT distinct(concat(trad_advisor_code,'^',trad_rr)) as sole_clients
																	FROM mry_comm_rr_trades 
																	WHERE trad_trade_date between '".$arr_quarter_brok_dates[0]."' AND '".$arr_quarter_brok_dates[1]."'
																		AND trad_is_cancelled = 0
																		AND trad_rr like '0%'
  																	order by trad_advisor_code, trad_rr";
		$result_primary_clients_more = mysql_query($qry_primary_clients_more) or die (tdw_mysql_error($qry_primary_clients_more));
		while ( $row_primary_clients_more = mysql_fetch_array($result_primary_clients_more) ) 
		{
			$arr_sole_clnts_more[$row_primary_clients_more["sole_clients"]] = $row_primary_clients_more["sole_clients"];
	  }	  
    
	  //show_array($arr_sole_clnts_more);	
		foreach ($arr_sole_clnts_more as $k=>$v) {
			$arr_cval = explode("^",$v);
			if (!in_array($arr_cval[0],$arr_sole_clnts)) {
			 $arr_sole_clnts[$v] = $v;
			 //echo "This was not picked up ".$arr_cval[0]."<br>";
			} 
		}
		
	
		$str_sole_clnts = implode(",",$arr_sole_clnts);
		$str_sole_clnts = "'".str_replace(",","','",$str_sole_clnts)."'";
		
		//echo $str_sole_clnts;
			
	//COMMISSION
	$arr_comm_for_qtr_comm = array();
	$qry_comm_for_qtr = "SELECT trad_advisor_code, sum(trad_commission) as commission 
												FROM mry_comm_rr_trades 
												WHERE trad_trade_date between '".$arr_quarter_brok_dates[0]."' AND '".$arr_quarter_brok_dates[1]."'
													AND trad_advisor_code in (".$str_sole_clnts.")
													AND trad_is_cancelled = 0
												GROUP by trad_advisor_code";
	$result_comm_for_qtr = mysql_query($qry_comm_for_qtr) or die (tdw_mysql_error($qry_comm_for_qtr));
	while ( $row_comm_for_qtr = mysql_fetch_array($result_comm_for_qtr) ) 
	{
		$arr_comm_for_qtr_comm[$row_comm_for_qtr["trad_advisor_code"]] = $row_comm_for_qtr["commission"];
	}
  
	//show_array($arr_comm_for_qtr_comm);	
	
	////////////////////////////////////////////////////////////////////////////////////////////////////
	$arr_comm_for_qtr_comm_by_rr = array();
	$qry_comm_for_qtr_by_rr = "SELECT trad_advisor_code, sum(trad_commission) as commission , trad_rr
												FROM mry_comm_rr_trades 
												WHERE trad_trade_date between '".$arr_quarter_brok_dates[0]."' AND '".$arr_quarter_brok_dates[1]."'
													AND trad_advisor_code in (".$str_sole_clnts.")
													AND trad_is_cancelled = 0
												GROUP by trad_advisor_code, trad_rr";
	$result_comm_for_qtr_by_rr = mysql_query($qry_comm_for_qtr_by_rr) or die (tdw_mysql_error($qry_comm_for_qtr_by_rr));
	while ( $row_comm_for_qtr_by_rr = mysql_fetch_array($result_comm_for_qtr_by_rr) ) 
	{
		$arr_comm_for_qtr_comm_by_rr[$row_comm_for_qtr_by_rr["trad_advisor_code"]."^".$row_comm_for_qtr_by_rr["trad_rr"]] = $row_comm_for_qtr_by_rr["commission"];
	}
	////////////////////////////////////////////////////////////////////////////////////////////////////
	//show_array($arr_comm_for_qtr_comm_by_rr);	

	
	//CHECKS
	$arr_comm_for_qtr_chek = array();
	$qry_comm_for_qtr = "SELECT chek_advisor, sum(chek_amount) as commission  
												FROM chk_chek_payments_etc 
												WHERE chek_date between '".$arr_quarter_cal_dates[0]."' AND '".$arr_quarter_cal_dates[1]."' 
													AND chek_isactive = 1
													AND chek_advisor in (".$str_sole_clnts.")
												GROUP BY chek_advisor";
	$result_comm_for_qtr = mysql_query($qry_comm_for_qtr) or die (tdw_mysql_error($qry_comm_for_qtr));
	while ( $row_comm_for_qtr = mysql_fetch_array($result_comm_for_qtr) ) 
	{
		$arr_comm_for_qtr_chek[$row_comm_for_qtr["chek_advisor"]] = $row_comm_for_qtr["commission"];
	}

	//incorporate checks into comm array
	$arr_composite_primary = array();
	$arr_tmp_processed = array();
	foreach ($arr_comm_for_qtr_comm as $code=>$comm) {
		if (array_key_exists($code, $arr_comm_for_qtr_chek)) {
			$arr_composite_primary[$code] = $arr_comm_for_qtr_chek[$code] + $comm;
			$arr_tmp_processed[] = $code;
		} else {
			$arr_composite_primary[$code] = $comm;
		} 
	}
	
	foreach ($arr_comm_for_qtr_chek as $code=>$comm) {
		if (!in_array($code, $arr_tmp_processed)) {
			$arr_composite_primary[$code] = $comm;
		} 
	}
	//show_array($arr_composite_primary);

	//BY RR repeating from above
	$arr_composite_primary_by_rr = array();
	$arr_tmp_processed_by_rr = array();
	foreach ($arr_comm_for_qtr_comm_by_rr as $code_by_rr=>$comm) {
		$arr_code_rr = explode("^", $code_by_rr);
		if (array_key_exists($arr_code_rr[0], $arr_comm_for_qtr_chek)) {
			$arr_composite_primary_by_rr[$code_by_rr] = $arr_comm_for_qtr_chek[$arr_code_rr[0]] + $comm;
			$arr_tmp_processed_by_rr[] = $arr_code_rr[0];
		} else {
			$arr_composite_primary_by_rr[$code_by_rr] = $comm;
		} 
	}

	//show_array($arr_tmp_processed_by_rr);
	
	foreach ($arr_comm_for_qtr_chek as $code=>$comm) {
		if (!in_array($code, $arr_tmp_processed_by_rr)) {
		     $contruct_code_rr = $code . "^" . get_rep_for_client ($arr_client_rrs, $code);
			   if (array_key_exists($contruct_code_rr,$arr_composite_primary_by_rr)) {
				   $arr_composite_primary_by_rr[$contruct_code_rr] = $arr_composite_primary_by_rr[$contruct_code_rr] + $comm;
				 } else {
				   //echo "Added ".$contruct_code_rr."<br>";
				   $arr_composite_primary_by_rr[$contruct_code_rr] = $comm;
				 }
		} 
	}

	//show_array($arr_composite_primary_by_rr);





	//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

	//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	//SHARED SECTION
														
		//Get lookup relevant client codes from client master (internal) for verification
		$qry_shared_clients = "SELECT DISTINCT (a.clnt_code) as shrd_client
																			FROM int_clnt_clients a, Users b
																		WHERE (
																							(
																							trim(a.clnt_rr1) != ''
																							AND trim(a.clnt_rr2) != ''
																							)
																					)
																		ORDER BY a.clnt_name";
		//xdebug("qry_shared_clients",$qry_shared_clients);
		$result_shared_clients = mysql_query($qry_shared_clients) or die (tdw_mysql_error($qry_shared_clients));
		$arr_shrd_clnts = array();
		while ( $row_shrd_clients = mysql_fetch_array($result_shared_clients) ) 
		{
			$arr_shrd_clnts[$row_shrd_clients["shrd_client"]] = $row_shrd_clients["shrd_client"];
		}
	
		$str_shrd_clnts = implode(",",$arr_shrd_clnts);
		$str_shrd_clnts = "'".str_replace(",","','",$str_shrd_clnts)."'";
		//echo "<br>".$str_shrd_clnts;
		//show_array($arr_rel_shrd_clnts);
		//xdebug("str_shrd_clnts",$str_shrd_clnts);
		
		//00000000000000000000000000000000000000000000000000000000000000000000000000000000
		//Now get the trad commissions for the clients for the selected period
		$arr_comm_shrd = array();
		$qry_comm_shrd = "SELECT trad_advisor_code, sum( trad_commission ) as trad_comm 
													 FROM mry_comm_rr_trades 
													 WHERE trad_trade_date between '".$arr_quarter_brok_dates[0]."' AND '".$arr_quarter_brok_dates[1]."'
													 AND trad_is_cancelled = 0
													 AND trad_advisor_code in (".$str_shrd_clnts.")
													 GROUP BY trad_advisor_code
													 ORDER BY trad_advisor_code";
		//xdebug("qry_day_comm_shrd",$qry_day_comm_shrd);
		$result_comm_shrd = mysql_query($qry_comm_shrd) or die (tdw_mysql_error($qry_comm_shrd));
		while ( $row_comm_shrd = mysql_fetch_array($result_comm_shrd) ) 
		{
			$arr_comm_shrd[$row_comm_shrd["trad_advisor_code"]] = $row_comm_shrd["trad_comm"];
		}
		//show_array($arr_comm_shrd);
	
		//00000000000000000000000000000000000000000000000000000000000000000000000000000000
		//Now get the check commissions for the clients for the selected period
		$arr_check_shrd = array();
		$qry_check_shrd =     "SELECT sum(a.chek_amount) as total_checks, a.chek_advisor
													 FROM chk_chek_payments_etc a
													 WHERE chek_date between '".$arr_quarter_cal_dates[0]."' AND '".$arr_quarter_cal_dates[1]."' 
															AND a.chek_isactive = 1 
															AND a.chek_advisor in (".$str_shrd_clnts.")
													 GROUP BY a.chek_advisor
													 ORDER BY a.chek_advisor";
		//xdebug("qry_check_shrd",$qry_check_shrd);
		$result_check_shrd = mysql_query($qry_check_shrd) or die (tdw_mysql_error($qry_check_shrd));
		while ( $row_check_shrd = mysql_fetch_array($result_check_shrd) ) 
		{
			$arr_check_shrd[$row_check_shrd["chek_advisor"]] = $row_check_shrd["total_checks"];
		}
		//$arr_check_shrd['ZZZZ'] = 99999;
		//show_array($arr_check_shrd);
	
		//incorporate checks & comm
		$arr_composite_shared = array();
		$arr_tmp_processed = array();
		foreach ($arr_comm_shrd as $code=>$comm) {
			if (array_key_exists($code, $arr_check_shrd)) {
				$arr_composite_shared[$code] = $arr_check_shrd[$code] + $comm;
				$arr_tmp_processed[] = $code;
			} else {
				$arr_composite_shared[$code] = $comm;
			} 
		}
		
		foreach ($arr_check_shrd as $code=>$comm) {
			if (!in_array($code, $arr_tmp_processed)) {
				$arr_composite_shared[$code] = $comm;
			} 
		}
		
		//show_array($arr_composite_shared);

	//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

	function getsole($initials) {
	global $arr_composite_primary, $arr_clnt_rr_initials;
		$total = 0;
		foreach($arr_clnt_rr_initials as $clnt=>$rr) {
			if ($rr == $initials) {
				$total = $total + $arr_composite_primary[$clnt];
			}
		}
		return $total;
	}
	
	//show_array($arr_composite_primary_by_rr);
	//show_array($arr_clnt_rr_initials);

	function getsole_by_rr($initials) {  //get_rr_num_for_initials
	global $arr_composite_primary_by_rr;
		$total = 0;
		$rr_num = get_rr_num_for_initials($initials);
		//xdebug("rr_num",$rr_num);
		foreach($arr_composite_primary_by_rr as $clnt_rr=>$comm) {
			$arr_code_rr = explode("^",$clnt_rr);
			//echo $construct_code_rr;
			if ($arr_code_rr[1] == $rr_num) {
				//echo $construct_code_rr;
				$total = $total + $arr_composite_primary_by_rr[$clnt_rr];
			}
		}
		return $total;
	}
	
	
	function getsole_new ($rep_to_process) {
	  global $sel_qtr, $sel_year;
		include('pay_analyst_new_inc_main.php');
		include('pay_analyst_new_inc_main_more.php');
		$arr_quarter_brok_dates = get_quarter_dates ($sel_qtr, $sel_year);
		$arr_quarter_cal_dates = get_quarter_dates ($sel_qtr, $sel_year, "C");
		foreach ($arr_master_clnt_rr as $k=>$v) {
				$total_commission = $arr_composite_primary[$k];  //arr_composite_primary
				$val_total_commission = $val_total_commission + $total_commission;
		}
		return $val_total_commission;
	}

	function getshrd($initials) {
	global $arr_composite_shared, $arr_clnt_rr_initials;
		$total = 0;
		foreach($arr_clnt_rr_initials as $clnt=>$rr) {
			if ($rr == $initials) {
				$total = $total + $arr_composite_shared[$clnt];
			}
		}
		return $total;
	}
	
 //print_r($arr_composite_primary);	
 //print_r($arr_clnt_rr_initials);
	// ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ 
	//SHOW THE DATA
	?>
		<div id="notify">&nbsp;</div>
		<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
					<!--TABLE 2 START-->
					<script language="JavaScript" src="includes/javascript/sorttable.js" type="text/javascript"></script>

					<table class="sortable" preserve_style="cell" width="100%"  border="0" cellspacing="1" cellpadding="1">
						<tr>
						  <td width="28"> # </td>
							<td width="200">Name</td>
							<td width="80">Period</td>
							<td width="130">Sole Total</td>
							<td width="130">Shrd Total (RR1)</td>
							<td width="100">Finalized</td>
							<td width="100">Unlock</td>
							<td width="100">Send Msg</td>
							<td>&nbsp;</td>
						</tr>
	<?
		$arr_valid_salesppl = create_arr("select user_id as v from pay_analyst_users", 1);

	
		$arr_name_sales = create_arr("select a.Initials as k, concat(a.Firstname, ' ', a.Lastname) as v 
		                              from users a, pay_analyst_users b
																	WHERE a.ID = b.user_id
																	order by a.Lastname", 2);
		//show_array($arr_name_sales);
		$scount = 1;
		foreach($arr_name_sales as $i=>$n) {
		?>
						<tr <? if ($scount % 2) { echo "class='trlight'"; } else { echo "class='trdark'";}?>>
						  <td> <?=$scount?> </td>
							<td><?=$n?></td>
							<td><?="Q".$sel_qtr." ".$sel_year?></td>
							<td align="right">$<?=number_format(getsole_new(get_rr_num_for_initials($i)),2,'.',',')?>&nbsp;&nbsp;&nbsp;</td>
							<td align="right">$<?=number_format(getshrd($i),2,'.',',')?>&nbsp;&nbsp;&nbsp;</td>
							<?
							if ($arr_isfinal[$i] == 'Yes'){
								echo "<td id='a".$scount."'>&nbsp;&nbsp;&nbsp;<strong><font color='green'>Yes</font></strong></td>";
							} else {
								echo "<td id='a".$scount."'>&nbsp;&nbsp;&nbsp;<strong><font color='red'>No</font></strong></td>";
							}
							?>
							<?
							if ($arr_isfinal[$i] == 'Yes'){
								echo '<td id="b'.$scount.'">&nbsp;&nbsp;&nbsp;<a href="./#" onclick="javascript:procx(\'b^'.$arr_initials_id[$i]."^".$sel_qtr."^".$sel_year."^".$scount."^".str_replace("'","",$n)."');return false;\">Unlock for Edit</a></td>";
							} else {
								echo '<td id="b'.$scount.'">&nbsp;&nbsp;&nbsp;</td>';
							}
							?>
							<?
							if ($arr_isfinal[$i] == 'Yes'){
								echo '<td id="c'.$scount.'">&nbsp;&nbsp;&nbsp;</td>';
							} else {
								echo '<td id="c'.$scount.'">&nbsp;&nbsp;&nbsp;<a href="./#" onclick="javascript:procx(\'c^'.$arr_initials_id[$i]."^".$sel_qtr."^".$sel_year."^".$scount."^".str_replace("'","",$n).'\');return false;">Send</a></td>';
							}
							?>
							<td>&nbsp;</td>
						</tr>
		<?
		$scount = $scount + 1;
		}
		?>
					</table>
				</td>
			</tr>
		</table>
		<?
	// ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ ++ 

	//@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@--@@
} else {
	echo "&nbsp;&nbsp;Please select Quarter and Year.<br /><br />";
}

tep();
?>