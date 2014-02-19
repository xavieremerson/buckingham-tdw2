<?php
#
#Create an samoke SQL statement to test the database
#
$SQLstatement = "Select Software, Hardware, Services From revenue Where Year(TimeStamp)=2001 Order By TimeStamp";

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
while ($row = mysql_fetch_row($status)) {
	$software[] = $row[0];
	$hardware[] = $row[1];
	$services[] = $row[2];
}
?>
<html>
<body>
<p><b>Database connection test successful</b></p>
</body>
</html>
