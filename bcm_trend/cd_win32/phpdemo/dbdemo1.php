<?php
require_once("../lib/phpchartdir.php");

# The currently selected year
if (isset($_GET["year"]))
    $selectedYear = $_GET["year"];
else
    $selectedYear = 2001;

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
    Database Integration Demo (1)
</div>
<hr color="#000080">
<div style="font-size:10pt; font-family:verdana; margin-bottom:20px">
&#8226; <a href="viewsource.php?file=<?php echo $_SERVER["SCRIPT_NAME"]?>">
    View containing HTML page source code
</a>
<br>
&#8226; <a href="viewsource.php?file=dbdemo1a.php">
    View chart generation page source code
</a>
<br>
<br>
<form>
    I want to obtain the revenue data for the year
    <select name="year">
        <?php echo $selectYearOptions?>
    </select>
    <input type="submit" value="OK">
</form>
</div>

<img src="dbdemo1a.php?year=<?php echo $selectedYear?>">

</body>
</html>
