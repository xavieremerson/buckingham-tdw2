<?php
include('top.php');
include('includes/functions.php'); 
?>

<script type="text/javascript">
<!--
/***********************************************
* DD Tab Menu II script- © Dynamic Drive DHTML code library (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code
***********************************************/

//Set tab to intially be selected when page loads:
//[which tab (1=first tab), ID of tab content to display]: 
var initialtab=[1, "sc1"]

//Turn menu into single level image tabs (completely hides 2nd level)?
var turntosingle=0 //0 for no (default), 1 for yes

//Disable hyperlinks in 1st level tab images?
var disabletablinks=0 //0 for no (default), 1 for yes


////////Stop editting////////////////

var previoustab=""

if (turntosingle==1)
document.write('<style type="text/css">\n#tabcontentcontainer{display: none;}\n</style>')

function expandcontent(cid, aobject){
if (disabletablinks==1)
aobject.onclick=new Function("return false")
if (document.getElementById && turntosingle==0){
highlighttab(aobject)
if (previoustab!="")
document.getElementById(previoustab).style.display="none"
document.getElementById(cid).style.display="block"
previoustab=cid
}
}

function highlighttab(aobject){
if (typeof tabobjlinks=="undefined")
collectddimagetabs()
for (i=0; i<tabobjlinks.length; i++)
tabobjlinks[i].className=""
aobject.className="current"
}

function collectddimagetabs(){
var tabobj=document.getElementById("ddimagetabs")
tabobjlinks=tabobj.getElementsByTagName("A")
}

function do_onload(){
collectddimagetabs()
expandcontent(initialtab[1], tabobjlinks[initialtab[0]-1])
}

if (window.addEventListener)
window.addEventListener("load", do_onload, false)
else if (window.attachEvent)
window.attachEvent("onload", do_onload)
else if (document.getElementById)
window.onload=do_onload
-->
</script>




<?

//Date in YYYY-MM-DD format
$trade_date_to_process = previous_business_day();

//**********************************************************************
//Get Employee Accounts data in a local variable
$result = mysql_query("SELECT acct_number FROM Employee_accounts where acct_is_active = 1 ORDER BY acct_number") or die (mysql_error());

$i = 0;
$arr_accounts = array();

while ( $row = mysql_fetch_array($result) ) 
{
	$arr_accounts[$i] = $row["acct_number"];
	$i = $i+1;
}

//**********************************************************************
//Get Money Laundering Accounts data in a local variable
$result = mysql_query("SELECT aact_acct_number FROM aact_accounts WHERE aact_acct_number = '40500242' OR aact_acct_number = '80610785' OR aact_acct_number = '80613054' ORDER BY aact_acct_number") or die (mysql_error());

$i = 0;
$arr_laundering_accounts = array();

while ( $row = mysql_fetch_array($result) ) 
{
	$arr_laundering_accounts[$i] = $row["aact_acct_number"];
	$i = $i+1;
}


//Get Employee Names on account
$result1 = mysql_query("SELECT acct_number, concat( acct_name1, ', ', acct_name2, ' <BR>(Rep: ', acct_rep, ')' ) as 'acct_name'  FROM Employee_accounts where acct_is_active = 1 ORDER BY acct_number") or die (mysql_error());

$i = 0;
$arr_accountnames = array();

while ( $row = mysql_fetch_array($result1) ) 
{
	$arr_accountnames[$row["acct_number"]] = $row["acct_name"];
	$i = $i+1;
}
	
//Get Trade_id from comments/notes table in an array
$result = mysql_query("SELECT distinct nadd_trade_id as trade_id from nadd_add_notes where nadd_is_active = 1") or die (mysql_error());
$i = 0;
$arr_trade_id = array();

while ( $row = mysql_fetch_array($result) )
{
	$arr_trade_id[$i] = $row["trade_id"];
	$i = $i+1;
}

//Get Trade_id from action items table in an array
$result = mysql_query("SELECT distinct acti_trade_id as trade_id FROM acti_action_item_flag WHERE acti_is_active = 1 AND acti_user_id = " . $user_id) or die (mysql_error());
$i = 0;
$arr_trade_id_acti = array();

while ( $row = mysql_fetch_array($result) ) 
{
	$arr_trade_id_acti[$i] = $row["trade_id"];
	$i = $i+1;
}
//***********************************************************************

$query_banks = "SELECT * FROM chou_clearing_house WHERE chou_isactive = '1'";
$result_banks = mysql_query($query_banks) or die(mysql_error());

?>

<tr>
	<td align="left" valign="top">
	<table width="100%" cellpadding="4" cellspacing="4"><tr><td>
	<? table_start_percent(100, "Filter"); ?>
	<?
	if($start == '')
	$start = 0;
	
	if($end == '')
	$end = 9999999999;
		
		//echo "<BR><BR> START   " . $start;
		//echo "<BR><br> END   " . $end;
	?>

		<!-- START TABLE 1 -->
		<table class="tablewithdata" width="100%"  border="0" cellspacing="0" cellpadding="0">
			<tr>
				<form action="<?=$PHP_SELF?>?action=filter" id="filtertrade" method="post"> 
				<td width="550" align="left" valign="middle"><b><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Filter</font></b></td>
				<td align="right">
				<select class="Text" name="trdm_trade_date" size="1" >
				<option value="">&nbsp;TRADE DATE</option>
				<option value="">=======</option>
				<?
						
				$i = 1;
				while ($i < 90) 
				{
					$previoustime = time() - (60*60*24*$i);
					$previousday = date("Y-m-d", $previoustime);

					if (date("l", $previoustime) == "Sunday") 
					{
						$previoustime = time() - (60*60*24*($i+2));
						$previousday = date("Y-m-d", $previoustime);
						$i = $i+2 + 1;	
						if ( check_holiday($previousday) == 1) 
						{						
							$previoustime = time() - (60*60*24*($i));
							$previousday = date("Y-m-d", $previoustime);
							$i = $i+1;	
						}
					} 
					elseif (date("l", $previoustime) == "Monday" and check_holiday($previousday) == 1) 
					{
						$previoustime = time() - (60*60*24*($i+3));
						$previousday = date("Y-m-d", $previoustime);
						$i = $i+3 + 1;	
					} 
					else 
					{
						$previousday = "ERROR!";
						$i = $i+1;						
					}
				?>
				<option value="<?=date("Y-m-d", time() - (60*60*24*($i-1)))?>"><?=date("m-d-Y", time() - (60*60*24*($i-1)))?></option>
				<?
				}						
				?>
				</select>
				</td>
				<td align="right">
				<select class="Text" name="trdm_symbol" size="1" >
				<option value="">&nbsp;SYMBOL&nbsp;</option>
				<option value="">======</option>
				<?
				$query_statement = "SELECT distinct (UPPER(trdm_symbol)) as 'trdm_symbol'
									 FROM Trades_m 
									 WHERE trdm_trade_date > '". date("Y-m-d", time() - (60*60*24*30)) ."' 
									 AND trdm_symbol != ''
									 AND LENGTH(trdm_symbol) < 8
									 AND trdm_account_number NOT LIKE '0000%' 
									 ORDER BY trdm_symbol";	
				
//echo "<BR><BR> $query_statement1  " . $query_statement;

				
				$result = mysql_query($query_statement) or die (mysql_error());
				
				while ( $row = mysql_fetch_array($result) )
				{
					?> 
					<option value="<?=$row["trdm_symbol"]?>"><?=$row["trdm_symbol"]?></option>
					<?
				}						
					?>
				
				</select>
				</td>
				<td align="right">
				
				<select class="Text" name="trdm_account_number" size="1" >
				<option value="">&nbsp;ACCT. #&nbsp;</option>
				<option value="">======</option>
				<?
				$query_statement = "SELECT distinct (trdm_account_number)
														 FROM Trades_m 
														 where trdm_trade_date > '". date("Y-m-d", time() - (60*60*24*30)) ."' 
														 and trdm_symbol != ''
														 and trdm_account_number not like '0000%' 
														 ORDER BY trdm_account_number";	

//echo "<BR><BR> $query_statement2  " . $query_statement;

				$result = mysql_query($query_statement) or die (mysql_error());
				while ( $row = mysql_fetch_array($result) ) 
				{
					?>
					<option value="<?=$row["trdm_account_number"]?>"><?=$row["trdm_account_number"]?></option>
					<?
				}						
				?>
				
				</select>
				</td>
				<td nowrap align="right"><input name="emp_trades" type="checkbox" value="1"><a class="fieldlabel10"><b><font color="#FFFFFF" size="1" face="Verdana, Arial, Helvetica, sans-serif">Employee</font></b></a></td>
				<!-- <td align="right"><input class="Submit" name="submit1" type="submit" value="  Apply  "></td> -->
				<td align="right"><INPUT type=image name="submit1" src="images/submit_buttons/apply_filter.jpg" border="0"></td>
				</form>
			</tr>
		</table>
		<!-- END TABLE 1 -->
	<? table_end_percent(); ?>
	</td></tr>
	<tr><td>
	<? table_start_percent(100, "Trades"); ?>
	<?
	//if($start == '')
	//$start = 0;
	
//	if($end == '')
//	$end = 25;
	?>
		<!-- START TABLE 2 -->	
		<!--Table with thin cell border-->
<!-- 		<table width="100%" cellpadding="0", cellspacing="0" bgcolor="#6B696B" height="100%"> -->
		<table width="100%" cellpadding="0", cellspacing="0" bgcolor="#ffffff" height="100%">
			<tr><td height="5">&nbsp;</td></tr>
			<tr valign="top">
				<td valign="top">
				<div id="ddimagetabs">
				<?
				while($row_banks = mysql_fetch_array($result_banks))
				{
				?>
				
				<a id = "tmenu" href="view_trades_mult.php?action=filter&start=<?=$row_banks['chou_start']?>&end=<?=$row_banks['chou_end']?>" onMouseover="expandcontent('sc1', this)"><?=$row_banks['chou_name']?></a> 
				
				<?
				}
				?>
				
				</div> 
						
				</td>
			</tr>
<!-- 			<tr>
				<td valign="bottom">
					<DIV id="tabcontentcontainer">
					<div id="sc1" class="tabcontent">
					</div>
					</DIV>

				</td>
			</tr> -->
			
			<?php
			include('includes/dbconnect.php');
					  
			$date = date("Ymd");
			$lastLogin = mysql_query("UPDATE Users SET LastLogin = '$date' WHERE Username = '$user'") or die (mysql_error());
			?>
				
			<?
			if ($trdm_trade_date != '') 
			{ 
				$str_trdm_trade_date = " where trdm_trade_date = '". $trdm_trade_date ."'";
			} 
			else 
			{
				$str_trdm_trade_date = " where trdm_trade_date = '". $trade_date_to_process ."'";
			}			  
	
			if ($trdm_symbol != '') 
			{ 
				$str_trdm_symbol = " and trdm_symbol = '".$trdm_symbol."'";
			} 
			else 
			{
				$str_trdm_symbol = " and trdm_symbol != '' and LENGTH(trdm_symbol) < 8";
			}			  
	
			if ($trdm_account_number != '') 
			{ 
				$str_trdm_account_number = " and trdm_account_number = '".$trdm_account_number."'";
			} 
			else 
			{
				$str_trdm_account_number = " and trdm_account_number not like '0000%' ";
			}
				
			$query_statement = "SELECT 	trdm_auto_id, 
				trdm_account_number, 
				trdm_trade_date, 
				trdm_settle_date, 
				abs(round(trdm_quantity,0)) as 'trdm_quantity',
				round(trdm_price,2) as 'trdm_price',
				trdm_buy_sell,
				UPPER(trdm_symbol) as 'trdm_symbol',
				trdm_sec_description,
				trdm_trade_time
				FROM Trades_m " . $str_trdm_trade_date . 
				$str_trdm_symbol .
				$str_trdm_account_number .
				" ORDER BY trdm_symbol, trdm_trade_time LIMIT ".$start. "," . $end;
				
				$result = mysql_query($query_statement) or die (mysql_error());
			?>
			
			<tr>
				<td valign="top">
					<!-- START TABLE 3 -->
					<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
						<tr>
							<td valign="top">
								<!-- START TABLE 4 -->
								<!-- class="tablewithdata" -->
								<table class="sortable" id="accounts_table"  width="100%"  border="0" cellspacing="1" cellpadding="0">
									<!-- class="tableheading12" -->
									<tr bgcolor="#CCCCCC"> 
										<td height="20" width="25" align="center" valign="middle" >&nbsp;</td>
										<td width="18"><img src="images/add_note_heading.gif" border="0" alt="Add Note">N</td>  
										<td width="18"><img src="images/flag_heading.gif" border="0" alt="Add Flag and Create Action Item">F</td>  
										<td >Account #</td>  
										<td >Symbol</td> 
										<td >Description</td>
										<td >B/S</td>
										<td >Quantity</td>
										<td >Price</td>
										<td >Trade Date</td>
										<td >Time</td>
									</tr>
			
									<?			    
									//WHILE START
									while ( $row = mysql_fetch_array($result) ) 
									{
										//IF 1 START
										if ($emp_trades != 1) 
										{
											if (in_array($row["trdm_account_number"], $arr_accounts)) 
											{
												//echo '<tr class="tablerowhighlight">';
												echo '<tr class="tablerow">';
											} 
											else 
											if (in_array($row["trdm_account_number"], $arr_laundering_accounts)) 
											{
											?>
												<tr class="tablerow_red_highlight" onClick="javascript:CreateWnd('pop_acct_note.php', 400, 125, false);">					
											<?
											}
											else
											{
												echo '<tr class="tablerow">';					
											}
											
											?>
											
											<td><?
											if (in_array($row["trdm_account_number"], $arr_accounts))
											{
												echo '<img src="images/arrow.gif" onMouseover="ddrivetip(\''.$arr_accountnames[$row["trdm_account_number"]].'\',\'yellow\', 300)"; onMouseout="hideddrivetip()">';
											}
											else 
											{
												echo '&nbsp;';					
											}
											?>
											</td>
												
											<?
											//IF 2 START
											if (in_array($row["trdm_account_number"], $arr_accounts)) 
											{
												if (in_array($row["trdm_auto_id"], $arr_trade_id)) 
												{
												?>
													<td><a href="javascript:CreateWnd('av_note.php?trade_id=<?=$row["trdm_auto_id"]?>&user_id=<?=$user_id?>', 360, 300, false);"><img src="images/added_note.gif" border="0" alt="Add Note"></a></td>
												<?
												}
												else 
												{
												?>
													<td><a href="javascript:CreateWnd('av_note.php?trade_id=<?=$row["trdm_auto_id"]?>&user_id=<?=$user_id?>', 360, 300, false);"><img src="images/add_note.gif" border="0" alt="Add Note"></a></td>
												<?
												}							
												?>
											
												<?
												if (in_array($row["trdm_auto_id"], $arr_trade_id_acti)) 
												{
												?>
													<td><a href="javascript:CreateWnd('av_flag.php?trade_id=<?=$row["trdm_auto_id"]?>&user_id=<?=$user_id?>', 360, 300, false);"><img src="images/added_flag.gif" border="0" alt="Add Flag and Create Action Item"></a></td>
												<?
												}
												else 
												{
												?>
													<td><a href="javascript:CreateWnd('av_flag.php?trade_id=<?=$row["trdm_auto_id"]?>&user_id=<?=$user_id?>', 360, 300, false);"><img src="images/add_flag.gif" border="0" alt="Add Flag and Create Action Item"></a></td>
												<?
												}							
												?>
											<!-- <td><img src="images/add_flag.gif" border="0" alt="Flag and Create Action Item"></td>  -->							
											<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_account_number"]?></a></td>
											<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CreateWnd('pop_quote.php?param_symbol=<?=$row["trdm_symbol"]?>', 500, 200, false);"><?=$row["trdm_symbol"]?></a></td>
											<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_sec_description"]?></a></td>
											<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=convert_buy_sell($row["trdm_buy_sell"])?>&nbsp;&nbsp;&nbsp;&nbsp;</a></td>
											<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_quantity"]?>&nbsp;&nbsp;&nbsp;</a></td>
											<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_price"]?>&nbsp;&nbsp;&nbsp;</a></td>
											<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=format_date_ymd_to_mdy($row["trdm_trade_date"])?>&nbsp;&nbsp;</a></td>
											<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_trade_time"]?>&nbsp;&nbsp;&nbsp;</a></td>
											<?
											} //IF 2 END
											else 
											{
											?>
											<td>&nbsp;</td>
											<td>&nbsp;</td>							
											<td>&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_account_number"]?></td>
											<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CreateWnd('pop_quote.php?param_symbol=<?=$row["trdm_symbol"]?>', 500, 200, false);"><?=$row["trdm_symbol"]?></a></td>
											<td>&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_sec_description"]?></td>
											<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;<?=convert_buy_sell($row["trdm_buy_sell"])?>&nbsp;&nbsp;&nbsp;</td>
											<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_quantity"]?>&nbsp;&nbsp;</td>
											<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_price"]?>&nbsp;&nbsp;</td>
											<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;<?=format_date_ymd_to_mdy($row["trdm_trade_date"])?>&nbsp;&nbsp;</td>
											<td align="right">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_trade_time"]?>&nbsp;&nbsp;</td>
											<?
											}
											?>
										</tr>
										<?
										} //IF 1 END
										else 
										{
											if (in_array($row["trdm_account_number"], $arr_accounts)) 
											{
												echo '<tr class="tablerowhighlight">';
												?><td><?
												if (in_array($row["trdm_account_number"], $arr_accounts)) 
												{
													echo '<img src="images/arrow.gif" onMouseover="ddrivetip(\''.$arr_accountnames[$row["trdm_account_number"]].'\',\'yellow\', 300)"; onMouseout="hideddrivetip()">';
												} 
												else 
												{
													echo '&nbsp;';					
												}
											?>
												</td>
												<td><img src="images/add_note.gif" border="0" alt="Add Note"></td>
												<td><img src="images/add_flag.gif" border="0" alt="Flag and Create Action Item"></td>							
												<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()">&nbsp;&nbsp;&nbsp;&nbsp;<a class="emptrades"><?=$row["trdm_account_number"]?></a></td> 
												<td>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CreateWnd('pop_quote.php?param_symbol=<?=$row["trdm_symbol"]?>', 500, 200, false);"><!-- &nbsp;&nbsp;&nbsp;&nbsp; --><?=$row["trdm_symbol"]?></a></td>
												<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_sec_description"]?></a></td>
												<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=convert_buy_sell($row["trdm_buy_sell"])?>&nbsp;&nbsp;&nbsp;</a></td>
												<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_quantity"]?>&nbsp;&nbsp;</a></td>
												<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_price"]?>&nbsp;&nbsp;</a></td>
												<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=format_date_ymd_to_mdy($row["trdm_trade_date"])?>&nbsp;&nbsp;</a></td>
												<td onMouseover="ddrivetip('<?=$arr_accountnames[$row["trdm_account_number"]]?>','white', 300)"; onMouseout="hideddrivetip()" align="right"><a class="emptrades">&nbsp;&nbsp;&nbsp;&nbsp;<?=$row["trdm_trade_time"]?>&nbsp;&nbsp;</a></td>
											</tr>
											<?
											} 
										}
									}//WHILE END
									?>
									</table>
									<!-- END TABLE 4 -->
									<script language="JavaScript">
									<!--
										//tigra_tables('accounts_table', 3, 1, '#ffffff', '#ffffcc', '#ffcc66', '#cccccc');
										//tigra_tables('accounts_table', 1, 0, '#ffffff', '#F3F1FF', '#B8D6FE', '#cccccc');
										tigra_tables('accounts_table', 1, 0, '#ffffff', '#F3F1FF', '#B8D6FE', '#cccccc');
									// -->
									</script>
								</td>
							</tr>
						</table>
						<!-- END TABLE 3 -->
				</td>
			</tr>
		</table>
		<!-- END TABLE 2 -->
		<!--Table with thin cell border ends-->
	<? table_end_percent(); ?>
		</td></tr></table>
	</td>
</tr>

<?php
  include('bottom.php');
?>
