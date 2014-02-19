//---------------------------------------------------------------------------- 
//  Copyright (c) 2006, Humanized, Inc.
//  All rights reserved.
// 
//  This software is distributed under a Attribution-NonCommercial-ShareAlike 2.5
//  license. For more information, please see:
//
//      http://creativecommons.org/licenses/by-nc-sa/2.5/
//
//  For commercial use, please contact hello@humanized.com.
//
//---------------------------------------------------------------------------- 

//----------------------------------------------------------------------------
// 
//  TransparentMessage.js
//  Author: Aza Raskin <aza@humanized.com>
// 
//  This script depends on MochiKit (http://www.mochikit.org). To use it, just
//  include it in the head of your html page and whenever you want to use a 
//  transparent message, use the alert() function.
//
//  You'll want to include CSS descriptions for styling the transparent messages.
//  The following is an example CSS styling that can be used:
//    <style>
//      .humanized_msg{
//          position: absolute; left: 25%; width: 50%;
//          color: white; background-color: #8CC63F;
//          font-size: 36pt; text-align: center; 
//      }
//
//      .humanized_msg .round{
//          border-left: solid 2px white; border-right: solid 2px white;
//          font-size: 1px; height: 2px;
//       }
//
//      .humanized_msg p{ padding: .3em; display: inline;}
//    </style>
//
//----------------------------------------------------------------------------



//----------------------------------------------------------------------------
// Contants and Globals
//----------------------------------------------------------------------------

// How transparent the message is
var OPACITY = .9;

// The id and class of the message
var MSG = "humanized_msg"

// How far, in pixels, is the message from the top of the screen
var TOP_OFFSET = 100;

// The div of class round is for making slightly "rounded" corners.
var FORMAT = "<div class='round'></div><p>%s</p><div class='round'></div>"

// A global used for holding the current animation
var _msgFadeEffect = null;

// A global used for storing the content of the current message
var _currMsgContent = "";


//----------------------------------------------------------------------------
// Utility Functions
//----------------------------------------------------------------------------

function getScrollHeight(){
  /* Returns the y scroll height in all browsers. */
  var y;
  // all except Explorer
  if (self.pageYOffset)
    y = self.pageYOffset;
  else if (document.documentElement && document.documentElement.scrollTop)   
    y = document.documentElement.scrollTop;
  else if (document.body) // all other Explorers
    y = document.body.scrollTop;
  return parseInt(y);
}



//----------------------------------------------------------------------------
// Transparent Message Manipulation
//----------------------------------------------------------------------------

function getMessageOpacity(){
    /* Returns the current message opacity. */
    if (computedStyle( MSG, "display" ) == "none") return 0.0;
    return computedStyle( MSG, "opacity" );
}

function hideMessage(){
    /* Hides the message via fade animation. It won't attempt to hide a 
       message if it is currently being hidden. */
    if (getMessageOpacity() >= OPACITY )
        _msgFadeEffect = new MochiKit.Visual.fade( MSG, {duration: .5});
}

function _showMessage( message ){
  /* A private method which shows the transparent message. Doesn't handle
     concurrency. */
  $( MSG ).innerHTML = FORMAT.replace( "%s", message );
  _currMsgContent = message;
  showElement( MSG );

  var pos = elementPosition( MSG );
  pos.y = getScrollHeight() + TOP_OFFSET;

  setElementPosition( MSG, pos );
  setOpacity( MSG, OPACITY);
}

function showMessage( message ){
  /* Shows the message: handles the cases where a second message is attempted
     to be displayed before the first is done. */
  if (_msgFadeEffect != null && _msgFadeEffect.state != "finished"){
    // If the same message is displayed while the current message is being
    // faded away, it doesn't need to be shown again. This handles the case
    // where the user takes an action to dismiss a message and inadvertantly
    // causes the message to be shown again.
    if (_currMsgContent != message ){
      _msgFadeEffect.options.afterFinish = function(){ _showMessage(message) }
    }
  }
  else{
    /* This is needed for IE: otherwise after a click, a mousemove event would
       cause the message to fade away instantly. */
    MochiKit.Async.callLater( .05, _showMessage, message );
  }
}

function alert( message ){
  /* An alias for showMessage */
  showMessage( message );
}




//----------------------------------------------------------------------------
// Initialization
//----------------------------------------------------------------------------

function initTransparentMessage(){
    /* Adds the message div to the page and sets up its event handlers. */
    var msg = DIV( "" )
    msg.className = MSG;
    msg.id = MSG;

    var hideEvents = ["onmousedown", "onkeydown", "onmousemove"];
    for(i in hideEvents){
        MochiKit.Signal.connect( document.body, hideEvents[i], hideMessage );
    }

    hideElement( msg )
    document.body.appendChild( msg )
}

addLoadEvent( initTransparentMessage );
