<html>
<body>
<h1>Database Integration Demo (1)</h1>
<p>The example demonstrates creating a chart using data from a database.</p>

<ul>
	<li><a href="viewsource.php?file=<?php echo $HTTP_SERVER_VARS["SCRIPT_NAME"] ?>">
		View containing HTML page source code
	</a></li>
	<li><a href="viewsource.php?file=dbdemo1a.php">
		View chart generation page source code
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

<?php
if (isset($HTTP_GET_VARS["year"]))
	$SelectedYear = $HTTP_GET_VARS["year"];
else
	$SelectedYear = 2001;
?>

<SCRIPT>
	//make sure the select box displays the current selected year.
	document.forms[0].year.selectedIndex = <?=$SelectedYear - 1990?>;
</SCRIPT>

<img src="dbdemo1a.php?year=<?php echo $SelectedYear ?>">

</body>
</html>
