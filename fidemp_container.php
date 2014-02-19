<?php
//BRG
include('inc_header.php');
 
?>
		<!-- START TABLE 1 -->
		<table width="100%" height="100%" border="0" cellspacing="1" cellpadding="0">
			<tr> 
				<td valign="top" align="center">
				<?
				/*if ($user_id != 79 and $user_id != 93 and $user_id != 253) {
				echo "<center><br><br><br><a class='ilt'>This module is currently under development.
				<br>If you have access privileges to view this module, you will be notified 
				    when this module goes into production.
				<br>
				Thank You.
				<br>
				TDW Admin</a></center>";*/
				if ($user_id != 79 and $user_id != 93 and $user_id != 253 and $user_id != 302) { //
				echo "<center><br><br><br><a class='ilt'>You do not have appropriate access privilege to this module.
				<br>Please contact Technical Support for to set access privileges.
				<br>
				Thank You.
				<br>
				TDW Admin</a></center>";				
				} else {				
				include('fidemp_config.php');
				}
				?>
				</td>
			</tr>
		</table>
		<!-- END TABLE 1 -->
<?php
include('inc_footer.php'); 
?>
