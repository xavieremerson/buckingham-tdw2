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

AW.Grid.Controllers.Navigation = (function(){

	function sync(){

		var mode = this.getCurrentSelection();
		var c1 = this.getSelectedColumns();
		var r1 = this.getSelectedRows();

		if (mode == "cell") {
			this.setTimeout(function(){

				var c2 = this.getSelectedColumns();
				var r2 = this.getSelectedRows();

				var i, j, x1 = {}, x2 = {};

				for (i=0; i<c1.length; i++){
					x1[c1[i]] = {};
					for (j=0; j<r1.length; j++){
						x1[c1[i]][r1[j]] = true;
					}
				}

				for (i=0; i<c2.length; i++){
					x2[c2[i]] = {};
					for (j=0; j<r2.length; j++){
						x2[c2[i]][r2[j]] = true;
					}
				}

				for (i=0; i<c1.length; i++){
					for (j=0; j<r1.length; j++){
						if((!x2[c1[i]] || !x2[c1[i]][r1[j]]) && this.getCellSelected(c1[i], r1[j])){
							this.setCellSelected(false, c1[i], r1[j]);
						}
					}
				}

				for (i=0; i<c2.length; i++){
					for (j=0; j<r2.length; j++){
						if((!x1[c2[i]] || !x1[c2[i]][r2[j]]) && !this.getCellSelected(c2[i], r2[j])){
							this.setCellSelected(true, c2[i], r2[j]);
						}
					}
				}
			}, 0);
		}
		else if (mode == "row") {
			this.setTimeout(function(){
				var r2 = this.getSelectedRows();

				var i, x1 = {}, x2 = {};

				for (i=0; i<r1.length; i++){
					x1[r1[i]] = true;
				}

				for (i=0; i<r2.length; i++){
					x2[r2[i]] = true;
				}

				for (i=0; i<r1.length; i++){
					if(!x2[r1[i]] && this.getRowSelected(r1[i])){
						this.setRowSelected(false, r1[i]);
					}
				}

				for (i=0; i<r2.length; i++){
					if(!x1[r2[i]] && !this.getRowSelected(r2[i])){
						this.setRowSelected(true, r2[i]);
					}
				}

			}, 0);
		}
	}

	function syncRows(value, index){

		var i, a = this.getSelectedRows();

		for(i=0; i<a.length; i++){
			if(a[i]==index){
				if(!value){
					a = a.concat();
					a.splice(i, 1);
					this.setSelectedRows(a);
				}
				return;
			}
		}

		if(value){
			a = a.concat(index);
			this.setSelectedRows(a);
		}
	}

	function row(dir, select){
		return function(event, i){

			i = (dir == "this") ? i : this.getCurrentRow();

			var k;

			switch(dir){

				case "previous":	i = this.getRowPrevious(i); break;
				case "next":		i = this.getRowNext(i); break;

				case "first":		i = this.getRowFirst(); break;
				case "last":		i = this.getRowLast(); break;

				case "pageup":		for(k=0; k<10; k++) {i = this.getRowPrevious(i)} break;
				case "pagedown":	for(k=0; k<10; k++) {i = this.getRowNext(i)} break;

			}

			this.setCurrentRow(i);

			if (select) {
				this.setSelectedRows([i]);
			}

			AW.setReturnValue(event, false);
		}
	}

	function cell(dir, select){
		return function(event, i, j){

			i = (dir == "this") ? i : this.getCurrentColumn();
			j = (dir == "this") ? j : this.getCurrentRow();

			var k;

			switch(dir){
				case "next":		i = this.getColumnNext(i); break;
				case "previous":	i = this.getColumnPrevious(i); break;

				case "first":		i = this.getColumnFirst(); break;
				case "last":		i = this.getColumnLast(); break;

				case "up":			j = this.getRowPrevious(j); break;
				case "down":		j = this.getRowNext(j); break;

				case "top":			j = this.getRowFirst(); break;
				case "bottom":		j = this.getRowLast(); break;

				case "pageup":		for(k=0; k<10; k++) {j = this.getRowPrevious(j)} break;
				case "pagedown":	for(k=0; k<10; k++) {j = this.getRowNext(j)} break;

				case "home":		i = this.getColumnFirst(); j = this.getRowFirst(); break;
				case "end":			i = this.getColumnLast(); j = this.getRowLast(); break;

			}

			this.setCurrentColumn(i);
			this.setCurrentRow(j);

			if (select) {
				this.setSelectedColumns([i]);
				this.setSelectedRows([j]);
			}

			AW.setReturnValue(event, false);
		}
	}

	function toggleThisRow(event, i){

		var v = this.getRowSelected(i);
		this.setRowSelected(!v, i);
		AW.setReturnValue(event, false);
	}

	return {

		toggleThisRow:			toggleThisRow,

		gotoThisRow:			row("this"),
		gotoPreviousRow:		row("previous"),
		gotoNextRow:			row("next"),
		gotoFirstRow:			row("first"),
		gotoLastRow:			row("last"),
		gotoPageUpRow:			row("pageup"),
		gotoPageDownRow:		row("pagedown"),

		selectThisRow:			row("this", true),
		selectPreviousRow:		row("previous", true),
		selectNextRow:			row("next", true),
		selectFirstRow:			row("first", true),
		selectLastRow:			row("last", true),
		selectPageUpRow:		row("pageup", true),
		selectPageDownRow:		row("pagedown", true),

		selectThisCell:			cell("this", true),
		selectUpperCell:		cell("up", true),
		selectLowerCell:		cell("down", true),
		selectTopCell:			cell("top", true),
		selectBottomCell:		cell("bottom", true),
		selectPreviousCell:		cell("previous", true),
		selectNextCell:			cell("next", true),
		selectFirstCell:		cell("first", true),
		selectLastCell:			cell("last", true),
		selectTopLeftCell:		cell("home", true),
		selectBottomRightCell:	cell("end", true),
		selectPageUpCell:		cell("pageup", true),
		selectPageDownCell:		cell("pagedown", true),

		onSelectedColumnsChanging:	sync,
		onSelectedRowsChanging:		sync,
		onRowSelectedChanged: 		syncRows,

		onSelectionModeChanged: function(mode){

			switch(mode){
				case "single-cell":
					this.setController("selection", AW.Grid.Controllers.SingleCell);
					this.setCurrentSelection("cell");
					break;

				case "single-row":
					this.setController("selection", AW.Grid.Controllers.SingleRow);
					this.setCurrentSelection("row");
					break;

				case "multi-row":
					this.setController("selection", AW.Grid.Controllers.MultiRow);
					this.setCurrentSelection("row");
					break;

				case "multi-row-marker":
					this.setController("selection", AW.Grid.Controllers.MultiRowMarker);
					this.setCurrentSelection("row");

					var checkbox = new AW.Templates.CheckedItem;
					this.setCellTemplate(checkbox, 0);

					break;
			}
		}
	};

})();

