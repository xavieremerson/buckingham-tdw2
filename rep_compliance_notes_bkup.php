<?
   include 'includes/global.php';
   include 'includes/dbconnect.php';
   include 'includes/functions.php';
?>
<script type="text/javascript" src="includes/javascript/calendar/calendar.js"></script>
<script type="text/javascript" src="includes/javascript/calendar/lang/calendar-en.js"></script>
<!-- helper script that uses the calendar -->
<script type="text/javascript" src="includes/javascript/calendar/helper.js"></script>
<link rel="alternate stylesheet" type="text/css" media="all" href="includes/javascript/calendar/calendar-win2k-2.css" title="win2k-2" />
<script type="text/javascript">setActiveStyleSheet(document.getElementById("defaultTheme"), "win2k-2");</script>

<SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
<SCRIPT LANGUAGE="JavaScript">
<!--
function showhide_pac() { 
	if (document.getElementById) { // DOM3 = IE5, NS6 Generally this is what it is

		if (document.getElementById("chk_pac").checked == true) {                               // document.getElementById("div_pac").style.getAttribute("visibility") == "" || document.getElementById("div_pac").style.getAttribute("visibility") == "hidden" ) {
		document.getElementById("div_pac").style.visibility = 'visible'; 
		document.getElementById("div_pac").style.display = 'block';  //block
		} else {
		document.getElementById("div_pac").style.visibility = 'hidden'; 
		document.getElementById("div_pac").style.display = 'none'; 
		}		
	} 
} 
-->
</SCRIPT>

<Script Language=JavaScript>
//Allow for auto expandable textarea
function cursorEOT(isField){
	isRange = isField.createTextRange();
	isRange.move('textedit');
	isRange.select();
	testOverflow = isField.scrollTop;
	if (testOverflow != 0){return true}
	else {return false}
}

function adjustRows(isField){
	while (cursorEOT(isField)){isField.rows++}
}

function clrAndUcase (tbox) {
	if (tbox.value == "SYMBOL") {
		tbox.value = "";
	} else {
		tbox.value = tbox.value.toUpperCase();
	}
}
</Script>
<?
//print_r($_POST);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Add/View Notes</title>
<link rel="stylesheet" type="text/css" href="includes/styles.css" />
<style type="text/css">
<!--
#scrollElement {
	width: 590px;
	height: 370px;
	padding: 1px;
	border: 1px solid #cc0000;
	overflow: scroll; 
}
-->
</style>
<!-- EMP TRADES PART -->
<script language="javascript">
		var g_intControlCount = 1;
		function AddEmpTradesSection( )
		{
				 // Get our next highest control count
				 var intControlCount = g_intControlCount + 1;
				 // Build our new form options
				 var strOptions = "<table>" +
				 										"<tr>" +
															"<td width=\"100\">" +
																	"<select name=\"sel_emp[]\" size=\"1\">" +
																	"<option value=\"_ALL_\">Select Emp.</option>" +
																	<?
																	$str_sql_select = "SELECT 
																												distinct(TRIM(trad_account_name)) as trad_account_name
																											FROM emp_employee_trades
																											WHERE trad_is_cancelled = 0
																											AND TRIM(trad_account_name) != ''
																											AND TRIM(trad_account_name) not like '&%'
																											ORDER BY trad_account_name";
																	$result_select = mysql_query($str_sql_select) or die(tdw_mysql_error($str_sql_select));
																	$count_row_select = 0;
																	while ( $row_select = mysql_fetch_array($result_select) ) {
																	?>
																	"<option value=\"<?=$row_select['trad_account_name']?>\"><?=$row_select['trad_account_name']?></option>" +
																	<?
																	}
																	?>
																	"</select>" +
															"</td><td width='5'>" +
															"<td width=\"100\" align=\"left\">" +
																	"<input type='text' size='12' maxlength='20' name='sel_symbol[]' value='SYMBOL' onblur=\"clrAndUcase(this)\" onfocus=\"clrAndUcase(this)\" />" +
															"</td>" +
															"<td>&nbsp;</td>" + 
														"</tr>" +
														"</table>" +
														"<table>" +
														"<tr>" +
															"<td>" +
																"<textarea wrap=\"physical\" name=\"addnote[]\" rows='2' cols='92' style='overflow:auto' onkeyup=\"adjustRows(this)\" onfocus=\"adjustRows(this)\"></textarea><br />" +
															"</td>" +
														"</tr>" +
													"</table>";
				 // Add one to our total count
				 g_intControlCount++;
				 // Add to the form
				 document.getElementById( "idAddEmpTrades" ).innerHTML += strOptions;
		}     
	</script>
</head>

<body leftmargin="3" topmargin="3" rightmargin="3" bottommargin="3" onUnload="window.opener.location.reload();self.close();return false;"> <!-- onunload="window.opener.location.reload();self.close();return false;" -->

		 <?	   
	   tsp(100, "Notes")
		 ?>
		 <input id="chk_pac" type="checkbox" value="1" onclick="showhide_pac()" />&nbsp;&nbsp;<a class="ilt" align="right">Potential Agency Cross</a>
     <div id="div_pac" style="visibility:hidden" style="display:none">
			<form action="<?=$_SERVER['REQUEST_URI']?>" method="post" name="potential_ac" id="idpotential_ac">
			<hr size="1" noshade color="#CCCCCC" />
			<table width="100%" border="0">
				<tr>
					<td width="200" nowrap="nowrap">
					<SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
						<SCRIPT LANGUAGE="JavaScript">
						var caldate = new CalendarPopup("divdate");
						caldate.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
					</SCRIPT>
					&nbsp;&nbsp;&nbsp;<a class="ilt" align="right">Date</a><input type="text" id="iddate" class="Text1" name="valdate" size="14" maxlength="12" value="">
					<A HREF="#" onClick="caldate.select(document.forms['potential_ac'].valdate,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
					</td>
					<td width="5">&nbsp;</td>
					<td width="200" align="left" nowrap="nowrap">
							<input type="text" size="12" maxlength="20" name="sel_symbol[]" value="SYMBOL" onBlur="clrAndUcase(this)" onFocus="clrAndUcase(this)"/>
					</td>
					<td>
						<table width="100">
						  <tr>
						    <td><label>
						      <input type="radio" name="rad_potentialagencycross" value="1" />
						      Yes</label></td>
					    <td><label>
						      <input type="radio" name="rad_potentialagencycross" value="0" />
						      No</label></td></tr>
					  </table>					
					</td>
					<td>&nbsp;</td>
				</tr>
			</table>
			<table>
				<tr>
					<td>
						<textarea wrap="physical" name="addnote[]" cols="92" rows="2" style='overflow:auto' onKeyUp="adjustRows(this)" onFocus="adjustRows(this)" onchange="adjustRows(this)"></textarea><br />
					</td>
				</tr>
			</table>
			<table>
				<tr valign="top"> 
					<td><div id="idAddEmpTrades"></div></td>
				</tr>
			</table>
			<table>
				<tr valign="top"> 
					<td><a href="javascript:AddEmpTradesSection()" class="email"><img src="images/btn_add_more.png" /></a>&nbsp;&nbsp;&nbsp;<input type="image" src="images/btn_save.png" /></td>
				</tr>
			</table> 
			</form>
		 </div>


		 <form action="<?=$_SERVER['REQUEST_URI']?>" method="post" name="mainnote" id="mainnote">
			<a class="ilt" align="right">Employee Trades:</a>
			<table width="100%" border="0">
				<tr>
					<td width="100">
							<select name="sel_emp[]" size="1">
							<option value="_ALL_">Select Emp.</option>
							<?
							$str_sql_select = "SELECT 
																		distinct(TRIM(trad_account_name)) as trad_account_name
																	FROM emp_employee_trades
																	WHERE trad_is_cancelled = 0
																	AND TRIM(trad_account_name) != ''
																	AND TRIM(trad_account_name) not like '&%'
																	ORDER BY trad_account_name";
							$result_select = mysql_query($str_sql_select) or die(tdw_mysql_error($str_sql_select));
							$count_row_select = 0;
							while ( $row_select = mysql_fetch_array($result_select) ) {
							?>
							<option value="<?=$row_select['trad_account_name']?>"><?=$row_select['trad_account_name']?></option>
							<?
							}
							?>
							</select>			
					</td>
					<td width="5">&nbsp;</td>
					<td width="100" align="left">
							<input type="text" size="12" maxlength="20" name="sel_symbol[]" value="SYMBOL" onBlur="clrAndUcase(this)" onFocus="clrAndUcase(this)"/>
					</td>
					<td>&nbsp;</td>
				</tr>
			</table>
			<table>
				<tr>
					<td>
						<textarea wrap="physical" name="addnote[]" cols="92" rows="2" style='overflow:auto' onKeyUp="adjustRows(this)" onFocus="adjustRows(this)"></textarea><br />
					</td>
				</tr>
			</table>
			<table>
				<tr valign="top"> 
					<td><div id="idAddEmpTrades"></div></td>
				</tr>
			</table>
			<table>
				<tr valign="top"> 
					<td><a href="javascript:AddEmpTradesSection()" class="email"><img src="images/btn_add_more.png" /></a>&nbsp;&nbsp;&nbsp;<input type="image" src="images/btn_save.png" /></td>
				</tr>
			</table> 
			</form>
		 <?	
	   tep();
		 
?>		 
	<DIV ID="divdate" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>		 
</body>
</html>