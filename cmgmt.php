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
			
<?
///////////////////////////////////////////START OF CREATE SECTION//////////////////////////////////
 if($type == "create")
    {
		echo "<center>";
		echo tsp(100,"Add New Client");  
		echo "<br>";
			
		//START OF IF 1
		if($createClient)
		{
					//Client Name AND Client Code ERROR CHECKING
				$array    = array();
				$test_name = array();
				$test_name[1] = "Client Name cannot be blank.";
				$test_name[2] = "The Client Name entered is invalid.";
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
				if((ord($cname) > 47 and ord($cname) < 58) or (ord($cname) > 64 and ord($cname) < 91) or (ord($cname) > 96 and ord($cname) < 123)) 
				{
					$array[2] = "1";
					$cname_first = "1";
				} 
				else  
				{
					$array[2] = "0";
					$cname_first = "0";
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
				if((ord($code) > 47 and ord($code) < 58) or (ord($code) > 64 and ord($code) < 91) or (ord($code) > 96 and ord($code) < 123)) 
				{
					$array[4] = "1";
					$code_first = "1";
				} 
				else  
				{
					$array[4] = "0";
					$code_first = "0";
				}

?> 
				<!-- ERROR DISPLAY TABLE -->
				<table cellspacing="0" cellpadding="5">
					<tr> 
						<td class="errnote"><?php
									for($x = 1; $x < 5; $x++)
									{
										if($array[$x] == "0") 
										{
											echo "&nbsp;&nbsp;".$test_name[$x]."<br>";
										} 
									}
								?></td>
					</tr>
				</table>
<?
            // ERRORS FOUND IN INPUT
			if($array[1] == "0" OR $array[2] == "0" OR $array[3] == "0" OR $array[4] == "0") 
			{
			?>
				<a class="red9">&nbsp;&nbsp;There are one or more invalid or incomplete fields. Please resolve this problem and re-submit the data.</a>
			<?   
			}
			// NO ERRORS FOUND, HENCE INSERT DATA IN TABLE
			else
			{
				//INSERT USER DATA
				$query_create = "INSERT INTO
												 int_clnt_clients(
													 clnt_name, 
													 clnt_code) 
												 VALUES('"
												 .$cname."','"
												 .$code."')";
				//xdebug("query_create",$query_create);
				$result_create = mysql_query($query_create) or die (mysql_error());
				
				$query_id = "SELECT max(clnt_auto_id) as ID FROM int_clnt_clients";
				$result_id = mysql_query($query_id) or die(mysql_error());
				$row_id = mysql_fetch_array($result_id);
				
				$mailsubject  = "Client Account (".$cname.") created in TDW Buckingham";
				$emailheading = "TDW Client";
				$mailbody     = '<font color = "#000080" family = "Verdana,Arial,Helvetica">'.$cname.': <br><br><br>';
				$mailbody    .= 'Your client account has been created in <b>'.$_app_name.'</b>.<br><br>Your Password: <b>'.$pass.'</b><br><br>';
				$mailbody    .= 'Click on the link to launch <a href="'.$_site_url.'">'.$_app_name.'</a></font>';
				
				$html_body .= zSysMailHeader("");
				$html_body .= $mailbody;
				$html_body .= zSysMailFooter ();
				$subject = $mailsubject;
				$text_body = $subject;
				zSysMailer($email, $fullname, $subject, $html_body, $text_body, "") ;
				
				//html_emails_dynamic($email, $from, $mailsubject, $mailbody, $emailheading, $fileattach, gen_control_number());
	?>
				<!-- CREATE ACCOMPLISHED TABLE -->
				<table width="400" cellpadding="2" cellspacing="0" border="0">
					<tr valign="top">
						<td class="green11">&nbsp; User: <?=$cname?> added successfully.</td>
					</tr>
				</table>
	<?		
	    	} // END OF INSERTING DATA IN TABLE
		} // END OF IF 1

?>

		<!-- 'CREATE' FIELDS TABLE -->
		<table cellpadding="2" cellspacing="0" border="0" height="100%" width="70%">  
			<form action="<?=$php_self?>" method="post"> 
			<tr>
				<td> 
					<table>
					
											<tr valign="top">
							<td colspan="2"><hr align="left" width="400" size="1" noshade color="#0000FF"></td>
						</tr>
						<tr valign="top">
							<td class="ilt">Client Name :</td>
							<td><input class="Text" name="cname" type="text" value="<?=$cname?>" size="30" maxlength="40"><font color="#FF0000">*</font></td>
						</tr>
						<tr valign="top">
							<td class="ilt">Code :</td>
							<td><input class="Text" name="code" type="text" value="<?=$code?>" size="5" maxlength="10"><font color="#FF0000">*</font></td>
						</tr>
						<tr valign="top">
							<td colspan="2"><hr align="left" width="400" size="1" noshade color="#0000FF"></td>
						</tr>
						<tr valign="top">
							<td colspan="2" align="center"><p class="Contact">Fields marked with an asterisk <font color="#FF0000">*</font> are required</p></td></tr>
						<tr valign="top">
							<td colspan="2" align="center"><input class="Submit" type="submit" name="createClient" value="Create Client">
							</td>
						</tr>  
					</table>
				</td>
			</tr> 
			</form>
		</table>
<?
		tep();
		echo "</center>";
	} 
///////////////////////////////////////////////  END OF CREATE SECTION  //////////////////////////////////////////////////////////////

///////////////////////////////////////////////  START OF MANAGE SECTION  ////////////////////////////////////////////////////////////
	if($type == "manage")
    {
		echo "<center>";
	?>
	<? tsp(100, "Client Management"); ?>
	<?	
		if($action == "remove")
		{
			$query_delete = "UPDATE int_clnt_clients SET clnt_isactive = '0' WHERE clnt_auto_id = '$ID'";
			$result_delete = mysql_query($query_delete) or die(mysql_error());
		}

		if($action == "undelete")
		{
			$query_delete = "UPDATE int_clnt_clients SET clnt_isactive = '1' WHERE clnt_auto_id = '$ID'";
			$result_delete = mysql_query($query_delete) or die(mysql_error());
		}

?>
		<table width="100%" border="0" cellpadding="1", cellspacing="0"><tr> 
    <td width="160" class="quotes">
    <form name="udel" method="get" action="<?=$PHP_SELF?>">
    <input name="show_deleted" type="checkbox" value="1" <? if($show_deleted) {echo " checked";}?> /> Show Deleted Clients
    <input type="hidden" name="ID" value="<?=$ID?>" />
    <input type="hidden" name="strltr" value="<?=$strltr?>" />
    <input type="hidden" name="type" value="manage" />
    </td>
    <td width="40" align="left">
    <input type="submit" name="GO" value=" GO " /> 
		</td>
    </form>
    <td width="160"><a class="links11">&nbsp;&nbsp;Filter by Client Name: </a></td>
    <td width="650">
		<?
		$str_get_link = "";
		if ($show_deleted == 1) {
		$str_get_link = "&show_deleted=1";
		} else {
		$str_get_link = "";
		}

		
		$qry = "SELECT count(clnt_name) as xcount , substring(clnt_name, 1, 1 ) as strltr
				FROM int_clnt_clients 
				WHERE clnt_isactive =1
				GROUP BY substring(clnt_name, 1, 1 )";
		$result = mysql_query($qry) or die(tdw_mysql_error($qry));
		
		
		echo '<a class="links11" href="cmgmt.php?type=manage&strltr='.$str_get_link.'"> '."[SHOW ALL]".' </a>&nbsp;';
		
		while ( $row = mysql_fetch_array($result) )	{
			echo '<a class="links11" href="cmgmt.php?type=manage&strltr='.$row["strltr"].$str_get_link.'"> '.$row["strltr"].' </a>&nbsp;';
		}
		?>
    </td>
    <td width="150">&#9658;<a class="ilt" href="cmgmt_export_excel.php" target="_blank">Export to Excel</a></td>
    <td>&nbsp;</td>
		</tr></table>
		<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
					<!--TABLE 2 START-->
					<script language="JavaScript" src="includes/javascript/sorttable.js" type="text/javascript"></script>

					<table class="sortable" preserve_style="cell" width="100%"  border="0" cellspacing="1" cellpadding="1">
						<tr>
						  <td width="28">DEL</td>
							<td width="30">EDIT</td>
							<td width="35">UnDEL</td>
							<td width="250">Name</td>
							<td width="80">Code</td>
							<td width="200">Tradeware Code</td>
							<td width="80">RR1</td>
							<td width="80">RR2</td>
							<td width="80">Trader</td>
							<td>&nbsp;</td>
						</tr>
						
							<script type="text/javascript">
							var dc = new Array()
				
						<? 
						if ($show_deleted == 1) {
						  $str_show_deleted = " clnt_isactive like '%' ";
						} else {
						  $str_show_deleted = " clnt_isactive = '1' ";
						}

						$query_clients = "SELECT * from int_clnt_clients where ".$str_show_deleted." and clnt_name like '".$strltr."%' order by clnt_name";
						//echo $query_trades;
						
						$str_get_append = "";
						if ($show_deleted == 1) {
						$str_get_append = "&show_deleted=1&strltr=".$strltr;
						} else {
						$str_get_append = "&strltr=".$strltr;
						}
						
						
						$result = mysql_query($query_clients) or die(mysql_error());
						$count_row = 0;
						while ( $row = mysql_fetch_array($result) ) 
						{
						
						if ($row["clnt_alt_code"]=='INACTIVE') {
						$str_clnt_code = "<font color=red>".$row["clnt_alt_code"].'</font>';
						} else {
						$str_clnt_code = $row["clnt_alt_code"];
						}

						echo 'dc ['.$count_row.'] = "'.$row["clnt_auto_id"].'^'.
																									trim($row["clnt_name"]).'^'.
																									trim(str_replace("'","",$row["clnt_name"])).'^'.
																									$row["clnt_code"].'^'.
																									$str_clnt_code.'^'.
																									$row["clnt_rr1"].'^'.
																									$row["clnt_rr2"].'^'.
																									$row["clnt_trader"].'^'.
																									$row["clnt_isactive"].'^'.
																									$str_get_append.'"'.";\n";
						
						$count_row = $count_row + 1;
						}
						?>
							for (i=0;i<dc.length;i++)
							{
									var rowclients_array = new Array()
									var rowclass
									if (i%2 == 0) {
										rowclass = " class=\"trdark\"";
									} else {
										rowclass = " class=\"trlight\"";
									}
							
									rowclients_array=dc[i].split("^");
									
									if(rowclients_array[8]=="1") {
											document.write(
													"<tr" + rowclass + ">"+
													"<td nowrap>&nbsp; <a href=\"cmgmt.php?type=manage&action=remove&ID="+rowclients_array[0]+rowclients_array[9]+"\"  onclick=\"javascript:return confirm('Are you sure you want to remove "+rowclients_array[2]+" from the list?')\"><img src=\"images/themes/standard/delete.gif\" alt=\"Delete\"></a>&nbsp; </td>"+
													"<td nowrap>&nbsp; <a href=\"javascript:CreateWnd(\'cmgmt_edit.php?ID="+rowclients_array[0]+"', 550, 450, false);\"><img src=\"images/themes/standard/edit.gif\" alt=\"Edit\"></a>&nbsp; </td>"+
													"<td>"+" "+"</td>"+ 
													"<td>"+rowclients_array[1]+"</td>"+ 
													"<td>"+rowclients_array[3]+"</td>"+ 
													"<td>"+rowclients_array[4]+"</td>"+ 
													"<td>"+rowclients_array[5]+"</td>"+ 
													"<td>"+rowclients_array[6]+"</td>"+ 
													"<td>"+rowclients_array[7]+"</td>"+ 
													"<td></td></tr>");							
									} else {
											document.write(
													"<tr" + rowclass + ">"+
													"<td nowrap>&nbsp;</td>"+
													"<td nowrap>&nbsp;</td>"+
													"<td nowrap>&nbsp; <a href=\"cmgmt.php?type=manage&action=undelete&ID="+rowclients_array[0]+rowclients_array[9]+"\"  onclick=\"javascript:return confirm('Are you sure you want to ADD BACK "+rowclients_array[2]+" to the list?')\"><img src=\"images/plus14.png\" alt=\"Add Back\"></a>&nbsp; </td>"+
													"<td>"+rowclients_array[1]+"</td>"+ 
													"<td>"+rowclients_array[3]+"</td>"+ 
													"<td>"+rowclients_array[4]+"</td>"+ 
													"<td>"+rowclients_array[5]+"</td>"+ 
													"<td>"+rowclients_array[6]+"</td>"+ 
													"<td>"+rowclients_array[7]+"</td>"+ 
													"<td></td></tr>");							
									}
							}
							
							</script>

					</table>
				</td>
			</tr>
		</table>
	
		<? tep();
		
		echo "</center>";
	}  
/////////////////////////////////////////////////END OF DELETE SECTION/////////////////////////////////////////////////

/////////////////////////////////////////////////START OF EDIT SECTION////////////////////////////////////////////////
 if($type == "edit")
  {

			echo "<center>";
			tsp(100, "Edit Client");
			echo "<br>";
			
			//START OF IF 1
			if($editClient)
			{
					//Client Name AND Client Code ERROR CHECKING
				$array    = array();
				$test_name = array();
				$test_name[1] = "Client Name cannot be blank.";
				$test_name[2] = "The Client Name entered is invalid.";
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
				if((ord($cname) > 47 and ord($cname) < 58) or (ord($cname) > 64 and ord($cname) < 91) or (ord($cname) > 96 and ord($cname) < 123)) 
				{
					$array[2] = "1";
					$cname_first = "1";
				} 
				else  
				{
					$array[2] = "0";
					$cname_first = "0";
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
				if((ord($code) > 47 and ord($code) < 58) or (ord($code) > 64 and ord($code) < 91) or (ord($code) > 96 and ord($code) < 123)) 
				{
					$array[4] = "1";
					$code_first = "1";
				} 
				else  
				{
					$array[4] = "0";
					$code_first = "0";
				}			
?> 
				<!-- ERROR DISPLAY TABLE -->
				<table cellspacing="0" cellpadding="5">
					<tr> 
						<td class="errnote"><?php
									for($x = 1; $x < 8; $x++)
									{
										if($array[$x] == "0") 
										{
											echo "&nbsp;&nbsp;".$test_name[$x]."<br>";
										} 
									}
								?></td>
					</tr>
				</table>
<?
            // ERRORS FOUND IN INPUT
			if($array[1] == "0" OR $array[2] == "0" OR $array[3] == "0" OR $array[4] == "0") 
			{
			?>
				<a class="red9">&nbsp;&nbsp;There are one or more invalid or incomplete fields. Please resolve this problem and re-submit the data.</a>
			<?   
			}
			// NO ERRORS FOUND, HENCE INSERT DATA IN TABLE
			else
			{
				$query_edit = "UPDATE int_clnt_clients 
											SET clnt_name='".strtoupper($cname)."',
											    clnt_code='".strtoupper($code)."',
													clnt_alt_code='".strtoupper($altcode)."',
													clnt_rr1='".strtoupper($rr1)."',
													clnt_rr2='".strtoupper($rr2)."',
													clnt_trader='".strtoupper($trader)."'
											WHERE clnt_auto_id='$ID'";
				//xdebug("query_edit",$query_edit);
				$result_edit = mysql_query($query_edit) or die (tdw_mysql_error($query_edit));
	?>
				<!-- CREATE ACCOMPLISHED TABLE -->
				<table width="400" cellpadding="2" cellspacing="0" border="0">
					<tr valign="top">
						<td class="green11">&nbsp; Client (<?=$cname?>) updated successfully.</td>
					</tr>
				</table>
	<?		
	    	} // END OF INSERTING DATA IN TABLE
		} // END OF IF 1


    //show_array($_POST);
		$result_client = mysql_query("SELECT * FROM int_clnt_clients WHERE clnt_auto_id = '$ID'") or die (mysql_error());
		while ( $row_client = mysql_fetch_array($result_client) ) 
		{
			$cname = $row_client["clnt_name"];
			$code = $row_client["clnt_code"];
			$altcode = $row_client["clnt_alt_code"];
			$rr1 = $row_client["clnt_rr1"];
			$rr2 = $row_client["clnt_rr2"];
			$trader = $row_client["clnt_trader"];
		}
		
?>


		<!-- 'CREATE' FIELDS TABLE -->
		<table cellpadding="2" cellspacing="0" border="0" height="100%" width="70%">  
			<form action="<?=$php_self?>" method="post"> 
			<tr> 
				<td>  
					<table>
						<tr valign="top">
							<td colspan="2"><hr align="left" width="400" size="1" noshade color="#0000FF"></td>
						</tr>
						<tr valign="top">
							<td class="ilt">Client Name :</td>
							<td><input name="cname" type="text" class="Text" value="<?=$cname?>" size="30" maxlength="40" readonly="true" /><font color="#FF0000">*</font></td>
						</tr>
						<tr valign="top">
							<td class="ilt">Code :</td>
							<td><input class="Text" name="code"  readonly="true"  type="text" value="<?=$code?>" size="20" maxlength="10"><font color="#FF0000">*</font></td>
						</tr> 
						<tr valign="top">
							<td class="ilt">Tradeware Code :</td>
							<td><input class="Text" name="altcode" type="text" value="<?=$altcode?>" size="20" maxlength="10"><font color="#FF0000"></font></td>
						</tr>
						<tr valign="top">
							<td class="ilt">Sales Rep. 1 :</td>
							<td><input class="Text" name="rr1" type="text" value="<?=$rr1?>" size="5" maxlength="10"><font color="#FF0000"></font></td>
						</tr>
						<tr valign="top">
							<td class="ilt">Sales Rep. 2 :</td>
							<td><input class="Text" name="rr2" type="text" value="<?=$rr2?>" size="5" maxlength="10"><font color="#FF0000"></font></td>
						</tr>
						<tr valign="top">
							<td class="ilt">Trader :</td>
							<td><input class="Text" name="trader" type="text" value="<?=$trader?>" size="5" maxlength="10"><font color="#FF0000"></font></td>
						</tr>
						<tr valign="top">
							<td colspan="2"><hr align="left" width="400" size="1" noshade color="#0000FF"></td>
						</tr>						
						<tr valign="top">
							<td colspan="2" align="center"><p class="Contact">Fields marked with an asterisk <font color="#FF0000">*</font> are required</p></td></tr>
						<tr valign="top">
							<td colspan="2" align="center"><p>
								<a href="cmgmt.php?type=manage" class="ilt">Go Back</a>&nbsp;&nbsp;&nbsp;&nbsp;<input class="Submit" type="submit" name="editClient" value="Update"></p>
							</td>
						</tr>  
					</table>
				</td>
			</tr> 
			</form>
		</table>
<?
		tep();
	} 
/////////////////////////////////////////////////END OF EDIT SECTION/////////////////////////////////////////////////

  include('inc_footer.php');
?>
