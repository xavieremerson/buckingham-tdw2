<?php
require_once("../lib/phpchartdir.php");

# The values to display on the meter
$value0 = 30.99;
$value1 = 45.35;
$value2 = 77.64;

# Create an LinearMeter object of size 60 x 245 pixels, using silver background with
# a 2 pixel black 3D depressed border.
$m = new LinearMeter(60, 245, silverColor(), 0, -2);

# Set the scale region top-left corner at (25, 30), with size of 20 x 200 pixels. The
# scale labels are located on the left (default - implies vertical meter)
$m->setMeter(25, 30, 20, 200);

# Set meter scale from 0 - 100, with a tick every 10 units
$m->setScale(0, 100, 10);

# Set 0 - 50 as green (99ff99) zone, 50 - 80 as yellow (ffff66) zone, and 80 - 100 as
# red (ffcccc) zone
$m->addZone(0, 50, 0x99ff99);
$m->addZone(50, 80, 0xffff66);
$m->addZone(80, 100, 0xffcccc);

# Add deep red (000080), deep green (008000) and deep blue (800000) pointers to
# reflect the values
$m->addPointer($value0, 0x000080);
$m->addPointer($value1, 0x008000);
$m->addPointer($value2, 0x800000);

# Add a text box label at top-center (30, 5) using Arial Bold/8 pts/deep blue
# (000088), with a light blue (9999ff) background
$textBoxObj = $m->addText(30, 5, "Temp C", "arialbd.ttf", 8, 0x000088, TopCenter);
$textBoxObj->setBackground(0x9999ff);

# output the chart
header("Content-type: image/png");
print($m->makeChart2(PNG));
?>
