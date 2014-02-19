<?
ini_set('max_execution_time', 72000);
?>
<script language="JavaScript" src="includes/prototype/prototype.js"></script>
<script language="JavaScript" src="includes/js/ajax_tbx.js"></script>
<script language ="Javascript">
<!--
function procx() {

	var valdatefrom = $("iddatefrom").value;
	var valdateto = $("iddateto").value;

	//alert(valdatefrom);
	//alert(valdateto);
	
	
	if (valdatefrom == "" || valdateto == "") {
	  alert("Please select Start and End Date.");
		return false;
	}
	
	var progressbar;
	progressbar = 'Report generation in progress. Please do not close this window.<br><br><img src="images/loading-bar.gif" border="0">';
	document.getElementById('notify').innerHTML=progressbar; 
	
	//return false;
	//alert(valqtr + " " + valyear);
	var autotransmit;
	if ($("dotransmit").checked == 1) {
		autotransmit = 1;
	} else {
		autotransmit = 0;
	}
	

	AjaxRequest.get(
			{
				'url':'gta_processes_ajx.php?valdatefrom='+ valdatefrom + '&valdateto=' + valdateto + '&autotransmit=' + autotransmit
				,'onSuccess':function(req){ 
																		parse_req(req.responseText);
																	}
				,'onError':function(req){ document.getElementById('notify').innerHTML='Program Error! Please contact Technical Support.';}
			}
		);
}

function parse_req(response) {
		document.getElementById('notify').innerHTML=response; 
	//alert($response);
}

function noenter() {
  return !(window.event && window.event.keyCode == 13); }
-->
</script>

<?
tsp(100, "Create File for GT Analytics (Auto/Manual Transmission)");
?> 		
    <!-- START TABLE 4 -->
    <!-- class="tablewithdata" -->
            <table width="100%" bgcolor="#FFFFFF">
              <tr><td>&nbsp;</td></tr>
              <tr>
                <td>
                <table cellpadding="0" cellspacing="0" border="0">
                  <form name="clnt_activity" id="idclnt_activity">
                  <tr>

                    <SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
                    <SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
                      <SCRIPT LANGUAGE="JavaScript">
                      var calfrom = new CalendarPopup("divfrom");
                      calfrom.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
                      var calto = new CalendarPopup("divto");
                      calto.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
                      
                      </SCRIPT>						
                    <td width="5">&nbsp;</td>
                    <td width="75" class="ilt" align="right">Start Date:</td>
                    <td width="10"><input type="text" id="iddatefrom" class="Text1" name="datefrom" size="12" maxlength="12" value="<?=$sel_datefrom?>"></td>
                    <td width="20" align="center"><A HREF="#" onClick="calfrom.select(document.forms['clnt_activity'].datefrom,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
                    <td width="25">&nbsp;</td>
                    <td width="75" class="ilt" align="right">End Date:</td>
                    <td width="10"><input type="text" id="iddateto" class="Text1" name="dateto" size="12" maxlength="12" value="<?=$sel_dateto?>"></td>
                    <td width="20" align="center"><A HREF="#" onClick="calto.select(document.forms['clnt_activity'].dateto,'anchor2','MM/dd/yyyy'); return false;" NAME="anchor2" ID="anchor2"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
                    <td width="5">&nbsp;</td>
                    <td width="10"><input type="image" src="images/lf_v1/form_submit.png" onclick="procx(); return false;"></td>
                    <td width="10" align="center">&nbsp;</td>
                    <td width="10" align="center">&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                  	<td colspan="13">
                    	<input type="checkbox" name="dotransmit" id="dotransmit" value="1" /> &nbsp;&nbsp;<a class="ilt">Check this box if you want the output to be transmitted to GT Analytics</a> 
                    </td>
                  </tr>
                  </form>			
                </table>
                </td> 
              </tr>
              <tr><td>&nbsp;</td></tr>
              <tr><td>&nbsp;<div id="notify" class="ilt"></div></td></tr>
              <tr><td>&nbsp;</td></tr>
              <tr><td class="ilt">Please Note: For one day worth of data, it takes an average of 12 seconds to process.<br />
                                  As an example, if you select a time-frame of a quarter, it will take approximately 15 minutes to process.<br />
                                  After completion, you will be provided with a link to download the file (Zipped, Excel:CSV file).<br />
                                  After download, you can share/send this file to relevant recipients via email or any other mode of your choosing.</td></tr>
              <tr><td>&nbsp;</td></tr>
            </table>
            <!-- END TABLE 4 -->
<?
tep();

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	if ($submit) 
	{
		$result = mysql_query("Update lkup_ftp_servers set host_server = '$host_server', host_port = '$host_port', username = '$username', password = '$password' where auto_id = 1") or die (mysql_error());
	
		echo '<br>&nbsp;&nbsp;&nbsp;&nbsp;<font color="green">&nbsp;&nbsp;<strong>Configuration Saved!</strong></font>';
	}
	
	$resultmember = mysql_query("SELECT auto_id, host_server, host_port, username, password FROM lkup_ftp_servers where auto_id = 1") or die (mysql_error());
	while ( $row = mysql_fetch_array($resultmember) ) 
	{
		$host_server = $row["host_server"];
		$host_port = $row["host_port"];
		$username = $row["username"];
		$password = $row["password"];
	}
	
?>

<br />
<? tsp(100, "Transmission Configuration Information"); ?>
<form action="<?=$php_self?>" method="post">
<table width="100%" bgcolor="#FFFFFF">
  <tr><td>&nbsp;</td></tr>
  <tr>
    <td>


    
<table cellpadding="0" cellspacing="8" border="0">
  <tr>
    <td>
      <table cellspacing="11" >
        <tr valign="top"><td><p class="Contact">&nbsp;&nbsp;&nbsp;FTP Server (Hostname or IP)</p></td><td><p class="Contact">:</p></td><td><p><input class="Text" type="text" name="host_server" size="40" maxlength="100" value="<?=$host_server?>"></p></td></tr>
        <tr valign="top"><td><p class="Contact">&nbsp;&nbsp;&nbsp;FTP/S Port</p></td><td><p class="Contact">:</p></td><td><p><input class="Text" type="text" name="host_port" size="40" maxlength="40" value="<?=$host_port?>"></p></td><td></tr>
        <tr valign="top"><td><p class="Contact">&nbsp;&nbsp;&nbsp;Username</p></td><td><p class="Contact">:</p></td><td><p><input class="Text" type="text" name="username" size="40" maxlength="20" value="<?=$username?>"></p></td></tr>
        <tr valign="top"><td><p class="Contact">&nbsp;&nbsp;&nbsp;Password</p></td><td><p class="Contact">:</p></td><td><p><input class="Text" type="text" name="password" size="40" maxlength="50" value="<?=$password?>"></p></td></tr>
      </table>
    </td>
  </tr>
</table>


<table cellpadding="0" cellspacing="4" border="0">
  <tr>
    <td colspan="3">
      <table cellpadding="2" cellspacing="0" border="0">
        <tr valign="top"><td colspan="3" align="center"><p><input name="submit" class="Submit" type="submit" value="Update Configuration"></p></td></tr> 
      </table>
    </td>
  </tr>
</table>		

</td>
</tr>
</table>

</form>
<? tep(); ?>




					
	<DIV ID="divfrom" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
	<DIV ID="divto" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>			