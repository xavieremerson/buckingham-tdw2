<?

include('../includes/dbconnect.php');
include('../includes/global.php'); 
include('../includes/functions.php'); 

?>

<SELECT class="input" NAME="Advisors" SIZE="30" style="width=400">
<OPTION VALUE="-1" class="headselect">S E L E C T &nbsp; A C C O U N T</OPTION>
<?
$query_advisor = "SELECT DISTINCT (
nadd_advisor
), nadd_rr_exec_rep
FROM nfs_nadd where nadd_advisor not like '&%' and nadd_advisor not like 'XXXX' 
ORDER BY nadd_advisor";	

$result_advisor = mysql_query($query_advisor) or die (mysql_error());
$count_i = 1;
while ( $row = mysql_fetch_array($result_advisor) ) 
{
?>
<option value="<?=$row["nadd_advisor"]?> : <?=$row["nadd_rr_exec_rep"]?>"><?=$row["nadd_advisor"]?> : <?=$row["nadd_rr_exec_rep"]?></option>
<?
$count_i = $count_i + 1;
}						
?>
</select>

								