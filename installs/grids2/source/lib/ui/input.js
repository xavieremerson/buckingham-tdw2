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

AW.UI.Input = AW.UI.ImageText.subclass();

AW.UI.Input.create = function(){

	AW.Templates.Input.create.call(this);

	var obj = this.prototype;

	obj.setClass("ui", "input");

	obj.onKeyEnter = function(){
		try{
			if (this.$owner){
				this.$owner.element().focus();
				this.setTimeout(function(){
					this.$owner.element().focus();
				});
			}
			else {
				this.raiseEvent("update");
			}
		}
		catch(e){
		}
	};

	var _update = function(){

		var text = this.getControlProperty("text");
		var format = this.getControlProperty("format");
		var value = format ? format.textToValue(text) : text;

		this.setControlProperty("value", value);

		if (this.$owner){
			this.$owner.raiseEvent("update", "", this.$0, this.$1);
		}
	};



	var _refresh = function(){this.refresh()};

	var _text = function(){

		var e = this.getContent("box/text").element();
		var text = this.getControlProperty("text");

		if (AW.safari && e && e.innerText != text){
			e.innerHTML = text;
		}

		if (!AW.safari && e && e.value != text) {
			e.value = text;
		}
	};

	var itemController = {
		update:					_update,
		onControlTextChanged:	_text,
		onControlImageChanged:	_refresh,
		onControlValueChanged:	_text,
		onControlLinkChanged:	_refresh,
		onControlTooltipChanged:_refresh,
		onControlStateChanged:	_refresh };

	obj.setController("item", itemController);


};


