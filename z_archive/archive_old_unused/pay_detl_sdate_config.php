<?
//CHK 02/05/08
tsp(100, "Payout Report Configuration");
?>
<script language ="Javascript">
<!--
function getFormValues(){
params_val = "sel_month=" + document.prc_config.sel_month.value + "&";
params_val = params_val + "tdw_user=<?=$user_id?>&";
params_val = params_val + "percent_payout_comm=" + document.prc_config.percent_payout_comm.value + "&";
params_val = params_val + "rnd_process_id=" + Math.round(100*Math.random()) + "&";
params_val = params_val + "check_basis=" + document.prc_config.check_basis.value;
showDetail(params_val);
}

function showDetail(str)
{ 
	var url = "<?=$_site_url?>pay_detl_sdate_gen_excel.php" + "?" +  str;
  var trid;
	trid = 'if_status'; 
	if (document.getElementById) { // DOM3 = IE5, NS6 Generally this is what it is
			document.getElementById(trid).style.visibility = 'visible'; 
			document.getElementById(trid).style.display = 'block'; 
			document.getElementById(trid).src=url;
	} 
	/*
	else { 
		if (document.layers) { // Netscape 4 
			alert("Netscape 4");
			document.trid.visibility = 'visible'; 
		} 
		else { // IE 4 
			alert("IE 4");
			document.all.trid.style.visibility = 'visible'; 
		}
	} 
	*/
} 
-->
</script>




<form name="prc_config">
<table height="100%" border="0" cellspacing="0" cellpadding="5">
  <tr>
		<td width="200">&nbsp;</td>
		<td width="400">&nbsp;</td>
	</tr>  
	<tr>
    <td class="ilt" align="right">&nbsp;&nbsp;&nbsp;Select Month</td>
    <td width="400">
					<select name="sel_month" size="1" >
					<option value="">&nbsp;BROKERAGE MONTH&nbsp;&nbsp;</option>
					<option value="">_______________</option>
					<?
					echo create_commission_month();
					?>
					</select>		
		</td>
  </tr>
  <tr>
    <td class="ilt" align="right">&nbsp;&nbsp;&nbsp;Enter Payout % (Comm.)</td>
    <td><input class="text" type="text" name="percent_payout_comm" value="<?=get_global_param_val ("percent_payout_comm")?>" size="20"> %</td>
  </tr>
  <tr>
    <td class="ilt" align="right">&nbsp;&nbsp;&nbsp;Checks</td>
    <td>
		<select name="check_basis" size="1">
				<option value="C">Calendar Month Basis</option>
				<option value="B">Brokerage Month Basis</option>
				</select>
		</td>
  </tr>
  <tr>
    <td class="ilt" align="right">&nbsp;&nbsp;&nbsp;</td>
    <td><input class="button" name="Submit" type="button" onClick="getFormValues()" value="&nbsp;Create Payout Report (XLS)&nbsp;"></td>
  </tr>
</table>
<input type="hidden" name="venteredby" value="<?=$user_id?>">
</form>
<?
tep();
?>
<table width="100%"><!-- style="visibility:hidden; display=none"-->
<tr>
	<td valign="top"> 
	<iframe src="" name="if_status" width="100%" height="400" marginwidth="0" marginheight="0" align="top" scrolling="yes" frameborder="0" id="if_status"></iframe>
	</td>
</tr>
</table>


<?
////
//  get a config value from the db

function get_global_param_val ($param_name) {

	$qry = "select var_value from var_global_parameters where var_name = '".$param_name."' and var_isactive = 1";
	$result = mysql_query($qry) or die(tdw_mysql_error($qry));
	 while($row = mysql_fetch_array($result)) {
	 $var_value = $row["var_value"];
	 }

	return $var_value;
}

//echo get_global_param_val ("percent_payout");
?>

