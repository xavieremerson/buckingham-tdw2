<?php
require_once("../lib/phpchartdir.php");

# The data for the pie chart
$data0 = array(25, 18, 15);
$data1 = array(14, 32, 24);
$data2 = array(25, 23, 9);

# The labels for the pie chart
$labels = array("Software", "Hardware", "Services");

# Create a PieChart object of size 180 x 160 pixels
$c = new PieChart(180, 160);

# Set the center of the pie at (90, 80) and the radius to 60 pixels
$c->setPieSize(90, 80, 60);

# Set the border color of the sectors to white (ffffff)
$c->setLineColor(0xffffff);

# Set the background color of the sector label to pale yellow (ffffc0) with a black
# border (000000)
$textBoxObj = $c->setLabelStyle();
$textBoxObj->setBackground(0xffffc0, 0x000000);

# Set the label to be slightly inside the perimeter of the circle
$c->setLabelLayout(CircleLayout, -10);

# Set the title, data and colors according to which pie to draw
if ($_REQUEST["img"] == "0") {
    $c->addTitle("Alpha Division", "arialbd.ttf", 8);
    $c->setData($data0, $labels);
    $c->setColors2(DataColor, array(0xff3333, 0xff9999, 0xffcccc));
} else if ($_REQUEST["img"] == "1") {
    $c->addTitle("Beta Division", "arialbd.ttf", 8);
    $c->setData($data1, $labels);
    $c->setColors2(DataColor, array(0x33ff33, 0x99ff99, 0xccffcc));
} else {
    $c->addTitle("Gamma Division", "arialbd.ttf", 8);
    $c->setData($data2, $labels);
    $c->setColors2(DataColor, array(0x3333ff, 0x9999ff, 0xccccff));
}

# output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
