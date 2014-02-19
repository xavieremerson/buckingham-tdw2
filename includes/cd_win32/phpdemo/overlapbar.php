<?php
require_once("../lib/phpchartdir.php");

# The data for the bar chart
$data0 = array(100, 125, 156, 147, 87, 124, 178, 109, 140, 106, 192, 122);
$data1 = array(122, 156, 179, 211, 198, 177, 160, 220, 190, 188, 220, 270);
$data2 = array(167, 190, 213, 267, 250, 320, 212, 199, 245, 267, 240, 310);
$labels = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sept",
    "Oct", "Nov", "Dec");

# Create a XYChart object of size 580 x 280 pixels
$c = new XYChart(580, 280);

# Add a title to the chart using 14 pts Arial Bold Italic font
$c->addTitle("Product Revenue For Last 3 Years", "arialbi.ttf", 14);

# Set the plot area at (50, 50) and of size 500 x 200. Use two alternative background
# colors (f8f8f8 and ffffff)
$c->setPlotArea(50, 50, 500, 200, 0xf8f8f8, 0xffffff);

# Add a legend box at (50, 25) using horizontal layout. Use 8pts Arial as font, with
# transparent background.
$legendObj = $c->addLegend(50, 25, false, "arial.ttf", 8);
$legendObj->setBackground(Transparent);

# Set the x axis labels
$c->xAxis->setLabels($labels);

# Draw the ticks between label positions (instead of at label positions)
$c->xAxis->setTickOffset(0.5);

# Add a multi-bar layer with 3 data sets
$layer = $c->addBarLayer2(Side);
$layer->addDataSet($data0, 0xff8080, "Year 2003");
$layer->addDataSet($data1, 0x80ff80, "Year 2004");
$layer->addDataSet($data2, 0x8080ff, "Year 2005");

# Set 50% overlap between bars
$layer->setOverlapRatio(0.5);

# Add a title to the y-axis
$c->yAxis->setTitle("Revenue (USD in millions)");

# output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
