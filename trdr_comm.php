<?
include('trdr_comm_inc_main.php');
tsp(100,"Trader (".$userfullname.") : COMMISSIONS : As of ".format_date_ymd_to_mdy($trade_date_to_process));

?>
				<!-- START TABLE 3 -->
					<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#FFFFFF">
						<tr>
							<td valign="top"> 
								<!-- START TABLE 4 -->
								<!-- class="tablewithdata" -->
												<table width="100%" bgcolor="#FFFFFF">
													<tr>
														<td>
														<table width="100%" cellpadding="0" cellspacing="0">
															<tr>
																<td>&nbsp;</td>
																<td width="150">
																	<SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
																	<SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
																	<SCRIPT LANGUAGE="JavaScript">
																	var cal = new CalendarPopup("divfrom");
																	cal.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
																	</SCRIPT>																
																<form name="datefilter" id="iddatefilter" action="">
																		<input type="text" id="iddatefilterval" class="Text" name="datefilterval" readonly size="12" maxlength="12" value="<?=format_date_ymd_to_mdy($trade_date_to_process)?>">
																		<A HREF="#" onClick="cal.select(document.forms['datefilter'].datefilterval,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A>
																		<input type="image" src="images/lf_v1/form_submit.png">
																		</form>
																</td>
																<td width="14" align="center">&nbsp;</td>
																<td width="30">
																<script language="javascript">
																function go_excel () {
																 document.prntexcel.sel_rep.value = '<?=$rr_num."^".$user_id."^".$userfullname?>';
																 document.prntexcel.datefilterval.value = document.datefilter.datefilterval.value;
																 document.prntexcel.info_str.value = '<?=$userfullname?>';
																 if (document.datefilter.last_year.checked==true) {
																 	document.prntexcel.show_no_activity.value = 1;
																 } else {
																 	document.prntexcel.show_no_activity.value = 0;
																 }
																}
																</script>
																	<form name="prntexcel" action="trdr_comm_excel_exp.php" method="get" target="_blank">
																		<input type="image" src="images/lf_v1/exp2excel.png" border="0" alt="Export to Excel" onclick="go_excel()" />
                                    <input type="hidden" name="sel_rep" value="" />
																		<input type="hidden" name="show_no_activity" value="" />
																		<input type="hidden" name="datefilterval" value="" />
																		<input type="hidden" name="info_str" value="" />
																		<input type="hidden" name="user_id" value="<?=$user_id?>" />
																	</form>
																</td>
																<td width="14" align="center">&nbsp;</td>
																<td width="100"><a href="rep_trdr_ca_container.php"><img src="images/lf_v1/clnt_activity.png" border="0"></a></td>
															</tr>
														</table>
														</td> 
													</tr>
												</table>
								         
									<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
										<tr>
											<td valign="top"> 
            
                        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
                          <tr> 
                            <td colspan="2" bgcolor="#ffffff" width="300"><a class="ghm">&nbsp;&nbsp;"Brokerage Month Basis"</a></td>
                            <td bgcolor="#222222" colspan="4" align="center"><a class="tblhead_a">C O M M I S S I O N S</a></td>
                            <td bgcolor="#888888" colspan="3" align="center"><a class="tblhead_a">C H E C K S</a></td>
                            <td bgcolor="#222222" colspan="3" align="center"><a class="tblhead_a">T O T A L</a></td>
														<td bgcolor="#222222" align="center"><a class="tblhead_a">LAST YEAR</a></td>
                            <td bgcolor="#222222" align="center"><a class="tblhead_a">%</a></td>
                            <td bgcolor="#222222">&nbsp;</td>
                          <tr bgcolor="#333333"> 
                            <td width="240"><a class="tblhead_a">&nbsp;&nbsp;&nbsp;&nbsp;ADVISOR / CLIENT (PRIMARY)</a></td>
                            <td width="40"><a class="tblhead_a">&nbsp;&nbsp;RR</a></td>
                            <td width="70" align="right"><a class="tblhead_a"><?=substr(format_date_ymd_to_mdy($trade_date_to_process),0,5)?>&nbsp;&nbsp;&nbsp;&nbsp;</a></td>
                            <td width="70" align="right"><a class="tblhead_a">MTD&nbsp;&nbsp;</a></td>
                            <td width="70" align="right"><a class="tblhead_a">QTD&nbsp;&nbsp;</a></td>
                            <td width="80" align="right"><a class="tblhead_a">YTD&nbsp;&nbsp;</a></td>
                            <td bgcolor="#888888" width="70" align="right"><a class="tblhead_a">MTD&nbsp;&nbsp;</a></td>
                            <td bgcolor="#888888" width="70" align="right"><a class="tblhead_a">QTD&nbsp;&nbsp;</a></td>
                            <td bgcolor="#888888" width="70" align="right"><a class="tblhead_a">YTD&nbsp;&nbsp;</a></td>
                            <td width="70" bgcolor="#222222" align="right"><a class="tblhead_a">MTD&nbsp;&nbsp;</a></td>
                            <td width="70" bgcolor="#555555" align="right"><a class="tblhead_a">QTD&nbsp;&nbsp;</a></td>
                            <td width="80" bgcolor="#888888" align="right"><a class="tblhead_a">YTD&nbsp;&nbsp;</a></td>
                            <td width="80" bgcolor="#222222" align="right"><a class="tblhead_a"> </a></td>
                            <td width="80" bgcolor="#222222" align="center"><a class="tblhead_a"> of LY </a></td>
                            <td>&nbsp;</td>
                          </tr>
													<?
													//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

													foreach($arr_show_clnt_rr as $zk=>$zv) {
														$tmp_arr_vals = explode("#",$zv);
														$k = $zv;
														$zclnt = $tmp_arr_vals[0];
														$zrr =   $tmp_arr_vals[1];
														//echo "<!--".$zclnt."+++++".look_up_client($zclnt)."+++++".( $arr_ytd_comm[$k]+$arr_ytd_check[$k] )."-->\n";
														//echo "<!--".$zclnt."+++++".look_up_client($zclnt)."+++++".$arr_ytd_comm[$k]."-->\n";
														echo "<!--".$k."+++++".$zclnt."+++++".look_up_client($zclnt)."+++++".(int)$arr_ytd_check_new[$zclnt]."-->\n";
													}


													echo "<!--";
													print_r($arr_ytd_check_new); 
													echo "-->";



													//show_array($arr_show_clnt_rr);
													$level_a_count = 0;
													foreach($arr_show_clnt_rr as $zk=>$zv) {

														$tmp_arr_vals = explode("#",$zv);
														$k = $zv;
														$zclnt = $tmp_arr_vals[0];
														$zrr =   $tmp_arr_vals[1];
														
														
														
														//$v = $tmp_arr_vals[1];
														echo "<!--";
														echo look_up_client($zclnt)."[".( $arr_ytd_comm[$k]+$arr_ytd_check[$k] )."][".(int)get_previous_yr_data($tmp_arr_vals[0])."]-->";
														if ($k != '' 
														    AND ( (int)get_previous_yr_data($tmp_arr_vals[0]) > 0 OR ( $arr_ytd_comm[$k]+$arr_ytd_check_new[$zclnt] ) > 0 )
																) {  //AND get_previous_yr_data($tmp_arr_vals[0]) > 0 AND ( $arr_ytd_comm[$k]+$arr_ytd_check[$k] ) > 0

																if (
																			( (int)date('m') > 6 AND ($arr_ytd_comm[$k]+$arr_ytd_check_new[$zclnt]) > 0 )
																				OR
																			( (int)date('m') <= 6 AND ($arr_ytd_comm[$k]+$arr_ytd_check_new[$zclnt]) > 0)  
																){

																//In the above condition added "AND ($arr_ytd_comm[$k]+$arr_ytd_check[$k]) > 0" in the bottom line. 8 Feb 2010 after traders complained of dupes.


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

														?>
															<tr class="<?=$class_row?>" >
																<td valign="middle">&nbsp;&nbsp;<a href='#'><img src="images/t12m_s.png" border="0" onclick="CreateWnd('chart_t12m.php?clnt=<?=$k?>', 620, 330, false);"></a>
																&nbsp;
																<?=substr(look_up_client($zclnt),0,24)?></td>
																<td>&nbsp;&nbsp;<?=$zrr?></td>
																<td align="right"><?=show_numbers($arr_day_comm[$k])?>&nbsp;&nbsp;</td>
																<td align="right"><?=show_numbers($arr_mtd_comm[$k])?>&nbsp;&nbsp;</td>
																<td align="right"><?=show_numbers($arr_qtd_comm[$k])?>&nbsp;&nbsp;</td>
																<td align="right"><?=show_numbers($arr_ytd_comm[$k])?>&nbsp;&nbsp;</td>
																<td align="right"><?=show_numbers($arr_mtd_check_new[$zclnt])?>&nbsp;&nbsp;</td>
																<td align="right"><?=show_numbers($arr_qtd_check_new[$zclnt])?>&nbsp;&nbsp;</td>
																<td align="right"><?=show_numbers($arr_ytd_check_new[$zclnt])?>&nbsp;&nbsp;</td>
																<td align="right"><?=show_numbers($arr_mtd_comm[$k]+$arr_mtd_check_new[$zclnt])?>&nbsp;&nbsp;</td>
																<td align="right"><?=show_numbers($arr_qtd_comm[$k]+$arr_qtd_check_new[$zclnt])?>&nbsp;&nbsp;</td>
																<td align="right"><?=show_numbers($arr_ytd_comm[$k]+$arr_ytd_check_new[$zclnt])?>&nbsp;&nbsp;</td>
																<td align="right"><?=show_numbers(get_previous_yr_data($tmp_arr_vals[0]))?>&nbsp;&nbsp;</td>
																<td align="right"><?=$pyc_percent?>&nbsp;&nbsp;&nbsp;&nbsp;</td> 
																<td align="right">&nbsp;</td>
															</tr>
															<?
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
													
												}													
												?>
                        </table>
                        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
												   <tr class="display_totals"> 
                            <td width="240" align="left">&nbsp;&nbsp;TOTALS:</td>
                            <td width="40">&nbsp;&nbsp;</td>
                            <td width="70" align="right"><?=show_numbers($total_pbd)?>&nbsp;&nbsp;</td>
                            <td width="70" align="right"><?=show_numbers($total_mtd)?>&nbsp;&nbsp;</td>
                            <td width="70" align="right"><?=show_numbers($total_qtd)?>&nbsp;&nbsp;</td>
                            <td width="80" align="right"><?=show_numbers($total_ytd)?>&nbsp;&nbsp;</td>
                            <td width="70" align="right"><?=show_numbers($total_cmtd)?>&nbsp;&nbsp;</td>
                            <td width="70" align="right"><?=show_numbers($total_cqtd)?>&nbsp;&nbsp;</td>
                            <td width="70" align="right"><?=show_numbers($total_cytd)?>&nbsp;&nbsp;</td>
 														<td width="70" align="right"><?=show_numbers($total_grand_mtd)?>&nbsp;&nbsp;</td>
                            <td width="70" align="right"><?=show_numbers($total_grand_qtd)?>&nbsp;&nbsp;</td>
                            <td width="80" align="right"><?=show_numbers($total_grand_ytd)?>&nbsp;&nbsp;</td>
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
							<tr id="pbd">
								<td>
									<br>
									<?
									include('trdr_comm_inc_trade.php');
									?>
								</td>
							</tr>
						</table>
						<!-- END TABLE 3 -->
					<DIV ID="divfrom" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
