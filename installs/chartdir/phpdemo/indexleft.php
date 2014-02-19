<html>
<script language="Javascript">
var charts = [
    ['', "Pie Chart Samples"],
    ['simplepie.php', "Simple Pie Chart", 1],
    ['threedpie.php', "3D Pie Chart", 1],
    ['multidepthpie.php', "Multi-Depth Pie Chart", 1],
    ['sidelabelpie.php', "Side Label Layout", 1],
    ['circlelabelpie.php', "Circular Label Layout", 2],
    ['legendpie.php', "Pie Chart with Legend", 1],
    ['smallsectorpie.php', "Pie with Small Sectors", 1],
    ['iconpie.php', "Icon Pie Chart", 1],
    ['iconpie2.php', "Icon Pie Chart (2)", 1],
    ['fontpie.php', "Text Style and Colors", 1],
    ['goldpie.php', "Metallic Background Coloring", 1],
    ['colorpie.php', "Coloring and Wallpaper", 4],
    ['borderpie.php', "Sectors with Borders", 1],
    ['threedanglepie.php', "3D Angle", 7],
    ['threeddepthpie.php', "3D Depth", 5],
    ['shadowpie.php', "3D Shadow Mode", 4],
    ['anglepie.php', "Start Angle and Direction", 2],
    ['', ""],
    ['', "Bar Chart Samples"],
    ['simplebar.php', "Simple Bar Chart", 1],
    ['threedbar.php', "3D Bar Chart", 1],
    ['colorbar.php', "Multi-Color Bar Chart", 1],
    ['stackedbar.php', "Stacked Bar Chart", 1],
    ['percentbar.php', "Percentage Bar Chart", 1],
    ['multibar.php', "Multi-Bar Chart", 1],
    ['multistackbar.php', "Multi-Stacked Bar Chart", 1],
    ['depthbar.php', "Depth Bar Chart", 1],
    ['posnegbar.php', "Positive Negative Bars", 1],
    ['hbar.php', "Borderless Bar Chart", 1],
    ['dualhbar.php', "Dual Horizontal Bar Charts", 1],
    ['pareto.php', "Pareto Chart", 1],
    ['gapbar.php', "Bar Gap", 6],
    ['', ""],
    ['', "Line Chart Samples"],
    ['simpleline.php', "Simple Line Chart", 1],
    ['enhancedline.php', "Enhanced Line Chart", 1],
    ['threedline.php', "3D Line Chart", 1],
    ['multiline.php', "Multiline Chart", 1],
    ['symbolline.php', "Symbol Line Chart", 1],
    ['splineline.php', "Spline Line Chart", 1],
    ['stepline.php', "Step Line Chart", 1],
    ['linecompare.php', "Line Comparison", 1],
    ['errline.php', "Line with Error Symbols", 1],
    ['customsymbolline.php', "Custom Symbols", 1],
    ['rotatedline.php', "Rotated Line Chart", 1],
    ['xyline.php', "Arbitrary XY Line Chart", 1],
    ['discontline.php', "Discontinuous Lines", 1],
    ['', ""],
    ['', "Trending and Curve Fitting"],
    ['trendline.php', "Trend Line Chart", 1],
    ['scattertrend.php', "Scatter Trend Chart", 1],
    ['confidenceband.php', "Confidence Band", 1],
    ['curvefitting.php', "General Curve Fitting", 1],
    ['', ""],
    ['', "Scatter/Bubble Charts"],
    ['scatter.php', "Scatter Chart", 1],
    ['scattersymbols.php', "Custom Scatter Symbols", 1],
    ['scatterlabels.php', "Custom Scatter Labels", 1],
    ['bubble.php', "Bubble Chart", 1],
    ['bubblescale.php', "Bubble XY Scaling", 1],
    ['', ""],
    ['', "Area Chart Samples"],
    ['simplearea.php', "Simple Area Chart", 1],
    ['enhancedarea.php', "Enhanced Area Chart", 1],
    ['threedarea.php', "3D Area Chart", 1],
    ['patternarea.php', "Pattern Area Chart", 1],
    ['stackedarea.php', "Stacked Area Chart", 1],
    ['threedstackarea.php', "3D Stacked Area Chart", 1],
    ['percentarea.php', "Percentage Area Chart", 1],
    ['deptharea.php', "Depth Area Chart", 1],
    ['rotatedarea.php', "Rotated Area Chart", 1],
    ['', ""],
    ['', "Box Whisker Charts"],
    ['boxwhisker.php', "Box Whisker Chart", 1],
    ['boxwhisker2.php', "Box Whisker Chart (2)", 1],
    ['gantt.php', "Simple Gantt Chart", 1],
    ['', ""],
    ['', "Finance Charts"],
    ['hloc.php', "Simple HLOC Chart", 1],
    ['candlestick.php', "Simple Candlestick Chart", 1],
    ['finance.php', "Finance Chart (1)", 1],
    ['finance2.php', "Finance Chart (2)", 1],
    ['', ""],
    ['', "Other XY Chart Features"],
    ['markzone.php', "Marks and Zones", 1],
    ['markzone2.php', "Marks and Zones (2)", 1],
    ['yzonecolor.php', "Y Zone Coloring", 1],
    ['xzonecolor.php', "X Zone Coloring", 1],
    ['dualyaxis.php', "Dual Y-Axis", 1],
    ['dualxaxis.php', "Dual X-Axis", 1],
    ['fontxy.php', "Text Style and Colors", 1],
    ['background.php', "Background and Wallpaper", 4],
    ['logaxis.php', "Log Scale Axis", 2],
    ['axisscale.php', "Y-Axis Scaling", 5],
    ['ticks.php', "Tick Density", 2],
    ['', ""],
    ['', "Polar Charts"],
    ['simpleradar.php', "Simple Radar Chart", 1],
    ['multiradar.php', "Multi Radar Chart", 1],
    ['polarline.php', "Polar Line Chart", 1],
    ['polarspline.php', "Polar Spline Chart", 1],
    ['polarscatter.php', "Polar Scatter Chart", 1],
    ['polarbubble.php', "Polar Bubble Chart", 1],
    ['polarzones.php', "Polar Zones", 1],
    ['', ""],
    ['', "Clickable Charts"],
    ['clickbar.php', "Simple Clickable Charts", 0],
    ['jsarea.php', "Javascript Clickable Charts", 0],
    ['customclick.php', "Custom Clickable Objects", 0],
    ['', ""],
    ['', "Working With Database"],
    ['dbdemo1.php', "Database Integration (1)", 0],
    ['dbdemo2.php', "Database Integration (2)", 0],
    ['dbdemo3.php', "Database Clickable Charts", 0],
    ];
function setChart(c)
{
    var doc = top.indexright.document;
    doc.open();
    doc.writeln('<body topmargin="5" leftmargin="5" rightmargin="0" marginwidth="5" marginheight="5">');
    doc.writeln('<div style="font-size:18pt; font-family:verdana; font-weight:bold">' + charts[c][1] + '</div><hr color="#000080">');
    doc.writeln('<a href="viewsource.php?file=' + charts[c][0] + '"><font size="2" face="Verdana">View Chart Source Code</font></a>');
    doc.writeln('<p>');
    for (var i = 0; i < charts[c][2]; ++i)
        doc.writeln('<img src="' + charts[c][0] + '?img=' + i + '">');

    doc.writeln('</p>');
    doc.writeln('</body>');
    doc.close();
}
</script>
<head>
<style type="text/css">
p.demotitle {margin-top:0; margin-bottom:0; padding-left:3; font-family:verdana; font-weight:bold; font-size:9pt; line-height:140%;}
p.demolink {margin-top:0; margin-bottom:0; padding-left:3; padding-top:2; padding-bottom:1; font-family:verdana; font-size:8pt;}
</style>
<body topmargin="0" leftmargin="0" rightmargin="0" marginwidth="0" marginheight="0">
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family:verdana; font-size:8pt;">
<script language="Javascript">
for (var c in charts)
{
    if (charts[c][1] == "")
        document.writeln('<tr><td><p class="demolink">&nbsp;</p></td></tr>');
    if (charts[c][0] == "")
        document.writeln('<tr><td colspan="2" bgcolor="#9999FF"><p class="demotitle">' + charts[c][1] + '</p></td></tr>');
    else if (charts[c][2] != 0)
        document.writeln('<tr valign="top"><td><p class="demolink">&#8226;</p></td><td><p class="demolink"><a href="javascript:;" onclick="setChart(\'' + c + '\');">' + charts[c][1] + '</a></p></td></tr>');
    else
        document.writeln('<tr valign="top"><td><p class="demolink">&#8226;</p></td><td><p class="demolink"><a href="' + charts[c][0] + '" target="indexright">' + charts[c][1] + '</a></p></td></tr>');
}
</script>
</table>
</body>
</html>
