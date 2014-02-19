<?php

include('../includes/dbconnect.php');
include('../includes/global.php'); 
include('../includes/functions.php'); 

	//FOR DATES OTHER THAN PREVIOUS BUSINESS DAY, CREATE MECHANISM TO HANDLE IT.
	$trade_date_to_process = previous_business_day();
	//$trade_date_to_process = '2006-01-18';
	xdebug('trade_date_to_process',$trade_date_to_process);
  
   	
	//Empty the trad_nfs_raw table
	$_query_empty = "TRUNCATE TABLE nfs_nadd_101";
	$_result_empty = mysql_query($_query_empty) or die(mysql_error());
  
  ////
  // Funtion to check if uploads have been performed for the previous trade date.
		
  //Upload/Parse file TRADREV.TXT
  
    echo "Processing file NABASE.DAT\n<br>";
  
  	$row = 1;
	$name_add_row = 0;
	$fp = fopen ($download_location.$trade_date_to_process."\\NABASE.DAT", "r"); 
	while (!feof ($fp)) { 
		$content = fgets( $fp, 4096 ); 
		if ($row > 1) {

	
		$recnum = substr($content,0,1);
		//xdebug("recnum",$recnum);
			if ($recnum == "1") {
			
				$nadd_firm = substr($content,1,4);
				$nadd_branch = substr($content,5,3);
				$nadd_account_number = substr($content,8,6);
				$nadd_full_account_number = $nadd_branch.$nadd_account_number;
				xdebug("nadd_full_account_number",$nadd_full_account_number);
				$nadd_record_number = substr($content,14,3);

				
				
				if ($nadd_record_number == "101") {
				//Process 101
				xdebug("nadd_record_number",$nadd_record_number);
				
				$nadd_num_confirms = substr($content,18,2);
				$nadd_num_statements = substr($content,20,2);
				$nadd_irs_no = substr($content,22,9);
				$nadd_irs_code = substr($content,31,1);
				$nadd_zip_code = substr($content,32,5);
				$nadd_state_country_code = substr($content,38,3);
				$nadd_short_name = substr($content,44,10);
				xdebug("nadd_short_name",$nadd_short_name);
				$nadd_transfer_legend_code = substr($content,54,1);
				$nadd_managed_account_code = substr($content,55,1);
				$nadd_last_update_code = substr($content,56,1);
				$nadd_last_update_date = substr($content,57,6);
				$nadd_rr_owning_rep = substr($content,63,3);
				xdebug("nadd_rr_owning_rep",$nadd_rr_owning_rep);
				$nadd_trading_auth_code = substr($content,66,1);
				$nadd_employee_code = substr($content,67,1);
				$nadd_citizen_code = substr($content,68,1);
				$nadd_country_tax_residency = substr($content,69,3);
				$nadd_do_not_purge_code = substr($content,72,1);
				$nadd_account_classification = substr($content,73,2);
				$nadd_proceeds_instructions = substr($content,75,1);
				$nadd_securities_instructions = substr($content,76,1);
				$nadd_cash_dividend_instructions = substr($content,77,1);
				$nadd_nobo_code = substr($content,79,1);
				$nadd_commission_class = substr($content,80,1);
				$nadd_commission_discount_percent = substr($content,81,5);
				$nadd_commission_schedule = substr($content,86,2);
				$nadd_parent_branch = substr($content,89,3);
				xdebug("nadd_parent_branch",$nadd_parent_branch);
				$nadd_parent_account = substr($content,92,6);
				xdebug("nadd_parent_account",$nadd_parent_account);
				$nadd_agency_code = substr($content,98,3);
				
				
				} else if ($nadd_record_number == "102") {
				//Process 102
				//echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "103") {
				//Process 101
				//echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "104") {
				//Process 101
				//echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "113") {
				//Process 101
				//echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "115") {
				//Process 101
				//echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "2X0") {
				//Process 101
				echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "2X1") {
				//Process 101
				echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "2X2") {
				//Process 101
				echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "2X3") {
				//Process 101
				echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "901") {
				//Process 101
				echo $nadd_record_number . " detected<br>";
				}	
			
			}
			else if ($recnum == "2") {
			
			
				if ($nadd_record_number == "101") {
				//Process 101
				xdebug("nadd_record_number",$nadd_record_number);

				$nadd_agency_code .= substr($content,1,5);
				xdebug("nadd_agency_code",$nadd_agency_code);
				
				$nadd_rr_exec_rep = substr($content,11,3);
				xdebug("nadd_rr_exec_rep",$nadd_rr_exec_rep);
				$nadd_inst_delivery_code = substr($content,29,1);
				$nadd_inst_delivery_num = substr($content,30,5);
				$nadd_agent_bank_num = substr($content,35,5);
				$nadd_establish_date = substr($content,41,6);
				$nadd_restriction_code = substr($content,47,1);
				$nadd_invest_club_agreement = substr($content,64,1);
				$nadd_joint_account_agreement = substr($content,65,1);
				$nadd_corporate_agreement = substr($content,69,1);
				$nadd_partner_agreement = substr($content,71,1);
				$nadd_margin_agreement = substr($content,72,1);
				$nadd_option_status = substr($content,73,1);
				$nadd_option_agreement = substr($content,74,1);
				$nadd_non_purpose_loan_agreement = substr($content,75,1);
				$nadd_trust_agreement = substr($content,76,1);
				$nadd_new_account_papers = substr($content,77,1);
				$nadd_omnibus_code = substr($content,79,2);

				} else if ($nadd_record_number == "102") {
				//Process 102
				//echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "103") {
				//Process 101
				//echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "104") {
				//Process 101
				//echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "113") {
				//Process 101
				//echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "115") {
				//Process 101
				//echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "2X0") {
				//Process 101
				echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "2X1") {
				//Process 101
				echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "2X2") {
				//Process 101
				echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "2X3") {
				//Process 101
				echo $nadd_record_number . " detected<br>";

				} else if ($nadd_record_number == "901") {
				//Process 101
				echo $nadd_record_number . " detected<br>";
				}	


			}
			else if ($recnum == "3") {

				if ($nadd_record_number == "101") {
				//Process 101
				xdebug("nadd_record_number",$nadd_record_number);

				$nadd_abandoned_property_date = substr($content,6,6);
				$nadd_preferred_customer_code = substr($content,31,3);
				$nadd_zip_code_a = substr($content,34,9);
				$nadd_num_address_lines = substr($content,66,1);
				$nadd_address_line_1 = substr($content,67,32);
				xdebug("nadd_address_line_1",$nadd_address_line_1);
				$nadd_address_line_2 = substr($content,99,2);

				} else if ($nadd_record_number == "102") {
				//Process 102
				//echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "103") {
				//Process 101
				//echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "104") {
				//Process 101
				//echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "113") {
				//Process 101
				//echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "115") {
				//Process 101
				//echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "2X0") {
				//Process 101
				echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "2X1") {
				//Process 101
				echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "2X2") {
				//Process 101
				echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "2X3") {
				//Process 101
				echo $nadd_record_number . " detected<br>";

				} else if ($nadd_record_number == "901") {
				//Process 101
				echo $nadd_record_number . " detected<br>";
				}	


				
			}
			else if ($recnum == "4") {

				if ($nadd_record_number == "101") {
				//Process 101
				xdebug("nadd_record_number",$nadd_record_number);

				$nadd_address_line_2 .= substr($content,1,30);
				xdebug("nadd_address_line_2",$nadd_address_line_2);
				$nadd_address_line_3 = substr($content,31,32);
				xdebug("nadd_address_line_3",$nadd_address_line_3);
				$nadd_address_line_4 = substr($content,63,32);
				xdebug("nadd_address_line_4",$nadd_address_line_4);
				$nadd_address_line_5 = substr($content,95,6);

				} else if ($nadd_record_number == "102") {
				//Process 102
				//echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "103") {
				//Process 101
				//echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "104") {
				//Process 101
				//echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "113") {
				//Process 101
				//echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "115") {
				//Process 101
				//echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "2X0") {
				//Process 101
				echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "2X1") {
				//Process 101
				echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "2X2") {
				//Process 101
				echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "2X3") {
				//Process 101
				echo $nadd_record_number . " detected<br>";

				} else if ($nadd_record_number == "901") {
				//Process 101
				echo $nadd_record_number . " detected<br>";
				}	

				
			}
			else if ($recnum == "5") {

				if ($nadd_record_number == "101") {
				//Process 101
				xdebug("nadd_record_number",$nadd_record_number);

				$nadd_address_line_5 .= substr($content,1,26);
				xdebug("nadd_address_line_5",$nadd_address_line_5);
				$nadd_address_line_6 = substr($content,27,32);
				$nadd_registration_type = substr($content,76,4);
				$nadd_birth_date = substr($content,80,6);
				$nadd_product_level = substr($content,86,2);
				$nadd_birth_date_shadow = substr($content,88,8);
				
//In this case do the processing/insert for nadd_101

$str_sql = "INSERT into nfs_nadd_101 (
nadd_firm,
nadd_branch,
nadd_account_number,
nadd_num_confirms,
nadd_num_statements,
nadd_irs_no,
nadd_irs_code,
nadd_zip_code,
nadd_state_country_code,
nadd_short_name,
nadd_transfer_legend_code,
nadd_managed_account_code,
nadd_last_update_code,
nadd_last_update_date,
nadd_rr_owning_rep,
nadd_trading_auth_code,
nadd_employee_code,
nadd_citizen_code,
nadd_country_tax_residency,
nadd_do_not_purge_code,
nadd_account_classification,
nadd_proceeds_instructions,
nadd_securities_instructions,
nadd_cash_dividend_instructions,
nadd_nobo_code,
nadd_commission_class,
nadd_commission_discount_percent,
nadd_commission_schedule,
nadd_parent_branch,
nadd_parent_account,
nadd_agency_code,
nadd_rr_exec_rep,
nadd_inst_delivery_code,
nadd_inst_delivery_num,
nadd_agent_bank_num,
nadd_establish_date,
nadd_restriction_code,
nadd_invest_club_agreement,
nadd_joint_account_agreement,
nadd_corporate_agreement,
nadd_partner_agreement,
nadd_margin_agreement,
nadd_option_status,
nadd_option_agreement,
nadd_non_purpose_loan_agreement,
nadd_trust_agreement,
nadd_new_account_papers,
nadd_omnibus_code,
nadd_abandoned_property_date,
nadd_preferred_customer_code,
nadd_zip_code_a,
nadd_num_address_lines,
nadd_address_line_1,
nadd_address_line_2,
nadd_address_line_3,
nadd_address_line_4,
nadd_address_line_5,
nadd_address_line_6,
nadd_registration_type,
nadd_birth_date,
nadd_product_level,
nadd_birth_date_shadow)
values(".
"'".trim($nadd_firm)."',".
"'".trim($nadd_branch)."',".
"'".trim($nadd_account_number)."',".
"'".trim($nadd_num_confirms)."',".
"'".trim($nadd_num_statements)."',".
"'".trim($nadd_irs_no)."',".
"'".trim($nadd_irs_code)."',".
"'".trim($nadd_zip_code)."',".
"'".trim($nadd_state_country_code)."',".
"'".trim(str_replace ("'", "\'", $nadd_short_name))."',".
"'".trim($nadd_transfer_legend_code)."',".
"'".trim($nadd_managed_account_code)."',".
"'".trim($nadd_last_update_code)."',".
"'".trim($nadd_last_update_date)."',".
"'".trim($nadd_rr_owning_rep)."',".
"'".trim($nadd_trading_auth_code)."',".
"'".trim($nadd_employee_code)."',".
"'".trim($nadd_citizen_code)."',".
"'".trim($nadd_country_tax_residency)."',".
"'".trim($nadd_do_not_purge_code)."',".
"'".trim($nadd_account_classification)."',".
"'".trim($nadd_proceeds_instructions)."',".
"'".trim($nadd_securities_instructions)."',".
"'".trim($nadd_cash_dividend_instructions)."',".
"'".trim($nadd_nobo_code)."',".
"'".trim($nadd_commission_class)."',".
"'".trim($nadd_commission_discount_percent)."',".
"'".trim($nadd_commission_schedule)."',".
"'".trim($nadd_parent_branch)."',".
"'".trim($nadd_parent_account)."',".
"'".trim($nadd_agency_code)."',".
"'".trim($nadd_rr_exec_rep)."',".
"'".trim($nadd_inst_delivery_code)."',".
"'".trim($nadd_inst_delivery_num)."',".
"'".trim($nadd_agent_bank_num)."',".
"'".trim($nadd_establish_date)."',".
"'".trim($nadd_restriction_code)."',".
"'".trim($nadd_invest_club_agreement)."',".
"'".trim($nadd_joint_account_agreement)."',".
"'".trim($nadd_corporate_agreement)."',".
"'".trim($nadd_partner_agreement)."',".
"'".trim($nadd_margin_agreement)."',".
"'".trim($nadd_option_status)."',".
"'".trim($nadd_option_agreement)."',".
"'".trim($nadd_non_purpose_loan_agreement)."',".
"'".trim($nadd_trust_agreement)."',".
"'".trim($nadd_new_account_papers)."',".
"'".trim($nadd_omnibus_code)."',".
"'".trim($nadd_abandoned_property_date)."',".
"'".trim($nadd_preferred_customer_code)."',".
"'".trim($nadd_zip_code_a)."',".
"'".trim($nadd_num_address_lines)."',".
"'".trim(str_replace ("'", "\'", $nadd_address_line_1))."',".
"'".trim(str_replace ("'", "\'", $nadd_address_line_2))."',".
"'".trim(str_replace ("'", "\'", $nadd_address_line_3))."',".
"'".trim(str_replace ("'", "\'", $nadd_address_line_4))."',".
"'".trim(str_replace ("'", "\'", $nadd_address_line_5))."',".
"'".trim(str_replace ("'", "\'", $nadd_address_line_6))."',".
"'".trim($nadd_registration_type)."',".
"'".trim($nadd_birth_date)."',".
"'".trim($nadd_product_level)."',".
"'".trim($nadd_birth_date_shadow)."')";

				//echo $str_sql."<br><br>";
				$result = mysql_query($str_sql) or die(mysql_error());
				echo "Rows inserted : " . ($name_add_row + 1) ."<br>";	
				$name_add_row = $name_add_row + 1;
				

				} else if ($nadd_record_number == "102") {
				//Process 102
				//echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "103") {
				//Process 101
				//echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "104") {
				//Process 101
				//echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "113") {
				//Process 101
				//echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "115") {
				//Process 101
				//echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "2X0") {
				//Process 101
				echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "2X1") {
				//Process 101
				echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "2X2") {
				//Process 101
				echo $nadd_record_number . " detected<br>";
				} else if ($nadd_record_number == "2X3") {
				//Process 101
				echo $nadd_record_number . " detected<br>";

				} else if ($nadd_record_number == "901") {
				//Process 101
				echo $nadd_record_number . " detected<br>";
				}	

			}
			else {
			echo ""; //Unrecognized recnum encountered! FATAL ERROR!";
			}
		}
	$row = $row + 1;		
	} 
	fclose ($fp); 
  
echo "Process completed!<br>";

//Empty the nfs_nadd table
$_query_empty_nfs_nadd = "TRUNCATE TABLE nfs_nadd";
$_result_empty_nfs_nadd = mysql_query($_query_empty_nfs_nadd) or die(mysql_error());

//temporary procedure to populate main nadd
$str_pop_nadd = "INSERT into nfs_nadd (
nadd_firm,
nadd_branch,
nadd_account_number,
nadd_full_account_number,
nadd_short_name,
nadd_rr_owning_rep,
nadd_rr_exec_rep,
nadd_num_address_lines,
nadd_address_line_1,
nadd_address_line_2,
nadd_address_line_3,
nadd_address_line_4,
nadd_address_line_5,
nadd_address_line_6
) select 
nadd_firm,
nadd_branch,
nadd_account_number,
concat(nadd_branch,nadd_account_number),
nadd_short_name,
nadd_rr_owning_rep,
nadd_rr_exec_rep,
nadd_num_address_lines,
nadd_address_line_1,
nadd_address_line_2,
nadd_address_line_3,
nadd_address_line_4,
nadd_address_line_5,
nadd_address_line_6 from nfs_nadd_101";

	echo $str_pop_nadd."<br><br>";
	$result = mysql_query($str_pop_nadd) or die(mysql_error());	 
	 

?>