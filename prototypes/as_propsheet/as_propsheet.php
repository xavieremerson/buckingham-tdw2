<?
/*================================================================================
 as_propsheet.php - PHP wrapper class for creating "Property Sheet"-like HTML pages
 Tabs-style (default) and Wizard-style supported
 @author Alexander Selifonov,
 @url    http://as-works.narod.ru/en/php/
 @email  as-works@narod.ru
 @Copyright Alexander Selifonov, 2006
 @version 1.01.001
 @updated 2006-10-26
 =================================================================================
*/
define('TABSTYLE',0);
define('WIZARDSTYLE',1);
# css class names that will be used in rendered HTML code
if(!isset($as_cssclass)) $as_cssclass = array();
if(empty($as_cssclass['textfield'])) $as_cssclass['textfield'] = 'ibox';
if(empty($as_cssclass['button'])) $as_cssclass['button'] = 'button';
if(empty($as_cssclass['tactive'])) $as_cssclass['tactive'] = 'tblactive';
if(empty($as_cssclass['tinactive'])) $as_cssclass['tinactive'] = 'tblinactive';
if(empty($as_cssclass['pagebody'])) $as_cssclass['pagebody'] = 'pagebody';
if(empty($as_cssclass['pagebodywiz'])) $as_cssclass['pagebodywiz'] = 'pagebodywiz';
if(empty($as_cssclass['row_header'])) $as_cssclass['row_header'] = 'head';

# localized interface strings:
if(empty($as_iface['btn_prev'])) $as_iface['btn_prev'] ='Previous';
if(empty($as_iface['btn_next'])) $as_iface['btn_next'] ='Next';

class CPropertyPage {
  var $tabtitle = '';
  var $drawobject = '';
  var $func_evtnext = '';
  function CPropertyPage($title,$drawfunc,$evtnext='') {
     $this->tabtitle = $title;
     $this->drawobject = $drawfunc;
     $this->func_evtnext = $evtnext;
  }
}

class CFormField {
  var $fname = '';
  var $ftype = 'text';
  var $prompt = '';
  var $initvalue = '';
  var $options = '';
  var $maxlength = 0;
  var $width = 0;
  var $title = '';
  var $addparams = '';
  var $onchange = '';
  function CFormField($name,$type='text',$prompt='',$initvalue='',$vlist='',
            $maxlength=10,$width=0,$title='',$onchange='',$addprm='') {
    $this->fname = $name;
    $this->ftype = strtolower($type);
    $this->prompt = $prompt;
    $this->initvalue = $initvalue;
    $this->options = $vlist;
    $this->maxlength = $maxlength;
    $this->width = $width;
    $this->title = $title;
    $this->onchange = $onchange;
    $this->addparams = $addprm;
  }
}

class CPropertySheet {
  var $sheetid = ''; // unique Sheet id, used as prefix for all pages id's
  var $style = TABSTYLE;
  var $width = 800;
  var $height= 120;
  var $finishfunc = '';
  var $finish_caption = 'Finish';
  var $finish_enabled = false;
  var $pages = array();
  function CPropertySheet($id, $width='',$height='',$sheetstyle=TABSTYLE) {
    $this->sheetid = $id;
    if($width!=='') $this->width = $width;
    if($height!=='') $this->height = $height;
    $this->style = $sheetstyle;
  }
  function AddPage($tabtitle,$drawfunc, $jsfunc_next='') {
    $this->pages[] = new CPropertyPage($tabtitle,$drawfunc,$jsfunc_next);
  }
  function SetFinishButton($function,$caption='',$enabled=false) {
    $this->finishfunc = $function;
    if($caption!='') $this->finish_caption = $caption;
    $this->finish_enabled = $enabled;
  }
  function Draw($startpage=0) {
    global $as_cssclass,$as_iface,$psheet_jsdone;
    if(count($this->pages)<1) return false;
    $twidth = intval(100/count($this->pages));
?>
<SCRIPT language="javascript">
var prop_events<?=$this->sheetid?> = [];
var prop_curpage<?=$this->sheetid?> = <?=$startpage?>;
function AsPropSheetSwitchPage<?=$this->sheetid?>(shid,no) {
<? if($this->style==WIZARDSTYLE) { ?>
  var curpg = prop_curpage<?=$this->sheetid?>;
  var result = 1;
  if(no=='+1' && prop_events<?=$this->sheetid?>[curpg]!=undefined)
    try { result = eval(prop_events<?=$this->sheetid?>[curpg]); }
    catch(e) {alert("exception. Error name: "+e.name+". Error message: "+e.message);  }
  if(!result) return; // wrong parameters, don't leave a page until he fix it !
  if(no=='-1') {no = prop_curpage<?=$this->sheetid?> -=1; prop_curpage<?=$this->sheetid?>=no; }
  else if(no=='+1') { no = prop_curpage<?=$this->sheetid?>+=1; prop_curpage<?=$this->sheetid?>=no; }

<? } ?>
  for(k=0;k< <?=count($this->pages)?>;k++) {
<? if($this->style==TABSTYLE) { ?>
    window.document.getElementById(shid+k).className=(k==no)?'<?=$as_cssclass['tactive']?>':'<?=$as_cssclass['tinactive']?>';
<? } ?>
    var vpage = window.document.getElementById('as_page'+shid+k);
    vpage.style.display = (k==no)?'block':'none';
  }
<? if($this->style==WIZARDSTYLE) { ?>
  if(document.getElementById('asprbt_prev'+shid) != undefined) {
     document.getElementById('asprbt_prev'+shid).disabled = (prop_curpage<?=$this->sheetid?>==0);
     document.getElementById('asprbt_next'+shid).disabled=(prop_curpage<?=$this->sheetid?>+1 >=<?=count($this->pages)?>);

  }
<? } ?>
  return false;
}
</SCRIPT>
<div align='center'>
<?
    if($this->style==TABSTYLE) {
      echo "<table border='0' width='{$this->width}' cellpadding=0 cellspacing=0><tr>";
      for($kk=0;$kk<count($this->pages);$kk++) {
        $cls = ($kk==$startpage)? $as_cssclass['tactive']:$as_cssclass['tinactive'];
        $prompt = $this->pages[$kk]->tabtitle;
        echo "<td width='{$twidth}%' align=center id='{$this->sheetid}{$kk}' onClick=\"AsPropSheetSwitchPage{$this->sheetid}('{$this->sheetid}',$kk)\" class='$cls'>$prompt</td>\n";
      }
      echo "</tr></table>";
    }
    // now draw main area - FORM pages
    $cls = ($this->style==TABSTYLE)? $as_cssclass['pagebody']:$as_cssclass['pagebodywiz'];
    echo "<table width='{$this->width}' border='0'  height='{$this->height}' class='$cls'><tr>\n";
    for($kk=0;$kk<count($this->pages);$kk++) {
       $displ = ($kk==$startpage)? '':"style='display:none'";
       echo "<TD id='as_page{$this->sheetid}{$kk}' {$displ}>\n";
       if($this->style==WIZARDSTYLE) {
         echo "<h3 align='center'>{$this->pages[$kk]->tabtitle}</h3>\n";
       }
       $lowclass = strtolower(get_class($this->pages[$kk]->drawobject));
       if($lowclass=='cpropertysheet') { echo "<br>"; $this->pages[$kk]->drawobject->Draw(); echo "<br>"; }
       elseif(is_array($this->pages[$kk]->drawobject)) {
         $this->DrawFormPage($this->pages[$kk]->drawobject);
       }
       elseif(is_string($this->pages[$kk]->drawobject) && (function_exists($this->pages[$kk]->drawobject))) call_user_func($this->pages[$kk]->drawobject);
       else echo "<!-- No drawing data for this page ($kk) -->";
       echo "</TD>";
    }
    echo "</table></div>\n";
    if($this->style==WIZARDSTYLE) { // register 'next' event functions
      echo '<SCRIPT language="javascript">';
      for($kk=0;$kk<count($this->pages);$kk++){
        if($this->pages[$kk]->func_evtnext!='') echo "prop_events{$this->sheetid}[$kk]='{$this->pages[$kk]->func_evtnext}';\n";
      }
      echo "</SCRIPT>\n";
      echo "<br><br><div align=center><!-- wizard buttons -->\n";
      $prevdis = ($startpage==0)? 'disabled':'';
      $nextdis = ($startpage+1<count($this->pages))? '':'disabled';
      echo "<button id='asprbt_prev{$this->sheetid}' class='{$as_cssclass['button']}' style='width:160' $prevdis onClick='AsPropSheetSwitchPage{$this->sheetid}(\"{$this->sheetid}\",\"-1\")'>{$as_iface['btn_prev']}</button>&nbsp;&nbsp;
      <button id='asprbt_next{$this->sheetid}' class='{$as_cssclass['button']}' style='width:160' $nextdis onClick='AsPropSheetSwitchPage{$this->sheetid}(\"{$this->sheetid}\",\"+1\")'>{$as_iface['btn_next']}</button>";
      if(!empty($this->finish_caption) && !empty($this->finishfunc)) {
        $finenab=$this->finish_enabled?'':'disabled';
        echo "&nbsp;&nbsp;<button id='asprbt_finish{$this->sheetid}' class='{$as_cssclass['button']}' $finenab style='width:160' onClick='{$this->finishfunc}'>{$this->finish_caption}</button>";
      }
    }
    echo "</div><!-- wizard buttons end -->\n";
  } //Draw() end

  function DrawFormPage($fields, $initfunc='') {
    // echoes <input...> fields for all fields passed in $fields[]
    // fields[] = CFormField object
    global $as_cssclass;
    echo "<br><table width='100%'>\n";
    for($ii=0; $ii<count($fields); $ii++) { #<3>
      if(!is_object($fields[$ii])) continue;
      $fname = $fields[$ii]->fname;
      $prompt = $fields[$ii]->prompt;
      $init = $fields[$ii]->initvalue;
      $addstr = '';
      $style='';
      if(!empty($fields[$ii]->maxlength)) $addstr .= " maxlength='{$fields[$ii]->maxlength}'";
      if(!empty($fields[$ii]->addparams)) $addstr .= ' '.$fields[$ii]->addparams;
      if(!empty($fields[$ii]->title)) $addstr .= " title='{$fields[$ii]->title}'";
      if(!empty($fields[$ii]->width)) $style.=" width:{$fields[$ii]->width};";
      if($style!='') $style = " style='".trim($style)."'";
      switch($fields[$ii]->ftype) { #<4>
        case 'head': echo "<tr class='{$as_cssclass['row_header']}'><td colspan=2 align='center' {$addstr}><b>$prompt</b></td></tr>\n";
          break;
        case 'text':
          $init=str_replace("'",'"',$init);
          echo "<tr><td align='right'><b>$prompt</b></td><td><input type='text' name='$fname' class='{$as_cssclass['textfield']}'{$style}{$addstr} value='$init'></td></tr>\n";
          break;
        case 'password':
          echo "<tr><td align='right'><b>$prompt</b></td><td><input type='password' name='$fname' class='{$as_cssclass['textfield']}' style='width:32' value='$init'></td></tr>\n";
          break;
        case 'textarea':
          echo "<tr><td colspan=2><b>$prompt</b><br><TEXTAREA name='$fname' class='{$as_cssclass['textfield']}' {$addstr}>$init</TEXTAREA></td></tr>\n";
          break;

        case 'checkbox':
          $chk = $init?'checked':'';
          $onClick= empty($fields[$ii]->onchange)? '':" onClick='{$fields[$ii]->onchange}'";
          echo "<tr><td align='right'><b>$prompt</b></td><td><input type='checkbox' name='$fname' value='1' {$onClick} {$chk}></td></tr>\n";
          break;
        case 'select':
          echo "<tr><td align='right'><b>$prompt</b></td><td><SELECT name='$fname' class='{$as_cssclass['textfield']}'{$style}>\n";
          $lst = $fields[$ii]->options;
          if(is_array($lst)) foreach($lst as $key=>$vval) {
             $optval=is_array($vval)? $vval[0]:$key;
             $opttext=is_array($vval)? $vval[1]:$vval;
             $slct = ($optval==$init)? 'selected':'';
             echo "<OPTION value='$optval' $slct>$opttext</OPTION>\n";
          }
          echo"</SELECT></td></tr>\n";
          break;
        case 'button':
          $onClick= empty($fields[$ii]->onchange)? '':" onClick='{$fields[$ii]->onchange}'";
          echo "<tr><td align='right'>&nbsp;</td><td><button name='$fname' class='{$as_cssclass['button']}' {$style}{$addstr}{$onClick}>$prompt</button></td></tr>\n";
          break;
      } #<4>
    } #<3>
    echo "</table>";
  } // DrawFormPage end
} //CPropertySheet def. end
?>