<? 
include('includes/dbconnect.php');
include('includes/global.php');
include('includes/functions.php');
?>

<title>TDW</title>
<link rel="stylesheet" type="text/css" href="includes/styles.css" />

<?
//if ($_GET["user_id"] != 79) {
//echo "Module in pre-production testing. Only CenterSys users can access at this time.";
//exit;
//}


tsp(100, "Edit External Employee Account");

//====================================================================================================
			//START OF IF 1
			if($editAcct)
			{
					//FIRST NAME, LAST NAME AND EMAIL ERROR CHECKING
				$atSign   = strstr($email, "@"); 
				$fullStop = strstr($email, ".");
				$array    = array();
				$test_name = array();
				$test_name[1] = "Account Number cannot be blank.";
				$test_name[2] = "Custodian cannot be blank.";
				if(trim($oac_account_number) == "") 
				{
					$array[1] = "0";
					$acct_blank = "0";
				}  
				else 
				{
					$array[1] = "1";
					$acct_blank = "1";
				}
				if(trim($oac_custodian) == "") 
				{
					$array[2] = "0";
					$custodian_blank = "0";
				} 
				else 
				{
					$array[2] = "1";
					$custodian_blank = "1";
				}				
				
       // ERRORS FOUND IN INPUT
			if($array[1] == "0" OR $array[2] == "0") 
			{
					$create_err_msg = "There are one or more invalid or incomplete fields. Please resolve this problem and re-submit the data.";
					$show_err = 0;
						for($x = 1; $x <= count($array); $x++)
						{
							if($array[$x] == "0") 
							{
								$create_err_msg = $create_err_msg . "<br>" . $test_name[$x];
								$show_err = 1;
							} 
						}
					
					if ($show_err == 1) {
					showmsg(3, $create_err_msg);
					}
			}
			// NO ERRORS FOUND, HENCE UPDATE DATA IN TABLE
			else
			{
				//$fullname = $fname . " " . $lname;
				
				if ($oac_isactive) {
				  $val_oac_isactive = 1;
				} else {
				  $val_oac_isactive = 0;
        }
				
				//auto_id  oac_emp_userid  oac_custodian  oac_account_number  oac_acct_close_date  oac_entered_by  oac_last_edited_by  oac_last_edited_on  oac_comment oac_isactive 
				
				$old_comment = db_single_val("select oac_comment as single_val from oac_emp_accounts where auto_id ='".$acctid."' LIMIT 1");
				
				$query_edit = "UPDATE oac_emp_accounts
											 SET
											 oac_custodian = '".str_replace("'","\\'",$oac_custodian)."',
											 oac_account_number = '".str_replace("'","\\'",$oac_account_number)."',
											 oac_acct_close_date = NULL ,
											 oac_last_edited_by = '".$user."', 
											 oac_last_edited_on = now(),
											 oac_isactive = '".$val_oac_isactive."',
											 oac_comment = concat_ws('".str_replace("'","\\'",$oac_comment)."','<br>','".str_replace("'","\\'",$old_comment)."')
											WHERE auto_id ='".$acctid."' LIMIT 1";

				//xdebug("query_edit",$query_edit);
				$result_edit = mysql_query($query_edit) or die (tdw_mysql_error($query_edit));

        showmsg(1, "Account Number: [".$oac_account_number."] updated successfully.");

	    	} // END OF INSERTING DATA IN TABLE
		} // END OF IF 1
//====================================================================================================













		$result_acct = mysql_query("SELECT a. * , b.Fullname as employee, c.Fullname as editedby
																FROM oac_emp_accounts a, users b, users c
																WHERE a.oac_emp_userid = b.ID
																and a.oac_last_edited_by = c.ID
																AND a.auto_id = '".$acctid."'") or die (mysql_error());
    //auto_id  oac_emp_userid  oac_custodian  oac_account_number  oac_acct_close_date  oac_entered_by  oac_last_edited_by  oac_isactive  oac_isactive 
		while ( $row = mysql_fetch_array($result_acct) ) 
		{
			$employee = $row["employee"];  //str_replace("'","\'",$row["Fullname"]);
			$oac_custodian = $row["oac_custodian"];
			$oac_account_number = $row["oac_account_number"];
			$editedby = $row["editedby"];
			$oac_isactive = $row["oac_isactive"];
			$oac_last_edited_on = $row["oac_last_edited_on"];
			$oac_comment = $row["oac_comment"];
		}
		
?>
<?
$req_field = '<font color="#FF0000">*</font>';
?>

		<!-- 'CREATE' FIELDS TABLE -->
		<table cellpadding="2" cellspacing="0" border="0" height="100%" width="70%">  
			<form action="<?=$php_self?>" method="post"> 
			<tr>
				<td> 
					<table>
						<tr valign="top">
							<td class="ilt">Employee:</td>
							<td><?=$employee?></td>
						</tr>
						<tr valign="top">
							<td class="ilt">Account Number:</td>
							<td><input class="Text" name="oac_account_number" type="text" value="<?=$oac_account_number?>" size="30" maxlength="40"><?=$req_field?></td>
						</tr>				
						<tr valign="top">
							<td class="ilt">Custodian:</td>
							<td><input class="Text" name="oac_custodian" type="text" value="<?=$oac_custodian?>" size="30" maxlength="40"><?=$req_field?></td>
						</tr>				
						<tr valign="top">
							<td class="ilt">Is Active:</td>
							<td><input name="oac_isactive" type="checkbox" value="1" <? if($oac_isactive == 1) { echo "checked"; } ?>/></td>
						</tr>
						<tr valign="top">
							<td class="ilt">Comment:</td>
							<td><textarea name="oac_comment" rows="3" cols="30"/></textarea></td>
						</tr>
						<tr valign="top">
							<td colspan="2">Last Edited by <?=$editedby?> on <?=date('m/d/Y h:ia',strtotime($oac_last_edited_on))?></td>
						</tr>
						<tr valign="top">
							<td colspan="2"><hr align="left" width="400" size="1" noshade color="#0000FF"></td>
						</tr>
						<tr valign="top">
							<td colspan="2" align="center"><p class="Contact">Fields marked with an asterisk <?=$req_field?> are required</p></td></tr>
						<tr valign="top">
							<td colspan="2" align="center"><input class="Submit" type="submit" name="editAcct" value="Update"></td>
						</tr>  
					</table>
				</td>
			</tr> 
      <input type="hidden" name="user" value="<?=$user_id?>" />
      <input type="hidden" name="acctid" value="<?=$acctid?>" />
			</form>
		</table>
<?
tep();
?>
