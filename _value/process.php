<?
include('../../../includes/dbconnect.php');
include('../../../includes/functions.php');

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
		while ($row_rres = mssql_fetch_array($ms_results_rres)) {
					//show_array($row_rres);
					$val_symbol = $row_rres[1];
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
					}
		}

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
