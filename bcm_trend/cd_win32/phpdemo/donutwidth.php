<?php
require_once("../lib/phpchartdir.php");

# query string to determine the donut inner radius, expressed as percentage of outer
# radius
$donutRadius = (int)($_REQUEST["img"]) * 25;

# The data for the pie chart
$data = array(10, 10, 10, 10, 10);

# The labels for the pie chart
$labels = array("Marble", "Wood", "Granite", "Plastic", "Metal");

# Create a PieChart object of size 150 x 120 pixels, with a grey (EEEEEE) background,
# black border and 1 pixel 3D border effect
$c = new PieChart(150, 120, 0xeeeeee, 0x000000, 1);

# Set donut center at (75, 65) and the outer radius to 50 pixels. Inner radius is
# computed according donutWidth
$c->setDonutSize(75, 60, 50, (int)(50 * $donutRadius / 100));

# Add a title to show the donut width
$textBoxObj = $c->addTitle("Inner Radius = $donutRadius %", "Arial", 10);
$textBoxObj->setBackground(0xcccccc, 0);

# Draw the pie in 3D
$c->set3D(12);

# Set the pie data and the pie labels
$c->setData($data, $labels);

# Disable the sector labels by setting the color to Transparent
$c->setLabelStyle("", 8, Transparent);

# output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
