<title>Edit Trade</title>
<script language="Javascript" SRC="../includes/javascript.js"></script>
<link rel="stylesheet" type="text/css" href="includes/styles.css" />

<? 
include('includes/dbconnect.php');
include('includes/global.php');
include('includes/functions.php');

echo "<center>";
tsp(100, "Edit Trade");

 ?>
	<form action="<?=$php_self?>" method="post" enctype="multipart/form-data" name="passreset" target="_self">
		<?
				if (isset($submit)) {
				
				$qry_update = "UPDATE otd_emp_trades_external
											 SET otd_account_id = '".$otd_account_id."',
											     otd_trade_date = '".format_date_mdy_to_ymd($otd_trade_date)."',
													 otd_buysell = '".$otd_buysell."',
													 otd_symbol = '".$otd_symbol."',
													 otd_quantity = '".$otd_quantity."',
													 otd_price = '".$otd_price."',
													 otd_last_edited_by = '".$uid."',
													 otd_last_edited_on = now()
												WHERE auto_id = '".$tid."'";
						
				//xdebug("qry_update",$qry_update);
				$result_update = mysql_query($qry_update) or die (tdw_mysql_error($qry_update));
				
    ?>					
		<?					
					sys_message(1, "Trade edited successfully.");
				};
		?>
		<table width="460" height="180" border="0" cellspacing="0" cellpadding="2" align="center">
<?
$str_sql_select = "SELECT a. * , a.auto_id as tid, a.otd_account_id as acct_id, b.Fullname, c.oac_account_number, c.oac_custodian
																FROM otd_emp_trades_external a, users b, oac_emp_accounts c
																WHERE a.otd_account_id = c.auto_id
																AND c.oac_emp_userid = b.ID
																AND a.auto_id = '".$tid."'";
$result_select = mysql_query($str_sql_select) or die(tdw_mysql_error($str_sql_select));
while ( $row_select = mysql_fetch_array($result_select) )  {
?>
			<tr> 
				<td><p class="changepasswd">Trade Date:</p></td>
				<td><input class="Text" type="text" name="otd_trade_date" value="<?=format_date_ymd_to_mdy($row_select["otd_trade_date"])?>" size="20" maxlength="20"></td>
			</tr>
			<tr> 
				<td><p class="changepasswd">Account:</p></td>
				<td>
				<select class="Text" name="otd_account_id">
				<?
				//dropdown of accounts just in case this needs to be edited.
				$qry_acct = "select a.auto_id as otd_account_id, concat(b.Fullname,'  (',a.oac_account_number, ': ', a.oac_custodian,')') as show_acct from 
											oac_emp_accounts a, users b
											where a.oac_emp_userid = b.ID
											order by b.Fullname";
				$result_acct = mysql_query($qry_acct) or die(tdw_mysql_error($qry_acct));
				while ( $row_acct = mysql_fetch_array($result_acct) )  {
				?>
				<option value="<?=$row_acct["otd_account_id"]?>" <?=($row_acct["otd_account_id"]==$row_select["acct_id"])? 'selected':'';?>><?=$row_acct["show_acct"]?></option>
				<?
				}
				?>
				</select>				
			</tr>
			<tr> 
				<td><p class="changepasswd">Symbol:</p></td>
				<td><input class="Text" type="text" name="otd_symbol" value="<?=$row_select["otd_symbol"]?>" size="20" maxlength="20"></td>
			</tr>
			<tr> 
				<td><p class="changepasswd">Buy / Sell:</p></td>
				<td><input class="Text" type="text" name="otd_buysell" value="<?=$row_select["otd_buysell"]?>" size="20" maxlength="20"></td>
			</tr>
			<tr> 
				<td><p class="changepasswd">Quantity:</p></td>
				<td><input class="Text" type="text" name="otd_quantity" value="<?=$row_select["otd_quantity"]?>" size="20" maxlength="20"></td>
			</tr>
			<tr> 
				<td><p class="changepasswd">Price:</p></td>
				<td><input class="Text" type="text" name="otd_price" value="<?=$row_select["otd_price"]?>" size="20" maxlength="20">
				<input type="hidden" name="tid" value="<?=$tid?>" />
				<input type="hidden" name="uid" value="<?=$uid?>" />
				</td>
			</tr>
			<tr> 
				<td colspan="2" class="names" align="center">&nbsp;</td>
			</tr>
			<tr> 
				<td colspan="2" align="center"><input class="Submit" name="submit" type="submit" value="Save Changes">&nbsp;&nbsp;<input class="Submit" name="close" type="button" value="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Close&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" onClick="javascript:window.close()"></td>
			</tr>
<?
}
?>		


		</table>
	</form>
<? 	
table_end_percent();
	echo "</center>";
?>
