<?
  include('includes/dbconnect.php');
  include('includes/global.php'); 
	include('includes/functions.php');
	require_once 'Spreadsheet/Excel/Writer.php';

	//These values are passed to this page
	//sel_qtr = 2;
	//$sel_year = 2008;
  if ($sel_qtr== "" OR $sel_year == "") {
	  echo "Improper input to program. Please input Qtr. and Year.";
	  exit;
	}

  $arr_pbreak = array();	
	$arr_hold_sales_total_row = array();

	$arr_xl_cols = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');


	$xlfilename = "AnalystAllocations_Q".$sel_qtr."_".$sel_year.".xls";
	//$xlfilename = "test.xls";
	$wkb = new Spreadsheet_Excel_Writer('data/exports/'.$xlfilename);
	
		//FORMATTING IN THE FOLLOWING FILE
		include('pay_analyst_report_gen_excel_format.php');
	
		$wks =& $wkb->addWorksheet("Analyst Allocations Q".$sel_qtr." ".$sel_year);
		$wks->setLandscape();
		$wks->setMarginLeft(0.4);
		$wks->setMarginRight(0.4);
		$wks->setMarginTop(1.0);
		$wks->setMarginBottom(0.4);
		$wks->setHeader ("Analyst Allocations :" . "Q".$sel_qtr." ".$sel_year, $margin=0.5);
		$wks->setFooter ("TDW (Buckingham : Trade Data Warehouse) : Analyst Allocations", $margin=0.5);
		
		//$wks->setPaper(1);
		$wks->setPaper(5);
		$wks->setPrintScale(75);
		//$wks->fitToPages(1,30);
	
		//contains functions used across sales reps
		include('pay_analyst_report_excel_inc_main.php');

		$arr_analysts = create_arr("select ID as k, concat(Lastname, ', ', Firstname) as v from users WHERE Role = 1 and user_isactive = 1", 2);
		asort($arr_analysts);
		$arr_analysts[288] = "BRG (Non allocable)";
		$arr_analysts_lastname = create_arr("select ID as k, concat(Lastname, ', ', SUBSTRING(Firstname,1,1),'.') as v from users WHERE Role = 1 and user_isactive = 1", 2);
		asort($arr_analysts_lastname);
		$arr_analysts_lastname[288] = "BRG (N/A)";

		$arr_valid_salesppl = create_arr("select user_id as v from pay_analyst_users", 1);

		//FOLLOWING FILE CONTAINS THE HEADER DATA FOR EXCEL WORKSHEET
		include('pay_analyst_report_excel_header.php');



		//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
		//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
		//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

		$arr_sales_reps_master = array();
		$arr_processing_values = array();
		$qry_get_reps = "SELECT
											ID, rr_num, concat(Firstname, ' ', Lastname ) as rep_name 
											from users
										WHERE user_isactive = 1
										AND is_login_acct  = 1
										ORDER BY Lastname";
		$result_get_reps = mysql_query($qry_get_reps) or die (tdw_mysql_error($qry_get_reps));
		while($row_get_reps = mysql_fetch_array($result_get_reps))
		{
				$arr_sales_reps_master[$row_get_reps["ID"]] = $row_get_reps["rep_name"];
				$arr_processing_values[$row_get_reps["ID"]] = $row_get_reps["rr_num"];
		}
		//show_array($arr_sales_reps_master);
		//show_array($arr_processing_values);

		$global_xl_row = 2;
		//$global_xl_col = 1;
		
		//foreach($arr_processing_values as $rid=>$rnum) {
		foreach($arr_initials_id as $kx=>$ky) {
		if (in_array($ky,$arr_valid_salesppl)) {	
			//echo $kx.">>".$ky."<br>";
			$rid = $ky;
			$rnum = $arr_processing_values[$ky];
			$user_sales = $rid; 
			$rep_to_process = $rnum;
			
			include('pay_analyst_report_excel_inc_main_more.php');
			
			//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
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

        //=========================================================================
				//Allocations data
				$arr_data = array();
				$qry_get_data = "select * from pay_analyst_allocations
													where pay_qtr = '".$sel_qtr."'
													and pay_year = '".$sel_year."'
													and pay_sales_id = '".$user_sales."'";
				//echo $qry_get_data;
				$result_get_data = mysql_query($qry_get_data) or die (tdw_mysql_error($qry_get_data));
				while ( $row_get_data = mysql_fetch_array($result_get_data) ) 
				{
					$arr_data[$row_get_data["pay_analyst_id"]][$row_get_data["pay_advisor_code"]] = $row_get_data["pay_percent"]; 
				}
        //=========================================================================
				//Adjustments data
				$arr_adj = array();
				$arr_previous_qtr = gpq($sel_qtr,$sel_year);
				$qry_get_adj = "select * from pay_analyst_allocations_adj 
													where pay_qtr = '".$arr_previous_qtr[0]."'
													and pay_year = '".$arr_previous_qtr[1]."'
													and pay_sales_id = '".$user_sales."'
													and pay_isactive = 1";
				//echo $qry_get_adj;
				$result_get_adj = mysql_query($qry_get_adj) or die (tdw_mysql_error($qry_get_adj));
				while ( $row_get_adj = mysql_fetch_array($result_get_adj) ) 
				{
					$arr_adj[$row_get_adj["pay_analyst_id"]][$row_get_adj["pay_advisor_code"]] = $row_get_adj["pay_percent"]; 
				}
				//show_array($arr_adj);
        //=========================================================================
			}



		
			//write section heading
			$xl_col = 1;
			$wks->write($global_xl_row, $xl_col, "ACCT NAME", $arial8bold);
			$xl_col++;
			$wks->write($global_xl_row, $xl_col, "Q".$sel_qtr." ".$sel_year." Total", $arial8bold);
			$xl_col++;
			foreach($arr_analysts_lastname as $ka=>$va) {
				$wks->write($global_xl_row, $xl_col, $va, $arial8);
				$xl_col++;
			}
			$wks->write($global_xl_row, $xl_col, "Total", $arial8bold);
			
			$global_xl_row = $global_xl_row + 1;
			$hold_start_sales_row = $global_xl_row;

			$xl_col = 0;
			foreach($arr_master_clnt_rr as $k=>$v) {
				$wks->write($global_xl_row, $xl_col, $arr_sales_reps_master[$rid], $arial8);
				$wks->write($global_xl_row, $xl_col+1, $v, $arial7);
				$wks->write($global_xl_row, $xl_col+2, $arr_master_composite[$k], $format_currency_arial8);
				
				//fill in the values for analysts
				$local_xl_col = 3;
				foreach($arr_analysts as $kka=>$vva) {
					if ($arr_data[$kka][$k]) {$percentval = $arr_data[$kka][$k];} else {$percentval = 0;}
					//echo $global_xl_row." >> ".$xl_col." >> ". "=C".($global_xl_row+1)."*".$arr_data[$kka][$k]."*0.01"."<br>";
					$wks->writeFormula($global_xl_row, $local_xl_col, "=C".($global_xl_row+1)."*".$percentval."*0.01", $format_currency_arial8);
				  $local_xl_col = $local_xl_col + 1;	
				}
				$wks->writeFormula($global_xl_row, $local_xl_col, "=SUM(D".($global_xl_row+1).":".$arr_xl_cols[$local_xl_col-1].($global_xl_row+1).")", $format_currency_arial8);
				//echo "=SUM(D".($global_xl_row+1).":".$arr_xl_cols[$local_xl_col].($global_xl_row+1).")<br>";

				$global_xl_row = $global_xl_row + 1;
				
				
			}
			
			//write section footer
			$arr_hold_sales_total_row[] = $global_xl_row + 1;
			$wks->write($global_xl_row, 1, "TOTAL", $arial8bold);
			for($zval=2;$zval<=(count($arr_analysts_lastname)+3);$zval++) {
				$wks->writeFormula($global_xl_row, $zval, "=SUM(".$arr_xl_cols[$zval].($hold_start_sales_row+1).":".$arr_xl_cols[$zval].($global_xl_row).")", $format_currency_arial8);
			}
			
			$global_xl_row = $global_xl_row + 2;
			//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
      $arr_pbreak[] = $global_xl_row;
		}
		}

			//write overall totals footer
			$wks->write($global_xl_row, 1, "OVERALL TOTAL", $arial8bold);
			for($zval=2;$zval<=(count($arr_analysts_lastname)+3);$zval++) {
				
				$str_formula_total = "";

				foreach($arr_hold_sales_total_row as $k=>$v) {
					$str_formula_total .= $arr_xl_cols[$zval].($v)."+";
				}
				$str_formula_total = substr($str_formula_total,0,strlen($str_formula_total)-1);
				//echo $str_formula_total;
				$wks->writeFormula($global_xl_row, $zval, "=SUM(".$str_formula_total.")", $curr_arial8b);
			}
			
			$global_xl_row = $global_xl_row + 1;
		
			//write another section heading
			$xl_col = 1;
			$wks->write($global_xl_row, $xl_col, "", $arial8bold);
			$xl_col++;
			$wks->write($global_xl_row, $xl_col, "", $arial8bold);
			$xl_col++;
			foreach($arr_analysts_lastname as $ka=>$va) {
				$wks->write($global_xl_row, $xl_col, $va, $arial8);
				$xl_col++;
			}

			$global_xl_row = $global_xl_row + 1;
			//write 20% section heading
			$wks->write($global_xl_row, 2, "at 20%", $arial8bold);			
			for($zval=3;$zval<=(count($arr_analysts_lastname)+2);$zval++) {
				$wks->writeFormula($global_xl_row, $zval, "=".$arr_xl_cols[$zval].($global_xl_row-1)."*"."0.2", $curr_arial8b);
			}


			$global_xl_row = $global_xl_row + 1;
			//write 65% section heading
			$wks->write($global_xl_row, 2, "at 65%", $arial8bold);			
			for($zval=3;$zval<=(count($arr_analysts_lastname)+2);$zval++) {
				$wks->writeFormula($global_xl_row, $zval, "=".$arr_xl_cols[$zval].($global_xl_row)."*"."0.65", $curr_arial8b);
			}

			$global_xl_row = $global_xl_row + 1;
			//write 65% section heading
			$wks->write($global_xl_row, 2, "at @35% reserve", $arial8bold);			
			for($zval=3;$zval<=(count($arr_analysts_lastname)+2);$zval++) {
				$wks->writeFormula($global_xl_row, $zval, "=".$arr_xl_cols[$zval].($global_xl_row-1)."*"."0.35", $curr_arial8b);
			}

		//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
		//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
		//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

			//$wks->printArea(0,0,$hold_count_row_xls_new_3+2,26);
			//$wks->fitToPages(1,1);

			$wks->setHPagebreaks($arr_pbreak);

// We still need to explicitly close the workbook
$wkb->close();

//print_r($arr_hold_sales_total_row);
?>
&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://192.168.20.63/tdw/fileserve_xls.php?l=data/exports/&f=<?=$xlfilename?>" target="_blank"><img src="images/download_report.gif" border="0"></a>
<?
exit;
?>