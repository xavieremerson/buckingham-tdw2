<?
  include('../includes/dbconnect.php');
  include('../includes/global.php');
	include('../includes/functions.php');

	function format_buysellcovershort ($str) {
		$str = trim($str);
		$str_show = str_replace('Sell','S',$str);
		$str_show = str_replace('Buy','B',$str_show);
		$str_show = str_replace('Cover','C',$str_show);
		$str_show = str_replace('Short','SS',$str_show);
		$str_show = str_replace(' )',')',$str_show);
		$str_show = str_replace(' )',')',$str_show);
		return $str_show;
	}

	//echo "test";
	//exit;       
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

	function single_val_returns($qry, $col=null) {
		$result = mysql_query($qry) or die (tdw_mysql_error($qry));
		while ( $row = mysql_fetch_array($result) ) 
		{
			$returnval = $row["singleval"];
		}
		return $returnval;
	}

	//Email procedure
	function get_user_id ($email) {
		$result_id = mysql_query("SELECT ID FROM Users where Email = '".$email."'") or die (mysql_error());
		while ( $row = mysql_fetch_array($result_id) ) {
			$return_id = $row["ID"];
		}
		return $return_id;	
	}

							//Previous Business Day should be applied here.
							$trade_date_to_process = previous_business_day();
							//$trade_date_to_process = '2010-01-25';
						
							//**************************************************************
								$dval = explode("-", $trade_date_to_process); 
								$y1 = $dval[0];
								$m1 = $dval[1];
								$d1 = $dval[2];
								
								$timeval = mktime(0,0,0, $m1, $d1, $y1);
								
								$newtime = $timeval + (60*60*24);	
								$nextday = date("Y-m-d", $newtime);
								
								xdebug("next_day", $nextday);
							//**************************************************************
						
							//Create array of analysts/stocks in coverage universe for lookup
							$qry_acv = "SELECT DISTINCT (
																	concat(acv_symbol, '^', acv_tdw_rr_num) 
																	) as acv_val
																	FROM acv_analyst_coverage 
																	WHERE acv_tdw_rr_num != ''
																	ORDER BY acv_symbol";
							$result_acv = mysql_query($qry_acv) or die (tdw_mysql_error($qry_acv));
							$arr_acv = array();
							$maint_acv_count = 0;
							while ( $row_acv = mysql_fetch_array($result_acv) ) 
							{
								$arr_acv[$maint_acv_count] = trim($row_acv["acv_val"]);
								$maint_acv_count = $maint_acv_count + 1;
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
																															'0'."^".
																															'NO';
								$maint_count = $maint_count + 1;
							}	
						
							//Create array of analysts/stocks in coverage universe for lookup for external trades
							$qry_acv_ext = "SELECT DISTINCT (
																	concat(acv_symbol, '^', acv_tdw_userid) 
																	) as acv_val
																	FROM acv_analyst_coverage 
																	WHERE acv_tdw_rr_num != ''
																	ORDER BY acv_symbol";
							$result_acv_ext = mysql_query($qry_acv_ext) or die (tdw_mysql_error($qry_acv_ext));
							$arr_acv_ext = array();
							$maint_acv_count_ext = 0;
							while ( $row_acv_ext = mysql_fetch_array($result_acv_ext) ) 
							{
								$arr_acv_ext[$maint_acv_count_ext] = trim($row_acv_ext["acv_val"]);
								$maint_acv_count_ext = $maint_acv_count_ext + 1;
							}
						
							//keep Outside Trades (Employee) in an array
							$qry_oemp_trades = "SELECT 
																		a.auto_id,
																		a.otd_account_id,
																		a.otd_trade_date,
																		a.otd_buysell,
																		a.otd_symbol,
																		a.otd_quantity,
																		round(a.otd_price,2) as otd_price,
																		a.otd_entered_by,
																		a.otd_last_edited_by,
																		a.otd_last_edited_on,
																		a.otd_isactive, 
																		c.Fullname,
																		c.ID
																	FROM otd_emp_trades_external a, oac_emp_accounts b, users c
																	WHERE a.otd_account_id = b.auto_id
																	AND b.oac_emp_userid = c.ID
																	AND a.otd_trade_date = '".$trade_date_to_process."'
																	AND a.otd_isactive =1";
							
							//xdebug("qry_oemp_trades",$qry_oemp_trades);
							$result_oemp_trades = mysql_query($qry_oemp_trades) or die (tdw_mysql_error($qry_oemp_trades));
							$arr_oemp_trades_to_merge = array();
							$maint_count_oemp = 9999;
							$count_acv_ext = 0;
							while ( $row_oemp_trades = mysql_fetch_array($result_oemp_trades) ) 
							{
							
								if (in_array(trim($row_oemp_trades["otd_symbol"]).'^'.$row_oemp_trades["ID"], $arr_acv_ext)) {
									$str_acv_ext = 'YES';
									$count_acv_ext = $count_acv_ext + 1;
								} else {
									$str_acv_ext = 'NO';
								}
							
								$arr_oemp_trades_to_merge[$maint_count_oemp] = trim($row_oemp_trades["otd_symbol"])."^".
																															$row_oemp_trades["Fullname"]."^".
																															'  '."^".
																															$row_oemp_trades["otd_buysell"]."^".
																															$row_oemp_trades["otd_quantity"]."^".
																															$row_oemp_trades["otd_price"]."^".
																															'0.00'."^".
																															'0.0'."^".
																															'#outside_employee_trade#'."^".
																															'0'."^".
																															$str_acv_ext;
								$maint_count_oemp = $maint_count_oemp + 1;
							}	
							
							
							//xdebug("count_acv_ext",$count_acv_ext);
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
						/*$msconnect=mssql_connect("1Z92.168.20.48","BUCKINGHAM_login","BUCKINGHAM_pw");
						$msdb=mssql_select_db("BUCKINGHAM",$msconnect);
						*/
						$msconnect=mssql_connect("192.168.1.78","buckinghamtwo_db","9eFah9fe");
						$msdb=mssql_select_db("BuckinghamTwo",$msconnect);
						
						
							xdebug('Connecting to Jovus Server @ Buckingham','Successful');
							//Most recent research date from Jovus
							 
								$arr_rres = array();
								$arr_rres_symbols = array();
						
								$research_date_next = date('Y-m-d', strtotime('+1 day', strtotime($trade_date_to_process)));
								xdebug("Next day : Used to get latest research",$research_date_next);


								$ms_qry_rres   = "SELECT dbo.Prod_Issuers.IssuerID, dbo.ExchangeSecurities.Ticker as CUSIP, 
																	Max(dbo.Prod_Statuses.DateTime) AS MaxOfDateTime
																	 FROM ((dbo.Prod_Issuers INNER JOIN dbo.Products ON dbo.Prod_Issuers.ProductID = dbo.Products.ProductID) 
																	INNER JOIN dbo.Issuers ON dbo.Prod_Issuers.IssuerID = dbo.Issuers.IssuerID) 
																	INNER JOIN dbo.Prod_Statuses ON dbo.Products.ProductID = dbo.Prod_Statuses.ProductID 
																	INNER JOIN dbo.ExchangeSecurities ON dbo.Issuers.IssuerID = dbo.ExchangeSecurities.SecurityID 
																	WHERE dbo.ExchangeSecurities.Ticker <> '' 
																	AND ((dbo.Prod_Statuses.DateTime) < CAST(FLOOR(CAST(convert(datetime,'".format_date_ymd_to_mdy($research_date_next)."',120) AS float)) as datetime)) 
																	AND ((dbo.Products.CreationDateTime) < CAST(FLOOR(CAST(convert(datetime,'".format_date_ymd_to_mdy($research_date_next)."',120) AS float)) as datetime)) 
																	GROUP BY dbo.Prod_Issuers.IssuerID, dbo.ExchangeSecurities.Ticker, dbo.Prod_Statuses.StatusTypeID 
																	HAVING (((dbo.Prod_Statuses.StatusTypeID)=3)) 
																	order by dbo.ExchangeSecurities.Ticker;";
						
								//xdebug("ms_qry_rres",$ms_qry_rres);
								$ms_results_rres = mssql_query($ms_qry_rres);
								
								$v_count_rres = 0;
								while ($row_rres = mssql_fetch_array($ms_results_rres)) {
											
											$symbol = $row_rres[1];
											$rres_date = $row_rres[2];
											$arr_rres_symbols[$symbol] = $rres_date;
						
								}
						
						//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
						
								//*************************************************************************************************************	
								//Found Initiation of Coverage
								/*$ms_qry_ioc = "SELECT dbo.ExchangeSecurities.Ticker as CUSIP, 
																			 count(dbo.ExchangeSecurities.Ticker) as CountCUSIP,
																			 CONVERT(VARCHAR(10), max(dbo.Prod_Statuses.DateTime), 120) as IOCdate,
																			 CONVERT(VARCHAR(10), min(dbo.Prod_Statuses.DateTime), 120) as PreviousDate 
																FROM (
																	(
																	dbo.Prod_Issuers INNER JOIN dbo.Products ON dbo.Prod_Issuers.ProductID = dbo.Products.ProductID
																	) 
																	INNER JOIN dbo.Issuers ON dbo.Prod_Issuers.IssuerID = dbo.Issuers.IssuerID
																		 ) 
																		 INNER JOIN dbo.Prod_Statuses ON dbo.Products.ProductID = dbo.Prod_Statuses.ProductID 
																		 INNER JOIN dbo.ExchangeSecurities ON dbo.Issuers.IssuerID = dbo.ExchangeSecurities.SecurityID 
																WHERE (((dbo.ExchangeSecurities.Ticker <> '') 
																AND (dbo.Products.CreationDateTime BETWEEN (CAST(FLOOR(CAST(convert(datetime,'".format_date_ymd_to_mdy($trade_date_to_process)."',120) AS float)) as datetime)-180) 
																	AND CAST(FLOOR(CAST(convert(datetime,'".format_date_ymd_to_mdy($nextday)."',120) AS float)) as datetime)) 
																AND ((dbo.Prod_Statuses.StatusTypeID)=3)) 
																)
																AND dbo.Products.Title like  '%nitiat%'
																GROUP BY dbo.ExchangeSecurities.Ticker
																ORDER BY dbo.ExchangeSecurities.Ticker;";	*/
						
										$ms_qry_ioc = "
										SELECT dbo.ExchangeSecurities.Ticker as CUSIP, 
													 count(dbo.ExchangeSecurities.Ticker) as CountCUSIP,
													 CONVERT(VARCHAR(10), max(dbo.Prod_Statuses.DateTime), 120) as IOCdate,
													 max(dbo.Prod_Statuses.DateTime) as date_in_mri_format,
													 max(dbo.Prod_SubjectCodes.SubjectCode) as SubjectCode
													 
										FROM (
											(
											dbo.Prod_Issuers INNER JOIN dbo.Products ON dbo.Prod_Issuers.ProductID = dbo.Products.ProductID
											) 
											INNER JOIN dbo.Issuers ON dbo.Prod_Issuers.IssuerID = dbo.Issuers.IssuerID
												 ) 
												 INNER JOIN dbo.Prod_Statuses ON dbo.Products.ProductID = dbo.Prod_Statuses.ProductID 
												 INNER JOIN dbo.ExchangeSecurities ON dbo.Issuers.IssuerID = dbo.ExchangeSecurities.SecurityID
												 INNER JOIN dbo.Prod_SubjectCodes on dbo.Products.ProductID = dbo.Prod_SubjectCodes.ProductID 
										WHERE (
														(
															(dbo.ExchangeSecurities.Ticker <> '') 
															AND 
																(
																	dbo.Products.CreationDateTime BETWEEN 
																	(CAST(FLOOR(CAST(convert(datetime,'".format_date_ymd_to_mdy($trade_date_to_process)."',120) AS float)) as datetime)-180) 
											            AND 
																	CAST(FLOOR(CAST(convert(datetime,'".format_date_ymd_to_mdy($nextday)."',120) AS float)) as datetime)
																) 
															AND ((dbo.Prod_Statuses.StatusTypeID)=3)
														)
													) 
										AND dbo.Prod_SubjectCodes.SubjectCode = 'Initiating Coverage'
										GROUP BY dbo.ExchangeSecurities.Ticker
										ORDER BY dbo.ExchangeSecurities.Ticker;";	
										//AND dbo.Products.Title like  '%nitiat%'

								//xdebug("ms_qry_ioc",$ms_qry_ioc);
								$ms_results_ioc = mssql_query($ms_qry_ioc);
								
								$v_count_ioc = 0;
								$arr_ioc = array();
								$arr_ioc_mri_format = array();
								while ($row_ioc = mssql_fetch_array($ms_results_ioc)) {
											
											$symbol = $row_ioc[0];
											$ioc_date = $row_ioc[2];
											$count_num = $row_ioc[1];
											$ioc_date_mri_format = $row_ioc[3];
						
											//xdebug("ioc_info",$symbol."/".$count_num."/".$ioc_date);
											if ($count_num == 1 and $ioc_date == $trade_date_to_process) {
												$arr_ioc[] = $symbol;
												$arr_ioc_mri_format[$symbol] = $ioc_date_mri_format;
											}
								}
								//*************************************************************************************************************	
								//$arr_ioc[] = 'KG';
								//$arr_ioc_mri_format['KG'] = 'Apr 17 2009 9:01AM';
						
								echo "Initiating Coverage\n<br>";
								show_array($arr_ioc);
								echo "Initiating Coverage MRI Format\n<br>";
								show_array($arr_ioc_mri_format);
								//*************************************************************************************************************	
		
		//$arr_ioc[] = 'DSW';
		
		//show_array($arr_ioc);
		//exit;
    //Most recent MRI Date
		$arr_mri = array();
		$arr_mri_symbols = array();

		$ms_qry_mri = "SELECT 
											dbo.ExchangeSecurities.Ticker as CUSIP,
											dbo.Prod_Statuses.DateTime, 
											dbo.Prod_Issuers.IssuerID, 
											dbo.Prod_Issuers.Recommendation, 
											dbo.Prod_Issuers.PreviousRecommendation, 
											dbo.Prod_Issuers.RecommendationAction, 
											dbo.Prod_Issuers.TargetPrice, 
											dbo.Prod_Statuses.StatusTypeID
										FROM ((dbo.Prod_Issuers INNER JOIN dbo.Products ON dbo.Prod_Issuers.ProductID = dbo.Products.ProductID) 
										INNER JOIN dbo.Issuers ON dbo.Prod_Issuers.IssuerID = dbo.Issuers.IssuerID) 
										INNER JOIN dbo.Prod_Statuses ON dbo.Products.ProductID = dbo.Prod_Statuses.ProductID
										INNER JOIN dbo.ExchangeSecurities ON dbo.Issuers.IssuerID = dbo.ExchangeSecurities.SecurityID
									  WHERE 
												(
													(
														(dbo.ExchangeSecurities.Ticker <> '') 
														AND 
														(dbo.Prod_Statuses.DateTime BETWEEN 
															(
														 		CAST(FLOOR(CAST(convert(datetime,'".format_date_ymd_to_mdy($trade_date_to_process)."',120) AS float)) as datetime)-180) 
												 				AND CAST(FLOOR(CAST(convert(datetime,'".format_date_ymd_to_mdy($nextday)."',120) AS float)) as datetime)
															) 
														AND 
														(
															(dbo.Prod_Statuses.StatusTypeID)=3
														)
													)
												)
										ORDER BY dbo.ExchangeSecurities.Ticker, dbo.Prod_Statuses.DateTime DESC;";	


		//xdebug("ms_qry_mri",$ms_qry_mri);
		//exit;
		$ms_results_mri = mssql_query($ms_qry_mri);
		
		$v_count_mri = 0;
		while ($row_mri = mssql_fetch_array($ms_results_mri)) {
					
					//show_array($row_mri);
					$symbol = trim($row_mri[0]);
					$mri_date = trim($row_mri[1]);
					$rating = trim($row_mri[3]);
					$rating_previous = trim($row_mri[4]);
					$rating_change = trim($row_mri[5]); 
					$target = trim($row_mri[6]);

					if ($rating_change == "Increase") {
					  $img_show = '<img src="images/themes/standard/arrow_up.gif" border="0">';
						$arr_mri[$v_count_mri] = $symbol."<###>".$mri_date."<###>".$rating."<###>".$rating_change."<###>".$img_show."<###>".$target."<###>".$rating_previous;
						$arr_mri_symbols[$v_count_mri] = $symbol;
						$v_count_mri = $v_count_mri + 1;
					} elseif ($rating_change == "Decrease"){
					  $img_show = '<img src="images/themes/standard/arrow_down.gif" border="0">';
						$arr_mri[$v_count_mri] = $symbol."<###>".$mri_date."<###>".$rating."<###>".$rating_change."<###>".$img_show."<###>".$target."<###>".$rating_previous;
						$arr_mri_symbols[$v_count_mri] = $symbol;
						$v_count_mri = $v_count_mri + 1;
					} else {
					  $img_show = '';
						$arr_mri[$v_count_mri] = $symbol."<###>".$mri_date."<###>".$rating."<###>".$rating_change."<###>".$img_show."<###>".$target."<###>".$rating_previous;
						$arr_mri_symbols[$v_count_mri] = $symbol;
						$v_count_mri = $v_count_mri + 1;
					}

		}
   
	 //show_array($arr_mri);
	 
	 //CLEAN UP NULL VALUES
	 foreach($arr_mri as $key=>$value) {
		 $arr_data = explode("<###>",$value);
		 if ($arr_data[2]=='' && $arr_data[3]=='' && $arr_data[4]=='' && $arr_data[5]=='' && $arr_data[6]=='') {
		   unset($arr_mri[$key]);
		 }
	 }
	 
	 //show_array($arr_mri);
	 
	 //re index
	 $arr_mri_new = array();
	 foreach($arr_mri as $key=>$value) {
			$arr_mri_new[] = $value;
	 }

   $arr_mri = array();
	 $arr_mri = $arr_mri_new;


	 //get historical data from table
	 $arr_mri_alt = array();
	 $result_alt = mysql_query("select * from _jovus_migration_mri"); 
	 while ($row_alt = mysql_fetch_array($result_alt))  {
	 		$arr_mri_alt[] = $row_alt["mri_data"];	
	 }


   //show_array($arr_mri_alt);
	 //show_array($arr_mri);
	 
	 
	 

	 $arr_mri_new = array();
   $arr_mri_new = array_merge($arr_mri, $arr_mri_alt);
	 //show_array($arr_mri_new);

   //sort by ticker and date descending
	 $arr_mri_sort = array();
	 $arr_mri_sort_x = array();
	 foreach($arr_mri_new as $key=>$value) {
	 		$arr_vals = explode("<###>",$value);
			$arr_mri_sort[$arr_vals[0].strtotime($arr_vals[1])] = $value;
			$arr_mri_sort_x[$value] = $arr_vals[0].strtotime($arr_vals[1]);
	 }


	 //show_array($arr_mri_sort);
   krsort($arr_mri_sort);
	 //show_array($arr_mri_sort);
	 
	 //reindex the array with numerics
	 $arr_mri = array();
	 foreach($arr_mri_sort as $key=>$value) {
			$arr_mri[] = $value;
	 }

	 //show_array($arr_mri);
   //exit;
	 //show_array($arr_mri_symbols);
	 //show_array($arr_mri);
	 //Array of relevant MRI data 
	 $arr_recent_mri = array();
	 $arr_recent_mri_trade_found = array();
	 $ignore_more_mri = array();
	 
	 foreach($arr_mri as $key=>$value) {

						 $arr_data = explode("<###>",$value);
						 
						 //show_array($arr_data);
						 $str_x_symbol = $arr_data[0];
						 $str_x_date = $arr_data[1];
						 $str_x_rating = $arr_data[2];
						 $str_x_rating_previous = $arr_data[6];
						 $str_x_target = $arr_data[5];
						 
						 if (date('Y-m-d', strtotime($arr_data[1])) == $trade_date_to_process) { // && $str_x_target !='N/A' && $str_x_target != 'NA'
						 	 //get the next in line data if applicable
							 $arr_data_next = explode("<###>",$arr_mri[$key+1]);
							 if ($str_x_symbol == $arr_data_next[0]) {
								 $str_x_symbol_next = $arr_data_next[0];
								 $str_x_date_next = $arr_data_next[1];
								 $str_x_rating_next = $arr_data_next[2];
								 $str_x_rating_previous_next = $arr_data_next[6];
								 $str_x_target_next = $arr_data_next[5];
							 } else {
								 $str_x_symbol_next = "";
								 $str_x_date_next = "";
								 $str_x_rating_next = "";
								 $str_x_rating_previous_next = "";
								 $str_x_target_next = "";
							 }
							 
							 //Condition 1: Rating Current and Previous are different
							 if ($str_x_rating != $str_x_rating_previous && trim($str_x_rating_previous) != "") {
							 		$arr_recent_mri[$str_x_symbol] = $str_x_date;
							 }
							 
							 //Condition 2: Target Current and Previous are different
							 if ((int)str_replace("$",'',$str_x_target) != (int)str_replace("$",'',$str_x_target_next) && trim($str_x_target_next) != "") {
							 		$arr_recent_mri[$str_x_symbol] = $str_x_date;
							 }
							 
							 
							 //xdebug("Variables :",$str_x_symbol."//".$str_x_date."//".$str_x_rating."//".$str_x_rating_previous."//".$str_x_target);
							 //show_array($arr_data_next);
						 }
						 
						 

	 } 

	 
	 //$arr_recent_mri[$str_symbol_old] = $str_date_old;

	 
	 show_array($arr_recent_mri);
	 //exit;
						//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
						//exit;
						
						
						//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
						// END JOVUS SECTION
						//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
						
																																							
						//===================================================================
							//exit;
						//===================================================================
						
						//show_array($arr_recent_mri_trade_found);
						
						//CREATE ARRAY FOR LOOKBACK TABLE
						$arr_show_lookback_symbols = array();
						
						if (count($arr_recent_mri_trade_found) > 0 ) {
							foreach ($arr_recent_mri_trade_found as $key => $val) {
							$str_show_mri_action .= ', <font color="#FF0000" size="3" face="Courier"><strong>'.$val.'</strong></font>'; 
							$arr_show_lookback_symbols[] = $val;
							}
							$str_show_mri_action = substr($str_show_mri_action, 2, 5000);
						} else {
							$str_show_mri_action = '<font color="#000000" size="3" face="Courier"><strong>NONE</strong></font>';
						}
						
						echo  $str_show_mri_action."<br>";
						
						 //show_array($arr_recent_mri);
						
						$str_show_no_mri_action = "";		
						$count_no_mri_action = 0;				
						foreach ($arr_recent_mri as $key => $val) {
						$arr_dateval = explode(' ',$val);
							if ($arr_dateval[1]."-".$arr_dateval[0] == $date_match_val){ 
							 if (!in_array($key,$arr_recent_mri_trade_found)) { 
								 //echo  $key.">>".$val."<br>";
								 $count_no_mri_action = $count_no_mri_action + 1;
								 $str_show_no_mri_action .= ', <font color="#FF0000" size="3" face="Courier"><strong>'.$key.'</strong></font>'; //", <u>". $key . "</u> (". ucwords(strtolower(get_company_name($key))) . ")";
								 $arr_show_lookback_symbols[] = $key;
							 }
							}
						}
						
						if ($count_no_mri_action == 0) {
							$str_show_no_mri_action = '<font color="#000000" size="3" face="Courier"><strong>NONE</strong></font>';
						} else {
						$str_show_no_mri_action = substr($str_show_no_mri_action, 2, 5000);
						}
						
						
						echo  $str_show_no_mri_action."<br>";

						include('sproc_dly_mri_lookback_inc.php');
?>