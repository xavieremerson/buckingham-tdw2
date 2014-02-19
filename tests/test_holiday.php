<?
  include('includes/dbconnect.php');
  include('includes/global.php');
	include('includes/functions.php');


//====================================================================================================
//  NEEDS TO RUN ONLY ON WEEKDAYS AND NON-HOLIDAYS, THIS IS TO CHECK THAT CONDITION
	$str_date = date('Y-m-d');
	echo $str_date."<br>";
	if (
	    check_holiday ($str_date)==1 
	    or date('D',strtotime($str_date))=='Sat' 
			or date('D',strtotime($str_date))=='Sun'
		 ) {
		echo "Today is a holiday or weekend, hence terminating program execution!";
		exit;
	} else {
		echo "Today is not a holiday or weekend, hence proceeding with program execution!";
	}
  echo "<br>Proceeding after holiday/weekend check....";
//====================================================================================================

?>
	