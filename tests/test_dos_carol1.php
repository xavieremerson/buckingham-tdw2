<?
include('../includes/global.php');
include('../includes/dbconnect.php');
include('../includes/functions.php');

mysql_query("INSERT INTO carol_test (field1, field2, field3) VALUES ('$val1', '$val2', '$val3')") or die(mysql_error());

show_array($_GET);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
</head>

<body>
edfgdfgdfgdf
</body>
</html>
