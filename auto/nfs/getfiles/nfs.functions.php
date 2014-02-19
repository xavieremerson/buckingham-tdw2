<?
////
// Function to write debug data/info to console.
// Change the $show_debug val to 0 to suppress output to console

function xdebug ($varname, $varval) {
	$show_debug = 1;
		if ($show_debug == 1 ) {
			//echo "\n>> ".$varname." = [".$varval."]\n\n";
			if ($varval == '') {
			echo ">> ".$varname."\n\n";
			} else {
			echo ">> ".$varname." = [".$varval."]\n\n";
			}
		}
	}

////
// {{{ lpad($input, $padLength, $padString) 

function lpad($input, $padLength, $padString) { 
		return str_pad($input, $padLength, $padString, STR_PAD_LEFT); 
	} 

////
// {{{ rpad($input, $padlength, $padString) 

function rpad($input, $padlength, $padString) { 
		return str_pad($input, $padLength, $padString, STR_PAD_RIGHT); 
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

?>