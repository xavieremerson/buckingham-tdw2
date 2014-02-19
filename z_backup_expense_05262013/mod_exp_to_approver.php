<?
include('includes/global.php');
include('includes/dbconnect.php');
include('includes/functions.php');

/*  user_id = [79]
  sel_month = [2011-05-01]
  sel_approver = [309]
  ms = [1305617585524]
  xrand = [2.116937055065437]*/

$requester = db_single_val("select Fullname as single_val from users where ID = '".$user_id."'");
$approver  = db_single_val("select Fullname as single_val from users where ID = '".$sel_approver."'");
$approveremail  = db_single_val("select Email as single_val from users where ID = '".$sel_approver."'");

//show numbers
function sn ($num) {
	$nval = number_format($num,2,".",",");
	if ($nval == '0.00') {
		return "";
	} else {
		return $nval;
	}
}

////
// Update the db with checked records as being marked submitted

$arr_ids = explode("^",$ids);
//show_array($arr_ids);

$arr_item_ids = array();

foreach($arr_ids as $k=>$v) {

	$varname = 'chk_'.$v;
	if ($$varname) {
	  $arr_item_ids[] = $v;
		//echo "checkbox ".$varname . " found";
		$qry = 	"update exp_expense_items 
							set exp_submitted = 1,
							exp_approver = '".$sel_approver."'
							where auto_id = '".$v."'";
	  $result = mysql_query($qry) or die(tdw_mysql_error($qry));
	}

}

//pass to sql
$str_item_ids = implode(",",$arr_item_ids);


//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&


$str = '<style type="text/css">
<!--
.htr {	font-family: Arial, Helvetica, sans-serif;	font-size: 12px;	font-weight: bold;	color: #ffffff;	background-color: #000000;}
.rilt {	font-family: Arial, Helvetica, sans-serif;	font-size: 11px;	font-weight: bold;	color: #003399;	text-align: right; }
.lilt {	font-family: Arial, Helvetica, sans-serif;	font-size: 11px;	font-weight: bold;	color: #003399;	text-align: left; }
.bdark {	font-family: Arial, Helvetica, sans-serif;	font-size: 12px;	font-weight: bold;	color: #1b3ba1; background-color: #eeeeee;}
.blight {	font-family: Arial, Helvetica, sans-serif;	font-size: 12px;	font-weight: bold;	color: #1b3ba1; }
.thisdoclink {	font-family: Arial, Helvetica, sans-serif;	font-size: 11px;	font-weight: bold;	color: #FF6600;	text-align: left;	text-decoration: underline;}
-->
</style>';
		
$str .= '<table width="100%"  border="1" cellspacing="0" cellpadding="0">
            <tr class="htr">
            <td width="80">Date</td>
            <td width="80">Have Receipt</td>
            <td width="60">Type</td>
            <td width="120">Client / Prospect</td>
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
            </tr>';

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
									WHERE auto_id in (".$str_item_ids.") 
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
						while ( $row = mysql_fetch_array($result_select) ) 
						{
							if ($count_row_select%2) { $class_row_select = "bdark"; } else { $class_row_select = "blight"; } 

							$str .= '<tr class="'.$class_row_select.'"> 
												<td>'.format_date_ymd_to_mdy($row["exp_date_from"]).'</td>
												<td align="center">';
												
									if ($row["exp_have_receipt"] == 1) {
									 $str .= 'Yes'; 
									} else {
									 $str .= 'No'; 									
									}
									
							$str .= '</td>
												<td>'.$row["exp_type"].'</td>
												<td>'.$row["exp_c_p_name"].'</td>
												<td>'.nl2br($row["exp_description"]).'</td>
												<td align="right">'.sn($row["exp_accomodation"]).'</td> 
												<td align="right">'.sn($row["exp_transport_air"]).'</td>
												<td align="right">'.sn($row["exp_transport_train"]).'</td>
												<td align="right">'.sn($row["exp_transport_cab"]).'</td>
												<td align="right">'.sn($row["exp_transport_rental"]).'</td>
												<td align="right">'.sn($row["exp_transport_mileage"] * $mile_rate).'</td>
												<td align="right">'.sn($row["exp_transport_other"]).'</td>
												<td align="right">'.sn($row["exp_food"]).'</td>
												<td align="right">'.sn($row["exp_phone"]).'</td>
												<td align="right">'.sn($row["exp_entertainment"]).'</td>
												<td align="right">'.sn($row["exp_misc"]).'</td>
												<td align="right">'.sn($row["exp_accomodation"]+
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
																 ).'</td>
												<td nowrap="nowrap">';
												if ($arr_persons_short[$row["auto_id"]]) {
													$str .= $arr_persons_short[$row["auto_id"]].'</td>';
												} else {
													$str .= '</td>';
												}
												
							$str .= '</tr>';
						$count_row_select = $count_row_select + 1;
						}

	$str .= '</table>';


//&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&

/*
user_id = [79]
  sel_month = [2011-05-01]
  sel_approver = [309]
*/

//put monitoring ID
$rand_val = md5(rand(1,999999999999));

$rec_email = db_single_val("select max(Email) as single_val from users where ID = '".$sel_approver."'");

$sqry = "INSERT INTO exp_expense_email_actions 
						(auto_id, exp_user_id, exp_month, exp_approver_id, 
						exp_datetimestamp, exp_email_to, exp_md5, exp_acted_upon, 
						exp_acted_upon_when, exp_isactive) 
				 VALUES (NULL, '".$user_id."', '".$sel_month."', '".$sel_approver."', 
				         now(), '".$rec_email."', '".$rand_val."', '0', NULL, '1')";
$sresult = mysql_query($sqry) or die(tdw_mysql_error($sqry));						

$link = $_site_url.'proc/?mod=mod_exp_approver&user_id='.$user_id.'&sel_month='.$sel_month.'&sel_approver='.$sel_approver.'&eid='.$rand_val.'&strids='.$str_item_ids;

$email_log = '
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr> 
							<td valign="top">
								<p><a class="bodytext12"><strong>Expense Report Approval.</strong></a></p>			
								<p class="bodytext12">Please click <strong><a href="'.$link.'">&gt;&gt;HERE&lt;&lt;</a></strong> to access the Expense Report ['. date('F', strtotime($sel_month)) . " " . date('Y', strtotime($sel_month)) . ']
								submitted by '. $requester.' for your review and approval.</p>
								'.$str.'
								<p>&nbsp;</p>
								<p><a class="bodytext12"><strong>TDW Administrator</strong></a></p>
							</td>
						</tr>
					</table>';
//create mail to send
$html_body = ""; 
$html_body .= zSysMailHeader("");
$html_body .= $email_log;
$html_body .= zSysMailFooter ();

$subject = $requester ." has submitted Expense Report [". date('F', strtotime($sel_month)) . " " . date('Y', strtotime($sel_month)) . "] for your review/approval.";
$text_body = $subject;

zSysMailer($approveremail, "", $subject, $html_body, $text_body, "") ;

echo "Submitted for review and approval to ". $approver;
?>
