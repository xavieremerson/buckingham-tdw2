<title>TEST1</title>
<?

	include('includes/functions.php'); 
	
	echo previous_business_day();

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


echo "<BR>2004-12-23 would be " . format_date_ymd_to_mdy("2004-12-23") . "<BR>";
echo "<BR>null would be " . format_date_ymd_to_mdy("") . "<BR>";

echo "<BR>";
echo "<BR>";
echo "<BR>";
echo "<BR>";
echo "<BR>";
echo "<BR>";
echo "<BR>";
echo "<BR>";
echo "<BR>";
echo "<BR>";
echo "<BR>";
echo "<BR>";
echo "<BR>";
echo "<BR>";
echo "<BR>";
echo "<BR>";
echo "<BR>";
echo "<BR>";
echo "<BR>";
echo "<BR>";

echo strtotime("now"), "<BR>";
echo strtotime("10 September 2000"), "<BR>";
echo strtotime("+1 day"), "<BR>";
echo strtotime("+1 week"), "<BR>";
echo strtotime("+1 week 2 days 4 hours 2 seconds"), "<BR>";
echo strtotime("next Thursday"), "<BR>";
echo strtotime("last Monday"), "<BR>";

?>


