<?php
include("phpchartdir.php");

#The data for the line chart
$data = array(30, 28, 40, 55, 75, 68, 54, 60, 50, 62, 75, 65, 75, 91, 60, 55,
    53, 35, 50, 66, 56, 48, 52, 65, 62);

#The labels for the line chart
$labels = array("0", "", "", "3", "", "", "6", "", "", "9", "", "", "12", "",
    "", "15", "", "", "18", "", "", "21", "", "", "24");

#Create a XYChart object of size 250 x 250 pixels
$c = new XYChart(250, 250);

#Set the plotarea at (30, 20) and of size 200 x 200 pixels
$c->setPlotArea(30, 20, 200, 200);

#Add a line chart layer using the given data
$c->addLineLayer($data);

#Set the x axis labels using the given labels
$c->xAxis->setLabels($labels);

#output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
