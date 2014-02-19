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

AW.UI.Combo = AW.UI.List.subclass();

AW.UI.Combo.create = function(){

	AW.UI.ImageText.create.call(this);
	AW.UI.Input.create.call(this);
	AW.Templates.Combo.create.call(this);

	var obj = this.prototype;

	obj.setClass("ui", "combo");

	obj.defineTemplate("popup", new AW.Templates.Frame);

	obj.onCurrentItemChanged = function(i){
		var text = this.getItemText(i);
		this.setControlText(text);

		this.hidePopup();

		var e = this.getContent("box/text").element();
		if (AW.safari) {
			e.innerHTML = text;
		}
		else {
			e.value = text;
		}
		e = null;
	};

	var kb = {
		onKeyUp:	"selectPreviousItem",
		onKeyDown:	"selectNextItem" };

	obj.setController("keyboard", kb);

};

