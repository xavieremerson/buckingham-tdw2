<?
//###################################################################################################
//Based on the Date Selection, get the rolling 12 months start and end date.
$back_brk_year = $brk_year-1;
$arr_back_brk_dates = get_commission_month_dates($brk_month,$back_brk_year);

$back_brk_start_date = $arr_back_brk_dates[0];
$back_brk_end_date = $arr_back_brk_dates[1];

$back_brk_start_settle_date = db_single_val("select min(trad_settle_date) as single_val from mry_comm_rr_trades where trad_trade_date ='".$back_brk_start_date."' and trad_is_cancelled = 0");
$back_brk_end_settle_date = db_single_val("select min(trad_settle_date) as single_val from mry_comm_rr_trades where trad_trade_date ='".$back_brk_end_date."' and trad_is_cancelled = 0");

xdebug("Last Year Settle Date Start",$back_brk_start_settle_date);
xdebug("Last Year Settle Date End",$brk_end_settle_date);

$qry_rolling12 = "SELECT 
										trad_rr,
										trad_advisor_code,
										sum(trad_commission) as sum_trad_commission,
										max(trad_settle_date) as trad_settle_date
									FROM mry_comm_rr_trades 
									WHERE trad_is_cancelled = 0 
									AND trad_settle_date > '".$back_brk_end_settle_date."' AND trad_settle_date <='".$brk_end_settle_date."'
									GROUP BY trad_rr, trad_advisor_code 
									ORDER BY trad_advisor_code, trad_settle_date";
//xdebug("qry_rolling12",$qry_rolling12);
$result_rolling12 = mysql_query($qry_rolling12) or die (tdw_mysql_error($qry_rolling12));
$arr_nest_client_list = array();
while($row_rolling12 = mysql_fetch_array($result_rolling12)) {
			$arr_rcc[] = $row_rolling12["trad_rr"]."^".$row_rolling12["trad_advisor_code"]."^".$row_rolling12["sum_trad_commission"]."^".$row_rolling12["trad_settle_date"];
			$arr_nest_client_list[] = $row_rolling12["trad_advisor_code"];
}

//show_array($arr_rcc);

/*
Improving this with the following, if there more than one instances of the client in the array, assimilate the multiple
records to a single record with the sum of dollars and the single record woill be theone with the max of the dates.
*/
//Assimmilating  //NO CHECKS UP UNTIL NOW.
$arr_combined = array();
$i = 0;
foreach ($arr_rcc as $key=>$value) {
	//echo $value."<br>";
	
	//+++++++++++++++++++++++++++++++++++++++++++++++
	$tmp_store = explode("^",$value);
	$new_amount = 0;
	if ($tmp_store[1] == $hold_clnt_to_combine) {
	  //xdebug ("DUPES",$tmp_store[1]);	
		//echo "[".$i."][".$hold_clnt_to_combine."] / [".$hold_amnt_to_combine."]"."<br>";
		//echo $arr_combined[$i-1]."<br>";
		$arr_combined[$i-1] = $tmp_store[0]."^".$tmp_store[1]."^".($hold_amnt_to_combine + $tmp_store[2]);
		//echo $arr_combined[$i-1]."<br>";

		$hold_clnt_to_combine = $tmp_store[1];
		$hold_amnt_to_combine = ($hold_amnt_to_combine + $tmp_store[2]);

	} else {
	  //xdebug ("NO DUPES",$tmp_store[1]);	
			$arr_combined[$i] = $tmp_store[0]."^".$tmp_store[1]."^".$tmp_store[2];

		$hold_clnt_to_combine = $tmp_store[1];
		$hold_amnt_to_combine = $tmp_store[2];
   
	  $i = $i+1;
	}
	//+++++++++++++++++++++++++++++++++++++++++++++++
		
	//$i = $i+1;
}

//show_array($arr_combined);
//exit;

// 
//OUTSTANDING TODO THE  DATES BELOW SHOULD ME CALENDAR BASED NOT BROKERAGE BASED, FOR NOW, LETS SEE
//getting checks from the prior year period and assimiliating into the array created above.
$qry_rolling12_checks = "SELECT 
														chek_advisor,
														sum(chek_amount) as chek_amount,
														max(chek_date) as chek_date
													FROM chk_chek_payments_etc 
													WHERE chek_date > '".$back_brk_end_date."' AND chek_date <='".$brk_end_date."' 
													AND chek_isactive = 1 
													GROUP BY chek_advisor 
													ORDER BY chek_advisor";
//xdebug("qry_rolling12_checks",$qry_rolling12_checks);
$result_rolling12_checks = mysql_query($qry_rolling12_checks) or die (tdw_mysql_error($qry_rolling12_checks));
$arr_rolling12_checks = array();
$arr_rolling12_checks_date = array();
while($row_rolling12_checks = mysql_fetch_array($result_rolling12_checks)) {
			$arr_rolling12_checks[$row_rolling12_checks["chek_advisor"]] = $row_rolling12_checks["chek_amount"];
			$arr_rolling12_checks_date[$row_rolling12_checks["chek_advisor"]] = $row_rolling12_checks["chek_date"];
}

//show_array($arr_rolling12_checks);
//exit;

//Reassigning the array
$arr_rcc = $arr_combined;
//show_array($arr_rcc);
//exit;

//NOW ADD CHECKS
$arr_new_combined = array();
$arr_check_processed_list   = array();
foreach ($arr_rcc as $numindex=>$valstring) {

		 $tmp_store = explode("^",$valstring);
		 $newamount = $tmp_store[2];
		 foreach ($arr_rolling12_checks as $clnt=>$amt) {
				if ($tmp_store[1] == $clnt) {
				  //xdebug("clnt",$clnt."/".$tmp_store[2]."/".$amt);
					$newamount = $newamount + $amt;
	        $arr_check_processed_list[] = $tmp_store[1];
				}		 		
		 }
		
	 $arr_new_combined[] = $tmp_store[0]."^".$tmp_store[1]."^".$newamount;
}

//show_array($arr_new_combined);
//show_array($arr_check_processed_list);
//exit;

//now add checks only client to the combined list
$arr_delta = array();
foreach ($arr_rolling12_checks as $clnt=>$amt) {
  if (!in_array($clnt, $arr_check_processed_list)) {
		//get rr_num for client
		$qry = "select trim(clnt_rr1) as rr1, trim(clnt_rr2) as rr2 from int_clnt_clients where clnt_code = '".$clnt."'";
		$result = mysql_query($qry) or die (tdw_mysql_error($qry));
		while($row = mysql_fetch_array($result)) {
			$rr1 = $row["rr1"];
			$rr2 = $row["rr2"];	
			if ($rr1 != '' OR $rr2 != '') {
				if ($rr2 == '') {
					//xdebug("clnt/rr1/rr2", $clnt."/".$rr1."/".$rr2);
					$tmp_rr_num = get_rr_num (get_userid_for_initials ($rr1));
					//xdebug("tmp_rr_num",$tmp_rr_num);	
					$arr_delta[] = $tmp_rr_num."^".$clnt."^".$amt;
				} else {
					//xdebug("clnt/rr1/rr2", $clnt."/".$rr1."/".$rr2);
					$tmp_rr_num = get_shared_rr_num ($rr1, $rr2);
					//xdebug("tmp_rr_num",$tmp_rr_num);				
					$arr_delta[] = $tmp_rr_num."^".$clnt."^".$amt;
				}
			}
		}
	}
}

//show_array($arr_delta);	

//now reassign the $arr_rcc which is below
$arr_rcc = array();
$arr_rcc = $arr_new_combined;

//now merge the arrays
$arr_merged = array_merge($arr_rcc, $arr_delta);
//show_array($arr_rcc);	
//exit;
//now reassign the $arr_rcc which is below
$arr_rcc = array();
$arr_rcc = $arr_merged;

//show_array($arr_rcc);
//xdebug("ADAG/030",$arr_nest_client["ADAG"]["030"]);	
//###################################################################################################
?>