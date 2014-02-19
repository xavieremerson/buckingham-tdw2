<?
include('../includes/dbconnect.php');
include('../includes/functions.php');

if ($val1 != '') {
mysql_query("INSERT INTO carol_test (field1, field2, field3) VALUES ('$val1', '$val2', '$val3')") or die(mysql_error());
}
?>
<script language="JavaScript" src="../includes/js/popup.js"></script>
<table class="txt_status">
<tr>
<td></td>
<td></td>
<td>id</td>
<td>val1</td>
<td>val2</td>
<td>val3</td>
</tr>
<?						
$query_test = "SELECT * from carol_test order by auto_id desc";
$result_test = mysql_query($query_test) or die(mysql_error());

while($row_test = mysql_fetch_array($result_test))
{
?>
<tr>
<td><a href="javascript:CreateWnd('test_edit_info.php?auto_id=<?=$row_test[auto_id]?>', 350, 250, false);">Edit</a></td>
<td><a href="javascript:CreateWnd('test_del_info.php?auto_id=<?=$row_test[auto_id]?>', 350, 250, false);">Delete</a></td>
<td><?=$row_test["auto_id"]?></td>
<td><?=$row_test["field1"]?></td>
<td><?=$row_test["field2"]?></td>
<td><?=$row_test["field3"]?></td>
</tr>
<?
}
?>
</table>