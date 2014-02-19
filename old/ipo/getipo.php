<?
include('../includes/dbconnect.php');


//Get current month and year
$current_date = getdate();
//month has to be rpadded with 0
$current_month = str_pad($current_date["mon"], 2, "0", STR_PAD_LEFT);
$current_year = $current_date["year"];

//Get all tickers from database (IPO table) into array.
$ipo_tickers = mysql_query("SELECT ipo_symbol FROM IPO_info where ipo_isactive = 1 ORDER BY ipo_symbol") or die (mysql_error());
$i = 0;
$arr_ipo_tickers = array();
	while ( $row = mysql_fetch_array($ipo_tickers) ) 
	{
		$arr_ipo_tickers[$i] = $row["ipo_symbol"];
		$i = $i+1;
	}

//Get the IPO Tickers for the current month
shell_exec('curl -c cookies_in.txt -b cookies_in.txt -d "user=tocq&password=z4j33td" http://www.ipomonitor.com/IPOMonitorLogin');
shell_exec('curl -c cookies_in.txt -b cookies_in.txt -o check.html "http://www.ipomonitor.com/cgi-bin/ipo/pw/search-complex?cmd=go&formtype=complex&segment=&state=&acs=&logic=AND&odate1_mm='.$current_month.'&odate1_yy='.$current_year.'&odate2_mm='.$current_month.'&odate2_yy='.$current_year.'&submit=+Search+"');

//download the html file from ipomonitor.com with the result IPO tickers from the complex search page
$fd = fopen ("check.html", "r");
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

//extract the section with tickers only
$str_process = substr($str_whole, strpos( $str_whole, "<ol>")+4, strpos( $str_whole, "</ol>")-strpos( $str_whole, "<ol>")+1); 

//Remove </ol>
$str_process = ereg_replace("</ol>","",$str_process);

$arr_symbols_line = explode("<li>", $str_process);
$count_inserted = 0;
$symbol_list_inserted = "";
foreach ($arr_symbols_line as $key=>$value)
  {
   $str_symbol = substr($value, strpos( $value, "&nbsp;&nbsp;")+12, strlen($value)-strpos($value, "&nbsp;&nbsp;"));
	 $str_name = trim(substr($value, strpos($value, ">")+1, strpos($value, "</a>")- strpos($value, ">")-1));
	 $str_name = ereg_replace("'","\'",$str_name);
	 if (strlen(trim($str_symbol)) > 0) {
	   $ticker_val = trim($str_symbol);
	  //Put these tickers in the IPO table in database if it dows not already exist
			if (in_array($ticker_val, $arr_ipo_tickers)) 
				{
						 echo $ticker_val. "[".$str_name."]" .' exists in the database.<br>';
				} else {
				$insert_val = mysql_query("insert into IPO_info(ipo_symbol, ipo_description, ipo_date) values('".$ticker_val."','".$str_name."', now())") or die (mysql_error());
					echo $ticker_val.' added to database.<br>';
					$symbol_list_inserted .= "^^^".$ticker_val;
					$count_inserted = $count_inserted + 1;
				}
		}
	}

//Create/Format a status string
if ($count_inserted > 0) {
	$str_status = $count_inserted . " symbols found. [". $symbol_list_inserted ."]";
} else {
	$str_status = "No IPO symbols found.";
}


//insert status into table				
$insert_status = mysql_query("insert into IPO_status(status, status_date) values('".$str_status. "', now())") or die (mysql_error());

?> 
