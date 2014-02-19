ase_0=navigator.userAgent.toLowerCase();ase_1=(ase_0.indexOf('gecko')!=-1&&ase_0.indexOf('safari')==-1);ase_2=(ase_0.indexOf('konqueror')!=-1);ase_3=(ase_0.indexOf('safari')!=-1);ase_4=(ase_0.indexOf('opera')!=-1);ase_5=(ase_0.indexOf('msie')!=-1&&!ase_4&&(ase_0.indexOf('webtv')==-1));ase_6=ase_5?-2:0;function ase_7(){return(new RegExp("msie ([0-9]{1,}[\.0-9]{0,})").exec(ase_0)!=null)?parseFloat(RegExp.$1):6.0;}
function ase_8(id){return document.getElementById?document.getElementById(id):document.all[id];}
function ase_9(e,l9,la,lb,lc){if(window.event)return window.event[l9]+((document.documentElement&&document.documentElement[la])||document.body[la]);else return(typeof e[lb]!='undefined')?e[lb]:e[l9]+window[lc];}
function ase_a(e){return ase_9(e,"clientX","scrollLeft","pageX","scrollX")+ase_6;}
function ase_b(e){return ase_9(e,"clientY","scrollTop","pageY","scrollY")+ase_6;}
function ase_c(e){if(ase_5&&window.event)return window.event.button;else return(e.which==3)?2:e.which;}
function ase_d(ld,le){return ld?ld[le]+ase_d(ld.offsetParent,le):0;}
function ase_e(ld){return ase_d(ld,"offsetLeft")+(ld.offsetWidth-ld.clientWidth)/2;}
function ase_f(ld){return ase_d(ld,"offsetTop")+(ld.offsetHeight-ld.clientHeight)/2;}
function ase_g(lf,lg){return lf+((lf.indexOf('?')!=-1)?'&':'?')+lg;}
function ase_h(lh,li,lj){var re=new RegExp(li,'g');return lh.replace(re,lj);}
function ase_i(ll){var lm=document.scripts;if(((!lm)||(!lm.length))&&document.getElementsByTagName)lm=document.getElementsByTagName("script");if(lm){for(var i=0;i<lm.length;++i){var lo=lm[i].src;if(!lo)continue;var lp=lo.indexOf(ll);if(lp!=-1)return lo.substring(0,lp);}
}
return "";}
function ase_j(lq,lr,ls){var lt=lq.indexOf(lr);var lu=lq.indexOf(ls);if((lt<0)||(lu<=lt))return '';else return lq.substring(lt+lr.length,lu);}
function ase_k(lq,lv){var lp=lq.indexOf(lv);return(lp>=0)?lq.substring(0,lp):lq;}
function ase_l(lq,lv){var lp=lq.indexOf(lv);return(lp>=0)?lq.substring(lp+1,lq.length):"";}
function ase_m(v){return ase_h(ase_h(v,'&','&amp;'),'"','&#34;');}
function ase_n(n){n.onload=n.onerror=n.src='';var p=n.parentNode||n.parentElement;if(p&&p.removeChild)p.removeChild(n);else if(n.outerHTML)n.outerHTML='';}
function ase_o(f){var f2=frames[f.id];if(f2){var d2=f2.contentDocument||f2.document;if(d2)return d2.body.innerHTML;}
return null;}
function ase_AJAX_frame_loaded(f){if(f.lx1)return;var l21=ase_o(f);if(null!=l21)f.lh1(l21);ase_n(f);}
function ase_AJAX_frame_error(f){if(f.li1)f.li1(700,ase_o(f));ase_n(f);}
function ase_p(lf,l31,l41){var f=null;var l51="ase_AJAX_frame_"+(new Date().getTime());if(ase_5&&(ase_7()>=5.5)){document.body.insertAdjacentHTML('AfterBegin',"<IFRAME NAME='"+l51+"' ID='"+l51+"' style='display:none' onload='ase_AJAX_frame_loaded(this)' onerror='ase_AJAX_frame_error(this)'></IFRAME>");f=ase_8(l51);}
if(!f){if(l41)l41(600,"Cannot create IFRAME '"+l51+"'");return null;}
var f2=frames[f.id];var d2=f2.contentDocument||f2.document;f.lx1=true;d2.open();d2.write('<html><body><form name="ase_form" method="'+((lf.length<1000)?'get':'post')+'" action="'+ase_m(ase_k(lf,"?"))+'">');var l61=ase_l(lf,"?").split("&");for(var i=0;i<l61.length;++i){d2.write('<input type="hidden" name="'+ase_m(ase_k(l61[i],"="))+'" value="'+ase_m(unescape(ase_l(l61[i],"=")))+'">');}
d2.write('</form></body></html>');d2.close();f.lx1=false;f.lh1=l31;f.li1=l41;d2.forms['ase_form'].submit();return{'abort':function(){ase_n(f);}};}
function ase_q(){if(typeof XMLHttpRequest!='undefined')return new XMLHttpRequest();
/*@cc_on
@if(@_jscript_version>=5)
try{return new ActiveXObject("Msxml2.XMLHTTP");}catch(e){}
try{return new ActiveXObject("Microsoft.XMLHTTP");}catch(e){}
@end
@*/
}
function ase_r(lf,l31,l41){var r=ase_q();if(r){r.onreadystatechange=function(){if(r.readyState==4){var status=-9999;eval("try { status = r.status; } catch(e) {}");if(status==-9999)return;if((r.status==200)||(r.status==304))l31(r.responseText);else if(l41)l41(r.status,r.responseText);window.setTimeout(function(){r.onreadystatechange=function(){};r.abort();},1);}
}
if((lf.length<1000)||(ase_4&&!r.setRequestHeader)){r.open('GET',lf,true);r.send(null);}
else {r.open('POST',ase_k(lf,"?"),true);r.setRequestHeader("Content-Type","application/x-www-form-urlencoded");r.send(ase_l(lf,"?"));}
return r;}
return ase_p(lf,l31,l41);}
function _jcv(v){this.lr=v.id;v.lc2=v.useMap;this.lp1=v.style.cursor;this.lr1(v);this.lo={};var l91=v.id+"_JsChartViewerState";this.lu1=ase_8(l91);if(!this.lu1){var p=v.parentNode||v.parentElement;if(p&&p.insertBefore){var s=this.lu1=document.createElement("HIDDEN");s.id=s.name=l91;s.value=this.la1();p.insertBefore(s,v);}
else if(v.insertAdjacentHTML){v.insertAdjacentHTML("AfterEnd","<HIDDEN id='"+l91+"' name='"+l91+"'>");this.lu1=ase_8(l91);if(this.lu1)this.lu1.value=this.la1();else this.lu1={"name":l91,"id":l91,"value":this.la1()};}
}
else this.decodeState(this.lu1.value);this.ls1();if(!ase_5)this.l11(this.lw1());if(this.ln)this.partialUpdate();}
_jcvp=_jcv.prototype;_jcv.l22=function(lc1){var ld1=window.cdjcv_path;if(typeof ld1=="undefined")ld1=ase_i("cdjcv.js");else if((ld1.length>0)&&("/=".indexOf(ld1.charAt(ld1.length-1))==-1))ld1+='/';return ld1+lc1;}
_jcv.Horizontal=0;_jcv.Vertical=1;_jcv.HorizontalVertical=2;_jcv.Default=0;_jcv.Scroll=2;_jcv.ZoomIn=3;_jcv.ZoomOut=4;_jcv.msgContainer='<div style="font-family:Verdana;font-size:8pt;font-weight:bold;padding:3 8 3 8;border:1pt solid #000000;background-color:#FFCCCC;color:#000000">%msg</div>';_jcv.okButton='<center>[<a href="javascript:%closeScript"> OK </a>]</center>';_jcv.xButton='[<a href="javascript:%closeScript"> X </a>]';_jcv.shortErrorMsg='Error %errCode accessing server'+_jcv.okButton;_jcv.serverErrorMsg=_jcv.xButton+'<div style="font-family:Arial; font-weight:bold; font-size:15pt;">Error %errCode accessing server</div><hr>%errMsg';_jcv.updatingMsg='<div style="padding:0 8 0 6;background-color:#FFFFCC;color:#000000;border:1px solid #000000"><table><tr><td><img src="'+_jcv.l22('wait.gif')+'"></td><td style="font-size:8pt;font-weight:bold;font-family:Verdana">Updating</td></tr></table></div>';_jcv.lj1=new Array("l0","l1","l2","l3","l4","l5","l6","l7","l8","l9","la","lb","lc","ld","le","lf","lg","lh","li","lj","lk","ll","lm","ln","lo","lp","lq");_jcv.get=function(id){var imgObj=ase_8(id);if(!imgObj)return null;if(!imgObj._jcv)imgObj._jcv=new _jcv(imgObj);return imgObj._jcv;}
_jcvp.getId=function(){return this.lr;}
_jcvp.lt1=function(){return ase_8(this.lr);}
_jcvp.lr1=function(){this.lt1().ly=function(e,id){var lf1;if(!this._jcv.lm1)lf1=this._jcv["onImg"+id](e);if(this["_jcvOn"+id+"Chain"])lf1=this["_jcvOn"+id+"Chain"](e);return lf1;};this.lt1()._jcvOnMouseMoveChain=this.lt1().onmousemove;this.lt1()._jcvOnMouseUpChain=this.lt1().onmouseup;this.lt1()._jcvOnMouseDownChain=this.lt1().onmousedown;var lg1=this.lr;this.lt1().onmousemove=function(e){return ase_8(lg1).ly(e,"MouseMove");}
this.lt1().onmousedown=function(e){return ase_8(lg1).ly(e,"MouseDown");}
this.lt1().onmouseup=function(e){return ase_8(lg1).ly(e,"MouseUp");}
}
_jcvp.lq2=function(x){return x-ase_e(this.lt1());}
_jcvp.lr2=function(y){return y-ase_f(this.lt1());}
_jcvp.lp2=function(w){return w;}
_jcvp.lo2=function(h){return h;}
_jcvp.lm2=function(x){return x+ase_e(this.lt1());}
_jcvp.ln2=function(y){return y+ase_f(this.lt1());}
_jcvp.ll2=function(w){return w;}
_jcvp.lk2=function(h){return h;}
_jcvp.setCustomAttr=function(k,v){this.lo[k]=v;this.le2();}
_jcvp.getCustomAttr=function(k){return this.lo[k];}
_jcvp.l4=0;_jcvp.l5=0;_jcvp.l6=1;_jcvp.l7=1;_jcvp.setViewPortLeft=function(x){this.l4=x;this.le2();}
_jcvp.getViewPortLeft=function(){return this.l4;}
_jcvp.setViewPortTop=function(y){this.l5=y;this.le2();}
_jcvp.getViewPortTop=function(){return this.l5;}
_jcvp.setViewPortWidth=function(w){this.l6=w;this.le2();}
_jcvp.getViewPortWidth=function(){return this.l6;}
_jcvp.setViewPortHeight=function(h){this.l7=h;this.le2();}
_jcvp.getViewPortHeight=function(){return this.l7;}
_jcvp.l0=-1;_jcvp.l1=-1;_jcvp.l2=-1;_jcvp.l3=-1;_jcvp.l01=function(x,y){x=this.lq2(x);y=this.lr2(y);return(this.l0<=x)&&(x<=this.l0+this.l2)&&(this.l1<=y)&&(y<=this.l1+this.l3);}
_jcvp.msgBox=function(lm1,ln1){var m=this.l21;if(!m&&lm1){var d=document;if(d.body.insertAdjacentHTML){var lq1='msg_'+this.lr;d.body.insertAdjacentHTML("BeforeEnd","<DIV ID='"+lq1+"' style='position:absolute;visibility:hidden;'></DIV>");m=ase_8(lq1);}
else if(d.createElement){m=d.createElement("DIV");m.style.position='absolute';m.style.visibility='hidden';d.body.appendChild(m);}
if(m)this.l21=m;}
if(m){window.clearTimeout(m.l31);var s=m.style;if(lm1){if(ln1)m.l31=window.setTimeout(function(){s.visibility='hidden';},Math.abs(ln1));if(ln1<0)lm1+=_jcv.okButton;if(lm1.substring(0,4).toLowerCase()!="<div")lm1=ase_h(_jcv.msgContainer,'%msg',lm1);var lr1="_jcv.get('"+this.lr+"').msgBox();";m.innerHTML=ase_h(lm1,'%closeScript',lr1);s.visibility='visible';s.left=this.lm2(Math.max(0,this.l0+(this.l2-m.offsetWidth)/2));s.top=this.ln2(Math.max(0,this.l1+(this.l3-m.offsetHeight)/2));}
else {s.visibility='hidden';}
}
}
_jcvp.l8=2;_jcvp.l9="#000000";_jcvp.setSelectionBorderWidth=function(w){this.l8=w;this.le2();}
_jcvp.getSelectionBorderWidth=function(){return this.l8;}
_jcvp.setSelectionBorderColor=function(c){this.l9=c;this.le2();}
_jcvp.getSelectionBorderColor=function(){return this.l9;}
_jcvp.lq1=function(){_jcv.l92=this.l62("_jcv_leftLine");_jcv.la2=this.l62("_jcv_rightLine");_jcv.l72=this.l62("_jcv_topLine");_jcv.l82=this.l62("_jcv_bottomLine");}
function _jcvp_false_function(){return false;}
_jcvp.l62=function(id){var d=document;if(d.body.insertAdjacentHTML){d.body.insertAdjacentHTML("BeforeEnd","<DIV ID='"+id+"' style='position:absolute;visibility:hidden;background-color:#000000;width:1px;height:1px;'><IMG WIDTH='1' HEIGHT='1'></DIV>");var lf1=ase_8(id);if(ase_5&&(ase_7()<5.5))lf1.onmousemove=_jcvp_false_function;return lf1;}
else if(d.createElement){var lf1=d.createElement("DIV");var s=lf1.style;s.position="absolute";s.visibility="hidden";s.backgroundColor="#000000";s.width="1px";s.height="1px";d.body.appendChild(lf1);return lf1;}
}
_jcvp.lk1=function(x,y,lt1,lu1){if(!_jcv.l92)this.lq1();if(!_jcv.l92)return;var lv1=_jcv.l92.style;var lw1=_jcv.la2.style;var lx1=_jcv.l72.style;var ly1=_jcv.l82.style;lv1.left=lx1.left=ly1.left=x;lv1.top=lw1.top=lx1.top=y;lx1.width=ly1.width=lt1;ly1.top=y+lu1-this.l8+1;lv1.height=lw1.height=lu1;lw1.left=x+lt1-this.l8+1;lv1.width=lw1.width=lx1.height=ly1.height=this.l8;lv1.backgroundColor=lw1.backgroundColor=lx1.backgroundColor=ly1.backgroundColor=this.l9;}
_jcvp.ll1=function(b){if(b&&!_jcv.l72)this.lq1();if(ase_5&&_jcv.l72&&(ase_7()<5.5)){_jcv.l92.onmouseup=_jcv.la2.onmouseup=_jcv.l72.onmouseup=_jcv.l82.onmouseup=this.lt1().onmouseup;}
if(_jcv.l72)_jcv.l92.style.visibility=_jcv.la2.style.visibility=_jcv.l72.style.visibility=_jcv.l82.style.visibility=b?"visible":"hidden";}
_jcvp.la=_jcv.Default;_jcvp.lb=_jcv.Horizontal;_jcvp.lc=_jcv.Horizontal;_jcvp.ld=2;_jcvp.le=0.5;_jcvp.lf=0.01;_jcvp.lg=1;_jcvp.lh=0.01;_jcvp.li=1;_jcvp.getMouseUsage=function(){return this.la;}
_jcvp.setMouseUsage=function(l02){this.la=l02;this.lf2();this.le2();}
_jcvp.lf2=function(){var a=this.lb2;if(a){switch(this.la){case _jcv.ZoomIn:a.href="javascript://ZoomIn";break;case _jcv.ZoomOut:a.href="javascript://ZoomOut";break;default:a.removeAttribute("href");}
}
}
_jcvp.getScrollDirection=function(){return this.lb;}
_jcvp.setScrollDirection=function(l22){this.lb=l22;this.le2();}
_jcvp.getZoomDirection=function(){return this.lc;}
_jcvp.setZoomDirection=function(l22){this.lc=l22;this.le2();}
_jcvp.getZoomInRatio=function(){return this.ld;}
_jcvp.setZoomInRatio=function(l32){if(l32>0)this.ld=l32;this.le2();}
_jcvp.getZoomOutRatio=function(){return this.le;}
_jcvp.setZoomOutRatio=function(l32){if(l32>0)this.le=l32;this.le2();}
_jcvp.getZoomInWidthLimit=function(){return this.lf;}
_jcvp.setZoomInWidthLimit=function(l32){this.lf=l32;this.le2();}
_jcvp.getZoomOutWidthLimit=function(){return this.lg;}
_jcvp.setZoomOutWidthLimit=function(l32){this.lg=l32;this.le2();}
_jcvp.getZoomInHeightLimit=function(){return this.lh;}
_jcvp.setZoomInHeightLimit=function(l32){this.lh=l32;this.le2();}
_jcvp.getZoomOutHeightLimit=function(){return this.li;}
_jcvp.setZoomOutHeightLimit=function(l32){this.li=l32;this.le2();}
_jcvp.lb1=function(){return((this.lc!=_jcv.Vertical)&&(this.l6>this.lf))||((this.lc!=_jcv.Horizontal)&&(this.l7>this.lh));}
_jcvp.lc1=function(){return((this.lc!=_jcv.Vertical)&&(this.l6<this.lg))||((this.lc!=_jcv.Horizontal)&&(this.l7<this.li));}
_jcvp.ls2=-1;_jcvp.lt2=-1;_jcvp.lj=5;_jcvp.getMinimumDrag=function(){return this.lj;}
_jcvp.setMinimumDrag=function(l42){this.lj=l42;this.le2();}
_jcvp.l41=function(e,d){var l52=Math.abs(ase_a(e)-this.ls2);var l62=Math.abs(ase_b(e)-this.lt2);switch(d){case _jcv.Horizontal:return l52>=this.lj;case _jcv.Vertical:return l62>=this.lj;default:return(l52>=this.lj)||(l62>=this.lj);}
}
_jcvp.onImgMouseDown=function(e){if(this.l01(ase_a(e),ase_b(e))&&(ase_c(e)==1)){if(e&&e.preventDefault&&(this.la!=_jcv.Default))e.preventDefault();this.ld2(true);this.ls(e);}
}
_jcvp.onImgMouseMove=function(e){if(this.l12&&window.event&&(ase_c(e)!=1)){this.ld2(false);this.l02=false;this.ll1(false);}
this.lz1=this.l12||this.l01(ase_a(e),ase_b(e));if(this.lz1){this.lu(e);if(this.l12){if((this.la!=_jcv.Default)&&this.lt1().useMap)this.lt1().useMap=null;this.lt(e);}
}
this.l11(this.lz(e));return this.la==_jcv.Default;}
_jcvp.onImgMouseUp=function(e){if(this.l12&&(ase_c(e)==1)){this.ld2(false);this.lv(e);}
}
_jcvp.ld2=function(b){var imgObj=this.lt1();if(b){if(((this.la==_jcv.ZoomIn)||(this.la==_jcv.ZoomOut))&&imgObj.useMap)imgObj.useMap=null;}
else {if(imgObj.useMap!=imgObj.lc2)imgObj.useMap=imgObj.lc2;}
if(!ase_5){if(b){if(!window._jcvOnMouseUpChain)window._jcvOnMouseUpChain=window.onmouseup;if(!window._jcvOnMouseMoveChain)window._jcvOnMouseMoveChain=window.onmousemove;window.onmouseup=imgObj.onmouseup;window.onmousemove=imgObj.onmousemove;}
else {window.onmouseup=window._jcvOnMouseUpChain;window.onmousemove=window._jcvOnMouseMoveChain;window._jcvOnMouseUpChain=null;window._jcvOnMouseMoveChain=null;}
}
this.l12=b;}
_jcvp.setZoomInCursor=function(l72){this.lk=l72;this.le2();}
_jcvp.getZoomInCursor=function(){return this.lk;}
_jcvp.setZoomOutCursor=function(l72){this.ll=l72;this.le2();}
_jcvp.getZoomOutCursor=function(){return this.ll;}
_jcvp.setNoZoomCursor=function(l72){this.lq=l72;this.le2();}
_jcvp.getNoZoomCursor=function(){return this.lq;}
_jcvp.setScrollCursor=function(l72){this.lm=l72;this.le2();}
_jcvp.getScrollCursor=function(){return this.lm;}
_jcvp.lw1=function(){if(ase_5&&(ase_7()<6.0))return "";switch(this.la){case _jcv.ZoomIn:if(this.lb1()){if(this.lk)return this.lk;else return ase_1?"-moz-zoom-in":"url('"+_jcv.l22('zoomin.cur')+"')";}
else {if(this.lq)return this.lq;else return ase_1?"default":"url('"+_jcv.l22('nozoom.cur')+"')";}
case _jcv.ZoomOut:if(this.lc1()){if(this.ll)return this.ll;else return ase_1?"-moz-zoom-out":"url('"+_jcv.l22('zoomout.cur')+"')";}
else {if(this.lq)return this.lq;else return ase_1?"default":"url('"+_jcv.l22('nozoom.cur')+"')";}
default:return "";}
}
_jcvp.lz=function(e){if(this.lm1)return "wait";if(this.l02){if(this.lm)return this.lm;switch(this.lb){case _jcv.Horizontal:return(ase_a(e)>=this.ls2)?"e-resize":"w-resize";case _jcv.Vertical:return(ase_b(e)>=this.lt2)?"s-resize":"n-resize";default:return "move";}
}
if(this.lz1)return this.lw1();else return "";}
_jcvp.l11=function(l82){if(l82!=this.lp1){this.lp1=l82;this.lt1().style.cursor=new String(l82);}
}
_jcvp.ls=function(e){this.ls2=ase_a(e);this.lt2=ase_b(e);}
_jcvp.lu=function(e){}
_jcvp.lt=function(e){var eX=ase_a(e);var eY=ase_b(e);if(this.la==_jcv.ZoomIn){var d=this.lc;var lb2=this.lb1()&&this.l41(e,d);if(lb2){var lc2=Math.min(eX,this.ls2);var ld2=Math.min(eY,this.lt2);var l52=Math.abs(eX-this.ls2);var l62=Math.abs(eY-this.lt2);switch(d){case _jcv.Horizontal:this.lk1(lc2,this.ln2(this.l1),l52,this.lk2(this.l3));break;case _jcv.Vertical:this.lk1(this.lm2(this.l0),ld2,this.ll2(this.l2),l62);break;default:this.lk1(lc2,ld2,l52,l62);break;}
}
this.ll1(lb2);}
else if(this.la==_jcv.Scroll){var d=this.lb;if(this.l02||this.l41(e,d)){this.l02=true;var le2=(d==_jcv.Vertical)?0:(eX-this.ls2);var lf2=(d==_jcv.Horizontal)?0:(eY-this.lt2);if((le2<0)&&(this.l4+this.l6-this.l6*this.lp2(le2)/this.l2>1))le2=Math.min(0,(this.l4+this.l6-1)*this.l2/this.l6);if((le2>0)&&(this.l6*this.lp2(le2)/this.l2>this.l4))le2=Math.max(0,this.l4*this.l2/this.l6);if((lf2<0)&&(this.l5+this.l7-this.l7*this.lo2(lf2)/this.l3>1))lf2=Math.min(0,(this.l5+this.l7-1)*this.l3/this.l7);if((lf2>0)&&(this.l7*this.lp2(lf2)/this.l3>this.l5))lf2=Math.max(0,this.l5*this.l3/this.l7);this.lk1(this.lm2(this.l0)+le2,this.ln2(this.l1)+lf2,this.ll2(this.l2),this.lk2(this.l3));this.ll1(true);}
}
}
_jcvp.lv=function(e){this.ll1(false);switch(this.la){case _jcv.ZoomIn:if(this.lb1()){if(this.l41(e,this.lc))this.le1(e);else this.lg1(e,this.ld);}
break;case _jcv.ZoomOut:if(this.lc1())this.lg1(e,this.le);break;default:if(this.l02)this.lf1(e);break;}
this.l02=false;}
_jcvp.lg1=function(e,lg2){var eX=ase_a(e);var eY=ase_b(e);var lh2=this.l6/lg2;var li2=this.l7/lg2;this.l71(this.lc,(this.lq2(eX)-this.l0)*this.l6/this.l2-lh2/2,lh2,(this.lr2(eY)-this.l1)*this.l7/this.l3-li2/2,li2);}
_jcvp.lf1=function(e){var eX=ase_a(e);var eY=ase_b(e);this.l71(this.lb,this.l6*this.lp2(this.ls2-eX)/this.l2,this.l6,this.l7*this.lo2(this.lt2-eY)/this.l3,this.l7);}
_jcvp.le1=function(e){var eX=ase_a(e);var eY=ase_b(e);var lh2=this.l6*this.lp2(Math.abs(this.ls2-eX))/this.l2;var li2=this.l7*this.lo2(Math.abs(this.lt2-eY))/this.l3;this.l71(this.lc,this.l6*(this.lq2(Math.min(this.ls2,eX))-this.l0)/this.l2,lh2,this.l7*(this.lr2(Math.min(this.lt2,eY))-this.l1)/this.l3,li2);}
_jcvp.l71=function(d,lj2,lk2,ll2,lm2){var ln2=this.l4;var lo2=this.l5;var lh2=this.l6;var li2=this.l7;if((((lk2<this.l6)&&(this.l6<this.lf))||(d==_jcv.Vertical))&&(((lm2<this.l7)&&(this.l7<this.lh))||(d==_jcv.Horizontal)))return;if(d!=_jcv.Vertical){if(lk2!=this.l6){lh2=Math.max(this.lf,Math.min(lk2,this.lg));lj2-=(lh2-lk2)/2;}
ln2=Math.max(0,Math.min(this.l4+lj2,1-lh2));}
if(d!=_jcv.Horizontal){if(lm2!=this.l7){li2=Math.max(this.lh,Math.min(lm2,this.li));ll2-=(li2-lm2)/2;}
lo2=Math.max(0,Math.min(this.l5+ll2,1-li2));}
if((ln2!=this.l4)||(lo2!=this.l5)||(lh2!=this.l6)||(li2!=this.l7)){this.lh2=this.l4;this.li2=this.l5;this.lj2=this.l6;this.lg2=this.l7;this.l4=ln2;this.l5=lo2;this.l6=lh2;this.l7=li2;this.lp=1;this.le2();this.applyHandlers("viewportchanged");this.lp=0;}
}
_jcvp.lo1=function(lp2){var id=(lp2+"events").toLowerCase();if(!this[id])this[id]=[];return this[id];}
_jcvp.attachHandler=function(lp2,f){var a=this.lo1(lp2);a[a.length]=f;return lp2+":"+(a.length-1);}
_jcvp.detachHandler=function(lq2){var ab=lq2.split(':');var a=this.lo1(ab[0]);a[parseInt(ab[1])]=null;}
_jcvp.applyHandlers=function(lp2){var lf1=false;var a=this.lo1(lp2);for(var i in a){this.lu2=a[i];if(this.lu2!=null)lf1|=this.lu2();}
this.lu2=null;return lf1;}
_jcvp.partialUpdate=function(){if(this.lm1)return;_jcv.ld1(this.lt1());this.applyHandlers("preupdate");this.ln=1;this.le2();var ls2=this.updatingMsg;if(!ls2)ls2=_jcv.updatingMsg;if(ls2&&(ls2!="none"))this.msgBox(ls2);var lf=ase_g(ase_8(this.lr+"_callBackURL").value,"cdPartialUpdate="+this.lr+"&cdCacheDefeat="+(new Date().getTime())+"&"+this.lu1.name+"="+escape(this.lu1.value));var lt2=this;this.lm1=true;ase_r(lf,function(t){lt2.l42(t)},function(lv2,lw2){lt2.lx(lv2,lw2);});}
_jcvp.l42=function(lq){var lx2=ase_j(lq,"<!--CD_SCRIPT "," CD_SCRIPT-->");if(lx2){var ly2=ase_j(lq,"<!--CD_MAP "," CD_MAP-->");var imgObj=this.lt1();var imgBuffer=this.l61=(this.doubleBuffering)?new Image():imgObj;if(imgObj.useMap)imgObj.useMap=null;imgObj.loadImageMap=function(){window.setTimeout(function(){_jcv.putMap(imgObj,ly2);},100);};imgBuffer.onload=function(){imgObj._jcv.onPartialLoad(true);}
imgBuffer.onerror=imgBuffer.onabort=function(lm1){imgObj._jcv.lx(999,"Error loading image '"+this.src+"'["+lm1+"]");}
var l03=window.onerror;window.onerror=function(lm1){imgObj._jcv.lx(801,"Error interpretating partial update result ["+lm1+"] <div style='margin:20px;background:#dddddd'><xmp>"+lx2+"</xmp></div>")};eval(lx2);window.onerror=l03;if(ase_1)this.le2();}
else this.lx(800,"Partial update returns invalid data <div style='margin:20px;background:#dddddd'><xmp>"+lq+"</xmp></div>");}
_jcvp.lw=function(l13){var imgObj=this.lt1();var imgBuffer=this.l61;if(imgBuffer)imgBuffer.onerror=imgBuffer.onabort=imgBuffer.onload='';imgObj.onUpdateCompleted='';this.lm1=false;if(l13){if(imgObj!=imgBuffer){imgObj.src=imgBuffer.src;imgObj.style.width=imgBuffer.style.width;imgObj.style.height=imgBuffer.style.height;}
imgObj.loadImageMap();}
else {imgObj.useMap=imgObj.lc2;if(this.lj2||this.lg2){this.l4=this.lh2;this.l7=this.lg2;this.l5=this.li2;this.l6=this.lj2;this.le2();}
}
imgObj.loadImageMap='';}
_jcvp.onPartialLoad=function(l13){if(this.lt1().onUpdateCompleted)this.lt1().onUpdateCompleted();else this.msgBox();this.lw(l13);this.applyHandlers("postupdate");}
_jcvp.lx=function(lv2,lw2){this.lw(false);this.msgBox();this.errCode=lv2;this.errMsg=lw2;if(!this.applyHandlers("updateerror")){var l23=this.serverErrorMsg;if(!l23)l23=_jcv.serverErrorMsg;if(l23&&(l23!="none"))this.msgBox(ase_h(ase_h(l23,'%errCode',lv2),'%errMsg',lw2));}
this.errCode=null;this.errMsg=null;}
_jcvp.streamUpdate=function(l33){var l43=new Date().getTime();if(!l33)l33=60;var l53=this.l52;if(l53){if(l33*1000>=l43-l53.l51)return false;l53.src=null;l53.onerror=l53.onabort=l53.onload=null;}
if(!this.l32)this.l32=this.lt1().src;this.l52=l53=new Image();l53.l51=l43;var lt2=this;l53.onload=function(){var imgObj=lt2.lt1();if(imgObj.useMap)imgObj.useMap=null;var b=lt2.l52;if(imgObj!=b)imgObj.src=b.src;b.onabort();}
l53.onerror=l53.onabort=function(){var b=lt2.l52;if(b)b.onload=b.onabort=b.onerror=null;lt2.l52=null;}
l53.src=ase_g(this.l32,"cdDirectStream="+this.lr+"&cdCacheDefeat="+l43);return true;}
_jcvp.l91=function(a,v){return a+((typeof v!="number")?"**":"*")+v;}
_jcvp.l81=function(av){var lp=av.indexOf("*");if(lp==-1)return null;var a=av.substring(0,lp);var v=av.substring(lp+1,av.length);if(v.charAt(0)=="*")v=v.substring(1,v.length);else v=parseFloat(v);return{"attr":a,"value":v};}
_jcvp.la1=function(){var lf1="";for(var i=0;i<_jcv.lj1.length;++i){var a=_jcv.lj1[i];var v=null;if((a=="lo")&&this.lo){for(var le in this.lo)v=((v==null)?"":v+"\x1f")+this.l91(le,this.lo[le]);}
else v=this[a];if((typeof v!="undefined")&&(null!=v))lf1+=(lf1?"\x1e":"")+this.l91(i,v);}
return lf1;}
_jcvp.decodeState=function(s){var l61=s.split("\x1e");for(var i=0;i<l61.length;++i){var av=this.l81(l61[i]);if(!av)continue;var a=_jcv.lj1[parseInt(av.attr)];if(a=="lo"){var l73=av.value.split("\x1f");for(var i2=0;i2<l73.length;++i2){var l93=this.l81(l73[i2]);this.lo[l93.attr]=l93.value;}
}
else this[a]=av.value;}
this.lp=0;}
_jcvp.le2=function(){if(this.lu1)this.lu1.value=this.la1();}
_jcvp.ls1=function(){if(!ase_5){var imgObj=this.lt1();var m=_jcv.ln1(imgObj);if(m){m.onmousedown=imgObj.onmousedown;m.onmousemove=imgObj.onmousemove;if(ase_1&&document.createElement){var a=this.lb2=document.createElement("AREA");a.coords=""+this.l0+","+this.l1+","+(this.l0+this.l2)+","+(this.l1+this.l3);a.shape="rect";a.onclick="return false;";m.appendChild(a);m.ly1=a;this.lf2();}
}
}
}
_jcv.ln1=function(imgObj){var la3=imgObj.lc2;if(!la3)la3=imgObj.useMap;if(!la3)return null;var lp=la3.indexOf('#');if(lp>=0)la3=la3.substring(lp+1);return ase_8(la3);}
_jcv.loadMap=function(imgObj,lf){if(!imgObj.lc2)imgObj.lc2=imgObj.useMap;_jcv.ld1(imgObj);imgObj.lv1=ase_r(lf,function(t){_jcv.putMap(imgObj,ase_j(t,"<!--CD_MAP "," CD_MAP-->"));},function(lv2,lw2){_jcv.onLoadMapError(lv2,lw2);}
);}
_jcv.loadPendingMap=function(){if(!window._jcvPendingMap)return;for(var a in window._jcvPendingMap){var ld=ase_8(a);if(ld){var lf=window._jcvPendingMap[a];window._jcvPendingMap[a]=null;if(lf)_jcv.loadMap(ld,lf);}
}
}
_jcv.ld1=function(imgObj){if(imgObj.lv1){imgObj.lv1.abort();imgObj.lv1=null;}
}
_jcv.onLoadMapError=function(lv2,lw2){}
_jcv.putMap=function(imgObj,lb3){var m=_jcv.ln1(imgObj);if(!m&&lb3){var la3='map_'+imgObj.id;imgObj.useMap=imgObj.lc2='#'+la3;var d=document;if(d.body.insertAdjacentHTML){d.body.insertAdjacentHTML("BeforeEnd","<MAP ID='"+la3+"'></MAP>");m=ase_8(la3);}
else if(d.createElement){m=d.createElement("MAP");m.id=m.name=la3;d.body.appendChild(m);}
if(imgObj._jcv)imgObj._jcv.ls1();}
if(m){m.innerHTML=lb3;if(m.ly1)m.appendChild(m.ly1);if(imgObj.useMap!=imgObj.lc2)imgObj.useMap=imgObj.lc2;}
imgObj.lv1=null;}
_jcv.canSupportPartialUpdate=function(){return((ase_5&&(ase_7()>=5.5))||window.XMLHttpRequest||ase_q());}
JsChartViewer=_jcv;_jcv.loadPendingMap();