<?						
//*********************************************************************************************
//Create Lookup Array of Client Code / Client Name

	$qry_clients = "select * from int_clnt_clients";
	$result_clients = mysql_query($qry_clients) or die (tdw_mysql_error($qry_clients));
	$arr_clients = array();
	while ( $row_clients = mysql_fetch_array($result_clients) ) 
	{
		$arr_clients[$row_clients["clnt_code"]] = $row_clients["clnt_name"];
	}
	
	//temporary MUST CHANGE THIS LATER
	function look_up_client($clnt) {
		global $arr_clients;
		if ($arr_clients[$clnt] == '') {
		   return $clnt;
		} else {
		   return $arr_clients[$clnt];
		}
	}
//*********************************************************************************************

				
			//Show research link only for the symbols in the coverage universe
			$arr_cu = get_coverage_universe();

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
				$chk_qry_string_client = " AND chek_advisor = '".$show_client."' ";
			} else {
				$qry_string_client = "";
				$chk_qry_string_client = "";
			}
//			if ($show_rep != "Show All") {
//				$qry_string_rep = " AND trad_rr = '".$show_rep."' ";
//        $chk_rep = db_single_val("select Initials as single_val from users where rr_num = '".$show_rep."'");
//				$chk_qry_string_rep = " AND chek_reps_and like '%".$chk_rep."%' ";
				//xdebug("chk_qry_string_rep",$chk_qry_string_rep);
//			} else {
				$qry_string_rep = "";
				$chk_qry_string_rep = "";
//			}
			
			$query_checks = "SELECT chek_amount,
												chek_type,
												chek_advisor,
												chek_comments,
												DATE_FORMAT(chek_date,'%m/%d/%Y') as chek_date,
												chek_reps_and
												from chk_chek_payments_etc
												where chek_date between '".$datefrom."' AND '".$dateto."' ".
												$chk_qry_string_rep . $chk_qry_string_client;
			
			//xdebug("query_checks",$query_checks);

			//There is a know issue that since some clients have multiple RRs, e.g. GART the data shown gets max(rr)
			//which means the totals will be accurate but the rr agains the client will be inaccurate.
			
			//fixing the query (excel) to account for the incorrect subtotals by rr (carol)
			
			$query_trades = "SELECT 
													trad_advisor_code,
													trad_symbol,
													trad_buy_sell,
													DATE_FORMAT(trad_trade_date,'%m/%d/%Y') as trad_trade_date,
													max(trad_advisor_name) as trad_advisor_name,
													FORMAT(sum(trad_quantity),0) as trad_quantity,
													sum(trad_quantity) as for_sum_trad_quantity,
													FORMAT(avg(trade_price),2) as trade_price,
													FORMAT(sum(trad_commission),2) as trad_commission,
													sum(trad_commission) as for_sum_trad_commission,
													sum(trad_commission) as val_commission,
													FORMAT(avg(trad_cents_per_share),3) as trad_cents_per_share,
													trad_rr 
												FROM mry_comm_rr_trades 
												WHERE trad_is_cancelled = 0 
												AND trad_trade_date between '".$datefrom."' AND '".$dateto."'"
												. $qry_string_symbol . $qry_string_client .$qry_string_rep .
												" GROUP BY trad_advisor_code, trad_rr, trad_symbol, trad_buy_sell, trad_trade_date 
												ORDER BY trad_advisor_name, trad_symbol, trad_buy_sell, trad_trade_date";

												//max(trad_rr) as trad_rr 
												//FORMAT(max(trade_price),2) as trade_price,
			
			//xdebug("query_trades",$query_trades);
			//$passtoexcel = $query_trades;
			
			$query_shared_rep_trades = "SELECT 
													a.trad_advisor_code,
													a.trad_symbol,
													a.trad_buy_sell,
													DATE_FORMAT(a.trad_trade_date,'%m/%d/%Y') as trad_trade_date,
													max(a.trad_advisor_name) as trad_advisor_name,
													FORMAT(sum(a.trad_quantity),0) as trad_quantity,
													sum(a.trad_quantity) as for_sum_trad_quantity,
													FORMAT(max(a.trade_price),2) as trade_price,
													FORMAT(sum(a.trad_commission),2) as trad_commission,
													sum(a.trad_commission) as val_commission,
													sum(a.trad_commission) as for_sum_trad_commission,
													FORMAT(avg(a.trad_cents_per_share),3) as trad_cents_per_share,
													trad_rr
												FROM mry_comm_rr_trades a, sls_sales_reps b
												WHERE a.trad_rr = b.srep_rrnum 
												AND b.srep_user_id = '".$rep_id."'
												AND trad_is_cancelled = 0 
												AND b.srep_isactive = 1 
												AND trad_trade_date between '".$datefrom."' AND '".$dateto."'"
												. $qry_string_symbol . $qry_string_client .
												" GROUP BY trad_advisor_code, trad_rr, trad_symbol, trad_buy_sell, trad_trade_date 
												ORDER BY trad_advisor_name, trad_symbol, trad_buy_sell, trad_trade_date";	
			
		  $passtoexcel = md5(rand(100,999)).'^'.$rep_id.'^'.$datefrom.'^'.$dateto.'^'.$qry_string_symbol.'^'.$qry_string_client.'^'.$qry_string_rep;
			
			/*
			$passtoexcel = "SELECT 
												trad_advisor_code,
												trad_symbol,
												trad_buy_sell,
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
											" GROUP BY trad_rr, trad_advisor_code, trad_symbol, trad_buy_sell, trad_trade_date 
											ORDER BY trad_advisor_name, trad_symbol, trad_buy_sell, trad_trade_date";
			*/
						
echo '
		<table width="100%" border="0" cellspacing="1" cellpadding="2" bgcolor="#FFFFFF">				
			<tr>
					<td align="left"><a class="links_temp" href="anly_rep_all_rep_ca_exp_trade_excel.php?xl='.$passtoexcel.'" target="_blank"><img src="images/lf_v1/exp2excel.png" border="0"></a>&nbsp;&nbsp;&nbsp;<font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#666666"><strong>'.$string_heading.'</strong>&nbsp;&nbsp;</font></td>
					<td>&nbsp;</td>
				</tr>
		</table>';
?>

		<?
		if ($show_symbol == "This condition can be developed later") {
		echo '		
		<table width="100%" border="0" cellspacing="1" cellpadding="2" bgcolor="#FFFFFF">				
			<tr>
					<td align="right">
					<a class="links11" href="#">'.$show_symbol.' ('.get_company_name($show_symbol).') Rating: STRONG BUY (02/20/2004) Target: $60</a> | 
					<a class="links11" href="http://192.168.20.65/icil/owa/list_results?in_doc_type=ALL&author=NONE&in_industry=NONE&tickers='.$show_symbol.'&dated='.format_date_ymd_to_mdy(business_day_backward(strtotime(previous_business_day()),60)).'&dated2='.format_date_ymd_to_mdy(previous_business_day()).'" target="_blank">Recent Research on '.$show_symbol.'</a>
					</td>
				</tr>
		</table>';
		}
		?>

		<table width="100%" height="100%" cellpadding="1", cellspacing="0" bgcolor="#AAAAAA">
			<tr>
				<td>		

		<script language="JavaScript" src="includes/javascript/sorttable.js" type="text/javascript"></script>
		<table class="sortable" preserve_style="cell" width="100%" border="0"  cellspacing="1" cellpadding="1">
		  <thead class="datadisplay">
				<tr bgcolor="#cccccc">
					<td ts_type="date" width="90">Trade Date</td>
				  <td width="276">ADVISOR / CLIENT</td>
					<td width="56">RR #</td>
					<td width="80">Symbol</td>
					<td width="80">B/S</td>
					<td ts_type="money" width="80">Shares</td>
					<td ts_type="money" width="80">Price</td>
					<td ts_type="money" width="80">Commission</td>
					<td ts_type="money" width="100">Comm./Shr.</td>
					<td align="right">&nbsp;&nbsp;&nbsp;RESEARCH</td>
				</tr>
  		</thead>
  		<tbody id="offTblBdy" class="datadisplay">
						
			<script type="text/javascript">
			var dt = new Array()

			<? 
			//This section populates the javascript array
			//Performance hurdles are taken care of
			$result_trades = mysql_query($query_trades) or die(tdw_mysql_error($query_trades));
			$result_shared_rep_trades = mysql_query($query_shared_rep_trades) or die(tdw_mysql_error($query_shared_rep_trades));
			
			$count_row_trades = 0;
			$running_trad_commission_total = 0;
			$running_quantity_total = 0;
			while($row_trades = mysql_fetch_array($result_trades))
			{
				
				if ($row_trades["trad_advisor_name"] == '') {
				    $get_from_db = db_single_val("select max(clnt_name) as single_val from int_clnt_clients where clnt_code = '".$row_trades["trad_advisor_code"]."'");
					//xdebug("get_from_db",$get_from_db);
					if (trim($get_from_db) != "") {
						$show_trad_advisor_name = $get_from_db;
					} else {
						$show_trad_advisor_name = $row_trades["trad_advisor_code"];
					}
				} else {
					$show_trad_advisor_name = $row_trades["trad_advisor_name"];
				}
				
				$running_trad_commission_total = $running_trad_commission_total + $row_trades["for_sum_trad_commission"];
				$running_quantity_total = $running_quantity_total + $row_trades["for_sum_trad_quantity"]; 
				//xdebug("running_quantity_total",$running_quantity_total);
				
				if (in_array($row_trades["trad_symbol"],$arr_cu)) {
				$int_in_cu = 1;
				} else {
				$int_in_cu = 0;
				}
				
				$str_cents_per_share = round(($row_trades["val_commission"]/$row_trades["for_sum_trad_quantity"]),3);
				//xdebug("commission",$row_trades["val_commission"]);
				//xdebug("quantity",$row_trades["for_sum_trad_quantity"]);
				//echo "//".$row_trades["val_commission"].">>".$row_trades["for_sum_trad_quantity"].">>".round(($row_trades["val_commission"]/$row_trades["for_sum_trad_quantity"]),3)."\n";
				
				echo 'dt ['.$count_row_trades.'] = "'.$row_trades["trad_trade_date"].'^'.
																												trim($show_trad_advisor_name).'^'.
																												$row_trades["trad_rr"].'^'.
																												$row_trades["trad_symbol"].'^'.
																												offset_buy_sell($row_trades["trad_buy_sell"]).'^'.
																												$row_trades["trad_quantity"].'^'.
																												$row_trades["trade_price"].'^'.
																												$row_trades["trad_commission"].'^'.
																												$str_cents_per_share.'^'.
																												$int_in_cu.'^'.
																												trim($row_trades["trad_advisor_code"]).'"'.";\n";
																												//$row_trades["trad_cents_per_share"]
																												//$str_cents_per_share

				$count_row_trades = $count_row_trades + 1;
			}
			
			//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			//Checks
/*			$result_checks = mysql_query($query_checks) or die(tdw_mysql_error($query_checks));
			$count_row_checks = $count_row_trades;
			while($row_checks = mysql_fetch_array($result_checks))
			{
				//look_up_client				
				$running_trad_commission_total = $running_trad_commission_total + $row_checks["chek_amount"];
				//xdebug("running_quantity_total",$running_quantity_total);

				$arr_reps = explode("#",$row_checks["chek_reps_and"]);
				//show_array($arr_reps); 
				
				echo 'dt ['.$count_row_checks.'] = "'.$row_checks["chek_date"].'^'.
																												trim(look_up_client($row_checks["chek_advisor"])).'^'.
																												$row_trades["trad_rr"].'^'.
																												"---".'^'.
																												"---".'^'.
																												"[CHECK]".'^'.
																												"---".'^'.
																												$row_checks["chek_amount"].'^'.
																												"[CHECK]".'^'.
																												"0".'^'.
																												trim($row_checks["chek_advisor"]).'"'.";\n";

				$count_row_checks = $count_row_checks + 1;
			}*/
			//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

?>		
				var datefromString = '<?=date('m-d-Y',time() - (60*60*24*90))?>';
				var datetoString = '<?=date('m-d-Y')?>';
				
				for (i=0;i<dt.length;i++)
					{
					var rowtrades_array = new Array()
					var rowclass
					var research_link
					if (i%2 == 0) {
						rowclass = "trdark";
					} else {
						rowclass = "trlight";
					}
					
					rowtrades_array=dt[i].split("^");
					
					if (rowtrades_array[9]==1) {
					research_link = "<a href='http://192.168.20.63/rv/sr/?in_ticker="+
										rowtrades_array[3]+
										"&datefrom=" + datefromString + "&dateto=" + datetoString +
										"'" +
										" target='_blank'><img src='images/lf_v1/research.png' border='0' alt='Recent Research on "+
										rowtrades_array[3]+
										"'></a>";
					} else {
					research_link = "";
					}
					
					
						document.write(
										"<tr class='" + rowclass + "'>"+"<td>&nbsp;&nbsp;&nbsp;"+rowtrades_array[0]+"</td>"+
										//"<td onclick=\"CreateWnd(\'chart_t12m.php?clnt="+rowtrades_array[10]+"\', 620, 330, false);\"><div align='left'>&nbsp; <a href='#'><img src=\"images/t12m.png\" border=\"0\" onclick=\"CreateWnd(\'chart_t12m.php?clnt="+rowtrades_array[10]+"\', 620, 330, false);\"></a>&nbsp; &nbsp; "+rowtrades_array[1]+"</div></td>"+
										"<td onclick=\"showPopWin(\'chart_t12m.php?clnt="+rowtrades_array[10]+"\', 626, 340, null);\"><div align='left'>&nbsp; <a href='#'><img src=\"images/t12m.png\" border=\"0\"></a>&nbsp; &nbsp; "+rowtrades_array[1]+"</div></td>"+
										"<td>&nbsp;&nbsp;&nbsp;"+rowtrades_array[2]+"</td>"+
										"<td>&nbsp;&nbsp;&nbsp;"+rowtrades_array[3]+"</td>"+
										"<td>&nbsp;&nbsp;&nbsp;"+rowtrades_array[4]+"</td>"+
										"<td align='right'>"+rowtrades_array[5]+"&nbsp;&nbsp;&nbsp;</td>"+
										"<td align='right'>"+rowtrades_array[6]+"&nbsp;&nbsp;&nbsp;</td>"+
										"<td align='right'>"+rowtrades_array[7]+"&nbsp;&nbsp;&nbsp;</td>"+
										"<td align='right'>"+rowtrades_array[8]+"&nbsp;&nbsp;&nbsp;</td>"+
										"<td align='right'>&nbsp;" + research_link + "&nbsp;&nbsp;&nbsp;</td></tr>");
					}
					</script>
<?		
			echo '
				</tbody>
				<tfoot>
					<tr bgcolor="#CCCCCC" class="display_totals">
						<td><div align="left">&nbsp;&nbsp;TOTALS:</div></td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td align="right">'.number_format($running_quantity_total,0,'.',',').'</td>
						<td align="right">&nbsp;</td>
						<td align="right">'.number_format($running_trad_commission_total,2,'.',',').'&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</tfoot>
				</table>
				</td></tr></table>';

$str_timedebug .= "<br>Trades: " .sprintf("%01.4f",((getmicrotime()-$arr_timedebug[$str_timecount])/1000))." s.";
$str_timecount++;
$arr_timedebug[$str_timecount]=getmicrotime();

?>
