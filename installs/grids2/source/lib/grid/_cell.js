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

AW.Grid.Controllers.Cell = (function(){

	function refresh(value, col, row){
		this.getCellTemplate(col, row).refresh();
	}

	function refreshClasses(value, col, row){
		this.getCellTemplate(col, row).refreshClasses();
	}

	function edit(){

		var i = this.getCurrentColumn();
		var j = this.getCurrentRow();

		if (!this.getCellEditable(i, j)){
			return;
		}

		var editor = this.getEditorTemplate(i, j);
		var cell = this.getCellTemplate(i, j);
		if (cell.element()){

			AW.setOuterHTML(cell.element(), editor);
			this.$edit = true;
			this.setTimeout(function(){
				var text = editor.element().getElementsByTagName("input")[0];
				text.focus();
				text.select();
				text = null;
			});
		}
	}

	function update(text, i, j){
		var cell = this.getCellTemplate(i, j);
		var editor = this.getEditorTemplate(i, j);
		if (editor.element()) {
			AW.setOuterHTML(editor.element(), cell);
		}
		this.$edit = false;
	}

	function checkUpdate(v, i, j){
		if (this.$edit) {
			update.call(this, v, i, j);
		}
	}

	function cellData(val, col, row){

		function dataToText(i, j){
			var data = this.getCellData(i, j);
			var format = this.getCellFormat(i, j);
			return format ? format.dataToText(data) : data;
		}

		function dataToValue(i, j){
			var data = this.getCellData(i, j);
			var format = this.getCellFormat(i, j);
			return format ? format.dataToValue(data) : data;
		}

		this.setCellText(dataToText, col, row);
		this.setCellValue(dataToValue, col, row);

	}

	function tooltip(event, col, row){
		var e = this.getCellTemplate(col, row).element();
		var s = this.getCellTooltip(col, row);
		if (e) {e.setAttribute("title", s)}
		e = null;
	}


	return {

		onCellMouseOver:	tooltip,

		editCurrentCell: edit,
		update: update,

		onCellSelectedChanging: checkUpdate,
		onCellSelectedChanged: "calculateCellState",

		onCellDataChanged:	cellData,

		onCellTextChanged:	refresh,
		onCellImageChanged:	refresh,
		onCellValueChanged:	refresh,
		onCellStateChanged:	refreshClasses	};

})();

