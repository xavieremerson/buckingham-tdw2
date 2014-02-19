<?
tsp(100, "Fidelity Report (Wierd name?) [Configuration Parameters]");
?>
<script language ="Javascript">
<!--
function getFormValues(){
params_val = "sel_month=" + document.prc_config.sel_month.value + "&";
params_val = params_val + "rnd_process_id=" + Math.round(100*Math.random()) + "&";
showDetail(params_val);
}

function showDetail(str)
{ 
	var url = "<?=$_site_url?>fidemp_gen_excel.php" + "?" +  str;
	//alert(url);
  var trid;
	trid = 'if_status'; 
	if (document.getElementById) { // DOM3 = IE5, NS6 Generally this is what it is
			document.getElementById(trid).style.visibility = 'visible'; 
			document.getElementById(trid).style.display = 'block'; 
			document.getElementById(trid).src=url;
			//alert(document.getElementById(trid).src)
	} 
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

	//setFocus('vemployee');
	//document.ext_accts.vemployee.value = ""
	//document.ext_accts.vaccount.value = ""
	//document.ext_accts.vcustodian.value = ""		
} 
-->
</script>

<form name="prc_config">
<table height="100%" border="0" cellspacing="0" cellpadding="5">
  <tr>
		<td width="100">&nbsp;</td>
		<td width="400">&nbsp;</td>
	</tr>  
	<tr>
    <td class="ilt" align="right">&nbsp;&nbsp;&nbsp;Select Month</td>
    <td width="400">
					<select name="sel_month" size="1" >
					<option value="">&nbsp;MONTH&nbsp;&nbsp;</option>
					<option value="">_______________</option>
					<?
					echo create_commission_month();
					?>
					</select>		
		</td>
  </tr>
  <tr>
    <td class="ilt" align="right">&nbsp;&nbsp;&nbsp;</td>
    <td><input class="button" name="Submit" type="button" onClick="getFormValues()" value="&nbsp;Create Report (XLS)&nbsp;"></td>
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

