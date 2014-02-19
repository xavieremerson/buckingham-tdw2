<title>Edit Employee Account</title>
<?
include('includes/dbconnect.php');
include('includes/global.php');
include('includes/functions.php');


////
// Creates a dropdown option values with recordset, but show selected value if present
function create_option_values_selected($data_query, $val = NULL) {
	$result = mysql_query($data_query) or die(mysql_error("Function create_option_values has errors"));
	while ($row = mysql_fetch_array($result)) {
		if ($row["d_value"] == $val) {
			echo '<option value="' . $row["d_value"] . '" selected="selected">' . $row["d_option"] . '</option>'."\n";
		} else {
			echo '<option value="' . $row["d_value"] . '">' . $row["d_option"] . '</option>'."\n";
		}
	}
}

////
// Converts YYYY-MM-DD to MM/DD/YYYY
function fdate_ymdtomdy ($date_input) {
	if ($date_input != '') {
		$date=explode("-",trim($date_input));
		return $date[1]."/".$date[2]."/".$date[0]; 
	} else {
		return NULL;
	}
}

////
// Converts  MM/DD/YYYY to YYYY-MM-DD
function fdate_mdytoymd ($date_input) {
	if ($date_input != '' && $date_input != '--/--/----') {
		$date=explode("/",trim($date_input));
		return $date[2]."-".$date[0]."-".$date[1]; 
	}	else {
		return NULL;
	}
}

if ($_POST && $d_emp_acct_deac) { //DEACTIVATE Employee Account

//print_r($_POST);
//exit;


//First create a copy of the current record to an audit table
$qry = "insert into emp_employee_accounts_master_audit SELECT NULL AS u_id, a.* FROM emp_employee_accounts_master a WHERE a.auto_id = '".$d_auto_id."'";
$result = mysql_query($qry) or die(tdw_mysql_error($qry));


//Clean some data (Megan Dahl issue with date_active)
if (fdate_mdytoymd($d_emp_acct_deac) == "") {
	$str_deac_on = 'NULL';
} else {
	$str_deac_on = "'".fdate_mdytoymd($d_emp_acct_deac)."'";
}


//Array ( [d_citta_date_deac] => 09/15/2011 [d_citta_comments] => xzczczczc [d_venteredby] => [d_uid] => 79 [d_auto_id] => 81 ) 
//then update the current record in the table
$qry = "update emp_employee_accounts_master 
				SET 
					emp_close_date = ".$str_deac_on.",
					emp_closed_by = '" . $d_uid . "',  
					emp_last_edit_time = now(), 
					emp_acct_status = 0, 
					emp_comments = '" . str_replace("'","\\'",$d_emp_comments)  . "', 
					emp_last_edit_by = '" . $d_uid . "',  
					emp_last_edit_ip  = '".$_SERVER['REMOTE_ADDR']."' 
				WHERE auto_id = '".$d_auto_id."'";
	
	//xdebug("qry",$qry);
	//exit;

	if (trim($d_emp_comments) != "") {
		$result = mysql_query($qry);
		$d_status_message = '<font color="green">Employee Account deactivation successfully.</font>';
		$val_success = 2;
	} else {
		$d_status_message = '<font color="red">Employee Account deactivation failed. Please make sure you have entered a comment. Please try again or contact Technical Support.</font>';
		$val_success = 3;
	}
	
	$cid = $d_auto_id;
	$uid = $d_uid;

}




//=================================================================================================
//Process Submit/Save
if ($_POST && !$d_emp_acct_deac) {

//First create a copy of the current record to an audit table
$qry = "insert into emp_employee_accounts_master_audit SELECT NULL AS u_id, a.* FROM emp_employee_accounts_master a WHERE a.auto_id = '".$auto_id."'";
$result = mysql_query($qry) or die(tdw_mysql_error($qry));

//Then update the current record in the table.

//Clean some data (Megan Dahl issue with date_active)
if (fdate_mdytoymd($emp_establish_date) == "") {
	$str_active_since = 'NULL';
} else {
	$str_active_since = "'".fdate_mdytoymd($emp_establish_date)."'";
}

$qry = "UPDATE emp_employee_accounts_master 
				SET 
					emp_user_id = '".$emp_user_id."',
					emp_acct_number = '".str_replace("'","\\'",$emp_acct_number)."',
					emp_name_and_address_1 = '".str_replace("'","\\'",$emp_name_and_address_1)."',
					emp_name_and_address_2 = '".str_replace("'","\\'",$emp_name_and_address_2)."',
					emp_name_and_address_3 = '".str_replace("'","\\'",$emp_name_and_address_3)."',
					emp_name_and_address_4 = '".str_replace("'","\\'",$emp_name_and_address_4)."',
					emp_name_and_address_5 = '".str_replace("'","\\'",$emp_name_and_address_5)."',
					emp_name_and_address_6 = '".str_replace("'","\\'",$emp_name_and_address_6)."',
					emp_establish_date = ".$str_active_since.",
					emp_acct_status = '1',
					emp_comments = '".str_replace("'","\\'",$emp_comments)."',
					emp_close_date = NULL,
					emp_closed_by = NULL,
					emp_last_edit_by = '".$uid."',
					emp_last_edit_time = now(),
					emp_last_edit_ip = '".$_SERVER['REMOTE_ADDR']."' 
				 WHERE auto_id = '".$auto_id."'";

	$result = mysql_query($qry); // or die(tdw_mysql_error($qry));

	if ($result) {
		$status_message = '<font color="green">Account Information updated successfully.</font>';
		$val_success = 1;
	} else {
		$status_message = '<font color="red">Account Information update failed. Please check if you have selected Employee. Please try again or contact Technical Support.</font>';
		$val_success = 0;
	}
	
	$cid = $auto_id;
} 
//=================================================================================================

if (!$cid) {
	echo "Illegal operation performed!";
	exit;
} else {
//Populated the edit form
      $qry = "select 
								auto_id,
								emp_user_id,
								emp_acct_number,
								emp_name_and_address_1,
								emp_name_and_address_2,
								emp_name_and_address_3,
								emp_name_and_address_4,
								emp_name_and_address_5,
								emp_name_and_address_6,
								emp_establish_date,
								emp_acct_status,
								emp_comments,
								emp_close_date,
								emp_closed_by,
								emp_last_edit_by,
								emp_last_edit_time,
								emp_last_edit_ip							
							FROM emp_employee_accounts_master where auto_id = '".$cid."'";
							
				$result = mysql_query($qry) or die(tdw_mysql_error($qry));
				while ($row = mysql_fetch_array($result)) {
					$auto_id = $row['auto_id'];
					$emp_user_id = $row['emp_user_id'];
					$emp_acct_number = $row['emp_acct_number'];
					$emp_name_and_address_1 = $row['emp_name_and_address_1'];
					$emp_name_and_address_2 = $row['emp_name_and_address_2'];
					$emp_name_and_address_3 = $row['emp_name_and_address_3'];
					$emp_name_and_address_4 = $row['emp_name_and_address_4'];
					$emp_name_and_address_5 = $row['emp_name_and_address_5'];
					$emp_name_and_address_6 = $row['emp_name_and_address_6'];
					$emp_establish_date = $row['emp_establish_date'];
					$emp_acct_status = $row['emp_acct_status'];
					$emp_comments = $row['emp_comments'];
					$emp_close_date = $row['emp_close_date'];
					$emp_closed_by = $row['emp_closed_by'];
					$emp_last_edit_by = $row['emp_last_edit_by'];
					$emp_last_edit_time = $row['emp_last_edit_time'];
					$emp_last_edit_ip = $row['emp_last_edit_ip'];
				}
}

?>
<script language="JavaScript" src="includes/js/popup.js"></script>
<script language="JavaScript" src="includes/js/ajax_tbx.js"></script>
<script language="javascript" src="includes/prototype/prototype.js"></script>
<script language ="Javascript">
<!--

function setFocus(nextid) {
  document.getElementById(nextid).focus();
}


function processFormValues(){
	//var params_val = allitems;
	//alert(params_val);
	//return false;
	document.forms['empacclist'].submit();
	//showDetail(params_val);
}

function d_processFormValues(){
	document.forms['empacct_deac'].submit();
}

function processDeactivate() {

	if ($("chk_deactivate").checked==false) {
		$("sec_deactivate").style.visibility = "hidden";
		$("sec_deactivate").style.display = "none";
		$("sec_edit").style.visibility = "visible";
		$("sec_edit").style.display = "block";
	} else {
		$("sec_deactivate").style.visibility = "visible";
		$("sec_deactivate").style.display = "block";
		$("sec_edit").style.visibility = "hidden";
		$("sec_edit").style.display = "none";
	}
}

function processClose() {
top.opener.location.reload();
self.close();
}

-->
</script>

<link rel="stylesheet" type="text/css" href="includes/styles.css">
<style type="text/css">
<!--
.txt_status { 	font-family: Arial, Helvetica, sans-serif;	font-size: 10px;	color: #0000FF; }
.txt_statusx {
	background-color: #FFFFFF; 	border: thin dotted #0000FF;
}
-->
</style>
<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0" onLoad="setFocus('citta_fund_name');"><!-- showDetail('');-->

    <SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
    <SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
    <SCRIPT LANGUAGE="JavaScript">
      var calfrom = new CalendarPopup("divfrom");
      calfrom.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
      var calto = new CalendarPopup("divto");
      calto.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
			var caldeac = new CalendarPopup("divdeac");
      caldeac.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
    </SCRIPT>						


<?
tsp(100, "Employee Account (Edit or Deactivate)");
?>
  
	<?
	if ($val_success != 2) {
 	?>

 <table width="100%" style="background-color: #FFFFFF; 	border: thin dotted #0000FF;">
 <tr><td class="ilt"><input type="checkbox" value="D" name="chk_deactivate" id="chk_deactivate" onClick="processDeactivate();" <? if($val_success == 3) { echo " checked "; }  ?>         >&nbsp;&nbsp;&nbsp;DEACTIVATE<br>
 <font color="red" style="font-family:Arial; font-size:9px; font-weight:bold">&nbsp;&nbsp;Note: You may not deactivate and simultaneously edit any item shown below.</font></td></tr>
 </table>
	<?
	}
	?>
 
	<?
	if ($val_success != 2 && $val_success != 3) {
 	?>
 
  <div id="sec_edit" style="visibility:visible; display:block">

				 <form id="empacclist" name="empacclist" action="<?=$PHP_SELF?>" method="post">
					<table border="0">
						<tr>
							<td colspan="3"><?=$str_status?>
							</td>
						</tr>
          </table>
          
																<SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
																<SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
																	<SCRIPT LANGUAGE="JavaScript">
																	var calfrom = new CalendarPopup("divfrom");
																	calfrom.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
																	var calto = new CalendarPopup("divto");
																	calto.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
																</SCRIPT>						

          <table border="0" cellpadding="0" cellspacing="0"><tr class="ilt">
          <td align="left" width="231">Employee</td>
          <td align="left" width="231">Account Number</td>
          <td align="left" width="231">Establish Date</td>
					<td>&nbsp;</td>
          </tr>
					<tr class="ilt">
          <td align="left" width="100">
            <select name="emp_user_id" id="emp_user_id" size="1">
              <option value="">Select Employee</option> 
              <?=create_option_values_selected("select ID as d_value, Fullname as d_option from users order by Fullname", $emp_user_id)?> <? // where user_isactive = 1  SCOTT BRUNNER INACTIVE BUT ACCT. ACTIVE  ?>
            </select>
          </td>
          <td width="201" align="left"><input class="text" name="emp_acct_number" id="emp_acct_number" type="text" value="<?=$emp_acct_number?>" size="20"/></td>
          <td nowrap><input type="text" id="emp_establish_date" class="Text1" name="emp_establish_date" size="12" maxlength="12" value="<?=date('m/d/Y', strtotime($emp_establish_date))?>">
          <A HREF="#" onClick="calfrom.select(document.forms['empacclist'].emp_establish_date,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A>
          </td>
					<td>&nbsp;</td>
          </tr>
          </table>

          <table border="0" cellpadding="0" cellspacing="0">
          <tr class="ilt">
          <td align="left" width="231">Name Address Line 1</td>
          <td align="left" width="231">Name Address Line 2</td>
          <td align="left" width="231">Name Address Line 3</td>
          </tr>
          <tr>
          <td align="left"><input class="text" name="emp_name_and_address_1" id="emp_name_and_address_1" type="text" value="<?=$emp_name_and_address_1?>" size="36" /></td>
          <td align="left"><input class="text" name="emp_name_and_address_2" id="emp_name_and_address_2" type="text" value="<?=$emp_name_and_address_2?>" size="36" /></td>
          <td align="left"><input class="text" name="emp_name_and_address_3" id="emp_name_and_address_3" type="text" value="<?=$emp_name_and_address_3?>" size="36" /></td>
          </tr>
          </table>
          
          <table border="0" cellpadding="0" cellspacing="0">
          <tr class="ilt">
          <td align="left" width="231">Name Address Line 4</td>
          <td align="left" width="231">Name Address Line 5</td>
          <td align="left" width="231">Name Address Line 6</td>
          </tr>
          <tr>
          <td align="left"><input class="text" name="emp_name_and_address_4" id="emp_name_and_address_4" type="text" value="<?=$emp_name_and_address_4?>" size="36" /></td>
          <td align="left"><input class="text" name="emp_name_and_address_5" id="emp_name_and_address_5" type="text" value="<?=$emp_name_and_address_5?>" size="36" /></td>
          <td align="left"><input class="text" name="emp_name_and_address_6" id="emp_name_and_address_6" type="text" value="<?=$emp_name_and_address_6?>" size="36" /></td>
          </tr>
          </table>

          <table border="0" cellpadding="0" cellspacing="0">
          <tr class="ilt">
          <td align="left" width="100">Comments</td>
          </tr>
          <tr>
          <td align="left"><textarea name="emp_comments" id="emp_comments" rows="3" cols="88"/><?=$emp_comments?></textarea></td>
          </tr>
          </table>

          <table width="100%" border="0">
            <tr>
              <td class="ilt" colspan="2" align="left">Entered By: <?=get_user_by_id($uid)?></td>
            </tr>
            <tr>
              <td>
              <?
              if ($val_success == 1) {
              ?>
              <input name="Close" id="Close" type="button" onClick="processClose()" value="&nbsp;&nbsp;&nbsp;CLOSE&nbsp;&nbsp;&nbsp;">              
              <?
              } else {
              ?>
              <input name="Submit" id="Submit" type="button" onClick="processFormValues()" value="&nbsp;&nbsp;&nbsp;SAVE&nbsp;&nbsp;&nbsp;">&nbsp;&nbsp;<input type="reset" value="REVERT FORM">
              <?
              }
              ?>
              </td>
              <td>&nbsp;<?=$status_message?></td>
             </tr>  
          </table>  
					<input type="hidden" name="venteredby" value="<?=$uid?>">
					<input type="hidden" name="uid" value="<?=$uid?>">
    			<input type="hidden" name="auto_id" id="auto_id" value="<?=$auto_id?>" />
					</form>

  </div>
	<?
	}
	?>

	<?
	if ($val_success != 2  && $val_success != 3) {
 	?>
   <div id="sec_deactivate" style="visibility:hidden; display:none">
	<?
	} else {
	?>
   <div id="sec_deactivate">
	<?
	}
	?>

   <form id="empacct_deac" name="empacct_deac" action="<?=$PHP_SELF?>" method="post">

	<?
	if ($val_success != 2) {
 	?>
		<table><tr>
    <td class="ilt">Deactivate As Of</td>
    <td><input type="text" id="d_emp_acct_deac" class="Text1" name="d_emp_acct_deac" size="12" maxlength="12" value="<?=date('m/d/Y')?>">&nbsp;&nbsp;
    <A HREF="#" onClick="caldeac.select(document.forms['empacct_deac'].d_emp_acct_deac,'anchor3','MM/dd/yyyy'); return false;" NAME="anchor3" ID="anchor3"><img src="images/lf_v1/sel_date.png" border="0"></A>
    </td>
    </tr>
    </table><br>
    <table border="0" cellpadding="0" cellspacing="0">
    <tr class="ilt">
    <td align="left">Comments</td>
    </tr>
    <tr class="ilt">
    <td align="left"><textarea name="d_emp_comments" id="d_emp_comments" rows="3" cols="88"/></textarea></td>
    </tr>
    </table>
	<?
	}
	?>
    <table width="100%" border="0">
      <tr>
        <td class="ilt" colspan="2" align="left">Entered By: <?=get_user_by_id($uid)?></td>
      </tr>
      <tr>
        <td>
        <?
        if ($val_success == 2) {
        ?>
        <input name="Close" id="Close" type="button" onClick="processClose()" value="&nbsp;&nbsp;&nbsp;CLOSE&nbsp;&nbsp;&nbsp;">              
        <?
        } else {
        ?>
        <input name="Submit" id="Submit" type="button" onClick="d_processFormValues()" value="   SAVE   ">&nbsp;&nbsp;<input type="reset" value="REVERT FORM">
        <?
        }
        ?>
        </td>
        <td>&nbsp;<?=$d_status_message?></td>
       </tr>  
    </table>  
    <input type="hidden" name="d_uid" value="<?=$uid?>">
    <input type="hidden" name="d_auto_id" id="d_auto_id" value="<?=$auto_id?>" />
		</form>
 </div> 

<?
tep();
?>
	<DIV ID="divfrom" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
	<DIV ID="divto" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>			
	<DIV ID="divdeac" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>			
</body>