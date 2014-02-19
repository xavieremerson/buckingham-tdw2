<?
//show_array($_POST);
if ($_POST) {
	//xdebug('datefilterval',$datefilterval);
	$trade_date_to_process = format_date_mdy_to_ymd($datefilterval);
	$arr_repinfo = split('\^',$sel_rep);
	$rep_to_process = $arr_repinfo[0];
	$rep_id = $arr_repinfo[1];
	$rep_name = $arr_repinfo[2];
} else {
  $trade_date_to_process = previous_business_day();

/*	$qry_get_rep_default = "SELECT
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
	xdebug("sel_rep",$sel_rep);
	xdebug("rep_name",$rep_name);*/
	
	$sel_rep = '091^288';
  $rep_name = 'BRG,    (091)'; 


	$arr_repinfo = split('\^',$sel_rep);
	$rep_to_process = $arr_repinfo[0];
	$rep_id = $arr_repinfo[1];
	//xdebug('trade_date_to_process',$trade_date_to_process);
	//xdebug('rep_to_process',$rep_to_process);
	//xdebug('rep_id',$rep_id);
	//xdebug('rep_name',$rep_name);
}
//$rep_to_process = '035'; //'028';

include('comm_m_src_inc_main.php');
//*********************************************************************************************
//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

tsp(100,"Sales Rep : COMMISSIONS : As of ".format_date_ymd_to_mdy($trade_date_to_process)." for ".$rep_name);
?>
				<!-- START TABLE 3 -->
					<table width="100%" cellpadding="1", cellspacing="0"> <!-- bgcolor="#CCCCCC"-->
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
																<td width="200">
																<form name="selectionfilter" id="idselectionfilter" action="" method="post">
																<?
																//get reps from query  on table mry_comm_rr_trades and join on users
																$qry_get_reps = "SELECT
																									a.ID, a.rr_num, concat(a.Firstname, ' ', a.Lastname) as rep_name, a.rr_num as trad_rr 
																									from users a
																								WHERE a.rr_num like '0%'
																								AND a.Role > 2
																								AND a.Role < 5
																								AND a.user_isactive = 1
																								ORDER BY a.Lastname";
																$result_get_reps = mysql_query($qry_get_reps) or die (tdw_mysql_error($qry_get_reps));
																?>
																<select name="sel_rep" class="Text2">
																<?
																while ( $row = mysql_fetch_array($result_get_reps) )
																	{
																		?> 
																		<option value="<?=$row["trad_rr"]."^".$row["ID"]."^".$row["rep_name"]."&nbsp; &nbsp; (".$row["rr_num"].")"?>"<? if ($row["ID"] == $rep_id) {echo " selected";} ?>><?=str_pad($row["rep_name"], 20, ".")?>(<?=$row["rr_num"]?>)</option>
																		<?
																	}
																?>
																</select>
																</td>
																<td width="5">&nbsp;</td>
																<td width="150">
																<SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
																	<SCRIPT LANGUAGE="JavaScript">
																	var cal = new CalendarPopup();
																	cal.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
																	</SCRIPT>																
																		<input type="text" id="iddatefilterval" class="Text" name="datefilterval" readonly size="12" maxlength="12" value="<?=format_date_ymd_to_mdy($trade_date_to_process)?>">
																		<A HREF="#" onClick="cal.select(document.forms['selectionfilter'].datefilterval,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A>
																		<input type="image" src="images/lf_v1/form_submit.png">
																		
																		</form>
																</td>
																<td width="30">
																<script language="javascript">
																function go_prntscrn () {
																 document.prntscrn.sel_rep.value = document.selectionfilter.sel_rep.value;
																 document.prntscrn.datefilterval.value = document.selectionfilter.datefilterval.value;
																 document.prntscrn.info_str.value = '<?=$userfullname?>';
																}
																</script>
																	<form name="prntscrn" action="comm_m_src_print.php" method="get" target="_blank">
																		<input type="image" src="images/printer.png" border="0" alt="Print content of Window." onclick="go_prntscrn()" />&nbsp;&nbsp;
																		<input type="hidden" name="sel_rep" value="" />
																		<input type="hidden" name="datefilterval" value="" />
																		<input type="hidden" name="info_str" value="" />
																	</form>
																</td>
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
																<td colspan="2" bgcolor="#ffffff" width="240"><a class="ghm">&nbsp;&nbsp;"Brokerage Month Basis"</a></td>
																<td bgcolor="#222222" colspan="4" align="center"><a class="tblhead_a">C O M M I S S I O N S</a></td>
																<td bgcolor="#888888" colspan="3" align="center"><a class="tblhead_a">CHECKS (Cal. Mnth.)</a></td>
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
																<td width="50" bgcolor="#222222" align="center"><a class="tblhead_a"> of LY </a></td>
																<td>&nbsp;</td>
															</tr>
															<?
															$level_a_count = 0;
															$arr_main_processed = array();
															foreach($arr_clnt_for_rr as $k=>$v) {
																if ($k != '' AND ($arr_ytd_comm[$k]+$arr_ytd_check[$k])> 0) {
		
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
		
																?>
																	<tr class="<?=$class_row?>" >
																		<td valign="middle">&nbsp;&nbsp;<a href='#'><img src="images/t12m_s.png" border="0" onclick="showPopWin('chart_t12m.php?clnt=<?=$k?>', 626, 340, null);"></a>
                                    <!-- 
                                    "<td onclick="showPopWin('chart_t12m.php?clnt=<?=$k?>', 626, 340, null);">
                                    "CreateWnd('chart_t12m.php?clnt=<?=$k?>', 620, 330, false);"
                                    -->
																		
                                    &nbsp;
																		<?
																		if ($k == 'MILP') {
																		?>
																		<img src="images/subacct.png" border="0" onclick="CreateWnd('comm_src_inc_subacct_pop.php?rr=<?=$rep_to_process?>&dt=<?=$trade_date_to_process?>&adv=<?=$k?>', 620, 330, false);">&nbsp;&nbsp;
																		<?
																		}																								
																		?> 
																		<?=substr(look_up_client($k),0,24)?></td>
																		<td>&nbsp;&nbsp;<?=$rep_to_process?></td>
																		<td align="right"><?=show_numbers($arr_day_comm[$k])?>&nbsp;&nbsp;</td>
																		<td align="right"><?=show_numbers($arr_mtd_comm[$k])?>&nbsp;&nbsp;</td>
																		<td align="right"><?=show_numbers($arr_qtd_comm[$k])?>&nbsp;&nbsp;</td>
																		<td align="right"><?=show_numbers($arr_ytd_comm[$k])?>&nbsp;&nbsp;</td>
																		<td align="right"><?=show_numbers($arr_mtd_check[$k])?>&nbsp;&nbsp;</td>
																		<td align="right"><?=show_numbers($arr_qtd_check[$k])?>&nbsp;&nbsp;</td>
																		<td align="right"><?=show_numbers($arr_ytd_check[$k])?>&nbsp;&nbsp;</td>
																		<td align="right"><?=show_numbers($arr_mtd_comm[$k]+$arr_mtd_check[$k])?>&nbsp;&nbsp;</td>
																		<td align="right"><?=show_numbers($arr_qtd_comm[$k]+$arr_qtd_check[$k])?>&nbsp;&nbsp;</td>
																		<td align="right"><?=show_numbers($arr_ytd_comm[$k]+$arr_ytd_check[$k])?>&nbsp;&nbsp;</td>
																		<td align="right"><?=show_numbers(get_previous_yr_data($k))?>&nbsp;&nbsp;</td>
																		<td align="right"><?=$pyc_percent?>&nbsp;&nbsp;&nbsp;&nbsp;</td> 
																		<td align="right">&nbsp;</td>
																	</tr>
																	<?
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
														?>
														</table>
                            <?
														//xdebug("total_grand_ytd_ly",$total_grand_ytd_ly);
														?>
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
																<td width="80" align="right"><?=show_numbers($total_grand_ytd_ly)?>&nbsp;&nbsp;</td>
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
									<tr id="shrd">
										<td>
											<br>
												<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
													<tr>
														<td valign="top">		
														<table width="100%"  border="0" cellspacing="1" cellpadding="0">
															<tr> 
																<td colspan="2" bgcolor="#ffffff" width="240"><a class="ghm">&nbsp;&nbsp;"Brokerage Month Basis"</a></td>
																<td bgcolor="#222222" colspan="4" align="center"><a class="tblhead_a">C O M M I S S I O N S</a></td>
																<td bgcolor="#888888" colspan="3" align="center"><a class="tblhead_a">C H E C K S</a></td>
																<td bgcolor="#222222" colspan="3" align="center"><a class="tblhead_a">T O T A L</a></td>
																<td bgcolor="#222222" align="center"><a class="tblhead_a">LAST YEAR</a></td>
																<td bgcolor="#222222" align="center"><a class="tblhead_a">%</a></td>
																<td bgcolor="#222222">&nbsp;</td>
															<tr bgcolor="#333333"> 
																<td width="240"><a class="tblhead_a">&nbsp;&nbsp;&nbsp;&nbsp;CLIENT (SHARED)</a></td>
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
																<td width="50" bgcolor="#222222" align="center"><a class="tblhead_a"> of LY </a></td>
																<td>&nbsp;</td>
															</tr>
															<?
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
		
																?>
																	<tr class="<?=$class_row?>" >
																		<td valign="middle">&nbsp;&nbsp;<a href='#'><img src="images/t12m_s.png" border="0" onclick="CreateWnd('chart_t12m.php?clnt=<?=$k?>', 620, 330, false);"></a>
																		&nbsp;
																		<?
																		if ($k == 'MILP') {
																		?>
																		<img src="images/subacct.png" border="0" onclick="CreateWnd('rep_if2y_src_inc_subacct_pop.php?rr=<?=$rep_to_process?>&dt=<?=$trade_date_to_process?>&adv=<?=$k?>', 620, 330, false);">&nbsp;&nbsp;
																		<?
																		}																								
																		?> 
																		<?=substr(look_up_client($k),0,24)?></td>
																		<td>&nbsp;&nbsp;<?=$v?></td>
																		<td align="right"><?=show_numbers($arr_day_comm_shrd[$k])?>&nbsp;&nbsp;</td>
																		<td align="right"><?=show_numbers($arr_mtd_comm_shrd[$k])?>&nbsp;&nbsp;</td>
																		<td align="right"><?=show_numbers($arr_qtd_comm_shrd[$k])?>&nbsp;&nbsp;</td>
																		<td align="right"><?=show_numbers($arr_ytd_comm_shrd[$k])?>&nbsp;&nbsp;</td>
																		<td align="right"><?=show_numbers($val_chek_mtd)?>&nbsp;&nbsp;</td>
																		<td align="right"><?=show_numbers($val_chek_qtd)?>&nbsp;&nbsp;</td>
																		<td align="right"><?=show_numbers($val_chek_ytd)?>&nbsp;&nbsp;</td>
																		<td align="right"><?=show_numbers($arr_mtd_comm_shrd[$k]+$val_chek_mtd)?>&nbsp;&nbsp;</td>
																		<td align="right"><?=show_numbers($arr_qtd_comm_shrd[$k]+$val_chek_qtd)?>&nbsp;&nbsp;</td>
																		<td align="right"><?=show_numbers($arr_ytd_comm_shrd[$k]+$val_chek_ytd)?>&nbsp;&nbsp;</td>
																		<td align="right"><?=show_numbers(get_previous_yr_data($k))?>&nbsp;&nbsp;</td>
																		<td align="right"><?=show_numbers($pyc_percent)?>&nbsp;&nbsp;&nbsp;&nbsp;</td> 
																		<td align="right">&nbsp;</td>
																	</tr>
																	<?
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
		
																	$level_b_count = $level_b_count + 1;			
															}
														}													
														?>
														</table>
														<table width="100%"  border="0" cellspacing="1" cellpadding="0">
															 <tr class="display_totals"> 
																<td width="240" align="left">&nbsp;&nbsp;TOTALS:</td>
																<td width="40">&nbsp;&nbsp;</td>
																<td width="70" align="right"><?=show_numbers($total_pbd_shrd)?>&nbsp;&nbsp;</td>
																<td width="70" align="right"><?=show_numbers($total_mtd_shrd)?>&nbsp;&nbsp;</td>
																<td width="70" align="right"><?=show_numbers($total_qtd_shrd)?>&nbsp;&nbsp;</td>
																<td width="80" align="right"><?=show_numbers($total_ytd_shrd)?>&nbsp;&nbsp;</td>
																<td width="70" align="right"><?=show_numbers($total_cmtd_shrd)?>&nbsp;&nbsp;</td>
																<td width="70" align="right"><?=show_numbers($total_cqtd_shrd)?>&nbsp;&nbsp;</td>
																<td width="70" align="right"><?=show_numbers($total_cytd_shrd)?>&nbsp;&nbsp;</td>
																<td width="70" align="right"><?=show_numbers($total_grand_mtd_shrd)?>&nbsp;&nbsp;</td>
																<td width="70" align="right"><?=show_numbers($total_grand_qtd_shrd)?>&nbsp;&nbsp;</td>
																<td width="80" align="right"><?=show_numbers($total_grand_ytd_shrd)?>&nbsp;&nbsp;</td>
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
							<tr id="pbd"> <!--  style="display=none; visibility=hidden" -->
								<td>
									<br>
									<?
									include('comm_m_src_inc_trade.php');
									?>
								</td>
							</tr>
							<tr id="pbd"> <!--  style="display=none; visibility=hidden" -->
								<td>
									<br>
									<?
									include('comm_m_src_inc_shared_trade.php');
									?>
								</td>
							</tr>
						</table>
						<!-- END TABLE 3 -->
<?
tep();
?>