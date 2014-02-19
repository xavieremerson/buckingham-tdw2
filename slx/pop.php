<?

include('../includes/dbconnect.php');
include('../includes/global.php'); 
include('../includes/functions.php'); 
?>
<script language="JavaScript" type="text/JavaScript">
function process(form) {
//alert("This is a testfor " + document.test.Advisors.options[document.test.Advisors.selectedIndex].value);
window.opener.document.processowner.newmap<?=$count?>.value = document.test.Advisors.options[document.test.Advisors.selectedIndex].value;
this.close();

		return true;

}
</script>


<?
/* 

		$qry_codename = "SELECT * from slx_codemap";
		$result_codename = mysql_query($qry_codename) or die (mysql_error());	 
		$arr_codes = array();

		while ( $row_codes = mysql_fetch_array($result_codename) ) 
			{
				$arr_codes[$row_codes["codeval"]] = $row_codes["nameval"];
			}

*/
		$qry_codename = "SELECT `nadd_advisor` as codeval , min( `nadd_address_line_1` ) as nameval
											FROM `nfs_nadd` 
											GROUP BY `nadd_advisor` ";
		$result_codename = mysql_query($qry_codename) or die (mysql_error());	 
		$arr_codes = array();

		while ( $row_codes = mysql_fetch_array($result_codename) ) 
			{
				$arr_codes[$row_codes["codeval"]] = $row_codes["nameval"];
			}





$qry_slx = "SELECT auto_id, rrname, rrnum from slx_bmap where auto_id = ".$rep;
$result_slx = mysql_query($qry_slx) or die (mysql_error());	 

while ( $row_slx = mysql_fetch_array($result_slx) ) 
	{
		$getrrnum = explode(";",$row_slx["rrnum"]);
	}
foreach($getrrnum as $key => $value)
	 {
	$str_rrs .= $value . ",&nbsp;";
	$rrnumval = trim($value);
	if ($rrnumval != '') {
	$sqlstr .= " or nadd_rr_exec_rep = '".$rrnumval."' ";
	 }
}
?>
<a class="showtoptext"><?=$str_rrs?></a><br>
<a class="showtoptext">Finding Match for </a><a class="showtop"><?=$company?></a>
<?


$query_advisor = "SELECT DISTINCT (
nadd_advisor
), nadd_rr_exec_rep
FROM nfs_nadd where nadd_rr_exec_rep = '99999' ".$sqlstr."
ORDER BY nadd_advisor";
//echo $query_advisor;	
?>
<style type="text/css">
<!--
.showdata {
	font-family: "Courier New", Courier, mono;
	font-size: 14px;
	font-weight: bold;
	color: #000099;
	background-color: #F3F3F3;
}
.showtop {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
	color: #990000;
}
.showtoptext {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: normal;
	color: #990000;
}
-->
</style>
<form name="test">
<SELECT class="showdata" NAME="Advisors" SIZE="20" class="input" style="width=380" onDblClick="return process(this);">
<?
$result_advisor = mysql_query($query_advisor) or die (mysql_error());
$count_i = 1;
while ( $row = mysql_fetch_array($result_advisor) ) 
{
	if (strpos($row["nadd_advisor"],"&") === false) {
	?>
	<option value="<?=$row["nadd_advisor"]?> : <?=$row["nadd_rr_exec_rep"]?>                                             ^<?=$count?>"><?=$row["nadd_advisor"]?> : <?=$row["nadd_rr_exec_rep"]?> : <?=$arr_codes[$row["nadd_advisor"]]?> </option>
	<?
	}
$count_i = $count_i + 1;
}						
?>
</select>
</form>