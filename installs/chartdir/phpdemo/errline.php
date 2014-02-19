<?php
include("phpchartdir.php");

#The data with error information
$data = array(42, 49, 33, 38, 51, 46, 29, 41, 44, 57, 59, 52, 37, 34, 51, 56,
    56, 60, 70, 76, 63, 67, 75, 64, 51);
$errData = array(5, 6, 5.1, 6.5, 6.6, 8, 5.4, 5.1, 4.6, 5.0, 5.2, 6.0, 4.9, 5.6,
    4.8, 6.2, 7.4, 7.1, 6.0, 6.6, 7.1, 5.3, 5.5, 7.9, 6.1);

#The labels for the chart
$labels = array("0", "-", "-", "3", "-", "-", "6", "-", "-", "9", "-", "-",
    "12", "-", "-", "15", "-", "-", "18", "-", "-", "21", "-", "-", "24");

#Create a XYChart object of size 600 x 300 pixels, with a light grey (0xc0c0c0)
#background, a black border, and 1 pixel 3D border effect.
$c = new XYChart(600, 300, 0xc0c0c0, 0, 1);

#Set directory for loading images to current script directory
#Need when running under Microsoft IIS
$c->setSearchPath(dirname(__FILE__));

#Set the plotarea at (55, 45) and of size 520 x 205 pixels, with white
#background. Turn on both horizontal and vertical grid lines with light grey
#color (0xc0c0c0)
$c->setPlotArea(55, 45, 520, 210, 0xffffff, -1, -1, 0xc0c0c0, 0xc0c0c0);

#Add a title box to the chart using 13 pts Arial Bold Italic font. The title is
#in CDML and includes embedded images for highlight. The text is white
#(0xffffff) on a black background, with a 1 pixel 3D border.
$titleObj = $c->addTitle(
    "<*block,valign=absmiddle*><*img=star.png*><*img=star.png*> Molecular ".
    "Temperature Control <*img=star.png*><*img=star.png*><*/*>", "arialbi.ttf",
    13, 0xffffff);
$titleObj->setBackground(0x0, -1, 1);

#Add a title to the y axis
$c->yAxis->setTitle("Temperature");

#Add a title to the x axis using CMDL
$c->xAxis->setTitle(
    "<*block,valign=absmiddle*><*img=clock.png*>  Elapsed Time (hour)<*/*>");

#Set the labels on the x axis
$c->xAxis->setLabels($labels);

#Set the axes width to 2 pixels
$c->xAxis->setWidth(2);
$c->yAxis->setWidth(2);

#Add a line layer to the chart
$lineLayer = $c->addLineLayer2();

#Add a blue (0xff) data set to the line layer, with yellow (0xffff80) diamond
#symbols
$dataSetObj = $lineLayer->addDataSet($data, 0xff);
$dataSetObj->setDataSymbol(DiamondSymbol, 12, 0xffff80);

#Set the line width to 2 pixels
$lineLayer->setLineWidth(2);

#Add a box whisker layer to the chart. Use only the upper and lower mark of the
#box whisker layer to act as error zones. The upper and lower marks are computed
#using the ArrayMath object.
$upperMark = new ArrayMath($data);
$upperMark->add($errData);

$lowerMark = new ArrayMath($data);
$lowerMark->sub($errData);

$errLayer = $c->addBoxWhiskerLayer(null, null, $upperMark->result(),
    $lowerMark->result(), null, Transparent, 0xbb6633);

#Set the line width to 2 pixels
$errLayer->setLineWidth(2);

#Set the error zone to occupy half the space between the symbols
$errLayer->setDataGap(0.5);

#Add a custom CDML text at the bottom right of the plot area as the logo
$textObj = $c->addText(575, 255,
    "<*block,valign=absmiddle*><*img=small_molecule.png*> <*block*>".
    "<*font=timesbi.ttf,size=10,color=804040*>Molecular\nEngineering<*/*>");
$textObj->setAlignment(BottomRight);

#output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
