<?
include('includes/functions.php');
include('includes/global.php');
include('includes/dbconnect.php');
		
function extract_client ($str) {
	return substr($str,strpos($str,"[")+1,4); //Code is always 4 characters long.
}
									
$payment_type = array();

$payment_type[1] = "Research - Research";
$payment_type[2] = "Research - Independent";
$payment_type[3] = "Research - Geneva";
$payment_type[4] = "Broker-to-Broker";
$payment_type[5] = "Trading 2";
$payment_type[6] = "Other";

//Create Lookup Array of Client Code / Client Name
$qry_clients = "select clnt_code,
                       clnt_name,
											 trim(clnt_rr1) as clnt_rr1,
											 trim(clnt_rr2) as clnt_rr2
								from int_clnt_clients";
$result_clients = mysql_query($qry_clients) or die (tdw_mysql_error($qry_clients));
$arr_clients = array();
$arr_client_rrs = array();
while ( $row_clients = mysql_fetch_array($result_clients) ) 
{
	$arr_clients[$row_clients["clnt_code"]] = $row_clients["clnt_name"];
	$arr_client_rrs[$row_clients["clnt_code"]] = $row_clients["clnt_rr1"]."##".$row_clients["clnt_rr2"];
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

//function get sole rr_num from ID
function get_rr_num ($ID) {
	$rr_num = db_single_val("SELECT rr_num as single_val FROM users WHERE ID = '".$ID."'");   
	return $rr_num;
}

//get rr_num and initials for client
//function corrected, was giving wrong output
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


//echo get_rep_for_client ($arr_client_rrs, 'HIGS');
//exit;

/*print_r($_GET);
exit;
*/	
	if ($chk_type == 'ALL') {
		$str_append = " AND a.chek_type like '%' ";
	} else {
		$str_append = " AND a.chek_type = '".$chk_type."' ";
	}

	if($rep == '^ALL^') {
		$str_append_rep = " AND a.chek_reps_and like '%' ";
	} else {
		$arr_repinfo = split('\^',$rep);
		$rep_id = $arr_repinfo[1];
		$rep_initials = db_single_val("select Initials as single_val from users where ID = '".$rep_id."'");
		$str_append_rep = " AND a.chek_reps_and like '%".$rep_initials."%' ";
	}

	if ($clnt == 'Enter Client' or $clnt == '') {
		$str_append_client = " AND a.chek_advisor like '%' ";
	} else {
		$str_append_client = " AND a.chek_advisor = '". extract_client($clnt) ."' ";
	}

	//xdebug('datefilterval',$datefilterval);
	$date_from = format_date_mdy_to_ymd($datefrom);
	$date_to = format_date_mdy_to_ymd($dateto);


//xdebug("datefrom",$datefrom);
//xdebug("dateto",$dateto);
//exit;	
			
$output_filename = "checks.csv";
$fp = fopen($exportlocation.$output_filename, "w");

$string = "\"Date\",\"Client Code\",\"Client Name\",\"Amount\",\"Type\",\"Reps\",\"Rep#\",\"Comments\",\"Entered By"."\"".chr(13); 

fputs ($fp, $string);

						//Clients List
						$query_check = "SELECT distinct(a.auto_id), a.*, b.Fullname, c.clnt_name 
																from chk_chek_payments_etc a, 
                                     Users b, 
                                     int_clnt_clients c 
                                where a.chek_entered_by = b.ID
																	and a.chek_date between '".$date_from."' and '".$date_to."' 
																	and a.chek_advisor = c.clnt_code
																	and a.chek_isactive = 1 ".$str_append . $str_append_rep . $str_append_client ."
                                order by a.chek_date desc";
/*						xdebug("query_check",$query_check);
						exit;
*/						$result_check = mysql_query($query_check) or die(tdw_mysql_error($query_check));
						while($row_check = mysql_fetch_array($result_check)) {
						$string = "\"".$row_check["chek_date"]."\",\"".
						               $row_check["chek_advisor"]."\",\"".
													 $row_check["clnt_name"]."\",\"".
													 $row_check["chek_amount"]."\",\"".
													 $payment_type[$row_check["chek_type"]]."\",\"".
													 ' '.str_replace('##'," ",$arr_client_rrs[$row_check["chek_advisor"]]).' '."\",\"".
													 "'".get_rep_for_client ($arr_client_rrs, $row_check["chek_advisor"])."'"."\",\"".
													 $row_check["chek_comment"]."\",\"".
													 $row_check["Fullname"]."\"".chr(13); 
						//echo $string;
						fputs ($fp, $string);
						}

fclose($fp);

Header("Location: data/exports/".$output_filename);
?>