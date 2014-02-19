<!-- EMP TRADES PART -->
<script language="javascript">
		var g_intControlCount = 1;
		function AddEmpTradesSection( )
		{
				 // Get our next highest control count
				 var intControlCount = g_intControlCount + 1;
				 // Build our new form options
				 var strOptions = "<hr size=\"1\" noshade color=\"#CCCCCC\" />" +
														"<table width=\"100%\" border=\"0\">" +
															"<tr>" +
																"<td>" +
																	"<select name=\"sel_emp[]\" size=\"1\">" +
																	"<option value=\"\">Select Emp.</option>" +
																	<?
																	$str_sql_select = "SELECT 
																												ID, Fullname
																											FROM users
																											WHERE user_isactive = 1
																											ORDER BY Fullname";
																	$result_select = mysql_query($str_sql_select) or die(tdw_mysql_error($str_sql_select));
																	$count_row_select = 0;
																	while ( $row_select = mysql_fetch_array($result_select) ) {
																	?>
																	"<option value=\"<?=$row_select['ID']?>\"><?=$row_select['Fullname']?></option>" +
																	<?
																	}
																	?>
																	"</select>" +
																	"&nbsp;&nbsp;" +							
																	"<input type=\"text\" size=\"12\" maxlength=\"20\" name=\"sel_symbol[]\" value=\"SYMBOL\" onBlur=\"clrAndUcase(this)\" onFocus=\"clrAndUcase(this)\"/>" +
																	"&nbsp;&nbsp;	" +
																	"<select name=\"sel_trd_approver[]\" size=\"1\">" +
																	"<option value=\"\">Trade Approver</option>" +
																	<?
																	$str_sql_select = "SELECT 
																												ID, Fullname
																											FROM users
																											WHERE is_trade_approver = 1
																											ORDER BY Fullname";
																	$result_select = mysql_query($str_sql_select) or die(tdw_mysql_error($str_sql_select));
																	$count_row_select = 0;
																	while ( $row_select = mysql_fetch_array($result_select) ) {
																	?>
																	"<option value=\"<?=$row_select['ID']?>\"><?=$row_select['Fullname']?></option>" +
																	<?
																	}
																	?>
																	"</select>" +
																"</td>" +
															"</tr>" +
															"<tr>" +
																"<td>" +
																	"<select name=\"sel_emptrd_type[]\" size=\"1\"><option value=\"NA\">Type</option><option value=\"NA\">-------------</option><option value=\"vs_clnt\">vs. Client</option><option value=\"vs_rlist\">vs. Restricted List</option></select>" +
																	"&nbsp;&nbsp; " +
																	"<select name=\"sel_client[]\" size=\"1\">" +
																		"<option value=\"\">Select Client</option>" +
																	<?
																	$query_sel_client = "SELECT comm_advisor_code, trim(comm_advisor_name) as comm_advisor_name 
																												FROM lkup_clients
																												WHERE comm_advisor_name != ''
																												ORDER BY comm_advisor_name, comm_advisor_code";
																	$result_sel_client = mysql_query($query_sel_client) or die(mysql_error());
																	?>																
																	<?
																	while($row_sel_client = mysql_fetch_array($result_sel_client))
																	{
																	?>
																	"<option value=\"<?=$row_sel_client['comm_advisor_code']?>\"><?=$row_sel_client['comm_advisor_name']?></option>" +
																	<?						
																	}
																	?>
																	"</select>" +
																"</td>" +
															"</tr>" +
														"</table>" +
														"<table>" +
															"<tr>" +
																"<td>" +
																	"<textarea wrap=\"physical\" name=\"addnote[]\" cols=\"92\" rows=\"2\" style='overflow:auto' onKeyUp=\"adjustRows(this)\" onFocus=\"adjustRows(this)\"></textarea><br />" +
																"</td>" +
															"</tr>" +
														"</table>";
				 // Add one to our total count
				 g_intControlCount++;
				 // Add to the form
				 document.getElementById( "idAddEmpTrades" ).innerHTML += strOptions;
		}     
	  //End Employee Trades Section
	
		//Potential Agency Cross Section
		function addPAC( )
		{
				 // Get our next highest control count
				 var intControlCount = g_intControlCount + 1;
				 // Build our new form options
				 var strOptions = "<hr size=\"1\" noshade color=\"#CCCCCC\" />" +
															"<table width=\"100%\" border=\"0\">" +
																"<tr>" +
																	"<td width=\"150\" align=\"left\" nowrap=\"nowrap\">" +
																		"	<input type=\"text\" size=\"12\" maxlength=\"20\" name=\"sel_symbol[]\" value=\"SYMBOL\" onBlur=\"clrAndUcase(this)\" onFocus=\"clrAndUcase(this)\"/>" +
																	"</td>" +
																	"<td><a class=\"ilt\" align=\"right\">Potential Agency Cross?</a><select name=\"sel_is_pac[]\"><option value=\"1\">Yes</option><option value=\"0\">No</option></select>" +
																	"<input type='hidden' name='rad_pac[]' value=''>" +
																	"</td>" +
																	"<td>&nbsp;</td>" +
																"</tr>" +
															"</table>" +
															"<table>" +
																"<tr>" +
																	"<td>" +
																		"<textarea wrap=\"physical\" name=\"addnote[]\" cols=\"92\" rows=\"2\" style='overflow:auto' onKeyUp=\"adjustRows(this)\" onFocus=\"adjustRows(this)\" onChange=\"adjustRows(this)\"></textarea><br />" +
																	"</td>" +
																"</tr>" +
															"</table>";
				 // Add one to our total count
				 g_intControlCount++;
				 // Add to the form
				 document.getElementById( "idPotentialAgencyCross" ).innerHTML += strOptions;
		}     
	
		//Potential Agency Cross Section
		function addMRI( )
		{
				 // Get our next highest control count
				 var intControlCount = g_intControlCount + 1;
				 // Build our new form options
				 var strOptions = "<hr size=\"1\" noshade color=\"#CCCCCC\" />" +
														"<table width=\"100%\" border=\"0\">" +
															"<tr>" +
																"<td width=\"115\" align=\"left\" nowrap=\"nowrap\">" +
																		"<input type=\"text\" size=\"12\" maxlength=\"20\" name=\"sel_symbol[]\" value=\"SYMBOL\" onBlur=\"clrAndUcase(this)\" onFocus=\"clrAndUcase(this)\"/>" +
																"</td>" +
																"<td width=\"5\">&nbsp;</td>" +
																"<td width=\"100\">" +
																	"<select name=\"sel_rating[]\" size=\"1\">" +
																		"<option value=\"\">Rating</option>" +
																		"<option value=\"nochange\">No Change</option>" +
																		"<option value=\"downgrade\">Downgrade</option>" +
																		"<option value=\"upgrade\">Upgrade</option>" +
																		"<option value=\"other\">Other</option>" +
																	"</select>" +
																"</td>" +
																"<td width=\"5\">&nbsp;</td>" +
																"<td width=\"100\">" +
																	"<select name=\"sel_target[]\" size=\"1\">" +
																		"<option value=\"\">Target</option>" +
																		"<option value=\"nochange\">No Change</option>" +
																		"<option value=\"decrease\">Decrease</option>" +
																		"<option value=\"increase\">Increase</option>" +
																		"<option value=\"other\">Other</option>" +
																	"</select>" +
																"</td>" +
																"<td width=\"5\">&nbsp;</td>" +
																"<td width=\"100\">" +
																	"<select name=\"sel_analyst[]\" size=\"1\">" +
																	"<option value=\"\">Analyst</option>" +
																	<?
																	$str_sql_select = "SELECT 
																												ID, Fullname
																											FROM users
																											WHERE user_isactive = 1
																											AND Role = 1
																											ORDER BY Fullname";
																	$result_select = mysql_query($str_sql_select) or die(tdw_mysql_error($str_sql_select));
																	$count_row_select = 0;
																	while ( $row_select = mysql_fetch_array($result_select) ) {
																	?>
																	"<option value=\"<?=$row_select['ID']?>\"><?=$row_select['Fullname']?></option>" +
																	<?
																	}
																	?>
																	"</select>" + 			
																"</td>" +
																"<td width=\"5\">&nbsp;</td>" +
																"<td width=\"100\">" +
																	"<select name=\"sel_pm[]\" size=\"1\">" +
																	"<option value=\"\">Portfolio Mgr.</option>" +
																	<?
																	$str_sql_select = "SELECT 
																												ID, Fullname
																											FROM users
																											WHERE user_isactive = 1
																											AND custom1 = 1
																											ORDER BY Fullname";
																	$result_select = mysql_query($str_sql_select) or die(tdw_mysql_error($str_sql_select));
																	$count_row_select = 0;
																	while ( $row_select = mysql_fetch_array($result_select) ) {
																	?>
																	"<option value=\"<?=$row_select['ID']?>\"><?=$row_select['Fullname']?></option>" +
																	<?
																	}
																	?>
																	"</select>" +
																"</td>" +
																"<td>&nbsp;</td>" +
															"</tr>" +
															"<tr>" +
																"<td colspan=\"10\">" +
																	"<select name=\"sel_t0[]\" size=\"1\"><option value=\"NA\">T-0</option><option value=\"NA\">-------------</option><option value=\"Buy\">Buy</option><option value=\"Sell\">Sell</option><option value=\"NA\">NA</option></select>&nbsp;&nbsp;" +
																	"<select name=\"sel_t1[]\" size=\"1\"><option value=\"NA\">T-1</option><option value=\"NA\">-------------</option><option value=\"Buy\">Buy</option><option value=\"Sell\">Sell</option><option value=\"NA\">NA</option></select>&nbsp;&nbsp;" +
																	"<select name=\"sel_t2[]\" size=\"1\"><option value=\"NA\">T-2</option><option value=\"NA\">-------------</option><option value=\"Buy\">Buy</option><option value=\"Sell\">Sell</option><option value=\"NA\">NA</option></select>&nbsp;&nbsp;" +
																	"<select name=\"sel_t3[]\" size=\"1\"><option value=\"NA\">T-3</option><option value=\"NA\">-------------</option><option value=\"Buy\">Buy</option><option value=\"Sell\">Sell</option><option value=\"NA\">NA</option></select>&nbsp;&nbsp;" +
																	"<select name=\"sel_t4[]\" size=\"1\"><option value=\"NA\">T-4</option><option value=\"NA\">-------------</option><option value=\"Buy\">Buy</option><option value=\"Sell\">Sell</option><option value=\"NA\">NA</option></select>&nbsp;" +
																	"<font class=\"ilt\">MRI Reqd. </font><input type=\"checkbox\" name=\"mri_reqd\" value=\"1\" checked=\"checked\" />" +
																"</td>" +
															"</tr>" +
														"</table>" +
														"<table>" +
															"<tr>" +
																"<td>" +
																	"<textarea wrap=\"physical\" name=\"addnote[]\" cols=\"92\" rows=\"2\" style='overflow:auto' onKeyUp=\"adjustRows(this)\" onFocus=\"adjustRows(this)\"></textarea><br />" +
																"</td>" +
															"</tr>" +
														"</table>";
				 // Add one to our total count
				 g_intControlCount++;
				 // Add to the form
				 document.getElementById( "idAddMRI" ).innerHTML += strOptions;
		}     
	</script>
