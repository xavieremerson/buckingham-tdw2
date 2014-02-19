<?
include('top.php');
include('includes/functions.php'); 
?>

<script language="javascript">
	function validate(form)
	{
		if(list_form.list_name.value == '')
		{
			alert('"List Name" is required !');
			return false;	
		}
	}		
</script>


<?
$tdate = previous_business_day ();

if($type == "create")
{
	echo "<center>";
	echo $table_start;
	echo "<br>";
			
	//START OF IF 1
	if($createList)
	{

		$query_get = "SELECT usli_title_name FROM usli_user_lists WHERE usli_title_name = '".$list_name."' AND usli_user_id = '".$user_id."' AND usli_isactive = '1'";
		$result_get = mysql_query($query_get) or die(mysql_error());
		
		if(mysql_num_rows($result_get) > 0)
		{
			?>
			
			<table width="100%" cellpadding="2" cellspacing="0" border="0">
				<tr>
					 <td colspan="3"><p class="Contact">List <b><?=str_replace("\\","",$list_name)?></b> already exists! Use a different name.</p></td>
				</tr>
				<tr><td colspan="3"><br></td></tr>
			</table>
			<?
		}
		else
		{
			$query_create = "INSERT INTO usli_user_lists(usli_user_id, usli_list_type_id, usli_title_name) VALUES('".$user_id."', '3', '".$list_name."')";
			$result_create = mysql_query($query_create) or die (mysql_error());

			$query_get_id = "SELECT max(usli_auto_id) as uid FROM usli_user_lists WHERE usli_user_id = '".$user_id."' AND usli_isactive = '1'";
			$result_get_id = mysql_query($query_get_id) or die(mysql_error());
			$row_get_id = mysql_fetch_array($result_get_id);
			
			$query_list_types = "SELECT usli_auto_id, usli_list_type_id, usli_title_name FROM usli_user_lists WHERE usli_auto_id = '".$row_get_id['uid']."'";
			$result_list_types = mysql_query($query_list_types) or die(mysql_error());
			//START WHILE 1
			while($row_list_types = mysql_fetch_array($result_list_types))
			{
				$query_get_symbol = "SELECT usll_symbol FROM usll_user_list_lists WHERE usll_list_id = '".$row_list_types['usli_auto_id']."' AND usll_isactive = '1'";
				$result_get_symbol = mysql_query($query_get_symbol) or die(mysql_error());
				
				$arr_symbol = array();
				$count_list = 0;
				while($row_get_symbol = mysql_fetch_array($result_get_symbol))
				{
					$arr_symbol[] = $row_get_symbol['usll_symbol'];
				}
				
				$query_get_trades = "SELECT trdm_account_number, trdm_symbol FROM Trades_m WHERE trdm_trade_date = '".$tdate."'";
				$result_get_trades = mysql_query($query_get_trades) or die(mysql_error());
				while($row_get_trades = mysql_fetch_array($result_get_trades))
				{
					if(in_array($row_get_trades["trdm_symbol"],$arr_symbol) AND in_array($row_get_trades["trdm_account_number"],$arr_emp_accts))
					{
						$count_list++;
					}
				}
				
				$query_insert = "INSERT INTO mlis_main_list(mlis_list_id, mlis_list_type_id, mlis_num_trades, mlis_trade_date)  
								 VALUES('".$row_list_types["usli_auto_id"]."', '".$row_list_types["usli_list_type_id"]."', '".$count_list."',  '".$tdate."')";
				$result_insert = mysql_query($query_insert) or die(mysql_error());
			}

	?>
			<!-- CREATE ACCOMPLISHED TABLE -->
			<table width="100%" cellpadding="2" cellspacing="0" border="0">
				<tr>
					<td colspan="3"><p class="Contact">List <b><?=str_replace("\\","",$list_name)?></b> created successfully!</p></td>
				</tr>
			</table>
				
	<?		
			echo "<Br><br>";			
		} // END OF INSERTING DATA IN TABLE
	} // END OF IF 1
?>
	<table cellpadding="2" cellspacing="0" border="0">  
		<form action="<?=$php_self?>" method="post" onSubmit="return validate(this.form);" name="list_form"> 
			<tr valign="top">
				<td><p class="Contact">List Name:</p></td>
				<td><p>&nbsp;&nbsp;<input class="Text" name="list_name" type="text" value="" size="25" maxlength="40"></p></td>
				<td><p>&nbsp;&nbsp;<input class="Submit" type="submit" name="createList" value="Create List"></p></td>
			</tr>
		</form>
	</table>
<?
	echo $table_end;
	echo "</center>";
}



if($type == 'manage')
{
	echo "<center>";
	echo $table_start;
	
	if($action == "remove")
	{
		$query_delete = "UPDATE usli_user_lists SET usli_isactive = '0' WHERE usli_auto_id = '$id'";
		$result_delete = mysql_query($query_delete) or die(mysql_error());
	}
		
?>
	<table width="100%" cellpadding="1", cellspacing="0" bgcolor="#CCCCCC">
		<tr>
			<td>
				<!--TABLE 2 START-->
				<table class="sortable" width="100%" id="accounts_table" border="0" cellspacing="1" cellpadding="1">
					<tr>
						<td width="25"></td>
						<td width="25"></td>
						<td>List Type</td>
					</tr>
					<?
					$query_users = "SELECT usli_auto_id, usli_title_name FROM usli_user_lists WHERE usli_user_id = '".$user_id."' AND usli_isactive = '1'";
					//echo $query_trades;
					$result = mysql_query($query_users) or die(mysql_error());
					while ( $row = mysql_fetch_array($result) ) 
					{
					?>
					<tr class="tablerow"> 
						<td><a href="<?=$PHP_SELF?>?type=<?=$type?>&action=remove&id=<?=$row["usli_auto_id"]?>"  onclick="javascript:return confirm('Are you sure you want to remove \n\n<?=$row["usli_title_name"]?>\n\nfrom the list?')"><img src="images/delete.gif" alt="Delete"></a></td>
						<td><a href="<?=$PHP_SELF?>?type=edit&id=<?=$row["usli_auto_id"]?>"><img src="images/edit.gif" alt="Edit"></a></td>
						<td><?=$row["usli_title_name"]?></td>
					</tr>
					<?php
					}
					?>
				</table>
				<!-- TABLE 2 END -->
				<script language="JavaScript">
				<!--
					///////////////////////tigra_tables('accounts_table', 3, 1, '#ffffff', '#ffffcc', '#ffcc66', '#cccccc');
					tigra_tables('accounts_table', 1, 0, '#ffffff', '#F3F1FF', '#B8D6FE', '#cccccc');
				// -->
				</script>
			</td>
		</tr>
	</table>
<?	
echo $table_end;
echo "</center>";
}

if($type == "edit")
{
	echo "<center>";
	echo $table_start;
	echo "<br>";
	
	//START OF IF 1
	if($editList)
	{
	
		$query_get = "SELECT usli_title_name FROM usli_user_lists WHERE usli_title_name = '".$list_name."' AND usli_auto_id != '".$id."' AND usli_user_id = '".$user_id."' AND usli_isactive = '1'";
		$result_get = mysql_query($query_get) or die(mysql_error());
		
		if(mysql_num_rows($result_get) > 0)
		{
			?>
			<table width="100%" cellpadding="2" cellspacing="0" border="0">
				<tr>
					<td colspan="3"><p class="Contact">List <b><?=str_replace("\\","",$list_name)?></b> already exists! Use a different name.</p></td>
				</tr>
				<tr><td colspan="3"><br></td></tr>
			</table>
			<?
		}
		else
		{
			//FIRST NAME, LAST NAME AND EMAIL ERROR CHECKING
			$query_edit = "UPDATE usli_user_lists SET usli_title_name = '".$list_name."' WHERE usli_auto_id = '$id'";
			//echo $query_edit;
			$result_edit = mysql_query($query_edit) or die (mysql_error());
		
			?>
			<!-- CREATE ACCOMPLISHED TABLE -->
			<table width="400" cellpadding="2" cellspacing="0" border="0">
				<tr>
					<td colspan="3"><p class="Contact">List <b><?=str_replace("\\","",$list_name)?></b> updated successfully!</p></td>
				</tr>
			</table>
			<?	
		}	
		echo "<Br><br>";			
	} // END OF IF 1
		
	$result_list = mysql_query("SELECT usli_title_name FROM usli_user_lists WHERE usli_auto_id = '$id'") or die (mysql_error());
	$row_list = mysql_fetch_array($result_list);
?>


	<!-- 'CREATE' FIELDS TABLE -->
	<table cellpadding="2" cellspacing="0" border="0">  
		<form action="<?=$php_self?>" method="post"> 
			<tr valign="top">
				<td><p class="Contact">List Type:</p></td>
				<td>&nbsp;&nbsp;<input class="Text" name="list_name" type="text" value="<?=$row_list['usli_title_name']?>" size="25" maxlength="40"></td>
				<td colspan="2" align="center">&nbsp;&nbsp;<input class="Submit" type="submit" name="editList" value="Update"></td>
			</tr>  
		</form>
	</table>
<?
	echo $table_end;
	echo "</center>";
} 

include('bottom.php');
?>