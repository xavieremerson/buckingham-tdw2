<?php
include("phpchartdir.php");

#The data for the area chart
$data0 = array(42, 49, 33, 38, 51, 46, 29, 41, 44, 57, 59, 52, 37, 34, 51, 56,
    56, 60, 70, 76, 63, 67, 75, 64, 51);
$data1 = array(50, 55, 47, 34, 42, 49, 63, 62, 73, 59, 56, 50, 64, 60, 67, 67,
    58, 59, 73, 77, 84, 82, 80, 84, 98);
$data2 = array(87, 89, 85, 66, 53, 39, 24, 21, 37, 56, 37, 23, 21, 33, 13, 17,
    14, 23, 16, 25, 29, 30, 45, 47, 46);

#The labels for the area chart
$labels = array("1996", "-", "-", "-", "1997", "-", "-", "-", "1998", "-", "-",
    "-", "1999", "-", "-", "-", "2000", "-", "-", "-", "2001", "-", "-", "-",
    "2002");

#Create a XYChart object of size 500 x 280 pixels, using 0xffffcc as background
#color, with a black border, and 1 pixel 3D border effect
$c = new XYChart(500, 280, 0xffffcc, 0, 1);

#Set directory for loading images to current script directory
#Need when running under Microsoft IIS
$c->setSearchPath(dirname(__FILE__));

#Set the plotarea at (50, 45) and of size 320 x 200 pixels with white
#background. Enable horizontal and vertical grid lines using the grey (0xc0c0c0)
#color.
$plotAreaObj = $c->setPlotArea(50, 45, 320, 200, 0xffffff);
$plotAreaObj->setGridColor(0xc0c0c0, 0xc0c0c0);

#Add a legend box at (370, 45) using vertical layout and 8 points Arial Bold
#font.
$legendBox = $c->addLegend(370, 45, true, "arialbd.ttf", 8);

#Set the legend box background and border to transparent
$legendBox->setBackground(Transparent, Transparent);

#Set the legend box icon size to 16 x 32 pixels to match with custom icon size
$legendBox->setKeySize(16, 32);

#Add a title to the chart using 14 points Times Bold Itatic font and white font
#color, and 0x804020 as the background color
$titleObj = $c->addTitle("Quarterly Product Sales", "timesbi.ttf", 14, 0xffffff)
    ;
$titleObj->setBackground(0x804020);

#Set the labels on the x axis
$c->xAxis->setLabels($labels);

#Add a percentage area layer to the chart
$layer = $c->addAreaLayer2(Percentage);

#Add the three data sets to the area layer, using icons images with labels as
#data set names
$layer->addDataSet($data0, 0x40ddaa77,
    "<*block,valign=absmiddle*><*img=service.png*> Service<*/*>");
$layer->addDataSet($data1, 0x40aadd77,
    "<*block,valign=absmiddle*><*img=software.png*> Software<*/*>");
$layer->addDataSet($data2, 0x40aa77dd,
    "<*block,valign=absmiddle*><*img=computer.png*> Hardware<*/*>");

#For a vertical stacked chart with positive data only, the last data set is
#always on top. However, in a vertical legend box, the last data set is at the
#bottom. This can be reversed by using the setLegend method.
$layer->setLegend(ReverseLegend);

#output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
