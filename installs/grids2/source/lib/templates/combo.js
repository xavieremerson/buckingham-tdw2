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

AW.Templates.Combo = AW.Templates.Input.subclass();

AW.Templates.Combo.create = function(){

	AW.Templates.Popup.create.call(this);

	var obj = this.prototype;

	obj.setAttribute("awx", "combo");
	obj.setClass("templates", "combo");
	obj.setClass("combo", "box");
	obj.setClass("input", "");
	obj.setClass("text", "");

	var button = new AW.HTML.TABLE;

	button.setClass("combo", "button");
	button.setAttribute("cellspacing", "0");

	button.setEvent("onclick", function(){
		this.showPopup();
		this.getContent("box/text").element().focus();
		this.getContent("box/text").element().parentNode.scrollTop = 0;
	});

	obj.setContent("box/sign", button);
	obj.setContent("box/sign/html", "<tr class=\"aw-cb-1\"><td></td></tr><tr class=\"aw-cb-2\"><td>&nbsp;</td></tr><tr class=\"aw-cb-3\"><td></td></tr>");

};

