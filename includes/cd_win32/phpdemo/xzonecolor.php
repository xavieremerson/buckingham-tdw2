<?php
require_once("../lib/phpchartdir.php");

# The data for the chart
$data = array(50, 55, 47, 34, 42, 49, 63, 62, 73, 59, 56, 50, 64, 60, 67, 67, 58, 59,
    73, 77, 84, 82, 80, 84, 89);

# The error data representing the error band around the data points
$errData = array(5, 6, 5.1, 6.5, 6.6, 8, 5.4, 5.1, 4.6, 5.0, 5.2, 6.0, 4.9, 5.6, 4.8,
    6.2, 7.4, 7.1, 6.5, 9.6, 12.1, 15.3, 18.5, 20.9, 24.1);

# The timestamps for the data
$labels = array(chartTime(2001, 1, 1), chartTime(2001, 2, 1), chartTime(2001, 3, 1),
    chartTime(2001, 4, 1), chartTime(2001, 5, 1), chartTime(2001, 6, 1), chartTime(
    2001, 7, 1), chartTime(2001, 8, 1), chartTime(2001, 9, 1), chartTime(2001, 10, 1
    ), chartTime(2001, 11, 1), chartTime(2001, 12, 1), chartTime(2002, 1, 1),
    chartTime(2002, 2, 1), chartTime(2002, 3, 1), chartTime(2002, 4, 1), chartTime(
    2002, 5, 1), chartTime(2002, 6, 1), chartTime(2002, 7, 1), chartTime(2002, 8, 1),
    chartTime(2002, 9, 1), chartTime(2002, 10, 1), chartTime(2002, 11, 1), chartTime(
    2002, 12, 1), chartTime(2003, 1, 1));

# Create a XYChart object of size 550 x 220 pixels
$c = new XYChart(550, 220);

# Set the plot area at (50, 10) and of size 480 x 180 pixels. Enabled both vertical
# and horizontal grids by setting their colors to light grey (cccccc)
$plotAreaObj = $c->setPlotArea(50, 10, 480, 180);
$plotAreaObj->setGridColor(0xcccccc, 0xcccccc);

# Add a legend box (50, 10) (top of plot area) using horizontal layout. Use 8 pts
# Arial font. Disable bounding box (set border to transparent).
$legendBox = $c->addLegend(50, 10, false, "", 8);
$legendBox->setBackground(Transparent);

# Add keys to the legend box to explain the color zones
$legendBox->addKey("Historical", 0x9999ff);
$legendBox->addKey("Forecast", 0xff9966);

# Add a title to the y axis.
$c->yAxis->setTitle("Energy Consumption");

# Set the labels on the x axis
$c->xAxis->setLabels2($labels);

# Set multi-style axis label formatting. Use Arial Bold font for yearly labels and
# display them as "yyyy". Use default font for monthly labels and display them as
# "mmm". Replace some labels with minor ticks to ensure the labels are at least 3
# units apart.
$c->xAxis->setMultiFormat(StartOfYearFilter(), "<*font=arialbd.ttf*>{value|yyyy}",
    StartOfMonthFilter(), "{value|mmm}", 3);

# Add a line layer to the chart
$layer = $c->addLineLayer2();

# Create the color to draw the data line. The line is blue (0x333399) to the left of
# x = 18, and become a red (0xd04040) dash line to the right of x = 18.
$lineColor = $layer->xZoneColor(18, 0x333399, $c->dashLineColor(0xd04040, DashLine));

# Add the data line
$layer->addDataSet($data, $lineColor);

# Create the color to draw the err zone. The color is semi-transparent blue
# (0x809999ff) to the left of x = 18, and become semi-transparent red (0x80ff9966) to
# the right of x = 18.
$errColor = $layer->xZoneColor(18, 0x809999ff, 0x80ff9966);

# Add the upper border of the err zone
$upperBorder = new ArrayMath($data);
$upperBorder->add($errData);
$layer->addDataSet($upperBorder->result(), $errColor);

# Add the lower border of the err zone
$lowerBorder = new ArrayMath($data);
$lowerBorder->sub($errData);
$layer->addDataSet($lowerBorder->result(), $errColor);

# Set the default line width to 2 pixels
$layer->setLineWidth(2);

# Color the region between the err zone lines
$c->addInterLineLayer($layer->getLine(1), $layer->getLine(2), $errColor);

# output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
