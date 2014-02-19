<?php
include("phpchartdir.php");

#The data for the area chart
$data = array(30, 28, 40, 55, 75, 68, 54, 60, 50, 62, 75, 65, 75, 89, 60, 55,
    53, 35, 50, 66, 56, 48, 52, 65, 62);

#The labels for the area chart
$labels = array("0 m", "", "", "3 m", "", "", "6 m", "", "", "9 m", "", "",
    "12 m", "", "", "15 m", "", "", "18 m", "", "", "21 m", "", "", "24 m");

#Create a XYChart object of size 320 x 320 pixels
$c = new XYChart(320, 320);

#Swap the x and y axis to become a rotated chart
$c->swapXY();

#Set the y axis on the top side (right + rotated = top)
$c->setYAxisOnRight();

#Reverse the x axis so it is pointing downwards
$c->xAxis->setReverse();

#Set the plotarea at (50, 50) and of size 200 x 200 pixels. Enable horizontal
#and vertical grids by setting their colors to grey (0xc0c0c0).
$plotAreaObj = $c->setPlotArea(50, 50, 250, 250);
$plotAreaObj->setGridColor(0xc0c0c0, 0xc0c0c0);

#Add a line chart layer using the given data
$c->addAreaLayer($data, $c->gradientColor(50, 0, 300, 0, 0xffffff, 0xff));

#Set the x axis labels using the given labels
$c->xAxis->setLabels($labels);

#Add a title to the x axis
$c->xAxis->setTitle("Depth");

#Add a title to the y axis
$c->yAxis->setTitle("Carbon Dioxide Concentration (ppm)");

#output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
