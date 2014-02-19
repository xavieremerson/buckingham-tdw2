<?php
require_once("../lib/phpchartdir.php");

#
# For demo purpose, we use hard coded data. In real life, the following data could
# come from a database.
#
$revenue = array(4500, 5600, 6300, 8000, 12000, 14000, 16000, 20000, 24000, 28000);
$grossMargin = array(62, 65, 63, 60, 55, 56, 57, 53, 52, 50);
$backLog = array(563, 683, 788, 941, 1334, 1522, 1644, 1905, 2222, 2544);
$labels = array("1996", "1997", "1998", "1999", "2000", "2001", "2002", "2003",
    "2004", "2005");

# Create a XYChart object of size 600 x 360 pixels
$c = new XYChart(600, 360);

# Add a title to the chart using 18pts Times Bold Italic font
$c->addTitle("Annual Revenue for Star Tech", "timesbi.ttf", 18);

# Set the plotarea at (60, 40) and of size 480 x 280 pixels. Use a vertical gradient
# color from light green (eeffee) to dark green (008800) as background. Set border
# and grid lines to white (ffffff).
$c->setPlotArea(60, 40, 480, 280, $c->linearGradientColor(60, 40, 60, 280, 0xeeffee,
    0x008800), -1, 0xffffff, 0xffffff);

# Add a multi-color bar chart layer using the revenue data.
$layer = $c->addBarLayer3($revenue);

# Set cylinder bar shape
$layer->setBarShape(CircleShape);

# Add extra field to the bars. These fields are used for showing additional
# information.
$layer->addExtraField2($grossMargin);
$layer->addExtraField2($backLog);

# Set the labels on the x axis.
$c->xAxis->setLabels($labels);

# In this example, we show the same scale using both axes
$c->syncYAxis();

# Set the axis line to transparent
$c->xAxis->setColors(Transparent);
$c->yAxis->setColors(Transparent);
$c->yAxis2->setColors(Transparent);

# Set the axis label to using 8pt Arial Bold as font
$c->yAxis->setLabelStyle("arialbd.ttf", 8);
$c->yAxis2->setLabelStyle("arialbd.ttf", 8);
$c->xAxis->setLabelStyle("arialbd.ttf", 8);

# Add title to the y axes
$c->yAxis->setTitle("USD (millions)", "arialbd.ttf", 10);
$c->yAxis2->setTitle("USD (millions)", "arialbd.ttf", 10);

# Create the image and save it in a temporary location
$chart1URL = $c->makeSession("chart1");

# Client side Javascript to show detail information "onmouseover"
$showText = "onmouseover='showInfo(\"{xLabel}\", {value}, {field0}, {field1});' ";

# Client side Javascript to hide detail information "onmouseout"
$hideText = "onmouseout='showInfo(null);' ";

# "title" attribute to show tool tip
$toolTip = "title='{xLabel}: US\$ {value|0}M'";

# Create an image map for the chart
$imageMap = $c->getHTMLImageMap("xystub.php", "", "$showText$hideText$toolTip");
?>
<html>
<body topmargin="5" leftmargin="5" rightmargin="0" marginwidth="5" marginheight="5">
<div style="font-size:18pt; font-family:verdana; font-weight:bold">
    Javascript Clickable Chart
</div>
<hr color="#000080">
<div style="font-size:10pt; font-family:verdana; margin-bottom:20">
    <a href="viewsource.php?file=<?php echo $_SERVER["SCRIPT_NAME"]?>">View Source Code</a>
</div>
<img src="getchart.php?<?php echo $chart1URL?>" border="0" usemap="#map1">
<map name="map1">
<?php echo $imageMap?>
</map>
<div id="detailInfo" style="margin-left:60"></div>
<script>
//
//Client side script function to show and hide detail information.
//
function showInfo(year, revenue, grossMargin, backLog) {
    var obj;
    if (document.getElementById)
        //IE 5.x or NS 6.x or above
        obj = document.getElementById('detailInfo');
    else
        //IE 4.x
        obj = document.all['detailInfo'];

    if (!year)
    {
        obj.style.visibility = "hidden";
        return;
    }

    var content = "<table border='1' cellpadding='3' style='font-size:10pt; " +
       "font-family:verdana; background-color:#CCCCFF' width='480'>";
    content += "<tr><td><b>Year</b></td><td width='300'>" + year + "</td></tr>";
    content += "<tr><td><b>Revenue</b></td><td>US$ " + revenue + "M</td></tr>";
    content += "<tr><td><b>Gross Margin</b></td><td>" + grossMargin + "%</td></tr>";
    content += "<tr><td><b>Back Log</b></td><td>US$ " + backLog + "M</td></tr>";
    content += "</table>";

    obj.innerHTML = content;
    obj.style.visibility = "visible";
}
</script>
</body>
</html>
