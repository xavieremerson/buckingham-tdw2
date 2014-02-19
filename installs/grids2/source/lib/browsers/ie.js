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

(function(){

	if (AW.ie) {

		AW.attachEvent = function(element, name, handler){
			return element.attachEvent(name, handler);
		};

		AW.detachEvent = function(element, name, handler){
			return element.detachEvent(name, handler);
		};

		AW.srcElement = function(event){
			return event.srcElement;
		};

		AW.toElement = function(event){
			return event.toElement;
		};

		AW.setReturnValue = function(event, value){
			event.returnValue = value;
		};

		AW.setCapture = function(element){
			return element.setCapture();
		};

		AW.releaseCapture = function(element){
			return element.releaseCapture();
		};

		AW.addRule = function(stylesheet, selector, rule){
			return stylesheet.addRule(selector, rule);
		};

		AW.getRules = function(stylesheet){
			return stylesheet.rules;
		};

		AW.setOuterHTML = function(element, html){
			element.outerHTML = html;
		};

		AW.createXMLHttpRequest = function(){
			return new ActiveXObject("MSXML2.XMLHTTP");
		};

		AW.getLeft = function(element){
			return element.getBoundingClientRect().left;
		};

		AW.getTop = function(element){
			return element.getBoundingClientRect().top;
		};
	}

})();
