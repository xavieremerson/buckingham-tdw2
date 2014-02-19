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

AW.UI.List = AW.System.Control.subclass();

AW.UI.List.create = function(){

	var obj = this.prototype;
	var box = new AW.HTML.SPAN;

	obj.setAttribute("tabIndex", 1);
	obj.setAttribute("hideFocus", "true");

	obj.setClass("ui", "list");
	obj.setClass("list", "control");
	obj.setClass("flow", "vertical");
	obj.setClass("text", "normal");

	box.setClass("list", "box");

	box.setContent("html", function(){return this.getLayoutTemplate()});
	obj.setContent("box", box);

	obj.onItemTemplateChanged = function(item){
		item.setClass("list", "item");
//		item.setClass("item", function(){return this.$0});
		item.setClass("items", function(){return this.getControlProperty("state") || "normal"});
		item.setAttribute("aw", "item");
		item.mapModel("control", "item");
		item.mapModel("state", "item");
	};

	obj.onContentTemplateChanged = function(view){
		view.setClass("list", "template");
	};

	obj.defineTemplate("layout", function(){return this.getScrollTemplate()});
	obj.defineTemplate("scroll", function(){return this.getContentTemplate()});
	obj.defineTemplate("content", new AW.Templates.List);
	obj.defineTemplate("item", new AW.Templates.ImageText);


//	---

	function viewNext(i){
		var p = Math.min(this.getViewPosition(i) + 1, this.getViewOffset() + this.getViewCount() - 1);
		var a = this.getViewIndices();
		return a ? a[p] : p;
	}

	function viewPrevious(i){
		var p = Math.max(this.getViewPosition(i) - 1, this.getViewOffset());
		var a = this.getViewIndices();
		return a ? a[p] : p;
	}

	function viewFirst(){
		var p = this.getViewOffset();
		var a = this.getViewIndices();
		return a ? a[p] : p;
	}

	function viewLast(){
		var p = this.getViewOffset() + this.getViewCount() - 1;
		var a = this.getViewIndices();
		return a ? a[p] : p;
	}

	function itemValue(i){
		var text = this.getItemText(i);
		var format = this.getItemFormat(i);
		return format ? format.textToValue(text) : text;
	}

	var itemModel = {
		count: 0,
		text:  "",
		image: "",
		link:  "",
		value: itemValue,
		format: "",
		tooltip: "",
		state: "",
		selected: false	};

	var viewModel = {
		offset: 0,
		count: function(){return this.getItemCount()},
		position: function(i){return Number(i)},
		next: viewNext,
		previous: viewPrevious,
		first: viewFirst,
		last: viewLast,
		expanded: false };	

	var selectionModel = {
		mode: "single"	};	// or multiple

	var selectedModel = {};

	var currentModel = {
		item: 0	};

	obj.defineModel("item", itemModel);
	obj.defineModel("state", {});
	obj.defineModel("view", viewModel);
	obj.defineModel("selection", selectionModel);
	obj.defineModel("selected", selectedModel);
	obj.defineModel("current", currentModel);


	obj.defineViewProperty("indices", "", true);
	obj.defineSelectedProperty("items", [], true);


//	---





	function refresh(v, i){
		this.getItemTemplate(i).refresh();
	}

	var itemController = {
		onItemTextChanged:	refresh,
		onItemImageChanged:	refresh,
		onItemValueChanged:	refresh,
		onItemLinkChanged:	refresh,
		onItemTooltipChanged:	refresh,
		onItemStateChanged:	refresh	};

	obj.setController("item", itemController);

//	---

	obj.setController("view", {

		onViewIndicesChanged: function(indices){
			var positions = [];
			for (var i=0; i<indices.length; i++){
				positions[indices[i]] = i;
			}
			this.setViewPosition(positions);
			this.refresh();
		}

	});

//	---


	function go(dir, select){
		return function(event, index){
			var i = (dir == "this") ? index : this.getCurrentItem();
			switch(dir){
				case "next":		i = this.getViewNext(i); break;
				case "previous":	i = this.getViewPrevious(i); break;
				case "first":		i = this.getViewFirst(); break;
				case "last":		i = this.getViewLast(); break;
			}
			if (select) {
				this.setSelectedItems([i]);
			}
			this.setCurrentItem(i);
			event.returnValue = false;
		}
	}

	function toggleThis(event, i){
		this.setItemSelected(!this.getItemSelected(i), i);
		this.setCurrentItem(i);
	}

	function toggleCurrent(){
		var i = this.getCurrentItem();
		this.setItemSelected(!this.getItemSelected(i), i);
	}

	var kb1 = {
		onKeyHome:			"selectFirstItem",
		onKeyEnd:			"selectLastItem",
		onKeyUp:			"selectPreviousItem",
		onKeyDown:			"selectNextItem",
		onKeyLeft:			"selectPreviousItem",
		onKeyRight:			"selectNextItem",
		onKeyPageUp:		"selectPreviousPage",
		onKeyPageDown:		"selectNextPage" };

	var kb2 = {
		onKeyHome:			"selectFirstItem",
		onKeyEnd:			"selectLastItem",
		onKeyUp:			"selectPreviousItem",
		onKeyDown:			"selectNextItem",
		onKeyPageUp:		"selectPreviousPage",
		onKeyPageDown:		"selectNextPage",

		onKeyCtrlHome:		"gotoFirstItem",
		onKeyCtrlEnd:		"gotoLastItem",
		onKeyCtrlUp:		"gotoPreviousItem",
		onKeyCtrlDown:		"gotoNextItem",
		onKeyCtrlPageUp:	"gotoPreviousPage",
		onKeyCtrlPageDown:	"gotoNextPage",

		onKeyCtrlSpace:		"toggleCurrent",
		onKeySpace:			"toggleCurrent"	};

	var ms1 = {
		onItemClicked:		"selectThisItem" };

	var ms2 = {
		onItemClicked:		"toggleThisItem" };

	var cur1 = {
		gotoThisItem:		go("this"),
		gotoPreviousItem:	go("previous"),
		gotoNextItem:		go("next"),
		gotoPreviousPage:	go("pageup"),
		gotoNextPage:		go("pagedown"),
		gotoFirstItem:		go("first"),
		gotoLastItem:		go("last"),

		selectThisItem:		go("this", true),
		selectPreviousItem:	go("previous", true),
		selectNextItem:		go("next", true),
		selectPreviousPage:	go("pageup", true),
		selectNextPage:		go("pagedown", true),
		selectFirstItem:	go("first", true),
		selectLastItem:		go("last", true),

		toggleThisItem:		toggleThis,
		toggleCurrent:		toggleCurrent };

	obj.setController("keyboard", kb1);
	obj.setController("mouse", ms1);
	obj.setController("navigation", cur1);

	obj.setController("selection", {
		onSelectionModeChanged: function(mode){
			switch(mode){
				case "single":
					this.setController("keyboard", kb1);
					this.setController("mouse", ms1);
					break;
				case "multi":
					this.setController("keyboard", kb2);
					this.setController("mouse", ms2);
					break;
			}
		}
	});


	function calcSelected(value, index){

		var i, a = this.getSelectedItems();

		for(i=0; i<a.length; i++){
			if(a[i]==index){
				if(!value){
					a = a.concat();
					a.splice(i, 1);
					this.setSelectedItems(a);
				}
				return;
			}
		}

		if(value){
			a = a.concat(index);
			this.setSelectedItems(a);
		}
	}

	obj.setController("selected", {
		onItemSelectedChanged: calcSelected }
	);

	function syncState(items2){

		var items1 = this.getSelectedItems();

		this.setTimeout(function(){

			var i, r1 = {}, r2 = {};

			for (i=0; i<items1.length; i++){
				r1[items1[i]] = true;
			}

			for (i=0; i<items2.length; i++){
				r2[items2[i]] = true;
			}

			for (i=0; i<items1.length; i++){
				if(!r2[items1[i]] && this.getItemSelected(items1[i])){
					this.setItemSelected(false, items1[i]);
				}
			}

			for (i=0; i<items2.length; i++){
				if(!r1[items2[i]] && !this.getItemSelected(items2[i])){
					this.setItemSelected(true, items2[i]);
				}
			}
		}, 0);
	}


	obj.setController("state", {
		onCurrentItemChanging: updateCurrent,
		onSelectedItemsChanging: syncState,
		onItemSelectedChanged: "calculateItemState" }
	);

	function updateCurrent(i2){
		var i1 = this.getCurrentItem();
		this.setTimeout(function(){
			this.raiseEvent("calculateItemState", 0, i2);
			var e2 = this.getItemTemplate(i2).getContent("box/text").element();
			if (e2 && e2.focus) {
				e2.tabIndex = this.getTabIndex();
				e2.focus();
			}
			e2 = null;
			this.raiseEvent("calculateItemState", 0, i1);
			var e1 = this.getItemTemplate(i1).getContent("box/text").element();
			if (e1 && i1 != i2) {
				e1.tabIndex = -1;
			}
			e1 = null;
		}, 0);
	}

	obj.calculateItemState = function(v, i){
		var state = "";

		if (this.getCurrentItem()==i){state = "current"}
		if (this.getItemSelected(i)){state = "selected"}

		this.setItemState(state, i);
	};

};

