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

AW.Grid.Row = AW.Templates.List.subclass();

AW.Grid.Row.create = function(){

	var obj = this.prototype;
	var _super = this.superclass.prototype;

	obj.setClass("text", "normal");

	var span = AW.HTML.SPAN;

	var space = new span;
	var box = new span;

	space.setClass("item", "template");
	space.setClass("grid", "cell");
	space.setClass("column", "space");
	box.setClass("item", "box");

	space.setContent("box", box);
	obj.setContent("end", space);

	obj.setContent("start", function(){
		return this.getSelectorProperty("visible") && !this.$1 ? this.getSelectorTemplate() : "";
	});

	var items = obj.getContent("items");

	obj.setContent("items", function(){
		return this.$name == "row" && this.$owner._fast ? "" : items.call(this);
	});

	var refresh = obj.refresh;

	obj.refresh = function(){
		if (typeof(this.$1) == "undefined" && this.$owner.$extended){
			for (var i=0; i<3; i++) {
				refresh.call(this.$owner.getRowTemplate(this.$0, i));
			}
		}
		else {
			refresh.call(this);
		}
	};

	var refreshClasses = obj.refreshClasses;

	obj.refreshClasses = function(){
		if (typeof(this.$1) == "undefined" && this.$owner.$extended){
			for (var i=0; i<3; i++) {
				refreshClasses.call(this.$owner.getRowTemplate(this.$0, i));
			}
		}
		else {
			refreshClasses.call(this);
		}
	};

};

