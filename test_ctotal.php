<?
include('./includes/functions.php');
include('./includes/generate_pdf.php');
include('./includes/global.php');
include('./includes/dbconnect.php');

$trade_date_to_process = previous_business_day();

function get_clnt_total_per_year ($year) {
		$qry = "select yrt_year, yrt_advisor_code, yrt_rr, yrt_commission from yrt_yearly_total_lookup where yrt_year = '".$year."'";
		$result = mysql_query($qry) or die(tdw_mysql_error($qry));
		$arr_year_total = array();
		while ( $row = mysql_fetch_array($result) )  {
		//echo $row["yrt_advisor_code"]." >> ".$row["yrt_rr"]." >> ".$row["yrt_commission"]."<br>";
			if ($arr_year_total[$row["yrt_advisor_code"]."^".$row["yrt_rr"]]) {
				$arr_year_total[$row["yrt_advisor_code"]."^".$row["yrt_rr"]] = $arr_year_total[$row["yrt_advisor_code"]] + $row["yrt_commission"];
			} else {
				$arr_year_total[$row["yrt_advisor_code"]."^".$row["yrt_rr"]] = $row["yrt_commission"];
			}
		}
		return $arr_year_total;
}


$arr_master_history = array();
$arr_2_years = array(substr($trade_date_to_process,0,4)-1,substr($trade_date_to_process,0,4)-2); //,substr($trade_date_to_process,0,4)-3
//show_array($arr_3_years);
foreach ($arr_2_years as $k=>$v) {
	$arr_master_history[$v] = get_clnt_total_per_year ($v);
}


//master array of client with rep number
//get all reps initials and rep numbers
$arr_reps_initials = array();
$qry = "SELECT distinct(clnt_rr1) as reps from int_clnt_clients where trim(clnt_rr1) != ''"; //, yrt_advisor_code, yrt_rr, yrt_commission from yrt_yearly_total_lookup where yrt_year = '".$year."'";
$result = mysql_query($qry) or die(tdw_mysql_error($qry));
$arr_prim_rep_year_total = 0;
while ( $row = mysql_fetch_array($result) )  {
	$arr_reps_initials[$row["reps"]] = $row["reps"];
}
$qry = "SELECT distinct(clnt_rr2) as reps from int_clnt_clients where trim(clnt_rr2) != ''"; //, yrt_advisor_code, yrt_rr, yrt_commission from yrt_yearly_total_lookup where yrt_year = '".$year."'";
$result = mysql_query($qry) or die(tdw_mysql_error($qry));
$arr_prim_rep_year_total = 0;
while ( $row = mysql_fetch_array($result) )  {
	$arr_reps_initials[$row["reps"]] = $row["reps"];
}

$str_initials = " ('". implode("','",$arr_reps_initials)  ."') ";  //  

$qry = "SELECT Initials, rr_num from users where trim(Initials) in ".$str_initials; //
$result = mysql_query($qry) or die(tdw_mysql_error($qry));
$arr_initials_rr_num = array();
while ( $row = mysql_fetch_array($result) )  {
	$arr_initials_rr_num[$row["Initials"]] = $row["rr_num"];
}

//shared reps number with initials
$qry = "SELECT a.srep_rrnum, trim(b.Initials) as initials FROM sls_sales_reps a left join users b on a.srep_user_id = b.ID where a.srep_isactive = 1  order by a.srep_rrnum";
$result = mysql_query($qry) or die(tdw_mysql_error($qry));
$arr_shrd_rr_num_initials = array();
$hold_val = "";
while ( $row = mysql_fetch_array($result) )  {
	if ($hold_val == "" || $hold_val != $row["srep_rrnum"]) {
		$arr_shrd_rr_num_initials[$row["srep_rrnum"]] = $row["initials"];
	} else {
		$arr_shrd_rr_num_initials[$row["srep_rrnum"]] = $arr_shrd_rr_num_initials[$row["srep_rrnum"]]."^".$row["initials"];
	}
	$hold_val = $row["srep_rrnum"]; 
}

$arr_shrd_rr_num_initials = array_flip($arr_shrd_rr_num_initials);
//show_array($arr_shrd_rr_num_initials);

//get all clients code and rep nums, shared situation


//get all clients code rrnum from initials
$qry = "SELECT clnt_code, trim(clnt_rr1) as rep1, trim(clnt_rr2) as rep2 from int_clnt_clients where  clnt_status = 'A' and clnt_isactive = 1 order by clnt_code"; //
$result = mysql_query($qry) or die(tdw_mysql_error($qry));
$arr_clnt_code_rr_num = array();
while ( $row = mysql_fetch_array($result) )  {
	if ($row["rep2"] == '') { //primary rep client
	$arr_clnt_code_rr_num[$row["clnt_code"]] = $arr_initials_rr_num[$row["rep1"]];
	} else {
		if ($arr_shrd_rr_num_initials[$row["rep1"]."^".$row["rep2"]] != "") {
			$arr_clnt_code_rr_num[$row["clnt_code"]] = $arr_shrd_rr_num_initials[$row["rep1"]."^".$row["rep2"]]; 
		} else {
			$arr_clnt_code_rr_num[$row["clnt_code"]] = $arr_shrd_rr_num_initials[$row["rep2"]."^".$row["rep1"]]; 
		}
	}
}

//show_array($arr_clnt_code_rr_num);

//if one initial then primary rep number
//if multiple initials then get from sales rep table

function yr_total_rep_prim($rr_num, $year, $arr_master_history, $sumtype = 'SUMMARY') { 
		$initials = db_single_val("select Initials as single_val from users where rr_num = '".$rr_num."'");
		$qry = "select clnt_code from int_clnt_clients where clnt_rr1 = '".$initials."' and trim(clnt_rr2) = ''"; 
		$result = mysql_query($qry) or die(tdw_mysql_error($qry));
		if ($sumtype == "SUMMARY") {
			$arr_prim_rep_year_total = array($year=>0);
			while ( $row = mysql_fetch_array($result) )  {
				$arr_prim_rep_year_total[$year] = $arr_prim_rep_year_total[$year] + $arr_master_history[$year][$row["clnt_code"]."^".$rr_num];
			}
		} else {
			while ( $row = mysql_fetch_array($result) )  {
				$arr_prim_rep_year_total[$row["clnt_code"]] = $arr_master_history[$year][$row["clnt_code"]."^".$rr_num]; 
			}
		}
			return $arr_prim_rep_year_total; 
}    

function yr_total_rep_shared($initials, $year, $arr_master_history, $sumtype = 'SUMMARY') { 
		$qry = "select clnt_code from int_clnt_clients where ( (trim(clnt_rr1) = '".$initials."' and trim(clnt_rr2) != '') or trim(clnt_rr2) = '".$initials."' ) and clnt_status ='A'"; 
		$result = mysql_query($qry) or die(tdw_mysql_error($qry));
		if ($sumtype == "SUMMARY") {
			$arr_shrd_rep_year_total = array($year=>0);
			while ( $row = mysql_fetch_array($result) )  {
				$arr_shrd_rep_year_total[$year] = $arr_shrd_rep_year_total[$year] + $arr_master_history[$year][$row["clnt_code"]."^".$arr_clnt_code_rr_num[$row["clnt_code"]]];
			}
		} else {
			while ( $row = mysql_fetch_array($result) )  {
				$arr_shrd_rep_year_total[$row["clnt_code"]] = $arr_master_history[$year][$row["clnt_code"]."^".$arr_clnt_code_rr_num[$row["clnt_code"]]];
			}
		}
		return $arr_shrd_rep_year_total; 
}    

$arr_summary = yr_total_rep_prim('041', 2011, $arr_master_history, "SUMMARY");
show_array($arr_summary);
$arr_detail = yr_total_rep_prim('041', 2011, $arr_master_history, "DETAIL");
show_array($arr_detail);
$arr_summary1 = yr_total_rep_shared('CC', 2011, $arr_master_history, "SUMMARY");
show_array($arr_summary1);
$arr_detail1 = yr_total_rep_shared('CC', 2011, $arr_master_history, "DETAIL");
show_array($arr_detail1);

?>