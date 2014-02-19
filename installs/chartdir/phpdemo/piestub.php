
<html>
<body>
<h1>Simple Clickable Pie Chart Handler</h1>
<p><a href="viewsource.php?file=<?php echo $HTTP_SERVER_VARS["SCRIPT_NAME"]?>">
View Source Code
</a></p>

<p><b>You have clicked on the following sector :</b></p>
<ul>
    <li>Sector Number : <?php echo $HTTP_GET_VARS["sector"]?></li>
    <li>Sector Name : <?php echo $HTTP_GET_VARS["label"]?></li>
    <li>Sector Value : <?php echo $HTTP_GET_VARS["value"]?></li>
    <li>Sector Percentage : <?php echo $HTTP_GET_VARS["percent"]?>%</li>
</ul>
</body>
</html>
