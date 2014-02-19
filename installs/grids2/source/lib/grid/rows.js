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

AW.Grid.Rows = AW.Templates.List.subclass();

AW.Grid.Rows.create = function(){

	var obj = this.prototype;
	var _super = this.superclass.prototype;

	obj.setClass("grid", "view");

	obj.setContent("items", function(){

		var i, ii, a = [];
		var count = this.getViewProperty("count");
		var offset = this.getViewProperty("offset");
		var indices = this.getViewProperty("indices");
		var virtual = this.getVirtualProperty("mode");

		if (!virtual) {

			for(i=0; i<count; i++){
				ii = indices ? indices[i+offset] : i+offset;
				a[i] = this.getItemTemplate(ii).toString();
			}

		}
		else {

			var scroll = this.getScrollProperty("top");
			var height = this.getRowProperty("height");

			var top = Math.floor(scroll/height);

			var e = this.$owner.element();
			var client;

			if (e) {
				client = Math.floor(e.offsetHeight/height);
			}

			e = null;

			if (!client) {
				client = 40;
			}

			var x1 = Math.min(count, Math.max(0, top - 50));
			var x2 = Math.min(count, top);
			var x3 = Math.min(count, top + client);
			var x4 = Math.min(count, top + client + 50);

			this.$owner._fast = true;

			for(i=x1; i<x2; i++){
				ii = indices ? indices[i+offset] : i+offset;
				a[i] = this.getItemTemplate(ii).toString();
			}

			this.$owner._fast = false;

			for(i=x2; i<x3; i++){
				ii = indices ? indices[i+offset] : i+offset;
				a[i] = this.getItemTemplate(ii).toString();
			}

			this.$owner._fast = true;

			for(i=x3; i<x4; i++){
				ii = indices ? indices[i+offset] : i+offset;
				a[i] = this.getItemTemplate(ii).toString();
			}

			this.$owner._fast = false;
		}

		return a.join("");
	});

	var span = AW.HTML.SPAN;

	var top = new span;
	top.setClass("view", "top");
	top.setStyle("height", function(){

		var virtual = this.getVirtualProperty("mode");

		if (!virtual) {
			return 0;
		}

		var scroll = this.getScrollProperty("top");
		var height = this.getRowProperty("height");
		var offset = Math.max(0, Math.floor(scroll/height)-50) * height;

		return offset + "px";
	});

	obj.setContent("start", top);

	var space = new span;
	var box = new span;

	space.setClass("item", "template");
	space.setClass("row", "selector");
	space.setClass("selector", "space");
	box.setClass("item", "box");

	space.setContent("box", box);
	obj.setContent("end", space);

	obj.refresh = function(){
		if (typeof(this.$0) == "undefined" && this.$owner.$extended){
			for (var i=0; i<3; i++) {
				_super.refresh.call(this.$owner.getRowsTemplate(i));
			}
		}
		else {
			_super.refresh.call(this);
		}
	};

};

