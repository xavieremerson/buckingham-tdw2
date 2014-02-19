<html>
<head>
<!--<meta http-equiv="X-UA-Compatible" content="IE=8" />-->
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<script language="javascript" src="includes/prototype/prototype.js"></script>
<style type="text/css">
<!--
.gilt {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
	color: #006600;
	text-align: left;
	background-color: #99FF99;
	border: 1px solid #006600;
	padding-top: 3px;
	padding-right: 6px;
	padding-bottom: 3px;
	padding-left: 6px;
	text-decoration: none; 
}
.gilt:hover {
	text-decoration: underline;
}
.htr {	font-family: Arial, Helvetica, sans-serif;	font-size: 12px;	font-weight: bold;	color: #003399;	background-color: #eeeeee;}
.rilt {	font-family: Arial, Helvetica, sans-serif;	font-size: 11px;	font-weight: bold;	color: #003399;	text-align: right; }
.lilt {	font-family: Arial, Helvetica, sans-serif;	font-size: 11px;	font-weight: bold;	color: #003399;	text-align: left; }
.thisdoclink {	font-family: Arial, Helvetica, sans-serif;	font-size: 11px;	font-weight: bold;	color: #FF6600;	text-align: left;	text-decoration: underline;}
-->
</style>
<script type="text/javascript">

function chkall() {
	var ids = $("ids").value;
	
	var ids_array=ids.split("^");
	for (i=0;i<ids_array.length;i++)
	{
		var chkbox_id = 'chk_'+ ids_array[i];
		$(chkbox_id).checked = true;
	}
}

function unchkall() {
	var ids = $("ids").value;
	
	var ids_array=ids.split("^");
	for (i=0;i<ids_array.length;i++)
	{
		var chkbox_id = 'chk_'+ ids_array[i];
		$(chkbox_id).checked = false;
	}
}


function showsubmit () {
			$("divapprover").style.display = 'block';
			$("divapprover").style.visibility = 'visible';
			$("sh").style.display = 'none';
			$("sh").style.visibility = 'hidden';
			
}

function submit_appr() {

	if ($("sel_approver").value == "") {
		alert('Please select approver from the list.');
		return false;	
	}

	var url = 'http://192.168.20.63/tdw/mod_exp_to_approver.php';
	var pars = 'user_id=<?=$user_id?>';
	pars = pars + '&sel_month='+ parent.$("sel_month").value;
	pars = pars + '&sel_approver='+ $("sel_approver").value;
	
	var str_form_apprs = $("appr_items").serialize();
	//var str_form_apprs = Form.serialize("appr_items");
	
	//$("finderror2").innerHTML = str_form_apprs;
	
	pars = pars + '&'+ str_form_apprs;
	//return false;
  
	var mytime= '&ms='+new Date().getTime();
	pars = pars + mytime;
    var ran_number= Math.random()*5; 
	pars = pars + '&xrand=' + ran_number;

	new Ajax.Request
	(
		url,   
		{     
			method:'get', 
			parameters:pars,    
			onSuccess: 
				function(transport){       
					var response = "";
					response = transport.responseText;       
          //alert(response);
					$("sh").style.display = 'block';
					$("sh").style.visibility = 'visible';
					$("sh").removeAttribute("href");
					$("sh").innerHTML = response;
					$("divapprover").innerHTML = "";
					parent.$("if_status").src= 'mod_exp_data_process.php?user_id=<?=$user_id?>';
				},     
			onFailure: 
			function(){ 
									alert('Unexpected error. Please notify Technical Support.'); 
								}
		}
	);
}
</script>
<script language="JavaScript" src="includes/js/popup.js"></script>
<link rel="stylesheet" type="text/css" href="includes/styles.css">
</head>
<body>
<?
include('includes/dbconnect.php');
include('includes/functions.php');

//show numbers
function sn ($num) {
	$nval = number_format($num,2,".",",");
	if ($nval == '0.00') {
		return "";
	} else {
		return $nval;
	}
}

?>

<? tsp(100, "Expense Items"); ?>
		<table width="100%" height="100%" cellpadding="1" cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
          <form name="appr_items" id="appr_items">
					<table width="100%"  border="0" cellspacing="1" cellpadding="1">
            <tr class="htr">
            <td width="30">&nbsp;</td>
            <td width="80">Date</td>
            <td width="30" onMouseOver="Tip('Submitted for Approval', WIDTH, 200, PADDING, 6, BGCOLOR, '#fff7b1')">Subm.</td>
            <td width="30" onMouseOver="Tip('Approval Status', WIDTH, 200, PADDING, 6, BGCOLOR, '#fff7b1')">Appr.</td>
            <td width="30" onMouseOver="Tip('Processed/Paid', WIDTH, 200, PADDING, 6, BGCOLOR, '#fff7b1')">Proc.</td>
            <td width="30" onMouseOver="Tip('Have Receipts', WIDTH, 200, PADDING, 6, BGCOLOR, '#fff7b1')">Rcpt.</td>
            <td width="70">Type</td>
            <td width="200">Client / Prospect</td>
            <td width="300">Description</td>
            <td width="60">Hotel</td>
            <td width="60">Airline</td> 
            <td width="60">Train</td> 
            <td width="60">Cab</td> 
            <td width="60">Rental</td> 
            <td width="70">Mileage ($)</td> 
            <td width="60">Other</td> 
            <td width="60">Meals</td>
            <td width="60">Phone</td>
            <td width="60">Entertain</td>
            <td width="60">Misc.</td>
            <td width="60">Total</td>
            <td width="100">Persons</td>
            <td>&nbsp;</td>
            </tr>
						<?
						//if no sel_month_passed, use current month. else use passed value
						if ($sel_month && $sel_month != "") {
								$dstart = $sel_month;
								$dend = lastday(date('m', strtotime($sel_month)), date('Y', strtotime($sel_month)));
						} else {
								$dstart = date('Y-m-01');;
								$dend = lastday(date('m'), date('Y'));
						}

						$mile_rate = db_single_val("select exp_lookup_val as single_val from exp_expense_lookup_vals where exp_lookup_name = 'MILEAGE' and exp_isactive = 1");

						
						$str_sql_select = "SELECT auto_id,
																			exp_user_id,
																			exp_date_from,
																			exp_date_to,
																			exp_type,
																			exp_client_id,
																			exp_client_code,
																			exp_c_p_name,
																			exp_description,
																			exp_accomodation,
																			exp_transport_air, 
																			exp_transport_train,
																			exp_transport_cab,
																			exp_transport_rental,
																			exp_transport_mileage,
																			exp_transport_other, 
																			exp_food,
																			exp_phone,
																			exp_entertainment,
																			exp_misc,
																			exp_have_receipt,
																			exp_submitted,
																			exp_processed,
																			exp_approved,
																			exp_approver,
																			exp_approval_date,
																			exp_approver_comment,
																			exp_approver,
																			exp_approval_date,
																			exp_approver_comment,
																			exp_datetimestamp,
																			exp_isactive
															FROM exp_expense_items 
															WHERE exp_user_id = '".$user_id."'
															AND exp_date_from between '".$dstart."' and '".$dend."'
															order by auto_id desc";
															//AND  ";
            //xdebug("str_sql_select",$str_sql_select);
						//exit;
						$result_select = mysql_query($str_sql_select) or die(tdw_mysql_error($str_sql_select));

						//======================================================================================
						//Polulated Person Data
						$arr_parent_items = array();
						while ( $row = mysql_fetch_array($result_select) ) {
							$arr_parent_items[] = $row["auto_id"];
						}
						$str_parent_items = implode("','",$arr_parent_items);
						$str_parent_items = " ('".$str_parent_items."') ";
						$query_child = "select exp_parent_id, exp_first_name, exp_last_name, exp_person_note, exp_added_on     
														from exp_expense_contacts where exp_parent_id in ".$str_parent_items." and exp_isactive = 1 order by exp_parent_id";
						//xdebug("query_child",$query_child);
						$result_child = mysql_query($query_child) or die(tdw_mysql_error($query_child));
						$arr_raw_child = array();
						while ( $row_child = mysql_fetch_array($result_child) ) {
							$arr_raw_child[] = $row_child["exp_parent_id"]."^".$row_child["exp_first_name"]."^".$row_child["exp_last_name"]."^".$row_child["exp_person_note"];
						} 
						$hold_parent_id = "";
						$arr_persons = array();
						$arr_persons_short = array();
						if (is_array($arr_raw_child)) {
								foreach ($arr_raw_child as $k=>$v) {
									$str_persons = explode("^",$v);
									if ($str_persons[3] == "") { $str_note = ""; } else { $str_note = "<br>Note: " . $str_persons[3]; }
										if ($str_persons[0] != $hold_parent_id || $hold_parent_id == "") {
											$str_details = $str_persons[1] . " ". $str_persons[2] . $str_note;
											$str_details_short = $str_persons[1] . " ". $str_persons[2];
										} else {
											$str_details .= "<br><br>".$str_persons[1] ." ". $str_persons[2] . $str_note;
											$str_details_short .= ", ". $str_persons[1] . " ". $str_persons[2];
										}
										$arr_persons[$str_persons[0]] = $str_details;
										$arr_persons_short[$str_persons[0]] = $str_details_short;
										$hold_parent_id = $str_persons[0];
								}
						}
						//show_array($arr_persons);
						//======================================================================================
						?>
						<script language="JavaScript" src="includes/wz/wz_tooltip.js" type="text/javascript"></script>
            <!--<form name="appr_items" id="appr_items" action="#">-->
						<?
						$result_select = mysql_query($str_sql_select) or die(tdw_mysql_error($str_sql_select));						
						$count_row_select = 0;
						$arr_ids = array();
						?>
            <?
						while ( $row = mysql_fetch_array($result_select) ) 
						{

							//capture all unsubmitted id's 
							if ($row["exp_submitted"] == 0) {
								$arr_ids[] = $row["auto_id"];
							}

							if ($count_row_select%2) { $class_row_select = "trdark"; } else { $class_row_select = "trlight"; } 
						?>
						<tr class="<?=$class_row_select?>"> 
              <td><? if ($row["exp_submitted"] == 0) { ?>
              			<input type="checkbox" name="chk_<?=$row["auto_id"]?>" id="chk_<?=$row["auto_id"]?>" />
									<? } else { echo '&nbsp;'; } ?>
							</td>
              <td><?=format_date_ymd_to_mdy($row["exp_date_from"])?></td>
              <!-- exp_have_receipt, exp_submitted, exp_processed,exp_approved, -->
              <td align="center"><? if ($row["exp_submitted"] == 1) { echo '<img src="images/gcheck.png" border="0">'; } ?></td>
              <td align="center">
							<? 
								if ($row["exp_submitted"] == 1 && $row["exp_approved"] == 0) { echo '<img src="images/wait.png" border="0">'; } 
								if ($row["exp_submitted"] == 1 && $row["exp_approved"] == 1) { echo '<img src="images/gcheck.png" border="0">'; } 
							?>
              </td>
              <td align="center"><? if ($row["exp_submitted"] == 1 && $row["exp_approved"] == 1 && $row["exp_processed"] == 0) 
																		{ 
																				echo '<img src="images/wait.png" border="0">'; 
																		} elseif ($row["exp_submitted"] == 1 && $row["exp_approved"] == 1 && $row["exp_processed"] == 1)  {
																				echo '<img src="images/gcheck.png" border="0">'; 
																		} else {
																				echo '&nbsp;';
																		}
																		?></td>
              <td align="center"><? if ($row["exp_have_receipt"] == 1) { echo '<img src="images/gcheck.png" border="0">'; } ?></td>
              <td><?=$row["exp_type"]?></td>
              <td><?=$row["exp_c_p_name"]?></td>
              <td><?=nl2br($row["exp_description"])?></td>
              <td align="right"><?=sn($row["exp_accomodation"])?></td> 
              <td align="right"><?=sn($row["exp_transport_air"])?></td>
              <td align="right"><?=sn($row["exp_transport_train"])?></td>
              <td align="right"><?=sn($row["exp_transport_cab"])?></td>
              <td align="right"><?=sn($row["exp_transport_rental"])?></td>
              <td align="right"><?=sn($row["exp_transport_mileage"] * $mile_rate)?></td>
              <td align="right"><?=sn($row["exp_transport_other"])?></td>
              <td align="right"><?=sn($row["exp_food"])?></td>
              <td align="right"><?=sn($row["exp_phone"])?></td>
              <td align="right"><?=sn($row["exp_entertainment"])?></td>
              <td align="right"><?=sn($row["exp_misc"])?></td>
              <td align="right"><?=sn($row["exp_accomodation"]+
											 $row["exp_transport_air"]+
											 $row["exp_transport_train"]+
											 $row["exp_transport_cab"]+
											 $row["exp_transport_rental"]+
											 number_format(($row["exp_transport_mileage"]*$mile_rate),2,".","")+
											 $row["exp_transport_other"]+
											 $row["exp_food"]+
											 $row["exp_phone"]+
											 $row["exp_entertainment"]+
											 $row["exp_misc"]
											 )?></td>
              <td nowrap="nowrap"><?
							if ($arr_persons_short[$row["auto_id"]]) {
							?><a class="iltr"><img src="images/details.png" border="0" onMouseOver="Tip('<?=str_replace("'","\\'",$arr_persons[$row["auto_id"]])?>', WIDTH, 250, PADDING, 6, BGCOLOR, '#eeeeee', TITLE, 'Contact Details')"/>
              </a><? } ?><?=$arr_persons_short[$row["auto_id"]] ?></td>
            	<td>&nbsp;</td>
						</tr>
						<?php
						$count_row_select = $count_row_select + 1;
						}
						$str_ids = implode("^",$arr_ids);
						?>
					</table>
          <input type="hidden" name="ids" id="ids" value="<?=$str_ids?>" />
          </form>
				</td>
			</tr>
		</table>

	
<?

if (count($arr_ids) > 0) {  
?>
<br />
<a href="#" onClick="chkall();" class="ilt">Check All</a> &nbsp;
<a href="#" onClick="unchkall();" class="ilt">UnCheck All</a> 
<a class="gilt" id="sh" href="javascript:showsubmit();" style="cursor:pointer">Submit for approval.</a>
  <br />
  <div id="divapprover" style="visibility:hidden; display:none">
  	<table border="0">
    	<tr>
      <td>
        <select class="Text" name="sel_approver" id="sel_approver" size="1">
        <option value="">Select Approver</option>
        <option value="" >------ ------</option>
        <?
        $result = mysql_query("SELECT * FROM users WHERE user_isactive = '1' AND is_login_acct = '1' AND custom2 = 1 ORDER BY Fullname") or die (mysql_error());
        
        while ( $row = mysql_fetch_array($result) ) {
        ?>
          <option value="<?=$row["ID"]?>" ><?=substr($row["Fullname"],0,30)?></option>
        <?
        }
        ?>	
        </select>
      </td>
      <td><input type="button" class="submit" name="test" value="Submit for approval" onClick="submit_appr();" /></td>
      </tr>
    </table>
		</div>
    <img src="images/spacer.gif" height="4" width="4" />
    <!--<div id="finderror2">ERR DIV</div>-->
<?
}

?>

<? tep(); ?>
		</body>
</html>