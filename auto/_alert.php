<?
$fd = fopen ("http://www.centersys.com/alert.php?subject=".$_GET["subject"]."&message=".$_GET["message"], "r");
fclose ($fd);
echo "ALERT SENT <br>";
?>