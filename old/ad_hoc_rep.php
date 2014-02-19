<?
include('top.php');
include('includes/functions.php');
?>

<script language="javascript">
	function validate(form)
	{
		if(frm_select.name.value == '')
		{
			alert('"Report Name" is required !');
			return false;	
		}
	}		
</script>

<?
if($save)
{
	$query_id = "SELECT max(arep_auto_id) AS id FROM arep_adhoc_reports";
	$result_id = mysql_query($query_id) or die(mysql_error());
	$row_id = mysql_fetch_array($result_id);
	
	$query_update = "UPDATE arep_adhoc_reports SET arep_isactive = '1' WHERE arep_auto_id = '".$row_id['id']."'";
	$result_update = mysql_query($query_update) or die(mysql_error());

	
	$query_id = "SELECT max(rdat_auto_id) AS id FROM rdat_report_data";
	$result_id = mysql_query($query_id) or die(mysql_error());
	$row_id = mysql_fetch_array($result_id);
	
	$query_update = "UPDATE rdat_report_data SET rdat_isactive = '1' WHERE rdat_auto_id = '".$row_id['id']."'";
	$result_update = mysql_query($query_update) or die(mysql_error());
	
}
?>
<table cellpadding="10" width="100%"><tr><td>
<!-- START TABLE 1 -->
<table width="650" cellpadding="1", cellspacing="0" align="left">
	<tr>
		<td valign="top" >
		
		<form action="rep_ad_hoc.php" method="post" enctype="multipart/form-data" name="frm_select" onSubmit="return validate(this.form);">
		<? 
		//GET ACCOUNTS
		$query_accts = "SELECT acct_number AS d_value, CONCAT(acct_number, ': ', acct_name1) AS d_option  FROM Employee_accounts WHERE acct_is_active = '1'";
		$result_accts = mysql_query($query_accts);
		
		//GET TICKERS
//			$query_tickers = "SELECT distinct(trdm_symbol) AS d_value, CONCAT(trdm_symbol, ': ', trdm_sec_description) AS d_option  FROM Trades_m WHERE trdm_symbol != '' ORDER BY trdm_symbol";	

		$query_tickers = "SELECT distinct(trdm_symbol) AS d_value, CONCAT(trdm_symbol, ':', trdm_sec_description) AS d_option  FROM Trades_m WHERE trdm_symbol != '' ORDER BY trdm_symbol";	
		$result_tickers = mysql_query($query_tickers);
		
		
		
		
		//GET LISTS
		//SYSTEM LISTS
		$query_lists = "SELECT CONCAT(slis_list_type_id, ',', slis_auto_id) AS d_value, slis_title_name AS d_option FROM slis_system_lists WHERE slis_isactive = '1' 
						UNION ALL SELECT CONCAT(alis_list_type_id, ',', alis_auto_id) AS d_value, alis_title_name AS d_option FROM alis_admin_lists WHERE alis_isactive = '1' 
						UNION ALL SELECT CONCAT(usli_list_type_id, ',', usli_auto_id) AS d_value, usli_title_name AS d_option FROM usli_user_lists WHERE usli_user_id = '".$user_id."' AND usli_isactive = '1'";
		$result_lists = mysql_query($query_lists);

		table_start_percent(100, "Adhoc Reports"); 
		?>
		<table width="100%" cellpadding="10"><tr><td>
		<? table_start_percent(100, "Save Report as:"); ?>
			<!-- START TABLE 2 -->
			<table border="0" cellpadding="5" cellspacing="0" bordercolor="#FF0000" bordercolorlight="#CCCCCC" bordercolordark="#666666">
				<tr>
					<td colspan="2"><input class="Text" type="text" name="name" size="30" maxlength="50" value="<?=$name?>">&nbsp; </td>
				</tr>
			</table>
			<!-- END TABLE 2 -->
			<? table_end_percent();  ?>
			<br>
		<? table_start_percent(100, "Select Accounts"); ?>
			<!-- START DISPLAYING ACCOUNTS -->
			<!-- START TABLE 3 -->
			<table align="left" border="0" cellpadding="5" cellspacing="0" bordercolor="#FF0000" bordercolorlight="#CCCCCC" bordercolordark="#666666">
				<!-- <form action="ad_hoc_rep.php" method="post" enctype="multipart/form-data" name="frm_select_accounts"> -->
				<tr> 
					<td>
					<select class="Text1" name="selectAcct" multiple size="10" style="width: 250" onDblClick="move(selectAcct, acctSelected)">
					<? 
						createdropdown($result_accts); 
					?>
					</select>
					</td>
					<td>
					<input class="Submit" onclick="move(selectAcct, acctSelected)" type="button" value="&nbsp;&nbsp;&gt;&gt;&nbsp;&nbsp;">
					<br>
					<input class="Submit" onclick="move(acctSelected,  selectAcct)" type="button" value="&nbsp;&nbsp;&lt;&lt;&nbsp;&nbsp;">
					</td>
					<td>
					<select class="Text1" name="acctSelected" multiple size="10" style="width: 250" onDblCLick="move(acctSelected,  selectAcct)">
					</select>
					</td>
				</tr>
				<tr>
					<td colspan="3" align="center">&nbsp;</td>
				</tr>
				<!-- </form> -->
			</table>
			<!-- END TABLE 3 -->
			<!-- END DISPLAYING ACCOUNTS -->
			<? table_end_percent();  ?>
			<br>
		<? table_start_percent(100, "Select Companies/Symbols"); ?>
			<!-- START DISPLAYING TICKERS -->
			<!-- START TABLE 4 -->
			<table align="left" border="0" cellpadding="5" cellspacing="0" bordercolor="#FF0000" bordercolorlight="#CCCCCC" bordercolordark="#666666">
				<!-- <form action="ad_hoc_rep.php" method="post" enctype="multipart/form-data" name="frm_select_tickers">  -->
				<tr> 
					<td>
					<select class="Text1" name="selectTick" multiple size="10" style="width: 250" onDblClick="move(selectTick, tickSelected)">
					<? 
						createdropdown1($result_tickers); 
					?>
					</select>
					</td>
					<td>
					<input class="Submit" onclick="move(selectTick, tickSelected)" type="button" value="&nbsp;&nbsp;&gt;&gt;&nbsp;&nbsp;">
					<br>
					<input class="Submit" onclick="move(tickSelected,  selectTick)" type="button" value="&nbsp;&nbsp;&lt;&lt;&nbsp;&nbsp;">
					</td>
					<td>
					<select class="Text1" name="tickSelected" multiple size="10" style="width: 250" onDblCLick="move(tickSelected,  selectTick)">
					</select>
					</td>
				</tr>
				<tr>
					<td colspan="3" align="center">&nbsp;</td>
				</tr>
				<!-- </form>  -->
			</table>
			<!-- END TABLE 4 -->
			<!-- END DISPLAYING TICKERS -->
			<? table_end_percent();  ?>
			<br>
			<? table_start_percent(100, "Select Lists of Stocks"); ?>
			<!-- START DISPLAYING LISTS -->
			<!-- START TABLE 5 -->
			<table align="left" border="0" cellpadding="5" cellspacing="0" bordercolor="#FF0000" bordercolorlight="#CCCCCC" bordercolordark="#666666">
				<!-- <form action="ad_hoc_rep.php" method="post" enctype="multipart/form-data" name="frm_select_lists">  -->
				<tr> 
					<td>
					<select class="Text1" name="selectList" multiple size="10" style="width: 250" onDblClick="move(selectList, listSelected)">
					<? 
						createdropdown($result_lists); 
					?>
					</select>
					</td>
					<td>
					<input class="Submit" onclick="move(selectList, listSelected)" type="button" value="&nbsp;&nbsp;&gt;&gt;&nbsp;&nbsp;">
					<br>
					<input class="Submit" onclick="move(listSelected,  selectList)" type="button" value="&nbsp;&nbsp;&lt;&lt;&nbsp;&nbsp;">
					</td>
					<td>
					<select class="Text1" name="listSelected" multiple size="10" style="width: 250" onDblCLick="move(listSelected,  selectList)">
					</select>
					</td>
				</tr>
				<tr>
					<td colspan="3" align="center">&nbsp;</td>
				</tr>
				<!-- </form> -->
			</table>
			<!-- END TABLE 5 -->
			<!-- END DISPLAYING LISTS -->
			<? table_end_percent();  ?>
			<br>
			<? table_start_percent(100, "Select Report Format"); ?>
			<!-- START TABLE 6 -->
			<table  border="0" cellpadding="5" cellspacing="0" bordercolor="#FF0000" bordercolorlight="#CCCCCC" bordercolordark="#666666">		
				<!-- <form action="ad_hoc_rep.php" method="post" enctype="multipart/form-data" name="frm_select_lists"> -->
				<tr>
					<td colspan="2">
						<select name="format" class="Text">
							<option value="1">&nbsp;&nbsp;&nbsp;Link&nbsp;&nbsp;&nbsp;</option>
							<option value="2">&nbsp;&nbsp;&nbsp;PDF&nbsp;&nbsp;&nbsp;</option>
							<option value="3">&nbsp;&nbsp;&nbsp;HTML&nbsp;&nbsp;&nbsp;</option>
						</select>
					</td>
				</tr>
				
				<!-- </form> -->
			</table>
			
			<!-- END TABLE 6 -->
			<? table_end_percent();  ?>
			<br>
			<? table_start_percent(100, "Preview / Edit"); ?>
			<!-- START TABLE 7 -->
			<table align="left" border="0" cellpadding="5" cellspacing="0" bordercolor="#FF0000" bordercolorlight="#CCCCCC" bordercolordark="#666666">		
				
				<tr>
					<td><input name="preview" class="Submit" type="submit" value=" Preview " onclick="javascript:select_all(acctSelected); select_all(tickSelected); select_all(listSelected);"></td>
					<td><input name="reset" class="Submit" type="reset" value=" Reset "></td>
				</tr>
				
			</table>
			
			<!-- END TABLE 7 -->
			<? table_end_percent();  ?>
			<br>
			</td></tr></table>
		<? table_end_percent(); ?>
		</form>
		</td>

	</tr>
</table>
<!-- END TABLE 1 -->
</td></tr></table>
<?
include('bottom.php');
?>


<!-- 

		<fieldset style="BORDER-RIGHT: #DAD8D8 1px solid; 
			BORDER-TOP: #DAD8D8 1px solid; 
			BORDER-LEFT: #777777 1px solid;
			PADDING: 10px;
			WIDTH: 460px; 
			HEIGHT: 460px;
			COLOR: #000000; 
			BORDER-BOTTOM: #777777 1px solid">
		rrrrrrrrrrrrrrRR
		</fieldset>

 -->