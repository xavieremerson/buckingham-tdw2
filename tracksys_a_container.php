<?php
//BRG
include('inc_header.php');
 
?>
		<!-- START TABLE 1 -->
		<table width="100%" height="100%" border="0" cellspacing="1" cellpadding="0">
			<tr> 
				<td valign="top">
				<iframe height="100%" width="100%" src="http://192.168.20.63/trk/login.php?username=<?=trim(strtolower($user_email))?>"></iframe>
				</td>
			</tr>
		</table>
		<!-- END TABLE 1 -->
<?php
include('inc_footer.php'); 
?>

