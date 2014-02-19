<script language="JavaScript" src="includes/prototype/prototype.js"></script>
<style type="text/css">
<!--
/* Sortable tables */
table.sortable thead {
	background-color:#d5e2f9;
	color:#003399;
	font-weight: bold;
	cursor:pointer;
	cursor:hand;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
-->
</style>			
<?

$trade_date_to_process = previous_business_day();
$previous_year_date = get_date_previous_year($trade_date_to_process);


//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

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

//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
// auto_id  clnt_code  clnt_year  clnt_attribute Values are B and T for Budget and Tier clnt_budget (USD) 
// clnt_tier Tier Data (1-4) clnt_override_id  clnt_override_comment  clnt_timestamp  clnt_isactive  
//int_clnt_clients_tiering
$arr_clnt_budget_count = array();
$arr_clnt_budget = array();
$arr_clnt_budget_history = "";
$qry_budget = "select auto_id, clnt_code, clnt_budget, clnt_override_id, clnt_override_comment, clnt_timestamp 
               FROM int_clnt_clients_tiering
							 WHERE clnt_attribute = 'B'
							 AND clnt_year = '".substr($trade_date_to_process,0,4)."'
							 AND clnt_isactive = 1
							 ORDER BY clnt_code, clnt_timestamp"; 
$result_budget = mysql_query($qry_budget) or die (tdw_mysql_error($qry_budget));
while ( $row_budget = mysql_fetch_array($result_budget) ) 
{
	$arr_clnt_budget_count[$row_budget["clnt_code"]] = $arr_clnt_budget_count[$row_budget["clnt_code"]] + 1;
	$arr_clnt_budget[$row_budget["clnt_code"]] = $row_budget["clnt_budget"];
}
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
//????????????????????????????????????????
$arr_clnt_comment = array();
if ($mode == 'm') {
$qry_comment = "select auto_id, clnt_code, clnt_attribute, clnt_comment, clnt_override_id, clnt_override_comment, clnt_timestamp 
               FROM int_clnt_clients_tiering
							 WHERE clnt_year = '".substr($trade_date_to_process,0,4)."'
							 AND clnt_isactive = 1
							 AND clnt_code != '----'
							 ORDER BY clnt_code, clnt_timestamp desc"; 
} else {
$qry_comment = "select auto_id, clnt_code, clnt_attribute, clnt_comment, clnt_override_id, clnt_override_comment, clnt_timestamp 
               FROM int_clnt_clients_tiering
							 WHERE clnt_year = '".substr($trade_date_to_process,0,4)."'
							 AND clnt_override_id = '".$user_id."' 
							 AND clnt_isactive = 1
							 AND clnt_code != '----'
							 ORDER BY clnt_code, clnt_timestamp desc"; 
}
$result_comment = mysql_query($qry_comment) or die (tdw_mysql_error($qry_comment));
while ( $row_comment = mysql_fetch_array($result_comment) ) 
{
  $whowhen = "";
	
	if ($row_comment["clnt_override_id"] != "") { $whowhen = "[".date('m/d/y h:ia',strtotime($row_comment["clnt_timestamp"]))." ".$arr_name_for_id[$row_comment["clnt_override_id"]]."]<br>";}
	if (($row_comment["clnt_attribute"] == 'T' || $row_comment["clnt_attribute"] == 'B') && strlen($row_comment["clnt_override_comment"]) > 1) { //
		$str_comment_data = str_replace('"','',$row_comment["clnt_override_comment"]);
		//$str_comment_data = str_replace('"','\\"',$row_comment["clnt_override_comment"]);
		$arr_clnt_comment[$row_comment["clnt_code"]] = "&#9658;".$whowhen.$str_comment_data."<br>".$arr_clnt_comment[$row_comment["clnt_code"]]; 
	} elseif ($row_comment["clnt_attribute"] == 'C') {
		$str_comment_data = str_replace('"','',$row_comment["clnt_comment"]);
		//$str_comment_data = str_replace('"','\\"',$row_comment["clnt_override_comment"]);
		$arr_clnt_comment[$row_comment["clnt_code"]] = "&#9658;".$whowhen.$str_comment_data."<br>".$arr_clnt_comment[$row_comment["clnt_code"]];
	} else {
	 $dummy = 1;
	}
}

//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&




///////////////////////////////////////////////  START OF MANAGE SECTION  ////////////////////////////////////////////////////////////



	echo "<center>";
	?>
	<? tsp(100, "Client Tiering"); ?>

		<table width="100%" border="0" cellpadding="1", cellspacing="0">
    <tr> 
		<?
    if ($mode == 'm') {
    ?>
    <td width="200">&#9658;<a class="ilt" href="client_tiering_excel.php?mode=m&filter_rep=<?=$filter_rep?>&filter_tier=<?=$filter_tier?>" target="_blank">Export to Excel</a></td>
		<?
    } else {
		?>
    <td width="200">&#9658;<a class="ilt" href="client_tiering_excel.php?mode=r&user_initials=<?=$user_initials?>" target="_blank">Export to Excel</a></td>
    <?
		}
		if ($mode == 'm') {
		?>
    <td width="10">&nbsp;</td>
    <td width="150">
    <form action="<?=$PHP_SELF?>" method="get">
    <input type="hidden" name="mod" value="client_tiering" />
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
    </td>
    <td width="10">&nbsp;</td>
    <td width="60">
    <select name="filter_tier" id="filter_tier" size="1">
			<option value="" selected="selected"> All Tiers. </option>
			<option value="1"> Tier 1 </option>
			<option value="2"> Tier 2 </option>
			<option value="3"> Tier 3 </option>
			<option value="4"> Tier 4 </option>
    </select>
    </td>
    <td width="10">&nbsp;</td>
    <td width="50"><input type="submit" name="Filter" value="   SHOW   "/></td>
		</form>	
		<?
    }
    ?>    
    <td align="right"><a class="ilt"><font color="red">NOTE:</font> To Edit / Override TIER, BUDGET or COMMENT, please click on it.</a></td>
		</tr></table>
		<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
					<!--TABLE 2 START-->
					<script language="JavaScript" src="includes/sortable2/sorttable.js" type="text/javascript"></script>

					<table class="sortable" preserve_style="cell" width="100%"  border="0" cellspacing="1" cellpadding="1">
						<tr>
							<td width="5"> </td>
							<td width="35" class="sorttable_nosort">12m</td>
							<td width="220">Name</td>
							<td width="50">Code</td>
							<td width="60">Rep. #</td>
							<td width="40">RR1</td>
							<td width="40">RR2</td>
							<td width="40">Trdr.</td>
							<td width="90"><?=substr($previous_year_date,0,4)?> Rev.</td>
							<td width="80">Tier</td>
							<td width="8">&nbsp;</td>
							<td width="100"><?=substr($trade_date_to_process,0,4)?> Budget</td>
							<td width="8">&nbsp;</td>
							<td width="300" class="sorttable_nosort">Comments</td>
							<td>&nbsp;</td>
						</tr>
						
						<? 

			ob_flush();
			flush();
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
						
						$query_clients = "SELECT * from int_clnt_clients 
															where ". $augment_filter . " AND clnt_status ='A' AND clnt_isactive = 1 order by clnt_name";
						//echo $query_clients;
						$result = mysql_query($query_clients) or die(mysql_error());
						$count_row = 0;
						while ( $row = mysql_fetch_array($result) ) 
						{

						//GET TIER
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
						
						//GET BUDGET
						$clnt_budget = "";
						$clnt_budgetoverride = 0;
						if ($arr_clnt_budget_count[$row["clnt_code"]] >= 1) {
							$clnt_budget = $arr_clnt_budget[$row["clnt_code"]];						
						} 
						
						if ($arr_clnt_budget_count[$row["clnt_code"]] > 1) {
							$clnt_budgetoverride = 1;						
						} 

						
						$proper_cname = trim(str_replace("'","",$row["clnt_name"]));
						$proper_cname = trim(str_replace("&","and",$proper_cname));
						
						
/*						echo 'dc ['.$count_row.'] = "'.$row["clnt_auto_id"].'^'.
																									trim($row["clnt_name"]).'^'.
																									$proper_cname.'^'.
																									$row["clnt_code"].'^'.
																									$row["clnt_rr1"].'^'.
																									$row["clnt_rr2"].'^'.
																									$row["clnt_trader"].'^'.
																									number_format($ann_val,0,"",",").'^'.
																									$val_tier.'^'.
																									$ann_string.str_replace("'","",$arr_clnt_comment[$row["clnt_code"]]).'^'.
																									get_rep_for_client ($arr_client_rrs, $row["clnt_code"]).'^'.
																									number_format($clnt_budget,0,"",",").'^'.
																									$clnt_budgetoverride.'^'.
																									$clnt_tieroverride.'"'.";\n";*/
										
						if ($count_row%2 == 0) {
							$rowclass = ' class="trdark"';
						} else {
							$rowclass = ' class="trlight"';
						}

						$b_override = "";
						if ($clnt_budgetoverride == "1") { 
							$b_override = "&Ocirc;";
						}
						
						$t_override = "";
						if ($clnt_tieroverride == "1") { 
							$t_override = "&Ocirc;";
						}

            if ($filter_tier && $filter_tier != "") {
								if ($val_tier == $filter_tier) {
								echo	"<tr " . $rowclass . ">".
													"<td>&nbsp;</td>".
													"<td valign='middle' nowrap='nowrap'><a href='#'><img src='images/t12m_s.png' border='0' onclick='CreateWnd(\"chart_t12m.php?clnt=" .$row["clnt_code"]."\", 620, 330, false);'></a></td>" .
													"<td>&nbsp;".trim($row["clnt_name"])."</td>". 
													"<td>&nbsp;".$row["clnt_code"]."</td>". 
													"<td>&nbsp;".get_rep_for_client ($arr_client_rrs, $row["clnt_code"])."</td>".
													"<td>&nbsp;".$row["clnt_rr1"]."</td>". 
													"<td>&nbsp;".$row["clnt_rr2"]."</td>". 
													"<td>&nbsp;".$row["clnt_trader"]."</td>".
													"<td align='right'>".number_format($ann_val,0,"",",")."</td>".
													"<td style=\"cursor:pointer;cursor:hand;\" align='right' onclick=\"CreateWnd('client_tiering_submodal.php?user_id=" . $user_id . "&element_id=T_" .$row["clnt_code"]. "&elementval=".$val_tier. "&cname=".$proper_cname."', 500, 280, false);\" id='T_" .$row["clnt_code"]. "'>".$val_tier." <img src='images/tier/".$val_tier.".png' height='13' border='0'>&nbsp;&nbsp;&nbsp;</td>".
													"<td id='o1_".$row["clnt_code"]."'>". $t_override ."</td>".
													"<td style=\"cursor:pointer;cursor:hand;\" align='right' onclick=\"CreateWnd('client_tiering_submodal.php?user_id=" . $user_id . "&element_id=B_" .$row["clnt_code"]. "&elementval=".number_format($clnt_budget,0,"",",")."&cname=".$proper_cname."', 500, 280, false);\" id='B_" .$row["clnt_code"]. "'>".number_format($clnt_budget,0,"",",")."</td>".
													"<td id='o2_".$row["clnt_code"]."'>". $b_override ."</td>".
													"<td style=\"cursor:pointer;cursor:hand;\"               onclick=\"CreateWnd('client_tiering_submodal.php?user_id=" . $user_id . "&element_id=C_" .$row["clnt_code"]. "&elementval=comment&cname=".$proper_cname."', 500, 280, false);\" id='C_" .$row["clnt_code"]. "'>".$ann_string.str_replace("'","",$arr_clnt_comment[$row["clnt_code"]])."</td>".
													"<td>&nbsp;</td>".
												"</tr>";
									$count_row = $count_row + 1;
									}
							} else {
								echo	"<tr " . $rowclass . ">".
													"<td>&nbsp;</td>".
													"<td valign='middle' nowrap='nowrap'><a href='#'><img src='images/t12m_s.png' border='0' onclick='CreateWnd(\"chart_t12m.php?clnt=" .$row["clnt_code"]."\", 620, 330, false);'></a></td>" .
													"<td>&nbsp;".trim($row["clnt_name"])."</td>". 
													"<td>&nbsp;".$row["clnt_code"]."</td>". 
													"<td>&nbsp;".get_rep_for_client ($arr_client_rrs, $row["clnt_code"])."</td>".
													"<td>&nbsp;".$row["clnt_rr1"]."</td>". 
													"<td>&nbsp;".$row["clnt_rr2"]."</td>". 
													"<td>&nbsp;".$row["clnt_trader"]."</td>".
													"<td align='right'>".number_format($ann_val,0,"",",")."</td>".
													"<td style=\"cursor:pointer;cursor:hand;\" align='right' onclick=\"CreateWnd('client_tiering_submodal.php?user_id=" . $user_id . "&element_id=T_" .$row["clnt_code"]. "&elementval=".$val_tier. "&cname=".$proper_cname."', 500, 350, false);\" id='T_" .$row["clnt_code"]. "'>".$val_tier." <img src='images/tier/".$val_tier.".png' height='13' border='0'>&nbsp;&nbsp;&nbsp;</td>".
													"<td id='o1_".$row["clnt_code"]."'>". $t_override ."</td>".
													"<td style=\"cursor:pointer;cursor:hand;\" align='right' onclick=\"CreateWnd('client_tiering_submodal.php?user_id=" . $user_id . "&element_id=B_" .$row["clnt_code"]. "&elementval=".number_format($clnt_budget,0,"",",")."&cname=".$proper_cname."', 500, 350, false);\" id='B_" .$row["clnt_code"]. "'>".number_format($clnt_budget,0,"",",")."</td>".
													"<td id='o2_".$row["clnt_code"]."'>". $b_override ."</td>".
													"<td style=\"cursor:pointer;cursor:hand;\"               onclick=\"CreateWnd('client_tiering_submodal.php?user_id=" . $user_id . "&element_id=C_" .$row["clnt_code"]. "&elementval=comment&cname=".$proper_cname."', 500, 350, false);\" id='C_" .$row["clnt_code"]. "'>".$ann_string.str_replace("'","",$arr_clnt_comment[$row["clnt_code"]])."</td>".
													"<td>&nbsp;</td>".
												"</tr>";
							$count_row = $count_row + 1;
							}																								
										
						}
							// onmouseout=\"document.getElementById('C_" + rowclients_array[3] + "').className='m_off';\" onmouseover=\"document.getElementById('C_" + rowclients_array[3] + "').className='m_on';\"
						?> 
 					</table>
				</td>
			</tr>
		</table>
	
		<? tep();
		
		echo "</center>";
/////////////////////////////////////////////////END OF MANAGE SECTION/////////////////////////////////////////////////
?>