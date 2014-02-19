		<form name="frm_payout" method="post" onkeypress="return noenter()"> 
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td width="20" valign="top"> 
					<!-- Analyst List -->
					<table class="tbl_xl" border="0" cellpadding="0" cellspacing="0" >
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="71" /></td>
							<td class="tdx" valign="middle" nowrap="nowrap"><h1>Q<?=$sel_qtr?> <?=$sel_year?>&nbsp;&nbsp;&nbsp;</h1></td></tr>
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="25" /></td>
							<td class="tdx" nowrap="nowrap"><a class="pplname">&nbsp;<!--Totals Commissions--></a></td>
						</tr>	
						<!-- Analyst Array -->
						<?
						$str_analyst_csv = "";
						foreach ($arr_analysts as $k=>$v) {
							$str_analyst_csv = $str_analyst_csv . "," . $k;
						}
						?>
						<input type="hidden" name="frmitem_analysts" id="id_str_analysts" value="<?=$str_analyst_csv?>" />  
						<!-- End Analyst Array -->
						<?
						$rcount = 1;
						foreach ($arr_analysts as $k=>$v) {
						?>
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="25" /></td>
							<td class="tdx" nowrap="nowrap">
								<table width="100%">
									<tr>
										<td class="pplname" valign="top" nowrap="nowrap">&nbsp;&nbsp;<?=$v?></td>
										<td class="pplname" align="right" nowrap="nowrap">&nbsp;<!--: $<a id="at|<?=$rcount?>">0.00</a>--> </td>
									</tr>
								</table>
							</td>
						</tr>		
						<?
						$rcount++;
						}
						?>
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="25" /></td>
							<td class="tdx" nowrap="nowrap">Totals (%)</td>
						</tr>		
					</table>
					<!-- End Analyst List -->
				</td>
				<td width="752" align="left" valign="top">
				<!-- Begin Clients Section -->
				<!-- Client Array -->
				<?
				$str_clnts_csv = "";
				foreach ($arr_clnt_for_rr as $k=>$v) {
					$str_clnts_csv = $str_clnts_csv . "," . $k;
				}
				$str_clntname_psv = "";  //pipe separated values
				foreach ($arr_clnt_for_rr as $k=>$v) {
					$str_clntname_psv = $str_clntname_psv . "|" . $v;
				}
				?>
				<input type="hidden" name="frmitem_clients" id="id_str_clients" value="<?=$str_clnts_csv?>" />  
				<input type="hidden" name="frmitem_clientnames" id="id_str_clientnames" value="<?=$str_clntname_psv?>" />  
				<!-- End Client Array -->
				<div id="scrollGrid_1">
					<table class="tbl_xl" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="70" /></td>
							
						<?
						$ccount = 1;
						foreach ($arr_clnt_for_rr as $k=>$v) {
						?>
							<td class="tdx" valign="top"><a class="pplname">&nbsp;&nbsp;<?=str_replace(" ","&nbsp;&nbsp;<br>&nbsp;&nbsp;",trim($v))?></a></td>
						<?
						$ccount++;
						}
						?>
						</tr>
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="25" /></td>
						<?
						$val_total_commission = 0;
						$str_debug = "";
						$j = 1;
						foreach ($arr_clnt_for_rr as $k=>$v) {
							$total_commission = $arr_composite_primary[$k];
							$str_debug = $str_debug . " + " . $total_commission;
							$val_total_commission = $val_total_commission + $total_commission;
						?>
							<td class="tdx" align="right" valign="middle"><a class="pplname" id="tot|<?=$j?>"><?=number_format($total_commission,2,".",",")?></a>&nbsp;&nbsp;</td>
						<?
						$j = $j + 1;
						}
						?>
						</tr>
						<!-- <? echo $val_total_commission ?> -->
						<?
						for ($i=1; $i<($rcount); $i++) {
						?>
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="25" /></td>
							<?
							for ($j=1; $j<($ccount); $j++) {
							?>
							<td class="tdx">
								<table width="100%">
									<tr>
										<td><input type="text" style="border: 0px;" name="<?=$i."|".$j?>" id="<?=$i."|".$j?>" value="<?=$pop_array[$i."|".$j]?>" size="3" maxlength="3" onchange="xlrecalc('<?=$i."|".$j?>')" onkeyup="return xlmove(event, '<?=$i."|".$j?>')" onfocus="selitem('<?=$i."|".$j?>')"/><!--<a class="num_1">% </a>--></td>
										<td class="num_1" nowrap="nowrap" id="curnum|<?=$i."|".$j?>"></td>
									</tr>
								</table>
							</td>		
							<?
							}
							?>
						</tr>				
						<?
						}
						?>
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="25" /></td>
						<?
						for ($i=1; $i<$ccount; $i++) {
						?>
							<td class="tdx">&nbsp;<a id="total|<?=$i?>" class="valtotal">0</a></td>
						<?
						}
						?>
						</tr>
					</table>  
				</div>
				<!-- End Clients Section -->
				</td> 
				<td>
				<img src="images/spacer.gif" width="10" height="1" />
				</td>
				<td valign="top">
				<!-- SUMMARY SECTION -->
					<table class="tbl_xl" border="0" cellpadding="0" cellspacing="0" >
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="71" /></td>
							<td colspan="3" class="tdx" valign="middle" nowrap="nowrap">
								&nbsp;&nbsp;&nbsp;<font face="Arial, Helvetica, sans-serif" size="+2">SUMMARY</font><br />
								&nbsp;&nbsp;&nbsp;<font face="Arial, Helvetica, sans-serif" size="+1">Grand Total: $<?=number_format($val_total_commission,2,".",",")?></font>
							</td>
						</tr>
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="25" /></td>
							<td class="tdx" nowrap="nowrap"><a class="pplname">Name</a></td>
							<td class="tdx" nowrap="nowrap" align="right">&nbsp;&nbsp;<a class="pplname">$ Allocated</a>&nbsp;&nbsp;</td>
							<td class="tdx" nowrap="nowrap" align="right">&nbsp;&nbsp;<a class="pplname">% of Total</a>&nbsp;&nbsp;</td>
						</tr>		
						<?
						$rcount = 1;
						foreach ($arr_analysts as $k=>$v) {
						?>
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="25" /></td>
							<td class="tdx" nowrap="nowrap"><a class="pplname">&nbsp;&nbsp;<?=$v?></a>&nbsp;&nbsp;</td>
							<td class="tdx" nowrap="nowrap" align="right"><a class="pplname">$</a><a class="pplname" id="sat|<?=$rcount?>">0.00</a>&nbsp;&nbsp;</td>
							<td class="tdx" nowrap="nowrap" align="right"><a class="pplname" id="sap|<?=$rcount?>">0.00</a><a class="pplname">%</a>&nbsp;&nbsp;</td>
						</tr>		
						<?
						$rcount++;
						}
						?>
						<tr>
							<td width="1"><img src="images/spacer.gif" width="1" height="25" /></td> 
							<td class="tdx" nowrap="nowrap">Totals</td>
							<td class="tdx" nowrap="nowrap" align="right"><a class="pplname">$</a><a class="pplname" id="sum_sat_total">0.00</a>&nbsp;&nbsp;</td>
							<td class="tdx" nowrap="nowrap" align="right"><a class="pplname" id="sum_sap_total">0</a><a class="pplname">%</a>&nbsp;&nbsp;</td>
						</tr>		
					</table>
				<!-- END SUMMARY SECTION -->
				</td>
			</tr>
			<tr>
				<td colspan="3" valign="middle"><br />
						<img src="images/spacer.gif" width="20" height="20" />
						<input type="image" src="images/btn_save_1.png" onclick="submit_save()"/>
						<img src="images/spacer.gif" width="10" height="1" />
						<input type="image" src="images/btn_finalize.png" onclick="return submit_final()"/> 
				</td>
			</tr>
		</table>
		<input type="hidden" name="frmitem_rcount" id="id_rcount" value="<?=($rcount-1)?>" />  
		<input type="hidden" name="frmitem_ccount" id="id_ccount" value="<?=($ccount-1)?>" />  
		<input type="hidden" name="frmitem_qtr" id="id_qtr" value="<?=$sel_qtr?>" />  
		<input type="hidden" name="frmitem_year" id="id_year" value="<?=$sel_year?>" />  
		<input type="hidden" name="frmitem_gtotal" id="id_gtotal" value="<?=$val_total_commission?>" />  
		<input type="hidden" name="frmitem_final" id="id_final" value="0" />  
		<input type="hidden" name="frmitem_save" id="id_save" value="0" />  
		</form>
		<script language="javascript">
		//xlrecalcfinalform();
		</script>
