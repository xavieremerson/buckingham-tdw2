<html>
<head>
    <title>ChartDirector Realtime Chart Demonstration</title>
    <script language="Javascript" src="cdjcv.js"></script>
</head>
<body leftMargin="0" topMargin="0">
<table cellSpacing="0" cellPadding="0" border="0">
    <tr>
        <td align="right" bgColor="#000088" colSpan="2">
            <div style="padding-bottom:2px; padding-right:3px; font-weight:bold; font-size:10pt; font-style:italic; font-family:Arial;">
                <A style="color:#FFFF00; text-decoration:none" href="http://www.advsofteng.com/">Advanced Software Engineering</a>
            </div>
        </td>
    </tr>
    <tr vAlign="top">
        <td style="border-left:black 1px solid; border-right:black 1px solid; border-bottom:black 1px solid;" width="150" bgColor="#c0c0ff">
            <br><br>
            <div style="padding:10px; font-size:9pt; font-family:Verdana">
                <b>Update Period</b><br>
                <select id="UpdatePeriod" style="width:130px">
                    <option Value="1">1 seconds</option>
                    <option Value="2">2 seconds</option>
                    <option Value="5">5 seconds</option>
                    <option Value="10" Selected>10 seconds</option>
                    <option Value="20">20 seconds</option>
                    <option Value="30">30 seconds</option>
                    <option Value="60">60 seconds</option>
                </select>
            </div>
            <div style="padding:10px; font-size:9pt; font-family:Verdana">
                <b>Time Remaining</b><br>
                <div style="width:130px; border:#888888 1px inset;">
                    <div style="margin:3px" id="TimeRemaining">&nbsp;</div>
                </div>
            </div>
        </td>
        <td>
            <div style="font-weight:bold; font-size:20pt; margin:5px 0px 0px 5px; font-family:Arial">
                ChartDirector Real-Time Chart Demonstration
            </div>
            <hr color="#000088">
            <div style="padding:0px 5px 0px 10px">
                <!-- ****** Here is the image tag for the chart image ****** -->
                <img id="ChartImage1" src="realtimechart.php?chartId=demoChart1">
            </div>
        </td>
    </tr>
</table>
<script>
// The followings is executed once every second
function updateDisplay()
{
    // Utility to get an object by id that works with most browsers
    var getObj = function(id) {    return document.getElementById ? document.getElementById(id) : document.all[id]; }

    // Get the configured update period
    var updatePeriodObj = getObj("UpdatePeriod");
    var updatePeriod = parseInt(updatePeriodObj.value);

    // Subtract 1 second for the remaining time - reload the counter if remaining time is 0
    if (!updatePeriodObj.timeLeft || (updatePeriodObj.timeLeft <= 0))
        updatePeriodObj.timeLeft = updatePeriod - 1;
    else
        updatePeriodObj.timeLeft = Math.min(updatePeriod, updatePeriodObj.timeLeft) - 1;

    // Update the chart if configured time has elasped
    if ((updatePeriodObj.timeLeft <= 0) && window.JsChartViewer)
        JsChartViewer.get('ChartImage1').streamUpdate();

    // Update the display to show remaining time
    getObj("TimeRemaining").innerHTML = updatePeriodObj.timeLeft + ((updatePeriodObj.timeLeft > 1) ? " seconds" : " second");
}
window.setInterval("updateDisplay()", 1000);
</script>
</body>
</html>
