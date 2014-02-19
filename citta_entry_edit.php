<title>Edit CITTA Record</title>
<?
include('includes/dbconnect.php');
include('includes/global.php');
include('includes/functions.php');

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

if ($_POST && $d_citta_date_deac) { //DEACTIVATE

//print_r($_POST);
//exit;


//First create a copy of the current record to an audit table
$qry = "insert into citta_list_audit SELECT NULL AS u_id, a.* FROM citta_list a WHERE a.auto_id = '".$d_auto_id."'";
$result = mysql_query($qry) or die(tdw_mysql_error($qry));

//Array ( [d_citta_date_deac] => 09/15/2011 [d_citta_comments] => xzczczczc [d_venteredby] => [d_uid] => 79 [d_auto_id] => 81 ) 
//then update the current record in the table
$qry = "update citta_list 
				SET
					 citta_deactivated_on = '" . fdate_mdytoymd($d_citta_date_deac)  . "', 
					 citta_deactivated_by = '" . $d_uid . "', 
					 citta_deactivated_datetime = now(), 
					 citta_comments = '" . str_replace("'","\\'",$d_citta_comments)  . "', 
					 citta_isactive = 0 
				 WHERE auto_id = '".$d_auto_id."'";
	
	if (trim($d_citta_comments) != "") {
		//xdebug("d_citta_comments",$d_citta_comments);
		$result = mysql_query($qry); // or die(tdw_mysql_error($qry));
	}
	
	if ($result && trim($d_citta_comments) != "") {
		$d_status_message = '<font color="green">Entry deactivation successfully.</font>';
		$val_success = 2;
	} else {
		$d_status_message = '<font color="red">Deactivation failed. Please make sure you have entered a comment. Please try again or contact Technical Support.</font>';
		$val_success = 3;
	}
	
	$cid = $d_auto_id;
	$uid = $d_uid;


}




//=================================================================================================
//Process Submit/Save
if ($_POST && !$d_citta_date_deac) {

//First create a copy of the current record to an audit table
$qry = "insert into citta_list_audit SELECT NULL AS u_id, a.* FROM citta_list a WHERE a.auto_id = '".$auto_id."'";
$result = mysql_query($qry) or die(tdw_mysql_error($qry));

//Then update the current record in the table.

//Clean some data (Megan Dahl issue with date_active)
if (fdate_mdytoymd($citta_active_since) == "") {
	$str_active_since = 'NULL';
} else {
	$str_active_since = "'".fdate_mdytoymd($citta_active_since)."'";
}

$qry = "update citta_list 
				SET
					 citta_fund_name = '" . $citta_fund_name  . "', 
					 citta_client_code = '" . $citta_client_code  . "', 
					 citta_account_name = '" . $citta_account_name  . "', 
					 citta_date_received = '" . fdate_mdytoymd($citta_date_received)  . "', 
					 citta_information_source = '" . $citta_information_source  . "', 
					 citta_update_information = '" . $citta_update_information  . "', 
					 citta_is_corporate_insider = '" . $citta_is_corporate_insider  . "', 
					 citta_company_name = '" . $citta_company_name  . "', 
					 citta_company_symbol = '" . $citta_company_symbol  . "', 
					 citta_company_person = '" . $citta_company_person  . "', 
					 citta_company_person_title = '" . $citta_company_person_title  . "', 
					 citta_broker_dealer_affiliate = '" . $citta_broker_dealer_affiliate  . "', 
					 citta_affiliate_name = '" . $citta_affiliate_name  . "', 
					 citta_insider_name = '" . $citta_insider_name  . "', 
					 citta_insider_title = '" . $citta_insider_title  . "', 
					 citta_is_financial_services = '" . $citta_is_financial_services  . "', 
					 citta_finserv_company_name = '" . $citta_finserv_company_name  . "', 
					 citta_finserv_company_person = '" . $citta_finserv_company_person  . "', 
					 citta_finserv_type = '" . $citta_finserv_type  . "', 
					 citta_finserv_entity_type = '" . $citta_finserv_entity_type  . "', 
					 citta_finserv_investment_type = '" . $citta_finserv_investment_type  . "', 
					 citta_active_since = ".$str_active_since.", 
					 citta_deactivated_on = NULL, 
					 citta_deactivated_by = NULL, 
					 citta_deactivated_datetime = NULL, 
					 citta_entered_by = '" . $uid . "', 
					 citta_entered_on = now(), 
					 citta_comments = '" . str_replace("'","\\'",$citta_comments)  . "', 
					 citta_isactive = 1 
				 WHERE auto_id = '".$auto_id."'";
	$result = mysql_query($qry) or die(tdw_mysql_error($qry));

	if ($result) {
		$status_message = '<font color="green">Data updated successfully.</font>';
		$val_success = 1;
	} else {
		$status_message = '<font color="red">Data update failed. Please try again or contact Technical Support.</font>';
		$val_success = 0;
	}
	
	$cid = $auto_id;
} 
//=================================================================================================

if (!$cid) {
	echo "Illegal operation performed! Please attempt to edit a CITTA record from the CITTA module!";
	exit;
} else {
//Populated the edit form
      $qry = "select 
							auto_id ,
							citta_fund_name ,
							citta_client_code ,
							citta_account_name ,
							citta_date_received ,
							citta_information_source ,
							citta_update_information ,
							citta_is_corporate_insider ,
							citta_company_name ,
							citta_company_symbol ,
							citta_company_person ,
							citta_company_person_title ,
							citta_broker_dealer_affiliate ,
							citta_affiliate_name ,
							citta_insider_name ,
							citta_insider_title ,
							citta_is_financial_services ,
							citta_finserv_company_name ,
							citta_finserv_company_person ,
							citta_finserv_type ,
							citta_finserv_entity_type ,
							citta_finserv_investment_type ,
							citta_active_since ,
							citta_deactivated_on ,
							citta_deactivated_by ,
							citta_deactivated_datetime ,
							citta_entered_by ,
							citta_entered_on ,
							citta_comments ,
							citta_isactive 
							FROM citta_list where auto_id = '".$cid."'";
							
				$result = mysql_query($qry) or die(tdw_mysql_error($qry));
				while ($row = mysql_fetch_array($result)) {
							$auto_id = $row['auto_id'];
							$citta_fund_name = $row['citta_fund_name'];
							$citta_client_code = $row['citta_client_code'];
							$citta_account_name = $row['citta_account_name'];
							$citta_date_received = $row['citta_date_received'];
							$citta_information_source = $row['citta_information_source'];
							$citta_update_information = $row['citta_update_information'];
							$citta_is_corporate_insider = $row['citta_is_corporate_insider'];
							$citta_company_name = $row['citta_company_name'];
							$citta_company_symbol = $row['citta_company_symbol'];
							$citta_company_person = $row['citta_company_person'];
							$citta_company_person_title = $row['citta_company_person_title'];
							$citta_broker_dealer_affiliate = $row['citta_broker_dealer_affiliate'];
							$citta_affiliate_name = $row['citta_affiliate_name'];
							$citta_insider_name = $row['citta_insider_name'];
							$citta_insider_title = $row['citta_insider_title'];
							$citta_is_financial_services = $row['citta_is_financial_services'];
							$citta_finserv_company_name = $row['citta_finserv_company_name'];
							$citta_finserv_company_person = $row['citta_finserv_company_person'];
							$citta_finserv_type = $row['citta_finserv_type'];
							$citta_finserv_entity_type = $row['citta_finserv_entity_type'];
							$citta_finserv_investment_type = $row['citta_finserv_investment_type'];
							$citta_active_since = $row['citta_active_since'];
							$citta_deactivated_on = $row['citta_deactivated_on'];
							$citta_deactivated_by = $row['citta_deactivated_by'];
							$citta_deactivated_datetime = $row['citta_deactivated_datetime'];
							$citta_entered_by = $row['citta_entered_by'];
							$citta_entered_on = $row['citta_entered_on'];
							$citta_comments = $row['citta_comments'];
							$citta_isactive = $row['citta_isactive'];
				}
}

?>
<script language="JavaScript" src="includes/js/popup.js"></script>
<script language="JavaScript" src="includes/js/ajax_tbx.js"></script>
<script language="javascript" src="includes/prototype/prototype.js"></script>
<script language ="Javascript">
<!--

function get_company_name() {

	if ($("citta_company_symbol").value.length == 0) {
		//alert("no symbol");
		return false;
	}
	var url = 'http://192.168.20.63/tdw/get_companyname_v2.php';
	var pars = 'symbol='+ $("citta_company_symbol").value;

  new Ajax.Request
	(
		url,   
		{     
			method:'get', 
			parameters:pars,    
			onSuccess: 
				function(transport){       
					var response = transport.responseText;  
          $("citta_company_name").value = response.toUpperCase();
          $("citta_company_symbol").value = $("citta_company_symbol").value.toUpperCase();
					toUpperCase()
				},     
			onFailure: 
			function(){ $("citta_company_name").value = "PROBABLE ERROR"; }
		}
	);
}

function setFocus(nextid) {
  document.getElementById(nextid).focus();
}


function processFormValues(){
	//allitems = Form.serialize("cittalist");
	//var params_val = allitems;
	//alert(params_val);
	//return false;
	document.forms['cittalist'].submit();
	//showDetail(params_val);
}

function d_processFormValues(){
	document.forms['cittalist_deac'].submit();
}

function showDetail(str)
{ 
	document.forms["cittalist"].reset();
	setFocus('citta_fund_name');
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
tsp(100, "CITTA List (Edit of Deactivate)");
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
   <form id="cittalist" name="cittalist" action="<?=$PHP_SELF?>" method="post">
    
    <table border="0" cellpadding="0" cellspacing="0"><tr class="ilt">
    <td align="left" nowrap="nowrap">Fund</td>
    <td align="left" nowrap="nowrap">Client Code</td>
    <td align="left" nowrap="nowrap">Account Name</td>
    <td align="left" nowrap="nowrap">Date Received</td>
    </tr>
    <tr class="ilt">
    <td align="left">
      <select class="text" name="citta_fund_name" id="citta_fund_name" size="1" style="width:75px">
        <option value="">Select</option>
      <? 
        $var_fundnames = array("BIP", "BP","BPII","BRIP","RAF","RAFII", "OTHER"); 
        foreach ($var_fundnames as $k=>$v) {
          if ($v == $citta_fund_name) {
          echo '<option value="'.$v.'" selected="selected">'.$v.'</option>';
          } else {
          echo '<option value="'.$v.'">'.$v.'</option>';
          }
        }
      ?>
      </select>
    </td>
    <td align="left"><input class="text" name="citta_client_code" id="citta_client_code" type="text" value="<?=$citta_client_code?>" size="20"/></td>
    <td align="left"><input class="text" name="citta_account_name" id="citta_account_name" type="text" value="<?=$citta_account_name?>" size="30"/></td>
    <td><input type="text" id="citta_date_received" class="Text1" name="citta_date_received" size="12" maxlength="12" value="<?=format_date_ymd_to_mdy($citta_date_received)?>">&nbsp;&nbsp;
    <A HREF="#" onClick="calfrom.select(document.forms['cittalist'].citta_date_received,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A>
    </td>
    </tr>
    </table>
   
    <table border="0" cellpadding="0" cellspacing="0"><tr class="ilt">
    <td align="left" nowrap="nowrap">Info. Source</td>
    <td align="left" nowrap="nowrap">Update Info.</td>
    <td align="left" nowrap="nowrap">Is Corp. Insider</td>
    </tr><tr>
    <td align="left"><input class="text" name="citta_information_source" id="citta_information_source" type="text" value="<?=$citta_information_source?>" size="12" maxlength="12"/></td>
    <td align="left"><select style="width:88px;" name="citta_update_information" id="citta_update_information" size="1">
    <option value="Y" <? if ($citta_update_information == 'Y') { echo '	selected="selected"'; } ?>>Yes</option><option value="N" <? if ($citta_update_information == 'Y') { echo '	selected="selected"'; } ?>>No</option></select></td>
    <td align="left"><select style="width:88px;" name="citta_is_corporate_insider" id="citta_is_corporate_insider" size="1">
    <option value="Y" <? if ($citta_is_corporate_insider == 'Y') { echo '	selected="selected"'; } ?>>Yes</option><option value="N" <? if ($citta_is_corporate_insider == 'Y') { echo '	selected="selected"'; } ?>>No</option></select></td>
    </tr></table>
    
    <table border="0" cellpadding="0" cellspacing="0">
    <tr class="ilt">
    <td align="left" nowrap="nowrap">Symbol</td>
    <td align="left" nowrap="nowrap">Company Name</td>
    <td align="left" nowrap="nowrap">Person</td>
    <td align="left" nowrap="nowrap">Title</td>
    </tr>
    <tr class="ilt">
    <td align="left" nowrap="nowrap"><input class="text" name="citta_company_symbol" id="citta_company_symbol" type="text" value="<?=$citta_company_symbol?>" size="12" onBlur="get_company_name()"/></td>
    <td align="left" nowrap="nowrap"><input class="text" name="citta_company_name" id="citta_company_name" type="text" value="<?=$citta_company_name?>" size="30"/></td>
    <td align="left" nowrap="nowrap"><input class="text" name="citta_company_person" id="citta_company_person" type="text" value="<?=$citta_company_person?>" size="30"/></td>
    <td align="left" nowrap="nowrap"><input class="text" name="citta_company_person_title" id="citta_company_person_title" type="text" value="<?=$citta_company_person_title?>" size="30"/></td>
    </tr>
    </table>
    <table border="0" cellpadding="0" cellspacing="0">
    <tr class="ilt">
    <td align="left" nowrap="nowrap">B/D Affiliate</td>
    <td align="left" nowrap="nowrap">Affiliate Name</td>
    <td align="left" nowrap="nowrap">Insider Name</td>
    <td align="left" nowrap="nowrap">Insider Title</td>
    </tr>
    <tr class="ilt">
    <td align="left"><select style="width:88px;" name="citta_broker_dealer_affiliate" id="citta_broker_dealer_affiliate" size="1">
    <option value="Y" <? if ($citta_broker_dealer_affiliate == 'Y') { echo '	selected="selected"'; } ?>>Yes</option><option value="N" <? if ($citta_broker_dealer_affiliate == 'Y') { echo '	selected="selected"'; } ?>>No</option></select></td>
    <td align="left"><input class="text" name="citta_affiliate_name" id="citta_affiliate_name" type="text" value="<?=$citta_affiliate_name?>" size="30"/></td>
    <td align="left"><input class="text" name="citta_insider_name" id="citta_insider_name" type="text" value="<?=$citta_insider_name?>" size="30"/></td>
    <td align="left"><input class="text" name="citta_insider_title" id="citta_insider_title" type="text" value="<?=$citta_insider_title?>" size="30"/></td>
    </tr>
    </table>
    <table border="0" cellpadding="0" cellspacing="0">
    <tr class="ilt">
    <td align="left" nowrap="nowrap">Is Fin. Serv.</td>
    <td align="left" nowrap="nowrap">Company Name</td>
    <td align="left" nowrap="nowrap">Person</td>
    <td align="left" nowrap="nowrap">Fin. Service Type</td>
    </tr>
    <tr class="ilt">
    <td align="left"><select style="width:88px;" name="citta_is_financial_services" id="citta_is_financial_services" size="1">
    <option value="Y" <? if ($citta_is_financial_services == 'Y') { echo '	selected="selected"'; } ?>>Yes</option><option value="N" <? if ($citta_is_financial_services == 'Y') { echo '	selected="selected"'; } ?>>No</option></select></td>
    <td align="left"><input class="text" name="citta_finserv_company_name" id="citta_finserv_company_name" type="text" value="<?=$citta_finserv_company_name?>" size="30"/></td>
    <td align="left"><input class="text" name="citta_finserv_company_person" id="citta_finserv_company_person" type="text" value="<?=$citta_finserv_company_person?>" size="30"/></td>
    <td align="left"><input class="text" name="citta_finserv_type" id="citta_finserv_type" type="text" value="<?=$citta_finserv_type?>" size="30"/></td>
    </tr>
    </table>
    <table border="0" cellpadding="0" cellspacing="0">
    <tr class="ilt">
    <td align="left" nowrap="nowrap">Entity Type</td>
    <td align="left" nowrap="nowrap">Investment Type</td>
    <td align="left" nowrap="nowrap">Active Since</td>
    </tr>
    <tr class="ilt">
    <td align="left"><input class="text" name="citta_finserv_entity_type" id="citta_finserv_entity_type" type="text" value="<?=$citta_finserv_entity_type?>" size="30"/></td>
    <td align="left"><input class="text" name="citta_finserv_investment_type" id="citta_finserv_investment_type" type="text" value="<?=$citta_finserv_investment_type?>" size="30"/></td>
    <td align="left"><input type="text" id="citta_active_since" class="Text1" name="citta_active_since" size="12" maxlength="12" value="<?=format_date_ymd_to_mdy($citta_active_since)?>">&nbsp;&nbsp;
    <A HREF="#" onClick="calto.select(document.forms['cittalist'].citta_active_since,'anchor2','MM/dd/yyyy'); return false;" NAME="anchor2" ID="anchor2"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
    </tr>
    </table>
    <table border="0" cellpadding="0" cellspacing="0">
    <tr class="ilt">
    <td align="left">Comments</td>
    </tr>
    <tr class="ilt">
    <td align="left"><textarea style="width:723px;" class="text" name="citta_comments" id="citta_comments" rows="3" cols="87"/></textarea></td>
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
    <input type="hidden" name="venteredby" value="<?=$user_id?>">
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

   <form id="cittalist_deac" name="cittalist_deac" action="<?=$PHP_SELF?>" method="post">

	<?
	if ($val_success != 2) {
 	?>
		<table><tr>
    <td class="ilt">Deactivate As Of</td>
    <td><input type="text" id="d_citta_date_deac" class="Text1" name="d_citta_date_deac" size="12" maxlength="12" value="<?=date('m/d/Y')?>">&nbsp;&nbsp;
    <A HREF="#" onClick="caldeac.select(document.forms['cittalist_deac'].d_citta_date_deac,'anchor3','MM/dd/yyyy'); return false;" NAME="anchor3" ID="anchor3"><img src="images/lf_v1/sel_date.png" border="0"></A>
    </td>
    </tr>
    </table><br>
    <table border="0" cellpadding="0" cellspacing="0">
    <tr class="ilt">
    <td align="left">Comments</td>
    </tr>
    <tr class="ilt">
    <td align="left"><textarea style="width:723px;" class="text" name="d_citta_comments" id="d_citta_comments" rows="3" cols="87"/></textarea></td>
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