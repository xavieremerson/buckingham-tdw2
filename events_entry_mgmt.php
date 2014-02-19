<?php
//BRG
include('inc_header.php');
?>
			<script type="text/javascript" src="includes/javascript/calendar/calendar.js"></script>
			<script type="text/javascript" src="includes/javascript/calendar/lang/calendar-en.js"></script>
			<!-- helper script that uses the calendar -->
			<script type="text/javascript" src="includes/javascript/calendar/helper.js"></script>
			<link rel="alternate stylesheet" type="text/css" media="all" href="includes/javascript/calendar/calendar-win2k-2.css" title="win2k-2" />
			<script type="text/javascript">setActiveStyleSheet(document.getElementById("defaultTheme"), "win2k-2");</script>
			
	<? tsp(100, "Events/News Data Management"); ?>
	<?	
	  
		
		if($action == "remove")
		{
			$query_delete = "UPDATE news_events set news_isactive = 0 where auto_id = '$ID'";
			$result_delete = mysql_query($query_delete) or die(mysql_error());
		}
		
		if ($_POST) {
			//print_r($_POST);
			$sel_datefrom = $datefrom;
			$sel_dateto = $dateto;

			if ($newstype == 'ALL') {
			  $str_append = " AND a.news_event like '%' ";
			} else {
			  $str_append = " AND a.news_event = '".$newstype."' ";
			}

			if ($val_symbol == 'Enter Symbol' or $val_symbol == '') {
			  $str_append_symbol = " AND a.news_symbol like '%' ";
			} else {
			  $str_append_symbol = " AND a.news_symbol = '". $val_symbol ."' ";
			}

			//echo $str_append;
		} else {
		$sel_datefrom = format_date_ymd_to_mdy(business_day_backward(strtotime(date('Ymd')), 5)); 
		$sel_dateto = date('m/d/Y');
		$datefrom = format_date_ymd_to_mdy(business_day_backward(strtotime(date('Ymd')), 5)); 
		$dateto = date('m/d/Y');
		$newstype = 'ALL';
		}
		
		
?>

			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
			<form name="news_event" id="news_event" action="" method="post">
					<td width="10">&nbsp;</td>
					<SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
					<SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
						<SCRIPT LANGUAGE="JavaScript">
						var calfrom = new CalendarPopup("divfrom");
						calfrom.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
						var calto = new CalendarPopup("divto");
						calto.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
						</SCRIPT>																
					<td width="10">From:</td>
					<td width="10">&nbsp;</td>
					<td width="10"><input type="text" id="iddatefrom" class="Text1" name="datefrom" size="12" maxlength="12" value="<?=$sel_datefrom?>"></td>
					<td width="20" align="center"><A HREF="#" onClick="calfrom.select(document.forms['news_event'].datefrom,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
					<td width="10">&nbsp;</td>
					<td width="10">To:</td>
					<td width="10">&nbsp;</td>
					<td width="10"><input type="text" id="iddateto" class="Text1" name="dateto" size="12" maxlength="12" value="<?=$sel_dateto?>"></td>
					<td width="20" align="center"><A HREF="#" onClick="calto.select(document.forms['news_event'].dateto,'anchor2','MM/dd/yyyy'); return false;" NAME="anchor2" ID="anchor2"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
					<td width="10">&nbsp;</td>
          <td width="105">
						<script language="javascript" type="text/javascript" src="includes/actb/actb.js"></script>
            <script language="javascript" type="text/javascript" src="includes/actb/common.js"></script>
            <script>
            var tickerarray=new Array(
              <?
              $query_sel_symbol = "SELECT distinct(news_symbol) from news_events
                                    ORDER BY news_symbol";
              $result_sel_symbol = mysql_query($query_sel_symbol) or die(mysql_error());
              $count_row_symbol = 0;
              while($row_sel_symbol = mysql_fetch_array($result_sel_symbol))
              {
                echo "'". $row_sel_symbol["news_symbol"]."',"; //."\n"
              }
              ?>
              '');
            
            function set_val_null(str_id) {
              if (document.getElementById(str_id).value == 'Enter Symbol') {
                document.getElementById(str_id).value = ""; 
              }
            }
            </script>
            <input type='text' name="val_symbol" style='font-family:verdana;width:100px;font-size:12px' id='tb' value='Enter Symbol' onFocus="set_val_null('tb')" /> 
            <script>
            	var obj = actb(document.getElementById('tb'),tickerarray);
            </script>
          </td>
					<td width="10">&nbsp;</td>
					<td width="10">
					<select name="newstype" id="newstype" size="1">
					  <option value="ALL" <? if ($newstype == "ALL") { echo "selected"; } ?> >Event Types (ALL)</option>
            <option value="News" <? if ($newstype == "News") { echo "selected"; } ?> >News</option>
            <option value="Earnings" <? if ($newstype == "Earnings") { echo "selected"; } ?> >Earnings</option>
            <option value="Meeting" <? if ($newstype == "Meeting") { echo "selected"; } ?> >Meeting</option>
            <option value="Other" <? if ($newstype == "Other") { echo "selected"; } ?> >Other</option>
					</select>
					</td>
					<td width="10">&nbsp;</td>
					<td width="10"><input type="image" src="images/lf_v1/form_submit.png"></td>
					<td width="10" align="center">&nbsp;</td>
			 </form>															
					<td width="80">
          
          <script language="javascript">
					function go_excel () {
						document.prnt_excel.datefrom.value =  document.news_event.datefrom.value;
						document.prnt_excel.dateto.value =    document.news_event.dateto.value;
						document.prnt_excel.symbol.value =    document.news_event.val_symbol.value;;
						document.prnt_excel.info_str.value =  '<?=$userfullname?>';
						document.prnt_excel.newstype.value =  document.getElementById('newstype').options[document.getElementById('newstype').selectedIndex].value;
					}
					</script>
					<form name="prnt_excel" action="events_entry_export_excel.php" method="POST" target="_blank">
						<input type="image" src="images/lf_v1/exp2excel.png" border="0" alt="Output to Excel" onclick="go_excel()" />&nbsp;&nbsp;
						<input type="hidden" name="datefrom" value="" />
						<input type="hidden" name="dateto" value="" />
						<input type="hidden" name="symbol" value="" />
						<input type="hidden" name="info_str" value="" />
						<input type="hidden" name="newstype" value="" />
					</form>

          
<!--          <a href="check_mgmt_export_excel.php?xl=<?=$passtoexcel?>" target="_blank"><img src="images/lf_v1/exp2excel.png" border="0"></a></td>
-->					<td width="10">&nbsp;</td>
					<td>&nbsp;</td>
			</tr>
			</table>
		<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
					<!--TABLE 2 START-->
					<script language="JavaScript" src="includes/javascript/sorttable.js" type="text/javascript"></script>

					<table class="sortable" preserve_style="cell" width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr class="tbl_head_news">
							<td width="30">#</td>
							<td width="30">Del.</td>
							<td width="30">Edit</td>
							<td width="60">Date</td>
						  <td width="80">Type</td>
							<td width="60">Symbol</td> 
							<td width="600">Note</td>
							<td width="150">Entered By</td>
							<td>&nbsp;</td>
						</tr>
						<?
						$str_sql_select = "SELECT a.*, b.Fullname 
																from news_events  a
																left join Users b on a.news_entered_by = b.ID
																	WHERE a.news_isactive = 1 " . $str_append . $str_append_symbol .
																	" and a.news_date between '".format_date_mdy_to_ymd($sel_datefrom) . "' and '" . format_date_mdy_to_ymd($sel_dateto) . "'
                                order by a.news_date desc";
						$result_select = mysql_query($str_sql_select) or die(tdw_mysql_error($str_sql_select));

						$count_row_select = 0;
						while ( $row_select = mysql_fetch_array($result_select) ) 
						{
							if ($count_row_select%2) {
										$class_row_select = "trdark";
							} else { 
									$class_row_select = "trlight"; 
							} 
						?>
						<tr class="<?=$class_row_select?>"> 
							<td width="30">&nbsp;&nbsp;<?=($count_row_select + 1)?></td>
              <td nowrap>&nbsp; <a href="events_entry_mgmt.php?action=remove&ID=<?=$row_select["auto_id"]?>"  onclick="javascript:return confirm('Are you sure you want to remove the selected item?')"><img src="images/themes/standard/delete.gif" alt="Delete"></a>&nbsp; </td>
              <td nowrap>&nbsp; <a href="javascript:CreateWnd('events_entry_mgmt_edit.php?ID=<?=$row_select["auto_id"]?>&user_id=<?=$user_id?>', 550, 350, false);"><img src="images/themes/standard/edit.gif" alt="Edit"></a>&nbsp; </td>
 							<td valign="top">&nbsp;&nbsp;<?=format_date_ymd_to_mdy($row_select["news_date"])?></td>
							<td valign="top">&nbsp;&nbsp;<?=$row_select["news_event"]?></td>
							<td valign="top">&nbsp;&nbsp;<?=$row_select["news_symbol"]?></td>
							<td valign="top">&nbsp;&nbsp;<?=$row_select["news_notes"]?></td>
							<td valign="top">&nbsp;&nbsp;<?=trim($row_select["Fullname"])?></td>
							<td>&nbsp;</td>
						</tr>
						<?php
						$count_row_select = $count_row_select + 1;
						}
						?>
					</table>

									<!--"<tr" + rowclass + ">"+
									"<td nowrap>&nbsp; <a href=\"check_mgmt.php?type=manage&action=remove&ID="+rowchecks_array[0]+"\"  onclick=\"javascript:return confirm('Are you sure you want to remove the check for $"+rowchecks_array[2]+" from the list?')\"><img src=\"images/themes/standard/delete.gif\" alt=\"Delete\"></a>&nbsp; </td>"+
									"<td nowrap>&nbsp; <a href=\"javascript:CreateWnd(\'check_mgmt_edit.php?ID="+rowchecks_array[0]+"&user_id=<?=$user_id?>', 550, 350, false);\"><img src=\"images/themes/standard/edit.gif\" alt=\"Edit\"></a>&nbsp; </td>"+
									"<td>&nbsp;"+rowchecks_array[1]+"</td>"+ 
									"<td align=\"right\">"+rowchecks_array[2]+"&nbsp;</td>"+ 
									"<td>&nbsp;"+rowchecks_array[3]+"</td>"+ 
									"<td>&nbsp;"+rowchecks_array[4]+"</td>"+ 
									"<td>&nbsp;"+rowchecks_array[5]+"</td>"+ 
									"<td>&nbsp;"+rowchecks_array[6]+"</td>"+ 
									"<td>&nbsp;"+rowchecks_array[7]+"</td>"+ 
									"<td>&nbsp;</td></tr>");-->							
				</td>
			</tr>
		</table>
	
		<? tep(); ?>
			<DIV ID="divfrom" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
    	<DIV ID="divto" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>			
<?
 include('inc_footer.php');
?>
