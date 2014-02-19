<?php
include("phpchartdir.php");

#The data for the line chart
$data = array(30, 28, 40, 55, 75, 68, 54, 60, 50, 62, 75, 65, 75, 91, 60, 55,
    53, 35, 50, 66, 56, 48, 52, 65, 62);

#The labels for the line chart
$labels = array("0", "", "", "3", "", "", "6", "", "", "9", "", "", "12", "",
    "", "15", "", "", "18", "", "", "21", "", "", "24");

#Create a XYChart object of size 300 x 280 pixels
$c = new XYChart(300, 280);

#Set the plotarea at (45, 30) and of size 200 x 200 pixels
$c->setPlotArea(45, 30, 200, 200);

#Add a title to the chart using 12 pts Arial Bold Italic font
$c->addTitle("Daily Server Utilization", "arialbi.ttf", 12);

#Add a title to the y axis
$c->yAxis->setTitle("MBytes");

#Add a title to the x axis
$c->xAxis->setTitle("June 12, 2001");

#Add a blue (0x6666ff) 3D line chart layer using the give data
$lineLayerObj = $c->addLineLayer($data, 0x6666ff);
$lineLayerObj->set3D();

#Set the x axis labels using the given labels
$c->xAxis->setLabels($labels);

#output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
