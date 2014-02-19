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

AW.System.Object = function(){};

AW.System.Object.subclass = function(){

	var create = function(cls){
		cls.created = true;
		if (cls.superclass && !cls.superclass.created){
			create(cls.superclass);
		}
		cls.create();
	};

	var constructor = function(a, b, c){

		if (constructor.defer){
			return;
		}

		if (!constructor.created){
			create(constructor);
		}

		if (this.init){
			this.init(a, b, c);
		}
	};


	for (var i in this) {
		constructor[i] = this[i];
	}

	this.defer = true;
	constructor.prototype = new this();
	this.defer = false;

	constructor.prototype.constructor = constructor;
	constructor.superclass = this;
	constructor.created = false;

	return constructor;
};

AW.System.Object.handle = function(error){
	throw(error);
};

AW.System.Object.create = function(){

/****************************************************************

	Generic base class - root of the ActiveWidgets class hierarchy.

*****************************************************************/

	var obj = this.prototype;

/****************************************************************

	Creates an object clone.

	@return		A new object.

	The clone function creates a fast copy of the object. Instead of
	physically copying each property and method of the source object -
	it creates a clone as a ‘subclass’ of the source object, i.e.
	properties and methods  are inherited from the source object into
	the clone.

	Note that the clone continues to be dependent on the source
	object. Changes in the source object property or method will
	affect all the clones unless this property is already overwritten
	in the clone object itself.

*****************************************************************/

	obj.clone = function(){
		if (this._clone.prototype!==this) {
			this._clone = function(){this.init()};
			this._clone.prototype = this;
		}
		return new this._clone();
	};

	obj._clone = function(){};

/****************************************************************

	Initializes the object.

	@remarks

	This method normaly contains all object initialization code
	(instead of the constructor function).	Constructor function is
	the same for all objects and only contains object.init() call.

*****************************************************************/

	obj.init = function(){
		// overload
	};

/****************************************************************

	Handles exceptions in the ActiveWidgets methods.

	@param	error (Error) Error object.

	The default error handler just throws the same exception to the
	next level. Overload this function to add your own diagnostics
	and error logging.

*****************************************************************/

 	obj.handle = function(error){
		throw(error);
	};

/****************************************************************

	Calls a method after a specified time interval has elapsed.

	@param	handler (Function) Method to call.
	@param	delay (Number) Time interval in milliseconds.
	@return An identifier that can be used with window.clearTimeout
			to cancel the current method call.

	This method has the same effect as window.setTimeout except that
	the function will be evaluated not as a global function but
	as a method of the current object.

*****************************************************************/

	obj.setTimeout = function(handler, delay){
		var self = this;
		var wrapper = function(){handler.call(self)};
		return window.setTimeout(wrapper, delay ? delay : 0);
	};

	obj.timeout = obj.setTimeout;

/****************************************************************

	Converts object to string.

	@return Text or HTML representation of the object.

	This method is overloaded in ActiveWidgets subclasses.

*****************************************************************/

 	obj.toString = function(){
		return "";
	};

};

AW.System.Object.create();

