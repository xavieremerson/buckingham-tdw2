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

AW.System.Control = AW.System.HTML.subclass();

AW.System.Control.create = function(){

/****************************************************************

	Generic user interface control class. Control is a screen element,
	which can have focus and responds to the keyboard or mouse commands.

	Typical control has a set of built-in or external data models
	and may also contain additional presentation templates.

*****************************************************************/

	AW.System.Template.create.call(this);

	var obj = this.prototype;
	var templates = AW.System.Template.prototype;

	obj.setTag("span");
	obj.setClass("system", "control");
	obj.setAttribute("aw", "control");

	obj.setEvent("oncontextmenu", "return false");
	obj.setEvent("onselectstart", "return false");

	obj.clear = function(){
		// empty
	};

/****************************************************************

	Returns the data model object. For a built-in model this method
	will create a temporary proxy attached to the template.

	@param	name	(String) Name of the data model.
	@return			A data model object.

*****************************************************************/

	obj.getModel = function(name){
		var getModel = AW.camelCase("get", name, "model");
		return this[getModel]();
	};

/****************************************************************

	Sets the external data model.

	@param	name	(String) Name of the data model.
	@param	model	(Object) Data model object.

*****************************************************************/

	obj.setModel = function(name, model){
		var setModel = AW.camelCase("set", name, "model");
		return this[setModel](model);
	};

/****************************************************************

	Creates a new data model.

	@param	name	(String) New data model name.

*****************************************************************/

	obj.defineModel = function(m, z){

		var ext = "_" + m + "Model";

		var defineProperty = AW.camelCase("define", m, "property");
		var getProperty = AW.camelCase("get", m, "property");
		var setProperty = AW.camelCase("set", m, "property");

		var getModel = AW.camelCase("get", m, "model");
		var setModel = AW.camelCase("set", m, "model");
		var clearModel = AW.camelCase("clear", m, "model");

		var getInt = {};
		var setInt = {};
		var getExt = {};
		var setExt = {};
		var changing = {};
		var changed = {};
		var error = {};

		var undef;

//		------------------------------------------------------------

		this[defineProperty] = function(p, v, arrayValue){

			var _p = "_" + AW.camelCase(m, p);

			var _p1 = _p + "1";
			var _p2 = _p + "2";
			var _p3 = _p + "3";

			// internal
			var get = (getInt[p] = AW.camelCase("get", m, p));
			var set = (setInt[p] = AW.camelCase("set", m, p));

			// external
			var $get = (getExt[p] = AW.camelCase("get", p));
			var $set = (setExt[p] = AW.camelCase("set", p));

			var _changing = (changing[p] = AW.camelCase("on", m, p, "changing"));
			var _changed = (changed[p] = AW.camelCase("on", m, p, "changed"));
			var _error = (error[p] = AW.camelCase("on", m, p, "error"));


			this[get] = function(a, b, c){

				if (this[ext] && this[ext][$get]) {return this[ext][$get](a, b, c)}

				var r;
				if (c !== undef &&
					this[_p3] &&
					this[_p3][c] &&
					this[_p3][c][b] &&
					this[_p3][c][b][a] !== undef ) {
					r = this[_p3][c][b][a];
				}
				else if (b !== undef &&
					this[_p2] &&
					this[_p2][b] &&
					this[_p2][b][a] !== undef ) {
					r = this[_p2][b][a];
				}
				else if (a !== undef &&
					this[_p1] &&
					this[_p1][a] !== undef ) {
					r = this[_p1][a];
				}
				else {
					r = this[_p];
				}
				return (typeof(r)=="function") ? r.call(this, a, b, c) : r;
			};

			function isArray(a){
				return a && typeof(a)=="object" && !a.constructor.subclass && !arrayValue;
			}

			var setProp = function (v, a, b, c){

				var i;

				if (isArray(v)) {
					for(i in v){
						if (isArray(v[i])) {
							this[_p2] = v;
							delete this[_p3];
							return;
						}
						break;
					}
					if (a !== undef){
						if (!this[_p2]){this[_p2]={}}
						this[_p2][a] = v;
						delete this[_p3];
					}
					else {
						this[_p1] = v;
						delete this[_p2];
						delete this[_p3];
					}
					return;
				}

				if (c !== undef) {
					if (!this[_p3]){this[_p3]={}}
					if (!this[_p3][c]){this[_p3][c]={}}
					if (!this[_p3][c][b]){this[_p3][c][b]={}}
					this[_p3][c][b][a] = v;
				}
				else if (b !== undef) {
					if (!this[_p2]){this[_p2]={}}
					if (!this[_p2][b]){this[_p2][b]={}}
					this[_p2][b][a] = v;
				}
				else if (a !== undef) {
					if (!this[_p1]){this[_p1]={$owner: this}}
					else if (this[_p1].$owner != this){
						var r = this[_p1]; this[_p1]={};
						for (i in r){this[_p1][i] = r[i]}
						this[_p1].$owner = this;
					}
					this[_p1][a] = v;
				}
				else {
					this[_p] = v;
					delete this[_p1];
					delete this[_p2];
					delete this[_p3];
				}

			};


			this[set] = function(v, a, b, c){
				if (this[ext] && this[ext][$set]) {return this[ext][$set](v, a, b, c)}

				var r = this.raiseEvent(_changing, v, a, b, c);
				if (r) {
					this.raiseEvent(_error, r, a, b, c);
					return false;
				}

				setProp.call(this, v, a, b, c);
				this.raiseEvent(_changed, v, a, b, c);
				return true;
			};

			setProp.call(this, v);

			var clearPrevious = this[clearModel];

			this[clearModel] = function(){

				delete this[_p3];
				delete this[_p2];
				delete this[_p1];
				delete this[_p];

				clearPrevious.call(this);
				setProp.call(this, v);
			}

		};

//		------------------------------------------------------------

		this[getProperty] = function(p, a, b, c){
			try {
				if (this[ext] && this[ext][getExt[p]]) {return this[ext][getExt[p]](a, b, c)}
				return this[getInt[p]](a, b, c);
			}
			catch(error){
				return this.handle(error);
			}
		};

//		------------------------------------------------------------

		this[setProperty] = function(p, v, a, b, c){
			try {
				if (this[ext] && this[ext][setExt[p]]) {return this[ext][setExt[p]](v, a, b, c)}
				return this[setInt[p]](v, a, b, c);
			}
			catch(error){
				return this.handle(error);
			}
		};

//		------------------------------------------------------------

		templates[getProperty] = function(p, a, b, c){
			if (a === undef) {return this.$owner[getProperty](p, this.$0, this.$1, this.$2)}
			if (b === undef) {return this.$owner[getProperty](p, a, this.$0, this.$1)}
			if (c === undef) {return this.$owner[getProperty](p, a, b, this.$0)}
			return this.$owner[getProperty](p, a, b, c);
		};

		templates[setProperty] = function(p, v, a, b, c){
			if (a === undef) {return this.$owner[setProperty](p, v, this.$0, this.$1, this.$2)}
			if (b === undef) {return this.$owner[setProperty](p, v, a, this.$0, this.$1)}
			if (c === undef) {return this.$owner[setProperty](p, v, a, b, this.$0)}
			return this.$owner[setProperty](p, v, a, b, c);
		};

//		------------------------------------------------------------


		this[getModel] = function(){
			return this[ext];
		};

		this[setModel] = function(model){
			this[ext] = model;
			if (model) {model.$owner = this}
		};

		this[clearModel] = function(){
			if (this[ext] && this[ext].$owner){
				delete this[ext].$owner;
			}
			delete this[ext];
		};

//		------------------------------------------------------------

		var clear = this.clear;

		this.clear = function(){
			clear.call(this);
			this[clearModel]();
		};

//		------------------------------------------------------------

		var i, zz = {};

		for (i in z){
			if (!zz[i]){
				this[defineProperty](i, z[i]);
			}
		}
	};



/****************************************************************

	Creates a link to the new content template (array).

	@param	name	(String) Template name.
	@param	template	(Object) Template object.

*****************************************************************/


	obj.defineTemplate = function(name, template){

		var ref = "_" + name + "Template";
		var ref1 = ref + "1", ref2 = ref + "2", ref3 = ref + "3";

		var get = AW.camelCase("get", name, "template");
		var set = AW.camelCase("set", name, "template");

		var clone = AW.camelCase("_", name, "template", "clone");

		var name1 = "-" + name;
		var name2 = "-" + name + "-";

		var undef;

		this[get] = function(a, b, c){

			if (typeof(this[ref])=="function") {
				return this[ref](a, b, c);
			}

			var r, id, useClone = false;

			if (a === undef){
				id = this._id + name1;
				r = this[ref];
			}
			else if (b === undef) {
				id = this._id + name2 + a;
				if(this[ref1] && this[ref1][a]){
					r = this[ref1][a];
				}
				else {
					r = this[ref];
					useClone = true;
				}
			}
			else if (c === undef) {
				id = this._id + name2 + a + "-" + b;
				if(this[ref2] && this[ref2][a] && this[ref2][a][b]){
					r = this[ref2][a][b];
				}
				else if(this[ref1] && this[ref1][a]){
					r = this[ref1][a];
					useClone = true;
				}
				else {
					r = this[ref];
					useClone = true;
				}
			}
			else {
				id = this._id + name2 + a + "-" + b + "-" + c;
				if(this[ref3] && this[ref3][a] && this[ref3][a][b] && this[ref3][a][b][c] ){
					r = this[ref2][a][b][c];
				}
				else if(this[ref2] && this[ref2][a] && this[ref2][a][b]){
					r = this[ref2][a][b];
					useClone = true;
				}
				else if(this[ref1] && this[ref1][a]){
					r = this[ref1][a];
					useClone = true;
				}
				else {
					r = this[ref];
					useClone = true;
				}
			}

			if (useClone || r.$owner != this) {
				if (!r.$clone) {
					r.$clone = r.clone();
					r.$clone.$clone = null;
				}
				r = r.$clone;
			}

			r.$owner = this;
			r.$0 = a;
			r.$1 = b;
			r.$2 = c;
			r._id = id;
			return r;

		};

		templates[get] = function(a, b, c){
			if (a === undef) {return this.$owner[get](this.$0, this.$1, this.$2)}
			if (b === undef) {return this.$owner[get](a, this.$0, this.$1)}
			if (c === undef) {return this.$owner[get](a, b, this.$0)}
			return this.$owner[get](a, b, c);
		};

		this[set] = function(template, a, b, c){

			if (a === undef) {

				if (this[ref] == template) {
					return;
				}

				if (this[ref]){
					this[ref].$clone = ""; 
				}

				this[ref] = template;

				if (template) {
					template.$clone = "";
					template.$name = name;
					if (template.$owner != this){
						template.$owner = this;
						this.raiseEvent(AW.camelCase("on", name, "templateChanged"), template);
					}
				}

				return;
			}

			if (b === undef) {
				if (!this[ref1]){
					this[ref1] = {};
				}
				if (this[ref1][a] == template) {
					return;
				}

				this[ref1][a] = template;

				template.$clone = "";
				template.$name = name;
				template.$0 = a;

				this[ref].$clone = ""; 

				if (template.$owner != this){
					template.$owner = this;
					this.raiseEvent(AW.camelCase("on", name, "templateChanged"), template, a);
				}
				return;
			}

			if (c === undef) {
				if (!this[ref2]){
					this[ref2] = {};
				}
				if (!this[ref2][a]){
					this[ref2][a] = {};
				}
				this[ref2][a][b] = template;

				
				template.$clone = "";
				template.$name = name;

				this[ref].$clone = ""; 
				if (this[ref1] && this[ref1][a]){
					this[ref1][a].$clone = ""; 
				}

				template.$0 = a;
				template.$1 = b;

				if (template.$owner != this){
					template.$owner = this;
					this.raiseEvent(AW.camelCase("on", name, "templateChanged"), template, a, b);
				}

				return;
			}

		};

		this[set](template);

	};

//	---

	function controlValue(){
		var text = this.getControlText();
		var format = this.getControlFormat();
		return format ? format.textToValue(text) : text;
	}

//	---

	obj.defineModel("tab", {
		index: 1 }
	);

	obj.defineModel("control", {
		text: "",
		image: "",
		link: "",
		value: controlValue,
		format: "",
		tooltip: "",
		state: "",
		visible: true }
	);

	obj.setControlSize = obj.setSize;
	obj.setControlPosition = obj.setPosition;

//	---

	obj.setController = function(name, controller){
		var i, n = "_" + name + "Controller";
		this[n] = controller;
		for(i=0; i<this._controllers.length; i++){
			if (this._controllers[i] == n) {
				return;
			}
		}
		this._controllers = this._controllers.concat();
		this._controllers.push(n);
	};

	obj._controllers = [];

	obj.raiseEvent = function(name, source, a, b, c){
		var i, r;
		var handler = this[name];
		if (typeof(handler)=="function"){
			r = handler.call(this, source, a, b, c);
			if (r) {return r}
		}
		for (i=0; i<this._controllers.length; i++){
			handler = this[this._controllers[i]] ? this[this._controllers[i]][name] : null;
			if (typeof(handler)=="function") {
				r = handler.call(this, source, a, b, c);
				if (r) {return r}
			}
			else if (typeof(handler) == "string" && handler != name) {
				r = this.raiseEvent(handler, source, a, b, c);
				if (r) {return r}
			}
		}
	};

//	obsolete - do not use
	obj.action = function(name, source, a, b, c){
		this.raiseEvent(AW.camelCase("on", name), source, a, b, c);
	};

	var keyNames = {
		8:  "Backspace",
		9:  "Tab",
		13: "Enter",
		27: "Escape",
		32: "Space",
		33: "PageUp",
		34: "PageDown",
		35: "End",
		36: "Home",
		37: "Left",
		38: "Up",
		39: "Right",
		40: "Down",
		45: "Insert",
		46: "Delete",
		112: "F1",
		113: "F2",
		114: "F3",
		115: "F4",
		116: "F5",
		117: "F6",
		118: "F7",
		119: "F8",
		120: "F9",
		121: "F10",
		122: "F11",
		123: "F12"};

	obj.setEvent("onkeydown", function(event){
		var key = keyNames[event.keyCode];
		if (event.keyCode >= 48 && event.keyCode <= 90){
			key = String.fromCharCode(event.keyCode);
		}
		if (key) {
			if (event.shiftKey) {key = "Shift" + key;}
			if (event.altKey) {key = "Alt" + key;}
			if (event.ctrlKey) {key = "Ctrl" + key;}
			this.raiseEvent("onKey" + key, event);
			event.cancelBubble = true;
		}
	});

//	---


	var targets = {};

	function raiseControlEvent(name, element, event){
		var a = ((element.id.match(/-*\d*-*\d*-*\d+$/) || ["-"])+"").split("-");
		var target = AW.object(element.id);
		if (target && target.raiseEvent){
			target.raiseEvent(name, event, a[1], a[2], a[3]);
		}
	}

	function handleMouse(e, event){
		try{

			var i, t = {};
			while(e) {
				if (e.getAttribute && (e.getAttribute("aw")||e.getAttribute("awx"))){
					t[e.id] = true;
				}
				e = e.parentNode;
			}
			for (i in targets){
				if (!t[i]){
					e = AW.element(i);
					if (e.getAttribute("aw")){
						raiseControlEvent(AW.camelCase("on", e.getAttribute("aw"), "mouseOut"), e, event);
						if (e.getAttribute("aw") != "control") {
							e.className = e.className.replace(/ aw-mouseover-\w+/g, "");
						}
					}
					if (e.getAttribute("awx")){
						raiseControlEvent(AW.camelCase("on", e.getAttribute("awx"), "mouseOut"), e, event);
						e.className = e.className.replace(/ aw-mouseover-\w+/g, "");
					}
				}
			}
			for (i in t){
				if (!targets[i]){
					e = AW.element(i);
					if (e.getAttribute("aw")){
						raiseControlEvent(AW.camelCase("on", e.getAttribute("aw"), "mouseOver"), e, event);
						if (e.getAttribute("aw") != "control") {
							e.className += " aw-mouseover-" + e.getAttribute("aw").toLowerCase();
						}
					}
					if (e.getAttribute("awx")){
						raiseControlEvent(AW.camelCase("on", e.getAttribute("awx"), "mouseOver"), e, event);
						e.className += " aw-mouseover-" + e.getAttribute("awx").toLowerCase();
					}
				}
			}
			targets = t;
		}
		catch(error){
		}
	}


	var empty = {};
	var handlers = {

		onmousemove: function(event){handleMouse(AW.srcElement(event), event)},
		onmouseover: function(event){handleMouse(AW.srcElement(event), event)},
		onmouseout: function(event){handleMouse(AW.toElement(event), event)},

		onmousedown: function(event){
			try{
				var e = AW.srcElement(event);
				while(e) {
					if (e.getAttribute && e.getAttribute("aw")){
						raiseControlEvent(AW.camelCase("on", e.getAttribute("aw"), "mouseDown"), e, event);
						if (e.getAttribute("aw") != "control") {
							e.className += " aw-mousedown-" + e.getAttribute("aw").toLowerCase();
						}
					}
					if (e.getAttribute && e.getAttribute("awx")){
						raiseControlEvent(AW.camelCase("on", e.getAttribute("awx"), "mouseDown"), e, event);
						e.className += " aw-mousedown-" + e.getAttribute("awx").toLowerCase();
					}
					e = e.parentNode;
				}
			}
			catch(x){
			}
		},

		onmouseup: function(event){
			try{
				var e = AW.srcElement(event);
				while(e) {
					if (e.getAttribute && e.getAttribute("aw")){
						raiseControlEvent(AW.camelCase("on", e.getAttribute("aw"), "mouseUp"), e, event);
						if (e.getAttribute("aw") != "control") {
							e.className = e.className.replace(/ aw-mousedown-\w+/g, "");
						}
					}
					if (e.getAttribute && e.getAttribute("awx")){
						raiseControlEvent(AW.camelCase("on", e.getAttribute("awx"), "mouseUp"), e, event);
						e.className = e.className.replace(/ aw-mousedown-\w+/g, "");
					}
					e = e.parentNode;
				}
			}
			catch(x){
			}
		},

		onclick: function(event){
			try{
				var e = AW.srcElement(event);
				var s = "Clicked";
				if (event.shiftKey) {s = "Shift" + s;}
				if (event.altKey) {s = "Alt" + s;}
				if (event.ctrlKey) {s = "Ctrl" + s;}
				while(e) {
					if (e.getAttribute && e.getAttribute("aw")){
						raiseControlEvent(AW.camelCase("on", e.getAttribute("aw"), s), e, event);
					}
					if (e.getAttribute && e.getAttribute("awx")){
						raiseControlEvent(AW.camelCase("on", e.getAttribute("awx"), s), e, event);
					}
					e = e.parentNode;
				}
			}
			catch(x){
			}
		},

		ondblclick: function(event){
			try{
				var e = AW.srcElement(event);
				var s = "DoubleClicked";
				if (event.shiftKey) {s = "Shift" + s;}
				if (event.altKey) {s = "Alt" + s;}
				if (event.ctrlKey) {s = "Ctrl" + s;}
				while(e) {
					if (e.getAttribute && e.getAttribute("aw")){
						raiseControlEvent(AW.camelCase("on", e.getAttribute("aw"), s), e, event);
					}
					if (e.getAttribute && e.getAttribute("awx")){
						raiseControlEvent(AW.camelCase("on", e.getAttribute("awx"), s), e, event);
					}
					e = e.parentNode;
				}
			}
			catch(x){
			}
		}
	};


	AW.register = function(win){

		if (win != window){
			win.AW = AW;
			AW.docs.push(win.document);
		}

		AW.forEach(handlers, function(name, handler){
			AW.attachEvent(win.document, name, handler);
		});

		function unregister(){
			AW.unregister(win);
			AW.detachEvent(win, "onunload", unregister);
			win = null;
		}

		AW.attachEvent(win, "onunload", unregister);
	};

	AW.unregister = function(win){

		AW.forEach(handlers, function(name, handler){
			AW.detachEvent(win.document, name, handler);
		});

		if (win != window){
			var i, docs = AW.docs;
			for(i=0; i<docs.length; i++) {
				if (docs[i]==win.document){
					docs.splice(i, 1);
					return;
				}
			}

			win.AW	= null;
		}
	};

	AW.register(window);
};


