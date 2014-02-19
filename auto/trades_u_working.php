<?php

ini_set('max_execution_time', 3600);
//This is a once a day process in the morning.
include('../includes/dbconnect.php');
include('../includes/functions.php'); 
include('../includes/global.php'); 

$trade_date_to_process = '2007-01-02';



	//move data from trad_nfs_raw to archive_trade_data_table
	$_query_move = "INSERT INTO nfs_trades_archive select * from nfs_trades_raw";
	$_result_move = mysql_query($_query_move) or die(mysql_error());
	
	//Empty the trad_nfs_raw table
	$_query_empty = "TRUNCATE TABLE nfs_trades_raw";
	$_result_empty = mysql_query($_query_empty) or die(mysql_error());
  
  	//Upload/Parse file TRADREV.TXT
  
    echo "Processing file TRADREV.TXT\n<br>";
  
  	$row = 1;
	$traderow = 0;
	$fp = fopen ($download_location.$trade_date_to_process."\\TRDREV_TD.DAT", "r"); 
	while (!feof ($fp)) { 
		$content = fgets( $fp, 4096 ); 
		if ($row > 1) {
		
		$recnum = substr($content,0,2);
		//xdebug("recnum",$recnum);
			if ($recnum == "01") {
			    $trad_firm = substr($content,2,4);
				$trad_buy_sell = substr($content,6,1);
				$trad_trade_date = substr($content,7,8);
				$trad_settle_date = substr($content,15,8);
				$trad_market_code = substr($content,23,1);
				$trad_blotter_code = substr($content,24,1);
				$trad_cancel_code = substr($content,25,1);
				$trad_streetside_code = substr($content,26,1);
				$trad_due_bill = substr($content,27,1);
				$trad_correction_code = substr($content,28,1);
				$trad_branch = substr($content,29,3);
				$trad_account_number = substr($content,32,6);
				$trad_account_type = substr($content,38,1);
				$trad_country_code = substr($content,39,2);
				$trad_cusip = substr($content,41,9);
				$trad_basis_price_code = substr($content,55,1);
				$trad_run_date = substr($content,56,8);
				$trad_trade_reference_number = substr($content,64,11);
				$trad_user_reference = substr($content,75,11);
				$trad_canceled_combined_ref = substr($content,86,11);
				$trad_batch = substr($content,97,4);
			}
			else if ($recnum == "02") {

				$trad_batch .= substr($content,2,1);
				$trad_count = substr($content,3,6);
				$trad_symbol = substr($content,9,16);
				$trad_sec_type = substr($content,25,1);
				$trad_sec_type_modifier = substr($content,26,1);
				$trad_sec_type_calc = substr($content,27,1);
				$trad_cns = substr($content,28,1);
				$trad_primary_exchange = substr($content,29,2);
				$trad_dtc_eligibility_code = substr($content,31,1);
				$trad_foreign_code = substr($content,32,1);
				$trad_registered_rep = substr($content,33,3);
				$trad_state_country_code = substr($content,36,3);
				$trad_ny_tax = substr($content,39,1);
				$trad_sec_instructions = substr($content,40,1);
				$trad_service = substr($content,41,1);
				$trad_parent_account = substr($content,42,9);
				$trad_agency_code = substr($content,51,8);
				$trad_mode_del = substr($content,60,1);
				$trad_proceed_instructions = substr($content,61,1);
				$trad_income_instructions = substr($content,62,1);
				$trad_sales_prod = substr($content,63,1);
				$trad_trade_unit = substr($content,64,1);
				$trad_short_name = substr($content,76,10);
				$trad_acct_classification = substr($content,86,2);
				$trad_citizen_code = substr($content,88,1);
				$trad_country_of_tax_residency = substr($content,89,3);
				$trad_transfer_legend_code = substr($content,92,1);
				$trad_marketmaker_code = substr($content,93,1);
				$trad_rr_penalty = substr($content,94,1);
				$trad_minor_exec_broker = substr($content,95,4);
				$trad_minor_clearing_broker = substr($content,99,2);
			}
			else if ($recnum == "03") {

				$trad_minor_clearing_broker .= substr($content,2,2);
				$trad_offset_account = substr($content,4,10);
				$trad_offset_shortname = substr($content,14,10);
				$trad_offset_rr = substr($content,24,3);
				$trad_offset_commission = substr($content,27,7);
				$trad_source = substr($content,37,1);
				$trad_type_of_order = substr($content,38,1);
				$trad_confirm_print = substr($content,39,1);
				$trad_commission_accumulation = substr($content,41,1);
				$trad_commission_schedule = substr($content,42,2);
				$trad_blotter_override_code = substr($content,44,1);
				$trad_nscc_code = substr($content,45,1);
				$trad_commission_concession_code = substr($content,47,1);
				$trad_quantity = substr($content,49,16);
				$trad_price = substr($content,65,18);
				$trad_alphaprice_dollar = substr($content,83,9);
				$trad_alphaprice_space = substr($content,92,1);
				$trad_alphaprice_fraction = substr($content,93,8);
			}
			else if ($recnum == "04") {

				$trad_alphaprice_fraction .= substr($content,2,1);
				$trad_plus_minus = substr($content,3,18);
				$trad_principal = substr($content,21,15);
				$trad_accrued_interest = substr($content,36,12);
				$trad_trade_commission = substr($content,48,10);
				$trad_state_tax = substr($content,58,8);
				$trad_sec_fee = substr($content,66,8);
				$trad_service_charge_misc_fee = substr($content,90,10);
				$trad_net = substr($content,100,1);
			}
			else if ($recnum == "05") {

				$trad_net .= substr($content,2,14);
				$trad_brokerage = substr($content,16,10);
				$trad_trade_concession = substr($content,26,10);
				$trad_standard_commission = substr($content,46,10);
				$trad_sec_desc_1 = substr($content,65,20);
				$trad_sec_desc_2 = substr($content,85,16);
			}
			else if ($recnum == "06") {

				$trad_sec_desc_2 .= substr($content,2,4);

				$trad_sec_desc_3 = substr($content,6,20);
				$trad_sec_desc_4 = substr($content,26,20);
				$trad_sec_desc_5 = substr($content,46,20);
				$trad_sec_desc_6 = substr($content,66,20);
				$trad_sec_desc_7 = substr($content,86,15);
			}
			else if ($recnum == "07") {

				$trad_sec_desc_7 = substr($content,2,5);
				
				$trad_sec_desc_8 = substr($content,7,20);
				$trad_sec_desc_9 = substr($content,27,20);
				
				$trad_confirm_legend_code = substr($content,52,2);
				
				$trad_confirm_legend_code .= substr($content,54,2);
				
				$trad_rr_exec_rep = substr($content,56,3);
				$trad_comm_discount_percent = substr($content,69,10);
				$trad_strike_price = substr($content,79,9);
				$trad_sec_group_code = substr($content,88,1);
				$trad_due_bill_multiplier = substr($content,88,5);
				$trad_d_market_code = substr($content,89,1);
				$trad_d_blotter_code = substr($content,90,1);
				$trad_commission_concession_code_a = substr($content,94,1);
				$trad_commission_preference_code = substr($content,95,1);
				$trad_fund_load_override = substr($content,98,3);
			}
			else if ($recnum == "08") {

				$trad_fund_load_override .= substr($content,2,1);

				$trad_quantity_type = substr($content,3,1);
				$trad_confirm_line = substr($content,4,1);
				$trad_exchange_line = substr($content,5,1);
				$trad_yield = substr($content,6,5);
				$trad_yield_type = substr($content,11,1);
				$trad_yield_date = substr($content,12,7);

				$trad_yield_price = substr($content,19,6);
				$trad_trading_away_code = substr($content,25,1);
				$trad_major_clearing_broker = substr($content,33,4);
				$trad_major_exec_broker = substr($content,37,4);
				$trad_execution_time = substr($content,41,4);
				$trad_branch_a = substr($content,45,3);
				$trad_irs_no = substr($content,48,9);
				$trad_market_place = substr($content,60,5);
				$trad_market_sequence = substr($content,65,6);
				$trad_market_override = substr($content,71,1);
				$trad_time_in_force = substr($content,72,1);
				$trad_auto_exec_code = substr($content,73,1);
				$trad_issuer = substr($content,74,6);
				$trad_issuer_type = substr($content,80,2);
				$trad_bond_trader = substr($content,82,4);
				$trad_bond_class_code = substr($content,86,1);
				$trad_additional_markup = substr($content,87,10);
				$trad_terminal_id = substr($content,97,4);
			}
			else if ($recnum == "09") {

				$trad_signon_rep_location = substr($content,2,5);
				$trad_rr_signon_rep = substr($content,7,3);
				$trad_rr_owning_rep = substr($content,10,3);
				$trad_fund_load_percent = substr($content,13,4);
				$trad_product_code = substr($content,17,12);
				$trad_trading_flat_code = substr($content,29,1);
				$trad_12B1_code = substr($content,30,1);
				$trad_additional_fee_code_1 = substr($content,31,2);
				$trad_additional_fee_1 = substr($content,33,9);
				$trad_additional_fee_code_2 = substr($content,42,2);
				$trad_additional_fee_2 = substr($content,44,9);
				$trad_additional_fee_code_3 = substr($content,53,2); 
				$trad_additional_fee_3 = substr($content,55,9);
				$trad_additional_fee_code_4 = substr($content,64,2);
				$trad_additional_fee_4 = substr($content,66,9);
				$trad_additional_fee_code_5 = substr($content,75,2);
				$trad_additional_fee_5 = substr($content,77,9);
				$trad_additional_fee_code_6 = substr($content,86,2);
				$trad_additional_fee_6 = substr($content,88,9);
				$trad_institutional_third_party = substr($content,97,4);
			}
			else if ($recnum == "10") {

				$trad_institutional_lot_number = substr($content,10,4);
				$trad_bord_tord_code = substr($content,14,1);
				$trad_mutual_fund_dtc_number = substr($content,15,4);
				$trad_trade_entry = substr($content,20,6);
				$trad_entry_sequence_number = substr($content,26,5);
				$trad_solicited_code = substr($content,31,1);

				$trad_elec_trade_id = substr($content,32,3);
				$trad_rollup_count = substr($content,35,3);
			}
			else if ($recnum == "11") {

				$trad_revenue_clearing_charge_amt = substr($content,89,7);
				$trad_revenue_misc_fee_amt = substr($content,97,4);
			}
			else if ($recnum == "12") {

				$trad_revenue_misc_fee_amt .= substr($content,2,3);

				$trad_product_level = substr($content,5,2);
				$trad_concession_code = substr($content,7,1);
				$trad_purchase_type_code = substr($content,8,2);
				$trad_trade_definition_type = substr($content,10,1);
				$trad_trade_definition_trade_id = substr($content,11,9);
				$trad_revenue_commission_sign = substr($content,20,1);
				$trad_revenue_commission_amount = substr($content,21,7);
				$trad_revenue_concession_sign = substr($content,28,1);
				$trad_revenue_concession_amount = substr($content,29,7);
				$trad_revenue_load_sign = substr($content,36,1);
				$trad_order_reference_number = substr($content,44,11);
				$trad_input_commission_sign = substr($content,64,1);
				$trad_input_commission_amount = substr($content,65,10);

				$trad_confirm_legend_code .= substr($content,75,2);
				$trad_confirm_legend_code .= substr($content,77,2);
				
				$trad_original_description_1 = substr($content,81,20);
			}
			else if ($recnum == "13") {

				$trad_original_description_2 = substr($content,2,20);
				$trad_execution_time_a = substr($content,22,6);
				$trad_rr_enter_rep = substr($content,28,3);
				$trad_clearing_charge_sign = substr($content,31,1);
				$trad_clearing_charge = substr($content,32,7);
				$trad_execution_fee_sign = substr($content,39,1);
				$trad_execution_fee = substr($content,40,7);
				$trad_foreign_surcharge_sign = substr($content,47,1);
				$trad_foreign_surcharge = substr($content,48,6);
				$trad_super_branch = substr($content,61,3);

//In this case do the processing/insert.
$str_sql = "INSERT into nfs_trades_raw(
trad_firm,
trad_buy_sell,
trad_trade_date,
trad_settle_date,
trad_market_code,
trad_blotter_code,
trad_cancel_code,
trad_streetside_code,
trad_due_bill,
trad_correction_code,
trad_branch,
trad_account_number,
trad_account_type,
trad_country_code,
trad_cusip,
trad_basis_price_code,
trad_run_date,
trad_trade_reference_number,
trad_user_reference,
trad_canceled_combined_ref,
trad_batch,
trad_count,
trad_symbol,
trad_sec_type,
trad_sec_type_modifier,
trad_sec_type_calc,
trad_cns,
trad_primary_exchange,
trad_dtc_eligibility_code,
trad_foreign_code,
trad_registered_rep,
trad_state_country_code,
trad_ny_tax,
trad_sec_instructions,
trad_service,
trad_parent_account,
trad_agency_code,
trad_mode_del,
trad_proceed_instructions,
trad_income_instructions,
trad_sales_prod,
trad_trade_unit,
trad_short_name,
trad_acct_classification,
trad_citizen_code,
trad_country_of_tax_residency,
trad_transfer_legend_code,
trad_marketmaker_code,
trad_rr_penalty,
trad_minor_exec_broker,
trad_minor_clearing_broker,
trad_offset_account,
trad_offset_shortname,
trad_offset_rr,
trad_offset_commission,
trad_source,
trad_type_of_order,
trad_confirm_print,
trad_commission_accumulation,
trad_commission_schedule,
trad_blotter_override_code,
trad_nscc_code,
trad_commission_concession_code,
trad_quantity,
trad_price,
trad_alphaprice_dollar,
trad_alphaprice_space,
trad_alphaprice_fraction,
trad_plus_minus,
trad_principal,
trad_accrued_interest,
trad_trade_commission,
trad_state_tax,
trad_sec_fee,
trad_service_charge_misc_fee,
trad_net,
trad_brokerage,
trad_trade_concession,
trad_standard_commission,
trad_sec_desc_1,
trad_sec_desc_2,
trad_sec_desc_3,
trad_sec_desc_4,
trad_sec_desc_5,
trad_sec_desc_6,
trad_sec_desc_7,
trad_sec_desc_8,
trad_sec_desc_9,
trad_confirm_legend_code,
trad_rr_exec_rep,
trad_comm_discount_percent,
trad_strike_price,
trad_sec_group_code,
trad_due_bill_multiplier,
trad_d_market_code,
trad_d_blotter_code,
trad_commission_concession_code_a,
trad_commission_preference_code,
trad_fund_load_override,
trad_quantity_type,
trad_confirm_line,
trad_exchange_line,
trad_yield,
trad_yield_type,
trad_yield_date,
trad_yield_price,
trad_trading_away_code,
trad_major_clearing_broker,
trad_major_exec_broker,
trad_execution_time,
trad_branch_a,
trad_irs_no,
trad_market_place,
trad_market_sequence,
trad_market_override,
trad_time_in_force,
trad_auto_exec_code,
trad_issuer,
trad_issuer_type,
trad_bond_trader,
trad_bond_class_code,
trad_additional_markup,
trad_terminal_id,
trad_signon_rep_location,
trad_rr_signon_rep,
trad_rr_owning_rep,
trad_fund_load_percent,
trad_product_code,
trad_trading_flat_code,
trad_12B1_code,
trad_additional_fee_code_1,
trad_additional_fee_1,
trad_additional_fee_code_2,
trad_additional_fee_2,
trad_additional_fee_code_3,
trad_additional_fee_3,
trad_additional_fee_code_4,
trad_additional_fee_4,
trad_additional_fee_code_5,
trad_additional_fee_5,
trad_additional_fee_code_6,
trad_additional_fee_6,
trad_institutional_third_party,
trad_institutional_lot_number,
trad_bord_tord_code,
trad_mutual_fund_dtc_number,
trad_trade_entry,
trad_entry_sequence_number,
trad_solicited_code,
trad_elec_trade_id,
trad_rollup_count,
trad_revenue_clearing_charge_amt,
trad_revenue_misc_fee_amt,
trad_product_level,
trad_concession_code,
trad_purchase_type_code,
trad_trade_definition_type,
trad_trade_definition_trade_id,
trad_revenue_commission_sign,
trad_revenue_commission_amount,
trad_revenue_concession_sign,
trad_revenue_concession_amount,
trad_revenue_load_sign,
trad_order_reference_number,
trad_input_commission_sign,
trad_input_commission_amount,
trad_original_description_1,
trad_original_description_2,
trad_execution_time_a,
trad_rr_enter_rep,
trad_clearing_charge_sign,
trad_clearing_charge,
trad_execution_fee_sign,
trad_execution_fee,
trad_foreign_surcharge_sign,
trad_foreign_surcharge,
trad_super_branch)
values(".
"'".trim($trad_firm)."',".
"'".trim($trad_buy_sell)."',".
"'".trim($trad_trade_date)."',".
"'".trim($trad_settle_date)."',".
"'".trim($trad_market_code)."',".
"'".trim($trad_blotter_code)."',".
"'".trim($trad_cancel_code)."',".
"'".trim($trad_streetside_code)."',".
"'".trim($trad_due_bill)."',".
"'".trim($trad_correction_code)."',".
"'".trim($trad_branch)."',".
"'".trim($trad_account_number)."',".
"'".trim($trad_account_type)."',".
"'".trim($trad_country_code)."',".
"'".trim($trad_cusip)."',".
"'".trim($trad_basis_price_code)."',".
"'".trim($trad_run_date)."',".
"'".trim($trad_trade_reference_number)."',".
"'".trim($trad_user_reference)."',".
"'".trim($trad_canceled_combined_ref)."',".
"'".trim($trad_batch)."',".
"'".trim($trad_count)."',".
"'".trim($trad_symbol)."',".
"'".trim($trad_sec_type)."',".
"'".trim($trad_sec_type_modifier)."',".
"'".trim($trad_sec_type_calc)."',".
"'".trim($trad_cns)."',".
"'".trim($trad_primary_exchange)."',".
"'".trim($trad_dtc_eligibility_code)."',".
"'".trim($trad_foreign_code)."',".
"'".trim($trad_registered_rep)."',".
"'".trim($trad_state_country_code)."',".
"'".trim($trad_ny_tax)."',".
"'".trim($trad_sec_instructions)."',".
"'".trim($trad_service)."',".
"'".trim($trad_parent_account)."',".
"'".trim($trad_agency_code)."',".
"'".trim($trad_mode_del)."',".
"'".trim($trad_proceed_instructions)."',".
"'".trim($trad_income_instructions)."',".
"'".trim($trad_sales_prod)."',".
"'".trim($trad_trade_unit)."',".
"'".trim(str_replace("'","",$trad_short_name))."',".
"'".trim($trad_acct_classification)."',".
"'".trim($trad_citizen_code)."',".
"'".trim($trad_country_of_tax_residency)."',".
"'".trim($trad_transfer_legend_code)."',".
"'".trim($trad_marketmaker_code)."',".
"'".trim($trad_rr_penalty)."',".
"'".trim($trad_minor_exec_broker)."',".
"'".trim($trad_minor_clearing_broker)."',".
"'".trim($trad_offset_account)."',".
"'".trim($trad_offset_shortname)."',".
"'".trim($trad_offset_rr)."',".
"'".trim($trad_offset_commission)."',".
"'".trim($trad_source)."',".
"'".trim($trad_type_of_order)."',".
"'".trim($trad_confirm_print)."',".
"'".trim($trad_commission_accumulation)."',".
"'".trim($trad_commission_schedule)."',".
"'".trim($trad_blotter_override_code)."',".
"'".trim($trad_nscc_code)."',".
"'".trim($trad_commission_concession_code)."',".
"'".trim($trad_quantity)."',".
"'".trim($trad_price)."',".
"'".trim($trad_alphaprice_dollar)."',".
"'".trim($trad_alphaprice_space)."',".
"'".trim($trad_alphaprice_fraction)."',".
"'".trim($trad_plus_minus)."',".
"'".trim($trad_principal)."',".
"'".trim($trad_accrued_interest)."',".
"'".trim($trad_trade_commission)."',".
"'".trim($trad_state_tax)."',".
"'".trim($trad_sec_fee)."',".
"'".trim($trad_service_charge_misc_fee)."',".
"'".trim($trad_net)."',".
"'".trim($trad_brokerage)."',".
"'".trim($trad_trade_concession)."',".
"'".trim($trad_standard_commission)."',".
"'".trim(str_replace("'","\'",$trad_sec_desc_1))."',".
"'".trim(str_replace("'","\'",$trad_sec_desc_2))."',".
"'".trim(str_replace("'","\'",$trad_sec_desc_3))."',".
"'".trim(str_replace("'","\'",$trad_sec_desc_4))."',".
"'".trim(str_replace("'","\'",$trad_sec_desc_5))."',".
"'".trim(str_replace("'","\'",$trad_sec_desc_6))."',".
"'".trim(str_replace("'","\'",$trad_sec_desc_7))."',".
"'".trim(str_replace("'","\'",$trad_sec_desc_8))."',".
"'".trim(str_replace("'","\'",$trad_sec_desc_9))."',".
"'".$trad_confirm_legend_code."',".  //Removed trim as space is needed to determine Cover/Short
"'".trim($trad_rr_exec_rep)."',".
"'".trim($trad_comm_discount_percent)."',".
"'".trim($trad_strike_price)."',".
"'".trim($trad_sec_group_code)."',".
"'".trim($trad_due_bill_multiplier)."',".
"'".trim($trad_d_market_code)."',".
"'".trim($trad_d_blotter_code)."',".
"'".trim($trad_commission_concession_code_a)."',".
"'".trim($trad_commission_preference_code)."',".
"'".trim($trad_fund_load_override)."',".
"'".trim($trad_quantity_type)."',".
"'".trim($trad_confirm_line)."',".
"'".trim($trad_exchange_line)."',".
"'".trim($trad_yield)."',".
"'".trim($trad_yield_type)."',".
"'".trim($trad_yield_date)."',".
"'".trim($trad_yield_price)."',".
"'".trim($trad_trading_away_code)."',".
"'".trim($trad_major_clearing_broker)."',".
"'".trim($trad_major_exec_broker)."',".
"'".trim($trad_execution_time)."',".
"'".trim($trad_branch_a)."',".
"'".trim($trad_irs_no)."',".
"'".trim($trad_market_place)."',".
"'".trim($trad_market_sequence)."',".
"'".trim($trad_market_override)."',".
"'".trim($trad_time_in_force)."',".
"'".trim($trad_auto_exec_code)."',".
"'".trim($trad_issuer)."',".
"'".trim($trad_issuer_type)."',".
"'".trim($trad_bond_trader)."',".
"'".trim($trad_bond_class_code)."',".
"'".trim($trad_additional_markup)."',".
"'".trim($trad_terminal_id)."',".
"'".trim($trad_signon_rep_location)."',".
"'".trim($trad_rr_signon_rep)."',".
"'".trim($trad_rr_owning_rep)."',".
"'".trim($trad_fund_load_percent)."',".
"'".trim($trad_product_code)."',".
"'".trim($trad_trading_flat_code)."',".
"'".trim($trad_12B1_code)."',".
"'".trim($trad_additional_fee_code_1)."',".
"'".trim($trad_additional_fee_1)."',".
"'".trim($trad_additional_fee_code_2)."',".
"'".trim($trad_additional_fee_2)."',".
"'".trim($trad_additional_fee_code_3)."',".
"'".trim($trad_additional_fee_3)."',".
"'".trim($trad_additional_fee_code_4)."',".
"'".trim($trad_additional_fee_4)."',".
"'".trim($trad_additional_fee_code_5)."',".
"'".trim($trad_additional_fee_5)."',".
"'".trim($trad_additional_fee_code_6)."',".
"'".trim($trad_additional_fee_6)."',".
"'".trim($trad_institutional_third_party)."',".
"'".trim($trad_institutional_lot_number)."',".
"'".trim($trad_bord_tord_code)."',".
"'".trim($trad_mutual_fund_dtc_number)."',".
"'".trim($trad_trade_entry)."',".
"'".trim($trad_entry_sequence_number)."',".
"'".trim($trad_solicited_code)."',".
"'".trim($trad_elec_trade_id)."',".
"'".trim($trad_rollup_count)."',".
"'".trim($trad_revenue_clearing_charge_amt)."',".
"'".trim($trad_revenue_misc_fee_amt)."',".
"'".trim($trad_product_level)."',".
"'".trim($trad_concession_code)."',".
"'".trim($trad_purchase_type_code)."',".
"'".trim($trad_trade_definition_type)."',".
"'".trim($trad_trade_definition_trade_id)."',".
"'".trim($trad_revenue_commission_sign)."',".
"'".trim($trad_revenue_commission_amount)."',".
"'".trim($trad_revenue_concession_sign)."',".
"'".trim($trad_revenue_concession_amount)."',".
"'".trim($trad_revenue_load_sign)."',".
"'".trim($trad_order_reference_number)."',".
"'".trim($trad_input_commission_sign)."',".
"'".trim($trad_input_commission_amount)."',".
"'".trim($trad_original_description_1)."',".
"'".trim($trad_original_description_2)."',".
"'".trim($trad_execution_time_a)."',".
"'".trim($trad_rr_enter_rep)."',".
"'".trim($trad_clearing_charge_sign)."',".
"'".trim($trad_clearing_charge)."',".
"'".trim($trad_execution_fee_sign)."',".
"'".trim($trad_execution_fee)."',".
"'".trim($trad_foreign_surcharge_sign)."',".
"'".trim($trad_foreign_surcharge)."',".
"'".trim($trad_super_branch)."')";

				//echo $str_sql."\n<br>\n<br>";
				$result = mysql_query($str_sql) or die("<b>A fatal Database (MySQL) error occured</b>.\n<br />Query: " . $str_sql . "<br />\nError: (" . mysql_errno() . ") " . mysql_error());
				//echo "Rows inserted : " . ($traderow + 1) ."\n<br>";	
				$traderow = $traderow + 1;
			}
			else {
			echo ""; //Unrecognized recnum encountered! FATAL ERROR!";
			}
		}
	$row = $row + 1;		
	} 
	fclose ($fp); 
?>