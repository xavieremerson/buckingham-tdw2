<title>Edit Client</title>
<script language="Javascript" SRC="../includes/javascript.js"></script>
<script language="JavaScript" src="includes/js/popup.js"></script>
<link rel="stylesheet" type="text/css" href="includes/styles.css" />
<script language="JavaScript" type="text/javascript">
function showhidepayout(divid) { 
	if (document.getElementById) { // DOM3 = IE5, NS6 Generally this is what it is

		if (document.getElementById(divid).style.getAttribute("visibility") == "" || document.getElementById(divid).style.getAttribute("visibility") == "hidden" ) {
		document.getElementById(divid).style.visibility = 'visible'; 
		document.getElementById(divid).style.display = 'block'; 
		} else {
		document.getElementById(divid).style.visibility = 'hidden'; 
		document.getElementById(divid).style.display = 'none'; 
		}		

	} 
	else { 
			alert("Browser Version not compatible!");
	} 
} 
</script>

<? 
include('includes/dbconnect.php');
include('includes/global.php');
include('includes/functions.php');
?>

			<script type="text/javascript" src="includes/javascript/calendar/calendar.js"></script>
			<script type="text/javascript" src="includes/javascript/calendar/lang/calendar-en.js"></script>
			<!-- helper script that uses the calendar -->
			<script type="text/javascript" src="includes/javascript/calendar/helper.js"></script>
			<link rel="alternate stylesheet" type="text/css" media="all" href="includes/javascript/calendar/calendar-win2k-2.css" title="win2k-2" />
			<script type="text/javascript">setActiveStyleSheet(document.getElementById("defaultTheme"), "win2k-2");</script>
			
<?
			tsp(100, "Edit News / Event");
			//echo "<br>";
			
			//$ID = 8;
			
			//START OF IF 1
			if($editnews)
			{
					//Client Name AND Client Code ERROR CHECKING
				$array    = array();
				$test_name = array();
				$test_name[1] = "Date cannot be blank.";
				$test_name[2] = "You have not added any Note.";
				$test_name[3] = "Symbol cannot be blank";

				if($newsdate == "") 
				{
					$array[1] = "0";
					$newsdate_blank = "0";
				}  
				else 
				{
					$array[1] = "1";
					$newsdate_blank = "1";
				}
				if($details == "") 
				{
					$array[1] = "0";
					$details_blank = "0";
				}  
				else 
				{
					$array[1] = "1";
					$details_blank = "1";
				}
				if($symbol == "") 
				{
					$array[3] = "0";
					$symbol_blank = "0";
				}  
				else 
				{
					$array[3] = "1";
					$symbol_blank = "1";
				}

					$create_err_msg = "There are one or more invalid or incomplete fields. Please resolve this problem and re-submit the data.";
					$show_err = 0;
						for($x = 1; $x <= count($array); $x++)
						{
							if($array[$x] == "0") 
							{
								$create_err_msg = $create_err_msg . "<br>" . $test_name[$x];
								$show_err = 1;
							} 
						}
					
					if ($show_err == 1) {
					showmsg(3, $create_err_msg);
					}
			
			// NO ERRORS FOUND, HENCE INSERT DATA IN TABLE
			else
			{
			
				//xdebug("clnt_default_payout",$clnt_default_payout);
				//xdebug("clnt_special_payout_rate",$clnt_special_payout_rate);
				
				$val_date = date('Y-m-d', strtotime($newsdate));
			
				$query_edit = "UPDATE news_events 
											SET news_notes='".str_replace("'","\\'",$details)."',
											    news_date='".$val_date."',
													news_event='".$newstype."',
													news_symbol='".strtoupper($symbol)."'
											WHERE auto_id='$ID'";
				//xdebug("query_edit",$query_edit);
				$result_edit = mysql_query($query_edit) or die (tdw_mysql_error($query_edit));
								
				//<!-- showmsg success -->
        showmsg(1, "Item for Symbol [".strtoupper($symbol)."] updated successfully.");
	    	} // END OF INSERTING DATA IN TABLE
		} // END OF IF 1


    //show_array($_POST);
		$qry_news = "SELECT * from news_events WHERE auto_id = '".$ID."'";

		$result_news = mysql_query($qry_news) or die (tdw_mysql_error($qry_news));
		while ( $row_news = mysql_fetch_array($result_news) ) 
		{
			//show_array($row_client);
			$newsdate = $row_news["news_date"]; 
			$symbol = $row_news["news_symbol"];
			$newstype = $row_news["news_event"];
			$details = $row_news["news_notes"];
		}
		
?>


		<!-- 'CREATE' FIELDS TABLE -->
		<table cellpadding="2" cellspacing="0" border="0" height="100%" width="70%">  
			<form action="<?=$php_self?>" method="post"> 
			<tr> 
				<td>  
					<table border="0" width="520">
						<tr valign="top">
							<td class="ilt" width="110">&nbsp;</td>
							<td class="ilt" width="410">&nbsp;</td>
						</tr>
						<tr valign="top">
							<td class="ilt">Date :</td>
							<td><input name="newsdate" type="text" class="Text" value="<?=format_date_ymd_to_mdy($newsdate)?>" size="30" maxlength="40" /><font color="#FF0000">*</font></td>
						</tr>
						<tr valign="top">
							<td class="ilt">Type :</td>
							<td>
              	<select name="newstype" id="newstype" size="1" style="width:120px;">
                  <option value="News" <? if ($newstype == "News") { echo "selected"; } ?> >News</option>
                  <option value="Earnings" <? if ($newstype == "Earnings") { echo "selected"; } ?> >Earnings</option>
                  <option value="Meeting" <? if ($newstype == "Meeting") { echo "selected"; } ?> >Meeting</option>
            		<option value="Other" <? if ($newstype == "Other") { echo "selected"; } ?> >Other</option>
								</select><font color="#FF0000">*</font>
              </td>
						</tr> 
						<tr valign="top">
							<td class="ilt">Symbol :</td>
							<td><input class="Text" name="symbol" type="text" value="<?=$symbol?>" size="20" maxlength="10"></td>
						</tr>
						<tr valign="top">
							<td class="ilt" colspan="2">Notes :<br /><br />
              	<textarea name="details" rows="7" cols="60"><?=$details?></textarea>
              </td>
						</tr>
						<tr valign="top">
							<td colspan="2" align="center"><p>
								<input class="Submit" type="submit" name="editnews" value="Update"></p>
							</td>
						</tr>  
					</table>
				</td>
			</tr> 
			</form>
		</table>
<?
		tep();
/////////////////////////////////////////////////END OF EDIT SECTION/////////////////////////////////////////////////
?>
