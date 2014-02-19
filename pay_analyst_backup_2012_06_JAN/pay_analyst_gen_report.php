<script language="JavaScript" src="includes/js/ajax_tbx.js"></script>
<script language ="Javascript">
<!--
function procx() {
	var valqtr = document.getElementById('sel_qtr').options[document.getElementById('sel_qtr').selectedIndex].value;
	var valyear = document.getElementById('sel_year').options[document.getElementById('sel_year').selectedIndex].value;
	
	if (valqtr == "" || valyear == "") {
	  alert("Please select Quarter and Year");
		return false;
	}
	
	var progressbar;
	progressbar = 'Report will be ready in 5-10 seconds.<br><img src="images/loading-bar.gif" border="0">';
	document.getElementById('notify').innerHTML=progressbar; 
	
	//alert(valqtr + " " + valyear);

	AjaxRequest.get(
			{
				'url':'pay_analyst_report_excel_create.php?sel_qtr='+ valqtr + '&sel_year=' + valyear
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
tsp(100, "Generate Analyst Allocations Report");
?>
<style type="text/css">
<!--
.notify_x {
	font-family: verdana;
	font-size: 12px;
	font-weight: bold;
	color: #009900;
	text-decoration: none;
	background-color: #E6FFE6;
	border-top-width: 1px;
	border-bottom-width: 1px;
	border-top-style: solid;
	border-right-style: none;
	border-bottom-style: solid;
	border-left-style: none;
	border-top-color: #00CC33;
	border-bottom-color: #00CC33;
}
-->
</style>
<br />
<?
if ($sel_qtr != "" AND $sel_year != "") {
?>
<table width="100%" border="0" cellpadding="4" cellspacing="0"> 
	<tr>
		<td>&nbsp;&nbsp;&nbsp;&nbsp;
		<!-- Top Menu -->		
		<select name="sel_qtr" id="sel_qtr">
			<option value="">Select Quarter</option>
			<option value="1" <? if ($sel_qtr == 1) { echo "selected";}?>>Qtr. 1</option>
			<option value="2" <? if ($sel_qtr == 2) { echo "selected";}?>>Qtr. 2</option>
			<option value="3" <? if ($sel_qtr == 3) { echo "selected";}?>>Qtr. 3</option>
			<option value="4" <? if ($sel_qtr == 4) { echo "selected";}?>>Qtr. 4</option>
		</select>		
		&nbsp;&nbsp;&nbsp;
		<select name="sel_year" id="sel_year">
			<option value="">Select Year</option>
<!--			<option value="2007" <? if ($sel_year == 2007) { echo "selected";}?>>2007</option>
-->			<option value="2008" <? if ($sel_year == 2008) { echo "selected";}?>>2008</option>
			<option value="2009" <? if ($sel_year == 2009) { echo "selected";}?>>2009</option>
			<option value="2010" <? if ($sel_year == 2010) { echo "selected";}?>>2010</option>
			<option value="2011" <? if ($sel_year == 2011) { echo "selected";}?>>2011</option>
		</select>		
		&nbsp;&nbsp;&nbsp;
		<input type="image" src="images/lf_v1/form_submit.png"/>
		&nbsp;&nbsp;&nbsp;
		<?
} else {
?>
		&nbsp;&nbsp;&nbsp;&nbsp;<select name="sel_qtr" id="sel_qtr">
			<option value="">Select Quarter</option>
			<option value="1" <? if ($sel_qtr == 1) { echo "selected";}?>>Qtr. 1</option>
			<option value="2" <? if ($sel_qtr == 2) { echo "selected";}?>>Qtr. 2</option>
			<option value="3" <? if ($sel_qtr == 3) { echo "selected";}?>>Qtr. 3</option>
			<option value="4" <? if ($sel_qtr == 4) { echo "selected";}?>>Qtr. 4</option>
		</select>		
		&nbsp;&nbsp;&nbsp;
		<select name="sel_year" id="sel_year">
			<option value="">Select Year</option>
<!--			<option value="2007" <? if ($sel_year == 2007) { echo "selected";}?>>2007</option>
-->			<option value="2008" <? if ($sel_year == 2008) { echo "selected";}?>>2008</option>
			<option value="2009" <? if ($sel_year == 2009) { echo "selected";}?>>2009</option>
			<option value="2010" <? if ($sel_year == 2010) { echo "selected";}?>>2010</option>
			<option value="2011" <? if ($sel_year == 2011) { echo "selected";}?>>2011</option>
		</select>		
		&nbsp;&nbsp;&nbsp;
		<input type="image" src="images/lf_v1/form_submit.png" onclick="procx()"/>
		&nbsp;&nbsp;&nbsp;
<?
	echo "<br><br>&nbsp;&nbsp;&nbsp;&nbsp;Please select Quarter and Year.<br /><br />";
}
?>
		<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div id="notify" class="ilt"></div>
		<!-- End Top Menu -->		
		</td>
	</tr>
</table>
<?
tep();
?>