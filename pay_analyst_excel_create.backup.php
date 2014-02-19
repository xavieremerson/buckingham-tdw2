<?
	// Same as error_reporting(E_ALL);
	//ini_set('error_reporting', E_ALL);


  include('includes/dbconnect.php');
  include('includes/global.php'); 
	include('includes/functions.php');
	require_once 'Spreadsheet/Excel/Writer.php';

	$arr_xl_cols = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR');

	//echo $xl;
	$arr_vals = split('\^',$xl);
	//show_array($arr_vals);
	
	$user_sales = $arr_vals[0]; 
	$rep_to_process = $arr_vals[1];
	$sel_qtr = $arr_vals[2];
	$sel_year = $arr_vals[3];

	include('pay_analyst_excel_inc_main.php');
	include('pay_analyst_excel_inc_main_more.php');

	
	$arr_analysts = create_arr("select ID as k, concat(Lastname, ', ', Firstname) as v from users WHERE Role = 1 and user_isactive = 1", 2);
	asort($arr_analysts);

	$xlfilename = "Q".$sel_qtr."_".$sel_year."__".substr(md5(rand(1000000000,9999999999)),0,5).".xls";
	

	
	//$xlfilename = "test.xls";
	$wkb = new Spreadsheet_Excel_Writer('data/exports/'.$xlfilename);
	
		//FORMATTING IN THE FOLLOWING FILE
		include('pay_analyst_gen_excel_format.php');
	
		$wks =& $wkb->addWorksheet("Analyst Allocations");
		$wks->setLandscape ();
		$wks->setMarginLeft(0.4);
		$wks->setMarginRight(0.4);
		$wks->setMarginTop(0.5);
		$wks->setMarginBottom(0.4);
		$wks->setFooter ("TDW (Buckingham : Trade Data Warehouse) : Analyst Allocations", $margin=0.5);
		
		$wks->setPaper(1);
	
		//FOLLOWING FILE CONTAINS THE HEADER DATA FOR EXCEL WORKSHEET
		include('pay_analyst_excel_header.php');

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
											and pay_analyst_id != '288'
											and pay_sales_id = '".$user_sales."'";

		$result_get_data = mysql_query($qry_get_data) or die (tdw_mysql_error($qry_get_data));
		while ( $row_get_data = mysql_fetch_array($result_get_data) ) 
		{
			$arr_data[$row_get_data["pay_analyst_id"]][$row_get_data["pay_advisor_code"]] = number_format($row_get_data["pay_percent"],2,".",","); 
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
			$pop_array[$rcount."|".$ccount] = number_format($arr_data[$ka][$kc],2,".",",");
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
			$pop_array[$rcount."|".$ccount] = number_format(0,2,".",",");;
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
	
	$xl_row = 2;
	$xl_col = 1;


	foreach($arr_master_clnt_rr as $k=>$v) {
		$wks->write($xl_row, $xl_col, trim($v), $format_title_2);
		$xl_col = $xl_col + 1;
	}
	
	//col to start summary at
	$hold_col_right = $xl_col + 1;

	//show_array($arr_master_composite);
	$xl_row = $xl_row + 1;
	$xl_col = 1;
	//foreach($arr_master_composite as $k=>$v) {
	foreach($arr_master_clnt_rr as $k=>$v) {
		$wks->write($xl_row, $xl_col, $arr_master_composite[trim($k)], $format_currency_2);
		//xdebug("Debugging going on", $xl_row."/".$xl_col."/".trim($v));
		$xl_col = $xl_col + 1;
	}


  //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	$xl_row = 4;
	$rcount = 1;
	foreach ($arr_analysts as $k=>$v) {
		$xl_col = 0;
		$wks->write($xl_row, $xl_col, $v, $format_data_1);
		$xl_col = 1;
		$arr_vals = array();
		foreach ($pop_array as $m=>$n) { // for lack of better varnames?
			$arr_vals = explode("|",$m);
			if ($arr_vals[0] == $rcount) {
				//xdebug("m",$m);
				$wks->write($xl_row, $xl_col, number_format($n,2,".",","), $format_num1);
				//xdebug("row/col/val",$xl_row."/".$xl_col."/".number_format($n,2,".",","));
				$xl_col = $xl_col + 1;
			}
		}
	$xl_row = $xl_row + 1;
	$rcount++;
	}
	
	$hold_row_bottom = $xl_row - 1;


  //Print Totals Percentage Row
	$wks->write($xl_row, 0, "Total Percentage", $bold10right);
	for($i=1;$i<$xl_col;$i++) {
		$wks->writeFormula($xl_row, $i, "=sum(".$arr_xl_cols[$i].'5'.":".$arr_xl_cols[$i].($xl_row).")", $format_num1);
	}


	//Print Summary
	//$wks->write($hold_row_bottom, $hold_col_right, "Total Percentage", $bold10right);
	for($i=4;$i<$hold_row_bottom+1;$i++) {
		$str_formula = "=";
		for($j=1;$j<$hold_col_right-1;$j++) {
			$str_formula .= $arr_xl_cols[$j].($i+1)."*".$arr_xl_cols[$j]."4"."/100"."+";
		}
		$str_formula = substr($str_formula,0,strlen($str_formula)-1);
		$wks->writeFormula($i, $hold_col_right, $str_formula, $format_currency_2);
		//xdebug("str_formula",$str_formula);
	}


	//Print Total for Row Total Client
		$wks->write(2, $hold_col_right, "TOTAL", $format_title_2);
		$wks->writeFormula(3, $hold_col_right, "=sum(".$arr_xl_cols[1].'4'.":".$arr_xl_cols[$hold_col_right-2]."4".")", $format_currency_2);
		//xdebug(">>>","=sum(".$arr_xl_cols[1].'4'.":".$arr_xl_cols[$hold_col_right-2]."4".")");
		//xdebug(">>>",$hold_col_right - 2);

	//Print Total for Summary Client
	 //xdebug(">> ",$hold_col_right+1);
		$wks->writeFormula($hold_row_bottom+1, $hold_col_right, "=sum(".$arr_xl_cols[$hold_col_right].'5'.":".$arr_xl_cols[$hold_col_right].($hold_row_bottom+1).")", $format_currency_2);
    //xdebug(">>>>","=sum(".$arr_xl_cols[$hold_col_right+1].'5'.":".$arr_xl_cols[$hold_col_right+1].($hold_row_bottom).")");
    //G19
    //xdebug(">>>>",$hold_row_bottom.":".$hold_col_right);
	//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	//echo $xlfilename;
	//exit;

			//$wks->printArea(0,0,$hold_count_row_xls_new_3+2,26);
			//$wks->fitToPages(1,1);

// We still need to explicitly close the workbook
$wkb->close();

//exit;

//fputs ($fp, $string);

//fclose($fp);

//echo "Location: data/exports/"."EmployeeAccounts_".Date("m-d-Y").".csv";

//This works!

//header("Location: data/exports/".$output_filename);
//exit;
$export_file = $xlfilename; //"my_name.xls";
$myFile = "data/exports/".$xlfilename;

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
header('Content-Disposition: attachment; filename="'.basename($xlfilename).'"');
readfile($myFile);
?>