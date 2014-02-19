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

AW.XML.Table = AW.HTTP.Request.subclass();

AW.XML.Table.create = function(){

/****************************************************************

	Table model for loading and parsing data in XML format.

*****************************************************************/

	var obj = this.prototype;
	var _super = this.superclass.prototype;

	if (AW.gecko) {
		var xpath = new XPathEvaluator();
	}

/****************************************************************

	Allows to process the received data.

	@param xml (DOMDocument) The received data.

*****************************************************************/

	obj.response = function(xml){
		this.setXML(xml);

		if (this.$owner) {
			this.$owner.clearSelectionModel();
			this.$owner.clearSortModel();
			this.$owner.clearRowModel();
			this.$owner.setRowCount(this.getCount());
			this.$owner.refresh();
		}
//		_super.response.call(this);
	};

/****************************************************************

	Sets or retrieves the XML document (or string).

*****************************************************************/

	obj.defineProperty("XML");

	obj.setXML = function(xml){

		if (!xml.nodeType) {
			var s = "" + xml;
			xml = new ActiveXObject("MSXML2.DOMDocument");
			xml.loadXML(s);
		}

		xml.setProperty("SelectionLanguage", "XPath");
		if (this._namespaces) {xml.setProperty("SelectionNamespaces", this._namespaces);}

		this._xml = xml;
		this._data = this._xml.selectSingleNode(this._dataPath);
		this._items = this._data ? this._data.selectNodes(this._itemPath) : null;
		this._ready = true;
	};


	if (AW.gecko){
		obj.setXML = function(xml){

			if (!xml.nodeType) {
				var parser = new DOMParser;
				xml = parser.parseFromString("" + xml, "text/xml");
			}
			else if (xml.nodeName == "XML" && xml.ownerDocument == document) {
				var node = xpath.evaluate("*", xml, null, 9, null).singleNodeValue;
				xml = document.implementation.createDocument("","", null);
				xml.appendChild(node);
			}

			namespaces = {};
			var a = this._namespaces.split(" xmlns:");

			for (var i=1; i<a.length; i++){
				var s = a[i].split("=");
				namespaces[s[0]] = s[1].replace(/\"/g, "");
			}

			this._ns = {
				lookupNamespaceURI : function(prefix){return namespaces[prefix]}
			};

			this._xml = xml;
			this._data = xpath.evaluate(this._dataPath, this._xml, this._ns, 9, null).singleNodeValue;
			this._items = this._data ? xpath.evaluate(this._itemPath, this._data, this._ns, 7, null) : null;
			this._ready = true;
		};
	}



	obj.getXML = function(){
		return this._xml;
	};

	obj._dataPath = "*";
	obj._itemPath = "*";
	obj._valuePath = "*";
	obj._valuesPath = [];
	obj._formats = [];

/****************************************************************

	Sets the XPath expressions to retrieve values for each column.

	@param array (Array) The array of XPaths expressions.

*****************************************************************/

	obj.setColumns = function(array){
		this._valuesPath = array;
	};

/****************************************************************

	Specifies the XPath expression to retrieve the set of rows.

	@param xpath (String) The xpath expression.

*****************************************************************/

	obj.setRows = function(xpath){
		this._itemPath = xpath;
	};

/****************************************************************

	Specifies the XPath expression to select the table root element.

	@param xpath (String) The xpath expression.

*****************************************************************/

	obj.setTable = function(xpath){
		this._dataPath = xpath;
	};

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

	Returns the number of the data rows.

*****************************************************************/

	obj.getCount = function(){
		if (!this._items) {return 0}
		return AW.gecko ? this._items.snapshotLength : this._items.length;
	};

/****************************************************************

	Returns the cell text.

	@param i (Index) Row index.
	@param j (Index) Column index.

*****************************************************************/

	obj.getText = function(i, j){
		var node = this.getNode(i, j);
		var data = node ? (AW.ie ? node.text : node.textContent) : "";
		var format = this._formats[i];
		return format ? format.dataToText(data) : data.replace(AW.textPattern, AW.textReplace);
	};


/****************************************************************

	Returns the cell image.

	@param i (Index) Row index.
	@param j (Index) Column index.

*****************************************************************/

	obj.getImage = function(){
		return "none";
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
		var node = this.getNode(i, j);
		var data = node ? (AW.ie ? node.text : node.textContent) : "";
		var format = this._formats[i];
		return format ? format.dataToValue(data) : data;
	};

/****************************************************************

	Returns the cell XML node text (internal).

	@param i (Index) Row index.
	@param j (Index) Column index.

*****************************************************************/

	obj.getNode = function(j, i){
		if (!this._items || !this._items[i]) {
			return null;
		}
		if (this._valuesPath[j]) {
			return this._items[i].selectSingleNode(this._valuesPath[j]);
		}
		else {
			return this._items[i].selectNodes(this._valuePath)[j];
		}
	};

	if (AW.gecko) {
		obj.getNode = function(c, r){

			if (!this._items) {return null}

			var row = this._items.snapshotItem(r);

			if (!row) {return null}

			if (this._valuesPath[c]) {
				return xpath.evaluate(this._valuesPath[c], row, this._ns, 9, null).singleNodeValue;
			}
			else {
				return xpath.evaluate(this._valuePath, row, this._ns, 7, null).snapshotItem(c);
			}
		};
	}

	obj.getFormat = function(i){
		return this._formats[i];
	};

};

