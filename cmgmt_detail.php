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
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// Create some lookup stuff
$arr_initial_to_name = array();
$qry_initial_to_name = "SELECT Initials, concat(Lastname, ', ', SUBSTRING(Firstname,1,1),'.') as user_good_name FROM users where length(Initials) = 2";
$result_initial_to_name = mysql_query($qry_initial_to_name) or die (tdw_mysql_error($qry_initial_to_name));
while($row_initial_to_name = mysql_fetch_array($result_initial_to_name)) {
	$arr_initial_to_name[$row_initial_to_name["Initials"]] = $row_initial_to_name["user_good_name"];
}

////
// Show Names for Initials
function show_names_for_initials ($initials, $arr_initial_to_name) {
	return $arr_initial_to_name[$initials];
}

$arr_idnum_to_name = array();
$qry_idnum_to_name = "SELECT ID, concat(Lastname, ', ', SUBSTRING(Firstname,1,1),'.') as user_good_name FROM users where length(Initials) = 2";
$result_idnum_to_name = mysql_query($qry_idnum_to_name) or die (tdw_mysql_error($qry_idnum_to_name));
while($row_idnum_to_name = mysql_fetch_array($result_idnum_to_name)) {
	$arr_idnum_to_name[$row_idnum_to_name["ID"]] = $row_idnum_to_name["user_good_name"];
}

////
// Show Names for ID
function show_names_for_idnum ($id, $arr_idnum_to_name) {
	return $arr_idnum_to_name[$id];
}

//show_array($arr_initial_to_name);
//exit;

//clnt_auto_id  clnt_default_payout  clnt_special_payout_rate  clnt_start_month  clnt_default_n_months  clnt_name  clnt_timestamp  clnt_isactive  
$arr_clnt_details = array();
$qry_clnt_details = "SELECT 
											 clnt_auto_id,
											 clnt_default_payout,
											 clnt_special_payout_rate,
											 clnt_start_month,
											 clnt_default_n_months,
											 clnt_name,
											 clnt_timestamp,
											 clnt_isactive
										 FROM int_clnt_payout_rate 
										 WHERE clnt_isactive = 1";
$result_clnt_details = mysql_query($qry_clnt_details) or die (tdw_mysql_error($qry_clnt_details));
while($row_clnt_details = mysql_fetch_array($result_clnt_details)) {
  $str_0 = "";
	if ($row_clnt_details["clnt_default_n_months"] == 1)  {
	  $str_0 = "<img src='images/check_yes.png' border='0'>";
	} else {
	  $str_0 = "<img src='images/check_no.png' border='0'>";
	}
	$str_1 = "";
	$str_2 = "";
	//array has to be 0=n-mos, 1=def-pay, 2=payout rates
	if ($row_clnt_details["clnt_default_payout"] == 0) {
	  $str_1 = "<img src='images/check_no.png' border='0'>";
	  $arr_temp = explode("^",$row_clnt_details["clnt_special_payout_rate"]);
		$str_2 = $arr_idnum_to_name[$arr_temp[0]]. " (".$arr_temp[1]."%)";
	} elseif ($row_clnt_details["clnt_default_payout"] == 1) {
	  $str_1 = "<img src='images/check_yes.png' border='0'>";
	  $str_2 = "";
	} elseif ($row_clnt_details["clnt_default_payout"] == 2)  {
	  $str_1 = "<img src='images/check_no.png' border='0'>";

	  $arr_temp = explode("#",$row_clnt_details["clnt_special_payout_rate"]);
		
		$arr_temp_a = explode("^",$arr_temp[0]);
		$arr_temp_b = explode("^",$arr_temp[1]);
		
		$str_2 = $arr_idnum_to_name[$arr_temp_a[0]]. " (".$arr_temp_a[1]."%)". "<b> / </b>" . $arr_idnum_to_name[$arr_temp_b[0]]. " (".$arr_temp_b[1]."%)";

	} else {
	  $str_1 = "[ERROR]";
	  $str_2 = "[ERROR]";
	}
	$arr_clnt_details[$row_clnt_details["clnt_auto_id"]] = $str_0."^".$str_1."^".$str_2;
}

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
///////////////////////////////////////////////  START OF MANAGE SECTION  //////////////////////////////////////
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
?>
		&nbsp;&nbsp;&#9658;<a class="ilt" href="cmgmt_export_excel.php" target="_blank"">Export to Excel</a>		
		<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
					<!--TABLE 2 START-->
					<script language="JavaScript" src="includes/javascript/sorttable.js" type="text/javascript"></script>

					<table class="sortable" preserve_style="cell" width="100%"  border="0" cellspacing="1" cellpadding="1">
						<tr>
						  <td width="20">DEL</td>
							<td width="20">EDIT</td>
							<td width="200">Name</td>
							<td width="40">Code</td>
							<td width="80">Tware</td>
							<td width="110">RR1</td> <!--First Initial, Lastname-->
							<td width="110">RR2</td>
							<td width="110">Trader</td>
							<td width="60">n-mos</td>
							<td width="80">def-pay</td>
							<td width="100">rate</td>
							<td>&nbsp;</td>
						</tr>
						
							<script type="text/javascript">
							var dc = new Array()
				
							<? 

						$query_clients = "SELECT * from int_clnt_clients where clnt_isactive = '1' order by clnt_name";
						//echo $query_trades;
						$result = mysql_query($query_clients) or die(mysql_error());
						$count_row = 0;
						while ( $row = mysql_fetch_array($result) ) 
						{
						
						if ($row["clnt_alt_code"]=='INACTIVE') {
						$str_clnt_code = "<font color=red>".$row["clnt_alt_code"].'</font>';
						} else {
						$str_clnt_code = $row["clnt_alt_code"];
						}
						
						$arr_specials = explode("^",$arr_clnt_details[$row["clnt_auto_id"]]);
						
						echo 'dc ['.$count_row.'] = "'.$row["clnt_auto_id"].'^'.
																					trim($row["clnt_name"]).'^'.
																					trim(str_replace("'","",$row["clnt_name"])).'^'.
																					$row["clnt_code"].'^'.
																					$str_clnt_code.'^'.
																					show_names_for_initials($row["clnt_rr1"],$arr_initial_to_name).'^'.
																					show_names_for_initials($row["clnt_rr2"],$arr_initial_to_name).'^'.
																					show_names_for_initials($row["clnt_trader"],$arr_initial_to_name).'^'.
																					$arr_specials[0].'^'.
																					$arr_specials[1].'^'.
																					$arr_specials[2].'"'.";\n";						
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
							
							document.write(
									"<tr" + rowclass + ">"+
									"<td nowrap>&nbsp; <a href=\"cmgmt.php?type=manage&action=remove&ID="+rowclients_array[0]+"\"  onclick=\"javascript:return confirm('Are you sure you want to remove "+rowclients_array[2]+" from the list?')\"><img src=\"images/themes/standard/delete.gif\" alt=\"Delete\"></a>&nbsp; </td>"+
									"<td nowrap>&nbsp; <a href=\"javascript:CreateWnd(\'cmgmt_edit.php?ID="+rowclients_array[0]+"', 550, 450, false);\"><img src=\"images/themes/standard/edit.gif\" alt=\"Edit\"></a>&nbsp; </td>"+
									"<td>&nbsp;"+rowclients_array[1]+"</td>"+ 
									"<td>&nbsp;"+rowclients_array[3]+"</td>"+ 
									"<td>&nbsp;"+rowclients_array[4]+"</td>"+ 
									"<td>&nbsp;"+rowclients_array[5]+"</td>"+ 
									"<td>&nbsp;"+rowclients_array[6]+"</td>"+ 
									"<td>&nbsp;"+rowclients_array[7]+"</td>"+ 
									"<td>&nbsp;"+rowclients_array[8]+"</td>"+ 
									"<td>&nbsp;"+rowclients_array[9]+"</td>"+ 
									"<td>&nbsp;"+rowclients_array[10]+"</td>"+ 
									"<td></td></tr>");							
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