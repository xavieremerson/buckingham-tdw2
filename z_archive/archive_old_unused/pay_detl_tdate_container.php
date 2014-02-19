<?php
//BRG
include('inc_header.php');
 
?>
		<!-- START TABLE 1 -->
		<table width="100%" height="100%" border="0" cellspacing="1" cellpadding="0">
			<tr> 
				<td valign="top" align="center">
				<?
				if ($user_id != 79 and $user_id != 93 and $user_id != 253) {
				echo "<center><br><br><br><a class='ilt'>This module is currently under development.
				<br>If you have access privileges to view this module, you will be notified 
				    when this module goes into production.
				<br>
				Thank You.
				<br>
				TDW Admin</a></center>";
				} else {				
				include('pay_detl_tdate_config.php');
				}
				?>
				</td>
			</tr>
		</table>
		<!-- END TABLE 1 -->
<?php
include('inc_footer.php'); 
?>
