<? 
include('includes/dbconnect.php');
include('includes/global.php');
include('includes/functions.php');
?>

<title>TDW : Add Prospect</title>
<script language="Javascript" SRC="../includes/javascript.js"></script>
<script language="JavaScript" src="includes/js/popup.js"></script>
<link rel="stylesheet" type="text/css" href="includes/styles.css" />

<?
			tsp(100, "Add Prospect");
			//echo "<br>";
			
			//$ID = 8;
			
			//START OF IF 1
			if($_POST)
			{
			
				//show_array($_POST);
				
					//Client Name AND Client Code ERROR CHECKING
				$array    = array();
				$test_name = array();
				$test_name[1] = "Prospect Name cannot be blank.";
				$test_name[2] = "Prospect Name entered is invalid.";
				$test_name[3] = "The Client Code cannot be blank";
				$test_name[4] = "The Client Code entered is invalid.";

				if($cname == "") 
				{
					$array[1] = "0";
					$cname_blank = "0";
				}  
				else 
				{
					$array[1] = "1";
					$cname_blank = "1";
				}

				$create_err_msg = "There are one or more invalid or incomplete fields. Please resolve this problem and re-submit the data.";
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
			
				//xdebug("clnt_default_payout",$clnt_default_payout);
				//xdebug("clnt_special_payout_rate",$clnt_special_payout_rate);
			
				$query_add = "INSERT INTO int_clnt_clients 
											(clnt_name,
											 clnt_code,
											 clnt_alt_code,
											 clnt_status,
											 clnt_isactive)
											VALUES
											('".strtoupper($cname)."',".
											"'----',".
											"'----',".
											"'P4',2)"; 

				//xdebug("query_add",$query_add);
				$result_add = mysql_query($query_add) or die (tdw_mysql_error($query_add));

				$qry_new_client = "select max(clnt_auto_id) as single_val from int_clnt_clients";
				$new_clnt_id = db_single_val($qry_new_client);
				
				//========================================================================================================
				if (trim($clnt_comment) != '') {
					$qry_comment = "insert into int_clnt_clients_comments
													(auto_id, clnt_auto_id, clnt_comment, clnt_comment_by, clnt_timestamp, clnt_isactive) 
													values (
													NULL,
													'".$new_clnt_id."',
													'".'New Prospect Added.'."',
													'".$user_id."',
													now(),
													1												
													)";
					$result_comment = mysql_query($qry_comment) or die (tdw_mysql_error($qry_comment));
					$qry_comment = "insert into int_clnt_clients_comments
													(auto_id, clnt_auto_id, clnt_comment, clnt_comment_by, clnt_timestamp, clnt_isactive) 
													values (
													NULL,
													'".$new_clnt_id."',
													'".$clnt_comment."',
													'".$user_id."',
													now(),
													1												
													)";
					$result_comment = mysql_query($qry_comment) or die (tdw_mysql_error($qry_comment));
				}	else {
					$qry_comment = "insert into int_clnt_clients_comments
													(auto_id, clnt_auto_id, clnt_comment, clnt_comment_by, clnt_timestamp, clnt_isactive) 
													values (
													NULL,
													'".$new_clnt_id."',
													'".'New Prospect Added.'."',
													'".$user_id."',
													now(),
													1												
													)";
					$result_comment = mysql_query($qry_comment) or die (tdw_mysql_error($qry_comment));
				}
				//========================================================================================================
			
				//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
				//Send emails to reviewers
				//production
				$arr_recipient = array();
				$arr_userfullname = array();
				$qry_email = 	"select b.Email, b.Fullname  
											 		from var_lookup_emails a
													left join users b  on a.var_user_id = b.ID
													where a.var_isactive = 1 
													and b.user_isactive = 1
													and a.var_category = 'CREATEPROSPECT'";
				$result_email = mysql_query($qry_email) or die(tdw_mysql_error($qry_email));
				while ( $row_email = mysql_fetch_array($result_email) ) {
					$arr_recipient[] = $row_email["Email"];
					$arr_userfullname[] = $row_email["Fullname"];
				}
				
				$user_creator = db_single_val("select Fullname as single_val from users where ID = '".$user_id."'");
				
				foreach ($arr_recipient as $key => $emailval) {
				
								//$user_id = get_user_id($emailval);
								//$link = "";
								//$link = $_site_url."repsvr.php?rep=DCARV2&src=".rand(10000000,99999999).str_replace('-','N',$trade_date_to_process).str_pad($user_id,10,'Q',1).md5("pprasad@centersys.com");
								
								$email_log = '
													<table width="100%" border="0" cellspacing="0" cellpadding="10">
														<tr> 
															<td valign="top">
																<p><a class="bodytext12"><strong>Prospect added by '. $user_creator .' on '.date("m/d/Y").' at '.date("h:ia").'</a></p>			
																<p><a class="bodytext12">Prospect Name: <strong>'.strtoupper($cname).'</strong></a></p>
																<p class="bodytext12">Please review  in TDW and take appropriate action.</p>
																<p>&nbsp;</p>
																<p>&nbsp;</p>
																<p><a class="bodytext12"><strong>TDW Administrator</strong></a></p></td>
														</tr>
													</table>
														';
								//create mail to send
								$html_body = "";
								$html_body .= zSysMailHeader("");
								$html_body .= $email_log;
								$html_body .= zSysMailFooter ();
								
								$subject = "Prospect [".strtoupper($cname)."] added by ". $user_creator;
								$text_body = $subject;
								
								zSysMailer($emailval, "", $subject, $html_body, $text_body, "") ;
								//echo $link . "<br>";
				}
				//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			
			
				
				//<!-- showmsg success -->
        showmsg(1, "Prospect [".$cname."] added successfully.");
	    	} // END OF INSERTING DATA IN TABLE
		} // END OF IF 1

		//xdebug("qry_new_client",$qry_new_client);
		//xdebug("new_clnt_id",$new_clnt_id);

    //show_array($_POST);
		
		if ($new_clnt_id > 0) {
			$qry_client = "SELECT 
											a.clnt_auto_id,
											a.clnt_code,
											a.clnt_alt_code,
											a.clnt_name,
											a.clnt_rr1,
											a.clnt_rr2,
											a.clnt_trader,
											a.clnt_status,
											b.clnt_default_payout,
											b.clnt_special_payout_rate,
											b.clnt_start_month,
											b.clnt_default_n_months
										FROM int_clnt_clients a 
										LEFT JOIN int_clnt_payout_rate b on a.clnt_auto_id = b.clnt_auto_id
										AND a.clnt_auto_id = '".$new_clnt_id."'";
	
			$result_client = mysql_query($qry_client) or die (tdw_mysql_error($qry_client));
			while ( $row_client = mysql_fetch_array($result_client) ) 
			{
				//show_array($row_client);
				$cname = $row_client["clnt_name"]; 
				$code = $row_client["clnt_code"];
				$altcode = $row_client["clnt_alt_code"];
				$rr1 = $row_client["clnt_rr1"];
				$rr2 = $row_client["clnt_rr2"];
				$trader = $row_client["clnt_trader"];
				$default_payout = $row_client["clnt_default_payout"];
				$special_payout_rate = $row_client["clnt_special_payout_rate"];
				$default_n_months = $row_client["clnt_default_n_months"];
			}
		}
		
		//xdebug("default_payout",$default_payout);
		//xdebug("special_payout_rate",$special_payout_rate);
?>


		<!-- 'CREATE' FIELDS TABLE -->
		<table cellpadding="2" cellspacing="0" border="0" height="100%" width="70%">  
			<form action="<?=$php_self?>" method="post"> 
			<tr> 
				<td>  
					<table>
						<tr valign="top">
							<td class="ilt" nowrap="nowrap">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                              &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                              </td>
							<td class="ilt">&nbsp;</td>
						</tr>
						<tr valign="top">
							<td class="ilt">Prospect Name :</td>
							<td><input name="cname" type="text" class="Text" value="<?=$cname?>" size="38" maxlength="60" /><font color="#FF0000">*</font></td>
						</tr>
						<tr valign="top">
							<td colspan="2"><p align="justify"><font style="font-size:9px; font-family:Verdana;" color="blue">All new prospects are assigned a default status code of P4 (Prospect Unassigned, New Prospect). 
              This Prospect will then be reviewed by relevant personnel and processed appropriately.</font></p></td>
						</tr> 
						<tr valign="top">
							<td colspan="2">&nbsp;</td>
						</tr>
						<tr valign="top">
							<td class="ilt" colspan="2">Comment :</td>
						</tr>
						<tr valign="top">
							<td class="ilt" colspan="2"><textarea name="clnt_comment" id="clnt_comment" rows="4" cols="43"></textarea></td>
						</tr>
						<tr valign="top">
							<td colspan="2" align="center"><p class="Contact">Fields marked with an asterisk <font color="#FF0000">*</font> are required</p></td></tr>
						<tr valign="top">
							<td colspan="2" align="center">
              <input type="hidden" name="user_id" id="user_id" value="<?=$user_id?>" />
              <? if (!$_POST) {
							?>
								<input class="Submit" type="submit" name="addClient" value="Save Prospect">
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
		tep();
?>
