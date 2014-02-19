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

AW.CSV.Table = AW.HTTP.Request.subclass();

AW.CSV.Table.create = function(){

/****************************************************************

	Table model for loading and parsing data in CSV text format.

*****************************************************************/

	var obj = this.prototype;
	var _super = this.superclass.prototype;

/****************************************************************

	Allows to process the received text.

	@param text (String) The downloaded text.

*****************************************************************/

	obj.response = function(text){
		var i, s, table = [];

		this._rows = text.split(/\r*\n/);

		if (this.$owner) {
			this.$owner.clearSelectionModel();
			this.$owner.clearSortModel();
			this.$owner.clearRowModel();
			this.$owner.setRowCount(this.getCount());
			this.$owner.refresh();
		}
//		_super.response.call(this);
	};

	obj._rows = [];
	obj._data = [];
	obj._formats = [];

/****************************************************************

	Allows to specify the formatting object for the column.

	@param format (Object) The formatting object.
	@param index (Index) The column index.

*****************************************************************/

	obj.setFormat = function(format, index){
		this._formats = this._formats.concat();
		this._formats[index] = format;
	};

/****************************************************************

	Allows to specify the formatting objects for each column.

	@param formats (Array) The array of formatting objects.

*****************************************************************/

	obj.setFormats = function(formats){
		this._formats = formats;
	};

/****************************************************************

	Returns the number of data rows.

*****************************************************************/

	obj.getCount = function(){
		return this._rows.length;
	};

	var pattern = new RegExp("(^|\\t|,)(\"*|'*)(.*?)\\2(?=,|\\t|$)", "g");

/****************************************************************

	Returns the cell text.

	@param r (Index) Row index.
	@param c (Index) Column index.

*****************************************************************/

	obj.getData = function(c, r){

		if (!this._data[r]){

			var s = this._rows[r].replace(/""/g, "'");
			s = s.replace(pattern, "$3\t");
			s = s.replace(/\t$/, "");
			this._data[r] = s ? s.split(/\t/) : [];
		}

		return this._data[r][c];

	};

/****************************************************************

	Returns the cell text.

	@param i (Index) Row index.
	@param j (Index) Column index.

*****************************************************************/

	obj.getText = function(i, j){
		var data = this.getData(i, j) || "";
		var format = this._formats[i];
		return format ? format.dataToText(data) : data.replace(AW.textPattern, AW.textReplace);
	};

/****************************************************************

	Returns the cell image.

	@param i (Index) Row index.
	@param j (Index) Column index.

*****************************************************************/

	obj.getImage = function(){
		return "";
	};

/****************************************************************

	Returns the cell hyperlink.

	@param i (Index) Row index.
	@param j (Index) Column index.

*****************************************************************/

	obj.getLink = function(){
		return "";
	};

/****************************************************************

	Returns the cell value.

	@param i (Index) Row index.
	@param j (Index) Column index.

*****************************************************************/

	obj.getValue = function(i, j){
		var data = this.getData(i, j) || "";
		var format = this._formats[i];
		return format ? format.dataToValue(data) : data;
	};

	obj.getFormat = function(i){
		return this._formats[i];
	};

};

