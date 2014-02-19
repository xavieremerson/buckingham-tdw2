<?php
include("phpchartdir.php");

#The data for the chart
$data0 = array(0.05, 0.06, 0.48, 0.1, 0.01, 0.05);
$data1 = array(100, 125, 265, 147, 67, 105);
$labels = array("Jan", "Feb", "Mar", "Apr", "May", "Jun");

#Create a XYChart object of size 300 x 180 pixels
$c = new XYChart(300, 180);

#Set the plot area at (50, 20) and of size 200 x 130 pixels
$c->setPlotArea(50, 20, 200, 130);

#Add a title to the chart using 8 pts Arial Bold font
$c->addTitle("Independent Y-Axis Demo", "arialbd.ttf", 8);

#Set the x axis labels using the given labels
$c->xAxis->setLabels($labels);

#Add a title to the primary (left) y axis
$c->yAxis->setTitle("Packet Drop Rate (pps)");

#Set the axis, label and title colors for the primary y axis to red (0xc00000)
#to match the first data set
$c->yAxis->setColors(0xc00000, 0xc00000, 0xc00000);

#Add a title to the secondary (right) y axis
$yAxis2Obj = $c->yAxis2();
$yAxis2Obj->setTitle("Throughtput (MBytes)");

#set the axis, label and title colors for the primary y axis to green (0x008000)
#to match the second data set
$yAxis2Obj = $c->yAxis2();
$yAxis2Obj->setColors(0x8000, 0x8000, 0x8000);

#Add a line layer to for the first data set using red (0xc00000) color with a
#line width to 3 pixels
$lineLayerObj = $c->addLineLayer($data0, 0xc00000);
$lineLayerObj->setLineWidth(3);

#Add a bar layer to for the second data set using green (0x00C000) color. Bind
#the second data set to the secondary (right) y axis
$barLayerObj = $c->addBarLayer();
$dataSetObj = $barLayerObj->addDataSet($data1, 0xc000);
$dataSetObj->setUseYAxis2();

#output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
