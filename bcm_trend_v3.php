<?
$trade_date_to_process = previous_business_day();
?>

<script language="javascript" src="includes/prototype/prototype.js"></script>
<SCRIPT LANGUAGE="JavaScript">

function showDiv() {

	if($("rd_seldiv_0").checked) {
		$("div_citta").style.visibility = "hidden";
		$("div_citta").style.display = "none";
		$("div_symbol").style.visibility = "visible";
		$("div_symbol").style.display = "block";
	} else {
		$("div_citta").style.visibility = "visible";
		$("div_citta").style.display = "block";
		$("div_symbol").style.visibility = "hidden";
		$("div_symbol").style.display = "none";
	}
	
}

</SCRIPT>
<?
/*
if ($_POST) {
print_r($_POST);
}

*/

if ($proc_symbol) { // form submitted //format_date_ymd_to_mdy($trade_date_to_process)

			$checked_symbol = 1;

} else if ($proc_citta) {

			$checked_symbol = 0;
			$datefrom = $c_datefrom;
			$dateto = $c_dateto;

} else {

			$checked_symbol = 1;
			$datefrom = format_date_ymd_to_mdy(business_day_backward(strtotime($trade_date_to_process),22));
			$dateto = format_date_ymd_to_mdy(previous_business_day());
}
?>
<?
tsp(100,"BCM Trends Analysis: ".$sel_symbol);
?>	
	<SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
  <SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
  <SCRIPT LANGUAGE="JavaScript">
  var calfrom = new CalendarPopup("divfrom");
  calfrom.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
  var calto = new CalendarPopup("divto");
  calto.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
  var c_calfrom = new CalendarPopup("cdivfrom");
  c_calfrom.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
  var c_calto = new CalendarPopup("cdivto");
  c_calto.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
  </SCRIPT>																                            

  <!-- START TABLE 4 -->
  <!-- class="tablewithdata" -->
  <style type="text/css">
<!--
.purp {
	background-color: #F5F1FA;
	border: 1px solid #9933CC;
}
-->
  </style>
  
  <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr>
      <td valign="top" width="275">

      <table class="purp"  width="274"><tr><td>
      <br />
      <center><font style="font-family:Arial; font-size:14px; font-weight:bold; color:#0000CC">SELECT CRITERIA</font></center>
      <br />
        <p>
          <label>
          	<? if ($checked_symbol == 1) { ?>
            <input type="radio" name="rd_seldiv" value="1" id="rd_seldiv_0" checked="checked" onClick="showDiv()" />
						<? } else { ?>
            <input type="radio" name="rd_seldiv" value="1" id="rd_seldiv_0" onClick="showDiv()" />
            <? } ?>					
            <font style="font-family:Arial; font-size:14px; font-weight:bold; color:#800000">&nbsp;&nbsp;Symbol [BCM] Trend Chart</font></label>
          <br />
          <br />
          <label>
					<? if ($checked_symbol != 1) { ?>
            <input type="radio" name="rd_seldiv" value="2" id="rd_seldiv_1" checked="checked" onClick="showDiv()"/>
						<? } else { ?>
            <input type="radio" name="rd_seldiv" value="2" id="rd_seldiv_1" onClick="showDiv()"/>
          <? } ?>					
            <font style="font-family:Arial; font-size:14px; font-weight:bold; color:#800000">&nbsp;&nbsp;CITTA</font></label>
          <br />
        </p>			
      <br />
			</td></tr></table>



        <? if ($checked_symbol == 1) { ?>
        <div id="div_symbol" style="visibility:visible; display:block">
				<? } else { ?>
        <div id="div_symbol" style="visibility:hidden; display:none">
				<? } ?>					
          <table width="252" cellpadding="0" cellspacing="0" border="0">
          <form name="f_symbol" id="f_symbol" action="bcm_trend_v3_container.php" method="post">
            <tr><td width="250">&nbsp;</td></tr>
            <tr><td align="right"><font style="font-family:Arial; font-size:14px; font-weight:bold; color:#0000CC">Enter Symbol & Dates</font></td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr>	
              <td valign="bottom" align="right" nowrap="nowrap">Symbol(s):&nbsp;&nbsp;&nbsp;<input type="text" width="30" maxlength="30" class="Text1" name="sel_symbol" value="<?=$sel_symbol?>"></td>
            </tr>
            <tr><td valign="top" align="right"><font style="font-family:Arial; font-size:11px; color:#0000CC">&nbsp;Can enter multiple values separated by commas.</font></td></tr>
            <tr>	
              <td valign="bottom" align="right">From:&nbsp;&nbsp;&nbsp;<input type="text" id="iddatefrom" class="Text1" name="datefrom" size="12" maxlength="12" value="<?=$datefrom?>">&nbsp;&nbsp;<A HREF="#" onClick="calfrom.select(document.forms['f_symbol'].datefrom,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
            </tr>
            <tr><td>&nbsp;</td></tr>
            <tr>	
              <td valign="bottom" align="right">To:&nbsp;&nbsp;&nbsp;<input type="text" id="iddateto" class="Text1" name="dateto" size="12" maxlength="12" value="<?=$dateto?>">&nbsp;&nbsp;<A HREF="#" onClick="calto.select(document.forms['f_symbol'].dateto,'anchor2','MM/dd/yyyy'); return false;" NAME="anchor2" ID="anchor2"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
            </tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td valign="bottom" align="right"><input type="image" src="images/lf_v1/form_submit.png"></td></tr>
    				<input type="hidden" name="proc_symbol" id="proc_symbol" value="S" />
          </form>															
          </table>
        </div>

        <? if ($checked_symbol == 1) { ?>
        <div id="div_citta" style="visibility:hidden; display:none">
				<? } else { ?>
        <div id="div_citta" style="visibility:visible; display:block">
				<? } ?>					
          <table width="252" cellpadding="0" cellspacing="0" border="0">
          <form name="f_citta" id="f_citta" action="bcm_trend_v3_container.php" method="post">
            <tr><td width="250">&nbsp;</td></tr>
            <tr><td align="right"><font style="font-family:Arial; font-size:14px; font-weight:bold; color:#0000CC">Select dates for CITTA</font></td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr>	
              <td valign="bottom" align="right">From:&nbsp;&nbsp;&nbsp;<input type="text" id="c_iddatefrom" class="Text1" name="c_datefrom" size="12" maxlength="12" value="<?=$datefrom?>">&nbsp;&nbsp;<A HREF="#" onClick="c_calfrom.select(document.forms['f_citta'].c_datefrom,'anchor3','MM/dd/yyyy'); return false;" NAME="anchor3" ID="anchor3"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
            </tr>
            <tr><td>&nbsp;</td></tr>
            <tr>	
              <td valign="bottom" align="right">To:&nbsp;&nbsp;&nbsp;<input type="text" id="c_iddateto" class="Text1" name="c_dateto" size="12" maxlength="12" value="<?=$dateto?>">&nbsp;&nbsp;<A HREF="#" onClick="c_calto.select(document.forms['f_citta'].c_dateto,'anchor4','MM/dd/yyyy'); return false;" NAME="anchor4" ID="anchor4"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
            </tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td valign="bottom" align="right">Full Sweep:&nbsp;&nbsp;&nbsp;<input type="checkbox" name="fsweep" id="fsweep" value="1" <?  if ($fsweep) { echo " checked='checked'"; } ?>/></td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td valign="bottom" align="right"><input type="image" src="images/lf_v1/form_submit.png"></td></tr>
    				<input type="hidden" name="proc_citta" id="proc_citta" value="C" />
          </form>															
          </table>
        </div>
        <br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
        <br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
      </td>
      <td width="1" bgcolor="#999999"></td>
      <td height="100%" valign="top">
        <?
        if ($proc_symbol) {
						if ($sel_symbol != "") {
							if (strpos($sel_symbol,",") > 0 ) {
								$arr_symbols = explode(",",$sel_symbol);
								foreach ($arr_symbols as $k=>$v) {
									if (trim(strtoupper($v)) != "") {
									?>
									<iframe frameborder="0" height="650" width="900" src="./bcm_trend/chart_img_v3.php?symbol=<?=strtoupper(trim($v))?>&date_start=<?=format_date_mdy_to_ymd($datefrom)?>&date_end=<?=format_date_mdy_to_ymd($dateto)?>" /></iframe>
									<?
									}
								}
							} else {
								?>
								<iframe frameborder="0" height="650" width="900" src="./bcm_trend/chart_img_v3.php?symbol=<?=strtoupper(trim($sel_symbol))?>&date_start=<?=format_date_mdy_to_ymd($datefrom)?>&date_end=<?=format_date_mdy_to_ymd($dateto)?>" /></iframe>
								<?
							}
						}
				} else if ($proc_citta) {

					include('bcm_trend_v3_citta.php');

				} else {
					echo "&nbsp;";
				}
        ?>
      </td>
    </tr>
  </table>
  <!-- END TABLE 4 -->
	<DIV ID="divfrom" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
	<DIV ID="divto" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>			
	<DIV ID="cdivfrom" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
	<DIV ID="cdivto" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>			
<?
tep();
?>				