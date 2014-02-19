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

AW.Scroll.Bars = AW.System.Template.subclass();

AW.Scroll.Bars.create = function(){

	var obj = this.prototype;

	obj.setClass("scroll", "bars");

	obj.setClass("scrollbars", function(){
		return this.getScrollProperty("bars");
	});

	var span = AW.HTML.SPAN;

	var box = new span;
	var spacer = new span;
	var content = new span;

	box.setClass("bars", "box");
	spacer.setClass("bars", "spacer");
	content.setClass("bars", "content");

	spacer.setStyle("width", function(){
		return this.getScrollProperty("width") + "px";
	});

	spacer.setStyle("height", function(){
		return this.getScrollProperty("height") + "px";
	});

	if (AW.ie && AW.strict) {
		content.setStyle("width", "expression(this.parentElement.clientWidth-20)");
		content.setStyle("height", "expression(this.parentElement.clientHeight-20)");
	}

	obj.setContent("box", box);
	obj.setContent("box/spacer", spacer);
	obj.setContent("content", content);
	obj.setContent("content/html", function(){
		return this.getContentTemplate();
	});

	obj.setEvent("onresize", function(){
		this.raiseEvent("adjustScrollBars");
	});

	box.setEvent("onscroll", function(){
		var e = this.getContent("box").element();

		var left = this.getScrollProperty("left");
		var top = this.getScrollProperty("top");

		if (e.scrollLeft != left) {
			this.setScrollProperty("left", e.scrollLeft);
		}

		if (e.scrollTop != top) {
			this.setScrollProperty("top", e.scrollTop);
		}

		e = null;
	});

	obj.setEvent("onmousewheel", function(event){
		var top = this.getScrollProperty("top");
		top -= event.wheelDelta/2;
		var e = this.element();
		if (e){
			var max = this.getScrollProperty("height") - e.offsetHeight;
			var bars = this.getScrollProperty("bars");
			max += (bars == "horizontal" || bars == "both") ? 16 : 0;
			top = top > max ? max : top;
		}
		top = top < 0 ? 0 : top;
		this.setScrollProperty("top", top);
		AW.setReturnValue(event, false);
	});
};

