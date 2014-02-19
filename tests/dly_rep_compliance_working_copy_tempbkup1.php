<?
  include('includes/dbconnect.php');
  include('includes/global.php');
	include('includes/functions.php');

	//Previous Business Day should be applied here.
	$trade_date_to_process = previous_business_day();
	//$trade_date_to_process = '2006-11-27';
	
	////
	// Function to process Buy/Sell/Cover/Short
	function process_bscs ($bscs) {
		if (trim($bscs) == 'Buy') {
		return 'B';
		} elseif (trim($bscs) == 'Sell') {
		return 'S';
		} elseif (trim($bscs) == 'Cover') {
		return 'C';
		} elseif (trim($bscs) == 'Short') {
		return 'SS';
		} else {
		return '?';
		}
	}
	//keep BCM Trades in an array
	$qry_oth_trades = "SELECT auto_id,
													oth_trade_date,
													oth_broker,
													oth_buysell,
													oth_symbol,
													FORMAT(oth_quantity,0) as oth_quantity,
													oth_price,
													oth_commission,
													FORMAT((oth_commission/oth_quantity)*100,1) as centspershare,
													oth_net_money,
													oth_trade_time,
													oth_isactive
										FROM oth_other_trades
										WHERE oth_trade_date = '".$trade_date_to_process."'
										AND trim(oth_broker) != 'BUCK'
										ORDER BY oth_symbol";
	//xdebug("qry_acct_emp",$qry_acct_emp);

	$result_oth_trades = mysql_query($qry_oth_trades) or die (tdw_mysql_error($qry_oth_trades));
	$arr_oth_trades_to_merge = array();
	$arr_oth_processed = array(); // This is used further down.
	$maint_count = 0;
	while ( $row_oth_trades = mysql_fetch_array($result_oth_trades) ) 
	{
 		$arr_oth_trades_to_merge[$maint_count] =      trim($row_oth_trades["oth_symbol"])."^".
																									'BUCK CAPITAL MGMT'."^".
																									'  '."^".
																									process_bscs($row_oth_trades["oth_buysell"])."^".
																									$row_oth_trades["oth_quantity"]."^".
																									$row_oth_trades["oth_price"]."^".
																									'non-brg'."^".
																									$row_oth_trades["centspershare"]."^".
																									'code'."^".
																									'0';
		$maint_count = $maint_count + 1;
	}	

	//keep Outside Trades (Employee) in an array
	$qry_oemp_trades = "SELECT 
												a.auto_id,
												a.otd_account_id,
												a.otd_trade_date,
												a.otd_buysell,
												a.otd_symbol,
												a.otd_quantity,
												a.otd_price,
												a.otd_entered_by,
												a.otd_last_edited_by,
												a.otd_last_edited_on,
												a.otd_isactive, 
												c.Fullname
											FROM otd_emp_trades_external a, oac_emp_accounts b, users c
											WHERE a.otd_account_id = b.auto_id
											AND b.oac_emp_userid = c.ID
											AND a.otd_trade_date = '".$trade_date_to_process."'
											AND a.otd_isactive =1";
	
	//xdebug("qry_oemp_trades",$qry_oemp_trades);
	$result_oemp_trades = mysql_query($qry_oemp_trades) or die (tdw_mysql_error($qry_oemp_trades));
	$arr_oemp_trades_to_merge = array();
	$maint_count_oemp = 9999;
	while ( $row_oemp_trades = mysql_fetch_array($result_oemp_trades) ) 
	{
 		$arr_oemp_trades_to_merge[$maint_count_oemp] = trim($row_oemp_trades["otd_symbol"])."^".
																									$row_oemp_trades["Fullname"]."^".
																									'  '."^".
																									$row_oemp_trades["otd_buysell"]."^".
																									$row_oemp_trades["otd_quantity"]."^".
																									$row_oemp_trades["otd_price"]."^".
																									'0.00'."^".
																									'0.0'."^".
																									'#outside_employee_trade#'."^".
																									'0';
		$maint_count_oemp = $maint_count_oemp + 1;
	}	
	
	//show_array($arr_oth_trades_to_merge);
	//show_array($arr_oth_trades);
	//show_array($arr_oth_trades_symbol);

	//$date_match_val = date("M j Y",strtotime('2006-08-02'));
	$date_match_val = date("j-M",strtotime($trade_date_to_process));
	$date_to_show = date("m/d/Y",strtotime($trade_date_to_process));
	
	//xdebug("date_match_val",$date_match_val);

	//PDS Accounts to include
		$arr_pds = array();
		$arr_pds[0] = 'PDS000086';
		$arr_pds[1] = 'PDS000094';
		$arr_pds[2] = 'PDS000108';
		$arr_pds[3] = 'PDS000124';
		$arr_pds[4] = 'PDS000175';

?>
<?
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// BEGIN JOVUS SECTION
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
# SQL Server Connection Information
$msconnect=mssql_connect("192.168.20.48","BUCKINGHAM_login","BUCKINGHAM_pw");
$msdb=mssql_select_db("BUCKINGHAM",$msconnect);


	xdebug('Connecting to Jovus Server @ Buckingham','Successful');
  //Most recent research date from Jovus
	 
		$arr_rres = array();
		$arr_rres_symbols = array();

		$ms_qry_rres   = 	"SELECT dbo.Prod_Issuers.IssuerID, dbo.Issuers.CUSIP, Max(dbo.Prod_Statuses.DateTime) AS MaxOfDateTime
											FROM ((dbo.Prod_Issuers INNER JOIN dbo.Products ON dbo.Prod_Issuers.ProductID = dbo.Products.ProductID) 
											INNER JOIN dbo.Issuers ON dbo.Prod_Issuers.IssuerID = dbo.Issuers.IssuerID) INNER JOIN dbo.Prod_Statuses ON 
											dbo.Products.ProductID = dbo.Prod_Statuses.ProductID
											WHERE dbo.Issuers.CUSIP <> ''
											GROUP BY dbo.Prod_Issuers.IssuerID, dbo.Issuers.CUSIP, dbo.Prod_Statuses.StatusTypeID
											HAVING (((dbo.Prod_Statuses.StatusTypeID)=3))
											order by dbo.Issuers.CUSIP;
											";



		//xdebug("ms_qry_rres",$ms_qry_rres);
		$ms_results_rres = mssql_query($ms_qry_rres);
		
		$v_count_rres = 0;
		while ($row_rres = mssql_fetch_array($ms_results_rres)) {
					
					$symbol = $row_rres[1];
					$rres_date = $row_rres[2];
					$arr_rres_symbols[$symbol] = $rres_date;

		}

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  //Most recent MRI Date
	 
		$arr_mri = array();
		$arr_mri_symbols = array();

		$ms_qry_mri = 	"SELECT 
										dbo.Issuers.CUSIP, 
										dbo.Products.CreationDateTime, 
										dbo.Prod_Issuers.IssuerID, 
										dbo.Prod_Issuers.Recommendation, 
										dbo.Prod_Issuers.PreviousRecommendation, 
										dbo.Prod_Issuers.RecommendationAction, 
										dbo.Prod_Issuers.TargetPrice
										FROM (dbo.Prod_Issuers INNER JOIN dbo.Products ON dbo.Prod_Issuers.ProductID = dbo.Products.ProductID) 
										INNER JOIN dbo.Issuers ON dbo.Prod_Issuers.IssuerID = dbo.Issuers.IssuerID
										WHERE (((dbo.Issuers.CUSIP)<> '') AND ((dbo.Products.CreationDateTime)> GETDATE()-180))
										ORDER BY dbo.Issuers.CUSIP, dbo.Products.CreationDateTime DESC;";
 									 //AND CAST(FLOOR(CAST(dbo.Prod_Statuses.DateTime AS float)) AS datetime) = '".$trade_date_to_process."'

		//xdebug("ms_qry_mri",$ms_qry_mri);
		$ms_results_mri = mssql_query($ms_qry_mri);
		
		$v_count_mri = 0;
		while ($row_mri = mssql_fetch_array($ms_results_mri)) {
					
					//show_array($row_mri);
					$symbol = $row_mri[0];
					$mri_date = $row_mri[1];
					$rating = $row_mri[3];
					$rating_change = $row_mri[5]; 
					$target = $row_mri[6];

					if ($rating_change == "Increase") {
					  $img_show = '<img src="images/themes/standard/arrow_up.gif" border="0">';
						$arr_mri[$v_count_mri] = $symbol."<###>".$mri_date."<###>".$rating."<###>".$rating_change."<###>".$img_show."<###>".$target;
						$arr_mri_symbols[$v_count_mri] = $symbol;
						$v_count_mri = $v_count_mri + 1;
					} elseif ($rating_change == "Decrease"){
					  $img_show = '<img src="images/themes/standard/arrow_down.gif" border="0">';
						$arr_mri[$v_count_mri] = $symbol."<###>".$mri_date."<###>".$rating."<###>".$rating_change."<###>".$img_show."<###>".$target;
						$arr_mri_symbols[$v_count_mri] = $symbol;
						$v_count_mri = $v_count_mri + 1;
					} else {
					  $img_show = '';
						$arr_mri[$v_count_mri] = $symbol."<###>".$mri_date."<###>".$rating."<###>".$rating_change."<###>".$img_show."<###>".$target;
						$arr_mri_symbols[$v_count_mri] = $symbol;
						$v_count_mri = $v_count_mri + 1;
					}

		}
   //show_array($arr_mri);
	 //Array of relevant MRI data
	 $arr_recent_mri = array();
	 
	 
   //show_array($arr_mri_symbols);
	 foreach($arr_mri as $key=>$value) {
           if ($key == 0) {
						 $arr_data = explode("<###>",$value);
						 $str_symbol_old = $arr_data[0];
						 $str_date_old = $arr_data[1];
						 $str_rating_old = $arr_data[2];
						 $str_compare_old = $arr_data[2].$arr_data[5];
						 xdebug("First Row",'');
					 } else {
					 	 //xdebug("Row Number",$key);	
						 $arr_data = explode("<###>",$value);
						 $str_symbol_new = $arr_data[0];
						 $str_date_new = $arr_data[1];
						 $str_rating_new = $arr_data[2];
						 $str_compare_new = $arr_data[2].$arr_data[5];
						 //show_array($arr_data);
						 //Compare with old and then proceed
						 if ($str_symbol_new == $str_symbol_old) { //within the same ticker
						 		if($str_compare_new != $str_compare_old && $str_rating_new != '' && $str_rating_old != '' && $ignore != $str_symbol_old) { //compare new to old 
										if ($ignore == $str_symbol_old) {
												//do nothing for this symbol anymore
												$ignore = $str_symbol_new;
										} else {
												//xdebug("strings", $str_compare_new."||".$str_compare_old);
												
												//echo "marked => ". $str_symbol_old . " on " . $str_date_old . "<br>";
												
												//CAPTURE THE VALUE IN AN ARRAY
												$arr_recent_mri[$str_symbol_old] = $str_date_old;
												$ignore = $str_symbol_new;
										}
						    } else {
										if ($ignore == $str_symbol_old) {
												$ignore = $str_symbol_new;
										} else {
												$ignore = "";
										}
									//echo "nothing 1: " . $str_symbol_old . " on " . $str_date_old . "<br>";
									//xdebug("strings compare", $str_compare_new."||".$str_compare_old);
									//xdebug("strings rating", $str_rating_new."||".$str_rating_old);
									// do nothing and proceed
								}
								
								if ($str_rating_new == '')	{
								 //don't set old values
								 $str_symbol_old = $str_symbol_new;
								} else {
								 //set old values
								 $str_symbol_old = $str_symbol_new;
								 $str_date_old = $str_date_new;
								 $str_rating_old = $str_rating_new;
								 $str_compare_old = $str_compare_new;
								}
							
							} else {
									// do nothing and proceed
									//xdebug("symbols", $str_symbol_new."||".$str_symbol_old);
									//echo "nothing 2: " . $str_symbol_new . " on " . $str_date_new . "<br>";
								  //set old values
								 $str_symbol_old = $str_symbol_new;
								 $str_date_old = $str_date_new;
								 $str_rating_old = $str_rating_new;
								 $str_compare_old = $str_compare_new;
							}
						 
						 }

       }
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++



//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// END JOVUS SECTION
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

//Create Lookup Array of Client Code / Client Name
$qry_clients = "select * from int_clnt_clients";
$result_clients = mysql_query($qry_clients) or die (tdw_mysql_error($qry_clients));
$arr_clients = array();
while ( $row_clients = mysql_fetch_array($result_clients) ) 
{
	$arr_clients[$row_clients["clnt_code"]] = $row_clients["clnt_name"];
}
//show_array($arr_clients);

//Create an array of account names and advisor code for lookup.
$qry_acct_adv = "select nadd_full_account_number, nadd_advisor from mry_nfs_nadd";
$result_acct_adv = mysql_query($qry_acct_adv) or die (tdw_mysql_error($qry_acct_adv));
$arr_acct_adv = array();
while ( $row_acct_adv = mysql_fetch_array($result_acct_adv) ) 
{
	$arr_acct_adv[strtoupper(trim($row_acct_adv["nadd_full_account_number"]))] = $row_acct_adv["nadd_advisor"];
}

//Create an array of employee account names
$qry_acct_emp = "SELECT distinct(nadd_advisor) as nadd_advisor FROM mry_nfs_nadd WHERE nadd_branch like '%PDZ%' and nadd_advisor not like '%&' order by nadd_advisor";
$result_acct_emp = mysql_query($qry_acct_emp) or die (tdw_mysql_error($qry_acct_emp));
$arr_acct_emp = array();
while ( $row_acct_emp = mysql_fetch_array($result_acct_emp) ) 
{
	$arr_acct_emp[trim($row_acct_emp["nadd_advisor"])] = trim($row_acct_emp["nadd_advisor"]);
}
//show_array($arr_acct_emp);


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
	  						//xdebug ("query_trades",$query_trades);
  							$result_trades = mysql_query($query_trades) or die(tdw_mysql_error($query_trades));
								$countval = 1;
								while($row_trades = mysql_fetch_array($result_trades))
								{
									//get data to insert into temp table to process further
									$trad_branch                 =  trim($row_trades["trad_branch"]); 
									$comm_trade_reference_number = 	trim($row_trades["trad_trade_reference_number"]);
									$trad_full_account_number = 		trim($row_trades["trad_full_account_number"]);
									$trad_short_name = 							str_replace("'","",trim($row_trades["trad_short_name"]));
									$comm_rr = 											trim($row_trades["trad_registered_rep"]);
									$comm_trade_date = 							$row_trades["trad_trade_date"];
									$comm_run_date = 							  $row_trades["trad_run_date"];
									$comm_advisor_code = 						$arr_acct_adv[strtoupper(trim($row_trades["trad_full_account_number"]))];
									if (substr(trim($row_trades["trad_full_account_number"]),0,3)=='PDZ') {
									$comm_advisor_name = "";
									} else {
									$comm_advisor_name = 						str_replace("'","\'",$arr_clients[substr($row_trades["trad_short_name"],0,4)]);
									}
									$comm_account_name = 						str_replace("'","",get_account_name($row_trades["trad_full_account_number"])); //stupid single quote
									$comm_account_number = 					trim($row_trades["trad_full_account_number"]);
									$comm_symbol = 									trim($row_trades["trad_symbol"]);
									$trad_sec_desc_1 =              trim($row_trades["trad_sec_desc_1"]);
									$comm_buy_sell = 								trim($row_trades["trad_buy_sell"]);
									$comm_quantity = 								round($row_trades["trad_quantity"],0);
									$comm_price = 									$row_trades["trad_price"];
									$comm_commission_code = 				$row_trades["trad_commission_concession_code"];
									$comm_commission = 							$row_trades["trad_trade_commission"];
									
									if ($row_trades["trad_commission_concession_code"] == 3) { //This indicates cents/share
										$comm_cents_per_share = $row_trades["trad_trade_commission"]/$row_trades["trad_quantity"];
									} else {
										$comm_cents_per_share = 0;
									}
									
									if ($comm_symbol == '') {
									$comm_symbol = $trad_sec_desc_1;
									}
							
									//Excluding trades (PDS) not in the list (Lloyd Karp)
									if ($trad_branch == 'PDS' && !in_array($comm_account_number, $arr_pds)) {
									//echo "Not processed =".$comm_account_number."<br>";
									} else {
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
												$query_insert_trade = "INSERT INTO tmp_mry_cmpl_trades 
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
												$result_insert_trade = mysql_query($query_insert_trade) or die(tdw_mysql_error($query_insert_trade));
																								
											}
								}

			$datefrom = $trade_date_to_process;
			$dateto = $trade_date_to_process;

			/*
			CREATE AN ARRAY OF TRADES TO LATER MERGE TO ONE
			*/
			//===========================================================================================================
			$maint_count_nfs = 9999999;
			$arr_nfs_trades_to_merge = array();
			$query_nfs_trades = "SELECT 
													trad_advisor_code,
													trad_symbol,
													trad_buy_sell,
													DATE_FORMAT(trad_trade_date,'%m/%d/%Y') as trad_trade_date,
													max(trad_advisor_name) as trad_advisor_name,
													FORMAT(sum(trad_quantity),0) as trad_quantity,
													FORMAT(max(trade_price),2) as trade_price,
													FORMAT(sum(trad_commission),2) as trad_commission,
													sum(trad_commission) as for_sum_trad_commission,
													FORMAT(avg(trad_cents_per_share)*100,1) as trad_cents_per_share,
													max(trad_rr) as trad_rr 
												FROM tmp_mry_cmpl_trades 
												WHERE trad_is_cancelled = 0 
												AND trad_trade_date between '".$datefrom."' AND '".$dateto."'".
												" GROUP BY trad_advisor_code, trad_symbol, trad_buy_sell, trad_trade_date 
												ORDER BY trad_symbol, trad_advisor_name, trad_buy_sell, trad_trade_date";
			
		   xdebug("query_nfs_trades",$query_nfs_trades);
	     $result_nfs_trades = mysql_query($query_nfs_trades) or die (tdw_mysql_error($query_nfs_trades));
				while ( $row_nfs_trades = mysql_fetch_array($result_nfs_trades)) 
				{
					$arr_nfs_trades_to_merge[$maint_count_nfs] =  trim($row_nfs_trades["trad_symbol"])."^". 	//0
																												$row_nfs_trades["trad_advisor_name"]."^". 	//1
																												$row_nfs_trades["trad_rr"]."^".           	//2
																												$row_nfs_trades["trad_buy_sell"]."^".     	//3
																												$row_nfs_trades["trad_quantity"]."^".     	//4
																												$row_nfs_trades["trade_price"]."^".       	//5
																												$row_nfs_trades["trad_commission"]."^".   	//6
																												$row_nfs_trades["trad_cents_per_share"]."^".//7
																												$row_nfs_trades["trad_advisor_code"]."^". 	//8
																												$row_nfs_trades["for_sum_trad_commission"]; //9
					$maint_count_nfs = $maint_count_nfs + 1;
				}	
	
	
	
	     //show_array($arr_nfs_trades_to_merge);



       //combine two arrays (this creates a new index)
			 $merged_array_trades = array();
			 $merged_array_trades = array_merge($arr_oth_trades_to_merge, $arr_oemp_trades_to_merge, $arr_nfs_trades_to_merge);
			 $sorted_merged_array_trades = array();
			 $count_sorted = 0;
			 //sort the array
			 asort($merged_array_trades);
			 //xdebug("MERGED TRADES",$dummy);
			 
	     //show_array($merged_array_trades);
			 foreach ($merged_array_trades as $key => $val) {
					 $sorted_merged_array_trades[$count_sorted] = $val;
					 $count_sorted = $count_sorted + 1;
			 }
			 
	     show_array($sorted_merged_array_trades);

	     //exit;
			//===========================================================================================================
			
		  //xdebug("query_trades",$query_trades);
	
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
					<td valign="top" align="left" width="400">
						<font color="#333333" size="3" face="Arial, Helvetica, sans-serif"><b>&nbsp;Daily Compliance Activity Report</b></font>
						<br>
						<font color="#333333" size="2" face="Arial, Helvetica, sans-serif">
							&nbsp;Trade Date: '.format_date_ymd_to_mdy($trade_date_to_process).'
						</font>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					</td>
				</tr>
			</table>
			<img src="../../images/border_a.png" width="670" height="5">
			<table width="670" border="0" cellspacing="0" cellpadding="1">
				<tr>
				  <td>
					<font color="#000000" size="2" face="Courier"><strong>
					'.str_replace(" ","&nbsp;",str_pad(" ",4," ",1)).
					  str_replace(" ","&nbsp;",str_pad("Account",26," ",1)).
					  str_replace(" ","&nbsp;",str_pad("RR #",8," ",1)).
					  str_replace(" ","&nbsp;",str_pad("Symbol",10," ",1)).
					  str_replace(" ","&nbsp;",str_pad("B/S",6," ",1)).
					  str_replace(" ","&nbsp;",str_pad("Shares",10," ",0)).
					  str_replace(" ","&nbsp;",str_pad("Price",8," ",0)).
					  str_replace(" ","&nbsp;",str_pad(" MRI ",14," ",0)).
					  str_replace(" ","&nbsp;",str_pad("Research",10," ",0)).
					  str_replace(" ","&nbsp;",str_pad("Comm.",14," ",0)).
					  str_replace(" ","&nbsp;",str_pad("&cent;/Shr.",6," ",0)).
					'</strong></font></td>
				</tr>';

			$heading_row = '				
				<tr>
					<td valign="bottom"><img src="../../images/border_a.png" width="670" height="5"></td>
				<tr>
				  <td>
					<font color="#000000" size="2" face="Courier"><strong>
					'.str_replace(" ","&nbsp;",str_pad(" ",4," ",1)).
					  str_replace(" ","&nbsp;",str_pad("Account",26," ",1)).
					  str_replace(" ","&nbsp;",str_pad("RR #",8," ",1)).
					  str_replace(" ","&nbsp;",str_pad("Symbol",10," ",1)).
					  str_replace(" ","&nbsp;",str_pad("B/S",6," ",1)).
					  str_replace(" ","&nbsp;",str_pad("Shares",10," ",0)).
					  str_replace(" ","&nbsp;",str_pad("Price",8," ",0)).
					  str_replace(" ","&nbsp;",str_pad(" MRI ",14," ",0)).
					  str_replace(" ","&nbsp;",str_pad("Research",10," ",0)).
					  str_replace(" ","&nbsp;",str_pad("Comm.",14," ",0)).
					  str_replace(" ","&nbsp;",str_pad("&cent;/Shr.",6," ",0)).
					'</strong></font></td>
				</tr>';

		
			//$result_trades = mysql_query($query_trades) or die(tdw_mysql_error($query_trades));
			$count_row_trades = 0;
			$count_page = 1;
			$running_trad_commission_total = 0;
			$hold_symbol = "";

			foreach ($sorted_merged_array_trades as $key => $val) {

 			 $rec_to_process = explode("^",$val);
				
				if ($rec_to_process[1] == '') {
						$show_trad_advisor_name = $rec_to_process[8];
				} else {
						$show_trad_advisor_name = $rec_to_process[1];
				}
				
				$running_trad_commission_total = $running_trad_commission_total + $rec_to_process[9];
				
				if ($arr_recent_mri[$rec_to_process[0]] == '') {
						$show_mri = 'n/a';
				} else {
						$arr_dateval = explode(' ',$arr_recent_mri[$rec_to_process[0]]);
						$show_mri = $arr_dateval[1]."-".$arr_dateval[0];
				}
								
				if ($arr_rres_symbols[$rec_to_process[0]] == '') {
						$show_rres = 'n/a';
				} else {
						$arr_dateval = explode(' ',$arr_rres_symbols[$rec_to_process[0]]);
						$show_rres = $arr_dateval[1]."-".$arr_dateval[0];
				}
								
//
				if ($show_mri == $date_match_val or $show_rres == $date_match_val) {
						$rowcolor = "ff0000";
						$rowstrongstart = "<strong>";
						$rowstrongend = "</strong>";
				} else {
						if (
						substr($rec_to_process[2],0,2) != '09' && $rec_to_process[6] == '0.0' && in_array(trim($show_trad_advisor_name),$arr_acct_emp)) {
							$rowcolor = "0000ff";
							$rowstrongstart = "<strong>";
							$rowstrongend = "</strong>";
						} elseif (substr($rec_to_process[2],0,2) == '09') { 
							$rowcolor = "0000ff";
							$rowstrongstart = "<strong>";
							$rowstrongend = "</strong>";
						} elseif (trim($show_trad_advisor_name) == 'BUCK CAPITAL MGMT') {
							$rowcolor = "0000ff";
							$rowstrongstart = "<strong>";
							$rowstrongend = "</strong>";
						} elseif ($rec_to_process[8] == '#outside_employee_trade#') {
							$rowcolor = "0000ff";
							$rowstrongstart = "<strong>";
							$rowstrongend = "</strong>";
						}  else {
							$rowcolor = "000000";
							$rowstrongstart = "";
							$rowstrongend = "";
						}
				}
				
				//section for type
				if ($rec_to_process[6] == 'non-brg') {
						$show_type = '�';
				} elseif ($rec_to_process[8] == '#outside_employee_trade#') {
						$show_type = '�';
				} else {
						$show_type = ' ';
				}


//				
				if ($count_page == 1) {
						if ($count_row_trades != 0 && $count_row_trades % 2 == 0) {
							if ($count_row_trades % 28 == 0) {
								$data_to_html_file .=  '<!-- NEW PAGE -->';
								$data_to_html_file .= $heading_row;
								echo "page break added >> ".$count_row_trades."<br>";
						      $count_page = 2;
								$count_row_trades = 1;
							}
						} elseif ($count_row_trades != 0 && $count_row_trades % 2 != 0) {
								if ($count_row_trades % 29 == 0) {
									$data_to_html_file .=  '<!-- NEW PAGE -->';
									$data_to_html_file .= $heading_row;
									echo "page break added >> ".$count_row_trades."<br>";
						      $count_page = 2;
									$count_row_trades = 1;
								}
						} else {
						//do nothing
						}
				} else {
						if ($count_row_trades != 0 && $count_row_trades % 2 == 0) {
							if ($count_row_trades != 0 && $count_row_trades % 32 == 0) {
							$data_to_html_file .=  '<!-- NEW PAGE -->';
							$data_to_html_file .= $heading_row;
							echo "page break added<br>";
							$count_row_trades = 1;
							}
						} elseif ($count_row_trades != 0 && $count_row_trades % 2 != 0) {
							if ($count_row_trades != 0 && $count_row_trades % 31 == 0) {
							$data_to_html_file .=  '<!-- NEW PAGE -->';
							$data_to_html_file .= $heading_row;
							echo "page break added<br>";
							$count_row_trades = 1;
							}
						} else {
						//do nothing
						}
				}
				
				if (trim($hold_symbol) == trim($rec_to_process[0])) {
				//�  �
				
						$data_to_html_file .= '
											 <tr>
													<td><font color="#'.$rowcolor.'" size="2" face="Courier">'.$rowstrongstart.
													str_replace(" ","&nbsp;",str_pad($show_type,4," ",1)).
													str_replace(" ","&nbsp;",str_pad($show_trad_advisor_name,26," ",1)).
													str_replace(" ","&nbsp;",str_pad($rec_to_process[2],8," ",1)).
													str_replace(" ","&nbsp;",str_pad($rec_to_process[0],10," ",1)).
													str_replace(" ","&nbsp;",str_pad($rec_to_process[3],5," ",1)).
													str_replace(" ","&nbsp;",str_pad($rec_to_process[4],10," ",0)).
													str_replace(" ","&nbsp;",str_pad($rec_to_process[5],8," ",0)).
													str_replace(" ","&nbsp;",str_pad($show_mri,14," ",0)).
													str_replace(" ","&nbsp;",str_pad($show_rres,10," ",0)).
													str_replace(" ","&nbsp;",str_pad($rec_to_process[6],14," ",0)).
													str_replace(" ","&nbsp;",str_pad($rec_to_process[7],6," ",0)).
													$rowstrongend.'</font></td>
												</tr>';
												$count_row_trades = $count_row_trades + 1;
												
				} else {
						$data_to_html_file .= '
											 <tr>
													<td><font color="#000000" size="2" face="Courier">&nbsp;</font></td>
												</tr>';
						$data_to_html_file .= '
											 <tr>
													<td><font color="#'.$rowcolor.'" size="2" face="Courier">'.$rowstrongstart.
													str_replace(" ","&nbsp;",str_pad($show_type,4," ",1)).
													str_replace(" ","&nbsp;",str_pad($show_trad_advisor_name,26," ",1)).
													str_replace(" ","&nbsp;",str_pad($rec_to_process[2],8," ",1)).
													str_replace(" ","&nbsp;",str_pad($rec_to_process[0],10," ",1)).
													str_replace(" ","&nbsp;",str_pad($rec_to_process[3],5," ",1)).
													str_replace(" ","&nbsp;",str_pad($rec_to_process[4],10," ",0)).
													str_replace(" ","&nbsp;",str_pad($rec_to_process[5],8," ",0)).
													str_replace(" ","&nbsp;",str_pad($show_mri,14," ",0)).
													str_replace(" ","&nbsp;",str_pad($show_rres,10," ",0)).
													str_replace(" ","&nbsp;",str_pad($rec_to_process[6],14," ",0)).
													str_replace(" ","&nbsp;",str_pad($rec_to_process[7],6," ",0)).
													$rowstrongend.'</font></td>
												</tr>';
																
												$count_row_trades = $count_row_trades + 2;
						

				}
				
				$hold_symbol = $rec_to_process[0];
			}
			
				if ($count_row_trades > 28) {
							$data_to_html_file .=  '<!-- NEW PAGE -->';
				}
				$data_to_html_file .=	'
												<tr>
													<td valign="top"><hr></td>
												<tr>
												<tr>
													<td><font color="#000000" size="3" face="Courier"><strong>'.
													str_replace(" ","&nbsp;",str_pad("TOTAL (Trade Date):",10," ",1)).
													str_replace(" ","&nbsp;",str_pad(number_format($running_trad_commission_total,2,'.',','),70," ",0)).
													'</strong></font></td>
												</tr>
												<tr>
													<td valign="bottom"><img src="../../images/border_a.png" width="670" height="5"></td>
												<tr>
											</table>
											';
				$store_running_trad_commission_total = $running_trad_commission_total;


			 
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// NEW SECTION (WITH PAGE BREAK)
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++			 
				$data_to_html_file .=  '<!-- NEW PAGE -->';

							//FLUSH temp tables
							$result_flush = mysql_query("truncate table tmp_mry_cmpl_temp") or die (mysql_error());
							$result_flush = mysql_query("truncate table tmp_mry_cmpl_trades") or die (mysql_error());
							echo "tmp_mry_cmpl_temp and tmp_mry_cmpl_trades are flushed and ready for the next set of data<br>";

							//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++						
						  //get only regular trades, not the cancelled trades, the cancelled trades will be 
							//processed in a separate section at the end of segment	
								$query_trades = "SELECT * 
																 FROM nfs_trades
																 WHERE trad_run_date = '".$trade_date_to_process."'";
																 //AND trad_cancel_code != '1'"; //AND trad_branch = 'PDY'
	  						//xdebug ("query_trades",$query_trades);
  							$result_trades = mysql_query($query_trades) or die(tdw_mysql_error($query_trades));
								$countval = 1;
								while($row_trades = mysql_fetch_array($result_trades))
								{
									//get data to insert into temp table to process further
									$trad_branch                 =  trim($row_trades["trad_branch"]); 
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
									$comm_quantity = 								round($row_trades["trad_quantity"],0);
									$comm_price = 									$row_trades["trad_price"];
									$comm_commission_code = 				$row_trades["trad_commission_concession_code"];
									$comm_commission = 							$row_trades["trad_trade_commission"];
									if ($row_trades["trad_cancel_code"] == '') {
									$comm_cancel_code = '0';
									} else {
									$comm_cancel_code = '1';
									}
									$comm_correction_code = 				$row_trades["trad_correction_code"];
									
									if ($row_trades["trad_commission_concession_code"] == 3) { //This indicates cents/share
										$comm_cents_per_share = $row_trades["trad_trade_commission"]/$row_trades["trad_quantity"];
									} else {
										$comm_cents_per_share = 0;
									}
							
									//Excluding trades (PDS) not in the list (Lloyd Karp)
									if ($trad_branch == 'PDS' && !in_array($comm_account_number, $arr_pds)) {
									//echo "Not processed =".$comm_account_number."<br>";
									} else {
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
																			comm_cents_per_share,
																			comm_cancel_code,
																			comm_correction_code)
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
																			"'".$comm_cents_per_share."',".
																			"'".$comm_cancel_code."',".
																			"'".$comm_correction_code."')";
																			
									$result_insert_trade = mysql_query($qry_insert_trade) or die(tdw_mysql_error($qry_insert_trade));
									$countval = $countval + 1;
									}
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
																								trad_cents_per_share,
																								trad_is_cancelled,
																								trad_correction_code
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
																								"'".$row_comm_trd["comm_cents_per_share"]."',".
																								"'".$row_comm_trd["comm_cancel_code"]."',".
																								"'".$row_comm_trd["comm_correction_code"]."')";
												$result_insert_trade = mysql_query($qyery_insert_trade) or die(tdw_mysql_error($qyery_insert_trade));
																								
											}
								}


			$sel_datefrom = format_date_ymd_to_mdy($trade_date_to_process);
			$sel_dateto = format_date_ymd_to_mdy($trade_date_to_process);

			$string_heading = "";
			$show_rep = "Show All";
			$show_client = "Show All";
			$show_symbol = "Show All";
			$datefrom = $trade_date_to_process;
			$dateto = $trade_date_to_process;

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
													DATE_FORMAT(trad_trade_date,'%m/%d') as show_trade_date,
													DATE_FORMAT(trad_trade_date,'%m/%d/%Y') as trad_trade_date,
													DATE_FORMAT(trad_run_date,'%m/%d/%Y') as trad_run_date,
													trad_advisor_name,
													FORMAT(trad_quantity,0) as trad_quantity,
													FORMAT(trade_price,2) as trade_price,
													trad_commission,
													trad_commission as for_sum_trad_commission,
													FORMAT(trad_cents_per_share*100,1) as trad_cents_per_share,
													trad_rr,
													trad_account_name,
													trad_is_cancelled,
													trad_correction_code
												FROM tmp_mry_cmpl_trades 
												ORDER BY trad_symbol, trad_advisor_name, trad_buy_sell, trad_trade_date";
		
		xdebug("query_trades",$query_trades);
	

		$data_to_html_file .= '
			<style type="text/css">
			<!--
			.data_black {font-family: "Courier New", Courier, mono;	font-size: 10px;	color: #000000;}
			-->
			</style>
			<table width="670" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td><font color="#000000" size="3" face="Courier"><strong><u>Cancel/Corrects and As-Of Trades</u></strong><br></font></td>
				</tr>
			</table>
			<img src="../../images/border_a.png" width="670" height="5">
			<table width="670" border="0" cellspacing="0" cellpadding="1">
				<tr>
				  <td>
					<font color="#000000" size="2" face="Courier"><strong>
					'.str_replace(" ","&nbsp;",str_pad("Type",8," ",1)).
					  str_replace(" ","&nbsp;",str_pad("Date",7," ",1)).
					  str_replace(" ","&nbsp;",str_pad("Account",38," ",1)).
					  str_replace(" ","&nbsp;",str_pad("RR #",6," ",1)).
					  str_replace(" ","&nbsp;",str_pad("Symbol",8," ",1)).
					  str_replace(" ","&nbsp;",str_pad("B/S",6," ",1)).
					  str_replace(" ","&nbsp;",str_pad("Shares",10," ",0)).
					  str_replace(" ","&nbsp;",str_pad("Price",8," ",0)).
					  str_replace(" ","&nbsp;",str_pad(" MRI ",8," ",0)).
					  str_replace(" ","&nbsp;",str_pad("Research",8," ",0)).
					  str_replace(" ","&nbsp;",str_pad("Comm.",9," ",0)).
					  str_replace(" ","&nbsp;",str_pad("&cent;/Shr.",6," ",0)).
					'</strong></font></td>
				</tr>';

			$heading_row = '				
				<tr>
					<td valign="bottom"><img src="../../images/border_a.png" width="670" height="5"></td>
				<tr>
				  <td>
					<font color="#000000" size="2" face="Courier"><strong>
					'.str_replace(" ","&nbsp;",str_pad("Type",8," ",1)).
					  str_replace(" ","&nbsp;",str_pad("Date",7," ",1)).
					  str_replace(" ","&nbsp;",str_pad("Account",38," ",1)).
					  str_replace(" ","&nbsp;",str_pad("RR #",6," ",1)).
					  str_replace(" ","&nbsp;",str_pad("Symbol",8," ",1)).
					  str_replace(" ","&nbsp;",str_pad("B/S",6," ",1)).
					  str_replace(" ","&nbsp;",str_pad("Shares",10," ",0)).
					  str_replace(" ","&nbsp;",str_pad("Price",8," ",0)).
					  str_replace(" ","&nbsp;",str_pad(" MRI ",8," ",0)).
					  str_replace(" ","&nbsp;",str_pad("Research",8," ",0)).
					  str_replace(" ","&nbsp;",str_pad("Comm.",9," ",0)).
					  str_replace(" ","&nbsp;",str_pad("&cent;/Shr.",6," ",0)).
					'</strong></font></td>
				</tr>';

		
			$result_trades = mysql_query($query_trades) or die(tdw_mysql_error($query_trades));
			
			$count_row_trades = 0;
			$count_page = 1;
			$running_trad_commission_total = 0;
			$hold_symbol = "";
			while($row_trades = mysql_fetch_array($result_trades))
			{
				
				if ($row_trades["trad_advisor_name"] == '') {
					$show_trad_advisor_name = $row_trades["trad_advisor_code"];
				} else {
					$show_trad_advisor_name = $row_trades["trad_advisor_name"];
				}
				
				if ($row_trades["trad_is_cancelled"] != 1 && $row_trades["trad_run_date"] == $row_trades["trad_trade_date"]) {
				$running_trad_commission_total = $running_trad_commission_total + $row_trades["for_sum_trad_commission"];
				}
				
				if ($arr_recent_mri[$row_trades["trad_symbol"]] == '') {
				$show_mri = 'n/a';
				} else {
				$arr_dateval = explode(' ',$arr_recent_mri[$row_trades["trad_symbol"]]);
				$show_mri = $arr_dateval[1]."-".$arr_dateval[0];
				}
				
				if ($arr_rres_symbols[$row_trades["trad_symbol"]] == '') {
				$show_rres = 'n/a';
				} else {
				$arr_dateval = explode(' ',$arr_rres_symbols[$row_trades["trad_symbol"]]);
				$show_rres = $arr_dateval[1]."-".$arr_dateval[0];
				}
				
				if ($show_mri == $date_match_val or $show_rres == $date_match_val) {
				$rowcolor = "ff0000";
				$rowstrongstart = "<strong>";
				$rowstrongend = "</strong>";
				} else {
						if (substr($row_trades["trad_rr"],0,2) != '09' && $row_trades["trad_commission"] == '0.0' && in_array($show_trad_advisor_name,$arr_acct_emp) ) {
							$rowcolor = "0000ff";
							$rowstrongstart = "<strong>";
							$rowstrongend = "</strong>";
						} elseif (substr($row_trades["trad_rr"],0,2) == '09') { 
							$rowcolor = "0000ff";
							$rowstrongstart = "<strong>";
							$rowstrongend = "</strong>";
						} else {
							$rowcolor = "000000";
							$rowstrongstart = "";
							$rowstrongend = "";
						}
				}
				
				//===================================================================================
				// TRADE TYPE STRING (CXL, CRT, ASOF)
				//===================================================================================
				$str_type = "";
				if ($row_trades["trad_run_date"] != $row_trades["trad_trade_date"]) {
				$str_type .= "AO,";
				}
				if ($row_trades["trad_is_cancelled"] == 1) {
				$str_type .= "CXL";
				}
				if ($row_trades["trad_is_cancelled"] == 0 && $row_trades["trad_correction_code"] != "5" && $row_trades["trad_correction_code"] != "") {
				$str_type .= "CRT";
				}
				
				//===================================================================================
				
				
				


				if ($count_page == 1) {
						if ($count_row_trades != 0 && $count_row_trades % 2 == 0) {
							if ($count_row_trades % 28 == 0) {
								$data_to_html_file .=  '<!-- NEW PAGE -->';
								$data_to_html_file .= $heading_row;
								echo "page break added >> ".$count_row_trades."<br>";
						      $count_page = 2;
								$count_row_trades = 1;
							}
						} elseif ($count_row_trades != 0 && $count_row_trades % 2 != 0) {
								if ($count_row_trades % 29 == 0) {
									$data_to_html_file .=  '<!-- NEW PAGE -->';
									$data_to_html_file .= $heading_row;
									echo "page break added >> ".$count_row_trades."<br>";
						      $count_page = 2;
									$count_row_trades = 1;
								}
						} else {
						//do nothing
						}
				} else {
						if ($count_row_trades != 0 && $count_row_trades % 2 == 0) {
							if ($count_row_trades != 0 && $count_row_trades % 32 == 0) {
							$data_to_html_file .=  '<!-- NEW PAGE -->';
							$data_to_html_file .= $heading_row;
							echo "page break added<br>";
							$count_row_trades = 1;
							}
						} elseif ($count_row_trades != 0 && $count_row_trades % 2 != 0) {
							if ($count_row_trades != 0 && $count_row_trades % 31 == 0) {
							$data_to_html_file .=  '<!-- NEW PAGE -->';
							$data_to_html_file .= $heading_row;
							echo "page break added<br>";
							$count_row_trades = 1;
							}
						} else {
						//do nothing
						}
				}
							
							
							if ($str_type != "") {
															$name_to_show = trim($show_trad_advisor_name)." (".trim($row_trades["trad_account_name"]).")";
															//IMPORTANT
															//echo $name_to_show."<br>";

													if (trim($hold_symbol) == trim($row_trades["trad_symbol"])) {
													    
													
															$data_to_html_file .= '
																				 <tr>
																						<td><font color="#'.$rowcolor.'" size="2" face="Courier">'.$rowstrongstart.
																						str_replace(" ","&nbsp;",str_pad($str_type,8," ",1)).
																						str_replace(" ","&nbsp;",str_pad($row_trades["show_trade_date"],7," ",1)).
																						str_replace(" ","&nbsp;",str_pad($name_to_show,38," ",1)).
																						str_replace(" ","&nbsp;",str_pad($row_trades["trad_rr"],6," ",1)).
																						str_replace(" ","&nbsp;",str_pad($row_trades["trad_symbol"],8," ",1)).
																						str_replace(" ","&nbsp;",str_pad(offset_buy_sell_space($row_trades["trad_buy_sell"]),5," ",1)).
																						str_replace(" ","&nbsp;",str_pad($row_trades["trad_quantity"],10," ",0)).
																						str_replace(" ","&nbsp;",str_pad($row_trades["trade_price"],8," ",0)).
																						str_replace(" ","&nbsp;",str_pad($show_mri,8," ",0)).
																						str_replace(" ","&nbsp;",str_pad($show_rres,8," ",0)).
																						str_replace(" ","&nbsp;",str_pad($row_trades["trad_commission"],9," ",0)).
																						str_replace(" ","&nbsp;",str_pad($row_trades["trad_cents_per_share"],6," ",0)).
																						$rowstrongend.'</font></td>
																					</tr>';
																					$count_row_trades = $count_row_trades + 1;
																					
																					if (stripos($str_type,"CXL") === false) {
																					//echo $running_aocxl_sum . " + " . $row_trades["trad_commission"] . "<br>";
																					$running_aocxl_sum = $running_aocxl_sum + $row_trades["trad_commission"];
																					} else {
																					//echo $running_aocxl_sum . " - " . $row_trades["trad_commission"] . "<br>";
																					$running_aocxl_sum = $running_aocxl_sum - $row_trades["trad_commission"];
																					}
									
													} else {
															$data_to_html_file .= '
																				 <tr>
																						<td><font color="#000000" size="2" face="Courier">&nbsp;</font></td>
																					</tr>';
															$data_to_html_file .= '
																				 <tr>
																						<td><font color="#'.$rowcolor.'" size="2" face="Courier">'.$rowstrongstart.
																						str_replace(" ","&nbsp;",str_pad($str_type,8," ",1)).
																						str_replace(" ","&nbsp;",str_pad($row_trades["show_trade_date"],7," ",1)).
																						str_replace(" ","&nbsp;",str_pad($name_to_show,38," ",1)).
																						str_replace(" ","&nbsp;",str_pad($row_trades["trad_rr"],6," ",1)).
																						str_replace(" ","&nbsp;",str_pad($row_trades["trad_symbol"],8," ",1)).
																						str_replace(" ","&nbsp;",str_pad(offset_buy_sell_space($row_trades["trad_buy_sell"]),5," ",1)).
																						str_replace(" ","&nbsp;",str_pad($row_trades["trad_quantity"],10," ",0)).
																						str_replace(" ","&nbsp;",str_pad($row_trades["trade_price"],8," ",0)).
																						str_replace(" ","&nbsp;",str_pad($show_mri,8," ",0)).
																						str_replace(" ","&nbsp;",str_pad($show_rres,8," ",0)).
																						str_replace(" ","&nbsp;",str_pad($row_trades["trad_commission"],9," ",0)).
																						str_replace(" ","&nbsp;",str_pad($row_trades["trad_cents_per_share"],6," ",0)).
																						$rowstrongend.'</font></td>
																					</tr>';
																									
																					$count_row_trades = $count_row_trades + 2;

																					if (stripos($str_type,"CXL") === false) {
																					//echo $running_aocxl_sum . " + " . $row_trades["trad_commission"] . "<br>";
																					$running_aocxl_sum = $running_aocxl_sum + $row_trades["trad_commission"];
																					} else {
																					//echo $running_aocxl_sum . " - " . $row_trades["trad_commission"] . "<br>";
																					$running_aocxl_sum = $running_aocxl_sum - $row_trades["trad_commission"];
																					}

													}
							}
				$hold_symbol = $row_trades["trad_symbol"];
			}
			
  									$data_to_html_file .=	'<tr><td><hr></td></tr>
																					<tr>
																						<td><font color="#000000" size="3" face="Courier"><strong>'.
																						str_replace(" ","&nbsp;",str_pad("TOTAL (AO, CXL, CRT):",10," ",1)).
																						str_replace(" ","&nbsp;",str_pad(number_format($running_aocxl_sum,2,'.',','),73," ",0)).
																						'</strong></font></td>
																					</tr>
																					<tr>
																						<td valign="top"><img src="../../images/border_a.png" width="670" height="5"></td>
																					<tr>
																					';
																					
										$data_to_html_file .=	'<tr><td>&nbsp;</td></tr><tr><td>&nbsp;</td></tr><tr><td>&nbsp;</td></tr>
																					<tr>
																						<td valign="top"><img src="../../images/border_a.png" width="670" height="5"></td>
																					<tr>
																					<tr>
																						<td><font color="#000000" size="3" face="Courier">'.
																						str_replace(" ","&nbsp;",str_pad("TOTAL (Trade Date):",10," ",1)).
																						str_replace(" ","&nbsp;",str_pad(number_format($running_trad_commission_total,2,'.',','),75," ",0)).
																						'</font></td>
																					</tr>
																					<tr>
																						<td><font color="#000000" size="3" face="Courier">'.
																						str_replace(" ","&nbsp;",str_pad("TOTAL (AO, CXL, CRT):",10," ",1)).
																						str_replace(" ","&nbsp;",str_pad(number_format($running_aocxl_sum,2,'.',','),73," ",0)).
																						'</font></td>
																					</tr>
																					<tr>
																						<td><hr></td>
																					<tr>
																					<tr>
																						<td><font color="#000000" size="3" face="Courier"><strong>'.
																						str_replace(" ","&nbsp;",str_pad("TOTAL (RUN DATE) / NFS:",10," ",1)).
																						str_replace(" ","&nbsp;",str_pad(number_format(($running_aocxl_sum+$running_trad_commission_total),2,'.',','),71," ",0)).
																						'</strong></font></td>
																					</tr>';
															
		
			
			
			echo ">>>>>>>>>>" . $running_aocxl_sum . ">>>>>>>>>>" . "<br>";
			
				$data_to_html_file .=	'
												<tr>
													<td valign="bottom"><img src="../../images/border_a.png" width="670" height="5"></td>
												<tr>
											</table
											<br><br>
											<br><br>
											<font color="#000000" size="3" face="Courier"><strong><u>Legend:</u></strong><br></font>
											<font color="#000000" size="3" face="Courier"><strong>�: Employee Trades in Outside Accounts</strong><br></font>
											<font color="#000000" size="3" face="Courier"><strong>�: Non-BRG BCM Trades</strong><br></font>
											<font color="#0000FF" size="3" face="Courier"><strong>Blue: Employee and Proprietary Trades.</strong><br></font>
											<font color="#FF0000" size="3" face="Courier"><strong>Red: Trades in stocks with same day Research/MRI.</strong><br></font>
											<font color="#000000" size="3" face="Courier"><strong>Type: AO = As Of, CXL = Cancel, CRT = Corrected.</strong></font>
											';

			 
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// END NEW SECTION (WITH PAGE BREAK)
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++			

	//$file_name = $trade_date_to_process."_dly_a_".substr(md5(rand(1000,9999)), 0, 8).".html";     

//test

	$file_name = $trade_date_to_process."_dcar_test.html";     
	$file_pdf_name = $trade_date_to_process."_dcar_test.pdf";     

//production
/*
	$file_name = $trade_date_to_process."_dcar.html";     
	$file_pdf_name = $trade_date_to_process."_dcar.pdf";     
*/

	$fp = fopen ($export_compliance.$file_name, "w");  
	fwrite ($fp,$data_to_html_file);        
	fclose ($fp); 

//=========================================================================================================================================
//LOG THIS DATA TO TABLE
		$result_exist = mysql_query("SELECT * FROM mgmt_reports_creation where msrv_rep_file = '".$file_pdf_name."'") or die (mysql_error());
		$countx = mysql_num_rows($result_exist);
		if ($countx == 0) {
		$qry_insert = "INSERT INTO mgmt_reports_creation 
										( auto_id , 
										msrv_rep_id, 
										msrv_trade_date , 
										msrv_creation_datetime , 
										msrv_rep_file , 
										msrv_isactive ) 
										VALUES (
										NULL , 
										'DCAR', 
										'".$trade_date_to_process."', 
										NOW(), 
										'".$file_pdf_name."', 
										'1'
										)";
		
		//$result_insert = mysql_query($qry_insert) or die (tdw_mysql_error($qry_insert));
		}
//=========================================================================================================================================

$cmd_pdf = "d:\\tdw\\tdw\\includes\\createpdf.bat ". $file_pdf_name. " " . $file_name;
echo $cmd_pdf."<br>";
shell_exec($cmd_pdf);


//Email procedure
function get_user_id ($email) {
	$result_id = mysql_query("SELECT ID FROM Users where Email = '".$email."'") or die (mysql_error());
	while ( $row = mysql_fetch_array($result_id) ) {
		$return_id = $row["ID"];
	}
	return $return_id;	
}

$arr_recipient = array();

//test
$arr_recipient[0] = 'pprasad@centersys.com';

//production
/*
$arr_recipient[0] = 'lkarp@buckresearch.com';
$arr_recipient[1] = 'tsutera@buckresearch.com';
$arr_recipient[2] = 'jperno@buckresearch.com';
$arr_recipient[3] = 'centersys@buckresearch.com';
*/

foreach ($arr_recipient as $key => $emailval) {

				$user_id = get_user_id($emailval);
				$link = "";
				$link = $_site_url."repsvr.php?rep=DCAR&src=".rand(10000000,99999999).str_replace('-','N',$trade_date_to_process).str_pad($user_id,10,'Q',1).md5("pprasad@centersys.com");
				
				$email_log = '
									<table width="100%" border="0" cellspacing="0" cellpadding="10">
										<tr> 
											<td valign="top">
												<p>&nbsp;</p>
												<p><a class="bodytext12"><strong>Daily Compliance Activity Report</strong></a></p>			
												<p><a class="bodytext12">Trade Date: <strong>'.$date_to_show.'</strong></a></p>
												<p class="bodytext12">Please click <strong><a href="'.$link.'">&gt;&gt;HERE&lt;&lt;</a></strong> to access the report.</p>
												<p>&nbsp;</p>
												<p>&nbsp;</p>
												<p><a class="bodytext12"><strong>TDW Administrator</strong></a></p></td>
										</tr>
									</table>
										';
				//create mail to send
				$html_body = "";
				$html_body .= zSysMailHeader("");
				$html_body .= $email_log;
				$html_body .= zSysMailFooter ();
				
				$subject = "Daily Compliance Activity Report : (Trade Date: ".$date_to_show.")";
				$text_body = $subject;
				
				//zSysMailer($emailval, "", $subject, $html_body, $text_body, "") ;
				echo $link . "<br>";
}
?>
