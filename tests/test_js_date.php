<html>
<head>

<script type="text/javascript" language="JavaScript">
<!-- Copyright 2002 Bontrager Connection, LLC

function getCalendarDate()
{
   var months = new Array(13);
   months[0]  = "January";
   months[1]  = "February";
   months[2]  = "March";
   months[3]  = "April";
   months[4]  = "May";
   months[5]  = "June";
   months[6]  = "July";
   months[7]  = "August";
   months[8]  = "September";
   months[9]  = "October";
   months[10] = "November";
   months[11] = "December";
   var now         = new Date();
	 document.write(now);
   var monthnumber = now.getMonth();
   	 document.write(monthnumber);
	 var monthname   = months[monthnumber];
   var monthday    = now.getDate();
	 
   var year        = now.getYear();
   if(year < 2000) { year = year + 1900; }
   var datefromString = (monthnumber+1) +
                    '-' +
                    monthday +
                    '-' +
                    year;
   return dateString;
} // function getCalendarDate()

function getClockTime()
{
   var now    = new Date();
   var hour   = now.getHours();
   var minute = now.getMinutes();
   var second = now.getSeconds();
   var ap = "AM";
   if (hour   > 11) { ap = "PM";             }
   if (hour   > 12) { hour = hour - 12;      }
   if (hour   == 0) { hour = 12;             }
   if (hour   < 10) { hour   = "0" + hour;   }
   if (minute < 10) { minute = "0" + minute; }
   if (second < 10) { second = "0" + second; }
   var timeString = hour +
                    ':' +
                    minute +
                    ':' +
                    second +
                    " " +
                    ap;
   return timeString;
} // function getClockTime()

//-->
</script>

</head>
<body>

<script type="text/javascript" language="JavaScript"><!--
var calendarDate = getCalendarDate();
var clockTime = getClockTime();
document.write('Date is ' + calendarDate);
document.write('<br>');
document.write('Time is ' + clockTime);
//--></script>

</body>
</html>