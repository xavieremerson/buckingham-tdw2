<?php
include("phpchartdir.php");

#The data for the bar chart
$data = array(85, 156, 179.5, 211, 123);

#The labels for the bar chart
$labels = array("Mon", "Tue", "Wed", "Thu", "Fri");

#The colors for the bar chart
$colors = array(0xb8bc9c, 0xa0bdc4, 0x999966, 0x333366, 0xc3c3e6);

#Create a XYChart object of size 260 x 220 pixels
$c = new XYChart(260, 220);

#Set the background color of the chart to gold (goldGradient). Use a 2 pixel 3D
#border.
$c->setBackground($c->gradientColor($goldGradient), -1, 2);

#Add a title box using 10 point Arial Bold font. Set the background color to
#blue metallic (blueMetalGradient). Use a 1 pixel 3D border.
$titleObj = $c->addTitle("Daily Network Load", "arialbd.ttf", 10);
$titleObj->setBackground($c->gradientColor($blueMetalGradient), -1, 1);

#Set the plotarea at (40, 40) and of 200 x 150 pixels in size
$c->setPlotArea(40, 40, 200, 150);

#Add a multi-color bar chart layer using the given data and colors. Use a 1
#pixel 3D border for the bars.
$barLayer3Obj = $c->addBarLayer3($data, $colors);
$barLayer3Obj->setBorderColor(-1, 1);

#Set the x axis labels using the given labels
$c->xAxis->setLabels($labels);

#output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
