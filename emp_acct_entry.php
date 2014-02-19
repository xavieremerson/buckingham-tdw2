<?
////
// Creates a dropdown option values with recordset
function create_option_values($data_query) {
	$result = mysql_query($data_query) or die(mysql_error("Function create_option_values has errors"));
	while ($row = mysql_fetch_array($result)) {
		echo '<option value="' . $row["d_value"] . '">' . $row["d_option"] . '</option>'."\n";
	}
}
?>
<script language="JavaScript" src="includes/js/popup.js"></script>
<script language="JavaScript" src="includes/js/ajax_tbx.js"></script>
<script language="javascript" src="includes/prototype/prototype.js"></script>
<script language ="Javascript">
<!--

function setFocus(nextid) {
  document.getElementById(nextid).focus();
}

function bar(evt, nextid){
var k=evt.keyCode||evt.which;
 if (k==13 && nextid != "") {
   setFocus(nextid);
 }
return k!=13;
}

function getFormValues(){
	allitems = Form.serialize("empacclist");
	var params_val = allitems;
	//alert(params_val);
	//return false;
	showDetail(params_val);
}

function showDetail(str)
{ 
	var url = "<?=$_site_url?>emp_acct_entry_process.php" + "?" +  str;
	//alert(url);
  //return false;
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
			document.AELT.visibility = 'visible'; 
		} 
		else { // IE 4 
			alert("IE 4");
			document.all.AELT.style.visibility = 'visible'; 
		}
	} 

	document.forms["empacclist"].reset();
	setFocus('emp_user_id');

} 
-->
</script>


<style type="text/css">
<!--
.txt_status {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10px;
	color: #0000FF;
}
-->
</style>
<body onLoad="setFocus('emp_user_id');"><!-- showDetail('');-->
<?
tsp(100, "Employee Account (Data Entry)");

//    emp_name_and_address_2  emp_name_and_address_3  emp_name_and_address_4  emp_name_and_address_5  emp_name_and_address_6    emp_acct_status  emp_close_date  emp_closed_by  emp_last_edit_time  emp_last_edit_ip  

?>
				 <form id="empacclist" name="empacclist">
					<table border="0">
						<tr>
							<td colspan="3"><?=$str_status?>
							</td>
						</tr>
          </table>
          
																<SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
																<SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
																	<SCRIPT LANGUAGE="JavaScript">
																	var calfrom = new CalendarPopup("divfrom");
																	calfrom.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
																	var calto = new CalendarPopup("divto");
																	calto.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
																</SCRIPT>						

          <table border="0" cellpadding="0" cellspacing="0"><tr class="ilt">
          <td align="left" width="231">Employee</td>
          <td align="left" width="231">Account Number</td>
          <td align="left" width="231">Establish Date</td>
					<td>&nbsp;</td>
          </tr>
					<tr class="ilt">
          <td align="left" width="100">
            <select name="emp_user_id" id="emp_user_id" size="1">
              <option value="" selected>Select Employee</option>
              <?=create_option_values("select ID as d_value, Fullname as d_option from users where user_isactive = 1 order by Fullname")?>
            </select>
          </td>
          <td width="201" align="left"><input class="text" name="emp_acct_number" id="emp_acct_number" type="text" value="" size="20"/></td>
          <td nowrap><input type="text" id="emp_establish_date" class="Text1" name="emp_establish_date" size="12" maxlength="12" value="<?=date('m/d/Y')?>">
          <A HREF="#" onClick="calfrom.select(document.forms['empacclist'].emp_establish_date,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A>
          </td>
					<td>&nbsp;</td>
          </tr>
          </table>

          <table border="0" cellpadding="0" cellspacing="0">
          <tr class="ilt">
          <td align="left" width="231">Name Address Line 1</td>
          <td align="left" width="231">Name Address Line 2</td>
          <td align="left" width="231">Name Address Line 3</td>
          </tr>
          <tr>
          <td align="left"><input class="text" name="emp_name_and_address_1" id="emp_name_and_address_1" type="text" value="" size="36" /></td>
          <td align="left"><input class="text" name="emp_name_and_address_2" id="emp_name_and_address_2" type="text" value="" size="36" /></td>
          <td align="left"><input class="text" name="emp_name_and_address_3" id="emp_name_and_address_3" type="text" value="" size="36" /></td>
          </tr>
          </table>
          
          <table border="0" cellpadding="0" cellspacing="0">
          <tr class="ilt">
          <td align="left" width="231">Name Address Line 4</td>
          <td align="left" width="231">Name Address Line 5</td>
          <td align="left" width="231">Name Address Line 6</td>
          </tr>
          <tr>
          <td align="left"><input class="text" name="emp_name_and_address_4" id="emp_name_and_address_4" type="text" value="" size="36" /></td>
          <td align="left"><input class="text" name="emp_name_and_address_5" id="emp_name_and_address_5" type="text" value="" size="36" /></td>
          <td align="left"><input class="text" name="emp_name_and_address_6" id="emp_name_and_address_6" type="text" value="" size="36" /></td>
          </tr>
          </table>

          <table border="0" cellpadding="0" cellspacing="0">
          <tr class="ilt">
          <td align="left" width="100">Comments</td>
          </tr>
          <tr>
          <td align="left"><textarea name="emp_comments" id="emp_comments" value="" rows="3" cols="100"/></textarea></td>
          </tr>
          </table>

					<table width="100%">
            <tr>
              <td class="ilt" colspan="3" align="left">Entered By: <?=$userfullname?></td>
            </tr>
						<tr>
							<td colspan="2">
              <input name="Submit" id="Submit" type="button" onClick="getFormValues()" value="&nbsp;&nbsp;&nbsp;SAVE&nbsp;&nbsp;&nbsp;">&nbsp;&nbsp;<input type="reset" value="CLEAR FORM">
							</td>
              <td align="right" valign="bottom"><img src="images/lf_v1/exp2excel.png" border="0">&nbsp;&nbsp;&nbsp;<a href="emp_acct_entry_export_excel.php?uid=<?=$user_id?>&mode=all" target="_blank" class="ilt">[ ALL ACCOUNTS ]</a>&nbsp;&nbsp;&nbsp;<a href="emp_acct_entry_export_excel.php?uid=<?=$user_id?>&mode=active" target="_blank" class="ilt">[ ACTIVE ACCOUNTS ]</a></td>
						 </tr>  
					</table>  
					<input type="hidden" name="venteredby" value="<?=$user_id?>">
					<input type="hidden" name="uid" value="<?=$user_id?>">
					</form>
<?
tep();
?>
<table width="100%"><!-- style="visibility:hidden; display=none"-->
<tr>
	<td> 
	<iframe name="if_status" id="if_status" src="emp_acct_entry_process.php?uid=<?=$user_id?>" height="400" width="100%" marginwidth="0" marginheight="0" scrolling="yes" frameborder="0"></iframe>
	</td>
</tr>
</table>
	<DIV ID="divfrom" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
	<DIV ID="divto" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>			
</body>