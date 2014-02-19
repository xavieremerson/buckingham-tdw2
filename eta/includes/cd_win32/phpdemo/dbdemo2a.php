<?php
require_once("../lib/phpchartdir.php");

#
# Retrieve the data from the query parameters
#

$selectedYear = $_REQUEST["year"];

$software = split(",", $_GET["software"]);
$hardware = split(",", $_GET["hardware"]);
$services = split(",", $_GET["services"]);

#
# Now we obtained the data into arrays, we can draw the chart using ChartDirector
#

# Create a XYChart object of size 600 x 300 pixels, with a light grey (eeeeee)
# background, black border, 1 pixel 3D border effect and rounded corners.
$c = new XYChart(600, 300, 0xeeeeee, 0x000000, 1);
$c->setRoundedFrame();

# Set the plotarea at (60, 60) and of size 520 x 200 pixels. Set background color to
# white (ffffff) and border and grid colors to grey (cccccc)
$c->setPlotArea(60, 60, 520, 200, 0xffffff, -1, 0xcccccc, 0xccccccc);

# Add a title to the chart using 15pts Times Bold Italic font, with a light blue
# (ccccff) background and with glass lighting effects.
$textBoxObj = $c->addTitle("Global Revenue for Year $selectedYear", "timesbi.ttf", 15
    );
$textBoxObj->setBackground(0xccccff, 0x000000, glassEffect());

# Add a legend box at (70, 32) (top of the plotarea) with 9pts Arial Bold font
$legendObj = $c->addLegend(70, 32, false, "arialbd.ttf", 9);
$legendObj->setBackground(Transparent);

# Add a line chart layer using the supplied data
$layer = $c->addLineLayer2();
$dataSetObj = $layer->addDataSet($software, 0xff0000, "Software");
$dataSetObj->setDataSymbol(CircleShape, 9);
$dataSetObj = $layer->addDataSet($hardware, 0x00ff00, "Hardware");
$dataSetObj->setDataSymbol(DiamondShape, 11);
$dataSetObj = $layer->addDataSet($services, 0xffaa00, "Services");
$dataSetObj->setDataSymbol(Cross2Shape(), 11);

# Set the line width to 3 pixels
$layer->setLineWidth(3);

# Set the x axis labels. In this example, the labels must be Jan - Dec.
$labels = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sept",
    "Oct", "Nov", "Dec");
$c->xAxis->setLabels($labels);

# Set the y axis title
$c->yAxis->setTitle("USD (Millions)");

# Set axes width to 2 pixels
$c->xAxis->setWidth(2);
$c->yAxis->setWidth(2);

# output the chart in PNG format
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
