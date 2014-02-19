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

AW.Templates.Popup = AW.System.Template.subclass();

AW.Templates.Popup.create = function(){

	var obj = this.prototype;

	obj.setClass("popup", "normal");

	obj.showPopup = function(){

		var popup = window.createPopup();
		this.$popup = popup;

		var doc = popup.document;
		doc.open();

		AW.register(doc.parentWindow);

		if (AW.strict){
			doc.write("<!DOCTYPE HTML PUBLIC \"-\/\/W3C\/\/DTD HTML 4.01\/\/EN\" \"http:\/\/www.w3.org/TR/html4/strict.dtd\">");
		}

		var cls = document.getElementsByTagName("html")[0].className;

		doc.write("<html class=\"aw-popup-window aw-system-control " + cls + "\"><head>");

		for (var i=0; i<document.styleSheets.length; i++){
			doc.write(document.styleSheets[i].owningElement.outerHTML);
		}

		doc.write("</head><body onselectstart=\"return false\" oncontextmenu=\"return false\">");
		doc.write(this.getPopupTemplate().toString());
		doc.write("</body></html>");
		doc.close();

		var ref = this.element();

		var left = 0;
		var top = ref.offsetHeight;
		var width = ref.offsetWidth;
		var height = 1;

		popup.show(left, top, width, height, ref);

		width = Math.max(doc.body.scrollWidth, width);
		height = Math.max(doc.body.scrollHeight+1, 20);

		popup.show(left, top, width, height, ref);

		ref = null;
	};

	obj.hidePopup = function(){
		if (this.$popup && this.$popup.isOpen ){
			this.$popup.hide();
		}
		if (this.$popup){
			this.$popup = null;
		}
	};

if (!AW.ie){

	obj.showPopup = function(){

		if (this.$popup){
			document.body.removeChild(this.$popup);
			this.$popup = null;
		}

		var ref = this.element() ? this.element() : document.body;

		var left = AW.getLeft(ref);
		var top = AW.getTop(ref) + ref.offsetHeight;

		var popup = document.createElement("div");
		this.$popup = popup;

		document.body.appendChild(popup);
		popup.className = "aw-popup-window aw-system-control";
		popup.style.left = left + "px";
		popup.style.top = top + "px";
		popup.innerHTML = this.getPopupTemplate().toString();

		var self = this;

		AW.attachEvent(popup, "onmousedown", function(){
			self.$target = true;
		});
	};

	obj.hidePopup = function(){
		if (this.$popup){
			this.$popup.style.visibility = "hidden";
			var popup = this.$popup;
			this.setTimeout(function(){
				if (this.$popup == popup){
					document.body.removeChild(this.$popup);
					this.$popup = null;
					popup = null;
				}
			}, 5000);
		}
	};

}

};

