																<td width="200" valign="top">		
<!--+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++-->
<link type="text/css" rel="stylesheet" href="includes/yui/build/reset/reset.css">
<link type="text/css" rel="stylesheet" href="includes/yui/build/fonts/fonts.css">
<link type="text/css" rel="stylesheet" href="includes/yui/build/logger/assets/logger.css">
<link type="text/css" rel="stylesheet" href="includes/yui/docs/assets/dpSyntaxHighlighter.css">

<style type="text/css">
    #tickersmod {position:relative;}
    #tickersautocomplete, #tickersautocomplete2 {position:absolute;width:7.5em;margin-bottom:1em;}/* set width of widget here*/
    #tickersautocomplete {z-index:9000} /* for IE z-index of absolute divs inside relative divs issue 404040*/
    #tickersinput, #tickersinput2 {width:100%;height:1.8em;z-index:0;}
    #tickerscontainer, #tickerscontainer2 {position:absolute;top:1.8em;width:100%}
    #tickerscontainer .yui-ac-content, #tickerscontainer2 .yui-ac-content {position:absolute;width:100%;border:1px solid #4040FF;background:#fff;overflow:hidden;z-index:9050;}
    #tickerscontainer .yui-ac-shadow, #tickerscontainer2 .yui-ac-shadow {position:absolute;margin:.3em;width:100%;background:#5BA1FF;z-index:9049;}
    #tickerscontainer ul, #tickerscontainer2 ul {padding:5px 0;width:100%;}
    #tickerscontainer li, #tickerscontainer2 li {padding:0 5px;cursor:default;white-space:nowrap;}
    #tickerscontainer li.yui-ac-highlight, #tickerscontainer2 li.yui-ac-highlight {background:#B2DEFA;}
    #tickerscontainer li.yui-ac-prehighlight,#tickerscontainer2 li.yui-ac-prehighlight {background:#CFF2FF;}
</style>
</head>

<div id="bd">
    <!-- AutoComplete begins -->
    <div id="tickersmod">
            <div id="tickersautocomplete2">
						 &nbsp;&nbsp;&nbsp;<input type="text" id="tickersinput2" name="tickersinput2"><br>
						   &nbsp;&nbsp;&nbsp;<div id="tickerscontainer2"></div>
            </div>
    </div>
    <!-- AutoComplete ends -->
</div>
<!-- Content ends -->

<!-- Libary begins -->
<script type="text/javascript" src="includes/yui/build/yahoo/yahoo.js"></script>
<script type="text/javascript" src="includes/yui/build/dom/dom.js"></script>
<script type="text/javascript" src="includes/yui/build/event/event-debug.js"></script>
<script type="text/javascript" src="includes/yui/build/animation/animation.js"></script>
<script type="text/javascript" src="includes/yui/build/autocomplete/autocomplete-debug.js"></script>
<script type="text/javascript" src="includes/yui/build/logger/logger.js"></script>
<!-- Library ends -->



<!-- In-memory JS array begins-->
<script type="text/javascript">
var tickersArray = [
["^ALL^","ALL SYMBOLS"]
<?
	$query_sel_symbol = "SELECT symbol, description
											 FROM sec_master 
											 ORDER BY symbol";
	$result_sel_symbol = mysql_query($query_sel_symbol) or die(mysql_error());
	while($row_sel_symbol = mysql_fetch_array($result_sel_symbol))
	{
	echo ', ["'.$row_sel_symbol["symbol"].'", "'.$row_sel_symbol["description"].'"]'."\n";	
	}
?>
//, ["AB", "Alliancebernstein"]
];
</script>
<!-- In-memory JS array ends-->


<script type="text/javascript">
YAHOO.example.ACJSArray = function() {
    //var mylogger;
    var oACDS,oACDS2;
    var oAutoComp,oAutoComp2;
    return {
        init: function() {

						//Logger
            //mylogger = new YAHOO.widget.LogReader("logger");
						
            // Instantiate second JS Array DataSource
            oACDS2 = new YAHOO.widget.DS_JSArray(tickersArray);

            // Instantiate second AutoComplete
            oAutoComp2 = new YAHOO.widget.AutoComplete('tickersinput2','tickerscontainer2', oACDS2);
            oAutoComp2.queryDelay = 0;
            oAutoComp2.prehighlightClassName = "yui-ac-prehighlight";
            oAutoComp2.typeAhead = true;
            oAutoComp2.useShadow = true;
            oAutoComp2.forceSelection = true;
            oAutoComp2.formatResult = function(oResultItem, sQuery) {
                var sMarkup = oResultItem[0] + " (" + oResultItem[1] + ")";
                return (sMarkup);
            };
        },

        validateForm: function() {
            // Validate form inputs here
            return false;
        }
    };
}();

YAHOO.util.Event.addListener(this,'load',YAHOO.example.ACJSArray.init);
</script>
<script type="text/javascript" src="includes/yui/docs/assets/dpSyntaxHighlighter.js"></script>
<script type="text/javascript">
dp.SyntaxHighlighter.HighlightAll('code');
</script>
<!--++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++-->																
</td>
