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

AW.System.Model = AW.System.Object.subclass();

AW.System.Model.create = function(){

/****************************************************************

	Generic data model class.

*****************************************************************/

	var obj = this.prototype;

	var join = function(){
		var i, s = arguments[0];
		for (i=1; i<arguments.length; i++){s += arguments[i].substr(0,1).toUpperCase() + arguments[i].substr(1)}
		return s;
	};

/****************************************************************

	Creates a new property.

	@param	name	(String) Property name.
	@param	value	(String) Default property value.

*****************************************************************/

	obj.defineProperty = function(name, value){

		var _getProperty = join("get", name);
		var _setProperty = join("set", name);
		var _property = "_" + name;

		var getProperty = function(){
			return this[_property];
		};

		this[_setProperty] = function(value){
			if(typeof value == "function"){
				this[_getProperty] = value;
			}
			else {
				this[_getProperty] = getProperty;
				this[_property] = value;
			}
		};

		this[_setProperty](value);
	};

	var get = {};
	var set = {};

/****************************************************************

	Returns property value.

	@param	name	(String) Property name.
	@return Property value.

*****************************************************************/

	obj.getProperty = function(name, a, b, c){
		if (!get[name]) {get[name] = join("get", name)}
		return this[get[name]](a, b, c);
	};

/****************************************************************

	Sets property value.

	@param	name	(String) Property name.
	@param	value	(String) Property value.

*****************************************************************/

	obj.setProperty = function(name, value, a, b, c){
		if (!set[name]) {set[name] = join("set", name)}
		return this[set[name]](value, a, b, c);
	};

/****************************************************************

	Indicates whether the data is available.

*****************************************************************/

	obj.isReady = function(){
		return true;
	};
};


