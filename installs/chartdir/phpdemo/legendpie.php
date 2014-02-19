<?php
include("phpchartdir.php");

#The data for the pie chart
$data = array(25, 18, 15, 12, 8, 30, 35);

#The labels for the pie chart
$labels = array("Labor", "Licenses", "Taxes", "Legal", "Insurance",
    "Facilities", "Production");

#Create a PieChart object of size 450 x 240 pixels
$c = new PieChart(450, 240);

#Set the center of the pie at (150, 100) and the radius to 80 pixels
$c->setPieSize(150, 100, 80);

#Add a title at the bottom of the chart using Arial Bold Italic font
$c->addTitle2(Bottom, "Project Cost Breakdown", "arialbi.ttf");

#Draw the pie in 3D
$c->set3D();

#add a legend box where the top left corner is at (330, 40)
$c->addLegend(330, 40);

#modify the label format for the sectors to $nnnK (pp.pp%)
$c->setLabelFormat("{label} \${value}K\n({percent}%)");

#Set the pie data and the pie labels
$c->setData($data, $labels);

#Explode the 1st sector (index = 0)
$c->setExplode(0);

#output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
