<html>
<head>

<?
  include('includes/dbconnect.php');
  include('includes/global.php');
  include('includes/functions.php');
	
?>
<script language="JavaScript" src="includes/prototype/prototype.js"></script>
<script language="javascript">

function getComment(cid)
{
	var btnid = "btn_" + cid;
	var divid = "comment_" + cid;

	if ($(btnid).src == 'http://192.168.20.63/tdw/images/lf_v1/collapse_b.png') {
							$(divid).innerHTML = "";
							$(divid).style.visibility = "hidden";
							$(divid).style.display = "none";
							$(btnid).src = 'images/lf_v1/expand_b.png';
	} else {
	
		var url = 'http://192.168.20.63/tdw/rep_client_master_ajx_ajx.php';
		var pars = 'user_id=<?=$user_id?>';
		pars = pars + '&mod_request=comment';
		pars = pars + '&cid=' + cid;
		pars = pars + '&req_ajax=1';
		var ran_number= Math.random()*5; 
		pars = pars + '&xrand=' + ran_number;
	
			new Ajax.Request
			(
				url,   
				{     
					method:'get', 
					parameters:pars,    
					onSuccess: 
						function(transport){       
							var response = "";
							response = transport.responseText; 
							
							$(divid).style.visibility = "visible";
							$(divid).style.display = "block";
      
							$(divid).innerHTML = response;
							$(btnid).src = 'images/lf_v1/collapse_b.png';
							
						},     
					onFailure: 
					function(){ showdebug('Error accessing TDW Server.') }
				}
			);	
	}
	
}

</script>

<?	
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&

$trade_date_to_process = previous_business_day();
$previous_year_date = get_date_previous_year($trade_date_to_process);

//print_r($_GET);
//exit;

//[thiscriteria] => filter_tier [valcriteria] => 4
//[thiscriteria] => filter_name [valcriteria] => A
//[thiscriteria] => filter_reps [valcriteria] => AF
//[thiscriteria] => filter_trdr [valcriteria] => TS 
//[thiscriteria] => filter_type [valcriteria] => AP 


if ($_GET && !$proc_user) {

	$process_tier = 0;

//--------------------------------------------------------------
	if ($thiscriteria == 'filter_tier') {
		$process_tier = 1;
	}
//--------------------------------------------------------------
	if ($show_deleted == 1) {
		$str_show_deleted = " clnt_isactive like '%' ";
	} else {
		$str_show_deleted = " clnt_isactive != '0' ";
	}
//--------------------------------------------------------------
	if ($thiscriteria == 'filter_name') {
		$strltr = $valcriteria;	
	} else {
		$strltr = "";	
	}
//--------------------------------------------------------------
	if ($thiscriteria == 'filter_reps') {
		$strrep = " and (clnt_rr1='".$valcriteria."' OR clnt_rr2='".$valcriteria."') ";	
	} else {
		$strrep = " ";	
	}
//--------------------------------------------------------------
	if ($thiscriteria == 'filter_trdr') {
		$strtrdr = " and clnt_trader ='".$valcriteria."' ";	
	} else {
		$strtrdr = " ";	
	}
//--------------------------------------------------------------
	if ($thiscriteria == 'filter_type') {
		if ($valcriteria == 'AP') {
			$strtype = " and clnt_status like 'P%' ";	
		} else {
			$strtype = " and clnt_status ='".$valcriteria."' ";	
		}
	} else {
		$strtype = " ";	
	}
//--------------------------------------------------------------
}

//rep filter criteria
$str_rep_filter_criteria = " AND ( (clnt_rr1 = '".$user_initials."' OR clnt_rr2 = '".$user_initials."') 
                                   OR 
																	 (clnt_rr1 = '' AND clnt_rr2 = '' AND clnt_status like 'P%')
																	 OR 
																	 (clnt_rr1 IS NULL AND clnt_rr2 IS NULL AND clnt_status LIKE 'P%')
																	 OR
																	 (clnt_rr1 like '%')
																	 OR 
																	 (  (clnt_rr1 != NULL or clnt_rr1 != '' OR clnt_rr2= NULL or clnt_rr2 != '') AND clnt_status = 'P1' ) 
																 ) ";


//[thiscriteria] => filter_name [valcriteria] => E


if ($req_ajax) {
	$query_clients = "SELECT * from int_clnt_clients where ".$str_show_deleted." and clnt_name like '".$strltr."%' ". $strrep . $strtrdr. $strtype.$str_rep_filter_criteria." order by clnt_name";
	$query_money = "SELECT clnt_code from int_clnt_clients where ".$str_show_deleted." and clnt_name like '".$strltr."%' ". $strrep . $strtrdr. $strtype.$str_rep_filter_criteria. " order by clnt_name";
} else {
	$query_clients = "SELECT * from int_clnt_clients where clnt_isactive != 0 ".$str_rep_filter_criteria." order by clnt_name";
	$query_money = "SELECT clnt_code from int_clnt_clients where clnt_isactive != 0 ".$str_rep_filter_criteria." order by clnt_name";
}


	$arr_subset_clients = array();
	$result_subset_clients = mysql_query($query_money) or die (tdw_mysql_error($query_money));
	while ( $row = mysql_fetch_array($result_subset_clients) ) {
	  $arr_subset_clients[] = $row["clnt_code"];
	}
	$str_subset_clients = implode(",",$arr_subset_clients);
	$str_subset_clients = "'".str_replace(",","','",$str_subset_clients)."'";


		//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
		if($action == "remove")
		{
			$query_delete = "UPDATE int_clnt_clients SET clnt_isactive = '0' WHERE clnt_auto_id = '$ID'";
			$result_delete = mysql_query($query_delete) or die(mysql_error());
			
			$qry_comment = "insert into int_clnt_clients_comments
								(auto_id, clnt_auto_id, clnt_comment, clnt_comment_by, clnt_timestamp, clnt_isactive) 
								values (
								NULL,
								'".$ID."',
								'DELETED',
								'".$user_id."',
								now(),
								1												
								)";
			$result_comment = mysql_query($qry_comment) or die (tdw_mysql_error($qry_comment));

		}

		if($action == "undelete")
		{
			$query_delete = "UPDATE int_clnt_clients SET clnt_isactive = '1' WHERE clnt_auto_id = '$ID'";
			$result_delete = mysql_query($query_delete) or die(mysql_error());

			$qry_comment = "insert into int_clnt_clients_comments
								(auto_id, clnt_auto_id, clnt_comment, clnt_comment_by, clnt_timestamp, clnt_isactive) 
								values (
								NULL,
								'".$ID."',
								'UNDELETED',
								'".$user_id."',
								now(),
								1												
								)";
			$result_comment = mysql_query($qry_comment) or die (tdw_mysql_error($qry_comment));
		}
		//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&




//===========================================================================================================================
//===========================================================================================================================
//===========================================================================================================================
//===========================================================================================================================

//now mtd comm
//get the start day of month for this
$global_qry_date_start_mtd = db_single_val("SELECT brk_start_date as single_val 
																			FROM `brk_brokerage_months` 
																			WHERE `brk_start_date` <= '".$trade_date_to_process."'
																			AND `brk_end_date` >= '".$trade_date_to_process."'");
//xdebug("global_qry_date_start_mtd",$global_qry_date_start_mtd);

$qry_mtd_comm = "SELECT trad_advisor_code, sum( trad_commission ) as trad_comm 
								 FROM mry_comm_rr_trades 
								 WHERE trad_trade_date between '".$global_qry_date_start_mtd."' and '".$trade_date_to_process."'
								 AND trad_is_cancelled =0
								 AND trad_advisor_code in (".$str_subset_clients.")
								 GROUP BY trad_advisor_code";
$result_mtd_comm = mysql_query($qry_mtd_comm) or die (tdw_mysql_error($qry_mtd_comm));
while ( $row_mtd_comm = mysql_fetch_array($result_mtd_comm) ) 
{
	$arr_mtd_comm[$row_mtd_comm["trad_advisor_code"]] = $row_mtd_comm["trad_comm"];
}
//show_array($arr_mtd_comm);

//get qtd values
//get quarter start date
$arr_qtr_start = array(1=>'Jan',2=>'Apr',3=>'Jul',4=>'Oct');

$arr_month_in_qtr = array('01'=>1,'02'=>1,'03'=>1,'04'=>2,'05'=>2,'06'=>2,'07'=>3,'08'=>3,'09'=>3,'10'=>4,'11'=>4,'12'=>4);

$qtr_start_val = $arr_qtr_start[$arr_month_in_qtr[substr($trade_date_to_process,5,2)]];
$year_to_process = substr($trade_date_to_process,0,4);
$global_qtr_start_date = db_single_val("SELECT brk_start_date as single_val 
																	FROM `brk_brokerage_months` 
																	WHERE brk_month = '".$qtr_start_val."'
																	  AND brk_year = '".$year_to_process."'");
//xdebug("qtr_start_val",$qtr_start_val);
//xdebug("year_to_process",$year_to_process);
//xdebug("global_qtr_start_date",$global_qtr_start_date);


$qry_qtd_comm = "SELECT trad_advisor_code, sum( trad_commission ) as trad_comm 
								 FROM mry_comm_rr_trades 
								 WHERE trad_trade_date between '".$global_qtr_start_date."' and '".$trade_date_to_process."'
								 AND trad_is_cancelled =0
								 AND trad_advisor_code in (".$str_subset_clients.")
								 GROUP BY trad_advisor_code";
//xdebug("qry_qtd_comm",$qry_qtd_comm);
$result_qtd_comm = mysql_query($qry_qtd_comm) or die (tdw_mysql_error($qry_qtd_comm));
while ( $row_qtd_comm = mysql_fetch_array($result_qtd_comm) ) 
{
	$arr_qtd_comm[$row_qtd_comm["trad_advisor_code"]] = $row_qtd_comm["trad_comm"];
}
//show_array($arr_qtd_comm);

//GET THE START OF THE YEAR
//xdebug("First Day of the current Brokerage Year",substr($trade_date_to_process,0,4));
$global_year_start_date = db_single_val("SELECT brk_start_date as single_val 
																		FROM `brk_brokerage_months` 
																		WHERE brk_month = 'Jan'
																	  AND brk_year = '".substr($trade_date_to_process,0,4)."'");
//now get ytd
$qry_ytd_comm = "SELECT trad_advisor_code, sum( trad_commission ) as trad_comm 
								 FROM mry_comm_rr_trades 
								 WHERE trad_trade_date between '".$global_year_start_date."' and '".$trade_date_to_process."'
								 AND trad_is_cancelled = 0
								 AND trad_advisor_code in (".$str_subset_clients.")
								 GROUP BY trad_advisor_code";
								 //removed 	WHERE trad_trade_date between '".substr($trade_date_to_process,0,4)."-01-01' and '".$trade_date_to_process."'
								 							 
//xdebug("qry_ytd_comm",$qry_ytd_comm);
$result_ytd_comm = mysql_query($qry_ytd_comm) or die (tdw_mysql_error($qry_ytd_comm));
while ( $row_ytd_comm = mysql_fetch_array($result_ytd_comm) ) 
{
	$arr_ytd_comm[$row_ytd_comm["trad_advisor_code"]] = $row_ytd_comm["trad_comm"];
}
//show_array($arr_ytd_comm);


//now get the check commissions for the clients for the qtd, mtd, ytd

//for the mtd
$qry_mtd_check = "SELECT sum(a.chek_amount) as total_checks, a.chek_advisor
									FROM chk_chek_payments_etc a
                   left join int_clnt_clients b on a.chek_advisor = b.clnt_code 
								  WHERE a.chek_date between '".substr($trade_date_to_process,0,8)."01"."' and '".$trade_date_to_process."' 
									  AND a.chek_isactive = 1
										AND a.chek_advisor in  (".$str_subset_clients.") 
								 GROUP BY a.chek_advisor";

$result_mtd_check = mysql_query($qry_mtd_check) or die (tdw_mysql_error($qry_mtd_check));
while ( $row_mtd_check = mysql_fetch_array($result_mtd_check) ) 
{
	$arr_mtd_check[$row_mtd_check["chek_advisor"]] = $row_mtd_check["total_checks"];
}

//show_array($arr_mtd_check);

//for the qtd

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

$z_arr_month_qtr = array("01"=>"1","02"=>"1","03"=>"1","04"=>"2","05"=>"2","06"=>"2","07"=>"3","08"=>"3","09"=>"3","10"=>"4","11"=>"4","12"=>"4",);

//xdebug("Q",$z_arr_month_qtr[substr($trade_date_to_process,5,2)]);
//xdebug("Y",substr($trade_date_to_process,0,4));

$z_qtr_dates = get_quarter_dates($z_arr_month_qtr[substr($trade_date_to_process,5,2)],substr($trade_date_to_process,0,4),"C");

$qry_qtd_check = "SELECT sum(a.chek_amount) as total_checks, a.chek_advisor
									FROM chk_chek_payments_etc a
                   left join int_clnt_clients b on a.chek_advisor = b.clnt_code 
								  WHERE a.chek_date between '".$z_qtr_dates[0]."' and '".$trade_date_to_process."'
									  AND a.chek_isactive = 1
										AND a.chek_advisor in  (".$str_subset_clients.") 
								 GROUP BY a.chek_advisor";
								 //(b.clnt_rr1 = '".$user_initials."' or b.clnt_rr2 = '".$user_initials."')  
//xdebug("qry_qtd_check",$qry_qtd_check);
$result_qtd_check = mysql_query($qry_qtd_check) or die (tdw_mysql_error($qry_qtd_check));
while ( $row_qtd_check = mysql_fetch_array($result_qtd_check) ) 
{
	$arr_qtd_check[$row_qtd_check["chek_advisor"]] = $row_qtd_check["total_checks"];
}

//show_array($arr_qtd_check);
//xdebug("qry_qtd_check",$qry_qtd_check);

//for the ytd
$qry_ytd_check = "SELECT sum(a.chek_amount) as total_checks, a.chek_advisor
									FROM chk_chek_payments_etc a
                   left join int_clnt_clients b on a.chek_advisor = b.clnt_code 
								  WHERE a.chek_date between '".substr($trade_date_to_process,0,4)."-01-01' and '".$trade_date_to_process."'
									  AND a.chek_isactive = 1
										AND a.chek_advisor in  (".$str_subset_clients.") 
								 GROUP BY a.chek_advisor";
								 //(b.clnt_rr1 = '".$user_initials."' or b.clnt_rr2 = '".$user_initials."')  
//xdebug("qry_ytd_check",$qry_ytd_check);
$result_ytd_check = mysql_query($qry_ytd_check) or die (tdw_mysql_error($qry_ytd_check));
while ( $row_ytd_check = mysql_fetch_array($result_ytd_check) ) 
{
	$arr_ytd_check[$row_ytd_check["chek_advisor"]] = $row_ytd_check["total_checks"];
}

//show_array($arr_ytd_check);
//xdebug("qry_day_check",$qry_day_check);
//===========================================================================================================================
//===========================================================================================================================
//===========================================================================================================================
//===========================================================================================================================







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

		//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++=
		$qry = "SELECT min( trad_trade_date ) as trad_trade_date, `trad_advisor_code` 
						FROM `mry_comm_rr_trades` 
						WHERE trad_advisor_code NOT LIKE '&%'
						GROUP BY trad_advisor_code";
		$result = mysql_query($qry) or die (tdw_mysql_error($qry));
		$arr_client_last_year = array();
		while ( $row = mysql_fetch_array($result) ) 
		{
			if (substr($row["trad_trade_date"],0,4) == substr($previous_year_date,0,4)) {
				$arr_client_last_year[$row["trad_advisor_code"]] = $row["trad_trade_date"];
			}
		}
		
		//show_array($arr_client_last_year);

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
		global $arr_prev_year, $arr_prev_year_shared;
			 if ($arr_prev_year[$clntval] == "") {
			 		if ($arr_prev_year_shared[$clntval] != "") {
						$pyc = $arr_prev_year_shared[$clntval];
						return $pyc;
					} else {
						$pyc = "";
						return $pyc;
					}
			 } else {
					$pyc = $arr_prev_year[$clntval];
					return $pyc;
			 }
		 }	
		
		//Get all data from table into an array
		$qry_prev_year = "SELECT yrt_advisor_code, yrt_commission 
											FROM yrt_yearly_total_lookup
											WHERE yrt_year = EXTRACT(YEAR FROM '".$previous_year_date."')
											GROUP BY yrt_advisor_code
											ORDER BY yrt_advisor_code";
		//xdebug('qry_prev_year',$qry_prev_year);
		$result_prev_year = mysql_query($qry_prev_year) or die (tdw_mysql_error($qry_prev_year));
		$arr_prev_year = array();
		while ( $row_prev_year = mysql_fetch_array($result_prev_year) ) 
		{
			$arr_prev_year[$row_prev_year["yrt_advisor_code"]] = $row_prev_year["yrt_commission"];
		}

//????????????????????????????????????????
$arr_name_for_id = array(); //[$row_comment["clnt_comment_by"]]
$qry_usr = "select ID, Fullname FROM users"; 
$result_usr = mysql_query($qry_usr) or die (tdw_mysql_error($qry_usr));
while ( $row_usr = mysql_fetch_array($result_usr) ) 
{
	$arr_name_for_id[$row_usr["ID"]] = $row_usr["Fullname"];
}

$arr_comment_count = array();;
$qry_comment_count = "select count(clnt_auto_id) as commcount, clnt_auto_id
											 FROM int_clnt_clients_comments 
											 WHERE clnt_isactive = 1
											 GROUP BY clnt_auto_id"; 
$result_comment_count = mysql_query($qry_comment_count) or die (tdw_mysql_error($qry_comment_count));
while ( $row_comment_count = mysql_fetch_array($result_comment_count) ) 
{
	$arr_comment_count[$row_comment_count["clnt_auto_id"]] = $row_comment_count["commcount"];
}
//tdw/images/lf_v1/expand.png
//show_array($arr_comment_count);

$arr_clnt_comment = array();
$qry_comment = "select auto_id, clnt_auto_id, clnt_comment, clnt_comment_by, clnt_timestamp, clnt_isactive  
               FROM int_clnt_clients_comments 
							 WHERE clnt_isactive = 1
							 ORDER BY clnt_auto_id, clnt_timestamp"; 
$result_comment = mysql_query($qry_comment) or die (tdw_mysql_error($qry_comment));
while ( $row_comment = mysql_fetch_array($result_comment) ) 
{
  $whowhen = "";
	
	$whowhen = "[".date('m/d/y h:ia',strtotime($row_comment["clnt_timestamp"]))." ".$arr_name_for_id[$row_comment["clnt_comment_by"]]."]<br>";
	$str_comment_data = str_replace('"','',$row_comment["clnt_comment"]);
	
	if ($arr_comment_count[$row_comment["clnt_auto_id"]] > 1) {
		$arr_clnt_comment[$row_comment["clnt_auto_id"]] = "<img id='btn_".$row_comment["clnt_auto_id"]."' src='images/lf_v1/expand_b.png' border='0' onclick='getComment(".$row_comment["clnt_auto_id"].");'>". 
																											"  " .
																											$whowhen.$str_comment_data.
																											"<div id='comment_".$row_comment["clnt_auto_id"]."'></div>";
	} else {
		$arr_clnt_comment[$row_comment["clnt_auto_id"]] = $whowhen.$str_comment_data; //"&#9658;".
	}
}

//show_array($arr_clnt_comment);
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&

if ($proc_user) {
	$val_user_id = $proc_user;
} else {
	$val_user_id = $user_id;
} 

?>
<link rel="stylesheet" type="text/css" href="includes/styles.css">


<script language="JavaScript" src="includes/js/popup.js"></script>

<style type="text/css">
<!--
.headrow {
font-family: Verdana;
font-size: 11px;
font-weight: bold;
color: #000099;
text-decoration: none;
}
tr.ztrlight {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
	color: #000000;
	background-color: #FFFFFF;
}
tr.ztrdark {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
	color: #000000;
	background-color: #EEEEEE;
}
-->
</style>
<script language="JavaScript" src="includes/javascript/gs_sortable.js" type="text/javascript"></script>
<script type="text/javascript">
<!--
	var TSort_Data = new Array ('sort_this', '','s', 's', 's', 's', 's','s', 's', 'g', 'g','s','');
	var TSort_Classes = new Array ('ztrlight', 'ztrdark')
	var TSort_Icons = new Array (' &#923;',' V');
	tsRegister();
// -->
</script> 
</head>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0">
    <table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
					<!--TABLE 2 START-->
					<table id="sort_this" width="100%"  border="0" cellspacing="1" cellpadding="1"><!-- class="sortable" preserve_style="cell"-->
						<thead>
							<tr class="headrow">
							<td width="30">&nbsp;</td>
							<td width="250">Client Name </td>
							<td width="80">Code </td>
							<td width="80">T'ware </td>
							<td width="40">RR1 </td>
							<td width="40">RR2 </td>
							<td width="45">Trdr. </td>
							<td width="55">Status </td>
							<td width="85">
              <?
              if ($mqy_sel == 'M') {
                echo 'Curr. MTD';
              } else if ($mqy_sel == 'Q') {
                echo 'Curr. QTD';
              } else {
                echo date('Y').' YTD';
              }
							?> </td>
							<td width="75">Last Year </td>
							<td width="65">Tier </td>
							<td width="300">Comments</td>
							<td>&nbsp;</td>
							</tr>
            </thead>
						<?

						$result = mysql_query($query_clients) or die(mysql_error());
						//xdebug("query_clients",$query_clients);
						//exit;
						$count_row = 0;
						while ( $row = mysql_fetch_array($result) ) 
						{
	
								//GET TIER
								if ($row["clnt_status"] == 'A') {
								
										$clnt_tieroverride = 0;
										if ($arr_clnt_tier_count[$row["clnt_code"]] > 0) {
										//$arr_clnt_tier_count[$row_tier["clnt_code"]] = $arr_clnt_tier_count[$row_tier["clnt_code"]] + 1;
										//$arr_clnt_tier[$row_tier["clnt_code"]] = $row_tier["clnt_tier"];
												$month_val = 1;
												$ann_val = get_previous_yr_data($row["clnt_code"]);
												$ann_string = "&#9658;"."Tier Modified."."<br>";
												$val_tier = $arr_clnt_tier[$row["clnt_code"]];
												$clnt_tieroverride = 1;
										} else {
											if (array_key_exists($row["clnt_code"],$arr_client_last_year)) {
												$month_val = (int)substr($arr_client_last_year[$row["clnt_code"]],5,2);
												if ($month_val != 1 && get_previous_yr_data($row["clnt_code"]) != 0) {
													$ann_val = (get_previous_yr_data($row["clnt_code"])*12)/(13-$month_val);
													$ann_string = "Annualized $". get_previous_yr_data($row["clnt_code"])." with a start date of ". format_date_ymd_to_mdy($arr_client_last_year[$row["clnt_code"]])."<br>";
													$val_tier = get_tier($ann_val);
												} else {
													$ann_val = (get_previous_yr_data($row["clnt_code"])*12)/(13-$month_val);
													$ann_string = "";
													$val_tier = get_tier($ann_val);
												}
											} else {
												$month_val = 1;
												$ann_val = get_previous_yr_data($row["clnt_code"]);
												$ann_string = "";
												$val_tier = get_tier($ann_val);
											}
										}
									$str_tier_show = "<td>&nbsp;".$val_tier. " <img src='images/tier/".$val_tier.".png' height='13' border='0'></td>";
								} else {
									$str_tier_show = "<td></td>";
								}
	
	
	
	
						
						if ($row["clnt_alt_code"]=='INACTIVE') {
						$str_clnt_code = " "; //"<font color=red>".$row["clnt_alt_code"].'</font>';
						} else {
						$str_clnt_code = $row["clnt_alt_code"];
						}

						if ($count_row%2 == 0) {
							$rowclass = ' class="ztrdark"';
						} else {
							$rowclass = ' class="ztrlight"';
						}


						$short_td_string = "";
						
						if ($mqy_sel == 'M') {
						$short_td_string = '<td align="right">'. number_format(($arr_mtd_comm[$row["clnt_code"]] + $arr_mtd_check[$row["clnt_code"]]),0,'',",") .'&nbsp;</td>';
						} else if ($mqy_sel == 'Q') {
						$short_td_string = '<td align="right">'. number_format(($arr_qtd_comm[$row["clnt_code"]] + $arr_qtd_check[$row["clnt_code"]]),0,'',",") .'&nbsp;</td>';
						} else {
						$short_td_string = '<td align="right">'. number_format(($arr_ytd_comm[$row["clnt_code"]] + $arr_ytd_check[$row["clnt_code"]]),0,'',",") .'&nbsp;</td>';
						}

								if ($process_tier == 1) {
								
									if ($row["clnt_status"] == 'A') {
									
											if ($val_tier == $valcriteria) {
											
													//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
													// Show only prospects assigned to the user or show image 3stars
													$show_rep_code_1 = "";
													$show_rep_code_2 = "";
													
													$hide_money_tier = 1;
													if ($row["clnt_rr1"] == $user_initials || $row["clnt_rr2"] == $user_initials) {
														$hide_money_tier = 0;
													}
													
													if ($row["clnt_rr1"] == $user_initials) { // && $row["clnt_status"] == 'P1'
														$show_rep_code_1 = $row["clnt_rr1"];
													} else if ($row["clnt_rr1"] != $user_initials && trim($row["clnt_rr1"]) != '') { // && $row["clnt_status"] == 'P1'
														$show_rep_code_1 = '<img src="images/3star.png" alt="Not assigned to you">'; //['.$user_initials.']['.$row["clnt_rr1"].']
													} else {
														$show_rep_code_1 = $row["clnt_rr1"];
													}
													if ($row["clnt_rr2"] == $user_initials) { // && $row["clnt_status"] == 'P1'
														$show_rep_code_2 = $row["clnt_rr2"];
													} else if ($row["clnt_rr2"] != $user_initials && trim($row["clnt_rr2"]) != '') { // && $row["clnt_status"] == 'P1'
														$show_rep_code_2 = '<img src="images/3star.png" alt="Not assigned to you">'; //['.$user_initials.']['.$show_rep_code_2.']
													} else {
														$show_rep_code_2 = $row["clnt_rr2"];
													}
													//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

											
											
											
											
															if ($row["clnt_isactive"] != 0) {
															?>
																	<tr <?=$rowclass?>>
																	<td> </td>
																	<td>&nbsp;<?=trim($row["clnt_name"])?></td>
																	<td>&nbsp;<?=$row["clnt_code"]?></td>
																	<td>&nbsp;<?=$str_clnt_code?></td>

                                  <td>&nbsp;<?=$show_rep_code_1?></td>
                                  <td>&nbsp;<?=$show_rep_code_2?></td>
                                  <td>&nbsp;<?=$row["clnt_trader"]?></td>
                                  <td>&nbsp;<?=$row["clnt_status"]?></td>
                                  
                                  <?
                                  if ($hide_money_tier == 1) {
                                  ?>
                                  <td></td><td></td><td></td>
                                  <? } else {
                                  ?>
                                  <?=$short_td_string?>
                                  <td align="right"><?=number_format(get_previous_yr_data($row["clnt_code"]),0,'',",")?>&nbsp;</td>
                                  <?=$str_tier_show?>
                                  <?
                                  }
                                  ?>
																	<td><?=$arr_clnt_comment[$row["clnt_auto_id"]]?></td>
																	<td></td>
																	</tr>							
															<?						
															} else {
															?>
																	<tr <?=$rowclass?>>
																	<td nowrap>&nbsp; >> </td>
																	<td>&nbsp;<?=trim($row["clnt_name"])?></td>
																	<td>&nbsp;<?=$row["clnt_code"]?></td>
																	<td>&nbsp;<?=$str_clnt_code?></td>

                                  <td>&nbsp;<?=$show_rep_code_1?></td>
                                  <td>&nbsp;<?=$show_rep_code_2?></td>
                                  <td>&nbsp;<?=$row["clnt_trader"]?></td>
                                  <td>&nbsp;<?=$row["clnt_status"]?></td>
                                  
                                  <?
                                  if ($hide_money_tier == 1) {
                                  ?>
                                  <td></td><td></td><td></td>
                                  <? } else {
                                  ?>
                                  <?=$short_td_string?>
                                  <td align="right"><?=number_format(get_previous_yr_data($row["clnt_code"]),0,'',",")?>&nbsp;</td>
                                  <?=$str_tier_show?>
                                  <?
                                  }
                                  ?>
																	<td><?=$arr_clnt_comment[$row["clnt_auto_id"]]?></td>
																	<td></td>
																	</tr>							
															<?						
															}
															$count_row = $count_row + 1;
											}
									}
								} else {
								
													//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
													// Show only prospects assigned to the user or show image 3stars
													$show_rep_code_1 = "";
													$show_rep_code_2 = "";
													
													$hide_money_tier = 1;
													if ($row["clnt_rr1"] == $user_initials || $row["clnt_rr2"] == $user_initials) {
														$hide_money_tier = 0;
													}
													
													if ($row["clnt_rr1"] == $user_initials) { // && $row["clnt_status"] == 'P1'
														$show_rep_code_1 = $row["clnt_rr1"];
													} else if ($row["clnt_rr1"] != $user_initials && trim($row["clnt_rr1"]) != '') { // && $row["clnt_status"] == 'P1'
														$show_rep_code_1 = '<img src="images/3star.png" alt="Not assigned to you">'; //['.$user_initials.']['.$row["clnt_rr1"].']
													} else {
														$show_rep_code_1 = $row["clnt_rr1"];
													}
													if ($row["clnt_rr2"] == $user_initials) { // && $row["clnt_status"] == 'P1'
														$show_rep_code_2 = $row["clnt_rr2"];
													} else if ($row["clnt_rr2"] != $user_initials && trim($row["clnt_rr2"]) != '') { // && $row["clnt_status"] == 'P1'
														$show_rep_code_2 = '<img src="images/3star.png" alt="Not assigned to you">'; //['.$user_initials.']['.$show_rep_code_2.']
													} else {
														$show_rep_code_2 = $row["clnt_rr2"];
													}
													//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
													
													if ($row["clnt_isactive"]  != 0) { // == 1
													?>
															<tr <?=$rowclass?>>
															<td> </td>
															<td>&nbsp;<?=trim($row["clnt_name"])?></td>
															<td>&nbsp;<?=$row["clnt_code"]?></td>
															<td>&nbsp;<?=$str_clnt_code?></td>

															<td>&nbsp;<?=$show_rep_code_1?></td>
															<td>&nbsp;<?=$show_rep_code_2?></td>
 															<td>&nbsp;<?=$row["clnt_trader"]?></td>
															<td>&nbsp;<?=$row["clnt_status"]?></td>
															
															<?
                              if ($hide_money_tier == 1) {
                              ?>
															<td></td><td></td><td></td>
															<? } else {
															?>
															<?=$short_td_string?>
															<td align="right"><?=number_format(get_previous_yr_data($row["clnt_code"]),0,'',",")?>&nbsp;</td>
															<?=$str_tier_show?>
                              <?
															}
															?>
															
                              
                              <td><?=$arr_clnt_comment[$row["clnt_auto_id"]]?></td>
															<td></td>
															</tr>							
													<?						
													} else {
													?>
															<tr <?=$rowclass?>>
															<td nowrap>&nbsp; >> </td>
															<td>&nbsp;<?=trim($row["clnt_name"])?></td>
															<td>&nbsp;<?=$row["clnt_code"]?></td>
															<td>&nbsp;<?=$str_clnt_code?></td>
															<td>&nbsp;<?=$row["clnt_rr1"]?></td>
															<td>&nbsp;<?=$row["clnt_rr2"]?></td>
															<td>&nbsp;<?=$row["clnt_trader"]?></td>
															<td>&nbsp;<?=$row["clnt_status"]?></td>
															
															<?
                              if ($hide_money_tier == 1) {
                              ?>
															<td></td><td></td><td></td>
															<? } else {
															?>
															<?=$short_td_string?>
															<td align="right"><?=number_format(get_previous_yr_data($row["clnt_code"]),0,'',",")?>&nbsp;</td>
															<?=$str_tier_show?>
                              <?
															}
															?>
															
                              
															<td><?=$arr_clnt_comment[$row["clnt_auto_id"]]?></td>
															<td></td>
															</tr>							
													<?						
													}
													$count_row = $count_row + 1;
								}
						}
						?>
					</table>

				</td>
			</tr>
		</table>
</body>
</html>