<?
include('inc_header.php');
?>
<script src="includes/prototype/prototype.js" type="text/javascript"></script>
<link href="main_appr.css" rel="stylesheet" type="text/css">
<script language="javascript">
<? include('main_appr.js.php');?>
b_startExec();
</script>

<script LANGUAGE="JavaScript">
<!--
function confirmSubmit()
{
var agree=confirm("Are you sure you wish to Save and Continue?");
if (agree)
	return true ;
else
	return false ;
}
// -->
</script>

							<?
							if($_POST) {
									
									if (trim($symbol) != "") {
									
											$str_start_date = date ("Y-m-d H:i:s", strtotime($syear."-".$smonth."-".$sday." ".$shour.":".$smin.$sampm));
											if ($is_manual_close) {
												$str_end_date = '2099-12-31 00:00:00';
												$str_open_end = 1;
											} else {
												$str_end_date = date ("Y-m-d H:i:s", strtotime($eyear."-".$emonth."-".$eday." ".$ehour.":".$emin.$eampm));
												$str_open_end = 0;
											}
											
											$qry="insert into etpa_restricted_list
														(auto_id,
														etpa_date_added,
														etpa_cusip,
														etpa_datetime_start,
														etpa_datetime_stop,
														etpa_auto,
														etpa_added_by,
														etpa_open_end,
														etpa_comment) values (
														NULL,
														now(),
														'".strtoupper(trim($symbol))."',
														'".$str_start_date."',
														'".$str_end_date."',
														0,
														'".$user_id."',
														'".$str_open_end."',
														'".str_replace("'","",$notes)."'
														)";
											$result = mysql_query($qry) or die(tdw_mysql_error($qry));
											$str_status = $qry."<br>"."<font color='green'>Restricted List entry for ".strtoupper(trim($symbol))." saved.</font>";
										} else {
											$str_status = "<font color='red'>Record not saved. Symbol is missing. Please try again.</font>";
										}
							} else {
									$str_status = "";
							}
							?>

		<!-- BEGIN CONTAINER -->
		 <?
		 include('main_appr_top_menu.php');
	
				 tsp(100, "ADD TO RESTRICTED LIST"); 
				 ?>		
				 <div id="scrollElement_a">
				 <table width="100%" cellpadding="1" cellspacing="0" style="border: 1px solid #ffc9a6;"><tr><td valign="top">
				 <form id="trd_request" name="trd_request" method="post" action="main_appr_rlist_add.php">
					<table class="font_etpa">
						<tr>
							<td colspan="3"><?=$str_status?>
							</td>
						</tr>
						<tr>
							<td>Symbol</td>
							<td><input class="text" name="symbol" id="symbol" type="text" value="" size="12" maxlength="12"/></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>Start Time</td>
							<td>
							<select name="smonth">
              	<?
								for ($i=1;$i<13;$i++) {
								  if ($i == date('m')) { $msel =  " selected "; } else { $msel =  ""; }
									echo "<option value='".$i."' ". $msel . ">".$i."</option>\n";
								}
								?>
               </select>
							 <select name="sday">
              	<?
								for ($i=1;$i<32;$i++) {
								  if ($i == date('d')) { $dsel =  " selected "; } else { $dsel =  ""; }
									echo "<option value='".$i."' ". $dsel . ">".$i."</option>\n";
								}
								?>
                </select>
                <select name="syear">
                <option value="<?=date('Y')?>" selected><?=date('Y')?></option>
                <option value="<?=date('Y')+1?>"><?=date('Y')+1?></option>
								</select>
                &nbsp;
							 <select name="shour">
              	<?
								for ($i=1;$i<13;$i++) {
								  if ($i == date('H')) { $hsel =  " selected "; } else { $hsel =  ""; }
									echo "<option value='".$i."' ". $hsel . ">".$i."</option>\n";
								}
								?>
                </select>
							 <select name="smin">
              	<?
								for ($i=1;$i<61;$i++) {
								  if ($i == date('i')) { $minsel =  " selected "; } else { $minsel =  ""; }
									echo "<option value='".$i."' ". $minsel . ">".$i."</option>\n";
								}
								?>
                </select>
                <select name="sampm">
                <option value="am" <? if (date('a')=='am') {echo " selected ";}?>>AM</option>
                <option value="pm" <? if (date('a')=='pm') {echo " selected ";}?>>PM</option>
								</select>
              </td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>End Time</td>
							<td>
							<select name="emonth">
              	<?
								for ($i=1;$i<13;$i++) {
								  if ($i == date('m')) { $msel =  " selected "; } else { $msel =  ""; }
									echo "<option value='".$i."' ". $msel . ">".$i."</option>\n";
								}
								?>
               </select>
							 <select name="eday">
              	<?
								for ($i=1;$i<32;$i++) {
								  if ($i == date('d')) { $dsel =  " selected "; } else { $dsel =  ""; }
									echo "<option value='".$i."' ". $dsel . ">".$i."</option>\n";
								}
								?>
                </select>
                <select name="eyear">
                <option value="<?=date('Y')?>" selected><?=date('Y')?></option>
                <option value="<?=date('Y')+1?>"><?=date('Y')+1?></option>
								</select>
                &nbsp;
							 <select name="ehour">
              	<?
								for ($i=1;$i<13;$i++) {
								  if ($i == date('H')) { $hsel =  " selected "; } else { $hsel =  ""; }
									echo "<option value='".$i."' ". $hsel . ">".$i."</option>\n";
								}
								?>
                </select>
							 <select name="emin">
              	<?
								for ($i=1;$i<61;$i++) {
								  if ($i == date('i')) { $minsel =  " selected "; } else { $minsel =  ""; }
									echo "<option value='".$i."' ". $minsel . ">".$i."</option>\n";
								}
								?>
                </select>
                <select name="eampm">
                <option value="am" <? if (date('a')=='am') {echo " selected ";}?>>AM</option>
                <option value="pm" <? if (date('a')=='pm') {echo " selected ";}?>>PM</option>
								</select>
              <br>
              OR 
							<input type="checkbox" name="is_manual_close" value="1"> Keep Active until manual removal.</td>
              <td>&nbsp;</td>
						</tr>
						<tr>
							<td>Notes:</td>
							<td colspan="2"><textarea name="notes" rows="4" cols="40"></textarea></td>
						 </tr>  
						<tr>
							<td colspan="2">
								<input type="submit" name="submit" value="Save" onclick="confirmSubmit()" />&nbsp;&nbsp;
								<input type="reset" name="clear" value="Reset"/>&nbsp;&nbsp;
							</td>
						 </tr>  
					</table>  
					</form>
					<div id="instruct" class="instruct" align="right"></div>
					</td></tr></table>
				 </div>
				 <? tep(); ?> 
		 <table width="100%" cellpadding="1" cellspacing="0" style="border: 1px solid #ffc9a6;"><tr><td valign="top">
     <font class="font_etpa_sm">Approvers Online:</font> 
     <div id="appr_online">
		 <?
		 $qry_online = "SELECT concat(substring(`Firstname`,1,1), '. ', `Lastname`) as approver, `user_isonline`
										FROM `users` WHERE `is_trade_approver` = 1
										AND `user_isactive` = 1";
		 $result_online = mysql_query($qry_online) or die(tdw_mysql_error($qry_online));
		 while ($row_online = mysql_fetch_array($result_online)) {
			
			 if ($row_online["user_isonline"] == 1) {
				echo '<img src="images/isonline.png" border="0">&nbsp;<a class="online">'.$row_online["approver"]."</a>&nbsp;&nbsp;";
			 } else {
				echo '<img src="images/isoffline.png" border="0">&nbsp;<a class="offline">'.$row_online["approver"]."</a>&nbsp;&nbsp;";
			 }
		 }
		 ?>
		 </div>
     </td></tr></table>
		 <div id="err_notify" class="font_etpa_error"></div>
     <!-- END CONTAINER -->
<?
include('inc_footer.php');
?>