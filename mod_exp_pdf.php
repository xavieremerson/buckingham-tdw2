<?
  include('includes/dbconnect.php');
  include('includes/global.php'); 
	include('includes/functions.php');

$str = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<style type="text/css">
<!--
.htr {	font-family: Arial, Helvetica, sans-serif;	font-size: 12px;	font-weight: bold;	color: #003399;	background-color: #eeeeee;}
.rilt {	font-family: Arial, Helvetica, sans-serif;	font-size: 11px;	font-weight: bold;	color: #003399;	text-align: right; }
.lilt {	font-family: Arial, Helvetica, sans-serif;	font-size: 11px;	font-weight: bold;	color: #003399;	text-align: left; }
.thisdoclink {	font-family: Arial, Helvetica, sans-serif;	font-size: 11px;	font-weight: bold;	color: #FF6600;	text-align: left;	text-decoration: underline;}
-->
</style>
</head>
<body>';

$period_start_date = $sel_month;
$period_end_date = date('Y-m-'.date('t',strtotime($sel_month)),strtotime($sel_month));

$user_fullname = db_single_val("select Fullname as single_val from users where ID = '".$user_id."'");
		
$str .= '<table width="100%"  border="1" cellspacing="0" cellpadding="0">
				 <tr><td>
				 EXPENSE REPORT: <font color="blue"><b>'.$user_fullname.'</b></font><br>
				 PERIOD: <font color="blue">'.format_date_ymd_to_mdy($period_start_date).' TO '.format_date_ymd_to_mdy($period_end_date).'</font><br>
				 Report Printed on <font color="blue">'.date('m/d/Y h:i:sa').'</font></td></tr></table><br>';

$str .= '<table width="100%"  border="1" cellspacing="0" cellpadding="0">
            <tr class="htr">
            <td width="80">From</td>
            <td width="80">To</td>
            <td width="80">DIV.</td>
            <td width="60">Type</td>
            <td width="120">Client / Prospect</td>
            <td width="60">Hotel</td>
            <td width="60">Air</td> 
            <td width="60">Train</td>
            <td width="60">Cab</td>
            <td width="60">Rental</td>
            <td width="60">Mileage</td>
            <td width="60">Other</td>
            <td width="60">Meals</td>
            <td width="60">Phone</td>
            <td width="60">Entertain</td>
            <td width="60">Misc.</td>
            <td width="60">Total</td>
            <td width="200">Persons</td>
            </tr>';

$str_sql_select = "SELECT auto_id,
													exp_user_id,
													exp_date_from,
													exp_date_to,
													exp_division,
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
													exp_approver,
													exp_approval_date,
													exp_approver_comment,
													exp_datetimestamp,
													exp_isactive
									FROM exp_expense_items 
									WHERE exp_user_id = '".$user_id."'
									AND exp_date_from between '".$period_start_date."' and '".$period_end_date."' 
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
						$query_child = "select exp_parent_id, exp_first_name, exp_last_name, exp_person_note, exp_person_note, exp_added_on   
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
											$str_details = $str_persons[1] . $str_phone . $str_email . $str_note;
											$str_details_short = $str_persons[1] ." ".$str_persons[2];
										} else {
											$str_details .= "<br><br>".$str_persons[1] . $str_phone . $str_email . $str_note;
											$str_details_short .= ", ". $str_persons[1] ." ".$str_persons[2];
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
							//if ($count_row_select%2) { $class_row_select = "trdark"; } else { $class_row_select = "trlight"; } 
							//class="'.$class_row_select.'"

							$str .= '<tr><td colspan="2">&nbsp;</td>
											 <td colspan="16">Desc: '.nl2br($row["exp_description"]).'</td></tr>';
							$str .= '<tr> 
              <td valign="top">'.date('m/d',strtotime($row["exp_date_from"])).'</td>
              <td valign="top">'.date('m/d',strtotime($row["exp_date_to"])).'</td>
              <td valign="top">'.$row["exp_division"].'</td>
              <td valign="top">'.$row["exp_type"].'&nbsp;</td>
              <td valign="top">'.$row["exp_c_p_name"].'&nbsp;</td>
              <td valign="top" align="right">'.number_format($row["exp_accomodation"],2,".",",").'</td> 
              <td valign="top" align="right">'.number_format($row["exp_transport_air"],2,".",",").'</td>
              <td valign="top" align="right">'.number_format($row["exp_transport_train"],2,".",",").'</td>
              <td valign="top" align="right">'.number_format($row["exp_transport_cab"],2,".",",").'</td>
              <td valign="top" align="right">'.number_format($row["exp_transport_rental"],2,".",",").'</td>
              <td valign="top" align="right">'.number_format($row["exp_transport_mileage"],2,".",",").'</td>
              <td valign="top" align="right">'.number_format($row["exp_transport_other"],2,".",",").'</td>
              <td valign="top" align="right">'.number_format($row["exp_food"],2,".",",").'</td>
              <td valign="top" align="right">'.number_format($row["exp_phone"],2,".",",").'</td>
              <td valign="top" align="right">'.number_format($row["exp_entertainment"],2,".",",").'</td>
              <td valign="top" align="right">'.number_format($row["exp_misc"],2,".",",").'</td>
              <td valign="top" align="right">'.number_format(($row["exp_accomodation"]
							                        +$row["exp_transport_air"]
							                        +$row["exp_transport_train"]
																			+$row["exp_transport_cab"]
																			+$row["exp_transport_rental"]
																			+$row["exp_transport_mileage"]
																			+$row["exp_transport_other"]
																			+$row["exp_food"]
																			+$row["exp_phone"]
																			+$row["exp_entertainment"]
																			+$row["exp_misc"]),2,".",",").'</td>
							<td valign="top">';

							if ($arr_persons_short[$row["auto_id"]]) {
								$str .= $arr_persons_short[$row["auto_id"]].'</td>';
							} else {
								$str .= '&nbsp;</td>';
							}
            	
							$str .= '</tr>';
							
              $r_sum_exp_accomodation = $r_sum_exp_accomodation + $row["exp_accomodation"];
              $r_sum_exp_transport_air = $r_sum_exp_transport_air + $row["exp_transport_air"];
              $r_sum_exp_transport_train = $r_sum_exp_transport_train + $row["exp_transport_train"];
              $r_sum_exp_transport_cab = $r_sum_exp_transport_cab + $row["exp_transport_cab"];
              $r_sum_exp_transport_rental = $r_sum_exp_transport_rental + $row["exp_transport_rental"];
              $r_sum_exp_transport_mileage = $r_sum_exp_transport_mileage + $row["exp_transport_mileage"];
              $r_sum_exp_transport_other = $r_sum_exp_transport_other + $row["exp_transport_other"];
              $r_sum_exp_food = $r_sum_exp_food + $row["exp_food"];
              $r_sum_exp_phone = $r_sum_exp_phone + $row["exp_phone"];
              $r_sum_exp_entertainment = $r_sum_exp_entertainment + $row["exp_entertainment"];
              $r_sum_exp_misc = $r_sum_exp_misc + $row["exp_misc"];
              $r_sum_grand = $r_sum_grand + $row["exp_accomodation"]
							                        +$row["exp_transport_air"]
							                        +$row["exp_transport_train"]
																			+$row["exp_transport_cab"]
																			+$row["exp_transport_rental"]
																			+$row["exp_transport_mileage"]
																			+$row["exp_transport_other"]
																			+$row["exp_food"]
																			+$row["exp_phone"]
																			+$row["exp_entertainment"]
																			+$row["exp_misc"];
							
						$count_row_select = $count_row_select + 1;
						}
							$str .= '<tr> 
              <td colspan="5"><strong>TOTALS</strong></td>
              <td valign="top" align="right">'.number_format($r_sum_exp_accomodation,2,".",",").'</td> 
              <td valign="top" align="right">'.number_format($r_sum_exp_transport_air,2,".",",").'</td>
              <td valign="top" align="right">'.number_format($r_sum_exp_transport_train,2,".",",").'</td>
              <td valign="top" align="right">'.number_format($r_sum_exp_transport_cab,2,".",",").'</td>
              <td valign="top" align="right">'.number_format($r_sum_exp_transport_rental,2,".",",").'</td>
              <td valign="top" align="right">'.number_format($r_sum_exp_transport_mileage,2,".",",").'</td>
              <td valign="top" align="right">'.number_format($r_sum_exp_transport_other,2,".",",").'</td>
              <td valign="top" align="right">'.number_format($r_sum_exp_food,2,".",",").'</td>
              <td valign="top" align="right">'.number_format($r_sum_exp_phone,2,".",",").'</td>
              <td valign="top" align="right">'.number_format($r_sum_exp_entertainment,2,".",",").'</td>
              <td valign="top" align="right">'.number_format($r_sum_exp_misc,2,".",",").'</td>
              <td valign="top" align="right">'.number_format($r_sum_grand,2,".",",").'</td>
							<td>&nbsp;</td></tr>';
							
	$str .= '</table>
	</body>
</html>';

/*$output_filename = "ExpenseReport.xls";
$fp = fopen($exportlocation.$output_filename, "w");
fputs ($fp, $str);
fclose($fp);

Header("Location: http://192.168.20.63/tdw/fileserve_xls.php?l=data/exports/&f=".$output_filename);*/
	$file_name_prefix = "ExpenseReport_".date('Hisa');
	$file_name = $file_name_prefix.".html";    
	$file_pdf_name = $file_name_prefix.".pdf"; 
	
$fp = fopen ("d:\\tdw\\tdw\\data\\prnt\\".$file_name, "w");  
fputs ($fp, $str);
fclose ($fp); 

$cmd_pdf = "d:\\tdw\\tdw\\includes\\createpdf_args.bat ". $file_pdf_name. " " . $file_name;
//echo $cmd_pdf."<br>";
shell_exec($cmd_pdf);

//delete the temp html file
$cmd = "del d:\\tdw\\tdw\\data\\prnt\\".$file_name;
//shell_exec($cmd);

header("Content-type: application/pdf");
header("Location: http://192.168.20.63/tdw/data/prnt/".$file_pdf_name);
?>