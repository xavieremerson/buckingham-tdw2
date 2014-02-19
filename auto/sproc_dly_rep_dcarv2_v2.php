<?
	ini_set("memory_limit","256M");
	
  include('../includes/dbconnect.php');
  include('../includes/global.php');
	include('../includes/functions.php');
	include('../includes/functions_eq_opts.php');


////SECTION: DO NOT RUN ON WEEKENDS OR HOLIDAYS
//====================================================================================================
//  NEEDS TO RUN ONLY ON WEEKDAYS AND NON-HOLIDAYS, THIS IS TO CHECK THAT CONDITION
	$str_date = date('Y-m-d');
	echo $str_date."<br>";
	if (
	    check_holiday ($str_date)==1 
	    or date('D',strtotime($str_date))=='Sat' 
			or date('D',strtotime($str_date))=='Sun'
		 ) {
		echo "Today is a holiday or weekend, hence terminating program execution!<br>";
		exit;
	} else {
		echo "Today is not a holiday or weekend, hence proceeding with program execution!<br>";
	}
  echo "Proceeding after holiday/weekend check....<br>";
//====================================================================================================

////SECTION: WHAT DAY TO PROCESS? 
	//Previous Business Day should be applied here.
	//$trade_date_to_process = previous_business_day();
	$trade_date_to_process = '2010-05-04';  //10/28
  xdebug("trade_date_to_process",$trade_date_to_process);

  //**************************************************************
		$dval = explode("-", $trade_date_to_process); 
		$y1 = $dval[0];
		$m1 = $dval[1];
		$d1 = $dval[2];
		
		$timeval = mktime(0,0,0, $m1, $d1, $y1);
		
		$newtime = $timeval + (60*60*24);	
		$nextday = date("Y-m-d", $newtime);
	//**************************************************************

////SECTION: Create array of analysts/stocks in coverage universe for lookup
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

////SECTION: Create array of stocks in BCM WatchList for lookup // bcm_datetime_start  bcm_datetime_stop
	$qry_bcm = "SELECT DISTINCT (
							bcm_cusip
							) AS bcm_val
							FROM bcm_watchlist
							WHERE 
							(	    bcm_datetime_start < '".$trade_date_to_process." 00:00:00' 
							  AND bcm_datetime_stop > '".$trade_date_to_process." 00:00:00' 
								AND bcm_datetime_stop < '".business_day_forward(strtotime($trade_date_to_process),1)." 00:00:00' )
							OR 
							(	    bcm_datetime_start > '".$trade_date_to_process." 00:00:00' 
							  AND bcm_datetime_start < '".business_day_forward(strtotime($trade_date_to_process),1)." 00:00:00' 
								AND bcm_datetime_stop > '".$trade_date_to_process." 00:00:00' 
								AND bcm_datetime_stop < '".business_day_forward(strtotime($trade_date_to_process),1)." 00:00:00' )
							OR 
							(	    bcm_datetime_start > '".$trade_date_to_process." 00:00:00' 
							  AND bcm_datetime_start < '".business_day_forward(strtotime($trade_date_to_process),1)." 00:00:00' 
								AND bcm_datetime_stop > '".business_day_forward(strtotime($trade_date_to_process),1)." 00:00:00' )
							OR 
							(	    bcm_datetime_start < '".$trade_date_to_process." 00:00:00' 
								AND bcm_datetime_stop  > '".business_day_forward(strtotime($trade_date_to_process),1)." 00:00:00' )
							OR 
							(	bcm_datetime_start < '".$trade_date_to_process." 00:00:00' AND bcm_datetime_stop = '2099-12-31' )
							OR 
							(	    bcm_datetime_start > '".$trade_date_to_process." 00:00:00' 
							  AND bcm_datetime_start < '".business_day_forward(strtotime($trade_date_to_process),1)." 00:00:00'
								AND bcm_datetime_stop = '2099-12-31' )";

	//xdebug("qry",$qry_bcm);
	$result_bcm = mysql_query($qry_bcm) or die (tdw_mysql_error($qry_bcm));
	$arr_bcm = array();
	while ( $row_bcm = mysql_fetch_array($result_bcm) )  
	{
 		$arr_bcm[] = trim($row_bcm["bcm_val"]);
	}
  
	echo "Watch List items<br>";
	show_array($arr_bcm);

  //exit;
	
////SECTION: Functions
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
	$count_bcm_watchlist = 0;
	while ( $row_oth_trades = mysql_fetch_array($result_oth_trades) ) 
	{
		
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		//PROCESS OPTIONS
		$var_bcm_ticker = "";
		if (stripos(trim($row_oth_trades["oth_symbol"]), "+")!==false) { // or stripos($rec_to_process[0], " ")!==false
			$arr_get_option_detail = array();
			
			//########################################################################
			$str_option = ereg_replace("[^A-Za-z]", "", trim($row_oth_trades["oth_symbol"]));
			$str_option = $str_option.".x";

			$str_company_detail = get_company_detail($str_option);
			$acd = explode("^", $str_company_detail);
			$str = $acd[0];

			if (substr($str,0,strpos($str," ")) != "") {
				$var_bcm_ticker = substr($str,0,strpos($str," "));
			  echo trim($row_oth_trades["oth_symbol"])." >> ".$var_bcm_ticker."\n";
			  ob_flush();
			  flush();

			} else {
				$var_bcm_ticker = "???";
			  echo trim($row_oth_trades["oth_symbol"])." >> ".$var_bcm_ticker."\n";
			  ob_flush();
			  flush();
			}
			//###################################################################################
		} else {
			$var_bcm_ticker = trim($row_oth_trades["oth_symbol"]);
		}
		//xdebug("var_bcm_ticker",$var_bcm_ticker);
		//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		$show_bcm_watch = "";
		if (in_array($var_bcm_ticker,$arr_bcm)) {
			$count_bcm_watchlist = $count_bcm_watchlist + 1;
			$show_bcm_watch = "BCM_WATCH";
		}	 else {
			$show_bcm_watch = "NO_BCM_WATCH";
		}

 		$arr_oth_trades_to_merge[$maint_count] =      trim($row_oth_trades["oth_symbol"])."^".
																									'BUCK CAPITAL MGMT'."^".
																									'  '."^".
																									process_bscs($row_oth_trades["oth_buysell"])."^".
																									$row_oth_trades["oth_quantity"]."^".
																									$row_oth_trades["oth_price"]."^".
																									'non-brg'."^".
																									$row_oth_trades["centspershare"]."^".
																									$show_bcm_watch."^".
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

//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
	//keep FIDELITY (Employee Trades) in an array
	$qry_fid_trades = "SELECT 
												auto_id,
												acct_num,
												trade_date,
												buy_sell,
												symbol,
												round(sum(quantity),0) as quantity, 
												round(avg(price),2) as price,
												is_active, 
												substring(concat(trim(first_name),' ',trim(middle_name),' ',trim(last_name)),1,20) as Fullname
											FROM fidelity_emp_trades
											WHERE trade_date = '".$trade_date_to_process."'
											AND is_active  = 1
											GROUP BY acct_num, symbol, trade_date, buy_sell";
	
	//xdebug("qry_fid_trades",$qry_fid_trades);
	$result_fid_trades = mysql_query($qry_fid_trades) or die (tdw_mysql_error($qry_fid_trades));
	$arr_fid_trades_to_merge = array();
	$maint_count_fid = 99999;
	$count_fid_ext = 0;
	while ( $row_fid_trades = mysql_fetch_array($result_fid_trades) ) 
	{
	
 		$arr_fid_trades_to_merge[$maint_count_fid] = trim($row_fid_trades["symbol"])."^".
																									$row_fid_trades["Fullname"]."^".
																									'FID'."^".
																									$row_fid_trades["buy_sell"]."^".
																									$row_fid_trades["quantity"]."^".
																									$row_fid_trades["price"]."^".
																									'0.00'."^".
																									'0.0'."^".
																									'#outside_fidelity_trade#'."^".
																									'0'."^".
																									$str_acv_ext;
		$maint_count_fid = $maint_count_fid + 1;
	}	
	//show_array($arr_fid_trades_to_merge);
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&



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
											WHERE 
												dbo.ExchangeSecurities.Ticker <> '' 
												AND 
														(
																(dbo.Prod_Statuses.DateTime) < CAST(FLOOR(CAST(convert(datetime,'".format_date_ymd_to_mdy($research_date_next)."',120) AS float)) as datetime)
														) 
												AND 
														(
																(dbo.Products.CreationDateTime) < CAST(FLOOR(CAST(convert(datetime,'".format_date_ymd_to_mdy($research_date_next)."',120) AS float)) as datetime)
														) 
											GROUP BY dbo.Prod_Issuers.IssuerID, dbo.ExchangeSecurities.Ticker, dbo.Prod_Statuses.StatusTypeID 
											HAVING (((dbo.Prod_Statuses.StatusTypeID)=3)) 
											order by dbo.ExchangeSecurities.Ticker;";

		//xdebug("ms_qry_rres",$ms_qry_rres);
		//exit;
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
		$ms_qry_ioc = "SELECT dbo.ExchangeSecurities.Ticker as CUSIP, 
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
		//exit;
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
		show_array($arr_ioc);
		show_array($arr_ioc_mri_format);
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
	 
	 //($arr_mri_new);
	 //exit;


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
	 
			$zarr_lookback_new = array_merge($arr_recent_mri,$arr_ioc_mri_format); 
			
			asort($zarr_lookback_new);
			show_array($zarr_lookback_new);
			
	
	 $arr_recent_mri = $zarr_lookback_new;	
	 show_array($arr_recent_mri);
	 
	 
	 
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
									
									if ($comm_cents_per_share > 10) {
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
			$count_acv_nfs = 0;
			$arr_nfs_trades_to_merge = array();
			$query_nfs_trades = "SELECT 
													trad_advisor_code,
													max(trad_account_number) as trad_account_number,
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
			
		   //xdebug("query_nfs_trades",$query_nfs_trades);
	     $result_nfs_trades = mysql_query($query_nfs_trades) or die (tdw_mysql_error($query_nfs_trades));
				while ( $row_nfs_trades = mysql_fetch_array($result_nfs_trades)) 
				{

					if (in_array(trim($row_nfs_trades["trad_symbol"]).'^'.$row_nfs_trades["trad_rr"], $arr_acv) and substr(trim($row_nfs_trades["trad_account_number"]),0,3)=='PDZ') {
						$str_acv = 'YES';
						$count_acv_nfs = $count_acv_nfs + 1;
					} else {
						$str_acv = 'NO';
					}

					//incorporate BCM watch list condition here as well
					//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					//PROCESS OPTIONS
					$var_bcm_ticker = "";
					if ($row_nfs_trades["trad_advisor_code"] == 'BUCK' and stripos(trim($row_nfs_trades["trad_symbol"]), "+")!==false) { // or stripos($rec_to_process[0], " ")!==false
						$arr_get_option_detail = array();
						
						//###################################################################################3
						$str_option = ereg_replace("[^A-Za-z]", "", trim($row_nfs_trades["trad_symbol"]));
						$str_option = $str_option.".x";
			
						$str_company_detail = get_company_detail($str_option);
						$acd = explode("^", $str_company_detail);
						$str = $acd[0];
			
						if (substr($str,0,strpos($str," ")) != "") {
							$var_bcm_ticker = substr($str,0,strpos($str," "));
						  echo trim($row_oth_trades["oth_symbol"])." >> ".$var_bcm_ticker."\n";
						  ob_flush();
						  flush();
			
						} else {
							$var_bcm_ticker = "???";
						  echo trim($row_oth_trades["oth_symbol"])." >> ".$var_bcm_ticker."\n";
						  ob_flush();
						  flush();
						}
						//###################################################################################3
					} elseif ($row_nfs_trades["trad_advisor_code"] == 'BUCK' and stripos(trim($row_oth_trades["oth_symbol"]), "+")===false) {
							$var_bcm_ticker = trim($row_nfs_trades["trad_symbol"]);
					} else {
						$var_bcm_ticker = "XXXXXXXXXXXXXXX";
					}
					//xdebug("var_bcm_ticker",$var_bcm_ticker);
					//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

					//xdebug("row_nfs_trades['trad_symbol']",$row_nfs_trades["trad_symbol"]);

					$arr_nfs_trades_to_merge[$maint_count_nfs] =  trim($row_nfs_trades["trad_symbol"])."^". 	//0
																												$row_nfs_trades["trad_advisor_name"]."^". 	//1
																												$row_nfs_trades["trad_rr"]."^".           	//2
																												$row_nfs_trades["trad_buy_sell"]."^".     	//3
																												$row_nfs_trades["trad_quantity"]."^".     	//4
																												$row_nfs_trades["trade_price"]."^".       	//5
																												$row_nfs_trades["trad_commission"]."^".   	//6
																												$row_nfs_trades["trad_cents_per_share"]."^".//7
																												$row_nfs_trades["trad_advisor_code"]."^". 	//8
																												$row_nfs_trades["for_sum_trad_commission"]."^".//9
																												$str_acv."^". 	//10
																												substr(trim($row_nfs_trades["trad_account_number"]),0,3); //11
																												
					$maint_count_nfs = $maint_count_nfs + 1;
				}	

			 //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
			 //combine two arrays (this creates a new index)
			 $merged_array_trades = array();
			 $merged_array_trades = array_merge($arr_oth_trades_to_merge, $arr_oemp_trades_to_merge, $arr_nfs_trades_to_merge, $arr_fid_trades_to_merge);
			 $sorted_merged_array_trades = array();
			 $count_sorted = 0;
			 //sort the array
			 asort($merged_array_trades);
			 
	     //show_array($merged_array_trades);
			 foreach ($merged_array_trades as $key => $val) {
					 $sorted_merged_array_trades[$count_sorted] = $val;
					 $count_sorted = $count_sorted + 1;
			 }
			 
	     //show_array($sorted_merged_array_trades);
			 xdebug("count(sorted_merged_array_trades)",count($sorted_merged_array_trades));
			 //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&

			 //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
			 //WATCH LIST INCLUSION  The list now includes all trades, not just BCM.
			 // 0 = [AAPL^BUCK CAPITAL MGMT^ ^S^3,173^333.20^non-brg^2.0^NO_BCM_WATCH^0^NO]
				$count_bcm_watchlist = 0;			 
				$sorted_merged_array_trades_v2 = array();
				foreach ($sorted_merged_array_trades as $key=>$val) {
						$arr_temp = array();
						$arr_temp = explode("^",$val);
						if (in_array($arr_temp[0],$arr_bcm)) { // and $row_nfs_trades["trad_advisor_code"] == 'BUCK'
							$count_bcm_watchlist = $count_bcm_watchlist + 1;
							$sorted_merged_array_trades_v2[] = $val."^WATCHLIST";
						} else {
							$sorted_merged_array_trades_v2[] = $val."^NOWATCHLIST";
						}
				}
	     
			 //show_array($sorted_merged_array_trades_v2);
			 
			 //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	     //xdebug("count_acv_nfs",$count_acv_nfs);
			 $total_count_acv_events = $count_acv_nfs + $count_acv_ext;
	     //xdebug("total_count_acv_events",$total_count_acv_events);
			 
			 if ($total_count_acv_events == 0) {
			 $report_header_string = '<font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">
			 														<strong>Potential ANALYST TRADE Violations: 0</strong>
																</font>';
			 } else {
			 $report_header_string = '<font color="#FF0000" size="2" face="Verdana, Arial, Helvetica, sans-serif">
			 														<strong>Potential ANALYST TRADE Violations: '.$total_count_acv_events.'</strong>
																</font>';
			 }

			 if ($count_bcm_watchlist == 0) {
			 $report_header_string1 = '<font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">
			 															<strong>Watch List ACTIVITY ALERT: 0</strong>
																 </font>';
			 } else {
			 $report_header_string1 = '<font color="#FF00FF" size="2" face="Verdana, Arial, Helvetica, sans-serif">
			 															<strong>Watch List ACTIVITY ALERT: '.$count_bcm_watchlist.'</strong>
																 </font>';
			 }

	     //show_array($arr_nfs_trades_to_merge);


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
					<td valign="top" width="60"><img src="../../images/logo_small.gif" width="47"></td>
					<td valign="top" align="left" width="250">
						<font color="#333333" size="3" face="Arial, Helvetica, sans-serif"><b>&nbsp;Daily Compliance Activity Report</b></font>
						<br>
						<font color="#333333" size="2" face="Arial, Helvetica, sans-serif">
							&nbsp;Trade Date: '.format_date_ymd_to_mdy($trade_date_to_process).'
						</font>
					</td>
					<td width="360" valign="bottom" align="right" nowrap>
					'.$report_header_string.'					
					<br>
					'.$report_header_string1.'
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
					  str_replace(" ","&nbsp;",str_pad("RR#",5," ",1)).
					  str_replace(" ","&nbsp;",str_pad("Symbol",13," ",1)).
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
					  str_replace(" ","&nbsp;",str_pad("RR#",5," ",1)).
					  str_replace(" ","&nbsp;",str_pad("Symbol",13," ",1)).
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
			$count_processed_mri = 0;
			
			//foreach ($sorted_merged_array_trades as $key => $val) {
			foreach ($sorted_merged_array_trades_v2 as $key => $val) {
			

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

				//xdebug('$rec_to_process[0]',$rec_to_process[0]);
				//xdebug('show_mri',"$show_mri");
				
				if ($arr_rres_symbols[$rec_to_process[0]] == '') {
						$show_rres = 'n/a';
				} else {
						$arr_dateval = explode(' ',$arr_rres_symbols[$rec_to_process[0]]);
						$show_rres = $arr_dateval[1]."-".$arr_dateval[0];
				}
								
				//INIT
				$rowstrongstart = '';
				$rowstrongend = '';
				$fontcolor_mri = '';
				$fontcolor_mri_end = '';
				$fontcolor_rres = '';
				$fontcolor_rres_end = '';
				
				//Process here to account for Initiation of Coverage
				//} elseif (in_array($rec_to_process[0],$arr_ioc)) {
				//		$arr_dateval = explode(' ',$arr_recent_mri[$rec_to_process[0]]);
				//		$show_mri = $arr_dateval[1]."-".$arr_dateval[0]."[i]";
				
				if ($show_mri == $date_match_val or $show_rres == $date_match_val) {
				   if (!in_array($rec_to_process[0],$arr_recent_mri_trade_found) && $show_mri == $date_match_val) {
					 $arr_recent_mri_trade_found[$count_processed_mri] = $rec_to_process[0];
					 $count_processed_mri = $count_processed_mri + 1; 
					 }
						if ($show_mri == $date_match_val and $show_rres == $date_match_val) {
							$rowstrongstart = "<strong>";
							$rowstrongend = "</strong>";
							$fontcolor_mri = '<font color="red">';
							$fontcolor_mri_end = '</font>';
							$fontcolor_rres = '<font color="red">';
							$fontcolor_rres_end = '</font>';
						} elseif ($show_mri == $date_match_val and $show_rres != $date_match_val) {
							$rowstrongstart = "<strong>";
							$rowstrongend = "</strong>";
							$fontcolor_mri = '<font color="red">';
							$fontcolor_mri_end = '</font>';
							$fontcolor_rres = '';
							$fontcolor_rres_end = '';
						} elseif ($show_mri !== $date_match_val and $show_rres == $date_match_val) {
							$rowstrongstart = "<strong>";
							$rowstrongend = "</strong>";
							$fontcolor_mri = '';
							$fontcolor_mri_end = '';
							$fontcolor_rres = '<font color="red">';
							$fontcolor_rres_end = '</font>';
						} else {
							//do nothing
							echo '';
						}					
						//$rowcolor = "ff0000";
				} 
					
					//INIT
					$rowcolor = "000000";
					$rowstrongstart = "";
					$rowstrongend = "";
				
				if (
				substr($rec_to_process[2],0,2) != '09' && $rec_to_process[6] == '0.0' && in_array(trim($show_trad_advisor_name),$arr_acct_emp)) {
					$rowcolor = "0000ff";
					$rowstrongstart = "<strong>";
					$rowstrongend = "</strong>";
				} elseif (substr($rec_to_process[2],0,2) == '09' and $rec_to_process[11] == 'PDS') { 
					$rowcolor = "0000ff";
					$rowstrongstart = "<strong>";
					$rowstrongend = "</strong>";
					} elseif ($rec_to_process[2] == 'FID') {  // Fidelity Employee Trade
					$rowcolor = "800000";
					$rowstrongstart = "<strong>";
					$rowstrongend = "</strong>";
					} elseif ($rec_to_process[11] == 'PDZ') { 
					$rowcolor = "0000ff";
					$rowstrongstart = "<strong>";
					$rowstrongend = "</strong>";
				} elseif (trim($show_trad_advisor_name) == 'BUCK CAPITAL MGMT' and $rec_to_process[8] != 'BCM_WATCH' and $rec_to_process[10] != 'BCM_WATCH') {
					$rowcolor = "0000ff";
					$rowstrongstart = "<strong>";
					$rowstrongend = "</strong>";
				} elseif ($rec_to_process[11] == 'WATCHLIST' or $rec_to_process[12] == 'WATCHLIST') {
					$rowcolor = "000000";
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

				
				//section for type
				if ($rec_to_process[6] == 'non-brg') {
						$show_type = 'ß';
				} elseif ($rec_to_process[8] == '#outside_employee_trade#') {
						$show_type = 'Õ';
				} elseif ($rec_to_process[8] == '#outside_fidelity_trade#') {
						$show_type = 'f';
				} else {
						$show_type = ' ';
				}  
				
				//echo "&fnof;"; ƒ

//				
				if ($count_page == 1) {
						if ($count_row_trades != 0 && $count_row_trades % 2 == 0) {
							if ($count_row_trades % 28 == 0) {
									$data_to_html_file .=  '<!-- NEW PAGE -->';
									$data_to_html_file .= $heading_row;
									//echo "page break added >> ".$count_row_trades."<br>";
						      $count_page = 2;
									$count_row_trades = 1;
							}
						} elseif ($count_row_trades != 0 && $count_row_trades % 2 != 0) {
								if ($count_row_trades % 29 == 0) {
									$data_to_html_file .=  '<!-- NEW PAGE -->';
									$data_to_html_file .= $heading_row;
									//echo "page break added >> ".$count_row_trades."<br>";
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
								//echo "page break added >> ".$count_row_trades."<br>";
							$count_row_trades = 1;
							}
						} elseif ($count_row_trades != 0 && $count_row_trades % 2 != 0) {
							if ($count_row_trades != 0 && $count_row_trades % 31 == 0) {
							$data_to_html_file .=  '<!-- NEW PAGE -->';
							$data_to_html_file .= $heading_row;
								//echo "page break added >> ".$count_row_trades."<br>";
							$count_row_trades = 1;
							}
						} else {
						//do nothing
						}
				}
				

				//THIS PIECE IS JUST TO SHOW THE MRI DATE WITH A [i]
				if (in_array($rec_to_process[0], $arr_ioc)) {
					$arr_dateval = explode(' ',$arr_recent_mri[$rec_to_process[0]]);
					$show_mri = $arr_dateval[1]."-".$arr_dateval[0]."[i]";
				}
				
        //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
				//PROCESS OPTIONS
				$var_show_ticker = "";
				if (stripos($rec_to_process[0], "+")!==false) { // or stripos($rec_to_process[0], " ")!==false
					$arr_get_option_detail = array();
					
					//###################################################################################3
					$str_option = ereg_replace("[^A-Za-z]", "", trim($rec_to_process[0]));
					$str_option = $str_option.".x";
		
					$str_company_detail = get_company_detail($str_option);
					$acd = explode("^", $str_company_detail);
					$str = $acd[0];
		
					if (substr($str,0,strpos($str," ")) != "") {
					  $var_show_ticker = $rec_to_process[0]."[".substr($str,0,strpos($str," "))."]";
					  echo trim($rec_to_process[0])." >> ".substr($str,0,strpos($str," "))."\n";
					  ob_flush();
					  flush();
		
					} else {
					  xdebug("Symbol", $rec_to_process[0].">>"."ERROR");
					  $var_show_ticker = $rec_to_process[0]."[???]";
					  echo trim($rec_to_process[0])." >> "."ERROR"."\n";
					  ob_flush();
					  flush();
					}
					//###################################################################################3

				} else {
					$var_show_ticker = $rec_to_process[0];
				}

				if ($rec_to_process[11] == 'NOWATCHLIST') {
				$str_bgcolor = " bgcolor='ffffff'";
				} elseif ($rec_to_process[11] == 'WATCHLIST' || $rec_to_process[12] == 'WATCHLIST') { // or $rec_to_process[10] == 'BCM_WATCH'
				$str_bgcolor = " bgcolor='FFA8FF'";
				} else {
				$str_bgcolor = " bgcolor='ffffff'";
				}
        //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
				
				if (trim($hold_symbol) == trim($rec_to_process[0])) {
				//Õ  ß
				
						$data_to_html_file .= '
											 <tr'.$str_bgcolor.'>
													<td><font color="#'.$rowcolor.'" size="2" face="Courier">'.$rowstrongstart.
													str_replace(" ","&nbsp;",str_pad($show_type,4," ",1)).
													str_replace(" ","&nbsp;",str_pad($show_trad_advisor_name,26," ",1)).
													str_replace(" ","&nbsp;",str_pad($rec_to_process[2],5," ",1)).
													str_replace(" ","&nbsp;",str_pad($var_show_ticker,13," ",1)).
													str_replace(" ","&nbsp;",str_pad(offset_buy_sell_space($rec_to_process[3]),5," ",1)).
													str_replace(" ","&nbsp;",str_pad($rec_to_process[4],10," ",0)).
													str_replace(" ","&nbsp;",str_pad($rec_to_process[5],8," ",0)).
													$fontcolor_mri.str_replace(" ","&nbsp;",str_pad($show_mri,14," ",0)).$fontcolor_mri_end.
													$fontcolor_rres.str_replace(" ","&nbsp;",str_pad($show_rres,10," ",0)).$fontcolor_rres_end.
													str_replace(" ","&nbsp;",str_pad($rec_to_process[6],14," ",0)).
													str_replace(" ","&nbsp;",str_pad($rec_to_process[7],6," ",0)).
													$rowstrongend.'</font></td>
												</tr>';
													//str_replace(" ","&nbsp;",str_pad($rec_to_process[0],10," ",1)).
												  //$var_show_ticker
												$count_row_trades = $count_row_trades + 1;
												
				} else {

						$data_to_html_file .= '
											 <tr>
													<td><font color="#000000" size="2" face="Courier">&nbsp;</font></td>
												</tr>';
						$data_to_html_file .= '
											 <tr'.$str_bgcolor.'>
													<td><font color="#'.$rowcolor.'" size="2" face="Courier">'.$rowstrongstart.
													str_replace(" ","&nbsp;",str_pad($show_type,4," ",1)).
													str_replace(" ","&nbsp;",str_pad($show_trad_advisor_name,26," ",1)).
													str_replace(" ","&nbsp;",str_pad($rec_to_process[2],5," ",1)).
													str_replace(" ","&nbsp;",str_pad($var_show_ticker,13," ",1)).
													str_replace(" ","&nbsp;",str_pad(offset_buy_sell_space($rec_to_process[3]),5," ",1)).
													str_replace(" ","&nbsp;",str_pad($rec_to_process[4],10," ",0)).
													str_replace(" ","&nbsp;",str_pad($rec_to_process[5],8," ",0)).
													$fontcolor_mri.str_replace(" ","&nbsp;",str_pad($show_mri,14," ",0)).$fontcolor_mri_end.
													$fontcolor_rres.str_replace(" ","&nbsp;",str_pad($show_rres,10," ",0)).$fontcolor_rres_end.
													str_replace(" ","&nbsp;",str_pad($rec_to_process[6],14," ",0)).
													str_replace(" ","&nbsp;",str_pad($rec_to_process[7],6," ",0)).
													$rowstrongend.'</font></td>
												</tr>';
																
												$count_row_trades = $count_row_trades + 2;
						

				}
				
				//release the color scheme for mri/rres
				$fontcolor_mri = '';
				$fontcolor_mri_end = '';
				$fontcolor_rres = '';
				$fontcolor_rres_end = '';

				
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
xdebug("store_running_trad_commission_total",$store_running_trad_commission_total);

//===================================================================
	//exit;
//===================================================================

show_array($arr_recent_mri_trade_found);

if (count($arr_recent_mri_trade_found) > 0 ) {
	foreach ($arr_recent_mri_trade_found as $key => $val) {
	$str_show_mri_action .= ', <font color="#FF0000" size="3" face="Courier"><strong>'.$val.'</strong></font>'; 
	}
	$str_show_mri_action = substr($str_show_mri_action, 2, 5000);
} else {
  $str_show_mri_action = '<font color="#000000" size="3" face="Courier"><strong>NONE</strong></font>';
}

echo  $str_show_mri_action."<br>\n\n";

$str_show_no_mri_action = "";		
$count_no_mri_action = 0;				
foreach ($arr_recent_mri as $key => $val) {
$arr_dateval = explode(' ',$val);
  if ($arr_dateval[1]."-".$arr_dateval[0] == $date_match_val){ 
   if (!in_array($key,$arr_recent_mri_trade_found)) { 
	 //echo  $key.">>".$val."<br>";
	 $count_no_mri_action = $count_no_mri_action + 1;
	 $str_show_no_mri_action .= ', <font color="#FF0000" size="3" face="Courier"><strong>'.$key.'</strong></font>'; //", <u>". $key . "</u> (". ucwords(strtolower(get_company_name($key))) . ")";
	 }
	}
}

if ($count_no_mri_action == 0) {
	$str_show_no_mri_action = '<font color="#000000" size="3" face="Courier"><strong>NONE</strong></font>';
} else {
$str_show_no_mri_action = substr($str_show_no_mri_action, 2, 5000);
}


echo  $str_show_no_mri_action."<br>\n\n";
//exit;
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// NEW SECTION (WITH PAGE BREAK)
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++			 
				$data_to_html_file .=  '<!-- NEW PAGE -->';

							//FLUSH temp tables
							$result_flush = mysql_query("truncate table tmp_mry_cmpl_temp") or die (mysql_error());
							$result_flush = mysql_query("truncate table tmp_mry_cmpl_trades") or die (mysql_error());
							echo "tmp_mry_cmpl_temp and tmp_mry_cmpl_trades are flushed and ready for the next set of data<br>\n\n";

							//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++						
						  //get only regular trades, not the cancelled trades, the cancelled trades will be 
							//processed in a separate section at the end of segment	
								$query_trades = "SELECT * 
																 FROM nfs_trades
																 WHERE trad_run_date <> trad_trade_date
																 AND trad_run_date = '".$trade_date_to_process."'";
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
									$comm_canceled_combined_ref =   trim($row_trades["trad_canceled_combined_ref"]);
									
									if ($row_trades["trad_commission_concession_code"] == 3) { //This indicates cents/share
										$comm_cents_per_share = $row_trades["trad_trade_commission"]/$row_trades["trad_quantity"];
									} else {
										$comm_cents_per_share = 0;
									}
									
									if ($comm_cents_per_share > 10) {
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
																			comm_correction_code,
																			comm_canceled_combined_ref)
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
																			"'".$comm_correction_code."',".
																			"'".$comm_canceled_combined_ref."')";
																			
									$result_insert_trade = mysql_query($qry_insert_trade) or die(tdw_mysql_error($qry_insert_trade));
									$countval = $countval + 1;
									}
								}
								echo "Data inserted to temporary table for further processing.<br>\n\n";
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
																								trad_correction_code,
																								trad_canceled_combined_ref
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
																								"'".$row_comm_trd["comm_correction_code"]."',".
																								"'".$row_comm_trd["comm_canceled_combined_ref"]."')";
												$result_insert_trade = mysql_query($qyery_insert_trade) or die(tdw_mysql_error($qyery_insert_trade));
																								
											}
								}

			//There is a know issue that since some clients have multiple RRs, e.g. GART the data shown gets max(rr)
			//which means the totals will be accurate but the rr agains the client will be inaccurate.
			
			//fixing the query (excel) to account for the incorrect subtotals by rr (carol)
			
			$query_trades = "SELECT 
													trad_advisor_code,
													max(trad_advisor_name) as trad_advisor_name,
													trad_symbol,
													trad_buy_sell,
													DATE_FORMAT(trad_trade_date,'%m/%d') as show_trade_date,
													DATE_FORMAT(trad_trade_date,'%m/%d/%Y') as trad_trade_date,
													DATE_FORMAT(trad_run_date,'%m/%d/%Y') as trad_run_date,
													max(trad_advisor_name),
													FORMAT(sum(trad_quantity),0) as trad_quantity,
													FORMAT(avg(trade_price),2) as trade_price,
													sum(trad_commission) as trad_commission,
													sum(trad_commission) as for_sum_trad_commission,
													FORMAT(avg(trad_cents_per_share)*100,1) as trad_cents_per_share,
													trad_rr,
													trad_is_cancelled,
													trad_correction_code,
													trad_canceled_combined_ref
												FROM tmp_mry_cmpl_trades
												GROUP BY  trad_advisor_code, trad_symbol, trad_buy_sell, trad_run_date, trad_trade_date, trad_rr, trad_is_cancelled, trad_correction_code 
												ORDER BY trad_symbol, trad_advisor_name, trad_buy_sell, trad_trade_date";
		
		//xdebug("query_trades",$query_trades);
	

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
				
				//xdebug("row_trades[trad_advisor_name]",$row_trades["trad_advisor_name"]);
				//xdebug("row_trades[trad_advisor_code]",$row_trades["trad_advisor_code"]);

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
				
				//INIT
				$rowstrongstart = '';
				$rowstrongend = '';
				$fontcolor_mri = '';
				$fontcolor_mri_end = '';
				$fontcolor_rres = '';
				$fontcolor_rres_end = '';

				/*
				if ($show_mri == $date_match_val or $show_rres == $date_match_val) {
						if ($show_mri == $date_match_val and $show_rres == $date_match_val) {
							$rowstrongstart = "<strong>";
							$rowstrongend = "</strong>";
							$fontcolor_mri = '<font color="red">';
							$fontcolor_mri_end = '</font>';
							$fontcolor_rres = '<font color="red">';
							$fontcolor_rres_end = '</font>';
						} elseif ($show_mri == $date_match_val and $show_rres != $date_match_val) {
							$rowstrongstart = "<strong>";
							$rowstrongend = "</strong>";
							$fontcolor_mri = '<font color="red">';
							$fontcolor_mri_end = '</font>';
							$fontcolor_rres = '';
							$fontcolor_rres_end = '';
						} elseif ($show_mri !== $date_match_val and $show_rres == $date_match_val) {
							$rowstrongstart = "<strong>";
							$rowstrongend = "</strong>";
							$fontcolor_mri = '';
							$fontcolor_mri_end = '';
							$fontcolor_rres = '<font color="red">';
							$fontcolor_rres_end = '</font>';
						} else {
							//do nothing
							echo '';
						}					
						//$rowcolor = "ff0000";
				}
				*/ 
					
					//INIT
					$rowcolor = "000000";
					$rowstrongstart = "";
					$rowstrongend = "";

					if (substr($row_trades["trad_rr"],0,2) != '09' && $row_trades["trad_commission"] == '0.0' && in_array($show_trad_advisor_name,$arr_acct_emp) ) {
						$rowcolor = "0000ff";
						$rowstrongstart = "<strong>";
						$rowstrongend = "</strong>";
					} elseif (substr($row_trades["trad_rr"],0,2) == '09') { 
						$rowcolor = "0000ff";
						$rowstrongstart = "<strong>";
						$rowstrongend = "</strong>";
					} elseif (trim($show_trad_advisor_name) == 'BUCK CAPITAL MGMT') {
						$rowcolor = "0000ff";
						$rowstrongstart = "<strong>";
						$rowstrongend = "</strong>";
					} else {
						$rowcolor = "000000";
						$rowstrongstart = "";
						$rowstrongend = "";
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
					if ($row_trades["trad_is_cancelled"] == 0 && $row_trades["trad_correction_code"] != "5" && $row_trades["trad_canceled_combined_ref"] != "") {
					$str_type .= "CRT";
					}
					
					//===================================================================================
				
				
				


					if ($count_page == 1) {
							if ($count_row_trades != 0 && $count_row_trades % 2 == 0) {
								if ($count_row_trades % 28 == 0) {
									$data_to_html_file .=  '<!-- NEW PAGE -->';
									$data_to_html_file .= $heading_row;
									//echo "page break added >> ".$count_row_trades."<br>";
										$count_page = 2;
									$count_row_trades = 1;
								}
							} elseif ($count_row_trades != 0 && $count_row_trades % 2 != 0) {
									if ($count_row_trades % 29 == 0) {
										$data_to_html_file .=  '<!-- NEW PAGE -->';
										$data_to_html_file .= $heading_row;
										//echo "page break added >> ".$count_row_trades."<br>";
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
									//echo "page break added >> ".$count_row_trades."<br>";
								$count_row_trades = 1;
								}
							} elseif ($count_row_trades != 0 && $count_row_trades % 2 != 0) {
								if ($count_row_trades != 0 && $count_row_trades % 31 == 0) {
								$data_to_html_file .=  '<!-- NEW PAGE -->';
								$data_to_html_file .= $heading_row;
									//echo "page break added >> ".$count_row_trades."<br>";
								$count_row_trades = 1;
								}
							} else {
							//do nothing
							}
					}
							
							
							if ($str_type != "") {
															$name_to_show = trim($show_trad_advisor_name); //." (".trim($row_trades["trad_account_name"]).")";
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
																						$fontcolor_mri.str_replace(" ","&nbsp;",str_pad($show_mri,8," ",0)).$fontcolor_mri_end.
																						$fontcolor_rres.str_replace(" ","&nbsp;",str_pad($show_rres,8," ",0)).$fontcolor_rres_end.
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
																						str_replace(" ","&nbsp;",str_pad(number_format($store_running_trad_commission_total,2,'.',','),75," ",0)).
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
																						str_replace(" ","&nbsp;",str_pad(number_format(($running_aocxl_sum+$store_running_trad_commission_total),2,'.',','),71," ",0)).
																						'</strong></font></td>
																					</tr>												
																					<tr>
																						<td valign="bottom"><img src="../../images/border_a.png" width="670" height="5"></td>
																					<tr></table>';
															

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// BEGIN SECTION (WITH PAGE BREAK) BCM AS OF AND CANCEL/CORRECT
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++			 

							//FLUSH temp tables
							$result_flush = mysql_query("truncate table tmp_mry_cmpl_bcm_asof") or die (mysql_error());
							//$result_flush = mysql_query("truncate table tmp_mry_cmpl_trades") or die (mysql_error());
							echo "tmp_mry_cmpl_temp and tmp_mry_cmpl_trades are flushed and ready for the next set of data<br>\n\n";

							//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++						
						  //get only regular trades, not the cancelled trades, the cancelled trades will be 
							//processed in a separate section at the end of segment	
								$query_trades = "SELECT * 
																 FROM oth_other_trades      
																 WHERE oth_trade_date <> '".$trade_date_to_process."'
																 AND oth_process_date = '".$trade_date_to_process."'";
																 //AND trad_cancel_code != '1'"; //AND trad_branch = 'PDY'
	  						//xdebug ("query_trades",$query_trades);
  							//exit;
								$result_trades = mysql_query($query_trades) or die(tdw_mysql_error($query_trades));
								$countval = 1;
								while($row_trades = mysql_fetch_array($result_trades))
								{
									//get data to insert into temp table to process further
									//auto_id  oth_trade_date  oth_process_date  oth_original_trade_id  oth_broker  oth_buysell  oth_symbol  
									//oth_quantity  oth_price  oth_commission  oth_net_money  oth_trade_time  oth_pm_code  oth_emp_client  
									//oth_emp_alloc  oth_trade_id  oth_trade_ts  oth_first_exec  oth_last_exec  oth_isactive
									 
									$comm_advisor_name           = "BUCK CAPITAL MGMT";
									$comm_symbol                 = trim($row_trades["oth_symbol"]);
									$comm_buy_sell               = trim($row_trades["oth_buysell"]);
									$comm_quantity               = round($row_trades["oth_quantity"],0);
									$comm_price                  = $row_trades["oth_price"];
									$comm_trade_date             = $row_trades["oth_trade_date"];
									$comm_run_date               = $row_trades["oth_process_date"];
									$comm_commission             = $row_trades["oth_commission"];
									$comm_advisor_code           = $row_trades["oth_broker"];			
									
									$comm_oth_trade_id          = $row_trades["oth_trade_id"];
									$comm_orig_trade_id          = $row_trades["oth_original_trade_id"];
															
									$comm_cents_per_share = $row_trades["oth_commission"]/$row_trades["oth_quantity"];
									
									if ($comm_cents_per_share > 10) {
									  $comm_cents_per_share = 0;
									}
							
									$qry_insert_trade = "insert into tmp_mry_cmpl_bcm_asof (
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
																			comm_correction_code,
																			comm_canceled_combined_ref)
																			values(".
																			"'".$comm_oth_trade_id."',".
																			"'',".
																			"'".$comm_trade_date."',". 
																			"'".$comm_run_date."',". 
																			"'".$comm_advisor_code."',". 
																			"'".$comm_advisor_name."',". 
																			"'".$comm_account_name."',". 
																			"'',". 
																			"'".$comm_symbol."',". 
																			"'".$comm_buy_sell."',".
																			"'".$comm_quantity."',". 
																			"'".$comm_price."',". 
																			"'',". 
																			"'".$comm_commission."',". 
																			"'".$comm_cents_per_share."',".
																			"'',".
																			"'',".
																			"'".$comm_orig_trade_id."')";
																			
									$result_insert_trade = mysql_query($qry_insert_trade) or die(tdw_mysql_error($qry_insert_trade));
									$countval = $countval + 1;
								}
								echo "Data inserted to temporary table for further processing.<br>\n\n";
							
							//// Processing from temporary table.
							
			//There is a know issue that since some clients have multiple RRs, e.g. GART the data shown gets max(rr)
			//which means the totals will be accurate but the rr agains the client will be inaccurate.
			//fixing the query (excel) to account for the incorrect subtotals by rr (carol)
			//xdebug("query_trades",$query_trades);
			//exit;
		
		$data_to_html_file .= '<!-- NEW PAGE -->
			<style type="text/css">
			<!--
			.data_black {font-family: "Courier New", Courier, mono;	font-size: 10px;	color: #000000;}
			-->
			</style>
			<table width="670" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td><font color="#000000" size="3" face="Courier"><strong><u>BCM Cancel/Corrects and As-Of Trades</u></strong><br></font></td>
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
					  str_replace(" ","&nbsp;",str_pad("Symbol",14," ",1)).
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
					  str_replace(" ","&nbsp;",str_pad("Symbol",14," ",1)).
					  str_replace(" ","&nbsp;",str_pad("B/S",6," ",1)).
					  str_replace(" ","&nbsp;",str_pad("Shares",10," ",0)).
					  str_replace(" ","&nbsp;",str_pad("Price",8," ",0)).
					  str_replace(" ","&nbsp;",str_pad(" MRI ",8," ",0)).
					  str_replace(" ","&nbsp;",str_pad("Research",8," ",0)).
					  str_replace(" ","&nbsp;",str_pad("Comm.",9," ",0)).
					  str_replace(" ","&nbsp;",str_pad("&cent;/Shr.",6," ",0)).
					'</strong></font></td>
				</tr>';

			$query_trades = "SELECT 
													comm_advisor_code as trad_advisor_code,
													comm_advisor_name as trad_advisor_name,
													comm_symbol as trad_symbol,
													comm_buy_sell as trad_buy_sell,
													DATE_FORMAT(comm_trade_date,'%m/%d') as show_trade_date,
													DATE_FORMAT(comm_trade_date,'%m/%d/%Y') as trad_trade_date,
													DATE_FORMAT(comm_run_date,'%m/%d/%Y') as trad_run_date,
													FORMAT(comm_quantity,0) as trad_quantity,
													FORMAT(comm_price,2) as trad_price,
													comm_commission as trad_commission,
													comm_commission as for_sum_trad_commission,
													FORMAT((comm_cents_per_share)*100,1) as trad_cents_per_share,
													comm_rr as trad_rr,
													comm_correction_code as trad_correction_code,
													comm_canceled_combined_ref as trad_canceled_combined_ref
												FROM tmp_mry_cmpl_bcm_asof order by comm_symbol";		
			$result_trades = mysql_query($query_trades) or die(tdw_mysql_error($query_trades));
			
			$count_row_trades = 0;
			$count_page = 1;
			$running_trad_commission_total = 0;
			$hold_symbol = "";
			while($row_trades = mysql_fetch_array($result_trades))
			{

				if ($row_trades["trad_advisor_name"] == '') {
					$show_trad_advisor_name = $row_trades["trad_advisor_code "];
				} else {
					$show_trad_advisor_name = $row_trades["trad_advisor_name"];
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
				
				//INIT
				$rowstrongstart = '';
				$rowstrongend = '';
				$fontcolor_mri = '';
				$fontcolor_mri_end = '';
				$fontcolor_rres = '';
				$fontcolor_rres_end = '';
					
					//INIT
					$rowcolor = "000000";
					$rowstrongstart = "";
					$rowstrongend = "";

					if (trim($show_trad_advisor_name) == 'BUCK CAPITAL MGMT') {
						$rowcolor = "0000ff";
						$rowstrongstart = "<strong>";
						$rowstrongend = "</strong>";
					} else {
						$rowcolor = "000000";
						$rowstrongstart = "";
						$rowstrongend = "";
					}

					//===================================================================================
					// TRADE TYPE STRING (CXL, CRT, ASOF)   //comm_trade_date  comm_run_date  comm_canceled_combined_ref 
					//===================================================================================
					$var_process_cancel = 0;

					echo $row_trades["trad_canceled_combined_ref"]." >> ".$row_trades["trad_trade_date"]." >> ".$row_trades["trad_run_date"]." <br> ";
					
					$str_type = "";
					if ($row_trades["trad_trade_date"] != $row_trades["trad_run_date"] && trim($row_trades["trad_canceled_combined_ref"]) == "") {
					$str_type .= "AO";
					}
					if (trim($row_trades["trad_canceled_combined_ref"]) != "") {
					$str_type .= "CRT";
					$var_process_cancel = 1;
					}

					xdebug("str_type",$str_type);
					
					//===================================================================================

					if ($count_page == 1) {
							if ($count_row_trades != 0 && $count_row_trades % 2 == 0) {
								if ($count_row_trades % 28 == 0) {
									$data_to_html_file .=  '<!-- NEW PAGE -->';
									$data_to_html_file .= $heading_row;
									//echo "page break added >> ".$count_row_trades."<br>";
										$count_page = 2;
									$count_row_trades = 1;
								}
							} elseif ($count_row_trades != 0 && $count_row_trades % 2 != 0) {
									if ($count_row_trades % 29 == 0) {
										$data_to_html_file .=  '<!-- NEW PAGE -->';
										$data_to_html_file .= $heading_row;
										//echo "page break added >> ".$count_row_trades."<br>";
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
									//echo "page break added >> ".$count_row_trades."<br>";
								$count_row_trades = 1;
								}
							} elseif ($count_row_trades != 0 && $count_row_trades % 2 != 0) {
								if ($count_row_trades != 0 && $count_row_trades % 31 == 0) {
								$data_to_html_file .=  '<!-- NEW PAGE -->';
								$data_to_html_file .= $heading_row;
									//echo "page break added >> ".$count_row_trades."<br>";
								$count_row_trades = 1;
								}
							} else {
							//do nothing
							}
					}
							
							
							if ($str_type != "") {
															$name_to_show = trim($show_trad_advisor_name); //." (".trim($row_trades["trad_account_name"]).")";
															//IMPORTANT
															//echo $name_to_show."<br>";

													//get the appropriate original trade to show here 
													$str_cxl_append = "";
													if ($var_process_cancel == 1) {
														$qry = "select *, DATE_FORMAT(oth_trade_date,'%m/%d') as show_trade_date from oth_other_trades where oth_trade_id = '".$row_trades["trad_canceled_combined_ref"]."'";
														$res = mysql_query($qry) or die(tdw_mysql_error($qry));
														while ($row = mysql_fetch_array($res)) {
															
															$str_cxl_append = '
																					<tr>
																						<td><font color="#'.$rowcolor.'" size="2" face="Courier">'.$rowstrongstart.
																						str_replace(" ","&nbsp;",str_pad("CXL",8," ",1)).
																						str_replace(" ","&nbsp;",str_pad($row["show_trade_date"],7," ",1)).
																						str_replace(" ","&nbsp;",str_pad("BUCK CAPITAL MGMT.",38," ",1)).
																						str_replace(" ","&nbsp;",str_pad($row["oth_symbol"],14," ",1)).
																						str_replace(" ","&nbsp;",str_pad(offset_buy_sell_space($row["oth_buysell"]),5," ",1)).
																						str_replace(" ","&nbsp;",str_pad(number_format($row["oth_quantity"],0,".",","),10," ",0)).
																						str_replace(" ","&nbsp;",str_pad($row["oth_price"],8," ",0)).
																						$fontcolor_mri.str_replace(" ","&nbsp;",str_pad($show_mri,8," ",0)).$fontcolor_mri_end.
																						$fontcolor_rres.str_replace(" ","&nbsp;",str_pad($show_rres,8," ",0)).$fontcolor_rres_end.
																						str_replace(" ","&nbsp;",str_pad($row["oth_commission"],9," ",0)).
																						str_replace(" ","&nbsp;",str_pad(" ",6," ",0)).
																						$rowstrongend.'</font></td>
																					</tr>';
														}
													}


													if (trim($hold_symbol) == trim($row_trades["trad_symbol"])) {
																										    
															$data_to_html_file .= $str_cxl_append . '
																				 <tr>
																						<td><font color="#'.$rowcolor.'" size="2" face="Courier">'.$rowstrongstart.
																						str_replace(" ","&nbsp;",str_pad($str_type,8," ",1)).
																						str_replace(" ","&nbsp;",str_pad($row_trades["show_trade_date"],7," ",1)).
																						str_replace(" ","&nbsp;",str_pad($name_to_show,38," ",1)).
																						str_replace(" ","&nbsp;",str_pad($row_trades["trad_symbol"],14," ",1)).
																						str_replace(" ","&nbsp;",str_pad(offset_buy_sell_space($row_trades["trad_buy_sell"]),5," ",1)).
																						str_replace(" ","&nbsp;",str_pad($row_trades["trad_quantity"],10," ",0)).
																						str_replace(" ","&nbsp;",str_pad($row_trades["trad_price"],8," ",0)).
																						$fontcolor_mri.str_replace(" ","&nbsp;",str_pad($show_mri,8," ",0)).$fontcolor_mri_end.
																						$fontcolor_rres.str_replace(" ","&nbsp;",str_pad($show_rres,8," ",0)).$fontcolor_rres_end.
																						str_replace(" ","&nbsp;",str_pad($row_trades["trad_commission"],9," ",0)).
																						str_replace(" ","&nbsp;",str_pad($row_trades["trad_cents_per_share"],6," ",0)).
																						$rowstrongend.'</font></td>
																					</tr>';
																					$count_row_trades = $count_row_trades + 1;
																														
													} else {
															$data_to_html_file .= '
																				 <tr>
																						<td><font color="#000000" size="2" face="Courier">&nbsp;</font></td>
																					</tr>';
															$data_to_html_file .= $str_cxl_append . '
																				 <tr>
																						<td><font color="#'.$rowcolor.'" size="2" face="Courier">'.$rowstrongstart.
																						str_replace(" ","&nbsp;",str_pad($str_type,8," ",1)).
																						str_replace(" ","&nbsp;",str_pad($row_trades["show_trade_date"],7," ",1)).
																						str_replace(" ","&nbsp;",str_pad($name_to_show,38," ",1)).
																						str_replace(" ","&nbsp;",str_pad($row_trades["trad_symbol"],14," ",1)).
																						str_replace(" ","&nbsp;",str_pad(offset_buy_sell_space($row_trades["trad_buy_sell"]),5," ",1)).
																						str_replace(" ","&nbsp;",str_pad($row_trades["trad_quantity"],10," ",0)).
																						str_replace(" ","&nbsp;",str_pad($row_trades["trad_price"],8," ",0)).
																						str_replace(" ","&nbsp;",str_pad($show_mri,8," ",0)).
																						str_replace(" ","&nbsp;",str_pad($show_rres,8," ",0)).
																						str_replace(" ","&nbsp;",str_pad($row_trades["trad_commission"],9," ",0)).
																						str_replace(" ","&nbsp;",str_pad($row_trades["trad_cents_per_share"],6," ",0)).
																						$rowstrongend.'</font></td>
																					</tr>';
																									
																					$count_row_trades = $count_row_trades + 2;
													}
							}
				$hold_symbol = $row_trades["trad_symbol"];
			}
			
  									$data_to_html_file .=	'<tr>
																						<td valign="top"><img src="../../images/border_a.png" width="670" height="5"></td>
																					<tr>
																					';
																					
										$data_to_html_file .=	'<tr><td>&nbsp;</td></tr><tr><td>&nbsp;</td></tr><tr><td>&nbsp;</td></tr>
																					</table>';
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// END SECTION (WITH PAGE BREAK) BCM AS OF AND CANCEL/CORRECT
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++			 


				//INSERT MRI TRADE SYMBOLS
				$data_to_html_file .=	'
											<table>
											<tr>
												<td>
												<br>
												<table border="1" width="665">
													<tr>
														<td>
														<font color="#000000" size="3" face="Courier"><strong>
												    MRI Securities with Trade Activity on '.$date_match_val.'.</strong><br>
														'.$str_show_mri_action	.'
														</td>
													</tr>
												</table>
												</td>
											</tr>';

				//INSERT NO MRI TRADE SYMBOLS
				$data_to_html_file .=	'
											<tr>
												<td>
												<br>
												<table border="1" width="665">
													<tr>
														<td>
														<font color="#000000" size="3" face="Courier"><strong>
												    MRI Securities without Trade Activity on '.$date_match_val.'.</strong><br>
														'.$str_show_no_mri_action.'
														</td>
													</tr>
												</table>
												</td>
											</tr>';

				//INSERT PERCENT %BUCK IN BCM TRADES
				
				$start_date_for_ytd = substr($trade_date_to_process, 0, 4)."-01-01";
				
				function single_val_returns($qry, $col=null) {
					$result = mysql_query($qry) or die (tdw_mysql_error($qry));
					while ( $row = mysql_fetch_array($result) ) 
					{
						$returnval = $row["singleval"];
					}
					return $returnval;
				}
			
				$qry_ytd_buck = "SELECT sum(oth_commission) as singleval
										FROM oth_other_trades
										WHERE oth_trade_date between '".$start_date_for_ytd."' and '".$trade_date_to_process."'
										and oth_broker = 'BUCK'";
										
				$qry_ytd_all = "SELECT sum(oth_commission) as singleval
										FROM oth_other_trades
										WHERE oth_trade_date between '".$start_date_for_ytd."' and '".$trade_date_to_process."'";
										
				$qry_pbd_buck = "SELECT sum(oth_commission) as singleval 
										FROM oth_other_trades
										WHERE oth_trade_date = '".$trade_date_to_process."'
										and oth_broker = 'BUCK'";
										
				$qry_pbd_all = "SELECT sum(oth_commission) as singleval 
										FROM oth_other_trades
										WHERE oth_trade_date = '".$trade_date_to_process."'";
										
										
				xdebug("YTD", round((single_val_returns($qry_ytd_buck)*100)/single_val_returns($qry_ytd_all),0)."%");	
			
				xdebug("T-Date : (".format_date_ymd_to_mdy($trade_date_to_process).")", round((single_val_returns($qry_pbd_buck)*100)/single_val_returns($qry_pbd_all),0)."%");	
			
				
				$data_to_html_file .=	'
											<tr>
												<td>
												<br>
												<table border="1" width="665">
													<tr>
														<td>
														<font color="#000000" size="3" face="Courier"><strong>
														BRG comm. as a % of BCM Total Comm. paid.<br>
														&nbsp;&nbsp;T-Date = '.round((single_val_returns($qry_pbd_buck)*100)/single_val_returns($qry_pbd_all),0).'%<br>
														&nbsp;&nbsp;YTD&nbsp;&nbsp;&nbsp; = '.round((single_val_returns($qry_ytd_buck)*100)/single_val_returns($qry_ytd_all),0).'%
														</strong></font>
														</td>
													</tr>
												</table>
												</td>
											</tr>';

				
				
				
				$data_to_html_file .=	'
											<tr>
												<td>&nbsp;</td>
											</tr>
											</table>';
			
			
			
			
				$data_to_html_file .=  '<!-- NEW PAGE -->';
				$data_to_html_file .=	'
											<table width="670" border="1" cellspacing="0" cellpadding="1" bordercolor="#CCCCCC">
												<tr>
													<td colspan="2">
													<font color="#000000" size="3" face="Courier"><strong><u>Legend:</u></strong><br></font>
													</td>
												</tr>
												<tr>
													<td colspan="2">
													<font color="#000000" size="3" face="Courier"><strong>Õ: Employee Trades in Outside Accounts</strong><br></font>
													</td>
												</tr>
												<tr>
													<td colspan="2">
													<font color="#000000" size="3" face="Courier"><strong>ß: Non-BRG BCM Trades</strong><br></font>
													</td>
												</tr>
												<tr>
													<td colspan="2" nowrap>
													<font color="#FF0000" size="3" face="Courier">[i]: Initiation of Coverage<br></font>
													</td>
												</tr>
												<tr>
													<td colspan="2">
													<font color="#0000FF" size="3" face="Courier"><strong>Blue: Employee, Affiliate(BCM) and Proprietary Trades.</strong><br></font>
													</td>
												</tr>
												<tr>
													<td colspan="2" nowrap>
													<font color="#FF0000" size="3" face="Courier"><strong>Red: Trades in stocks with same day Research/MRI.</strong><br></font>
													</td>
												</tr>
												<tr>
													<td colspan="2">
													<font color="#000000" size="3" face="Courier"><strong>Type: AO = As Of, CXL = Cancel, CRT = Corrected.</strong></font><br>
													</td>
												</tr>
												<tr>
													<td colspan="2" bgcolor="#CCCCCC">
													<font color="#0000ff" size="2" face="Courier"><strong>POTENTIAL ANALYST TRADE VIOLATION</strong></font>
													</td>
												</tr>
												<tr>
													<td colspan="2" bgcolor="#FFA4FF">
													<font color="#000000" size="2" face="Courier"><strong>Watch List Trades</strong></font>
													</td>
												</tr>
												<tr>
													<td colspan="2">
													<font color="#800000" size="3" face="Courier"><strong>f: Employee Trades (Fidelity)</strong><br></font>
													</td>
												</tr>
											</table>';

			 
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// END NEW SECTION (WITH PAGE BREAK)
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++			

	//$file_name = $trade_date_to_process."_dly_a_".substr(md5(rand(1000,9999)), 0, 8).".html";     

//test

	$file_name = $trade_date_to_process."_dcarv2.html";    // _testnew   
	$file_pdf_name = $trade_date_to_process."_dcarv2.pdf"; // _testnew

//production
/*
	$file_name = $trade_date_to_process."_dcar.html";     
	$file_pdf_name = $trade_date_to_process."_dcar.pdf";     
*/

	$fp = fopen ($export_compliance.$file_name, "w");  
	fwrite ($fp,$data_to_html_file);        
	fclose ($fp); 

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
//$arr_recipient[0] = 'pprasad@centersys.com';


//production
$arr_recipient[] = 'pprasad@centersys.com';

foreach ($arr_recipient as $key => $emailval) {

				$user_id = get_user_id($emailval);
				$link = "";
				$link = $_site_url."repsvr.php?rep=DCARV2&src=".rand(10000000,99999999).str_replace('-','N',$trade_date_to_process).str_pad($user_id,10,'Q',1).md5("pprasad@centersys.com");
				
				$email_log = '
									<table width="100%" border="0" cellspacing="0" cellpadding="10">
										<tr> 
											<td valign="top">
												<p><a class="bodytext12"><strong>Daily Compliance Activity Report (v2)</strong></a></p>			
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
				
				$subject = "Daily Compliance Activity Report (v2) : (Trade Date: ".$date_to_show.")";
				$text_body = $subject;
				
				//zSysMailer($emailval, "", $subject, $html_body, $text_body, "") ;
				echo $link . "<br>";
}

//***********************************************************************************************************************************
//FILE MAINTENANCE AND ARCHIVAL

//delete the temp html file
$cmd = "del d:\\tdw\\tdw\\data\\compliance\\".$file_name;
xdebug("cmd",$cmd);
//shell_exec($cmd);

//IMPORTANT : Copy to archive location.
$cmd = "copy d:\\tdw\\tdw\\data\\compliance\\".$file_pdf_name. " \\\\buckfilesrv\\e$\\TDW_ComplianceReports\\".$file_pdf_name;
xdebug("cmd",$cmd);
shell_exec($cmd);
//************************************************************************************************************************************

?>
