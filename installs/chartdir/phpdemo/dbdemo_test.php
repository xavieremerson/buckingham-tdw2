<?php
#
#Create an samoke SQL statement to test the database
#
$SQLstatement = "Select Month(TimeStamp) - 1, Software, Hardware, Services From revenue Where Year(TimeStamp)=2001";

#
#Connect to the database and execute the SQL statement
#
do {
	if (!($status = mysql_connect("localhost", "test", "test"))) break;
	if (!($status = mysql_select_db("sample"))) break;
	if (!($status = mysql_query($SQLstatement))) break;
} while (0);
if (!$status) {
	print "<br><br><h2>Error accessing sample database</h2>";
	if (mysql_errno()) print "Error code = ".mysql_errno()." : ".mysql_error()."<br><br>";
	exit();
}

#
#Read in the revenue data into arrays
#
$software = array_pad(array(), 12, 0);
$hardware = array_pad(array(), 12, 0);
$services = array_pad(array(), 12, 0);
while ($row = mysql_fetch_row($status)) {
	$software[$row[0]] = $row[1];
	$hardware[$row[0]] = $row[2];
	$services[$row[0]] = $row[3];
}
?>
<html>
<body>
<p><b>Database connection test successful</b></p>
</body>
</html>
