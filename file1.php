<html>
<head>
<style type="text/css"><!--

div {
 margin: 1px;
 height: 20px;
 padding: 1px;
 border: 1px solid #000;
 width: 275px;
 background: #fff;
 color: #000;
 float: left;
 clear: right;
 top: 38px;
 z-index: 9
}

.percents {
 background: #FFF;
 border: 1px solid #CCC;
 margin: 1px;
 height: 20px;
 position:absolute;
 width:275px;
 z-index:10;
 left: 10px;
 top: 38px;
 text-align: center;
}

.blocks {
 background: #EEE;
 border: 1px solid #CCC;
 margin: 1px;
 height: 20px;
 width: 10px;
 position: absolute;
 z-index:11;
 left: 12px;
 top: 38px;
 filter: alpha(opacity=50);
 -moz-opacity: 0.5;
 opacity: 0.5;
 -khtml-opacity: .5
}

-->
</style>
</head>
<body>

<?php

if (ob_get_level() == 0) {
   ob_start();
}
echo str_pad('Loading... ',4096)."<br />\n";
for ($i = 0; $i < 25; $i++) {
   $d = $d + 11;
   $m=$d+10;
   //This div will show loading percents
   echo '<div class="percents">' . $i*4 . '%&nbsp;complete</div>';
   //This div will show progress bar
   echo '<div class="blocks" style="left: '.$d.'px">&nbsp;</div>';
   flush();
   ob_flush();
   sleep(1);
}
ob_end_flush();
?>
<div class="percents" style="z-index:12">Done.</div>
</body>
</html> 