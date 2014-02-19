<script language ="Javascript">
<!--
function getFormValues(){
params_val = "sel_month=" + document.pos_config.sel_month.value + "&";
params_val = params_val + "sel_symbol=" + document.pos_config.sel_symbol.value + "&";
params_val = params_val + "rnd_process_id=" + Math.round(100*Math.random());
//alert(params_val);
showDetail(params_val);
}

function showDetail(str)
{ 
	var url = "<?=$_site_url?>bcm_pos_gen_excel.php" + "?" +  str;
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

<style type="text/css">
<!--
.style_symbol {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 14px;
	font-weight: bold;
	color: #003399;
	background-color: #F4FAFF;
	letter-spacing: 1px;
	text-align: left;
	border: 1px solid #0066FF;
}
-->
</style>
<?

function lastbizday($month = '', $year = '') {
	 if (empty($month)) {
			$month = date('m');
	 }
	 if (empty($year)) {
			$year = date('Y');
	 }

	 $result = strtotime("{$year}-{$month}-01");
	 $result = strtotime('-1 day', strtotime('+1 month', $result));

	 if (check_holiday(date('Y-m-d', $result))==1 || date('D', $result) == 'Sat' || date('D', $result) == 'Sun') {
	 		$result = strtotime("{$year}-{$month}-01");
	 		$result = strtotime('-2 day', strtotime('+1 month', $result));		
			if (check_holiday(date('Y-m-d', $result))==1 || date('D', $result) == 'Sat' || date('D', $result) == 'Sun') {
				$result = strtotime("{$year}-{$month}-01");
				$result = strtotime('-3 day', strtotime('+1 month', $result));		
				if (check_holiday(date('Y-m-d', $result))==1 || date('D', $result) == 'Sat' || date('D', $result) == 'Sun') {
					$result = strtotime("{$year}-{$month}-01");
					$result = strtotime('-4 day', strtotime('+1 month', $result));		
					if (check_holiday(date('Y-m-d', $result))==1 || date('D', $result) == 'Sat' || date('D', $result) == 'Sun') {
						return "ERROR";
					} else {
						return date('Y-m-d', $result);
					}
				} else {
	 					return date('Y-m-d', $result);
				}
			} else {
	 				return date('Y-m-d', $result);
			}
	 } else {
	 		return date('Y-m-d', $result);
	 }
}

$thismonth       = date('Y-m-d');// '2009-12-03';
$thismonth       = strtotime($thismonth);
$lastmonth       = strtotime("-1 month", $thismonth);
$monthbeforelast = strtotime("-2 month", $thismonth);
$monthbeforelast2 = strtotime("-3 month", $thismonth);
$monthbeforelast3 = strtotime("-4 month", $thismonth);
$monthbeforelast4 = strtotime("-5 month", $thismonth);
$monthbeforelast5 = strtotime("-6 month", $thismonth);
$monthbeforelast6 = strtotime("-7 month", $thismonth);
$monthbeforelast7 = strtotime("-8 month", $thismonth);
$monthbeforelast8 = strtotime("-9 month", $thismonth);
$monthbeforelast9 = strtotime("-10 month", $thismonth);
$monthbeforelast10 = strtotime("-11 month", $thismonth);
$monthbeforelast11 = strtotime("-12 month", $thismonth);

// turn them into the proper format

$val_lastmonth  	 = lastbizday(date("m", $lastmonth),date("Y", $lastmonth));
$val_monthbeforelast = lastbizday(date("m", $monthbeforelast),date("Y", $monthbeforelast));
$val_monthbeforelast2 = lastbizday(date("m", $monthbeforelast2),date("Y", $monthbeforelast2));
$val_monthbeforelast3 = lastbizday(date("m", $monthbeforelast3),date("Y", $monthbeforelast3));
$val_monthbeforelast4 = lastbizday(date("m", $monthbeforelast4),date("Y", $monthbeforelast4));
$val_monthbeforelast5 = lastbizday(date("m", $monthbeforelast5),date("Y", $monthbeforelast5));
$val_monthbeforelast6 = lastbizday(date("m", $monthbeforelast6),date("Y", $monthbeforelast6));
$val_monthbeforelast7 = lastbizday(date("m", $monthbeforelast7),date("Y", $monthbeforelast7));
$val_monthbeforelast8 = lastbizday(date("m", $monthbeforelast8),date("Y", $monthbeforelast8));
$val_monthbeforelast9 = lastbizday(date("m", $monthbeforelast9),date("Y", $monthbeforelast9));
$val_monthbeforelast10 = lastbizday(date("m", $monthbeforelast10),date("Y", $monthbeforelast10));
$val_monthbeforelast11 = lastbizday(date("m", $monthbeforelast11),date("Y", $monthbeforelast11));


$lastmonth  	 = date("F", $lastmonth);
$monthbeforelast = date("F", $monthbeforelast);
$monthbeforelast2 = date("F", $monthbeforelast2);
$monthbeforelast3 = date("F", $monthbeforelast3);
$monthbeforelast4 = date("F", $monthbeforelast4);
$monthbeforelast5 = date("F", $monthbeforelast5);
$monthbeforelast6 = date("F", $monthbeforelast6);
$monthbeforelast7 = date("F", $monthbeforelast7);
$monthbeforelast8 = date("F", $monthbeforelast8);
$monthbeforelast9 = date("F", $monthbeforelast9);
$monthbeforelast10 = date("F", $monthbeforelast10);
$monthbeforelast11 = date("F", $monthbeforelast11);

tsp(100, "BCM POS Report 13G");
?>
<form name="pos_config">
<table height="100%" border="0" cellspacing="0" cellpadding="5">
  <tr>
		<td width="20">&nbsp;</td>
		<td width="400">&nbsp;</td>
		<td>&nbsp;</td>
	</tr>  
	<tr>
    <td class="ilt" align="right">&nbsp;&nbsp;&nbsp;Month</td>
    <td width="400">
					<select name="sel_month" size="1" >
					<option value="">&nbsp;SELECT MONTH&nbsp;&nbsp;</option>
					<option value="">_______________</option>
          <option value="<?php echo $val_lastmonth; ?>"><?php echo $lastmonth . " [".format_date_ymd_to_mdy($val_lastmonth)."]"; ?></option>
          <option value="<?php echo $val_monthbeforelast; ?>"><?php echo $monthbeforelast . " [".format_date_ymd_to_mdy($val_monthbeforelast)."]"; ?></option>
          <option value="<?php echo $val_monthbeforelast2; ?>"><?php echo $monthbeforelast2 . " [".format_date_ymd_to_mdy($val_monthbeforelast2)."]"; ?></option>
          <option value="<?php echo $val_monthbeforelast3; ?>"><?php echo $monthbeforelast3 . " [".format_date_ymd_to_mdy($val_monthbeforelast3)."]"; ?></option>
          <option value="<?php echo $val_monthbeforelast4; ?>"><?php echo $monthbeforelast4 . " [".format_date_ymd_to_mdy($val_monthbeforelast4)."]"; ?></option>
          <option value="<?php echo $val_monthbeforelast5; ?>"><?php echo $monthbeforelast5 . " [".format_date_ymd_to_mdy($val_monthbeforelast5)."]"; ?></option>
          <option value="<?php echo $val_monthbeforelast6; ?>"><?php echo $monthbeforelast6 . " [".format_date_ymd_to_mdy($val_monthbeforelast6)."]"; ?></option>
          <option value="<?php echo $val_monthbeforelast7; ?>"><?php echo $monthbeforelast7 . " [".format_date_ymd_to_mdy($val_monthbeforelast7)."]"; ?></option>
          <option value="<?php echo $val_monthbeforelast8; ?>"><?php echo $monthbeforelast8 . " [".format_date_ymd_to_mdy($val_monthbeforelast8)."]"; ?></option>
          <option value="<?php echo $val_monthbeforelast9; ?>"><?php echo $monthbeforelast9 . " [".format_date_ymd_to_mdy($val_monthbeforelast9)."]"; ?></option>
          <option value="<?php echo $val_monthbeforelast10; ?>"><?php echo $monthbeforelast10 . " [".format_date_ymd_to_mdy($val_monthbeforelast10)."]"; ?></option>
          <option value="<?php echo $val_monthbeforelast11; ?>"><?php echo $monthbeforelast11 . " [".format_date_ymd_to_mdy($val_monthbeforelast11)."]"; ?></option>
					</select>		
		</td>
		<td>&nbsp;</td>
  </tr>
  <tr>
		<td class="ilt" width="20">Symbols</td>
		<td width="400"><input class="style_symbol" type="text" name="sel_symbol" value="" size="80" maxlength="200" /></td>
		<td align="left" class="ilt" >&nbsp;&nbsp; Comma Separated values, e.g. ibm,yhoo,frx (Case does not matter)</td>
	</tr>  
  <tr>
    <td class="ilt" align="right">&nbsp;&nbsp;&nbsp;</td>
    <td><input class="button" name="Submit" type="button" onClick="getFormValues()" value="&nbsp;Create Report (XLS)&nbsp;"></td>
		<td>&nbsp;</td>
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