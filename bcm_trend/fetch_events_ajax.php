<?
  include('../includes/dbconnect.php');
  include('../includes/global.php');
  include('../includes/functions.php');

	
if ($mod_request == 'news') { //show approvers online

		$sql = "select * from news_events where auto_id = '".$auto_id."'";
		$result = mysql_query($sql) or die(mysql_error($sql));
		while ($row = mysql_fetch_array($result)) {
			echo $row["news_event"] . " [".format_date_ymd_to_mdy($row["news_date"])."]<br><br>".
					 $row["news_notes"];
		}


}
?>