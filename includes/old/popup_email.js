var popup = null;
function CreateWnd (file, width, height, resize)
{
	var doCenter = false;
	if((popup == null) || popup.closed)
	{
		attribs = "";
		if(resize) size = "yes"; else size = "no";
		for(var item in window)
			{ if(item == "screen") { doCenter = true; break; } }
		if(doCenter)
		{	/*/ center the window /*/
			if(screen.width <= width || screen.height <= height) size = "yes";
			WndTop  = (screen.height - height) / 2;
			WndLeft = (screen.width  - width)  / 2;
			attribs = "width=" + width + ",height=" + height + ",resizable=" + size + ",scrollbars=yes" + size + "," + 
			"status=no,titlebar=no,toolbar=no,directories=no,menubar=no,location=no,top=" + WndTop + ",left=" + WndLeft; 
		}
		else
		{
			if(navigator.appName=="Netscape" && navigator.javaEnabled())
			{	/*/ center the window /*/
				var toolkit = java.awt.Toolkit.getDefaultToolkit();
				var screen_size = toolkit.getScreenSize();
				if(screen_size.width <= width || screen_size.height <= height) size = "yes";

				WndTop  = (screen_size.height - height) / 2;
				WndLeft = (screen_size.width  - width)  / 2;

				attribs = "width=" + width + ",height=" + height + ",resizable=" + size + ",scrollbars=yes" + size + "," + 
				"status=no,titlebar=no,toolbar=no,directories=no,menubar=no,location=no,top=" + WndTop + ",left=" + WndLeft;
			}
			else
			{	/*/ use the default window position /*/
				size = "yes";
				attribs = "width=" + width + ",height=" + height + ",resizable=" + size + ",scrollbars=yes" + size + "," + 
				"status=no,toolbar=no,directories=no,menubar=no,location=no";
			}
		}

		popup = open(file, "", attribs);
	}
	else
	{
		DestroyWnd();
		CreateWnd(file, width, height, resize);
	}
}

function DestroyWnd ()
{
	if(popup != null)
	{
		popup.close();
		popup = null;
	}
}

