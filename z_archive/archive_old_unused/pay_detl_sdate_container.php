<?php
//BRG
include('inc_header.php');
 
?>
		<!-- START TABLE 1 -->
		<table width="100%" height="100%" border="0" cellspacing="1" cellpadding="0">
			<tr> 
				<td valign="top" align="center">
				<?
				if (1==1) { //$user_id != 79 and $user_id != 93 and $user_id != 253
				echo "<center><br><br><br><a class='ilt'>Lloyd & Kevin : This module is currently being updated.
				<br>You will be notified when this module goes into production. [tentatively Tuesday 4 Mar evening).
				<br>
				Thank You.
				<br>
				TDW Admin</a></center>";
				} else {				
				include('pay_detl_sdate_config.php');
				}
				?>
				</td>
			</tr>
		</table>
		<!-- END TABLE 1 -->
<?php
include('inc_footer.php'); 
?>
