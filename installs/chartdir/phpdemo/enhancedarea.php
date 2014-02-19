<?php
include("phpchartdir.php");

#The data for the area chart
$data = array(30, 28, 40, 55, 75, 68, 54, 60, 50, 62, 75, 65, 75, 89, 60, 55,
    53, 35, 50, 66, 56, 48, 52, 65, 62);

#The labels for the area chart
$labels = array("0", "", "", "3", "", "", "6", "", "", "9", "", "", "12", "",
    "", "15", "", "", "18", "", "", "21", "", "", "24");

#Create a XYChart object of size 500 x 300 pixels, using 0xf0e090 as background
#color, with a black border, and 1 pixel 3D border effect
$c = new XYChart(500, 300, 0xf0e090, 0x0, 1);

#Set directory for loading images to current script directory
#Need when running under Microsoft IIS
$c->setSearchPath(dirname(__FILE__));

#Set the plotarea at (55, 50) and of size 420 x 205 pixels, with white
#background. Set border and grid line colors to 0xa08040.
$c->setPlotArea(55, 50, 420, 205, 0xffffff, -1, 0xa08040, 0xa08040, 0xa08040);

#Add a title box to the chart using 13 pts Arial Bold Italic font. The title is
#in CDML and includes embedded images for highlight. The text is white
#(0xffffff) on a brown (0x807040) background, with a 1 pixel 3D border.
$titleObj = $c->addTitle(
    "<*block,valign=absmiddle*><*img=star.png*><*img=star.png*> Performance ".
    "Enhancer <*img=star.png*><*img=star.png*><*/*>", "arialbi.ttf", 13,
    0xffffff);
$titleObj->setBackground(0x807040, -1, 1);

#Add a title to the y axis
$c->yAxis->setTitle("Energy Concentration (KJ per liter)");

#Set the labels on the x axis
$c->xAxis->setLabels($labels);

#Add a title to the x axis using CDML
$c->xAxis->setTitle(
    "<*block,valign=absmiddle*><*img=clock.png*>  Elapsed Time (hour)<*/*>");

#Set the axes width to 2 pixels
$c->xAxis->setWidth(2);
$c->yAxis->setWidth(2);

#Add an area layer to the chart using a semi-transparent gradient color
$c->addAreaLayer($data, $c->gradientColor(0, 50, 0, 255, 0x40ff8000, 0x40ffffff)
    );

#Add a custom CDML text at the bottom right of the plot area as the logo
$textObj = $c->addText(475, 255,
    "<*block,valign=absmiddle*><*img=small_molecule.png*> <*block*>".
    "<*font=timesbi.ttf,size=10,color=804040*>Molecular\nEngineering<*/*>");
$textObj->setAlignment(BottomRight);

#output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
