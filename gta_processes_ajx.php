<?
ini_set('max_execution_time', 3600);

//print_r($_GET);
//exit;

//autotransmit

  include('includes/dbconnect.php');
  include('includes/global.php');
  include('includes/functions.php');

$date_start = format_date_mdy_to_ymd($valdatefrom);
$date_end = format_date_mdy_to_ymd($valdateto);


$qry_clients = "select * from int_clnt_clients";
$result_clients = mysql_query($qry_clients) or die (tdw_mysql_error($qry_clients));
$arr_clients = array();
$arr_reps = array();
$arr_trader = array();
while ( $row_clients = mysql_fetch_array($result_clients) ) 
{
	$arr_clients[$row_clients["clnt_code"]] = $row_clients["clnt_name"];
	$arr_reps[$row_clients["clnt_code"]] = $row_clients["clnt_rr1"]."^".$row_clients["clnt_rr2"];
	$arr_trader[$row_clients["clnt_code"]] = $row_clients["clnt_trader"];
	
}

//*********************************************************************************************************************************************************
//create array of relevant stepins
$qry_stepin = "select trade_date, symbol, quantity from tradeware_trades_remove_stepins where trade_date between '".$date_start."' and '".$date_end."'";
$arr_stepin = array();
$result_stepin = mysql_query($qry_stepin) or die (tdw_mysql_error($qry_stepin));
while ( $row = mysql_fetch_array($result_stepin) ) {
	$arr_stepin[] = $row["trade_date"]."^".$row["symbol"]."^".$row["quantity"];
}
//*********************************************************************************************************************************************************

//show_array($arr_stepin);

$arr_users = array();
$qry_users = "select * from users";
$result_users = mysql_query($qry_users) or die (tdw_mysql_error($qry_users));
while ( $row_users = mysql_fetch_array($result_users) ) 
{
	$arr_users[$row_users["Initials"]] = $row_users["Fullname"]; 
}


//initiate page load time routine
$time=getmicrotime(); 

	
$str = '"trad_auto_id"|"trad_firm"|"trad_buy_sell"|"trad_trade_date"|"trad_settle_date"|"trad_market_code"|"trad_blotter_code"|"trad_cancel_code"|"trad_streetside_code"|"trad_due_bill"|"trad_correction_code"|"trad_branch"|"trad_account_number"|"trad_full_account_number"|"trad_account_type"|"trad_country_code"|"trad_cusip"|"trad_basis_price_code"|"trad_run_date"|"trad_trade_reference_number"|"trad_user_reference"|"trad_canceled_combined_ref"|"trad_batch"|"trad_count"|"trad_symbol"|"trad_sec_type"|"trad_sec_type_modifier"|"trad_sec_type_calc"|"trad_cns"|"trad_primary_exchange"|"trad_dtc_eligibility_code"|"trad_foreign_code"|"trad_registered_rep"|"trad_state_country_code"|"trad_ny_tax"|"trad_sec_instructions"|"trad_service"|"trad_parent_account"|"trad_agency_code"|"trad_mode_del"|"trad_proceed_instructions"|"trad_income_instructions"|"trad_sales_prod"|"trad_trade_unit"|"trad_short_name"|"trad_acct_classification"|"trad_citizen_code"|"trad_country_of_tax_residency"|"trad_transfer_legend_code"|"trad_marketmaker_code"|"trad_rr_penalty"|"trad_minor_exec_broker"|"trad_minor_clearing_broker"|"trad_offset_account"|"trad_offset_shortname"|"trad_offset_rr"|"trad_offset_commission"|"trad_source"|"trad_type_of_order"|"trad_confirm_print"|"trad_commission_accumulation"|"trad_commission_schedule"|"trad_blotter_override_code"|"trad_nscc_code"|"trad_commission_concession_code"|"trad_quantity"|"trad_price"|"trad_alphaprice_dollar"|"trad_alphaprice_space"|"trad_alphaprice_fraction"|"trad_plus_minus"|"trad_principal"|"trad_accrued_interest"|"trad_trade_commission"|"trad_state_tax"|"trad_sec_fee"|"trad_service_charge_misc_fee"|"trad_net"|"trad_brokerage"|"trad_trade_concession"|"trad_standard_commission"|"trad_sec_desc_1"|"trad_sec_desc_2"|"trad_sec_desc_3"|"trad_sec_desc_4"|"trad_sec_desc_5"|"trad_sec_desc_6"|"trad_sec_desc_7"|"trad_sec_desc_8"|"trad_sec_desc_9"|"trad_confirm_legend_code"|"trad_rr_exec_rep"|"trad_comm_discount_percent"|"trad_strike_price"|"trad_sec_group_code"|"trad_due_bill_multiplier"|"trad_d_market_code"|"trad_d_blotter_code"|"trad_commission_concession_code_a"|"trad_commission_preference_code"|"trad_fund_load_override"|"trad_quantity_type"|"trad_confirm_line"|"trad_exchange_line"|"trad_yield"|"trad_yield_type"|"trad_yield_date"|"trad_yield_price"|"trad_trading_away_code"|"trad_major_clearing_broker"|"trad_major_exec_broker"|"trad_execution_time"|"trad_branch_a"|"trad_irs_no"|"trad_market_place"|"trad_market_sequence"|"trad_market_override"|"trad_time_in_force"|"trad_auto_exec_code"|"trad_issuer"|"trad_issuer_type"|"trad_bond_trader"|"trad_bond_class_code"|"trad_additional_markup"|"trad_terminal_id"|"trad_signon_rep_location"|"trad_rr_signon_rep"|"trad_rr_owning_rep"|"trad_fund_load_percent"|"trad_product_code"|"trad_trading_flat_code"|"trad_12B1_code"|"trad_additional_fee_code_1"|"trad_additional_fee_1"|"trad_additional_fee_code_2"|"trad_additional_fee_2"|"trad_additional_fee_code_3"|"trad_additional_fee_3"|"trad_additional_fee_code_4"|"trad_additional_fee_4"|"trad_additional_fee_code_5"|"trad_additional_fee_5"|"trad_additional_fee_code_6"|"trad_additional_fee_6"|"trad_institutional_third_party"|"trad_institutional_lot_number"|"trad_bord_tord_code"|"trad_mutual_fund_dtc_number"|"trad_trade_entry"|"trad_entry_sequence_number"|"trad_solicited_code"|"trad_elec_trade_id"|"trad_rollup_count"|"trad_revenue_clearing_charge_amt"|"trad_revenue_misc_fee_amt"|"trad_product_level"|"trad_concession_code"|"trad_purchase_type_code"|"trad_trade_definition_type"|"trad_trade_definition_trade_id"|"trad_revenue_commission_sign"|"trad_revenue_commission_amount"|"trad_revenue_concession_sign"|"trad_revenue_concession_amount"|"trad_revenue_load_sign"|"trad_order_reference_number"|"trad_input_commission_sig"|"trad_input_commission_amount"|"trad_original_description_1"|"trad_original_description_2"|"trad_execution_time_a"|"trad_rr_enter_rep"|"trad_clearing_charge_sign"|"trad_clearing_charge"|"trad_execution_fee_sign"|"trad_execution_fee"|"trad_foreign_surcharge_sign"|"trad_foreign_surcharge"|"trad_super_branch"|"client name"|"sales rep"|"trader"|"fill start time"|"fill end time"'."\n";


//Generate a filename with dates.
$gen_filename = "BRG_". $date_start . "_" . $date_end . "_trades.pipe_delimited";

//echo $gen_filename;
//exit;

$fp = fopen("d:\\tdw\\tdw\\".$gen_filename,"w");

fputs($fp,$str); 



$sql = "SELECT * from nfs_trades where trad_branch = 'PDY' and trad_trade_date between '".$date_start."' and '".$date_end."'";   //limit 1, 100";

//echo $sql;

$result = mysql_query($sql) or die(mysql_error());

$i = 1;
while ($row = mysql_fetch_array($result)) {


      //========================================================================================================

			$qry_nfs = "select sum(trad_quantity) as single_val from mry_comm_rr_trades where trad_symbol = '".$row["trad_symbol"]."'
			                         and trad_advisor_code = '".substr($row["trad_short_name"],0,4)."'
															 and trad_trade_date = '".$row["trad_trade_date"]."'
															 and trad_is_cancelled = 0";
			//xdebug("qry_nfs",$qry_nfs);
			$qty_nfs = db_single_val($qry_nfs);

			//echo "Quantity : ".$qty_nfs." for ".substr($row["trad_short_name"],0,4)."<br>";

			//get the data from tradeware data
			$qry_tw = "SELECT Ticker, sum(Quantity) as qty, min(manual_time) as start_time, 
			                  max(manual_time) as end_time FROM `tradeware_trades` 
												where Ticker = '".$row["trad_symbol"]."' 
												AND Datez = '".$row["trad_trade_date"]."' 
												group by Ticker, customer_id"; // ";parent_id
												//avg(fill_price) avgprice, 
			//xdebug("qry_tw",$qry_tw);
			$result_tw = mysql_query($qry_tw) or die(mysql_error());
			$start_time = 'NOT FOUND';
			$end_time = 'NOT FOUND';
			while ($row_tw = mysql_fetch_array($result_tw)) {
					if ($qty_nfs == $row_tw["qty"] or 1==1) {   //
						$start_time = $row_tw["start_time"];
						$end_time = $row_tw["end_time"];
					}
			}
			
			//xdebug("start_time",$start_time);
			
			//========================================================================================================
			//try to figure out step in from table data
			if (!in_array($row["trad_trade_date"]."^".$row["trad_symbol"]."^".round($qty_nfs,0),$arr_stepin) ) { // or 1==1
			//$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
				////echo $row["trad_trade_date"]."^".$row["trad_symbol"]."^".$qty_nfs."<br>";

			$dstr = $row["trad_auto_id"] . "|" . 
							$row["trad_firm"] . "|" . 
							$row["trad_buy_sell"] . "|" . 
							$row["trad_trade_date"] . "|" . 
							$row["trad_settle_date"] . "|" . 
							$row["trad_market_code"] . "|" . 
							$row["trad_blotter_code"] . "|" . 
							$row["trad_cancel_code"] . "|" . 
							$row["trad_streetside_code"] . "|" . 
							$row["trad_due_bill"] . "|" . 
							$row["trad_correction_code"] . "|" . 
							$row["trad_branch"] . "|" . 
							$row["trad_account_number"] . "|" . 
							$row["trad_full_account_number"] . "|" . 
							$row["trad_account_type"] . "|" . 
							$row["trad_country_code"] . "|" . 
							$row["trad_cusip"] . "|" . 
							$row["trad_basis_price_code"] . "|" . 
							$row["trad_run_date"] . "|" . 
							$row["trad_trade_reference_number"] . "|" . 
							$row["trad_user_reference"] . "|" . 
							$row["trad_canceled_combined_ref"] . "|" . 
							$row["trad_batch"] . "|" . 
							$row["trad_count"] . "|" . 
							$row["trad_symbol"] . "|" . 
							$row["trad_sec_type"] . "|" . 
							$row["trad_sec_type_modifier"] . "|" . 
							$row["trad_sec_type_calc"] . "|" . 
							$row["trad_cns"] . "|" . 
							$row["trad_primary_exchange"] . "|" . 
							$row["trad_dtc_eligibility_code"] . "|" . 
							$row["trad_foreign_code"] . "|" . 
							$row["trad_registered_rep"] . "|" . 
							$row["trad_state_country_code"] . "|" . 
							$row["trad_ny_tax"] . "|" . 
							$row["trad_sec_instructions"] . "|" . 
							$row["trad_service"] . "|" . 
							$row["trad_parent_account"] . "|" . 
							$row["trad_agency_code"] . "|" . 
							$row["trad_mode_del"] . "|" . 
							$row["trad_proceed_instructions"] . "|" . 
							$row["trad_income_instructions"] . "|" . 
							$row["trad_sales_prod"] . "|" . 
							$row["trad_trade_unit"] . "|" . 
							$row["trad_short_name"] . "|" . 
							$row["trad_acct_classification"] . "|" . 
							$row["trad_citizen_code"] . "|" . 
							$row["trad_country_of_tax_residency"] . "|" . 
							$row["trad_transfer_legend_code"] . "|" . 
							$row["trad_marketmaker_code"] . "|" . 
							$row["trad_rr_penalty"] . "|" . 
							$row["trad_minor_exec_broker"] . "|" . 
							$row["trad_minor_clearing_broker"] . "|" . 
							$row["trad_offset_account"] . "|" . 
							$row["trad_offset_shortname"] . "|" . 
							$row["trad_offset_rr"] . "|" . 
							$row["trad_offset_commission"] . "|" . 
							$row["trad_source"] . "|" . 
							$row["trad_type_of_order"] . "|" . 
							$row["trad_confirm_print"] . "|" . 
							$row["trad_commission_accumulation"] . "|" . 
							$row["trad_commission_schedule"] . "|" . 
							$row["trad_blotter_override_code"] . "|" . 
							$row["trad_nscc_code"] . "|" . 
							$row["trad_commission_concession_code"] . "|" . 
							$row["trad_quantity"] . "|" . 
							$row["trad_price"] . "|" . 
							$row["trad_alphaprice_dollar"] . "|" . 
							$row["trad_alphaprice_space"] . "|" . 
							$row["trad_alphaprice_fraction"] . "|" . 
							$row["trad_plus_minus"] . "|" . 
							$row["trad_principal"] . "|" . 
							$row["trad_accrued_interest"] . "|" . 
							$row["trad_trade_commission"] . "|" . 
							$row["trad_state_tax"] . "|" . 
							$row["trad_sec_fee"] . "|" . 
							$row["trad_service_charge_misc_fee"] . "|" . 
							$row["trad_net"] . "|" . 
							$row["trad_brokerage"] . "|" . 
							$row["trad_trade_concession"] . "|" . 
							$row["trad_standard_commission"] . "|" . 
							$row["trad_sec_desc_1"] . "|" . 
							$row["trad_sec_desc_2"] . "|" . 
							$row["trad_sec_desc_3"] . "|" . 
							$row["trad_sec_desc_4"] . "|" . 
							$row["trad_sec_desc_5"] . "|" . 
							$row["trad_sec_desc_6"] . "|" . 
							$row["trad_sec_desc_7"] . "|" . 
							$row["trad_sec_desc_8"] . "|" . 
							$row["trad_sec_desc_9"] . "|" . 
							$row["trad_confirm_legend_code"] . "|" . 
							$row["trad_rr_exec_rep"] . "|" . 
							$row["trad_comm_discount_percent"] . "|" . 
							$row["trad_strike_price"] . "|" . 
							$row["trad_sec_group_code"] . "|" . 
							$row["trad_due_bill_multiplier"] . "|" . 
							$row["trad_d_market_code"] . "|" . 
							$row["trad_d_blotter_code"] . "|" . 
							$row["trad_commission_concession_code_a"] . "|" . 
							$row["trad_commission_preference_code"] . "|" . 
							$row["trad_fund_load_override"] . "|" . 
							$row["trad_quantity_type"] . "|" . 
							$row["trad_confirm_line"] . "|" . 
							$row["trad_exchange_line"] . "|" . 
							$row["trad_yield"] . "|" . 
							$row["trad_yield_type"] . "|" . 
							$row["trad_yield_date"] . "|" . 
							$row["trad_yield_price"] . "|" . 
							$row["trad_trading_away_code"] . "|" . 
							$row["trad_major_clearing_broker"] . "|" . 
							$row["trad_major_exec_broker"] . "|" . 
							$row["trad_execution_time"] . "|" . 
							$row["trad_branch_a"] . "|" . 
							$row["trad_irs_no"] . "|" . 
							$row["trad_market_place"] . "|" . 
							$row["trad_market_sequence"] . "|" . 
							$row["trad_market_override"] . "|" . 
							$row["trad_time_in_force"] . "|" . 
							$row["trad_auto_exec_code"] . "|" . 
							$row["trad_issuer"] . "|" . 
							$row["trad_issuer_type"] . "|" . 
							$row["trad_bond_trader"] . "|" . 
							$row["trad_bond_class_code"] . "|" . 
							$row["trad_additional_markup"] . "|" . 
							$row["trad_terminal_id"] . "|" . 
							$row["trad_signon_rep_location"] . "|" . 
							$row["trad_rr_signon_rep"] . "|" . 
							$row["trad_rr_owning_rep"] . "|" . 
							$row["trad_fund_load_percent"] . "|" . 
							$row["trad_product_code"] . "|" . 
							$row["trad_trading_flat_code"] . "|" . 
							$row["trad_12B1_code"] . "|" . 
							$row["trad_additional_fee_code_1"] . "|" . 
							$row["trad_additional_fee_1"] . "|" . 
							$row["trad_additional_fee_code_2"] . "|" . 
							$row["trad_additional_fee_2"] . "|" . 
							$row["trad_additional_fee_code_3"] . "|" . 
							$row["trad_additional_fee_3"] . "|" . 
							$row["trad_additional_fee_code_4"] . "|" . 
							$row["trad_additional_fee_4"] . "|" . 
							$row["trad_additional_fee_code_5"] . "|" . 
							$row["trad_additional_fee_5"] . "|" . 
							$row["trad_additional_fee_code_6"] . "|" . 
							$row["trad_additional_fee_6"] . "|" . 
							$row["trad_institutional_third_party"] . "|" . 
							$row["trad_institutional_lot_number"] . "|" . 
							$row["trad_bord_tord_code"] . "|" . 
							$row["trad_mutual_fund_dtc_number"] . "|" . 
							$row["trad_trade_entry"] . "|" . 
							$row["trad_entry_sequence_number"] . "|" . 
							$row["trad_solicited_code"] . "|" . 
							$row["trad_elec_trade_id"] . "|" . 
							$row["trad_rollup_count"] . "|" . 
							$row["trad_revenue_clearing_charge_amt"] . "|" . 
							$row["trad_revenue_misc_fee_amt"] . "|" . 
							$row["trad_product_level"] . "|" . 
							$row["trad_concession_code"] . "|" . 
							$row["trad_purchase_type_code"] . "|" . 
							$row["trad_trade_definition_type"] . "|" . 
							$row["trad_trade_definition_trade_id"] . "|" . 
							$row["trad_revenue_commission_sign"] . "|" . 
							$row["trad_revenue_commission_amount"] . "|" . 
							$row["trad_revenue_concession_sign"] . "|" . 
							$row["trad_revenue_concession_amount"] . "|" . 
							$row["trad_revenue_load_sign"] . "|" . 
							$row["trad_order_reference_number"] . "|" . 
							$row["trad_input_commission_sig"] . "|" . 
							$row["trad_input_commission_amount"] . "|" . 
							$row["trad_original_description_1"] . "|" . 
							$row["trad_original_description_2"] . "|" . 
							$row["trad_execution_time_a"] . "|" . 
							$row["trad_rr_enter_rep"] . "|" . 
							$row["trad_clearing_charge_sign"] . "|" . 
							$row["trad_clearing_charge"] . "|" . 
							$row["trad_execution_fee_sign"] . "|" . 
							$row["trad_execution_fee"] . "|" . 
							$row["trad_foreign_surcharge_sign"] . "|" . 
							$row["trad_foreign_surcharge"] . "|" . 
							$row["trad_super_branch"];

			//echo $dstr;
			fputs($fp,$dstr); 
			
			//get client name
			$clntname = $arr_clients[substr($row["trad_short_name"],0,4)];
			fputs($fp,"|".$clntname); 

			//get reps
			$arr_repval = explode("^",$arr_reps[substr($row["trad_short_name"],0,4)]);
			if (trim($arr_repval[1]) == "") {
				$str_rep = $arr_users[$arr_repval[0]];
			} else {
			  $str_rep = $arr_users[$arr_repval[0]] . " & " . $arr_users[$arr_repval[1]];
			}
			fputs($fp,"|".$str_rep); 
			
			//get trader
			$trader = $arr_users[$arr_trader[substr($row["trad_short_name"],0,4)]];
			fputs($fp,"|".$trader."|".$start_time."|".$end_time); 

			fputs($fp,"\n"); 
			
			//$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
			}	else {
				//echo "REMOVED >>>>>>>>>>>>>>>>>>>>>>>>>>.".$row["trad_trade_date"]."^".$row["trad_symbol"]."^".$qty_nfs."<br>";
				$removed = 1;
			}	
			
			
			//echo $i . " rows\n";
			$i++;
			ob_flush();
			flush();
}


// close the open said file after writing the text
fclose($fp);

$str_perf = sprintf("%01.2f",((getmicrotime()-$time)/1000));
echo $i. " rows processed in ". $str_perf." s.<br><br>";             
?>
Please <a href="<?=$gen_filename?>">click here</a> to download file.<br /><br />
<?
if ($autotransmit == 1) {
$str_shell_exec = "C:\\curl\\curl -T d:\\tdw\\tdw\\".$gen_filename." --ftp-ssl -k -u buckingham:d46889 ftp://ftp.gtanalytics.com";
shell_exec($str_shell_exec);
echo "File transmitted to GT Analytics.";
}
?>