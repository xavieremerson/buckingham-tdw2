<?php
include("phpchartdir.php");

#The data for the chart
$data0 = array(43, 89, 76, 64, 48, 18, 92, 68, 44, 79, 71, 85);
$angles0 = array(45, 96, 169, 258, 15, 30, 330, 260, 60, 75, 110, 140);

$data1 = array(50, 91, 26, 29, 80, 53, 62, 87, 19, 40);
$angles1 = array(230, 210, 240, 310, 179, 250, 244, 199, 89, 160);

$data2 = array(88, 65, 76, 49, 80, 53);
$angles2 = array(340, 310, 340, 210, 30, 300);

#The labels on the angular axis (spokes)
$labels = array("North", "North\nEast", "East", "South\nEast", "South",
    "South\nWest", "West", "North\nWest");

#Create a PolarChart object of size 460 x 460 pixels
$c = new PolarChart(460, 460);

#Add a title to the chart at the top left corner using 15pts Arial Bold Italic
#font
$c->addTitle2(TopLeft, "<*underline=2*>Plants in Wonderland", "arialbi.ttf", 15)
    ;

#Set center of plot area at (230, 240) with radius 180 pixels
$c->setPlotArea(230, 240, 180);

#Set the grid style to circular grid
$c->setGridStyle(false);

#Add a legend box at the top right corner of the chart using 9 pts Arial Bold
#font
$legendObj = $c->addLegend(459, 0, true, "arialbd.ttf", 9);
$legendObj->setAlignment(TopRight);

#Set angular axis as 0 - 360, either 8 spokes
$angularAxisObj = $c->angularAxis();
$angularAxisObj->setLinearScale2(0, 360, $labels);

#Set the radial axis label format
$radialAxisObj = $c->radialAxis();
$radialAxisObj->setLabelFormat("{value} km");

#Add a blue (0xff) polar line layer to the chart using (data0, angle0)
$layer0 = $c->addLineLayer($data0, 0xff, "Immortal Weed");
$layer0->setAngles($angles0);

$layer0->setLineWidth(0);
$layer0->setDataSymbol(TriangleSymbol, 11);

#Add a red (0xff0000) polar line layer to the chart using (data1, angles1)
$layer1 = $c->addLineLayer($data1, 0xff0000, "Precious Flower");
$layer1->setAngles($angles1);

#Disable the line by setting its width to 0, so only the symbols are visible
$layer1->setLineWidth(0);

#Use a 11 pixel diamond data point symbol
$layer1->setDataSymbol(DiamondSymbol, 11);

#Add a green (0x00ff00) polar line layer to the chart using (data2, angles2)
$layer2 = $c->addLineLayer($data2, 0xff00, "Magical Tree");
$layer2->setAngles($angles2);

#Disable the line by setting its width to 0, so only the symbols are visible
$layer2->setLineWidth(0);

#Use a 9 pixel square data point symbol
$layer2->setDataSymbol(SquareSymbol, 9);

#output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
