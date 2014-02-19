<?						
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
					<td width="80">Trade Date</td>
				  <td width="276">ADVISOR / CLIENT</td>
					<td width="56">RR #</td>
					<td width="80">Symbol</td>
					<td width="80">B/S</td>
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
			$query_trades_group = "SELECT 
													trad_advisor_code,
													trad_symbol,
													trad_buy_sell,
													trad_trade_date as vtradedate,
													DATE_FORMAT(trad_trade_date,'%m/%d/%Y') as trad_trade_date,
													max(trad_advisor_name) as trad_advisor_name,
													FORMAT(sum(trad_quantity),0) as trad_quantity,
													FORMAT(max(trade_price),2) as trade_price,
													FORMAT(sum(trad_commission),2) as trad_commission,
													sum(trad_commission) as for_sum_trad_commission,
													FORMAT(avg(trad_cents_per_share),3) as trad_cents_per_share,
													trad_rr 
												FROM mry_comm_rr_trades 
												WHERE trad_is_cancelled = 0 
												AND trad_trade_date between '".$datefrom."' AND '".$dateto."'"
												. $qry_string_symbol . $qry_string_client .$qry_string_rep .
												" GROUP BY trad_advisor_code, trad_symbol, trad_buy_sell, trad_trade_date, trad_rr
												ORDER BY trad_advisor_name, trad_symbol, trad_buy_sell, trad_trade_date";
												
			$result_trades_groups = mysql_query($query_trades_group) or die(tdw_mysql_error($query_trades_group));
			$count_row_trades_groups = 0;
			$count_row_trades = 0;
			$running_trad_commission_total = 0;
			while($row_trades_groups = mysql_fetch_array($result_trades_groups))
			{
				//get the running sum of commissions
				$running_trad_commission_total = $running_trad_commission_total + $row_trades_groups["for_sum_trad_commission"];
				
				//variables for the next query
				$v_tradedate = $row_trades_groups["vtradedate"]; 
				$v_advcode = $row_trades_groups["trad_advisor_code"];
				$v_rr = $row_trades_groups["trad_rr"];
				$v_symbol = $row_trades_groups["trad_symbol"];
				$v_buysell = $row_trades_groups["trad_buy_sell"];
				
				if ($row_trades_groups["trad_advisor_name"] == '') {
					$show_trad_advisor_name = $row_trades_groups["trad_advisor_code"];
				} else {
					$show_trad_advisor_name = $row_trades_groups["trad_advisor_name"];
				}
				
				//next query to figure out singletons or multiples with details
				$query_trades_details = "SELECT 
																		trad_reference_number,
																		trad_advisor_code,
																		concat(trad_account_name, '  ',trad_account_number) as show_acctinfo,
																		trad_account_number,
																		trad_symbol,
																		trad_buy_sell,
																		DATE_FORMAT(trad_trade_date,'%m/%d/%Y') as trad_trade_date,
																		trad_advisor_name,
																		FORMAT(trad_quantity,0) as trad_quantity,
																		FORMAT(trade_price,2) as trade_price,
																		trad_commission,
																		FORMAT(trad_cents_per_share,3) as trad_cents_per_share,
																		trad_rr 
																	FROM mry_comm_rr_trades 
																	WHERE trad_is_cancelled = 0 
																	AND trad_trade_date = '".$v_tradedate."'
																	AND trad_advisor_code = '".$v_advcode."'
																	AND trad_symbol = '".$v_symbol."'
																	AND trad_rr = '".$v_rr."'
																	AND trad_buy_sell = '".$v_buysell."' ".
																	"ORDER BY trad_advisor_name, trad_symbol, trad_buy_sell, trad_trade_date";
																	
				$result_trades_details = mysql_query($query_trades_details) or die(tdw_mysql_error($query_trades_details));
				$is_singleton = mysql_num_rows($result_trades_details);

				//if the detail is a single record then don't show the [+] as well as the detail record
				//xdebug("is_singleton",$is_singleton);
				if ($is_singleton == 1) {

					//S indicates single update
					$str_param_inline = $user_id."@".'S'."@".$v_tradedate ."@".$v_advcode ."@".$v_rr ."@".$v_symbol ."@".$v_buysell;
					//Show only the group record which is of course single 
									echo 't ['.$count_row_trades.'] = "'.'S'.'^'.
																												$row_trades_groups["trad_trade_date"].'^'.
																												$show_trad_advisor_name.'^'.
																												$row_trades_groups["trad_rr"].'^'.
																												$row_trades_groups["trad_symbol"].'^'.
																												offset_buy_sell($row_trades_groups["trad_buy_sell"]).'^'.
																												$row_trades_groups["trad_quantity"].'^'.
																												$row_trades_groups["trade_price"].'^'.
																												$row_trades_groups["trad_commission"].'^'.
																												$row_trades_groups["trad_cents_per_share"].'^'.
																												''.'^'.
																												$str_param_inline.'^'.
																												'S'.'"'.";\n";
				$count_row_trades = $count_row_trades + 1;

				} else { //there are more than one trades in the group
	

									$str_details_html = "";
									while($row_trades_details = mysql_fetch_array($result_trades_details))
									{
									
										
										$show_accinfo = $row_trades_details["show_acctinfo"];
										$str_param_inline_detail = $user_id."@".'SREF'."@".$v_tradedate ."@".$v_advcode ."@".$v_rr ."@".$v_symbol ."@".$v_buysell."@".$row_trades_details["trad_reference_number"]."@".$row_trades_details["trad_account_number"];
										$str_details_html .= "<tr class='alternaterow'>".
																				"<td width='14'> </td>".
																				"<td width='80'>".$row_trades_details["trad_trade_date"]."</td>".
																				"<td width='276' align='left'>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;".$show_accinfo."</td>".
																				"<td width='56'>&nbsp;&nbsp;&nbsp;".$row_trades_details["trad_rr"]."</td>".
																				"<td width='80'>&nbsp; &nbsp;&nbsp;".$row_trades_details["trad_symbol"]."</td>".
																				"<td width='80'>&nbsp;&nbsp;&nbsp;".offset_buy_sell($row_trades_details["trad_buy_sell"])."</td>".
																				"<td width='80' align='right'>".$row_trades_details["trad_quantity"]."&nbsp;&nbsp;</td>".
																				"<td width='80' align='right'>".$row_trades_details["trade_price"]."&nbsp;&nbsp;</td>".
																				"<td width='80' align='right'>".$row_trades_details["trad_commission"]."&nbsp;</td>".
																				"<td width='100' align='right'>".$row_trades_details["trad_cents_per_share"]."&nbsp;</td>".
																				"<td align='left'><a href=\\\"javascript:CreateWnd('rep_adj_all_rep_ca_pop.php?param=".$str_param_inline_detail."', 500, 350, false);\\\"><img src='".$_site_url."images/lf_v1/adj_singsing.png'></a></td></tr>";
																			  
																			
									}
									$str_param_inline_group = $user_id."@".'G'."@".$v_tradedate ."@".$v_advcode ."@".$v_rr ."@".$v_symbol ."@".$v_buysell;
									echo 't ['.$count_row_trades.'] = "'.''.'^'.
																												$row_trades_groups["trad_trade_date"].'^'.
																												$show_trad_advisor_name.'^'.
																												$row_trades_groups["trad_rr"].'^'.
																												$row_trades_groups["trad_symbol"].'^'.
																												offset_buy_sell($row_trades_groups["trad_buy_sell"]).'^'.
																												$row_trades_groups["trad_quantity"].'^'.
																												$row_trades_groups["trade_price"].'^'.
																												$row_trades_groups["trad_commission"].'^'.
																												$row_trades_groups["trad_cents_per_share"].'^'.
																												$str_details_html.'^'.
																												$str_param_inline_group.'^'.
																												''.'"'.";\n";
				//write the values in the previous variable to be passed to the array.
				//this way the array just gets processed with the required information and is boom baam boom																												
																												
				$count_row_trades = $count_row_trades + 1;
				
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
									"<td><div align='left'>&nbsp; &nbsp; &nbsp; "+rowtrades_array[2]+"</div></td>"+
									"<td>&nbsp;&nbsp;&nbsp;"+rowtrades_array[3]+"</td>"+
									"<td>&nbsp;&nbsp;&nbsp;"+rowtrades_array[4]+"</td>"+
									"<td>&nbsp;&nbsp;&nbsp;"+rowtrades_array[5]+"</td>"+
									"<td align='right'>"+rowtrades_array[6]+"&nbsp;&nbsp;&nbsp;</td>"+
									"<td align='right'>"+rowtrades_array[7]+"&nbsp;&nbsp;&nbsp;</td>"+
									"<td align='right'>"+rowtrades_array[8]+"&nbsp;&nbsp;&nbsp;</td>"+
									"<td align='right'>"+rowtrades_array[9]+"&nbsp;&nbsp;&nbsp;</td>"+
									"<td align='left'><a href=\"javascript:CreateWnd('rep_adj_all_rep_ca_pop.php?param="+rowtrades_array[11]+"', 500, 350, false);\">"+disp_img+"</a></td></tr>");

					document.write(next_row_open + rowtrades_array[10] + next_row_close);

					}
					</script>

<?		
			echo '
				</tbody>
					<tr bgcolor="#CCCCCC" class="display_totals">
						<td>&nbsp;</td>
						<td><div align="left">&nbsp;&nbsp;TOTALS:</div></td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">'.number_format($running_trad_commission_total,2,'.',',').'&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>';
?>