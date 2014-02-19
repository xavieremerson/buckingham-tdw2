<?
//BRG
include('inc_header.php');
?>
		<!-- START TABLE 1 -->
		<table width="100%" height="100%" border="0" cellspacing="1" cellpadding="0">
			<tr> 
				<td valign="top">
				<?
        //MAIN =================================================================================================
				$trade_date_to_process = previous_business_day();

				if ($x) { // form submitted //format_date_ymd_to_mdy($trade_date_to_process)
						if($_POST["sel_client"] == '^ALL^') {
							$show_client = "Show All";
						} else {
							$show_client = $_POST["sel_client"];
						}
						if($_POST["sel_rep"] == '^ALL^') {
							$show_rep = "Show All";
						} else {
							$arr_repinfo = split('\^',$_POST["sel_rep"]);
							$show_rep = $arr_repinfo[0];
							$rep_id = $arr_repinfo[1];
						}
						if($_POST["sel_symbol"] == '^ALL^') {
							$show_symbol = "Show All";
						} else {
							$show_symbol = $_POST["sel_symbol"];
						}
				
							$sel_datefrom = $datefrom;
							$sel_dateto = $dateto;
				
							//if brokerage month is selected use that info to create dateto and datefrom values
							if($_POST["sel_month"] == '') {
				
								$sel_datefrom = $datefrom;
								$sel_dateto = $dateto;
								
								$datefrom = format_date_mdy_to_ymd($datefrom);
								$dateto = format_date_mdy_to_ymd($dateto);
								
								$string_heading = "Selection: RR #: ".$show_rep." Client(s): ".$show_client. "&nbsp;&nbsp;&nbsp;&nbsp;Symbol(s): ".$show_symbol. "&nbsp;&nbsp;&nbsp;&nbsp;Date From: ".$_POST["datefrom"]. "&nbsp;&nbsp;&nbsp;&nbsp;Date To: ".$_POST["dateto"];
				
							} else {
				
								$sel_datefrom = $datefrom;
								$sel_dateto = $dateto;
				
								// ^ caused problems, had to escape it				
								$arr_split_input = split("\^", $sel_month);
								$arr_dates = get_commission_month_dates($arr_split_input[0],$arr_split_input[1]);
								$datefrom = $arr_dates[0];
								$dateto = $arr_dates[1];
								
								$string_heading = "Selection: RR #: ".$show_rep." Client(s): ".$show_client. "&nbsp;&nbsp;&nbsp;&nbsp;Symbol(s): ".$show_symbol. "&nbsp;&nbsp;&nbsp;&nbsp;Date From: ".format_date_ymd_to_mdy($datefrom). "&nbsp;&nbsp;&nbsp;&nbsp;Date To: ".format_date_ymd_to_mdy($dateto);
								
							}
				
				} else {
				
							$sel_datefrom = format_date_ymd_to_mdy($trade_date_to_process);
							$sel_dateto = format_date_ymd_to_mdy($trade_date_to_process);
				
							$string_heading = "";
							$show_rep = "Show All";
							$show_client = "Show All";
							$show_symbol = "Show All";
							$datefrom = previous_business_day();
							$dateto = previous_business_day();
				}
				?>
				<?
				tsp(100, "CLIENT MAINTENANCE");
				?> 		
				<!-- START TABLE 4 -->
				<!-- class="tablewithdata" -->
				<table width="100%" bgcolor="#FFFFFF">
					<tr>
						<td>
						<table width="100%" cellpadding="0" cellspacing="0">
						<form name="clnt_activity" id="idclnt_activity" action="" method="post">
							<tr>
								<td width="100">
								<select class="Text1" name="sel_rep" size="1" >
								<option value="^ALL^">&nbsp;REGISTERED REPS.&nbsp;(ALL)</option>
								<option value="^ALL^">____________</option>
								<?
								//get reps from query  on table mry_comm_rr_trades and join on users
								$qry_get_reps = "SELECT
																	a.ID, a.rr_num, concat(a.Lastname, ', ', a. Firstname) as rep_name, b.trad_rr 
																	from users a, mry_comm_rr_trades b
																WHERE a.rr_num = b.trad_rr
																AND b.trad_rr like '0%'
																GROUP BY b.trad_rr
																ORDER BY a.Lastname";
								$result_get_reps = mysql_query($qry_get_reps) or die (tdw_mysql_error($qry_get_reps));
								while($row_get_reps = mysql_fetch_array($result_get_reps))
								{
								//for tradesfor shared rep, do a reverse lookup in the users table to get the id and then the shared reps
									if ($rep_id == $row_get_reps["ID"]) {
									?>
														<option value="<?=$row_get_reps["trad_rr"]."^".$row_get_reps["ID"]?>" selected><?=$row_get_reps["rep_name"]?>&nbsp; &nbsp; (<?=$row_get_reps["rr_num"]?>)</option>
									<?
									} else {
									?>
														<option value="<?=$row_get_reps["trad_rr"]."^".$row_get_reps["ID"]?>"><?=$row_get_reps["rep_name"]?>&nbsp; &nbsp; (<?=$row_get_reps["rr_num"]?>)</option>
									<?
									}
								}
								?>
								</select>
								</td>
								<td width="5">&nbsp;</td>
								<td width="100">
								<select class="Text1" name="sel_client" size="1" >
								<option value="^ALL^">&nbsp;CLIENTS&nbsp;(ALL)</option>
								<option value="^ALL^">____________</option>
								<?
								$query_sel_client = "SELECT comm_advisor_code, max( comm_advisor_name ) as comm_advisor_name 
																			FROM rep_comm_rr_level_a
																			GROUP BY comm_advisor_code
																			ORDER BY comm_advisor_name";
								$result_sel_client = mysql_query($query_sel_client) or die(mysql_error());
								?>	
									<script type="text/javascript">
										var c = new Array()
								<?
								$count_client = 0;
								while($row_sel_client = mysql_fetch_array($result_sel_client))
								{
									if ($row_sel_client["comm_advisor_name"] == '') {
									$display_val_client = $row_sel_client["comm_advisor_code"];
									} else {
									$display_val_client = $row_sel_client["comm_advisor_name"];
									}
								echo 'c['.$count_client.']="'.$row_sel_client["comm_advisor_code"]."^".trim($display_val_client).'";';
								$count_client = $count_client + 1;
								}
								?>
								for (k=0;k<c.length;k++)
									{
										var selclient = new Array()
										selclient=c[k].split("^");
										if (selclient[0] == "<?=$show_client?>") {
										document.write("<option value=\""+selclient[0]+"\" selected>"+selclient[1]+"</option>");
										} else {
										document.write("<option value=\""+selclient[0]+"\">"+selclient[1]+"</option>");
										}
									}
									</script>
								</select>
								</td>
								<td width="5">&nbsp;</td>
								<td width="100">																
								<select class="Text1" name="sel_symbol" size="1" >
								<option value="^ALL^">&nbsp;SYMBOLS&nbsp;(ALL)&nbsp;&nbsp;</option>
								<option value="^ALL^">_____________</option><?
								
								$query_sel_symbol = "SELECT DISTINCT(trad_symbol)
																			FROM rep_comm_rr_trades 
																			ORDER BY trad_symbol";
								$result_sel_symbol = mysql_query($query_sel_symbol) or die(mysql_error());
								?>	
									<script type="text/javascript">
										var s = new Array()
								<?
								$count_symbol = 0;
								while($row_sel_symbol = mysql_fetch_array($result_sel_symbol))
								{
								echo 's['.$count_symbol.']="'.$row_sel_symbol["trad_symbol"].'";';
								$count_symbol = $count_symbol + 1;
								}
								?>
								for (j=0;j<s.length;j++)
									{
										if (s[j] == "<?=$show_symbol?>") {
										document.write("<option value=\""+s[j]+"\" selected>"+s[j]+"</option>");
										} else {
										document.write("<option value=\""+s[j]+"\">"+s[j]+"</option>");
										}
									}
									</script>
								</select>
								
								</td>
								<td width="5">&nbsp;</td>
								<td width="100">																
								<select class="Text1" name="sel_month" size="1" >
								<option value="">&nbsp;BROKERAGE MONTH&nbsp;&nbsp;</option>
								<option value="">_______________</option>
								<?
								echo create_commission_month();
								?>
								</select>
								</td>
								
								
									<SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
									<SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
									<SCRIPT LANGUAGE="JavaScript">
									var calfrom = new CalendarPopup("divfrom");
									calfrom.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
									var calto = new CalendarPopup("divto");
									calto.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
									
									</SCRIPT>																
								<td width="5">&nbsp;</td>
								<td width="10">From:</td>
								<td width="10"><input type="text" id="iddatefrom" class="Text1" name="datefrom" readonly size="12" maxlength="12" value="<?=$sel_datefrom?>"></td>
								<td width="20" align="center"><A HREF="#" onClick="calfrom.select(document.forms['clnt_activity'].datefrom,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
								<td width="5">&nbsp;</td>
								<td width="10">To:</td>
								<td width="10"><input type="text" id="iddateto" class="Text1" name="dateto" readonly size="12" maxlength="12" value="<?=$sel_dateto?>"></td>
								<td width="20" align="center"><A HREF="#" onClick="calto.select(document.forms['clnt_activity'].dateto,'anchor2','MM/dd/yyyy'); return false;" NAME="anchor2" ID="anchor2"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
								<td width="5">&nbsp;</td>
								<td width="10"><input type="image" src="images/lf_v1/form_submit.png"></td>
								<td width="10" align="center">&nbsp;</td>
								<td width="10" align="center">&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
						</form>															
						</table>
						</td> 
					</tr>
					<tr id="pbd"> <!--  style="display=none; visibility=hidden" -->
						<td>
							<?
							//INC CLIENTS  **************************************************************************************
							//Get trades for the default/selected previous trade date	(table : rep_comm_rr_trades)		
							//fields are trad_rr  trad_trade_date  trad_advisor_code  trad_advisor_name  trad_account_name  trad_account_number  
							//trad_symbol  trad_buy_sell  trad_quantity  trade_price  trad_commission  trad_cents_per_share 						
							if ($show_symbol != "Show All") {
								$qry_string_symbol = " AND trad_symbol = '".$show_symbol."' ";
							} else {
								$qry_string_symbol = "";
							}
							if ($show_client != "Show All") {
								$qry_string_client = " AND trad_advisor_code = '".$show_client."' ";
							} else {
								$qry_string_client = "";
							}
							if ($show_rep != "Show All") {
								$qry_string_rep = " AND trad_rr = '".$show_rep."' ";
								$rep_id = $rep_id;
							} else {
								$qry_string_rep = "";
							}
							
						?>
						<table width="100%" border="0" cellspacing="1" cellpadding="2" bgcolor="#FFFFFF">
							<tbody id="offTblBdy" class="datadisplay">			
								<tr bgcolor="#333333" class="tblhead_a">
									<td width="14">&nbsp;</td>
									<td width="250">Client Name</td>
									<td width="80">Client Code</td>
									<td width="80">Trdware Code</td>
									<td width="80">Rep(s)</td>
									<td width="80">Trader</td>
									<td width="80">Shares</td>
									<td width="80">Price</td>
									<td width="80">Commission</td>
									<td width="100">Comm./Shr. ($)</td>
									<td>Apply Adjustment</td>
								</tr>
				
							<script type="text/javascript">
								var t = new Array()
				
								
							<?
							//There is a know issue that since some clients have multiple RRs, e.g. GART the data shown gets max(rr)
							//which means the totals will be accurate but the rr agains the client will be inaccurate.
							
							//fixing the query (excel) to account for the incorrect subtotals by rr (carol)
							
							//Get the group trades for purposes of using the [+] feature where a single update will  update all components
							$qry_client_group =  "SELECT 
																	a.clnt_auto_id,
																	a.clnt_code,
																	a.clnt_alt_code,
																	a.clnt_name,
																	a.clnt_rr1,
																	a.clnt_rr2,
																	a.clnt_trader,
																	b.clnt_default_payout,
																	b.clnt_special_payout_rate,
																	b.clnt_start_month,
																	b.clnt_default_n_months
																FROM int_clnt_clients a, int_clnt_payout_rate b
																WHERE a.clnt_auto_id = b.clnt_auto_id";
							$result_client_group = mysql_query($qry_client_group) or die(tdw_mysql_error($qry_client_group));
							$count_row_client_group = 0;
							$count_row_client = 0;
							$running_trad_commission_total = 0;
							while($row_client_group = mysql_fetch_array($result_client_group))
							{
								//variables for the next query
								$v_clnt_auto_id = $row_client_group["clnt_auto_id"]; 
								$v_client_code = $row_client_group["clnt_code"]; 
								
								//next query to figure out singletons or multiples with details
								$query_client_details = "SELECT 
																						a.clnt_auto_id,
																						a.clnt_code,
																						a.clnt_alt_code,
																						a.clnt_name,
																						a.clnt_rr1,
																						a.clnt_rr2,
																						a.clnt_trader,
																						b.clnt_reps_auto_id,
																						b.clnt_reps_user_id,
																						b.clnt_reps_start_date,
																						b.clnt_reps_end_date,
																						b.clnt_reps_isactive,
																						b.clnt_reps_edited_by
																					FROM int_clnt_clients a
																					LEFT JOIN int_clnt_reps b on a.clnt_auto_id = b.clnt_auto_id
																					WHERE a.clnt_auto_id = '".$v_clnt_auto_id."'";
								//xdebug("is_singleton",$is_singleton);
								$result_client_details = mysql_query($query_client_details) or die(tdw_mysql_error($query_client_details));
								$is_singleton = mysql_num_rows($result_client_details);
				
								//if the detail is a single record then don't show the [+] as well as the detail record
								//xdebug("is_singleton",$is_singleton);
								if ($is_singleton == 1) {
				
									//S indicates single update
									$str_param_inline = $user_id."@".'S'."@".$v_clnt_auto_id;  // ."@".$v_advcode ."@".$v_rr ."@".$v_symbol ."@".$v_buysell;
									//Show only the group record which is of course single 
													echo 't ['.$count_row_client.'] = "'.'S'.'^'.
																														$row_client_group["clnt_name"].'^'.
																														$row_client_group["clnt_code"].'^'.
																														$row_client_group["clnt_alt_code"].'^'.
																														$row_client_group["clnt_trader"].'^'.
																														$row_client_group["clnt_auto_id"].'^'.
																														$row_client_group["clnt_auto_id"].'^'.
																														$row_client_group["clnt_auto_id"].'^'.
																														$row_client_group["clnt_auto_id"].'^'.
																														$row_client_group["clnt_auto_id"].'^'.
																														''.'^'.
																														$str_param_inline.'^'.
																														'S'.'"'.";\n";
								$count_row_client = $count_row_client + 1;
				
								} else { //there are more than one client reps in the group
					
													$str_details_html = "";
													while($row_client_details = mysql_fetch_array($result_client_details))
													{
														$show_accinfo = $row_trades_details["show_acctinfo"];
														$str_param_inline_detail = $user_id."@".'SREF'."@".$v_tradedate ."@".$v_advcode ."@".$v_rr ."@".$v_symbol ."@".$v_buysell."@".$row_client_details["trad_reference_number"]."@".$row_trades_details["trad_account_number"];
														$str_details_html .= "<tr class='alternaterow'>".
																								 "<td width='14'> </td>".
																								 "<td width='250'>".$row_client_details["clnt_name"]."</td>".
																								 "<td width='80' align='left'>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;".$show_accinfo."</td>".
																								 "<td width='80'>&nbsp;&nbsp;&nbsp;".$row_client_details["trad_rr"]."</td>".
																								 "<td width='80'>&nbsp; &nbsp;&nbsp;".$row_client_details["trad_symbol"]."</td>".
																								 "<td width='80'>&nbsp;&nbsp;&nbsp;".$row_client_details["trad_buy_sell"]."</td>".
																								 "<td width='80' align='right'>".$row_client_details["trad_quantity"]."&nbsp;&nbsp;</td>".
																								 "<td width='80' align='right'>".$row_client_details["trade_price"]."&nbsp;&nbsp;</td>".
																								 "<td width='80' align='right'>".$row_client_details["trad_commission"]."&nbsp;</td>".
																								 "<td width='100' align='right'>".$row_client_details["trad_cents_per_share"]."&nbsp;</td>".
																								 "<td align='left'><a href=\\\"javascript:CreateWnd('rep_adj_all_rep_ca_pop.php?param=".$str_param_inline_detail."', 500, 230, false);\\\"><img src='".$_site_url."images/lf_v1/adj_singsing.png'></a></td></tr>";
													}
													$str_param_inline_group = $user_id."@".'G'."@".$v_tradedate ."@".$v_advcode ."@".$v_rr ."@".$v_symbol ."@".$v_buysell;
													echo 't ['.$count_row_client.'] = "'.''.'^'.
																																$row_client_group["clnt_name"].'^'.
																																$show_trad_advisor_name.'^'.
																																$row_client_group["trad_rr"].'^'.
																																$row_client_group["trad_symbol"].'^'.
																																$row_client_group["trad_buy_sell"].'^'.
																																$row_client_group["trad_quantity"].'^'.
																																$row_client_group["trade_price"].'^'.
																																$row_client_group["trad_commission"].'^'.
																																$row_client_group["trad_cents_per_share"].'^'.
																																$str_details_html.'^'.
																																$str_param_inline_group.'^'.
																																''.'"'.";\n";
								//write the values in the previous variable to be passed to the array.
								//this way the array just gets processed with the required information and is boom baam boom																																
								$count_row_client = $count_row_client + 1;
								
								}
				
								//$count_row_trades = $count_row_trades + 1;
							}
							
							?>
								for (i=0;i<t.length;i++)
									{
									var rowtrades_array = new Array()
									var rowclass, disp_img, disp_plus, next_row_open, next_row_close;
									if (i%2 == 0) {
										rowclass = " class=\"alternateRow\"";
									} else {
										rowclass = "";
									}
									
									rowtrades_array=t[i].split("^");
				
									if (rowtrades_array[0] == "S") {
										disp_plus = " ";
									} else {
										disp_plus = "<a href=\"javascript\:showhidedetail("+i+")\"><img id=\"img" + i + "\" src=\"<?=$_site_url?>images/lf_v1/expand.png\" border=\"0\"></a>";
									}
				
									if (rowtrades_array[12] == "S") {
										disp_img = "<img src=\"<?=$_site_url?>images/lf_v1/adj_sing.png\" border=\"0\">";
									} else {
										disp_img = "<img src=\"<?=$_site_url?>images/lf_v1/adj_mult.png\" border=\"0\">";
									}
									
									next_row_open = "<tr class=\"trlight\" id=\""+i+"\" style=\"display=none; visibility=hidden\">"+ 
																		"<td colspan=\"11\">"+
																				"<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\" bgcolor=\"#FFFFFF\"><tbody id=\"offTblBdy\" class=\"datadisplay\">";
									
									next_row_close = "</tbody></table></td></tr>";
									/*
									next_row_open = "<div id=\""+i+"\" style=\"display=none; visibility=hidden\">";
									next_row_close = "</div>";
									*/
									
									document.write(
													"<tr" + rowclass + ">"+
													"<td>"+disp_plus+"</td>"+
													"<td>"+rowtrades_array[1]+"</td>"+
													"<td>&nbsp;"+rowtrades_array[2]+"</td>"+
													"<td>&nbsp;"+rowtrades_array[3]+"</td>"+
													"<td>&nbsp;&nbsp;&nbsp;"+rowtrades_array[4]+"</td>"+
													"<td>&nbsp;&nbsp;&nbsp;"+rowtrades_array[5]+"</td>"+
													"<td align='right'>"+rowtrades_array[6]+"&nbsp;&nbsp;&nbsp;</td>"+
													"<td align='right'>"+rowtrades_array[7]+"&nbsp;&nbsp;&nbsp;</td>"+
													"<td align='right'>"+rowtrades_array[8]+"&nbsp;&nbsp;&nbsp;</td>"+
													"<td align='right'>"+rowtrades_array[9]+"&nbsp;&nbsp;&nbsp;</td>"+
													"<td align='left'><a href=\"javascript:CreateWnd('rep_adj_all_rep_ca_pop.php?param="+rowtrades_array[11]+"', 500, 230, false);\">"+disp_img+"</a></td></tr>");
				
									document.write(next_row_open + rowtrades_array[10] + next_row_close);
				
									}
									</script>
				
									<?		
									echo '
										</tbody>
										</table>
									</td>
								</tr>
							</table>';
							//END INC TRADE **********************************************************************************
							?>
						</td>
					</tr>
				</table>
				<!-- END TABLE 4 -->
				<?
				tep();
				?>						
				<DIV ID="divfrom" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
				<DIV ID="divto" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>			
				<?				
				//END MAIN =============================================================================================
				?>
				</td>
			</tr>
		</table>
		<!-- END TABLE 1 -->
<?
include('inc_footer.php'); 
?>