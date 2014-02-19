<?php
require_once("../lib/phpchartdir.php");

# The XYZ points for the bubble chart
$dataX0 = array(150, 300, 1000, 1700);
$dataY0 = array(12, 60, 25, 65);
$dataZ0 = array(20, 50, 50, 85);

$dataX1 = array(500, 1000, 1300);
$dataY1 = array(35, 50, 75);
$dataZ1 = array(30, 55, 95);

# Create a XYChart object of size 450 x 420 pixels
$c = new XYChart(450, 420);

# Set the plotarea at (55, 65) and of size 350 x 300 pixels, with a light grey border
# (0xc0c0c0). Turn on both horizontal and vertical grid lines with light grey color
# (0xc0c0c0)
$c->setPlotArea(55, 65, 350, 300, -1, -1, 0xc0c0c0, 0xc0c0c0, -1);

# Add a legend box at (50, 30) (top of the chart) with horizontal layout. Use 12 pts
# Times Bold Italic font. Set the background and border color to Transparent.
$legendObj = $c->addLegend(50, 30, false, "timesbi.ttf", 12);
$legendObj->setBackground(Transparent);

# Add a title to the chart using 18 pts Times Bold Itatic font.
$c->addTitle("Product Comparison Chart", "timesbi.ttf", 18);

# Add a title to the y axis using 12 pts Arial Bold Italic font
$c->yAxis->setTitle("Capacity (tons)", "arialbi.ttf", 12);

# Add a title to the x axis using 12 pts Arial Bold Italic font
$c->xAxis->setTitle("Range (miles)", "arialbi.ttf", 12);

# Set the axes line width to 3 pixels
$c->xAxis->setWidth(3);
$c->yAxis->setWidth(3);

# Add (dataX0, dataY0) as a scatter layer with semi-transparent red (0x80ff3333)
# circle symbols, where the circle size is modulated by dataZ0. This creates a bubble
# effect.
$scatterLayerObj = $c->addScatterLayer($dataX0, $dataY0, "Technology AAA",
    CircleSymbol, 9, 0x80ff3333, 0x80ff3333);
$scatterLayerObj->setSymbolScale($dataZ0);

# Add (dataX1, dataY1) as a scatter layer with semi-transparent green (0x803333ff)
# circle symbols, where the circle size is modulated by dataZ1. This creates a bubble
# effect.
$scatterLayerObj = $c->addScatterLayer($dataX1, $dataY1, "Technology BBB",
    CircleSymbol, 9, 0x803333ff, 0x803333ff);
$scatterLayerObj->setSymbolScale($dataZ1);

# output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
