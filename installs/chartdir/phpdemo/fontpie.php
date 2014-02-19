<?php
include("phpchartdir.php");

#The data for the pie chart
$data = array(25, 18, 15, 12, 8, 30, 35);

#The labels for the pie chart
$labels = array("Labor", "Licenses", "Taxes", "Legal", "Insurance",
    "Facilities", "Production");

#Create a PieChart object of size 480 x 300 pixels
$c = new PieChart(480, 300);

#Set the center of the pie at (150, 150) and the radius to 100 pixels
$c->setPieSize(150, 150, 100);

#Add a title to the pie chart using Monotype Corsiva ("mtcorsva")/20 points/deep
#blue (0x000080) as font
$c->addTitle("Project Cost Breakdown", "mtcorsva.ttf", 20, 128);

#Draw the pie in 3D
$c->set3D();

#Add a legend box using 12 points Times New Romans Bold ("timesbd.ttf") font.
#Set background color to light grey (0xd0d0d0), with a 1 pixel 3D border.
$legendObj = $c->addLegend(340, 80, true, "timesbd.ttf", 12);
$legendObj->setBackground(0xd0d0d0, 0xd0d0d0, 1);

#Set the default font for all sector labels to Impact/8 points/dark green
#(0x008000).
$c->setLabelStyle("impact.ttf", 8, 0x8000);

#Set the pie data and the pie labels
$c->setData($data, $labels);

#Explode the 3rd sector
$c->setExplode(2, 40);

#Use Impact/12 points/red as label font for the 3rd sector
$sectorObj = $c->sector(2);
$sectorObj->setLabelStyle("impact.ttf", 12, 0xff0000);

#Use Arial/8 points/deep blue as label font for the 5th sector. Add a background
#box using the sector fill color (SameAsMainColor), with a black (0x000000) edge
#and 2 pixel 3D border.
$sectorObj = $c->sector(4);
$labelStyleObj = $sectorObj->setLabelStyle("", 8, 0x80);
$labelStyleObj->setBackground(SameAsMainColor, 0x0, 2);

#Use Times New Romans/8 points/light red (0xff9999) as label font for the 6th
#sector. Add a dark blue (0x000080) background box with a 2 pixel 3D border.
$sectorObj = $c->sector(0);
$labelStyleObj = $sectorObj->setLabelStyle("times.ttf", 8, 0xff9999);
$labelStyleObj->setBackground(0x80, Transparent, 2);

#Use Impact/8 points/deep green (0x008000) as label font for 7th sector. Add a
#yellow (0xFFFF00) background box with a black (0x000000) edge.
$sectorObj = $c->sector(6);
$labelStyleObj = $sectorObj->setLabelStyle("impact.ttf", 8, 0x8000);
$labelStyleObj->setBackground(0xffff00, 0x0);

#output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
