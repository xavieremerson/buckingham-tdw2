<?php
$mbox = imap_open("{owa.smarshexchange.com}", "tdw@buckresearch.com", "BRmail678", OP_HALFOPEN)
      or die("can't connect: " . imap_last_error());

$list = imap_getmailboxes($mbox, "{owa.smarshexchange.com}", "*");
if (is_array($list)) {
    foreach ($list as $key => $val) {
        echo "($key) ";
        echo imap_utf7_decode($val->name) . ",";
        echo "'" . $val->delimiter . "',";
        echo $val->attributes . "<br />\n";
    }
} else {
    echo "imap_getmailboxes failed: " . imap_last_error() . "\n";
}

imap_close($mbox);
?> 
