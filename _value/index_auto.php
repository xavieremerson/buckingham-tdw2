<?
include('../includes/global.php');
include('../includes/dbconnect.php');
include('../includes/functions.php');
include('config.php');

	# SQL Server Connection Information
	$msconnect=mssql_connect("192.168.20.48","BUCKINGHAM_login","BUCKINGHAM_pw");
	$msdb=mssql_select_db("BUCKINGHAM",$msconnect);


	echo "Processing started at ".date('m/d/y h:i:sa')."\n";
	ob_flush();
	flush();

	//initiate page load time routine
	$time=getmicrotime(); 
	
	////
	// MAIN FUNCTION TO RELATE YAHOO TO JOVUS
		$arr_x = array();
		$arr_x["JAN"] = "Q1_FEB_MAR_APR^Q2_MAY_JUN_JUL^Q3_AUG_SEP_OCT^Q4_NOV_DEC_JAN";
		$arr_x["FEB"] = "Q1_MAR_APR_MAY^Q2_JUN_JUL_AUG^Q3_SEP_OCT_NOV^Q4_DEC_JAN_FEB";
		$arr_x["MAR"] = "Q1_APR_MAY_JUN^Q2_JUL_AUG_SEP^Q3_OCT_NOV_DEC^Q4_JAN_FEB_MAR";
		$arr_x["APR"] = "Q1_MAY_JUN_JUL^Q2_AUG_SEP_OCT^Q3_NOV_DEC_JAN^Q4_FEB_MAR_APR";
		$arr_x["MAY"] = "Q1_JUN_JUL_AUG^Q2_SEP_OCT_NOV^Q3_DEC_JAN_FEB^Q4_MAR_APR_MAY";
		$arr_x["JUN"] = "Q1_JUL_AUG_SEP^Q2_OCT_NOV_DEC^Q3_JAN_FEB_MAR^Q4_APR_MAY_JUN";
		$arr_x["JUL"] = "Q1_AUG_SEP_OCT^Q2_NOV_DEC_JAN^Q3_FEB_MAR_APR^Q4_MAY_JUN_JUL";
		$arr_x["AUG"] = "Q1_SEP_OCT_NOV^Q2_DEC_JAN_FEB^Q3_MAR_APR_MAY^Q4_JUN_JUL_AUG";
		$arr_x["SEP"] = "Q1_OCT_NOV_DEC^Q2_JAN_FEB_MAR^Q3_APR_MAY_JUN^Q4_JUL_AUG_SEP";
		$arr_x["OCT"] = "Q1_NOV_DEC_JAN^Q2_FEB_MAR_APR^Q3_MAY_JUN_JUL^Q4_AUG_SEP_OCT";
		$arr_x["NOV"] = "Q1_DEC_JAN_FEB^Q2_MAR_APR_MAY^Q3_JUN_JUL_AUG^Q4_SEP_OCT_NOV";
		$arr_x["DEC"] = "Q1_JAN_FEB_MAR^Q2_APR_MAY_JUN^Q3_JUL_AUG_SEP^Q4_OCT_NOV_DEC";
		
		$str_y = "JAN|JAN|-1,JAN|FEB|0,JAN|MAR|0,JAN|APR|0,JAN|MAY|0,JAN|JUN|0,JAN|JUL|0,JAN|AUG|0,JAN|SEP|0,JAN|OCT|0,JAN|NOV|0,JAN|DEC|0,".
						 "FEB|JAN|0,FEB|FEB|0,FEB|MAR|0,FEB|APR|0,FEB|MAY|0,FEB|JUN|0,FEB|JUL|0,FEB|AUG|0,FEB|SEP|0,FEB|OCT|0,FEB|NOV|0,FEB|DEC|0,".
						 "MAR|JAN|-1,MAR|FEB|-1,MAR|MAR|0,MAR|APR|0,MAR|MAY|0,MAR|JUN|0,MAR|JUL|0,MAR|AUG|0,MAR|SEP|0,MAR|OCT|0,MAR|NOV|0,MAR|DEC|1,".
						 "APR|JAN|-1,APR|FEB|0,APR|MAR|0,APR|APR|0,APR|MAY|0,APR|JUN|0,APR|JUL|0,APR|AUG|0,APR|SEP|0,APR|OCT|0,APR|NOV|0,APR|DEC|0,".
						 "MAY|JAN|0,MAY|FEB|0,MAY|MAR|0,MAY|APR|0,MAY|MAY|0,MAY|JUN|0,MAY|JUL|0,MAY|AUG|0,MAY|SEP|0,MAY|OCT|0,MAY|NOV|0,MAY|DEC|0,".
						 "JUN|JAN|0,JUN|FEB|0,JUN|MAR|0,JUN|APR|0,JUN|MAY|0,JUN|JUN|0,JUN|JUL|0,JUN|AUG|0,JUN|SEP|0,JUN|OCT|0,JUN|NOV|0,JUN|DEC|1,".
						 "JUL|JAN|0,JUL|FEB|0,JUL|MAR|0,JUL|APR|0,JUL|MAY|0,JUL|JUN|0,JUL|JUL|0,JUL|AUG|0,JUL|SEP|0,JUL|OCT|0,JUL|NOV|0,JUL|DEC|0,".
						 "AUG|JAN|0,AUG|FEB|0,AUG|MAR|0,AUG|APR|0,AUG|MAY|0,AUG|JUN|0,AUG|JUL|0,AUG|AUG|0,AUG|SEP|0,AUG|OCT|0,AUG|NOV|0,AUG|DEC|0,".
						 "SEP|JAN|0,SEP|FEB|0,SEP|MAR|0,SEP|APR|0,SEP|MAY|0,SEP|JUN|0,SEP|JUL|0,SEP|AUG|0,SEP|SEP|0,SEP|OCT|0,SEP|NOV|0,SEP|DEC|1,".
						 "OCT|JAN|0,OCT|FEB|0,OCT|MAR|0,OCT|APR|0,OCT|MAY|0,OCT|JUN|0,OCT|JUL|0,OCT|AUG|0,OCT|SEP|0,OCT|OCT|0,OCT|NOV|0,OCT|DEC|0,".
						 "NOV|JAN|0,NOV|FEB|0,NOV|MAR|0,NOV|APR|0,NOV|MAY|0,NOV|JUN|0,NOV|JUL|0,NOV|AUG|0,NOV|SEP|0,NOV|OCT|0,NOV|NOV|0,NOV|DEC|0,".
						 "DEC|JAN|0,DEC|FEB|0,DEC|MAR|0,DEC|APR|0,DEC|MAY|0,DEC|JUN|0,DEC|JUL|0,DEC|AUG|0,DEC|SEP|0,DEC|OCT|0,DEC|NOV|0,DEC|DEC|0";
						 
	$arr_y = explode(",",$str_y);
	//show_array($arr_y);
	
	function y2j ($fye, $mon, $yr) {
		$var_mon = strtoupper($mon);
		$var_yr = '20'.$yr;
		$var_fye = $fye;
		//xdebug("var_mon",$var_mon);
		//xdebug("var_yr",$var_yr);
		global $arr_x, $arr_y;
		//show_array($arr_x);
				
				$var_break = explode("^",$arr_x[$var_fye]); 
				//show_array($var_break);
				foreach($var_break as $bk=>$bv) {
					//xdebug("bv",$bv);
					if(stripos($bv, $var_mon) > 0) {   //strtoupper(date('M'))
						 //echo "Found ".strtoupper(date('M'))." in ".$bv."<br>";
						 
						 //get the year additive from the array $arr_y
						 foreach ($arr_y as $yk=>$yv) {
							 $arr_tmp = explode("|",$yv);
							 if ($arr_tmp[0] == $var_fye and $arr_tmp[1] == $var_mon) {
								 $var_yr = $var_yr + $arr_tmp[2];   
							 }
						 }
						 
						 return $var_yr."^".substr($bv,1,1); 
					} else {
						 //echo "Not Found ".strtoupper(date('M'))." in ".$bv."<br>";
					}
				}  
	}
	
	
	//test function and exit;
	//echo y2j('JAN', "Jan", '08');
	//exit;
	
	//==================================================================================================================
	
	$arr_rating = array();
 	$ms_qry_maxid = "SELECT dbo.Prod_Issuers.IssuerID, 
											max(dbo.Prod_Issuers.ProductID)
										FROM (
											(dbo.Prod_Issuers INNER JOIN dbo.Products ON dbo.Prod_Issuers.ProductID = dbo.Products.ProductID) 
											INNER JOIN dbo.Issuers ON dbo.Prod_Issuers.IssuerID = dbo.Issuers.IssuerID
													) INNER JOIN dbo.Prod_Statuses ON dbo.Products.ProductID = dbo.Prod_Statuses.ProductID 
											INNER JOIN dbo.ExchangeSecurities ON dbo.Issuers.IssuerID = dbo.ExchangeSecurities.SecurityID 
										WHERE (((dbo.Issuers.CUSIP)<>'') AND (dbo.Products.CreationDateTime 
											BETWEEN ( CAST(FLOOR(CAST(convert(datetime,'".date('m/d/Y')."',120) AS float)) as datetime)-365) 
												AND CAST(FLOOR(CAST(convert(datetime,'".date('m/d/Y')."',120) AS float)) as datetime) ) 
										AND ((dbo.Prod_Statuses.StatusTypeID)=3))
										AND dbo.Prod_Issuers.Recommendation is not null
										GROUP BY dbo.Prod_Issuers.IssuerID
										ORDER BY dbo.Prod_Issuers.IssuerID";
	$ms_results_maxid = mssql_query($ms_qry_maxid);
	while ($row_maxid = mssql_fetch_array($ms_results_maxid)) {

      $ms_qry_rating = "select IssuerID, Recommendation from dbo.Prod_Issuers
			                  where IssuerID = '".$row_maxid[0]."' and ProductID = '".$row_maxid[1]."'";
			$ms_results_rating = mssql_query($ms_qry_rating);
			while ($row_rating = mssql_fetch_array($ms_results_rating)) {
		  	$arr_rating[$row_maxid[0]] = $row_rating[1]; 
			} 
	}

	//==================================================================================================================
	
	$arr_FYE = array("January"=>"JAN","February"=>"FEB","March"=>"MAR","April"=>"APR","May"=>"MAY","June"=>"JUN","July"=>"JUL","August"=>"AUG","September"=>"SEP","October"=>"OCT","November"=>"NOV","December"=>"DEC");
	
	$exportlocation  = "D:\\tdw\\tdw\\estimates\\";   /* Trailing slash must exist */
	$filelocation =    "D:\\tdw\\tdw\\estimates\\files\\";
	
	$fp = fopen($exportlocation."out.csv", "w");

	$string = "\"\",\"BRG\",\"BRG\",\"BRG\",\"BRG\",\"CONSENSUS\",\"CONSENSUS\",\"CONSENSUS\",\"CONSENSUS\",\"\",\"\"\n";
	//echo $string."<br>";
	fputs ($fp, $string);
	
	$string = "\"SYMBOL\",\"CURRENT Q\",\"NEXT Q\",\"CURRENT YEAR\",\"NEXT YEAR\",\"CURRENT Q\",\"NEXT Q\",\"CURRENT YEAR\",\"NEXT YEAR\",\"RATING\",\"CURRENT QTR\",\"FISCAL YEAR\",\"DIRTY\"\n";
	//echo $string."<br>";
	fputs ($fp, $string);

	//Get tickers from Jovus
	
			$ms_qry_rres   = "SELECT dbo.Issuers.IssuerID, dbo.ExchangeSecurities.Ticker as CUSIP, dbo.Issuers.FiscalYearEnd
												FROM  dbo.Issuers
												INNER JOIN dbo.ExchangeSecurities ON dbo.Issuers.IssuerID = dbo.ExchangeSecurities.SecurityID 
												WHERE dbo.ExchangeSecurities.IsActive = 1
												AND dbo.ExchangeSecurities.Ticker like '%'											
												order by dbo.ExchangeSecurities.Ticker;";
												
												//
			//xdebug("ms_qry_rres",$ms_qry_rres);
			$ms_results_rres = mssql_query($ms_qry_rres);
			$v_count_rres = 0;
			while ($row_rres = mssql_fetch_array($ms_results_rres)) {
			
			$v_count_rres = $v_count_rres + 1;
			
			if ($v_count_rres == 10000) {
				exit;
			}
						//show_array($row_rres);
						$val_IssuerID = $row_rres[0];
						$val_symbol = $row_rres[1];
						$var_FYE = $arr_FYE[trim($row_rres[2])];
						//xdebug("val_symbol",$val_symbol);
						if ($val_symbol != 'xxx') {
						//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
						echo "Processing : ".$val_symbol."\n";
									 //echo "Currently processing : [".$val_symbol."]\n<br>";
									 
									
										$str_whole = "";
										$lines = array();
										$all_table_data = array();
										
										$lines = file($filelocation.str_replace(".","-",$val_symbol).".html");
			 										
										//Get the content of the file into a string
										foreach ($lines as $key=>$value)
											{
											 $str_whole .=$value;
											}
										
										$lines = array();
							
									 //echo $str_whole;
									 //exit;
							
									 $str_whole = str_replace("<TABLE","<table",$str_whole);
									 
									 //exit;
	
										//echo strposnth($str_whole, "<table", 10, 0); //$str_whole;
										
										//strip everything before the 11th <table
										$str_whole_a = substr($str_whole, strposnth($str_whole, "<table", 11, 1), 100000);
										
										//strip everything after the first </table
										$str_whole_b = substr($str_whole_a, 0, strposnth($str_whole_a, "</table", 1, 0)+8);
										
										//extract all data from table into an array
										$all_table_data = table_into_array($str_whole_b,$needle="",$needle_within=0,$allowed_tags="");
	
										
										if (substr($all_table_data[0][1],0,11)!='Current Qtr') {
											//==============================================================================
											$str_whole = "";
											$lines = array();
											$all_table_data = array();

											$lines = file($filelocation.str_replace(".","-",$val_symbol).".html");
											//Get the content of the file into a string
											foreach ($lines as $key=>$value)
												{
												 $str_whole .=$value;
												}
											
											$lines = array();
										  $str_whole = str_replace("<TABLE","<table",$str_whole);

											//strip everything before the 13th <table
											$str_whole_a = substr($str_whole, strposnth($str_whole, "<table", 13, 1), 100000);
											
											//strip everything after the first </table
											$str_whole_b = substr($str_whole_a, 0, strposnth($str_whole_a, "</table", 1, 0)+8);
											
											//echo $str_whole_b."<br>";
											
											//extract all data from table into an array
											$all_table_data = table_into_array($str_whole_b,$needle="",$needle_within=0,$allowed_tags="");			
											//show_array($all_table_data);								
											//==============================================================================
										}

										//echo $str_whole_b."<br><br>";
										if (substr($all_table_data[0][1],0,11)=='Current Qtr') {
										?>
														<?
														//echo $var_FYE."////".y2j($var_FYE, $arr_M_Y[0], $arr_M_Y[1])."///".trim(str_replace('Current Qtr','',$all_table_data[0][1]));

														$arr_M_Y = explode("-",trim(str_replace('Current Qtr','',$all_table_data[0][1])));
														
														$arr_vals_year_qtr = explode("^",y2j($var_FYE, $arr_M_Y[0], $arr_M_Y[1]));
														
														//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
																				//))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))
																				//get Max ProductID
																				$ms_qry_pid   = "SELECT max(dbo.Prod_FinancialValues.ProductID) from dbo.Prod_FinancialValues
																												 INNER JOIN dbo.Prod_Statuses on dbo.Prod_FinancialValues.ProductID = dbo.Prod_Statuses.ProductID
																												 WHERE dbo.Prod_FinancialValues.IssuerID = '".$row_rres[0]."'
																												 AND dbo.Prod_Statuses.StatusTypeID = 3";
																				//xdebug("ms_qry_pid",$ms_qry_pid);
																				$ms_results_pid = mssql_query($ms_qry_pid);
																				while ($row_pid = mssql_fetch_array($ms_results_pid)) {
																						
																						$max_product_id = $row_pid[0];
																						//xdebug("max_product_id",$max_product_id);
																						
																						if ($row_pid != '') {
																						
																						//initialize some vars
																						$curr_qtr =  'NO EST';
																						$next_qtr =  'NO EST';
																						$curr_year = 'NO EST';
																						$next_year = 'NO EST';
														
																								//xdebug("MaxProductID",$row_pid[0]);
																								//))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))
																								//get relevant data for the qtr
																								$val_dirty = "";
																								if ($arr_vals_year_qtr[1] < 4) {
																												$ms_qry_relevant_vals = 	"SELECT Q".$arr_vals_year_qtr[1]."Value, Q".($arr_vals_year_qtr[1]+1)."Value, FinancialValue2
																																										FROM dbo.Prod_FinancialValues
																																										WHERE IssuerID = '".$row_rres[0]."'
																																										AND ProductID = '".$row_pid[0]."'
																																										AND FinancialType = 'EPS'
																																										AND PeriodYear = '".$arr_vals_year_qtr[0]."'";
																												//xdebug("ms_qry_relevant_vals",$ms_qry_relevant_vals);
																												$ms_relevant_vals = mssql_query($ms_qry_relevant_vals);
																												while ($row_relevant_vals = mssql_fetch_array($ms_relevant_vals)) {
																												
																														if (stripos($row_relevant_vals[0], 'A') > 0 or
																														    stripos($row_relevant_vals[1], 'A') > 0 or
																																stripos($row_relevant_vals[2], 'A') > 0) {
																														$val_dirty = 1;  
																														}
																														$curr_qtr =  str_replace('E','',$row_relevant_vals[0]);
																														$curr_qtr =  str_replace('A','',$curr_qtr);
																														$curr_qtr =  str_replace('$','',$curr_qtr);
																														
																														$next_qtr =  str_replace('E','',$row_relevant_vals[1]);
																														$next_qtr =  str_replace('A','',$next_qtr);
																														$next_qtr =  str_replace('$','',$next_qtr);
														
																														$curr_year = str_replace('E','',$row_relevant_vals[2]);
																														$curr_year = str_replace('A','',$curr_year);
																														$curr_year = str_replace('$','',$curr_year);
																												}
																												
																												$ms_qry_relevant_vals_more = 	"SELECT FinancialValue2
																																												FROM dbo.Prod_FinancialValues
																																												WHERE IssuerID = '".$row_rres[0]."'
																																												AND ProductID = '".$row_pid[0]."'
																																												AND FinancialType = 'EPS'
																																												AND PeriodYear = '".($arr_vals_year_qtr[0]+1)."'";
																												//xdebug("ms_qry_relevant_vals_more",$ms_qry_relevant_vals_more);
																												$ms_relevant_vals_more = mssql_query($ms_qry_relevant_vals_more);
																												while ($row_relevant_vals_more = mssql_fetch_array($ms_relevant_vals_more)) {

																														if (stripos($row_relevant_vals_more[0], 'A') > 0) {
																														$val_dirty = 1;  
																														}

																														$next_year = str_replace('E','',$row_relevant_vals_more[0]);
																														$next_year =  str_replace('A','',$next_year);
																														$next_year =  str_replace('$','',$next_year);
																												}
																								} else {
																												$ms_qry_relevant_vals = 	"SELECT Q".$arr_vals_year_qtr[1]."Value, FinancialValue2
																																										FROM dbo.Prod_FinancialValues
																																										WHERE IssuerID = '".$row_rres[0]."'
																																										AND ProductID = '".$row_pid[0]."'
																																										AND FinancialType = 'EPS'
																																										AND PeriodYear = '".$arr_vals_year_qtr[0]."'";
																												//xdebug("ms_qry_relevant_vals",$ms_qry_relevant_vals);
																												$ms_relevant_vals = mssql_query($ms_qry_relevant_vals);
																												while ($row_relevant_vals = mssql_fetch_array($ms_relevant_vals)) {
																												
																														if (stripos($row_relevant_vals[0], 'A') > 0 or
																														    stripos($row_relevant_vals[1], 'A') > 0) {
																														$val_dirty = 1;  
																														}

																														$curr_qtr =  str_replace('E','',$row_relevant_vals[0]);
																														$curr_qtr =  str_replace('A','',$curr_qtr);
																														$curr_qtr =  str_replace('$','',$curr_qtr);
														
																														$curr_year = str_replace('E','',$row_relevant_vals[1]);
																														$curr_year =  str_replace('A','',$curr_year);
																														$curr_year =  str_replace('$','',$curr_year);
																												}
																												
																												$ms_qry_relevant_vals_more = 	"SELECT Q1Value, FinancialValue2
																																												FROM dbo.Prod_FinancialValues
																																												WHERE IssuerID = '".$row_rres[0]."'
																																												AND ProductID = '".$row_pid[0]."'
																																												AND FinancialType = 'EPS'
																																												AND PeriodYear = '".($arr_vals_year_qtr[0]+1)."'";
																												//xdebug("ms_qry_relevant_vals_more",$ms_qry_relevant_vals_more);
																												$ms_relevant_vals_more = mssql_query($ms_qry_relevant_vals_more);
																												while ($row_relevant_vals_more = mssql_fetch_array($ms_relevant_vals_more)) {
																												
																														if (stripos($row_relevant_vals_more[0], 'A') > 0 or
																														    stripos($row_relevant_vals_more[1], 'A') > 0) {
																														$val_dirty = 1;  
																														}

																														$next_qtr =  str_replace('E','',$row_relevant_vals_more[0]);
																														$next_qtr =  str_replace('A','',$next_qtr);
																														$next_qtr =  str_replace('$','',$next_qtr);
														
																														$next_year = str_replace('E','',$row_relevant_vals_more[1]);
																														$next_year = str_replace('A','',$next_year);
																														$next_year = str_replace('$','',$next_year);
																												}
																								}
																								
																							}
																							/*
																							echo '<tr><td colspan="9">';
																							echo  " SELECT * from dbo.Prod_FinancialValues 
																										WHERE IssuerID = '".$row_rres[0]."' 
																										AND FinancialType = 'EPS'
																										and ProductID = '".$row_pid[0]."'
																										order by ProductID desc, PeriodYear";
																							echo '</td></tr>';
																							*/
																					}
														
														//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&													
													  $val_curr_qtr = "";
														$val_next_qtr = "";
														$val_curr_year = "";
														$val_next_year = "";
														
														if (trim($curr_qtr) == '' or $curr_qtr == 'NO EST' or $curr_qtr == NULL ) {$val_curr_qtr = "NO EST"; } else {$val_curr_qtr = $curr_qtr; }
														if (trim($next_qtr) == '' or $next_qtr == 'NO EST' or $next_qtr == NULL ) {$val_next_qtr = "NO EST"; } else {$val_next_qtr = $next_qtr; }
														if (trim($curr_year) == '' or $curr_year == 'NO EST' or $curr_year == NULL ) {$val_curr_year = "NO EST"; } else {$val_curr_year = $curr_year; }
														if (trim($next_year) == '' or $next_year == 'NO EST' or $next_year == NULL ) {$val_next_year = "NO EST"; } else {$val_next_year = $next_year; }

														$str_to_put = '"'.$val_symbol.'","'.$val_curr_qtr.'","'.$val_next_qtr.'","'.$val_curr_year.'","'.$val_next_year.'","'.$all_table_data[1][1].'","'.$all_table_data[1][2].'","'.$all_table_data[1][3].'","'.$all_table_data[1][4].'","'.$arr_rating[$val_IssuerID].'","['.trim(str_replace('Current Qtr','',$all_table_data[0][1])).']","'.$var_FYE.'","'.$val_dirty.'"'."\n";
														fputs ($fp, $str_to_put);
														?>
										<?
										}
										ob_flush();
										flush();
	
										//print_r($all_table_data);
										
										//exit;
										
										
						//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
						}
			}
								echo "<br>&nbsp;&nbsp;&nbsp;Processing completed successfully. Time taken: ". sprintf("%01.2f",((getmicrotime()-$time)/1000))." s.";             
  fclose($fp);

  //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
				$link = "";
				$link = $_site_url."estimates/out.csv";
				
				$email_log = '
									<table width="100%" border="0" cellspacing="0" cellpadding="10">
										<tr> 
											<td valign="top">
												<p><a class="bodytext12"><strong>Estimates Collation</strong></a></p>			
												<p><a class="bodytext12">Date: <strong>'.date('m/d/Y h:ia').'</strong></a></p>
												<p class="bodytext12">The report is attached (Excel/CSV Format)</p>
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
				
				$subject = "Estimates Collation: ".date('m/d/Y h:ia');
				$text_body = $subject;
				
				$arr_attachfile = array("out.csv"=>"D:/tdw/tdw/estimates/out.csv");
				
				zSysMailer($sendto, "", $subject, $html_body, $text_body, $arr_attachfile) ;
				zSysMailer("pprasad@centersys.com", "", $subject, $html_body, $text_body, $arr_attachfile) ;
				echo "<br>&nbsp;&nbsp;&nbsp;Email Sent with file attached. You can also access the file <a href='". $link ."'>>>HERE<<</a><br>";
  //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++




//} //ending if : form submission
exit;								
									
//Get the content of the file into a csv output file
foreach ($all_table_data as $keyval=>$arr_value)
	{
	 if ($keyval == 0) {
	 $zline = $arr_value;
	 $str_out = substr($zline[0],10,10)."|".substr($zline[0],30,10)."|".str_replace('&nbsp;','',$zline[1])."|".str_replace('&nbsp;','',$zline[2])."|".str_replace('&nbsp;','',$zline[3])."|".strip_tags($zline[4])."|".str_replace('&nbsp;','',$zline[5])."|".str_replace('&nbsp;','',$zline[6])."|".str_replace('&nbsp;','',$zline[7])."|".str_replace('&nbsp;','',$zline[8])."|".str_replace('&nbsp;','',$zline[9])."|".str_replace('&nbsp;','',$zline[10])."|".str_replace('&nbsp;','',$zline[11])."|".str_replace('&nbsp;','',$zline[12])."\n";
	 $str_out = unhtmlspecialchars($str_out);
	 fwrite($fp,$str_out);
	 }
	 //echo ".";
	}


//print_r($all_table_data);


function strposnth($haystack, $needle, $nth=1, $insenstive=1)
{
   //if its case insenstive, convert strings into lower case
   if ($insenstive) {
       $haystack=strtolower($haystack);
       $needle=strtolower($needle);
   }
   //count number of occurances
   $count=substr_count($haystack,$needle);
   
   //first check if the needle exists in the haystack, return false if it does not
   //also check if asked nth is within the count, return false if it doesnt
   if ($count<1 || $nth > $count) return false;

   
   //run a loop to nth number of accurance
   //start $pos from -1, cause we are adding 1 into it while searchig
   //so the very first iteration will be 0
   for($i=0,$pos=0,$len=0;$i<$nth;$i++)
   {    
       //get the position of needle in haystack
       //provide starting point 0 for first time ($pos=0, $len=0)
       //provide starting point as position + length of needle for next time
       $pos=strpos($haystack,$needle,$pos+$len);

       //check the length of needle to specify in strpos
       //do this only first time
       if ($i==0) $len=strlen($needle);
     }
   
   //return the number
   return $pos;
}



function win3utf($s)    { 
   for($i=0, $m=strlen($s); $i<$m; $i++)    { 
       $c=ord($s[$i]); 
       if ($c<=127) {$t.=chr($c); continue; } 
       if ($c>=192 && $c<=207)    {$t.=chr(208).chr($c-48); continue; } 
       if ($c>=208 && $c<=239) {$t.=chr(208).chr($c-48); continue; } 
       if ($c>=240 && $c<=255) {$t.=chr(209).chr($c-112); continue; } 
       if ($c==184) { $t.=chr(209).chr(209); continue; }; 
   if ($c==168) { $t.=chr(208).chr(129);  continue; }; 
   } 
   return $t; 
} 

function unhtmlspecialchars( $string )
{
  $string = str_replace ( '&amp;', '&', $string );
  $string = str_replace ( '&#039;', '\'', $string );
  $string = str_replace ( '&quot;', '"', $string );
  $string = str_replace ( '&lt;', '<', $string );
  $string = str_replace ( '&gt;', '>', $string );
  $string = str_replace ( '&Uuml;', '?', $string );
  return $string;
} 

				/*
				Static method table_into_array()
				Generic function to return data array from HTML table data
				rawHTML: the page source
				needle: optional string to start parsing source from
				needle_within: 0 = needle is BEFORE table, 1 = needle is within table
				allowed_tags: list of tags to NOT strip from data, e.g. "<a><b>"
				*/
				function table_into_array($rawHTML,$needle="",$needle_within=0,$allowed_tags="") {
								$upperHTML = strtoupper($rawHTML);
								$idx = 0;
								if (strlen($needle) > 0) {
												$needle = strtoupper($needle);
												$idx = strpos($upperHTML,$needle);
												if ($idx === false) { return false; }
												if ($needle_within == 1) {
																$cnt = 0;
																while(($cnt < 100) && (substr($upperHTML,$idx,6) != "<TABLE")) {
																				$idx = strrpos(substr($upperHTML,0,$idx-1),"<");
																				$cnt++;
																}
												}
								}
								$aryData = array();
								$rowIdx = 0;
								/*    If this table has a header row, it may use TD or TH, so
								check special for this first row. */
								$tmp = strpos($upperHTML,"<TR",$idx);
								if ($tmp === false) { return false; }
								$tmp2 = strpos($upperHTML,"</TR>",$tmp);
								if ($tmp2 === false) { return false; }
								$row = substr($rawHTML,$tmp,$tmp2-$tmp);
								$pattern = "/<TH>|<TH\ |<TD>|<TD\ /";
								preg_match($pattern,strtoupper($row),$matches);
								$hdrTag = $matches[0];

								while ($tmp = strpos(strtoupper($row),$hdrTag) !== false) {
												$tmp = strpos(strtoupper($row),">",$tmp);
												if ($tmp === false) { return false; }
												$tmp++;
												$tmp2 = strpos(strtoupper($row),"</T");
												$aryData[$rowIdx][] = trim(strip_tags(substr($row,$tmp,$tmp2-$tmp),$allowed_tags));
												$row = substr($row,$tmp2+5);
												preg_match($pattern,strtoupper($row),$matches);
												$hdrTag = $matches[0];
								}
								$idx = strpos($upperHTML,"</TR>",$idx)+5;
								$rowIdx++;

								/* Now parse the rest of the rows. */
								$tmp = strpos($upperHTML,"<TR",$idx);
								if ($tmp === false) { return false; }
								$tmp2 = strpos($upperHTML,"</TABLE>",$idx);
								if ($tmp2 === false) { return false; }
								$table = substr($rawHTML,$tmp,$tmp2-$tmp);

								while ($tmp = strpos(strtoupper($table),"<TR") !== false) {
												$tmp2 = strpos(strtoupper($table),"</TR");
												if ($tmp2 === false) { return false; }
												$row = substr($table,$tmp,$tmp2-$tmp);

												while ($tmp = strpos(strtoupper($row),"<TD") !== false) {
																$tmp = strpos(strtoupper($row),">",$tmp);
																if ($tmp === false) { return false; }
																$tmp++;
																$tmp2 = strpos(strtoupper($row),"</TD");
																$aryData[$rowIdx][] = trim(strip_tags(substr($row,$tmp,$tmp2-$tmp),$allowed_tags));
																$row = substr($row,$tmp2+5);
												}
												$table = substr($table,strpos(strtoupper($table),"</TR>")+5);
												$rowIdx++;
								}
								return $aryData;
				}



?>
