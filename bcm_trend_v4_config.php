<?php
//BRG
include('inc_header.php');
  
?>
<script language="JavaScript" src="includes/prototype/prototype.js"></script>
<script language ="Javascript">
<!--
function monthly_report() {

	var sel_month = $("sel_month").value;
	
	if (sel_month == "") {
	  alert("Please select Month/Year.");
		return false;
	}

	var progressbar;
	progressbar = 'Processing. Please do not close this window.<br><br><img src="images/loading-bar.gif" border="0">';
	document.getElementById('notify').innerHTML=progressbar; 
	

	var url = 'http://192.168.20.63/tdw/bcm_trend_v4_privol_ajx.php';
	var pars = 'user_id=<?=$user_id?>';
  pars = pars + '&sel_month='+ sel_month;
  var ran_number= Math.random()*5; 
	pars = pars + '&xrand=' + ran_number;

  new Ajax.Request
    (
      url,   
      {     
        method:'get', 
        parameters:pars,    
        onSuccess: 
          function(transport){       
            var response = "";
            response = transport.responseText;       
						$("test_results").innerHTML = response;
						document.getElementById('notify').innerHTML=""; 
          },     
        onFailure: 
        	function(){ 
						$("notify").innerHTML = "Communication Error! Please report with Code TDW-BCM3";
					}
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


<!-- START TABLE 1 -->
<style type="text/css">
<!--
.val_input {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 14px;
	font-weight: bold;
	color: #003399;
	background-color: #FFFFCC;
	margin: 2px;
	padding: 2px;
	border: thin solid #666666;
	height: 28px;
}
-->
</style>

<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
<!--<tr> 
<td>Main Page<BR>User logged in: <B><? echo $user; ?></B></td>
</tr>-->
<tr valign="top"> 
<td valign="top" nowrap width="100%">
<!-- =================================================================================================================== -->
<!-- CONTENT -->
<?
tsp(100, "CONFIG: BCM PRICE/VOLUME TREND");
?>
<?
if ($_POST) {
	//set 
	$result1 = mysql_query("update bcm_trend_config set bct_isactive = 0 where auto_id = '".$price_id."'") or die(mysql_error());
	$result2 = mysql_query("update bcm_trend_config set bct_isactive = 0 where auto_id = '".$volume_id."'") or die(mysql_error());
	$result3 = mysql_query("INSERT INTO bcm_trend_config VALUES (NULL , 'price', '".$price."', '".$user_id."', NOW( ) , '1')");
	$result4 = mysql_query("INSERT INTO bcm_trend_config VALUES (NULL , 'volume', '".$volume."', '".$user_id."', NOW( ) , '1')");
	
	$val_success = "Configuration values saved successfully.";
}

$qry = "select * from bcm_trend_config where bct_isactive = 1";
$result = mysql_query($qry) or die(tdw_mysql_error($qry));
while ($row = mysql_fetch_array($result)) {
	if ($row["bct_type"] == "price") {
		$val_price = $row["bct_value"];
		$val_price_id = $row["auto_id"];						
	} else {
		$val_volume = $row["bct_value"];
		$val_volume_id = $row["auto_id"];						
	}
}

if ($datefrom) {
	$sel_datefrom = $datefrom;
	$sel_dateto = $dateto;
} else {
	$sel_datefrom = date('m/d/Y', strtotime(date('Y-m-d')) - 432000);
	$sel_dateto = date('m/d/Y');
}


?>

<table width="100%" border="0">
	<tr>
  <td width="400" valign="top">
  <table border="0">
  	<tr>
    	<td width="10">&nbsp;</td>
    	<td><a class="ilt">Change Price/Volume Parameters</a><br />
        <font face="Arial" style="font-size:10px; color:#ff0000">Please note, these values are used in daily email alerts<br />and monthly reporting.</font>
  			<font face="Verdana" style="font-size:12px" color="green"><strong><?=$val_success?></strong></font><br />
        <form action="<?=$PHP_SELF?>" method="post" enctype="multipart/form-data" name="frm_select_users">
        <table width="100%">
        <tr> 
          <td><a class="ilt">Price Change</a></td>			
          <td><input class="val_input" type="text" style="width:110px" name="price" value="<?=$val_price?>" />% </td>			
          <td><input type="hidden" name="price_id" value="<?=$val_price_id?>" />&nbsp;</td>			
        </tr>
<!--        <tr><td colspan="3">&nbsp;</td></tr>
-->        <tr> 
          <td><a class="ilt">% of Total Daily Volume</a></td>			
          <td><input class="val_input" type="text" style="width:110px" name="volume" value="<?=$val_volume?>" />% </td>			
          <td><input type="hidden" name="volume_id" value="<?=$val_volume_id?>" />&nbsp;</td>			
        </tr>
<!--        <tr><td colspan="3">&nbsp;</td></tr>
-->        <tr>
        <td align="left" class="csys_regtext">&nbsp;</td>
        <td align="left"><input class="Submit" style="width:110px" name="Save" type="submit" value="Save"></td>
        <td>&nbsp;</td>
        </tr>
        </table>
        </form>
      </td>
    </tr>
    <tr>
    	<td width="10">&nbsp;</td>
    	<td><hr size="2" noshade color="#006699" /><a class="ilt">Use Price/Volume Criteria</a><br />
      
        <form action="#" method="post" enctype="multipart/form-data" name="frm_test" id="frm_test">
        <table width="100%">
        <tr> 
          <td><a class="ilt">Symbol</a></td>			
          <td><input class="val_input" type="text" style="width:150px" name="symbol" id="symbol" value="" /></td>			
          <td>&nbsp;</td>			
        </tr>
					<SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
          <SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
          <SCRIPT LANGUAGE="JavaScript">
          var calfrom = new CalendarPopup("divfrom");
          calfrom.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
          var calto = new CalendarPopup("divto");
          calto.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
         
				 
/*
*/				 
				 
				  var calmon = new CalendarPopup("divmon");
					calmon.setDisplayType("month");
					calmon.setReturnMonthFunction("monthReturn");
					calmon.showYearNavigation();
					function monthReturn(y,m) {
						document.forms['frm_pvol'].sel_month.value=m+"-"+y;          						
          }
					</SCRIPT>
        <tr> 
          <td><a class="ilt">Date From</a></td>
          <td><input type="text" id="iddatefrom" class="val_input" name="datefrom" size="12" maxlength="12" value="<?=$sel_datefrom?>"><A HREF="#" onClick="calfrom.select(document.forms['frm_test'].datefrom,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A></td>			
          <td>&nbsp;</td>			
        </tr>
        <tr> 
          <td><a class="ilt">Date To</a></td>
          <td><input type="text" id="iddateto" class="val_input" name="dateto" size="12" maxlength="12" value="<?=$sel_dateto?>"><A HREF="#" onClick="calto.select(document.forms['frm_test'].dateto,'anchor2','MM/dd/yyyy'); return false;" NAME="anchor2" ID="anchor2"><img src="images/lf_v1/sel_date.png" border="0"></A></td>			
          <td>&nbsp;</td>			
        </tr>
        <tr><td colspan="3"><font face="Arial" style="font-size:11px; color:#ff0000">*** Please enter shorter timeframes for faster execution</font></td></tr>
        <tr>
        <td align="left" class="csys_regtext">&nbsp;</td>
        <td align="left"><input class="Submit" style="width:150px" name="Save" type="button" value=" SUBMIT " onclick="run_test(); return false;"></td>
        <td>&nbsp;</td>
        </tr>
        </table>
        </form>
      </td>
    </tr>
    <tr>
    	<td width="10">&nbsp;</td>
    	<td><hr size="2" noshade color="#006699" /><a class="ilt">Monthly Price/Volume Report</a><br />
      
        <form action="#" method="post" enctype="multipart/form-data" name="frm_pvol" id="frm_pvol">
        <table width="100%">
        <tr> 
          <td><a class="ilt">Select Month</a></td>
          <td><input type="text" class="val_input" id="sel_month" name="sel_month" size="12" maxlength="12" value="<?=date('n-Y')?>">
          <A HREF="#" onClick="calmon.showCalendar('anchor3'); return false;" NAME="anchor3" ID="anchor3"><img src="images/lf_v1/sel_date.png" border="0"></A></td>			
          <td>&nbsp;</td>			
        </tr>
        <tr>
        <td align="left" class="csys_regtext">&nbsp;</td>
        <td align="left"><input class="Submit" style="width:150px" name="Save" type="button" value=" SUBMIT "  onclick="monthly_report(); return false;"></td>
        <td>&nbsp;</td>
        </tr>
        <tr><td colspan="3"><div id="notify" class="ilt"></div></td></tr>
        </table>
        </form>
      </td>
    </tr>
    <tr>
    	<td width="10">&nbsp;</td>
    	<td><hr size="2" noshade color="#006699" />
    </td>
  </tr>
  </table>
  </td>

  <td valign="top">
  <div id="test_results"></div>
  </td>
  </tr>
</table>


<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
  <div id="err_data"></div>

<?
tep();
?>
	<DIV ID="divfrom" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
	<DIV ID="divto" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>			
	<DIV ID="divmon" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>			
<!-- =================================================================================================================== -->
<!-- END TABLE 2 -->		
</td>
</tr>
</table>
<script language="javascript">
	function run_test() {

		if ($("symbol").value == "") {
			alert("Please enter a symbol.");
			return false;
		}
		
		var url = 'http://192.168.20.63/tdw/bcm_trend_v4_privol_apply_ajx.php';
		var pars = 'mod_request=runtest';
		pars = pars + "&" + $("frm_test").serialize();
		pars = pars + '&xrand='+ Math.random();
		//alert(pars);
		//return false;
		//$("err_data").innerHTML = "bcm_trend_v4_privol_apply_ajx.php?"+pars;
		//return false;  

		new Ajax.Request
		(
			url,   
			{     
				method:'get', 
				parameters:pars,    
				onSuccess: 
					function(transport){       
						var response = transport.responseText;  
							//alert(response);
							$("test_results").innerHTML = response;
					},     
				onFailure: 
				function(){ $("test_results").innerHTML = "Error accessing TDW Server [CODE:BCMT-1129]"; }
			}
		);
	}
</script>
<!-- END TABLE 1 -->
<?php
include('inc_footer.php');
?>

