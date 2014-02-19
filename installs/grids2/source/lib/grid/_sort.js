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

AW.Grid.Controllers.Sort = {

	doSort: function(src, index, header){

		if (!(header == "0" || typeof(header) == "undefined")) {return}

		var format = this.getCellFormat(index);

		function compare(values, pos, dir){

			var greater = 1, less = -1;
			if (dir == "descending"){
				greater = -1;
				less = 1;
			}

			var equal = function(i, j){
				var a = pos[i];
				var b = pos[j];
				if (a > b) {return 1}
				if (a < b) {return -1}
				return 0;
			};

			var error = function(){
				return 0;
			};

			var types = {
				"undefined"	: 0,
				"boolean"	: 1,
				"number"	: 2,
				"string"	: 3,
				"object"	: 4,
				"function"	: 5	};

			if (format) {
				return format.comparator(values, greater, less, equal, error);
			}
			else if ("".localeCompare) {
				return function(i, j){
					try {
						var a = values[i], b = values[j], x, y;
						if (typeof(a) != typeof(b)){
							x = types[typeof(a)];
							y = types[typeof(b)];
							if (x > y) {return greater}
							if (x < y) {return less}
						}
						else if (typeof(a)=="number"){
							if (a > b) {return greater}
							if (a < b) {return less}
						}
						else {
							return (greater * (("" + a).localeCompare(b))) || equal(i, j);
						}
					}
					catch(e){
						return error(i, j, e);
					}
				}
			}
			else {
				return function(i, j){
					try {
						var a = values[i], b = values[j], x, y;
						if (typeof(a) != typeof(b)){
							x = types[typeof(a)];
							y = types[typeof(b)];
							if (x > y) {return greater}
							if (x < y) {return less}
						}
						else {
							if (a > b) {return greater}
							if (a < b) {return less}
						}
						return equal(i, j);
					}
					catch(e){
						return error(i, j, e);
					}
				}
			}
		}

		var direction = this.getSortDirection(index);

		if (direction != "ascending" ) {
			direction = "ascending";
		}
		else {
			direction = "descending";
		}

		if (src == "ascending" || src == "descending"){
			direction = src;
		}

		var i, value = {}, pos = {};
		var offset = this.getRowOffset();
		var count = this.getRowCount();
		var rows = this.getRowIndices();

		if (offset){
			//alert("sorting not implemented");
			//return;
		}

//		var old_rows = rows;

		if (!rows) {
			rows = [];
			for(i=0; i<count;i++){
				rows[i]=i+offset;
			}
		}
		else {
			rows = rows.slice(offset, offset+count);
		}

		for (i=0; i<rows.length; i++) {
			value[rows[i]] = this.getCellValue(index, rows[i]);
			pos[rows[i]] = i;
		}

		rows.sort(compare(value, pos, direction));

//		if (old_rows) {
//			rows = [].concat(old_rows.slice(0, offset), rows, old_rows.slice(offset+count));
//		}
//		else {
			var a = [];
			for(i=0; i<offset;i++){
				a[i]=i;
			}
			rows = a.concat(rows);
//		}

		var old = this.getSortColumn();

		if (old != -1) {
			this.setSortDirection("", old);
		}

		this.setSortColumn(index);
		this.setSortDirection(direction, index);
		this.setRowIndices(rows);
	},

	onHeaderClicked: "doSort" };
