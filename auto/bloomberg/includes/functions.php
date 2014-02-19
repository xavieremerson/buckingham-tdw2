<?
//////Functions
////
// Used to calculate page load time
function getmicrotime(){ 
    list($usec, $sec) = explode(" ",microtime()); 
    return ((float)$usec + (float)$sec*1000); 
} 

////
// Show variable values and debugging information
// $show_debug = 1; shows debug information
	function xdebug ($varname, $varval) {
	$show_debug = 1;
		if ($show_debug == 1 ) {
		echo '<font color="#0000FF"><strong>'.$varname."</strong></font> = [".$varval."]<br>";
		}
	}

////
// Show variable values and debugging information
// $show_debug = 1; shows debug information
	function ydebug ($varname, $varval) {
	$show_debug = 1;
		if ($show_debug == 1 ) {
		echo $varname." = [".$varval."]\n\n";
		}
	}

////
// This function checks if the import/parse/process procedure had taken place already for a given trade date.

function check_process_status ($process_name, $process_date) {

	$str_SQL = "SELECT proc_process, proc_date, proc_status FROM tdw_proc_process_status WHERE proc_process = '".$process_name."' AND proc_date = '".$process_date."' AND proc_status = '1'";
	//ydebug('str_SQL',$str_SQL);  
	$check = mysql_query($str_SQL) or die (mysql_error());

    if (mysql_num_rows($check) >= 1) {
		return 1; //already processed
	} else {
	    return 0; //not processed yet
	}
}

////
// This function generated an auto-incremented number from data in table.

function gen_control_number () {
	$result = mysql_query("SELECT ctrl_control_number FROM ctrl_control_number") or die (mysql_error());
	while ( $row = mysql_fetch_array($result) ) {
		$num_val = $row["ctrl_control_number"];
	}
  	$sql_string = "UPDATE ctrl_control_number SET ctrl_control_number =".($num_val+1);
		$result = mysql_query($sql_string) or die (mysql_error());
		return $num_val;
}


////
// This function count days between $start and $end dates in mysql format (yyyy-mm-dd)
// if one of paramters is 0000-00-00 will return 0
// $start date must be less then $end


function datediff($start, $end)
{
   if( $start != '0000-00-00' and $end != '0000-00-00' )
   {
       $timestamp_start = strtotime($start);
       $timestamp_end = strtotime($end);
       if( $timestamp_start >= $timestamp_end ) return 0;
       $start_year = date("Y",$timestamp_start);
       $end_year = date("Y", $timestamp_end);
       $num_days_start = date("z",strtotime($start));
       $num_days_end = date("z", strtotime($end));
       $num_days = 0;
       $i = 0;
       if( $end_year > $start_year )
       {
           while( $i < ( $end_year - $start_year ) )
           {
             $num_days = $num_days + date("z", strtotime(($start_year + $i)."-12-31"));
             $i++;
           }
         }
         return ( $num_days_end + $num_days ) - $num_days_start;
   }
   else
   {
         return 0;
     }
}


///GENERATES A RANDOM PASSWORD 8 CHARACTERS LONG
function password_generator()
{
	$gen_password = substr(md5(rand(8888888888,9999999999)), 0, 8);
	return $gen_password;
}



////
// As the name suggest, gives x days ahead of given date, which is not a holiday or a weekend (gets next one in line)
// Used in the preparation of demo data.

function dateplusxdays($x,$dateval) {

		$dval = explode("-", $dateval); 
		$y1 = $dval[0];
		$m1 = $dval[1];
		$d1 = $dval[2];
		
		$timeval = mktime(0,0,0, $m1, $d1, $y1);
		
		$newtime = $timeval + (60*60*24*$x);	
		$newday = date("Y-m-d", $newtime);
		
		//return $newday;
 				$dayname = date("l", $newtime);
				if ($dayname == "Monday" or $dayname == "Tuesday" or $dayname == "Wednesday" or $dayname == "Thursday") {
						if ( check_holiday($newday) == 1 ) {
						 	$newtime = $newtime + (60*60*24*1);	
					    $newday = date("Y-m-d", $newtime);
						}
						return $newday;
				} elseif ($dayname == "Friday"){
						if ( check_holiday($newday) == 1 ) {
						 	$newtime = $newtime + (60*60*24*3);	
					    $newday = date("Y-m-d", $newtime);
						}
						return $newday;
				} elseif ($dayname == "Saturday"){
						$newtime = $newtime + (60*60*24*2);
						$newday = date("Y-m-d", $newtime);
						if ( check_holiday($newday) == 1 ) {
						 	$newtime = $newtime + (60*60*24);	
					    $newday = date("Y-m-d", $newtime);
						}
						return $newday;
				} else { //essentially Sunday
						$newtime = $newtime + (60*60*24);
						$newday = date("Y-m-d", $newtime);
						if ( check_holiday($previousday) == 1 ) {
							$newtime = $newtime + (60*60*24);
							$newday = date("Y-m-d", $newtime);
						}
						return $newday;
				}
}


//// 
// Get Price for Ticker from Yahoo. (20 min. delayed)

function getpricefromyahoo($ticker) {

$fd = fopen ("http://quote.yahoo.com/d/quotes.csv?s=".$ticker."&f=sl1d1t1c1ohgv&e=.csv", "r");
$contents = fread ($fd, 200);
fclose ($fd);
 
$contents = str_replace ("\"", "", $contents);
$contents = explode (",", $contents);
 
return $contents[1];

}



////
// Convert Total Cost by removing decimals and putting commas

function format_no_decimal_comma($inputval){

$inputval = round($inputval,0);
$lenval = strlen($inputval);

	if ($lenval > 3){
	
		if     ($lenval == 4) { return substr($inputval,0,1).",".substr($inputval,1,3); }
		elseif ($lenval == 5) { return substr($inputval,0,2).",".substr($inputval,2,3); }
		elseif ($lenval == 6) { return substr($inputval,0,3).",".substr($inputval,3,3); }
		elseif ($lenval == 7) { return substr($inputval,0,1).",".substr($inputval,1,3).",".substr($inputval,4,3); }
		elseif ($lenval == 8) { return substr($inputval,0,2).",".substr($inputval,2,3).",".substr($inputval,5,3); }
		elseif ($lenval == 9) { return substr($inputval,0,3).",".substr($inputval,3,3).",".substr($inputval,6,3); }
		else { return $inputval;}		
	
	} else {
	return $inputval; 
	}

}

////
// Function to change the currency in moxy trda.csv file to number/double.

function process_price ($priceval) {
	if (    substr($priceval,1,1) == '0'	     or substr($priceval,1,1) == '1'	     or substr($priceval,1,1) == '2'	     or substr($priceval,1,1) == '3'	     or substr($priceval,1,1) == '4'	     or substr($priceval,1,1) == '5'	     or substr($priceval,1,1) == '6'	     or substr($priceval,1,1) == '7'	     or substr($priceval,1,1) == '8'	     or substr($priceval,1,1) == '9' ) {			 
	return substr($priceval, 1, strlen($priceval)-1);
	}	else {
		if (  substr($priceval,2,1) == '0'				 or substr($priceval,2,1) == '1'				 or substr($priceval,2,1) == '2'				 or substr($priceval,2,1) == '3'				 or substr($priceval,2,1) == '4'				 or substr($priceval,2,1) == '5'				 or substr($priceval,2,1) == '6'				 or substr($priceval,2,1) == '7'				 or substr($priceval,2,1) == '8'				 or substr($priceval,2,1) == '9' ) {				 
			return substr($priceval, 2, strlen($priceval)-2);
			} else {
				if (  substr($priceval,3,1) == '0'					 or substr($priceval,3,1) == '1'					 or substr($priceval,3,1) == '2'					 or substr($priceval,3,1) == '3'					 or substr($priceval,3,1) == '4'					 or substr($priceval,3,1) == '5'					 or substr($priceval,3,1) == '6'					 or substr($priceval,3,1) == '7'					 or substr($priceval,3,1) == '8'					 or substr($priceval,3,1) == '9' ) {
				return substr($priceval, 3, strlen($priceval)-3);
				} else {
						if (  substr($priceval,4,1) == '0'							 or substr($priceval,4,1) == '1'							 or substr($priceval,4,1) == '2'							 or substr($priceval,4,1) == '3'							 or substr($priceval,4,1) == '4'							 or substr($priceval,4,1) == '5'							 or substr($priceval,4,1) == '6'							 or substr($priceval,4,1) == '7'							 or substr($priceval,4,1) == '8'							 or substr($priceval,4,1) == '9' ) {
						return substr($priceval, 4, strlen($priceval)-4);
						} else {
						return "ERROR!";
						}
				}
			}
		}
	}


////
// Offset B and S in emails and reports for ease of viewing.
	function offset_buy_sell($buysellval) {
		if ($buysellval == 'B') {
			return "B";
		} else {
			return "&nbsp;&nbsp;&nbsp;&nbsp;S";
		}
	}
	
////
// Get list of recipients based on the email preferences in Users tableS.
  function email_report_to() {
		$result_ = mysql_query("SELECT Email FROM Users where Report_via_email = 'Yes'") or die (mysql_error());
		while ( $row = mysql_fetch_array($result_) ) {
			$return_list = $return_list.",".$row["Email"];
		}
		return $return_list;	
	} 	

////
// Convert Buy Sell Status for display in the browser.

function convert_buy_sell($val){

		if     ($val == 'sl'){ return 'S';}		
		elseif ($val == 'by'){ return 'B';}
		elseif ($val == 'cs'){ return 'C';}
		elseif ($val == 'Cover'){ return 'C';}
		elseif ($val == 'Sell'){ return 'S';}
		elseif ($val == 'Buy'){ return 'B';}
		else {return '?';}

}

////
// Write status to a table Status_system with the information: severity (1-3), message, datetime, user
// 1=Info, 2=Warning, 3=Error

function write_status ($severity, $statusmsg) {

	$writestatus = mysql_query("insert into Status_system(ssys_severity, ssys_message, ssys_datetime, ssys_user) values('$severity', '$statusmsg', now(), '$user')") or die (mysql_error());

	return 1;
}

////
// Check if a given date is a holiday based on holiday entry in table

function check_holiday ($checkdate) {
	$check = mysql_query("SELECT holi_date from holidays where holi_date = '$checkdate'") or die (mysql_error());
  if (mysql_num_rows($check) >= 1) {
	return 1;
	} else {
	return 0;
	}		
}

////
// Check if a given RBC Trade File has been downloaded
// if return is 0 then proceed with download

function check_rbc_download ($checkfile) {
	$check = mysql_query("SELECT sdow_filename from Status_downloads where sdow_filename = '$checkfile'") or die (mysql_error());
  if (mysql_num_rows($check) >= 1) {
		//File exists. Now check status of download
		$result = mysql_query("SELECT sdow_status from Status_downloads where sdow_filename = '$checkfile'") or die (mysql_error());
					while ( $row = mysql_fetch_array($result) ) {
					$downloadstatus = $row["sdow_status"];
					}
					if ($downloadstatus == 0) {
					return 0;
					} else {
					return 1;
					}
	} else {
	return 0;
	}		
}

////
// Create Trade Report HTML File and put in /data/exports folder
function create_trade_report($email, $mailsubject, $mailbodysubinfo, $emailheading )
		{
		
		// INCLUDE GLOBAL.PHP for CONSTANTS
			 include('includes/global.php');
		 

					$mailbody = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
					<html>
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
					<style type="text/css">
					<!--
					.names {font-family: Verdana; font-size: 12px; font-weight: bold; color: #333399;}
					.others {font-family: Verdana; font-size: 12px; font-weight: normal; color: #336699;}
					.companyname {font-family: "Times New Roman, Times, serif"; font-size: 16px; font-weight: bold; color: #FFFFFF;}
					.appname {font-family: Verdana; font-size: 14px; font-weight: bold; color: #FFFFFF;}
					.heading {font-family: Verdana; font-size: 12px; font-weight: bold; color: #FFFFFF;}
   				    .headingblue {font-family: Verdana; font-size: 12px; font-weight: bold; color: #0000FF;}

					tr.tableheading {	font-family: verdana;	font-size: 12px;	text-decoration: underline;	color: #660000;	font-weight: bold;	text-align: left;	vertical-align: middle;	background-position: left center;	border: 1px solid #0000FF;	background-color: #FFFFFF;}
					tr.tablerow {font-family: verdana;font-size: 10px;text-decoration: none;	color: #000099;	font-weight: normal;	text-align: left;	vertical-align: middle;	background-position: left center;	border: 1px solid #0000FF;}
					tr.tablerowhighlight {font-family: verdana;font-size: 10px;text-decoration: none;	color: #FF0000;	font-weight: bold;	text-align: left;	vertical-align: middle;	background-position: left center;	border: 1px solid #0000FF;}
					-->
					</style>					
					</head>
					<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0">
					<table width="100%" height="100%"  border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td valign="top" height="14" bgcolor="#21427B">
						<table width="100%">
							<tr>
								<td class="companyname">'.$_company_name.'</td>
								<td class="appname" align="right">'.$_app_name.' '.$_version.'</td>
							</tr>
							<tr>
								<td colspan=2 class="heading">'.$emailheading.'</td>
							</tr>
						</table>
						</td>
					</tr>
					<tr>
						<td height="6" bgcolor="#999999"></td>
					</tr>
					<tr>
						<td valign="top" bgcolor="#FFFFFF">';
						
					$mailbody .= $mailbodysubinfo; 
							
					$mailbody .='</td>
										</tr>
										<tr>
											<td height="6" bgcolor="#999999"></td>
										</tr>
								</table>
								</body>
								</html>';
								
			//Copy this email body info to a html file and put it in /data/exports folder
			
			$trade_date_to_process_a = previous_business_day();
			
			$fp = fopen($exportlocation."Trades_Report_".$trade_date_to_process_a.".html", "w");

			fputs ($fp, $mailbody);

			fclose($fp);
				
}


////
// Gets the previous business day given a certain day
// IF argument is null then current date (Unix Timestamp) is taken as input
// CAUTION: This function takes Unix Timestamp as argument
//          Use strtotime() wherever required to create argument.
//

function previous_business_day () {

 				$dayname = date("l");
				if ($dayname == "Wednesday" or $dayname == "Thursday" or $dayname == "Friday") {
						$previoustime = time() - (60*60*24*1);
						$previousday = date("Y-m-d", $previoustime);
						if ( check_holiday($previousday) == 1 ) {
						 	$previoustime = time() - (60*60*24*2);	
					    $previousday = date("Y-m-d", $previoustime);
						}
						return $previousday;
				} elseif ($dayname == "Monday"){
						$previoustime = time() - (60*60*24*3);
						$previousday = date("Y-m-d", $previoustime);
						if ( check_holiday($previousday) == 1 ) {
						 	$previoustime = time() - (60*60*24*4);	
					    $previousday = date("Y-m-d", $previoustime);
						}
						return $previousday;
				} elseif ($dayname == "Tuesday"){
						$previoustime = time() - (60*60*24*1);
						$previousday = date("Y-m-d", $previoustime);
						if ( check_holiday($previousday) == 1 ) {
						 	$previoustime = time() - (60*60*24*4);	
					    $previousday = date("Y-m-d", $previoustime);
						}
						return $previousday;
				} elseif ($dayname == "Saturday"){
						$previoustime = time() - (60*60*24*1);
						$previousday = date("Y-m-d", $previoustime);
						if ( check_holiday($previousday) == 1 ) {
						 	$previoustime = time() - (60*60*24*2);	
					    $previousday = date("Y-m-d", $previoustime);
						}
						return $previousday;
				} else { //essentially Sunday
						$previoustime = time() - (60*60*24*2);
						$previousday = date("Y-m-d", $previoustime);
						if ( check_holiday($previousday) == 1 ) {
						 	$previoustime = time() - (60*60*24*3);	
					    $previousday = date("Y-m-d", $previoustime);
						}
						return $previousday;
				}

}


//RETURNS A DAY(YYYY-MM-DD) BEFORE $days BUSINESS DAYS.  THE ARGUMENT GIVES INPUT DATE AND THE NUMBER OR BUSINESS DAYS TO GO BACK
//DATE INPUT IS IN THE FORMAT OF strtotime() FUNCTION AND DAYS INPUT IS INT
function business_day_backward($str_date, $days)
{
	$i = 0;
	$j = 0;
	$checkDay = '';
	$subString = $str_date;
	
	while($i < 40)
	{
		$subString = $subString-(60*60*24*1);
		if(date("w", $subString) != 0 AND date("w", $subString) != 6 AND !(check_holiday(date("Y-m-d", $subString))))
		{
			$j++;
			$checkDay =date("Y-m-d", $subString);
		}
		if($j == $days)
		break;
	}
	return $checkDay;
}

//RETURNS A DAY(YYYY-MM-DD) BEFORE $days DAYS.  THE ARGUMENT GIVES INPUT DATE AND THE NUMBER OR DAYS TO GO BACK
//DATE INPUT IS IN THE FORMAT OF strtotime() FUNCTION AND DAYS INPUT IS INT
function days_backward($str_date, $days)
{
	$checkDay = '';
	$subString = $str_date;
	$subString = $subString-(60*60*24*$days);
	$checkDay =date("Y-m-d", $subString);
	
	return $checkDay;
}


//RETURNS A DAY(YYYY-MM-DD) AFTER $days BUSINESS DAYS.  THE ARGUMENT GIVES INPUT DATE AND THE NUMBER OR BUSINESS DAYS TO GO FORWARD
//THIS IS MAINLY TO CALCULATE SETTLE DATE
//DATE INPUT IS IN THE FORMAT OF strtotime() FUNCTION AND DAYS INPUT IS INT
//This function will also be used to do a bulk process starting on a given day and going forward n days.

function business_day_forward($str_date, $days)
{
	$i = 0;
	$j = 0;
	$checkDay = '';
	$subString = $str_date;
	
	while($i < 40)
	{
		$subString = $subString+(60*60*24*1);
		if(date("w", $subString) != 0 AND date("w", $subString) != 6 AND !(check_holiday(date("Y-m-d", $subString))))
		{
			$j++;
			$checkDay =date("Y-m-d", $subString);
		}
		if($j == $days)
		break;
	}
	return $checkDay;
}



//CONVERTS 00:00 AM/PM  TO YYYY-MM-DD HH:MM:SS
function format_time_to_military($time_input)
{
	if($time_input != '')
	{
		$time_input = trim($time_input);
		$time = explode(" ", $time_input);
		$hour = explode(":", $time[0]);
		
		if($time[1] == "AM")
		{
			if($hour[0] == 12)
			{
				$hour[0] = "00";
			}
			return "0000-00-00 " . $hour[0].":".$hour[1].":".$hour[2];
		}
		
		if($time[1] == "PM")
		{
			if($hour[0] < 12)
			{
				$hour[0] = ($hour[0] + 12);	
			}
			return "0000-00-00 " . $hour[0].":".$hour[1].":".$hour[2];
		}	
	}
	else
	return "0000-00-00 00:00:00";
}

////
// Displays the time in 12 hr am/pm format
// 9:45 will be 9:45 am and 13:45 will be 1:45 pm
function military_to_ampm ($time_input) 
{
	$ampm = " AM";
	
	if ($time_input != '') 
	{
		$time_hr = substr($time_input,0,2);
		if ($time_hr > 12) 
		{
		$time_hr = $time_hr - 12;
		$ampm = " PM";
		}
		if ($time_hr == 12) 
		{
		$ampm = " PM";
		}
		$time_min = substr($time_input,3,2);
		
		return $time_hr.":".$time_min.$ampm;
	} 
	else 
	{
		return "--:--";
	}
}


////
// Displays the time in 12 hr am/pm format
// 945 will be 9:45 am and 1345 will be 1:45 pm
function format_time ($time_input) {

$ampm = " am";

		if ($time_input != '') {

			if (strlen($time_input) == 3) {
			
				$time_hr = substr($time_input,0,1);
				$time_min = substr($time_input,1,2);
			} else {
				$time_hr = substr($time_input,0,2);
				if ($time_hr > 12) {
				$time_hr = $time_hr - 12;
				$ampm = " pm";
				}
				if ($time_hr == 12) {
				$ampm = " pm";
				}
				$time_min = substr($time_input,2,2);
			}
			
			return $time_hr.":".$time_min.$ampm;
			
		} else {
		return "--:--";
		}
}


////
// Converts YYYY-MM-DD to MM/DD/YYYY

function format_date_ymd_to_mdy ($date_input) {

	if ($date_input != '') {
		$date=explode("-",trim($date_input));
		return $date[1]."/".$date[2]."/".$date[0]; 
	} 
	else {
		return "--/--/----";
	}
	
}

////
// Converts  MM/DD/YYYY to YYYY-MM-DD

function format_date_mdy_to_ymd ($date_input) {

	if ($date_input != '') {
		$date=explode("/",trim($date_input));
		return $date[2]."-".$date[0]."-".$date[1]; 
	} 
	else {
		return "--/--/----";
	}
	
}


////
// Creates a dropdown with recordset
   function createdropdown($data_query) {

       while ($dataset = tep_db_fetch_array($data_query)) {
       
			 echo '<option value="' . $dataset["d_value"] . '">' . $dataset["d_option"] . '</option>'."\n";

       }
	}

	
////
// Creates a dropdown with recordset
function createdropdown1($data_query) 
{

	while ($dataset = tep_db_fetch_array($data_query)) 
	{   
		$spaces = '&nbsp;';
		$option = $dataset["d_option"];
		
		list($tick, $desc) = explode(":",$option);

		if(strlen($tick) == 2)
		{
			$spaces = $spaces . "&nbsp;&nbsp;";
		}
		
		if(strlen($tick) == 3)
		{
			$spaces = $spaces . "&nbsp;";
		}
		//if(strlen($tick) 
		   
		$dataset["d_option"] = $tick . $spaces . ": " . $desc;
		
		   
		echo '<option value="' . $dataset["d_value"] . '">' . $dataset["d_option"] . '</option>'."\n";
	}
}
	
// Creates a dropdown with recordset
function createdropdown2($data_query, $id) 
{
	if($id == '')
	$id = 1;

	while ($dataset = tep_db_fetch_array($data_query)) 
	{
		echo "<option value='" . $dataset['d_value'] . "'"; 
		if($dataset['d_value'] == $id)
		{
			echo 'selected';
		}
		echo ">" . $dataset['d_option'] . "</option>"."\n";
   }
}

// Creates a dropdown with recordset
function createdropdown3($data_query, $id) 
{
	if($id == '')
	$id = 1;

	while ($dataset = tep_db_fetch_array($data_query)) 
	{
		echo "<option value='" . $dataset['d_value'] . "'"; 
		
		if($id == 1)
		{
			if($dataset['d_option'] == 'Limit')
			{
				echo 'selected';
			}
		
		}
		else
		if($id == 2)
		{
			if($dataset['d_option'] == 'Day')
			{
				echo 'selected';
			}
		}
		else
		if($id == 3)
		{
			if($dataset['d_option'] == 'None')
			{
				echo 'selected';
			}
		}
		
		
		echo ">" . $dataset['d_option'] . "</option>"."\n";
   }
}

	
	
	  function tep_db_connect($server = DB_SERVER, $username = DB_SERVER_USERNAME, $password = DB_SERVER_PASSWORD, $database = DB_DATABASE, $link = 'db_link') {
    global $$link;

    if (USE_PCONNECT == 'true') {
      $$link = mysql_pconnect($server, $username, $password);
    } else {
      $$link = mysql_connect($server, $username, $password);
    } 

    if ($$link) mysql_select_db($database);

    return $$link;
  }

  function tep_db_close($link = 'db_link') {
    global $$link;

    return mysql_close($$link);
  }

  function tep_db_error($query, $errno, $error) { 
    die('<font color="#000000"><b>' . $errno . ' - ' . $error . '<br><br>' . $query . '<br><br><small><font color="#ff0000">[TEP STOP]</font></small><br><br></b></font>');
  }

  function tep_db_query($query, $link = 'db_link') {
    global $$link;

    if (defined('STORE_DB_TRANSACTIONS') && (STORE_DB_TRANSACTIONS == 'true')) {
      error_log('QUERY ' . $query . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
    }

    $result = mysql_query($query, $$link) or tep_db_error($query, mysql_errno(), mysql_error());

    if (defined('STORE_DB_TRANSACTIONS') && (STORE_DB_TRANSACTIONS == 'true')) {
       $result_error = mysql_error();
       error_log('RESULT ' . $result . ' ' . $result_error . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
    }

    return $result;
  }

  function tep_db_perform($table, $data, $action = 'insert', $parameters = '', $link = 'db_link') {
    reset($data);
    if ($action == 'insert') {
      $query = 'insert into ' . $table . ' (';
      while (list($columns, ) = each($data)) {
        $query .= $columns . ', ';
      }
      $query = substr($query, 0, -2) . ') values (';
      reset($data);
      while (list(, $value) = each($data)) {
        switch ((string)$value) {
          case 'now()':
            $query .= 'now(), ';
            break;
          case 'null':
            $query .= 'null, ';
            break;
          default:
            $query .= '\'' . tep_db_input($value) . '\', ';
            break;
        }
      }
      $query = substr($query, 0, -2) . ')';
    } elseif ($action == 'update') {
      $query = 'update ' . $table . ' set ';
      while (list($columns, $value) = each($data)) {
        switch ((string)$value) {
          case 'now()':
            $query .= $columns . ' = now(), ';
            break;
          case 'null':
            $query .= $columns .= ' = null, ';
            break;
          default:
            $query .= $columns . ' = \'' . tep_db_input($value) . '\', ';
            break;
        }
      }
      $query = substr($query, 0, -2) . ' where ' . $parameters;
    }

    return tep_db_query($query, $link);
  }

  function tep_db_fetch_array($db_query) {
    return mysql_fetch_array($db_query, MYSQL_ASSOC);
  }

  function tep_db_num_rows($db_query) {
    return mysql_num_rows($db_query);
  }

  function tep_db_data_seek($db_query, $row_number) {
    return mysql_data_seek($db_query, $row_number);
  }

  function tep_db_insert_id() {
    return mysql_insert_id();
  }

  function tep_db_free_result($db_query) {
    return mysql_free_result($db_query);
  }

  function tep_db_fetch_fields($db_query) {
    return mysql_fetch_field($db_query);
  }

  function tep_db_output($string) {
    return htmlspecialchars($string);
  }

  function tep_db_input($string) {
    return addslashes($string);
  }

  function tep_db_prepare_input($string) {
    if (is_string($string)) {
      return trim(tep_sanitize_string(stripslashes($string)));
    } elseif (is_array($string)) {
      reset($string);
      while (list($key, $value) = each($string)) {
        $string[$key] = tep_db_prepare_input($value);
      }
      return $string;
    } else {
      return $string;
    }
  }
	
////
// Create message with image depending on severity
// 1 = green, 2 = orange, 3 = red
   function sys_message($severity, $msgtext) {
	 
	 
	 //Rounded corner tables used across the application
$table_start = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td width="7" height="7" background="images/tables/lt.jpg"></td>
    <td background="images/tables/ts.jpg"></td>
    <td width="7" height="7" background="images/tables/rt.jpg"></td>
  </tr>
  <tr> 
    <td width="7" background="images/tables/ls.jpg"></td>
    <td>';
$table_end = '</td>
    <td width="7" background="images/tables/rs.jpg"></td>
  </tr>
  <tr> 
    <td width="7" height="7" background="images/tables/lb.jpg"></td>
    <td background="images/tables/bs.jpg"></td>
    <td width="7" height="7" background="images/tables/rb.jpg"></td>
  </tr>
</table>';

   if ($severity == 1){
	 $imagefile = 'msg_success.gif';
 	 $varcssstyle = 'links10';
	 }
	 elseif ($severity == 2){
	 $imagefile = 'msg_warning.gif';
 	 $varcssstyle = 'links10';
	 }
	 elseif ($severity == 3){
	 $imagefile = 'msg_error.gif';
 	 $varcssstyle = 'links10';
	 }
	 else {
	 $imagefile = 'msg_warning.gif';
 	 $varcssstyle = 'msg_warning';
	 }
	 
	 
   echo '<tr><td valign="top">'.$table_start.'<img src="images/' . $imagefile . '"><a class="' . $varcssstyle . '">' . $msgtext . '</a>'.$table_end.'</td></tr>';
	 						 
	 }	 
	 

////
// Function to get Company Name given a ticker. User for data entry by tickers in Lists
function get_company_name($symbol) {

	//$symbol = $_POST['symbol']; 
	
	//$symbol = "MSFT,AA,T";

  $quotes = new Quotes(); 
	
	$symbols = explode(",",$symbol) ; 
	
	for ($n=0; $n<count($symbols); $n++)
    {
        $quotes->mSetSymbol(strtoupper($symbols[$n])) ; 
        $quotes->mLoadYahoo() ;
				return $quotes->_strCompany;
		}
		
}


class Quotes {

    var $_strSymbol  ; 
    var $_strCompany;
    var $_strLastPrice ; 
    var $_strTradeDate ; 
    var $_strTradeTime ; 
    var $_strChange ; 
    var $_strPercentChange ; 
    var $_strVolume ; 
    var $_strBid ; 
    var $_strAsk ; 
    var $_strPrevClose ; 
    var $_strOpen ; 
    var $_strYield ; 
    var $_strDivShare  ; 
    var $_strMarketCap ; 
   
   
    function Quotes()
    {
    }


    function mSetSymbol($symbol) 
    {
        $this->strSymbol = $symbol ; 
    }

    function mLoadYahoo () 
    {
	    /* if multiple symbols, replace the space with a + */
	    #$allsymbols=ereg_replace( " ", "+", $this->strSymbol );
        $allsymbols = $this->strSymbol ; 
	    $YAHOO_URL = ("http://quote.yahoo.com/d?f=snl1d1t1c1p2va2bapomwerr1dyj1&s=$allsymbols");
	    $file = fopen("$YAHOO_URL","r"); 
	
	    while ($data = fgetcsv($file,4096, ",")) 
		{
            $this->_strSymbol = $data[0] ;
            $this->_strCompany = $data[1] ; 
            $this->_strLastPrice = $data[2] ; 
            $this->_strTradeDate = $data[3] ; 
            $this->_strTradeTime = $data[4] ;             
            $this->_strChange = $data[5] ;
            $this->_strChangePercent = $data[6] ;  
            $this->_strVolume = $data[7] ; 
	    }
        
		//echo "<pre>"; 
		//print_r($hash) ;  

	    return $hash;
    }
}


function table_start($width, $title) {

echo			'<table width="'.$width.'" border="0" cellpadding="0" cellspacing="0" class="test">
						<tr> 
							<td>
								<table width="'.($width-2).'" border="0" cellpadding="0" cellspacing="0">
									<tr> 
										<td height="20" valign="middle" background="images/tables3/header_bk.jpg">
										&nbsp;&nbsp;<a class="table_heading_text">'.$title.'</a>
										</td>
									</tr>
									<tr> 
										<td valign="middle">';
}

function table_end() {

echo 			'				</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>';
}

function table_start_percent($width, $title) {

echo			'<table width="'.$width.'%" border="0" cellpadding="0" cellspacing="0" class="test">
						<tr> 
							<td>
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
									<tr> 
										<td height="20" valign="middle" background="images/tables3/header_bk.jpg">
										&nbsp;&nbsp;<a class="table_heading_text">'.$title.'</a>
										</td>
									</tr>
									<tr> 
										<td valign="middle">';
}

function table_end_percent() {

echo 			'				</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>';
}

function table_start_percent2($width, $title) {
  echo '    <table width="'.$width.'%" border="0" cellpadding="0" cellspacing="0" class="test" bgcolor="#F4F8FB">
      <thead class="datadisplay">
        <tr style="background-image: url(images/tables3/header_bk.jpg)">
          <td colspan="3" height="20">&nbsp;&nbsp;<a class="table_heading_text">'.$title.'</a></td>
        </tr>';
}

function table_end_percent2() {
  echo '      </tr>
    </tbody>
  </table>';
}

// This function uses the PHPMailer to sent html/text emails with or without attachments.
// Tested with the following
// Outlook 2003
// Yahoo
// Hotmail
// Gmail
// AOL

// TODO : Let the attachment be passed either as arrays or singletons and proces them within function depending on what they are.

require("class.phpmailer.php");

function zMailer($from_email, $from_name, $to_email, $to_name, $subject, $html_body, $text_body, $attachmentfullpath, $attachmentname) {

$mail = new phpmailer();

$mail->From     = $from_email;
$mail->FromName = $from_name;
$mail->Host     = "localhost";
$mail->Mailer   = "smtp"; 

$mail->Subject  = $subject;

    // HTML body
    $mail->Body    = $html_body;
    // Plain text body (for mail clients that cannot read HTML)
    $mail->AltBody = $text_body;
		
    $mail->AddAddress($to_email, $to_name);
    
	$mail->AddAttachment($attachmentfullpath, $attachmentname);

    if(!$mail->Send())
        echo "There has been a mail error sending to ".$to_name." <".$to_email.">";

    // Clear all addresses and attachments for next loop
    $mail->ClearAddresses();
    $mail->ClearAttachments();
		
		echo "Mail sent to ".$to_email;
}	


//// System Emailer with single or multiple file attachments
// $attachmentname, $attachmentfullpath is in an associative array called $attachment
function zSysMailer($to_email, $to_name, $subject, $html_body, $text_body, $attachment) {

	$mail = new PHPMailer();
	$mail->IsSMTP();                           // tell the class to use SMTP
	$mail->SMTPAuth   = true;                  // enable SMTP authentication
	$mail->Port       = 587;                    // set the SMTP server port
	$mail->Host       = "owa.smarshexchange.com"; // SMTP server
	$mail->Username   = "TDW@buckresearch.com";     // SMTP server username
	$mail->Password   = "BRmail678";            // SMTP server password

	$mail->AddReplyTo("TDW@buckresearch.com","TDW Buckingham");

	$mail->From       = "TDW@buckresearch.com";
	$mail->FromName   = "TDW Buckingham";

$mail->Subject  = $subject;

    // HTML body
    $mail->Body    = $html_body;
    // Plain text body (for mail clients that cannot read HTML)
    $mail->AltBody = $text_body;
		
    $mail->AddAddress($to_email, $to_name);
    
		if (is_array($attachment)) {
			foreach ($attachment as $attachmentname => $attachmentfullpath) {
			echo $attachmentname."<br>";
			echo $attachmentfullpath."<br>";
				$mail->AddAttachment($attachmentfullpath, $attachmentname);
			}	
		}
		
    if(!$mail->Send())
        echo "There has been a mail error sending to ".$to_name." (".$to_email.")";

    // Clear all addresses and attachments for next loop
    $mail->ClearAddresses();
    $mail->ClearAttachments();
		
		echo "<br>Mail sent to ".$to_name. " (".$to_email. ")";
}	

////
//
function zMailHeader ($headerinfo) {

	return '<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
.bodytext12 {	font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 12px;	color: #003399;}
.bodytext9 {	font-family: "Times New Roman", Times, serif;	font-size: 9px;	color: #003399;}
.CompanyName {	font-family: "Times New Roman", Times, serif;	font-size: 14px;	font-weight: bold;	color: #21427B;	letter-spacing: 3px;}
.AppName {	font-family: Verdana;	font-size: 14px;	font-weight: bold; 	color: #21427B;}
.bodytext12bb {	font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 12px;	color: #000000;	font-weight: normal;}
.emailbodytext9 {	font-family: "Times New Roman", Times, serif;	font-size: 9px;	color: #003399;}
.emailbodytext12 {	font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 12px;	color: #003399;}
.emailbodytext12bb {	font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 12px;	color: #000000;	font-weight: normal;}
.emailbodytext12bluebold {	font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 12px;	color: #000000;	font-weight: bold;}
.background_heading_row {font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 12px;	color: #FFFFFF;	font-weight: bold; background-color: #792020;}
.background_data_row_color {font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 12px;	color: #000000;	font-weight: normal; background-color: #E6E6E6;}
.background_data_row_white {font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 12px;	color: #000000;	font-weight: normal; background-color: #FFFFFF;}
-->
</style>
</head>
									
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0">
<table width="100%" height="100%"  border="0" cellspacing="0" cellpadding="10">
	<tr> 
		<td>
			<table width="100%" height="100%"  border="0" cellpadding="1" cellspacing="0" bgcolor="#91B5E7">
				<tr> 
					<td>
						<table width="100%" height="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFFFFF">
							<tr> 
								<td height="55" valign="top">
									<table width="100%"  border="0" cellpadding="0" cellspacing="0" >
										<tr> 
											<td width="50" height="50"><img src="http://csysg.dyndns.org:83/brg/images/logo.gif" border="0"></td>
											<td align="left" valign="top" class="bodytext12bb">'.$headerinfo.'</td>
											<td width="200" valign="top"><img src="http://csysg.dyndns.org:83/brg/images/client_app.jpg" border="0"></td>
										</tr>
									</table>
									<hr size="4" noshade color="#91B5E7">
								</td>
							</tr>
							<tr>
								<td height="100" valign="top">';
}		

function zMailFooter ($footerinfo) {

	return '
								</td>
							</tr>
							<tr> 
								<td valign="bottom">
									<table width="100%"  border="0" cellspacing="0" cellpadding="0">
										<tr> 
											<td>&nbsp;</td>
										</tr>
										<tr>
											<td><a class="bodytext12">Please do not reply to this email address. It is not equipped to handle user enquiries.</a></td>
										</tr>
										<tr> 
											<td valign="bottom" align="right"><a class="bodytext9">'.$footerinfo.'</a></td>
										</tr>
									</table>																	
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</body>
</html>';

}

////
//
function zSysMailHeader ($headerinfo) {

	return '<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
.bodytext12 {	font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 12px;	color: #003399;}
.bodytext9 {	font-family: "Times New Roman", Times, serif;	font-size: 9px;	color: #003399;}
.CompanyName {	font-family: "Times New Roman", Times, serif;	font-size: 14px;	font-weight: bold;	color: #21427B;	letter-spacing: 3px;}
.AppName {	font-family: Verdana;	font-size: 14px;	font-weight: bold; 	color: #21427B;}
.bodytext12bb {	font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 12px;	color: #000000;	font-weight: normal;}
.emailbodytext9 {	font-family: "Times New Roman", Times, serif;	font-size: 9px;	color: #003399;}
.emailbodytext12 {	font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 12px;	color: #003399;}
.emailbodytext12bb {	font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 12px;	color: #000000;	font-weight: normal;}
.emailbodytext12bluebold {	font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 12px;	color: #000000;	font-weight: bold;}
.background_heading_row {font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 12px;	color: #FFFFFF;	font-weight: bold; background-color: #792020;}
.background_data_row_color {font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 12px;	color: #000000;	font-weight: normal; background-color: #E6E6E6;}
.background_data_row_white {font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 12px;	color: #000000;	font-weight: normal; background-color: #FFFFFF;}
-->
</style>
</head>
									
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0">
<table width="100%" height="100%"  border="0" cellspacing="0" cellpadding="10">
	<tr> 
		<td>
			<table width="100%" height="100%"  border="0" cellpadding="1" cellspacing="0" bgcolor="#91B5E7">
				<tr> 
					<td>
						<table width="100%" height="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFFFFF">
							<tr> 
								<td valign="top">
									<table width="100%"  border="0" cellpadding="0" cellspacing="0" >
										<tr> 
											<td width="50"><img src="http://csysg.dyndns.org:83/brg/images/email/logo.gif" border="0"></td>
											<td align="left" valign="top" class="bodytext12bb">&nbsp;</td>
											<td width="200" valign="top"><img src="http://csysg.dyndns.org:83/brg/images/email/client_app.jpg" border="0"></td>
										</tr>
									</table>
									<hr size="4" noshade color="#91B5E7">
								</td>
							</tr>
							<tr>
								<td height="100" valign="top">';
}

function zSysMailFooter () {

	return '
								</td>
							</tr>
							<tr> 
								<td valign="bottom">
									<table width="100%"  border="0" cellspacing="0" cellpadding="0">
										<tr> 
											<td>&nbsp;</td>
										</tr>
										<tr>
											<td><a class="bodytext12">Please do not reply to this email address. It is not equipped to handle user enquiries.</a></td>
										</tr>
										<tr> 
											<td valign="bottom" align="right"><a class="bodytext9">'.date("D, m/d/Y h:i a").' CTRL# : '.gen_control_number().'</a></td>
										</tr>
									</table>																	
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</body>
</html>';

}

function generate_email_footer() {

return date("D, m/d/Y h:i a").' CTRL# : '.gen_control_number();

}


function getshortname($acctnum) {
	$result = mysql_query("SELECT nadd_address_line_1 from nfs_nadd where nadd_full_account_number ='".$acctnum."'") or die (mysql_error());
	while ( $row = mysql_fetch_array($result) ) {
		$nameval = $row["nadd_address_line_1"];
	}
		return $nameval;
}




////
// same year? take YYYY-MM-DD as input
function sameyear($old, $new) {
		$date_old=explode("-",trim($old));
		$date_new=explode("-",trim($new));
    if ($date_old[0] == $date_new[0]) {
		return 1;
		} else {
		return 0;
		}
}	

////
// same month? take YYYY-MM-DD as input
function samemonth($old, $new) {
		$date_old=explode("-",trim($old));
		$date_new=explode("-",trim($new));
    if ($date_old[0] == $date_new[0] AND $date_old[1] == $date_new[1]) {
		return 1;
		} else {
		return 0;
		}
}	

////
// same quarter? take YYYY-MM-DD as input
function sameqtr($old, $new) {
		$arr_q1 = array("01","02","03");
		$arr_q2 = array("04","05","06");
		$arr_q3 = array("07","08","09");
		$arr_q4 = array("10","11","12");

		$date_old=explode("-",trim($old));
		$date_new=explode("-",trim($new));
	
    if ($date_old[0] == $date_new[0]) {
	
				if (in_array($date_old[1], $arr_q1) and in_array($date_new[1], $arr_q1) ) {
				return 1;
				} elseif  (in_array($date_old[1], $arr_q2) and in_array($date_new[1], $arr_q2) ) {
				return 1;
				} elseif  (in_array($date_old[1], $arr_q3) and in_array($date_new[1], $arr_q3) ) {
				return 1;
				} elseif  (in_array($date_old[1], $arr_q4) and in_array($date_new[1], $arr_q4) ) {
				return 1;
				} else {
				return 0;
				}				
		} else {
				return 0;
		}
}	

////
// Get Account Short Name for Account Number
// Must have the Memory Table Populated
function get_account_name($acctnum) {
	$qry_acct = "select nadd_short_name from mry_nfs_nadd where nadd_full_account_number = '".$acctnum."'";
	$result_acct = mysql_query($qry_acct) or die (mysql_error());
	while ( $row_acct = mysql_fetch_array($result_acct) ) 
	{
		$acctname = $row_acct["nadd_short_name"];
	}
	return $acctname;
}

////
// Give a meaningful error output
function tdw_mysql_error($qry) {
return "<b>A fatal Database (MySQL) error occured</b>.\n<br>Query: " . $qry . "<br>\nError: (" . mysql_errno() . ") " . mysql_error();
}

////
// Echo the contents of a simple or nested array (Helpful with POST/GET variables)
function show_array($array) { 
    foreach ($array as $key => $value) { 
        if (is_array($value)) { 
            show_array($value); 
        } else { 
            echo $key . " = [" .$value. "]<br>"; 
        } 
    } 
}

//get shared reps for a given rep
function qry_str_shared_rep ($userid) {

	$query_shared_rep = "SELECT *
												FROM sls_sales_reps
												WHERE srep_user_id = '".$userid."'
												AND srep_isactive = 1
												ORDER BY srep_rrnum";
	//echo $query_shared_rep;
												
	$result_shared_rep = mysql_query($query_shared_rep) or die(mysql_error());
	while($row_shared_rep = mysql_fetch_array($result_shared_rep))
	{
					$str_sql .= "srep_rrnum = ".$row_shared_rep["srep_rrnum"]  .  " or ";
	}
   return $str_sql;

} 

////
// GET COMMISSION MONTH BEGIN AND END TRADE DATES BASED FROM TABLE
// LOGIC PROVIDED BY BRG
// ESSENTIALLY TRADE DATE CORRESPONDING TO LAST SETTLEMENT FRIDAY IS TAKEN
// FOR ALL MONTHS EXCEPT DECEMBER WHERE THE LAST SETTLEMENT DATE IS CONSIDERED
//
// USAGE: get_commission_month_dates("Feb","2006")
function get_commission_month_dates($month, $year) {

		$result_ = mysql_query("SELECT brk_start_date, brk_end_date FROM brk_brokerage_months where brk_month = '".$month."' and brk_year = '".$year."'") or die (mysql_error());
		while ( $row = mysql_fetch_array($result_) ) {
			$begin_tradedate = $row["brk_start_date"];
			$end_tradedate = $row["brk_end_date"];
		}

		$arr_return_dates = array($begin_tradedate,$end_tradedate);
		return $arr_return_dates;

}


////
// CREATES DROPDOWN FOR THE COMMISSION MONTHS
function create_commission_month() {

	//given that TDW starts with NFS Data as of JAN 18th 2006, lets start the date ranges from commission month 2006 Jan,
	//reverse chronological order
	$str_output_options = "";
	
	$getmonth = date('m');
	$getyear = date('Y');
	
	for ($i=0; $i<24; $i++) {
		$lastmonth = mktime(0, 0, 0, date("m")-$i, "01",  date("Y"));
		
			if ( $lastmonth < strtotime('2006-01-01')) {
				//do nothing
			} else {
				$putyear = date('Y', $lastmonth);
				$putmonth = date('M', $lastmonth);
				$str_output_options .=	'<option value="'.$putmonth.'^'.$putyear.'">'.$putmonth.' '.$putyear.'</option>';			
			}
	}
	return $str_output_options;
}


////
// Send email on certain error conditions
function err_email($sub, $msg) {
	$html_body .= zSysMailHeader("");
	$html_body .= $msg;
	$html_body .= zSysMailFooter ();
	$subject = "TDW Error Alert: (".date('m/d/Y h:i a').") : ".$sub;
	$text_body = $subject;
	//zSysMailer($to_email, $to_name, $subject, $html_body, $text_body, $attachment) 
	zSysMailer("pprasad@centersys.com", "", $subject, $html_body, $text_body, "") ;
}

////
//find if rows were returned from the database.
function empty_qry($result) { 
	$num_rows = mysql_num_rows($result); 
	if ($num_rows > 0) {
	return 1;
	} else {
	return 0;
	}
}

////
// Given a date find the brokerage year, qtr and month
// Date input is YYYY-MM-DD
// Return is BM-BQ-BY e.g. 12-4-2006

function get_brok_mqy($date) {

	$arr_date = explode("-",trim($date));
	$arr_brok_month_dates = get_commission_month_dates(flip_month_display($arr_date[1]), $arr_date[0]);
	$start_date = $arr_brok_month_dates[0];
	$end_date = $arr_brok_month_dates[1];
	
	$arr_start_date = explode("-",trim($start_date)); 
	$arr_end_date = explode("-",trim($end_date)); 
	
	// mktime(hour, minute, second, month, day, year)
	
	//check for the condition which will tell us what the brokerage month, qtr, year is.
	if (mktime(0,0,0,$arr_date[1],$arr_date[2],$arr_date[0]) >= mktime(0,0,0,$arr_start_date[1],$arr_start_date[2],$arr_start_date[0]) AND mktime(0,0,0,$arr_date[1],$arr_date[2],$arr_date[0]) <= mktime(0,0,0,$arr_end_date[1],$arr_end_date[2],$arr_end_date[0])) {
		$ret_brok_month = $arr_date[1];
				switch ($arr_date[1]) {
					case "01":
						 $ret_brok_qtr = "1";			 
						 break;
					case "02":
						 $ret_brok_qtr = "1";			 
						 break;
					case "03":
						 $ret_brok_qtr = "1";			 
						 break;
					case "04":
						 $ret_brok_qtr = "2";			 
						 break;
					case "05":
						 $ret_brok_qtr = "2";			 
						 break;
					case "06":
						 $ret_brok_qtr = "2";			 
						 break;
					case "07":
						 $ret_brok_qtr = "3";			 
						 break;
					case "08":
						 $ret_brok_qtr = "3";			 
						 break;
					case "09":
						 $ret_brok_qtr = "3";			 
						 break;
					case "10":
						 $ret_brok_qtr = "4";			 
						 break;
					case "11":
						 $ret_brok_qtr = "4";			 
						 break;
					case "12":
						 $ret_brok_qtr = "4";			 
						 break;
					}	
			 	$ret_brok_year = $arr_date[0];
	} else {
			switch ($arr_date[1]) {
			case "01":
				 $ret_brok_month = "02";
				 $ret_brok_qtr = "1";			 
				 $ret_brok_year = $arr_date[0];
				 break;
			case "02":
				 $ret_brok_month = "03";
				 $ret_brok_qtr = "1";			 
				 $ret_brok_year = $arr_date[0];
				 break;
			case "03":
				 $ret_brok_month = "04";
				 $ret_brok_qtr = "2";			 
				 $ret_brok_year = $arr_date[0];
				 break;
			case "04":
				 $ret_brok_month = "05";
				 $ret_brok_qtr = "2";			 
				 $ret_brok_year = $arr_date[0];
				 break;
			case "05":
				 $ret_brok_month = "06";
				 $ret_brok_qtr = "2";			 
				 $ret_brok_year = $arr_date[0];
				 break;
			case "06":
				 $ret_brok_month = "07";
				 $ret_brok_qtr = "3";			 
				 $ret_brok_year = $arr_date[0];
				 break;
			case "07":
				 $ret_brok_month = "08";
				 $ret_brok_qtr = "3";			 
				 $ret_brok_year = $arr_date[0];
				 break;
			case "08":
				 $ret_brok_month = "09";
				 $ret_brok_qtr = "3";			 
				 $ret_brok_year = $arr_date[0];
				 break;
			case "09":
				 $ret_brok_month = "10";
				 $ret_brok_qtr = "4";			 
				 $ret_brok_year = $arr_date[0];
				 break;
			case "10":
				 $ret_brok_month = "11";
				 $ret_brok_qtr = "4";			 
				 $ret_brok_year = $arr_date[0];
				 break;
			case "11":
				 $ret_brok_month = "12";
				 $ret_brok_qtr = "4";			 
				 $ret_brok_year = $arr_date[0];
				 break;
			case "12":
				 $ret_brok_month = "01";
				 $ret_brok_qtr = "1";			 
				 $ret_brok_year = $arr_date[0]+1;
				 break;
			}	
				//xdebug("ret_brok_month",$ret_brok_month);
				//xdebug("ret_brok_qtr",$ret_brok_qtr);
				//xdebug("ret_brok_year",$ret_brok_year);
	}
	
	return $ret_brok_month."-".$ret_brok_qtr."-".$ret_brok_year;
 	
}

////
// Either the input is Feb or 02, the return will be flipped
// Used to create function arguments
function flip_month_display($month) {

		$arr_monthname     = array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
		$arr_months    		 = array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");

		if (in_array($month, $arr_monthname)) {
			$arr_flip_monthname = array_flip($arr_monthname);
			return $arr_months[$arr_flip_monthname[$month]];

		} elseif (in_array($month, $arr_months)) {
			$arr_flip_months = array_flip($arr_months);
			return $arr_monthname[$arr_flip_months[$month]];
		
		} else {
		  return "Invalid input to function";
		}
}

?>