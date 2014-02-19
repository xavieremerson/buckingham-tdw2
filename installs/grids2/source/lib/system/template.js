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

AW.System.Template = AW.System.HTML.subclass();

AW.System.Template.create = function(){

/****************************************************************

	Generic HTML template class. Template is a re-usable HTML
	fragment aimed to produce markup as part of a larger
	object (control).

	Template can either be a simple element or a complex HTML structure
	and may include calls to other templates as part of the output.

	Templates can access properties of the parent control,
	so the template output will be different depending on
	the control's data. Templates can also accept parameters
	allowing to generate lists or tables of data with the
	single instance of the template.

*****************************************************************/

	var obj = this.prototype;

	obj.lock = function(){
		if (!this.$owner){
			return;
		}
		this.$owner[AW.camelCase("set", this.$name, "template")](this, this.$0, this.$1, this.$2);
	};

/****************************************************************

	Returns the template object.

	@param	name	(String) Template name.
	@return			Template object.

*****************************************************************/

	obj.getTemplate = function(name){
		var i, args = [], get = AW.camelCase("get", name, "template");
		for(i=1; i<arguments.length; i++) {args[i-1]=arguments[i]}
		return this[get].apply(this, args);
	};

/****************************************************************

	Sets the template.

	@param	name	(String) Template name.
	@param	template (Object) Template object.

*****************************************************************/

	obj.setTemplate = function(name, template, index){
		var set = AW.camelCase("set", name, "template");
		this[set](template, index);
	};


	obj.raiseEvent = function(name, source, a, b, c){

		if (typeof source == "undefined") {
			source = this;
			a = this.$0;
			b = this.$1;
			c = this.$2;
		}

		var handler = this[name];

		if (typeof(handler)=="function"){
			var r = handler.call(this, source, a, b, c);
			if (r) {
				return r;
			}
		}

		if (this.$owner && this.$owner.raiseEvent) {
			return this.$owner.raiseEvent(name, source, a, b, c);
		}

	};


//	obsolete - do not use
	obj.action = function(name, source, a, b, c){
		this.raiseEvent(AW.camelCase("on", name), source, a, b, c);
	};

	obj.mapTemplate = function(source, target){

		var get = AW.camelCase("get", source, "template");

		if (typeof(target)=="function") {
			this[get] = target;
		}
		else {
			var u, m = AW.camelCase("get", target, "template");
			this[get] = function(a, b, c){
				if (a === u) {return this.$owner[m](this.$0, this.$1, this.$2)}
				if (b === u) {return this.$owner[m](a, this.$0, this.$1)}
				if (c === u) {return this.$owner[m](a, b, this.$0)}
				return this.$owner[m](a, b, c);
			}
		}
		this.lock();
	};

	obj.mapModel = function(source, target, target2){

		var get = AW.camelCase("get", source, "property");
		var set = AW.camelCase("set", source, "property");

		if (typeof(target)=="function") {
			this[get] = target;
			if (typeof(target2)=="function") {
				this[set] = target2;
			}
			else {
				this[set] = function(){};
			}
		}
		else {
			var _get = AW.camelCase("get", target, "property");
			var _set = AW.camelCase("set", target, "property");
			var u;

			this[get] = function(p, a, b, c){
				if (a === u) {return this.$owner[_get](p, this.$0, this.$1, this.$2)}
				if (b === u) {return this.$owner[_get](p, a, this.$0, this.$1)}
				if (c === u) {return this.$owner[_get](p, a, b, this.$0)}
				return this.$owner[_get](p, a, b, c);
			};

			this[set] = function(p, v, a, b, c){
				if (a === u) {return this.$owner[_set](p, v, this.$0, this.$1, this.$2)}
				if (b === u) {return this.$owner[_set](p, v, a, this.$0, this.$1)}
				if (c === u) {return this.$owner[_set](p, v, a, b, this.$0)}
				return this.$owner[_set](p, v, a, b, c);
			};
		}
		this.lock();
	};
};

