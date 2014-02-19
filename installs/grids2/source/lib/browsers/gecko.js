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

	if (AW.gecko){

		var capture;

		AW.attachEvent = function(target, name, handler){
			if (capture){
				handler[name] = function(event){return handler.call(target, event)};
				window.addEventListener(name.replace(/^on/, ""), handler[name], true);
			}
			else{
				target.addEventListener(name.replace(/^on/, ""), handler, false);
			}
		};

		AW.detachEvent = function(target, name, handler){
			if (capture){
				window.removeEventListener(name.replace(/^on/, ""), handler[name], true);
				handler[name] = null;
			}
			else{
				target.removeEventListener(name.replace(/^on/, ""), handler, false);
			}
		};

		AW.srcElement = function(event){
			try {
				return (event.target && event.target.nodeType==3) ? event.target.parentNode : event.target;
			}
			catch(e){
				return event.target;
			}
		};

		AW.toElement = function(event){
			try {
				return (event.relatedTarget && event.relatedTarget.nodeType==3) ? event.relatedTarget.parentNode : event.relatedTarget;
			}
			catch(e){
				return event.relatedTarget;
			}
		};

		AW.setReturnValue = function(event, value){
			if (!value) {event.preventDefault()}
		};

		AW.setCapture = function(element){
			capture = element;
		};

		AW.releaseCapture = function(element){
			capture = null;
		};

		AW.addRule = function(stylesheet, selector, rule){
			var i = stylesheet.cssRules.length;
			stylesheet.insertRule(selector + "{" + rule + "}", i);
			stylesheet.cssRules[i].style.cssText = rule; 
		};

		AW.getRules = function(stylesheet){
			return stylesheet.cssRules;
		};

		AW.setOuterHTML = function(element, html){
		   var range = element.ownerDocument.createRange();
		   range.setStartBefore(element);
		   var fragment = range.createContextualFragment(html);
		   element.parentNode.replaceChild(fragment, element);
		};

		AW.createXMLHttpRequest = function(){
			return new XMLHttpRequest;
		};

		AW.getLeft = function(element){
			var doc = document.getBoxObjectFor(document.body);
			return document.getBoxObjectFor(element).screenX - doc.screenX + doc.x;
		};

		AW.getTop = function(element){
			var doc = document.getBoxObjectFor(document.body);
			return document.getBoxObjectFor(element).screenY - doc.screenY + doc.y;
		};

	}

})();
