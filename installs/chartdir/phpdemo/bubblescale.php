<?php
include("phpchartdir.php");

#The XY points for the bubble chart. The bubble chart has independent bubble
#size on the X and Y direction.
$dataX0 = array(1000, 1500, 1700);
$dataY0 = array(25, 20, 65);
$dataZX0 = array(500, 200, 600);
$dataZY0 = array(15, 30, 20);

$dataX1 = array(500, 1000, 1300);
$dataY1 = array(35, 50, 75);
$dataZX1 = array(800, 300, 500);
$dataZY1 = array(8, 27, 25);

$dataX2 = array(150, 300);
$dataY2 = array(20, 60);
$dataZX2 = array(160, 400);
$dataZY2 = array(30, 20);

#Create a XYChart object of size 450 x 420 pixels
$c = new XYChart(450, 420);

#Set the plotarea at (55, 65) and of size 350 x 300 pixels, with a light grey
#border (0xc0c0c0). Turn on both horizontal and vertical grid lines with light
#grey color (0xc0c0c0)
$c->setPlotArea(55, 65, 350, 300, -1, -1, 0xc0c0c0, 0xc0c0c0, -1);

#Add a legend box at (50, 30) (top of the chart) with horizontal layout. Use 12
#pts Times Bold Italic font. Set the background and border color to Transparent.
$legendObj = $c->addLegend(50, 30, false, "timesbi.ttf", 12);
$legendObj->setBackground(Transparent);

#Add a title to the chart using 18 pts Times Bold Itatic font.
$c->addTitle("Plasma Battery Comparison", "timesbi.ttf", 18);

#Add titles to the axes using 12 pts Arial Bold Italic font
$c->yAxis->setTitle("Operating Current", "arialbi.ttf", 12);
$c->xAxis->setTitle("Operating Voltage", "arialbi.ttf", 12);

#Set the axes line width to 3 pixels
$c->xAxis->setWidth(3);
$c->yAxis->setWidth(3);

#Add (dataX0, dataY0) as a standard scatter layer, and also as a "bubble"
#scatter layer, using circles as symbols. The "bubble" scatter layer has symbol
#size modulated by (dataZX0, dataZY0) using the scale on the x and y axes.
$c->addScatterLayer($dataX0, $dataY0, "Vendor A", CircleSymbol, 9, 0xff3333,
    0xff3333);
$scatterLayerObj = $c->addScatterLayer($dataX0, $dataY0, "", CircleSymbol, 9,
    0x80ff3333, 0x80ff3333);
$scatterLayerObj->setSymbolScale($dataZX0, XAxisScale, $dataZY0, YAxisScale);

#Add (dataX1, dataY1) as a standard scatter layer, and also as a "bubble"
#scatter layer, using squares as symbols. The "bubble" scatter layer has symbol
#size modulated by (dataZX1, dataZY1) using the scale on the x and y axes.
$c->addScatterLayer($dataX1, $dataY1, "Vendor B", SquareSymbol, 7, 0x3333ff,
    0x3333ff);
$scatterLayerObj = $c->addScatterLayer($dataX1, $dataY1, "", SquareSymbol, 9,
    0x803333ff, 0x803333ff);
$scatterLayerObj->setSymbolScale($dataZX1, XAxisScale, $dataZY1, YAxisScale);

#Add (dataX2, dataY2) as a standard scatter layer, and also as a "bubble"
#scatter layer, using diamonds as symbols. The "bubble" scatter layer has symbol
#size modulated by (dataZX2, dataZY2) using the scale on the x and y axes.
$c->addScatterLayer($dataX2, $dataY2, "Vendor C", DiamondSymbol, 9, 0xff00,
    0xff00);
$scatterLayerObj = $c->addScatterLayer($dataX2, $dataY2, "", DiamondSymbol, 9,
    0x8033ff33, 0x8033ff33);
$scatterLayerObj->setSymbolScale($dataZX2, XAxisScale, $dataZY2, YAxisScale);

#output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
