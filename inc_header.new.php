<?php
//BRG
ob_start();

session_start();
session_register('user');
session_register('pass');
session_register('userfullname');
session_register('user_id');
session_register('user_initials');
session_register('role');
session_register('user_email');
session_register('user_isadmin');
session_register('tval');
session_register('dval');
session_register('rr_num');
session_register('menufile');

session_register('privileges');
$menufile = 'inc_top_menu.php';
if ($user == '') {
    //Removed /tdw/ from the URI string, getting 404 errors.
    Header("Location: index.php?mod_requested=" . str_replace('/tdw/', '', $_SERVER["REQUEST_URI"]));
    exit;
}

//20110912 variable lost from session requiring login again.
if (!$menufile) {
    //Removed /tdw/ from the URI string, getting 404 errors.
    Header("Location: index.php?mod_requested=" . str_replace('/tdw/', '', $_SERVER["REQUEST_URI"]));
    exit;
}

include('includes/dbconnect.php');
include('includes/global.php');
include('includes/functions.php');

////
//Get user information for use within the application
//
// Currently implemented in login.php and registered as session variable.
// Have to include user privilege field later on and register that too.
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        if (DEBUG) {
            echo "<!--" . "Server: " . $_SERVER["SERVER_ADDR"] . "-->\n";
            echo "<!--" . "Client: " . $_SERVER["REMOTE_ADDR"] . "-->\n";
            echo "<!--" . "Administrator Email: " . $_SERVER["SERVER_ADMIN"] . "-->\n";
            echo "<!--" . "Page Process Time: " . date("D, m/d/Y h:i a") . "-->\n";
            echo "<!--" . "User ID: " . $user_id . "-->\n";
            echo "<!--" . "User Fullname: " . $userfullname . "-->\n";
            echo "<!--" . "pstr: " . $privileges . "-->\n";
            echo "<!--" . "dcar: " . checkpriv($privileges, "dcar") . "-->\n";
        }
        ?>
        <link rel="shortcut icon" href="favicon.ico"></link>
        <link rel="bookmark" href="favicon.ico"></link>
        <title><?= $_app_title ?>&nbsp;@&nbsp;BRG</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Load plugin scripts and css -->
        <link rel="stylesheet" type="text/css" href="css/plugin/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" href="css/plugin/bootstrap-theme.min.css" />
        <link rel="stylesheet" type="text/css" href="css/plugin/jquery.dataTables.css" />
        <link rel="stylesheet" type="text/css" href="css/plugin/jquery-ui-1.9.2.custom.min.css" />
        <script type="text/javascript" src="js/plugin/jquery-1.10.2.min.js"></script>
        <script type="text/javascript" src="js/plugin/jquery-ui-1.9.2.custom.min.js"></script>
        <script type="text/javascript" src='js/plugin/bootstrap.min.js'></script>
        <script type="text/javascript" src="js/plugin/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="js/plugin/JSCookMenu.js"></script>
        <script type="text/javascript" src="js/plugin/effect.js"></script>
        <!-- Load project css -->
        <link rel="stylesheet" type="text/css" href="css/styles.css" />
        <link rel="stylesheet" href="includes/menu/css/template_css.css" type="text/css" />
        <link rel="stylesheet" href="includes/menu/css/theme.css" type="text/css" />
        <!-- Load project scripts -->
        <script type="text/javascript" src="js/functions.js"></script>
        <script type="text/javascript" src="includes/menu/js/ThemeOffice/theme.js" type="text/javascript"></script>
        <script>
            function showPopWin(loadScript, width, heigth, xz) {
                $.get(loadScript, function(data) {
                    $('#modal .modalContent').html(data)
                    $('#modal .modalBox').css('width', width).css('heigth', heigth);
                    $('#modal').removeClass('hide');
                    $('#modal .modalTitle span').text($('#modal .modalContent title').text());
                    centredPopup(heigth);
                    $('#modal').modal('show');
                });
            }

            function centredPopup(heigth) {
                var wH = $(document).height();
                var top = wH / 2 - heigth / 2;
                $('#modal').css('top', top);
            }

            function CreateWnd(loadScript, width, heigth, xz) {
                var wH = $(document).height();
                var wW = $(document).width();
                var left = wW / 2 - width / 2;
                var wH = $(document).height();
                var top = wH / 2 - heigth / 2;
                var params = "menubar=no,location=no,resizable=yes,scrollbars=no,status=no"
                params += ",width=" + width + ",height=" + heigth;
                params += ",top=" + top + ",left=" + left;
                var childWnd = window.open(loadScript, xz, params);

            }
        </script>    
    </head>
    <body bgcolor="#F4F8FB" leftmargin="0" topmargin="0" rightmargin="0" bottommargin="0">
        <div id="modal" class="modal hide">
            <div class="modalBox">
                <div class="modalTitle"><span></span><img src="images/modal/close.gif" class="close" data-dismiss="modal" aria-hidden="true"></div>
                <div class="modalContent"></div>
            </div>
        </div>
        <!-- TOP LEVEL TABLE -->
        <table width="100%" height="100%" border="3" cellpadding="0" cellspacing="0" bordercolor="#333333" bordercolorlight="#999999" bordercolordark="#000000" bgcolor="#F4F8FB">
            <tr valign="top">
                <td height="20"> 
                    <table width="100%"  border="0" cellpadding="0" cellspacing="0" bgcolor="FFFFFF">
                        <tr> 
                            <td width="80"><img src="images/logow64h47.gif" ></td>
                            <td align="right" valign="top"> 
                                <table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                    <tr> 
                                        <td> 
                                            <table width="100%"  border="0" cellspacing="1" cellpadding="1">
                                                <tr> 
                                                    <td align="left" valign="top"><img src="images/client_appw290h47.gif" border="0"></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td valign="top">
                                <?php
                                if (strpos($_SERVER["HTTP_USER_AGENT"], 'MSIE') > 0) {
                                    $str_show_header = "";
                                } else {
                                    //$str_show_header = "<font color='red'>TDW is NOT approved for this browser.</font>";
                                }
                                ?>
                                <table width="100%" height="47">
                                    <tr>
                                      <td align="right" valign="top"><a class="links10top">User: <?= $userfullname ?><!--<br><?= $privileges ?>--><!--[Login expires on <?= $dval ?> at <?= $tval ?>]--> </a> [<a href="logout.php?logoutval=<?= $userfullname ?>" class="links10top">Logout</a>] </td>
                                    </tr>
                                    <tr>
                                        <td align="right" nowrap><a class="ghm"><?= $str_show_header ?></a></td><!-- _global_header_message-->
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <!-- Menu bar. -->
                    <?php
                    include($menufile);
                    //initiate page load time routine
                    $time = getmicrotime();
                    ?>
                    <!-- End Menu bar -->
                </td>
            </tr>
            <tr valign="top">
                <td valign="top">
                    <table width="100%" height="100%" border="0" cellpadding="3" cellspacing="0">
                        <tr>  
                            <td valign="top">
                                <div id="messageBox">
                                    <img src="images/modal/close.gif" class="close" data-dismiss="messageBox" aria-hidden="true">
                                    <div class="Title"></div>
                                    <div class="Content"></div>
                                </div>