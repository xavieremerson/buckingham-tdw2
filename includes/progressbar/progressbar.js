var gPopupMaskPB = null;
var gPopupContainerPB = null;
var gPopFramePB = null;
var gReturnFuncPB;
var gPopupIsShownPB = false;
var gHideSelectsPB = false;
var gReturnValPB = null;

var gTabIndexesPB = new Array();
// Pre-defined list of tags we want to disable/enable tabbing into
var gTabbableTagsPB = new Array("A","BUTTON","TEXTAREA","INPUT","IFRAME");	

// If using Mozilla or Firefox, use Tab-key trap.
if (!document.all) {
	document.onkeypress = keyDownHandler;
}

/**
 * Initializes popup code on load.	
 */
function initPopUpPB() {
	// Add the HTML to the body
	theBody = document.getElementsByTagName('BODY')[0];
	popmask = document.createElement('div');
	popmask.id = 'popupMask';
	popcont = document.createElement('div');
	popcont.id = 'popupContainer';
	popcont.innerHTML = '' +
		'<div id="popupInner">' +
			'<iframe src="" style="width:100%;height:100%;background-color:transparent;" scrolling="auto" frameborder="0" allowtransparency="true" id="popupFrame" name="popupFrame" width="100%" height="100%"></iframe>' +
		'</div>';
	theBody.appendChild(popmask);
	theBody.appendChild(popcont);
	
	gPopupMaskPB = document.getElementById("popupMask");
	gPopupContainerPB = document.getElementById("popupContainer");
	gPopFramePB = document.getElementById("popupFrame");	
	
	// check to see if this is IE version 6 or lower. hide select boxes if so
	// maybe they'll fix this in version 7?
	var brsVersion = parseInt(window.navigator.appVersion.charAt(0), 10);
	if (brsVersion <= 6 && window.navigator.userAgent.indexOf("MSIE") > -1) {
		gHideSelectsPB = true;
	}
	
	// Add onclick handlers to 'a' elements of class submodal or submodal-width-height
	var elms = document.getElementsByTagName('a');
	for (i = 0; i < elms.length; i++) {
		if (elms[i].className.indexOf("submodal") == 0) { 
			// var onclick = 'function (){showPopWin(\''+elms[i].href+'\','+width+', '+height+', null);return false;};';
			// elms[i].onclick = eval(onclick);
			elms[i].onclick = function(){
				// default width and height
				var width = 400;
				var height = 200;
				// Parse out optional width and height from className
				params = this.className.split('-');
				if (params.length == 3) {
					width = parseInt(params[1]);
					height = parseInt(params[2]);
				}
				showPopWin(this.href,width,height,null); return false;
			}
		}
	}
}
addEvent(window, "load", initPopUpPB);

 /**
	* @argument width - int in pixels
	* @argument height - int in pixels
	* @argument url - url to display
	* @argument returnFunc - function to call when returning true from the window.
	* @argument showCloseBox - show the close box - default true
	*/
function showProgressBar(url, width, height, returnFunc, showCloseBox) {
	gPopupIsShownPB = true;
	disableTabIndexes();
	gPopupMaskPB.style.display = "block";
	gPopupContainerPB.style.display = "block";
	// calculate where to place the window on screen
	centerPopWinPB(width, height);
	
	var titleBarHeight = 0; //parseInt(document.getElementById("popupTitleBar").offsetHeight, 10);


	gPopupContainerPB.style.width = width + "px";
	gPopupContainerPB.style.height = (height+titleBarHeight) + "px";
	
	setMaskSizePB();

  //image progressbar size = 227 x 18
	gPopFramePB.style.width = "230px"; 
	gPopFramePB.style.height = (height) + "px";
	
	// set the url
	gPopFramePB.src = url; 
	
	gReturnFuncPB = returnFunc;
	// for IE
	if (gHideSelectsPB == true) {
		hideSelectBoxes();
	}
	
}

//
var gi = 0;
function centerPopWinPB(width, height) {
	if (gPopupIsShownPB == true) {
		if (width == null || isNaN(width)) {
			width = gPopupContainerPB.offsetWidth;
		}
		if (height == null) {
			height = gPopupContainerPB.offsetHeight;
		}
		
		var theBody = document.getElementsByTagName("BODY")[0];
		var scTop = parseInt(getScrollTop(),10);
		var scLeft = parseInt(theBody.scrollLeft,10);
	
		setMaskSizePB();
		var titleBarHeight = 0;
		
		var fullHeight = getViewportHeight();
		var fullWidth = getViewportWidth();
		
		gPopupContainerPB.style.top = (scTop + ((fullHeight - (height+titleBarHeight)) / 2)) + "px";
		gPopupContainerPB.style.left =  (scLeft + ((fullWidth - width) / 2)) + "px";
	}
}
addEvent(window, "resize", centerPopWinPB);
addEvent(window, "scroll", centerPopWinPB);
window.onscroll = centerPopWinPB;

function setMaskSizePB() {
	var theBody = document.getElementsByTagName("BODY")[0];
			
	var fullHeight = getViewportHeight();
	var fullWidth = getViewportWidth();
	
	// Determine what's bigger, scrollHeight or fullHeight / width
	if (fullHeight > theBody.scrollHeight) {
		popHeight = fullHeight;
	} else {
		popHeight = theBody.scrollHeight;
	}
	
	if (fullWidth > theBody.scrollWidth) {
		popWidth = fullWidth;
	} else {
		popWidth = theBody.scrollWidth;
	}
	
	gPopupMaskPB.style.height = popHeight + "px";
	gPopupMaskPB.style.width = popWidth + "px";
}

// Tab key trap. iff popup is shown and key was [TAB], suppress it.
// @argument e - event - keyboard event that caused this function to be called.
function keyDownHandler(e) {
    if (gPopupIsShownPB && e.keyCode == 9)  return false;
}

// For IE.  Go through predefined tags and disable tabbing into them.
function disableTabIndexes() {
	if (document.all) {
		var i = 0;
		for (var j = 0; j < gTabbableTagsPB.length; j++) {
			var tagElements = document.getElementsByTagName(gTabbableTagsPB[j]);
			for (var k = 0 ; k < tagElements.length; k++) {
				gTabIndexesPB[i] = tagElements[k].tabIndex;
				tagElements[k].tabIndex="-1";
				i++;
			}
		}
	}
}

// For IE. Restore tab-indexes.
function restoreTabIndexes() {
	if (document.all) {
		var i = 0;
		for (var j = 0; j < gTabbableTagsPB.length; j++) {
			var tagElements = document.getElementsByTagName(gTabbableTagsPB[j]);
			for (var k = 0 ; k < tagElements.length; k++) {
				tagElements[k].tabIndex = gTabIndexesPB[i];
				tagElements[k].tabEnabled = true;
				i++;
			}
		}
	}
}

function hideSelectBoxes() {
  var x = document.getElementsByTagName("SELECT");

  for (i=0;x && i < x.length; i++) {
    x[i].style.visibility = "hidden";
  }
}

function displaySelectBoxes() {
  var x = document.getElementsByTagName("SELECT");

  for (i=0;x && i < x.length; i++){
    x[i].style.visibility = "visible";
  }
}

////////////////////////////////////////////////////////////////////////////////////////////
//common functions
function addEvent(obj, evType, fn){
 if (obj.addEventListener){
    obj.addEventListener(evType, fn, false);
    return true;
 } else if (obj.attachEvent){
    var r = obj.attachEvent("on"+evType, fn);
    return r;
 } else {
    return false;
 }
}
function removeEvent(obj, evType, fn, useCapture){
  if (obj.removeEventListener){
    obj.removeEventListener(evType, fn, useCapture);
    return true;
  } else if (obj.detachEvent){
    var r = obj.detachEvent("on"+evType, fn);
    return r;
  } else {
    alert("Handler could not be removed");
  }
}

/**
 * Code below taken from - http://www.evolt.org/article/document_body_doctype_switching_and_more/17/30655/
 *
 * Modified 4/22/04 to work with Opera/Moz (by webmaster at subimage dot com)
 *
 * Gets the full width/height because it's different for most browsers.
 */
function getViewportHeight() {
	if (window.innerHeight!=window.undefined) return window.innerHeight;
	if (document.compatMode=='CSS1Compat') return document.documentElement.clientHeight;
	if (document.body) return document.body.clientHeight; 

	return window.undefined; 
}
function getViewportWidth() {
	var offset = 17;
	var width = null;
	if (window.innerWidth!=window.undefined) return window.innerWidth; 
	if (document.compatMode=='CSS1Compat') return document.documentElement.clientWidth; 
	if (document.body) return document.body.clientWidth; 
}

/**
 * Gets the real scroll top
 */
function getScrollTop() {
	if (self.pageYOffset) // all except Explorer
	{
		return self.pageYOffset;
	}
	else if (document.documentElement && document.documentElement.scrollTop)
		// Explorer 6 Strict
	{
		return document.documentElement.scrollTop;
	}
	else if (document.body) // all other Explorers
	{
		return document.body.scrollTop;
	}
}
function getScrollLeft() {
	if (self.pageXOffset) // all except Explorer
	{
		return self.pageXOffset;
	}
	else if (document.documentElement && document.documentElement.scrollLeft)
		// Explorer 6 Strict
	{
		return document.documentElement.scrollLeft;
	}
	else if (document.body) // all other Explorers
	{
		return document.body.scrollLeft;
	}
}
