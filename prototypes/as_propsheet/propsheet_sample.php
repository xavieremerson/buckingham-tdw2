<?php
/**********************************************************************
 propsheet_sample.php - as_propsheet using sample
 @author Alexander Selifonov, http://as-works.narod.ru ,
 @email as-works@narod.ru
 @last_modified : 2006-10-26
 **********************************************************************/
ini_set('display_errors',1);
ini_set('error_reporting',E_ALL);

# require_once('basefunc.php');
require_once('as_propsheet.php');

$self = $_SERVER['PHP_SELF'];

$pgtitle = 'Using as-propsheet sample';

?>
<HTML>
<STYLE TYPE="text/css">
body { font-family:arial,helvetica;font-size:13px; background-color: #FFFFFF; }
td       { font-family:arial,helvetica;font-size:12px;}
tr.odd   { background-color: #F0F0F8; color:#000000; }
tr.even  { background-color: #E0E0F0; color:#000000; }
tr.head  { background-color: #B0B0E0; color:#000000;
  text-align: center;
}
.ibox { border-color: #000000; background-color: #F8F8FF; color:#000000;
  border-color: #6060E0; border-style:solid; border-width:1px;
  FONT-size: 11px; FONT-FAMILY: Arial, Helvetica;font-weight: bold;
}
button, input.button{
   background-color: #C4C4FF;
   border-color: #202080; border-style:solid; border-width:1px;
   color: #000080; font-family: Arial, Verdana, Helvetica, sans-serif;
   font-size: 11px;
}
.pagebody {  background-color:#E0E0F0;
   border-right:2px solid #202080; border-bottom:2px solid #202080;
   border-left:2px solid #202080; border-top: none;
}
.pagebodywiz {  background-color:#E0E0F0;
   border-right:2px solid #202080; border-bottom:2px solid #202080;
   border-left:2px solid #202080; border-top: 2px solid #202080;
}

.tblactive {    font-weight:bold;
                background-color:#E0E0F0;
                color:blue; cursor:hand;
                border-left:2px solid #4040A0;
                border-right:2px solid #4040A0;
                border-top:2px solid #4040A0;
                border-bottom:none
}
.tblinactive {  font-weight:bold;
                background-color:#A0A0F0;
                cursor:hand; color:white;
                border-left:none;
                border-right:1px solid #8080F0;
                border-top:1px solid #8080F0;
                border-bottom:2px solid #4040A0;
}

</STYLE>

<BODY><H4 align='center'><?=$pgtitle?></H4>
<script name="javascript">
function TryMyTheme() {
  alert('Here You will place your javascript code, called by pressing button...');
}
function ChangeUdfVar(obj) {
   // Finish button disabled until user unputs some value into User-def var 1:
   if(document.getElementById('asprbt_finishcfg')!=undefined)
     document.getElementById('asprbt_finishcfg').disabled = (obj.value.length==0);
}
function NextPressed1() {
  // this will be called in WIZARD mode, on switching from page 1 to 2
  var fm = document.fsample;
  if(fm.title.value == '') { alert('Main site title cannot be empty !\nPlease enter something.'); return false; }
  return true;
}
function FinishWizard() {
  alert('Here must be finish code - \nfor instance, submitting all fields to the server \nthrough GET/POST or AJAX calls');
}
</script>
<div align=center>
<?
$style = isset($_GET['style'])?$_GET['style']:TABSTYLE;

if($style==TABSTYLE) echo "<a href='$self?style=1'>switch to WIZARDSTYLE sheet</a>";
else echo "<a href='$self?style=0'>switch to TABSTYLE  sheet</a>";
echo "</div><br>";

$sheet = new CPropertySheet('cfg',600,200,$style);

if($style==WIZARDSTYLE) {
  # I want 'finish' button in my Wizard, initially disabled:
  $sheet->SetFinishButton('FinishWizard()','Finish it!',false);
}
echo "<form action='$self' METHOD='POST' name='fsample'><input type='hidden' name='action' value='save'>";
# suppose $cfg[] is an array with all parameters to change

$fd = array();
$curvalue = isset($cfg['title'])? $cfg['title']: '';
$fd[] = new CFormField('title','text','Your site main title',$curvalue,0,100,300);

$curvalue = isset($cfg['siteurl'])? $cfg['siteurl']: 'http://www.MyNiceSyte.net';
$fd[] = new CFormField('siteurl','text','URL for your site',$curvalue,0,100,300);

$curvalue = isset($cfg['shownews'])? $cfg['shownews']: 0;
$fd[] = new CFormField('shownews','checkbox','Show news on main page',$curvalue);

$curvalue = isset($cfg['theme_no'])? $cfg['theme_no']: 0;
$themelist = array('0'=>'default theme', '2'=>'Futuristic','3'=>'Las Vegas');
$fd[] = new CFormField('theme','select','Use this theme for the site',$curvalue,$themelist,0,200);

$fd[] = new CFormField('checktheme','button','Check chosen theme...',0,0,0,200,'Try chosen theme for the site','TryMyTheme()');
$sheet->AddPage('Site basic parameters',$fd, 'NextPressed1()');

# page 2
$fd = array();
$fd[] = new CFormField('h','head','Feedback parameters');
$fd[] = new CFormField('feedback_email','text','Email to send feedback','feedback@MyNiceSite.net',0,200,400);

// make 'SELECT box':
$fmtlist = array('TXT'=>'text','HTML'=>'HTML format');
$fd[] = new CFormField('fb_format','select','Letters format','TXT',$fmtlist,0,200);

$fd[] = new CFormField('fb_save_on_server','checkbox','Save all feedback copies on server',1);

$fd[] = new CFormField('h','head','Maintenance'); // header for next params
$fd[] = new CFormField('daily_rotate','checkbox','daily rotate logs',1);
$fd[] = new CFormField('daily_checktables','checkbox','daily check and optimize all tables in DB',1);

$sheet->AddPage('Feedback, maintenance',$fd);

# here we demonstrate how to add nested CPropertySheet:
$sheet2 = new CPropertySheet('n2','90%',100);
$flds = array();
$flds[] = new CFormField('f11','text','sub-field 1','initial value',0,100,200);
$flds[] = new CFormField('f12','checkbox','check box 12',0);
$sheet2->AddPage('sub-params-1',$flds);
$flds = array();
$flds[] = new CFormField('f21','head','Here is a second page with TEXTAREA field!');
$flds[] = new CFormField('f22','textarea','Your comments','init text',0,0,'','','',"style='width:100%; height:100'");
$sheet2->AddPage('sub-params-2',$flds);
$sheet->AddPage('nested sheet',$sheet2);
#don't call Draw for tthe shet2, main page will do it !

# and finally - add page with user-drawn form elements...
$sheet->AddPage('User defined parameters','UdfvarPage'); // this page will be drawn in my function


$sheet->Draw(0);
if($style==0) {
?>
<br><br><div align=center><button class='button' type='submit' name='sub' style='width:200'>Save parameters</button></FORM></div>
</BODY></HTML>
<? }
exit;

function UdfvarPage() { // this func draws a whole page in a CPropertySheet's tab
?>
<table width='100%'><tr>
 <td align=right><b>User def.Parameter 1<br>Input some non-empty string to enable Finish button</b></td></td>
 <td><input type='text' name='udfparm1' class='ibox' style='width:200' onChange='ChangeUdfVar(this)'></td></tr>
 <td align=right><b>User def.Parameter 2</b></td></td>
 <td><input type='text' name='udfparm2' class='ibox' style='width:200'></td>
</tr>
</table><div align=center><hr><b>And here we place nested CPopertyPage:</b></div><br><br>
<?
 $sheet2 = new CPropertySheet('nested',400,100);
 $flds= array();
 $flds[] = new CFormField('fldnest11','text','sub-field 1','initial value',0,100,200);
 $flds[] = new CFormField('fldnest12','checkbox','check box 12',0);
 $sheet2->AddPage('sub-params-01',$flds);
 $flds = array();
 $flds[] = new CFormField('fldnest21','text','sub-field 21','',0,100,200);
 $flds[] = new CFormField('fldnest22','checkbox','check box 22',0);
 $sheet2->AddPage('sub-params-02',$flds);
 $sheet2->Draw();
}
?>