<html>
<body topmargin="0" leftmargin="0" rightmargin="0" marginwidth="0" marginheight="0">
<div style="margin:5;">
<div style="font-family:verdana; font-weight:bold; font-size:18pt;">
ChartDirector Information
</div>
<hr color="#000080">
<div style="font-family:verdana; font-size:10pt;">
<?php
require_once("../lib/phpchartdir.php");
?>
<ul style="margin-top:0; list-style:square; font-family:verdana; font-size:10pt;">
<li>Description : <?php echo getDescription() ?><br><br>
<li>Version : <?php echo (getVersion() & 0x7f000000) / 0x1000000 ?>.<?php echo (getVersion() & 0xff0000) / 0x10000 ?>.<?php echo getVersion() & 0xffff ?><br><br>
<li>Copyright : <?php echo getCopyright() ?><br><br>
<li>Boot Log : <br><ul><li><?php echo str_replace("\n", "<li>", getBootLog()) ?></ul><br>
<li>Font Loading Test : <br><ul><li><?php echo str_replace("\n", "<li>", libgTTFTest())?></ul>
</ul>
</div>
</body>
</html>
