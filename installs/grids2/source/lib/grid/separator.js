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

AW.Grid.Separator = AW.System.Template.subclass();

AW.Grid.Separator.create = function(){

	var obj = this.prototype;

	obj.setClass("grid", "separator");

	obj.setEvent("onmousedown", function(event){

		var start = event.screenX;
		var self = this;
		var width = self.element().previousSibling.offsetWidth;
		var scroll = self.element().parentNode.parentNode.scrollLeft;

		function doResize(event){
			var w = width + event.screenX - start;
			w = w > 9 ? w : 9;
			self.element().previousSibling.style.width = (w - AW.dx) + "px";
		}

		function endResize(event){

			var w = width + event.screenX - start;
			w = w > 9 ? w : 9;

			var e = self.element();

			AW.detachEvent(e, "onmousemove", doResize);
			AW.detachEvent(e, "onmouseup", endResize);
			AW.detachEvent(e, "onlosecapture", endResize);
			AW.releaseCapture(e);

			if (AW.gecko){
				try {
					e.parentNode.parentNode.scrollLeft = scroll;
					self.$owner.element().focus(); // FF1.5
				}
				catch(err){
				}
			}

			var id = e.previousSibling.id;
			if (id.match("header")){
				self.$owner.setColumnProperty("width", w, self.$0);
			} else if (id.match("topSelector")){
				self.$owner.setSelectorProperty("width", w);
			}

			e.previousSibling.style.width = "";

			e = null;
		}

		var e = AW.srcElement(event);

		AW.setCapture(e);
		AW.attachEvent(e, "onmousemove", doResize);
		AW.attachEvent(e, "onmouseup", endResize);
		AW.attachEvent(e, "onlosecapture", endResize);

		e = null;

		event.cancelBubble = true; 

	});

};

