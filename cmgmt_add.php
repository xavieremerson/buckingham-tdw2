<? 
include('includes/dbconnect.php');
include('includes/global.php');
include('includes/functions.php');
?>

<title>TDW</title>
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
			tsp(100, "Add Client");
			//echo "<br>";
			
			//$ID = 8;
			
			//START OF IF 1
			if($_POST)
			{
			
				//show_array($_POST);
				
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
				foreach($array as $k=>$v) {
		
					if($v == "0") 
					{
						$create_err_msg = $create_err_msg . "<br>" . $test_name[$k];
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
			
				$query_add = "INSERT INTO int_clnt_clients 
											(clnt_name,
											 clnt_code,
											 clnt_alt_code,
											 clnt_rr1,
											 clnt_rr2,
											 clnt_trader)
											VALUES
											('".strtoupper($cname)."',".
											"'".strtoupper($code)."',".
											"'".strtoupper($altcode)."',".
											"'".strtoupper($rr1)."',".
											"'".strtoupper($rr2)."',".
											"'".strtoupper($trader)."')"; 

				//xdebug("query_add",$query_add);
				$result_add = mysql_query($query_add) or die (tdw_mysql_error($query_add));

				$qry_new_client = "select clnt_auto_id as single_val from int_clnt_clients where clnt_code = '".strtoupper($code)."'";
				$new_clnt_id = db_single_val($qry_new_client);
				
				$query_add_payout = "INSERT INTO int_clnt_payout_rate 
														( clnt_auto_id , 
														  clnt_default_payout , 
															clnt_special_payout_rate , 
															clnt_start_month , 
															clnt_default_n_months , 
															clnt_name , 
															clnt_timestamp , 
															clnt_isactive ) 
														VALUES (
														'".$new_clnt_id."',"."'1', NULL , '', '0', '".strtoupper($cname)."', NOW( ) , '1')";
											
				//xdebug("query_edit_payout",$query_edit_payout);
				$result_add_payout = mysql_query($query_add_payout) or die (tdw_mysql_error($query_add_payout));
				
				
				
				//<!-- showmsg success -->
        showmsg(1, "Client [".$cname."] added successfully.");
	    	} // END OF INSERTING DATA IN TABLE
		} // END OF IF 1

		//xdebug("qry_new_client",$qry_new_client);
		//xdebug("new_clnt_id",$new_clnt_id);

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
									AND a.clnt_auto_id = '".$new_clnt_id."'";

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
							<td class="ilt" nowrap="nowrap">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                              &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                              </td>
							<td class="ilt">&nbsp;</td>
						</tr>
						<tr valign="top">
							<td class="ilt">Client Name :</td>
							<td><input name="cname" type="text" class="Text" value="<?=$cname?>" size="36" maxlength="60" /><font color="#FF0000">*</font></td>
						</tr>
						<tr valign="top">
							<td class="ilt">Code :</td>
							<td><input class="Text" name="code" type="text" value="<?=$code?>" size="20" maxlength="10"><font color="#FF0000">*</font></td>
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
							<select class="Text1" name="rr1" size="1" style="width:200px">
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
								<select class="Text1" name="rr2" size="1" style="width:200px">
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
								<select class="Text1" name="trader" size="1" style="width:200px">
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
							<td colspan="2" align="center"><p class="Contact">Fields marked with an asterisk <font color="#FF0000">*</font> are required</p></td></tr>
						<tr valign="top">
							<td colspan="2" align="center">
              <? if (!$_POST) {
							?>
								<input class="Submit" type="submit" name="addClient" value="Save">
							<?
							}
							?>
              </td>
						</tr>  
					</table>
				</td>
			</tr> 
			</form>
		</table>
<?
		tep();
?>
