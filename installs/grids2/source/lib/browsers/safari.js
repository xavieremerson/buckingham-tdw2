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

	if (AW.safari){

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
			return (event.target && event.target.nodeType==3) ? event.target.parentNode : event.target;
		};

		AW.toElement = function(event){
			return (event.relatedTarget && event.relatedTarget.nodeType==3) ? event.relatedTarget.parentNode : event.relatedTarget;
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
			element.outerHTML = html;
		};

		AW.createXMLHttpRequest = function(){
			return new XMLHttpRequest;
		};

		AW.getLeft = function(element){
			return getRectangle(element).left;
		};

		AW.getTop = function(element){
			return getRectangle(element).top;
		};

		function getRectangle(e){

			var t = e, x = 0, y = 0;

			function getPos(el){
				if (!el) {
					return {x:0, y:0};
				}
				if (el == document.body.parentNode){
					return {x:0, y:0};
				}
				if (el == document.body){
					return {x:el.offsetLeft, y:el.offsetTop};
				}

				var p = el.offsetParent;

				var pp = getPos(p);

				return {
					x: el.offsetLeft + pp.x,
					y: el.offsetTop + pp.y };
			}

			var pp = getPos(e);

			return {
				left: pp.x,
				right: pp.x + e.offsetWidth,
				top: pp.y,
				bottom: pp.y + e.offsetHeight };
		}
	}

})();
