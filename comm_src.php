<?
error_reporting(0);

//some variables used down below
$arr_commission_clients = array();

if ($datefilterval) {
//xdebug('datefilterval',$datefilterval);
$trade_date_to_process = format_date_mdy_to_ymd($datefilterval);
//xdebug('trade_date_to_process',$trade_date_to_process);
} else {
$trade_date_to_process = previous_business_day();
//xdebug('trade_date_to_process',$trade_date_to_process);
}
//$rep_to_process = '035'; //'028';
$rep_to_process = $rr_num;

//*********************************************************************************************
//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
include('comm_src_inc_main.php');

//show_array($arr_relevant_shared_clients);

/*
$input = "Alien";
echo str_pad($input, 10);                      // produces "Alien     "
echo str_pad($input, 10, "-=", STR_PAD_LEFT);  // produces "-=-=-Alien"
echo str_pad($input, 10, "_", STR_PAD_BOTH);   // produces "__Alien___"
echo str_pad($input, 6 , "___");               // produces "Alien_"
*/
//creating array master for primary clients

?>

<?
tsp(100, "COMMISSIONS : As of ".format_date_ymd_to_mdy($trade_date_to_process));

//show post variables
//show_array($_POST);


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
																<form name="datefilter" id="iddatefilter" action="" method="post">
																<td width="250" class="quotes"><input name="last_year" type="checkbox" value="1" <? if($last_year) {echo " checked";}?> /> Also show Clients w/ NO activity</td>
																<td width="150">
																	<SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
																	<SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
																	<SCRIPT LANGUAGE="JavaScript">
																	var cal = new CalendarPopup("divfrom");
																	cal.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
																	</SCRIPT>																
																		<input type="text" id="iddatefilterval" class="Text" name="datefilterval" readonly size="12" maxlength="12" value="<?=format_date_ymd_to_mdy($trade_date_to_process)?>">
																		<A HREF="#" onClick="cal.select(document.forms['datefilter'].datefilterval,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A>
																		<input type="image" src="images/lf_v1/form_submit.png">
																		</form>
																</td>
																<td width="14" align="center">&nbsp;</td>
																<td width="60">
																<script language="javascript">
																function go_prntscrn () {
																 document.prntscrn.sel_rep.value = "<?=$rr_num."^".$user_id."^".$userfullname?>";
																 document.prntscrn.datefilterval.value = document.datefilter.datefilterval.value;
																 document.prntscrn.info_str.value = "<?=$userfullname?>";
																 if (document.datefilter.last_year.checked==true) {
																 	document.prntscrn.show_no_activity.value = 1;
																 } else {
																 	document.prntscrn.show_no_activity.value = 0;
																 }
																}
																</script>
																	<form name="prntscrn" action="comm_src_print.php" method="get" target="_blank">
																		<input type="image" src="images/printer.png" border="0" alt="Print content of Window." onclick="go_prntscrn()" />&nbsp;&nbsp;
																		<input type="hidden" name="sel_rep" value="" />
																		<input type="hidden" name="show_no_activity" value="" />
																		<input type="hidden" name="datefilterval" value="" />
																		<input type="hidden" name="info_str" value="" />
																	</form>
																</td>
																<td width="100">
																<script language="javascript">
																function go_excel () {
																 document.prntexcel.sel_rep.value = "<?=$rr_num."^".$user_id."^".$userfullname?>";
																 document.prntexcel.datefilterval.value = document.datefilter.datefilterval.value;
																 document.prntexcel.info_str.value = "<?=$userfullname?>";
																 if (document.datefilter.last_year.checked==true) {
																 	document.prntexcel.show_no_activity.value = 1;
																 } else {
																 	document.prntexcel.show_no_activity.value = 0;
																 }
																}
																</script>
																	<form name="prntexcel" action="comm_src_excel_exp.php" method="get" target="_blank">
																		<input type="image" src="images/lf_v1/exp2excel.png" border="0" alt="Print content of Window." onclick="go_excel()" />&nbsp;&nbsp;
																		<input type="hidden" name="sel_rep" value="" />
																		<input type="hidden" name="show_no_activity" value="" />
																		<input type="hidden" name="datefilterval" value="" />
																		<input type="hidden" name="info_str" value="" />
																	</form>
																</td>																
																<td width="100"><a href="rep_ca_container.php"><img src="images/lf_v1/clnt_activity.png" border="0"></a></td>
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
																<td colspan="2" bgcolor="#ffffff" width="260" nowrap="nowrap"><a class="ghm">&nbsp;&nbsp;"Brokerage Month Basis"</a></td>
																<td bgcolor="#222222" colspan="4" align="center"><a class="tblhead_a">C O M M I S S I O N S</a></td>
																<td bgcolor="#888888" colspan="3" align="center"><a class="tblhead_a">CHECKS <font color="red">(Cal. Mth.)</font></a></td>
																<td bgcolor="#222222" colspan="3" align="center"><a class="tblhead_a">T O T A L</a></td>
																<td bgcolor="#222222" align="center"><a class="tblhead_a">LAST YEAR</a></td>
																<td bgcolor="#222222" align="center"><a class="tblhead_a">%</a></td>
																<td bgcolor="#222222">&nbsp;</td>
                              </tr>
															<tr bgcolor="#333333"> 
																<td width="260"><a class="tblhead_a">&nbsp;&nbsp;&nbsp;&nbsp;ADVISOR / CLIENT (PRIMARY)</a></td>
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
																		?>
																		<tr class="<?=$class_row?>" >
																			<td valign="middle" nowrap="nowrap">&nbsp;&nbsp;<a href='#'><img src="images/t12m_s.png" border="0" onclick="showPopWin('chart_t12m.php?clnt=<?=$k?>', 640, 340, false);"></a>
																			&nbsp;
																			<?
																			if ($k == 'MILP' or $k == 'MILK' or 1==1) {
																			?>
																			<img src="images/subacct.png" border="0" onclick="showPopWin('comm_src_inc_subacct_pop.php?rr=<?=$rep_to_process?>&dt=<?=$trade_date_to_process?>&adv=<?=$k?>', 620, 330, false);">&nbsp;&nbsp;                                      <!--<img src="images/subacct.png" border="0" onclick="CreateWnd('comm_src_inc_subacct_pop.php?rr=<?=$rep_to_process?>&dt=<?=$trade_date_to_process?>&adv=<?=$k?>', 620, 330, false);">&nbsp;&nbsp;
																			--><?
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
						
																		?>
																		<tr class="<?=$class_row?>" >
																			<td valign="middle" nowrap="nowrap">&nbsp;&nbsp;<a href='#'><img src="images/t12m_s.png" border="0" onclick="showPopWin('chart_t12m.php?clnt=<?=$k?>', 640, 340, false);"></a>
																			&nbsp;
																			<?
																			if ($k == 'MILP' or $k == 'MILK' or 1==1) {
																			?>
																			<img src="images/subacct.png" border="0" onclick="showPopWin('comm_src_inc_subacct_pop.php?rr=<?=$rep_to_process?>&dt=<?=$trade_date_to_process?>&adv=<?=$k?>', 620, 330, false);">&nbsp;&nbsp;
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
															}												
															//+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_+_
															
														//removing table end and adding table row.	
														?>
														<!--</table>-->
														<?
														if ($total_grand_ytd > 0 || $total_grand_ytd_ly > 0) {
														?>
                            <!--<table width="100%"  border="1" cellspacing="1" cellpadding="0">-->
															 <tr class="display_totals"> 
																<td width="260" align="left">&nbsp;&nbsp;TOTALS:</td>
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
																<td width="50" align="right"><?
																if ($total_grand_ytd_ly == 0 || $total_grand_ytd_ly == "") {
																echo "0";
																} else {
																echo number_format((($total_grand_ytd/$total_grand_ytd_ly)*100),0,"",",");
																}
																?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
																<td>&nbsp;</td>                          
															</tr>
														</table>
                            <?
														}
														?>
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
																<td colspan="2" bgcolor="#ffffff" width="260" nowrap="nowrap"><a class="ghm">&nbsp;&nbsp;"Brokerage Month Basis"</a></td>
																<td bgcolor="#222222" colspan="4" align="center"><a class="tblhead_a">C O M M I S S I O N S</a></td>
																<td bgcolor="#888888" colspan="3" align="center"><a class="tblhead_a">C H E C K S</a></td>
																<td bgcolor="#222222" colspan="3" align="center"><a class="tblhead_a">T O T A L</a></td>
																<td bgcolor="#222222" align="center"><a class="tblhead_a">LAST YEAR</a></td>
																<td bgcolor="#222222" align="center"><a class="tblhead_a">%</a></td>
																<td bgcolor="#222222">&nbsp;</td>
															<tr bgcolor="#333333"> 
																<td width="260"><a class="tblhead_a">&nbsp;&nbsp;&nbsp;&nbsp;CLIENT (SHARED)</a></td>
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

																		if(get_previous_yr_data($k)>0) {
																			if ($arr_ytd_comm_shrd[$k]) {
																				$pyc_percent = number_format((((int)$arr_ytd_comm_shrd[$k]+(int)$val_chek_ytd)/get_previous_yr_data($k))*100,0,'',",");
																			} else {
																				$pyc_percent = number_format((((int)$val_chek_ytd)/get_previous_yr_data($k))*100,0,'',",");
																			}
																		} else {
																			$pyc_percent = 0;
																		}
		
																	
																		if ($v=="") {
																			$str_rep_num = getrepnum_for_client($k);
																		} else {
																			$str_rep_num = $v;
																		}
		
																?>
																	<tr class="<?=$class_row?>" >
																		<td valign="middle" nowrap="nowrap">&nbsp;&nbsp;<a href='#'><img src="images/t12m_s.png" border="0" onclick="showPopWin('chart_t12m.php?clnt=<?=$k?>', 620, 330, false);"></a>
																		&nbsp;
																		<?
																		if ($k == 'MILP' or $k == 'MILK' or 1==1) {
																		?>
																			<img src="images/subacct.png" border="0" onclick="showPopWin('comm_src_inc_subacct_pop.php?rr=<?=$v?>&dt=<?=$trade_date_to_process?>&adv=<?=$k?>', 620, 330, false);">&nbsp;&nbsp;
																		<?
																		}																								
																		?> 
																			<?=substr(look_up_client($k),0,24)?></td>
																		<td>&nbsp;&nbsp;<?=$str_rep_num?></td>
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
                                    <!-- <? echo ($arr_ytd_comm_shrd[$k]+$val_chek_ytd)."//////".get_previous_yr_data($k)?> -->
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

																	$total_grand_ytd_shrd_ly = $total_grand_ytd_shrd_ly + get_previous_yr_data($k);
		
																	$level_b_count = $level_b_count + 1;			
															}
														}													
														?>
														<!--</table>-->
														<?
														if ($total_grand_ytd_shrd > 0 || $total_grand_ytd_shrd_ly > 0) {
														?>
														<!--<table width="100%"  border="0" cellspacing="1" cellpadding="0">-->
															 <tr class="display_totals"> 
																<td width="260" align="left">&nbsp;&nbsp;TOTALS:</td>
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
																<td width="80" align="right"><?=show_numbers($total_grand_ytd_shrd_ly)?>&nbsp;&nbsp;</td>
																<td width="50" align="right"><?=number_format(($total_grand_ytd_shrd/$total_grand_ytd_shrd_ly)*100,0,"",",")?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
																<td>&nbsp;</td>                          
															</tr>
														</table>
                            <?
														}
														?>
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
									include('comm_src_inc_trade.php');
									?>
								</td>
							</tr>
							<tr id="pbd">
								<td>
									<br>
									<?
									include('comm_src_inc_shared_trade.php');
									?>
								</td>
							</tr>
						</table>
						<!-- END TABLE 3 -->

<DIV ID="divfrom" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>

<?
//show_array($arr_relevant_shared_clients);
?>