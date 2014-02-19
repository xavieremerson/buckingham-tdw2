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

AW.Grid.Controllers.Virtual = (function(){

	var serial = 0;

	function virtual(){
		var s = ++serial;

		this.setTimeout(function(){

			var i=0, ii, a = [];
			var count = this.getRowProperty("count");
			var offset = this.getRowProperty("offset");
			var indices = this.getRowProperty("indices");

			var scroll = this.getScrollProperty("top");
			var row = this.getRowProperty("height");

			var top = Math.floor(scroll/row);

			var dir = this.getVirtualTop() > top ? "back" : "forward";
			this.setVirtualTop(top);

			var client = Math.floor(this.getContentHeight(1)/row);

			if (!client) {
				client = 40;
			}

			var x1 = Math.min(count, Math.max(0, top - 20));
			var x2 = Math.min(count, top);
			var x3 = Math.min(count, top + client);
			var x4 = Math.min(count, top + client + 20);

			function r1(){
				if (s != serial) {
					return;
				}

				if (i1 < x4 ) {
					ii = indices ? indices[i1+offset] : i1+offset;
					this.getRowTemplate(ii).refresh();
					i1++;
					this.setTimeout(r1);
				}
				else if (dir == "forward"){
					this.setTimeout(r2);
				}
			}

			function r2(){
				if (s != serial) {
					return;
				}

				if (i2 >= x1 ) {
					ii = indices ? indices[i2+offset] : i2+offset;
					this.getRowTemplate(ii).refresh();
					i2--;
					this.setTimeout(r2);
				}
				else if (dir == "back"){
					this.setTimeout(r1);
				}
			}

			i1 = x3;
			i2 = x2-1;

			if (dir == "forward" ){
				r1.call(this);
			}
			else {
				r2.call(this);
			}
		});
	}


	return {

		onCurrentRowChanged: function(i){

			var scroll = this.getScrollProperty("top");
			var height = this.getRowProperty("height");
			var top = (this.getRowPosition(i)-this.getRowOffset()) * height;

			if(top < scroll) {
				this.setTimeout(function(){
					this.setScrollTop(top);
				});
			}

			var max = this.getContentHeight(1);
			var bottom = top + height;

			if (max + scroll < bottom){
				this.setTimeout(function(){
					this.setScrollTop(bottom - max);
				});
			}
		},


		onCurrentColumnChanged: function(index){

			var scroll = this.getScrollProperty("left");
			var col = this.getColumnPosition(index);

			var i, a = this.getColumnIndices();

			var lw = 0, lc = this.$extended ? this.getFixedLeft() : 0;
			var mw = 0, c = this.getColumnCount();
			var rw = 0, rc = this.$extended ? this.getFixedRight() : 0;

			lw = this.getSelectorVisible() ? this.getSelectorWidth() : lw;

			if (!this.$extended){
				mw = lw;
				lw = 0;
			}

			for(i=0; i<lc; i++){
				lw += this.getColumnWidth(a ? a[i] : i);
			}

			for(i=lc; i<Math.min(col, c-rc-1); i++){
				mw += this.getColumnWidth(a ? a[i] : i);
			}

			for(i=c-rc; i<c; i++){
				rw += this.getColumnWidth(a ? a[i] : i);
			}

			if (!col) {
				mw = 0;
			}

			if (mw < scroll){
				this.setTimeout(function(){
					this.setScrollLeft(mw);
				});
				return;
			}

			var max = this.getContentWidth(1);
			var right = mw + this.getColumnWidth(index);

			if (max + scroll < right){
				this.setTimeout(function(){
					this.setScrollLeft(right - max);
				});
			}
		},

		onScrollTopChanging: function(scroll){

			if (!this.getVirtualMode() || this.getScrollTop()==scroll){
				return;
			}

			var s = ++serial;

			var row = this.getRowHeight();
			var top = Math.floor(scroll/row);

			if (Math.abs(top - this.getVirtualTop()) < 6 ){
				this.setTimeout(refreshRows, 1000);
				return;
			}

			function refreshRows(){
				if (s != serial) {return}
				this.getRowsTemplate().refresh();
				virtual.call(this);
			}

			this.setTimeout(refreshRows, 200);
		},

		paint: function(){

			if (!this.getVirtualMode()){
				return;
			}

			virtual.call(this);
		}

	};

})();

