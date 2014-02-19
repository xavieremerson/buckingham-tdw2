<?
// Populate Accounts with Names made up with dummy data.

include ('includes/dbconnect.php');

$arr_first_names = array("John", "James","Shirley","Sherlock","Amanda","Joseph","Tom","Camille","Dorris","Nancy",
										"Harry","Richard","Kenneth","Matthew","Vincent","Roger","Elizabeth","David","Arun","Herbert",
										"Henry","Colin","Larry","Phillip","Glenn","Bernard","Julius","Manas","Daniel","Jeffrey");
										
$arr_last_names = array("Cox","Rather","Collins","Bredhoff","Pandick","Bosco","Prasad","Mason","Powell","Marx",
										 "Jones","Cheney","McCain","McDonald","McGreevey","Fiorina","Gates","Albanese","Mathers","Blitzer",
										 "King","Stephenson","Hauer","Solinski","Kim","Jong","Rider","Ceaeser","Barbone","Greeley");
										 
//							acct_auto_id = 
for ($i=1; $i<193; $i++) {

$lastname_common = $arr_last_names[rand(0,29)];

$query_str =	"update Employee_accounts set 
								acct_rep = 'RN".rand(15,45) ."', ".  
								"acct_name1 = '". $arr_first_names[rand(0,29)]." ".$lastname_common."', ".  
							  "acct_name2 = '". $arr_first_names[rand(0,29)]." ".$lastname_common."' ".
								"where acct_auto_id = ".$i;
//echo 	$query_str."<br><br>";						
$result = mysql_query($query_str) or die (mysql_error());

}








?>