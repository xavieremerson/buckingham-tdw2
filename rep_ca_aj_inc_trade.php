<script language="JavaScript" type="text/JavaScript">
function autofitIframe(id){
	if (!window.opera && !document.mimeType && document.all && document.getElementById){
      parent.document.getElementById(id).style.height=this.document.body.offsetHeight+"px";
    }
    else if(document.getElementById) {
    parent.document.getElementById(id).style.height=this.document.body.scrollHeight+"px"
   }
}
</script>
<link href="includes/styles.css" rel="stylesheet" type="text/css">
<body onLoad="autofitIframe('ca_trades')">

<?
//Since this is a AJAX requested page, all inputs to this page should be passed with the param string
//Also, all the relevant includes should be a part of this page including css, etc
include('includes/global.php');
include('includes/dbconnect.php');
include('includes/functions.php');
?>
<?
//show_array($_GET);
if ($_GET) { // form submitted //format_date_ymd_to_mdy($trade_date_to_process)
		if($_GET["sel_client"] == '^ALL^') {
			$show_client = "Show All";
		} else {
			$show_client = $_GET["sel_client"];
		}

		if($_GET["sel_symbol"] == '^ALL^') {
			$show_symbol = "Show All";
		} else {
			$show_symbol = $_GET["sel_symbol"];
		}

			$sel_datefrom = $datefrom;
			$sel_dateto = $dateto;
			
			//if brokerage month is selected use that info to create dateto and datefrom values
			if($_GET["sel_month"] == '') {

				$sel_datefrom = $datefrom;
				$sel_dateto = $dateto;
				
				$datefrom = format_date_mdy_to_ymd($datefrom);
				$dateto = format_date_mdy_to_ymd($dateto);
				
				$string_heading = "Selection: Client(s): ".$show_client. "&nbsp;&nbsp;&nbsp;&nbsp;Symbol(s): ".$show_symbol. "&nbsp;&nbsp;&nbsp;&nbsp;Date From: ".$_GET["datefrom"]. "&nbsp;&nbsp;&nbsp;&nbsp;Date To: ".$_GET["dateto"];

			} else {

				$sel_datefrom = $datefrom;
				$sel_dateto = $dateto;

				// ^ caused problems, had to escape it				
				$arr_split_input = split("\^", $sel_month);
				$arr_dates = get_commission_month_dates($arr_split_input[0],$arr_split_input[1]);
				$datefrom = $arr_dates[0];
				$dateto = $arr_dates[1];
				
				$string_heading = "Selection: Client(s): ".$show_client. "&nbsp;&nbsp;&nbsp;&nbsp;Symbol(s): ".$show_symbol. "&nbsp;&nbsp;&nbsp;&nbsp;Date From: ".format_date_ymd_to_mdy($datefrom). "&nbsp;&nbsp;&nbsp;&nbsp;Date To: ".format_date_ymd_to_mdy($dateto);
				

			}
			

} else {


			$sel_datefrom = format_date_ymd_to_mdy($trade_date_to_process);
			$sel_dateto = format_date_ymd_to_mdy($trade_date_to_process);


			$string_heading = "";
			$show_client = "Show All";
			$show_symbol = "Show All";
			$datefrom = previous_business_day();
			$dateto = previous_business_day();
}
?>

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
				//xdebug("show_client",$show_client);
				$qry_string_client = " AND trad_advisor_code = '".$show_client."' ";
			} else {
				//xdebug("show_client",$show_client);
				$qry_string_client = "";
			}

								$query_trades = "SELECT 
													trad_advisor_code,
													trad_symbol,
													trad_buy_sell,
													DATE_FORMAT(trad_trade_date,'%m/%d/%Y') as trad_trade_date,
													max(trad_advisor_name) as trad_advisor_name,
													FORMAT(sum(trad_quantity),0) as trad_quantity,
													FORMAT(max(trade_price),2) as trade_price,
													FORMAT(sum(trad_commission),2) as trad_commission,
													sum(trad_commission) as for_sum_trad_commission,
													FORMAT(avg(trad_cents_per_share),3) as trad_cents_per_share 
												FROM mry_comm_rr_trades 
												WHERE trad_rr = '".$rep_to_process."'  
												AND trad_is_cancelled = 0 
												AND trad_trade_date between '".$datefrom."' AND '".$dateto."'"
												. $qry_string_symbol . $qry_string_client .
												" GROUP BY trad_advisor_code, trad_symbol, trad_buy_sell, trad_trade_date 
												ORDER BY trad_advisor_name, trad_symbol, trad_buy_sell, trad_trade_date";
												
												//xdebug("query_trades",$query_trades);
												//xdebug("qry_string_client",$qry_string_client);
      											$query_shared_rep_trades = "SELECT 
																a.trad_advisor_code,
																a.trad_symbol,
																a.trad_buy_sell,
																DATE_FORMAT(a.trad_trade_date,'%m/%d/%Y') as trad_trade_date,
																max(a.trad_advisor_name) as trad_advisor_name,
																FORMAT(sum(a.trad_quantity),0) as trad_quantity,
																FORMAT(max(a.trade_price),2) as trade_price,
																FORMAT(sum(a.trad_commission),2) as trad_commission,
																sum(a.trad_commission) as for_sum_trad_commission,
																FORMAT(avg(a.trad_cents_per_share),3) as trad_cents_per_share,
																max(a.trad_rr) as trad_rr
															FROM mry_comm_rr_trades a, sls_sales_reps b
															WHERE a.trad_rr = b.srep_rrnum 
															AND b.srep_user_id = '".$user_id."'
															AND trad_is_cancelled = 0 
															AND b.srep_isactive = 1 
															AND trad_trade_date between '".$datefrom."' AND '".$dateto."'"
															. $qry_string_symbol . $qry_string_client .
															" GROUP BY trad_advisor_code, trad_symbol, trad_buy_sell, trad_trade_date 
															ORDER BY trad_advisor_name, trad_symbol, trad_buy_sell, trad_trade_date";	
																										
															//xdebug("query_shared_rep_trades",$query_shared_rep_trades);
			
			$passtoexcel = md5(rand(100,999)).'^'.$rep_to_process.'^'.$user_id.'^'.$datefrom.'^'.$dateto.'^'.$qry_string_symbol.'^'.$qry_string_client;
			
			$result_trades = mysql_query($query_trades) or die(tdw_mysql_error($query_trades));
			$result_shared_rep_trades = mysql_query($query_shared_rep_trades) or die(tdw_mysql_error($query_shared_rep_trades));
			if (empty_qry($result_trades)==0 && empty_qry($result_shared_rep_trades)==0) {
			?>
				<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
					<tr>
						<td valign="top">				
						<table width="100%" border="0" cellspacing="1" cellpadding="2" bgcolor="#FFFFFF">				
							<tr>
								<td align="left"><font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#666666"><strong><?=$string_heading?></strong></font></td>
							</tr>
						</table>
						<table width="100%" border="0" cellspacing="1" cellpadding="2" bgcolor="#FFFFFF">				
							<tr>
								<td align="left"><font face="Verdana, Arial, Helvetica, sans-serif" size="2" color="#666666"><strong>No trades found.</strong></font></td>
							</tr>
						</table>			
						</td>
					</tr>
				</table>
			<?			
			} else {
			?>
				<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
					<tr>
						<td valign="top">				
						<table width="100%" border="0" cellspacing="1" cellpadding="2" bgcolor="#FFFFFF">				
							<tr>
									<td align="left"><font face="Verdana, Arial, Helvetica, sans-serif" size="1" color="#666666"><strong><?=$string_heading?></strong></font></td>
								</tr>
						</table>
						<table width="100%" border="0" cellspacing="1" cellpadding="2" bgcolor="#FFFFFF">				
							<tr>
									<td align="left"><a class="links_temp" href="rep_ca_exp_trade_excel.php?xl=<?=$passtoexcel?>" target="_blank">GET TO EXCEL</a></td>
								</tr>
						</table>
						<script language="JavaScript" src="includes/javascript/sorttable.js" type="text/javascript"></script>
						<table class="sortable" preserve_style="cell" width="100%" border="0" cellspacing="1" cellpadding="2" bgcolor="#FFFFFF">
							<thead class="datadisplay"> <!--  class="datadisplay" -->
								<tr bgcolor="#CCCCCC">
									<td ts_type="date" width="80"> Trd Date</td>
									<td width="276">CLIENT</td>
									<td width="56"> RR #</td>
									<td width="80"> Symbol</td>
									<td width="80"> B/S</td>
									<td ts_type="money" width="80"> Shares</td>
									<td ts_type="money" width="80"> Price</td>
									<td ts_type="money" width="80"> Commission</td>
									<td ts_type="money" width="100"> Comm./Shr.</td>
									<td>&nbsp;&nbsp;&nbsp;RESEARCH</td>
								</tr>
							</thead>
							<tbody id="offTblBdy" class="datadisplay">
							
							<script type="text/javascript">
								var displaytrades = new Array()
				
							<? 
							$count_row_trades = 0;
							$running_trad_commission_total = 0;
							while($row_trades = mysql_fetch_array($result_trades))
							{
								
								if ($row_trades["trad_advisor_name"] == '') {
									$show_trad_advisor_name = $row_trades["trad_advisor_code"];
								} else {
									$show_trad_advisor_name = $row_trades["trad_advisor_name"];
								}
								
								$show_trad_rr = $rep_to_process;
								$show_trad_trade_date = format_date_ymd_to_mdy($row_trades["trad_trade_date"]);
								$show_trad_symbol = $row_trades["trad_symbol"];
								$show_trad_buy_sell = $row_trades["trad_buy_sell"];
								$show_trad_quantity = number_format($row_trades["trad_quantity"],0,'.',",");
								$show_trade_price = number_format($row_trades["trade_price"],2,'.',",");
								$show_trad_commission = number_format($row_trades["trad_commission"],2,'.',",");
								$show_trad_cents_per_share = number_format($row_trades["trad_cents_per_share"],3,'.',",");	
								$running_trad_commission_total = $running_trad_commission_total + $row_trades["for_sum_trad_commission"];
							
								echo 'displaytrades ['.$count_row_trades.'] = "'.$row_trades["trad_trade_date"].'^'.
																													$show_trad_advisor_name.'^'.
																													$rep_to_process.'^'.
																													$row_trades["trad_symbol"].'^'.
																													offset_buy_sell($row_trades["trad_buy_sell"]).'^'.
																													$row_trades["trad_quantity"].'^'.
																													$row_trades["trade_price"].'^'.
																													$row_trades["trad_commission"].'^'.
																													$row_trades["trad_cents_per_share"].'"'.";\n";
							
								$count_row_trades = $count_row_trades + 1;
							}

							while($row_shared_rep_trades = mysql_fetch_array($result_shared_rep_trades))
							{
								
								if ($row_shared_rep_trades["trad_advisor_name"] == '') {
									$show_trad_advisor_name = $row_shared_rep_trades["trad_advisor_code"];
								} else {
									$show_trad_advisor_name = $row_shared_rep_trades["trad_advisor_name"];
								}
								
								$show_trad_rr = $row_shared_rep_trades["trad_rr"];;
								$show_trad_trade_date = format_date_ymd_to_mdy($row_shared_rep_trades["trad_trade_date"]);
								$show_trad_symbol = $row_shared_rep_trades["trad_symbol"];
								$show_trad_buy_sell = $row_shared_rep_trades["trad_buy_sell"];
								$show_trad_quantity = number_format($row_shared_rep_trades["trad_quantity"],0,'.',",");
								$show_trade_price = number_format($row_shared_rep_trades["trade_price"],2,'.',",");
								$show_trad_commission = number_format($row_shared_rep_trades["trad_commission"],2,'.',",");
								$show_trad_cents_per_share = number_format($row_shared_rep_trades["trad_cents_per_share"],3,'.',",");	
								$running_trad_commission_total = $running_trad_commission_total + $row_shared_rep_trades["for_sum_trad_commission"];
							
								echo 'displaytrades ['.$count_row_trades.'] = "'.$row_shared_rep_trades["trad_trade_date"].'^'.
																													$show_trad_advisor_name.'^'.
																													$row_shared_rep_trades["trad_rr"].'^'.
																													$row_shared_rep_trades["trad_symbol"].'^'.
																													offset_buy_sell($row_shared_rep_trades["trad_buy_sell"]).'^'.
																													$row_shared_rep_trades["trad_quantity"].'^'.
																													$row_shared_rep_trades["trade_price"].'^'.
																													$row_shared_rep_trades["trad_commission"].'^'.
																													$row_shared_rep_trades["trad_cents_per_share"].'"'.";\n";
							
								$count_row_trades = $count_row_trades + 1;
							}
							?>
							
								for (i=0;i<displaytrades.length;i++)
									{
									var rowtrades_array = new Array()
									var rowclass
									if (i%2 == 0) {
										rowclass = " class=\"alternateRow\"";
									} else {
										rowclass = "";
									}
									
									rowtrades_array=displaytrades[i].split("^");
										document.write(
														"<tr" + rowclass + ">"+"<td>&nbsp;&nbsp;&nbsp;"+rowtrades_array[0]+"</td>"+
														"<td><div align='left'>&nbsp; &nbsp; &nbsp; "+rowtrades_array[1]+"</div></td>"+
														"<td>&nbsp;&nbsp;&nbsp;"+rowtrades_array[2]+"</td>"+
														"<td>&nbsp;&nbsp;&nbsp;"+rowtrades_array[3]+"</td>"+
														"<td>&nbsp;&nbsp;&nbsp;"+rowtrades_array[4]+"</td>"+
														"<td align='right'>"+rowtrades_array[5]+"&nbsp;&nbsp;&nbsp;</td>"+
														"<td align='right'>"+rowtrades_array[6]+"&nbsp;&nbsp;&nbsp;</td>"+
														"<td align='right'>"+rowtrades_array[7]+"&nbsp;&nbsp;&nbsp;</td>"+
														"<td align='right'>"+rowtrades_array[8]+"&nbsp;&nbsp;&nbsp;</td>"+
														"<td align='right'>&nbsp;<a href='javascript:window.alert(\"Recent Research on "+ rowtrades_array[3] + "\\" + "n" + "This feature scheduled for the next release"+"\");'>"+
														"<img src='images/lf_v1/research.png' border='0' alt='Recent Research on'>" +
														"</a>&nbsp;&nbsp;&nbsp;</td></tr>");
									}
									</script>
							
						</tbody>
						<tfoot>
								<tr bgcolor="#CCCCCC" class="display_totals">
									<td><div align="left">&nbsp;&nbsp;TOTALS:</div></td>
									<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
									<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
									<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
									<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td align="right"><?=number_format($running_trad_commission_total,2,'.',',')?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
									<td align="right">&nbsp;</td>
									<td>&nbsp;</td>
								</tr>
							</tfoot>
						</table>
						</td>
					</tr>
				</table>
			<?
			}
			?>
</body>