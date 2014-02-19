<?php
require_once("../lib/phpchartdir.php");

# The data for the area chart
$data = array(30, 28, 40, 55, 75, 68, 54, 60, 50, 62, 75, 65, 75, 89, 60, 55, 53, 35,
    50, 66, 56, 48, 52, 65, 62);

# The labels for the area chart
$labels = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12",
    "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24");

# Create a XYChart object of size 600 x 300 pixels, with a pale blue (eeeeff)
# background, black border, 1 pixel 3D border effect and rounded corners.
$c = new XYChart(600, 300, 0xeeeeff, 0x000000, 1);
$c->setRoundedFrame();

#Set directory for loading images to current script directory
#Need when running under Microsoft IIS
$c->setSearchPath(dirname(__FILE__));

# Set the plotarea at (55, 55) and of size 520 x 195 pixels, with white (ffffff)
# background. Set horizontal and vertical grid lines to grey (cccccc).
$c->setPlotArea(55, 55, 520, 195, 0xffffff, -1, -1, 0xcccccc, 0xcccccc);

# Add a title box to the chart using 15 pts Times Bold Italic font. The text is white
# (ffffff) on a deep blue (000088) background, with soft lighting effect from the
# right side.
$textBoxObj = $c->addTitle(
    "<*block,valign=absmiddle*><*img=star.png*><*img=star.png*> Performance ".
    "Enhancer <*img=star.png*><*img=star.png*><*/*>", "timesbi.ttf", 15, 0xffffff);
$textBoxObj->setBackground(0x000088, -1, softLighting(Right));

# Add a title to the y axis
$c->yAxis->setTitle("Energy Concentration (KJ per liter)");

# Set the labels on the x axis.
$c->xAxis->setLabels($labels);

# Display 1 out of 3 labels on the x-axis.
$c->xAxis->setLabelStep(3);

# Add a title to the x axis using CDML
$c->xAxis->setTitle(
    "<*block,valign=absmiddle*><*img=clock.png*>  Elapsed Time (hour)<*/*>");

# Set the axes width to 2 pixels
$c->xAxis->setWidth(2);
$c->yAxis->setWidth(2);

# Add an area layer to the chart using a gradient color that changes vertically from
# semi-transparent red (80ff0000) to semi-transparent white (80ffffff)
$c->addAreaLayer($data, $c->linearGradientColor(0, 50, 0, 255, 0x80ff0000, 0x80ffffff
    ));

# Add a custom CDML text at the bottom right of the plot area as the logo
$textBoxObj = $c->addText(575, 245,
    "<*block,valign=absmiddle*><*img=small_molecule.png*> <*block*>".
    "<*font=timesbi.ttf,size=10,color=804040*>Molecular\nEngineering<*/*>");
$textBoxObj->setAlignment(BottomRight);

# output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
