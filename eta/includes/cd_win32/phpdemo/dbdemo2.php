<?php
require_once("../lib/phpchartdir.php");

# The currently selected year
if (isset($_GET["year"]))
    $selectedYear = $_GET["year"];
else
    $selectedYear = 2001;

# SQL statement to get the monthly revenues for the selected year.
$SQL =
    "Select Software, Hardware, Services From revenue Where Year(TimeStamp) = ".
    "$selectedYear Order By TimeStamp";

#
# Connect to database and read the query result into arrays
#

mysql_connect("localhost", "test", "test");
mysql_select_db("sample");
$result = mysql_query($SQL);

while ($row = mysql_fetch_row($result)) {
    $software[] = $row[0];
    $hardware[] = $row[1];
    $services[] = $row[2];
}

# Serialize the data into a string to be used as HTTP query parameters
$httpParam = sprintf("year=%s&software=%s&hardware=%s&services=%s", $selectedYear,
    join(",", $software), join(",", $hardware), join(",", $services));

#
# The following code generates the <option> tags for the HTML select box, with the
# <option> tag representing the currently selected year marked as selected.
#

$optionTags = array_pad(array(), 2001 - 1990 + 1, null);
for($i = 1990; $i < 2001 + 1; ++$i) {
    if ($i == $selectedYear) {
        $optionTags[$i - 1990] = "<option value='$i' selected>$i</option>";
    } else {
        $optionTags[$i - 1990] = "<option value='$i'>$i</option>";
    }
}
$selectYearOptions = join("", $optionTags);
?>
<html>
<body topmargin="5" leftmargin="5" rightmargin="0" marginwidth="5" marginheight="5">
<div style="font-size:18pt; font-family:verdana; font-weight:bold">
    Database Integration Demo (2)
</div>
<hr color="#000080">
<div style="font-size:10pt; font-family:verdana; width:600px">
This example demonstrates creating a chart using data from a database. The database
query is performed in the containing HTML page. The data are then passed to the chart
generation pages as HTTP GET parameters.
<ul>
    <li><a href="viewsource.php?file=<?php echo $_SERVER["SCRIPT_NAME"]?>">
        View containing HTML page source code
    </a></li>
    <li><a href="viewsource.php?file=dbdemo2a.php">
        View chart generation page source code for upper chart
    </a></li>
    <li><a href="viewsource.php?file=dbdemo2b.php">
        View chart generation page source code for lower chart
    </a></li>
</ul>
<form>
    I want to obtain the revenue data for the year
    <select name="year">
        <?php echo $selectYearOptions?>
    </select>
    <input type="submit" value="OK">
</form>
</div>

<img src="dbdemo2a.php?<?php echo $httpParam?>">
<br><br>
<img src="dbdemo2b.php?<?php echo $httpParam?>">

</body>
</html>
