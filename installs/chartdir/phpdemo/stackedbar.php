<?php
include("phpchartdir.php");

#The data for the bar chart
$data0 = array(100, 125, 245, 147, 67);
$data1 = array(85, 156, 179, 211, 123);
$data2 = array(97, 87, 56, 267, 157);

#The labels for the bar chart
$labels = array("Mon", "Tue", "Wed", "Thu", "Fri");

#Create a XYChart object of size 500 x 320 pixels
$c = new XYChart(500, 320);

#Set the plotarea at (100, 40) and of size 280 x 240 pixels
$c->setPlotArea(100, 40, 280, 240);

#Add a legend box at (400, 100)
$c->addLegend(400, 100);

#Add a title to the chart using 14 points Times Bold Itatic font
$c->addTitle("Weekday Network Load", "timesbi.ttf", 14);

#Add a title to the y axis. Draw the title upright (font angle = 0)
$titleObj = $c->yAxis->setTitle("Average\nWorkload\n(MBytes\nPer Hour)");
$titleObj->setFontAngle(0);

#Set the labels on the x axis
$c->xAxis->setLabels($labels);

#Add a stacked bar layer and set the layer 3D depth to 8 pixels
$layer = $c->addBarLayer2(Stack, 8);

#Add the three data sets to the bar layer
$layer->addDataSet($data0, 0xff8080, "Server # 1");
$layer->addDataSet($data1, 0x80ff80, "Server # 2");
$layer->addDataSet($data2, 0x8080ff, "Server # 3");

#Enable bar label for the whole bar
$layer->setAggregateLabelStyle();

#Enable bar label for each segment of the stacked bar
$layer->setDataLabelStyle();

#output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
