<style type="text/css">
<!--
.textitem {
	font-family: verdana;
	font-size: 12px;
	font-weight: bold;
	color: #000099;
}
.labels {
	font-family: verdana;
	font-size: 12px;
	font-weight: bold;
	color: #000099;
}
-->
</style>
		<center>
											<!--MAIL MODULE-->
											
										<? if ($submit) {
													if	($msender == '') {
														$mstatus = "Your email is blank. Mail not sent.";
														} else { 
														mail($mtoemail.",".$mccemail,$msubject,$mbody,"From: $msender <$msender>");
														$mstatus = "Mail sent.";
														}
										?>
										
										<center>
										<p align="center">
											<font color="#999999" size="2" face="Verdana" >
												
												<B><?=$mstatus?></B><BR><BR>
											</font>
										</p>
										</center>
										
										<table bgcolor="#999999" border="0" cellpadding="1" cellspacing="0">
										<tr>
										<td>
												<table class="txtbluecsys" bgcolor="#FFFFFF" width="160" border="0" cellpadding="3" cellspacing="0">
												<tr><td colspan=2 align="center" valign="middle" height="20" background="images/boxheadbkground.gif"><font size="1" face="Verdana, Arial, Helvetica, sans-serif" color="#330099"><B>Send Email</B></font></td></tr>
												<form action="<?=$PHP_SELF?>" method="post" name="mailform">
												<tr><td class="labels" nowrap>To:</td><td><input type="text" name="mtoemail" size="50" class="textitem"></td></tr>
												<tr><td class="labels" nowrap>CC:</td><td><input type="text" name="mccemail" size="50" class="textitem"></td></tr>
												<tr><td class="labels" nowrap>Your email:</td><td><input type="text" name="msender" size="50" class="textitem"></td></tr>
												<tr><td class="labels">Subject:</td><td><input type="text" name="msubject" size="69" class="textitem"></td></tr>
												<tr><td class="labels">Message:</td><td><textarea name="mbody" cols="68" rows="16" wrap="virtual" class="textitem"></textarea></td></tr>
												<tr><td></td><td align="left"><input name="submit" type="submit" value="   Send Email   " class="textitem"></td></tr>
												
												<input type="hidden" name="mrecipient" value="<?=$recipient?>">
												</form>
												</table>
										</td>
										</tr>
										</table>										
										
										<p  class="labels">Intended recipients of Compliance Emails are: 
										<br>Roger Cotta rcc@tocqueville.com
										<br>Liz Bosco efb@tocqueville.com
										<br>Lucinda Lormier llt@tocqueville.com									
										</p>
										
										
										
										<?
										} else {
										?>
											
										<table bgcolor="#999999" border="0" cellpadding="1" cellspacing="0">
										<tr>
										<td>
												<table class="txtbluecsys" bgcolor="#FFFFFF" width="160" border="0" cellpadding="3" cellspacing="0">
												<tr><td colspan=2 align="center" valign="middle" height="20" background="images/boxheadbkground.gif"><font size="1" face="Verdana, Arial, Helvetica, sans-serif" color="#330099"><B>Send Email</B></font></td></tr>
												<form action="<?=$PHP_SELF?>" method="post" name="mailform">
												<tr><td class="labels" nowrap>To:</td><td><input type="text" name="mtoemail" size="50" class="textitem"></td></tr>
												<tr><td class="labels" nowrap>CC:</td><td><input type="text" name="mccemail" size="50" class="textitem"></td></tr>
												<tr><td class="labels" nowrap>Your email:</td><td><input type="text" name="msender" size="50" class="textitem"></td></tr>
												<tr><td class="labels">Subject:</td><td><input type="text" name="msubject" size="69" class="textitem"></td></tr>
												<tr><td class="labels">Message:</td><td><textarea name="mbody" cols="68" rows="16" wrap="virtual" class="textitem"></textarea></td></tr>
												<tr><td></td><td align="left"><input name="submit" type="submit" value="   Send Email   " class="textitem"></td></tr>
												
												<input type="hidden" name="mrecipient" value="<?=$recipient?>">
												</form>
												</table>
										</td>
										</tr>
										</table>										
										
										<p  class="labels">Intended recipients of Compliance Emails are: 
										<br>Roger Cotta rcc@tocqueville.com
										<br>Liz Bosco efb@tocqueville.com
										<br>Lucinda Lormier llt@tocqueville.com									
										</p>
											
										<?
										}
										?>
										

											<!--MAIL MODULE END-->
											</center>