<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<link        rel="stylesheet" href="progressbar.css" type="text/css" />
	<script type="text/javascript" src="progressbar.js"></script>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<?
if ($_POST) {
sleep(4);
echo "value = ".$_POST["xval"];
}
?>
<form action="index.php" method="post">
<input type="text" name="xval" value="test" size="10">
<input type="submit" name="Submit" value="Submit" onClick="showProgressBar('progressbar.html', 230, 24, null);">
</form>
</body>
</html>
