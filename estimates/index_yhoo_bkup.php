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
	
	$str_y = "JAN|JAN|-1,JAN|FEB|0,JAN|MAR|0,JAN|APR|0,JAN|MAY|0,JAN|JUN|0,JAN|JUL|0,JAN|AUG|0,JAN|SEP|0,JAN|OCT|0,JAN|NOV|0,JAN|DEC|0,".
           "FEB|JAN|-1,FEB|FEB|-1,FEB|MAR|0,FEB|APR|0,FEB|MAY|0,FEB|JUN|0,FEB|JUL|0,FEB|AUG|0,FEB|SEP|0,FEB|OCT|0,FEB|NOV|0,FEB|DEC|0,".
           "MAR|JAN|-1,MAR|FEB|-1,MAR|MAR|-1,MAR|APR|0,MAR|MAY|0,MAR|JUN|0,MAR|JUL|0,MAR|AUG|0,MAR|SEP|0,MAR|OCT|0,MAR|NOV|0,MAR|DEC|0,".
           "APR|JAN|-1,APR|FEB|0,APR|MAR|0,APR|APR|0,APR|MAY|0,APR|JUN|0,APR|JUL|0,APR|AUG|0,APR|SEP|0,APR|OCT|0,APR|NOV|0,APR|DEC|0,".
           "MAY|JAN|0,MAY|FEB|0,MAY|MAR|0,MAY|APR|0,MAY|MAY|0,MAY|JUN|0,MAY|JUL|0,MAY|AUG|0,MAY|SEP|0,MAY|OCT|0,MAY|NOV|0,MAY|DEC|0,".
           "JUN|JAN|0,JUN|FEB|0,JUN|MAR|0,JUN|APR|0,JUN|MAY|0,JUN|JUN|0,JUN|JUL|0,JUN|AUG|0,JUN|SEP|0,JUN|OCT|0,JUN|NOV|0,JUN|DEC|0,".
           "JUL|JAN|0,JUL|FEB|0,JUL|MAR|0,JUL|APR|0,JUL|MAY|0,JUL|JUN|0,JUL|JUL|0,JUL|AUG|0,JUL|SEP|0,JUL|OCT|0,JUL|NOV|0,JUL|DEC|0,".
           "AUG|JAN|0,AUG|FEB|0,AUG|MAR|0,AUG|APR|0,AUG|MAY|0,AUG|JUN|0,AUG|JUL|0,AUG|AUG|0,AUG|SEP|0,AUG|OCT|0,AUG|NOV|0,AUG|DEC|0,".
           "SEP|JAN|0,SEP|FEB|0,SEP|MAR|0,SEP|APR|0,SEP|MAY|0,SEP|JUN|0,SEP|JUL|0,SEP|AUG|0,SEP|SEP|0,SEP|OCT|0,SEP|NOV|0,SEP|DEC|0,".
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

$arr_FYE = array("January"=>"JAN","February"=>"FEB","March"=>"MAR","April"=>"APR","May"=>"MAY","June"=>"JUN","July"=>"JUL","August"=>"AUG","September"=>"SEP","October"=>"OCT","November"=>"NOV","December"=>"DEC");

//Get tickers from Jovus
# SQL Server Connection Information
$msconnect=mssql_connect("192.168.20.48","BUCKINGHAM_login","BUCKINGHAM_pw");
$msdb=mssql_select_db("BUCKINGHAM",$msconnect);


	xdebug('Connecting to Jovus Server @ Buckingham','Successful');

  //Most recent research date from Jovus
	 
		$arr_rres = array();
		$arr_rres_symbols = array();

		$ms_qry_rres   = "SELECT dbo.Issuers.IssuerID, dbo.ExchangeSecurities.Ticker as CUSIP, dbo.Issuers.FiscalYearEnd
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
    <td width="100">Current Qtr</td>
    <td width="100">Next Qtr</td>
    <td width="100">Current Year</td>
    <td width="100">Next Year</td>
  </tr>
</table>
<br />
<?		
		$v_count_rres = 0;
		while ($row_rres = mssql_fetch_array($ms_results_rres)) {
		
		$v_count_rres = $v_count_rres + 1;
		
		if ($v_count_rres == 10) {
		  exit;
		}
					//show_array($row_rres);
					$val_symbol = $row_rres[1];
					$var_FYE = $arr_FYE[trim($row_rres[2])];
					//xdebug("val_symbol",$val_symbol);
					if ($val_symbol != 'xxx') {
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
												<!--<tr>
													<td width="100"><?=$val_symbol?></td>
													<td width="100">[<?=str_replace('Current Qtr','',$all_table_data[0][1])?>]</td>
													<td width="100">[<?=str_replace('Next Qtr','',$all_table_data[0][2])?>]</td>
													<td width="100">[<?=str_replace('Current Year','',$all_table_data[0][3])?>]</td>
													<td width="100">[<?=str_replace('Next Year','',$all_table_data[0][4])?>]</td>
												</tr>-->
												<tr>
													<td colspan="8">
													<?
													$arr_M_Y = explode("-",trim(str_replace('Current Qtr','',$all_table_data[0][1])));
													
													//echo $var_FYE."////".y2j($var_FYE, $arr_M_Y[0], $arr_M_Y[1])."///".trim(str_replace('Current Qtr','',$all_table_data[0][1]));

													
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
							
								if ($row_pid != '') {
							
										//xdebug("MaxProductID",$row_pid[0]);
										//))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))
										//get relevant data for the qtr
										if ($arr_vals_year_qtr[1] < 4) {
											$ms_qry_relevant_vals = 	"SELECT Q".$arr_vals_year_qtr[1]."Value, Q".($arr_vals_year_qtr[1]+1)."Value, FinancialValue2
											                            FROM dbo.Prod_FinancialValues
																									WHERE IssuerID = '".$row_rres[0]."'
																									AND ProductID = '".$row_pid[0]."'
																									AND FinancialType = 'EPS'
																									AND ProductYear = '".$arr_vals_year_qtr[0]."'";
											$ms_relevant_vals = mssql_query($ms_qry_relevant_vals);
											while ($row_relevant_vals = mssql_fetch_array($ms_relevant_vals)) {
													$curr_qtr =  $row_relevant_vals[0];
													$next_qtr =  $row_relevant_vals[1];
													$curr_year = $row_relevant_vals[2];
											}
											
											$ms_qry_relevant_vals_more = 	"SELECT FinancialValue2
																											FROM dbo.Prod_FinancialValues
																											WHERE IssuerID = '".$row_rres[0]."'
																											AND ProductID = '".$row_pid[0]."'
																											AND FinancialType = 'EPS'
																											AND ProductYear = '".($arr_vals_year_qtr[0]+1)."'";
											$ms_relevant_vals_more = mssql_query($ms_qry_relevant_vals_more);
											while ($row_relevant_vals_more = mssql_fetch_array($ms_relevant_vals_more)) {
													$next_year =  $row_relevant_vals_more[1];
											}

										}
										
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

//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&													
													?>
													</td>
												</tr>
												<tr>
													<td width="100"><?=$val_symbol?></td>
													<td width="100"><?=$all_table_data[1][1]?></td>
													<td width="100"><?=$all_table_data[1][2]?></td>
													<td width="100"><?=$all_table_data[1][3]?></td>
													<td width="100"><?=$all_table_data[1][4]?></td>
													
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
					}
		}

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
