<?php
include('top.php');
include('includes/functions.php');
	 
?>
<tr>
	<td valign="top">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr> 
				<td valign="top">
				<!-- CONTENT BEGIN -->
				<?
				//Date in YYYY-MM-DD Format
				$trade_date_to_process = previous_business_day();
			
/**************** SYSTEM LISTS (MARKETMAKER, ANALYST, BANKER)  ***********************************************************************************************/	
				$query_list_types = "SELECT slis_auto_id, slis_title_name FROM slis_system_lists WHERE slis_isactive = '1'";
				$result_list_types = mysql_query($query_list_types) or die(mysql_error());
				
				//START WHILE 1
				while($row_list_types = mysql_fetch_array($result_list_types))
				{
					if($row_list_types['slis_auto_id'] != 4)
					{
						$result_listitems = mysql_query("SELECT syll_id, syll_symbol, syll_description, DATE_FORMAT(syll_date_added, '%m/%d/%y') as syll_date_added, TO_DAYS(NOW()) - TO_DAYS(syll_date_added) + 1 as 'syll_days_on_list' FROM syll_system_list_lists WHERE syll_id = '".$row_list_types['slis_auto_id']."' AND syll_isactive = '1'") or die (mysql_error());
				
						$htmlfilebodydata .= '<tr><td colspan="5" nowrap><font face="Verdana, Arial, Helvetica, sans-serif" color="#000099" size="2">'.$row_list_types["slis_title_name"].'</font><font color="#ffffff" size="3">_________________________________________________________________</font></td></tr>';									
						$htmlfilebodydata .= '<tr><td colspan="5"><hr size="1" noshade color="#3399FF"></td></tr>';									
						$htmlfilebodydata .= '<tr> 
												<td align="left" nowrap><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Symbol</u>&nbsp;&nbsp;</font></td>
												<td align="left" nowrap ><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Description</u></font></td>
												<td nowrap ><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Date Added</u></font></td>
												<td nowrap align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Days on List</u>&nbsp;&nbsp;</font></td>
												<td>&nbsp;</td>
											</tr>';
												
						while ( $row = mysql_fetch_array($result_listitems) ) 
						{
							$htmlfilebodydata .= '<tr>';
							$htmlfilebodydata .=   '<td align="left" nowrap><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["syll_symbol"].'</font></td>
							<td align="left" nowrap><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["syll_description"].'</font>&nbsp;&nbsp;</td>
							<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["syll_date_added"].'</font></td>
							<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["syll_days_on_list"].'</font>&nbsp;&nbsp;&nbsp;</td>';
							$htmlfilebodydata .=   '<td>&nbsp;</td>
							</tr>';
						}
						$htmlfilebodydata .= '<tr><td colspan="5"><hr size="1" noshade color="#3399FF"></td></tr>';
					}
				}//END WHILE 1	
/*****************************************************************************************************************************************************/


/**************** ADMIN LISTS (GRAY, WATCH, RESTRICTED)  ***********************************************************************************************/	
				$query_list_types = "SELECT alis_auto_id, alis_title_name FROM alis_admin_lists WHERE alis_isactive = '1'";
				$result_list_types = mysql_query($query_list_types) or die(mysql_error());
				//START WHILE 1
				while($row_list_types = mysql_fetch_array($result_list_types))
				{
				
					$result_listitems = mysql_query("SELECT adll_id, adll_symbol, adll_description, DATE_FORMAT(adll_date_added, '%m/%d/%y') as adll_date_added, TO_DAYS(NOW()) - TO_DAYS(adll_date_added) + 1 as 'adll_days_on_list' FROM adll_admin_list_lists WHERE adll_id = '".$row_list_types['alis_auto_id']."' AND  adll_isactive = '1'") or die (mysql_error());
			
					//START IF 
					if(mysql_num_rows($result_listitems) > 0)
					{
	
						$htmlfilebodydata .= '<tr><td colspan="5" nowrap><font face="Verdana, Arial, Helvetica, sans-serif" color="#000099" size="2">'.$row_list_types["alis_title_name"].'</font><font color="#ffffff" size="3">_________________________________________________________________</font></td></tr>';									
						$htmlfilebodydata .= '<tr><td colspan="5"><hr size="1" noshade color="#3399FF"></td></tr>';									
						$htmlfilebodydata .= '<tr> 
												<td align="left" nowrap><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Symbol</u>&nbsp;&nbsp;</font></td>
												<td align="left" nowrap ><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Description</u></font></td>
												<td nowrap ><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Date Added</u></font></td>
												<td nowrap align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Days on List</u>&nbsp;&nbsp;</font></td>
												<td>&nbsp;</td>
											</tr>';
												
						while ( $row = mysql_fetch_array($result_listitems) ) 
						{
							$htmlfilebodydata .= '<tr>';
							$htmlfilebodydata .=   '<td align="left" nowrap><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["adll_symbol"].'</font></td>
							<td align="left" nowrap><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["adll_description"].'</font>&nbsp;&nbsp;</td>
							<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["adll_date_added"].'</font></td>
							<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["adll_days_on_list"].'</font>&nbsp;&nbsp;&nbsp;</td>';
							$htmlfilebodydata .=   '<td>&nbsp;</td>
							</tr>';
						}
						$htmlfilebodydata .= '<tr><td colspan="5"><hr size="1" noshade color="#3399FF"></td></tr>';									
					} // END IF
				}//END WHILE 1
/*****************************************************************************************************************************************************/


/********************************* USER LISTS  ********************************************************************************************************************/	
				$query_list_types = "SELECT usli_auto_id, usli_title_name FROM usli_user_lists WHERE usli_user_id = '".$user_id."' AND usli_isactive = '1'";
				$result_list_types = mysql_query($query_list_types) or die(mysql_error());
				//START WHILE 1
				while($row_list_types = mysql_fetch_array($result_list_types))
				{
					$result_listitems = mysql_query("SELECT usll_list_id, usll_symbol, usll_description, DATE_FORMAT(usll_date_added, '%m/%d/%y') as usll_date_added, TO_DAYS(NOW()) - TO_DAYS(usll_date_added) + 1 as 'usll_days_on_list' FROM usll_user_list_lists WHERE usll_list_id = '".$row_list_types['usli_auto_id']."' AND  usll_isactive = '1'") or die (mysql_error());
			
					if(mysql_num_rows($result_listitems) > 0)
					{
						$htmlfilebodydata .= '<tr><td colspan="5" nowrap><font face="Verdana, Arial, Helvetica, sans-serif" color="#000099" size="2">'.$row_list_types["usli_title_name"].'</font><font color="#ffffff" size="3">_________________________________________________________________</font></td></tr>';									
						$htmlfilebodydata .= '<tr><td colspan="5"><hr size="1" noshade color="#3399FF"></td></tr>';									
						$htmlfilebodydata .= '<tr> 
												<td align="left" nowrap><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Symbol</u>&nbsp;&nbsp;</font></td>
												<td align="left" nowrap ><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Description</u></font></td>
												<td nowrap ><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Date Added</u></font></td>
												<td nowrap align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000000" size="2"><u>Days on List</u>&nbsp;&nbsp;</font></td>
												<td>&nbsp;</td>
											</tr>';
												
						while ( $row = mysql_fetch_array($result_listitems) ) 
						{
							$htmlfilebodydata .= '<tr>';
							$htmlfilebodydata .=   '<td align="left" nowrap><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["usll_symbol"].'</font></td>
							<td align="left" nowrap><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["usll_description"].'</font>&nbsp;&nbsp;</td>
							<td nowrap><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["usll_date_added"].'</font></td>
							<td nowrap align="right"><font face="Courier New, Courier, mono" color="#000000" size="1">'.$row["usll_days_on_list"].'</font>&nbsp;&nbsp;&nbsp;</td>';
							$htmlfilebodydata .=   '<td>&nbsp;</td>
							</tr>';
						}
						$htmlfilebodydata .= '<tr><td colspan="5"><hr size="1" noshade color="#3399FF"></td></tr>';
					}									
				}//END WHILE 1
/*****************************************************************************************************************************************************/

														
				//**************************************************************************************
				//CREATE HTML OUTPUT FILE
				// BEGIN 1						
				$htmlfiledata = rep_header_emp_trades($trade_date_to_process);
				$htmlfiledata .= $htmlfilebodydata;															
				$htmlfiledata .= rep_footer_emp_trades();

				$str_filename = "STOCKLISTS_".$trade_date_to_process.".html";
				$str_pdfname = "STOCKLISTS_".$trade_date_to_process.".pdf";
				$fp = fopen($exportlocation.$str_filename, "w");
				fputs ($fp, $htmlfiledata);
				fclose($fp);
				shell_exec('htmldoc --webpage -f ./data/exports/'.$str_pdfname.' ./data/exports/'.$str_filename); 							
				xdebug("File Written and PDF Created",$str_filename);
				xdebug('<a href="./data/exports/'.$str_pdfname.'">Click Here for Report</a><br><br>',0);
				//echo '<a href="./data/exports/'.$str_pdfname.'">Click Here</a><br><br>';
				$mailsubject = "Stock Lists Report for ". $trade_date_to_process;
				$email_heading = "Stock Lists Report for Date (".$trade_date_to_process.") Generated on ".date("D, m/d/Y h:i a");
				xdebug("email_heading",$email_heading);
				$fileattach = $str_pdfname;
				$control_id = gen_control_number();
				$mailbodysubinfo = 'Stock Lists Report (PDF Format) is attached.';
				html_emails($user_email, $mailsubject, $mailbodysubinfo, $email_heading, $fileattach, $control_id); 
				?>
										
				<br>
				
				<table cellpadding="10" width="100%"><tr><td>
				<? table_start_percent(100,"Report Status"); ?>
				<a class="appmytext">
				<?=$email_heading?><br>
				The report (PDF Format) has been generated and a copy of it has been sent to your email account on file (<?=$user_email?>).<br><br>
				You may also access the report <a class="links10" href="./data/exports/<?=$str_pdfname?>" target="_blank">HERE</a>
				</a>
				<? table_end_percent(); ?>
				</td></tr></table>

				<!-- The following added so the top menu will be visible and not hidden behing the iframe -->
				<br><br>  

				
				<script language="javascript">
				//Specify display mode (0 or 1)
				//0 causes document to be displayed in an inline frame, while 1 in a new browser window
				var displaymode=0
				//if displaymode=0, configure inline frame attributes (ie: dimensions, intial document shown
				var iframecode='<iframe id="external" style="width:100%;height:600px" src="./data/exports/<?=$str_pdfname?>"></iframe>'
				/////DO NOT EDIT BELOW HERE////////////
				if (displaymode==0)
				document.write(iframecode)
				//-->
				</script>
										
				<?
				function rep_header_emp_trades ($trade_date_to_process)
				{
					$str_rep_datetime = "Stock Lists Report for Date (".format_date_ymd_to_mdy($trade_date_to_process).") Generated on ".date("D, m/d/Y h:i a");
	
					return '<html>
					<head>
					<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
					</head>
					<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0">
							<!-- MEDIA BOTTOM 0.5in --> 
							<!-- MEDIA LANDSCAPE "NO" --> 
							<!-- MEDIA LEFT 0.5in --> 
							<!-- MEDIA RIGHT 0.5in --> 
							<!-- MEDIA SIZE "Letter" --> 
							<!-- MEDIA TOP 0.1in --> 
							<!-- MEDIA TYPE "Plain" -->	
									<table width="640" border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td valign="top" height="14" bgcolor="#FFFFFF">
											<table width="640">
												<tr> 
												<td><img src="../../images/compliancelogo.gif"></td>
													<td valign="top" align="right"><font face="Verdana, Arial, Helvetica, sans-serif" color="#000099" size="2"><b>CompSys v 2.0 (Demo)</b></font></td>
												</tr>
												<tr>
													<td colspan=2><img src="../../images/grey_red_bar.gif" border="0"></td>
												</tr>
												<tr>
													<td colspan=2><font face="Verdana, Arial, Helvetica, sans-serif" color="#000099" size="2">'.$str_rep_datetime.'</font></td>
												</tr>
												<tr>
													<td colspan=2><img src="../../images/gray_black_bar.gif" border="0"></td>
												</tr>
											</table>
											</td>
										</tr>
										<tr>
											<td>&nbsp;</td>
										</tr>
										<tr>
											<td valign="top" width="630">
											<!-- Begin Stock List Data -->
											<table width="630"  border="0" cellspacing="1" cellpadding="1">';
				}
	
				function rep_footer_emp_trades ()
				{
					return 					
					'</table>
					<!-- End Trades Data -->
					</td>
					</tr>
					
					<tr>
					<td valign="top" bgcolor="#FFFFFF"><tr>
					<td align="left" valign="top">
					</td>
					</tr>
					<tr>
					<td>
					</td>
					</tr>
					</table>
					</body>
					</html>';
				}	
				?>
				<!-- CONTENT END -->
				</td>
			</tr>
		</table>
	</td>
</tr>

<?php
include('bottom.php');
?>
