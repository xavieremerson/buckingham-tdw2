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

	AW.all = {id: 0};

	AW.docs = [document];

	AW.forEach = function(array, handler){
		var i, custom = {};
		for(i in array){
			if (!custom[i]){
				handler(i, array[i]);
			}
		}
	};

	AW.element = function(id){
		var i, e, docs = AW.docs;
		for(i=0; i<docs.length; i++) {
			e = docs[i].getElementById(id);
			if(e) {return e}
		}
	};

	AW.object = function(id){

		var parts = id.split("-");
		var tag = parts[0];
		var obj = AW.all[tag];
		var target = obj;

		for (var i=1; i<parts.length; i++){
			if (obj["_" + parts[i] + "Content"]){
				for (var j=i; j<parts.length; j++){
					target = target.getContent(parts[j]);
				}
				break;
			}
			else if (parts[i+1] && parts[i+1].match(/^(\d+)$/)){
				if (parts[i+2] && parts[i+2].match(/^(\d+)$/)){
					if (parts[i+3] && parts[i+3].match(/^(\d+)$/)){
						obj = obj.getTemplate(parts[i], parts[i+1], parts[i+2], parts[i+3]);
						i += 3;
					}
					else {
						obj = obj.getTemplate(parts[i], parts[i+1], parts[i+2]);
						i += 2;
					}
				}
				else {
					obj = obj.getTemplate(parts[i], parts[i+1]);
					i += 1;
				}
			}
			else {
				obj = obj.getTemplate(parts[i]);
			}
			target = obj;
		}
		return target;
	};

	var events = {
		"DOMFocusIn": "focus"};

	AW.dispatch = function(element, event){

		var type = "_on" + (events[event.type] || event.type) + "Event";
		var target = AW.object(element.id);
		var obj = target;

		while (obj._parent){
			obj = obj._parent;
		}

		return target[type].call(obj, event);
	};


	AW.paint = function(element){

		var obj = AW.object(element.id);

		while (obj._parent){
			obj = obj._parent;
		}

		if (obj && obj.raiseEvent && !obj.$paint) {
			obj.$paint = true;
			obj.raiseEvent("paint");
		}
		window.setTimeout(function(){
			element.style.removeExpression("visibility");
			element.style.display = "none";
			if (obj && obj.$paint) {
				obj.$paint = false;
			}
		}, 0);
		return "hidden";
	};

	AW.camelCase = function(){

		var i, s = arguments[0];
		for (i=1; i<arguments.length; i++){
			s += arguments[i].substr(0,1).toUpperCase() + arguments[i].substr(1);
		}
		return s;
	};

	AW.textPattern = /(\"|&|<|>)/gm;
	AW.textTable   = {"\"":"&quot;", "&":"&amp;", "<":"&lt;", ">":"&gt;"};
	AW.textReplace = function(c){return AW.textTable[c] || ""};

	AW.htmlPattern = /(&quot;|&amp;|&lt;|&gt|<[^<>]*>)/gm;
	AW.htmlTable   = {"&quot;":"\"", "&amp;":"&", "&lt;":"<", "&gt;":">"};
	AW.htmlReplace = function(e){return AW.htmlTable[e] || ""};

	AW.valueToText  = function(v){return v ? String(v).replace(AW.textPattern, AW.textReplace) : ""};
	AW.textToValue  = function(t){return t ? String(t).replace(AW.htmlPattern, AW.htmlReplace) : ""};

})();
