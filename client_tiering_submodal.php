<? 
include('includes/dbconnect.php');
include('includes/global.php');
include('includes/functions.php');
?>

<title>TDW</title>
<script language="Javascript" SRC="../includes/javascript.js"></script>
<script language="JavaScript" src="includes/js/popup.js"></script>
<link rel="stylesheet" type="text/css" href="includes/styles.css" />

<?
/*user_id = [79]
  element_id = [C_ARCC]
  elementval = [comment]
*/
$arr_element = explode("_",$element_id);

$arr_title = array("B"=>"Modify/Override Budget","T"=>"Modify/Override Tier","C"=>"Add Comment");


			tsp(100, $arr_title[$arr_element[0]]);
			
			
			//$ID = 8;
			
			//START OF IF 1
			if($_POST)
			{
			
				//show_array($_POST);
				
					//Client Name AND Client Code ERROR CHECKING
				$array    = array();
				$test_name = array();
				$test_name[1] = "You must enter a comment.";
				$test_name[2] = "Client Name entered is invalid.";
				$test_name[3] = "The Client Code cannot be blank";
				$test_name[4] = "The Client Code entered is invalid.";

				if($itemval_b == "") 
				{
					$array[1] = "0";
					$comment_blank = "0";
				}  
				else 
				{
					$array[1] = "1";
					$comment_blank = "1";
				}

				$create_err_msg = "There are one or more invalid or incomplete fields. Please resolve this problem and re-submit the data.<br><br>";
				$show_err = 0;
				foreach($array as $k=>$v) {
		
					if($v == "0") 
					{
						$create_err_msg = $create_err_msg . "<br>" . $test_name[$k];
						$show_err = 1;
					} 
							
				}			
						
			if ($show_err == 1) {
			showmsg(3, $create_err_msg);
			}
			
			// NO ERRORS FOUND, HENCE INSERT DATA IN TABLE
			else
			{
			
				//show_array($_POST);
				/*user_id = [79]
				attribute_val = [T]
				client_code = [ADAG]
				cname = [ADAGE CAPITAL MGMT]
				itemval_a = [1]
				itemval_b = [sdfsdfsdf]*/
				
				$str_comment_data = str_replace("'","",$itemval_b);
				$str_comment_data = str_replace('"'," ",$str_comment_data);
				$str_comment_data = str_replace("\n"," ",$str_comment_data);
				$str_comment_data = str_replace("\n\r"," ",$str_comment_data);
				$str_comment_data = str_replace("\r"," ",$str_comment_data);
				$str_comment_data = str_replace("&"," ",$str_comment_data);
				
				//xdebug("str_comment_data","<pre>".$str_comment_data."</pre>");
				
				
				if ($attribute_val == 'T') {
				$qry_z = "INSERT INTO int_clnt_clients_tiering (
										auto_id ,
										clnt_code ,
										clnt_year ,
										clnt_attribute ,
										clnt_budget ,
										clnt_tier ,
										clnt_comment ,
										clnt_override_id ,
										clnt_override_comment ,
										clnt_timestamp ,
										clnt_isactive 
									)
									VALUES (
									NULL , '".$client_code."', '2010', 'T', NULL , '".$itemval_a."', '', '".$user_id."', '".$str_comment_data."', CURRENT_TIMESTAMP , '1')";
				} elseif ($attribute_val == 'B') {
				
				$clean_budget = str_replace("'","",$itemval_a);
				$clean_budget = str_replace("$","",$clean_budget);
				$clean_budget = str_replace(",","",$clean_budget);
				$clean_budget = str_replace("&","",$clean_budget);
				$clean_budget = str_replace("@","",$clean_budget);
				$clean_budget = str_replace('"',"",$clean_budget);
				$clean_budget = str_replace(" ","",$clean_budget);
				
				$clean_budget = (int)$clean_budget;

				
				$qry_z = "INSERT INTO int_clnt_clients_tiering (
										auto_id ,
										clnt_code ,
										clnt_year ,
										clnt_attribute ,
										clnt_budget ,
										clnt_tier ,
										clnt_comment ,
										clnt_override_id ,
										clnt_override_comment ,
										clnt_timestamp ,
										clnt_isactive 
									)
									VALUES (
									NULL , '".$client_code."', '2010', 'B', '".$clean_budget ."' , NULL, '', '".$user_id."', '".$str_comment_data."', CURRENT_TIMESTAMP , '1')";
				} else { // just comment
				$qry_z = "INSERT INTO int_clnt_clients_tiering (
										auto_id ,
										clnt_code ,
										clnt_year ,
										clnt_attribute ,
										clnt_budget ,
										clnt_tier ,
										clnt_comment ,
										clnt_override_id ,
										clnt_override_comment ,
										clnt_timestamp ,
										clnt_isactive 
									)
									VALUES (
									NULL , '".$client_code."', '2010', 'C', NULL , NULL, '".$str_comment_data."', '".$user_id."', '', CURRENT_TIMESTAMP , '1')";
				}
			
				$result_z = mysql_query($qry_z) or die(tdw_mysql_error($qry_z));

				//<!-- showmsg success -->
        showmsg(1, "Client [".$cname."] updated successfully.");
				$database_updated = 1;
				?>
        <script language="javascript">
				var itemid_t = "<?="T_".$client_code?>";
				var itemid_c = "<?="C_".$client_code?>";
				var itemid_b = "<?="B_".$client_code?>";
				var itemid_o1 = "<?="o1_".$client_code?>";
				var itemid_o2 = "<?="o2_".$client_code?>";
					<?
					if ($attribute_val == 'T') {
					?>
					opener.document.getElementById(itemid_t).innerHTML = "<?=$itemval_a?> <img src='images/tier/<?=$itemval_a?>.png' height='13' border='0'>&nbsp;&nbsp;&nbsp;";
					opener.document.getElementById(itemid_c).innerHTML = opener.document.getElementById(itemid_c).innerHTML + "<br>&#9658;" + "<?=$str_comment_data?>";
					opener.document.getElementById(itemid_o1).innerHTML = "&Ocirc;";				
					<?
					} elseif($attribute_val == 'B') {
					?>
					opener.document.getElementById(itemid_b).innerHTML = "<?=number_format($clean_budget,0,"",",")?>&nbsp;";
					opener.document.getElementById(itemid_c).innerHTML = opener.document.getElementById(itemid_c).innerHTML + "<br>&#9658;" + "<?=$str_comment_data?>";
					opener.document.getElementById(itemid_o2).innerHTML = "&Ocirc;";									
					<?
					} else {
					?>
					//alert("<?=$str_comment_data?>");
					opener.document.getElementById(itemid_c).innerHTML = opener.document.getElementById(itemid_c).innerHTML + "<br>&#9658;" + "<?=$str_comment_data?>";
					<?
					}
					?>
        </script>
        <?
	    	} // END OF INSERTING DATA IN TABLE
		} // END OF IF 1

		//xdebug("qry_new_client",$qry_new_client);
		//xdebug("new_clnt_id",$new_clnt_id);

?>


		<!-- 'CREATE' FIELDS TABLE -->
		<?
		if ($database_updated != 1) {
		?>
    <table cellpadding="2" cellspacing="0" border="0" height="100%">  
			<form action="<?=$php_self?>" method="post"> 
      	<input type="hidden" name="user_id" value="<?=$user_id?>" />
      	<input type="hidden" name="attribute_val" value="<?=$arr_element[0]?>" />
      	<input type="hidden" name="client_code" value="<?=$arr_element[1]?>" />
      	<input type="hidden" name="cname" value="<?=$cname?>" />
			<tr> 
				<td>  
					<table>
						<tr valign="top">
							<td class="ilt" nowrap="nowrap">&nbsp; 
                                              </td>
							<td class="ilt" nowrap="nowrap">&nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                              &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</td>
						</tr>
						<tr valign="top">
							<td class="ilt">Client Name :</td>
							<td><font color="#000066" face="Verdana, Arial, Helvetica, sans-serif"><?=$cname?> [<?=$arr_element[1]?>]</font></td>
						</tr>
						<?
						if ($arr_element[0] == 'T') {
						?>
						<tr valign="top">
							<td class="ilt">Current Tier :</td>
							<td><font color="#000066" face="Verdana, Arial, Helvetica, sans-serif">Tier <?=$elementval?></font></td>
						</tr>
						<tr valign="top">
							<td class="ilt">New Tier : <font color="red">*</font></td>
							<td>
							<select class="Text1" name="itemval_a" size="1" style="width:200px">
							<option value=""> Select Tier </option>
							<option value="1"> Tier 1</option>
							<option value="2"> Tier 2</option>
							<option value="3"> Tier 3</option>
							<option value="4"> Tier 4</option>
							</select>
              </td>
						</tr>
						<tr valign="top">
							<td class="ilt">Comment: <font color="red">*</font></td>
							<td>
							<textarea rows="5" cols="40" class="Text" name="itemval_b"></textarea>
              </td>
						</tr>
            <?
						} elseif($arr_element[0] == 'B') {
						?>
						<tr valign="top">
							<td class="ilt">Current Budget:</td>
							<td><font color="#000066" face="Verdana, Arial, Helvetica, sans-serif"> <?=$elementval?></font></td>
						</tr>
						<tr valign="top">
							<td class="ilt">New Budget: <font color="red">*</font></td>
							<td>
							USD <input class="Text" name="itemval_a" style="width:200px" maxlength="16" />
              </td>
						</tr>
						<tr valign="top">
							<td class="ilt">Comment:  <font color="red">*</font></td>
							<td>
							<textarea rows="5" cols="40" class="Text" name="itemval_b"></textarea>
              </td>
						</tr>
						<?
						} else { //value is C for comment
						?>
						<tr valign="top">
							<td class="ilt">Comment:s <font color="red">*</font></td>
							<td>
							<textarea rows="5" cols="40" class="Text" name="itemval_b"></textarea>
              </td>
						</tr>
            <?
						}
						?>
						<tr valign="top">
							<td colspan="2" align="center"><p class="Contact">Fields marked with an asterisk <font color="#FF0000">*</font> are required</p></td></tr>
						<tr valign="top">
							<td colspan="2" align="center">
              <? if (!$_POST || 1==1) {
							?>
								<input class="Submit" type="submit" name="addClient" value="Save">&nbsp;&nbsp;&nbsp;
                <input type="button" class="Submit" onclick="javascript: self.close();" name="close" value="Close" />
							<?
							}
							?>
              </td>
						</tr>  
					</table>
				</td>
			</tr> 
			</form>
		</table>
    <?
		} else {
		?>
					<table>
          	<tr valign="top">
							<td align="center">
                <input type="button" class="Submit" onclick="javascript: self.close();" name="close" value="Close" />
              </td>
						</tr>  
					</table>
    <?
		}

		tep();
?>
