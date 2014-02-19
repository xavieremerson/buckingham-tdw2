<!--
var menu_speed=8;
var Lyr0h = 93;
var Lyr1h = 63;
var moving;

function getObj(objName)
	{
	if(document.all)
		var tempObj = eval("document.all."+objName);
	else if(document.layers)
		var tempObj = eval("document.getElemendById["+objName+"]");
	return tempObj;
	}

function moveLayer(layerName, dir)
	{
	layerObj = getObj(layerName);

	var x_pos = parseInt(layerObj.currentStyle.top);
	if (dir == 'up')
		{
		var uBound = parseInt("-"+ eval(layerName+'h'));
		if(x_pos > uBound)
			{
			layerObj.style.top = x_pos - menu_speed;
			pullup = setTimeout("moveLayer('"+layerName+"','"+dir+"');",1);
			}
		}
	else if (dir == 'down')
		{
		var x_pos = parseInt(layerObj.currentStyle.top);
		if(x_pos < 0)
			{
			layerObj.style.top = x_pos + menu_speed;
			dropdown = setTimeout("moveLayer('"+layerName+"','"+dir+"');",1);
			}
		}
	if (window.moving)
		clearTimeout(moving);
	}
-->