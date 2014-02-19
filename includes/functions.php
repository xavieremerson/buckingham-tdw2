<?
//////Functions

// Function to clean strings which somehow appear in weird character sets, 
// as happened in a account shortname download on April 14 2008
// Important Note: & is allowed in the string
function tdw_clean_string($str) {
	return ereg_replace("[^&A-Za-z0-9 _]", "", $str);
}

////
// Function to get last day of month
function lastday($month = '', $year = '') {
   if (empty($month)) {
      $month = date('m');
   }
   if (empty($year)) {
      $year = date('Y');
   }
   $result = strtotime("{$year}-{$month}-01");
   $result = strtotime('-1 second', strtotime('+1 month', $result)); 
   return date('Y-m-d', $result);
}



////
// Just like brokerage month dates, this function gives YYYY-MM-DD formatted start
// and end dates for any given month "Feb" and year "2007"
function get_calendar_month_dates ($brk_month,$brk_year) {
  $arr_months = array('Jan'=>1, 'Feb'=>2, 'Mar'=>3, 'Apr'=>4, 'May'=>5, 'Jun'=>6, 'Jul'=>7, 'Aug'=>8, 'Sep'=>9, 'Oct'=>10, 'Nov'=>11, 'Dec'=>12);
  $arr_months_char = array('Jan'=>'01', 'Feb'=>'02', 'Mar'=>'03', 'Apr'=>'04', 'May'=>'05', 'Jun'=>'06', 'Jul'=>'07', 'Aug'=>'08', 'Sep'=>'09', 'Oct'=>'10', 'Nov'=>'11', 'Dec'=>'12');
  return array($brk_year."-".$arr_months_char[$brk_month]."-01", $brk_year."-".$arr_months_char[$brk_month]."-".idate('d', mktime(0, 0, 0, ($arr_months[$brk_month] + 1), 0, $brk_year)));
}

////
// Function to get all functions in this file with definitions
function get_functions() {
	$fp = fopen ('d:\\tdw\\tdw\\includes\\functions.php', "r"); 
	while (!feof ($fp)) { 
		$content = fgets( $fp, 4096 ); 
		if (substr($content,0, 8) == 'function') {
		echo $content."<br>";
		echo $contentold."<br><hr>";
		}
		$contentold = fgets( $fp, 4096 ); 
	}
}

////
// Used to calculate page load time
function getmicrotime(){ 
    list($usec, $sec) = explode(" ",microtime()); 
    return ((float)$usec + (float)$sec*1000); 
} 

//// Function to write data to file
function write_to_file($location, $file, $data) {
	$filename = $location.$file;
	$fp = fopen ($filename, "w");  
	fwrite ($fp,$data);        
	fclose ($fp);   
}

//// Function to write debug information to file for review/troubleshooting
function debug_log($data) {
	$filename = $location.$file;
	$fp = fopen ("d:\\tdw\\tdw\\debug_log.wri", "a");  
	fwrite ($fp, date('Y-m-d h:i:s')."\n");
	fwrite ($fp,$data."\n");        
	fclose ($fp);   
}

////
// Show variable values and debugging information $show_debug = 1; shows debug information
function xdebug ($varname, $varval) {
$show_debug = 1;

if ($show_debug == 1 ) {
	echo '<strong><font face="Verdana" color="#66666" size="1">'.$varname.' : </font><font face="Verdana" color="#000000" size="1">'.$varval.'</font></strong><br>';
	}

/*	if ($show_debug == 1 ) {
	echo '<font color="#0000FF"><strong>'.$varname."</strong></font> = [".$varval."]<br>";
	}
*/
}

////
// Show variable values and debugging information
// $show_debug = 1; shows debug information
function ydebug ($varname, $varval) {
$show_debug = 1;
	if ($show_debug == 1 ) {
	echo $varname." = [".$varval."]\n";
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
  $arr_passwords = array();
  $arr_passwords[0] = "deer";
  $arr_passwords[1] = "falcon";
  $arr_passwords[2] = "eagle";
  $arr_passwords[3] = "cobra";
  $arr_passwords[4] = "zebra";
  $arr_passwords[5] = "mantis";
  $arr_passwords[6] = "frog";
  $arr_passwords[7] = "lion";
  $arr_passwords[8] = "tiger";
  $arr_passwords[9] = "skunk";

	//$gen_password = substr(md5(rand(8888888888,9999999999)), 0, 8);
	$gen_password = $arr_passwords[rand(0,9)];
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
	$bs = trim($buysellval);
	if       ($bs == 'B') {
		return "B";
	} elseif ($bs == 'S') {
		return "&nbsp;&nbsp;&nbsp;&nbsp;S";
	} elseif ($bs == 'SS') {
		return "&nbsp;&nbsp;&nbsp;&nbsp;SS";
	} elseif ($bs == 'C') {
		return "C";
	} elseif ($bs == 'Buy') {
		return "B";
	} elseif ($bs == 'Sell') {
		return "&nbsp;&nbsp;&nbsp;&nbsp;S";
	} elseif ($bs == 'Short') {
		return "&nbsp;&nbsp;&nbsp;&nbsp;SS";
	} elseif ($bs == 'Cover') {
		return "C";
	} else {
		return "??";
	}
}

////
// Offset B and S in character based reports for ease of viewing.
function offset_buy_sell_space($buysellval) {
	if       ($buysellval == 'B') {
		return "B";
	} elseif ($buysellval == 'S') {
		return "  S";
	} elseif ($buysellval == 'SS') {
		return "  SS";
	} elseif ($buysellval == 'C') {
		return "C";
	} elseif ($buysellval == 'Buy') {
		return "B";
	} elseif ($buysellval == 'Sell') {
		return "  S";
	} elseif ($buysellval == 'Short') {
		return "  SS";
	} elseif ($buysellval == 'Cover') {
		return "C";
	} else {
		return "??";
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
// IF argument is null then current date (yyyy-mm-dd) is taken as input
//

function previous_business_day($dateval=NULL) {

	if ($dateval==NULL) {
		$working_dateval = date('Y-m-d');
	} else {
		$working_dateval = $dateval;
	}
	
	$i = 1;
	while ($i < 7) {
		 if (date("w",strtotime($working_dateval)-(60*60*24*$i)) > 0 AND
				 date("w",strtotime($working_dateval)-(60*60*24*$i)) < 6 AND
				 check_holiday(date("Y-m-d", strtotime($working_dateval)-(60*60*24*$i))) == 0 ) {
				$val_pbd = date("Y-m-d",strtotime($working_dateval)-(60*60*24*$i));
			 return $val_pbd;
		 } else {
				$i = $i + 1;
		 }
	}
}

/*
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
*/

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
	
	$new_subString = $subString;
	
	//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ 2009-01-31
	//Making sure that we are working with a business day, to 
	//get the next business day.
	
  //echo date("Y-m-d", $new_subString)."<br>";
	//echo date('D',$new_subString)."<<<<<<<<<<<<<<< <br>";
	for ($k=0;$k<4;$k++) {
		
		//echo date('D',$new_subString)."<br>";

		if (check_holiday(date("Y-m-d", $new_subString)) == 1
		    or date('D',$new_subString)=='Sat' 
			  or date('D',$new_subString)=='Sun'
		) {
		  //echo "holiday detected!";
			$new_subString = $new_subString + (60*60*24*1);
		}
	}
	//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	
	while($i < 40)
	{
		$new_subString = $new_subString+(60*60*24*1);
		if(date("w", $new_subString) != 0 AND date("w", $new_subString) != 6 AND !(check_holiday(date("Y-m-d", $new_subString))))
		{
			$j++;
			$checkDay =date("Y-m-d", $new_subString);
		}
		if($j == $days)
		break;
	}
	return $checkDay;
}

//RETURNS A DAY(YYYY-MM-DD) AFTER $days DAYS.  THE ARGUMENT GIVES INPUT DATE AND THE NUMBER OR DAYS TO GO FORWARD
//THIS IS MAINLY TO CALCULATE CHECKS PAYMENT DATA
//DATE INPUT IS IN THE FORMAT OF strtotime() FUNCTION AND DAYS INPUT IS INT
//This function will also be used to do a bulk process starting on a given day and going forward n days.

function day_forward($str_date, $days)
{
	$i = 0;
	$j = 0;
	$checkDay = '';
	$subString = $str_date;
	
	while($i < 40)
	{
		$subString = $subString+(60*60*24*1);
			$j++;
			$checkDay =date("Y-m-d", $subString);
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

////
// Create message with image depending on severity
// 1 = green, 2 = orange, 3 = red
function sys_message($severity, $msgtext) {

	echo '<table class="msgtbl_'.$severity.'">
				<tr><td nowrap="nowrap"><font size="3"><strong>&raquo;</strong></font> '.$msgtext.'</td></tr>
				</table>';
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
////
// also used in ETPA
function get_company_detail($symbol) {
  $quotes = new Quotes(); 
	$symbols = explode(",",$symbol) ; 
	for ($n=0; $n<count($symbols); $n++)
    {
        $quotes->mSetSymbol(strtoupper($symbols[$n])) ; 
        $quotes->mLoadYahoo() ;
				return $quotes->_strCompany."^".$quotes->_strLastPrice."^".$quotes->_strVolume."^".$quotes->_strMarketCap;
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

/**
 * Class to fetch stock data from Yahoo! Finance
 *
 */
class YahooStock {
    /**
     * Array of stock code
     */
    private $stocks = array();
    /**
     * Parameters string to be fetched   
     */
    private $format;
    /**
     * Populate stock array with stock code
     *
     * @param string $stock Stock code of company    
     * @return void
     */
    public function addStock($stock)
    {
        $this->stocks[] = $stock;
    }
    /**
     * Populate parameters/format to be fetched
     *
     * @param string $param Parameters/Format to be fetched
     * @return void
     */
    public function addFormat($format)
    {
        $this->format = $format;
    }
    /**
     * Get Stock Data
     *
     * @return array
     */
    public function getQuotes()
    {        
        $result = array();      
        $format = $this->format;
        foreach ($this->stocks as $stock)
        {            
            /**
             * fetch data from Yahoo!
             * s = stock code
             * f = format
             * e = filetype
             */
            $s = file_get_contents("http://finance.yahoo.com/d/quotes.csv?s=$stock&f=$format&e=.csv");
            /** 
             * convert the comma separated data into array
             */
            $data = explode( ',', $s);
            /** 
             * populate result array with stock code as key
             */
            $result[$stock] = $data;
        }
        return $result;
    }
} 

function get_company_name_yhoo($symbol) {
//**************************************************************************
	$objYahooStock = new YahooStock; 
	/** 
			Add format/parameters to be fetched 
			s = Symbol, n = Name, l1 = Last Trade (Price Only), d1 = Last Trade Date, t1 = Last Trade Time, c = Change and Percent Change, v = Volume 
	 */ 
	$objYahooStock->addFormat("n"); //snl1d1t1cv 
	$objYahooStock->addStock($symbol); 
	/** 
	 * Printing out the data 
	 */ 
	foreach( $objYahooStock->getQuotes() as $code => $stock) 
	{ 
			return strtoupper(trim(str_replace('"','',$stock[0])));
	} 
//**************************************************************************
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
/*

echo '<table width="'.$width.'%" border="0" cellpadding="4" cellspacing="0"><tr><td valign="top">
			<table width="'.$width.'%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#F7F7F7" class="tblcont">
	<tr>
		<td>
			<table border="0" bgcolor="#F7F7F7" cellspacing="0" cellpadding="0" width="100%">
				<tr valign=top>
					<td width="50%" colspan="2" valign="top" align="left">
						<table border="0" width="100%" CELLPADDING="0" CELLSPACING="0">
							<tr>
								<td width="5%" class="tblconthead">&nbsp;</td>
								<td nowrap class="tblconthead">&#9658; '.$title.' </td>
								<td width="5%" class="tblconthead">&nbsp;</td>
								<td nowrap valign=top ><img src="images/tables4/r_angle.png"></td>
								<td width="100%">&nbsp;</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td valign="top"><table border="0" cellpadding="4" cellspacing="0"><tr><td valign="top">';
*/

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
										<td valign="middle"><table border="0" cellpadding="0" cellspacing="0">';
}

function table_end_percent() {
/*

echo '			</td></tr></table>
					</td>
				</tr>
			</table>
			</td></tr></table>';
*/
echo 			'				</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>';
}

function table_start_percent2($width, $title) {
  echo '    <table width="'.$width.'%" border="0" cellpadding="0" cellspacing="1" class="test" bgcolor="#F4F8FB">
      <thead class="datadisplay">
		<tr>
		  <td colspan="3" height="20" background="images/tables3/header_bk.jpg">&nbsp;&nbsp;<a class="table_heading_text">'.$title.'</a></td>
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

//log this email to db
$insert_log_email = mysql_query("INSERT INTO log_emails
																			(auto_id,
																			log_email_id,
																			log_email_subject,
																			log_message,
																			log_datetime,
																			log_isactive) 
																		VALUES (
																			NULL , 
																			'".$to_email."',
																			'".$subject."', 
																			'".$html_body."', 
																			now(), '1'
																			)");		

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

//Embed all images that need to go in the emails here
//usage is AddEmbeddedImage(PATH, CID, NAME);
$mail->AddEmbeddedImage('d:\\\\tdw\\tdw\\images\\themes\\standard\\email\\logo.gif', 'tdwlogo', 'logo.gif');
$mail->AddEmbeddedImage('d:\\\\tdw\\tdw\\images\\themes\\standard\\email\\client_app.jpg', 'client_app', 'client_app.jpg');


    // HTML body
    $mail->Body    = $html_body;
    // Plain text body (for mail clients that cannot read HTML)
    $mail->AltBody = $text_body;
		
    $mail->AddAddress($to_email, $to_name);
    
		if (is_array($attachment)) {
			foreach ($attachment as $attachmentname => $attachmentfullpath) {
			//echo $attachmentname."<br>";
			//echo $attachmentfullpath."<br>";
				$mail->AddAttachment($attachmentfullpath, $attachmentname);
			}	
		}
		
    if(!$mail->Send())
        echo "There has been a mail error sending to ".$to_name." (".$to_email.")";

    // Clear all addresses and attachments for next loop
    $mail->ClearAddresses();
    $mail->ClearAttachments();
		
		//echo "<br>Mail sent to ".$to_name. " (".$to_email. ")";
}	

//// System Emailer with single or multiple file attachments
// $attachmentname, $attachmentfullpath is in an associative array called $attachment
function zTextMailer($to_email, $to_name, $subject, $html_body, $text_body, $attachment) {

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
			//echo $attachmentname."<br>";
			//echo $attachmentfullpath."<br>";
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
<style type="text/css">
<!--
.bodytext12 {	font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 12px;	color: #003399;}
.bodytext9 {	font-family: Verdana, Arial, Helvetica, sans-serif;	font-size: 9px;	color: #003399;}
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
.datatable {
	border-top-color: #000000;
	border-right-color: #0000FF;
	border-bottom-color: #000000;
	border-left-color: #0000FF;
	border-style: solid;
	border-width: 1px 2px;
	border-collapse: collapse;
}
.notetext {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #333333;
	font-style: normal;
}
-->
</style>
</head>
<body leftmargin="3" topmargin="3" rightmargin="3" bottommargin="3">
<table class="datatable" width="100%" height="100%" border="0" cellpadding="1" cellspacing="1" bgcolor="#FFFFFF">
  <tr>
    <td valign="top" height="40">
			<table width="100%" height="40" border="0" cellpadding="0" cellspacing="0" >
        <tr>
          <td width="50"><img src="cid:tdwlogo" border="0"></td>
          <td align="left" valign="top" class="bodytext12bb">&nbsp;</td>
          <td width="200" valign="top"><img src="cid:client_app" border="0"></td>
        </tr>
      </table>
      <hr size="4" noshade color="#91B5E7">
		</td>
	</tr>
	<tr>
		<td valign="top">
			<table>
				<tr>
					<td valign="top" class="notetext">
					<!--email body-->';
}

function zSysMailFooter () {

	return '<!--end email body-->
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table height="100%" width="100%">
        <tr>
          <td valign="bottom" height="30">
						<table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td><a class="bodytext12">Please do not reply to this email address. It is not equipped to handle user enquiries.</a></td>
              </tr>
              <tr>
                <td align="right"><a class="bodytext9">'.date("D, m/d/Y h:i a").' CTRL# : '.gen_control_number().'</a></td>
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
$str = "<b>TDW encountered a serious data error</b>.\n<br>Query: " . $qry . "<br>\nError: (" . mysql_errno() . ") " . mysql_error();
showmsg(3, $str);
//return "<b>A fatal Database (MySQL) error occured</b>.\n<br>Query: " . $qry . "<br>\nError: (" . mysql_errno() . ") " . mysql_error();
}

function tdw_mysql_error_email($qry) {
				$email_log  = "<b>TDW encountered a serious data error</b>.\n<br>Query: " . $qry . "<br>\nError: (" . mysql_errno() . ") " . mysql_error();

				//create mail to send
				$html_body = "";
				$html_body .= zSysMailHeader("");
				$html_body .= $email_log;
				$html_body .= zSysMailFooter();
				
				$subject = "TDW encountered a serious data error [".date('m/d/Y h:i:sa')."]";
				$text_body = $subject;
				
				zSysMailer('pprasad@centersys.com', "Pravin Prasad", $subject, $html_body, $text_body, "") ;

}
////
// Echo the contents of a simple or nested array (Helpful with POST/GET variables)
function show_array($array) { 
    if (is_array($array)){
			foreach ($array as $key => $value) { 
					if (is_array($value)) { 
							echo "[" .$key. "]<br>";
							show_array($value); 
					} else { 
							echo "&nbsp;&nbsp;".$key . " = [" .$value. "]<br>"; 
					} 
			} 
		} else {
							echo "EMPTY ARRAY!<br>"; 
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
	
	$today_bmqy = get_brok_mqy(date('Y-m-d'));
	$arr_today_bmqy = explode('-',$today_bmqy);

	$str_output_options = "";
	
	for ($i=0; $i<24; $i++) {
		
		$lastmonth = mktime(0, 0, 0, $arr_today_bmqy[0]-$i, "01", $arr_today_bmqy[2]);
		
			if ( $lastmonth < strtotime('2006-01-01')) {
				//do nothing
			} else {
				$putyear = date('Y', $lastmonth);
				$putmonth = date('M', $lastmonth);
				$str_output_options .=	'<option value="'.$putmonth.'^'.$putyear.'">'.$putmonth.' '.$putyear.'</option>'."\n";			
			}
	}
	return $str_output_options;
}

/*

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
*/

////
// Send email on certain error conditions
function err_email($sub, $msg) {
	$html_body .= zSysMailHeader("");
	$html_body .= $msg;
	$html_body .= zSysMailFooter ();
	$subject = "TDW (".date('m/d/Y h:i a').") : ".$sub;
	$text_body = $subject;
	//zSysMailer($to_email, $to_name, $subject, $html_body, $text_body, $attachment) 
	zSysMailer("pprasad@centersys.com", "", $subject, $html_body, $text_body, "") ;
}

////
// Send email on certain error conditions
function notify_email($email, $sub, $msg) {
	$html_body .= zSysMailHeader("");
	$html_body .= $msg;
	$html_body .= zSysMailFooter ();
	$subject = $sub . " [".date('m/d/Y h:ia')."]";  
	$text_body = $subject;
	//zSysMailer($to_email, $to_name, $subject, $html_body, $text_body, $attachment) 
	zSysMailer($email, "", $subject, $html_body, $text_body, "") ;
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

	$arr_date = explode("-",$date);
	$arr_brok_month_dates = get_commission_month_dates(flip_month_display($arr_date[1]), $arr_date[0]);
	$start_date = $arr_brok_month_dates[0];
	$end_date = $arr_brok_month_dates[1];
	
	$arr_start_date = explode("-",trim($start_date)); 
	$arr_end_date = explode("-",trim($end_date)); 
	
	// mktime(hour, minute, second, month, day, year)
	
	//check for the condition which will tell us what the brokerage month, qtr, year is.
	if (
					mktime(0,0,0,$arr_date[1],$arr_date[2],$arr_date[0]) >= mktime(0,0,0,$arr_start_date[1],$arr_start_date[2],$arr_start_date[0]) 
			AND mktime(0,0,0,$arr_date[1],$arr_date[2],$arr_date[0]) <= mktime(0,0,0,$arr_end_date[1],$arr_end_date[2],$arr_end_date[0])
		 ) {
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
// same brokerage year? take YYYY-MM-DD as input
function samebrokyear($old, $new) {
		$bmqy_date_old=explode("-",get_brok_mqy($old));
		$bmqy_date_new=explode("-",get_brok_mqy($new));
    if ($bmqy_date_old[2] == $bmqy_date_new[2]) {
		return 1;
		} else {
		return 0;
		}
}	

////
// same brokerage month? take YYYY-MM-DD as input
function samebrokmonth($old, $new) {
		$bmqy_date_old=explode("-",get_brok_mqy($old));
		$bmqy_date_new=explode("-",get_brok_mqy($new));
    if ($bmqy_date_old[2] == $bmqy_date_new[2] AND $bmqy_date_old[0] == $bmqy_date_new[0]) {
		return 1;
		} else {
		return 0;
		}
}	

////
// same brokerage quarter? take YYYY-MM-DD as input
function samebrokqtr($old, $new) {
		$bmqy_date_old=explode("-",get_brok_mqy($old));
		$bmqy_date_new=explode("-",get_brok_mqy($new));
    if ($bmqy_date_old[2] == $bmqy_date_new[2] AND $bmqy_date_old[1] == $bmqy_date_new[1]) {
		return 1;
		} else {
		return 0;
		}
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



////
// function returns an array of check amounts for a given advisor
function get_checks_data ($adv_code, $arr_adv_checks, $i) {
	 $arr_data_val = explode("#",$arr_adv_checks[$adv_code]);
	 return $arr_data_val[$i];
}

function show_numbers($numval) {
		if ($numval == 0) {
			return '<a class="display_zero">'."0"."</a>";
		} else {
			return number_format($numval,0,'.',",");
		}
}	

// Gets the coverage universe from TDW based on data from Jovus		
// returns array
function get_coverage_universe() {
    $qry_cu = "select str_tickers from cvr_coverage_universe";
		$result_cu = mysql_query($qry_cu) or die(tdw_mysql_error($qry_cu));
			while($row_cu = mysql_fetch_array($result_cu))
			{
				$str_tickers = $row_cu["str_tickers"];
			}
		$arr_cu = explode(",",$str_tickers);
		return $arr_cu;
}

//// returns privilege for modules in TDW
function checkpriv ($pstr, $mod) {

$mods = array();
$mods["dcar"] = 0;
$mods["dcarv2"] = 1;
$mods["uedit"] = 2;
$mods["apadj"] = 3; // Analyst Payout Adjustment
$mods["a2"] = 4;
$mods["a3"] = 5;
$mods["a4"] = 6;
$mods["a5"] = 7;
$mods["a6"] = 8;
$mods["a7"] = 9;
$mods["a8"] = 10;
$mods["a9"] = 11;
$mods["a10"] = 12;
$mods["b1"] = 13;
$mods["b2"] = 14;
$mods["b3"] = 15;
$mods["b4"] = 16;
$mods["b5"] = 17;
$mods["b6"] = 18;
$mods["b7"] = 19;
$mods["b8"] = 20;
$mods["b9"] = 21;
$mods["c1"] = 22;
$mods["c2"] = 23;
$mods["c3"] = 24;
$mods["c4"] = 25;
$mods["c5"] = 26;
$mods["c6"] = 27;
$mods["c7"] = 28;
$mods["c8"] = 29;
$mods["c9"] = 30;

return substr($pstr, $mods[$mod], 1);

}

////
//

function get_user_by_id ($id) {
	$qry = "select Fullname from users where ID = '".$id."'";
	$result = mysql_query($qry) or die(tdw_mysql_error($qry));
	while($row = mysql_fetch_array($result)) {
	$user_fullname = $row["Fullname"];
	}
 
 	return $user_fullname;
}

function tsp($width, $title) {
//align="center" 
echo '<table width="'.$width.'%" cellpadding="0" cellspacing="0" bgcolor="#F7F7F7" class="tblcont">
				<tr>
					<td>
						<table border="0" CELLPADDING="0" CELLSPACING="0">
							<tr>
								<td align="left" nowrap class="tblconthead">&nbsp;&nbsp;&#9658; '.$title.' &nbsp;&nbsp;</td>
								<td nowrap valign=top ><img src="images/tables4/r_angle.png"></td>
								<td width="100%">&nbsp;</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
				<td valign="top">
					<table width="100%" border="0" cellpadding="4" cellspacing="0">
						<tr>
							<td valign="top">';
}

function tep() {
echo '			  </td>
						</tr>
					</table>
				</td>
			</tr>
		</table>';
}

function tsp100($width, $title) {
//align="center" 
echo '<table height="100%" width="'.$width.'%" cellpadding="0" cellspacing="0" bgcolor="#F7F7F7" class="tblcont">
				<tr>
					<td>
						<table border="0" CELLPADDING="0" CELLSPACING="0">
							<tr>
								<td align="left" nowrap class="tblconthead">&nbsp;&nbsp;&#9658; '.$title.' &nbsp;&nbsp;</td>
								<td nowrap valign=top ><img src="images/tables4/r_angle.png"></td>
								<td width="100%">&nbsp;</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
				<td valign="top">
					<table width="100%" border="0" cellpadding="4" cellspacing="0">
						<tr>
							<td valign="top">';
}

function tep100() {
echo '			  </td>
						</tr>
					</table>
				</td>
			</tr>
		</table>';
}

function tsp_b_px($width, $title) {
//align="center" 
echo '<table width="'.$width.'" cellpadding="0" cellspacing="0" bgcolor="#F7F7F7" style="BORDER-RIGHT: #000000 1px solid; 
        BORDER-TOP: #000000 1px solid; BORDER-LEFT: #000000 1px solid; BORDER-BOTTOM: #000000 1px solid;">
				<tr>
					<td>
						<table border="0" CELLPADDING="0" CELLSPACING="0">
							<tr>
								<td align="left" valign="top" nowrap style="FONT-WEIGHT: bold;	BORDER-TOP-STYLE: none;	
								PADDING-TOP: 0px; PADDING-BOTTOM: 2px; FONT-FAMILY: sans-serif;	
  							BORDER-RIGHT-STYLE: none;	BORDER-LEFT-STYLE: none;	background-image: url(images/tables5/base.png);	
								background-repeat: repeat-x;	color: #FFFFFF;	font-size: 10px;	letter-spacing: 2px;">&nbsp;&nbsp; '.$title.' &nbsp;&nbsp;</td>
								<td nowrap valign=top ><img src="images/tables5/r_angle.png"></td>
								<td nowrap width="100%">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
				<td valign="top">
					<table width="100%" border="0" cellpadding="4" cellspacing="0">
						<tr>
							<td valign="top">';
}

function tep_b_px() {
echo '			  </td>
						</tr>
					</table>
				</td>
			</tr>
		</table>';
}

////
// Return a single value from a database query.
// must call the single value single_val
function db_single_val($qry) {
	$result = mysql_query($qry) or die (tdw_mysql_error($qry));
	$count = mysql_num_rows($result);
	//xdebug("Count of single val", $count);
	if ($count > 0) {
		while ( $row = mysql_fetch_array($result) ) 
		{
			$returnval = $row["single_val"];
		}
		return $returnval;
	} else {
		return '';
	}
}

////
// Function to output formatted message in relevant colors
function showmsg ($severity, $msgtext) {
	echo '<table class="msgtbl_'.$severity.'" cellspacing="2" cellpadding = "0">
								<tr>
									<td valign="top">
						       <img src="images/info_'.$severity.'.png" border="0"> 
									</td>
									<td valign="top"> '.$msgtext.'
									</td>
								</tr>
							</table>';
}

////
// function to show help as popup
function showhelp($id) {
	echo '&nbsp;<a href="javascript:CreateWnd(\'help.php?item='.$id.'\', 404, 216, false)"><img src="images/help.png" border="0"></a>';
}

/* Functions for working with DB */
/**
 * return sql result as array with assotiatic sub arrays
 * @param string $sqlQueryString - MySQL query string 
 * @return array
 */
function fetchSqlArray($sqlQueryString)
{
    //get sql result
    $sqlResult = mysql_query($sqlQueryString) or die(tdw_mysql_error($sqlQueryString));
    //Proccessing result
    $result = array();
    while ($row = mysql_fetch_assoc($sqlResult)) {
        $result[] = $row;
    }
    return $result;
}

/**
 * return sql result as assoc array with assoc sub arrays
 * @param string $sqlQueryString - MySQL query string 
 * @param string $key - table column name for index in array
 * @return array
 */
function fetchSqlAssocArray($sqlQueryString, $key = 'id')
{
    //get sql result
    $sqlResult = mysql_query($sqlQueryString) or die(tdw_mysql_error($sqlQueryString));
    //Proccessing result
    $result = array();
    while ($row = mysql_fetch_assoc($sqlResult)) {
        if(isset($row[$key])){
            $result[$row[$key]] = $row;
        }
    }
    return $result;
}

/**
 * return sql result as assoc pairs array
 * @param string $sqlQueryString - MySQL query string 
 * @param string $keyColumnName - table column name for index in array
 * @param string $valueColumnName - table column name for value in array
 * @return array
 */
function fetchSqlPairsArray($sqlQueryString, $keyColumnName, $valueColumnName)
{
    //get sql result
    $sqlResult = mysql_query($sqlQueryString) or die(tdw_mysql_error($sqlQueryString));
    //Proccessing result
    $result = array();
    while ($row = mysql_fetch_assoc($sqlResult)) {
        if(isset($row[$keyColumnName])){
            $result[$row[$keyColumnName]] = (isset($row[$valueColumnName])) ? $row[$valueColumnName] : null;
        }
    }
    return $result;
}

/**
 * return sql column result as array
 * @param string $sqlQueryString - MySQL query string 
 * @param string $keyColumnName - table column name for values in array
 * @return array
 */
function fetchSqlColumn($sqlQueryString, $keyColumnName = null)
{
    //get sql result
    $sqlResult = mysql_query($sqlQueryString) or die(tdw_mysql_error($sqlQueryString));
    //Proccessing result
    $result = array();
    while ($row = mysql_fetch_assoc($sqlResult)) {
        if(isset($row[$keyColumnName])){
            $result[] = $row[$keyColumnName];
        } else {
            $result[] = $row[0];
        }
    }
    return $result;
}

/**
 * return sql scalar value
 * @param string $sqlQueryString - MySQL query string 
 * @param string $keyColumnName - table column name for values
 * @return array
 */
function fetchSqlScalar($sqlQueryString, $keyColumnName = 'scalar')
{
    //get sql result
    $sqlResult = mysql_query($sqlQueryString) or die(tdw_mysql_error($sqlQueryString));
    //Proccessing result
    $row = mysql_fetch_assoc($sqlResult);
    if (isset($row[$keyColumnName])) {
        return $row[$keyColumnName];
    }
    return false;
}
?>