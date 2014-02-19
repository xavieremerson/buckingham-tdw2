<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
table.demo
{
	font-size: 13px;
	border: 1px solid #0000C0;
}

table.demo th
{
	font-weight: bold;
	background-color: #C0C0FF;
	padding: 2px 5px 2px 5px;
}

table.demo td
{
	font-size: 12px;
	padding: 2px 5px 2px 5px;
/*	border-top: 1px dotted #C0C0FF;*/
}

tr.row1
{
	background-color: #FFFFFF;
}

tr.row2
{
	background-color: #E0E0FF;
}
</style>
</head>

<body bgcolor="#FFFFFF" text="#000000">

<a name='features'></a>
<a name='example'></a>
<h2>Example:</h2>
<p>Current sorting order of the second table is stored in a cookie.
If you refresh the page the table will have exactly the same sorting order
as it had before refreshing. Also, the second table has custom sorting
up/down characters.
<p>
<script language="javascript" src="includes/javascript/gs_sortable.js"></script>
<script type="text/javascript">
<!--
var TSort_Data = new Array ('table_demo_world', 's', 'i', 'f');
var TSort_Classes = new Array ('row1', 'row2');
var TSort_Initial = new Array ('1A', '2D');
tsRegister();
TSort_Data = new Array ('table_demo_us', 'h', 's', 'n');
TSort_Classes = new Array ('row2', 'row1');
TSort_Initial = new Array ('0D');
var TSort_Icons = new Array (' V', ' &#923;');
var TSort_Cookie = 'table_demo_us';
tsRegister();
// -->
</script>
<TABLE id="table_demo_world" class="demo">
<thead>
<tr><th rowspan=2>City Name</th><th colspan=2>City Information</th></tr>
<tr><th>Area (km<sup>2</sup>)</th><th>Population (millions)</th></tr>
</thead>
<tr class='row1'><td>Mumbai</td><td>440</td><td>12.78</td></tr>
<tr class='row2'><td>Karachi</td><td>3530</td><td>12.21</td></tr>
<tr class='row1'><td>Delhi</td><td>1400</td><td>11.06</td></tr>
<tr class='row2'><td>San Paulo</td><td>1520</td><td>10.84</td></tr>
<tr class='row1'><td>Moscow</td><td>1081</td><td>10.38</td></tr>
</table>
<p>
<TABLE id="table_demo_us" class="demo">
<thead>
<tr><th>US City</th><th>State</th><th>Population (millions)</th></tr>
</thead>
<tr class='row1'><td><a href="/Table_Sort/index.html">New York</a></td><td>New York</td><td>8,274,527</td></tr>
<tr class='row2'><td><a href="/Table_Sort/index.html">Los Angeles</a></td><td>California</td><td>3,834,340</td></tr>
<tr class='row1'><td><a href="/Table_Sort/index.html">Chicago</a></td><td>Illinois</td><td>2,836,658</td></tr>
<tr class='row2'><td><a href="/Table_Sort/index.html">Houston</a></td><td>Texas</td><td>2,208,180</td></tr>
<tr class='row1'><td><a href="/Table_Sort/index.html">Phoenix</a></td><td>Arizona</td><td>1,552,259</td></tr>
</table>

<a name='how_to_use_it'></a>
<h2>How to use it:</h2>

<ol><li>Please download a file gs_sortable.js (see links below) - this is
   the only external
   javascript file that you will need. Put it somewhere on your web server.
   Do not link to original file on this server because:
  <ul>
  <li>You will be using my bandwidth, and I will not like it.</li>
  <li>I may post updated version of the javascript file with the same name,
   and this may break your pages.</li>
  <li>If somebody breaks into my server it'd be easy for them to modify the
   file and do some nasty things to your page, like getting user cookies
   for your domain, or redirecting all users from your page to some other
   page.</li>
   </ul></li>
  <li>Add id="some_name" to the table that you want to sort:<br>
	<div class="code">
	&lt;table id="my_table"&gt;<br>
	... more HTML data ...
	</div>

   "some_name" id should be unique (it's an HTML requirement).</li>

  <li>Put "&lt;thead&gt;" and "&lt;/thead&gt;" tags around the table row that contains column names, like this:
	<div class="code">
&lt;table id="my_table"&gt;<br>
&lt;thead&gt;<br>
&lt;tr&gt;&lt;th&gt;Name&lt;/th&gt;&lt;th&gt;Year&lt;/th&gt;&lt;/tr&gt;<br>
&lt;/thead&gt;<br>
... table data ...<br>
&lt;/table&gt;
	</div>
You don't necessarily need to use TH tags for that row - TD will work too.</li>

  <li>Add javascript code to your page:
	<div class="code">
&lt;script type="text/javascript" src="/gs_sortable.js"&gt;&lt;/script&gt;<br>
&lt;script type="text/javascript"&gt;<br>
&lt;!--<br>
var TSort_Data = new Array ('my_table', 's', 'i', 'f');<br>
tsRegister();<br>
// --&gt;<br>
&lt;/script&gt;
	</div>
"tsRegister();" is optional if you want the script to handle only one
table on the page, and required if you want to add column sorting to
more than one table. The part that you need to change in this code is
located inside of Array parenthesis. The first parameter in array
("my_table" in the example above) should match id of the table. All
other parameters specify type of data in table columns - the second
parameter specifies type of data in the first column, the third
parameter specifies type of data in the second column, and so on.
"Type of data" parameters can be set to:<br><br> 
 
<b>'i'</b> - Column contains integer data. If the column data contains a number
followed by text then the text will ignored. For example, "54note" will be
interpreted as "54".<br><br>

<b>'n'</b> - Column contains integer number in which all three-digit
groups are optionally separated from each other by commas. For
example, column data "100,000,000" is treated as "100000000" when type
of data is set to 'n', or as "100" when type of data is set to
'i'.<br><br> 

<b>'f'</b> - Column contains floating point numbers in the form
###.###.<br><br> 

<b>'g'</b> - Column contains floating point numbers in the form ###.###.
Three-digit groups in the floating-point number may be separated from
each other by commas. For example, column data "65,432.1" is treated
as "65432.1" when type of data is set to 'g', or as "65" when type of
data is set to 'f'.<br><br> 

<b>'h'</b> - column contains HTML code. The script will strip all HTML code before sorting the data.<br><br>

<b>'s'</b> - column contains plain text data.<br><br>

<b>'d'</b> - column contains a date.<br><br>

<b>''</b> - do not sort the column.<br><br>

For example, if you used "sortable_table" id for your table and you
want to sort second column by text, third column by date, forth
column by numbers, and do not sort the first column, then the
javascript code will look like this: 
	<div class="code">
&lt;script type="text/javascript"&gt;<br>
&lt;!--<br>
var TSort_Data = new Array ('sortable_table', '', 's', 'd', 'i');<br>
// --&gt;<br>
&lt;/script&gt;
	</div></li>
  <li>If you want to add table sort feature to more than one table on
  the page then repeat steps 2, 3 and 4 for each table. Make sure that
  javascript code in the step 4 includes "tsRegister();" statement for
  each table. If you specify any advanced additional parameters (see
  "Advanced Features" section below for more information) then you
  don't need to reset them after each call to the tsRegister function
  - the function does it automatically.
  </li>
</ol>

<p>That's it. The javascript code was tested in IE6, IE7, Firefox 1.5 and 2,
and Opera 9.

<a name='advanced'></a>
<h2>Advanced Features</h2>
<ul>
<li><h3>Zebra striping</h3>
<p>If you want to use different background colors for odd and even rows
(zebra striping) then you can tell the script to apply background colors
to sorted records. First, create two CSS classes
with background colors for your table. Then add the line below between
&lt;script ...&gt; and &lt;/script&gt; tags:

<div class="code">
var TSort_Classes = new Array ('class1_name', 'class2_name');
</div>

For instance, if you created two classes row1 and row2 for
table rows, and you want to add zebra striping to the example table
from "How to use it" section, then your javascript code will look like this:

<div class="code">
&lt;script type="text/javascript"&gt;<br>
&lt;!--<br>
var TSort_Data = new Array ('sortable_table', '', 's', 's', 'i');<br>
var TSort_Classes = new Array ('row1', 'row2');<br>
// --&gt;<br>
&lt;/script&gt;
</div>

You may use the script to do more complex zebra striping.
For example, if you want to use row3 class for every 4th row, and row2
class for all other even rows then TSort_Classes definition will look
like:

<div class="code">
var TSort_Classes = new Array ('row1', 'row2', 'row1', 'row3');<br>
</div>
<br>
</li>

<li> <h3>Setting initial sorting</h3>

<p>You can specify initial sorting order of one or more columns in the
table by setting TSort_Initial variable. To sort one column in
ascending order set this variable to column number:

<div class="code">
&lt;script type="text/javascript"&gt;<br>
&lt;!--<br>
var TSort_Data = new Array ('sortable_table', '', 's', 's', 'i');<br>
var TSort_Initial = 1;<br>
// --&gt;<br>
&lt;/script&gt;
</div>

Please note that the column numbering starts from 0, so setting
TSort_Initial to 1 will sort the second column in ascending order.
You can tell the script to set exact sorting order (ascending,
descending or unsorted) for a column if you replace the column number
with a string in format "&lt;column_number&gt;&lt;sorting_order&gt;",
where &lt;sorting_order&gt; is:<br><br>
<b>A</b> - ascending order<br><br>
<b>D</b> - descending order<br><br>
<b>U</B> - unsorted<br><br>

For example, use '1D' string to sort the second column in the descending
order.<br><br>

To specify initial sorting for multiple columns you'll need to set TSort_Initial
variable to an array containing column numbers and/or column sorting order
strings:

<div class="code">
&lt;script type="text/javascript"&gt;<br>
&lt;!--<br>
var TSort_Data = new Array ('sortable_table', '', 's', 's', 'i');<br>
var TSort_Initial = new Array (1, '2D');<br>
// --&gt;<br>
&lt;/script&gt;
</div>

In the example above the table will be sorted by the second column in
ascending order, and then by the third column in descending order.
<br><br>
</li>

<li><h3>Using external buttons or objects to change sorting</h3>

<p>To change sorting order of any column when a user clicks on a
button, a link, or any other object, add an onClick event to the
object and call a tsDraw function with either a column number or
column sorting order string (see "Setting initial sorting" above) as
the first parameter, and id of the table as the second parameter. The
second parameter can be omitted if you have only one table with
sortable columns, or if you call tsDraw function the second, the
third, etc time in a row for the same table. For example, the button
below will change sorting order of all columns in the table at the top
of this page to "unsorted".

<p>
<script type="text/javascript">
<!--
var TSort_Data = new Array ('table_demo_ext', 's', 'i', 'f');
var TSort_Classes = new Array ('row1', 'row2');
var TSort_Initial = new Array ('0A', '1A', '2A');
tsRegister();
// -->
</script>
<TABLE id="table_demo_ext" class="demo">
<thead>
<tr><th>City</th><th>Area (km<sup>2</sup>)</th><th>Population (millions)</th></tr>
</thead>
<tr class='row1'><td>Mumbai</td><td>440</td><td>12.78</td></tr>
<tr class='row2'><td>Karachi</td><td>3530</td><td>12.21</td></tr>
</table>
<p><input type='button' name='action' value='Reset sorting'
onClick='tsDraw("0U", "table_demo_ext"); tsDraw("1U"); tsDraw("2U")'>

<p>Below is an HTML code for this button. Notice that only the first
call to tsDraw includes table id:

<div class="code">
&lt;input type='button' name='action' value='Reset sorting'
onClick='tsDraw("0U", "table_demo_world"); tsDraw("1U"); tsDraw("2U")'&gt;
</div>

<p>The button below will sort in ascending order three columns in the
table:

<p><input type='button' name='action' value='Sort all'
onClick='tsDraw("2A", "table_demo_ext"); tsDraw("1A"); tsDraw("0A")'>

<p>HTML code for the button:

<div class="code">
&lt;input type='button' name='action' value='Sort all'
onClick='tsDraw("2A", "table_demo_world"); tsDraw("1A"); tsDraw("0A")'&gt;
</div>
<br>
</li>

<li><h3>Replacing Up/Down arrows with other characters</h3>

<p>By default the script uses up and down arrows
to indicate current sorting order. You can replace these two
characters with any other characters, text or HTML code by setting
TSort_Icons variable: 

<div class="code">
&lt;script type="text/javascript"&gt;<br>
&lt;!--<br>
var TSort_Data = new Array ('table_demo_icons', '', 's', 'i', 'f');<br>
var TSort_Icons = new Array (' (Ascending)', ' (Descending)');<br>
// --&gt;<br>
&lt;/script&gt;
</div>

<p>Here is an example of a table that uses javascript code above:
<p>
<script type="text/javascript">
<!--
var TSort_Data = new Array ('table_demo_icons', 's', 'i', 'f');
var TSort_Icons = new Array (' (Ascending)', ' (Descending)');
tsRegister();
// -->
</script>
<TABLE id="table_demo_icons" class="demo">
<thead>
<tr><th>City</th><th>Area (km<sup>2</sup>)</th><th>Population (millions)</th></tr>
</thead>
<tr class='row1'><td>Mumbai</td><td>440</td><td>12.78</td></tr>
<tr class='row2'><td>Karachi</td><td>3530</td><td>12.21</td></tr>
</table>

<p>TSort_Icons parameter applies only to current table.
If desired, you can replace the characters / text with any HTML
code, including &lt;img ...&gt; tags. Be careful when you do that -
the script changes text color to indicate primary, secondary or
tertiary column, and, if the HTML code contains just an image, then it
will be impossible to differentiate between primary, secondary and
tertiary columns. 
<br><br>
</li>

<li><h3>Storing sorting order in a cookie</h3>

<p>By default, the script does not preserve current sorting order when
the page reloads, or when a visitor leaves the page and returns to it
later. You can tell the script to preserve sorting order for any table
on the page by setting TSort_Cookie variable to cookie name,
preferably the same as table id: 

<div class="code">
&lt;script type="text/javascript"&gt;<br>
&lt;!--<br>
var TSort_Data = new Array ('sortable_table', '', 's', 'i', 'f');<br>
var TSort_Cookie = 'sortable_table';<br>
// --&gt;<br>
&lt;/script&gt;
</div>

<p> Using the same name for table id and cookie name is not a
requirement, if you wish you give the cookie any name. If you
use gs_sortable script to sort data in multiple tables on the same
page and you want to preserve sorting order for all tables then you
will need to set TSort_Cookie variable for each of those tables. Make
sure that cookie names for all those tables are different!
</li>
</ul>


</body>
</html>
