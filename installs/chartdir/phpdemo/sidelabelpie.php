<?php
include("phpchartdir.php");

#The data for the pie chart
$data = array(25, 18, 15, 12, 8, 30, 35);

#The labels for the pie chart
$labels = array("Labor", "Licenses", "Taxes", "Legal", "Insurance",
    "Facilities", "Production");

#Create a PieChart object of size 500 x 230 pixels
$c = new PieChart(500, 230);

#Set the center of the pie at (250, 120) and the radius to 100 pixels
$c->setPieSize(250, 120, 100);

#Add a title box using 14 points Times Bold Italic as font
$c->addTitle("Project Cost Breakdown", "timesbi.ttf", 14);

#Draw the pie in 3D
$c->set3D();

#Use the side label layout method
$c->setLabelLayout(SideLayout);

#Set the pie data and the pie labels
$c->setData($data, $labels);

#output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
