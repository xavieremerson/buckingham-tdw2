<title>Edit Client</title>
<script language="Javascript" SRC="includes/js/javascript.js"></script>
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

tsp(100, "Edit Check Payment");
			
$payment_type = array();

$payment_type[1] = "Research - Research";
$payment_type[2] = "Research - Independent";
$payment_type[3] = "Research - Geneva";
$payment_type[4] = "Broker-to-Broker";
$payment_type[5] = "Trading 2";
$payment_type[6] = "Other";

//show_array($_POST);

//START OF IF 1
if($editPayment)
{
		//Client Name AND Client Code ERROR CHECKING
	$array    = array();
	$test_name = array();
	$test_name[1] = "Client Code cannot be blank.";
	$test_name[2] = "Amount entered is invalid.";
	$test_name[3] = "Date is invalid";
	$test_name[4] = "Extra condition... not programmed yet.";

	if($code == "") 
	{
		$array[1] = "0";
		$code_blank = "0";
	}  
	else 
	{
		$array[1] = "1";
		$code_blank = "1";
	}

	if($amount == "" or !$amount > 0) 
	{
		$array[2] = "0";
		$amount_blank = "0";
	}  
	else 
	{
		$array[2] = "1";
		$amount_blank = "1";
	}

//show_array($array);
//exit;

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
	// NO ERRORS FOUND, HENCE UPDATE DATA IN TABLE
	else
	{
		$query_edit = "UPDATE chk_chek_payments_etc
									 SET chek_amount = '".$amount."',
											 chek_type = '".$checktype."',
											 chek_advisor = '".$code."',
											 chek_comments = '".$comments."',
											 chek_date = '".format_date_mdy_to_ymd($checkdate)."',
											 chek_entered_by = '".$user_id."',
											 chek_entered_datetime = now() 
										WHERE auto_id = ".$ID." LIMIT 1";

		//debug("query_edit",$query_edit);
		$result_edit = mysql_query($query_edit) or die (tdw_mysql_error($query_edit));

		//<!-- showmsg success -->
		showmsg(1, "Check from ".$code." in the amount of ".$amount." updated successfully.");

 	} // END OF UPDATING DATA IN TABLE
} // END OF IF 1


    //show_array($_POST);
    $qry_get_check = "SELECT a.*, b.Fullname, c.clnt_name 
																from chk_chek_payments_etc a, 
                                     Users b, 
                                     int_clnt_clients c 
                                where a.chek_entered_by = b.ID 
                                  and a.chek_advisor = c.clnt_code
																	and a.chek_isactive = 1
                                  and a.auto_id = ".$ID;
	  //xdebug("qry_get_check",$qry_get_check);
		$result_get_check = mysql_query($qry_get_check) or die (tdw_mysql_error($qry_get_check));
		while ( $row_get_check = mysql_fetch_array($result_get_check) ) 
		{
			$code = $row_get_check["chek_advisor"];
			$amount = $row_get_check["chek_amount"];
			$comments = $row_get_check["chek_comments"];
			$checkdate = $row_get_check["chek_date"];
			$checktype = $row_get_check["chek_type"];
			$lasteditby = $row_get_check["Fullname"];
			$lastedittime = $row_get_check["chek_entered_datetime"];
			$editinguserid = $row_get_check["chek_entered_by"];
		}
		
?>
		<!-- 'CREATE' FIELDS TABLE -->
		<table cellpadding="2" cellspacing="0" border="0" height="100%" width="100%">  
			<form name="checkedit" id="checkedit" action="<?=$php_self?>" method="post"> 
			<tr>
				<td> 
					<table>
						<tr valign="top">
							<td colspan="2">&nbsp;</td>
						</tr>
						<tr valign="top">
							<td class="ilt">Client Code :</td>
							<td><input class="Text" name="code" type="text" value="<?=$code?>" size="30" maxlength="40"><font color="#FF0000">*</font></td>
						</tr>
						<tr valign="top">
							<td class="ilt">Payment Type :</td>
							<td>
						  <select class="Text" name="checktype"> 
							<?
								foreach($payment_type as $key=>$value) {
								  if ($key == $checktype) {
									?>
									<option value="<?=$key?>" selected><?=$value?></option>
									<?
									} else {
									?>
									<option value="<?=$key?>"><?=$value?></option>
									<?
									}
							 	}
							?>
							</select>
							</td>
						</tr>
						<tr valign="top">
							<td class="ilt">Amount :</td>
							<td><input class="Text" name="amount" type="text" value="<?=$amount?>" size="20" maxlength="10"><font color="#FF0000">*</font></td>
						</tr> 
						<tr valign="top">
							<td class="ilt">Date :</td>
							<td>
								<SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
								<SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
								<SCRIPT LANGUAGE="JavaScript">
								var calcheckdate = new CalendarPopup("divcheckdate");
								</SCRIPT>	
								<input type="text" id="checkdate" class="Text1" name="checkdate" readonly size="12" maxlength="12" value="<?=format_date_ymd_to_mdy($checkdate)?>">															
							<A HREF="#" onClick="calcheckdate.select(document.forms['checkedit'].checkdate,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A><font color="#FF0000">*</font></td>
							</td>
						</tr>
						<tr valign="top">
							<td class="ilt">Comments :</td>
							<td><input class="Text" name="comments" type="text" value="<?=$comments?>" size="50" maxlength="60"><font color="#FF0000"></font></td>
							</td>
						</tr>
						<tr valign="top">
							<td colspan="2" class="ilt">Last entered/updated by <?=$lasteditby?>.</td>
						</tr>						
						<tr valign="top">
							<td colspan="2" align="center"><p class="Contact"><br />Fields marked with an asterisk <font color="#FF0000">*</font> are required</p></td></tr>
						<tr valign="top">
							<td colspan="2" align="center"><p>
								<input type="hidden" name="user_id" value="<?=$user_id?>" />
								<input class="Submit" type="submit" name="editPayment" value="Update"></p>
							</td>
						</tr>  
					</table>
				</td>
			</tr> 
			</form>
		</table>
		<DIV ID="divcheckdate" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
<?
		tep();
/////////////////////////////////////////////////END OF EDIT SECTION/////////////////////////////////////////////////
?>
