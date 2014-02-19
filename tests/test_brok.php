<?
include('../includes/functions.php');
include('../includes/dbconnect.php');
include('../includes/global.php');


echo '2009-11-20'."<br>".get_brok_mqy('2009-11-20')."<br>";
echo '2009-11-23'."<br>".get_brok_mqy('2009-11-23')."<br>";
echo '2009-11-24'."<br>".get_brok_mqy('2009-11-24')."<br>";


//echo "=>".flip_month_display("04");
//echo "=>".flip_month_display("Apr");

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


//testing

echo "sbm for dates 2006-02-21 and 2006-03-28 ".samebrokmonth('2006-02-21', '2006-03-28')."<br>";
echo "sbq for dates 2006-02-21 and 2006-03-28 ".samebrokqtr('2006-02-21', '2006-03-28')."<br>";
echo "sby for dates 2006-02-21 and 2006-03-28 ".samebrokyear('2006-02-21', '2006-03-28')."<br>";

?>