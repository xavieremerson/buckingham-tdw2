<html>
<body>
<h1>Database Integration Demo (2)</h1>
<p style="width:500px;">This example demonstrates creating a chart using 
data from a database. The database query is performed in the containing
HTML page. The data are then passed to the chart generation pages as 
HTTP GET parameters.</p>

<ul>
	<li><a href="viewsource.php?file=<?php echo $HTTP_SERVER_VARS["SCRIPT_NAME"] ?>">
		View containing HTML page source code
	</a></li>
	<li><a href="viewsource.php?file=dbdemo2a.php">
		View chart generation page source code for upper chart
	</a></li>
	<li><a href="viewsource.php?file=dbdemo2b.php">
		View chart generation page source code for lower chart
	</a></li>
</ul>

<form action="<?php echo $HTTP_SERVER_VARS["SCRIPT_NAME"] ?>">
	I want to obtain the revenue data for the year 
	<select name="year">
		<option value="1990">1990
		<option value="1991">1991
		<option value="1992">1992
		<option value="1993">1993
		<option value="1994">1994
		<option value="1995">1995
		<option value="1996">1996
		<option value="1997">1997
		<option value="1998">1998
		<option value="1999">1999
		<option value="2000">2000
		<option value="2001">2001
	</select>
	<input type="submit" value="OK">
</form>

<?
#
#Perform the database query to get the required data. The selected year
#should be passed in as a query parameter called "year"
#
if (isset($HTTP_GET_VARS["year"]))
	$SelectedYear = $HTTP_GET_VARS["year"];
else
	$SelectedYear = 2001;

#
#Create an SQL statement to get the revenues of each month for the
#selected year.
#
$SQLstatement = "Select Month(TimeStamp) - 1, Software, Hardware, Services " .
      "From revenue Where Year(TimeStamp)=" . $SelectedYear;

#
#Read in the revenue data into arrays
#
mysql_connect("localhost", "test", "test");
$result = mysql_db_query("sample", $SQLstatement);
$software = array_pad(array(), 12, 0);
$hardware = array_pad(array(), 12, 0);
$services = array_pad(array(), 12, 0);
while ($row = mysql_fetch_row($result)) {
	$software[$row[0]] = $row[1];
	$hardware[$row[0]] = $row[2];
	$services[$row[0]] = $row[3];
}

#Serialize the data into a string to be used as HTTP query parameters
$httpParam = "year=" . $SelectedYear . "&software=".join(",", $software) .
   "&hardware=".join(",", $hardware) . "&services=".join(",", $services);
?>

<SCRIPT>
	//make sure the select box displays the current selected year.
	document.forms[0].year.selectedIndex = <?=$SelectedYear - 1990?>;
</SCRIPT>

<img src="dbdemo2a.php?<?php echo $httpParam ?>"><br>
<img src="dbdemo2b.php?<?php echo $httpParam ?>">

</body>
</html>
