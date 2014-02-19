<?

//////Functions

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
			return "&nbsp;&nbsp;B";
		} else {
			return "S&nbsp;&nbsp;";
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
	$check = mysql_query("SELECT holi_date from Holidays where holi_date = '$checkdate'") or die (mysql_error());
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
// Sending System Mails

function sys_mail($email, $mailsubject, $mailbodysubinfo, $emailheading )
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
			
			echo "<BR>writing file<BR>";
			
			$trade_date_to_process_a = previous_business_day();
			
			$fp = fopen($exportlocation."Trades_Report_".$trade_date_to_process_a.".html", "w");

			fputs ($fp, $mailbody);

			fclose($fp);
			echo "<BR>end writing file<BR>";				
					
					/* To send HTML mail, you can set the Content-type header. */
					$headers  = "MIME-Version: 1.0\n";
					$headers .= "Content-type: text/html; charset=iso-8859-1\n";
					$headers .= "From: Compliance <compliance@tocqueville.com>\n";
		/*			
					$fromval = "compliance@tocqueville.com";
					
					$rp    = 'compliance@tocqueville.com';
          $org    = 'tocqueville.com';
          $mailer = 'CenterSys Compliance';
	

  $headers  .= "Return-Path: $rp \r\n";
  $headers  .= "From: $fromval \r\n";
  $headers  .= "Sender: $fromval \r\n";
  $headers  .= "Reply-To: $fromval \r\n";
  $headers  .= "Organization: $org \r\n";
  $headers  .= "X-Sender: $fromval \r\n";
  $headers  .= "X-Priority: 3 \r\n";
  $headers  .= "X-Mailer: $mailer \r\n"; */


										
					//mail($email, $mailsubject, $mailbody, "From: $fullname <$useremail>");
			mail($email,$mailsubject,$mailbody,$headers,"-fcompliance@tocqueville.com");

}

////
// Gets the previous business day given a certain day
// IF argument is null then current date (Unix Timestamp) is taken as input
// CAUTION: This function takes Unix Timestamp as argument
//          Use strtotime() wherever required to create argument.
//
// Confirmed with RBC Dain that trade files are available early in the day
// so first attempt is going to be at 7:00 AM and upon failures try 5 times at
// 30 minutes intervalsupon which System Admin gets emails.

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
						$previoustime = time() - (60*60*24*2);
						$previousday = date("Y-m-d", $previoustime);
						if ( check_holiday($previousday) == 1 ) {
						 	$previoustime = time() - (60*60*24*3);	
					    $previousday = date("Y-m-d", $previoustime);
						}
						return $previousday;
				} else { //essentially Sunday
						$previoustime = time() - (60*60*24*($x+3));
						$previousday = date("Y-m-d", $previoustime);
						if ( check_holiday($previousday) == 1 ) {
						 	$previoustime = time() - (60*60*24*4);	
					    $previousday = date("Y-m-d", $previoustime);
						}
						return $previousday;
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
// 0 = normal, 1 = green, 2 = orange, 3 = red
   function sys_message($severity, $msgtext) {
	 if ($severity == 0){
	 $imagefile = 'sysmessage.gif';
	 $varcssstyle = 'sysMessage';
	 } 
	 elseif ($severity == 1){
	 $imagefile = 'sysmessagegreen.gif';
 	 $varcssstyle = 'sysMessageGreen';
	 }
	 elseif ($severity == 2){
	 $imagefile = 'sysmessageorange.gif';
 	 $varcssstyle = 'sysMessageOrange';
	 }
	 elseif ($severity == 3){
	 $imagefile = 'sysmessagered.gif';
 	 $varcssstyle = 'sysMessageRed';
	 }
	 else {
	 $imagefile = 'sysmessage.gif';
 	 $varcssstyle = 'sysMessage';
	 }
	 
	 echo '<tr class="' . $varcssstyle . '"><td>';
	 echo '<table border="0" width="100%" cellspacing="1" cellpadding="0">';
   echo '		<tr>';
	 echo	'		<td width="20"><img src="images/system/' . $imagefile . '"></td><td><a class="' . $varcssstyle . '">' . $msgtext . '</a></td>';
	 echo	' 	</tr>';
   echo '</table>';
	 echo '</td></tr>';
								 
	 }
	
	?>