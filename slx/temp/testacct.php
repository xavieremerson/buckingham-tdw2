<?php

  session_start();

  session_register('user');

  session_register('pass');


  include('../includes/dbconnect.php');
  include('../includes/global.php'); 

?>

<html>
<head>
<link rel="stylesheet" type="text/css" href="../includes/styles.css" />
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<TITLE><? echo "Displaying Holiday List for ". strtoupper($user);  ?></TITLE>
</HEAD>

<body leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td width="195" height="34"><img src="images/logo_left.jpg" width="195" height="34"></td>
    <td background="images/filler_right.jpg">&nbsp;</td>
  </tr>
</table>

<div align="center">



<?php include('../includes/global.htm'); ?>


<?
echo "<p class='GlobOps'><a class='GlobOps'>Displaying Holiday List for ". strtoupper($user) . "</a></p>"; 




if ($action == "")
{

?>

<table cellpadding="2" cellspacing="0" border="0">
	<form action="holidays.php?action=add" method="post">
	<tr valign="top">
	 <td><p class="Contact">Holiday Name</p></td>
	 <td><p class="Contact">:</p></td>
	 <td><p><input name="HolidayName" type="text" class="Text" value="is what you say, e.g. Merry Christmas" size="40" maxlength="100" ></p></td>
	</tr>
	<tr valign="top">
	 <td><p class="Contact">Holiday Date (YYYY-MM-DD)</p></td>
	 <td><p class="Contact">:</p></td>
	 <td><p><input class="Text" type="text" name="HolidayDate" size="40" maxlength="100" ></p></td>
	</tr>
	<tr valign="top">
	 <td><p class="Contact">Greetings</p></td>
	 <td><p class="Contact">:</p></td>
	 <td><textarea name="HolidayGreetings" cols="80" rows="8" class="TextArea">This message is going to go to all your contacts, so keep the message simple and generic, no names, dates, him's and her's ...</textarea></td>
	</tr>
	<tr valign="top">
	 <td colspan="3" align="right"><p><input class="Submit" type="submit" value="Add to Holiday List"></p></td>
	</tr>  
	</form>

<?


   $resultmember = mysql_query("SELECT holiday_id, HolidayName, HolidayDate, HolidayGreetings FROM tbl_".$user."_holidays order by holiday_id desc") or die (mysql_error());

   while ( $row = mysql_fetch_array($resultmember) ) {

			$holiday_id = $row["holiday_id"];
			$HolidayName = $row["HolidayName"];
			$HolidayDate = $row["HolidayDate"];
			$HolidayGreetings = $row["HolidayGreetings"];

?>

	<form action="holidays.php?action=update" method="post">
	<input name="holiday_id" type="hidden" class="Text" value="<?=$holiday_id?>">
	<tr valign="top">
	 <td><p class="Contact">Holiday Name</p></td>
	 <td><p class="Contact">:</p></td>
	 <td><p><input class="Text" type="text" name="HolidayName" size="40" maxlength="100" value="<?=$HolidayName?>"></p></td>
	</tr>
	<tr valign="top">
	 <td><p class="Contact">Holiday Date (YYYY-MM-DD)</p></td>
	 <td><p class="Contact">:</p></td>
	 <td><p><input class="Text" type="text" name="HolidayDate" size="40" maxlength="100" value="<?=$HolidayDate?>"></p></td>
	</tr>
	<tr valign="top">
	 <td><p class="Contact">Greetings</p></td>
	 <td><p class="Contact">:</p></td>
	 <td><p class="Text"><textarea name="HolidayGreetings" cols="80" rows="8"><?=$HolidayGreetings?></textarea></p></td>
	</tr>
  <tr valign="top">
	 <td colspan="3" align="right">
	  <table>
	   <tr>
	    <td><input class="Submit" type="submit" value="Update Holiday List"></form></td>
	    <td><form action="holidays.php?action=delete&deleteid=<?=$holiday_id?>" method="post"><input class="Submit" type="submit" value="DELETE"></form></td>
	   </tr>
	  </table>
	 </td>
	</tr> 


<?
		}
?>

</table>

<?
}


elseif ($action == "delete")
{
   
$resultmember = mysql_query("delete from tbl_".$user."_holidays where holiday_id = ".(int)$deleteid) or die (mysql_error());

?>

<table cellpadding="2" cellspacing="0" border="0">
	<form action="holidays.php?action=add" method="post">
	<tr valign="top">
	 <td><p class="Contact">Holiday Name</p></td>
	 <td><p class="Contact">:</p></td>
	 <td><p><input class="Text" type="text" name="HolidayName"  value="is what you say, e.g. Merry Christmas"  size="40" maxlength="100" ></p></td>
	</tr>
	<tr valign="top">
	 <td><p class="Contact">Holiday Date (YYYY-MM-DD)</p></td>
	 <td><p class="Contact">:</p></td>
	 <td><p><input class="Text" type="text" name="HolidayDate" size="40" maxlength="100" ></p></td>
	</tr>
	<tr valign="top">
	 <td><p class="Contact">Greetings</p></td>
	 <td><p class="Contact">:</p></td>
	 <td><textarea name="HolidayGreetings" cols="80" rows="8" class="TextArea">This message is going to go to all your contacts, so keep the message simple and generic, no names, dates, him's and her's ...</textarea></td>
	</tr>
	<tr valign="top">
	 <td colspan="3" align="right"><p><input class="Submit" type="submit" value="Add to Holiday List"></p></td>
	</tr>  
	</form>

<?


   $resultmember = mysql_query("SELECT holiday_id, HolidayName, HolidayDate, HolidayGreetings FROM tbl_".$user."_holidays order by holiday_id desc") or die (mysql_error());

   while ( $row = mysql_fetch_array($resultmember) ) {

			$holiday_id = $row["holiday_id"];
			$HolidayName = $row["HolidayName"];
			$HolidayDate = $row["HolidayDate"];
			$HolidayGreetings = $row["HolidayGreetings"];

?>

	<form action="holidays.php?action=update" method="post">
	<input name="holiday_id" type="hidden" class="Text" value="<?=$holiday_id?>">
	<tr valign="top">
	 <td><p class="Contact">Holiday Name</p></td>
	 <td><p class="Contact">:</p></td>
	 <td><p><input class="Text" type="text" name="HolidayName" size="40" maxlength="100" value="<?=$HolidayName?>"></p></td>
	</tr>
	<tr valign="top">
	 <td><p class="Contact">Holiday Date (YYYY-MM-DD)</p></td>
	 <td><p class="Contact">:</p></td>
	 <td><p><input class="Text" type="text" name="HolidayDate" size="40" maxlength="100" value="<?=$HolidayDate?>"></p></td>
	</tr>
	<tr valign="top">
	 <td><p class="Contact">Greetings</p></td>
	 <td><p class="Contact">:</p></td>
	 <td><p class="Text"><textarea name="HolidayGreetings" cols="80" rows="8"><?=$HolidayGreetings?></textarea></p></td>
	</tr>
  <tr valign="top">
	 <td colspan="3" align="right">
	  <table>
	   <tr>
	    <td><input class="Submit" type="submit" value="Update Holiday List"></form></td>
	    <td><form action="holidays.php?action=delete&deleteid=<?=$holiday_id?>" method="post"><input class="Submit" type="submit" value="DELETE"></form></td>
	   </tr>
	  </table>
	 </td>
	</tr> 

<?
		}
?>

</table>

<?
}


















elseif ($action == "add")
{

$resultx = mysql_query("insert into tbl_".$user."_holidays (HolidayName, HolidayDate, HolidayGreetings) values('$HolidayName', '$HolidayDate', '$HolidayGreetings')") or die (mysql_error());

?>

<table cellpadding="2" cellspacing="0" border="0">
	<form action="holidays.php?action=add" method="post">
	<tr valign="top">
	 <td><p class="Contact">Holiday Name</p></td>
	 <td><p class="Contact">:</p></td>
	 <td><p><input class="Text" type="text" name="HolidayName"  value="is what you say, e.g. Merry Christmas" size="40" maxlength="100" ></p></td>
	</tr>
	<tr valign="top">
	 <td><p class="Contact">Holiday Date (YYYY-MM-DD)</p></td>
	 <td><p class="Contact">:</p></td>
	 <td><p><input class="Text" type="text" name="HolidayDate" size="40" maxlength="100" ></p></td>
	</tr>
	<tr valign="top">
	 <td><p class="Contact">Greetings</p></td>
	 <td><p class="Contact">:</p></td>
	 <td><textarea name="HolidayGreetings" cols="80" rows="8" class="TextArea">This message is going to go to all your contacts, so keep the message simple and generic, no names, dates, him's and her's ...</textarea></td>
	</tr>
	<tr valign="top">
	 <td colspan="3" align="right"><p><input class="Submit" type="submit" value="Add to Holiday List"></p></td>
	</tr>  
	</form>

<?


$resultmember = mysql_query("SELECT holiday_id, HolidayName, HolidayDate, HolidayGreetings FROM tbl_".$user."_holidays order by holiday_id desc") or die (mysql_error());

   while ( $row = mysql_fetch_array($resultmember) ) {

			$holiday_id = $row["holiday_id"];
			$HolidayName = $row["HolidayName"];
			$HolidayDate = $row["HolidayDate"];
			$HolidayGreetings = $row["HolidayGreetings"];


?>

	<form action="holidays.php?action=update" method="post">
	<input name="holiday_id" type="hidden" class="Text" value="<?=$holiday_id?>">
	<tr valign="top">
	 <td><p class="Contact">Holiday Name</p></td>
	 <td><p class="Contact">:</p></td>
	 <td><p><input class="Text" type="text" name="HolidayName" size="40" maxlength="100" value="<?=$HolidayName?>"></p></td>
	</tr>
	<tr valign="top">
	 <td><p class="Contact">Holiday Date (YYYY-MM-DD)</p></td>
	 <td><p class="Contact">:</p></td>
	 <td><p><input class="Text" type="text" name="HolidayDate" size="40" maxlength="100" value="<?=$HolidayDate?>"></p></td>
	</tr>
	<tr valign="top">
	 <td><p class="Contact">Greetings</p></td>
	 <td><p class="Contact">:</p></td>
	 <td><p class="Text"><textarea name="HolidayGreetings" cols="80" rows="8"><?=$HolidayGreetings?></textarea></p></td>
	</tr>
  <tr valign="top">
	 <td colspan="3" align="right">
	  <table>
	   <tr>
	    <td><input class="Submit" type="submit" value="Update Holiday List"></form></td>
	    <td><form action="holidays.php?action=delete&deleteid=<?=$holiday_id?>" method="post"><input class="Submit" type="submit" value="DELETE"></form></td>
	   </tr>
	  </table>
	 </td>
	</tr> 
	
<?
				}
?>

</table>

<?
}


elseif ($action == "update")
{

//echo "update tbl_".$user."_holidays set HolidayName = '$HolidayName[i]', HolidayDate = '$HolidayDate[i]', HolidayGreetings = '$HolidayGreetings[i]' where holiday_id = $holiday_id[i]";
$resultx = mysql_query("update tbl_".$user."_holidays set HolidayName = '$HolidayName', HolidayDate = '$HolidayDate', HolidayGreetings = '$HolidayGreetings' where holiday_id = $holiday_id") or die (mysql_error());	


?>

<table cellpadding="2" cellspacing="0" border="0">
	<form action="holidays.php?action=add" method="post">
	<tr valign="top">
	 <td><p class="Contact">Holiday Name</p></td>
	 <td><p class="Contact">:</p></td>
	 <td><p><input class="Text" type="text" name="HolidayName"  value="is what you say, e.g. Merry Christmas" size="40" maxlength="100" ></p></td>
	</tr>
	<tr valign="top">
	 <td><p class="Contact">Holiday Date (YYYY-MM-DD)</p></td>
	 <td><p class="Contact">:</p></td>
	 <td><p><input class="Text" type="text" name="HolidayDate" size="40" maxlength="100" ></p></td>
	</tr>
	<tr valign="top">
	 <td><p class="Contact">Greetings</p></td>
	 <td><p class="Contact">:</p></td>
	 <td><textarea name="HolidayGreetings" cols="80" rows="8" class="TextArea">This message is going to go to all your contacts, so keep the message simple and generic, no names, dates, him's and her's ...</textarea></td>
	</tr>
	<tr valign="top">
	 <td colspan="3" align="right"><p><input class="Submit" type="submit" value="Add to Holiday List"></p></td>
	</tr>  
	</form>

<?


$resultmember = mysql_query("SELECT holiday_id, HolidayName, HolidayDate, HolidayGreetings FROM tbl_".$user."_holidays order by holiday_id desc") or die (mysql_error());

   while ( $row = mysql_fetch_array($resultmember) ) {

			$holiday_id = $row["holiday_id"];
			$HolidayName = $row["HolidayName"];
			$HolidayDate = $row["HolidayDate"];
			$HolidayGreetings = $row["HolidayGreetings"];


?>

	<form action="holidays.php?action=update" method="post">
	<input name="holiday_id" type="hidden" class="Text" value="<?=$holiday_id?>">
	<tr valign="top">
	 <td><p class="Contact">Holiday Name</p></td>
	 <td><p class="Contact">:</p></td>
	 <td><p><input class="Text" type="text" name="HolidayName" size="40" maxlength="100" value="<?=$HolidayName?>"></p></td>
	</tr>
	<tr valign="top">
	 <td><p class="Contact">Holiday Date (YYYY-MM-DD)</p></td>
	 <td><p class="Contact">:</p></td>
	 <td><p><input class="Text" type="text" name="HolidayDate" size="40" maxlength="100" value="<?=$HolidayDate?>"></p></td>
	</tr>
	<tr valign="top">
	 <td><p class="Contact">Greetings</p></td>
	 <td><p class="Contact">:</p></td>
	 <td><p class="Text"><textarea name="HolidayGreetings" cols="80" rows="8"><?=$HolidayGreetings?></textarea></p></td>
	</tr>
  <tr valign="top">
	 <td colspan="3" align="right">
	  <table>
	   <tr>
	    <td><input class="Submit" type="submit" value="Update Holiday List"></form></td>
	    <td><form action="holidays.php?action=delete&deleteid=<?=$holiday_id?>" method="post"><input class="Submit" type="submit" value="DELETE"></form></td>
	   </tr>
	  </table>
	 </td>
	</tr> 
	
<?
				}
?>

</table>

<?
}





else {

echo "else condition ...";

}

?>



<div align="center">
<?php include('../includes/global.htm'); ?>
</div>





</center>
	</BODY>

	</HTML>

