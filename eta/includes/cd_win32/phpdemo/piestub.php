<html>
<body topmargin="5" leftmargin="5" rightmargin="0" marginwidth="5" marginheight="5">
<div style="font-size:18pt; font-family:verdana; font-weight:bold">
    Simple Clickable Pie Chart Handler
</div>
<hr color="#000080">
<div style="font-size:10pt; font-family:verdana; margin-bottom:20">
    <a href="viewsource.php?file=<?php echo $_SERVER["SCRIPT_NAME"]?>">View Source Code</a>
</div>
<div style="font-size:10pt; font-family:verdana;">
<b>You have clicked on the following sector :</b><br>
<ul>
    <li>Sector Number : <?php echo $_REQUEST["sector"]?></li>
    <li>Sector Name : <?php echo $_REQUEST["label"]?></li>
    <li>Sector Value : <?php echo $_REQUEST["value"]?></li>
    <li>Sector Percentage : <?php echo $_REQUEST["percent"]?>%</li>
</ul>
</body>
</html>
