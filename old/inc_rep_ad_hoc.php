<?
//include('includes/dbconnect.php');

$acctSelected = array();
$tickSelected = array();
$listSelected = array();

//$id = '1';
//$user_id = '44';

$query_data = "SELECT * FROM arep_adhoc_reports WHERE arep_user_id = '".$user_id."' AND arep_auto_id = '".$id."' AND arep_isactive = '1'";
$result_data = mysql_query($query_data) or die(mysql_error());
$row_data = mysql_fetch_array($result_data);

$acctSelected = explode("||", $row_data['arep_acct']);
$tickSelected = explode("||", $row_data['arep_tick']);
$listSelected = explode("||", $row_data['arep_list']);

if(count($acctSelected) > 0)
{
	for($i = 0; $i < count($acctSelected); $i++)
	{
		$acctSelected[$i] = str_replace("|","",$acctSelected[$i]); 	
	}
}

if(count($tickSelected) > 0 AND $tickSelected[0] != '')
{
	for($i = 0; $i < count($tickSelected); $i++)
	{
		$tickSelected[$i] = str_replace("|","",$tickSelected[$i]); 	
	}
}

if(count($listSelected) > 0)
{

	for($i = 0; $i < count($listSelected); $i++)
	{
		$listSelected[$i] = str_replace("|","",$listSelected[$i]); 	
	}
}
/*
for($i = 0; $i < count($acctSelected); $i++)
{
echo $acctSelected[$i] . "<BR>";
}

echo '<BR><BR>';

for($i = 0; $i < count($tickSelected); $i++)
{
echo $tickSelected[$i] . "<BR>";
}

echo '<BR><BR>';

for($i = 0; $i < count($listSelected); $i++)
{
echo $listSelected[$i] . "<BR>";
}

echo '<BR><BR>';
*/
?>