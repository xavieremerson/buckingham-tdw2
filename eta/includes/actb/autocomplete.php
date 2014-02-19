<?  
    include('../includes/dbconnect.php');
    include('../includes/global.php');
	  include('../includes/functions.php');
?>
<html>
<head>
<script language="javascript" type="text/javascript" src="actb.js"></script>
<script language="javascript" type="text/javascript" src="common.js"></script>
<script>
var clientarray=new Array(
													<?
													$query_sel_client = "SELECT comm_advisor_code, trim(comm_advisor_name) as comm_advisor_name 
																								FROM lkup_clients
																								ORDER BY comm_advisor_name, comm_advisor_code";
													$result_sel_client = mysql_query($query_sel_client) or die(mysql_error());
													?>																
													<?
													$count_row_client = 0;
													while($row_sel_client = mysql_fetch_array($result_sel_client))
													{
														if ($row_sel_client["comm_advisor_name"] == '') {
														$display_val_client = $row_sel_client["comm_advisor_code"];
														} else {
														$display_val_client = str_replace("'","\\'",$row_sel_client["comm_advisor_name"]);
														}
														echo "'". $display_val_client . "  [" .$row_sel_client["comm_advisor_code"]."]',"."\n";
													}
													?>
													'');

function set_val_null(str_id) {
	if (document.getElementById(str_id).value == 'Enter Client') {
		document.getElementById(str_id).value = ""; 
	}
}

</script>

</head>

<body>
<?
function extract_client ($str) {
	return substr($str,strpos($str,"[")+1,4); //Code is always 4 characters long.
}

if($tsub or $val_client) {
	echo extract_client($val_client);
}

?>

<form name="test" action="<?=$PHP_SELF?>" method="get">
<input type='text' name="val_client" style='font-family:verdana;width:200px;font-size:12px' id='tb' value='Enter Client' onFocus="set_val_null('tb')" /> 
<input type="submit" name="tsub" value="test">
</form>
<script>
var obj = actb(document.getElementById('tb'),clientarray);
</script>
</body>
</html>