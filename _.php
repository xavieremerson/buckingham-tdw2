<?
include('inc_header.php');
?>
		<!-- START TABLE 1 -->
		<table width="100%" height="100%" border="0" cellspacing="1" cellpadding="0">
			<tr> 
				<td valign="top">
				<?
					if ($_GET["mod"] != "") { 
						include($_GET["mod"].".php");
					} else {
						echo "Module Error!";
					}
				?>
				</td>
			</tr>
		</table>
		<!-- END TABLE 1 -->
<? 
include('inc_footer.php'); 
?>