<?
include('../../../includes/dbconnect.php');
include('../../../includes/functions.php');

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

function y2j ($mon, $yr) {
  $var_mon = strtoupper($mon);
	$var_yr = '20'.$yr;
	//xdebug("var_mon",$var_mon);
	//xdebug("var_yr",$var_yr);
  global $arr_x;
	//show_array($arr_x);
	foreach($arr_x as $k=>$v) {
		if ($k == $var_mon) {
			$var_break = explode("^",$v); 
			//show_array($var_break);
			foreach($var_break as $bk=>$bv) {
				//xdebug("bv",$bv);
				if(stripos($bv, strtoupper(date('M'))) > 0) { 
					 //echo "Found ".strtoupper(date('M'))." in ".$bv."<br>";
					 return $var_yr."^".substr($bv,1,1); 
				} else {
					 //echo "Not Found ".strtoupper(date('M'))." in ".$bv."<br>";
				}
			}  
		}
	}
}

//test function
echo y2j ('Sep', '08');

exit;


$exportlocation              = "D:\\tdw\\tdw\\tmp\\extract\\nicpakler\\";   /* Trailing slash must exist */

$fp = fopen($exportlocation."out.csv", "w");

$string = "\"\",\"BRG\",\"BRG\",\"BRG\",\"CONSENSUS\",\"CONSENSUS\",\"CONSENSUS\",\"\",\"\"\n";
//echo $string."<br>";
fputs ($fp, $string);

$string = "\"SYMBOL\",\"CURRENT Q\",\"CURRENT YEAR\",\"NEXT YEAR\",\"CURRENT Q\",\"CURRENT YEAR\",\"NEXT YEAR\",\"RATING\",\"TARGET\"\n";
//echo $string."<br>";
fputs ($fp, $string);

//Get tickers from Jovus
# SQL Server Connection Information
$msconnect=mssql_connect("192.168.20.48","BUCKINGHAM_login","BUCKINGHAM_pw");
$msdb=mssql_select_db("BUCKINGHAM",$msconnect);


	xdebug('Connecting to Jovus Server @ Buckingham','Successful');

  //Most recent research date from Jovus
	 
		$arr_rres = array();
		$arr_rres_symbols = array();

		$ms_qry_rres   = "SELECT dbo.Issuers.IssuerID, dbo.ExchangeSecurities.Ticker as CUSIP 
											FROM  dbo.Issuers
											INNER JOIN dbo.ExchangeSecurities ON dbo.Issuers.IssuerID = dbo.ExchangeSecurities.SecurityID 
											WHERE dbo.ExchangeSecurities.IsActive = 1
											order by dbo.ExchangeSecurities.Ticker;";

		//xdebug("ms_qry_rres",$ms_qry_rres);
		$ms_results_rres = mssql_query($ms_qry_rres);
?>

<table cellpadding="0" cellspacing="0" width="500" >
  <tr>
		<td width="100">&nbsp;</td>
    <td width="100">Current Qtr</td>
    <td width="100">Next Qtr</td>
    <td width="100">Current Year</td>
    <td width="100">Next Year</td>
  </tr>
</table>
<br />
<?		
		$v_count_rres = 0;
		$tmp_count = 0;
		while ($row_rres = mssql_fetch_array($ms_results_rres)) {
					
					$tmp_count = $tmp_count + 1;
					
					if ($tmp_count == 50) {
              echo " Process Time ". sprintf("%01.2f",((getmicrotime()-$time)/1000))." s.";             
					  exit;
					}

					//show_array($row_rres);
					$val_symbol = $row_rres[1];
					//xdebug("val_symbol",$val_symbol);
					if ($val_symbol != 'xxx' ) { //and $val_symbol == 'AB'
										
						//))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))
						//get Max ProductID
						$ms_qry_pid   = "SELECT max(dbo.Prod_FinancialValues.ProductID) from dbo.Prod_FinancialValues
						                 INNER JOIN dbo.Prod_Statuses on dbo.Prod_FinancialValues.ProductID = dbo.Prod_Statuses.ProductID
														 WHERE dbo.Prod_FinancialValues.IssuerID = '".$row_rres[0]."'
														 AND dbo.Prod_Statuses.StatusTypeID = 3";
						//xdebug("ms_qry_pid",$ms_qry_pid);
						$ms_results_pid = mssql_query($ms_qry_pid);
						while ($row_pid = mssql_fetch_array($ms_results_pid)) {
							
								if ($row_pid != '') {
							
										//xdebug("MaxProductID",$row_pid[0]);
										//))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))
										//get Max Year with ALL ACTUALS
										$ms_qry_max_year = 	"SELECT max(PeriodYear) from dbo.Prod_FinancialValues
																					WHERE IssuerID = '".$row_rres[0]."'
																					AND ProductID = '".$row_pid[0]."'
																					AND FinancialValue2 != ''
																					AND FinancialType = 'EPS'
																					AND Q1Value = ''";
																				/*
																				"SELECT max(PeriodYear) from dbo.Prod_FinancialValues
																				WHERE IssuerID = '".$row_rres[0]."'
																				AND ProductID = '".$row_pid[0]."'
																				AND FinancialType = 'EPS'
																				AND Q4Value like '%A'";
																				*/
										$ms_results_max_year = mssql_query($ms_qry_max_year);
										while ($row_max_year = mssql_fetch_array($ms_results_max_year)) {
										
												//xdebug("row_max_year[0]",$row_max_year[0]);
												if ($row_max_year[0] !='') {
							
														//))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))
														//get all Q Values and figure out the required value from the next year
														$ms_qry_all_q = "SELECT Q1Value, Q2Value, Q3Value, Q4Value,
																										right(Q1Value,1)+right(Q2Value,1)+right(Q3Value,1)+right(Q4Value,1) 
																										from dbo.Prod_FinancialValues
																								WHERE IssuerID = '".$row_rres[0]."'
																								AND ProductID = '".$row_pid[0]."'
																								AND FinancialType = 'EPS'
																								AND PeriodYear = '".($row_max_year[0] - 1)."'";
														//xdebug("Max Year - First E",($row_max_year[0]-1));
														//xdebug("ms_qry_all_q",$ms_qry_all_q);
														$ms_results_all_q = mssql_query($ms_qry_all_q);
														while ($row_all_q = mssql_fetch_array($ms_results_all_q)) {
										
																if       ($row_all_q[4] == 'EEEE') {
																	$str_e_val = substr($row_all_q[0],0,strlen($row_all_q[0])-1);
																} elseif ($row_all_q[4] == 'AEEE') {
																	$str_e_val = substr($row_all_q[1],0,strlen($row_all_q[1])-1);
																} elseif ($row_all_q[4] == 'AAEE') {
																	$str_e_val = substr($row_all_q[2],0,strlen($row_all_q[2])-1);
																} elseif ($row_all_q[4] == 'AAAE') {
																	$str_e_val = substr($row_all_q[3],0,strlen($row_all_q[3])-1);
																} elseif (substr($row_all_q[4],0,3) == 'AAA') {  //ABG had no values entered for E
																	$str_e_val = substr($row_all_q[3],0,strlen($row_all_q[3])-1);
																} else {
																	$str_e_val = "?";

																}
										
															  echo $val_symbol."<br>";
																$str_data =  '"'.$val_symbol .'","'. $str_e_val . '"'."\n";
																fputs ($fp, $str_data);

																
																
																//echo ($row_max_year[0]-1). " > " .$row_all_q[4]." > " .$str_e_val." E<br>";
																
																if ($str_e_val == "?") {
																	 echo  "SELECT * from dbo.Prod_FinancialValues 
																					WHERE IssuerID = '".$row_rres[0]."' 
																					AND FinancialType = 'EPS'
																					and ProductID = '".$row_pid[0]."'
																					order by ProductID desc, PeriodYear"."<br>";
																}
																//$row_pid[0] . " > " .
																ob_flush();
																flush();
										
														}
												} else { //Check if this is a new company so the relevant data can be extracted.
												echo "Coverage for this company [".$val_symbol."] seems to be discontinued.<br>";
												echo  "SELECT * from dbo.Prod_FinancialValues 
															WHERE IssuerID = '".$row_rres[0]."' 
															AND FinancialType = 'EPS'
															and ProductID = '".$row_pid[0]."'
															order by ProductID desc, PeriodYear"."<br>";
												}
										}
								}
						}
					
					
					/*
					//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
								 //echo "Currently processing : [".$val_symbol."]\n<br>";
								 
								
								  $str_whole = "";
									$lines = array();
									$all_table_data = array();
		 
									$fd = fopen ("http://finance.yahoo.com/q/ae?s=".$val_symbol, "r");
										while (!feof ($fd)) 
											{
												 $buffer = fgets($fd, 4096);
												 $lines[] = $buffer;
											}
									fclose ($fd);
									
									
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


									//echo $str_whole_b."<br><br>";
									if (substr($all_table_data[0][1],0,11)=='Current Qtr') {
									?>
											<table cellpadding="0" cellspacing="0" width="500" >
												<tr>
													<td width="100"><?=$val_symbol?></td>
													<td width="100"><?=str_replace('Current Qtr','',$all_table_data[0][1])?></td>
													<td width="100"><?=str_replace('Next Qtr','',$all_table_data[0][2])?></td>
													<td width="100"><?=str_replace('Current Year','',$all_table_data[0][3])?></td>
													<td width="100"><?=str_replace('Next Year','',$all_table_data[0][4])?></td>
												</tr>
												<tr>
													<td width="100">&nbsp;</td>
													<td width="100"><?=$all_table_data[1][1]?></td>
													<td width="100"><?=$all_table_data[1][2]?></td>
													<td width="100"><?=$all_table_data[1][3]?></td>
													<td width="100"><?=$all_table_data[1][4]?></td>
												</tr>
											</table>
											<br />
									<?
									}
									ob_flush();
									flush();

									//print_r($all_table_data);
									
									//exit;
									
									
					//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					*/
					
					}
		}

fclose($fp);


              echo " Process Time ". sprintf("%01.2f",((getmicrotime()-$time)/1000))." s.";             

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
			
			/*
			[0] => Submitted 05/25/1977 Approved 05/25/1977
            [1] => 005720&nbsp;
            [2] => 321-81490&nbsp;
            [3] => MAY PETROLEUM, INC. (535720)&nbsp;
            [4] => <a href="/DP/drillDownQueryAction.do?fromPublicQuery=Y&amp;name=STATE%2BTRACT%2B195%252C%2BMATAGORDA%2BBAY&amp;univDocNo=64680" title="View details for this application">STATE TRACT 195, MATAGORDA BAY</a>
            [5] => 1  &nbsp;
            [6] => 03&nbsp;
            [7] => MATAGORDA&nbsp;
            [8] => Vertical&nbsp;
            [9] => New Drill&nbsp;
            [10] => Yes&nbsp;
            [11] => 3800&nbsp;
            [12] => Approved&nbsp;
			*/						



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
