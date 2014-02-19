<?PHP
class prtw
{
public function __construct($theme, $jspath)
	{
	$this->theme = $theme;
	$this->js = $jspath;
	}
public function js_init()
	{
	return '<script src="'.$this->js.'phprtw.js" type="text/javascript"></script><link href="'.$this->theme.'/windows.css" media="screen" rel="Stylesheet" type="text/css" />';
	}
public function do_window($title, $content, $name, $id)
	{
	return '<div id="'.$id.'" class="window_container" style="opacity:0.0;visibility:hidden;position:absolute;">
	<div id="'.$id.'_resize_handle" class="window_resize_handle"></div>
	<div id="'.$id.'_topslice" class="window_topslice" >
		<div id="'.$id.'_drag_handle" class="window_dragger">
	</div>
	<div class="window_button_holder" style="z-index:100;position:absolute;">
				<a href="#" onClick="'.$name.'.minimize();"><img border="0" src="'.$this->theme.'/but_mini.gif" /></a>
				<a href="#" onClick="'.$name.'.maximize();"><img border="0" src="'.$this->theme.'/but_maxi.gif" /></a>
				<a href="#" onClick="'.$name.'.close();"><img border="0" src="'.$this->theme.'/but_clos.gif" /></a>
			</div>
		
		<div class="window_handle_left"></div>
	<div class="window_handle_center"><span class="window_title">'.$title.'</span></div>
	<div class="window_handle_right"><img src="'.$this->theme.'/frame_top_right.gif" align="right"></div>
	</div>
	
	<div id="'.$id.'_middleslice" class="window_middleslice"><div class="window_border_left" >
				<div style="height:40px;width:15px;position:absolute;bottom:0px;"><img src="'.$this->theme.'/frame_left.gif"></div><img src="'.$this->theme.'/frame_left.gif">
	</div>
			
			<div class="window_content" id="'.$id.'_content">
			<div style="padding:5px;">
		<div class="debug_field" id="command_div"></div>
	'.$content.'
				
			</div>
			</div>
			
			<div class="window_border_right" >
				<div style="height:40px;width:15px;position:absolute;bottom:0px;"><img src="'.$this->theme.'/frame_right.gif" /></div><img src="'.$this->theme.'/frame_right.gif">
	
			</div>
	
	</div>
	
	<div id="'.$id.'_bottomslice" class="window_bottomslice">
		<div class="window_bottom_left"></div>
			<div class="window_bottom_center"></div>
			<div class="window_bottom_right"></div>
	</div>
	
	
	</div>  ';
	}
public function exec_windows($names)
	{
	ob_start();
	echo "<script type=\"text/javascript\">\n";
	foreach($names as $key => $val)
		{
		echo 'var '.$key.";\n";
		}
	echo "window.onload=function()
	{\n";
	foreach($names as $key => $val)
	{
		echo $key.'=new Window(
		{
		"name":"'.$key.'",
		"z_index":-1,
		"min_height":10,
		"id":"'.$val['id'].'",
		"scrollbars":1,
		"is_minimized":0,
		"stayontop":0,
		"min_width":90,
		"height":'.$val['height'].',
		"posx":'.$val['x'].',
		"posy":'.$val['y'].',
		"width":'.$val['width']."});\n\n";
	}
	echo '}
	</script>';
	$wynik = ob_get_contents();
	ob_end_clean();
	return $wynik;
	}
public function create_css()
	{
	foreach(new DirectoryIterator($this->theme) as $theme)
		{
		IF($theme->isFile() and !$theme->isDot())
			{
			$x = getImageSize($this->theme.'/'.$theme->getFilename());
			IF(is_array($x) and $x[0] > 0)
				{
				$ar[$theme->getFilename()] = array('wi' => $x[0], 'he' => $x[1]);
				}
			}
		}
		$css =  '.window_dragger a {
text-decoration:none;
font-weight:bold;
font-size:10px;
}

.window_dragger{
cursor:move;
z-index:1;
width:100%;
height:34px;
position:absolute;
top:0px;
left:0px;
}

.window_resize_handle {
position:absolute;
cursor:se-resize;
width:20px;
height:16px;
font-size:10px;
z-index:1;
}

.window_container {
padding:0px;
position:absolute;
}

.window_fader
{
border:0px;
width:100%;
}

.window_topslice
{
position:relative;
border:0px;
width:100%;
height:'.$ar['frame_top_right.gif']['he'].'px;
}

.window_middleslice
{
width:100%;
position:relative;
}

.window_bottomslice
{
position:absolute;
height:'.$ar['frame_foot_right.gif']['he'].'px;
border:0px;
width:100%;
}

.window_handle_left {
font-size:3px;
position: absolute;
left:0px;
top:0px;
width:'.$ar['frame_top_left.gif']['wi'].'px;
height:'.$ar['frame_top_left.gif']['he'].'px;
background:url(frame_top_left.gif);
}

.window_title {
line-height:'.$ar['frame_top_right.gif']['he'].'px;
font-size:12px;
color:white;
font-weight:bold;
font-family:sans-serif;
overflow:hidden;
}

.window_handle_center {
font-size:3px;
background:url(frame_top_mid.gif);
background-repeat:repeat-x;
top:0px;
border:0px;
margin-left: 0px;
margin-right:74px;
text-align:center;
}

.window_handle_right {
background:url(frame_top_mid.gif);
font-size:3px;
position: absolute;
right:0px;
top:0px;
width:74px;
border:0px;
}

.window_border_left {
background:url(frame_left.gif);
background-repeat:repeat-y;
position: absolute;
left:0px;
width:'.$ar['frame_left.gif']['wi'].'px;
padding:0px;
border:0px;
}

.window_content {
text-align:left;
color:black;
/*overflow:auto;*/
position:relative;
background:url(cell.gif);
font-family:sans-serif;
font-size:12px;
border:0px;
margin-left: '.$ar['frame_left.gif']['wi'].'px;
margin-right:'.$ar['frame_right.gif']['wi'].'px;
padding:0px;
}

.window_border_right {
background:url(frame_right.gif);
background-repeat:repeat-y;
position: absolute;
right:0px;
top:0px;
width:'.$ar['frame_right.gif']['wi'].'px;
padding:0px;
border:0px;
}

.window_bottom_left {
font-size:3px;
position: absolute;
left:0px;
bottom:0px;
width:'.$ar['frame_foot_left.gif']['wi'].'px;
height:'.$ar['frame_foot_left.gif']['he'].'px;
background:url(frame_foot_left.gif);
border:0px;
}

.window_bottom_center {
font-size:3px;
background:url(frame_foot_mid.gif);
background-repeat:repeat-x;
margin-bottom:0px;
height:'.$ar['frame_foot_mid.gif']['he'].'px;
border:0px;
margin-left: 0px;
margin-right:0px;
}

.window_bottom_right {
background:url(frame_foot_right.gif);  
font-size:3px;
position: absolute;
right:0px;
bottom:0px;
width:'.$ar['frame_foot_right.gif']['wi'].'px;
height:'.$ar['frame_foot_right.gif']['he'].'px;
border:0px;
}

.window_button_holder {
position:absolute;
right:19px;
text-align:right;
width:60px;
padding-top:8px;
font-size:6px
}

.window_iframe {
border:0px;
padding:0px;
margin:0px;
}
.splashscreen {
position: absolute;
top: 100;
left: 100;
width: 400px;
height: 300px;
text-align: left; 
background: #87cfff url(splash00.jpg);  
padding:0px;
color:black;
z-index:10000;
}
';
		
	file_put_contents($this->theme.'/windows.css', $css);
	}
}
?>