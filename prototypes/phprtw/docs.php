<?PHP
include 'phpRTWindows.php';
$a = new prtw('themes/orange', '');
echo $a->js_init();
echo $a->do_window('PHP-RTWindows', '
This class is a PHP wrapper and expander for RTWindows script located <a href="http://gonzo.uni-weimar.de/~scheffl2/amsterdam/rtwindows_0.5/rtwindows.php" target="_blank">here</a>.<BR>
<B>Features</B>:<BR>
- window generation from PHP<BR>
- title, content, location, size controled from PHP<BR>
- CSS generator for a theme image set.<BR>
- Xajax could be used to easily add Ajax support (Xajax is PHP oriented so you tell Xajax to cal this class methods :))<BR><BR>
<B>License</B>: RTWindows License (unknown) say it\'s LGPL or something.<BR>
<B>Author</B>: Piotr Malinski, riklaunim@gmail.com<BR>
<B>Requirements</B>: PHP5
', 'w1', 'window1');

echo $a->do_window('How to use', '
<B>Example</B>: this file, open in a text editor to get the code :)<BR><BR>

<B>Constructor</B>:
<center>$a = new prtw(\'themes/orange\', \'\');</center>
You pass two paths: first one is a path to a folder with a theme, second one is a path to <U>phprtw.js</U> (by default it is in the current folder)<BR><BR>

<B>Including CSS and JS</B>:
<center>echo $a->js_init();</center>
returns a string with "script" and "link" HTML tags which add needed CSS ad JS files.<BR><BR>

<B>Creating a window</B>:
<center>echo $a->do_window(\'title\', \'content\', \'UNIQUE NAME\', \'UNIQUE ID\');</center>
Where <B>title</B> is the window title (string), <B>content</B> - window content (string), <B>UNIQUE NAME</B> a name like "win1", "win2", <B>UNIQUE ID</B> - the same idea.
<BR><BR>

<B>Executing windows</B>:<BR>
<pre>echo $a->exec_windows(
array(
	<B>\'UNIQUE NAME\' => array(\'x\' => 100, \'y\' => 100, \'id\' => \'UNIQUE ID\', \'width\' => 400, \'height\' => 200)</B>
	)
);</pre><BR>
Where <B>UNIQUE NAME</B> and <B>UNIQUE ID</B> are name/id of an existing window. <B>x</B> and <B>y</B> - how far from top left corner the window should show, <B>width</B> and <B>height</B> - size of the window. For each window add extra sub-array<BR><BR>


<code><span style="color: #000000">
<span style="color: #0000BB"></span><span style="color: #007700">include </span><span style="color: #DD0000">\'phpRTWindows.php\'</span><span style="color: #007700">;<br /></span><span style="color: #FF8000"># create an object<br /></span><span style="color: #0000BB">$a </span><span style="color: #007700">= new </span><span style="color: #0000BB">prtw</span><span style="color: #007700">(</span><span style="color: #DD0000">\'path/to/theme/dir/\'</span><span style="color: #007700">, </span><span style="color: #DD0000">\'path/to/js/dir\'</span><span style="color: #007700">);<br /></span><span style="color: #FF8000"># return JS and CSS includes<br /></span><span style="color: #007700">echo </span><span style="color: #0000BB">$a</span><span style="color: #007700">-></span><span style="color: #0000BB">js_init</span><span style="color: #007700">();<br /></span><span style="color: #FF8000"># create a window<br /></span><span style="color: #007700">echo </span><span style="color: #0000BB">$a</span><span style="color: #007700">-></span><span style="color: #0000BB">do_window</span><span style="color: #007700">(</span><span style="color: #DD0000">\'title\'</span><span style="color: #007700">, </span><span style="color: #DD0000">\'content\'</span><span style="color: #007700">, </span><span style="color: #DD0000">\'id\'</span><span style="color: #007700">, </span><span style="color: #DD0000">\'window_name\'</span><span style="color: #007700">);<br /></span><span style="color: #FF8000"># configure all windows and execute them<br /></span><span style="color: #007700">echo </span><span style="color: #0000BB">$a</span><span style="color: #007700">-></span><span style="color: #0000BB">exec_windows</span><span style="color: #007700">(array(</span><span style="color: #DD0000">\'id\' </span><span style="color: #007700">=> array(</span><span style="color: #DD0000">\'x\' </span><span style="color: #007700">=> </span><span style="color: #0000BB">100</span><span style="color: #007700">, </span><span style="color: #DD0000">\'y\' </span><span style="color: #007700">=> </span><span style="color: #0000BB">100</span><span style="color: #007700">, </span><span style="color: #DD0000">\'id\' </span><span style="color: #007700">=> </span><span style="color: #DD0000">\'ID\'</span><span style="color: #007700">, </span><span style="color: #DD0000">\'width\' </span><span style="color: #007700">=> </span><span style="color: #0000BB">400</span><span style="color: #007700">, </span><span style="color: #DD0000">\'height\' </span><span style="color: #007700">=> </span><span style="color: #0000BB">200</span><span style="color: #007700">));<br /></span><span style="color: #0000BB"></span>
</span>
</code>
', 'w2', 'window2');

echo $a->exec_windows(array('w1' => array('x' => 10, 'y' => 10, 'id' => 'window1', 'width' => 700, 'height' => 250),
'w2' => array('x' => 10, 'y' => 260, 'id' => 'window2', 'width' => 700, 'height' => 300)));
?>