<?php
//BRG
include('inc_header.php');

function extract_client ($str) {
	$val_client_code = substr($str,strpos($str,"[")+1,6); //Code should be 4 characters long.
	$val_client_code = str_replace("]","",$val_client_code);
	return $val_client_code;
}

$payment_type = array();
$payment_type[1] = "Research - Research";
$payment_type[2] = "Research - Independent";
$payment_type[3] = "Research - Geneva";
$payment_type[4] = "Broker-to-Broker";
$payment_type[5] = "Trading 2";
$payment_type[6] = "Other";


//Create Lookup Array of Client Code / Client Name
$qry_clients = "select * from int_clnt_clients";
$result_clients = mysql_query($qry_clients) or die (tdw_mysql_error($qry_clients));
$arr_clients = array();
while ( $row_clients = mysql_fetch_array($result_clients) ) 
{
	$arr_clients[$row_clients["clnt_code"]] = $row_clients["clnt_name"];
}


?>
			<script type="text/javascript" src="includes/javascript/calendar/calendar.js"></script>
			<script type="text/javascript" src="includes/javascript/calendar/lang/calendar-en.js"></script>
			<!-- helper script that uses the calendar -->
			<script type="text/javascript" src="includes/javascript/calendar/helper.js"></script>
			<link rel="alternate stylesheet" type="text/css" media="all" href="includes/javascript/calendar/calendar-win2k-2.css" title="win2k-2" />
			<script type="text/javascript">setActiveStyleSheet(document.getElementById("defaultTheme"), "win2k-2");</script>
			
	<? tsp(100, "Checks/Payments Management"); ?>
	<?	
	  
		
		if($action == "remove")
		{
			$query_delete = "UPDATE chk_chek_payments_etc SET chek_isactive = '0' WHERE auto_id = '$ID'";
			$result_delete = mysql_query($query_delete) or die(mysql_error());
		}
		
		if ($_POST) {
			//print_r($_POST);
			$sel_datefrom = $datefrom;
			$sel_dateto = $dateto;

			if ($chk_type == 'ALL') {
			  $str_append = " AND a.chek_type like '%' ";
			} else {
			  $str_append = " AND a.chek_type = '".$chk_type."' ";
			}

			if ($val_client == 'Enter Client' or $val_client == '') {
			  $str_append_client = " AND a.chek_advisor like '%' ";
			} else {
			  $str_append_client = " AND a.chek_advisor = '". extract_client($val_client) ."' ";
			}

			if($sel_rep == '^ALL^') {
			  $str_append_rep = " AND a.chek_reps_and like '%' ";
			} else {
				$arr_repinfo = split('\^',$sel_rep);
				$rep_id = $arr_repinfo[1];
			  $rep_initials = db_single_val("select Initials as single_val from users where ID = '".$rep_id."'");
				$str_append_rep = " AND a.chek_reps_and like '%".$rep_initials."%' ";
			}

			//echo $str_append;
		} else {
		$sel_datefrom = format_date_ymd_to_mdy(business_day_backward(strtotime(date('Ymd')), 20)); 
		$sel_dateto = date('m/d/Y');
		$datefrom = format_date_ymd_to_mdy(business_day_backward(strtotime(date('Ymd')), 20)); 
		$dateto = date('m/d/Y');
		$chk_type = 'ALL';
		}
		
		
?>

			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
			<form name="chek_activity" id="idchek_activity" action="" method="post">
					<td width="10">&nbsp;</td>
					<SCRIPT LANGUAGE="JavaScript" SRC="includes/calendarpopup/CalendarPopup.js"></SCRIPT>
					<SCRIPT LANGUAGE="JavaScript">document.write(getCalendarStyles());</SCRIPT>
						<SCRIPT LANGUAGE="JavaScript">
						var calfrom = new CalendarPopup("divfrom");
						calfrom.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
						var calto = new CalendarPopup("divto");
						calto.addDisabledDates("<?=format_date_ymd_to_mdy(business_day_forward(strtotime(previous_business_day()),1))?>",null);
						</SCRIPT>																
					<td width="10">From:</td>
					<td width="10">&nbsp;</td>
					<td width="10"><input type="text" id="iddatefrom" class="Text1" name="datefrom" size="12" maxlength="12" value="<?=$sel_datefrom?>"></td>
					<td width="20" align="center"><A HREF="#" onClick="calfrom.select(document.forms['chek_activity'].datefrom,'anchor1','MM/dd/yyyy'); return false;" NAME="anchor1" ID="anchor1"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
					<td width="10">&nbsp;</td>
					<td width="10">To:</td>
					<td width="10">&nbsp;</td>
					<td width="10"><input type="text" id="iddateto" class="Text1" name="dateto" size="12" maxlength="12" value="<?=$sel_dateto?>"></td>
					<td width="20" align="center"><A HREF="#" onClick="calto.select(document.forms['chek_activity'].dateto,'anchor2','MM/dd/yyyy'); return false;" NAME="anchor2" ID="anchor2"><img src="images/lf_v1/sel_date.png" border="0"></A></td>
					<td width="10">&nbsp;</td>
          <td width="100">
          <select class="Text2" name="sel_rep" id="sel_rep" size="1" >
          <option value="^ALL^">&nbsp;REGISTERED REPS.&nbsp;(ALL)</option>
          <option value="^ALL^">____________</option>
          <?
          //get reps from query  on table mry_comm_rr_trades and join on users
          
          //*************************************************************************
          //This query with join on mry_comm_rr_trades was taking too long, altered
          //to just show reps.
          //*************************************************************************
          
          $qry_get_reps = "SELECT
                            a.ID, a.rr_num, concat(a.Firstname, ' ', a.Lastname ) as rep_name, a.rr_num as trad_rr 
                            from users a
                          WHERE a.rr_num like '0%'
                          AND a.Role > 2
                          AND a.Role < 5
                          ORDER BY a.Firstname";
          /*SELECT
                            a.ID, a.rr_num, concat(a.Firstname, ' ', a.Lastname ) as rep_name, b.trad_rr 
                            from users a, mry_comm_rr_trades b
                          WHERE a.rr_num = b.trad_rr
                          AND b.trad_rr like '0%'
                          AND a.Role > 2
                          AND a.Role < 5
                          GROUP BY b.trad_rr
                          ORDER BY a.Firstname	*/							
          
          $result_get_reps = mysql_query($qry_get_reps) or die (tdw_mysql_error($qry_get_reps));
          while($row_get_reps = mysql_fetch_array($result_get_reps))
          {
          //for trades for shared rep, do a reverse lookup in the users table to get the id and then the shared reps
          ?>
                    <option value="<?=$row_get_reps["trad_rr"]."^".$row_get_reps["ID"]?>" <? if ($sel_rep == $row_get_reps["trad_rr"]."^".$row_get_reps["ID"]) { echo 'selected="selected"';  } ?>><?=str_pad($row_get_reps["rep_name"], 20, ".")?>(<?=$row_get_reps["rr_num"]?>)</option>
          <?
          }
          ?>
          </select>
          </td>
					<td width="10">&nbsp;</td>
          <td width="205">
						<script language="javascript" type="text/javascript" src="includes/actb/actb.js"></script>
            <script language="javascript" type="text/javascript" src="includes/actb/common.js"></script>
            <script>
            var clientarray=new Array(
              <?
              /*$query_sel_client = "SELECT comm_advisor_code, trim(comm_advisor_name) as comm_advisor_name 
                                    FROM lkup_clients
                                    ORDER BY comm_advisor_name, comm_advisor_code";*/
																		
							$query_sel_client = "SELECT `clnt_code` as comm_advisor_code, 
																	trim(`clnt_name`) as comm_advisor_name FROM `int_clnt_clients`
																	where (`clnt_status` not like 'P%' AND `clnt_status` not like 'X%')
																	ORDER BY comm_advisor_name, comm_advisor_code";

              $result_sel_client = mysql_query($query_sel_client) or die(mysql_error());
              ?>																
              <?
              $count_row_client = 0;
              while($row_sel_client = mysql_fetch_array($result_sel_client))
              {
                if ($row_sel_client["comm_advisor_name"] == '') {
                $display_val_client = $row_sel_client["comm_advisor_code"];
                } else {
                $display_val_client = str_replace("'","\\'",$row_sel_client["comm_advisor_name"]);
                }
                echo "'". $display_val_client . "  [" .$row_sel_client["comm_advisor_code"]."]',"; //."\n"
              }
              ?>
              '');
            
            function set_val_null(str_id) {
              if (document.getElementById(str_id).value == 'Enter Client') {
                document.getElementById(str_id).value = ""; 
              }
            }
            </script>
            <input type='text' name="val_client" style='font-family:verdana;width:250px;font-size:12px' id='tb' value='Enter Client' onFocus="set_val_null('tb')" /> 
						<script>
              obj = new actb(document.getElementById('tb'),clientarray);
            </script>
          </td>
					<td width="10">&nbsp;</td>
					<td width="10">
					<select name="chk_type" id="idchktype" size="1">
					  <option value="ALL">Check Types (ALL)</option>
					<?
					foreach($payment_type as $k=>$v) {
						if ($k == $chk_type) {
						  echo '<option value="'.$k.'" selected>'.$v.'</option>';
						} else {
						  echo '<option value="'.$k.'">'.$v.'</option>';
						}
					}
					?>
					</select>
					</td>
					<td width="10">&nbsp;</td>
					<td width="10"><input type="image" src="images/lf_v1/form_submit.png"></td>
					<td width="10" align="center">&nbsp;</td>
			 </form>															
					<?
					//$passtoexcel = $datefrom.'^'.$dateto.'^'.$chk_type;
					?>
					<td width="80">
          
          <script language="javascript">
					function go_excel () {
						document.prnt_excel.datefrom.value =  document.chek_activity.iddatefrom.value;
						document.prnt_excel.dateto.value =    document.chek_activity.dateto.value;
						document.prnt_excel.clnt.value =      '<?=$val_client?>';
						document.prnt_excel.info_str.value =  '<?=$userfullname?>';
						document.prnt_excel.chk_type.value =  document.getElementById('idchktype').options[document.getElementById('idchktype').selectedIndex].value;
						document.prnt_excel.rep.value =       document.getElementById('sel_rep').value;
					}
					</script>
					<form name="prnt_excel" action="check_mgmt_export_excel.php" method="get" target="_blank">
						<input type="image" src="images/lf_v1/exp2excel.png" border="0" alt="Output to Excel" onclick="go_excel()" />&nbsp;&nbsp;
						<input type="hidden" name="datefrom" value="" />
						<input type="hidden" name="dateto" value="" />
						<input type="hidden" name="info_str" value="" />
						<input type="hidden" name="chk_type" value="" />
						<input type="hidden" name="rep" value="" />
						<input type="hidden" name="clnt" value="" />
					</form>

          
<!--          <a href="check_mgmt_export_excel.php?xl=<?=$passtoexcel?>" target="_blank"><img src="images/lf_v1/exp2excel.png" border="0"></a></td>
-->					<td width="10">&nbsp;</td>
					<td width="30">
					<script language="javascript">
					function go_prntscrn () {
						document.prntscrn.datefrom.value =  document.chek_activity.iddatefrom.value;
						document.prntscrn.dateto.value =    document.chek_activity.dateto.value;
						document.prntscrn.clnt.value =    '<?=$val_client?>';
						document.prntscrn.info_str.value =  '<?=$userfullname?>';
						document.prntscrn.chk_type.value =  document.getElementById('idchktype').options[document.getElementById('idchktype').selectedIndex].value;
						document.prntscrn.rep.value =       document.getElementById('sel_rep').value;
					}
					</script>
					<form name="prntscrn" action="check_mgmt_print.php" method="get" target="_blank">
						<input type="image" src="images/printer.png" border="0" alt="Print content of Window." onclick="go_prntscrn()" />&nbsp;&nbsp;
						<input type="hidden" name="datefrom" value="" />
						<input type="hidden" name="dateto" value="" />
						<input type="hidden" name="info_str" value="" />
						<input type="hidden" name="chk_type" value="" />
						<input type="hidden" name="rep" value="" />
						<input type="hidden" name="clnt" value="" />
					</form>
					</td>
					<td>&nbsp;</td>
			</tr>
			</table>
		<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
			<tr>
				<td>
					<!--TABLE 2 START-->
					<script language="JavaScript" src="includes/javascript/sorttable.js" type="text/javascript"></script>

					<?
          $qry_payments = "SELECT a.*, b.Fullname 
											from chk_chek_payments_etc a, 
											Users b 
											where a.chek_entered_by = b.ID
											and a.chek_date between '".format_date_mdy_to_ymd($sel_datefrom)."' and '".format_date_mdy_to_ymd($sel_dateto)."' 
											and a.chek_isactive = 1 ".
											$str_append.
											$str_append_client.
											$str_append_rep.
											"	order by a.chek_date desc";
						
						//xdebug("qry_payments",$qry_payments);
          ?>
					<table class="sortable" preserve_style="cell" width="100%"  border="0" cellspacing="1" cellpadding="1">
						<tr>
						  <td width="28">DEL</td>
							<td width="30">EDIT</td>
							<td width="80">Date</td>
							<td width="80">Amount</td>
							<td width="200">Type</td>
							<td width="60">Client</td>
							<td width="160">Client Name</td>
							<td width="80">Comments</td>
							<td width="120">Entered By</td>
							<td>&nbsp;</td>
						</tr>
						
							<script type="text/javascript">
							var displaychecks = new Array()
							<? 

						$result_payments = mysql_query($qry_payments) or die(mysql_error());
						$count_row = 0;
						$capture_chk_total = 0;
						while ( $row = mysql_fetch_array($result_payments) ) 
						{
						echo 'displaychecks ['.$count_row.'] = "'.$row["auto_id"].'^'.
																									format_date_ymd_to_mdy($row["chek_date"]).'^'.
																									number_format($row["chek_amount"],2,".",",").'^'.
																									$payment_type[$row["chek_type"]].'^'.
																									$row["chek_advisor"].'^'.
																									trim($arr_clients[$row["chek_advisor"]]).'^'.
																									$row["chek_comments"].'^'.
																									$row["Fullname"].'^'.
																									" ".'"'.";\n";
						$capture_chk_total = $capture_chk_total + $row["chek_amount"];
						$count_row = $count_row + 1;
						}
						?>
							for (i=0;i<displaychecks.length;i++)
							{
							var rowclients_array = new Array()
							var rowclass
							if (i%2 == 0) {
								rowclass = " class=\"trdark\"";
							} else {
								rowclass = " class=\"trlight\"";
							}
							
							rowchecks_array=displaychecks[i].split("^");
							
							document.write(
									"<tr" + rowclass + ">"+
									"<td nowrap>&nbsp; <a href=\"check_mgmt.php?type=manage&action=remove&ID="+rowchecks_array[0]+"\"  onclick=\"javascript:return confirm('Are you sure you want to remove the check for $"+rowchecks_array[2]+" from the list?')\"><img src=\"images/themes/standard/delete.gif\" alt=\"Delete\"></a>&nbsp; </td>"+
									"<td nowrap>&nbsp; <a href=\"javascript:CreateWnd(\'check_mgmt_edit.php?ID="+rowchecks_array[0]+"&user_id=<?=$user_id?>', 550, 350, false);\"><img src=\"images/themes/standard/edit.gif\" alt=\"Edit\"></a>&nbsp; </td>"+
									"<td>&nbsp;"+rowchecks_array[1]+"</td>"+ 
									"<td align=\"right\">"+rowchecks_array[2]+"&nbsp;</td>"+ 
									"<td>&nbsp;"+rowchecks_array[3]+"</td>"+ 
									"<td>&nbsp;"+rowchecks_array[4]+"</td>"+ 
									"<td>&nbsp;"+rowchecks_array[5]+"</td>"+ 
									"<td>&nbsp;"+rowchecks_array[6]+"</td>"+ 
									"<td>&nbsp;"+rowchecks_array[7]+"</td>"+ 
									"<td>&nbsp;</td></tr>");							
							}
							</script>
									<tr class="display_totals">
                    <td colspan="3" nowrap>&nbsp; TOTAL:</td>
                    <td align="right">&nbsp;<?=number_format($capture_chk_total,2,".",",")?>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>				
					</table>
				</td>
			</tr>
		</table>
	
		<? tep(); ?>
			<DIV ID="divfrom" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
    	<DIV ID="divto" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>			
<?
 include('inc_footer.php');
?>
