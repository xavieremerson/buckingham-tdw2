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

AW.Grid.Controllers.Row = (function(){

	function refresh(value, row){
		this.getRowTemplate(row).refreshClasses();
	}

	function addRow(row){
		var count = this.getRowCount();
		var a = this.getRowIndices();

		if (count<2){
			this.refresh();
			return;
		}

		var prev = a[count-2];

		if (!this.$extended){
			e = this.getRowTemplate(prev).element();
			if (e) {AW.setOuterHTML(e, this.getRowTemplate(prev).toString() + this.getRowTemplate(row).toString())}
			e = null;
		}
		else {
			for(i=0; i<3; i++){
				e = this.getRowTemplate(prev, i).element();
				if (e) {AW.setOuterHTML(e, this.getRowTemplate(prev, i).toString() + this.getRowTemplate(row, i).toString())}
				e = null;
			}
		}
		this.raiseEvent("adjustScrollHeight");
	}

	function removeRow(row){
		var i, e;
		if (!this.$extended){
			e = this.getRowTemplate(row).element();
			if (e) {AW.setOuterHTML(e, "")}
			e = null;
		}
		else {
			for(i=0; i<3; i++){
				e = this.getRowTemplate(row, i).element();
				if (e) {AW.setOuterHTML(e, "")}
				e = null;
			}
		}
		this.raiseEvent("adjustScrollHeight");
	}

	return {

		onRowAdded: addRow,
		onRowDeleted: removeRow,
		onRowSelectedChanged: "calculateRowState",
		onRowStateChanged:	refresh	};

})();

