<?php
require_once("../lib/phpchartdir.php");

# The data for the chart
$data0 = array(600, 800, 1200, 1500, 1800, 1900, 2000, 1950);
$data1 = array(300, 450, 500, 1000, 1500, 1600, 1650, 1600);

# The labels for the chart
$labels = array("1995", "1996", "1997", "1998", "1999", "2000", "2001", "2002");

# Create a XYChart object of size 450 x 250 pixels, with a pale yellow (0xffffc0)
# background, a black border, and 1 pixel 3D border effect.
$c = new XYChart(450, 250, 0xffffc0, 0, 1);

# Set the plotarea at (60, 45) and of size 360 x 170 pixels, using white (0xffffff)
# as the plot area background color. Turn on both horizontal and vertical grid lines
# with light grey color (0xc0c0c0)
$c->setPlotArea(60, 45, 360, 170, 0xffffff, -1, -1, 0xc0c0c0, -1);

# Add a legend box at (60, 20) (top of the chart) with horizontal layout. Use 8 pts
# Arial Bold font. Set the background and border color to Transparent.
$legendObj = $c->addLegend(60, 20, false, "arialbd.ttf", 8);
$legendObj->setBackground(Transparent);

# Add a title to the chart using 12 pts Arial Bold/white font. Use a 1 x 2 bitmap
# pattern as the background.
$textBoxObj = $c->addTitle("Information Resource Usage", "arialbd.ttf", 12, 0xffffff)
    ;
$textBoxObj->setBackground($c->patternColor(array(0x000040, 0x000080), 2));

# Set the labels on the x axis
$c->xAxis->setLabels($labels);

# Reserve 8 pixels margins at both side of the x axis to avoid the first and last
# symbols drawing outside of the plot area
$c->xAxis->setMargin(8, 8);

# Add a title to the y axis
$c->yAxis->setTitle("Population");

# Add a line layer to the chart
$layer = $c->addLineLayer2();

# Add the first line using small_user.png as the symbol.
$dataSetObj = $layer->addDataSet($data0, 0xcf4040, "Users");
$dataSetObj->setDataSymbol2(dirname(__FILE__)."/small_user.png");

# Add the first line using small_computer.png as the symbol.
$dataSetObj = $layer->addDataSet($data1, 0x40cf40, "Computers");
$dataSetObj->setDataSymbol2(dirname(__FILE__)."/small_computer.png");

# Set the line width to 3 pixels
$layer->setLineWidth(3);

# output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
