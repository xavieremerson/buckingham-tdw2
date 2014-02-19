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

AW.Panels.Grid = AW.System.Template.subclass();

AW.Panels.Grid.create = function(){

	var obj = this.prototype;

	obj.setClass("gpanel", "template");

	var span = AW.HTML.SPAN;

	var box = new span;
	var top = new span;
	var middle = new span;
	var bottom = new span;

	box.setClass("gpanel", "box");
	top.setClass("gpanel", "top");
	middle.setClass("gpanel", "middle");
	bottom.setClass("gpanel", "bottom");

	function height(i){
		return function(){
			return this.getPanelProperty("height", i) + "px";
		}
	}

	function width(i){
		return function(){
			return this.getPanelProperty("width", i) + "px";
		}
	}

	function visibility(i){
		return function(){
			return this.getPanelProperty("height", i) ? "visible" : "hidden";
		}
	}

	top.setStyle("visibility", visibility(0));
	top.setStyle("height", height(0));

	bottom.setStyle("display", function(){return this.getPanelProperty("height", 2) ? null : "none";});
	bottom.setStyle("height", height(2));

	if (AW.ie) {
		box.setStyle("padding-top", height(0));
		box.setStyle("padding-bottom", height(2));
	}
	else {
		middle.setStyle("top", height(0));
		middle.setStyle("bottom", height(2));
	}

	if (AW.ie && AW.strict) {
		box.setStyle("height", "expression(this.parentElement.clientHeight-this.firstChild.offsetHeight-this.lastChild.offsetHeight)");
	}


	function panel(i, j){
		return function(){
			return this.getPanelTemplate(i, j);
		}
	}

	var i, rows = [top, middle, bottom];

	for(i=0; i<3; i++){

		var left = new span;
		var center = new span;
		var right = new span;

		left.setClass("gpanel", "left");
		center.setClass("gpanel", "center");
		right.setClass("gpanel", "right");

		left.setContent("html", panel(i, 0));
		center.setContent("html", panel(i, 1));
		right.setContent("html", panel(i, 2));

		if (AW.ie && AW.strict) {
			rows[i].setStyle("width", "expression(this.parentElement.clientWidth-this.firstChild.offsetWidth-this.lastChild.offsetWidth)");
		}

		rows[i].setContent("left", left);
		rows[i].setContent("center", center);
		rows[i].setContent("right", right);
	}

	box.setContent("top", top);
	box.setContent("middle", middle);
	box.setContent("bottom", bottom);

	obj.setContent("box", box);

};

