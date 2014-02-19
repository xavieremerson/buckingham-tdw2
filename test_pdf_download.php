<?
include('includes/functions.php');
include('includes/global.php');
include('includes/dbconnect.php');
?>
<?php
if(isset($_GET['id'])) 
{
// if id is set then get the file with the id from database

$id    = $_GET['id'];
$query = "SELECT name, type, size, content " .
         "FROM a_upload WHERE id = '$id'";

$result = mysql_query($query) or die('Error, query failed');
list($name, $type, $size, $content) = mysql_fetch_array($result);

header("Content-length: $size");
header("Content-type: $type");
header("Content-Disposition: attachment; filename=$name");
echo $content;

exit;
}

?>

<html>
<head>
<title>Download File From MySQL</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<?php

$query = "SELECT id, name FROM a_upload";
$result = mysql_query($query) or die('Error, query failed');
if(mysql_num_rows($result) == 0)
{
echo "Database is empty <br>";
} 
else
{
while(list($id, $name) = mysql_fetch_array($result))
{
?>
<a href="test_pdf_download.php?id=<?=$id;?>"><?=$name;?></a> <br>
<?php 
}
}
?>






</body>
</html>
