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

AW.Templates.Input = AW.Templates.ImageText.subclass();

AW.Templates.Input.create = function(){

	var obj = this.prototype;

	obj.setClass("templates", "input");
	obj.setClass("input", "box");
	obj.setClass("text", "");

	var text = new AW.HTML.INPUT;

	text.setClass("item", "text");
	text.setAttribute("type", "text");
	text.setAttribute("tabindex", "-1");
	text.setAttribute("value", function(){
		return this.getControlProperty("text");
	});

	if (AW.safari) {

		obj.setEvent("onselectstart", "");

		text = new AW.HTML.DIV;
		text.setClass("item", "text");
		text.setAttribute("contentEditable", "true");
		text.setContent("html", function(){
			return this.getControlProperty("text");
		});

	}

	text.setEvent("onfocus", function(){

		var self = this;
		var e = this.getContent("box/text").element();

		function cancelBubble(event){
			event.cancelBubble = true;
		}

		function updateText(event){
			var text = AW.safari ? e.innerText : e.value;
			var old = self.getControlProperty("text");
			if (text != old){
				self.setControlProperty("text", text);
			}
		}

		this.$target = "";

		function mark(event){
			if(AW.srcElement(event) != e){
				self.$target = true;
			}
		}

		e.parentNode.scrollTop = 0;

		if (AW.ie){
			AW.attachEvent(e, "onselectstart", cancelBubble);
			AW.attachEvent(e, "oncontextmenu", cancelBubble);
			AW.attachEvent(e, "onpropertychange", updateText);
			e.setExpression("test", "this.value");
		}

		if(AW.gecko) {
			AW.attachEvent(e, "oninput", updateText);
		}

		if(AW.safari) {
			AW.attachEvent(e, "onDOMCharacterDataModified", updateText);
		}

		AW.attachEvent(e, "onblur", blur);
		AW.attachEvent(window, "onunload", blur);
		AW.attachEvent(self.element(), "onmousedown", mark);



		function blur(){

			if (AW.ie){
				AW.detachEvent(e, "onselectstart", cancelBubble);
				AW.detachEvent(e, "oncontextmenu", cancelBubble);
				AW.detachEvent(e, "onpropertychange", updateText);
				e.removeExpression("test");
			}

			if (AW.gecko) {
				AW.detachEvent(e, "oninput", updateText);
			}

			if(AW.safari) {
				AW.detachEvent(e, "onDOMCharacterDataModified", updateText);
			}

			AW.detachEvent(e, "onblur", blur);
			AW.detachEvent(window, "onunload", blur);
			AW.detachEvent(self.element(), "onmousedown", mark);

			if(self.$target){
				self.$target = false;
				self.setTimeout(function(){
					e.focus();
					e = null;
				});
			}
			else {
				self.$target = false;
				e = null;
				self.raiseEvent("update");

				if (self.hidePopup){
					self.hidePopup();
				}
			}

		}

	});



	obj.setContent("box/text", text);

};


