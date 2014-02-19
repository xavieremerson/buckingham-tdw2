<?
include('includes/global.php');
include('includes/dbconnect.php');
include('includes/functions.php');

/*
  mod = [mod_exp_approver]
  user_id = [79]
  sel_month = [2011-05-01]
  sel_approver = [79]
  eid = [1190b360f0035a4a9ead3fc27d04fe61]
*/

?>
<html>
<head>
<link rel="shortcut icon" href="favicon.ico"></link>
<link rel="bookmark" href="favicon.ico"></link>
<title>TDW</title>
 
<script> 
//Mute status bar texts
function hidestatus(){
  window.status=''
  return true
}
if (document.layers)
document.captureEvents(Event.MOUSEOVER | Event.MOUSEOUT)
document.onmouseover=hidestatus
document.onmouseout=hidestatus
</script>
 
<link rel="stylesheet" type="text/css" href="../includes/styles.css">
</head>
<body bgcolor="#F4F8FB" leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0">
 
<!-- TOP LEVEL TABLE -->
<table width="100%" height="100%" border="3" cellpadding="0" cellspacing="0" bordercolor="#333333" bordercolorlight="#999999" bordercolordark="#000000" bgcolor="#F4F8FB">
  <tr valign="top">
    <td height="20"> 
<table width="100%"  border="0" cellpadding="0" cellspacing="0" bgcolor="FFFFFF">
  <tr> 
  <td width="80"><img src="../images/logow64h47.gif" ></td>
    <td align="right" valign="top"> 
      <table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td> 
            <table width="100%"  border="0" cellspacing="1" cellpadding="1">
              <tr> 
                <td align="left" valign="top"><img src="../images/client_appw290h47.gif" border="0"></td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
    <td valign="top">
      <table width="100%" height="47">
        <tr>
          <td align="right" nowrap><a class="ghm"></a></td><!-- _global_header_message-->
        </tr>
      </table>
    </td>
  </tr>
	</table>
  <hr width="100%" size="6" noshade color="#0099FF">
  </td>
</tr>
<tr valign="top">
  <td valign="top">
    <table width="100%" height="100%" border="0" cellpadding="3" cellspacing="0">
      <tr>  
        <td valign="top">
          <table width="100%" height="100%" border="0" cellspacing="1" cellpadding="0">
            <tr> 
              <td valign="top">
              <!-- START MAIN -->




<link rel="stylesheet" type="text/css" href="../includes/styles.css">
<script type="text/javascript" language="javascript" src="../includes/prototype/prototype.js"></script>
<script type="text/javascript">

function exitthis() {
    window.opener='x'
    //window.opener = top;
    window.close();
}

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

function process_appr()  //get requests of pre-approval from users.
{
	var sform = $("appr").serialize();
	var url = 'http://192.168.20.63/tdw/mod_exp_ajx_approve.php';
	var pars = 'user_id=<?=$user_id?>';
	pars = pars + '&'+ sform;
  
	var mytime= '&ms='+new Date().getTime();
	pars = pars + mytime;
  var ran_number= Math.random()*5; 
	pars = pars + '&xrand=' + ran_number;
    
  //alert(pars);
  //return false;
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
						var responseLenght = response.length;
						 
							if (responseLenght > 0) {
								$("zerr").innerHTML = response;
							} else {
								$("data_sec").innerHTML = "";
								$("data_sec").innerHTML = "<br><br><center><input type='button' name='submit' value='Close Window' onclick='exitthis();'></center>";
								$("zerr").innerHTML = "";
							}
						//alert("Lenght of response is " + responseLenght);     
            //alert(response);
						//$("exp_add").reset();
          },     
        onFailure: 
        function(){ 
										alert('Unexpected error. Technical Support has been notified.') 
            				$("zerr").innerHTML = 'Unexpected error. Technical Support has been notified.';
									}
      }
    );
}


</script>

<?

$requester = db_single_val("select Fullname as single_val from users where ID = '".$user_id."'");
$approver  = db_single_val("select Fullname as single_val from users where ID = '".$sel_approver."'");
$approveremail  = db_single_val("select Email as single_val from users where ID = '".$sel_approver."'");

//show numbers
function sn ($num) {
	$nval = number_format($num,2,".",",");
	if ($nval == '0.00') {
		return "&nbsp;";
	} else {
		return $nval;
	}
}

//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
?>

<style type="text/css">
<!--
.htr {	font-family: Arial, Helvetica, sans-serif;	font-size: 12px;	font-weight: bold;	color: #ffffff;	background-color: #000000;}
.rilt {	font-family: Arial, Helvetica, sans-serif;	font-size: 11px;	font-weight: bold;	color: #003399;	text-align: right; }
.lilt {	font-family: Arial, Helvetica, sans-serif;	font-size: 11px;	font-weight: bold;	color: #003399;	text-align: left; }
.bdark {	font-family: Arial, Helvetica, sans-serif;	font-size: 12px;	font-weight: bold;	color: #1b3ba1; background-color: #eeeeee;}
.blight {	font-family: Arial, Helvetica, sans-serif;	font-size: 12px;	font-weight: bold;	color: #1b3ba1; }
.thisdoclink {	font-family: Arial, Helvetica, sans-serif;	font-size: 11px;	font-weight: bold;	color: #FF6600;	text-align: left;	text-decoration: underline;}
-->
</style>


<?
//if acted upon then show link to exit out.
$q = "select exp_acted_upon as single_val from exp_expense_email_actions where exp_md5 = '".$eid."'";
//xdebug("qry",$q);
$var_acted_upon = db_single_val($q);

if ($var_acted_upon == 1) {

	echo "You have already acted upon this Review/Approval. This email link is no longer valid.";

} else {

?>
<div id="data_sec">
<form id="appr" name="appr">
<input type="hidden" name="user_id" value="<?=$user_id?>" />
<input type="hidden" name="sel_month" value="<?=$sel_month?>" />
<input type="hidden" name="sel_approver" value="<?=$sel_approver?>" />
<input type="hidden" name="eid" value="<?=$eid?>" />

<table width="100%"  border="1" cellspacing="0" cellpadding="0">
            <tr class="htr">
            <td width="30">&nbsp;</td>
            <td width="80">Date</td>
            <td width="80">Rcpt.</td>
            <td width="80">Type</td>
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
            <td width="200">Persons</td>
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
									WHERE auto_id in (".$strids.") 
									order by auto_id desc";
															//AND  ";
            //xdebug("str_sql_select",$str_sql_select);
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

						$result_select = mysql_query($str_sql_select) or die(tdw_mysql_error($str_sql_select));						
						$count_row_select = 0;
						$arr_ids = array();
						while ( $row = mysql_fetch_array($result_select) ) 
						{
						
							//capture all id's 
							$arr_ids[] = $row["auto_id"];
						
							if ($count_row_select%2) { $class_row_select = "bdark"; } else { $class_row_select = "blight"; } 

							?> <tr class="<?=$class_row_select?>"> 
												<td><input type="checkbox" name="chk_<?=$row["auto_id"]?>" id="chk_<?=$row["auto_id"]?>" checked="checked" value="1" /></td>												
												<td><?=format_date_ymd_to_mdy($row["exp_date_from"])?></td>
												<td align="center">
												
							<?
									if ($row["exp_have_receipt"] == 1) {
									 echo 'Yes'; 
									} else {
									 echo 'No'; 									
									}
									
							?>
              					</td>
												<td>&nbsp;<?=$row["exp_type"]?></td>
												<td>&nbsp;<?=$row["exp_c_p_name"]?></td>
												<td>&nbsp;<?=nl2br($row["exp_description"])?></td>
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
												<td nowrap="nowrap">&nbsp;
                        <?
												if ($arr_persons_short[$row["auto_id"]]) {
													echo $arr_persons_short[$row["auto_id"]].'</td>';
												} else {
													echo '</td>';
												}
												
							echo '</tr>';
						  $count_row_select = $count_row_select + 1;
						}

	echo '</table>';
//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&

$str_ids = implode("^",$arr_ids);
?>
<input type="hidden" name="ids" id="ids" value="<?=$str_ids?>" />

<a href="#" class="ilt" onclick="chkall();" class="gilt">Check All</a>&nbsp;&nbsp;&nbsp;
<a href="#" class="ilt" onclick="unchkall();" class="gilt">UnCheck All</a>
<br />

<a class="ilt">Comment</a><br />
<textarea name="acomment" id="acomment" rows="4" cols="80"></textarea>
<input type="button" name="save" value="&nbsp;&nbsp;&nbsp;&nbsp;Save / Proceed&nbsp;&nbsp;&nbsp;&nbsp;" onClick="process_appr();" />
</form>
</div>
<div id="zerr" style="font:Verdana; color:#FF0000; font-size:12px; font-weight:bold; padding:4px"></div>
<div id="zgood" style="font:Verdana; color:#00ff00; font-size:12px; font-weight:bold; padding:4px"></div>
<?
/*
user_id = [79]
  sel_month = [2011-05-01]
  sel_approver = [309]
*/

}

?>
              <!-- END MAIN -->
              </td>
            </tr>
          </table>
  			</td>
			</tr>
<tr valign="bottom">
  <td height="10">
    <table width="100%"> <!-- height="20"-->
      <tr valign="top">
      <td align="center" valign="bottom">&nbsp;
      </td>
      </tr>
    </table>
  </td>
</tr>
</table>
</body>
</html>

