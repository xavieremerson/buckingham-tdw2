<?
  include('includes/dbconnect.php');
  include('includes/global.php');
	include('includes/functions.php');

	//Previous Business Day should be applied here.
	//$trade_date_to_process = previous_business_day();
	$trade_date_to_process = previous_business_day();
	//$trade_date_to_process = '2006-08-02';
	//$date_match_val = date("M j Y",strtotime('2006-08-02'));
	$date_match_val = date("M j Y",strtotime($trade_date_to_process));

?>
<?
/*
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// BEGIN JOVUS SECTION
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

# SQL Server Connection Information
$msconnect=mssql_connect("192.168.20.48","BUCKINGHAM_login","BUCKINGHAM_pw");
$msdb=mssql_select_db("BUCKINGHAM",$msconnect);


	xdebug('Connecting to Jovus Server @ Buckingham','Successful');

  //First get all ratings/target changes for the previous business day (if any)
	 
		$arr_mri = array();
		$arr_mri_symbols = array();

		$ms_qry_mri = "SELECT dbo.Prod_Issuers.ProductID, 
									dbo.Research.DocID, 
									dbo.Issuers.IssuerName, 
									dbo.Issuers.CUSIP, 
									dbo.Prod_Issuers.Recommendation, 
									dbo.Prod_Issuers.PreviousRecommendation, 
									dbo.Prod_Issuers.RecommendationAction, 
									dbo.Prod_Issuers.TargetPrice, 
									dbo.Prod_Statuses.DateTime, 
									dbo.Prod_Statuses.StatusTypeID
									FROM ((dbo.Issuers INNER JOIN dbo.Prod_Issuers ON dbo.Issuers.IssuerID = dbo.Prod_Issuers.IssuerID) 
												 INNER JOIN dbo.Prod_Statuses ON dbo.Prod_Issuers.ProductID = dbo.Prod_Statuses.ProductID) 
									INNER JOIN dbo.Research ON dbo.Prod_Issuers.ProductID = dbo.Research.ResearchID
									WHERE (((dbo.Prod_Statuses.StatusTypeID)=3))";
 									 //AND CAST(FLOOR(CAST(dbo.Prod_Statuses.DateTime AS float)) AS datetime) = '".$trade_date_to_process."'

		//xdebug("ms_qry_mri",$ms_qry_mri);
		$ms_results_mri = mssql_query($ms_qry_mri);
		
		$v_count_mri = 0;
		while ($row_mri = mssql_fetch_array($ms_results_mri)) {
					
					//show_array($row_mri);
					$symbol = $row_mri[3];
					$rating = $row_mri[4];
					$rating_change = $row_mri[6]; 
					$target = $row_mri[7];

					if ($rating_change == "Increase") {
					  $img_show = '<img src="images/themes/standard/arrow_up.gif" border="0">';
						$arr_mri[$v_count_mri] = $symbol."<###>".$rating."<###>".$rating_change."<###>".$img_show."<###>".$target;
						$arr_mri_symbols[$v_count_mri] = $symbol;
						$v_count_mri = $v_count_mri + 1;
					} elseif ($rating_change == "Decrease"){
					  $img_show = '<img src="images/themes/standard/arrow_down.gif" border="0">';
						$arr_mri[$v_count_mri] = $symbol."<###>".$rating."<###>".$rating_change."<###>".$img_show."<###>".$target;
						$arr_mri_symbols[$v_count_mri] = $symbol;
						$v_count_mri = $v_count_mri + 1;
					} else {
					  $img_show = '';
					}

					//echo "<hr>".$symbol." >> ".$rating." >> ".$rating_change." >> ".$img_show." >> ".$target; 
		}
   //show_array($arr_mri_symbols);


 	$arr_symbols = array(); 
	xdebug ('Processing Documents for date', $trade_date_to_process);

	$msquery = "exec prGetAllPublishedDocIds '".$trade_date_to_process."'";
	$msresults= mssql_query($msquery);
	
	$v_count_docs = 0;
	$str_symbols = "";

	while ($row = mssql_fetch_array($msresults)) {
				
				$date_matched = strripos("prefix".$row[3], $date_match_val);
				
				if ($date_matched) {

							 // Getting Ticker(s)
								$msquery2 = "EXEC prGetAllTickersInNote '".$row[0]."'";
								$msresults2= mssql_query($msquery2);
									while ($row2 = mssql_fetch_array($msresults2)) {
										$str_symbols .= $row2[0].",";
									}
									//echo "<b>Symbols: </b>".$str_symbols;
									$v_count_docs = $v_count_docs + 1;

				} else {
					 //do nothing;
				}
	}
	$arr_symbols = explode(",",$str_symbols);
  //show_array($arr_symbols);
	xdebug('Found document(s) in Jovus.',$v_count_docs);

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// END JOVUS SECTION
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
*/

//Create Lookup Array of Client Code / Client Name
$qry_clients = "select * from int_clnt_clients";
$result_clients = mysql_query($qry_clients) or die (tdw_mysql_error($qry_clients));
$arr_clients = array();
while ( $row_clients = mysql_fetch_array($result_clients) ) 
{
	$arr_clients[$row_clients["clnt_code"]] = $row_clients["clnt_name"];
}

//Create an array of account names and advisor code for lookup.
$qry_acct_adv = "select nadd_full_account_number, nadd_advisor from mry_nfs_nadd";
$result_acct_adv = mysql_query($qry_acct_adv) or die (tdw_mysql_error($qry_acct_adv));
$arr_acct_adv = array();
while ( $row_acct_adv = mysql_fetch_array($result_acct_adv) ) 
{
	$arr_acct_adv[strtoupper(trim($row_acct_adv["nadd_full_account_number"]))] = $row_acct_adv["nadd_advisor"];
}

/*
							//FLUSH temp tables
							$result_flush = mysql_query("truncate table tmp_mry_cmpl_temp") or die (mysql_error());
							$result_flush = mysql_query("truncate table tmp_mry_cmpl_trades") or die (mysql_error());
							echo "tmp_mry_cmpl_temp and tmp_mry_cmpl_trades are flushed and ready for the next set of data<br>";

							//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++						
						  //get only regular trades, not the cancelled trades, the cancelled trades will be 
							//processed in a separate section at the end of segment	
								$query_trades = "SELECT * 
																 FROM nfs_trades
																 WHERE trad_run_date = '".$trade_date_to_process."'
																 AND trad_cancel_code != '1'"; //AND trad_branch = 'PDY'
	  						xdebug ("query_trades",$query_trades);
  							$result_trades = mysql_query($query_trades) or die(tdw_mysql_error($query_trades));
								$countval = 1;
								while($row_trades = mysql_fetch_array($result_trades))
								{
									//get data to insert into temp table to process further
									$comm_trade_reference_number = 	trim($row_trades["trad_trade_reference_number"]);
									$trad_full_account_number = 		trim($row_trades["trad_full_account_number"]);
									$trad_short_name = 							str_replace("'","",trim($row_trades["trad_short_name"]));
									$comm_rr = 											trim($row_trades["trad_registered_rep"]);
									$comm_trade_date = 							$row_trades["trad_trade_date"];
									$comm_run_date = 							  $row_trades["trad_run_date"];
									$comm_advisor_code = 						$arr_acct_adv[strtoupper(trim($row_trades["trad_full_account_number"]))];
									$comm_advisor_name = 						str_replace("'","\'",$arr_clients[substr($row_trades["trad_short_name"],0,4)]);
									$comm_account_name = 						str_replace("'","",get_account_name($row_trades["trad_full_account_number"])); //stupid single quote
									$comm_account_number = 					trim($row_trades["trad_full_account_number"]);
									$comm_symbol = 									trim($row_trades["trad_symbol"]);
									$comm_buy_sell = 								trim($row_trades["trad_buy_sell"]);
									$comm_quantity = 								$row_trades["trad_quantity"];
									$comm_price = 									$row_trades["trad_price"];
									$comm_commission_code = 				$row_trades["trad_commission_concession_code"];
									$comm_commission = 							$row_trades["trad_trade_commission"];
									
									if ($row_trades["trad_commission_concession_code"] == 3) { //This indicates cents/share
										$comm_cents_per_share = $row_trades["trad_trade_commission"]/$row_trades["trad_quantity"];
									} else {
										$comm_cents_per_share = 0;
									}
							
									$qry_insert_trade = "insert into tmp_mry_cmpl_temp(
																			comm_trade_reference_number,
																			comm_rr, 
																			comm_trade_date, 
																			comm_run_date, 
																			comm_advisor_code,
																			comm_advisor_name, 
																			comm_account_name, 
																			comm_account_number, 
																			comm_symbol, 
																			comm_buy_sell, 
																			comm_quantity, 
																			comm_price, 
																			comm_commission_code, 
																			comm_commission, 
																			comm_cents_per_share)
																			values(".
																			"'".$comm_trade_reference_number."',".
																			"'".$comm_rr."',".
																			"'".$comm_trade_date."',". 
																			"'".$comm_run_date."',". 
																			"'".$comm_advisor_code."',". 
																			"'".$comm_advisor_name."',". 
																			"'".$comm_account_name."',". 
																			"'".$comm_account_number."',". 
																			"'".$comm_symbol."',". 
																			"'".$comm_buy_sell."',".
																			"'".$comm_quantity."',". 
																			"'".$comm_price."',". 
																			"'".$comm_commission_code."',". 
																			"'".$comm_commission."',". 
																			"'".$comm_cents_per_share."')";
																			
									$result_insert_trade = mysql_query($qry_insert_trade) or die(tdw_mysql_error($qry_insert_trade));
									$countval = $countval + 1;
								}
								echo "Data inserted to temporary table for further processing.<br>";
							//// Processing from temporary table.
							
							//Get unique RR from table
								$query_rr = "SELECT distinct(comm_rr) from tmp_mry_cmpl_temp order by comm_rr"; 
								$result_rr = mysql_query($query_rr) or die(mysql_error());
								while($row_rr = mysql_fetch_array($result_rr))
								{

											//_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_*_
											//PROCESS FOR TABLE: rep_comm_rr_trades 
											//fields in table mry_comm_rr : 
											//comm_trade_reference_number  comm_rr  comm_trade_date  comm_advisor_code comm_advisor_name  
											//comm_account_name  comm_account_number  comm_symbol  comm_buy_sell  
											//comm_quantity  comm_price  comm_commission_code  comm_commission  comm_cents_per_share 
											$query_comm_trd =  "SELECT *
																					FROM tmp_mry_cmpl_temp
																					WHERE comm_rr = '".$row_rr["comm_rr"]."'"; 
											$result_comm_trd = mysql_query($query_comm_trd) or die(mysql_error());
											
											while($row_comm_trd = mysql_fetch_array($result_comm_trd))
											{
												$qyery_insert_trade = "INSERT INTO tmp_mry_cmpl_trades 
																								(trad_reference_number,
																								trad_rr,
																								trad_trade_date,
																								trad_run_date,
																								trad_advisor_code,
																								trad_advisor_name,
																								trad_account_name,
																								trad_account_number,
																								trad_symbol,
																								trad_buy_sell,
																								trad_quantity,
																								trade_price,
																								trad_commission,
																								trad_cents_per_share
																								) VALUES (".
																								"'".$row_comm_trd["comm_trade_reference_number"]."',".
																								"'".$row_comm_trd["comm_rr"]."',".
																								"'".$row_comm_trd["comm_trade_date"]."',".
																								"'".$row_comm_trd["comm_run_date"]."',".
																								"'".$row_comm_trd["comm_advisor_code"]."',".
																								"'".str_replace("'","\'",$row_comm_trd["comm_advisor_name"])."',". 
																								"'".str_replace("'","\'",$row_comm_trd["comm_account_name"])."',".
																								"'".$row_comm_trd["comm_account_number"]."',". 
																								"'".$row_comm_trd["comm_symbol"]."',". 
																								"'".$row_comm_trd["comm_buy_sell"]."',". 
																								"'".$row_comm_trd["comm_quantity"]."',".
																								"'".$row_comm_trd["comm_price"]."',". 
																								"'".$row_comm_trd["comm_commission"]."',". 
																								"'".$row_comm_trd["comm_cents_per_share"]."')";
												$result_insert_trade = mysql_query($qyery_insert_trade) or die(tdw_mysql_error($qyery_insert_trade));
																								
											}
								}

*/								
////
//Get user information for use within the application
//
// Currently implemented in login.php and registered as session variable.
// Have to include user privilege field later on and register that too.

echo "<!--"."Server: ".$_SERVER["SERVER_ADDR"]."-->\n";
echo "<!--"."Client: ".$_SERVER["REMOTE_ADDR"]."-->\n";
echo "<!--"."Administrator Email: ".$_SERVER["SERVER_ADMIN"]."-->\n";
echo "<!--"."Page Process Time: ".date("D, m/d/Y h:i a")."-->\n";
?>
<link rel="shortcut icon" href="favicon.ico"></link>
<link rel="bookmark" href="favicon.ico"></link>
<title><?=$_app_title?> <?=$PHP_SELF?></title>
<link rel="stylesheet" type="text/css" href="includes/styles.css">
<?

			$sel_datefrom = format_date_ymd_to_mdy($trade_date_to_process);
			$sel_dateto = format_date_ymd_to_mdy($trade_date_to_process);

			$string_heading = "";
			$show_rep = "Show All";
			$show_client = "Show All";
			$show_symbol = "Show All";
			$datefrom = previous_business_day();
			$dateto = previous_business_day();

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
													FORMAT(max(trade_price),2) as trade_price,
													FORMAT(sum(trad_commission),2) as trad_commission,
													sum(trad_commission) as for_sum_trad_commission,
													FORMAT(avg(trad_cents_per_share),3) as trad_cents_per_share,
													max(trad_rr) as trad_rr 
												FROM tmp_mry_cmpl_trades 
												WHERE trad_is_cancelled = 0 
												AND trad_trade_date between '".$datefrom."' AND '".$dateto."'"
												. $qry_string_symbol . $qry_string_client .$qry_string_rep .
												" GROUP BY trad_advisor_code, trad_symbol, trad_buy_sell, trad_trade_date 
												ORDER BY trad_advisor_name, trad_symbol, trad_buy_sell, trad_trade_date";
			

			//xdebug("query_trades",$query_trades);
			//$passtoexcel = $query_trades;
			
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
												FROM tmp_mry_cmpl_trades a, sls_sales_reps b
												WHERE a.trad_rr = b.srep_rrnum 
												AND b.srep_user_id = '".$rep_id."'
												AND trad_is_cancelled = 0 
												AND trad_trade_date between '".$datefrom."' AND '".$dateto."'"
												. $qry_string_symbol . $qry_string_client .
												" GROUP BY trad_advisor_code, trad_symbol, trad_buy_sell, trad_trade_date 
												ORDER BY trad_advisor_name, trad_symbol, trad_buy_sell, trad_trade_date";	
			
		  $passtoexcel = md5(rand(100,999)).'^'.$rep_id.'^'.$datefrom.'^'.$dateto.'^'.$qry_string_symbol.'^'.$qry_string_client.'^'.$qry_string_rep;
			
		$data_to_html_file = "";


		$data_to_html_file .= '
			<style type="text/css">
			<!--
			.data_black {font-family: "Courier New", Courier, mono;	font-size: 10px;	color: #000000;}
			-->
			</style>
			<table width="670" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td valign="top" width="50"><img src="../../images/logo_small.gif" width="47"></td>
					<td valign="top" align="left" width="200">
						<font color="#333333" size="3" face="Arial, Helvetica, sans-serif"><b>&nbsp;Daily Compliance Report</b></font>
						<br>
						<font color="#333333" size="2" face="Arial, Helvetica, sans-serif">
							&nbsp;Trade Date: '.format_date_ymd_to_mdy($trade_date_to_process).'
						</font>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					</td>
				</tr>
			</table>
			<img src="../../images/border_a.png" width="670" height="5">
			<table width="670" border="1" cellspacing="0" cellpadding="1">
				<tr>
				  <td width="200"><font color="#000000" size="2" face="Courier"><strong>Account</strong></font></td>
					<td width="40"><font color="#000000" size="2" face="Courier"><strong>RR #</strong></font></td>
					<td width="55"><font color="#000000" size="2" face="Courier"><strong>Symbol</strong></font></td>
					<td width="50"><font color="#000000" size="2" face="Courier"><strong>B/S</strong></font></td>
					<td width="75"><font color="#000000" size="2" face="Courier"><strong>Shares</strong></font></td>
					<td width="60"><font color="#000000" size="2" face="Courier"><strong>Price</strong></font></td>
					<td width="40"><font color="#000000" size="2" face="Courier"><strong>Comm.</strong></font></td>
					<td width="30"><font color="#000000" size="2" face="Courier"><strong>$/Shr.</strong></font></td>
					<td width="120">&nbsp;</td><!-- width="140" -->
				</tr>';
		
			$result_trades = mysql_query($query_trades) or die(tdw_mysql_error($query_trades));
			$result_shared_rep_trades = mysql_query($query_shared_rep_trades) or die(tdw_mysql_error($query_shared_rep_trades));
			
			$count_row_trades = 0;
			$running_trad_commission_total = 0;
			while($row_trades = mysql_fetch_array($result_trades))
			{
				
				if ($row_trades["trad_advisor_name"] == '') {
					$show_trad_advisor_name = $row_trades["trad_advisor_code"];
				} else {
					$show_trad_advisor_name = $row_trades["trad_advisor_name"];
				}
				
				$running_trad_commission_total = $running_trad_commission_total + $row_trades["for_sum_trad_commission"];

				$data_to_html_file .=	'
									 <tr>
											<td align="left"><font color="#000000" size="2" face="Courier">'.$show_trad_advisor_name.'</font></td>
											<td><font color="#000000" size="2" face="Courier">'.$row_trades["trad_rr"].'</font></td>
											<td><font color="#000000" size="2" face="Courier">'.$row_trades["trad_symbol"].'</font></td>
											<td><font color="#000000" size="2" face="Courier">'.offset_buy_sell($row_trades["trad_buy_sell"]).'</font></td>
											<td align="right"><font color="#000000" size="2" face="Courier">'.$row_trades["trad_quantity"].'</font></td>
											<td align="right"><font color="#000000" size="2" face="Courier">'.$row_trades["trade_price"].'</font></td>
											<td align="right"><font color="#000000" size="2" face="Courier">'.$row_trades["trad_commission"].'</font></td>
											<td align="right"><font color="#000000" size="2" face="Courier">'.$row_trades["trad_cents_per_share"].'</font></td>
					 						<td>&nbsp;</td>
										</tr>';

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
							
								$data_to_html_file .=	'
										<tr>
											<td align="left"><font color="#000000" size="2" face="Courier">'.$show_trad_advisor_name.'</font></td>
											<td><font color="#000000" size="2" face="Courier">'.$row_shared_rep_trades["trad_rr"].'</font></td>
											<td><font color="#000000" size="2" face="Courier">'.$row_shared_rep_trades["trad_symbol"].'</font></td>
											<td><font color="#000000" size="2" face="Courier">'.offset_buy_sell($row_shared_rep_trades["trad_buy_sell"]).'</font></td>
											<td align="right"><font color="#000000" size="2" face="Courier">'.$row_shared_rep_trades["trad_quantity"].'</font></td>
											<td align="right"><font color="#000000" size="2" face="Courier">'.$row_shared_rep_trades["trade_price"].'</font></td>
											<td align="right"><font color="#000000" size="2" face="Courier">'.$row_shared_rep_trades["trad_commission"].'</font></td>
											<td align="right"><font color="#000000" size="2" face="Courier">'.$row_shared_rep_trades["trad_cents_per_share"].'</font></td>
					 						<td>&nbsp;</td>
										</tr>';
							
								$count_row_trades = $count_row_trades + 1;
							}


				$data_to_html_file .=	'
					<tr bgcolor="#CCCCCC" class="display_totals">
						<td><div align="left">&nbsp;&nbsp;TOTALS:</div></td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">'.number_format($running_trad_commission_total,2,'.',',').'&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</table>';

	//$file_name = $trade_date_to_process."_dly_a_".substr(md5(rand(1000,9999)), 0, 8).".html";     
	$file_name = $trade_date_to_process."_dly_a.html";     
	$file_pdf_name = $trade_date_to_process."_dly_a.pdf";     
	$fp = fopen ($export_compliance.$file_name, "w");  
	fwrite ($fp,$data_to_html_file);        
	fclose ($fp); 

$cmd_pdf = "d:\\tdw\\tdw\\includes\\createpdf.bat ". $file_pdf_name. " " . $file_name;
echo $cmd_pdf."<br>";
shell_exec($cmd_pdf);
  

?>
