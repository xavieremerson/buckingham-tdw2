

///////////////////////////////////////////////////
// configurable constants, modify as needed!
var sort_col_title = "Click here to Sort!";
var sort_col_class = "colsort"; // whichever class you want the heading to be
var sort_col_style = "text-decoration:none; font-weight:bold; color:black"; // whichever style you want the link to look like
var sort_col_mouseover = "this.style.color='red'"; // what style the link should use onmouseover?
var sort_col_mouseout = "this.style.color='black'"; // what style the link should use onmouseover?

//////////////////////////////////////////////////
// speed related constants, modify as needed!
var table_content_might_change = false; // table content could be changed by other JS on-the-fly? if so, some speed improvements cannot be used.
var preserve_style = ""; // (row, cell) preserves style for row or cell e.g., row is useful when the table highlights rows alternatively. cell is much slower while no preservation's the fastest by far!
var tell_me_time_consumption = false; // give stats about time consumed during sorting and table update/redrawing.

//////////////////////////////////////////////////////////
// anything below this line, modify at your own risk! ;)
var SORT_COLUMN_INDEX, LAST_SORTED_TABLE, CUSTOM_CODE, REPLACE_PATTERN, 
    DATE_ORDER_ARRAY;
var agt=navigator.userAgent.toLowerCase();    
var is_ie=((agt.indexOf("msie") != -1) && (agt.indexOf("opera") == -1));

addEvent(window, "load", sortables_init);

function sortables_init() {
    // Find all tables with class sortable and make them sortable
    if (!document.getElementsByTagName) return;
    tbls = document.getElementsByTagName("table");
    for (ti=0;ti<tbls.length;ti++) {
        thisTbl = tbls[ti];
        if ((' '+thisTbl.className+' ').indexOf("sortable") != -1) {
            ts_makeSortable(thisTbl);
        }
    }
}

function ts_makeSortable(table) {
    if (table.rows && table.rows.length > 0) {
        var firstRow = table.rows[0];
    }
    if (!firstRow) return;

    var sortCell;
    // We have a first row: assume it's the header (it works for <thead> too), 
    // and make its contents clickable links
    for (var i=0;i<firstRow.cells.length;i++) {
        var cell = firstRow.cells[i];
        var txt = ts_getInnerText(cell);
        if(cell.getAttribute("sortdir")) sortCell = cell;
        cell.innerHTML = '<a style="'+sort_col_style+'" onMouseOver="'+sort_col_mouseover+'" onMouseOut="'+sort_col_mouseout+'" class="'+sort_col_class+'" href="#" title="'+sort_col_title+'" onclick="ts_resortTable(this.parentNode);return false;">'+txt+'</a>';
    }
    if(sortCell) ts_resortTable(sortCell);
}

function ts_getInnerText(el) {
	if (typeof el == "string") return el;
	if (typeof el == "undefined") { return el };
	if (el.innerText) return el.innerText;	//Not needed but it is faster
	var str = "";
	
	var cs = el.childNodes;
	var l = cs.length;
	for (var i = 0; i < l; i++) {
		switch (cs[i].nodeType) {
			case 1: //ELEMENT_NODE
				str += ts_getInnerText(cs[i]);
				break;
			case 3:	//TEXT_NODE
				str += cs[i].nodeValue;
				break;
		}
	}
	return str;
}

function ts_resortTable(td) 
{
  var column = td.cellIndex;
  var table = getParent(td,'TABLE');

  var firstRow = new Array();
  var newRows = new Array();
  for (var i=0;i<table.rows[0].length;i++) firstRow[i] = table.rows[0][i]; 
  var headcount = 1;
  for (var i=0,j=1;j<table.rows.length;j++)
  {
    var t = table.rows[j].parentNode.tagName.toLowerCase();
    if(t == 'tbody') newRows[i++] = table.rows[j];
    else if(t == 'thead') headcount++;
  }
  var time2 = new Date();

  // check if we really need to sort
  if(td.getAttribute("sortdir") && td.getAttribute("sortdir").match(/^ts_/) 
     && !table_content_might_change && table == LAST_SORTED_TABLE && column 
     == SORT_COLUMN_INDEX)
    newRows.reverse();
  else
  {
    // Work out a type for the column
    var sortfn, type = td.getAttribute("ts_type");
    REPLACE_PATTERN = '';
    if(!type)
    {
      if (table.rows.length <= 1) return;
      var itm = ts_getInnerText(table.rows[headcount].cells[column]);
      sortfn = ts_sort_caseinsensitive;
      
      if (!isNaN(Date.parse(itm))) sortfn = ts_sort_date;
      else if (itm.match(/^[¥£€$]/)) sortfn = ts_sort_currency;
      else if (itm.match(/^\d{1,3}(\.\d{1,3}){3}$/)) sortfn = ts_sort_ip;
      else if (itm.match(/^[+-]?\s*[0-9]+(?:\.[0-9]+)?(?:\s*[eE]\s*[+-]?\s*\d+)?$/))
        sortfn = ts_sort_numeric;
    }
    else if(type == 'date') sortfn = ts_sort_date;
    else if(type == 'euro_date') 
    { ts_set_date_array('D/M/Y'); sortfn = ts_sort_date; }
    else if(type == 'other_date') 
    { 
      ts_set_date_array(td.getAttribute("ts_date_format")); 
      sortfn = ts_sort_date; 
    }
    else if(type == 'number') sortfn = ts_sort_numeric;
    else if(type == 'ip') sortfn = ts_sort_ip;
    else if(type == 'money') sortfn = ts_sort_currency;
//       else if(type == 'custom') sortfn = function(aa,bb) { a = ts_getInnerText(aa.cells[SORT_COLUMN_INDEX]); b = ts_getInnerText(bb.cells[SORT_COLUMN_INDEX]); eval(td.getAttribute("ts_sortfn")) }; // the coding here is shorter but interestingly it's also slower
    else if(type == 'custom') { CUSTOM_CODE = td.getAttribute("ts_sortfn"); sortfn = ts_custom_sortfn }
    else { alert("unsupported sorting type!"); return; }

    SORT_COLUMN_INDEX = column;
    newRows.sort(sortfn);
    if (td.getAttribute("sortdir") == 'desc' || td.getAttribute("sortdir") == 'ts_desc')
      newRows.reverse();
  }
  if (td.getAttribute("sortdir") == 'desc' || td.getAttribute("sortdir") == 'ts_desc')
    td.setAttribute('sortdir','ts_asc');
  else td.setAttribute('sortdir','ts_desc');

  LAST_SORTED_TABLE = table;
     
  var time3 = new Date();
  
  var ps = table.getAttribute("preserve_style") || preserve_style;
  if(ps == 'row' && !is_ie) 
  {
    var tmp = new Array(newRows.length);
    for (var i = 0; i < newRows.length; i++) tmp[i] = newRows[i].innerHTML;
    for (var i = 0; i < newRows.length; i++) table.rows[i+headcount].innerHTML = tmp[i];
  }
  else if(ps == 'cell' || (ps == 'row' && is_ie)) 
  {
    var tmp = new Array(newRows.length);
    for (var i = 0; i < newRows.length; i++)
      for (var j = 0; j < newRows[i].cells.length; j++)
      {
        if(!tmp[i]) tmp[i] = new Array(newRows[i].cells.length);
        tmp[i][j] = newRows[i].cells[j].innerHTML;
      }
    for (var i = 0; i < newRows.length; i++)
      for (var j = 0; j < newRows[i].cells.length; j++)
        table.rows[i+headcount].cells[j].innerHTML = tmp[i][j];
  }
  else
  {
    for (var i=0;i<newRows.length;i++) // We appendChild rows that already exist to the tbody, so it moves them rather than creating new ones
      table.tBodies[0].appendChild(newRows[i]);
  } 
  var time4 = new Date();
  if(tell_me_time_consumption)
  {
    alert('it took ' + diff(time3, time2) + ' seconds to do sorting!');
    alert('it took ' + diff(time4, time3) + ' seconds to do redrawing!');
  }
}

function diff(time2, time1)
{
  return (time2.getTime() - time1.getTime())/1000;
}

function getParent(el, pTagName) {
	if (el == null) return null;
	else if (el.nodeType == 1 && el.tagName.toLowerCase() == pTagName.toLowerCase())	// Gecko bug, supposed to be uppercase
		return el;
	else
		return getParent(el.parentNode, pTagName);
}

// Mingyi Note: it seems ridiculous to do so much processing for
// customizable date conversion, should try to find a zbetter way
// of doing it.
function ts_set_date_array(f)
{
  var tmp = [['D', f.indexOf('D')], ['M', f.indexOf('M')], ['Y', f.indexOf('Y')]];
  tmp.sort(function(a,b){ return a[1] - b[1]});
  DATE_ORDER_ARRAY = new Array(3);
  for(var i = 0; i < 3; i++) DATE_ORDER_ARRAY[tmp[i][0]] = '$' + (i + 2);
  REPLACE_PATTERN = f.replace(/[DMY]([^DMY]+)[DMY]([^DMY]+)[DMY]/, '^(.*?)(\\d+)\\$1(\\d+)\\$2(\\d+)(.*)$');
}

function ts_process_year(y)
{
  var tmp = parseInt(y);
  if(tmp < 32) return '20' + y; 
	else if(tmp < 100) return '19' + y;
	else return y;
}

// convert to MM/DD/YYYY (or M/D/YYYY) format
function ts_convert_date(a)
{
  var aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]);
  var re = 'RegExp.$1+RegExp.'+DATE_ORDER_ARRAY['M']+'+\'/\'+RegExp.'+DATE_ORDER_ARRAY['D']+'+\'/\'+ts_process_year(RegExp.'+DATE_ORDER_ARRAY['Y']+')+RegExp.$5';
  var code = 'if(aa.match(/'+REPLACE_PATTERN+'/)) (' + re + ')';
  return Date.parse(eval(code));
}

function ts_sort_date(a,b) {
    if(REPLACE_PATTERN == '')
    {
      aa = Date.parse(ts_getInnerText(a.cells[SORT_COLUMN_INDEX]));
      bb = Date.parse(ts_getInnerText(b.cells[SORT_COLUMN_INDEX]));
    }
    else
    {
      aa = ts_convert_date(a);
      bb = ts_convert_date(b);
    }
    if(isNaN(aa)) aa = 0;
    if(isNaN(bb)) bb = 0;
    return aa - bb;
}

// assume no scientific number in currency (if assumption incorrect, just use
// same code for ts_sort_numeric will do)
function ts_sort_currency(a,b) 
{ 
    return ts_sort_num(ts_getInnerText(a.cells[SORT_COLUMN_INDEX]).replace(/[^-0-9.+]/g,''),
                       ts_getInnerText(b.cells[SORT_COLUMN_INDEX]).replace(/[^-0-9.+]/g,''));
}

// let's allow scientific notation but also be strict on number format
function ts_sort_num(a, b)
{
    if(!isNaN(a)) aa = a;
    else if(a && a.match(/^[^0-9.+-]*([+-]?\s*[0-9]+(?:\.[0-9]+)?(?:\s*[eE]\s*[+-]?\s*\d+)?)/))
      aa = parseFloat(RegExp.$1.replace(/\s+/g, ''));
    else aa = 0;
    if(!isNaN(b)) bb = b;
    else if(b && b.match(/^[^0-9.+-]*([+-]?\s*[0-9]+(?:\.[0-9]+)?(?:\s*[eE]\s*[+-]?\s*\d+)?)/))
      bb = parseFloat(RegExp.$1.replace(/\s+/g, ''));
    else bb = 0;
    return aa - bb;
}

function ts_sort_numeric(a,b) 
{
    return ts_sort_num(ts_getInnerText(a.cells[SORT_COLUMN_INDEX]),
                       ts_getInnerText(b.cells[SORT_COLUMN_INDEX])); 
}

function ts_sort_ip(a,b) {
    aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]).split('.');
    bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]).split('.');
    return ts_sort_num(aa[0], bb[0]) || ts_sort_num(aa[1], bb[1]) || 
           ts_sort_num(aa[2], bb[2]) || ts_sort_num(aa[3], bb[3]);
}
 
function ts_sort_caseinsensitive(a,b) {
    aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]).toLowerCase();
    bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]).toLowerCase();
    if (aa==bb) return 0;
    if (aa<bb) return -1;
    return 1;
}

function ts_custom_sortfn(aa,bb)
{
  a = ts_getInnerText(aa.cells[SORT_COLUMN_INDEX]);
  b = ts_getInnerText(bb.cells[SORT_COLUMN_INDEX]);
  return eval(CUSTOM_CODE);
};

function addEvent(elm, evType, fn, useCapture)
// addEvent and removeEvent
// cross-browser event handling for IE5+,  NS6 and Mozilla
// By Scott Andrew
{
  if (elm.addEventListener){
    elm.addEventListener(evType, fn, useCapture);
    return true;
  } else if (elm.attachEvent){
    var r = elm.attachEvent("on"+evType, fn);
    return r;
  } else {
    alert("Handler could not be removed");
  }
} 
