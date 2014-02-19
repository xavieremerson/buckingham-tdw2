<?
  include('../includes/dbconnect.php');
  include('../includes/global.php');
	include('../includes/functions.php');

	//Previous Business Day should be applied here.
	$trade_date_to_process = previous_business_day();
	$trade_date_to_process = '2011-01-01';
						
	$nextday = date("Y-m-d");
	
	xdebug("next_day", $nextday);
	
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
						
		$ms_qry_ioc = "SELECT dbo.ExchangeSecurities.Ticker as CUSIP, 
													 count(dbo.ExchangeSecurities.Ticker) as CountCUSIP,
													 CONVERT(VARCHAR(10), max(dbo.Prod_Statuses.DateTime), 120) as IOCdate,
													 min(dbo.Prod_Statuses.DateTime) as date_in_mri_format,
													 min(dbo.Prod_SubjectCodes.SubjectCode) as SubjectCode
													 
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

								xdebug("ms_qry_ioc",$ms_qry_ioc);
								$ms_results_ioc = mssql_query($ms_qry_ioc);
								
								$v_count_ioc = 0;
								$arr_ioc = array();
								$arr_ioc_mri_format = array();
								while ($row_ioc = mssql_fetch_array($ms_results_ioc)) {
											
											$symbol = $row_ioc[0];
											$ioc_date = $row_ioc[2];
											$count_num = $row_ioc[1];
											$ioc_date_mri_format = $row_ioc[3];
											
											echo "<br>".$symbol.">>>".$ioc_date.">>>".$count_num.">>>".$ioc_date_mri_format."<br>";
						
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
		
?>