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

AW.Panels.Vertical = AW.System.Template.subclass();

AW.Panels.Vertical.create = function(){

	var obj = this.prototype;

	obj.setClass("vpanel", "template");

	var span = AW.HTML.SPAN;

	var box = new span;
	var left = new span;
	var center = new span;
	var right = new span;

	box.setClass("vpanel", "box");
	left.setClass("vpanel", "left");
	center.setClass("vpanel", "center");
	right.setClass("vpanel", "right");

	left.setContent("html", function(){return this.getPanelTemplate(0)});
	center.setContent("html", function(){return this.getPanelTemplate(1)});
	right.setContent("html", function(){return this.getPanelTemplate(2)});

	box.setContent("left", left);
	box.setContent("center", center);
	box.setContent("right", right);

	obj.setContent("box", box);
};

