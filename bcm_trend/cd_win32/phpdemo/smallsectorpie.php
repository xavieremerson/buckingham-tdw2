<?php
require_once("../lib/phpchartdir.php");

# The data for the pie chart
$data = array(35, 30, 25, 7, 6, 5, 4, 3, 2, 1);

# The labels for the pie chart
$labels = array("Labor", "Production", "Facilities", "Taxes", "Misc", "Legal",
    "Insurance", "Licenses", "Transport", "Interest");

# Create a PieChart object of size 560 x 270 pixels, with a golden background and a 1
# pixel 3D border
$c = new PieChart(560, 270, goldColor(), -1, 1);

# Add a title box using 15 pts Times Bold Italic font and metallic pink background
# color
$textBoxObj = $c->addTitle("Project Cost Breakdown", "timesbi.ttf", 15);
$textBoxObj->setBackground(metalColor(0xff9999));

# Set the center of the pie at (280, 135) and the radius to 110 pixels
$c->setPieSize(280, 135, 110);

# Draw the pie in 3D with 20 pixels 3D depth
$c->set3D(20);

# Use the side label layout method
$c->setLabelLayout(SideLayout);

# Set the label box background color the same as the sector color, with glass effect,
# and with 5 pixels rounded corners
$t = $c->setLabelStyle();
$t->setBackground(SameAsMainColor, Transparent, glassEffect());
$t->setRoundedCorners(5);

# Set the border color of the sector the same color as the fill color. Set the line
# color of the join line to black (0x0)
$c->setLineColor(SameAsMainColor, 0x000000);

# Set the start angle to 135 degrees may improve layout when there are many small
# sectors at the end of the data array (that is, data sorted in descending order). It
# is because this makes the small sectors position near the horizontal axis, where
# the text label has the least tendency to overlap. For data sorted in ascending
# order, a start angle of 45 degrees can be used instead.
$c->setStartAngle(135);

# Set the pie data and the pie labels
$c->setData($data, $labels);

# output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
