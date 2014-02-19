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

AW.Tree.Item = AW.Templates.ImageText.subclass();

AW.Tree.Item.create = function(){

	var obj = this.prototype;

	obj.setClass("tree", function(){
		return this.getViewProperty("count") ?  "folder" : "leaf";
	});

	obj.setClass("expanded", function(){
		return this.getViewProperty("expanded") ? "true" : "false";
	});

	var sign = new AW.HTML.SPAN;
	sign.setClass("tree", "sign");

	sign.setEvent("onclick", function(){
		this.raiseEvent("onTreeSignClicked");
	});

	obj.setContent("box/sign", sign);
};

