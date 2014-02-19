<?
   include 'includes/global.php';
   include 'includes/dbconnect.php';
   include 'includes/functions.php';
   
	 $report_id = $rep_auto_id;

	 //********************************************************************
	 //Prototype
	 ?>
   <script src="includes/prototype/prototype.js" language="javascript"></script>
   <script language="javascript">
			function get_code(ctype) {

				var url = 'http://192.168.20.63/tdw/rep_compliance_notes_ajx.php';
				var pars = 'mod_request=getcode';
				pars = pars + '&ctype='+ ctype;
				var zdiv = "zdiv_"+ ctype;
				pars = pars + '&divcount='+ $(zdiv).value;
				pars = pars + '&xrand='+ Math.random();
				//alert(pars);
				new Ajax.Request
				(
					url,   
					{     
						method:'get', 
						parameters:pars,    
						onSuccess: 
							function(transport){       
								var response = transport.responseText;  
								//if (ctype == 'pac') {
									var curr_div = "zdiv_" + ctype +"_"+ $(zdiv).value;
									//alert(curr_div);
									//alert(response);
									$(curr_div).innerHTML = response;
									$(zdiv).value = eval($(zdiv).value) + 1;
									if ($("save_" + ctype).innerHTML == "") {
										$("save_" + ctype).innerHTML = '&nbsp;&nbsp;&nbsp;<input type="image" id="btn_"' + ctype + ' src="images/btn_save.png" onClick="save_data(\'' + ctype + '\')" />';
									}
								//}     
								//$("err").innerHTML = response;
							},     
						onFailure: 
						function(){ $("div_" + ctype).innerHTML = "Error accessing TDW Server [CODE:1513]"; }
					}
				);
			}

			function save_data(ctype) {
			
				//alert("zdiv_"+ctype+"0");
				//don't fire if zdiv_pac_0 is mrity
				if ($("zdiv_"+ctype+"_0").innerHTML == '') {
					return false;
				}

				var url = 'http://192.168.20.63/tdw/rep_compliance_notes_ajx.php';
				
						var pars = 'mod_request=savedata';
						pars = pars + '&ctype='+ ctype;
						pars = pars + '&rep_auto_id='+ <?=$rep_auto_id?>;
						pars = pars + '&user_id='+ <?=$user_id?>;
						pars = pars + '&' + $("id_form_"+ctype).serialize(); 
						pars = pars + '&xrand='+ Math.random();

						//alert(pars);

						new Ajax.Request
						(
							url,   
							{     
								method:'get', 
								parameters:pars,    
								onSuccess: 
									function(transport){       
										var response = transport.responseText;  
											//alert(response);
											//alert("div_"+ctype+"_items");
											$("div_"+ctype+"_items").innerHTML = response;
											$("div_"+ctype).innerHTML = '<div id="zdiv_'+ctype+'_0"></div>';
											$("link_"+ctype).focus();
											$("save_"+ctype).innerHTML == "";
										//$("err").innerHTML = response;
									},     
								onFailure: 
								function(){ $("div_"+ctype).innerHTML = "Error accessing TDW Server [CODE:1513]"; }
							}
						);
			$("zdiv_"+ ctype).value = 0;
			return false;
			}

	 function close_out_item(ctype, itemid) {
	 		var show_td = 'close_'+ctype+'_' + itemid;
			$(show_td).innerHTML = '<textarea rows=3 cols=80 id="'+ctype+'_close_comment_'+itemid+'">'+
														 '</textarea>&nbsp;<input type="button" id="'+ctype+'_save_'+itemid+'" value="Save" onClick="close_comment(\''+ctype+'\','+itemid+');return false;">';
	 }
	 
	 function close_comment (ctype, itemid) {

				var url = 'http://192.168.20.63/tdw/rep_compliance_notes_ajx.php';
				var val_comment = $(ctype+'_close_comment_'+itemid).value;
				if (val_comment == '') {
					alert("To close an open action pending item, a comment must be entered.\nPlease enter a comment to proceed.");
					return false;
				}
				var pars = 'mod_request=close_item';
				pars = pars + '&ctype='+ ctype;
				pars = pars + '&itemid='+ itemid;
				pars = pars + '&rep_auto_id='+ <?=$rep_auto_id?>;
				pars = pars + '&user_id='+ <?=$user_id?>;
				pars = pars + '&val_comment=' + val_comment; 
				pars = pars + '&xrand='+ Math.random();
				//alert(pars);
				new Ajax.Request
				(
					url,   
					{     
						method:'get', 
						parameters:pars,    
						onSuccess: 
							function(transport){        
								var response = transport.responseText;  
									$("old_comment_"+ctype+"_" + itemid).style.visibility ="hidden";
									$("old_comment_"+ctype+"_" + itemid).style.display = 'none'; 
									$("link_close_"+ctype+"_" + itemid).style.visibility ="hidden";
									$("link_close_"+ctype+"_" + itemid).style.display = 'none';
									var show_td = 'close_'+ctype+'_' + itemid;
									$(show_td).innerHTML = response;
							},       
						onFailure: 
						function(){ $("div_"+ctype).innerHTML = "Error accessing TDW Server [CODE:1513]"; }
					}
				);
		return false;
	 }
	 </script>  

<SCRIPT LANGUAGE="JavaScript">
<!--
function showhide_section(str) { 
	if (document.getElementById) { // DOM3 = IE5, NS6 Generally this is what it is

		var div_val;
		var chk_val;
		
		div_val = 'div_' + str;
		chk_val = 'chk_' + str;
		
		//alert (div_val + "\n" + chk_val);

		if (document.getElementById(chk_val).checked == true) {                               // document.getElementById("div_pac").style.getAttribute("visibility") == "" || document.getElementById("div_pac").style.getAttribute("visibility") == "hidden" ) {
		document.getElementById(div_val).style.visibility = 'visible'; 
		document.getElementById(div_val).style.display = 'block';  //block
		} else {
		document.getElementById(div_val).style.visibility = 'hidden'; 
		document.getElementById(div_val).style.display = 'none'; 
		}		
	} 
} 
-->
</SCRIPT>

<Script Language=JavaScript>
//Allow for auto expandable textarea
function cursorEOT(isField){
	isRange = isField.createTextRange();
	isRange.move('textedit');
	isRange.select();
	testOverflow = isField.scrollTop;
	if (testOverflow != 0){return true}
	else {return false}
}

function adjustRows(isField){
	while (cursorEOT(isField)){isField.rows++}
}

function clrAndUcase (tbox) {
	if (tbox.value == "SYMBOL") {
		tbox.value = "";
	} else {
		tbox.value = tbox.value.toUpperCase();
	}
}
</Script>
<?
//print_r($_POST);
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
	   tsp(100, "Notes")
		 ?>
		 <?	
		 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		 ?>
     &nbsp;&nbsp;<a class="ilt" align="right">Potential Agency Cross</a>
     <?
		 //First get Potential Agency Cross data if any.
		 $val_pac_exists = db_single_val("select count(*) as single_val from crep_agency_cross where pac_rep_id = '".$report_id."'");
		 ?>
		 <?
		 if ($val_pac_exists > 0)   {
		 ?>
		 <div id="div_pac_items">
		 <table width="100%" class="compnotes">
		 <? 
				$str_sql = "SELECT * 
													 FROM crep_agency_cross 
													 WHERE pac_rep_id = '".$rep_auto_id."' 
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
		 ?>
		 <form id="id_form_pac" onSubmit="return false;">
		 <div id="div_pac"><div id="zdiv_pac_0"></div></div> 
     <table><tr><td><a id="link_pac" href="javascript:get_code('pac')"><img src="images/btn_add.gif" border="0" /></a></td><td id="save_pac"></td>
     <td><input type="hidden" name="pac" value="pac" />
     <input type="hidden" id="zdiv_pac" name="zdiv_pac" value="0" />
		 </form></td>
     </tr></table>
		 
     
		 <hr width="100%" size="2" noshade color="#990000" />
		 <?	
		 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		 ?>
     &nbsp;&nbsp;<a class="ilt" align="right">Employee Trades</a>
     <?
		 $val_emp_exists = db_single_val("select count(*) as single_val from crep_emp_trades where emp_rep_id = '".$report_id."'");
		 ?>
		 <?
		 if ($val_emp_exists > 0)   {
		 ?>
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
						if ($row['emp_client_id'] != '') {
          	$client_val = db_single_val("select clnt_name as single_val from int_clnt_clients where clnt_code = '".$row['emp_client_id']."'");
						} else {
						$client_val = "";
						}
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
		 ?>
		 <form id="id_form_emp" onSubmit="return false;">
		 <div id="div_emp"><div id="zdiv_emp_0"></div></div> 
     <table><tr><td><a id="link_emp" href="javascript:get_code('emp')"><img src="images/btn_add.gif" border="0" /></a></td><td id="save_emp"></td><td id="indicator_emp"></td>
     <td><input type="hidden" name="emp" value="emp" />
     <input type="hidden" id="zdiv_emp" name="zdiv_emp" value="0" />
		 </form></td>
     </tr></table>
		 
     
		 <hr width="100%" size="2" noshade color="#990000" />
		 <?	
		 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		 ?>
		 &nbsp;&nbsp;<a class="ilt" align="right">MRI</a>
          <?
		 $val_mri_exists = db_single_val("select count(*) as single_val from crep_mri_trades where mri_rep_id = '".$report_id."'");
		 ?>
		 <?
		 if ($val_mri_exists > 0)   {
		 ?>
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
		 ?>
		 <form id="id_form_mri" onSubmit="return false;">
		 <div id="div_mri"><div id="zdiv_mri_0"></div></div> 
     <table><tr><td><a id="link_mri" href="javascript:get_code('mri')"><img src="images/btn_add.gif" border="0" /></a></td><td id="save_mri"></td><td id="indicator_mri"></td>
     <td><input type="hidden" name="mri" value="mri" />
     <input type="hidden" id="zdiv_mri" name="zdiv_mri" value="0" />
		 </form></td>
     </tr></table>
		 
     
		 <hr width="100%" size="2" noshade color="#990000" />

		 <?	
		 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		 ?>
		 &nbsp;&nbsp;<a class="ilt" align="right">Sales & Research Approval</a>
     <?
		 //First get SRA data if any.
		 $val_sra_exists = db_single_val("select count(*) as single_val from crep_sra_approval where sra_rep_id = '".$report_id."'");
		 ?>
		 <?
		 if ($val_sra_exists > 0)   {
		 ?>
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
		 ?>
		 <form id="id_form_sra" onSubmit="return false;">
		 <div id="div_sra"><div id="zdiv_sra_0"></div></div> 
     <table><tr><td><a id="link_sra" href="javascript:get_code('sra')"><img src="images/btn_add.gif" border="0" /></a></td><td id="save_sra"></td><td id="indicator_sra"></td>
     <td><input type="hidden" name="sra" value="sra" />
     <input type="hidden" id="zdiv_sra" name="zdiv_sra" value="0" />
		 </form></td>
     </tr></table>
		 
 		 <hr width="100%" size="2" noshade color="#990000" />
		 <?	
		 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		 ?>
 		 &nbsp;&nbsp;<a class="ilt" align="right">Others</a>
     <?
		 //First get Other data if any.
		 $val_oth_exists = db_single_val("select count(*) as single_val from crep_other_notes where oth_rep_id = '".$report_id."'");
		 ?>
		 <?
		 if ($val_oth_exists > 0)   {
		 ?>
		 <div id="div_oth_items">
		 <table width="100%" class="compnotes">
		 <? 
				$str_sql = "SELECT * 
													 FROM crep_other_notes 
													 WHERE oth_rep_id = '".$rep_auto_id."' 
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
		 ?>
		 <form id="id_form_oth" onSubmit="return false;">
		 <div id="div_oth"><div id="zdiv_oth_0"></div></div> 
     <table><tr><td><a id="link_oth" href="javascript:get_code('oth')"><img src="images/btn_add.gif" border="0" /></a></td><td id="save_oth"></td><td id="indicator_oth"></td>
     <td><input type="hidden" name="oth" value="oth" />
     <input type="hidden" id="zdiv_oth" name="zdiv_oth" value="0" />
		 </form></td>
     </tr></table>
		 
 		 <hr width="100%" size="2" noshade color="#990000" />
			<!-- 0000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000 -->
		 <?	
	   tep();
		 ?>		 
</body>
</html>