<?
	include('includes/functions.php');
  include('includes/dbconnect.php');
  include('includes/global.php'); 

echo "Previous business day = ".previous_business_day()."<br><br>";

$previous_business_day = previous_business_day();

////
// Set the date added for each stock in each list to 2 days prior to today

$result = mysql_query("UPDATE lres_restricted_list set list_date_added = '".$previous_business_day."' where list_isactive = 1") or die (mysql_error());
echo "Restricted List updated successfully!<br>";

?>