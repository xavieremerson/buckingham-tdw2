<?
  include('../includes/dbconnect.php');
  include('../includes/global.php');
	include('../includes/functions.php');

	//Previous Business Day should be applied here.
	$trade_date_to_process = previous_business_day();
	//$trade_date_to_process = '2007-10-10';

  //**************************************************************
		$dval = explode("-", $trade_date_to_process); 
		$y1 = $dval[0];
		$m1 = $dval[1];
		$d1 = $dval[2];
		
		$timeval = mktime(0,0,0, $m1, $d1, $y1);
		
		$newtime = $timeval + (60*60*24);	
		$nextday = date("Y-m-d", $newtime);
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



	//$date_match_val = date("M j Y",strtotime('2006-08-02'));
	$date_match_val = date("j-M",strtotime($trade_date_to_process));
	$date_to_show = date("m/d/Y",strtotime($trade_date_to_process));
	
	//xdebug("date_match_val",$date_match_val);


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

		$ms_qry_rres   = "SELECT dbo.Prod_Issuers.IssuerID, dbo.ExchangeSecurities.Ticker as CUSIP, 
											Max(dbo.Prod_Statuses.DateTime) AS MaxOfDateTime
											 FROM ((dbo.Prod_Issuers INNER JOIN dbo.Products ON dbo.Prod_Issuers.ProductID = dbo.Products.ProductID) 
											INNER JOIN dbo.Issuers ON dbo.Prod_Issuers.IssuerID = dbo.Issuers.IssuerID) 
											INNER JOIN dbo.Prod_Statuses ON dbo.Products.ProductID = dbo.Prod_Statuses.ProductID 
											INNER JOIN dbo.ExchangeSecurities ON dbo.Issuers.IssuerID = dbo.ExchangeSecurities.SecurityID 
											WHERE dbo.Issuers.CUSIP <> '' 
											AND ((dbo.Prod_Statuses.DateTime) < CAST(FLOOR(CAST(convert(datetime,'".format_date_ymd_to_mdy(date('Y-m-d'))."',120) AS float)) as datetime)) 
											AND ((dbo.Products.CreationDateTime) < CAST(FLOOR(CAST(convert(datetime,'".format_date_ymd_to_mdy(date('Y-m-d'))."',120) AS float)) as datetime)) 
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
		$ms_qry_ioc = "SELECT dbo.ExchangeSecurities.Ticker as CUSIP, 
													 count(dbo.ExchangeSecurities.Ticker) as CountCUSIP,
													 CONVERT(VARCHAR(10), max(dbo.Prod_Statuses.DateTime), 120) as IOCdate
										FROM (
											(
											dbo.Prod_Issuers INNER JOIN dbo.Products ON dbo.Prod_Issuers.ProductID = dbo.Products.ProductID
											) 
											INNER JOIN dbo.Issuers ON dbo.Prod_Issuers.IssuerID = dbo.Issuers.IssuerID
												 ) 
												 INNER JOIN dbo.Prod_Statuses ON dbo.Products.ProductID = dbo.Prod_Statuses.ProductID 
												 INNER JOIN dbo.ExchangeSecurities ON dbo.Issuers.IssuerID = dbo.ExchangeSecurities.SecurityID 
										WHERE (((dbo.Issuers.CUSIP)<>'') 
										AND (dbo.Products.CreationDateTime BETWEEN (CAST(FLOOR(CAST(convert(datetime,'".format_date_ymd_to_mdy($trade_date_to_process)."',120) AS float)) as datetime)-180) 
											AND CAST(FLOOR(CAST(convert(datetime,'".format_date_ymd_to_mdy($nextday)."',120) AS float)) as datetime)) 
										AND ((dbo.Prod_Statuses.StatusTypeID)=3)) 
										GROUP BY dbo.ExchangeSecurities.Ticker
										ORDER BY dbo.ExchangeSecurities.Ticker;";	


		//xdebug("ms_qry_ioc",$ms_qry_ioc);
		$ms_results_ioc = mssql_query($ms_qry_ioc);
		
		$v_count_ioc = 0;
		$arr_ioc = array();
		while ($row_ioc = mssql_fetch_array($ms_results_ioc)) {
					
					$symbol = $row_ioc[0];
					$ioc_date = $row_ioc[2];
					$count_num = $row_ioc[1];


					//xdebug("ioc_info",$symbol."/".$count_num."/".$ioc_date);
					if ($count_num == 1 and $ioc_date == $trade_date_to_process) {
						$arr_ioc[] = $symbol;
					}
		}
		//*************************************************************************************************************	
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
									  WHERE (((dbo.Issuers.CUSIP)<>'') AND (dbo.Products.CreationDateTime BETWEEN 
											(
														 CAST(FLOOR(CAST(convert(datetime,'".format_date_ymd_to_mdy($trade_date_to_process)."',120) AS float)) as datetime)-180) 
												 AND CAST(FLOOR(CAST(convert(datetime,'".format_date_ymd_to_mdy($nextday)."',120) AS float)) as datetime)
											) 
											AND ((dbo.Prod_Statuses.StatusTypeID)=3))
										ORDER BY dbo.ExchangeSecurities.Ticker, dbo.Prod_Statuses.DateTime DESC;";	


		xdebug("ms_qry_mri",$ms_qry_mri);
		$ms_results_mri = mssql_query($ms_qry_mri);
		
		$v_count_mri = 0;
		while ($row_mri = mssql_fetch_array($ms_results_mri)) {
					
					//show_array($row_mri);
					$symbol = $row_mri[0];
					$mri_date = $row_mri[1];
					$rating = $row_mri[3];
					$rating_change = $row_mri[5]; 
					$target = $row_mri[6];

		}
   
?>