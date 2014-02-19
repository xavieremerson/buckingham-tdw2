/*****************************************************************

	ActiveWidgets 2.0.b3 (beta3)
	Copyright (C) 2003-2005 ActiveWidgets Ltd. All Rights Reserved. 
	http://www.activewidgets.com/

	WARNING: This software program is protected by copyright law 
	and international treaties. Unauthorized reproduction or
	distribution of this program, or any portion of it, may result
	in severe civil and criminal penalties, and will be prosecuted
	to the maximum extent possible under the law.

	FOR TESTING AND EVALUATION ONLY:
	This software is not free and is licensed to you for testing 
	and evaluation only. You are not allowed to distribute 
	or use any parts of this software for any other purposes.

*****************************************************************/

$import("namespaces/aw.js");

$import("browsers/common.js");
$import("browsers/detect.js");
$import("browsers/ie.js");
$import("browsers/gecko.js");
$import("browsers/safari.js");
$import("browsers/opera.js");

$import("system/object.js");
$import("system/model.js");
$import("system/format.js");
$import("system/html.js");
$import("system/template.js");
$import("system/control.js");

$import("formats/string.js");
$import("formats/number.js");
$import("formats/date.js");
$import("formats/html.js");

$import("html/tags.js");

$import("templates/imagetext.js");
$import("templates/image.js");
$import("templates/text.js");
$import("templates/link.js");
$import("templates/checkbox.js");
$import("templates/checkeditem.js");
$import("templates/radio.js");
$import("templates/popup.js");
$import("templates/frame.js");
$import("templates/list.js");
$import("templates/input.js");
$import("templates/combo.js");
$import("templates/html.js");

$import("scroll/bars.js");

$import("panels/horizontal.js");
$import("panels/vertical.js");
$import("panels/grid.js");

$import("ui/imagetext.js");
$import("ui/label.js");
$import("ui/group.js");
$import("ui/button.js");
$import("ui/link.js");
$import("ui/input.js");
$import("ui/list.js");
$import("ui/tabs.js");
$import("ui/combo.js");
$import("ui/checkbox.js");
$import("ui/checkedlist.js");
$import("ui/radio.js");

$import("grid/_size.js");
$import("grid/_cell.js");
$import("grid/_row.js");
$import("grid/_view.js");
$import("grid/_navigation.js");
$import("grid/_sort.js");
$import("grid/_overflow.js");
$import("grid/_scroll.js");
$import("grid/_width.js");
$import("grid/_virtual.js");
$import("grid/_grid.js");
$import("grid/_extended.js");

$import("grid/_singlecell.js");
$import("grid/_singlerow.js");
$import("grid/_multirow.js");
$import("grid/_multirowmarker.js");

$import("grid/separator.js");
$import("grid/header.js");
$import("grid/row.js");
$import("grid/rows.js");
$import("grid/control.js");
$import("grid/extended.js");

$import("tree/item.js");
$import("tree/view.js");
$import("tree/group.js");
$import("tree/control.js");

$import("http/request.js");
$import("csv/table.js");
$import("xml/table.js");

//	------------------------------------------------------------

function $import(path){
	var i, base, src = "aw.js", scripts = document.getElementsByTagName("script");
	for (i=0; i<scripts.length; i++){if (scripts[i].src.match(src)){ base = scripts[i].src.replace(src, "");break;}}
	document.write("<script src=\"" + base + path + "\"><\/script>");
}

