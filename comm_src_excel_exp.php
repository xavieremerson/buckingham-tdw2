<?

  include('includes/dbconnect.php');
  include('includes/global.php');
  include('includes/functions.php');

  

//show_array($_GET);
//exit;

function show_numbers_pdf($numval) {
		if ($numval == 0) {
			return '<font color="888888">0</font>';
		} else {
			return number_format($numval,0,'.',",");
		}
}	

//show_array($_GET);
//exit;

if ($_GET) {
	//xdebug('datefilterval',$datefilterval);
	$trade_date_to_process = format_date_mdy_to_ymd($datefilterval);
	$arr_repinfo = split('\^',$sel_rep);
	$rep_to_process = $arr_repinfo[0];
	$rep_id = $arr_repinfo[1];
	$rep_name = $arr_repinfo[2];
} else {
  $trade_date_to_process = previous_business_day();

	$qry_get_rep_default = "SELECT
														a.ID, a.rr_num, concat(a.Lastname, ', ', a. Firstname) as rep_name, b.trad_rr 
														from users a, mry_comm_rr_trades b
													WHERE a.rr_num = b.trad_rr
													AND b.trad_rr like '0%'
													AND a.user_isactive = 1
													AND a.Role > 2
													AND a.Role < 5
													GROUP BY b.trad_rr 
													ORDER BY a.Lastname LIMIT 1";
	$result_get_rep_default = mysql_query($qry_get_rep_default) or die (tdw_mysql_error($qry_get_rep_default));
	while ( $row_get_rep_default = mysql_fetch_array($result_get_rep_default) )
					{
						$sel_rep = $row_get_rep_default["trad_rr"]."^".$row_get_rep_default["ID"];
						$rep_name = $row_get_rep_default["rep_name"] . "&nbsp; &nbsp;". "(".$row_get_rep_default["rr_num"].")";
					}
	$arr_repinfo = split('\^',$sel_rep);
	$rep_to_process = $arr_repinfo[0];
	$rep_id = $arr_repinfo[1];

}

//some variables used down below
$arr_commission_clients = array();

//*********************************************************************************************
//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
include('comm_src_inc_main.php');
/*
$input = "Alien";
echo str_pad($input, 10);                      // produces "Alien     "
echo str_pad($input, 10, "-=", STR_PAD_LEFT);  // produces "-=-=-Alien"
echo str_pad($input, 10, "_", STR_PAD_BOTH);   // produces "__Alien___"
echo str_pad($input, 6 , "___");               // produces "Alien_"
*/
//creating array master for primary clients

tsp(100, "COMMISSIONS : As of ".format_date_ymd_to_mdy($trade_date_to_process));

//show post variables
//show_array($_POST);

//get the file as excel

$str_excel = "";

				$str_excel .= '
				<!-- START TABLE 3 -->
					<table width="100%" cellpadding="1", cellspacing="0" >
						<tr>
							<td valign="top"> 
								<!-- START TABLE 4 -->
								<!-- class="tablewithdata" -->
												<table width="100%" cellpadding="1", cellspacing="0" >
													<tr>
														<td valign="top">		
														<table width="100%"  border="0" cellspacing="1" cellpadding="0">
															<tr>
																<td width="260">&nbsp;&nbsp;<strong>CLIENTS</strong></td>
																<td width="40">&nbsp;&nbsp;<strong>RR</strong></td>
																<td width="70" align="right"><strong>'.substr(format_date_ymd_to_mdy($trade_date_to_process),0,5).'</strong>&nbsp;&nbsp;&nbsp;&nbsp;</td>
																<td width="70" align="right"><strong>Comm. MTD</strong>&nbsp;&nbsp;</td>
																<td width="70" align="right"><strong>Comm. QTD</strong>&nbsp;&nbsp;</td>
																<td width="80" align="right"><strong>Comm. YTD</strong>&nbsp;&nbsp;</td>
																<td width="70" align="right"><strong>Check MTD</strong>&nbsp;&nbsp;</td>
																<td width="70" align="right"><strong>Check QTD</strong>&nbsp;&nbsp;</td>
																<td width="70" align="right"><strong>Check YTD</strong>&nbsp;&nbsp;</td>
																<td width="70" align="right"><strong>Total MTD</strong>&nbsp;&nbsp;</td>
																<td width="70" align="right"><strong>Total QTD</strong>&nbsp;&nbsp;</td>
																<td width="80" align="right"><strong>Total YTD</strong>&nbsp;&nbsp;</td>
																<td width="80" align="right"><strong>Last Year</strong></td>
																<td width="50" align="center"> <strong>% of LY</strong> </td>
																<td>&nbsp;</td>
															</tr>';

															$level_a_count = 0;
															$arr_main_processed = array();
															//==========================================================================================
															if ($last_year) {
															 $show_no_activity = 1;
															}
															//show_array($arr_clnt_for_rr);
															//==========================================================================================
															//+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_
															if ($show_no_activity == 1) {
																foreach($arr_clnt_for_rr as $k=>$v) {
																	if ($k != '' AND (get_previous_yr_data($k)>0 OR ($arr_ytd_comm[$k]+$arr_ytd_check[$k])> 0) ) { // AND ($arr_ytd_comm[$k]+$arr_ytd_check[$k])> 0
																			$arr_main_processed[] = $k;
																			//$arr_master[] = str_pad($k, 7).str_pad($arr_clients[$k],40).$arr_day_comm[$row_day_comm["trad_advisor_code"]]."<br>";
																			if(get_previous_yr_data($k)> 0) {
																				$pyc_percent = number_format((($arr_ytd_comm[$k]+$arr_ytd_check[$k])/get_previous_yr_data($k))*100,0,'','');
																			} else {
																				$pyc_percent = 0;
																			}
																			if ($level_a_count % 2) { 
																					$class_row = "trdark";
																			} else { 
																					$class_row = "trlight"; 
																			} 
																		//get_previous_yr_data($k)
																		$str_excel .= '
																		  <tr class="'.$class_row.'" >
																			<td valign="middle" nowrap="nowrap">&nbsp;&nbsp;'.look_up_client($k).'</td>
																			<td>&nbsp;&nbsp;'.$rep_to_process.'</td>
																			<td align="right">'.show_numbers($arr_day_comm[$k]).'</td>
																			<td align="right">'.show_numbers($arr_mtd_comm[$k]).'</td>
																			<td align="right">'.show_numbers($arr_qtd_comm[$k]).'</td>
																			<td align="right">'.show_numbers($arr_ytd_comm[$k]).'</td>
																			<td align="right">'.show_numbers($arr_mtd_check[$k]).'</td>
																			<td align="right">'.show_numbers($arr_qtd_check[$k]).'</td>
																			<td align="right">'.show_numbers($arr_ytd_check[$k]).'</td>
																			<td align="right">'.show_numbers($arr_mtd_comm[$k]+$arr_mtd_check[$k]).'</td>
																			<td align="right">'.show_numbers($arr_qtd_comm[$k]+$arr_qtd_check[$k]).'</td>
																			<td align="right">'.show_numbers($arr_ytd_comm[$k]+$arr_ytd_check[$k]).'</td>
																			<td align="right">'.show_numbers(get_previous_yr_data($k)).'</td>
																			<td align="right">'.$pyc_percent.'</td> 
																			<td align="right">&nbsp;</td>
																		</tr>';

																		$total_pbd = $total_pbd + $arr_day_comm[$k];
																		$total_mtd = $total_mtd + $arr_mtd_comm[$k];
																		$total_qtd = $total_qtd + $arr_qtd_comm[$k];
																		$total_ytd = $total_ytd + $arr_ytd_comm[$k];
																		$total_cmtd = $total_cmtd + $arr_mtd_check[$k]; 
																		$total_cqtd = $total_cqtd + $arr_qtd_check[$k]; 
																		$total_cytd = $total_cytd + $arr_ytd_check[$k];  
																		$total_grand_mtd = $total_grand_mtd + $arr_mtd_comm[$k]+$arr_mtd_check[$k];
																		$total_grand_qtd = $total_grand_qtd + $arr_qtd_comm[$k]+$arr_qtd_check[$k];
																		$total_grand_ytd = $total_grand_ytd + $arr_ytd_comm[$k]+$arr_ytd_check[$k];
																		
																		$total_grand_ytd_ly = $total_grand_ytd_ly + get_previous_yr_data($k);
			
																		$level_a_count = $level_a_count + 1;			
																	}
																}	
															} else {
																foreach($arr_clnt_for_rr as $k=>$v) {
																	if ($k != '' AND ($arr_ytd_comm[$k]+$arr_ytd_check[$k])> 0) { // 
																			$arr_main_processed[] = $k;
																			//$arr_master[] = str_pad($k, 7).str_pad($arr_clients[$k],40).$arr_day_comm[$row_day_comm["trad_advisor_code"]]."<br>";
																			if(get_previous_yr_data($k)> 0) {
																				$pyc_percent = number_format((($arr_ytd_comm[$k]+$arr_ytd_check[$k])/get_previous_yr_data($k))*100,0,'','');
																			} else {
																				$pyc_percent = 0;
																			}
																			if ($level_a_count % 2) { 
																					$class_row = "trdark";
																			} else { 
																					$class_row = "trlight"; 
																			} 
			
																		$str_excel .= '
																		<tr class="'.$class_row.'" >
																			<td valign="middle" nowrap="nowrap">&nbsp;&nbsp;'.look_up_client($k).'</td>
																			<td>&nbsp;&nbsp;'.$rep_to_process.'</td>
																			<td align="right">'.show_numbers($arr_day_comm[$k]).'</td>
																			<td align="right">'.show_numbers($arr_mtd_comm[$k]).'</td>
																			<td align="right">'.show_numbers($arr_qtd_comm[$k]).'</td>
																			<td align="right">'.show_numbers($arr_ytd_comm[$k]).'</td>
																			<td align="right">'.show_numbers($arr_mtd_check[$k]).'</td>
																			<td align="right">'.show_numbers($arr_qtd_check[$k]).'</td>
																			<td align="right">'.show_numbers($arr_ytd_check[$k]).'</td>
																			<td align="right">'.show_numbers($arr_mtd_comm[$k]+$arr_mtd_check[$k]).'</td>
																			<td align="right">'.show_numbers($arr_qtd_comm[$k]+$arr_qtd_check[$k]).'</td>
																			<td align="right">'.show_numbers($arr_ytd_comm[$k]+$arr_ytd_check[$k]).'</td>
																			<td align="right">'.show_numbers(get_previous_yr_data($k)).'</td>
																			<td align="right">'.$pyc_percent.'</td> 
																			<td align="right">&nbsp;</td>
																		</tr>';

																		$total_pbd = $total_pbd + $arr_day_comm[$k];
																		$total_mtd = $total_mtd + $arr_mtd_comm[$k];
																		$total_qtd = $total_qtd + $arr_qtd_comm[$k];
																		$total_ytd = $total_ytd + $arr_ytd_comm[$k];
																		$total_cmtd = $total_cmtd + $arr_mtd_check[$k]; 
																		$total_cqtd = $total_cqtd + $arr_qtd_check[$k]; 
																		$total_cytd = $total_cytd + $arr_ytd_check[$k];  
																		$total_grand_mtd = $total_grand_mtd + $arr_mtd_comm[$k]+$arr_mtd_check[$k];
																		$total_grand_qtd = $total_grand_qtd + $arr_qtd_comm[$k]+$arr_qtd_check[$k];
																		$total_grand_ytd = $total_grand_ytd + $arr_ytd_comm[$k]+$arr_ytd_check[$k];

																		$total_grand_ytd_ly = $total_grand_ytd_ly + get_previous_yr_data($k);
			
																		$level_a_count = $level_a_count + 1;			
																	}
																}	
															}												
															//+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_
													 $str_excel .= '
														</table>
													</td>
												</tr>
											</table>
											<!-- END TABLE 4 -->
										</td>
									</tr>
									<tr id="shrd">
										<td>
												<table width="100%" cellpadding="1", cellspacing="0" >
													<tr>
														<td valign="top">		
														<table width="100%"  border="0" cellspacing="1" cellpadding="0">';

															$level_b_count = 0;
															foreach($arr_relevant_shared_clients as $k=>$v) {
																if ($k != '') {
		
																		if(get_previous_yr_data($k)>0) {
																			$pyc_percent = number_format((($arr_ytd_comm[$k]+$arr_ytd_check[$k])/get_previous_yr_data($k))*100,0,'',",");
																		} else {
																			$pyc_percent = 0;
																		}
		
																		if ($level_b_count % 2) { 
																				$class_row = "trdark";
																		} else { 
																				$class_row = "trlight"; 
																		} 
		
																	if (!in_array($k, $arr_main_processed)) {
																		$val_chek_mtd = $arr_mtd_check_shrd[$k];
																		$val_chek_qtd = $arr_qtd_check_shrd[$k];
																		$val_chek_ytd = $arr_ytd_check_shrd[$k];
																	} else {
																		$val_chek_mtd = 0;
																		$val_chek_qtd = 0;
																		$val_chek_ytd = 0;
																	}
		
													 			$str_excel .= '
																	<tr class="'.$class_row.'" >
																		<td valign="middle" nowrap="nowrap">&nbsp;&nbsp;'.look_up_client($k).'</td>
																		<td>&nbsp;&nbsp;'.$v.'</td>
																		<td align="right">'.show_numbers($arr_day_comm_shrd[$k]).'</td>
																		<td align="right">'.show_numbers($arr_mtd_comm_shrd[$k]).'</td>
																		<td align="right">'.show_numbers($arr_qtd_comm_shrd[$k]).'</td>
																		<td align="right">'.show_numbers($arr_ytd_comm_shrd[$k]).'</td>
																		<td align="right">'.show_numbers($val_chek_mtd).'</td>
																		<td align="right">'.show_numbers($val_chek_qtd).'</td>
																		<td align="right">'.show_numbers($val_chek_ytd).'</td>
																		<td align="right">'.show_numbers($arr_mtd_comm_shrd[$k]+$val_chek_mtd).'</td>
																		<td align="right">'.show_numbers($arr_qtd_comm_shrd[$k]+$val_chek_qtd).'</td>
																		<td align="right">'.show_numbers($arr_ytd_comm_shrd[$k]+$val_chek_ytd).'</td>
																		<td align="right">'.show_numbers(get_previous_yr_data($k)).'</td>
																		<td align="right">'.show_numbers($pyc_percent).'</td> 
																		<td align="right">&nbsp;</td>
																	</tr>';

																	$total_pbd_shrd = $total_pbd_shrd + $arr_day_comm_shrd[$k];
																	$total_mtd_shrd = $total_mtd_shrd + $arr_mtd_comm_shrd[$k];
																	$total_qtd_shrd = $total_qtd_shrd + $arr_qtd_comm_shrd[$k];
																	$total_ytd_shrd = $total_ytd_shrd + $arr_ytd_comm_shrd[$k];
																	$total_cmtd_shrd = $total_cmtd_shrd + $val_chek_mtd; 
																	$total_cqtd_shrd = $total_cqtd_shrd + $val_chek_qtd; 
																	$total_cytd_shrd = $total_cytd_shrd + $val_chek_ytd;  
																	$total_grand_mtd_shrd = $total_grand_mtd_shrd + $arr_mtd_comm_shrd[$k]+$val_chek_mtd;
																	$total_grand_qtd_shrd = $total_grand_qtd_shrd + $arr_qtd_comm_shrd[$k]+$val_chek_qtd;
																	$total_grand_ytd_shrd = $total_grand_ytd_shrd + $arr_ytd_comm_shrd[$k]+$val_chek_ytd;

																	$total_grand_ytd_shrd_ly = $total_grand_ytd_shrd_ly + get_previous_yr_data($k);

																	$level_b_count = $level_b_count + 1;			
															}
														}													
													  $str_excel .= '
														</table>
													</td>
												</tr>
											</table>
									<!-- END TABLE 4 -->
										</td>
							</tr>
							</table>
							<!-- END TABLE 3 -->';
							
							
$output_filename = "commissions_".substr(md5(rand(1000000000,9999999999)),0,1).".xls";
$fp = fopen($exportlocation.$output_filename, "w");
fputs ($fp, $str_excel);
fclose($fp);		
Header("Location: http://192.168.20.63/tdw/fileserve_xls.php?l=data/exports/&f=".$output_filename);
?>