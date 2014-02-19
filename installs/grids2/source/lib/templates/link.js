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

AW.Templates.Link = AW.Templates.ImageText.subclass();

AW.Templates.Link.create = function(){

	var obj = this.prototype;
	obj.setTag("a");
	obj.setClass("templates", "link");
	obj.setAttribute("tabIndex", "-1");
	obj.setAttribute("href", function(){return this.getControlProperty("link") || null});

	var box = obj.getContent("box");
	box.setTag("");

};

