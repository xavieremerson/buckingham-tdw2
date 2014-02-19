<?
include 'includes/global.php';
include 'includes/dbconnect.php';
include 'includes/functions.php';

if ($mod_request=='getcode') {

	if ($ctype == 'mri') {
		?>
			<hr size="1" noshade color="#CCCCCC" />
			<table width="100%" border="0">
				<tr>
					<td width="115" align="left" nowrap="nowrap">
							<input type="text" size="12" maxlength="20" name="sel_symbol[]" value="SYMBOL" onBlur="clrAndUcase(this)" onFocus="clrAndUcase(this)"/>
					</td>
					<td width="5">&nbsp;</td>
					<td width="100">
						<select name="sel_rating[]" size="1">
							<option value="">Rating</option>
							<option value="No Change">No Change</option>
							<option value="Downgrade">Downgrade</option>
							<option value="Upgrade">Upgrade</option>
							<option value="Other">Other</option>
						</select>			
					</td>
					<td width="5">&nbsp;</td>
					<td width="100">
						<select name="sel_target[]" size="1">
							<option value="">Target</option>
							<option value="No Change">No Change</option>
							<option value="Decrease">Decrease</option>
							<option value="Increase">Increase</option>
							<option value="Other">Other</option>
						</select>			
					</td>
					<td width="5">&nbsp;</td>
					<td width="100">
						<select name="sel_analyst[]" size="1">
						<option value="0">Analyst</option>
						<?
						$str_sql_select = "SELECT 
																ID, Fullname
																FROM users
																WHERE user_isactive = 1
																AND Role = 1
																ORDER BY Fullname";
						$result_select = mysql_query($str_sql_select) or die(tdw_mysql_error($str_sql_select));
						$count_row_select = 0;
						while ( $row_select = mysql_fetch_array($result_select) ) {
						?>
						<option value="<?=$row_select['ID']?>"><?=$row_select['Fullname']?></option>
						<?
						}
						?>
						</select>			
					</td>
					<td width="5">&nbsp;</td>
					<td width="100">
						<select name="sel_pm[]" size="1">
						<option value="0">Portfolio Mgr.</option>
						<?
						$str_sql_select = "SELECT 
																	ID, Fullname
																FROM users
																WHERE user_isactive = 1
																AND custom1 = 1
																ORDER BY Fullname";
						$result_select = mysql_query($str_sql_select) or die(tdw_mysql_error($str_sql_select));
						$count_row_select = 0;
						while ( $row_select = mysql_fetch_array($result_select) ) {
						?>
						<option value="<?=$row_select['ID']?>"><?=$row_select['Fullname']?></option>
						<?
						}
						?>
						</select>			
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="9">
						<select name="sel_t0[]" size="1"><option value="--">T-0</option><option value="Buy">Buy</option><option value="Sell">Sell</option><option value="NA">NA</option></select>&nbsp;&nbsp;
						<select name="sel_t1[]" size="1"><option value="--">T-1</option><option value="Buy">Buy</option><option value="Sell">Sell</option><option value="NA">NA</option></select>&nbsp;&nbsp;
						<select name="sel_t2[]" size="1"><option value="--">T-2</option><option value="Buy">Buy</option><option value="Sell">Sell</option><option value="NA">NA</option></select>&nbsp;&nbsp;
						<select name="sel_t3[]" size="1"><option value="--">T-3</option><option value="Buy">Buy</option><option value="Sell">Sell</option><option value="NA">NA</option></select>&nbsp;&nbsp;
						<select name="sel_t4[]" size="1"><option value="--">T-4</option><option value="Buy">Buy</option><option value="Sell">Sell</option><option value="NA">NA</option></select>&nbsp;
            <select name="sel_empmri[]" size="1" style="width:140px">
						<option value="0">Employee</option>
						<?
						$str_sql_select = "SELECT 
																	ID, Fullname
																FROM users
																WHERE user_isactive = 1
																and is_login_acct = 1
																ORDER BY Firstname";
						$result_select = mysql_query($str_sql_select) or die(tdw_mysql_error($str_sql_select));
						$count_row_select = 0;
						while ( $row_select = mysql_fetch_array($result_select) ) {
						?>
						<option value="<?=$row_select['ID']?>"><?=$row_select['Fullname']?></option>
						<?
						}
						?>
						</select>			
						&nbsp;&nbsp;
						<a class="ilt" align="right">MRI Reqd: &nbsp;
            <select name="mri_reqd[]">
            	<option value="1">Yes</option>
              <option value="0" selected="selected">No</option>
              </select>&nbsp;&nbsp;
              <a class="ilt" align="right">Keep Open: &nbsp;<select name="sel_is_open[]"><option value="1">Yes</option><option value="0" selected="selected">No</option></select>
					</td>
					<td>&nbsp;</td>
				</tr>
			</table>
			<table>
				<tr>
					<td>
						<textarea wrap="physical" name="addnote[]" cols="92" rows="2" style='overflow:auto' onChange="adjustRows(this)" onFocus="adjustRows(this)"></textarea><br />
					</td>
				</tr>
			</table>
      <div id="zdiv_mri_<?=($divcount+1)?>"></div>
    <?
	}

	if ($ctype == 'pac') {
		?>
			<hr size="1" noshade color="#CCCCCC" />
			<table width="100%" border="0">
				<tr>
					<td width="100" align="left" nowrap="nowrap">
							<input type="text" size="12" maxlength="20" name="sel_symbol[]" value="SYMBOL" onBlur="clrAndUcase(this)" onFocus="clrAndUcase(this)"/>
					</td>
					<td width="230"><a class="ilt" align="right">Potential Agency Cross?</a><select name="sel_is_pac[]"><option value="1">Yes</option><option value="0">No</option></select>
					</td>
					<td width="200"><a class="ilt" align="right">Keep Open: &nbsp;<select name="sel_is_open[]"><option value="1">Yes</option><option value="0" selected="selected">No</option></select></td>
					<td>&nbsp;</td>
				</tr>
			</table>
			<table>
				<tr>
					<td>
						<textarea wrap="physical" name="addnote[]" cols="92" rows="4" style='overflow:auto' onFocus="adjustRows(this)" onChange="adjustRows(this)"></textarea><br /><!-- onKeyUp="adjustRows(this)" -->
					</td>
				</tr>
			</table>
      <div id="zdiv_pac_<?=($divcount+1)?>"></div>
    <?
	}
	
	if ($ctype == 'oth') {
		?>
			<hr size="1" noshade color="#CCCCCC" />
			<table width="100%" border="0">
				<tr>
					<td width="200"><a class="ilt" align="right">Keep Open: &nbsp;<select name="sel_is_open[]"><option value="1">Yes</option><option value="0" selected="selected">No</option></select></td>
					<td>&nbsp;</td>
				</tr>
			</table>
			<table>
				<tr>
					<td>
						<textarea wrap="physical" name="addnote[]" cols="92" rows="2" style='overflow:auto' onFocus="adjustRows(this)" onChange="adjustRows(this)"></textarea><br />
					</td>
				</tr>
			</table>
      <div id="zdiv_oth_<?=($divcount+1)?>"></div>
    <?
	}
	
	if ($ctype == 'sra') {
		?>
			<hr size="1" noshade color="#CCCCCC" />
			<table width="100%" border="0">
				<tr>
					<td width="200"><a class="ilt" align="right">Keep Open: &nbsp;<select name="sel_is_open[]"><option value="1">Yes</option><option value="0" selected="selected">No</option></select></td>
					<td>&nbsp;</td>
				</tr>
			</table>
			<table>
				<tr>
					<td>
						<textarea wrap="physical" name="addnote[]" cols="92" rows="2" style='overflow:auto' onFocus="adjustRows(this)" onChange="adjustRows(this)"></textarea><br />
					</td>
				</tr>
			</table>
      <div id="zdiv_sra_<?=($divcount+1)?>"></div>
    <?
	}
	
	
	if ($ctype == 'emp') {
		?>
			<hr size="1" noshade color="#CCCCCC" />
			<table width="100%" border="0">
				<tr>
					<td>
						<select name="sel_emp[]" size="1">
						<option value="0">Select Emp.</option>
						<?
						$str_sql_select = "SELECT ID, Fullname FROM users WHERE user_isactive = 1 ORDER BY Fullname";
						$result_select = mysql_query($str_sql_select) or die(tdw_mysql_error($str_sql_select));
						$count_row_select = 0;
						while ( $row_select = mysql_fetch_array($result_select) ) {
						?><option value="<?=$row_select['ID']?>"><?=$row_select['Fullname']?></option><? } ?>
						</select>			
						&nbsp;&nbsp;							
						<input type="text" size="12" maxlength="20" name="sel_symbol[]" value="SYMBOL" onBlur="clrAndUcase(this)" onFocus="clrAndUcase(this)"/>
						&nbsp;&nbsp;	
						<select name="sel_trd_approver[]" size="1">
						<option value="0">Trade Approver</option>
						<?
						$str_sql_select = "SELECT ID, Fullname	FROM users	WHERE is_trade_approver = 1	ORDER BY Fullname";
						$result_select = mysql_query($str_sql_select) or die(tdw_mysql_error($str_sql_select));
						$count_row_select = 0;
						while ( $row_select = mysql_fetch_array($result_select) ) {
						?> <option value="<?=$row_select['ID']?>"><?=$row_select['Fullname']?></option>	<? } ?>
						</select>			
					</td>
				</tr>
				<tr>
					<td>
						<select name="sel_emptrd_type[]" size="1">
              <option value="0">Type</option>
              <option value="1">vs. Client</option>
              <option value="2">vs. Restricted List</option>
              <option value="3">Trade Approval Exception</option>
            </select>
						&nbsp;&nbsp; 
						<select name="sel_client[]" size="1">
						<option value="">Select Client</option>
						<? $query_sel_client = "SELECT clnt_code, clnt_name  
																		FROM `int_clnt_clients` where clnt_status = 'A' 
																		and clnt_isactive = 1 and clnt_code != '----' 
																		order by clnt_name";
						$result_sel_client = mysql_query($query_sel_client) or die(mysql_error());
						while($row_sel_client = mysql_fetch_array($result_sel_client))
						{ ?> <option value="<?=$row_sel_client['clnt_code']?>"><?=$row_sel_client['clnt_name']?></option> <? } ?> 
						</select>
            &nbsp;&nbsp;<a class="ilt">Keep Open: </a>&nbsp;<select name="sel_is_open[]"><option value="1">Yes</option><option value="0" selected="selected">No</option></select>			
					</td>
				</tr>
			</table>
			<table>
				<tr>
					<td>
						<textarea wrap="physical" name="addnote[]" cols="92" rows="2" style='overflow:auto' onChange="adjustRows(this)" onFocus="adjustRows(this)"></textarea><br />
					</td>
				</tr>
			</table>
      <div id="zdiv_emp_<?=($divcount+1)?>"></div>
    <?
	}

}
//======================================================================================================================
//======================================================================================================================
//======================================================================================================================
//======================================================================================================================
//======================================================================================================================
//======================================================================================================================
//======================================================================================================================
//[rep_auto_id] => 2045 [user_id] => 79 [sel_symbol] => Array ( [0] => ASDAD ) [sel_is_pac] => Array ( [0] => 1 ) [addnote] => Array ( [0] => xzczxc
if ($mod_request=='savedata') {

	if ($ctype == 'mri') {
		//show_array($_GET);
		//exit;
		foreach ($sel_symbol as $key=>$symbol) {
			if (trim($symbol) != "" && $symbol != 'SYMBOL') {
			$sql = "INSERT INTO `warehouse`.`crep_mri_trades` (
							`mri_auto_id` ,
							`mri_rep_id` ,
							`mri_symbol` ,
							`mri_rating` ,
							`mri_target` ,
							`mri_analyst` ,
							`mri_portfol_mgr` ,
							`mri_emp_mri` ,
							`mri_t-0` ,
							`mri_t-1` ,
							`mri_t-2` ,
							`mri_t-3` ,
							`mri_t-4` ,
							`mri_required` ,
							`mri_comment` ,
							`mri_entered_by` ,
							`mri_entered_on` ,
							`mri_isopen` ,
							`mri_isactive` 
							)
							VALUES (
							  NULL ,
								'".$rep_auto_id."', 
								'".$symbol."', 
								'".$sel_rating[$key]."',
								'".$sel_target[$key]."',
								'".$sel_analyst[$key]."',
								'".$sel_pm[$key]."',
								'".$sel_empmri[$key]."',
								'".$sel_t0[$key]."',
								'".$sel_t1[$key]."',
								'".$sel_t2[$key]."',
								'".$sel_t3[$key]."',
								'".$sel_t4[$key]."',
								'".$mri_reqd[$key]."',
								'".str_replace("'","\\'",$addnote[$key])."', 
								'".$user_id."', 
								now(), 
								'".$sel_is_open[$key]."', 
								'1'
								)";
			$result = mysql_query($sql) or die(tdw_mysql_error($sql));	
			}
		}
	?>
		 <table width="100%" class="compnotes">
		 <? 
				$str_sql = "SELECT * FROM crep_mri_trades 
													 WHERE mri_rep_id = '".$rep_auto_id."' 
													 AND mri_isactive = 1
													 ORDER BY mri_entered_on DESC";
				$result = mysql_query($str_sql) or die(tdw_mysql_error($str_sql));
				while ( $row = mysql_fetch_array($result) ) {
				//mri_auto_id  mri_rep_id  mri_symbol  mri_rating  mri_target  mri_analyst  mri_portfol_mgr  
				//mri_t-0  mri_t-1  mri_t-2  mri_t-3  mri_t-4  mri_required  mri_comment  mri_entered_by  mri_entered_on  mri_isopen  mri_isactive  
				?>
				<tr><td><img src="images/spacer.gif" width="10" height="1" /></td><td>Symbol: <strong><?=$row['mri_symbol']?></strong>
        &nbsp;&nbsp;Date : <?=date('m/d',strtotime($row['mri_entered_on']))?>&nbsp;&nbsp; By: <?=get_user_by_id($row['mri_entered_by'])?>
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
  
  <?
	}
	
	if ($ctype == 'oth') {
		foreach ($sel_is_open as $key=>$val) {
			$sql = "INSERT INTO crep_other_notes 
							( oth_auto_id ,
								oth_rep_id ,
								oth_comment ,
								oth_entered_by ,
								oth_entered_on ,
								oth_isopen ,
								oth_isactive 
								)
							VALUES (
								NULL , 
								'".$rep_auto_id."', 
								'".str_replace("'","\\'",$addnote[$key])."', 
								'".$user_id."', 
								now(), 
								'".$val."', 
									'1'
								)";
			$result = mysql_query($sql) or die(tdw_mysql_error($sql));	
		}
	?>
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
  
  <?
	}
	
	if ($ctype == 'sra') {
		foreach ($sel_is_open as $key=>$val) {
			$sql = "INSERT INTO crep_sra_approval 
							( sra_auto_id ,
								sra_rep_id ,
								sra_comment ,
								sra_entered_by ,
								sra_entered_on ,
								sra_isopen ,
								sra_isactive 
								)
							VALUES (
								NULL , 
								'".$rep_auto_id."', 
								'".str_replace("'","\\'",$addnote[$key])."', 
								'".$user_id."', 
								now(), 
								'".$val."', 
									'1'
								)";
			$result = mysql_query($sql) or die(tdw_mysql_error($sql));	
		}
	?>
		 <table width="100%" class="compnotes">
		 <? 
				$str_sql = "SELECT * 
													 FROM crep_sra_approval 
													 WHERE sra_rep_id = '".$rep_auto_id."' 
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
  
  <?
	}

	if ($ctype == 'pac') {
		foreach ($sel_symbol as $key=>$symbol) {
			if (trim($symbol) != "" && $symbol != 'SYMBOL') {
				$sql = "INSERT INTO crep_agency_cross 
								( pac_auto_id,
									pac_rep_id , 
									pac_symbol , 
									pac_yes_no , 
									pac_comment , 
									pac_entered_by , 
									pac_entered_on, 
									pac_isopen,
									pac_isactive ) 
								VALUES 
								(
									NULL, 
									'".$rep_auto_id."', 
									'".$symbol."', 
									'".$sel_is_pac[$key]."', 
									'".str_replace("'","\\'",$addnote[$key])."', 
									'".$user_id."', 
									now(), 
									'".$sel_is_open[$key]."', 
									'1'
								) ";
				$result = mysql_query($sql) or die(tdw_mysql_error($sql));	
			}
		}
	
	//now get the data from the database.
	//------------------------------------------------------------------------------------
	?>
  	 <table width="100%" class="compnotes">
		 <? 
				//pac_rep_id  pac_symbol  pac_yes_no  pac_comment  pac_entered_by  pac_entered_on  pac_isactive
				$str_sql = "SELECT * 
													 FROM crep_agency_cross 
													 WHERE pac_rep_id = '".$rep_auto_id."' 
													 AND pac_isactive = 1
													 ORDER BY pac_entered_on DESC";
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
     <?
	//------------------------------------------------------------------------------------
	}
  //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
	if ($ctype == 'emp') {
		//show_array($_GET);
		//exit;
	
		foreach ($sel_symbol as $key=>$symbol) {
		if ($symbol != 'SYMBOL' && trim($symbol) != "") {
			$sql = "INSERT INTO crep_emp_trades ( 
								emp_auto_id,
								emp_rep_id,
								emp_emp_id,
								emp_symbol,
								emp_approver,
								emp_trade_type,
								emp_client_id,
								emp_comment,
								emp_entered_by,
								emp_entered_on,
								emp_isopen,
								emp_isactive ) 
							 VALUES (
							 	NULL , 
								'".$rep_auto_id."', 
								'".$sel_emp[$key]."', 
								'".$symbol."', 
								'".$sel_trd_approver[$key]."', 
								'".$sel_emptrd_type[$key]."', 
								'".$sel_client[$key]."', 
								'".str_replace("'","\\'",$addnote[$key])."', 
								'".$user_id."', 
								now(), 
								'".$sel_is_open[$key]."', 
							 '1')";
			$result = mysql_query($sql) or die(tdw_mysql_error($sql));	
		}
		}
	
	//now get the data from the database.
	//------------------------------------------------------------------------------------
	?>
		 <table width="100%" class="compnotes">
		 <? 
				$str_sql = "SELECT * FROM crep_emp_trades 
													 WHERE emp_rep_id = '".$rep_auto_id."' 
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
          	//$client_val = db_single_val("select clnt_name as single_val from int_clnt_clients where clnt_code = '".$row['emp_client_id']."'");
					?>
						Trade Type : <strong>Vs. Client</strong> Client: <strong><?=$client_val?></strong>          
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
     <?
	//------------------------------------------------------------------------------------
	}
	
}

//======================================================================================================================
//======================================================================================================================
//======================================================================================================================
//======================================================================================================================
//======================================================================================================================
//======================================================================================================================
//======================================================================================================================

if ($mod_request=='close_item') {

		if ($ctype == 'pac') {
			$old_comment_val = db_single_val("select pac_comment as single_val from crep_agency_cross where pac_auto_id = '".$itemid."'");
		  $val_closer = db_single_val("select Fullname as single_val from users where ID = '".$user_id."'");  
			$new_comment_val = $old_comment_val."\nClosed by ". $val_closer ." [".date('m/d/Y h:ia')."]"."\n".$val_comment;
			$sql = "UPDATE crep_agency_cross 
							set pac_comment = '".str_replace("'","\\'",$new_comment_val)."',
							    pac_isopen = 0
								where pac_auto_id = '".$itemid."'";
			$result = mysql_query($sql) or die(tdw_mysql_error($sql));	
			echo nl2br($new_comment_val);
		}
		
		if ($ctype == 'emp') {
			$old_comment_val = db_single_val("select emp_comment as single_val from crep_emp_trades where emp_auto_id = '".$itemid."'");
		  $val_closer = db_single_val("select Fullname as single_val from users where ID = '".$user_id."'");  
			$new_comment_val = $old_comment_val."\nClosed by ". $val_closer ." [".date('m/d/Y h:ia')."]"."\n".$val_comment;
			$sql = "UPDATE crep_emp_trades 
							set emp_comment = '".str_replace("'","\\'",$new_comment_val)."',
							    emp_isopen = 0
								where emp_auto_id = '".$itemid."'";
			$result = mysql_query($sql) or die(tdw_mysql_error($sql));	
			echo nl2br($new_comment_val);
		}

			if ($ctype == 'mri') {
			$old_comment_val = db_single_val("select mri_comment as single_val from crep_mri_trades where mri_auto_id = '".$itemid."'");
		  $val_closer = db_single_val("select Fullname as single_val from users where ID = '".$user_id."'");  
			$new_comment_val = $old_comment_val."\nClosed by ". $val_closer ." [".date('m/d/Y h:ia')."]"."\n".$val_comment;
			$sql = "UPDATE crep_mri_trades 
							set mri_comment = '".str_replace("'","\\'",$new_comment_val)."',
							    mri_isopen = 0
								where mri_auto_id = '".$itemid."'";
			$result = mysql_query($sql) or die(tdw_mysql_error($sql));	
			echo nl2br($new_comment_val);
		}

			if ($ctype == 'sra') {
			$old_comment_val = db_single_val("select sra_comment as single_val from crep_sra_approval where sra_auto_id = '".$itemid."'");
		  $val_closer = db_single_val("select Fullname as single_val from users where ID = '".$user_id."'");  
			$new_comment_val = $old_comment_val."\nClosed by ". $val_closer ." [".date('m/d/Y h:ia')."]"."\n".$val_comment;
			$sql = "UPDATE crep_sra_approval 
							set sra_comment = '".str_replace("'","\\'",$new_comment_val)."',
							    sra_isopen = 0
								where sra_auto_id = '".$itemid."'";
			$result = mysql_query($sql) or die(tdw_mysql_error($sql));	
			echo nl2br($new_comment_val);
		}

		if ($ctype == 'oth') {
			$old_comment_val = db_single_val("select oth_comment as single_val from crep_other_notes where oth_auto_id = '".$itemid."'");
		  $val_closer = db_single_val("select Fullname as single_val from users where ID = '".$user_id."'");  
			$new_comment_val = $old_comment_val."\nClosed by ". $val_closer ." [".date('m/d/Y h:ia')."]"."\n".$val_comment;
			$sql = "UPDATE crep_other_notes 
							set oth_comment = '".str_replace("'","\\'",$new_comment_val)."',
							    oth_isopen = 0
								where oth_auto_id = '".$itemid."'";
			$result = mysql_query($sql) or die(tdw_mysql_error($sql));	
			echo nl2br($new_comment_val);
		}

	//------------------------------------------------------------------------------------
}
?>