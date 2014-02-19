<?php
require_once("../lib/phpchartdir.php");

# Get the selected year and month
$selectedYear = (int)($_REQUEST["year"]);
$selectedMonth = (int)($_REQUEST["x"]) + 1;

#
# In this demo, we just split the monthly revenue into 3 parts using random ratios.
# In real life, the data probably can come from a database based on selectedYear and
# selectedMonth.
#

# Get the monthly revenue
$monthlyRevenue = $_REQUEST["value"];

# Split into 3 parts
srand($selectedMonth * 2000 + $selectedYear);
$data = array_pad(array(), 4, 0);
$data[0] = (rand() / getrandmax() * 0.1 + 0.3) * $monthlyRevenue;
$data[1] = (rand() / getrandmax() * 0.1 + 0.2) * ($monthlyRevenue - $data[0]);
$data[2] = (rand() / getrandmax() * 0.4 + 0.3) * ($monthlyRevenue - $data[0] - $data[
    1]);
$data[3] = $monthlyRevenue - $data[0] - $data[1] - $data[2];

# The labels for the pie chart
$labels = array("Services", "Hardware", "Software", "Others");

# Create a PieChart object of size 600 x 240 pixels
$c = new PieChart(600, 280);

# Set the center of the pie at (300, 140) and the radius to 120 pixels
$c->setPieSize(300, 140, 120);

# Add a title to the pie chart using 18 pts Times Bold Italic font
$c->addTitle("Revenue Breakdown for $selectedMonth/$selectedYear", "timesbi.ttf", 18)
    ;

# Draw the pie in 3D with 20 pixels 3D depth
$c->set3D(20);

# Set label format to display sector label, value and percentage in two lines
$c->setLabelFormat("{label}<*br*>\${value|2}M ({percent}%)");

# Set label style to 10 pts Arial Bold Italic font. Set background color to the same
# as the sector color, with reduced-glare glass effect and rounded corners.
$t = $c->setLabelStyle("arialbi.ttf", 10);
$t->setBackground(SameAsMainColor, Transparent, glassEffect(ReducedGlare));
$t->setRoundedCorners();

# Use side label layout method
$c->setLabelLayout(SideLayout);

# Set the pie data and the pie labels
$c->setData($data, $labels);

# Create the image and save it in a temporary location
$chart1URL = $c->makeSession("chart1");

# Create an image map for the chart
$imageMap = $c->getHTMLImageMap("piestub.php", "", "title='{label}:US\$ {value|2}M'")
    ;
?>
<html>
<body topmargin="5" leftmargin="5" rightmargin="0" marginwidth="5" marginheight="5">
<div style="font-size:18pt; font-family:verdana; font-weight:bold">
    Simple Clickable Pie Chart
</div>
<hr color="#000080">
<div style="font-size:10pt; font-family:verdana; margin-bottom:20">
    <a href="viewsource.php?file=<?php echo $_SERVER["SCRIPT_NAME"]?>">View Source Code</a>
</div>
<img src="getchart.php?<?php echo $chart1URL?>" border="0" usemap="#map1">
<map name="map1">
<?php echo $imageMap?>
</map>
</body>
</html>
