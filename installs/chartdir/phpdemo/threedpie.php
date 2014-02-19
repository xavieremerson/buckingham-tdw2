<?php
include("phpchartdir.php");

#The data for the pie chart
$data = array(25, 18, 15, 12, 8, 30, 35);

#The labels for the pie chart
$labels = array("Labor", "Licenses", "Taxes", "Legal", "Insurance",
    "Facilities", "Production");

#Create a PieChart object of size 360 x 300 pixels
$c = new PieChart(360, 300);

#Set the center of the pie at (180, 140) and the radius to 100 pixels
$c->setPieSize(180, 140, 100);

#Add a title to the pie chart
$c->addTitle("Project Cost Breakdown");

#Draw the pie in 3D
$c->set3D();

#Set the pie data and the pie labels
$c->setData($data, $labels);

#Explode the 1st sector (index = 0)
$c->setExplode(0);

#output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
