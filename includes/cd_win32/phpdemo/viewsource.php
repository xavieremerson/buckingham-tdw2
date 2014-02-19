<html>
<body>
<?php

$myFile = isset($_GET) ? $_GET["file"] : $HTTP_GET_VARS["file"];
print("<p style='font-size:14pt; font-family:verdana; font-weight:bold'>$myFile</p>\n");
print("<p style='font-size:10pt; font-family:verdana;'><a href='javascript:history.go(-1);'>Back to Chart Graphics</a></p>\n");
print("<xmp>\n");
readfile(dirname(__FILE__)."/".basename($myFile));
print("</xmp>");

?>
</body>
</html>
