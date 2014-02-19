<html>
<head>
<title>ChartDirector Financial Chart Demonstration</title>
<script language="Javascript">

//Cross browser method to get an object
function getObject(id)
{
    if (document.getElementById)
        //IE 5.x or NS 6.x or above
        return document.getElementById(id);
    else if (document.all)
        //IE 4.x
        return document.all[id];
    else
        //Netscape 4.x
        return document[id];
}

//Update the chart according to user selection
function updateChart()
{
    //
    //we encode the values of all form elements as query parameters
    //
    var elements = getObject("Form1").elements;
    var url = "financedemochart.php?";
    for (var i = 0; i < elements.length; ++i)
    {
        var e = elements[i];
        if (e.type == "checkbox")
            url = url + e.id + "=" + (e.checked ? "1" : "0") + "&";
        else
            url = url + e.id + "=" + escape(e.value) + "&";
    }

    //Now we update the URL of the image to update the chart
    getObject("ChartImage").src = url;
}
</script>
</head>
<body leftMargin="0" topMargin="0" onLoad="updateChart();">
<table cellSpacing="0" cellPadding="0" border="0">
    <tr>
        <td align="right" bgColor="#000088" colSpan="2">
            <div style="padding-right:3px; padding-bottom:2px; font-family:arial; font-size:10pt; font-weight:bold; font-style:italic">
                <a style="color:#ffff00; text-decoration:none" href="http://www.advsofteng.com">
                    Advanced Software Engineering
                </a>
            </div>
        </td>
    </tr>
    <tr valign="top">
        <td width="150" bgColor="#bbddff">
            <form id="Form1" action="javascript:updateChart()">
            <div style="font-size:9pt; margin:10px 5px; font-family:verdana">
                <b>Ticker Symbol</b><br>
                <select id="TickerSymbol" name="TickerSymbol" style="width:140px; height:20px">
                    <option value="9">Random Data 9</option>
                    <option value="99">Random Data 99</option>
                    <option value="999">Random Data 999</option>
                    <option value="1">Random Data 1</option>
                    <option value="11">Random Data 11</option>
                    <option value="111">Random Data 111</option>
                    <option value="5">Random Data 5</option>
                    <option value="55">Random Data 55</option>
                    <option value="555">Random Data 555</option>
                    <option value="159">Random Data 159</option>
                </select>
            </div>
            <div style="font-size:9pt; margin:10px 5px; font-family:verdana">
                <b>Time Period</b><br>
                <select id="TimeRange" name="TimeRange" style="width:140px; height:20px">
                    <option value="1">1 day</option>
                    <option value="2">2 days</option>
                    <option value="5">5 days</option>
                    <option value="10">10 days</option>
                    <option value="30">1 month</option>
                    <option value="60">2 months</option>
                    <option value="90">3 months</option>
                    <option value="180" selected>6 months</option>
                    <option value="360">1 year</option>
                    <option value="720">2 years</option>
                    <option value="1080">3 years</option>
                    <option value="1440">4 years</option>
                    <option value="1800">5 years</option>
                    <option value="3600">10 years</option>
                </select>
            </div>
            <div style="font-size:9pt; margin:10px 5px; font-family:verdana">
                <b>Chart Size</b><br>
                <select id="ChartSize" name="ChartSize" style="width:140px; height:20px">
                    <option value="S">Small</option>
                    <option value="M">Medium</option>
                    <option value="L" selected>Large</option>
                    <option value="H">Huge</option>
                </select><br>
                <input id="Volume" name="Volume" type="checkbox" checked><label for="Volume">Show Volume Bars</label><br>
                <input id="VGrid" name="VGrid" type="checkbox" checked><label for="VGrid">Vertical Grid</label><br>
                <input id="HGrid" name="HGrid" type="checkbox" checked><label for="HGrid">Horizontal Grid</label><br>
                <input id="LogScale" name="LogScale" type="checkbox"><label for="LogScale">Log Scale</label><br>
            </div>
            <div style="font-size:9pt; margin:10px 5px; font-family:verdana">
                <b>Chart Type</b><br>
                <select id=ChartType style="width:140px; height:20px" name=ChartType>
                    <option value="None">None</option>
                    <option value="CandleStick" selected>CandleStick</option>
                    <option value="Close">Closing Price</option>
                    <option value="Median">Median Price</option>
                    <option value="OHLC">OHLC</option>
                    <option value="TP">Typical Price</option>
                    <option value="WC">Weighted Close</option>
                </select>
            </div>
            <div style="font-size:9pt; margin:10px 5px; font-family:verdana">
                <b>Price Band</b><br>
                <select id="Band" name="Band" style="width:140px; height:20px">
                    <option value="None">None</option>
                    <option value="BB" selected>Bollinger Band</option>
                    <option value="DC">Donchain Channel</option>
                    <option value="Envelop">Envelop (SMA 20 +/- 10%)</option>
                </select>
            </div>
            <div style="font-size:9pt; margin:10px 5px; font-family:verdana">
                <b>Moving Averages</b><br>
                <nobr><select id="avgType1" name="avgType1" style="width:105px; height:20px">
                    <option value="None">None</option>
                    <option value="SMA" selected>Simple</option>
                    <option value="EMA">Exponential</option>
                    <option value="TMA">Triangular</option>
                    <option value="WMA">Weighted</option>
                </select>
                <input id="movAvg1" name="movAvg1" style="width:30px; height:20px" value="10"></nobr><br>
                <nobr><select id="avgType2" name="avgType2" style="width:105px; height:20px">
                    <option value="None">None</option>
                    <option value="SMA" selected>Simple</option>
                    <option value="EMA">Exponential</option>
                    <option value="TMA">Triangular</option>
                    <option value="WMA">Weighted</option>
                </select>
                <input id="movAvg2" name="movAvg2" style="width:30px; height:20px" value="25"></nobr><br>
            </div>
            <div style="font-size:9pt; margin:10px 5px; font-family:verdana">
                <b>Technical Indicators</b><br>
                <select id="Indicator1" name="Indicator1" style="width:140px; height:20px">
                    <option value="None">None</option>
                    <option value="AccDist">Accumulation/Distribution</option>
                    <option value="AroonOsc">Aroon Oscillator</option>
                    <option value="Aroon">Aroon Up/Down</option>
                    <option value="ADX">Avg Directional Index</option>
                    <option value="ATR">Avg True Range</option>
                    <option value="BBW">Bollinger Band Width</option>
                    <option value="CMF">Chaikin Money Flow</option>
                    <option value="COscillator">Chaikin Oscillator</option>
                    <option value="CVolatility">Chaikin Volatility</option>
                    <option value="CLV">Close Location Value</option>
                    <option value="CCI">Commodity Channel Index</option>
                    <option value="DPO">Detrended Price Osc</option>
                    <option value="DCW">Donchian Channel Width</option>
                    <option value="EMV">Ease of Movement</option>
                    <option value="FStoch">Fast Stochastic</option>
                    <option value="MACD">MACD</option>
                    <option value="MDX">Mass Index</option>
                    <option value="Momentum">Momentum</option>
                    <option value="MFI">Money Flow Index</option>
                    <option value="NVI">Neg Volume Index</option>
                    <option value="OBV">On Balance Volume</option>
                    <option value="Performance">Performance</option>
                    <option value="PPO">% Price Oscillator</option>
                    <option value="PVO">% Volume Oscillator</option>
                    <option value="PVI">Pos Volume Index</option>
                    <option value="PVT">Price Volume Trend</option>
                    <option value="ROC">Rate of Change</option>
                    <option value="RSI" selected>RSI</option>
                    <option value="SStoch">Slow Stochastic</option>
                    <option value="StochRSI">StochRSI</option>
                    <option value="TRIX">TRIX</option>
                    <option value="UO">Ultimate Oscillator</option>
                    <option value="Vol">Volume</option>
                    <option value="WilliamR">William's %R</option>
                </select><br>
                <select id="Indicator2" name="Indicator2" style="width:140px; height:20px">
                    <option value="None">None</option>
                    <option value="AccDist">Accumulation/Distribution</option>
                    <option value="AroonOsc">Aroon Oscillator</option>
                    <option value="Aroon">Aroon Up/Down</option>
                    <option value="ADX">Avg Directional Index</option>
                    <option value="ATR">Avg True Range</option>
                    <option value="BBW">Bollinger Band Width</option>
                    <option value="CMF">Chaikin Money Flow</option>
                    <option value="COscillator">Chaikin Oscillator</option>
                    <option value="CVolatility">Chaikin Volatility</option>
                    <option value="CLV">Close Location Value</option>
                    <option value="CCI">Commodity Channel Index</option>
                    <option value="DPO">Detrended Price Osc</option>
                    <option value="DCW">Donchian Channel Width</option>
                    <option value="EMV">Ease of Movement</option>
                    <option value="FStoch">Fast Stochastic</option>
                    <option value="MACD" selected>MACD</option>
                    <option value="MDX">Mass Index</option>
                    <option value="Momentum">Momentum</option>
                    <option value="MFI">Money Flow Index</option>
                    <option value="NVI">Neg Volume Index</option>
                    <option value="OBV">On Balance Volume</option>
                    <option value="Performance">Performance</option>
                    <option value="PPO">% Price Oscillator</option>
                    <option value="PVO">% Volume Oscillator</option>
                    <option value="PVI">Pos Volume Index</option>
                    <option value="PVT">Price Volume Trend</option>
                    <option value="ROC">Rate of Change</option>
                    <option value="RSI">RSI</option>
                    <option value="SStoch">Slow Stochastic</option>
                    <option value="StochRSI">StochRSI</option>
                    <option value="TRIX">TRIX</option>
                    <option value="UO">Ultimate Oscillator</option>
                    <option value="Vol">Volume</option>
                    <option value="WilliamR">William's %R</option>
                </select><br>
                <select id="Indicator3" name="Indicator3" style="width:140px; height:20px">
                    <option value="None" selected>None</option>
                    <option value="AccDist">Accumulation/Distribution</option>
                    <option value="AroonOsc">Aroon Oscillator</option>
                    <option value="Aroon">Aroon Up/Down</option>
                    <option value="ADX">Avg Directional Index</option>
                    <option value="ATR">Avg True Range</option>
                    <option value="BBW">Bollinger Band Width</option>
                    <option value="CMF">Chaikin Money Flow</option>
                    <option value="COscillator">Chaikin Oscillator</option>
                    <option value="CVolatility">Chaikin Volatility</option>
                    <option value="CLV">Close Location Value</option>
                    <option value="CCI">Commodity Channel Index</option>
                    <option value="DPO">Detrended Price Osc</option>
                    <option value="DCW">Donchian Channel Width</option>
                    <option value="EMV">Ease of Movement</option>
                    <option value="FStoch">Fast Stochastic</option>
                    <option value="MACD">MACD</option>
                    <option value="MDX">Mass Index</option>
                    <option value="Momentum">Momentum</option>
                    <option value="MFI">Money Flow Index</option>
                    <option value="NVI">Neg Volume Index</option>
                    <option value="OBV">On Balance Volume</option>
                    <option value="Performance">Performance</option>
                    <option value="PPO">% Price Oscillator</option>
                    <option value="PVO">% Volume Oscillator</option>
                    <option value="PVI">Pos Volume Index</option>
                    <option value="PVT">Price Volume Trend</option>
                    <option value="ROC">Rate of Change</option>
                    <option value="RSI">RSI</option>
                    <option value="SStoch">Slow Stochastic</option>
                    <option value="StochRSI">StochRSI</option>
                    <option value="TRIX">TRIX</option>
                    <option value="UO">Ultimate Oscillator</option>
                    <option value="Vol">Volume</option>
                    <option value="WilliamR">William's %R</option>
                </select><br>
                <select id="Indicator4" name="Indicator4" style="width:140px; height:20px">
                    <option value="None" selected>None</option>
                    <option value="AccDist">Accumulation/Distribution</option>
                    <option value="AroonOsc">Aroon Oscillator</option>
                    <option value="Aroon">Aroon Up/Down</option>
                    <option value="ADX">Avg Directional Index</option>
                    <option value="ATR">Avg True Range</option>
                    <option value="BBW">Bollinger Band Width</option>
                    <option value="CMF">Chaikin Money Flow</option>
                    <option value="COscillator">Chaikin Oscillator</option>
                    <option value="CVolatility">Chaikin Volatility</option>
                    <option value="CLV">Close Location Value</option>
                    <option value="CCI">Commodity Channel Index</option>
                    <option value="DPO">Detrended Price Osc</option>
                    <option value="DCW">Donchian Channel Width</option>
                    <option value="EMV">Ease of Movement</option>
                    <option value="FStoch">Fast Stochastic</option>
                    <option value="MACD">MACD</option>
                    <option value="MDX">Mass Index</option>
                    <option value="Momentum">Momentum</option>
                    <option value="MFI">Money Flow Index</option>
                    <option value="NVI">Neg Volume Index</option>
                    <option value="OBV">On Balance Volume</option>
                    <option value="Performance">Performance</option>
                    <option value="PPO">% Price Oscillator</option>
                    <option value="PVO">% Volume Oscillator</option>
                    <option value="PVI">Pos Volume Index</option>
                    <option value="PVT">Price Volume Trend</option>
                    <option value="ROC">Rate of Change</option>
                    <option value="RSI">RSI</option>
                    <option value="SStoch">Slow Stochastic</option>
                    <option value="StochRSI">StochRSI</option>
                    <option value="TRIX">TRIX</option>
                    <option value="UO">Ultimate Oscillator</option>
                    <option value="Vol">Volume</option>
                    <option value="WilliamR">William's %R</option>
                </select>
            </div>
            <div style="font-size:9pt; font-family:verdana; text-align:center">
                <input id="Button1" name="Button1" type="submit" value="Update Chart">
            </div>
            </form>
        </td>
        <td>
            <div style="font-weight:bold;  font-family:arial; font-size:20pt; margin:5px 0px 0px 5px">
                ChartDirector Financial Chart Demonstration
            </div>
            <hr color="#000088">
            <br>
            <img id="ChartImage" align="top" border="0">
        </td>
    </tr>
</table>
</body>
</html>
