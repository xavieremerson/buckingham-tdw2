<?
include('pay_analyst_js.php');
include('pay_analyst_css.php');

if ($sel_rep) {
	$rep_and_id = explode("^",$sel_rep);
	$qry = "SELECT concat(Firstname, ' ', Lastname ) as single_val from users where ID = '".$rep_and_id[1]."'";
	//xdebug("qry", $qry);
	$str_name = db_single_val($qry); //'221';
	$str_heading = "Analyst Allocations : ". $str_name;
} else {
	$str_heading = "Analyst Allocations";
}


tsp(100, $str_heading);
?>
<table width="100%" border="0" cellpadding="4" cellspacing="0"> 
	<tr>
		<td>
		<!-- Top Menu -->		
		<form name="frm_criteria" action="<?=$PHP_SELF?>" method="get" onsubmit="return check_frm_criteria()">
		
		<select class="Text2" name="sel_rep" size="1" >
		<option value="">&nbsp;SALES REPS.&nbsp;</option>
		<option value="">____________</option>
		<?
		//get reps from query  on table mry_comm_rr_trades and join on users
		$qry_get_reps = "SELECT
											ID, rr_num, concat(Firstname, ' ', Lastname ) as rep_name 
											from users
										WHERE Role = 3
										AND user_isactive = 1
										AND is_login_acct  = 1
										ORDER BY Lastname";
		$result_get_reps = mysql_query($qry_get_reps) or die (tdw_mysql_error($qry_get_reps));
		while($row_get_reps = mysql_fetch_array($result_get_reps))
		{
		//for trades for shared rep, do a reverse lookup in the users table to get the id and then the shared reps
		?>
							<option value="<?=$row_get_reps["rr_num"]."^".$row_get_reps["ID"]?>"><?=str_pad($row_get_reps["rep_name"], 20, ".")?>(<?=$row_get_reps["rr_num"]?>)</option>
		<?
		}
		?>
		</select>
		&nbsp;&nbsp;&nbsp;
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
			<option value="2007" <? if ($sel_year == 2007) { echo "selected";}?>>2007</option>
			<option value="2008" <? if ($sel_year == 2008) { echo "selected";}?>>2008</option>
		</select>		
		&nbsp;&nbsp;&nbsp;
		<input type="image" src="images/lf_v1/form_submit.png"/>
		&nbsp;&nbsp;&nbsp;
		<img src="images/lf_v1/exp2excel.png" border="0" />
		</form>
		<!-- End Top Menu -->		
		</td>
	</tr>
</table>
<?
if ($sel_qtr != "" AND $sel_year != "" AND $sel_rep != "") {

//=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>

$rep_and_id = explode("^",$sel_rep);

$rep_to_process = $rep_and_id[0]; //'044';
$user_sales = $rep_and_id[1]; //'221';

//xdebug("rep_to_process",$rep_to_process);
//xdebug("user_sales",$user_sales);

include('pay_analyst_inc_main.php');

include('pay_analyst_inc_main_more.php');

$arr_analysts = create_arr("select ID as k, concat(Lastname, ', ', Firstname) as v from users WHERE Role = 1 and user_isactive = 1", 2);
asort($arr_analysts);
//=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>=<>

	//----^----^----^----^----^----^----^----^----^----^----^----^----^----^----^----^----^----^----^----^
	//// PROCESS FORM SUBMISSION
	if ($frmitem_gtotal) { //FORM HAS BEEN SUBMITTED
	
		//FIND IF FINAL OR JUST SAVE
		//echo "Variables Passed:<br>";
		//show_array($_POST);
		//xdebug("id_save", $frmitem_save);
		//xdebug("id_final",$frmitem_final);
		
		$arr_val_clients = explode(",",$frmitem_clients);
		$arr_val_analysts = explode(",",$frmitem_analysts); 
		//show_array($arr_val_analysts);
		/*
		foreach ($_POST as $name=>$val) {
			//echo $name . " = [". $val ."]<br>";
		}*/
		
		if ($frmitem_final == 0) { //form submitted for SAVE
		
		echo "<font size='1'>Data saved successfully.<br>";
		
			for ($i=1;$i<=$frmitem_rcount;$i++) {
				for ($j=1;$j<=$frmitem_ccount;$j++) {
					$str_varname = $i."|".$j;
						//echo "Analyst = ". " >> " . $arr_val_analysts[$i] . " " . "Client = ". $arr_val_clients[$j] . " >> " . " " . "Allocation = ". " >> " .$$str_varname . "<br>";
						
						//if new insert else update
						$val_count = db_single_val("select count(*) as single_val 
																				from pay_analyst_allocations 
																				where pay_qtr = '".$frmitem_qtr."'
																				and pay_year = '".$frmitem_year."'
																				and pay_sales_id = '".$user_sales."'
																				and pay_advisor_code = '".$arr_val_clients[$j]."'
																				and pay_analyst_id = '".$arr_val_analysts[$i]."'");
						if ($val_count == 0) {	//insert													
								$qry_insert = "INSERT INTO pay_analyst_allocations ( 
															auto_id, 
															pay_qtr,
															pay_year,
															pay_final,
															pay_sales_id,
															pay_advisor_code,
															pay_analyst_id,
															pay_percent,
															pay_lastedited,
															pay_lastedited_by,
															pay_isactive) 
													 VALUES (
														 NULL , 
														 '".$frmitem_qtr."', 
														 '".$frmitem_year."', 
														 '0', 
														 '".$user_sales."', 
														 '".$arr_val_clients[$j]."', 
														 '".$arr_val_analysts[$i]."', 
														 '".$$str_varname."', 
														 NOW( ) , 
														 '".$user_id."', 
														 '1'
														 )";
								$result_insert = mysql_query($qry_insert) or die (tdw_mysql_error($qry_insert));
						} else { //update
								$qry_update = "UPDATE pay_analyst_allocations
															SET
															pay_percent = '".$$str_varname."',
															pay_lastedited =  NOW( ) ,
															pay_lastedited_by = '".$user_id."',
															pay_isactive = 1
															WHERE pay_qtr = '".$frmitem_qtr."'
															and pay_year = '".$frmitem_year."'
															and pay_sales_id = '".$user_sales."'
															and pay_advisor_code = '".$arr_val_clients[$j]."'
															and pay_analyst_id = '".$arr_val_analysts[$i]."'";
								$result_update = mysql_query($qry_update) or die (tdw_mysql_error($qry_update));
						}
		
				}
			}
		} else { //form submitted for finalize
	
		//echo "Form submitted for FINALIZE<br>";
	
			for ($i=1;$i<=$frmitem_rcount;$i++) {
				for ($j=1;$j<=$frmitem_ccount;$j++) {
					$str_varname = $i."|".$j;
						//echo "Analyst = ". " >> " . $arr_val_analysts[$i] . " " . "Client = ". $arr_val_clients[$j] . " >> " . " " . "Allocation = ". " >> " .$$str_varname . "<br>";
						
						//if new insert else update
						$val_count = db_single_val("select count(*) as single_val 
																				from pay_analyst_allocations 
																				where pay_qtr = '".$frmitem_qtr."'
																				and pay_year = '".$frmitem_year."'
																				and pay_sales_id = '".$user_sales."'
																				and pay_advisor_code = '".$arr_val_clients[$j]."'
																				and pay_analyst_id = '".$arr_val_analysts[$i]."'");
						if ($val_count == 0) {	//insert													
								$qry_insert = "INSERT INTO pay_analyst_allocations ( 
															auto_id, 
															pay_qtr,
															pay_year,
															pay_final,
															pay_sales_id,
															pay_advisor_code,
															pay_analyst_id,
															pay_percent,
															pay_lastedited,
															pay_lastedited_by,
															pay_isactive) 
													 VALUES (
														 NULL , 
														 '".$frmitem_qtr."', 
														 '".$frmitem_year."', 
														 '1', 
														 '".$user_sales."', 
														 '".$arr_val_clients[$j]."', 
														 '".$arr_val_analysts[$i]."', 
														 '".$$str_varname."', 
														 NOW( ) , 
														 '".$user_id."', 
														 '1'
														 )";
								$result_insert = mysql_query($qry_insert) or die (tdw_mysql_error($qry_insert));
						} else { //update
								$qry_update = "UPDATE pay_analyst_allocations
															SET
															pay_percent = '".$$str_varname."',
															pay_lastedited =  NOW( ) ,
															pay_lastedited_by = '".$user_id."',
															pay_final = 1,
															pay_isactive = 1
															WHERE pay_qtr = '".$frmitem_qtr."'
															and pay_year = '".$frmitem_year."'
															and pay_sales_id = '".$user_sales."'
															and pay_advisor_code = '".$arr_val_clients[$j]."'
															and pay_analyst_id = '".$arr_val_analysts[$i]."'";
								$result_update = mysql_query($qry_update) or die (tdw_mysql_error($qry_update));
						}
		
				}
			}
		}
	}
	//----^----^----^----^----^----^----^----^----^----^----^----^----^----^----^----^----^----^----^----^
	
	// FIND OUT IF DATA EVER SAVED OR FINALIZED
	$val_saved = db_single_val("select count(*) as single_val 
															from pay_analyst_allocations 
															where pay_qtr = '".$sel_qtr."'
															and pay_year = '".$sel_year."'
															and pay_final = 0
															and pay_sales_id = '".$user_sales."'");
	if ($val_saved > 0) {
		$frm_saved = 1;
	} else {
		$frm_saved = 0;
	}
	
	$val_finalized = db_single_val("select count(*) as single_val 
																from pay_analyst_allocations 
																where pay_qtr = '".$sel_qtr."'
																and pay_year = '".$sel_year."'
																and pay_final = 1
																and pay_sales_id = '".$user_sales."'");
	if ($val_finalized > 0) {
		$frm_finalized = 1;
	} else {
		$frm_finalized = 0;
	}
	
	
	if ($frm_saved == 1 OR $frm_finalized == 1) { // get saved or finalized data 
		$arr_data = array();
		$qry_get_data = "select * from pay_analyst_allocations
											where pay_qtr = '".$sel_qtr."'
											and pay_year = '".$sel_year."'
											and pay_sales_id = '".$user_sales."'";
		//echo $qry_get_data;
		$result_get_data = mysql_query($qry_get_data) or die (tdw_mysql_error($qry_get_data));
		while ( $row_get_data = mysql_fetch_array($result_get_data) ) 
		{
			$arr_data[$row_get_data["pay_analyst_id"]][$row_get_data["pay_advisor_code"]] = round($row_get_data["pay_percent"],0);
		}
		//show_array($arr_data);
	}
	
	//populate array in the form of $i|$j to populate values.
	if ($frm_saved == 1 OR $frm_finalized == 1) {
		$newform = 0;
		$rcount = 1;
		$pop_array = array();
		foreach ($arr_analysts as $ka=>$va) {
			$ccount = 1;
			foreach ($arr_master_clnt_rr as $kc=>$vc) {
			$pop_array[$rcount."|".$ccount] = $arr_data[$ka][$kc];
			$ccount++;
			}
		$rcount++;
		}
	} else {
		$newform = 1;
		$rcount = 1;
		$pop_array = array();
		foreach ($arr_analysts as $ka=>$va) {
			$ccount = 1;
			foreach ($arr_master_clnt_rr as $kc=>$vc) {
			$pop_array[$rcount."|".$ccount] = 0;
			$ccount++;
			}
		$rcount++;
		}
	}
	//show_array($pop_array);
	
	//xdebug("frm_saved",$frm_saved);
	//xdebug("newform",$newform);
	//xdebug("frm_finalized",$frm_finalized);
	//exit;

	if ($frm_saved == 1 or $newform == 1) { //form has been initiated or saved but NOT finalized
	?>
		<form name="frm_payout" method="post" onkeypress="return noenter()"> 
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td width="20" valign="top"> 
					<!-- Analyst List -->
					<table class="tbl_xl" border="0" cellpadding="0" cellspacing="0" >
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="71" /></td>
							<td class="tdx" valign="middle" nowrap="nowrap"><h1>Q<?=$sel_qtr?> <?=$sel_year?>&nbsp;&nbsp;&nbsp;</h1></td></tr>
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="25" /></td>
							<td class="tdx" nowrap="nowrap"><a class="pplname">&nbsp;<!--Totals Commissions--></a></td>
						</tr>	
						<!-- Analyst Array -->
						<?
						$str_analyst_csv = "";
						foreach ($arr_analysts as $k=>$v) {
							$str_analyst_csv = $str_analyst_csv . "," . $k;
						}
						?>
						<input type="hidden" name="frmitem_analysts" id="id_str_analysts" value="<?=$str_analyst_csv?>" />  
						<!-- End Analyst Array -->
						<?
						$rcount = 1;
						foreach ($arr_analysts as $k=>$v) {
						?>
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="25" /></td>
							<td class="tdx" nowrap="nowrap">
								<table width="100%">
									<tr>
										<td class="pplname" valign="top" nowrap="nowrap">&nbsp;&nbsp;<?=$v?></td>
										<td class="pplname" align="right" nowrap="nowrap">&nbsp;<!--: $<a id="at|<?=$rcount?>">0.00</a>--> </td>
									</tr>
								</table>
							</td>
						</tr>		
						<?
						$rcount++;
						}
						?>
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="25" /></td>
							<td class="tdx" nowrap="nowrap">Totals (%)</td>
						</tr>		
					</table>
					<!-- End Analyst List -->
				</td>
				<td width="752" align="left" valign="top">
				<!-- Begin Clients Section -->
				<!-- Client Array -->
				<?
				$str_clnts_csv = "";
				foreach ($arr_master_clnt_rr as $k=>$v) {
					$str_clnts_csv = $str_clnts_csv . "," . $k;
				}
				$str_clntname_psv = "";  //pipe separated values
				foreach ($arr_master_clnt_rr as $k=>$v) {
					$str_clntname_psv = $str_clntname_psv . "|" . trim($v);
				}
				?>
				<input type="hidden" name="frmitem_clients" id="id_str_clients" value="<?=$str_clnts_csv?>" />  
				<input type="hidden" name="frmitem_clientnames" id="id_str_clientnames" value="<?=$str_clntname_psv?>" />  
				<!-- End Client Array -->
				<div id="scrollGrid_1">
					<table class="tbl_xl" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="70" /></td>
							
						<?
						$ccount = 1;
						foreach ($arr_master_clnt_rr as $k=>$v) {
						if ($ccount == ($sole_count + 1)) { echo '<td bgcolor="#666666" class="tdx" valign="top"><a class="pplname">&nbsp;&nbsp;</a></td>'; }
						?>
							<td class="tdx" valign="top"><a class="pplname">&nbsp;&nbsp;<?=str_replace(" ","&nbsp;&nbsp;<br>&nbsp;&nbsp;",trim($v))?></a></td>
						<?
						$ccount++;
						}
						?>
						</tr>
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="25" /></td>
						<?
						$val_total_commission = 0;
						$str_debug = "";
						$j = 1;
						foreach ($arr_master_clnt_rr as $k=>$v) {
							$total_commission = $arr_master_composite[$k];  //arr_composite_primary
							$str_debug = $str_debug . " + " . $total_commission;
							$val_total_commission = $val_total_commission + $total_commission;
						
							if ($j == ($sole_count + 1)) { echo '<td bgcolor="#666666" class="tdx" valign="top"><a class="pplname">&nbsp;&nbsp;</a></td>'; }
						?>
							<td class="tdx" align="right" valign="middle"><a class="pplname" id="tot|<?=$j?>"><?=number_format($total_commission,2,".",",")?></a>&nbsp;&nbsp;</td>
						<?
						$j = $j + 1;
						}
						?>
						</tr>
						<!-- <? echo $val_total_commission ?> -->
						<?
						for ($i=1; $i<($rcount); $i++) {
						?>
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="25" /></td>
							<?
							for ($j=1; $j<($ccount); $j++) {
								if ($j == ($sole_count + 1)) { echo '<td bgcolor="#666666" class="tdx" valign="top"><a class="pplname">&nbsp;&nbsp;</a></td>'; }
							?>
							<td class="tdx">
								<table width="100%">
									<tr>
										<td><input type="text" style="border: 0px;" name="<?=$i."|".$j?>" id="<?=$i."|".$j?>" value="<?=$pop_array[$i."|".$j]?>" size="3" maxlength="3" onchange="xlrecalc('<?=$i."|".$j?>')" onkeyup="return xlmove(event, '<?=$i."|".$j?>')" onfocus="selitem('<?=$i."|".$j?>')"/><!--<a class="num_1">% </a>--></td>
										<td class="num_1" nowrap="nowrap" id="curnum|<?=$i."|".$j?>"></td>
									</tr>
								</table>
							</td>		
							<?
							}
							?>
						</tr>				
						<?
						}
						?>
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="25" /></td>
						<?
						for ($i=1; $i<$ccount; $i++) {
								if ($i == ($sole_count + 1)) { echo '<td bgcolor="#666666" class="tdx" valign="top"><a class="pplname">&nbsp;&nbsp;</a></td>'; }
						?>
							<td class="tdx">&nbsp;<a id="total|<?=$i?>" class="valtotal">0</a></td>
						<?
						}
						?>
						</tr>
					</table>  
				</div>
				<!-- End Clients Section -->
				</td> 
				<td>
				<img src="images/spacer.gif" width="10" height="1" />
				</td>
				<td valign="top">
				<!-- SUMMARY SECTION -->
					<table class="tbl_xl" border="0" cellpadding="0" cellspacing="0" >
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="71" /></td>
							<td colspan="3" class="tdx" valign="top" nowrap="nowrap">
								<table>
									<tr>
										<td valign="top">
											&nbsp;&nbsp;&nbsp;<font face="Arial, Helvetica, sans-serif" size="+2"><strong>SUMMARY</strong></font><br />
										</td>
										<td valign="top" align="right">
											&nbsp;&nbsp;&nbsp;<a class="num_2">Sole: $<?=number_format($sum_sole,2,".",",")?></a><br />
											&nbsp;&nbsp;&nbsp;<a class="num_2">Shared: $<?=number_format($sum_shrd,2,".",",")?></a><br />
											&nbsp;&nbsp;&nbsp;<a class="num_2">Total: $<?=number_format($sum_sole_and_shrd,2,".",",")?></a>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="25" /></td>
							<td class="tdx" nowrap="nowrap"><a class="pplname">Name</a></td>
							<td class="tdx" nowrap="nowrap" align="right">&nbsp;&nbsp;<a class="pplname">$ Allocated</a>&nbsp;&nbsp;</td>
							<td class="tdx" nowrap="nowrap" align="right">&nbsp;&nbsp;<a class="pplname">% of Total</a>&nbsp;&nbsp;</td>
						</tr>		
						<?
						$rcount = 1;
						foreach ($arr_analysts as $k=>$v) {
						?>
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="25" /></td>
							<td class="tdx" nowrap="nowrap"><a class="pplname">&nbsp;&nbsp;<?=$v?></a>&nbsp;&nbsp;</td>

							<td class="tdx" nowrap="nowrap" align="right"><a class="pplname">$</a><a class="pplname" id="sat|<?=$rcount?>">0.00</a>&nbsp;&nbsp;</td>
							<td class="tdx" nowrap="nowrap" align="right"><a class="pplname" id="sap|<?=$rcount?>">0.00</a><a class="pplname">%</a>&nbsp;&nbsp;</td>
						</tr>		
						<?
						$rcount++;
						}
						?>
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="25" /></td> 
							<td class="tdx" nowrap="nowrap">Totals</td>
							<td class="tdx" nowrap="nowrap" align="right"><a class="pplname">$</a><a class="pplname" id="sum_sat_total">0.00</a>&nbsp;&nbsp;</td>
							<td class="tdx" nowrap="nowrap" align="right"><a class="pplname" id="sum_sap_total">0</a><a class="pplname">%</a>&nbsp;&nbsp;</td>
						</tr>		
					</table>
				<!-- END SUMMARY SECTION -->
				</td>
			</tr>
			<tr>
				<td colspan="3" valign="middle"><br />
						<img src="images/spacer.gif" width="20" height="20" />
						<input type="image" src="images/btn_save_1.png" onclick="submit_save()"/>
						<img src="images/spacer.gif" width="10" height="1" />
						<input type="image" src="images/btn_finalize.png" onclick="return submit_final()"/> 
				</td>
			</tr>
		</table>
		<input type="hidden" name="frmitem_rcount" id="id_rcount" value="<?=($rcount-1)?>" />  
		<input type="hidden" name="frmitem_ccount" id="id_ccount" value="<?=($ccount-1)?>" />  
		<input type="hidden" name="frmitem_qtr" id="id_qtr" value="<?=$sel_qtr?>" />  
		<input type="hidden" name="frmitem_year" id="id_year" value="<?=$sel_year?>" />  
		<input type="hidden" name="frmitem_gtotal" id="id_gtotal" value="<?=$val_total_commission?>" />  
		<input type="hidden" name="frmitem_final" id="id_final" value="0" />  
		<input type="hidden" name="frmitem_save" id="id_save" value="0" />  
		</form>
		<script language="javascript">
		xlrecalcform();
		</script>
	<?
	} else { //form has been FINALIZED
	//====================================================================================================
	?>
		<img src="images/login/exclaim.gif" border="0" />&nbsp;<font face="Verdana, Arial, Helvetica, sans-serif" size="1"><b>NOTE: This form has been finalized. You cannot make any changes to it and save.</b></font><br /><br />
		<form name="frm_payout" method="post" onkeypress="return noenter()"> 
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td width="20" valign="top"> 
					<!-- Analyst List -->
					<table class="tbl_xl" border="0" cellpadding="0" cellspacing="0" >
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="71" /></td>
							<td class="tdx" valign="middle" nowrap="nowrap"><h1>Q<?=$sel_qtr?> <?=$sel_year?>&nbsp;&nbsp;&nbsp;</h1></td></tr>
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="25" /></td>
							<td class="tdx" nowrap="nowrap"><a class="pplname">&nbsp;<!--Totals Commissions--></a></td>
						</tr>	
						<!-- Analyst Array -->
						<?
						$str_analyst_csv = "";
						foreach ($arr_analysts as $k=>$v) {
							$str_analyst_csv = $str_analyst_csv . "," . $k;
						}
						?>
						<input type="hidden" name="frmitem_analysts" id="id_str_analysts" value="<?=$str_analyst_csv?>" />  
						<!-- End Analyst Array -->
						<?
						$rcount = 1;
						foreach ($arr_analysts as $k=>$v) {
						?>
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="25" /></td>
							<td class="tdx" nowrap="nowrap">
								<table width="100%">
									<tr>
										<td class="pplname" valign="top" nowrap="nowrap">&nbsp;&nbsp;<?=$v?></td>
										<td class="pplname" align="right" nowrap="nowrap">&nbsp;<!--: $<a id="at|<?=$rcount?>">0.00</a>--> </td>
									</tr>
								</table>
							</td>
						</tr>		
						<?
						$rcount++;
						}
						?>
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="25" /></td>
							<td class="tdx" nowrap="nowrap">Totals (%)</td>
						</tr>		
					</table>
					<!-- End Analyst List -->
				</td>
				<td width="752" align="left" valign="top">
				<!-- Begin Clients Section -->
				<!-- Client Array -->
				<?
				$str_clnts_csv = "";
				foreach ($arr_master_clnt_rr as $k=>$v) {
					$str_clnts_csv = $str_clnts_csv . "," . $k;
				}
				$str_clntname_psv = "";  //pipe separated values
				foreach ($arr_master_clnt_rr as $k=>$v) {
					$str_clntname_psv = $str_clntname_psv . "|" . $v;
				}
				?>
				<input type="hidden" name="frmitem_clients" id="id_str_clients" value="<?=$str_clnts_csv?>" />  
				<input type="hidden" name="frmitem_clientnames" id="id_str_clientnames" value="<?=$str_clntname_psv?>" />  
				<!-- End Client Array -->
				<div id="scrollGrid_1">
					<table class="tbl_xl" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="70" /></td>
							
						<?
						$ccount = 1;
						foreach ($arr_master_clnt_rr as $k=>$v) {
						if ($ccount == ($sole_count + 1)) { echo '<td bgcolor="#666666" class="tdx" valign="top"><a class="pplname">&nbsp;&nbsp;</a></td>'; }
						?>
							<td class="tdx" valign="top"><a class="pplname">&nbsp;&nbsp;<?=str_replace(" ","&nbsp;&nbsp;<br>&nbsp;&nbsp;",trim($v))?></a></td>
						<?
						$ccount++;
						}
						?>
						</tr>
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="25" /></td>
						<?
						$val_total_commission = 0;
						$str_debug = "";
						$j = 1;
						foreach ($arr_master_clnt_rr as $k=>$v) {
							$total_commission = $arr_master_composite[$k];  //arr_composite_primary
							$str_debug = $str_debug . " + " . $total_commission;
							$val_total_commission = $val_total_commission + $total_commission;
						
							if ($j == ($sole_count + 1)) { echo '<td bgcolor="#666666" class="tdx" valign="top"><a class="pplname">&nbsp;&nbsp;</a></td>'; }
						?>
							<td class="tdx" align="right" valign="middle"><a class="pplname" id="tot|<?=$j?>"><?=number_format($total_commission,2,".",",")?></a>&nbsp;&nbsp;</td>
						<?
						$j = $j + 1;
						}
						?>
						</tr>
						<!-- <? echo $val_total_commission ?> -->
						<?
						for ($i=1; $i<($rcount); $i++) {
						?>
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="25" /></td>
							<?
							for ($j=1; $j<($ccount); $j++) {
								if ($j == ($sole_count + 1)) { echo '<td bgcolor="#666666" class="tdx" valign="top"><a class="pplname">&nbsp;&nbsp;</a></td>'; }
							?>
							<td class="tdx">
								<table width="100%">
									<tr>
										<td><input type="text" style="border: 0px;" name="<?=$i."|".$j?>" id="<?=$i."|".$j?>" value="<?=$pop_array[$i."|".$j]?>" size="3" maxlength="3" onchange="xlrecalc('<?=$i."|".$j?>')" onkeyup="return xlmove(event, '<?=$i."|".$j?>')" onfocus="selitem('<?=$i."|".$j?>')"/><!--<a class="num_1">% </a>--></td>
										<td class="num_1" nowrap="nowrap" id="curnum|<?=$i."|".$j?>"></td>
									</tr>
								</table>
							</td>		
							<?
							}
							?>
						</tr>				
						<?
						}
						?>
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="25" /></td>
						<?
						for ($i=1; $i<$ccount; $i++) {
								if ($i == ($sole_count + 1)) { echo '<td bgcolor="#666666" class="tdx" valign="top"><a class="pplname">&nbsp;&nbsp;</a></td>'; }
						?>
							<td class="tdx">&nbsp;<a id="total|<?=$i?>" class="valtotal">0</a></td>
						<?
						}
						?>
						</tr>
					</table>  
				</div>
				<!-- End Clients Section -->
				</td> 
				<td>
				<img src="images/spacer.gif" width="10" height="1" />
				</td>
				<td valign="top">
				<!-- SUMMARY SECTION -->
					<table class="tbl_xl" border="0" cellpadding="0" cellspacing="0" >
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="71" /></td>
							<td colspan="3" class="tdx" valign="top" nowrap="nowrap">
								<table>
									<tr>
										<td valign="top">
											&nbsp;&nbsp;&nbsp;<font face="Arial, Helvetica, sans-serif" size="+2"><strong>SUMMARY</strong></font><br />
										</td>
										<td valign="top" align="right">
											&nbsp;&nbsp;&nbsp;<a class="num_2">Sole: $<?=number_format($sum_sole,2,".",",")?></a><br />
											&nbsp;&nbsp;&nbsp;<a class="num_2">Shared: $<?=number_format($sum_shrd,2,".",",")?></a><br />
											&nbsp;&nbsp;&nbsp;<a class="num_2">Total: $<?=number_format($sum_sole_and_shrd,2,".",",")?></a>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="25" /></td>
							<td class="tdx" nowrap="nowrap"><a class="pplname">Name</a></td>
							<td class="tdx" nowrap="nowrap" align="right">&nbsp;&nbsp;<a class="pplname">$ Allocated</a>&nbsp;&nbsp;</td>
							<td class="tdx" nowrap="nowrap" align="right">&nbsp;&nbsp;<a class="pplname">% of Total</a>&nbsp;&nbsp;</td>
						</tr>		
						<?
						$rcount = 1;
						foreach ($arr_analysts as $k=>$v) {
						?>
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="25" /></td>
							<td class="tdx" nowrap="nowrap"><a class="pplname">&nbsp;&nbsp;<?=$v?></a>&nbsp;&nbsp;</td>
							<td class="tdx" nowrap="nowrap" align="right"><a class="pplname">$</a><a class="pplname" id="sat|<?=$rcount?>">0.00</a>&nbsp;&nbsp;</td>
							<td class="tdx" nowrap="nowrap" align="right"><a class="pplname" id="sap|<?=$rcount?>">0.00</a><a class="pplname">%</a>&nbsp;&nbsp;</td>
						</tr>		
						<?
						$rcount++;
						}
						?>
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="25" /></td>  
							<td class="tdx" nowrap="nowrap">Totals</td>
							<td class="tdx" nowrap="nowrap" align="right"><a class="pplname">$</a><a class="pplname" id="sum_sat_total">0.00</a>&nbsp;&nbsp;</td>
							<td class="tdx" nowrap="nowrap" align="right"><a class="pplname" id="sum_sap_total">0</a><a class="pplname">%</a>&nbsp;&nbsp;</td>
						</tr>		
					</table>
				<!-- END SUMMARY SECTION -->
				</td>
			</tr>
			<tr>
				<td colspan="3" valign="middle"><br /><br /><font face="Verdana, Arial, Helvetica, sans-serif" size="1"><b>NOTE: This form has been finalized. You cannot make any changes to it and save.</b></font><br /><br />

				</td>
			</tr>
		</table>
		<input type="hidden" name="frmitem_rcount" id="id_rcount" value="<?=($rcount-1)?>" />  
		<input type="hidden" name="frmitem_ccount" id="id_ccount" value="<?=($ccount-1)?>" />  
		<input type="hidden" name="frmitem_qtr" id="id_qtr" value="<?=$sel_qtr?>" />  
		<input type="hidden" name="frmitem_year" id="id_year" value="<?=$sel_year?>" />  
		<input type="hidden" name="frmitem_gtotal" id="id_gtotal" value="<?=$val_total_commission?>" />  
		<input type="hidden" name="frmitem_final" id="id_final" value="0" />  
		<input type="hidden" name="frmitem_save" id="id_save" value="0" />  
		</form>
		<script language="javascript">
		xlrecalcform();
		</script>
	<?	
	//====================================================================================================
	}
} else {
echo "&nbsp;&nbsp;Please select Sales Rep., Quarter and Year.<br /><br />";
}

//echo "&nbsp;&nbsp;Program running in debug mode. Please don't use now.<br />";

tep();
?>