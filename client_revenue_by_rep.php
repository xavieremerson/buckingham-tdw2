<!--<script language="JavaScript" src="includes/prototype/prototype.js"></script>-->
<?
if ($_POST) {
	$trade_date_to_process = format_date_mdy_to_ymd($datefrom);
	$previous_year_date = get_date_previous_year($trade_date_to_process);
	$sel_datefrom = $datefrom;
} else {
	$trade_date_to_process = previous_business_day();
	$previous_year_date = get_date_previous_year($trade_date_to_process);
	$sel_datefrom = format_date_ymd_to_mdy(previous_business_day());
}
//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

//33333333333333333333333333333333333333333333333333333333333333333333333333333333333333333333
//get all Sales Reps based on any transaction withing the last year.
$arr_rep_nums = array();
$qry_rep_nums = "SELECT distinct(trad_rr) as trad_rr from mry_comm_rr_trades 
								 WHERE trad_run_date > '".(date('Y',strtotime($trade_date_to_process))-1).date("-m-d")."' 
								 and trad_is_cancelled = 0
								 order by trad_rr";
$result = mysql_query($qry_rep_nums) or die(tdw_mysql_error($qry_rep_nums));
while ( $row = mysql_fetch_array($result) ) 
{
		$arr_rep_nums[] = $row["trad_rr"];
}

//get initials and names of reps.
$arr_initials_names = array();
foreach($arr_rep_nums as $k=>$v) {
	if (substr($v,0,1) == "0") {
		$str_qry = "select Initials, Fullname from users where rr_num = '".$v."'";
		$result = mysql_query($str_qry) or die(tdw_mysql_error($str_qry));
		while ( $row = mysql_fetch_array($result) ) {
			$arr_initials_names[$row["Initials"]] = $row["Fullname"];
		} 
	}
}

ksort($arr_initials_names);
//show_array($arr_initials_names);

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
		$clnt_rep_list[$row["clnt_code"]] = $row["clnt_rr1"]."^".$row["clnt_rr2"];
	}
}

//Count of Sole, Joint Primary and Joint Secondary Clients for Reps.
function total_client_types($rr_initials, $clnt_rep_list) {
	$arr_rep_client_type_nums = array();
	foreach ($clnt_rep_list as $k=>$v) {
		$arr_rep_vals = explode("^",$v);
		if ($arr_rep_vals[0] == $rr_initials && $arr_rep_vals[1] == "") {
			$arr_rep_client_type_nums["Sole"] = $arr_rep_client_type_nums["Sole"] + 1;
		} else if ($arr_rep_vals[0] == $rr_initials && $arr_rep_vals[1] != "") {
			$arr_rep_client_type_nums["JP"] = $arr_rep_client_type_nums["JP"] + 1;
		} else if ($arr_rep_vals[1] == $rr_initials) {
			$arr_rep_client_type_nums["JS"] = $arr_rep_client_type_nums["JS"] + 1;
		}
	}	
	
	$arr_sorted_counts = array();
	$arr_sorted_counts["Sole"] = $arr_rep_client_type_nums["Sole"];
	$arr_sorted_counts["JP"] = $arr_rep_client_type_nums["JP"];
	$arr_sorted_counts["JS"] = $arr_rep_client_type_nums["JS"];
	
	
	return $arr_sorted_counts;
}

//33333333333333333333333333333333333333333333333333333333333333333333333333333333333333333333

//22222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222
//Client current tier and how many years in that tier and if it went up or down.
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


	//Annualized Current Year as of the date selected
	$qry_cur_yearly_total = "select trad_advisor_code, round(sum(trad_commission),0) as clnt_revenue
													from mry_comm_rr_trades 
													where trad_trade_date between '".date('Y', strtotime($trade_date_to_process))."-01-01' and '".$trade_date_to_process."' 
													and trad_is_cancelled = 0
													group by trad_advisor_code
													order by trad_advisor_code";
	
	$result_cur_yearly_total = mysql_query($qry_cur_yearly_total) or die (tdw_mysql_error($qry_cur_yearly_total));
	$arr_cur_yearly_total = array();
	$arr_cur_yearly_total_actual = array();
	while ( $row =
	 mysql_fetch_array($result_cur_yearly_total) ) {
		$annualized_cur_year = round(($row["clnt_revenue"]/date('z'))*365,0);
		$arr_cur_yearly_total_actual[$row["trad_advisor_code"]] = round(($row["clnt_revenue"]/1000),0);
		$arr_cur_yearly_total[$row["trad_advisor_code"]] = $annualized_cur_year; 
	}
	
	//Annualized Current Year Checks
	$qry_cur_yearly_chk_total = "select chek_advisor, round(sum(chek_amount),0) as clnt_revenue
													from chk_chek_payments_etc  
													where chek_date between '".date('Y', strtotime($trade_date_to_process))."-01-01' and '".$trade_date_to_process."' 
													and chek_isactive = 1
													group by chek_advisor
													order by chek_advisor";
	//xdebug("qry_cur_yearly_chk_total",$qry_cur_yearly_chk_total);																
	$result_cur_yearly_chk_total = mysql_query($qry_cur_yearly_chk_total) or die (tdw_mysql_error($qry_cur_yearly_chk_total));
	$arr_cur_yearly_chk_total = array();
	$arr_cur_yearly_chk_total_actual = array();
	while ( $row = mysql_fetch_array($result_cur_yearly_chk_total) ) {
		$annualized_cur_year = round(($row["clnt_revenue"]/date('z'))*365,0);
		$arr_cur_yearly_chk_total_actual[$row["chek_advisor"]] = round(($row["clnt_revenue"]/1000),0);
		$arr_cur_yearly_chk_total[$row["chek_advisor"]] = $annualized_cur_year;
	}
	//show_array($arr_cur_yearly_chk_total);
	
	//Array of Client Revenue, Tier, Type and Reps.
	$clnt_rep_revenue_tier = array();
  foreach ($arr_active_clients as $k=>$v) {
		$str_revenue = $arr_cur_yearly_total[$v] + $arr_cur_yearly_chk_total[$v];
		$str_tier = get_tier($str_revenue);
		$arr_rep_codes = explode("^",$clnt_rep_list[$v]);
		$str_rep1 = $arr_rep_codes[0];
		$str_rep2 = $arr_rep_codes[1];
		$clnt_rep_revenue_tier[$v] = array($str_revenue,$str_tier,$str_rep1,$str_rep2);
		//echo $v. " ==> " . $str_revenue. " ==> " . $str_tier ." ==> " . $str_rep1." ==> " . $str_rep2."<br>";
	}
	//show_array($clnt_rep_revenue_tier);
	//exit;
	
	function total_revenue_by_rep_type ($rr_initials, $type, $clnt_rep_revenue_tier) {
		$total_revenue = 0;
		$average_revenue = 0;
		foreach($clnt_rep_revenue_tier as $k=>$v) {
			if ($type == 'Sole') {
				if ($v[2]==$rr_initials && $v[3] == "") { //Sole
						$count_clients = $count_clients + 1;
						$total_revenue = $total_revenue + $v[0];	
				}
			} else if ($type == 'JP') {
				if ($v[2]==$rr_initials && $v[3] != "") { //Joint Primary
						$count_clients = $count_clients + 1;
						$total_revenue = $total_revenue + $v[0];	
				}
			} else if ($type == 'JS') {
				if ($v[3]==$rr_initials) { //Joint Primary
						$count_clients = $count_clients + 1;
						$total_revenue = $total_revenue + $v[0];	
				}
			} else {
				$var_dummy = 1;
			}
		}
		
		if ($count_clients > 0) {
			$average_revenue = $total_revenue / $count_clients;
		} else {
			$average_revenue = 0;
		}
		
		return array($count_clients,number_format(round(($total_revenue/1000),0),0,"",","),round(($average_revenue/1000),0));
		
	}
	
	function total_revenue_by_rep_tier($rr_initials, $tier, $type, $clnt_rep_revenue_tier) {
		$count_clients = 0;
		$total_revenue = 0;
		$average_revenue = 0;
		foreach($clnt_rep_revenue_tier as $k=>$v) {
			if ($type == 'Sole') {
				if ($v[2]==$rr_initials && $v[3] == "" && $v[1] == $tier ) { //Sole
						$count_clients = $count_clients + 1;
						$total_revenue = $total_revenue + $v[0];	
				}
			} else if ($type == 'JP') {
				if ($v[2]==$rr_initials && $v[3] != "" && $v[1] == $tier ) { //Joint Primary
						$count_clients = $count_clients + 1;
						$total_revenue = $total_revenue + $v[0];	
				}
			} else if ($type == 'JS') {
				if ($v[3]==$rr_initials && $v[1] == $tier ) { //Joint Primary
						$count_clients = $count_clients + 1;
						$total_revenue = $total_revenue + $v[0];	
				}
			} else {
				$var_dummy = 1;
			}
		}
		
		if ($count_clients > 0) {
			$average_revenue = $total_revenue / $count_clients;
		} else {
			$average_revenue = 0;
		}
		
		return array($count_clients,number_format(round(($total_revenue/1000),0),0,"",","),round(($average_revenue/1000),0));
		
	}
	
	function client_tier_duration ($clnt_code, $arr_clnt_yearly_total, $arr_cur_yearly_total, $arr_cur_yearly_chk_total) {
		$arr_revenue = array();
		foreach ($arr_clnt_yearly_total as $k=>$v){
			$arr_val_pieces = explode("^",$v);
			if ($arr_val_pieces[0] == $clnt_code) {
				$arr_revenue[$arr_val_pieces[1]] = $arr_val_pieces[2];
			}
		}
		
		$arr_revenue[date('Y', strtotime($trade_date_to_process))] = $arr_cur_yearly_total[$clnt_code] + $arr_cur_yearly_chk_total[$clnt_code];
		
		$cur_tier = "";
		$cur_tier_years = "";
		$cur_tier_up_down = "--";
		
		$cur_year_tier = get_tier($arr_revenue[date('Y', strtotime($trade_date_to_process))]);
		$cur_tier = $cur_year_tier;
		$cur_tier_years = 1;
		
		
		$arr_prior_tiers = array();
		for ($i=1;$i<7;$i++) {
			$arr_prior_tiers[date('Y', strtotime($trade_date_to_process)) - $i] = get_tier($arr_revenue[date('Y', strtotime($trade_date_to_process))-$i]);
		}
		
		$hold_tier = $cur_tier;
		foreach ($arr_prior_tiers as $year=>$tier) {
			if ($tier == $hold_tier) {
				$cur_tier_years = $cur_tier_years + 1;
				$hold_tier = $tier;
			} else if ($tier > $hold_tier) {
				$cur_tier_up_down = "&uarr;";
				break;
			} else if ($tier < $hold_tier) {
				$cur_tier_up_down = "&darr;";
				break;
			} else {
				$var_dummy = 1;
			}
		}

		$arr_return = array($cur_tier, $cur_tier_years, $cur_tier_up_down);
		return $arr_return;
	}

//show_array(tier_duration ("CAPG", $arr_clnt_yearly_total,$arr_cur_yearly_total,$arr_cur_yearly_chk_total));
//22222222222222222222222222222222222222222222222222222222222222222222222222222222222222222222


//Get user lookup
$arr_id_for_initials = array();
$arr_id_for_rr_num = array();
$arr_rr_for_id = array();
$arr_name_for_id = array();
$arr_name_for_initials = array();
$q_users = "select ID, rr_num, Initials, Fullname from users;";
$r_users = mysql_query($q_users) or die (tdw_mysql_error($q_users));
while ( $row_users = mysql_fetch_array($r_users) ) {
	$arr_id_for_initials[$row_users["Initials"]] = $row_users["ID"];
	$arr_id_for_rr_num[$row_users["rr_num"]] = $row_users["ID"];
	$arr_rr_for_id[$row_users["ID"]] = $row_users["rr_num"];
	$arr_name_for_id[$row_users["ID"]] = $row_users["Fullname"];
	$arr_name_for_initials[$row_users["Initials"]] = $row_users["Fullname"];
}

//Create Lookup Array of Client Code / Client Name
// also get a list of reps to use in filter
$qry_clients = "select clnt_code,
                       clnt_name,
											 trim(clnt_rr1) as clnt_rr1,
											 trim(clnt_rr2) as clnt_rr2
								from int_clnt_clients where clnt_isactive = 1";
$result_clients = mysql_query($qry_clients) or die (tdw_mysql_error($qry_clients));
$arr_clients = array();
$arr_client_rrs = array();
$arr_reps = array();
while ( $row_clients = mysql_fetch_array($result_clients) ) 
{
	$arr_clients[$row_clients["clnt_code"]] = $row_clients["clnt_name"];
	$arr_client_rrs[$row_clients["clnt_code"]] = $row_clients["clnt_rr1"]."##".$row_clients["clnt_rr2"];
	if ($row_clients["clnt_rr1"] != "") { $arr_reps[$row_clients["clnt_rr1"]] = $arr_name_for_initials[$row_clients["clnt_rr1"]]; }
	if ($row_clients["clnt_rr2"] != "") { $arr_reps[$row_clients["clnt_rr2"]] = $arr_name_for_initials[$row_clients["clnt_rr2"]]; }
}

asort($arr_reps);
?>

<!--

<? // print_r($arr_reps); ?>

-->

<?

////
//function get user_id from rr_num
function get_userid_for_rr ($rr_num) {
  global $arr_id_for_rr_num;
	$user_id = $arr_id_for_rr_num[$rr_num];
	//$user_id = db_single_val("SELECT ID as single_val FROM users WHERE rr_num = '".$rr_num."'");   
	return $user_id;
}

//function get user_id from Initials
function get_userid_for_initials ($Initials) {
	global $arr_id_for_initials;
	$user_id = $arr_id_for_initials[$Initials];
	//$user_id = db_single_val("SELECT ID as single_val FROM users WHERE Initials = '".$Initials."'");   
	return $user_id;
}

//function get sole rr_num from ID
function get_rr_num ($ID) {
	global $arr_rr_for_id;
	$rr_num = $arr_rr_for_id[$ID];
	//$rr_num = db_single_val("SELECT rr_num as single_val FROM users WHERE ID = '".$ID."'");   
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
	    return "BRG"; 
	}
}

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
		
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&

//show_array($arr_clnt_budget);
//xdebug("qry_budget",$qry_budget);
$arr_clnt_tier_count = array();
$arr_clnt_tier = array();
$arr_clnt_tier_history = "";
$qry_tier = "select auto_id, clnt_code, clnt_tier, clnt_budget, clnt_override_id, clnt_override_comment, clnt_timestamp 
               FROM int_clnt_clients_tiering
							 WHERE clnt_attribute = 'T'
							 AND clnt_year = '".substr($trade_date_to_process,0,4)."'
							 AND clnt_isactive = 1
							 ORDER BY clnt_code, clnt_timestamp"; 
$result_tier = mysql_query($qry_tier) or die (tdw_mysql_error($qry_tier));
while ( $row_tier = mysql_fetch_array($result_tier) ) 
{
	$arr_clnt_tier_count[$row_tier["clnt_code"]] = $arr_clnt_tier_count[$row_tier["clnt_code"]] + 1;
	$arr_clnt_tier[$row_tier["clnt_code"]] = $row_tier["clnt_tier"];
}

//show_array($arr_clnt_tier);
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&




///////////////////////////////////////////////  START OF MANAGE SECTION  ////////////////////////////////////////////////////////////

	echo "<center>";
	?>
	<? tsp(100, "Client Revenue BY Sales Rep."); ?>

		<table width="100%" border="0" cellpadding="1", cellspacing="0">
    <tr> 
		<?
    if ($mode == 'm') {
    ?>
    <td width="200">&#9658;<a class="ilt" href="client_revenue_by_rep_excel.php?mode=m&filter_rep=<?=$filter_rep?>&filter_tier=<?=$filter_tier?>" target="_blank">Export to Excel</a></td>
		<?
    } else {
		?>
    <td width="200">&#9658;<a class="ilt" href="client_revenue_by_rep_excel.php?mode=r&user_initials=<?=$user_initials?>" target="_blank">Export to Excel</a></td>
    <?
		}
		if ($mode == 'm') {
		?>
    <td width="10">&nbsp;</td>
    <td width="150"><form name="dateval" id="dateval" method="post" action="#">
    <SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
		<SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
      <SCRIPT LANGUAGE="JavaScript">
				var calfrom = new CalendarPopup("divfrom");
				calfrom.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
      </SCRIPT>																
        <input type="text" id="iddatefrom" class="Text1" name="datefrom" size="12" maxlength="12" value="<?=$sel_datefrom?>">
        <A HREF="#" onClick="calfrom.select(document.forms['dateval'].datefrom,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A>
        <input type="image" src="images/lf_v1/form_submit.png">
        </form>
    </td>
    <!--<td width="150">
    <form action="<?=$PHP_SELF?>" method="get">
    <input type="hidden" name="mod" value="client_revenue" />
    <input type="hidden" name="mode" value="m" />
    <select name="filter_rep" id="filter_rep" size="1">
			<option value="" selected="selected"> All Reps. </option>
			<option value="BRG"> BRG </option>
    	<?
				foreach ($arr_reps as $k=>$v) {
					if ($v != "") {
						echo '<option value="'.$k.'"> '.$v.' </option>';
					} 
				}
			?>
    </select>
    </td>-->
    <td width="10">&nbsp;</td>
    <!--<td width="60">
    <select name="filter_tier" id="filter_tier" size="1">
			<option value="" selected="selected"> All Tiers. </option>
			<option value="1"> Tier 1 </option>
			<option value="2"> Tier 2 </option>
			<option value="3"> Tier 3 </option>
			<option value="4"> Tier 4 </option>
    </select>
    </td>
    <td width="10">&nbsp;</td>
    <td width="50"><input type="submit" name="Filter" value="   SHOW   "/></td>-->
		</form>	
		<?
    }
    ?>    
    <td align="right"><a class="ilt"><font color="red">NOTE:</font> To apply multiple column sort, please use Shift + Click.</a></td>
		</tr></table>
          <!--TABLE 2 START-->
					<link rel="stylesheet" href="includes/jquery/__jquery.tablesorter/themes/blue/style.css" type="text/css" media="print, projection, screen" />
					<script type="text/javascript" src="includes/jquery/jquery-1.8.2.min.js"></script>
					<script type="text/javascript" src="includes/jquery/__jquery.tablesorter/jquery.tablesorter.min.js"></script>
					<script type="text/javascript">

					$(document).ready(function() {     
						// call the tablesorter plugin and assign widgets with id "zebra" (Default widget in the core) and the newly created "reindextbl"
						$("#myTable").tablesorter(
							{widgets: ['zebra']},
							{headers:  {2: {sorter:false} } }
						);
					}); 
					</script>	
 					<table id="myTable" class="tablesorter" border="0" cellpadding="0" cellspacing="1">
						<thead>
            <tr>
              <td width="220" colspan="2" bgcolor="#f7f7f7">&nbsp;</td>
							<td width="200" colspan="3" bgcolor="#dddddd"><center><strong>TOTALS</strong></center></td>
              <td width="240" colspan="3" bgcolor="#009933"><center><strong><font color="#FFFFFF">TIER 1</font></strong></center></td>
              <td width="240" colspan="3" bgcolor="#00CC66"><center><strong>TIER 2</strong></center></td>
              <td width="240" colspan="3" bgcolor="#FFCC33"><center><strong>TIER 3</strong></center></td>
              <td width="240" colspan="3" bgcolor="#FF0000"><center><strong><font color="#FFFFFF">TIER 4</font></strong></center></td> 
							<!--<th width="80">Tier</th>-->
							<td bgcolor="#ffffff">&nbsp;</td>
						</tr>
            <tr>
              <th width="120">Rep.</th>
							<th width="100">Status</th>
							<th width="45"># Accts.</th>
              <th width="65">Revenue</th>
              <th width="40">Avg.</th>
							<th width="45"># Accts.</th>
              <th width="65">Revenue</th>
              <th width="40">Avg.</th>
							<th width="45"># Accts.</th>
              <th width="65">Revenue</th>
              <th width="40">Avg.</th>
							<th width="45"># Accts.</th>
              <th width="65">Revenue</th>
              <th width="40">Avg.</th>
							<th width="45"># Accts.</th>
              <th width="65">Revenue</th>
              <th width="40">Avg.</th>
							<!--<th width="80">Tier</th>-->
							<td bgcolor="#ffffff">&nbsp;</td>
						</tr>
					  </thead>
					  <tbody>	
						<? 
						//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
						if ($mode == 'm') {
							 if ($filter_rep && $filter_rep != "" && $filter_rep != 'BRG') {
							    $augment_filter = " (clnt_rr1 = '".$filter_rep."' OR clnt_rr2 = '".$filter_rep."' ) ";
							 } elseif ($filter_rep == 'BRG') {
							    $augment_filter = " (trim(clnt_rr1) = '' AND trim(clnt_rr2) = '' ) ";
							 } else {
							 		$augment_filter = " 1 ";
							 }
						} elseif ($mode == 'r') {
							 $augment_filter = " (clnt_rr1 = '".$user_initials."' OR clnt_rr2 = '".$user_initials."' ) ";
						} else {
							echo "<tr><td colspan='15'>Invalid Module Access, please contact Technical Support.</td></tr>";
							exit;
						}
						//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
						
						foreach($arr_initials_names as $k=>$v) {
								
								foreach(total_client_types($k, $clnt_rep_list) as $type=>$cnt_type) {
									if ($type == 'Sole' && $cnt_type > 0) {
									
									$arr_vals_1 = total_revenue_by_rep_tier($k, 1, "Sole", $clnt_rep_revenue_tier);
									$arr_vals_2 = total_revenue_by_rep_tier($k, 2, "Sole", $clnt_rep_revenue_tier);
									$arr_vals_3 = total_revenue_by_rep_tier($k, 3, "Sole", $clnt_rep_revenue_tier);
									$arr_vals_4 = total_revenue_by_rep_tier($k, 4, "Sole", $clnt_rep_revenue_tier);

									$arr_vals_totals = total_revenue_by_rep_type ($k, "Sole", $clnt_rep_revenue_tier);
									
									echo	"<tr>".
													"<td>".$v."</td>".
													"<td>Sole</td>". 
													"<td align='right'>".$cnt_type."</td>". 
													"<td align='right'>".$arr_vals_totals[1]."</td>". 
													"<td align='right'>".$arr_vals_totals[2]."</td>". 
													"<td align='right'>".$arr_vals_1[0]."</td>". 
													"<td align='right'>".$arr_vals_1[1]."</td>". 
													"<td align='right'>".$arr_vals_1[2]."</td>". 
													"<td align='right'>".$arr_vals_2[0]."</td>". 
													"<td align='right'>".$arr_vals_2[1]."</td>". 
													"<td align='right'>".$arr_vals_2[2]."</td>". 
													"<td align='right'>".$arr_vals_3[0]."</td>". 
													"<td align='right'>".$arr_vals_3[1]."</td>". 
													"<td align='right'>".$arr_vals_3[2]."</td>". 
													"<td align='right'>".$arr_vals_4[0]."</td>". 
													"<td align='right'>".$arr_vals_4[1]."</td>". 
													"<td align='right'>".$arr_vals_4[2]."</td>". 
													"<td>&nbsp;</td>". 
												"</tr>";
									} else if ($type == 'JP' && $cnt_type > 0){

									$arr_vals_1 = total_revenue_by_rep_tier($k, 1, "JP", $clnt_rep_revenue_tier);
									$arr_vals_2 = total_revenue_by_rep_tier($k, 2, "JP", $clnt_rep_revenue_tier);
									$arr_vals_3 = total_revenue_by_rep_tier($k, 3, "JP", $clnt_rep_revenue_tier);
									$arr_vals_4 = total_revenue_by_rep_tier($k, 4, "JP", $clnt_rep_revenue_tier);

									$arr_vals_totals = total_revenue_by_rep_type ($k, "JP", $clnt_rep_revenue_tier);
									
									echo	"<tr>".
													"<td>".$v."</td>".
													"<td>Joint Primary</td>". 
													"<td align='right'>".$cnt_type."</td>". 
													"<td align='right'>".$arr_vals_totals[1]."</td>". 
													"<td align='right'>".$arr_vals_totals[2]."</td>". 
													"<td align='right'>".$arr_vals_1[0]."</td>". 
													"<td align='right'>".$arr_vals_1[1]."</td>". 
													"<td align='right'>".$arr_vals_1[2]."</td>". 
													"<td align='right'>".$arr_vals_2[0]."</td>". 
													"<td align='right'>".$arr_vals_2[1]."</td>". 
													"<td align='right'>".$arr_vals_2[2]."</td>". 
													"<td align='right'>".$arr_vals_3[0]."</td>". 
													"<td align='right'>".$arr_vals_3[1]."</td>". 
													"<td align='right'>".$arr_vals_3[2]."</td>". 
													"<td align='right'>".$arr_vals_4[0]."</td>". 
													"<td align='right'>".$arr_vals_4[1]."</td>". 
													"<td align='right'>".$arr_vals_4[2]."</td>". 
													"<td>&nbsp;</td>". 
												"</tr>";
									} else if ($type == 'JS' && $cnt_type > 0) {

									$arr_vals_1 = total_revenue_by_rep_tier($k, 1, "JS", $clnt_rep_revenue_tier);
									$arr_vals_2 = total_revenue_by_rep_tier($k, 2, "JS", $clnt_rep_revenue_tier);
									$arr_vals_3 = total_revenue_by_rep_tier($k, 3, "JS", $clnt_rep_revenue_tier);
									$arr_vals_4 = total_revenue_by_rep_tier($k, 4, "JS", $clnt_rep_revenue_tier);

									$arr_vals_totals = total_revenue_by_rep_type ($k, "JS", $clnt_rep_revenue_tier);
									
									echo	"<tr>".
													"<td>".$v."</td>".
													"<td>Joint Secondary</td>". 
													"<td align='right'>".$cnt_type."</td>". 
													"<td align='right'>".$arr_vals_totals[1]."</td>". 
													"<td align='right'>".$arr_vals_totals[2]."</td>". 
													"<td align='right'>".$arr_vals_1[0]."</td>". 
													"<td align='right'>".$arr_vals_1[1]."</td>". 
													"<td align='right'>".$arr_vals_1[2]."</td>". 
													"<td align='right'>".$arr_vals_2[0]."</td>". 
													"<td align='right'>".$arr_vals_2[1]."</td>". 
													"<td align='right'>".$arr_vals_2[2]."</td>". 
													"<td align='right'>".$arr_vals_3[0]."</td>". 
													"<td align='right'>".$arr_vals_3[1]."</td>". 
													"<td align='right'>".$arr_vals_3[2]."</td>". 
													"<td align='right'>".$arr_vals_4[0]."</td>". 
													"<td align='right'>".$arr_vals_4[1]."</td>". 
													"<td align='right'>".$arr_vals_4[2]."</td>". 
													"<td>&nbsp;</td>". 
												"</tr>";
									} else {
											$var_dummmy = 0;
									}
								}
						}
						?> 
					 </tbody>
 					</table>
	
		<? tep();
		
		echo "</center>";
/////////////////////////////////////////////////END OF MANAGE SECTION/////////////////////////////////////////////////
?>
	<DIV ID="divfrom" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>