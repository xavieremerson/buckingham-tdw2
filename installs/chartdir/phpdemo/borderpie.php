<?php
include("phpchartdir.php");

#The data for the pie chart
$data = array(25, 18, 15, 12, 8, 30, 35);

#The labels for the pie chart
$labels = array("Labor", "Licenses", "Taxes", "Legal", "Insurance",
    "Facilities", "Production");

#Create a PieChart object of size 360 x 280 pixels
$c = new PieChart(360, 280);

#Set the background color of the chart to silver (silverGradient), and the
#border color to black, with 1 pixel 3D border effect.
$c->setBackground($c->gradientColor($silverGradient), 0, 1);

#Set the center of the pie at (180, 140) and the radius to 100 pixels
$c->setPieSize(180, 140, 100);

#Add a title to the pie chart, using light grey (0xc0c0c0) background and black
#border
$titleObj = $c->addTitle("Project Cost Breakdown");
$titleObj->setBackground(0xc0c0c0, 0x0);

#Draw the pie in 3D
$c->set3D();

#Set the border color of the sectors to black (0x0)
$c->setLineColor(0x0);

#Set the background color of the sector label to the same color as the sector.
#Use a black border.
$labelStyleObj = $c->setLabelStyle();
$labelStyleObj->setBackground(SameAsMainColor, 0x0);

#Set the pie data and the pie labels
$c->setData($data, $labels);

#Explode the 1st sector (index = 0)
$c->setExplode(0);

#output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
