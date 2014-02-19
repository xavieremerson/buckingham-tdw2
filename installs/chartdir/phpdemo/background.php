<?php
include("phpchartdir.php");

#The data for the chart
$data = array(85, 156, 179.5, 211, 123);
$labels = array("Mon", "Tue", "Wed", "Thu", "Fri");

#Create a XYChart object of size 270 x 270 pixels
$c = new XYChart(270, 270);

#Set the plot area at (40, 32) and of size 200 x 200 pixels
$plotarea = $c->setPlotArea(40, 32, 200, 200);

#Set the background style based on the input parameter
if ($HTTP_GET_VARS["img"] == "0") {
    #Has wallpaper image
    $c->setWallpaper(dirname(__FILE__)."/tile.gif");
} else if ($HTTP_GET_VARS["img"] == "1") {
    #Use a background image as the plot area background
    $plotarea->setBackground2(dirname(__FILE__)."/bg.png");
} else if ($HTTP_GET_VARS["img"] == "2") {
    #Use white (0xffffff) and grey (0xe0e0e0) as two alternate plotarea
    #background colors
    $plotarea->setBackground(0xffffff, 0xe0e0e0);
} else {
    #Use a dark background palette
    $c->setColors($whiteOnBlackPalette);
}

#Set the labels on the x axis
$c->xAxis->setLabels($labels);

#Add a color bar layer using the given data. Use a 1 pixel 3D border for the
#bars.
$barLayer3Obj = $c->addBarLayer3($data);
$barLayer3Obj->setBorderColor(-1, 1);

#output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
