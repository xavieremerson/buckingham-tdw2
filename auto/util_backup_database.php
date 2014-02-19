<?php
/*
#####################################################################
#                      MySQL Backup Utility Pro                     #
#                           Version 2.0.1                           #
#                 ©2002 0php.com - Free PHP Scripts                 #
#####################################################################

#####################################################################
#                                                                   #
#  Author      :  Mike Miller                                       #
#  Date        :  July 12, 2002                                     #
#  E-mail      :  webmaster@0php.com                                #
#  Website     :  http://www.0php.com/                              #
#  License     :  FREE (GPL);  See Copyright and Terms below        #
#                                                                   #
#        Donations accepted via PayPal to webmaster@0php.com        #
#                                                                   #
#####################################################################

>> Summary:
  PHP script to make SQL commands to delete, insert, and modify MySQL database table entries.

>> Useful for:
  (1) when you update your database offline and import the changes to online database
      (especially incremental backups), or
  (2) when you update your database online and want a SQL backup of the changes

>> Installation:
  (1) Optional but recommended: edit the default values below this documentation.
      (the file can be renamed if you want).
  (2) Copy the file to your server and run it.

>> How to use:
  Keep a list of the key values of records deleted and modified.
  Enter the list into the Values box of the form and click button.
  Copy the SQL commands generated.

>> Requirements: PHP 4 >= 4.0b4; MySQL

>> Copyright and Terms:

This software is copyright (C) 2002 Mike Miller.  It is distributed
under the terms of the GNU General Public License (GPL).  Because it is licensed
free of charge, there is NO WARRANTY, it is provided AS IS.  The author can not
be held liable for any damage that might arise from the use of this software.
Use it at your own risk.

All copyright notices and links to 0PHP.com website MUST remain intact in the scripts and in the HTML for the scripts.

For more details, see http://www.0php.com/license_GNU_GPL.php (or http://www.gnu.org/).


###########################################################################################
###########################################################################################
*/



### Can modify the default values below - optional but recommended ########################

$auth=1;						# User authentication?: if it is NOT set to =1, then
							#  it asks for username/password; set =0 to enable.
							#  if disabled (=1), then .htaccess is recommended
$auth_name="admin";				# User name for authorization to this script
$p_word="thepassword7";				# Password for authorization to this script

$strHost_Default     = "localhost";		# MySQL host name
$strDatabase_Default = "db-name";		# MySQL database name
$strUid_Default      = "me";			# MySQL User ID
$strPwd_Default      = "me";			# MySQL Password

$strTable_Default     = "table1";		# MySQL table name to be modified
$strField_Default     = "field1";		# MySQL field name to compare the values in
$strSeparator_Default = "\r\n";		# separator for values:  \r\n = return;   \t = tab

$strOrderby = " ORDER BY field1";		# Field Order for returned results
							# For example, " ORDER BY Field_Name DESC"

$strE_Default = "=";				# Comparison Expression:  LIKE,=,>,<,<>,<=,>=
$strA_Default = "DELETE";			# Action:  DELETE, INSERT, REPLACE


### No need to modify below here ##########################################################


if($auth!=1)	# HTTP Authentication (1 = no; anything else = yes)
{
	if (!isset($PHP_AUTH_USER))
	{
		header('WWW-Authenticate: Basic realm="MySQL Backup Utility Pro"');
		header('HTTP/1.0 401 Unauthorized');
		echo 'Authorization Required.';
		exit;
	}
	else
	{
		if (($PHP_AUTH_USER != $auth_name) || ($PHP_AUTH_PW != $p_word))
		{
			header('WWW-Authenticate: Basic realm="MySQL Backup Utility Pro"');
			header('HTTP/1.0 401 Unauthorized');
			echo 'Authorization Required.';
			exit;
		}
	}
}


$Version="2.0.1";
$QUOTE=chr(34);
error_reporting(0);  # remove # to see error messages

$strSeparator_Default=addcslashes($strSeparator_Default,"\0..\37");

# remember last values used for form if any
if (isset($db))    $strDatabase=$db; else $strDatabase=$strDatabase_Default;
if (isset($Table)) $strTable=$Table; else $strTable=$strTable_Default;
if (isset($Field)) $strField=$Field; else $strField=$strField_Default;
if (isset($Uid))   $strUid=$Uid;     else $strUid=$strUid_Default;
if (isset($Pwd))   $strPwd=$Pwd;     else $strPwd=$strPwd_Default;
if (isset($Host))  $strHost=$Host;   else $strHost=$strHost_Default;
if (isset($E))     $strE=$E;         else $strE=$strE_Default;
if (isset($A))     $strA=$A;         else $strA=$strA_Default;

if (isset($Separator))
{
	if ($v == "\\\\t") $strSeparator=stripslashes($Separator);
	else $strSeparator=$Separator;
}
else $strSeparator=$strSeparator_Default;


if (isset($Default)) if ($Default=="Default")
{
	$strDatabase=$strDatabase_Default;
	$strTable=$strTable_Default;
	$strField=$strField_Default;
	$strUid=$strUid_Default;
	$strPwd=$strPwd_Default;
	$strHost=$strHost_Default;
	$strE=$strE_Default;
	$strA=$strA_Default;
	$strSeparator=$strSeparator_Default;
      $Values="";
}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML><HEAD>
<TITLE>MySQL Backup Utility Pro <?php echo $Version?></TITLE>
<META NAME="description" CONTENT="PHP script utility to make SQL commands to backup, delete, insert, and modify/replace MySQL database table entries">
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1252">
<META HTTP-EQUIV="Content-Language" CONTENT="en-us">
<meta http-equiv="Content-Style-Type" content="text/css">
<style type="text/css">
<!--
A:Hover { text-decoration: underline; color: #DC2B19; }
-->
</style>
</HEAD>

<BODY BGCOLOR="#C0C0C0" ALINK="#000080" LINK="#000080" VLINK="#000080">
<div align="center">

<?php
if (isset($Values)) if ($Values !="")  # any values to process?
{
  $sql_text=$strErrors="";
  $intValues=$intErrors=$intFound=0;

  if ($v == "\\\\t") $Separator=stripslashes($Separator);
  $Separator=stripcslashes($Separator);

  $n_array = explode($Separator,$Values);
  while ($x = each($n_array))
  {
	$value_line = trim($x["value"]);
	if ($value_line!="")
      {
      $intValues++;
      if ($A=="DELETE")
        $sql_text .= "DELETE FROM $Table WHERE $Field $E '$value_line';<br>\n";

      else        #replace/insert
		{


# get record for $value_line
$strWhere = "$Field $E $QUOTE$value_line$QUOTE";
$strsql="SELECT * FROM ".$Table." WHERE $strWhere$strOrderby";
$xConn=mysql_connect($Host,$Uid,$Pwd);
mysql_select_db($db,$xConn);
if (!($result = mysql_query($strsql))) { echo "<br>Open Database Failed<br>"; break; }
$intTotalRecs=mysql_num_rows($result);
$intFieldCount= mysql_num_fields($result);

# build $sql_text for $value_line

$Error="y";
while(($row=mysql_fetch_row($result)))
  {
  $intFound++; $Error="n";
  $sql_text .= "$A INTO $Table VALUES ( ";
  $data="";
  for ($x=0; $x<=$intFieldCount-1; $x++)    $data .= "'$row[$x]', ";
  if (strlen($data)>=2) { $data=substr($data,0,strlen($data)-2); }
  $data=addcslashes($data,"\0..\37");
  $sql_text .=$data;
  $sql_text .= ");<br>\n";
  }
$E_Separator=nl2br($Separator);
if($Error=="y") {$intErrors++; $strErrors.="$value_line$E_Separator";}

mysql_close();
      	}
	}
  }


# print SQL commands and results
  if ($strErrors!="") $strErrors=substr($strErrors,0,strlen($strErrors)-strlen($E_Separator));
  echo "<table border=\"1\" cellpadding=\"20\" cellspacing=\"0\" width=\"80%\" bgcolor=\"#FFFFFF\"><tr><td>\n";
  echo "<font face=\"Arial\">$sql_text<br><br>
  <b>Values Entered: $intValues</b><br>";
  if ($A!="DELETE") echo "<b>Records Found: $intFound</b><br>\n";
  if ($intErrors>0) echo "<font color=\"#DC2B19\"><b>Errors - Values Not Found: $intErrors<br><blockquote>$strErrors</blockquote></b></font>\n";
  echo "</font>\n";
  echo "</td></tr></table><br>\n";
}





?>

<table border=1 cellpadding=20 cellspacing=0 width="80%" bgcolor="#FFFFFF">

<tr><td width="100%" valign="top" align="left">
<h2 align="center"><b><font face="Arial" color="#008FE0">MySQL Backup Utility Pro <?php echo $Version?></font></b></h2>

<div align="center"><center>


<table border=0 cellpadding=0 cellspacing=0>
<tr><td>

<form action="<?php echo $REDIRECT_URL?>" method=get>
<table border=0 cellpadding=0 cellspacing=0 width="100%"><tr>

<td width="17%" valign="top"><b><font face="Arial">Database:</font></b>
<input name="v" type="hidden" value="\t">
</td>

<td width="16%" valign="top">
<font face="Arial"><input type="text" name="db" size=8 value="<?php echo $strDatabase?>"></font></td>

<td width="17%" valign="top"><b><font face="Arial">Host:</font></b>
</td>

<td width="16%" valign="top"><font face="Arial"><input type="text" name="Host" size=8 value="<?php echo $strHost?>"></font></td>

<td width="17%" valign="top"><b><font face="Arial">Action:</font></b> </td>

<td width="17%" valign="top"><b><font face="Arial"><select size=1 name="A">
  <option value="DELETE">Choose one</option>
  <option <?php if($strA=="DELETE")       echo "selected";?> value="DELETE">Delete</option>
  <option <?php if($strA=="INSERT")       echo "selected";?> value="INSERT">Insert</option>
  <option <?php if($strA=="REPLACE")      echo "selected";?> value="REPLACE">Replace</option>
</select></font></b> </td></tr>

<tr><td width="17%" valign="top"><font face="Arial"><b>Table:</b></font></td>

<td width="16%" valign="top"><font face="Arial"><input type="text" name="Table" size=8 value="<?php echo $strTable?>"></font></td>

<td width="17%" valign="top"><b><font face="Arial">UserName:</font></b>
</td>

<td width="16%" valign="top"><font face="Arial"><input type="text" name="Uid" size=8 value="<?php echo $strUid?>"></font></td>

<td width="17%" valign="top"><b><font face="Arial">Expression:</font></b></td>

<td width="17%" valign="top"><b><font face="Arial"><select size=1 name="E">
  <option value="LIKE">Choose one</option>
  <option <?php if($strE=="=")          echo "selected";?> value="="   >=</option>
  <option <?php if($strE=="LIKE")       echo "selected";?> value="LIKE">LIKE</option>
  <option <?php if($strE==">")          echo "selected";?> value=">"   >&gt;</option>
  <option <?php if($strE=="<")          echo "selected";?> value="<"   >&lt;</option>
  <option <?php if($strE=="<>")         echo "selected";?> value="<>"  >&lt;&gt;</option>
  <option <?php if($strE==">=")         echo "selected";?> value=">="  >&gt;=</option>
  <option <?php if($strE=="<=")         echo "selected";?> value="<="  >&lt;=</option>
</select></font></b> </td></tr>

<tr><td width="17%" valign="top">
<font face="Arial"><b>Field:</b> </font></td>

<td width="16%" valign="top">
<font face="Arial"><input type="text" name="Field" size=8 value="<?php echo $strField?>"></font></td>

<td width="17%" valign="top"><b><font face="Arial">Password:</font></b> </td>

<td width="16%" valign="top"><font face="Arial"><input type="password" name="Pwd" size=8 value="<?php echo $strPwd?>"></font></td>

<td width="17%" valign="top"></td>

<td width="17%" valign="top"></td></tr>
</table>

<p><b><font face="Arial">Values separated by:</font></b> <font face="Arial">
<input type="text" name="Separator" size=10 value="<?php echo $strSeparator?>"> ( \r\n = new line ; \t = tab )</font></p>

<p><font face="Arial"><b>Values:</b> &nbsp; ( LIKE Wildcards: _=1 character; &nbsp; %=any characters)</font><br>
<textarea rows=7 name="Values" cols=60></textarea></p>
<p align="center"><font face="Arial">
<input type="submit" value="Create SQL command" name="Submit"> 
<input type="reset" value="Reset" name="Reset"> 
<input type="submit" value="Default" name="Default">
</font></p>
</form>

</td></tr>
</table></center></div>
  
</td></tr>
</table></div><br>

<p align="center"><font face="Arial" size=1>MySQL Backup Utility Pro - Version <?php echo $Version;?><br>
<a href="http://www.0php.com">Free PHP Scripts</a> - Copyright © <a href="http://www.0php.com">0php.com</a> 2002.</font>

</BODY></HTML>
