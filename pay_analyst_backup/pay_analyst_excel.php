<?
  include('includes/dbconnect.php');
  include('includes/global.php'); 
	include('includes/functions.php');

	$rep_to_process = $rr_num;
	$user_sales = $user_id; 

	include('pay_analyst_inc_main.php');
	include('pay_analyst_inc_main_more.php');
	
	$arr_analysts = create_arr("select ID as k, concat(Lastname, ', ', Firstname) as v from users WHERE Role = 1 and user_isactive = 1", 2);
	asort($arr_analysts);

$output_filename = date('mdY_h-ia')."_alloc.csv";

$fp = fopen($exportlocation.$output_filename, "w");

//echo $xl;
$arr_vals = split('\^',$xl);
show_array($arr_vals);


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
			foreach ($arr_clnt_for_rr as $kc=>$vc) {
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
			foreach ($arr_clnt_for_rr as $kc=>$vc) {
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
				foreach ($arr_clnt_for_rr as $k=>$v) {
					$str_clnts_csv = $str_clnts_csv . "," . $k;
				}
				$str_clntname_psv = "";  //pipe separated values
				foreach ($arr_clnt_for_rr as $k=>$v) {
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
						foreach ($arr_clnt_for_rr as $k=>$v) {
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
						foreach ($arr_clnt_for_rr as $k=>$v) {
							$total_commission = $arr_composite_primary[$k];
							$str_debug = $str_debug . " + " . $total_commission;
							$val_total_commission = $val_total_commission + $total_commission;
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
							<td colspan="3" class="tdx" valign="middle" nowrap="nowrap">
								&nbsp;&nbsp;&nbsp;<font face="Arial, Helvetica, sans-serif" size="+2">SUMMARY</font><br />
								&nbsp;&nbsp;&nbsp;<font face="Arial, Helvetica, sans-serif" size="+1">Grand Total: $<?=number_format($val_total_commission,2,".",",")?></font>
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
exit;

$string = "\"Trade Date\",\"ADVISOR / CLIENT\",\"RR #\",\"Symbol\",\"B/S\",\"Shares\",\"Price\",\"Commission\",\"Comm./Shr. ($)"."\"".chr(13); 
fputs ($fp, $string);

	while ( $row_trades = mysql_fetch_array($result_trades) ) {

				if ($row_trades["trad_advisor_name"] == '') {
					$show_trad_advisor_name = $row_trades["trad_advisor_code"];
				} else {
					$show_trad_advisor_name = $row_trades["trad_advisor_name"];
				}
				
				$show_trad_rr = $row_trades["trad_rr"];
				$show_trad_trade_date = $row_trades["trad_trade_date"];
				$show_trad_symbol = $row_trades["trad_symbol"];
				$show_trad_buy_sell = $row_trades["trad_buy_sell"];
				$show_trad_quantity = $row_trades["trad_quantity"];
				$show_trade_price = $row_trades["trade_price"];
				$show_trad_commission = $row_trades["trad_commission"];
				$show_trad_cents_per_share = $row_trades["trad_cents_per_share"];	
				//$running_trad_commission_total = $running_trad_commission_total + $row_trades["trad_commission"];


				//$string = "\"".$row_trades["nadd_advisor"]."\",\"".$row_trades["nadd_short_name"]."\",\"".$row_trades["nadd_rr_exec_rep"]."\",\"".$row_trades["nadd_full_account_number"]."\",\"".$row_trades["nadd_address_line_1"]."\",\"".$row_trades["nadd_address_line_2"]."\",\"".$row_trades["nadd_address_line_3"]."\",\"".$row_trades["nadd_address_line_4"]."\",\"".$row_trades["nadd_address_line_5"]."\",\"".$row_trades["nadd_address_line_6"]."\"\n";
				$string = "\"".$show_trad_trade_date."\",\"".$show_trad_advisor_name."\",\"".$show_trad_rr."\",\"".$show_trad_symbol."\",\"".$show_trad_buy_sell."\",\"".$show_trad_quantity."\",\"".$show_trade_price."\",\"".$show_trad_commission."\",\"".$show_trad_cents_per_share."\"".chr(13); 
		
		fputs ($fp, $string);
	}

	while ( $row_shared_rep_trades = mysql_fetch_array($result_shared_rep_trades) ) {

				if ($row_shared_rep_trades["trad_advisor_name"] == '') {
					$show_trad_advisor_name = $row_shared_rep_trades["trad_advisor_code"];
				} else {
					$show_trad_advisor_name = $row_shared_rep_trades["trad_advisor_name"];
				}
				
				$show_trad_rr = $row_shared_rep_trades["trad_rr"];
				$show_trad_trade_date = $row_shared_rep_trades["trad_trade_date"];
				$show_trad_symbol = $row_shared_rep_trades["trad_symbol"];
				$show_trad_buy_sell = $row_shared_rep_trades["trad_buy_sell"];
				$show_trad_quantity = $row_shared_rep_trades["trad_quantity"];
				$show_trade_price = $row_shared_rep_trades["trade_price"];
				$show_trad_commission = $row_shared_rep_trades["trad_commission"];
				$show_trad_cents_per_share = $row_shared_rep_trades["trad_cents_per_share"];	
				//$running_trad_commission_total = $running_trad_commission_total + $row_trades["trad_commission"];


				//$string = "\"".$row_trades["nadd_advisor"]."\",\"".$row_trades["nadd_short_name"]."\",\"".$row_trades["nadd_rr_exec_rep"]."\",\"".$row_trades["nadd_full_account_number"]."\",\"".$row_trades["nadd_address_line_1"]."\",\"".$row_trades["nadd_address_line_2"]."\",\"".$row_trades["nadd_address_line_3"]."\",\"".$row_trades["nadd_address_line_4"]."\",\"".$row_trades["nadd_address_line_5"]."\",\"".$row_trades["nadd_address_line_6"]."\"\n";
				$string = "\"".$show_trad_trade_date."\",\"".$show_trad_advisor_name."\",\"".$show_trad_rr."\",\"".$show_trad_symbol."\",\"".$show_trad_buy_sell."\",\"".$show_trad_quantity."\",\"".$show_trade_price."\",\"".$show_trad_commission."\",\"".$show_trad_cents_per_share."\"".chr(13); 
		
		fputs ($fp, $string);
	}

fclose($fp);


//echo "Location: data/exports/"."EmployeeAccounts_".Date("m-d-Y").".csv";

//This works!

//header("Location: data/exports/".$output_filename);

$export_file = $output_filename; //"my_name.xls";
$myFile = "data/exports/".$output_filename;

header('Pragma: public');
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");                  // Date in the past    
header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');     // HTTP/1.1 
header('Cache-Control: pre-check=0, post-check=0, max-age=0');    // HTTP/1.1
header ("Pragma: no-cache");
header("Expires: 0");
header('Content-Transfer-Encoding: none');
header('Content-Type: application/vnd.ms-excel;');  // This should work for IE & Opera
header("Content-type: application/x-msexcel");      // This should work for the rest
header('Content-Disposition: attachment; filename="'.basename($output_filename).'"');
readfile($myFile);
?>