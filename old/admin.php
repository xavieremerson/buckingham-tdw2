<!-----------------------------------------------------------------------------------------
@author : Ratan Rao (rrao@csysg.com)
@category : 
@copyright : © 2002-2004 Centersys Group, Inc.
@filesource : 
@license : 
@name : 
@package :
@param :
@todo :
@version : 2.0
@since :

@purpose : 

------------------------------------------------------------------------------------------->

<?php

  include('top.php');
	 
?>
<tr>
<td valign="top">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td valign="top">
		<!-- ADMIN CONTENT BEGIN -->
		<? echo $table_start; ?>
		<?
		if ($mode == "open") {
		// do something here
		} else {
		echo '<a class="links10">Please select from the following actions.</a>';
		}
		
		?>		
		<? echo $table_end; ?>				

		
		
		
		
		
		
		<? echo $table_start; ?>
		<a class="links10" href="<?=$php_self?>?mode=preparedata">Prepare Data for Demo</a>
		<?
		if ($mode == "preparedata") {
		echo '<br><hr>';
		include ('admin_prepare.php');
		echo '<hr>';
		}
		?>
		<? echo $table_end; ?>
		
		
		<? echo $table_start; ?>
		<a class="links10" href="<?=$php_self?>?mode=useradmin">User Administration</a>
		<?
		if ($mode == "useradmin") {
		echo '<br><hr>';
		include ('admin_useradmin.php');
		echo '<hr>';
		}
		?>
		<? echo $table_end; ?>
		
		
		
		
		
		
		
		
		
		
		
		
		
		<!-- ADMIN CONTENT END -->
		</td>
  </tr>
</table>
</td>
</tr>

<?php

  include('bottom.php');
	 
?>

