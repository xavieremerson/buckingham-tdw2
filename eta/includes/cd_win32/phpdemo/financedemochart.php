<?php
require_once("../lib/FinanceChart.php");

# Utility to compute modulus for large positive numbers. Although PHP has a built-in fmod
# function, it is only for PHP >= 4.2.0. So we need to define our own fmod function.
function fmod2($a, $b) { return $a - floor($a / $b) * $b; }

#
# Create a finance chart based on user selections, which are encoded as query
# parameters. This code is designed to work with the financedemo HTML form.
#

# The timeStamps, volume, high, low, open and close data
$timeStamps = null;
$volData = null;
$highData = null;
$lowData = null;
$openData = null;
$closeData = null;


#/ <summary>
#/ Get 15 minutes data series for timeStamps, highData, lowData, openData, closeData
#/ and volData.
#/ </summary>
#/ <param name="startDate">The starting date/time for the data series.</param>
#/ <param name="endDate">The ending date/time for the data series.</param>
function get15MinData($ticker, $startDate, $endDate) {
    #
    # In this demo, we use a random number generator to generate the data. In
    # practice, you may get the data from a database or by other means. If you do not
    # have 15 minute data, you may modify the "drawChart" method below to not using
    # 15 minute data.
    #
    generateRandomData($ticker, $startDate, $endDate, 900);
}


#/ <summary>
#/ Get daily data series for timeStamps, highData, lowData, openData, closeData
#/ and volData.
#/ </summary>
#/ <param name="startDate">The starting date/time for the data series.</param>
#/ <param name="endDate">The ending date/time for the data series.</param>
function getDailyData($ticker, $startDate, $endDate) {
    #
    # In this demo, we use a random number generator to generate the data. In
    # practice, you may get the data from a database or by other means.
    #
    generateRandomData($ticker, $startDate, $endDate, 86400);
}


#/ <summary>
#/ Get weekly data series for timeStamps, highData, lowData, openData, closeData
#/ and volData.
#/ </summary>
#/ <param name="startDate">The starting date/time for the data series.</param>
#/ <param name="endDate">The ending date/time for the data series.</param>
function getWeeklyData($ticker, $startDate, $endDate) {
    #
    # If you do not have weekly data, you may call "getDailyData(startDate, endDate)"
    # to get daily data, then call "convertDailyToWeeklyData()" to convert to weekly
    # data.
    #
    generateRandomData($ticker, $startDate, $endDate, 86400 * 7);
}


#/ <summary>
#/ Get monthly data series for timeStamps, highData, lowData, openData, closeData
#/ and volData.
#/ </summary>
#/ <param name="startDate">The starting date/time for the data series.</param>
#/ <param name="endDate">The ending date/time for the data series.</param>
function getMonthlyData($ticker, $startDate, $endDate) {
    #
    # If you do not have weekly data, you may call "getDailyData(startDate, endDate)"
    # to get daily data, then call "convertDailyToMonthlyData()" to convert to
    # monthly data.
    #
    generateRandomData($ticker, $startDate, $endDate, 86400 * 30);
}


#/ <summary>
#/ A random number generator designed to generate realistic financial data.
#/ </summary>
#/ <param name="startDate">The starting date/time for the data series.</param>
#/ <param name="endDate">The ending date/time for the data series.</param>
#/ <param name="resolution">The period of the data series.</param>
function generateRandomData($ticker, $startDate, $endDate, $resolution) {

    global $timeStamps, $volData, $highData, $lowData, $openData, $closeData;

    $db = new FinanceSimulator((int)($ticker), $startDate, $endDate, $resolution);
    $timeStamps = $db->getTimeStamps();
    $highData = $db->getHighData();
    $lowData = $db->getLowData();
    $openData = $db->getOpenData();
    $closeData = $db->getCloseData();
    $volData = $db->getVolData();
}


#/ <summary>
#/ A utility to convert daily to weekly data.
#/ </summary>
function convertDailyToWeeklyData() {
    $tmpArrayMath1 = new ArrayMath($timeStamps);
    aggregateData($tmpArrayMath1->selectStartOfWeek());
}


#/ <summary>
#/ A utility to convert daily to monthly data.
#/ </summary>
function convertDailyToMonthlyData() {
    $tmpArrayMath1 = new ArrayMath($timeStamps);
    aggregateData($tmpArrayMath1->selectStartOfMonth());
}


#/ <summary>
#/ An internal method used to aggregate daily data.
#/ </summary>
function aggregateData(&$aggregator) {

    global $timeStamps, $volData, $highData, $lowData, $openData, $closeData;

    $timeStamps = NTime($aggregator->aggregate(CTime($timeStamps), AggregateFirst));
    $highData = $aggregator->aggregate($highData, AggregateMax);
    $lowData = $aggregator->aggregate($lowData, AggregateMin);
    $openData = $aggregator->aggregate($openData, AggregateFirst);
    $closeData = $aggregator->aggregate($closeData, AggregateLast);
    $volData = $aggregator->aggregate($volData, AggregateSum);
}


#/ <summary>
#/ Create a financial chart according to user selections. The user selections are
#/ encoded in the query parameters.
#/ </summary>
function drawChart() {

    global $timeStamps, $volData, $highData, $lowData, $openData, $closeData;

    # In this demo, we just assume we plot up to the latest time. So end date is now.
    $endDate = chartTime2(time());

    # If the trading day has not yet started (before 9:30am), or if the end date is
    # on on Sat or Sun, we set the end date to 4:00pm of the last trading day
    while ((fmod2($endDate, 86400) < 9 * 3600 + 30 * 60) || (getChartWeekDay($endDate
        ) == 0) || (getChartWeekDay($endDate) == 6)) {
        $endDate = $endDate - fmod2($endDate, 86400) - 86400 + 16 * 3600;
    }

    # The duration selected by the user
    $durationInDays = (int)($_REQUEST["TimeRange"]);

    # Compute the start date by subtracting the duration from the end date.
    $startDate = $endDate;
    if ($durationInDays >= 30) {
        # More or equal to 30 days - so we use months as the unit
        $YMD = getChartYMD($endDate);
        $startMonth = (int)($YMD / 100) % 100 - (int)($durationInDays / 30);
        $startYear = (int)($YMD / 10000);
        while ($startMonth < 1) {
            $startYear = $startYear - 1;
            $startMonth = $startMonth + 12;
        }
        $startDate = chartTime($startYear, $startMonth, 1);
    } else {
        # Less than 30 days - use day as the unit. The starting point of the axis is
        # always at the start of the day (9:30am). Note that we use trading days, so
        # we skip Sat and Sun in counting the days.
        $startDate = $endDate - fmod2($endDate, 86400) + 9 * 3600 + 30 * 60;
        for($i = 1; $i < $durationInDays; ++$i) {
            if (getChartWeekDay($startDate) == 1) {
                $startDate = $startDate - 3 * 86400;
            } else {
                $startDate = $startDate - 86400;
            }
        }
    }

    # The moving average periods selected by the user.
    $avgPeriod1 = 0;
    $avgPeriod1 = (int)($_REQUEST["movAvg1"]);
    $avgPeriod2 = 0;
    $avgPeriod2 = (int)($_REQUEST["movAvg2"]);

    if ($avgPeriod1 < 0) {
        $avgPeriod1 = 0;
    } else if ($avgPeriod1 > 300) {
        $avgPeriod1 = 300;
    }

    if ($avgPeriod2 < 0) {
        $avgPeriod2 = 0;
    } else if ($avgPeriod2 > 300) {
        $avgPeriod2 = 300;
    }

    # We need extra leading data points in order to compute moving averages.
    $extraPoints = 20;
    if ($avgPeriod1 > $extraPoints) {
        $extraPoints = $avgPeriod1;
    }
    if ($avgPeriod2 > $extraPoints) {
        $extraPoints = $avgPeriod2;
    }

    # The data series we want to get.
    $tickerKey = $_REQUEST["TickerSymbol"];

    # In this demo, we can get 15 min, daily, weekly or monthly data depending on the
    # time range.
    $resolution = 86400;
    if ($durationInDays <= 10) {
        # 10 days or less, we assume 15 minute data points are available
        $resolution = 900;

        # We need to adjust the startDate backwards for the extraPoints. We assume
        # 6.5 hours trading time per day, and 5 trading days per week.
        $dataPointsPerDay = 6.5 * 3600 / $resolution;
        $adjustedStartDate = $startDate - fmod2($startDate, 86400) - (int)(
            $extraPoints / $dataPointsPerDay * 7 / 5 + 0.9999999) * 86400 - 2 * 86400
            ;

        # Get the required 15 min data
        get15MinData($tickerKey, $adjustedStartDate, $endDate);

    } else if ($durationInDays >= 4.5 * 360) {
        # 4 years or more - use monthly data points.
        $resolution = 30 * 86400;

        # Adjust startDate backwards to cater for extraPoints
        $YMD = getChartYMD($startDate);
        $currentMonth = (int)($YMD / 100) % 100 - $extraPoints;
        $currentYear = (int)($YMD / 10000);
        while ($currentMonth < 1) {
            $currentYear = $currentYear - 1;
            $currentMonth = $currentMonth + 12;
        }
        $adjustedStartDate = chartTime($currentYear, $currentMonth, 1);

        # Get the required monthly data
        getMonthlyData($tickerKey, $adjustedStartDate, $endDate);

    } else if ($durationInDays >= 1.5 * 360) {
        # 1 year or more - use weekly points.
        $resolution = 7 * 86400;

        # Adjust startDate backwards to cater for extraPoints
        $adjustedStartDate = $startDate - $extraPoints * 7 * 86400 - 6 * 86400;

        # Get the required weekly data
        getWeeklyData($tickerKey, $adjustedStartDate, $endDate);

    } else {
        # Default - use daily points
        $resolution = 86400;

        # Adjust startDate backwards to cater for extraPoints. We multiply the days
        # by 7/5 as we assume 1 week has 5 trading days.
        $adjustedStartDate = $startDate - fmod2($startDate, 86400) - (int)((
            $extraPoints * 7 + 4) / 5) * 86400 - 2 * 86400;

        # Get the required daily data
        getDailyData($tickerKey, $adjustedStartDate, $endDate);
    }

    # We now confirm the actual number of extra points (data points that are before
    # the start date) as inferred using actual data from the database.
    $extraPoints = count($timeStamps);
    for($i = 0; $i < count($timeStamps); ++$i) {
        if ($timeStamps[$i] >= $startDate) {
            $extraPoints = $i;
            break;
        }
    }

    # Check if there is any valid data
    if ($extraPoints >= count($timeStamps)) {
        # No data - just display the no data message.
        $errMsg = new MultiChart(400, 50);
        $errMsg->addTitle2(Center, "No data available for the specified time period",
            "arial.ttf", 10);
        return $errMsg;
    }

    # In some finance chart presentation style, even if the data for the latest day
    # is not fully available, the axis for the entire day will still be drawn, where
    # no data will appear near the end of the axis.
    if ($resolution < 86400) {
        # Add extra points to the axis until it reaches the end of the day. The end
        # of day is assumed to be 16:00 (it depends on the stock exchange).
        $lastTime = $timeStamps[count($timeStamps) - 1];
        $extraTrailingPoints = (int)((16 * 3600 - fmod2($lastTime, 86400)) /
            $resolution);
        for($i = 0; $i < $extraTrailingPoints; ++$i) {
            $timeStamps[] = $lastTime + $resolution * ($i + 1);
        }
    }

    #
    # At this stage, all data is available. We can draw the chart as according to
    # user input.
    #

    #
    # Determine the chart size. In this demo, user can select 4 different chart
    # sizes. Default is the large chart size.
    #
    $width = 780;
    $mainHeight = 250;
    $indicatorHeight = 80;

    $chartSize = $_REQUEST["ChartSize"];
    if ($chartSize == "S") {
        # Small chart size
        $width = 450;
        $mainHeight = 160;
        $indicatorHeight = 60;
    } else if ($chartSize == "M") {
        # Medium chart size
        $width = 620;
        $mainHeight = 210;
        $indicatorHeight = 65;
    } else if ($chartSize == "H") {
        # Huge chart size
        $width = 1000;
        $mainHeight = 320;
        $indicatorHeight = 90;
    }

    # Create the chart object using the selected size
    $m = new FinanceChart($width);

    # Set the data into the chart object
    $m->setData($timeStamps, $highData, $lowData, $openData, $closeData, $volData,
        $extraPoints);

    #
    # We configure the title of the chart. In this demo chart design, we put the
    # company name as the top line of the title with left alignment.
    #
    $m->addPlotAreaTitle(TopLeft, "Random Data $tickerKey");

    # We displays the current date as well as the data resolution on the next line.
    $resolutionText = "";
    if ($resolution == 30 * 86400) {
        $resolutionText = "Monthly";
    } else if ($resolution == 7 * 86400) {
        $resolutionText = "Weekly";
    } else if ($resolution == 86400) {
        $resolutionText = "Daily";
    } else if ($resolution == 900) {
        $resolutionText = "15-min";
    }

    $m->addPlotAreaTitle(BottomLeft, sprintf(
        "<*font=arial.ttf,size=8*>%s - %s chart", $m->formatValue(chartTime2(time()),
        "mmm dd, yyyy"), $resolutionText));

    # A copyright message at the bottom left corner the title area
    $m->addPlotAreaTitle(BottomRight,
        "<*font=arial.ttf,size=8*>(c) Advanced Software Engineering");

    #
    # Set the grid style according to user preference. In this simple demo user
    # interface, user can enable/disable grid lines. The code achieve this by setting
    # the grid color to dddddd (light grey) or Transparent. The plot area background
    # color is set to fffff0 (pale yellow).
    #
    $vGridColor = Transparent;
    if ($_REQUEST["VGrid"] == "1") {
        $vGridColor = 0xdddddd;
    }
    $hGridColor = Transparent;
    if ($_REQUEST["HGrid"] == "1") {
        $hGridColor = 0xdddddd;
    }
    $m->setPlotAreaStyle(0xfffff0, $hGridColor, $vGridColor, $hGridColor, $vGridColor
        );

    #
    # Set log or linear scale according to user preference
    #
    if ($_REQUEST["LogScale"] == "1") {
        $m->setLogScale(true);
    } else {
        $m->setLogScale(false);
    }

    #
    # Add the first techical indicator according. In this demo, we draw the first
    # indicator on top of the main chart.
    #
    addIndicator($m, $_REQUEST["Indicator1"], $indicatorHeight);

    #
    # Add the main chart
    #
    $m->addMainChart($mainHeight);

    #
    # Draw the main chart depending on the chart type the user has selected
    #
    $chartType = $_REQUEST["ChartType"];
    if ($chartType == "Close") {
        $m->addCloseLine(0x000040);
    } else if ($chartType == "TP") {
        $m->addTypicalPrice(0x000040);
    } else if ($chartType == "WC") {
        $m->addWeightedClose(0x000040);
    } else if ($chartType == "Median") {
        $m->addMedianPrice(0x000040);
    }

    #
    # Add moving average lines.
    #
    addMovingAvg($m, $_REQUEST["avgType1"], $avgPeriod1, 0x663300);
    addMovingAvg($m, $_REQUEST["avgType2"], $avgPeriod2, 0x9900ff);

    #
    # Draw the main chart if the user has selected CandleStick or OHLC. We draw it
    # here to make sure it is drawn behind the moving average lines (that is, the
    # moving average lines stay on top.)
    #
    if ($chartType == "CandleStick") {
        $m->addCandleStick(0x33ff33, 0xff3333);
    } else if ($chartType == "OHLC") {
        $m->addHLOC(0x008800, 0xcc0000);
    }

    #
    # Add price band/channel/envelop to the chart according to user selection
    #
    $band = $_REQUEST["Band"];
    if ($band == "BB") {
        $m->addBollingerBand(20, 2, 0x9999ff, 0xc06666ff);
    } else if ($band == "DC") {
        $m->addDonchianChannel(20, 0x9999ff, 0xc06666ff);
    } else if ($band == "Envelop") {
        $m->addEnvelop(20, 0.1, 0x9999ff, 0xc06666ff);
    }

    #
    # Add volume bars to the main chart if necessary
    #
    if ($_REQUEST["Volume"] == "1") {
        $m->addVolBars($indicatorHeight, 0x99ff99, 0xff9999, 0xc0c0c0);
    }

    #
    # Add additional indicators as according to user selection.
    #
    addIndicator($m, $_REQUEST["Indicator2"], $indicatorHeight);
    addIndicator($m, $_REQUEST["Indicator3"], $indicatorHeight);
    addIndicator($m, $_REQUEST["Indicator4"], $indicatorHeight);

    return $m;
}


#/ <summary>
#/ Add a moving average line to the FinanceChart object.
#/ </summary>
#/ <param name="m">The FinanceChart object to add the line to.</param>
#/ <param name="avgType">The moving average type (SMA/EMA/TMA/WMA).</param>
#/ <param name="avgPeriod">The moving average period.</param>
#/ <param name="color">The color of the line.</param>
function addMovingAvg(&$m, $avgType, $avgPeriod, $color) {
    if ($avgPeriod > 1) {
        if ($avgType == "SMA") {
            $m->addSimpleMovingAvg($avgPeriod, $color);
        } else if ($avgType == "EMA") {
            $m->addExpMovingAvg($avgPeriod, $color);
        } else if ($avgType == "TMA") {
            $m->addTriMovingAvg($avgPeriod, $color);
        } else if ($avgType == "WMA") {
            $m->addWeightedMovingAvg($avgPeriod, $color);
        }
    }
}


#/ <summary>
#/ Add an indicator chart to the FinanceChart object. In this demo example, the
#/ indicator parameters (such as the period used to compute RSI, colors of the lines,
#/ etc.) are hard coded to commonly used values. You are welcome to design a more
#/ complex user interface to allow users to set the parameters.
#/ </summary>
#/ <param name="m">The FinanceChart object to add the line to.</param>
#/ <param name="indicator">The selected indicator.</param>
#/ <param name="height">Height of the chart in pixels</param>
function addIndicator(&$m, $indicator, $height) {
    if ($indicator == "RSI") {
        $m->addRSI($height, 14, 0x800080, 20, 0xff6666, 0x6666ff);
    } else if ($indicator == "StochRSI") {
        $m->addStochRSI($height, 14, 0x800080, 30, 0xff6666, 0x6666ff);
    } else if ($indicator == "MACD") {
        $m->addMACD($height, 26, 12, 9, 0x0000ff, 0xff00ff, 0x008000);
    } else if ($indicator == "FStoch") {
        $m->addFastStochastic($height, 14, 3, 0x006060, 0x606000);
    } else if ($indicator == "SStoch") {
        $m->addSlowStochastic($height, 14, 3, 0x006060, 0x606000);
    } else if ($indicator == "ATR") {
        $m->addATR($height, 14, 0x808080, 0x0000ff);
    } else if ($indicator == "ADX") {
        $m->addADX($height, 14, 0x008000, 0x800000, 0x000080);
    } else if ($indicator == "DCW") {
        $m->addDonchianWidth($height, 20, 0x0000ff);
    } else if ($indicator == "BBW") {
        $m->addBollingerWidth($height, 20, 2, 0x0000ff);
    } else if ($indicator == "DPO") {
        $m->addDPO($height, 20, 0x0000ff);
    } else if ($indicator == "PVT") {
        $m->addPVT($height, 0x0000ff);
    } else if ($indicator == "Momentum") {
        $m->addMomentum($height, 12, 0x0000ff);
    } else if ($indicator == "Performance") {
        $m->addPerformance($height, 0x0000ff);
    } else if ($indicator == "ROC") {
        $m->addROC($height, 12, 0x0000ff);
    } else if ($indicator == "OBV") {
        $m->addOBV($height, 0x0000ff);
    } else if ($indicator == "AccDist") {
        $m->addAccDist($height, 0x0000ff);
    } else if ($indicator == "CLV") {
        $m->addCLV($height, 0x0000ff);
    } else if ($indicator == "WilliamR") {
        $m->addWilliamR($height, 14, 0x800080, 30, 0xff6666, 0x6666ff);
    } else if ($indicator == "Aroon") {
        $m->addAroon($height, 14, 0x339933, 0x333399);
    } else if ($indicator == "AroonOsc") {
        $m->addAroonOsc($height, 14, 0x0000ff);
    } else if ($indicator == "CCI") {
        $m->addCCI($height, 20, 0x800080, 100, 0xff6666, 0x6666ff);
    } else if ($indicator == "EMV") {
        $m->addEaseOfMovement($height, 9, 0x006060, 0x606000);
    } else if ($indicator == "MDX") {
        $m->addMassIndex($height, 0x800080, 0xff6666, 0x6666ff);
    } else if ($indicator == "CVolatility") {
        $m->addChaikinVolatility($height, 10, 10, 0x0000ff);
    } else if ($indicator == "COscillator") {
        $m->addChaikinOscillator($height, 0x0000ff);
    } else if ($indicator == "CMF") {
        $m->addChaikinMoneyFlow($height, 21, 0x008000);
    } else if ($indicator == "NVI") {
        $m->addNVI($height, 255, 0x0000ff, 0x883333);
    } else if ($indicator == "PVI") {
        $m->addPVI($height, 255, 0x0000ff, 0x883333);
    } else if ($indicator == "MFI") {
        $m->addMFI($height, 14, 0x800080, 30, 0xff6666, 0x6666ff);
    } else if ($indicator == "PVO") {
        $m->addPVO($height, 26, 12, 9, 0x0000ff, 0xff00ff, 0x008000);
    } else if ($indicator == "PPO") {
        $m->addPPO($height, 26, 12, 9, 0x0000ff, 0xff00ff, 0x008000);
    } else if ($indicator == "UO") {
        $m->addUltimateOscillator($height, 7, 14, 28, 0x800080, 20, 0xff6666,
            0x6666ff);
    } else if ($indicator == "Vol") {
        $m->addVolIndicator($height, 0x99ff99, 0xff9999, 0xc0c0c0);
    } else if ($indicator == "TRIX") {
        $m->addTRIX($height, 12, 0x0000ff);
    }
}

# create the finance chart
$c = drawChart();

# output the chart
header("Content-type: image/png");
print($c->makeChart2(PNG));
?>
