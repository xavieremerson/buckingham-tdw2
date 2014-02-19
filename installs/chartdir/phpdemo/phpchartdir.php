<?php

$ver = split('\.', phpversion());
$ver = $ver[0] * 10000 + $ver[1] * 100 + $ver[2];
if ($ver < 40004)
	die("ChartDirector PHP API only supports PHP Ver 4.0.4 or above. Your PHP version is ".phpversion().".");

if (!extension_loaded("ChartDirector PHP API"))
{
	if ($ver >= 40201)
		$ext = "phpchartdir421.dll";
	else if ($ver >= 40100)
		$ext = "phpchartdir410.dll";
	else if ($ver >= 40005)
		$ext = "phpchartdir405.dll";
	else
		$ext = "phpchartdir404.dll";

	if (!dl($ext))
	{ ?>
<br><br>Please make sure you have copied all ChartDirector PHP extension files to your PHP extension 
subdirectory.<br><br>

Your PHP extension directory is currently configured as "<?php echo get_cfg_var("extension_dir")?>". If 
it is a relative path name, please make sure you know where it is relative to (it depends on how your 
PHP is compiled). To avoid confusion, you may want to modify your PHP configuration to use an absolute 
path name instead.<br><br>

Please also make sure the web server anonymous user has sufficient privileges to read and execute the 
ChartDirector PHP extension files.<br><br>

If the problem persists, please email this entire web page to <a href="mailto:support@advsofteng.com">
support@advsofteng.com</a> for support.

<?php
		die();
	}
}

$dllVersion = callmethod("getVersion");
if (($dllVersion & 0x7fff0000) != 0x03000000)
{
	$majorDllVer = ($dllVersion >> 24) & 0xff;
	$minorDllVer = ($dllVersion >> 16) & 0xff;
	die("Version mismatch - \"phpchartdir.php\" is of Ver 3.0, but \"chartdir.dll/libchartdir.so\" is of Ver $majorDllVer.$minorDllVer");
}

#///////////////////////////////////////////////////////////////////////////////////
#//	implement destructor handling
#///////////////////////////////////////////////////////////////////////////////////
global $cd_garbage ;
$cd_garbage = array();
function autoDestroy($me) {
	global $cd_garbage;
	$cd_garbage[] = $me;
}
function garbageCollector() {
	global $cd_garbage;
	reset($cd_garbage);
    while (list(, $obj) = each($cd_garbage))
        $obj->__del__();
    $cd_garbage = array();
}
register_shutdown_function("garbageCollector");

#///////////////////////////////////////////////////////////////////////////////////
#//	constants
#///////////////////////////////////////////////////////////////////////////////////
define("BottomLeft", 1);
define("BottomCenter", 2);
define("BottomRight", 3);
define("Left", 4);
define("Center", 5);
define("Right", 6);
define("TopLeft", 7);
define("TopCenter", 8);
define("TopRight", 9);
define("Top", TopCenter);
define("Bottom", BottomCenter);

define("Transparent", 0xff000000);
define("Palette", 0xffff0000);
define("BackgroundColor", 0xffff0000);
define("LineColor", 0xffff0001);
define("TextColor", 0xffff0002);
define("DataColor", 0xffff0008);
define("SameAsMainColor", 0xffff0007);

define("NoValue", +1.7e308);
define("LogTick", +1.6e308);
define("TouchBar", -1.7e-100);

define("NoAntiAlias", 0);
define("AntiAlias", 1);
define("AutoAntiAlias", 2);

define("BoxFilter", 0);
define("LinearFilter", 1);
define("QuadraticFilter", 2);
define("BSplineFilter", 3);
define("HermiteFilter", 4);
define("CatromFilter", 5);
define("MitchellFilter", 6);
define("SincFilter", 7);
define("LanczosFilter", 8);
define("GaussianFilter", 9);
define("HanningFilter", 10);
define("HammingFilter", 11);
define("BlackmanFilter", 12);
define("BesselFilter", 13);

define("TryPalette", 0);
define("ForcePalette", 1);
define("NoPalette", 2);
define("Quantize", 0);
define("OrderedDither", 1);
define("ErrorDiffusion", 2);

define("PNG", 0);
define("GIF", 1);
define("JPG", 2);
define("WMP", 3);
define("BMP", 4);

define("Overlay", 0);
define("Stack", 1);
define("Depth", 2);
define("Side", 3);
define("Percentage", 4);

$defaultPalette = array(
	0xffffff, 0x000000, 0x000000, 0x808080,
	0x808080, 0x808080, 0x808080, 0x808080,
	0xff3333, 0x33ff33, 0x6666ff, 0xffff00,
	0xff66ff, 0x99ffff,	0xffcc33, 0xcccccc,
	0xcc9999, 0x339966, 0x999900, 0xcc3300,
	0x669999, 0x993333, 0x006600, 0x990099,
	0xff9966, 0x99ff99, 0x9999ff, 0xcc6600,
	0x33cc33, 0xcc99ff, 0xff6666, 0x99cc66,
	0x009999, 0xcc3333, 0x9933ff, 0xff0000,
	0x0000ff, 0x00ff00, 0xffcc99, 0x999999,
	-1
);
function defaultPalette() { global $defaultPalette; return $defaultPalette; }

$whiteOnBlackPalette = array(
	0x000000, 0xffffff, 0xffffff, 0x808080,
	0x808080, 0x808080, 0x808080, 0x808080,
	0xff0000, 0x00ff00, 0x0000ff, 0xffff00,
	0xff00ff, 0x66ffff,	0xffcc33, 0xcccccc,
	0x9966ff, 0x339966, 0x999900, 0xcc3300,
	0x99cccc, 0x006600, 0x660066, 0xcc9999,
	0xff9966, 0x99ff99, 0x9999ff, 0xcc6600,
	0x33cc33, 0xcc99ff, 0xff6666, 0x99cc66,
	0x009999, 0xcc3333, 0x9933ff, 0xff0000,
	0x0000ff, 0x00ff00, 0xffcc99, 0x999999,
	-1
);
function whiteOnBlackPalette() { global $whiteOnBlackPalette; return $whiteOnBlackPalette; }

$transparentPalette = array(
	0xffffff, 0x000000, 0x000000, 0x808080,
	0x808080, 0x808080, 0x808080, 0x808080,
	0x80ff0000, 0x8000ff00, 0x800000ff, 0x80ffff00,
	0x80ff00ff, 0x8066ffff,	0x80ffcc33, 0x80cccccc,
	0x809966ff, 0x80339966, 0x80999900, 0x80cc3300,
	0x8099cccc, 0x80006600, 0x80660066, 0x80cc9999,
	0x80ff9966, 0x8099ff99, 0x809999ff, 0x80cc6600,
	0x8033cc33, 0x80cc99ff, 0x80ff6666, 0x8099cc66,
	0x80009999, 0x80cc3333, 0x809933ff, 0x80ff0000,
	0x800000ff, 0x8000ff00, 0x80ffcc99, 0x80999999,
	-1
);
function transparentPalette() { global $transparentPalette; return $transparentPalette; }

define("NoSymbol", 0);
define("SquareSymbol", 1);
define("DiamondSymbol", 2);
define("TriangleSymbol", 3);
define("RightTriangleSymbol", 4);
define("LeftTriangleSymbol", 5);
define("InvertedTriangleSymbol", 6);
define("CircleSymbol", 7);
define("CrossSymbol", 8);
define("Cross2Symbol", 9);

define("DashLine", 0x0505);
define("DotLine", 0x0202);
define("DotDashLine", 0x05050205);
define("AltDashLine", 0x0A050505);

$goldGradient = array(0, 0xFFE743, 0x60, 0xFFFFE0, 0xB0, 0xFFF0B0, 0x100, 0xFFE743);
$silverGradient = array(0, 0xC8C8C8, 0x60, 0xF8F8F8, 0xB0, 0xE0E0E0, 0x100, 0xC8C8C8);
$redMetalGradient = array(0, 0xE09898, 0x60, 0xFFF0F0, 0xB0, 0xF0D8D8, 0x100, 0xE09898);
$blueMetalGradient = array(0, 0x9898E0, 0x60, 0xF0F0FF, 0xB0, 0xD8D8F0, 0x100, 0x9898E0);
$greenMetalGradient = array(0, 0x98E098, 0x60, 0xF0FFF0, 0xB0, 0xD8F0D8, 0x100, 0x98E098);
function goldGradient() { global $goldGradient; return $goldGradient; }
function silverGradient() { global $silverGradient; return $silverGradient; }
function redMetalGradient() { global $redMetalGradient; return $redMetalGradient; }
function blueMetalGradient() { global $blueMetalGradient; return $blueMetalGradient; }
function greenMetalGradient() { global $greenMetalGradient; return $greenMetalGradient; }

define("NormalLegend", 0);
define("ReverseLegend", 1);
define("NoLegend", 2);

define("SideLayout", 0);
define("CircleLayout", 1);

define("PixelScale", 0);
define("XAxisScale", 1);
define("YAxisScale", 2);
define("AngularAxisScale", XAxisScale);
define("RadialAxisScale", YAxisScale);

#///////////////////////////////////////////////////////////////////////////////////
#//	bindings to libgraphics.h
#///////////////////////////////////////////////////////////////////////////////////
class TTFText
{
	function TTFText($ptr) {
		$this->ptr = $ptr;
		autoDestroy($this);
	}
	function __del__() {
		callmethod("TTFText.destroy", $this->ptr);
	}
	function getWidth() {
		return callmethod("TTFText.getWidth", $this->ptr);
	}
	function getHeight() {
		return callmethod("TTFText.getHeight", $this->ptr);
	}
	function getLineHeight() {
		return callmethod("TTFText.getLineHeight", $this->ptr);
	}
	function getLineDistance() {
		return callmethod("TTFText.getLineDistance", $this->ptr);
	}
	function draw($x, $y, $color, $alignment) {
		callmethod("TTFText.draw", $this->ptr, $x, $y, $color, $alignment);
	}
}

class DrawArea {
	function DrawArea($ptr = Null) {
		if (is_null($ptr)) {
			$this->ptr = callmethod("DrawArea.create");
			autoDestroy($this);
		}
		else {
			$this->ptr = $ptr;
		}
	}
	function __del__() {
		callmethod("DrawArea.destroy", $this->ptr);
	}
	function setSize($width, $height, $bgColor = 0xffffff) {
		callmethod("DrawArea.setSize", $this->ptr, $width, $height, $bgColor);
	}
	function resize($newWidth, $newHeight, $f = LinearFilter, $blur = 1) {
		callmethod("DrawArea.resize", $this->ptr, $newWidth, $newHeight, $f, $blur);
	}
	function getWidth() {
		return callmethod("DrawArea.getWidth", $this->ptr);
	}
	function getHeight() {
		return callmethod("DrawArea.getHeight", $this->ptr);
	}
	function setClipRect($left, $top, $right, $bottom) {
		return callmethod("DrawArea.setClipRect", $this->ptr, $left, $top, $right, $bottom);
	}
	function setBgColor($c) {
		callmethod("DrawArea.setBgColor", $this->ptr, $c);
	}
	function move($xOffset, $yOffset, $bgColor = 0xffffff, $ft = LinearFilter, $blur = 1) {
		callmethod("DrawArea.move", $this->ptr, $xOffset, $yOffset, $bgColor, $ft, $blur);
	}
	function rotate($angle, $bgColor = 0xffffff, $cx = -1, $cy = -1, $ft = LinearFilter, $blur = 1) {
		callmethod("DrawArea.rotate", $this->ptr, $angle, $bgColor, $cx, $cy, $ft, $blur);
	}
	function hFlip() {
		callmethod("DrawArea.hFlip", $this->ptr);
	}
	function vFlip() {
		callmethod("DrawArea.vFlip", $this->ptr);
	}
	function clone($d, $x, $y, $align, $newWidth = -1, $newHeight = -1, $ft = LinearFilter, $blur = 1) {
		callmethod("DrawArea.clone", $this->ptr, $d->ptr, $x, $y, $align, $newWidth, $newHeight, $ft, $blur);
	}
	
	function pixel($x, $y, $c) {
		callmethod("DrawArea.pixel", $this->ptr, $x, $y, $c);
	}
	function getPixel($x, $y) {
		return callmethod("DrawArea.getPixel", $this->ptr, $x, $y);
	}

	function hline($x1, $x2, $y, $c) {
		callmethod("DrawArea.hline", $this->ptr, $x1, $x2, $y, $c);
	}
	function vline($y1, $y2, $x, $c) {
		callmethod("DrawArea.vline", $this->ptr, $y1, $y2, $x, $c);
	}
	function line($x1, $y1, $x2, $y2, $c, $lineWidth = 1) {
		callmethod("DrawArea.line", $this->ptr, $x1, $y1, $x2, $y2, $c, $lineWidth);
	}
	function arc($cx, $cy, $rx, $ry, $a1, $a2, $c) {
		callmethod("DrawArea.arc", $this->ptr, $cx, $cy, $rx, $ry, $a1, $a2, $c);
	}

	function rect($x1, $y1, $x2, $y2, $edgeColor, $fillColor, $raisedEffect = 0) {
		callmethod("DrawArea.rect", $this->ptr, $x1, $y1, $x2, $y2, $edgeColor, $fillColor, $raisedEffect);
	}
	function polygon($points, $edgeColor, $fillColor) {
		$x = array();
		$y = array();
		reset($points);
		while (list(, $coor) = each($points)) {
			$x[] = $coor[0];
			$y[] = $coor[1];
		}
		callmethod("DrawArea.polygon", $this->ptr, $x, $y, $edgeColor, $fillColor);
	}
	function surface($x1, $y1, $x2, $y2, $depthX, $depthY, $edgeColor, $fillColor) {
		callmethod("DrawArea.surface", $this->ptr, $x1, $y1, $x2, $y2, $depthX, $depthY, $edgeColor, $fillColor);
	}
	function sector($cx, $cy, $rx, $ry, $a1, $a2, $edgeColor, $fillColor) {
		callmethod("DrawArea.sector", $this->ptr, $cx, $cy, $rx, $ry, $a1, $a2, $edgeColor, $fillColor);
	}
	function cylinder($cx, $cy, $rx, $ry, $a1, $a2, $depthX, $depthY, $edgeColor, $fillColor) {
		callmethod("DrawArea.cylinder", $this->ptr, $cx, $cy, $rx, $ry, $a1, $a2, $depthX, $depthY, $edgeColor, $fillColor);
	}
	function circle($cx, $cy, $rx, $ry, $edgeColor, $fillColor) {
		callmethod("DrawArea.circle", $this->ptr, $cx, $cy, $rx, $ry, $edgeColor, $fillColor);
	}
	function circleShape($cx, $cy, $rx, $ry, $edgeColor, $fillColor) {
		callmethod("DrawArea.circle", $this->ptr, $cx, $cy, $rx, $ry, $edgeColor, $fillColor);
	}

	function fill($x, $y, $color, $borderColor = Null) {
		if (is_null($borderColor))
			callmethod("DrawArea.fill", $this->ptr, $x, $y, $color);
		else
			$this->fill2($x, $y, $color, $borderColor);
	}
	function fill2($x, $y, $color, $borderColor) {
		callmethod("DrawArea.fill2", $this->ptr, $x, $y, $color, $borderColor);
	}

	function text($str, $font, $fontSize, $x, $y, $color) {
		callmethod("DrawArea.text", $this->ptr, $str, $font, $fontSize, $x, $y, $color);
	}
	function text2($str, $font, $fontIndex, $fontHeight, $fontWidth, $angle, $vertical, $x, $y, $color, $alignment = TopLeft) {
		callmethod("DrawArea.text2", $this->ptr, $str, $font, $fontIndex, $fontHeight, $fontWidth, $angle, $vertical, $x, $y, $color, $alignment);
	}
	function text3($str, $font, $fontSize) {
		return new TTFText(callmethod("DrawArea.text3", $this->ptr, $str, $font, $fontSize));
	}
	function text4($text, $font, $fontIndex, $fontHeight, $fontWidth, $angle, $vertical) {
		return new TTFText(callmethod("DrawArea.text4", $this->ptr, $text, $font, $fontIndex, $fontHeight, $fontWidth, $angle, $vertical));
	}

	function merge($d, $x, $y, $align, $transparency) {
		callmethod("DrawArea.merge", $this->ptr, $d->ptr, $x, $y, $align, $transparency);
	}
	function tile($d, $transparency) {
		callmethod("DrawArea.tile", $this->ptr, $d->ptr, $transparency);
	}

	function setSearchPath($path) {
		callmethod("DrawArea.setSearchPath", $this->ptr, $path);
	}
	function loadGIF($filename) {
		return callmethod("DrawArea.loadGIF", $this->ptr, $filename);
	}
	function loadPNG($filename) {
		return callmethod("DrawArea.loadPNG", $this->ptr, $filename);
	}
	function loadJPG($filename) {
		return callmethod("DrawArea.loadJPG", $this->ptr, $filename);
	}
	function loadWMP($filename) {
		return callmethod("DrawArea.loadWMP", $this->ptr, $filename);
	}
	function load($filename) {
		return callmethod("DrawArea.load", $this->ptr, $filename);
	}
	
	function rAffineTransform($a, $b, $c, $d, $e, $f, $bgColor = 0xffffff, $ft = LinearFilter, $blur = 1) {
		callmethod("DrawArea.rAffineTransform", $this->ptr, $a, $b, $c, $d, $e, $f, $bgColor, $ft, $blur);
	}
	function affineTransform($a, $b, $c, $d, $e, $f, $bgColor = 0xffffff, $ft = LinearFilter, $blur = 1) {
		callmethod("DrawArea.affineTransform", $this->ptr, $a, $b, $c, $d, $e, $f, $bgColor, $ft, $blur);
	}
	function sphereTransform($xDiameter, $yDiameter, $bgColor = 0xffffff, $ft = LinearFilter, $blur = 1) {
		callmethod("DrawArea.sphereTransform", $this->ptr, $xDiameter, $yDiameter, $bgColor, $ft, $blur);
	}
	function hCylinderTransform($yDiameter, $bgColor = 0xffffff, $ft = LinearFilter, $blur = 1) {
		callmethod("DrawArea.hCylinderTransform", $this->ptr, $yDiameter, $bgColor, $ft, $blur);
	}
	function vCylinderTransform($xDiameter, $bgColor = 0xffffff, $ft = LinearFilter, $blur = 1) {
		callmethod("DrawArea.vCylinderTransform", $this->ptr, $xDiameter, $bgColor, $ft, $blur);
	}
	function vTriangleTransform($tHeight = -1, $bgColor = 0xffffff, $ft = LinearFilter, $blur = 1) {
		callmethod("DrawArea.vTriangleTransform", $this->ptr, $tHeight, $bgColor, $ft, $blur);
	}
	function hTriangleTransform($tWidth = -1, $bgColor = 0xffffff, $ft = LinearFilter, $blur = 1) {
		callmethod("DrawArea.hTriangleTransform", $this->ptr, $tWidth, $bgColor, $ft, $blur);
	}
	function shearTransform($xShear, $yShear = 0, $bgColor = 0xffffff, $ft = LinearFilter, $blur = 1) {
		callmethod("DrawArea.shearTransform", $this->ptr, $xShear, $yShear, $bgColor, $ft, $blur);
	}
	function waveTransform($period, $amplitude, $direction = 0, $startAngle = 0, $longitudinal = 0, 
		$bgColor = 0xffffff, $ft = LinearFilter, $blur = 1) {
		callmethod("DrawArea.waveTransform", $this->ptr, $period, $amplitude, $direction, $startAngle, 
			$longitudinal, $bgColor, $ft, $blur);
	}
	
	function out($filename) {
		return callmethod("DrawArea.out", $this->ptr, $filename);
	}
	function outGIF($filename) {
		return callmethod("DrawArea.outGIF", $this->ptr, $filename);
	}
	function outPNG($filename) {
		return callmethod("DrawArea.outPNG", $this->ptr, $filename);
	}
	function outJPG($filename, $quality = 80) {
		return callmethod("DrawArea.outJPG", $this->ptr, $filename, $quality);
	}
	function outWMP($filename) {
		return callmethod("DrawArea.outWMP", $this->ptr, $filename);
	}
	function outBMP($filename) {
		return callmethod("DrawArea.outBMP", $this->ptr, $filename);
	}

	function outGIF2() {
		return callmethod("DrawArea.outGIF2", $this->ptr);
	}
	function outPNG2() {
		return callmethod("DrawArea.outPNG2", $this->ptr);
	}
	function outJPG2($quality = 80) {
		return callmethod("DrawArea.outJPG2", $this->ptr, $quality);
	}
	function outWMP2() {
		return callmethod("DrawArea.outWMP2", $this->ptr);
	}
	function outBMP2() {
		return callmethod("DrawArea.outBMP2", $this->ptr);
	}

	function setPaletteMode($p) {
		callmethod("DrawArea.setPaletteMode", $this->ptr, $p);
	}
	function setDitherMethod($m) {
		callmethod("DrawArea.setDitherMethod", $this->ptr, $m);
	}
	function setTransparentColor($c) {
		callmethod("DrawArea.setTransparentColor", $this->ptr, $c);
	}
	function setAntiAliasText($a) {
		callmethod("DrawArea.setAntiAliasText", $this->ptr, $a);
	}
	function  setAntiAlias($shapeAntiAlias = 1, $textAntiAlias = AutoAntiAlias) {
		callmethod("DrawArea.setAntiAlias", $this->ptr, $shapeAntiAlias, $textAntiAlias);
	}
	function setInterlace($i) {
		callmethod("DrawArea.setInterlace", $this->ptr, $i);
	}

	function setColorTable($colors, $offset) {
		callmethod("DrawArea.setColorTable", $this->ptr, $colors, $offset);
	}
	function getARGBColor($c) {
		return callmethod("DrawArea.getARGBColor", $this->ptr, $c);
	}
	function dashLineColor($color, $dashPattern) {
		return callmethod("DrawArea.dashLineColor", $this->ptr, $color, $dashPattern);
	}
	function patternColor($c, $h = 0, $startX = 0, $startY = 0) {
 		if (!is_array($c))
	        return $this->patternColor2($c, $h, $startX);
 		return callmethod("DrawArea.patternColor", $this->ptr, $c, $h, $startX, $startY);
    }
	function patternColor2($filename, $startX = 0, $startY = 0) {
		return callmethod("DrawArea.patternColor2", $this->ptr, $filename, $startX, $startY);
	}
	function gradientColor($startX, $startY = 90, $endX = 1, $endY = 0, $startColor = 0, $endColor = Null) {
		if (is_array($startX))
			return $this->gradientColor2($startX, $startY, $endX, $endY, $startColor);
		return callmethod("DrawArea.gradientColor", $this->ptr, $startX, $startY, $endX, $endY, $startColor, $endColor);
	}
	function gradientColor2($c, $angle = 90, $scale = 1, $startX = 0, $startY = 0) {
		return callmethod("DrawArea.gradientColor2", $this->ptr, $c, $angle, $scale, $startX, $startY);
    }
    function halfColor($c) {
		return callmethod("DrawArea.halfColor", $this->ptr, $c);
    }

   	function setDefaultFonts($normal, $bold = "", $italic = "", $boldItalic = "") {
 		callmethod("DrawArea.setDefaultFonts", $this->ptr, $normal, $bold, $italic, $boldItalic);
    }
  	function setFontTable($index, $font) {
		callmethod("DrawArea.setFontTable", $this->ptr, $index, $font);
    }
}


#///////////////////////////////////////////////////////////////////////////////////
#//	bindings to drawobj.h
#///////////////////////////////////////////////////////////////////////////////////
class Box {
	function Box($ptr) {
		$this->ptr = $ptr;
	}
	function setPos($x, $y) {
		callmethod("Box.setPos", $this->ptr, $x, $y);
	}
	function setSize($w, $h) {
		callmethod("Box.setSize", $this->ptr, $w, $h);
	}
	function getWidth() {
		callmethod("Box.getWidth", $this->ptr);
	}
	function getHeight() {
		callmethod("Box.getHeight", $this->ptr);
	}
	function setBackground($color, $edgeColor = -1, $raisedEffect = 0) {
		callmethod("Box.setBackground", $this->ptr, $color, $edgeColor, $raisedEffect);
	}
	function getImageCoor($offsetX = 0, $offsetY = 0) {
		return callmethod("Box.getImageCoor", $this->ptr, $offsetX, $offsetY);
	}
}

class TextBox extends Box {
	function TextBox($ptr) {
		$this->ptr = $ptr;
	}
	function setText($text) {
		callmethod("TextBox.setText", $this->ptr, $text);
	}
	function setAlignment($a) {
		callmethod("TextBox.setAlignment", $this->ptr, $a);
	}
	function setFontStyle($font, $fontIndex = 0) {
		callmethod("TextBox.setFontStyle", $this->ptr, $font, $fontIndex);
	}
	function setFontSize($fontHeight, $fontWidth = 0) {
		callmethod("TextBox.setFontSize", $this->ptr, $fontHeight, $fontWidth);
	}
	function setFontAngle($angle, $vertical = 0) {
		callmethod("TextBox.setFontAngle", $this->ptr, $angle, $vertical);
	}
	function setFontColor($color) {
		callmethod("TextBox.setFontColor", $this->ptr, $color);
	}
	function setMargin2($leftMargin, $rightMargin, $topMargin, $bottomMargin) {
		callmethod("TextBox.setMargin2", $this->ptr,
			$leftMargin, $rightMargin, $topMargin, $bottomMargin);
	}
	function setMargin($m) {
		callmethod("TextBox.setMargin", $this->ptr, $m);
	}
}

class Line {
	function Line($ptr) {
		$this->ptr = $ptr;
	}
	function setPos($x1, $y1, $x2, $y2) {
		callmethod("Line.setPos", $this->ptr, $x1, $y1, $x2, $y2);
	}
	function setColor($c) {
		callmethod("Line.setColor", $this->ptr, $c);
	}
	function setWidth($w) {
		callmethod("Line.setWidth", $this->ptr, $w);
	}
}

#///////////////////////////////////////////////////////////////////////////////////
#//	bindings to basechart.h
#///////////////////////////////////////////////////////////////////////////////////
class LegendBox extends TextBox {
	function LegendBox($ptr) {
		$this->ptr = $ptr;
	}
	function addKey($text, $color, $lineWidth = 0, $drawarea = Null) {
		if (is_null($drawarea))
			callmethod("LegendBox.addKey", $this->ptr, $text, $color, $lineWidth, '$$pointer$$null');
		else
			callmethod("LegendBox.addKey", $this->ptr, $text, $color, $lineWidth, $drawarea->ptr);
	}
	function setKeySize($width, $height = -1, $gap = -1) {
		callmethod("LegendBox.setKeySize", $this->ptr, $width, $height, $gap);
	}
	function setKeySpacing($keySpacing, $lineSpacing = -1) {
		callmethod("LegendBox.setKeySpacing", $this->ptr, $keySpacing, $lineSpacing);
	}
	function getImageCoor2($dataItem, $offsetX = 0, $offsetY = 0) {
		return callmethod("LegendBox.getImageCoor", $this->ptr, $dataItem, $offsetX, $offsetY);
	}
	function getHTMLImageMap($url, $queryFormat = "", $extraAttr = "", $offsetX = 0, $offsetY = 0) {
		return callmethod("LegendBox.getHTMLImageMap", $this->ptr, $url, $queryFormat, $extraAttr, $offsetX, $offsetY);
	}
}

class BaseChart {
	function __del__() {
		callmethod("BaseChart.destroy", $this->ptr);
	}
	#//////////////////////////////////////////////////////////////////////////////////////
	#//	set overall chart
	#//////////////////////////////////////////////////////////////////////////////////////
	function setSize($width, $height) {
		callmethod("BaseChart.setSize", $this->ptr, $width, $height);
	}
	function setBorder($color) {
		callmethod("BaseChart.setBorder", $this->ptr, $color);
	}
	function setBackground($bgColor, $edgeColor = -1, $raisedEffect = 0) {
		callmethod("BaseChart.setBackground", $this->ptr, $bgColor, $edgeColor, $raisedEffect);
	}
	function setWallpaper($img) {
		callmethod("BaseChart.setWallpaper", $this->ptr, $img);
	}
	function setBgImage($img, $align = Center) {
		callmethod("BaseChart.setBgImage", $this->ptr, $img, $align);
	}
	function setTransparentColor($c) {
		callmethod("BaseChart.setTransparentColor", $this->ptr, $c);
	}
	function setAntiAlias($antiAliasShape = 1, $antiAliasText = AutoAntiAlias) {
		callmethod("BaseChart.setAntiAlias", $this->ptr, $antiAliasShape, $antiAliasText);
	}
	function setSearchPath($path) {
		callmethod("BaseChart.setSearchPath", $this->ptr, $path);
	}
	
	function addTitle2($alignment, $text, $font = "", $fontSize = 12, $fontColor = TextColor,
		$bgColor = Transparent, $edgeColor = Transparent) {
		return new TextBox(callmethod("BaseChart.addTitle2", $this->ptr,
			$alignment, $text, $font, $fontSize, $fontColor, $bgColor, $edgeColor));
	}
	function addTitle($text, $font = "", $fontSize = 12, $fontColor = TextColor,
		$bgColor = Transparent, $edgeColor = Transparent) {
		return new TextBox(callmethod("BaseChart.addTitle", $this->ptr,
			$text, $font, $fontSize, $fontColor, $bgColor, $edgeColor));
	}
	function addLegend($x, $y, $vertical = 1, $font = "", $fontSize = 10) {
		return new LegendBox(callmethod("BaseChart.addLegend", $this->ptr,
			$x, $y, $vertical, $font, $fontSize));
	}
	function getLegend() {
		return new LegendBox(callmethod("BaseChart.getLegend", $this->ptr));
	}
	#//////////////////////////////////////////////////////////////////////////////////////
	#//	drawing primitives
	#//////////////////////////////////////////////////////////////////////////////////////
	function getDrawArea() {
		return new DrawArea(callmethod("BaseChart.getDrawArea", $this->ptr));
	}
	function addDrawObj($obj) {
		callmethod("BaseChart.addDrawObj", $obj->ptr);
		return $obj;
	}
	function addText($x, $y, $text, $font = "", $fontSize = 8, $fontColor = TextColor,
		$alignment = TopLeft, $angle = 0, $vertical = 0) {
		return new TextBox(callmethod("BaseChart.addText", $this->ptr,
			$x, $y, $text, $font, $fontSize, $fontColor, $alignment, $angle, $vertical));
	}
	function addLine($x1, $y1, $x2, $y2, $color = LineColor, $lineWidth = 1) {
		return new Line(callmethod("BaseChart.addLine", $this->ptr,
			$x1, $y1, $x2, $y2, $color, $lineWidth));
	}
	#//////////////////////////////////////////////////////////////////////////////////////
	#//	$color management methods
	#//////////////////////////////////////////////////////////////////////////////////////
	function setColor($paletteEntry, $color) {
		callmethod("BaseChart.setColor", $this->ptr, $paletteEntry, $color);
	}
	function setColors($colors) {
		if (count($colors) <= 0 or $colors[count($colors) - 1] != -1)
			$colors[] = -1;
		callmethod("BaseChart.setColors", $this->ptr, $colors);
	}
	function setColors2($paletteEntry, $colors) {
		if (count($colors) <= 0 or $colors[count($colors) - 1] != -1 )
			$colors[] = -1;
		callmethod("BaseChart.setColors2", $this->ptr, $paletteEntry, $colors);
	}
	function getColor($paletteEntry) {
		return callmethod("BaseChart.getColor", $this->ptr, $paletteEntry);
	}
	function dashLineColor($color, $dashPattern) {
		return callmethod("BaseChart.dashLineColor", $this->ptr, $color, $dashPattern);
	}
	function patternColor($c, $h = 0, $startX = 0, $startY = 0) {
	    if (!is_array($c))
	        return $this->patternColor2($c, $h, $startX);
		return callmethod("BaseChart.patternColor", $this->ptr, $c, $h, $startX, $startY);
    }
	function patternColor2($filename, $startX = 0, $startY = 0) {
		return callmethod("BaseChart.patternColor2", $this->ptr, $filename, $startX, $startY);
	}
    function gradientColor($startX, $startY = 90, $endX = 1, $endY = 0, $startColor = 0, $endColor = Null) {
		if (is_array($startX))
			return $this->gradientColor2($startX, $startY, $endX, $endY, $startColor);
		return callmethod("BaseChart.gradientColor", $this->ptr, $startX, $startY, $endX, $endY, $startColor, $endColor);
	}
	function gradientColor2($c, $angle = 90, $scale = 1, $startX = 0, $startY = 0) {
		return callmethod("BaseChart.gradientColor2", $this->ptr, $c, $angle, $scale, $startX, $startY);
    }
	#//////////////////////////////////////////////////////////////////////////////////////
	#//	locale support
	#//////////////////////////////////////////////////////////////////////////////////////
	function setDefaultFonts($normal, $bold = "", $italic = "", $boldItalic = "") {
		callmethod("BaseChart.setDefaultFonts", $this->ptr, $normal, $bold, $italic, $boldItalic);
	}
	function setFontTable($index, $font) {
		callmethod("BaseChart.setFontTable", $this->ptr, $index, $font);
	}
	function setNumberFormat($thousandSeparator = '~', $decimalPointChar = '.', $signChar = '-') {
		callmethod("BaseChart.setNumberFormat", $this->ptr, $thousandSeparator , $decimalPointChar, $signChar);
	}
	function setMonthNames($names) {
		callmethod("BaseChart.setMonthNames", $this->ptr, $names);
	}
	function setWeekDayNames($names) {
		callmethod("BaseChart.setWeekDayNames", $this->ptr, $names);
	}
	function setAMPM($AM, $PM) {
		callmethod("BaseChart.setAMPM", $this->ptr, $AM, $PM);
	}
	#//////////////////////////////////////////////////////////////////////////////////////
	#//	chart creation methods
	#//////////////////////////////////////////////////////////////////////////////////////
	function layout() {
		callmethod("BaseChart.layout", $this->ptr);
	}
	function makeChart($filename) {
		return callmethod("BaseChart.makeChart", $this->ptr, $filename);
	}
	function makeChart2($format) {
		return callmethod("BaseChart.makeChart2", $this->ptr, $format);
	}
	function makeChart3() {
		return new DrawArea(callmethod("BaseChart.makeChart3", $this->ptr));
	}
	function makeSession($id, $format = PNG) {
		session_register($id);
		global $HTTP_SESSION_VARS;
		$HTTP_SESSION_VARS[$id] = $GLOBALS[$id] = $this->makeChart2($format);
		return "img=".$id."&id=".uniqid(session_id())."&".SID;	
	}
	function getHTMLImageMap($url, $queryFormat = "", $extraAttr = "", $offsetX = 0, $offsetY = 0) {
		return callmethod("BaseChart.getHTMLImageMap", $this->ptr, $url, $queryFormat, $extraAttr, $offsetX, $offsetY);
	}
	function halfColor($c) {
		return callmethod("BaseChart.halfColor", $this->ptr, $c);
	}
	function autoColor() {
		return callmethod("BaseChart.autoColor", $this->ptr);
	}
}

class MultiChart extends BaseChart {
	function MultiChart($width, $height, $bgColor = BackgroundColor, $edgeColor = Transparent, $raisedEffect = 0) {
		$this->ptr = callmethod("MultiChart.create", $width, $height, $bgColor, $edgeColor, $raisedEffect);
		autoDestroy($this);
	}
	function addChart($x, $y, $c) {
		callmethod("MultiChart.addChart", $this->ptr, $x, $y, $c->ptr);
		$this->dependencies[] = $c;
	}
}

#///////////////////////////////////////////////////////////////////////////////////
#//	bindings to piechart.h
#///////////////////////////////////////////////////////////////////////////////////
class Sector {
	function Sector($ptr) {
		$this->ptr = $ptr;
	}
	function setExplode($distance = -1) {
		callmethod("Sector.setExplode", $this->ptr, $distance);
	}
	function setLabelFormat($formatString) {
		callmethod("Sector.setLabelFormat", $this->ptr, $formatString);
	}
	function setLabelStyle($font = "", $fontSize = 8, $fontColor = TextColor) {
		return new TextBox(callmethod("Sector.setLabelStyle", $this->ptr, $font, $fontSize, $fontColor));
	}
	function setLabelPos($pos, $joinLineColor = -1) {
		callmethod("Sector.setLabelPos", $this->ptr, $pos, $joinLineColor);
	}
	function setJoinLine($joinLineColor, $joinLineWidth = 1) {
		callmethod("Sector.setJoinLine", $this->ptr, $joinLineColor, $joinLineWidth);
	}
	function setColor($color, $edgeColor = -1, $joinLineColor = -1) {
		callmethod("Sector.setColor", $this->ptr, $color, $edgeColor, $joinLineColor);
	}
	function getImageCoor($offsetX = 0, $offsetY = 0) {
		return callmethod("Sector.getImageCoor", $this->ptr, $offsetX, $offsetY);
	}
	function getLabelCoor($offsetX = 0, $offsetY = 0) {
		return callmethod("Sector.getLabelCoor", $this->ptr, $offsetX, $offsetY);
	}
	function setLabelLayout($layoutMethod, $pos = -1) {
		callmethod("Sector.setLabelLayout", $this->ptr, $layoutMethod, $pos);
	}
}

class PieChart extends BaseChart {
	function PieChart($width, $height, $bgColor = BackgroundColor, $edgeColor = Transparent, $raisedEffect = 0) {
		$this->ptr = callmethod("PieChart.create", $width, $height, $bgColor, $edgeColor, $raisedEffect);
		autoDestroy($this);
	}
	function setPieSize($x, $y, $r) {
		callmethod("PieChart.setPieSize", $this->ptr, $x, $y, $r);
	}
	function set3D($depth = -1, $angle = -1, $shadowMode = 0) {
		if (is_array($depth))
			$this->set3D2($depth, $angle, $shadowMode);
		else 
			callmethod("PieChart.set3D", $this->ptr, $depth, $angle, $shadowMode);
	}
	function set3D2($depths, $angle = 45, $shadowMode = 0) {
		callmethod("PieChart.set3D2", $this->ptr, $depths, $angle, $shadowMode);
	}
	function setStartAngle($startAngle, $clockWise = 1) {
		callmethod("PieChart.setStartAngle", $this->ptr, $startAngle, $clockWise);
	}
	function setExplode($sectorNo, $distance = -1) {
		callmethod("PieChart.setExplode", $this->ptr, $sectorNo, $distance);
	}
	function setExplodeGroup($startSector, $endSector, $distance = -1) {
		callmethod("PieChart.setExplodeGroup", $this->ptr, $startSector, $endSector, $distance);
	}

	function setLabelFormat($formatString) {
		callmethod("PieChart.setLabelFormat", $this->ptr, $formatString);
	}
	function setLabelStyle($font = "", $fontSize = 8, $fontColor = TextColor) {
		return new TextBox(callmethod("PieChart.setLabelStyle", $this->ptr, $font,
			$fontSize, $fontColor));
	}
	function setLabelPos($pos, $joinLineColor = -1) {
		callmethod("PieChart.setLabelPos", $this->ptr, $pos, $joinLineColor);
	}
	function setLabelLayout($layoutMethod, $pos = -1, $topBound = -1, $bottomBound = -1) {
		callmethod("PieChart.setLabelLayout", $this->ptr, $layoutMethod, $pos, $topBound, $bottomBound);
	}
	function setJoinLine($joinLineColor, $joinLineWidth = 1) {
		callmethod("PieChart.setJoinLine", $this->ptr, $joinLineColor, $joinLineWidth);
	}
	function setLineColor($edgeColor, $joinLineColor = -1) {
		callmethod("PieChart.setLineColor", $this->ptr, $edgeColor, $joinLineColor);
	}

	function setData($data, $labels = Null) {
		callmethod("PieChart.setData", $this->ptr, $data, $labels);
	}
	function addExtraField($texts) {
		callmethod("PieChart.addExtraField", $this->ptr, $texts);
	}
	function sector($sectorNo) {
		return new Sector(callmethod("PieChart.sector", $this->ptr, $sectorNo));
	}
}

#///////////////////////////////////////////////////////////////////////////////////
#//	bindings to axis.h
#///////////////////////////////////////////////////////////////////////////////////
class Mark extends TextBox {
	function Mark($ptr) {
		$this->ptr = $ptr;
	}
	function setValue($value) {
		callmethod("Mark.setValue", $this->ptr, $value);
	}
	function setMarkColor($lineColor, $textColor = -1, $tickColor = -1) {
		callmethod("Mark.setMarkColor", $this->ptr, $lineColor, $textColor, $tickColor);
	}
	function setLineWidth($w) {
		callmethod("Mark.setLineWidth", $this->ptr, $w);
	}
	function setDrawOnTop($b) {
		callmethod("Mark.setDrawOnTop", $this->ptr, $b);
	}
	function getLine() {
		return callmethod("Mark.getLine", $this->ptr);
	}
}

class Axis {
	function Axis($ptr) {
		$this->ptr = $ptr;
	}
	function setLabelStyle($font = "", $fontSize = 8, $fontColor = TextColor, $fontAngle = 0) {
		return new TextBox(callmethod("Axis.setLabelStyle", $this->ptr, $font, $fontSize, $fontColor, $fontAngle));
	}
	function setLabelFormat($formatString) {
		callmethod("Axis.setLabelFormat", $this->ptr, $formatString);
	}
	function setLabelGap($d) {
		callmethod("Axis.setLabelGap", $this->ptr, $d);
	}
	
	function setTitle($text, $font = "arialbd.ttf", $fontSize = 8, $fontColor = TextColor) {
		return new TextBox(callmethod("Axis.setTitle", $this->ptr, $text, $font, $fontSize, $fontColor));
	}
	function setTitlePos($alignment, $titleGap = 6) {
		callmethod("Axis.setTitlePos", $this->ptr, $alignment, $titleGap);
	}
	function setColors($axisColor, $labelColor = TextColor, $titleColor = -1, $tickColor = -1) {
		callmethod("Axis.setColors", $this->ptr, $axisColor, $labelColor, $titleColor, $tickColor);
	}
	
	function setTickLength($majorTickLen, $minorTickLen = Null) {
		if (is_null($minorTickLen))
			callmethod("Axis.setTickLength", $this->ptr, $majorTickLen);
		else
			$this->setTickLength2($majorTickLen, $minorTickLen);
	}
	function setTickLength2($majorTickLen, $minorTickLen) {
		callmethod("Axis.setTickLength2", $this->ptr, $majorTickLen, $minorTickLen);
	}
	function setTickWidth($majorTickWidth, $minorTickWidth = -1) {
		callmethod("Axis.setTickWidth", $this->ptr, $majorTickWidth, $minorTickWidth);
	}
	function setTickColor($majorTickColor, $minorTickColor = -1) {
		callmethod("Axis.setTickColor", $this->ptr, $majorTickColor, $minorTickColor);
	}
	
	function setWidth($width) {
		callmethod("Axis.setWidth", $this->ptr, $width);
	}
	function setLength($length) {
		callmethod("Axis.setLength", $this->ptr, $length);
	}
	function setPos($x, $y, $align = Center) {
		callmethod("Axis.setPos", $this->ptr, $x, $y, $align);
	}
	function setTopMargin($topMargin) {
		$this->setMargin($topMargin);
	}	
	function setMargin($topMargin, $bottomMargin = 0) {
		callmethod("Axis.setMargin", $this->ptr, $topMargin, $bottomMargin);
	}	
	function setIndent($indent) {
		callmethod("Axis.setIndent", $this->ptr, $indent);
	}

	function setAutoScale($topExtension = 0.1, $bottomExtension = 0.1, $zeroAffinity = 0.8) {
		callmethod("Axis.setAutoScale", $this->ptr, $topExtension, $bottomExtension, $zeroAffinity);
	}	
	function setRounding($roundMin, $roundMax) {
		callmethod("Axis.setRounding", $this->ptr, $roundMin, $roundMax);
	}	
	function setTickDensity($majorTickDensity, $minorTickSpacing = -1) {
		callmethod("Axis.setTickDensity", $this->ptr, $majorTickDensity, $minorTickSpacing);
	}
	function setReverse($b = 1) {
		callmethod("Axis.setReverse", $this->ptr, $b);
	}	

	function setLabels($labels, $formatString = Null) {
		if (is_null($formatString))
			return new TextBox(callmethod("Axis.setLabels", $this->ptr, $labels));
		else
			return $this->setLabels2($labels, $formatString);
	}
	function setLabels2($labels, $formatString = "") {
		return new TextBox(callmethod("Axis.setLabels2", $this->ptr, $labels, $formatString));
	}
	
	function setLinearScale($lowerLimit = Null, $upperLimit = Null, $majorTickInc = 0, $minorTickInc = 0) {
		if (is_null($lowerLimit))
			$this->setLinearScale3a();
		else if (is_null($upperLimit))
			$this->setLinearScale3a($lowerLimit);
		else if (is_array($majorTickInc))
			$this->setLinearScale2($lowerLimit, $upperLimit, $majorTickInc);
		else	
			callmethod("Axis.setLinearScale", $this->ptr, $lowerLimit, $upperLimit, $majorTickInc, $minorTickInc);
	}	
	function setLinearScale2($lowerLimit, $upperLimit, $labels) {
		callmethod("Axis.setLinearScale2", $this->ptr, $lowerLimit, $upperLimit, $labels);
	}
	function setLinearScale3($formatString = "") {
		callmethod("Axis.setLinearScale3", $this->ptr, $formatString);
	}

	function setLogScale($lowerLimit = Null, $upperLimit = Null, $majorTickInc = 0, $minorTickInc = 0) {
		if (is_null($lowerLimit))
			$this->setLogScale3();
		else if (is_null($upperLimit))
			$this->setLogScale3($lowerLimit);
		else if (is_array($majorTickInc))
			$this->setLogScale2($lowerLimit, $upperLimit, $majorTickInc);
		else	
			callmethod("Axis.setLogScale", $this->ptr, $lowerLimit, $upperLimit, $majorTickInc, $minorTickInc);
	}	
	function setLogScale2($lowerLimit, $upperLimit, $labels = 0) {
		if (is_array($labels))
			callmethod("Axis.setLogScale2", $this->ptr, $lowerLimit, $upperLimit, $labels);
		else
			#compatibility with ChartDirector Ver 2.5
			$this->setLogScale($lowerLimit, $upperLimit, $labels);
	}
	function setLogScale3($formatString = "") {
		if (!is_string($formatString)) {
			#compatibility with ChartDirector Ver 2.5
			if ($formatString)
				$this->setLogScale3();
			else
				$this->setLinearScale3();
		}
		else
			callmethod("Axis.setLogScale3", $this->ptr, $formatString);
	}	
	
	function setDateScale($lowerLimit = Null, $upperLimit = Null, $majorTickInc = 0, $minorTickInc = 0) {
		if (is_null($lowerLimit))
			$this->setDateScale3();
		else if (is_null($upperLimit))
			$this->setDateScale3($lowerLimit);
		else if (is_array($majorTickInc))
			$this->setDateScale2($lowerLimit, $upperLimit, $majorTickInc);
		else	
			callmethod("Axis.setDateScale", $this->ptr, $lowerLimit, $upperLimit, $majorTickInc, $minorTickInc);
	}	
	function setDateScale2($lowerLimit, $upperLimit, $labels) {
		callmethod("Axis.setDateScale2", $this->ptr, $lowerLimit, $upperLimit, $labels);
	}
	function setDateScale3($formatString = "") {
		callmethod("Axis.setDateScale3", $this->ptr, $formatString);
	}

	function addLabel($pos, $label) {
		callmethod("Axis.addLabel", $this->ptr, $pos, $label);
	}
	function addMark($lineColor, $value, $text = "", $font = "", $fontSize = 8) {
		return new Mark(callmethod("Axis.addMark", $this->ptr, $lineColor, $value, $text, $font, $fontSize));
	}
	function addZone($startValue, $endValue, $color) {
		callmethod("Axis.addZone", $this->ptr, $startValue, $endValue, $color);
	}
		
	function getCoor($v) {
		return callmethod("Axis.getCoor", $this->ptr, $v);
	}
	function getLength() {
		return callmethod("Axis.getLength", $this->ptr);
	}
	function getMinValue() {
		return callmethod("Axis.getMinValue", $this->ptr);
	}
	function getMaxValue() {
		return callmethod("Axis.getMaxValue", $this->ptr);
	}
	function getScaleType() {
		return callmethod("Axis.getScaleType", $this->ptr);
	}
	
	function getTicks() {
		return callmethod("Axis.getTicks", $this->ptr);
	}
	function getLabel($i) {
		return callmethod("Axis.getLabel", $this->ptr, $i);
	}
}

class AngularAxis {
	function AngularAxis($ptr) {
		$this->ptr = $ptr;
	}
	function setLabelStyle($font = "bold", $fontSize = 10, $fontColor = TextColor, $fontAngle = 0) {
		return new TextBox(callmethod("AngularAxis.setLabelStyle", $this->ptr, $font, $fontSize, $fontColor, $fontAngle));
	}
	function setLabelGap($d) {
		callmethod("AngularAxis.setLabelGap", $this->ptr, $d);
	}
	
	function setLabels($labels, $formatString = Null) {
		if (is_null($formatString))
			return new TextBox(callmethod("AngularAxis.setLabels", $this->ptr, $labels));
		else
			return $this->setLabels2($labels, $formatString);
	}
	function setLabels2($labels, $formatString = "") {
		return new TextBox(callmethod("AngularAxis.setLabels2", $this->ptr, $labels, $formatString));
	}	
	function addLabel($pos, $label) {
		callmethod("AngularAxis.addLabel", $this->ptr, $pos, $label);
	}	

	function setLinearScale($lowerLimit, $upperLimit, $majorTickInc = 0, $minorTickInc = 0) {
		if (is_array($majorTickInc))
			$this->setLinearScale2($lowerLimit, $upperLimit, $majorTickInc);
		else	
			callmethod("AngularAxis.setLinearScale", $this->ptr, $lowerLimit, $upperLimit, $majorTickInc, $minorTickInc);
	}	
	function setLinearScale2($lowerLimit, $upperLimit, $labels) {
		callmethod("AngularAxis.setLinearScale2", $this->ptr, $lowerLimit, $upperLimit, $labels);
	}

	function getCoor($v) {
		return callmethod("AngularAxis.getCoor", $this->ptr, $v);
	}
	function getTicks() {
		return callmethod("AngularAxis.getTicks", $this->ptr);
	}
	function getLabel($i) {
		return callmethod("AngularAxis.getLabel", $this->ptr, $i);
	}
}	

#///////////////////////////////////////////////////////////////////////////////////
#//	bindings to layer.h
#///////////////////////////////////////////////////////////////////////////////////
class DataSet {
	function DataSet($ptr) {
		$this->ptr = $ptr;
	}
	function setData($data) {
		callmethod("DataSet.setData", $this->ptr, $data);
	}
	function setDataName($name) {
		callmethod("DataSet.setDataName", $this->ptr, $name);
	}
	function setDataColor($dataColor, $edgeColor = -1, $shadowColor = -1, $shadowEdgeColor = -1) {
		callmethod("DataSet.setDataColor", $this->ptr, $dataColor, $edgeColor, $shadowColor, $shadowEdgeColor);
	}
	function setUseYAxis2($b = 1) {
		callmethod("DataSet.setUseYAxis2", $this->ptr, $b);
	}
	function setLineWidth($w) {
		callmethod("DataSet.setLineWidth", $this->ptr, $w);
	}
	
	function setDataLabelFormat($formatString) {
		callmethod("DataSet.setDataLabelFormat", $this->ptr, $formatString);
	}
	function setDataLabelStyle($font = "", $fontSize = 8, $fontColor = TextColor, $fontAngle = 0) {
		return new TextBox(callmethod("DataSet.setDataLabelStyle", $this->ptr, $font, $fontSize, $fontColor, $fontAngle));
	}
	
	function setDataSymbol($symbol, $size = 5, $fillColor = -1, $edgeColor = -1, $lineWidth = 1) {
	    if (!is_numeric($symbol))
        	return $this->setDataSymbol2($symbol);
		callmethod("DataSet.setDataSymbol", $this->ptr, $symbol, $size, $fillColor, $edgeColor, $lineWidth);
	}
	function setDataSymbol2($image) {
	    if (!is_string($image))
        	return $this->setDataSymbol3($image);
		callmethod("DataSet.setDataSymbol2", $this->ptr, $image);
	}
	function setDataSymbol3($image) {
		callmethod("DataSet.setDataSymbol3", $this->ptr, $image->ptr);
	}
}

class Layer {
	function Layer($ptr) {
		$this->ptr = $ptr;
	}
	function setSize($x, $y, $w, $h, $swapXY = 0) {
		callmethod("Layer.setSize", $this->ptr, $x, $y, $w, $h, $swapXY);
	}
	function setBorderColor($color, $raisedEffect = 0) {
		callmethod("Layer.setBorderColor", $this->ptr, $color, $raisedEffect);
	}
	function set3D($d = -1, $zGap = 0) {
		callmethod("Layer.set3D", $this->ptr, $d, $zGap);
	}
	function set3D2($xDepth, $yDepth, $xGap, $yGap) {
		callmethod("Layer.set3D2", $this->ptr, $xDepth, $yDepth, $xGap, $yGap);
	}
	function setLineWidth($w) {
		callmethod("Layer.setLineWidth", $this->ptr, $w);
	}
	function setLegend($m) {
		callmethod("Layer.setLegend", $this->ptr, $m);
	}	
	
	function setDataCombineMethod($m) {
		callmethod("Layer.setDataCombineMethod", $this->ptr, $m);
	}
	function addDataSet($data, $color = -1, $name = "") {
		return new DataSet(callmethod("Layer.addDataSet", $this->ptr, $data, $color, $name));
	}
	function addDataGroup($name = "") {
		callmethod("Layer.addDataGroup", $this->ptr, $name);
	}
	function addExtraField($texts) {
		callmethod("Layer.addExtraField", $this->ptr, $texts);
	}
	function getDataSet($dataSet) {
		return new DataSet(callmethod("Layer.getDataSet", $this->ptr, $dataSet));
	}
	function setUseYAxis2($b = 1) {
		callmethod("Layer.setUseYAxis2", $this->ptr, $b);
	}

	function setXData($xData, $maxValue = Null) {
		if (is_null($maxValue))
			callmethod("Layer.setXData", $this->ptr, $xData);
		else
			$this->setXData2($xData, $maxValue);
	}
	function setXData2($minValue, $maxValue) {
		callmethod("Layer.setXData2", $this->ptr, $minValue, $maxValue);
	}
	
	function getMinX() {
		return callmethod("Layer.getMinX", $this->ptr);
	}
	function getMaxX() {
		return callmethod("Layer.getMaxX", $this->ptr);
	}
	function getMaxY($yAxis = 1) {
		return callmethod("Layer.getMaxY", $this->ptr, $yAxis);
	}
	function getMinY($yAxis = 1) {
		return callmethod("Layer.getMinY", $this->ptr, $yAxis);
	}
	function getDepthX() {
		return callmethod("Layer.getDepthX", $this->ptr);
	}
	function getDepthY() {
		return callmethod("Layer.getDepthY", $this->ptr);
	}
	function getXCoor($v) {
		return callmethod("Layer.getXCoor", $this->ptr, $v);
	}
	function getYCoor($v, $yAxis = 1) {
		return callmethod("Layer.getYCoor", $this->ptr, $v, $yAxis);
	}
	function xZoneColor($threshold, $belowColor, $aboveColor) {
		return callmethod("Layer.xZoneColor", $this->ptr, $threshold, $belowColor, $aboveColor);
	}
	function yZoneColor($threshold, $belowColor, $aboveColor, $mainAxis = 1) {
		return callmethod("Layer.yZoneColor", $this->ptr, $threshold, $belowColor, $aboveColor, $mainAxis);
	}

	function setDataLabelFormat($formatString) {
		callmethod("Layer.setDataLabelFormat", $this->ptr, $formatString);
	}
	function setDataLabelStyle($font = "", $fontSize = 8, $fontColor = TextColor, $fontAngle = 0) {
		return new TextBox(callmethod("Layer.setDataLabelStyle", $this->ptr, $font, $fontSize, $fontColor, $fontAngle));
	}
	function setAggregateLabelFormat($formatString) {
		callmethod("Layer.setAggregateLabelFormat", $this->ptr, $formatString);
	}
	function setAggregateLabelStyle($font = "", $fontSize = 8, $fontColor = TextColor, $fontAngle = 0) {
		return new TextBox(callmethod("Layer.setAggregateLabelStyle", $this->ptr, $font, $fontSize, $fontColor, $fontAngle));
	}
	function addCustomDataLabel($dataSet, $dataItem, $label, $font = "", $fontSize = 8, $fontColor = TextColor, $fontAngle = 0) {
		return new TextBox(callmethod("Layer.addCustomDataLabel", $this->ptr, $dataSet, $dataItem, $label, $font, $fontSize, $fontColor, $fontAngle));
	}
	function addCustomAggregateLabel($dataItem, $label, $font = "", $fontSize = 8, $fontColor = TextColor, $fontAngle = 0) {
		return new TextBox(callmethod("Layer.addCustomAggregateLabel", $this->ptr, $dataItem, $label, $font, $fontSize, $fontColor, $fontAngle));
	}
	
	function getImageCoor($dataSet, $dataItem = Null, $offsetX = 0, $offsetY = 0) {
		if (is_null($dataItem))
			return $this->getImageCoor2($dataSet, $offsetX, $offsetY);
		return callmethod("Layer.getImageCoor", $this->ptr, $dataSet, $dataItem, $offsetX, $offsetY);
	}
	function getImageCoor2($dataItem, $offsetX = 0, $offsetY = 0) {
		return callmethod("Layer.getImageCoor2", $this->ptr, $dataItem, $offsetX, $offsetY);
	}
	function getHTMLImageMap($url, $queryFormat = "", $extraAttr = "", $offsetX = 0, $offsetY = 0) {
		return callmethod("Layer.getHTMLImageMap", $this->ptr, $url, $queryFormat, $extraAttr, $offsetX, $offsetY);
	}
}

#///////////////////////////////////////////////////////////////////////////////////
#//	bindings to barlayer.h
#///////////////////////////////////////////////////////////////////////////////////
class BarLayer extends Layer {
	function BarLayer($ptr) {
		$this->ptr = $ptr;
	}
	function setBarGap($barGap, $subBarGap = 0.2) {
		callmethod("BarLayer.setBarGap", $this->ptr, $barGap, $subBarGap);
	}
	function setBarWidth($barWidth, $subBarWidth = -1) {
		callmethod("BarLayer.setBarWidth", $this->ptr, $barWidth, $subBarWidth);
	}
	function setMinLabelSize($s) {
		callmethod("BarLayer.setMinLabelSize", $this->ptr, $s);
	}
}

#///////////////////////////////////////////////////////////////////////////////////
#//	bindings to linelayer.h
#///////////////////////////////////////////////////////////////////////////////////
class LineLayer extends Layer {
	function LineLayer($ptr) {
		$this->ptr = $ptr;
	}
	function setSymbolScale($zDataX, $scaleTypeX = PixelScale, $zDataY = Null, $scaleTypeY = PixelScale) {
		callmethod("LineLayer.setSymbolScale", $this->ptr, $zDataX, $scaleTypeX, $zDataY, $scaleTypeY);
	}
	function setGapColor($lineColor, $lineWidth = -1) {
		callmethod("LineLayer.setGapColor", $this->ptr, $lineColor, $lineWidth);
	}
	function setImageMapWidth($width) {
		callmethod("LineLayer.setImageMapWidth", $this->ptr, $width);
	}
	function getLine($dataSet = 0) {
		return callmethod("LineLayer.getLine", $this->ptr, $dataSet);
	}
}

class ScatterLayer extends LineLayer {
	function ScatterLayer($ptr) {
		$this->ptr = $ptr;
	}
}

class InterLineLayer extends LineLayer {
	function InterLineLayer($ptr) {
		$this->ptr = $ptr;
	}
}

class SplineLayer extends LineLayer {
	function SplineLayer($ptr) {
		$this->ptr = $ptr;
	}
	function setTension($tension) {
		return callmethod("SplineLayer.setTension", $this->ptr, $tension);
	}
}

class StepLineLayer extends LineLayer {
	function StepLineLayer($ptr) {
		$this->ptr = $ptr;
	}
	function setAlignment($a) {
		return callmethod("StepLineLayer.getLine", $this->ptr, $a);
	}
}

#///////////////////////////////////////////////////////////////////////////////////
#//	bindings to arealayer.h
#///////////////////////////////////////////////////////////////////////////////////
class AreaLayer extends Layer {
	function AreaLayer($ptr) {
		$this->ptr = $ptr;
	}
	function setMinLabelSize($s) {
		callmethod("AreaLayer.setMinLabelSize", $this->ptr, $s);
	}
}

#///////////////////////////////////////////////////////////////////////////////////
#//	bindings to trendlayer.h
#///////////////////////////////////////////////////////////////////////////////////
class TrendLayer extends Layer {
	function TrendLayer($ptr) {
		$this->ptr = $ptr;
	}
	function setImageMapWidth($width) {
		callmethod("TrendLayer.setImageMapWidth", $this->ptr, $width);
	}
	function getLine() {
		return callmethod("TrendLayer.getLine", $this->ptr);
	}
	function addConfidenceBand($confidence, $upperFillColor, $upperEdgeColor = Transparent, $upperLineWidth = 1,
		$lowerFillColor = -1, $lowerEdgeColor = -1, $lowerLineWidth = -1) {
		callmethod("TrendLayer.addConfidenceBand", $this->ptr, $confidence, $upperFillColor, $upperEdgeColor, $upperLineWidth,
			$lowerFillColor, $lowerEdgeColor, $lowerLineWidth);
	}
	function addPredictionBand($confidence, $upperFillColor, $upperEdgeColor = Transparent, $upperLineWidth = 1,
		$lowerFillColor = -1, $lowerEdgeColor = -1, $lowerLineWidth = -1) {
		callmethod("TrendLayer.addPredictionBand", $this->ptr, $confidence, $upperFillColor, $upperEdgeColor, $upperLineWidth,
			$lowerFillColor, $lowerEdgeColor, $lowerLineWidth);
	}	
	function getSlope() {
		return callmethod("TrendLayer.getSlope", $this->ptr);
	}
	function getIntercept() {
		return callmethod("TrendLayer.getIntercept", $this->ptr);
	}
	function getCorrelation() {
		return callmethod("TrendLayer.getCorrelation", $this->ptr);
	}
	function getStdError() {
		return callmethod("TrendLayer.getStdError", $this->ptr);
	}
}

#///////////////////////////////////////////////////////////////////////////////////
#//	bindings to hloclayer.h
#///////////////////////////////////////////////////////////////////////////////////
class HLOCLayer extends Layer {
	function HLOCLayer($ptr) {
		$this->ptr = $ptr;
	}
	function setDataGap($gap) {
		callmethod("HLOCLayer.setDataGap", $this->ptr, $gap);
	}
	function setChartType($t) {
		callmethod("HLOCLayer.setChartType", $this->ptr, $t);
	}
}

class CandleStickLayer extends HLOCLayer {
	function CandleStickLayer($ptr) {
		$this->ptr = $ptr;
	}
}

class BoxWhiskerLayer extends HLOCLayer {
	function BoxWhiskerLayer($ptr) {
		$this->ptr = $ptr;
	}
}

#///////////////////////////////////////////////////////////////////////////////////
#//	bindings to xychart.h
#///////////////////////////////////////////////////////////////////////////////////
class PlotArea {
	function PlotArea($ptr) {
		$this->ptr = $ptr;
	}
	function setBackground($color, $altBgColor = -1, $edgeColor = LineColor) {
		callmethod("PlotArea.setBackground", $this->ptr, $color, $altBgColor, $edgeColor);
	}
	function setBackground2($img, $align = Center) {
		callmethod("PlotArea.setBackground2", $this->ptr, $img, $align);
	}
	function setGridColor($hGridColor, $vGridColor = Transparent, $minorHGridColor = -1, $minorVGridColor = -1) {
		callmethod("PlotArea.setGridColor", $this->ptr, $hGridColor, $vGridColor, $minorHGridColor, $minorVGridColor);
	}
	function setGridWidth($hGridWidth, $vGridWidth = -1, $minorHGridWidth = -1, $minorVGridWidth = -1) {
		callmethod("PlotArea.setGridWidth", $this->ptr, $hGridWidth, $vGridWidth, $minorHGridWidth, $minorVGridWidth);
	}
}

class XYChart extends BaseChart {
	function XYChart($width, $height, $bgColor = BackgroundColor, $edgeColor = Transparent, $raisedEffect = 0) {
		$this->ptr = callmethod("XYChart.create", $width, $height, $bgColor, $edgeColor, $raisedEffect);
		$this->xAxis = new Axis(callmethod("XYChart.xAxis", $this->ptr));
		$this->xAxis2 = new Axis(callmethod("XYChart.xAxis2", $this->ptr));
		$this->yAxis = new Axis(callmethod("XYChart.yAxis", $this->ptr));
		$this->yAxis2 = new Axis(callmethod("XYChart.yAxis2", $this->ptr));
		autoDestroy($this);
	}
	function yAxis() {
		return new Axis(callmethod("XYChart.yAxis", $this->ptr));
	}
	function yAxis2() {
		return new Axis(callmethod("XYChart.yAxis2", $this->ptr));
	}
	function syncYAxis($slope = 1, $intercept = 0) {
		callmethod("XYChart.syncYAxis", $this->ptr, $slope, $intercept);
	}
	function setYAxisOnRight($b = 1) {
		callmethod("XYChart.setYAxisOnRight", $this->ptr, $b);
	}
	function xAxis() {
		return new Axis(callmethod("XYChart.xAxis", $this->ptr));
	}
	function xAxis2() {
		return new Axis(callmethod("XYChart.xAxis2", $this->ptr));
	}
	function setXAxisOnTop($b = 1) {
		callmethod("XYChart.setXAxisOnTop", $this->ptr, $b);
	}
	function swapXY($b = 1) {
		callmethod("XYChart.swapXY", $this->ptr, $b);
	}

	function setPlotArea($x, $y, $width, $height, $bgColor = Transparent, $altBgColor = -1,
		$edgeColor = LineColor, $hGridColor = 0xc0c0c0, $vGridColor = Transparent) {
		return new PlotArea(callmethod("XYChart.setPlotArea", $this->ptr,
			$x, $y, $width, $height, $bgColor, $altBgColor, $edgeColor, $hGridColor, $vGridColor));
	}
	function setClipping($margin = 0) {
		callmethod("XYChart.setClipping", $this->ptr, $margin);
	}

	function addBarLayer($data = Null, $color = -1, $name = "", $depth = 0) {
		if ($data != Null)
			return new BarLayer(callmethod("XYChart.addBarLayer", $this->ptr, $data, $color, $name, $depth));
		else
			return $this->addBarLayer2();
	}
	function addBarLayer2($dataCombineMethod = Side, $depth = 0) {
		return new BarLayer(callmethod("XYChart.addBarLayer2", $this->ptr, $dataCombineMethod, $depth));
	}
	function addBarLayer3($data, $colors = Null, $names = Null, $depth = 0) {
		return new BarLayer(callmethod("XYChart.addBarLayer3", $this->ptr, $data, $colors, $names, $depth));
	}
	function addLineLayer($data = Null, $color = -1, $name = "", $depth = 0) {
		if ($data != Null)
			return new LineLayer(callmethod("XYChart.addLineLayer", $this->ptr, $data, $color, $name, $depth));
		else
			return $this->addLineLayer2();
	}
	function addLineLayer2($dataCombineMethod = Overlay, $depth = 0) {
		return new LineLayer(callmethod("XYChart.addLineLayer2", $this->ptr, $dataCombineMethod, $depth));
	}
	function addAreaLayer($data = Null, $color = -1, $name = "", $depth = 0) {
		if ($data != Null)
			return new AreaLayer(callmethod("XYChart.addAreaLayer", $this->ptr, $data, $color, $name, $depth));
		else
			return $this->addAreaLayer2();
	}
	function addAreaLayer2($dataCombineMethod = Stack, $depth = 0) {
		return new AreaLayer(callmethod("XYChart.addAreaLayer2", $this->ptr, $dataCombineMethod, $depth));
	}
	function addHLOCLayer($highData = Null, $lowData = Null, $openData = Null, $closeData = Null, $color = -1) {
		if ($highData != Null)
			return new HLOCLayer(callmethod("XYChart.addHLOCLayer", $this->ptr,
				$highData, $lowData, $openData, $closeData, $color));
		else
			return $this->addHLOCLayer2();
	}
	function addHLOCLayer2() {
		return new HLOCLayer(callmethod("XYChart.addHLOCLayer2", $this->ptr));
	}
	function addScatterLayer($xData, $yData, $name = "", $symbol = SquareSymbol, $symbolSize = 5, $fillColor = -1, $edgeColor = -1) {
		return new ScatterLayer(callmethod("XYChart.addScatterLayer", $this->ptr, $xData, $yData, $name, $symbol, $symbolSize, $fillColor, $edgeColor));
	}
	function addCandleStickLayer($highData, $lowData, $openData, $closeData, $riseColor = 0xffffff, $fallColor = 0x0, $edgeColor = LineColor) {
		return new CandleStickLayer(callmethod("XYChart.addCandleStickLayer", $this->ptr, $highData, $lowData, $openData, $closeData, $riseColor, $fallColor, $edgeColor));
	}
	function addBoxWhiskerLayer($boxTop, $boxBottom, $maxData = Null, $minData = Null, $midData = Null, $fillColor = -1, $whiskerColor = LineColor, $edgeColor = LineColor) {
		return new BoxWhiskerLayer(callmethod("XYChart.addBoxWhiskerLayer", $this->ptr, $boxTop, $boxBottom, $maxData, $minData, $midData, $fillColor, $whiskerColor, $edgeColor));
	}
	function addTrendLayer($data, $color = -1, $name = "", $depth = 0) {
		return new TrendLayer(callmethod("XYChart.addTrendLayer", $this->ptr, $data, $color, $name, $depth));
	}
	function addTrendLayer2($xData, $yData, $color = -1, $name = "", $depth = 0) {
		return new TrendLayer(callmethod("XYChart.addTrendLayer2", $this->ptr, $xData, $yData, $color, $name, $depth));
	}
	function addSplineLayer($data = Null, $color = -1, $name = "") {
		return new SplineLayer(callmethod("XYChart.addSplineLayer", $this->ptr, $data, $color, $name));
	}
	function addStepLineLayer($data = Null, $color = -1, $name = "") {
		return new StepLineLayer(callmethod("XYChart.addStepLineLayer", $this->ptr, $data, $color, $name));
	}
	function addInterLineLayer($line1, $line2, $color12, $color21 = -1) {
		return new InterLineLayer(callmethod("XYChart.addInterLineLayer", $this->ptr, $line1, $line2, $color12, $color21));
	}
}

#///////////////////////////////////////////////////////////////////////////////////
#//	bindings to polarchart.h
#///////////////////////////////////////////////////////////////////////////////////
class PolarLayer
{
	function PolarLayer($ptr) {
		$this->ptr = $ptr;
	}
	function setData($data, $color = -1, $name = "") {
		callmethod("PolarLayer.setData", $this->ptr, $data, $color, $name);
	}
	function setAngles($angles) {
		callmethod("PolarLayer.setAngles", $this->ptr, $angles);
	}

	function setBorderColor($edgeColor) {
		callmethod("PolarLayer.setBorderColor", $this->ptr, $edgeColor);
	}
	function setLineWidth($w) {
		callmethod("PolarLayer.setLineWidth", $this->ptr, $w);
	}

	function setDataSymbol($symbol, $size = 7, $fillColor = -1, $edgeColor = -1, $lineWidth = 1) {
	    if (!is_numeric($symbol))
        	return $this->setDataSymbol2($symbol);
		callmethod("PolarLayer.setDataSymbol", $this->ptr, $symbol, $size, $fillColor, $edgeColor, $lineWidth);
	}
	function setDataSymbol2($image) {
	    if (!is_string($image))
        	return $this->setDataSymbol3($image);
		callmethod("PolarLayer.setDataSymbol2", $this->ptr, $image);
	}
	function setDataSymbol3($image) {
		callmethod("PolarLayer.setDataSymbol3", $this->ptr, $image->ptr);
	}
	function setSymbolScale($zData, $scaleType = PixelScale) {
		callmethod("PolarLayer.setSymbolScale", $this->ptr, $zData, $scaleType);
	}	

	function setImageMapWidth($width) {
		callmethod("PolarLayer.setImageMapWidth", $this->ptr, $width);
	}
	function getImageCoor($dataItem, $offsetX = 0, $offsetY = 0) {
		return callmethod("PolarLayer.getImageCoor", $this->ptr, $dataItem, $offsetX, $offsetY);
	}
	function getHTMLImageMap($url, $queryFormat = "", $extraAttr = "", $offsetX = 0, $offsetY = 0) {
		return callmethod("PolarLayer.getHTMLImageMap", $this->ptr, $url, $queryFormat, $extraAttr, $offsetX, $offsetY);
	}

	function setDataLabelFormat($formatString) {
		callmethod("PolarLayer.setDataLabelFormat", $this->ptr, $formatString);
	}
	function setDataLabelStyle($font = "", $fontSize = 8, $fontColor = TextColor, $fontAngle = 0) {
		return new TextBox(callmethod("PolarLayer.setDataLabelStyle", $this->ptr, $font, $fontSize, $fontColor, $fontAngle));
	}
	function addCustomDataLabel($i, $label, $font = "", $fontSize = 8, $fontColor = TextColor, $fontAngle = 0) {
		return new TextBox(callmethod("PolarLayer.addCustomDataLabel", $this->ptr, $i, $label, $font, $fontSize, $fontColor, $fontAngle));
	}
}

class PolarAreaLayer extends PolarLayer {
	function PolarAreaLayer($ptr) {
		$this->ptr = $ptr;
	}
}

class PolarLineLayer extends PolarLayer {
	function PolarLineLayer($ptr) {
		$this->ptr = $ptr;
	}
	function setCloseLoop($b) {
		callmethod("PolarLineLayer.setCloseLoop", $this->ptr, $b);
	}
	function setGapColor($lineColor, $lineWidth = -1) {
		callmethod("PolarLineLayer.setGapColor", $this->ptr, $lineColor, $lineWidth);
	}
}

class PolarSplineLineLayer extends PolarLineLayer {
	function PolarSplineLineLayer($ptr) {
		$this->ptr = $ptr;
	}
	function setTension($tension) {
		callmethod("PolarSplineLineLayer.setTension", $this->ptr, $tension);
	}
}

class PolarSplineAreaLayer extends PolarAreaLayer {
	function PolarSplineAreaLayer($ptr) {
		$this->ptr = $ptr;
	}
	function setTension($tension) {
		callmethod("PolarSplineAreaLayer.setTension", $this->ptr, $tension);
	}
}

class PolarChart extends BaseChart
{
	function PolarChart($width, $height, $bgColor = BackgroundColor, $edgeColor = Transparent, $raisedEffect = 0) {
		$this->ptr = callmethod("PolarChart.create", $width, $height, $bgColor, $edgeColor, $raisedEffect);
		$this->angularAxis = new AngularAxis(callmethod("PolarChart.angularAxis", $this->ptr));
		$this->radialAxis = new Axis(callmethod("PolarChart.radialAxis", $this->ptr));
	}
	function setPlotArea($x, $y, $r, $bgColor = Transparent, $edgeColor = Transparent, $edgeWidth = 1) {
		callmethod("PolarChart.setPlotArea", $this->ptr, $x, $y, $r, $bgColor, $edgeColor, $edgeWidth);
	}
	function setGridColor($rGridColor = 0x80000000, $rGridWidth = 1, $aGridColor = 0x80000000, $aGridWidth = 1) {
		callmethod("PolarChart.setGridColor", $this->ptr, $rGridColor, $rGridWidth, $aGridColor, $aGridWidth);
	}
	function setGridStyle($polygonGrid, $gridOnTop = 1) {
		callmethod("PolarChart.setGridStyle", $this->ptr, $polygonGrid, $gridOnTop);
	}
	function setStartAngle($startAngle, $clockwise = 1) {
		callmethod("PolarChart.setStartAngle", $this->ptr, $startAngle, $clockwise);
	}

	function angularAxis() {
		return new AngularAxis(callmethod("PolarChart.angularAxis", $this->ptr));
	}
	function radialAxis() {
		return new Axis(callmethod("PolarChart.radialAxis", $this->ptr));
	}
	function getXCoor($r, $a) {
		return callmethod("PolarChart.getXCoor", $this->ptr, $r, $a);
	}
	function getYCoor($r, $a) {
		return callmethod("PolarChart.getYCoor", $this->ptr, $r, $a);
	}
	function addExtraField($texts) {
		callmethod("PolarChart.addExtraField", $this->ptr, $texts);
	}

	function addAreaLayer($data, $color = -1, $name = "") {
		return new PolarAreaLayer(callmethod("PolarChart.addAreaLayer", $this->ptr, $data, $color, $name));
	}
	function addLineLayer($data, $color = -1, $name = "") {
		return new PolarLineLayer(callmethod("PolarChart.addLineLayer", $this->ptr, $data, $color, $name));
	}
	function addSplineLineLayer($data, $color = -1, $name = "") {
		return new PolarSplineLineLayer(callmethod("PolarChart.addSplineLineLayer", $this->ptr, $data, $color, $name));
	}
	function addSplineAreaLayer($data, $color = -1, $name = "") {
		return new PolarSplineAreaLayer(callmethod("PolarChart.addSplineAreaLayer", $this->ptr, $data, $color, $name));
	}
}

#///////////////////////////////////////////////////////////////////////////////////
#//	bindings to chartdir.h
#///////////////////////////////////////////////////////////////////////////////////
function getCopyright() {
	return callmethod("getCopyright");
}

function getVersion() {
	return callmethod("getVersion");
}

function getDescription() {
	return callmethod("getDescription");
}

function getBootLog() {
	return callmethod("getBootLog");
}

function libgTTFTest($font = "", $fontIndex = 0, $fontHeight = 8, $fontWidth = 8, $angle = 0) {
    return callmethod("testFont", $font, $fontIndex, $fontHeight, $fontWidth, $angle);
}

function testFont($font = "", $fontIndex = 0, $fontHeight = 8, $fontWidth = 8, $angle = 0) {
    return callmethod("testFont", $font, $fontIndex, $fontHeight, $fontWidth, $angle);
}

function setLicenseCode($licCode) {
    return callmethod("setLicenseCode", $licCode);
}

function chartTime($y, $m = Null, $d = 1, $h = 0, $n = 0, $s = 0) {
	if (is_null($m))
		return chartTime2($y);
	else
	    return callmethod("chartTime", $y, $m, $d, $h, $n, $s);
}

function chartTime2($t) {
    return callmethod("chartTime2", $t);
}

#///////////////////////////////////////////////////////////////////////////////////
#//	bindings to rantable.h
#///////////////////////////////////////////////////////////////////////////////////
class RanTable
{
	function RanTable($seed, $noOfCols, $noOfRows) {
		$this->ptr = callmethod("RanTable.create", $seed, $noOfCols, $noOfRows);
	}
	function __del__() {
		callmethod("RanTable.destroy", $this->ptr);
	}
	
	function setCol($colNo, $minValue, $maxValue, $p4 = Null, $p5 = -1E+308, $p6 = 1E+308) {
		if (is_null($p4))
			callmethod("RanTable.setCol", $this->ptr, $colNo, $minValue, $maxValue);
		else
			$this->setCol2($colNo, $minValue, $maxValue, $p4, $p5, $p6);
	}
	function setCol2($colNo, $startValue, $minDelta, $maxDelta, $lowerLimit = -1E+308, $upperLimit = 1E+308) {
		callmethod("RanTable.setCol2", $this->ptr, $colNo, $startValue, $minDelta, $maxDelta, $lowerLimit, $upperLimit);
	}
	function setDateCol($i, $startTime, $tickInc, $weekDayOnly = 0) {
		callmethod("RanTable.setDateCol", $this->ptr, $i, $startTime, $tickInc, $weekDayOnly);
	}
	function setHLOCCols($i, $startValue, $minDelta, $maxDelta,	$lowerLimit = 0, $upperLimit = 1E+308) {
		callmethod("RanTable.setHLOCCols", $this->ptr, $i, $startValue, $minDelta, $maxDelta, $lowerLimit, $upperLimit);
	}
	function getCol($i) {
		return callmethod("RanTable.getCol", $this->ptr, $i);
	}
}

#///////////////////////////////////////////////////////////////////////////////////
#//	bindings to datafilter.h
#///////////////////////////////////////////////////////////////////////////////////
class ArrayMath
{
	function ArrayMath($a) {
		$this->ptr = callmethod("ArrayMath.create", $a);
	}
	function __del__() {
		callmethod("ArrayMath.destroy", $this->ptr);
	}
	
	function add($b) { 
		if (!is_array($b)) 
			$this->add2($b);
		else 
			callmethod("ArrayMath.add", $this->ptr, $b);
		return $this;
	}
	function add2($b) {
		callmethod("ArrayMath.add2", $this->ptr, $b);
		return $this;
	}
	function sub($b) {
		if (!is_array($b)) 
			$this->sub2($b);
		else
			callmethod("ArrayMath.sub", $this->ptr, $b);
		return $this;
	}
	function sub2($b) {
		callmethod("ArrayMath.sub2", $this->ptr, $b);
		return $this;
	}
	function mul($b) {
		if (!is_array($b)) 
			$this->mul2($b);
		else
			callmethod("ArrayMath.mul", $this->ptr, $b);
		return $this;
	}
	function mul2($b) {
		callmethod("ArrayMath.mul2", $this->ptr, $b);
		return $this;
	}
	function div($b) {
		if (!is_array($b)) 
			$this->div2($b);
		else
			callmethod("ArrayMath.div", $this->ptr, $b);
		return $this;
	}
	function div2($b) {
		callmethod("ArrayMath.div2", $this->ptr, $b);
		return $this;
	}
	function delta($offset = 1) {
		callmethod("ArrayMath.delta", $this->ptr, $offset);
		return $this;
	}
	function abs() {
		callmethod("ArrayMath.abs", $this->ptr);
		return $this;
	}
	function acc() {
		callmethod("ArrayMath.acc", $this->ptr);
		return $this;
	}
	
	function selectGTZ($b = Null, $fillValue = 0) { callmethod("ArrayMath.selectGTZ", $this->ptr, $b, $fillValue); return $this; }
	function selectGEZ($b = Null, $fillValue = 0) { callmethod("ArrayMath.selectGEZ", $this->ptr, $b, $fillValue); return $this; }
	function selectLTZ($b = Null, $fillValue = 0) { callmethod("ArrayMath.selectLTZ", $this->ptr, $b, $fillValue); return $this; }
	function selectLEZ($b = Null, $fillValue = 0) { callmethod("ArrayMath.selectLEZ", $this->ptr, $b, $fillValue); return $this; }
	function selectEQZ($b = Null, $fillValue = 0) { callmethod("ArrayMath.selectEQZ", $this->ptr, $b, $fillValue); return $this; }
	function selectNEZ($b = Null, $fillValue = 0) { callmethod("ArrayMath.selectNEZ", $this->ptr, $b, $fillValue); return $this; }

	function selectStartOfHour($majorTickStep = 1, $initialMargin = 300) {
		callmethod("ArrayMath.selectStartOfHour", $this->ptr, $majorTickStep, $initialMargin);
		return $this; 
	}
	function selectStartOfDay($majorTickStep = 1, $initialMargin = 10800) {
		callmethod("ArrayMath.selectStartOfDay", $this->ptr, $majorTickStep, $initialMargin);
		return $this; 
	}
	function selectStartOfWeek($majorTickStep = 1, $initialMargin = 172800) {
		callmethod("ArrayMath.selectStartOfWeek", $this->ptr, $majorTickStep, $initialMargin);
		return $this; 
	}
	function selectStartOfMonth($majorTickStep = 1, $initialMargin = 432000) {
		callmethod("ArrayMath.selectStartOfMonth", $this->ptr, $majorTickStep, $initialMargin);
		return $this; 
	}
	function selectStartOfYear($majorTickStep = 1, $initialMargin = 5184000) {
		callmethod("ArrayMath.selectStartOfYear", $this->ptr, $majorTickStep, $initialMargin);
		return $this; 
	}

	function trim($startIndex = 0, $len = -1) {
		callmethod("ArrayMath.trim", $this->ptr, $startIndex, $len);
		return $this; 
	}
	function insert($a, $insertPoint = -1) {
		callmethod("ArrayMath.insert", $this->ptr, $a, $insertPoint);
		return $this; 
	}
	function insert2($c, $len, $insertPoint= -1) {
		callmethod("ArrayMath.insert2", $this->ptr, $c, $len, $insertPoint);
		return $this; 
	}
	function replace($a, $b) {
		callmethod("ArrayMath.replace", $this->ptr, $a, $b);
		return $this; 
	}

	function movAvg($interval) {
		callmethod("ArrayMath.movAvg", $this->ptr, $interval);
		return $this; 
	}
	function expAvg($smoothingFactor) {
		callmethod("ArrayMath.expAvg", $this->ptr, $smoothingFactor);
		return $this; 
	}
	function movMed($interval) {
		callmethod("ArrayMath.movMed", $this->ptr, $interval);
		return $this; 
	}
	function movPercentile($interval, $percentile) {
		callmethod("ArrayMath.movPercentile", $this->ptr, $interval, $percentile);
		return $this; 
	}
	function movMax($interval) {
		callmethod("ArrayMath.movMax", $this->ptr, $interval);
		return $this; 
	}
	function movMin($interval) {
		callmethod("ArrayMath.movMin", $this->ptr, $interval);
		return $this; 
	}
	function movStdDev($interval) {
		callmethod("ArrayMath.movStdDev", $this->ptr, $interval);
		return $this; 
	}
	function movCorr($interval, $b = Null) {
		callmethod("ArrayMath.movCorr", $this->ptr, $interval, $b);
		return $this; 
	}
	function lowess($smoothness = 0.25, $iteration = 0) {
		callmethod("ArrayMath.lowess", $this->ptr, $smoothness, $iteration);
		return $this; 
	}
	function lowess2($b, $smoothness = 0.25, $iteration = 0) {
		callmethod("ArrayMath.lowess2", $this->ptr, $b, $smoothness, $iteration);
		return $this; 
	}

	function result() {
		return callmethod("ArrayMath.result", $this->ptr);
	}
	function max() {
		return callmethod("ArrayMath.max", $this->ptr);
	}
	function min() {
		return callmethod("ArrayMath.min", $this->ptr);
	}
	function avg() {
		return callmethod("ArrayMath.avg", $this->ptr);
	}
	function sum() {
		return callmethod("ArrayMath.sum", $this->ptr);
	}
	function med() {
		return callmethod("ArrayMath.med", $this->ptr);
	}
	function percentile($p) {
		return callmethod("ArrayMath.percentile", $this->ptr, $p);
	}
	function maxIndex() {
		return callmethod("ArrayMath.maxIndex", $this->ptr);
	}
	function minIndex() {
		return callmethod("ArrayMath.minIndex", $this->ptr);
	}
}

?>
