<?
include('../includes/dbconnect.php');
include('../includes/functions.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Delete Info</title>
</head>

<body>
<? 
		if ($delete)
		{ 
			$query_delete = ("DELETE from carol_test WHERE auto_id = '$auto_id'");
			$result_upd = mysql_query($query_delete) or die(mysql_error());
			echo "<p align=\"center\">Info deleted!</p>";
			?>
			<p align="center">
			<input name="close" type="submit" onClick="javascript:window.close()" value="Close" />
			</p>
<?			
		}
			
		else
		{
?>		
<form action="<?=$PHP_SELF?>" method="post" name="del_comm" id="del_comm">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td align="center">Are you sure you want to delete your info?</td>
	</tr>
	<tr>
	  <td>&nbsp;</td>
    </tr>
	<tr>
	  <td align="center">
	  	<input name="auto_id" type="hidden" value="<?=$auto_id?>" />
		<input name="delete" type="submit" value="Delete" />&nbsp;&nbsp;&nbsp;&nbsp;
		<input name="cancel" type="button" onClick="javascript:window.close()" value="Cancel" />		
	  </td>
	</tr>
</table>
</form>
<?
}
?>
</body>
</html>
