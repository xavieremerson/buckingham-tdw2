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

AW.System.Format = AW.System.Object.subclass();

AW.System.Format.create = function(){

/****************************************************************

	Generic data formatting class.

*****************************************************************/

	var obj = this.prototype;

/****************************************************************

	Transforms the primitive value into the readable text.

	@param	value	(Any) Primitive value.
	@return		Readable text.

*****************************************************************/

	obj.valueToText = function(value){
		return value;
	};

/****************************************************************

	Transforms the wire data into the primitive value.

	@param	data	(String) Wire data.
	@return		Primitive value.

*****************************************************************/

	obj.dataToValue = function(data){
		return data;
	};

/****************************************************************

	Transforms the wire data into the readable text.

	@param	data	(String) Wire data.
	@return		Readable text.

*****************************************************************/

	obj.dataToText = function(data){
		var value = this.dataToValue(data);
		return this.valueToText(value);
	};

/****************************************************************

	Specifies the text to be returned in case of error.

	@param	text	(String) Error text.

*****************************************************************/

	obj.setErrorText = function(text){
		this._textError = text;
	};

/****************************************************************

	Specifies the value to be returned in case of error.

	@param	value	(Any) Error value.

*****************************************************************/

	obj.setErrorValue = function(value){
		this._valueError = value;
	};

	obj.setErrorText("#ERR");
	obj.setErrorValue(NaN);

	obj.textToValue = function(text){
		return text;
	};

	obj.textToData = function(text){
		return text;
	};

	obj.valueToData = function(value){
		return value;
	};

	obj.comparator = function(values, greater, less, equal, error){
		return function(i, j){
			try {
				var a = values[i];
				var b = values[j];
				if (a > b) {return greater}
				if (a < b) {return less}
				return equal(i, j);
			}
			catch(e){
				return error(i, j, e);
			}
		}
	};

};


