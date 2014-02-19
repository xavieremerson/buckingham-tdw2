/******************************************************************************
* navbar.js                                                                   *
*                                                                             *
* Copyright 2000 by Mike Hall.                                                *
* Web address: http://www.brainjar.com                                        *
* Last update: August 29th, 2001.                                                *
*                                                                             *
* Creates interactive DHTML navigation bars with drop-down menus.             *
* Note: requires dhtmllib.js                                                  *
******************************************************************************/
var navBars = new Array();    // Used to track all navigation bars.

//*****************************************************************************
// NavBarMenuItem constructor.
//*****************************************************************************

function NavBarMenuItem(text, link) {

  this.text = text;    // Item text.
  this.link = link;    // Link URL or JavaScript code.
}

//*****************************************************************************
// NavBarMenu constructor.
//*****************************************************************************

function NavBarMenu(hdrWidth, menuWidth) {

  this.hdrWidth  = hdrWidth;
  this.width     = menuWidth;
  this.height    = 0;

  this.items = new Array();

  // Define methods.

  this.addItem = navBarMenuAddItem;
}

//*****************************************************************************
// NavBarMenu methods.
//*****************************************************************************

function navBarMenuAddItem(item) {

  this.items[this.items.length] = item;
}

//*****************************************************************************
// NavBar constructor.
//*****************************************************************************

function NavBar(width) {

  this.x = 0;
  this.y = 0;

  this.width  = width;
  this.height = 0;

  this.align    = "left";
  this.minWidth = 0;
  this.inverted = false;

  this.menus = new Array();
  this.created = false;

  // Set default sizes.

  this.border    = 2;
  this.padding   = 4;
  this.separator = 1;
  this.zindex = 10;

  // Set default colors.

  this.borderColor  = "#FFFFFF";
  this.hdrFgColor   = "#FFFFFF";
  this.hdrBgColor   = "#21427B";
  this.hdrHiFgColor = "#ffffff";
  this.hdrHiBgColor = "#666666";
  this.itmFgColor   = "#000000";
  this.itmBgColor   = "#cccccc";
  this.itmHiFgColor = "#ffffff";
  this.itmHiBgColor = "#000080";

  // Set default fonts.

  this.hdrFontFamily = "Arial,Helvetica,sans-serif";
  this.hdrFontStyle  = "plain";
  this.hdrFontWeight = "bold";
  this.hdrFontSize   = "10pt";
  this.itmFontFamily = "MS Sans Serif,Arial,Helvetica,sans-serif";
  this.itmFontStyle  = "plain";
  this.itmFontWeight = "bold";
  this.itmFontSize   = "8pt"; 

  // Define methods.

  this.setSizes    = navBarSetSizes;
  this.setColors   = navBarSetColors;
  this.setFonts    = navBarSetFonts;
  this.addMenu     = navBarAddMenu;
  this.create      = navBarCreate;
  this.hide        = navBarHide;
  this.show        = navBarShow;
  this.moveTo      = navBarMoveTo;
  this.moveBy      = navBarMoveBy;
  this.getzIndex   = navBarGetzIndex;
  this.setzIndex   = navBarSetzIndex;
  this.getWidth    = navBarGetWidth;
  this.getMinWidth = navBarGetMinWidth;
  this.getAlign    = navBarGetAlign;
  this.setAlign    = navBarSetAlign;
  this.resize      = navBarResize;
  this.invert      = navBarInvert;
  this.isInverted  = navBarIsInverted;

  // Add to the list.

  this.index = navBars.length;
  navBars[this.index] = this;
}

//*****************************************************************************
// NavBar methods.
//*****************************************************************************

function navBarSetSizes(border, padding, separator) {

  if (!this.created) {
    this.border = border;
    this.padding = padding;
    this.separator = separator;
  }
}

function navBarSetColors(bdColor,
                         hdrFgColor, hdrBgColor, hdrHiFgColor, hdrHiBgColor,
                         itmFgColor, itmBgColor, itmHiFgColor, itmHiBgColor) {

  if (!this.created) {
    this.borderColor  = bdColor;
    this.hdrFgColor   = hdrFgColor;
    this.hdrBgColor   = hdrBgColor;
    this.hdrHiFgColor = hdrHiFgColor;
    this.hdrHiBgColor = hdrHiBgColor;
    this.itmFgColor   = itmFgColor;
    this.itmBgColor   = itmBgColor;
    this.itmHiFgColor = itmHiFgColor;
    this.itmHiBgColor = itmHiBgColor;
  }
}

function navBarSetFonts(hdrFamily, hdrStyle, hdrWeight, hdrSize,
                        itmFamily, itmStyle, itmWeight, itmSize) {

  if (!this.created) {
    this.hdrFontFamily = hdrFamily;
    this.hdrFontStyle  = hdrStyle;
    this.hdrFontWeight = hdrWeight;
    this.hdrFontSize   = hdrSize;
    this.itmFontFamily = itmFamily;
    this.itmFontStyle  = itmStyle;
    this.itmFontWeight = itmWeight;
    this.itmFontSize   = itmSize;
  }
}

function navBarAddMenu(menu) {

  if (!this.created)
    this.menus[this.menus.length] = menu;
}

function navBarCreate() {

  var str;
  var i, j;
  var norm, high, end;
  var width, height;
  var x, y;
  var scrX, scrY;

  if (this.created || (!isMinNS4 && !isMinIE4))
    return;

  // Build HTML for filler and header layers.

  str = "";

  // For IE4, need to scroll to end of page before inserting HTML.

  if (isMinIE4 && !isMinIE5) {
    scrX = getPageScrollX();
    scrY = getPageScrollY();
    window.scrollTo(getPageWidth(), getPageHeight());
  }

  if (isMinNS4)
    str += '<layer name="navBar' + this.index + '_filler"></layer>\n'
        +  '<layer name="navBar' + this.index + '_hdrsBase">\n';
  if (isMinIE4)
    str += '<div id="navBar' + this.index + '_filler"'
        +  ' style="position:absolute;">'
        +  '</div>\n'
        +  '<div id="navBar' + this.index + '_hdrsBase"'
        +  ' style="position:absolute;">\n';

  // Build HTML for the headers.

  for (i = 0; i < this.menus.length; i++) {
    norm = '<table border=0 cellpadding=' + this.padding
         + ' cellspacing=0'
         + (this.menus[i].hdrWidth > 0 ? ' width=' + this.menus[i].hdrWidth : '')
         + ((isMinIE4 && !isMinIE5) ? ' id="navBar' + this.index + '_tbl' + i + '"': '')
         + '><tr><td'
         + (this.menus[i].hdrWidth == 0 ? ' nowrap=1' + this.menus[i].hdrWidth : '')
         + '>'
         + '<span style="color:' + this.hdrFgColor + ';'
         + 'font-family:' + this.hdrFontFamily + ';'
         + 'font-size:' + this.hdrFontSize + ';'
         + 'font-style:' + this.hdrFontStyle + ';'
         + 'font-weight:' + this.hdrFontWeight + ';">';
    high = '<table border=0 cellpadding=' + this.padding
         + ' cellspacing=0'
         + (this.menus[i].hdrWidth > 0 ? ' width=' + this.menus[i].hdrWidth : '')
         + '><tr><td'
         + (this.menus[i].hdrWidth == 0 ? ' nowrap=1' + this.menus[i].hdrWidth : '')
         + '>'
         + '<span style="color:' + this.hdrHiFgColor + ';'
         + 'font-family:' + this.hdrFontFamily + ';'
         + 'font-size:' + this.hdrFontSize + ';'
         + 'font-style:' + this.hdrFontStyle + ';'
         + 'font-weight:' + this.hdrFontWeight + ';">';
    end  = '</span></td></tr></table>';
    if (isMinNS4)
      str += '<layer name="navBar' + this.index + '_head' + i + '">'
          +  norm + this.menus[i].items[0].text + end
          +  '</layer>\n'
          +  '<layer name="navBar' + this.index + '_headHigh' + i + '">'
          +  high + this.menus[i].items[0].text + end
          +  '</layer>\n'
          +  '<layer name="navBar' + this.index + '_headDummy' + i + '">'
          +  '</layer>\n';
    if (isMinIE4)
      str += '<div id="navBar' + this.index + '_head' + i + '"'
          +  ' style="position:absolute;">'
          +  norm + this.menus[i].items[0].text + end
          +  '</div>\n'
          +  '<div id="navBar' + this.index + '_headHigh' + i + '"'
          +  ' style="position:absolute;">'
          +  high + this.menus[i].items[0].text + end
          +  '</div>\n'
          +  '<div id="navBar' + this.index + '_headDummy' + i + '"'
          +  ' style="position:absolute;">';
	    if(isMinIE6)str+='<table cellspacing=0 width="100%" height="100%"><tr><td>&nbsp;</td></tr></table>';
      str += '</div>\n';
  }

  if (isMinNS4) {
    str += '</layer>\n';
    this.baseLayer = new Layer(this.width);
    this.baseLayer.document.open();
    this.baseLayer.document.write(str);
    this.baseLayer.document.close();
  }
  if (isMinIE4) {
    str += '</div>\n';
    str = '<div id="navBar' + this.index + '"'
        + ' style="position:absolute;left:0px;top:0px;">\n'
        + str
        + '</div>\n';
    document.body.insertAdjacentHTML("beforeEnd", str);
    this.baseLayer = getLayer("navBar" + this.index);
  }

  // Position and initialize each header.

  width = 0;
  height = 0;

  for (i = 0; i < this.menus.length; i++) {
    this.menus[i].hdrNormLayer = getLayer('navBar' + this.index + '_head' + i);
    this.menus[i].hdrHighLayer = getLayer('navBar' + this.index + '_headHigh' + i);
    this.menus[i].hdrDmmyLayer = getLayer('navBar' + this.index + '_headDummy' + i);
    height = Math.max(height, getHeight(this.menus[i].hdrNormLayer));
    this.height = height + 2 * this.border;

    // Fix for IE4 to resize headers to fit text width.

    if (isMinIE4 && !isMinIE5) {
      width = this.menus[i].hdrWidth;
      if (width == 0)
        width = eval('document.all.navBar' + this.index + '_tbl' + i + '.clientWidth');
      navBarIEResizeLayer(this.menus[i].hdrNormLayer, width, height);
      navBarIEResizeLayer(this.menus[i].hdrHighLayer, width, height);
      navBarIEResizeLayer(this.menus[i].hdrDmmyLayer, width, height);
    }
  }

  x = this.border;
  y = this.border;
  for (i = 0; i < this.menus.length; i++) {

    width = Math.max(this.menus[i].hdrWidth, getWidth(this.menus[i].hdrNormLayer));
    if (this.menus[i].width == 0)
      this.menus[i].width = width + 2 * this.border;

    moveLayerTo(this.menus[i].hdrNormLayer, x, y);
    setBgColor(this.menus[i].hdrNormLayer, this.hdrBgColor);
    clipLayer(this.menus[i].hdrNormLayer, 0, 0, width, height);
    inheritLayer(this.menus[i].hdrNormLayer);

    moveLayerTo(this.menus[i].hdrHighLayer, x, y);
    setBgColor(this.menus[i].hdrHighLayer, this.hdrHiBgColor);
    clipLayer(this.menus[i].hdrHighLayer, 0, 0, width, height);
    hideLayer(this.menus[i].hdrHighLayer);

    moveLayerTo(this.menus[i].hdrDmmyLayer, x, y);
    if (isMinIE4)
      navBarIEResizeLayer(this.menus[i].hdrDmmyLayer, width, height);
    clipLayer(this.menus[i].hdrDmmyLayer, 0, 0, width, height);
    inheritLayer(this.menus[i].hdrDmmyLayer);
    this.menus[i].hdrDmmyLayer.highLayer = this.menus[i].hdrHighLayer;

    this.menus[i].hdrLeft = x;
    x += width + this.border;
    this.menus[i].hdrRight = x;
  }

  // Save resulting width of headers and total width.

  this.minWidth = x;
  this.width = Math.max(this.minWidth, this.width);

  // Position and initialize base, filler and headers base layers.

  moveLayerTo(this.baseLayer, this.x, this.y);
  setBgColor(this.baseLayer, this.borderColor);
  if (isMinIE4)
    navBarIEResizeLayer(this.baseLayer, this.width, this.height);
  clipLayer(this.baseLayer, 0, 0, this.width, this.height);

  this.fillerLayer = getLayer('navBar' + this.index + '_filler');
  moveLayerTo(this.fillerLayer, this.border, this.border);
  setBgColor(this.fillerLayer, this.hdrBgColor);
  width = this.width - 2 * this.border;
  height = this.height - 2 * this.border;
  if (isMinIE4)
    navBarIEResizeLayer(this.fillerLayer, width, height);
  clipLayer(this.fillerLayer, 0, 0, width, height);
  inheritLayer(this.fillerLayer);

  this.hdrsBaseLayer = getLayer('navBar' + this.index + '_hdrsBase');
  if (this.align == "left")
    this.hdrsOffsetX = 0;
  else if (this.align == "center")
    this.hdrsOffsetX = Math.round((this.width - this.minWidth) / 2);
  else if (this.align == "right")
    this.hdrsOffsetX = this.width - this.minWidth;
  else
    this.hdrsOffsetX = Math.min(parseInt(this.align, 10), this.width - this.minWidth);
  moveLayerTo(this.hdrsBaseLayer, this.hdrsOffsetX, 0);
  setBgColor(this.hdrsBaseLayer, this.borderColor);
  if (isMinIE4)
    navBarIEResizeLayer(this.hdrsBaseLayer, this.minWidth, this.height);
  clipLayer(this.hdrsBaseLayer, 0, 0, this.minWidth, this.height);
  inheritLayer(this.hdrsBaseLayer);

  // Set up event handling and positioning for headers.

  for (i = 0; i < this.menus.length; i++) {
    this.menus[i].hdrDmmyLayer.index = this.index;
    this.menus[i].hdrDmmyLayer.offsetX = this.menus[i].hdrLeft - this.border;
    if (this.menus[i].hdrDmmyLayer.offsetX + this.menus[i].width > this.width)
      this.menus[i].hdrDmmyLayer.offsetX = this.menus[i].hdrRight - this.menus[i].width;
    this.menus[i].hdrDmmyLayer.offsetY = this.height - this.border;
    this.menus[i].hdrDmmyLayer.onmouseover = navBarHeaderOn;
    this.menus[i].hdrDmmyLayer.onmouseout = navBarHeaderOff;
    if (isMinNS4) {
      this.menus[i].hdrDmmyLayer.document.highLayer = this.menus[i].hdrHighLayer;
      this.menus[i].hdrDmmyLayer.document.link = this.menus[i].items[0].link;
      this.menus[i].hdrDmmyLayer.document.captureEvents(Event.MOUSEUP);
      this.menus[i].hdrDmmyLayer.document.onmouseup = navBarItemClick;
    }
    if (isMinIE4) {
      this.menus[i].hdrDmmyLayer.highLayer = this.menus[i].hdrHighLayer;
      this.menus[i].hdrDmmyLayer.link = this.menus[i].items[0].link;
      this.menus[i].hdrDmmyLayer.onclick = navBarItemClick;
    }
  }

  // Build the drop down menus.

  norm = '<table border=0 cellpadding=' + this.padding
       + ' cellspacing=0 width="100%"><tr><td>'
       + '<span style="color:' + this.itmFgColor + ';'
       + 'font-family:' + this.itmFontFamily + ';'
       + 'font-size:' + this.itmFontSize + ';'
       + 'font-style:' + this.itmFontStyle + ';'
       + 'font-weight:' + this.itmFontWeight + ';">';
  high = '<table border=0 cellpadding=' + this.padding
       + ' cellspacing=0 width="100%"><tr><td>'
       + '<span style="color:' + this.itmHiFgColor + ';'
       + 'font-family:' + this.itmFontFamily + ';'
       + 'font-size:' + this.itmFontSize + ';'
       + 'font-style:' + this.itmFontStyle + ';'
       + 'font-weight:' + this.itmFontWeight + ';">';
  end  = '</span></td></tr></table>';
  for (i = 0; i < this.menus.length; i++) {
    width = this.menus[i].width - 2 * this.border;
    str = "";
    for (j = 1; j < this.menus[i].items.length; j++) {
      if (isMinNS4)
        str += '<layer name="navBar' + this.index + '_menu' + i + '_norm' + j + '"'
            +  ' width=' + width + '>'
            +  norm + this.menus[i].items[j].text + end
            +  '</layer>\n'
            +  '<layer name="navBar' + this.index + '_menu' + i + '_high' + j + '"'
            +  ' width=' + width + '>'
            +  high + this.menus[i].items[j].text + end
            +  '</layer>\n'
            +  '<layer name="navBar' + this.index + '_menu' + i + '_dmmy' + j + '"'
            +  ' width=' + width + '>'
            +  '</layer>\n';
      if (isMinIE4)
        str += '<div id="navBar' + this.index + '_menu' + i + '_norm' + j + '"'
            +  ' style="position:absolute;width:' + width + 'px;">'
            +  norm + this.menus[i].items[j].text + end
            +  '</div>\n'
            +  '<div id="navBar' + this.index + '_menu' + i + '_high' + j + '"'
            +  ' style="position:absolute;width:' + width + 'px;">'
            +  high + this.menus[i].items[j].text + end
            +  '</div>\n'
            +  '<div id="navBar' + this.index + '_menu' + i + '_dmmy' + j + '"'
            +  ' style="position:absolute;width:' + width + 'px;">';
		if(isMinIE6)str+='<table cellspacing=0 width="100%" height="100%"><tr><td>&nbsp;</td></tr></table>';
        str += '</div>\n';
    }
    if (isMinNS4) {
      this.menus[i].baseLayer = new Layer(this.menus[i].width);
      this.menus[i].baseLayer.document.open();
      this.menus[i].baseLayer.document.write(str);
      this.menus[i].baseLayer.document.close();
    }
    if (isMinIE4) {
      str = '<div id="navBar' + this.index + '_menu' + i + '"'
          + ' style="position:absolute;left:0px; top:0px;'
          + 'width:' + this.menus[i].width + 'px;visibility:hidden;">\n'
          + str
          + '</div>\n';
      document.body.insertAdjacentHTML("beforeEnd", str);
      this.menus[i].baseLayer = getLayer("navBar" + this.index + "_menu" + i);
    }
  }

  // Restore original scroll position in IE4.

  if (isMinIE4 && !isMinIE5)
    window.scrollTo(x, y);

  // Position and initialize each menu.

  for (i = 0; i < this.menus.length; i++) {
    moveLayerTo(this.menus[i].baseLayer, this.menus[i].hdrDmmyLayer.offsetX, this.menus[i].hdrDmmyLayer.offsetY);
    setBgColor(this.menus[i].baseLayer, this.borderColor);

    if (this.menus[i].items.length > 1) {
      this.menus[i].hdrDmmyLayer.menuLayer = this.menus[i].baseLayer;
      if (isMinNS4)
        this.menus[i].hdrDmmyLayer.document.menuLayer = this.menus[i].baseLayer;
    }
    else {
      this.menus[i].hdrDmmyLayer.menuLayer = null;
      if (isMinNS4)
        this.menus[i].hdrDmmyLayer.document.menuLayer = this.menus[i].baseLayer;
    }

    // Position and initialize each item in the menu.

    x = this.border;
    y = this.border;
    width = this.menus[i].width - 2 * this.border;
    for (j = 1; j < this.menus[i].items.length; j++) {

      this.menus[i].items[j].normLayer = getLayer('navBar' + this.index + '_menu' + i + '_norm' + j);
      this.menus[i].items[j].highLayer = getLayer('navBar' + this.index + '_menu' + i + '_high' + j);
      this.menus[i].items[j].dmmyLayer = getLayer('navBar' + this.index + '_menu' + i + '_dmmy' + j);

      height = getHeight(this.menus[i].items[j].normLayer);

      moveLayerTo(this.menus[i].items[j].normLayer, x, y);
      setBgColor(this.menus[i].items[j].normLayer, this.itmBgColor);
      clipLayer(this.menus[i].items[j].normLayer, 0, 0, width, height);
      inheritLayer(this.menus[i].items[j].normLayer);

      moveLayerTo(this.menus[i].items[j].highLayer, x, y);
      setBgColor(this.menus[i].items[j].highLayer, this.itmHiBgColor);
      clipLayer(this.menus[i].items[j].highLayer, 0, 0, width, height);
      hideLayer(this.menus[i].items[j].highLayer);

      moveLayerTo(this.menus[i].items[j].dmmyLayer, x, y);
      if (isMinIE4)
        navBarIEResizeLayer(this.menus[i].items[j].dmmyLayer, width, height);
      clipLayer(this.menus[i].items[j].dmmyLayer, 0, 0, width, height);
      inheritLayer(this.menus[i].items[j].dmmyLayer);
      this.menus[i].items[j].dmmyLayer.highLayer = this.menus[i].items[j].highLayer;
      this.menus[i].items[j].dmmyLayer.onmouseover = navBarItemOn;
      this.menus[i].items[j].dmmyLayer.onmouseout = navBarItemOff;

      if (isMinNS4) {
        this.menus[i].items[j].dmmyLayer.document.highLayer = this.menus[i].items[j].highLayer;
        this.menus[i].items[j].dmmyLayer.document.parentHighLayer = this.menus[i].hdrHighLayer;
        this.menus[i].items[j].dmmyLayer.document.menuLayer = this.menus[i].baseLayer;
        this.menus[i].items[j].dmmyLayer.document.link = this.menus[i].items[j].link;
        this.menus[i].items[j].dmmyLayer.document.captureEvents(Event.MOUSEUP);
        this.menus[i].items[j].dmmyLayer.document.onmouseup = navBarItemClick;
      }
      if (isMinIE4) {
        this.menus[i].items[j].dmmyLayer.highLayer = this.menus[i].items[j].highLayer;
        this.menus[i].items[j].dmmyLayer.parentHighLayer = this.menus[i].hdrHighLayer;
        this.menus[i].items[j].dmmyLayer.menuLayer = this.menus[i].baseLayer;
        this.menus[i].items[j].dmmyLayer.link = this.menus[i].items[j].link;
        this.menus[i].items[j].dmmyLayer.onclick = navBarItemClick;
      }

      y += height + this.separator;
    }

    width = this.menus[i].width;
    height = y - this.separator + this.border;
    this.menus[i].baseLayer.width = this.menus[i].width;
    this.menus[i].baseLayer.height = height;
    if (isMinIE4)
      navBarIEResizeLayer(this.menus[i].baseLayer, width, height);
    clipLayer(this.menus[i].baseLayer, 0, 0, width, height);

    this.menus[i].baseLayer.parentHighLayer = this.menus[i].hdrHighLayer;
    this.menus[i].baseLayer.onmouseout = navBarMenuOff;
  }

  this.created = true;
  this.resize(this.width);
  showLayer(this.baseLayer);
}

function navBarHide() {

  if (this.created)
    hideLayer(this.baseLayer);
}

function navBarShow() {

  if (this.created)
    showLayer(this.baseLayer);
}

function navBarMoveTo(x, y) {

  this.x = x;
  this.y = y;
  if (this.created)
    moveLayerTo(this.baseLayer, this.x, this.y);
}

function navBarMoveBy(dx, dy) {

  this.x += dx;
  this.y += dy;
  if (this.created)
    moveLayerTo(this.baseLayer, this.x, this.y);
}

function navBarGetzIndex() {

  if (this.created)
    return getzIndex(this.baseLayer);
  return 0;
}

function navBarSetzIndex(z) {
  var i;
  if (this.created) {
    setzIndex(this.baseLayer, z);
    for (i = 0; i < this.menus.length; i++)
      setzIndex(this.menus[i].baseLayer, z);
  }
}

function navBarGetWidth() {

  return this.width;
}

function navBarGetMinWidth() {

  return this.minWidth;
}

function navBarGetAlign() {

  return this.align;
}

function navBarSetAlign(align) {

  this.align = align;
  if (this.created)
    this.resize(this.width);
}

function navBarResize(width) {

  if (this.created) {
    this.width = Math.max(width, this.minWidth);
    if (isMinIE4) {
      navBarIEResizeLayer(this.fillerLayer, this.width - 2 * this.border, this.height - 2 * this.border);
      navBarIEResizeLayer(this.baseLayer, this.width, this.height);
    }
    clipLayer(this.fillerLayer, 0, 0, this.width - 2 * this.border, this.height - 2 * this.border);
    clipLayer(this.baseLayer, 0, 0, this.width, this.height);

    if (this.align == "left")
      this.hdrsOffsetX = 0;
    else if (this.align == "center")
      this.hdrsOffsetX = Math.round((this.width - this.minWidth) / 2);
    else if (this.align == "right")
      this.hdrsOffsetX = this.width - this.minWidth;
    else
      this.hdrsOffsetX = Math.min(parseInt(this.align, 10), this.width - this.minWidth);
    moveLayerTo(this.hdrsBaseLayer, this.hdrsOffsetX, 0);

    for (i = 0; i < this.menus.length; i++) {
      this.menus[i].hdrDmmyLayer.offsetX = this.menus[i].hdrLeft - this.border;
      if (this.hdrsOffsetX + this.menus[i].hdrDmmyLayer.offsetX + this.menus[i].width > this.width)
        this.menus[i].hdrDmmyLayer.offsetX = this.menus[i].hdrRight - this.menus[i].width;
    }
  }
  else
    this.width = width;
}

function navBarInvert() {

  this.inverted = !this.inverted;
}

function navBarIsInverted() {

  return this.inverted;
}

//*****************************************************************************
// Layer resize function for IE.
//*****************************************************************************

function navBarIEResizeLayer(layer, width, height) {

  layer.style.pixelWidth = width;
  layer.style.pixelHeight = height;
}

//*****************************************************************************
// Event handlers for the navigation bar.
//*****************************************************************************

function navBarHeaderOn(e) {

  var bar;
  var x, y;

  bar = navBars[this.index];

  // Position drop down menu.

  if (this.menuLayer != null) {
    x = bar.x + bar.hdrsOffsetX + this.offsetX;
    y = bar.y + this.offsetY;
    if (bar.inverted)
      y = bar.y - this.menuLayer.height + bar.border;
    moveLayerTo(this.menuLayer, x, y);

    // Save drop down menu position and show it.

    this.menuLayer.left = getPageLeft(this.menuLayer);
    this.menuLayer.top = getPageTop(this.menuLayer);
    this.menuLayer.right = this.menuLayer.left + this.menuLayer.width + 1;
    this.menuLayer.bottom = this.menuLayer.top + this.menuLayer.height + 1;
  }

  // Hide any currently active header and drop down.

  if (isMinIE4) {
    if (bar.activeHeader != null && bar.activeHeader != this) {
      hideLayer(bar.activeHeader.highLayer);
      if (bar.activeHeader.menuLayer != null)
        hideLayer(bar.activeHeader.menuLayer);
    }
    bar.activeHeader = this;
  }

  // Display the header highlight layer and drop down menu.

  showLayer(this.highLayer);
  if (this.menuLayer != null)
    showLayer(this.menuLayer);
}

function navBarHeaderOff(e) {

  // If over drop down menu, exit. Otherwise hide menu and highlight layers.

  if (this.menuLayer != null) {
    if (isMinIE4) {
      mouseX = window.event.clientX + document.body.scrollLeft;
      mouseY = window.event.clientY + document.body.scrollTop;
    }
    if (mouseX >= this.menuLayer.left  &&
        mouseX <= this.menuLayer.right &&
        mouseY >= this.menuLayer.top   &&
        mouseY <= this.menuLayer.bottom)
      return;
    hideLayer(this.menuLayer);
  }

  hideLayer(this.highLayer);
}

function navBarMenuOff(e) {

  // If over header, exit.

  if (isMinIE4) {
    mouseX = window.event.clientX + document.body.scrollLeft;
    mouseY = window.event.clientY + document.body.scrollTop;
    if (mouseX >= this.left  &&
        mouseX <= this.right &&
        mouseY >= this.top   &&
        mouseY <= this.bottom)
      return;
  }

  // Otherwise, hide menu and header highlight layers.

  hideLayer(this);
  hideLayer(this.parentHighLayer);
}

function navBarItemOn() {

  showLayer(this.highLayer);
}

function navBarItemOff() {

  hideLayer(this.highLayer);
}

function navBarItemClick(e) {

  // If there is no link, exit.

  if (this.link == "")
    return true;

  // Hide the drop down menu and highlight layer.

  if (this.menuLayer != null) {
    hideLayer(this.menuLayer);
  }
  if (this.parentHighLayer != null) {
    hideLayer(this.parentHighLayer);
  }
  hideLayer(this.highLayer);

  // If the link starts with "javascript:" execute the code. Otherwise just
  // link to the URL.

  if (this.link.indexOf("javascript:") == 0)
    eval(this.link);
  else
    window.location.href = this.link;

  return true;
}

//*****************************************************************************
// Code for tracking the mouse position.
//*****************************************************************************

// These variables will hold the current mouse pointer position.

var mouseX = 0;
var mouseY = 0;

// Set up event capturing.

if (isMinNS4)
  document.captureEvents(Event.MOUSEMOVE);
document.onmousemove = navBarGetMousePosition;

function navBarGetMousePosition(e) {

  // Save mouse pointer position.

  if (isMinNS4) {
    mouseX = e.pageX;
    mouseY = e.pageY;
  }
  if (isMinIE4) {
    mouseX = window.event.clientX + document.body.scrollLeft;
    mouseY = window.event.clientY + document.body.scrollTop;
  }
}

//*****************************************************************************
// Code to handle a window resize.
//*****************************************************************************

// These variables are used to determine if a resize event is a true one in
// older releases of NS4.

var origWidth;
var origHeight;

// Reload page in case of a browser resize.

if (isMinNS4) {
  origWidth  = window.innerWidth;
  origHeight = window.innerHeight;
}
window.onresize = navBarReload;

function navBarReload() {

  if (isMinNS4 && origWidth == window.innerWidth && origHeight == window.innerHeight)
    return;

  // For IE, reload on a timer in case the Windows 'Show window contents while
  // dragging' display option is on.

  if (isMinIE4)
    setTimeout('window.location.href = window.location.href', 2000);
  else
    window.location.href = window.location.href;
}
