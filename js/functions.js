/**
 * File to gloabal functions for project
 */

/**
 * Load, centred and show modal window
 */
function showPopWin(loadScript, width, heigth, xz) {
    $.get(loadScript, function(data) {
        $('#modal .modalContent').html(data)
        $('#modal .modalBox').css('width', width).css('heigth', heigth);
        $('#modal').removeClass('hide');
        $('#modal .modalTitle span').text($('#modal .modalContent title').text());
        var wH = $(document).height();
        var top = wH / 2 - heigth / 2;
        $('#modal').css('top', top);
        $('#modal').modal('show');
    });
}

/**
 * Close modal window
 */
function hidePopWin(){
    $('#modal').modal('hide');
}

/**
 *  Created new centred window with custom parameters
 */
function CreateWnd(loadScript, width, heigth, xz) {
    var wH = $(document).height();
    var wW = $(document).width();
    var left = wW / 2 - width / 2;
    var wH = $(document).height();
    var top = wH / 2 - heigth / 2;
    var params = "menubar=no,location=no,resizable=yes,scrollbars=no,status=no";
    params += ",width=" + width + ",height=" + heigth;
    params += ",top=" + top + ",left=" + left;
    var childWnd = window.open(loadScript, xz, params);

}

function showMessage(type, title, content, delay){
    if(!delay) delay = 4000;
    var cssRule = '';
    switch(type){
        case 'success':
            cssRule = 'alert-success';
            break;
        case 'warning':
            cssRule = 'alert-warning';
            break;
        case 'error':
            cssRule = 'alert-danger';
            break;
    }
    
    $('#messageBox').removeClass('alert-success alert-warning alert-danger').addClass(cssRule);
    $('#messageBox .Title').text(title);
    $('#messageBox .Content').text(content);
    $('#messageBox').show();
    setTimeout(function(){
        $('#messageBox').hide();
    }, delay);
    
}