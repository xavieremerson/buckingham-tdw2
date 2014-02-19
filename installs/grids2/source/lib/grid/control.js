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

AW.Grid.Control = AW.System.Control.subclass();

AW.Grid.Control.create = function(){

	var obj = this.prototype;
	var _super = this.superclass.prototype;

	obj.setClass("grid", "control");
	obj.setClass("selectors", function(){return this.getSelectorVisible() ? "visible" : "hidden"});
	obj.setAttribute("tabIndex", -1);

	var sample = new AW.HTML.SPAN;
	sample.setClass("row", "sample");
	sample.setClass("grid", "row");
	obj.setContent("sample", sample);

	var box = new AW.HTML.SPAN;
	box.setClass("grid", "box");

	box.setContent("html", function(){return this.getLayoutTemplate()});
	obj.setContent("box", box);


//	---

	var GC = AW.Grid.Controllers;

	obj.setController("size", GC.Size);
	obj.setController("cell", GC.Cell);
	obj.setController("row", GC.Row);
	obj.setController("view", GC.View);
	obj.setController("navigation", GC.Navigation);
	obj.setController("selection", GC.SingleCell);
	obj.setController("sort", GC.Sort);
	obj.setController("overflow", GC.Overflow);
	obj.setController("scroll", GC.Scroll);
	obj.setController("width", GC.Width);
	obj.setController("virtual", GC.Virtual);
	obj.setController("grid", GC.Grid);

//	---

	obj.defineTemplate("layout", function(){return this.getScrollTemplate()});
	obj.defineTemplate("scroll", new AW.Scroll.Bars);
	obj.defineTemplate("content", new AW.Panels.Horizontal);
	obj.defineTemplate("panel", function(){return ""});
	obj.defineTemplate("rows", new AW.Grid.Rows);
	obj.defineTemplate("row", new AW.Grid.Row);
	obj.defineTemplate("cell", new AW.Templates.Text);
	obj.defineTemplate("headers", new AW.Grid.Row);
	obj.defineTemplate("footers", new AW.Grid.Row);
	obj.defineTemplate("header", new AW.Grid.Header);
	obj.defineTemplate("footer", new AW.Templates.ImageText);
	obj.defineTemplate("separator", new AW.Grid.Separator);
	obj.defineTemplate("editor", new AW.UI.Input);
	obj.defineTemplate("selector", new AW.Templates.ImageText);
	obj.defineTemplate("topSelector", new AW.Templates.ImageText);
	obj.defineTemplate("bottomSelector", new AW.Templates.ImageText);
	obj.defineTemplate("popup", new AW.System.Template);

//	---

	function rowNext(i){
		var p = Math.min(this.getRowPosition(i) + 1, this.getRowOffset() + this.getRowCount() - 1);
		var a = this.getRowIndices();
		return a ? a[p] : p;
	}

	function rowPrevious(i){
		var p = Math.max(this.getRowPosition(i) - 1, this.getRowOffset());
		var a = this.getRowIndices();
		return a ? a[p] : p;
	}

	function rowFirst(){
		var p = this.getRowOffset();
		var a = this.getRowIndices();
		return a ? a[p] : p;
	}

	function rowLast(){
		var p = this.getRowOffset() + this.getRowCount() - 1;
		var a = this.getRowIndices();
		return a ? a[p] : p;
	}

//	---

	function columnNext(i){
		var p = Math.min(this.getColumnPosition(i) + 1, this.getColumnOffset() + this.getColumnCount() - 1);
		var a = this.getColumnIndices();
		return a ? a[p] : p;
	}

	function columnPrevious(i){
		var p = Math.max(this.getColumnPosition(i) - 1, this.getColumnOffset());
		var a = this.getColumnIndices();
		return a ? a[p] : p;
	}

	function columnFirst(){
		var p = this.getColumnOffset();
		var a = this.getColumnIndices();
		return a ? a[p] : p;
	}

	function columnLast(){
		var p = this.getColumnOffset() + this.getColumnCount() - 1;
		var a = this.getColumnIndices();
		return a ? a[p] : p;
	}

//	---

	function cellValue(i, j){
		var text = this.getCellText(i, j);
		var format = this.getCellFormat(i, j);
		return format ? format.textToValue(text) : AW.textToValue(text);
	}

//	---

	var scrollModel = {
		left:	0,
		top:	0,
		width:	0,
		height:	0,
		bars:	"both" };

	var cellModel = {
		text:  "",
		image: "",
		link: "",
		value: cellValue,
		data: "",
		format: "",
		tooltip: "",
		state: "",
		selected: false,
		editable: true };

	var columnModel = {
		offset: 0,
		count:  0,
		position: function(i){return Number(i)},
		next: columnNext,
		previous: columnPrevious,
		first: columnFirst,
		last: columnLast,
		state: "",
		selected: false,
		resizable: true,
		width: 100 };

	var rowModel = {
		offset: 0,
		count:  0,
		position: function(i){return Number(i)},
		next: rowNext,
		previous: rowPrevious,
		first: rowFirst,
		last: rowLast,
		state: "",
		selected: false,
		height: 18 };

	var currentModel = {
		row:	0,
		column:	0,
		selection: "cell" };

	var selectedModel = {};

	var selectionModel = {
		mode:	"rows",
		multiple: false	};

	var sortModel = {
		column: -1,
		direction: ""};

	var headerModel = {
		count: 0,
		text:  "",
		image: "",
		value: "",
		tooltip: "",
		state: "",
		count:  1,
		offset: 0,
		height: 20,
		visible: true };

	var selectorModel = {
		text:  "",
		image: "",
		value: "",
		tooltip: "",
		state: "",
		width: 20,
		resizable: false,
		visible: false };

	var topModel = {
		text:  "",
		image: "",
		value: "",
		tooltip: "",
		state: "" };

	var fixedModel = {
		left:  1,
		right: 0 };

	var virtualModel = {
		mode:	true,
		top: 0 };

	var contentModel = {
		width: 0,
		height: 0 };


	obj.defineModel("scroll", scrollModel);
	obj.defineModel("cell", cellModel);
	obj.defineModel("header", headerModel);
	obj.defineModel("footer", headerModel);
	obj.defineModel("selector", selectorModel);
	obj.defineModel("top", topModel);
	obj.defineModel("bottom", topModel);
	obj.defineModel("column", columnModel);
	obj.defineModel("row", rowModel);
	obj.defineModel("current", currentModel);
	obj.defineModel("selected", selectedModel);
	obj.defineModel("selection", selectionModel);
	obj.defineModel("sort", sortModel);
	obj.defineModel("virtual", virtualModel);
	obj.defineModel("content", contentModel);
	obj.defineModel("panel", contentModel);
	obj.defineModel("fixed", fixedModel);

	obj.defineColumnProperty("indices", "", true);
	obj.defineRowProperty("indices", "", true);
	obj.defineHeaderProperty("indices", "", true);
	obj.defineFooterProperty("indices", "", true);

	obj.defineSelectedProperty("rows", [], true);
	obj.defineSelectedProperty("columns", [], true);

	obj.setFooterVisible(false);
	obj.setContentWidth(100, 0);
	obj.setContentHeight(20, 0);

//	---

	obj.getCellEditor = obj.getEditorTemplate;
	obj.setCellEditor = obj.setEditorTemplate;

//	---

	obj.calculateRowState = function(v, i){
		var state = "";
		if (this.getCurrentRow()==i){state = "current"}
		if (this.getRowSelected(i)){state = "selected"}
		this.setRowState(state, i);
	};

	obj.calculateCellState = function(v, i, j){
		var state = "";
		if (this.getCurrentColumn()==i && this.getCurrentRow()==j){state = "current"}
		if (this.getCellSelected(i, j)){state = "selected"}
		this.setCellState(state, i, j);
	};

//	---

	if (AW.ie){
		var paint = new AW.HTML.SPAN;
		paint.setStyle("visibility", "expression(AW.paint(this))");
		obj.setContent("paint", paint);
	}

	if (AW.safari || AW.opera){
		obj.toString = function(){
			this.setTimeout(function(){
				this.raiseEvent("paint");
			}, 3000);
			return _super.toString.call(this);
		}
	}

//	---

	obj.addRow = function(row){
		if (this.raiseEvent("onRowAdding", row)){
			return;
		}

		var i, count = this.getRowCount();
		var a = this.getRowIndices();

		if (typeof(row) == "undefined"){
			row = count;
		}

		if (this._cellModel && this._cellModel.addRow){
			this._cellModel.addRow(row);
		}

		if (!a) {
			a = [];
			for(i=0; i<count;i++){
				a[i]=i;
			}
		}

		a.push(row);

		var refresh = this.refresh;
		this.refresh = function(){};

		this.setRowIndices(a);
		this.setRowCount(count+1);
		this.setCurrentRow(row);
		this.setSelectedRows([row]);

		this.refresh = refresh;
		this.raiseEvent("onRowAdded", row);
	};

	obj.deleteRow = function(row){

		if (this.raiseEvent("onRowDeleting", row)){
			return;
		}

		if (this._cellModel && this._cellModel.deleteRow){
			this._cellModel.deleteRow(row);
		}

		var i, count = this.getRowCount();
		var a = this.getRowIndices();

		if (!a) {
			a = [];
			for(i=0; i<count;i++){
				a[i]=i;
			}
			i = row;
		}
		else {
			i = this.getRowPosition(row);
		}
		a.splice(i, 1);

		var refresh = this.refresh;
		this.refresh = function(){};

		this.setRowIndices(a);
		this.setRowCount(count-1);
		this.setCurrentRow(i>0 ? a[i-1] : -1);
		this.setSelectedRows(i>0 ? [a[i-1]] : []);

		this.refresh = refresh;
		this.raiseEvent("onRowDeleted", row);
	};

	obj.sort = function(column, direction){
		this.raiseEvent("doSort", direction, column);
	}
};

AW.UI.Grid = AW.Grid.Control;

