

<?
include('includes/dbconnect.php');
include('includes/functions.php');
include('includes/global.php');

$master_emls = array();
$qry_emls = "SELECT eml_trade_date,eml_is_ok
							FROM eml_research_compliance
							WHERE eml_isactive =1";
$result_emls = 	mysql_query($qry_emls) or die (tdw_mysql_error($qry_emls));
while ($row_emls = mysql_fetch_array($result_emls) ) 
{
		$master_emls[$row_emls['eml_trade_date']] = $row_emls['eml_is_ok'];	
}							
//show_array($master_emls);


$master_count_emls = array();
$qry_count_emls = "SELECT eml_trade_date,eml_count 
										FROM eml_research_counts 
										WHERE eml_type = 'Total'
										AND eml_isactive =1";
$result_count_emls = 	mysql_query($qry_count_emls) or die (tdw_mysql_error($qry_count_emls));
while ($row_count_emls = mysql_fetch_array($result_count_emls) ) 
{
		$master_count_emls[$row_count_emls['eml_trade_date']] = $row_count_emls['eml_count'];	
}							
//show_array($master_count_emls);

?>

<font size="+1" color="#00CC00">&#9658;</font><br />
<font size="+1" color="#FF0000">&#9658;</font><br />