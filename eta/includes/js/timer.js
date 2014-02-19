var secs
var timerID = null
var timerRunning = false
var delay = 1000

function InitializeTimer()
{
    //secs = <?=rand(5,10)?>
		secs = 4
		refreshval = 1
		
    StopTheClock()
    StartTheTimer()
}

function StopTheClock()
{
    if(timerRunning)
        clearTimeout(timerID)
    timerRunning = false
}

function StartTheTimer()
{
    if (secs==0)
    {
        //StopTheClock()
				//alert("This is a test");
				//Action statements here...
				//document.xtest.submit();
				//document.getElementById("status_app").innerHTML = "";
				var i = Math.round(100000000000*Math.random());
				//ajaxpage('http://buck-pravintest.buckresearch.com/tdw/', 'status_app');
				secs = 4
				timerRunning = true
        		timerID = self.setTimeout("StartTheTimer()", delay)
				if (refreshval == 20) {
					alert(refreshval);
					refreshval = 0;
					window.refresh();
				}
    }
    else
    {
        //self.status = " "
				//alert("This is a test");
				self.status = refreshval
				refreshval = refreshval + 1
        secs = secs - 1
        timerRunning = true
        timerID = self.setTimeout("StartTheTimer()", delay)
    }
}