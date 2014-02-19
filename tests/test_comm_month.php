<?

include('../includes/functions.php');
include('../includes/global.php');
include('../includes/dbconnect.php');


show_array(get_commission_month_dates("Dec", "2006"));
show_array(get_commission_month_dates("Nov", "2006"));


$arr_split_input = split("\^", "THIS IS A ^ TEST");

show_array($arr_split_input);


//$arr_split_input = split("^", $sel_month);

?>



<?

	//The Rule 
	//According to the Gregorian calendar, which is the civil calendar in use today, 
	//years evenly divisible by 4 are leap years, with the exception of centurial years 
	//that are not evenly divisible by 400. Therefore, the years 1700, 1800, 1900 and 2100 
	//are not leap years, but 1600, 2000, and 2400 are leap years.
	
?>