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
			alert("Browser Version not compatable!");
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
			tsp(100, "Edit Client");
			//echo "<br>";
			
			//$ID = 8;
			
			//START OF IF 1
			if($editClient)
			{
					//Client Name AND Client Code ERROR CHECKING
				$array    = array();
				$test_name = array();
				$test_name[1] = "Client Name cannot be blank.";
				$test_name[2] = "Client Name entered is invalid.";
				$test_name[3] = "The Client Code cannot be blank";
				$test_name[4] = "The Client Code entered is invalid.";

				if($cname == "") 
				{
					$array[1] = "0";
					$cname_blank = "0";
				}  
				else 
				{
					$array[1] = "1";
					$cname_blank = "1";
				}
				if($code == "") 
				{
					$array[3] = "0";
					$code_blank = "0";
				}  
				else 
				{
					$array[3] = "1";
					$code_blank = "1";
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
			
			  //show_array($_POST);
				if (!$default_payout) {
				//echo "Special Payout Scenario<br>";
					if ($sel_rep_2of2!="" AND $payout_2of2 != "") {
						$clnt_default_payout = 2;
						$str_1 = $sel_rep_1of2."^".$payout_1of2;
						$str_2 = $sel_rep_2of2."^".$payout_2of2;
						$clnt_special_payout_rate = $str_1 . "#" . $str_2;
					} else {
						$clnt_default_payout = 0;
						$str_1 = $sel_rep_1of2."^".$payout_1of2;
						$clnt_special_payout_rate = $str_1;
					} 
				} else {
					$clnt_default_payout = 1;
					$clnt_special_payout_rate = "";
				}
				
				
  			if (!$rolling_n_months) {
					$clnt_default_n_months = 0;
				} else {
					$clnt_default_n_months = 1;
				}

				//xdebug("clnt_default_payout",$clnt_default_payout);
				//xdebug("clnt_special_payout_rate",$clnt_special_payout_rate);
			
				$query_edit = "UPDATE int_clnt_clients 
											SET clnt_name='".str_replace("'","\\'",strtoupper($cname))."',
											    clnt_code='".strtoupper($code)."',
													clnt_alt_code='".strtoupper($altcode)."',
													clnt_rr1='".strtoupper($rr1)."',
													clnt_rr2='".strtoupper($rr2)."',
													clnt_trader='".strtoupper($trader)."'
											WHERE clnt_auto_id='$ID'";
				//xdebug("query_edit",$query_edit);
				$result_edit = mysql_query($query_edit) or die (tdw_mysql_error($query_edit));
				
				$query_edit_payout = "UPDATE int_clnt_payout_rate 
															SET clnt_default_payout='".$clnt_default_payout."',
																	clnt_special_payout_rate='".$clnt_special_payout_rate."',
																	clnt_default_n_months='".$clnt_default_n_months."'
															WHERE clnt_auto_id='".$ID."'";
											
				//xdebug("query_edit_payout",$query_edit_payout);
				$result_edit_payout = mysql_query($query_edit_payout) or die (tdw_mysql_error($query_edit_payout));
				
				
				
				//<!-- showmsg success -->
        showmsg(1, "Client [".$cname."] updated successfully.");
	    	} // END OF INSERTING DATA IN TABLE
		} // END OF IF 1


    //show_array($_POST);
		$qry_client = "SELECT 
										a.clnt_auto_id,
										a.clnt_code,
										a.clnt_alt_code,
										a.clnt_name,
										a.clnt_rr1,
										a.clnt_rr2,
										a.clnt_trader,
										b.clnt_default_payout,
										b.clnt_special_payout_rate,
										b.clnt_start_month,
										b.clnt_default_n_months
									FROM int_clnt_clients a, int_clnt_payout_rate b
									WHERE a.clnt_auto_id = b.clnt_auto_id
									AND a.clnt_auto_id = '".$ID."'";

		$result_client = mysql_query($qry_client) or die (tdw_mysql_error($qry_client));
		while ( $row_client = mysql_fetch_array($result_client) ) 
		{
			//show_array($row_client);
			$cname = $row_client["clnt_name"]; 
			$code = $row_client["clnt_code"];
			$altcode = $row_client["clnt_alt_code"];
			$rr1 = $row_client["clnt_rr1"];
			$rr2 = $row_client["clnt_rr2"];
			$trader = $row_client["clnt_trader"];
			$default_payout = $row_client["clnt_default_payout"];
			$special_payout_rate = $row_client["clnt_special_payout_rate"];
			$default_n_months = $row_client["clnt_default_n_months"];
		}
		
		//xdebug("default_payout",$default_payout);
		//xdebug("special_payout_rate",$special_payout_rate);
?>


		<!-- 'CREATE' FIELDS TABLE -->
		<table cellpadding="2" cellspacing="0" border="0" height="100%" width="70%">  
			<form action="<?=$php_self?>" method="post"> 
			<tr> 
				<td>  
					<table>
						<tr valign="top">
							<td class="ilt" width="175">&nbsp;</td>
							<td class="ilt">&nbsp;</td>
						</tr>
						<tr valign="top">
							<td class="ilt">Client Name :</td>
							<td><input name="cname" type="text" class="Text" value="<?=$cname?>" size="30" maxlength="40" /><font color="#FF0000">*</font></td>
						</tr>
						<tr valign="top">
							<td class="ilt">Code :</td>
							<td><input class="Text" name="code"  readonly="true"  type="text" value="<?=$code?>" size="20" maxlength="10"><font color="#FF0000">*</font></td>
						</tr> 
						<tr valign="top">
							<td class="ilt">Tradeware Code :</td>
							<td><input class="Text" name="altcode" type="text" value="<?=$altcode?>" size="20" maxlength="10"><font color="#FF0000"></font><?=showhelp(1)?></td>
						</tr>
						<tr valign="top">
							<td class="ilt">Tradeware Charge :</td>
							<td><input class="Text" name="charge_a" type="text" value="<?=$charge_a?>" size="20" maxlength="10"><font color="#FF0000"></font><?=showhelp(2)?></td>
						</tr>
						<tr valign="top">
							<td class="ilt">Sales Rep. 1 :</td>
							<td>
							<select class="Text1" name="rr1" size="1" >
							<option value="">              </option>
							<option value="">&nbsp;REGISTERED REPS.&nbsp;</option>
							<?
							//get reps from query  on table mry_comm_rr_trades and join on users
							$qry_get_reps = "SELECT
																Initials, Fullname 
																from users
															WHERE Role > 2
															AND Role < 6
															AND Initials != ''
															ORDER BY Fullname";
							$result_get_reps = mysql_query($qry_get_reps) or die (tdw_mysql_error($qry_get_reps));
							while($row_get_reps = mysql_fetch_array($result_get_reps))
							{
							//for trades for shared rep, do a reverse lookup in the users table to get the id and then the shared reps
							?>
								<option value="<?=$row_get_reps["Initials"]?>" <? if ($row_get_reps["Initials"]==$rr1) {echo "selected";}?>><?=$row_get_reps["Fullname"]?></option>
							<?
							}
							?>
							</select>
							</td>
						</tr>
						<tr valign="top">
							<td class="ilt">Sales Rep. 2 :</td>
							<td>							
								<select class="Text1" name="rr2" size="1" >
							<option value="">              </option>
							<option value="">&nbsp;REGISTERED REPS.&nbsp;</option>
								<?
								//get reps from query  on table mry_comm_rr_trades and join on users
								$qry_get_reps = "SELECT
																	Initials, Fullname 
																	from users
																WHERE Role > 2
																AND Role < 5
															  AND Initials != ''
																ORDER BY Fullname";
								$result_get_reps = mysql_query($qry_get_reps) or die (tdw_mysql_error($qry_get_reps));
								while($row_get_reps = mysql_fetch_array($result_get_reps))
								{
								//for tradesfor shared rep, do a reverse lookup in the users table to get the id and then the shared reps
								?>
													<option value="<?=$row_get_reps["Initials"]?>" <? if ($row_get_reps["Initials"]==$rr2) {echo "selected";}?>><?=$row_get_reps["Fullname"]?></option>
								<?
								}
								?>
								</select>
							</td>
						</tr>
						<tr valign="top">
							<td class="ilt">Trader :</td>
							<td>
								<select class="Text1" name="trader" size="1" >
								<option value="">              </option>
								<option value="">&nbsp;TRADERS&nbsp;</option>
								<?
								//get reps from query  on table mry_comm_rr_trades and join on users
								$qry_get_reps = "SELECT
																	Initials, Fullname 
																	from users
																WHERE Role = 4
																AND Initials != ''
																ORDER BY Fullname";
								$result_get_reps = mysql_query($qry_get_reps) or die (tdw_mysql_error($qry_get_reps));
								while($row_get_reps = mysql_fetch_array($result_get_reps))
								{
								//for tradesfor shared rep, do a reverse lookup in the users table to get the id and then the shared reps
								?>
													<option value="<?=$row_get_reps["Initials"]?>" <? if ($row_get_reps["Initials"]==$trader) {echo "selected";}?>><?=$row_get_reps["Fullname"]?></option>
								<?
								}
								?>
								</select>
						</tr>
						<tr valign="top">
							<td class="ilt">Default Payout :</td>
							<td><input type="checkbox" name="default_payout" id="default_payout" <? if ($default_payout == 1) { echo "checked";}?> onClick="showhidepayout('row_defpayout')"/></td>
						</tr>	
						<tr id="row_defpayout" <? if ($default_payout == 1) {echo 'style="display=none; visibility=hidden"';} else {echo 'style="display=block; visibility=visible"';}?>> 
							<?
							if ($default_payout == 0) {
									$arr_payout_val = explode("^",$special_payout_rate);
																?>
							<td class="ilt" valign="top" nowrap="nowrap">&nbsp;&nbsp;&nbsp;&nbsp;Rep.(s)/Payout(s) 
							</td>
							<td>							
																<select class="Text1" name="sel_rep_1of2" size="1" >
																<option value="">>> Select Rep.</option>
																<?
																//get reps from query  on table mry_comm_rr_trades and join on users
																$qry_get_reps = "SELECT
																									ID, Fullname 
																									from users
																								WHERE Role > 2
																								AND Role < 6
																								AND Initials != ''
																								ORDER BY Fullname";
																$result_get_reps = mysql_query($qry_get_reps) or die (tdw_mysql_error($qry_get_reps));
																while($row_get_reps = mysql_fetch_array($result_get_reps))
																{
																?>
																					<option value="<?=$row_get_reps["ID"]?>" <? if ($row_get_reps["ID"]==$arr_payout_val[0]) {echo "selected";}?>><?=$row_get_reps["Fullname"]?></option>
																<?
																}
																?>
																</select>&nbsp;<input class="text" name="payout_1of2" size="6" value="<?=$arr_payout_val[1]?>" />
								<br />	
																<select class="Text1" name="sel_rep_2of2" size="1" >
																<option value="" selected="selected">>> Select Rep.</option>
																<?
																//get reps from query  on table mry_comm_rr_trades and join on users
																$qry_get_reps = "SELECT
																									ID, Fullname 
																									from users
																								WHERE Role > 2
																								AND Role < 6
																								AND Initials != ''
																								ORDER BY Fullname";
																$result_get_reps = mysql_query($qry_get_reps) or die (tdw_mysql_error($qry_get_reps));
																while($row_get_reps = mysql_fetch_array($result_get_reps))
																{
																?>
																					<option value="<?=$row_get_reps["ID"]?>"><?=$row_get_reps["Fullname"]?></option>
																<?
																}
																?>
																</select>&nbsp;<input class="text" name="payout_2of2" size="6" value="" />
									</td>
																<?								
							} elseif ($default_payout == 2) {
									?>
									<td class="ilt" valign="top" nowrap="nowrap">&nbsp;&nbsp;&nbsp;&nbsp;Rep.(s)/Payout(s) 
									</td>
									<td>
									<?	
									$arr_payouts = explode("#",$special_payout_rate);
									foreach($arr_payouts as $k=>$v) {
										$arr_payout_val = explode("^",$v);
																?>
																<select class="Text1" name="sel_rep_<?=($k+1)?>of2" size="1" >
																<option value="">>> Select Rep.</option>
																<?
																//get reps from query  on table mry_comm_rr_trades and join on users
																$qry_get_reps = "SELECT
																									ID, Fullname 
																									from users
																								WHERE Role > 2
																								AND Role < 6
																								AND Initials != ''
																								ORDER BY Fullname";
																$result_get_reps = mysql_query($qry_get_reps) or die (tdw_mysql_error($qry_get_reps));
																while($row_get_reps = mysql_fetch_array($result_get_reps))
																{
																?>
																					<option value="<?=$row_get_reps["ID"]?>" <? if ($row_get_reps["ID"]==$arr_payout_val[0]) {echo "selected";}?>><?=$row_get_reps["Fullname"]?></option>
																<?
																}
																?>
																</select>&nbsp;<input class="text" name="payout_<?=($k+1)?>of2" size="6" value="<?=$arr_payout_val[1]?>" />	<br />
																<?								
									}
									echo "</td>";
							} else {
							?>
							<td class="ilt" valign="top" nowrap="nowrap">&nbsp;&nbsp;&nbsp;&nbsp;Rep.(s)/Payout(s) 
							</td>
							<td>							
																<select class="Text1" name="sel_rep_1of2" size="1" >
																<option value="" selected="selected">>> Select Rep.</option>
																<?
																//get reps from query  on table mry_comm_rr_trades and join on users
																$qry_get_reps = "SELECT
																									ID, Fullname 
																									from users
																								WHERE Role > 2
																								AND Role < 6
																								AND Initials != ''
																								ORDER BY Fullname";
																$result_get_reps = mysql_query($qry_get_reps) or die (tdw_mysql_error($qry_get_reps));
																while($row_get_reps = mysql_fetch_array($result_get_reps))
																{
																?>
																					<option value="<?=$row_get_reps["ID"]?>"><?=$row_get_reps["Fullname"]?></option>
																<?
																}
																?>
																</select>&nbsp;<input class="text" name="payout_1of2" size="6" value="" />
								<br />	
																<select class="Text1" name="sel_rep_2of2" size="1" >
																<option value="" selected="selected">>> Select Rep.</option>
																<?
																//get reps from query  on table mry_comm_rr_trades and join on users
																$qry_get_reps = "SELECT
																									ID, Fullname 
																									from users
																								WHERE Role > 2
																								AND Role < 6
																								AND Initials != ''
																								ORDER BY Fullname";
																$result_get_reps = mysql_query($qry_get_reps) or die (tdw_mysql_error($qry_get_reps));
																while($row_get_reps = mysql_fetch_array($result_get_reps))
																{
																?>
																					<option value="<?=$row_get_reps["ID"]?>"><?=$row_get_reps["Fullname"]?></option>
																<?
																}
																?>
																</select>&nbsp;<input class="text" name="payout_2of2" size="6" value="" />
									</td>
							<?
							}
							?> 
						</tr>						
						<tr valign="top">
							<td class="ilt" nowrap="nowrap">Rolling 12 Months :</td>
							<td><input type="checkbox" name="rolling_n_months" <? if ($default_n_months == 1) { echo "checked";}?>/></td> 
						</tr>	
						<tr valign="top">
							<td colspan="2" align="center"><p class="Contact">Fields marked with an asterisk <font color="#FF0000">*</font> are required</p></td></tr>
						<tr valign="top">
							<td colspan="2" align="center"><p>
								<input class="Submit" type="submit" name="editClient" value="Update"></p>
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
