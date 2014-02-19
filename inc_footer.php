  </td>
</tr>
<tr valign="bottom">
  <td height="10">
    <table width="100%"> <!-- height="20"-->
      <tr valign="top">
      <td align="center" valign="bottom">
        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="tbl_bordertop_1">  <!--height="20" -->
          <tr valign="top"> 
          <td align="center" valign="bottom">
              <center>
              <a class="centersys" href="http://www.centersys.com" target="_blank">CenterSys Group, Inc.</a>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              <a class="centersys"> 
              <?
              $str_perf = sprintf("%01.2f",((getmicrotime()-$time)/1000));
							echo " Process Time ". $str_perf." s.";             
              ?>
              </a><br />
              <?
							$str_temp = "";
							if ($str_perf > 7.9) {
								if ($_GET) {
									foreach($_GET as $k=>$v) {
										$str_temp .= $k."=".$v."&";
									}
									$str_temp .= "rand=".md5(rand(1,999999));
									$getval = "?".$str_temp;
								} else {
									$getval = "";
								}
								$email_log = '
													<table width="100%" border="0" cellspacing="0" cellpadding="10">
														<tr> 
															<td valign="top">
																<p><a class="bodytext12"><strong>Performance Issue Indicator</strong></a></p>			
																<p><a class="bodytext12">Date: <strong>'.date('m/d/Y h:i:sa').'</strong></a></p>
																<p class="bodytext12">USER: <strong>'.$userfullname.'</strong></p>
																<p class="bodytext12">URL Accessed: <strong>'.$_SERVER['PHP_SELF'].$getval.'</strong></p>
																<p class="bodytext12">Time taken: <strong>'.$str_perf." s.".'</strong></p>
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
								
								$subject = "Performance Issue Indicator: " . date('m/d/Y h:i:sa') .": ".$_SERVER['PHP_SELF'].$getval;
								$text_body = $subject;
								
								zSysMailer("pprasad@centersys.com", "", $subject, $html_body, $text_body, "") ;
							}
							
							//make a database entry
							if ($str_perf > 2.9) {
							$qry = "INSERT INTO z_perf_issue_data (auto_id,perf_date,perf_url,perf_time,perf_user) 
											VALUES (
											NULL , now(), '".$_SERVER['PHP_SELF']."', '".$str_perf."', '".$user_id."'
											)";
							$result = mysql_query($qry) or die (tdw_mysql_error($qry));
							}


							//echo $str_timedebug;
							?>
              </center>
          </td>
          </tr>
        </table>
      </td>
      </tr>
    </table>
  </td>
</tr>
</table>
</body>
</html>
<?
exit;
?>