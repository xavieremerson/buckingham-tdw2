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

AW.Templates.CheckBox = AW.Templates.ImageText.subclass();

AW.Templates.CheckBox.create = function(){

	var obj = this.prototype;

	obj.setClass("value", function(){return this.getControlProperty("value") || false});
	obj.setClass("toggle", "checkbox");
	obj.setClass("templates", "checkbox");
	obj.setAttribute("awx", "toggle");

	var marker = new AW.HTML.SPAN;
	marker.setClass("item", "marker");

	obj.setContent("box/marker", marker);

	obj.onToggleClicked = function(){
		var value = this.getControlProperty("value");
		this.setControlProperty("value", !value);
	};
};

AW.Templates.Checkbox = AW.Templates.CheckBox;