<script language="JavaScript" src="includes/js/popup.js"></script>
<script language="JavaScript" src="includes/js/ajax_tbx.js"></script>
<script language="javascript" src="includes/prototype/prototype.js"></script>
<script language ="Javascript">
<!--
function get_companyname_from_symbol(symbol) {
	AjaxRequest.get(
		{
			'url':'get_companyname.php?symbol='+ symbol
			,'onSuccess':function(req){ document.getElementById('compname').innerHTML=req.responseText; }
			,'onError':function(req){ document.getElementById('compname').innerHTML='Retrieve error from Yahoo Finance';}
		}
	);
}

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
	allitems = Form.serialize("stocklist");
	var params_val = allitems;
	//alert(params_val);
	showDetail(params_val);
}

function showDetail(str)
{ 
	var url = "<?=$_site_url?>stocklist_entry_process.php" + "?" +  str;
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
			document.AELT.visibility = 'visible'; 
		} 
		else { // IE 4 
			alert("IE 4");
			document.all.AELT.style.visibility = 'visible'; 
		}
	} 

	document.forms["stocklist"].reset();
	setFocus('symbol');
	//document.ext_trades.vdate.value = ""
	//document.ext_trades.vaccount.value = ""
	//document.ext_trades.vbuysell.value = "B"
	//document.ext_trades.vsymbol.value = ""
	//document.ext_trades.vquantity.value = ""
	//document.ext_trades.vprice.value = ""
	//document.getElementById('compname').innerHTML=""
		
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
<body onLoad="setFocus('symbol');"><!-- showDetail('');-->
<?
tsp(100, "Watch List (Data Entry)");
?>
				 <form id="stocklist" name="stocklist">
					<table border="0">
						<tr>
							<td colspan="3"><?=$str_status?>
							</td>
						</tr>
						<tr>
							<td class="ilt">Symbol</td>
							<td><input class="text" name="symbol" id="symbol" type="text" value="" size="12" maxlength="12"/></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td class="ilt">Start Time</td>
							<td>
							<select name="smonth" id="smonth">
              	<?
								for ($i=1;$i<13;$i++) {
								  if ($i == date('m')) { $msel =  " selected "; } else { $msel =  ""; }
									echo "<option value='".$i."' ". $msel . ">".$i."</option>\n";
								}
								?>
               				 </select>
							 <select name="sday" id="sday">
              	<?
								for ($i=1;$i<32;$i++) {
								  if ($i == date('d')) { $dsel =  " selected "; } else { $dsel =  ""; }
									echo "<option value='".$i."' ". $dsel . ">".$i."</option>\n";
								}
								?>
                </select>
                <select name="syear" id="syear">
                <option value="<?=date('Y')?>" selected><?=date('Y')?></option>
                <option value="<?=date('Y')+1?>"><?=date('Y')+1?></option>
								</select>
                &nbsp;
							 <select name="shour" id="shour">
              	<?
								for ($i=1;$i<13;$i++) {
								  if ($i == date('H')) { $hsel =  " selected "; } else { $hsel =  ""; }
									echo "<option value='".$i."' ". $hsel . ">".$i."</option>\n";
								}
								?>
                </select>
							 <select name="smin" id="smin">
              	<?
								for ($i=0;$i<60;$i++) {
								  if ($i == date('i')) { $minsel =  " selected "; } else { $minsel =  ""; }
									echo "<option value='".str_pad($i, 2, "0", STR_PAD_LEFT)."' ". $minsel . ">".str_pad($i, 2, "0", STR_PAD_LEFT)."</option>\n";
								}
								?>
                </select>
                <select name="sampm" id="sampm">
                <option value="am" <? if (date('a')=='am') {echo " selected ";}?>>AM</option>
                <option value="pm" <? if (date('a')=='pm') {echo " selected ";}?>>PM</option>
								</select>
              </td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td valign="top" style="color: #002E4E;font-family: verdana;font-size: 11px;font-weight: bold;text-align: left;padding: 0px 15px 0px 0px;text-decoration: none;">End Time</td>
							<td>
							<select name="emonth" id="emonth">
              	<?
								for ($i=1;$i<13;$i++) {
								  if ($i == date('m')) { $msel =  " selected "; } else { $msel =  ""; }
									echo "<option value='".$i."' ". $msel . ">".$i."</option>\n";
								}
								?>
               </select>
							 <select name="eday" id="eday">
              	<?
								for ($i=1;$i<32;$i++) {
								  if ($i == date('d')) { $dsel =  " selected "; } else { $dsel =  ""; }
									echo "<option value='".$i."' ". $dsel . ">".$i."</option>\n";
								}
								?>
                </select>
                <select name="eyear" id="eyear">
                <option value="<?=date('Y')?>" selected><?=date('Y')?></option>
                <option value="<?=date('Y')+1?>"><?=date('Y')+1?></option>
								</select>
                &nbsp;
							 <select name="ehour" id="ehour">
              	<?
								for ($i=1;$i<13;$i++) {
								  if ($i == date('H')) { $hsel =  " selected "; } else { $hsel =  ""; }
									echo "<option value='".$i."' ". $hsel . ">".$i."</option>\n";
								}
								?>
                </select>
							 <select name="emin" id="emin">
              	<?
								for ($i=0;$i<60;$i++) {
								  if ($i == date('i')) { $minsel =  " selected "; } else { $minsel =  ""; }
									echo "<option value='".str_pad($i, 2, "0", STR_PAD_LEFT)."' ". $minsel . ">".str_pad($i, 2, "0", STR_PAD_LEFT)."</option>\n";
								}
								?>
                </select>
                <select name="eampm" id="eampm">
                <option value="am" <? if (date('a')=='am') {echo " selected ";}?>>AM</option>
                <option value="pm" <? if (date('a')=='pm') {echo " selected ";}?>>PM</option>
								</select>
              <font class="ilt">OR 
							<input type="checkbox" name="is_manual_close" id="is_manual_close" value="1"> Keep Active until manual removal.</font></td>
              <td>&nbsp;</td>
						</tr>
						<tr>
							<td valign="top"  style="color: #002E4E;font-family: verdana;font-size: 11px;font-weight: bold;text-align: left;padding: 0px 15px 0px 0px;text-decoration: none;">Notes:</td>
							<td colspan="2"><textarea name="notes"  id="notes" rows="2" cols="80"></textarea></td>
						 </tr>  
            <tr>
              <td class="ilt">Entered By:</td>
              <td class="ilt" colspan="2"><?=$userfullname?></td>
            </tr>
						<tr>
							<td colspan="3">
              <input name="Submit" id="Submit" type="button" onClick="getFormValues()" value="&nbsp;&nbsp;&nbsp;SAVE&nbsp;&nbsp;&nbsp;">&nbsp;&nbsp;<input type="reset" value="CLEAR FORM">
							</td>
						 </tr>  
					</table>  
					<input type="hidden" name="venteredby" value="<?=$user_id?>">
					</form>
<?
tep();
tsp(100, "Watch List Report");
?>
														<br><br>
                            <table width="100%" cellpadding="0" cellspacing="0">
														<form name="clnt_activity" id="idclnt_activity" action="stocklist_entry_report_excel.php" method="post" target="_blank">
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
																<td width="10">From:</td>
																<td width="10"><input type="text" id="iddatefrom" class="Text1" name="datefrom" size="12" maxlength="12" value="<?=$sel_datefrom?>"></td>
																<td width="20" align="center"><A HREF="#" onClick="calfrom.select(document.forms['clnt_activity'].datefrom,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
																<td width="5">&nbsp;</td>
																<td width="10">To:</td>
																<td width="10"><input type="text" id="iddateto" class="Text1" name="dateto" size="12" maxlength="12" value="<?=$sel_dateto?>"></td>
																<td width="20" align="center"><A HREF="#" onClick="calto.select(document.forms['clnt_activity'].dateto,'anchor2','MM/dd/yyyy'); return false;" NAME="anchor2" ID="anchor2"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
																<td width="5">&nbsp;</td>
																<td width="10"><input type="image" src="images/lf_v1/form_submit.png"></td>
																<td width="10" align="center">&nbsp;</td>
																<td width="10" align="center">&nbsp;</td>
                                <td>&nbsp;</td>
                              </tr>
                            </form>
                            </table>
                            <br>
	<DIV ID="divfrom" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
	<DIV ID="divto" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>			

<?
tep();
?>
<table width="100%"><!-- style="visibility:hidden; display=none"-->
<tr>
	<td> 
	<iframe name="if_status" id="if_status" src="stocklist_entry_process.php" height="500" width="100%" marginwidth="0" marginheight="0" scrolling="yes" frameborder="0"></iframe>
	</td>
</tr>
</table>
</body>

