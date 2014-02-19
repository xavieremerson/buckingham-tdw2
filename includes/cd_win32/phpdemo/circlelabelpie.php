<?php
require_once("../lib/phpchartdir.php");

# The data for the pie chart
$data = array(25, 18, 15, 12, 30, 35);

# The labels for the pie chart
$labels = array("Labor", "Licenses", "Taxes", "Legal", "Facilities", "Production");

# Create a PieChart object of size 300 x 300 pixels
$c = new PieChart(300, 300);

if ($_REQUEST["img"] == "0") {
#============================================================
#    Draw a pie chart where the label is on top of the pie
#============================================================

    # Set the center of the pie at (150, 150) and the radius to 120 pixels
    $c->setPieSize(150, 150, 120);

    # Set the label position to -40 pixels from the perimeter of the pie (-ve means
    # label is inside the pie)
    $c->setLabelPos(-40);

} else {
#============================================================
#    Draw a pie chart where the label is outside the pie
#============================================================

    # Set the center of the pie at (150, 150) and the radius to 80 pixels
    $c->setPieSize(150, 150, 80);

    # Set the sector label position to be 20 pixels from the pie. Use a join line to
    # connect the labels to the sectors.
    $c->setLabelPos(20, LineColor);

}

# Set the label format to three lines, showing the sector name, value, and
# percentage. The value 999 will be formatted as US$999K.
$c->setLabelFormat("{label}\nUS\${value}K\n({percent}%)");

# Set the pie data and the pie labels
$c->setData($data, $labels);

# Explode the 1st sector (index = 0)
$c->setExplode(0);

# output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
