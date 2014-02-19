<?php
include("phpchartdir.php");

#The data for the bar chart
$data = array(-6.3, 2.3, 0.7, -3.4, 2.2, -2.9, -0.1, -0.1, 3.3, 6.2, 4.3, 1.6);

#The labels for the bar chart
$labels = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep",
    "Oct", "Nov", "Dec");

#Create a XYChart object of size 500 x 320 pixels
$c = new XYChart(500, 320);

#Add a title to the chart using Arial Bold Italic font
$c->addTitle("Productivity Change - Year 2001", "arialbi.ttf");

#Set the plotarea at (50, 30) and of size 400 x 250 pixels
$c->setPlotArea(50, 30, 400, 250);

#Add a bar layer to the chart
$layer = $c->addBarLayer2();

#Add a data set to the bar using a y zone color. The color is configured to be
#orange (0xff6600) below zero, and blue (0x6666ff) above zero.
$layer->addDataSet($data, $layer->yZoneColor(0, 0xff6600, 0x6666ff));

#Add labels to the top of the bar using 8 pt Arial Bold font. The font color is
#configured to be red (0xcc3300) below zero, and blue (0x3333ff) above zero.
$layer->setAggregateLabelStyle("arialbd.ttf", 8, $layer->yZoneColor(0, 0xcc3300,
    0x3333ff));

#Set the labels on the x axis and use Arial Bold as the label font
$labelsObj = $c->xAxis->setLabels($labels);
$labelsObj->setFontStyle("arialbd.ttf");

#Draw the y axis on the right of the plot area
$c->setYAxisOnRight(true);

#Use Arial Bold as the y axis label font
$c->yAxis->setLabelStyle("arialbd.ttf");

#Add a title to the y axis
$c->yAxis->setTitle("Percentage");

#Add a light blue (0xccccff) zone for positive part of the plot area
$c->yAxis->addZone(0, 9999, 0xccccff);

#Add a pink (0xffffcc) zone for negative part of the plot area
$c->yAxis->addZone(-9999, 0, 0xffcccc);

#output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
