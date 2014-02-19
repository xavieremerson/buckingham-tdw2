<?
  include('includes/dbconnect.php');
  include('includes/global.php'); 
	include('includes/functions.php');

	//echo $xl;
	$arr_vals = split('\^',$xl);
	//show_array($arr_vals);
	
	$user_sales = $arr_vals[0]; 
	$rep_to_process = $arr_vals[1];
	$sel_qtr = $arr_vals[2];
	$sel_year = $arr_vals[3];

	include('pay_analyst_adj_excel_inc_main.php');
	include('pay_analyst_adj_excel_inc_main_more.php');

	$arr_analysts = create_arr("select ID as k, concat(Lastname, ', ', Firstname) as v from users WHERE Role = 1 and user_isactive = 1", 2);
	asort($arr_analysts);

	$output_filename = date('mdY_h-ia')."_alloc.csv";

	$fp = fopen($exportlocation.$output_filename, "w");

	// FIND OUT IF DATA EVER SAVED OR FINALIZED
	$val_saved = db_single_val("select count(*) as single_val 
															from pay_analyst_allocations_adj 
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
																from pay_analyst_allocations_adj 
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
		$qry_get_data = "select * from pay_analyst_allocations_adj
											where pay_qtr = '".$sel_qtr."'
											and pay_year = '".$sel_year."'
											and pay_sales_id = '".$user_sales."'";
		//echo $qry_get_data;
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

$string = "\"Q".$sel_qtr." ".$sel_year."\"".chr(13); 
$string .= chr(13);
$string .= '" ",';

foreach($arr_master_clnt_rr as $k=>$v) {
$string .= '"'.trim($v).'",';
}
$string .= '""'.chr(13);

$string .= '" ",';

foreach($arr_master_composite as $k=>$v) {
$string .= '"'.trim($v).'",';
}

	if ($frm_saved == 1 or $newform == 1) { //form has been initiated or saved but NOT finalized
						$rcount = 1;
						foreach ($arr_analysts as $k=>$v) {
							$string .= chr(13).'"'.$v.'",';
							foreach ($pop_array as $m=>$n) { // for lack of better varnames?
								$arr_vals = explode("|",$m);
								if ($arr_vals[0] == $rcount) {
									$string .= '"'.number_format($n,2,".",",").'",';
								}	
							}
						$rcount++;
						}
						$string .= '""'.chr(13);

	} else { //form has been FINALIZED
	//====================================================================================================
						$rcount = 1;
						foreach ($arr_analysts as $k=>$v) {
							$string .= chr(13).'"'.$v.'",';
							foreach ($pop_array as $m=>$n) { // for lack of better varnames?
								$arr_vals = explode("|",$m);
								if ($arr_vals[0] == $rcount) {
									$string .= '"'.number_format($n,2,".",",").'",';
								}	
							}
						$rcount++;
						}
						$string .= '""'.chr(13);

	//====================================================================================================
	}

echo "<pre>";
echo $string;
echo "</pre>";
exit;

fputs ($fp, $string);

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