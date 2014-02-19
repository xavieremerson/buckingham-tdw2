<?
include('inc_header.php');
?>

<script type="text/javascript" language="javascript" src="includes/prototype/prototype.js"></script>
<script type="text/javascript">

function divsh () {
	if ( $("sh").innerHTML == 'Show Data Entry Section') {  
			$("divde").style.display = 'block';
			$("divde").style.visibility = 'visible';
			$("sh").innerHTML = 'Hide Data Entry Section';
	} else {
			$("divde").style.display = 'none';
			$("divde").style.visibility = 'hidden';
			$("sh").innerHTML = 'Show Data Entry Section';
	}
}

function export_excel() {
// Change "_blank" to something like "newWindow" to load all links in the same new window

	var url = 'http://192.168.20.63/tdw/mod_exp_mod_processor_excel.php';
	var pars = 'user_id=<?=$user_id?>';
	var mytime= '&ms='+new Date().getTime();
	pars = pars + mytime;
  var ran_number= Math.random()*5; 
	pars = pars + '&xrand=' + ran_number;
	pars = pars + '&sel_month='+ $("sel_month").value;
/*	pars = pars + '&datefrom='+ $("iddatefrom").value;
	pars = pars + '&dateto='+ $("iddateto").value;
*/	
  var newurl = url + '?' + pars;
  //alert(newurl);
	//return false;

	var newWindow = window.open(newurl, '_blank');
	newWindow.focus();
	return false;
}


function get_expenses_for_month() {
// Change "_blank" to something like "newWindow" to load all links in the same new window

	var url = 'mod_exp_mod_processor_proc.php';
	var pars = 'user_id=<?=$user_id?>';
	var mytime= '&ms='+new Date().getTime();
	pars = pars + mytime;
  var ran_number= Math.random()*5; 
	pars = pars + '&xrand=' + ran_number;
	pars = pars + '&sel_month='+ $("sel_month").value;

  var newurl = url + '?' + pars;
	//alert(newurl);
	$("if_status").src= newurl;
	return false;
}


</script>
	
<!-- START TABLE 1 -->
<style type="text/css">
<!--
.gilt {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
	color: #006600;
	text-align: left;
	background-color: #99FF99;
	border: 1px solid #006600;
	padding-top: 3px;
	padding-right: 6px;
	padding-bottom: 3px;
	padding-left: 6px;
	text-decoration: none;
}
.gilt:hover {
	text-decoration: underline;
}
.oilt {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
	color: #FF6600;
	text-align: left;
	background-color: #FFF0E1;
	border: 1px solid #FFCC00;
	padding-top: 3px;
	padding-right: 6px;
	padding-bottom: 3px;
	padding-left: 6px;
	text-decoration: none;
}
.oilt:hover {
	text-decoration: underline;
}
.htr {	font-family: Arial, Helvetica, sans-serif;	font-size: 12px;	font-weight: bold;	color: #003399;	background-color: #eeeeee;}
.rilt {	font-family: Arial, Helvetica, sans-serif;	font-size: 11px;	font-weight: bold;	color: #003399;	text-align: right; }
.lilt {	font-family: Arial, Helvetica, sans-serif;	font-size: 11px;	font-weight: bold;	color: #003399;	text-align: left; }
.thisdoclink {	font-family: Arial, Helvetica, sans-serif;	font-size: 11px;	font-weight: bold;	color: #FF6600;	text-align: left;	text-decoration: underline;}

-->
</style>

<table width="100%" height="100%" border="0" cellspacing="1" cellpadding="0">
  <tr> 
    <td valign="top">
<? 
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%    
tsp(100, "Expense Approval");
?>

<table width="100%" cellpadding="0" cellspacing="0">
  <form name="exp_criteria" id="exp_criteria" action="" method="post">
    <tr>
      <td width="110" align="center" nowrap="nowrap" class="rilt">&nbsp;Expense Period:&nbsp;</td>
			<td width="5">&nbsp;</td>
      <td width="100">																
      <select class="Text1" name="sel_month" id="sel_month" size="1" >
      <option value="<?=date("Y-m-01")?>"><?=date("F Y")?></option>
      <?
      $now=strtotime(date("d F Y"));
			for ($i=0;$i<12;$i++) {
				$now=$now-(60*60*24*(365/12));
				echo "<option value=\"".date("Y-m-01", $now)."\">" . date('F Y', $now) . "</option>\n";
			}
			?>
      <?
      //echo create_commission_month();
      ?>
      </select>
      </td>
		  <td width="5">&nbsp;</td>
			<SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
      <SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
      <SCRIPT LANGUAGE="JavaScript">
				var calexpdate_f = new CalendarPopup("divexpdate_f");
				calexpdate_f.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
				var calexpdate_t = new CalendarPopup("divexpdate_t");
				calexpdate_t.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
      </SCRIPT>						
      <td width="10"><img src="images/lf_v1/form_submit.png" onclick="get_expenses_for_month();" style="cursor:pointer" /></td>
      <td width="10">&nbsp;</td>
      <td width="10" class="rilt">OR&nbsp;&nbsp;</td>
      <td width="10"><img src="images/lf_v1/exp2excel.png" onclick="export_excel();" style="cursor:pointer"/></td>
      <td>&nbsp;</td>
		</tr>
  </form>
</table>
<?
tep();
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%    
?>
<!--<hr size="2" noshade color="#003366" />
<hr size="1" noshade color="#f4f8fb" />-->
<table width="100%"><!-- style="visibility:hidden; display=none"-->
<tr>
	<td> 
	<iframe name="if_status" id="if_status" src="mod_exp_mod_processor_proc.php?user_id=<?=$user_id?>" height="800" width="100%" marginwidth="0" marginheight="0" scrolling="yes" frameborder="0"></iframe>
	</td>
</tr>
</table>
<?php
include('inc_footer.php'); 
?>
<DIV ID="divexpdate_f" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
<DIV ID="divexpdate_t" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>