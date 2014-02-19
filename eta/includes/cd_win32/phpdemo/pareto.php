<?php
require_once("../lib/phpchartdir.php");

# The data for the chart
$data = array(40, 15, 7, 5, 2);

# The labels for the chart
$labels = array("Hard Disk", "PCB", "Printer", "CDROM", "Keyboard");

# Create a XYChart object of size 400 x 225 pixels. Use golden background color, with
# a 2 pixel 3D border.
$c = new XYChart(400, 225, goldColor(), -1, 2);

# Add a title box using Arial Bold/11 pt font. Set the background color to metallic
# blue (9999FF). Use a 1 pixel 3D border.
$textBoxObj = $c->addTitle("Hardware Defects", "arialbd.ttf", 11);
$textBoxObj->setBackground(metalColor(0x9999ff), -1, 1);

# Set the plotarea at (50, 40) and of 300 x 150 pixels in size, with a silver
# background color.
$c->setPlotArea(50, 40, 300, 150, silverColor());

# Add a line layer for the pareto line
$lineLayer = $c->addLineLayer();

# Compute the pareto line by accumulating the data
$lineData = new ArrayMath($data);
$lineData->acc();

# Set a scaling factor such as the maximum point of the line is scaled to 100
$scaleFactor = 100 / $lineData->max();
$lineData->mul2($scaleFactor);

# Add the pareto line using the scaled data. Use deep blue (0x80) as the line color,
# with light blue (0x9999ff) diamond symbols
$dataSetObj = $lineLayer->addDataSet($lineData->result(), 0x000080);
$dataSetObj->setDataSymbol(DiamondSymbol, 9, 0x9999ff);

# Set the line width to 2 pixel
$lineLayer->setLineWidth(2);

# Add a multi-color bar layer using the given data.
$barLayer = $c->addBarLayer3($data);

# Bind the layer to the secondary (right) y-axis.
$barLayer->setUseYAxis2();

# Set soft lighting for the bars with light direction from the right
$barLayer->setBorderColor(Transparent, softLighting(Right));

# Set the labels on the x axis.
$c->xAxis->setLabels($labels);

# Set the primary y-axis scale as 0 - 100 with a tick every 20 units
$c->yAxis->setLinearScale(0, 100, 20);

# Set the label format of the y-axis label to include a percentage sign
$c->yAxis->setLabelFormat("{value}%");

# Add a title to the secondary y-axis
$c->yAxis2->setTitle("Frequency");

# Set the secondary y-axis label foramt to show no decimal point
$c->yAxis2->setLabelFormat("{value|0}");

# Set the relationship between the two y-axes, which only differ by a scaling factor
$c->syncYAxis(1 / $scaleFactor);

# Output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
