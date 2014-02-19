<?
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

//function get sole rr_num from ID
function get_rr_num ($ID) {
	$rr_num = db_single_val("SELECT rr_num as single_val FROM users WHERE ID = '".$ID."'");   
	return $rr_num;
}

//function get shared rr_num from client
//function corrected, was giving wrong output
function get_shared_rr_num ($initial_a, $initial_b) {
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
}

////
//
function get_rcc ($arr_rcc, $rr_num, $client){

	foreach($arr_rcc as $key=>$value) {
	   $val = array();
		 $val = explode("^",$value);
		 if ($val[0] == $rr_num AND $val[1] == $client) {
		 	$retval = $val[2];
		 }
	}
return $retval;
}																														


////
// Function to get Name of Rep
function get_repname_by_rr_num ($rr_num) {
	if (substr($rr_num,0,1) == '0') {
		$qry_name = "SELECT Fullname from Users where rr_num = '".$rr_num."'";   
		$result_name = mysql_query($qry_name) or die(tdw_mysql_error($qry_name));
		while($row_name = mysql_fetch_array($result_name)) {
			$rr_name = $row_name["Fullname"];
		}
		return $rr_name;
	} else {
		$qry_id = "SELECT srep_user_id, srep_rrnum from sls_sales_reps where srep_isactive = 1 AND srep_rrnum = '".$rr_num."'";
		//xdebug("qry_id",$qry_id);   
		$result_id = mysql_query($qry_id) or die(tdw_mysql_error($qry_id));
		while($row_id = mysql_fetch_array($result_id)) {
			$rr_id = $row_id["srep_user_id"];
			$qry_name = "SELECT Lastname from Users where ID = '".$rr_id."'";   
			//xdebug("qry_name",$qry_name);   
			$result_name = mysql_query($qry_name) or die(tdw_mysql_error($qry_name));
			while($row_name = mysql_fetch_array($result_name)) {
				$rr_name = $row_name["Lastname"];
				$out_rr_names = $rr_name . "/" .$out_rr_names;
			}
			if (substr($out_rr_names,strlen($out_rr_names)-1,strlen($out_rr_names)) == "/") {
			$out_rr_names = substr($out_rr_names,0,strlen($out_rr_names)-1);
			}
		}
		return $out_rr_names;
	}
}

////
// function to return special payout rate
//tested : works great
function sp_payout_rate($clnt, $userid, $arr_clnt_rates) {
	foreach ($arr_clnt_rates as $clntval=>$detail) {
		if ($clntval == $clnt) {
			$arr_vals = explode("#", $detail);
			if (count($arr_vals)==1) {
				$arr_final = explode("^",$arr_vals[0]);	
				if ($arr_final[0] == $userid) {
					return $arr_final[1];				
				}
			} else {
				foreach($arr_vals	as $k=>$v) {
					$arr_final = explode("^",$v);	
          if ($arr_final[0] == $userid) {
						return $arr_final[1];
					}
				}
			}
		}	
	}
}

////
//function get user_ids from shared rr_num
function get_userid_for_shared_rr ($rr_num) {
	$arr_out = array();
	$qry = "select srep_user_id from sls_sales_reps where srep_rrnum = '".$rr_num."'";
	$result = mysql_query($qry) or die(tdw_mysql_error($qry));
	while($row = mysql_fetch_array($result)) {
		$arr_out[] = $row["srep_user_id"];
	}
	return $arr_out;
}

?>