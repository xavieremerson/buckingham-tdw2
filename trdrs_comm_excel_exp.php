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

if ($_GET) {
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
?>



<?
include('trdr_comm_inc_main.php');
tsp(100,"Trader (".$userfullname.") : COMMISSIONS : As of ".format_date_ymd_to_mdy($trade_date_to_process));


$str_excel = "";

$str_excel .= '
				<!-- START TABLE 3 -->
					<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#FFFFFF">
						<tr>
							<td valign="top"> 
								<!-- START TABLE 4 -->
								<!-- class="tablewithdata" -->								         
									<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#ffffff">
										<tr>
											<td valign="top"> 
            
                        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
                          <tr bgcolor="#333333"> 
                            <td bgcolor="#ffffff"  width="240"><a class="tblhead_a">&nbsp;&nbsp;&nbsp;&nbsp;ADVISOR / CLIENT (PRIMARY)</a></td>
                            <td bgcolor="#ffffff"  width="40"><a class="tblhead_a">&nbsp;&nbsp;RR</a></td>
                            <td bgcolor="#ffffff"  width="70" align="right"><a class="tblhead_a">TRD. DATE'.substr(format_date_ymd_to_mdy($trade_date_to_process),0,5).'&nbsp;&nbsp;&nbsp;&nbsp;</a></td>
                            <td bgcolor="#ffffff"  width="70" align="right"><a class="tblhead_a">COMM. MTD&nbsp;&nbsp;</a></td>
                            <td bgcolor="#ffffff"  width="70" align="right"><a class="tblhead_a">COMM. QTD&nbsp;&nbsp;</a></td>
                            <td bgcolor="#ffffff"  width="80" align="right"><a class="tblhead_a">COMM. YTD&nbsp;&nbsp;</a></td>
                            <td bgcolor="#ffffff" width="70" align="right"><a class="tblhead_a">CHK. MTD&nbsp;&nbsp;</a></td>
                            <td bgcolor="#ffffff" width="70" align="right"><a class="tblhead_a">CHK. QTD&nbsp;&nbsp;</a></td>
                            <td bgcolor="#ffffff" width="70" align="right"><a class="tblhead_a">CHK. YTD&nbsp;&nbsp;</a></td>
                            <td width="70" bgcolor="#ffffff" align="right"><a class="tblhead_a">TOT. MTD&nbsp;&nbsp;</a></td>
                            <td width="70" bgcolor="#ffffff" align="right"><a class="tblhead_a">TOT. QTD&nbsp;&nbsp;</a></td>
                            <td width="80" bgcolor="#ffffff" align="right"><a class="tblhead_a">TOT. YTD&nbsp;&nbsp;</a></td>
                            <td width="80" bgcolor="#ffffff" align="right"><a class="tblhead_a">LAST YEAR</a></td>
                            <td width="80" bgcolor="#ffffff" align="center"><a class="tblhead_a">% of LY </a></td>
                            <td bgcolor="#ffffff">&nbsp;</td>
                          </tr>';

													//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

													//show_array($arr_show_clnt_rr);
													$level_a_count = 0;
													foreach($arr_show_clnt_rr as $zk=>$zv) {
														$tmp_arr_vals = explode("#",$zv);
														$k = $zv;
														$zclnt = $tmp_arr_vals[0];
														$zrr =   $tmp_arr_vals[1];
														//$v = $tmp_arr_vals[1];
														if ($k != '' ) {  //AND ($arr_ytd_comm[$k]+$arr_ytd_check[$k])> 0

																//$arr_master[] = str_pad($k, 7).str_pad($arr_clients[$k],40).$arr_day_comm[$row_day_comm["trad_advisor_code"]]."<br>";
																if(get_previous_yr_data($tmp_arr_vals[0])> 0) {
																	$pyc_percent = number_format((($arr_ytd_comm[$k]+$arr_ytd_check_new[$zclnt])/get_previous_yr_data($tmp_arr_vals[0]))*100,0,'','');
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
																<td valign="middle">&nbsp;&nbsp;
																&nbsp;
																'.look_up_client($zclnt).'</td>
																<td>&nbsp;&nbsp;'.$zrr.'</td>
																<td align="right">'.show_numbers($arr_day_comm[$k]).'</td>
																<td align="right">'.show_numbers($arr_mtd_comm[$k]).'</td>
																<td align="right">'.show_numbers($arr_qtd_comm[$k]).'</td>
																<td align="right">'.show_numbers($arr_ytd_comm[$k]).'</td>
																<td align="right">'.show_numbers($arr_mtd_check_new[$zclnt]).'</td>
																<td align="right">'.show_numbers($arr_qtd_check_new[$zclnt]).'</td>
																<td align="right">'.show_numbers($arr_ytd_check_new[$zclnt]).'</td>
																<td align="right">'.show_numbers($arr_mtd_comm[$k]+$arr_mtd_check_new[$zclnt]).'</td>
																<td align="right">'.show_numbers($arr_qtd_comm[$k]+$arr_qtd_check_new[$zclnt]).'</td>
																<td align="right">'.show_numbers($arr_ytd_comm[$k]+$arr_ytd_check_new[$zclnt]).'</td>
																<td align="right">'.show_numbers(get_previous_yr_data($tmp_arr_vals[0])).'</td>
																<td align="right">'.$pyc_percent.'</td> 
																<td align="right">&nbsp;</td>
															</tr>';

															$total_pbd = $total_pbd + $arr_day_comm[$k];
															$total_mtd = $total_mtd + $arr_mtd_comm[$k];
															$total_qtd = $total_qtd + $arr_qtd_comm[$k];
															$total_ytd = $total_ytd + $arr_ytd_comm[$k];
															$total_cmtd = $total_cmtd + $arr_mtd_check_new[$zclnt]; 
															$total_cqtd = $total_cqtd + $arr_qtd_check_new[$zclnt]; 
															$total_cytd = $total_cytd + $arr_ytd_check_new[$zclnt];  
															$total_grand_mtd = $total_grand_mtd + $arr_mtd_comm[$k]+$arr_mtd_check_new[$zclnt];
															$total_grand_qtd = $total_grand_qtd + $arr_qtd_comm[$k]+$arr_qtd_check_new[$zclnt];
															$total_grand_ytd = $total_grand_ytd + $arr_ytd_comm[$k]+$arr_ytd_check_new[$zclnt];

															$level_a_count = $level_a_count + 1;			
													}
												}													
												
												$str_excel .= '
                        </table>
                        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
												   <tr class="display_totals"> 
                            <td width="240" align="left">&nbsp;&nbsp;<strong>TOTALS:</strong></td>
                            <td width="40">&nbsp;&nbsp;</td>
                            <td width="70" align="right"><strong>'.show_numbers($total_pbd).'</strong></td>
                            <td width="70" align="right"><strong>'.show_numbers($total_mtd).'</strong></td>
                            <td width="70" align="right"><strong>'.show_numbers($total_qtd).'</strong></td>
                            <td width="80" align="right"><strong>'.show_numbers($total_ytd).'</strong></td>
                            <td width="70" align="right"><strong>'.show_numbers($total_cmtd).'</strong></td>
                            <td width="70" align="right"><strong>'.show_numbers($total_cqtd).'</strong></td>
                            <td width="70" align="right"><strong>'.show_numbers($total_cytd).'</strong></td>
 														<td width="70" align="right"><strong>'.show_numbers($total_grand_mtd).'</strong></td>
                            <td width="70" align="right"><strong>'.show_numbers($total_grand_qtd).'</strong></td>
                            <td width="80" align="right"><strong>'.show_numbers($total_grand_ytd).'</strong></td>
                            <td width="80" align="right">&nbsp;</td>
                            <td width="50" align="right">&nbsp;</td>
                            <td>&nbsp;</td>                          
													</tr>
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