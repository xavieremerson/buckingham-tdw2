<?
   include 'includes/global.php';
   include 'includes/dbconnect.php';
   include 'includes/functions.php';
   
	 //$report_id = 2401;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html> 
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Add/View Notes</title>
<link rel="stylesheet" type="text/css" href="includes/styles.css" />
<style type="text/css">
<!--
#scrollElement { 	width: 590px;	height: 370px;	padding: 1px;	border: 1px solid #cc0000;	overflow: scroll; }
.compnotes {	font-family: verdana;	font-size: 11px;	color: #000066;	text-decoration: none; }
label {	font-family: verdana;	font-size: 10px;	color: #000066;	text-decoration: none; }
-->
</style>
</head>

<body leftmargin="3" topmargin="3" rightmargin="3" bottommargin="3"> <!-- onunload="window.opener.location.reload();self.close();return false;" -->
		 <?	   
			//create an array of report id's between the selected dates.
			$arr_rid = array();
			$qry_rid = "select auto_id, msrv_trade_date 
									from mgmt_reports_creation 
									where msrv_trade_date between '2011-08-01' and '2011-08-31'
									and msrv_rep_id = 'DCARV2'"; 
									//between '2011-01-27' and '2011-01-31'
			$res_rid = mysql_query($qry_rid) or die(tdw_mysql_error($qry_rid));
			while ($row = mysql_fetch_array($res_rid)) {
					$arr_rid[$row["msrv_trade_date"]] = $row["auto_id"];
			}
			
			foreach ($arr_rid as $tdate=>$report_id) {
			
				?>
        <font face="Verdana, Arial, Helvetica, sans-serif" style="font-size:14px" color="#0000FF"><strong>Trade Date: <?=format_date_ymd_to_mdy($tdate)?></strong></font>
        <?
				
				//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
				//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
				//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
				//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

					 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 ?>
					 <?
					 //First get Potential Agency Cross data if any.
					 $val_pac_exists = db_single_val("select count(*) as single_val from crep_agency_cross where pac_rep_id = '".$report_id."'");
					 ?>
					 <?
					 if ($val_pac_exists > 0)   {
					 ?>
           &nbsp;&nbsp;<a class="ilt" align="right">Potential Agency Cross</a>
					 <div id="div_pac_items">
					 <table width="100%" class="compnotes">
					 <? 
							$str_sql = "SELECT * 
																 FROM crep_agency_cross 
																 WHERE pac_rep_id = '".$report_id."' 
																 AND pac_isactive = 1
																 ORDER BY pac_entered_on DESC";
																 //xdebug("rep_auto_id",$rep_auto_id);
							$result = mysql_query($str_sql) or die(tdw_mysql_error($str_sql));
							while ( $row = mysql_fetch_array($result) ) {
							$commentor = db_single_val("select Fullname as single_val from users where ID = '".$row['pac_entered_by']."'");  
							?>
							<tr><td><img src="images/spacer.gif" width="10" height="1" /></td><td>Symbol: <strong><?=$row['pac_symbol']?></strong>
							&nbsp;&nbsp;Is Potential Agency Cross? 
							<? if ($row['pac_yes_no'] == 1) { echo '<b>Yes</b>'; } else {  echo '<b>No</b>'; } ?>
							&nbsp;&nbsp;Date : <?=date('m/d',strtotime($row['pac_entered_on']))?>&nbsp;&nbsp; By: <?=$commentor?>
							&nbsp;&nbsp;&nbsp;&nbsp;<? if ($row['pac_isopen'] == 1) { echo "<a id='link_close_pac_". $row['pac_auto_id']. "' href=\"javascript:close_out_item('pac',".$row['pac_auto_id'].");\">[Close]</a>"; } ?> </td>
							<tr>
							<? if ($row['pac_isopen'] == 1) { echo "<tr><td></td><td id='close_pac_".$row['pac_auto_id']."'></td></tr>"; } ?>
							<tr><td>&nbsp;</td><td>
							<?
							if ($row['pac_isopen'] == 1) {
								echo '<div id="old_comment_pac_'.$row['pac_auto_id'].'">'.nl2br($row['pac_comment']).'</div>';
							} else {
								echo nl2br($row['pac_comment']);
							}
							?>
							<br /><img src="images/bdot.png" width="720" height="1" /></td></tr>
							<?
							}
					 ?>
					 </table>
					 </div>
					 <?
					 } else {
					 ?>
							<div id="div_pac_items"></div>
					 <?
					 }
					 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 ?>
					 <?
					 $val_emp_exists = db_single_val("select count(*) as single_val from crep_emp_trades where emp_rep_id = '".$report_id."'");
					 ?>
					 <?
					 if ($val_emp_exists > 0)   {
					 ?>
					 &nbsp;&nbsp;<a class="ilt" align="right">Employee Trades</a>
					 <div id="div_emp_items">
					 <table width="100%" class="compnotes">
					 <? 
							$str_sql = "SELECT * FROM crep_emp_trades 
																 WHERE emp_rep_id = '".$report_id."' 
																 AND emp_isactive = 1
																 ORDER BY emp_entered_on DESC";
							$result = mysql_query($str_sql) or die(tdw_mysql_error($str_sql));
							while ( $row = mysql_fetch_array($result) ) {
							?>
							<tr><td><img src="images/spacer.gif" width="10" height="1" /></td><td>Symbol: <strong><?=$row['emp_symbol']?></strong>
							&nbsp;&nbsp;Date : <?=date('m/d',strtotime($row['emp_entered_on']))?>&nbsp;&nbsp; By: <?=get_user_by_id($row['emp_entered_by'])?>
							&nbsp;&nbsp;&nbsp;&nbsp;<? if ($row['emp_isopen'] == 1) { echo "<a id='link_close_emp_". $row['emp_auto_id']. "' href=\"javascript:close_out_item('emp',".$row['emp_auto_id'].");\">[Close]</a>"; } ?> 
							<br />Employee: <strong><?=get_user_by_id($row['emp_emp_id'])?></strong>&nbsp;&nbsp; Approver: <strong><?=get_user_by_id($row['emp_approver'])?></strong>
							</td></tr>
							<tr>
								<td>&nbsp;</td>
								<td>
								<?
								if ($row['emp_trade_type'] == 2) { 
								?>
									Trade Type : <strong>Vs. Restricted List</strong>          
								<?
								} 
								
								if ($row['emp_trade_type'] == 1){
									$client_val = db_single_val("select clnt_name as single_val from int_clnt_clients where clnt_code = '".$row['emp_client_id']."'");
								?>
									Trade Type : <strong>Vs. Client</strong> Client: <strong><?=$client_val?></strong>          
								<?
								}
								?>
								<?
								if ($row['emp_trade_type'] == 3){
								?>
									Trade Type : <strong>Trade Approval Exception</strong>          
								<?
								}
								?>
								</td>
							</tr>        
							<tr>
							<? if ($row['emp_isopen'] == 1) { echo "<tr><td></td><td id='close_emp_".$row['emp_auto_id']."'></td></tr>"; } ?>
							
							<tr><td>&nbsp;</td><td>
							<?
							if ($row['emp_isopen'] == 1) {
								echo '<div id="old_comment_emp_'.$row['emp_auto_id'].'">'.nl2br($row['emp_comment']).'</div>';
							} else {
								echo nl2br($row['emp_comment']);
							}
							?>
							<br /><img src="images/bdot.png" width="720" height="1" /></td></tr>
							<?
							}
					 ?>
					 </table>
					 </div>
					 <?
					 } else {
					 ?>
					 <div id="div_emp_items"></div>
					 <?
					 }
					 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 ?>
								<?
					 $val_mri_exists = db_single_val("select count(*) as single_val from crep_mri_trades where mri_rep_id = '".$report_id."'");
					 ?>
					 <?
					 if ($val_mri_exists > 0)   {
					 ?>
					 &nbsp;&nbsp;<a class="ilt" align="right">MRI</a>
					 <div id="div_mri_items">
					 <table width="100%" class="compnotes">
					 <? 
							$str_sql = "SELECT * FROM crep_mri_trades 
																 WHERE mri_rep_id = '".$report_id."' 
																 AND mri_isactive = 1
																 ORDER BY mri_entered_on DESC";
							$result = mysql_query($str_sql) or die(tdw_mysql_error($str_sql));
							while ( $row = mysql_fetch_array($result) ) {
							?>
							<tr><td><img src="images/spacer.gif" width="10" height="1" /></td><td>Symbol: <strong><?=$row['mri_symbol']?></strong>
							&nbsp;&nbsp;Date : <?=date('m/d',strtotime($row['mri_entered_on']))?>&nbsp;&nbsp; By: <?=get_user_by_id($row['mri_entered_by'])?>&nbsp;&nbsp; Employee: <strong><?=get_user_by_id($row['mri_emp_mri'])?></strong>
							&nbsp;&nbsp;&nbsp;&nbsp;<? if ($row['mri_isopen'] == 1) { echo "<a id='link_close_mri_". $row['mri_auto_id']. "' href=\"javascript:close_out_item('mri',".$row['mri_auto_id'].");\">[Close]</a>"; } ?> 
							<br />
							Rating: <strong><?=$row['mri_rating']?></strong>&nbsp;&nbsp;
							Target: <strong><?=$row['mri_target']?></strong>&nbsp;&nbsp;
							Analyst: <strong><?=get_user_by_id($row['mri_analyst'])?></strong>&nbsp;&nbsp;
							Port. Mgr: <strong><?=get_user_by_id($row['mri_portfol_mgr'])?></strong>&nbsp;&nbsp;
							<br />
							T-0: <strong><?=$row['mri_t-0']?></strong>&nbsp;&nbsp; 
							T-1: <strong><?=$row['mri_t-1']?></strong>&nbsp;&nbsp; 
							T-2: <strong><?=$row['mri_t-2']?></strong>&nbsp;&nbsp; 
							T-3: <strong><?=$row['mri_t-3']?></strong>&nbsp;&nbsp; 
							T-4: <strong><?=$row['mri_t-4']?></strong>&nbsp;&nbsp; 
							MRI Required: <strong><? if ($row['mri_required'] == 1) { echo "Yes"; } else { echo "No"; } ?></strong>&nbsp;&nbsp; 
							</td>
							<tr>
							<? if ($row['mri_isopen'] == 1) { echo "<tr><td></td><td id='close_mri_".$row['mri_auto_id']."'></td></tr>"; } ?>
							<tr><td>&nbsp;</td><td>
							<?
							if ($row['mri_isopen'] == 1) {
								echo '<div id="old_comment_mri_'.$row['mri_auto_id'].'">'.nl2br($row['mri_comment']).'</div>';
							} else {
								echo nl2br($row['mri_comment']);
							}
							?>
							<br /><img src="images/bdot.png" width="720" height="1" /></td></tr>
							<?
							}
					 ?>
					 </table>
					 </div>
					 <?
					 } else {
					 ?>
					 <div id="div_mri_items"></div>
					 <?
					 }
					 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 ?>
					 <?
					 //First get SRA data if any.
					 $val_sra_exists = db_single_val("select count(*) as single_val from crep_sra_approval where sra_rep_id = '".$report_id."'");
					 ?>
					 <?
					 if ($val_sra_exists > 0)   {
					 ?>
					 &nbsp;&nbsp;<a class="ilt" align="right">Sales & Research Approval</a>
					 <div id="div_sra_items">
					 <table width="100%" class="compnotes">
					 <? 
							$str_sql = "SELECT * 
																 FROM crep_sra_approval 
																 WHERE sra_rep_id = '".$report_id."' 
																 AND sra_isactive = 1
																 ORDER BY sra_entered_on DESC";
																 //xdebug("rep_auto_id",$rep_auto_id);
							$result = mysql_query($str_sql) or die(tdw_mysql_error($str_sql));
							while ( $row = mysql_fetch_array($result) ) {
							$commentor = db_single_val("select Fullname as single_val from users where ID = '".$row['sra_entered_by']."'");  
							?>
							<tr><td><img src="images/spacer.gif" width="10" height="1" /></td><td>Date : <?=date('m/d',strtotime($row['sra_entered_on']))?>&nbsp;&nbsp; By: <?=$commentor?>
							&nbsp;&nbsp;&nbsp;&nbsp;<? if ($row['sra_isopen'] == 1) { echo "<a id='link_close_sra_". $row['sra_auto_id']. "' href=\"javascript:close_out_item('sra',".$row['sra_auto_id'].");\">[Close]</a>"; } ?> </td>
							<tr>
							<? if ($row['sra_isopen'] == 1) { echo "<tr><td></td><td id='close_sra_".$row['sra_auto_id']."'></td></tr>"; } ?>
							<tr><td>&nbsp;</td><td>
							<?
							if ($row['sra_isopen'] == 1) {
								echo '<div id="old_comment_sra_'.$row['sra_auto_id'].'">'.nl2br($row['sra_comment']).'</div>';
							} else {
								echo nl2br($row['sra_comment']);
							}
							?>
							<br /><img src="images/bdot.png" width="720" height="1" /></td></tr>
							<?
							}
					 ?>
					 </table>
					 </div>
					 <?
					 } else {
					 ?>
							<div id="div_sra_items"></div>
					 <?
					 }
					 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
					 ?>
					 <?
					 //First get Other data if any.
					 $val_oth_exists = db_single_val("select count(*) as single_val from crep_other_notes where oth_rep_id = '".$report_id."'");
					 ?>
					 <?
					 if ($val_oth_exists > 0)   {
					 ?>
					 &nbsp;&nbsp;<a class="ilt" align="right">Others</a>
					 <div id="div_oth_items">
					 <table width="100%" class="compnotes">
					 <? 
							$str_sql = "SELECT * 
																 FROM crep_other_notes 
																 WHERE oth_rep_id = '".$report_id."' 
																 AND oth_isactive = 1
																 ORDER BY oth_entered_on DESC";
																 //xdebug("rep_auto_id",$rep_auto_id);
							$result = mysql_query($str_sql) or die(tdw_mysql_error($str_sql));
							while ( $row = mysql_fetch_array($result) ) {
							$commentor = db_single_val("select Fullname as single_val from users where ID = '".$row['oth_entered_by']."'");  
							?>
							<tr><td><img src="images/spacer.gif" width="10" height="1" /></td><td>Date : <?=date('m/d',strtotime($row['oth_entered_on']))?>&nbsp;&nbsp; By: <?=$commentor?>
							&nbsp;&nbsp;&nbsp;&nbsp;<? if ($row['oth_isopen'] == 1) { echo "<a id='link_close_oth_". $row['oth_auto_id']. "' href=\"javascript:close_out_item('oth',".$row['oth_auto_id'].");\">[Close]</a>"; } ?> </td>
							<tr>
							<? if ($row['oth_isopen'] == 1) { echo "<tr><td></td><td id='close_oth_".$row['oth_auto_id']."'></td></tr>"; } ?>
							<tr><td>&nbsp;</td><td>
							<?
							if ($row['oth_isopen'] == 1) {
								echo '<div id="old_comment_oth_'.$row['oth_auto_id'].'">'.nl2br($row['oth_comment']).'</div>';
							} else {
								echo nl2br($row['oth_comment']);
							}
							?>
							<br /><img src="images/bdot.png" width="720" height="1" /></td></tr>
							<?
							}
					 ?>
					 </table>
					 </div>
					 <?
					 } else {
					 ?>
							<div id="div_oth_items"></div>
					 <?
					 }

				//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
				//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
				//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
				//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
			}
			exit;

		 ?>		 
</body>
</html>