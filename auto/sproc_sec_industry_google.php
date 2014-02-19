<?
ini_set('max_execution_time', 3600);

include('../includes/dbconnect.php');
include('../includes/global.php');
include('../includes/functions.php');

//create an array of tickers in sec master
$arr_master = array();
$query_symbol =  "SELECT symbol, length( symbol ) as lengthx 
									FROM sec_master
									WHERE (
									sector IS NULL 
									OR industry IS NULL
									OR sector = ''
									OR industry = '' 
									)
									AND length( symbol ) >0
									ORDER BY symbol";
$result_symbol = mysql_query($query_symbol) or die(mysql_error());
while($row_symbol = mysql_fetch_array($result_symbol))
{
$arr_master[] = $row_symbol["symbol"];	
}

//print_r($arr_master);
//exit;

foreach ($arr_master as $indexval => $symbol_val) {


								//http://finance.yahoo.com/q/in?s=FRX
								
								 echo "Currently processing : [".$symbol_val."]\n";
								 
								
								 $str_whole = "";
		 
									$fd = fopen ("http://finance.google.com/finance?q=".$symbol_val, "r");
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
						
								 $str_whole = str_replace("<TABLE","<table",$str_whole);
								 
								 //exit;

									//echo strposnth($str_whole, "<table", 10, 0); //$str_whole;
									
									//strip everything before the 16th <table
									
									if (strposnth($str_whole, "Sector:&nbsp;", 1, 1)) {
									
											$str_whole_a = substr($str_whole, strposnth($str_whole, "Sector:&nbsp;", 1, 1), 100000);
											
											//strip everything after the first </table
											$str_whole_b = substr($str_whole_a, 0, strposnth($str_whole_a, "</div>", 1, 1)+7);
											
											//extract all data from table into an array
											//$all_table_data = table_into_array($str_whole_b,$needle="",$needle_within=0,$allowed_tags="");
											
											
											//$arr_str_whole_b = table_into_array($str_whole_b,$needle="",$needle_within=0,$allowed_tags="");
											
											//print_r($arr_str_whole_b);
											
											$str_whole_c = strip_tags($str_whole_b);
											
											$str_whole_d = str_replace(">","",$str_whole_c);
											
											$arr_str_whole_e = explode("&nbsp;",$str_whole_d);
											
											//print_r($arr_str_whole_e);
											
											echo $arr_str_whole_e[1]." / ".$arr_str_whole_e[4]."\n";
											
										
									//echo $all_table_data[0][1]."<br>";
									//echo $all_table_data[1][1]."<br>";
									
									$sector = str_replace("&amp;", "&", $arr_str_whole_e[1]);
									$industry = str_replace("&amp;", "&", $arr_str_whole_e[4]);
									
									$result_sector = mysql_query("update sec_master set sector = '".$sector."', industry = '".$industry."' where symbol = '".$symbol_val."'") or die(mysql_error());
									echo "updated database...\n";
									ob_flush();
									flush();
									}

									
}
									
									//Get the content of the file into a csv output file
									
									/*
									foreach ($all_table_data as $keyval=>$arr_value)
										{
										 if ($keyval == 0) {
										 $zline = $arr_value;
										 $str_out = substr($zline[0],10,10)."|".substr($zline[0],30,10)."|".str_replace('&nbsp;','',$zline[1])."|".str_replace('&nbsp;','',$zline[2])."|".str_replace('&nbsp;','',$zline[3])."|".strip_tags($zline[4])."|".str_replace('&nbsp;','',$zline[5])."|".str_replace('&nbsp;','',$zline[6])."|".str_replace('&nbsp;','',$zline[7])."|".str_replace('&nbsp;','',$zline[8])."|".str_replace('&nbsp;','',$zline[9])."|".str_replace('&nbsp;','',$zline[10])."|".str_replace('&nbsp;','',$zline[11])."|".str_replace('&nbsp;','',$zline[12])."\n";
										 $str_out = unhtmlspecialchars($str_out);
										 fwrite($fp,$str_out);
										 }
										 echo ".";
										}
									*/

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