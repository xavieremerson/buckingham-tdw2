<?php

  include('top.php');
	include('includes/functions.php'); 
?>
<style>
	a, A:link, a:visited, a:active {color: #0000aa; text-decoration: none; font-family: Tahoma, Verdana; font-size: 11px}
	A:hover {color: #ff0000; text-decoration: none; font-family: Tahoma, Verdana; font-size: 11px}
	p, tr, td, ul, li {color: #000000; font-family: Tahoma, Verdana; font-size: 11px}
	.header1, h1 {color: #ffffff; background: #4682B4; font-weight: bold; font-family: Tahoma, Verdana; font-size: 13px; margin: 0px; padding: 2px;}
	.header2, h2 {color: #000000; background: #DBEAF5; font-weight: bold; font-family: Tahoma, Verdana; font-size: 12px;}
	.intd {color: #000000; font-family: Tahoma, Verdana; font-size: 11px; padding-left: 15px; padding-right: 15px;}
</style>
<link rel="stylesheet" href="includes/tree.css"> 
<tr valign="top">	
	<td valign="top" height="100%">
	<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td><!--Main Page<BR>User logged in: <B><? echo $user; ?></B>--></td>
  </tr>
	<tr valign="top"> 
    <td valign="top" height="100%">
		<!-- Tree Menu Implementation -->
		<table width="100%" height="100%"  border="0" cellspacing="0" cellpadding="10">
  <tr valign="top"> 
    <td valign="top" width="150">
		<script language="JavaScript" src="includes/tree.js"></script>
		
		<!-- This is a dynamic inclusion of trades -->
		<?
		//Create the tree content for trades
		create_tree_content_trades($user);
		?>
		<script language="JavaScript" src="includes/<?=$user?>.js"></script>
		<!-- END This is a dynamic inclusion of trades -->
		<script language="JavaScript" src="includes/tree_tpl.js"></script>
		<!-- Tree Here -->
		<script language="JavaScript">
			new tree (TREE_ITEMS, TREE_TPL);
		</script>
		</td>
    <td height="100%" valign="top">
		<!-- Display Data Here --> 
		<?
		if ($module == 'symbol') {
			if ($symbolval != '') {
			 //echo 'Details for trades in '.$symbolval. ' to be displayed here!';
			 include('inc_view_trades_tree.php');
			}
		}  
		if ($module == 'emptrades') {
			if ($listtype != '') {
			 //echo 'Details for trades in '.$symbolval. ' to be displayed here!';
			 include('inc_emp_list_trades_tree.php');
			}
		}
		if ($module == 'flagged') {

			 include('inc_view_flagged_trades_tree.php');

		}


		if ($module == 'actionitems')
		{
			 include('inc_action_items_tree.php');
		}
		

		?>
		
		</td>
  </tr>
</table>

		<!-- End Tree Menu Implementation -->		
		</td>
  </tr>
</table>
</td></tr>

<?php

  include('bottom.php');
	 
?>

