<html>
<body topmargin="5" leftmargin="5" rightmargin="0" marginwidth="5" marginheight="5">
<div style="font-size:18pt; font-family:verdana; font-weight:bold">
    Simple Clickable XY Chart Handler
</div>
<hr color="#000080">
<div style="font-size:10pt; font-family:verdana; margin-bottom:20">
    <a href="viewsource.php?file=<?php echo $_SERVER["SCRIPT_NAME"]?>">View Source Code</a>
</div>
<div style="font-size:10pt; font-family:verdana;">
<b>You have clicked on the following chart element :</b><br>
<ul>
    <li>Data Set : <?php echo $_REQUEST["dataSetName"]?></li>
    <li>X Position : <?php echo $_REQUEST["x"]?></li>
    <li>X Label : <?php echo $_REQUEST["xLabel"]?></li>
    <li>Data Value : <?php echo $_REQUEST["value"]?></li>
</ul>
</body>
</html>
