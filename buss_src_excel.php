<?
include('includes/dbconnect.php');
include('includes/global.php'); 
include('includes/functions.php');

include('buss_src_inc_common.php');

//show_array($_POST);
if ($_GET) {
	$arr_get = explode("^^",$_GET["xl"]);
  $trade_date_to_process = $arr_get[1];
} else {
  $trade_date_to_process = previous_business_day();
}

$output_filename = date('m-d-Y')."_biz_summary.xls";
$fp = fopen($exportlocation.$output_filename, "w");

//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

             $str = '<table width="100%" border="1" cellspacing="1" cellpadding="0">
                          <tr> 
                            <td width="270"><a class="ghm">&nbsp;&nbsp;"Brokerage Month Basis"</td>
                            <td bgcolor="#eeeeee" colspan="4" align="center">C O M M I S S I O N S</td>
                            <td colspan="3" align="center">C H E C K S</td>
                            <td bgcolor="#eeeeee" colspan="3" align="center">T O T A L</td>
                            <td align="center">LAST YEAR</td>
                            <td bgcolor="#eeeeee" align="center">% of LAST YEAR</td>
                            <td>&nbsp;</td>
                          <tr> 
                            <td width="320">&nbsp;&nbsp;<strong>As of '.format_date_ymd_to_mdy($trade_date_to_process).'</strong></td>
                            <td width="70" align="right">'.substr(format_date_ymd_to_mdy($trade_date_to_process),0,5).'</td>
                            <td width="100" align="right">MTD</td>
                            <td width="100" align="right">QTD</td>
                            <td width="100" align="right">YTD</td>
                            <td width="100" align="right">MTD</td>
                            <td width="100" align="right">QTD</td>
                            <td width="100" align="right">YTD</td>
                            <td width="100" align="right">MTD</td>
                            <td width="100" align="right">QTD</td>
                            <td width="100" align="right">YTD</td>
                            <td width="80" align="right"></td>
                            <td width="50" align="center"></td>
                            <td>&nbsp;</td>
                          </tr>
                        </table>';
								fputs ($fp, $str);
                        //set the running totals for this section
                        $running_total_comm_day = 0;
                        $running_total_comm_mtd  = 0;
                        $running_total_comm_qtd  = 0;
                        $running_total_comm_ytd  = 0;
                        $running_total_chek_mtd  = 0;
                        $running_total_chek_qtd  = 0;
                        $running_total_chek_ytd  = 0;
                        $running_total_checksum = 0;
                        ?>

                        <?
                        //get the names of registered reps which have active trades in THIS YEAR and have it ordered by lastname
                        $qry_get_reps = "SELECT
                                            a.ID, a.rr_num, concat(a.Lastname, ', ', a. Firstname) as rep_name, b.trad_rr 
                                            from users a, mry_comm_rr_trades b
                                          WHERE a.rr_num = b.trad_rr
                                          AND b.trad_rr like '0%'
                                          AND b.trad_trade_date > '".substr($trade_date_to_process,0,4)."-01-01'
                                          AND b.trad_is_cancelled = 0 
                                          GROUP BY b.trad_rr
                                          ORDER BY a.Lastname";
													//xdebug("qry_get_reps",$qry_get_reps);
                          $result_get_reps = mysql_query($qry_get_reps) or die (tdw_mysql_error($qry_get_reps));
                          while($row_get_reps = mysql_fetch_array($result_get_reps))
                          {
                          $mk_id = md5(rand(1000000000,9999999999));
                          //for tradesfor shared rep, do a reverse lookup in the users table to get the id and then the shared reps
                          $rep_to_process = $row_get_reps["rr_num"];
                          $srep_user_id = $row_get_reps["ID"];                           
                          $show_rr = $rep_to_process;

                          $tmp_rep = $rep_to_process;
													
                          include('buss_src_inc_each_rep.php');
                          include('buss_src_inc_each_rep_shrd.php');
                          
                          
                          
                          if ($arr_ytd_comm[$show_rr]+$arr_ytd_check[$show_rr] > 0) {
														$str = '<table width="100%" border="1">
																<tr>
																	<td colspan="14" bgcolor="#eeeeee"><strong>'.$row_get_reps["rep_name"].'</strong></td>
																</tr>
															</table>';
														fputs ($fp, $str);
                          }

                            if ($arr_ytd_comm[$show_rr]+$arr_ytd_check[$show_rr] > 0) {
                            $str = '<table width="100%" border="1" cellspacing="1" cellpadding="0" >  <!--class="tbl_test" -->
                              <tr> 
                                <td width="320" valign="left">&nbsp;'.$row_get_reps["rep_name"].' (RR#: '.$show_rr.')</td>
                                <td width="70"  align="right">'.show_numbers($arr_day_comm[$show_rr]).'</td>
                                <td width="100" align="right">'.show_numbers($arr_mtd_comm[$show_rr]).'</td>
                                <td width="100" align="right">'.show_numbers($arr_qtd_comm[$show_rr]).'</td>
                                <td width="100" align="right">'.show_numbers($arr_ytd_comm[$show_rr]).'</td>
                                <td width="100" align="right">'.show_numbers($arr_mtd_check[$show_rr]).'</td>
                                <td width="100" align="right">'.show_numbers($arr_qtd_check[$show_rr]).'</td>
                                <td width="100" align="right">'.show_numbers($arr_ytd_check[$show_rr]).'</td>
                                <td width="100" align="right">'.show_numbers($arr_mtd_comm[$show_rr]+$arr_mtd_check[$show_rr]).'</td>
                                <td width="100" align="right">'.show_numbers($arr_qtd_comm[$show_rr]+$arr_qtd_check[$show_rr]).'</td>
                                <td width="100" align="right">'.show_numbers($arr_ytd_comm[$show_rr]+$arr_ytd_check[$show_rr]).'</td>
                                <td width="80" align="right">&nbsp;</td>
                                <td width="50" align="right">&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
															</table>';
															fputs ($fp, $str);
	
															$running_total_comm_day  = $running_total_comm_day + $arr_day_comm[$show_rr];
															$running_total_comm_mtd  = $running_total_comm_mtd + $arr_mtd_comm[$show_rr];
															$running_total_comm_qtd  = $running_total_comm_qtd + $arr_qtd_comm[$show_rr];
															$running_total_comm_ytd  = $running_total_comm_ytd + $arr_ytd_comm[$show_rr];
															$running_total_chek_mtd  = $running_total_chek_mtd + $arr_mtd_check[$show_rr];
															$running_total_chek_qtd  = $running_total_chek_qtd + $arr_qtd_check[$show_rr];
															$running_total_chek_ytd  = $running_total_chek_ytd + $arr_ytd_check[$show_rr];
                            }
														
														//xdebug("running_total_comm_day",$running_total_comm_day);

                            //_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@
                            //get shared rep data (sls_sales_reps)
                            //fields are  srep_user_id  srep_rrnum  srep_percent
                            
                            //initialize running total for shared rep
                              $shrd_running_total_comm = 0;
                              $shrd_running_total_mtd  = 0;
                              $shrd_running_total_qtd  = 0;
                              $shrd_running_total_ytd  = 0;
                              
                              $show_row = 1;  
  

                            if ($show_row == 1) {
                          
                            $mk_sid = md5(rand(1000000000,9999999999));
                            
                                //echo "show row = 1...<br>";
                                if ($arr_ytd_comm_shrd[$show_rr]+$arr_ytd_check_shrd[$show_rr] > 0) {
                                  $str = '<table width="100%" border="1" cellspacing="1" cellpadding="0">
                                    <tr> 
                                      <td width="320" valign="left">&nbsp;'.$row_get_reps["rep_name"].' (Shared)</td>
                                      <td width="70"  align="right">'.show_numbers($arr_day_comm_shrd[$show_rr]).'</td>
                                      <td width="100" align="right">'.show_numbers($arr_mtd_comm_shrd[$show_rr]).'</td>
                                      <td width="100" align="right">'.show_numbers($arr_qtd_comm_shrd[$show_rr]).'</td>
                                      <td width="100" align="right">'.show_numbers($arr_ytd_comm_shrd[$show_rr]).'</td>
                                      <td width="100" align="right">'.show_numbers($arr_mtd_check_shrd[$show_rr]).'</td>
                                      <td width="100" align="right">'.show_numbers($arr_qtd_check_shrd[$show_rr]).'</td>
                                      <td width="100" align="right">'.show_numbers($arr_ytd_check_shrd[$show_rr]).'</td>
                                      <td width="100" align="right">'.show_numbers($arr_mtd_comm_shrd[$show_rr]+$arr_mtd_check_shrd[$show_rr]).'</td>
                                      <td width="100" align="right">'.show_numbers($arr_qtd_comm_shrd[$show_rr]+$arr_qtd_check_shrd[$show_rr]).'</td>
                                      <td width="100" align="right">'.show_numbers($arr_ytd_comm_shrd[$show_rr]+$arr_ytd_check_shrd[$show_rr]).'</td>
                                      <td width="80" align="right">&nbsp;</td>
                                      <td width="50" align="right">&nbsp;</td>
                                      <td>&nbsp;</td>
                                    </tr>
                                  </table>';
																	fputs ($fp, $str);
																	$running_total_comm_day  = $running_total_comm_day + $arr_day_comm_shrd[$show_rr]/2;
																	$running_total_comm_mtd  = $running_total_comm_mtd + $arr_mtd_comm_shrd[$show_rr]/2;
																	$running_total_comm_qtd  = $running_total_comm_qtd + $arr_qtd_comm_shrd[$show_rr]/2;
																	$running_total_comm_ytd  = $running_total_comm_ytd + $arr_ytd_comm_shrd[$show_rr]/2;
																	$running_total_chek_mtd  = $running_total_chek_mtd + $arr_mtd_check_shrd[$show_rr]/2;
																	$running_total_chek_qtd  = $running_total_chek_qtd + $arr_qtd_check_shrd[$show_rr]/2;
																	$running_total_chek_ytd  = $running_total_chek_ytd + $arr_ytd_check_shrd[$show_rr]/2;
																	$running_total_checksum = $running_total_checksum + $arr_mtd_comm_shrd[$show_rr];
                                }
                            }
                            //_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@_@
														//xdebug("running_total_comm_day",$running_total_comm_day);

                          } //end while looking for reps
                        
                        $running_total_comm_day_calc = db_single_val("SELECT sum( trad_commission ) as single_val 
                                                                       FROM mry_comm_rr_trades 
                                                                       WHERE trad_trade_date = '".$trade_date_to_process."'
                                                                       AND trad_is_cancelled = 0");
                        
                        $running_total_comm_mtd_calc = db_single_val("SELECT sum( trad_commission ) as single_val 
                                                                       FROM mry_comm_rr_trades 
                                                                       WHERE trad_trade_date between '".$global_qry_date_start_mtd."' and '".$trade_date_to_process."'
                                                                       AND trad_is_cancelled = 0");
                        
                        //echo $global_qtr_start_date;
                        $running_total_comm_qtd_calc = db_single_val("SELECT sum( trad_commission ) as single_val 
                                                                       FROM mry_comm_rr_trades 
                                                                       WHERE trad_trade_date between '".$global_qtr_start_date."' and '".$trade_date_to_process."'
                                                                       AND trad_is_cancelled =0");
                        
                        $running_total_comm_ytd_calc = db_single_val("SELECT sum( trad_commission ) as single_val 
                                                                       FROM mry_comm_rr_trades 
                                                                       WHERE trad_trade_date between '".$global_year_start_date."' and '".$trade_date_to_process."'
                                                                       AND trad_is_cancelled = 0");
                        
                        $running_total_chek_mtd_calc = db_single_val("SELECT sum(chek_amount) as single_val
                                                                        FROM chk_chek_payments_etc
                                                                        WHERE chek_date between '".$global_chk_qry_date_start_mtd."' and '".$trade_date_to_process."'
                                                                          AND chek_type = 1
                                                                          AND chek_isactive = 1");
                                                                       //AND a.chek_reps_and like '%".$user_initials."%'
												/*echo "SELECT sum(chek_amount) as single_val
                                                                        FROM chk_chek_payments_etc
                                                                        WHERE chek_date between '".$global_chk_qry_date_start_mtd."' and '".$trade_date_to_process."'
                                                                          AND chek_type = 1
                                                                          AND chek_isactive = 1";*/
												//xdebug("running_total_chek_mtd_calc",$running_total_chek_mtd_calc);																							 

                        $running_total_comm_mtd_calc = db_single_val("SELECT sum( trad_commission ) as single_val 
                                                                       FROM mry_comm_rr_trades 
                                                                       WHERE trad_trade_date between '".$global_qry_date_start_mtd."' and '".$trade_date_to_process."'
                                                                       AND trad_is_cancelled = 0");
                        
                        $running_total_comm_mtd_calc = db_single_val("SELECT sum( trad_commission ) as single_val 
                                                                       FROM mry_comm_rr_trades 
                                                                       WHERE trad_trade_date between '".$global_qry_date_start_mtd."' and '".$trade_date_to_process."'
                                                                       AND trad_is_cancelled = 0");
                        $str = '<table width="100%" border="1" cellspacing="1" cellpadding="0">
																 <tr> 
																	<td width="270"><div align="left"><strong>TOTALS:</strong></div></td>
																	<td width="70" align="right"><strong>'.number_format($running_total_comm_day_calc,0,'.',",").'</strong></td>
																	<td width="100" align="right"><strong>'.number_format($running_total_comm_mtd_calc,0,'.',",").'</strong></td>
																	<td width="100" align="right"><strong>'.number_format($running_total_comm_qtd_calc,0,'.',",").'</strong></td>
																	<td width="100" align="right"><strong>'.number_format($running_total_comm_ytd_calc,0,'.',",").'</strong></td>
																	<td width="100" align="right"><strong>'.number_format($running_total_chek_mtd,0,'.',",").'</strong></td>
																	<td width="100" align="right"><strong>'.number_format($running_total_chek_qtd,0,'.',",").'</strong></td>
																	<td width="100" align="right"><strong>'.number_format($running_total_chek_ytd,0,'.',",").'</strong></td>
																	<td width="100" align="right"><strong>'.number_format(($running_total_comm_mtd+$running_total_chek_mtd),0,'.',",").'</strong></td>
																	<td width="100" align="right"><strong>'.number_format(($running_total_comm_qtd+$running_total_chek_qtd),0,'.',",").'</strong></td>
																	<td width="100" align="right"><strong>'.number_format(($running_total_comm_ytd+$running_total_chek_ytd),0,'.',",").'</strong></td>
																	<td width="80" align="right"></td>
																	<td width="50" align="right"></td>
																	<td>&nbsp;</td>
																</tr>
															</table>';
                        fputs ($fp, $str);


fclose($fp);
Header("Location: http://192.168.20.63/tdw/fileserve_xls.php?l=data/exports/&f=".$output_filename);
?>