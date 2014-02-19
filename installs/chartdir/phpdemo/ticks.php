<?php
include("phpchartdir.php");

#The data for the chart
$data = array(100, 125, 265, 147, 67, 105);

#Create a XYChart object of size 250 x 250 pixels
$c = new XYChart(250, 250);

#Set the plot area at (27, 25) and of size 200 x 200 pixels
$c->setPlotArea(27, 25, 200, 200);

if ($HTTP_GET_VARS["img"] == "1") {
    #High tick density, uses 10 pixels as tick spacing
    $c->addTitle("Tick Density = 10 pixels");
    $c->yAxis->setTickDensity(10);
} else {
    #Normal tick density, just use the default setting
    $c->addTitle("Default Tick Density");
}

#Add a color bar layer using the given data. Use a 1 pixel 3D border for the
#bars.
$barLayer3Obj = $c->addBarLayer3($data);
$barLayer3Obj->setBorderColor(-1, 1);

#output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
