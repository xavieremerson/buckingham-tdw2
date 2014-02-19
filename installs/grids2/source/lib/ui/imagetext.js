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

AW.UI.ImageText = AW.System.Control.subclass();

AW.UI.ImageText.create = function(){

	AW.Templates.ImageText.create.call(this);

	var obj = this.prototype;

	obj.setClass("templates", "");
	obj.setClass("ui", "imagetext");
	obj.setClass("item", "control");
	obj.setClass("text", "expand");

	function _tabIndex(){
		return this.getTabProperty("index");
	}

	obj.getContent("box/text").setAttribute("tabIndex", _tabIndex);


	var _refresh = function(){this.refresh()};

	var itemController = {
		onControlTextChanged:	_refresh,
		onControlImageChanged:	_refresh,
		onControlValueChanged:	_refresh,
		onControlLinkChanged:	_refresh,
		onControlTooltipChanged:_refresh,
		onControlStateChanged:	_refresh };

	obj.setController("item", itemController);

};


