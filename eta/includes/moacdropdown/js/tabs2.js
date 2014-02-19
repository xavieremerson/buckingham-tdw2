/*
*	mo's Tabs script
*	
*	mo@momche.net
*   please keep this disclaimer and send me a message if you intend to use the script
*/
cMoTabs = new Object()

cMoTabs.getTabGroup = function( hElement )
{
	return getParentByTagName( hElement, 'UL' )
}

cMoTabs.getTabElement = function( hElement )
{
	if( hElement == null )
	{
		return null
	}
	try
	{
		if( typeof hElement.tagName == 'undefined' ) return null
	}
	catch( hException )
	{
		return null
	}
	return getParentByProperty( hElement, 'className', 'tab' )
}


cMoTabs.setActiveTabElement = function( hTabElement )
{
	if( hTabElement == null )
	{
		return
	}
    hTabElement.className = 'tabactive'
    var hView = document.getElementById( hTabElement.getAttribute( 'tabview' ) )
    if( hView )
    {
        hView.style.display = 'block'
    }
}

cMoTabs.setInactiveTabElement = function( hTabElement )
{
	if( hTabElement == null )
	{
		return
	}
    hTabElement.className = 'tab'
    var hView = document.getElementById( hTabElement.getAttribute( 'tabview' ) )
    if( hView )
    {
        hView.style.display = 'none'
    }
}

cMoTabs.doTab = function( e )
{
	cDomEvent.init( e )
    var hTabElement = null
    
    if( e.type.indexOf( 'keypress' ) > -1 )
    {
        if( e.keyCode != 13 )
        {
            return
        }
    }

    hTabElement = cMoTabs.getTabElement( cDomEvent.target )

	if ( hTabElement != null )
	{
        //var hLink = getSubNodeByName( hTabElement, 'a' )
        //hLink.blur()
        var hGroup = cMoTabs.getTabGroup( hTabElement )
        if( hGroup.hAcvtiveElm == null )
        {
            var hActiveTab = getSubNodeByProperty( hTabElement.parentNode, 'className', 'tabactive' )
        }
        else
        {
            var hActiveTab = hGroup.hAcvtiveElm
        }
        if( hActiveTab == hTabElement )
        {
        }
        else
        {
	        cMoTabs.setInactiveTabElement( hActiveTab )
	        cMoTabs.setActiveTabElement( hTabElement )
	        hGroup.hAcvtiveElm = hTabElement
	    }
	}
	return true
}

cMoTabs.doTabClick = function( e )
{
	cDomEvent.init( e )
	if( e.preventDefault )
	{
		e.preventDefault()
	}
	e.cancelBubble = false
	e.returnValue = false
	return false
}

cMoTabs.init = function( hListItem )
{
	var hLink = getSubNodeByName( hListItem, 'a' )
	cDomEvent.addEvent( hLink, 'activate', cMoTabs.doTab, false )
	cDomEvent.addEvent( hLink, 'focus', cMoTabs.doTab, false )
	cDomEvent.addEvent( hLink, 'mousedown', cMoTabs.doTab, false )
	cDomEvent.addEvent( hLink, 'keypress', cMoTabs.doTab, false )	
	cDomEvent.addEvent( hLink, 'click', cMoTabs.doTabClick, false )
}
 

cDomExtensionManager.register( new cDomExtension( document, [ "li[tabview]" ], cMoTabs.init ) ) 
