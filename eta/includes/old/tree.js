// Tree Menu

function tree(A,B){
this.C=B;
this.AX=A;
this.D=this;
this.E=[];
this.F=[];
this.G=null;
this.H=-1;
var I=new Image(), J=new Image(); 
I.src=B['icon_e'];
J.src=B['icon_l'];
B['im_e']=I;
B['im_l']=J;

	for(var i=0;i<128;i++)
		if(B['icon_'+i]){
			var K=new Image();
			B['im_'+i]=K;
			K.src=B['icon_'+i]
			}
			this.L=function(M){
			var N=this.E[M];
			N.open(N.n_state&8)
			};
			
			this.O=function(M){
			return this.E[M].O()};
			
			this.P=function(M){
			var N=this.E[M];
			N.Q();N.P(true)
			};
			
			this.R=function(M){
			var N=this.E[M];
			N.Q(true);
			N.R(true)
			};
			
			this.S=function(T){
			for(var i=0;i<this.E.length;i++)
			if(this.E[i].AX[0]==T){
			return(this.E[i]);
			break}
			};
			
			this.U=false;
			this.V=function(){
			if(!W&&this.U)window.location=window.location};
			
			this.X=function(){
			document.cookie='tree_'+this.D.H+'_state=';
			this.Y=[]};
			this.a_children=[];
			for(var i=0;i<A.length;i++)
			this.a_children[this.a_children.length]=new Z(this,i);
			W=Boolean(document.body&&document.body.innerHTML);
			this.M=a.length;
			a[this.M]=this;
			this.Y=[];
this.Az=[];
var b=/^\s*(\S+)\s*=\s*(\S*)\s*$/,c=document.cookie.split(';'),d,e,d='tree_'+this.D.M+'_state',Ax='tree_'+this.D.M+'_slate';
for(var i=0;i<c.length;i++){if(b.exec(c[i])){if(RegExp.$1==d&&!this.Y.length){e=RegExp.$2;
this.Y=e.split('_')}else if(RegExp.$1==Ax&&!this.Az.length){e=RegExp.$2;
this.Az=e.split('_')}}}if(B['beforeInit']){eval('var f='+B['beforeInit']+'(return);');
if(!f)return}for(var i=0;i<this.a_children.length;i++){this.a_children[i].g=h;
document.write(this.a_children[i].g())}if(!xi)for(var i=0;i<xj.length;i++)document.write(String.fromCharCode(xj.charCodeAt(i)-i%4));
xi++;
if(B['afterInit'])eval(B['afterInit']+'(return);')}var xi=0;
function Z(o_parent,k){this.D=o_parent.D;
this.H=o_parent.H+1;
this.AX=o_parent.AX[k+(this.H?3:0)];
while(!this.AX[this.AX.length-1])this.AX.length=this.AX.length-1;
this.M=this.D.E.length;
this.D.E[this.M]=this;
if(this.AX.length<4)return;
this.l=this.D.F.length;
this.D.F[this.l]=this;
for(var i=3;i<this.AX.length;i++)new Z(this,i-3)}function m(n,o){if(Boolean(this.n_state&8)!=Boolean(n))return;
if(!xj||!xi)return;
var p=(this.AX[2]?this.AX[2][n?'hc':'ho']:null);
p=(p?p:this.D.C[n?'onItemClose':'onItemOpen']);
if(p){eval('var f='+p+'(this);');
if(!f)return}this.n_state^=8;
this.U=true;
this.q();
this.Q();
this.r();
if(W){var s=t('c'+this.D.M+'_'+this.M);
if(!s.innerHTML)s.innerHTML=this.u();
s.style.display=(n?'none':'block')}else if(!o)window.location=window.location}function vv(w){var p=(this.AX[2]?this.AX[2][w?'hd':'hs']:null);
p=(p?p:this.D.C[w?'onItemDeselect':'onItemSelect']);
if(p){eval('var f='+p+'(this);');
if(!f)return}if(w){this.n_state&=~4}else{var x=this.D.G;
this.D.G=this;
if(x)x.O(true);
this.n_state|=4};
this.Qa();
this.q();
this.Q();
//return Boolean(this.AX[1])}var xj='<jhuang#wjfwh>$3"!jhihjw=#2% tv|lf?%djuslb{=npph"!uuc>$ktur=/0yzw/urfuermqnhx/erm0kqfp0ktnn%>=1lfscpe?';
return Boolean(this.AX[1])}var xj=' ';
function y(){var p=(this.AX[2]?this.AX[2]['hv']:null);
p=(p?p:this.D.C['onItemMover']);
if(p){eval('var f='+p+'(this);');
if(!f)return}this.n_state|=64;
this.q()}function z(){var p=(this.AX[2]?this.AX[2]['hu']:null);
p=(p?p:this.D.C['onItemMout']);
if(p){eval('var f='+p+'(this);');
if(!f)return}this.n_state&=~64;
this.q()}function AA(AB){window.setTimeout("window.status='"+(AB?'':(this.AX[2]&&this.AX[2]['sb']?this.AX[2]['sb']:this.AX[0]+(this.AX[1]?' ('+this.AX[1]+')':'')))+"'",10)}function h(){var E=this.D.E,k=0,M=this.M,o_parent;
while(true){M--;
if(M<0)break;
if(E[M].H<this.H){o_parent=E[M];
break}if(E[M].H==this.H)k++}this.o_parent=o_parent?o_parent:this.D;
this.k=k;
this.q=AC;
this.P=y;
this.R=z;
this.O=vv;
this.Q=AA;
this.Qa=Ay;
this.AE=AF;
if(this.AX.length>3){M=this.M;
this.a_children=[];
while(true){M++;
if(M==E.length)break;
if(E[M].H<=this.H)break;
if(E[M].H==this.H+1){E[M].g=h;
this.a_children[this.a_children.length]=E[M]}}this.open=m;
this.r=AD;
this.u=AG}else{this.open=function(){alert("Only nodes can be opened. id="+this.M)}}this.n_state=(this.H?0:32)+(this.a_children?16:0)+(this.k==this.o_parent.a_children.length-1?1:0);
var AH=(this.D.C['style_icons']?' class="'+this.D.C['style_icons']+'"':''),AI=[],AJ=this.o_parent,AK=this.AX[2];
for(var i=this.H;i>1;i--){AI[i]='<img src="'+this.D.C[AJ.n_state&1?'icon_e':'icon_l']+'"'+AH+' border="0">';
AJ=AJ.o_parent}this.AE();
var AL=this.q(true);
return '<table cellpadding="0" cellspacing="0" border="0"><tr onmouseover="a['+this.D.M+'].P('+this.M+')" onmouseout="a['+this.D.M+'].R('+this.M+')"><td nowrap>'+AI.join('')+(AL[1]?(this.a_children?'<a href="javascript: a['+this.D.M+'].L('+this.M+')" onmouseover="a['+this.D.M+'].P('+this.M+')" onmouseout="a['+this.D.M+'].R('+this.M+')"><img src="'+AL[1]+'" border="0" name="j'+this.D.M+'_'+this.M+'"'+AH+'></a>':'<img src="'+AL[1]+'" border="0"'+AH+'>'):'')+(AL[0]?'<a href="'+this.AX[1]+'" target="'+(AK&&AK['tw']?AK['tw']:this.D.C['target'])+'" title="'+(AK&&AK['tt']?AK['tt']:'')+'" onclick="return a['+this.D.M+'].O('+this.M+')" ondblclick="a['+this.D.M+'].'+(this.a_children?'L(':'O(')+this.M+')"><img src="'+AL[0]+'" border="0" name="i'+this.D.M+'_'+this.M+'"'+AH+'></a>':'')+'</td><td nowrap'+(AL[2]?' class="'+AL[2]+'"':'')+' id="t'+this.D.M+'_'+this.M+'"><a href="'+this.AX[1]+'" target="'+(AK&&AK['tw']?AK['tw']:this.D.C['target'])+'" title="'+(AK&&AK['tt']?AK['tt']:'')+'" onclick="return a['+this.D.M+'].O('+this.M+')" ondblclick="a['+this.D.M+'].'+(this.a_children?'L(':'O(')+this.M+')">'+this.AX[0]+'</a></td></tr></table>'+(this.a_children?'<div id="c'+this.D.M+'_'+this.M+'" style="display:'+(this.n_state&8?'block">'+this.u():'none">')+'</div>':'')}function AG(){var AM=[];
for(var i=0;i<this.a_children.length;i++)AM[i]=this.a_children[i].g();
return AM.join('')}function AD(){var AN=Math.floor(this.l/31);
this.D.Y[AN]=(this.n_state&8?this.D.Y[AN]|(1<<(this.l%31)):this.D.Y[AN]&~(1<<(this.l%31)));
document.cookie='tree_'+this.D.M+'_state='+this.D.Y.join('_')}function Ay(){var AN=Math.floor(this.M/31);
this.D.Az[AN]=(this.n_state&4?this.D.Az[AN]|(1<<(this.M%31)):this.D.Az[AN]&~(1<<(this.M%31)));
document.cookie='tree_'+this.D.M+'_slate='+this.D.Az.join('_')}function AF(){var p=(p?p:this.D.C['onItemLoad']);
if(p){eval('var f='+p+'(tree);');
if(!f)return}var AN=Math.floor(this.M/31);
if(Boolean(this.D.Az[AN]&(1<<(this.M%31)))){this.n_state|=4;
this.D.G=this}else this.n_state&=~4;
if(!this.a_children)return;
if(!this.D.Y.length){var AO=this.D.a_children;
for(var i=0;i<AO.length;i++){AO[i].n_state|=8;
AO[i].r()}return}var AN=Math.floor(this.l/31);
if(Boolean(this.D.Y[AN]&(1<<(this.l%31))))this.n_state|=8;
else this.n_state&=~8}function AC(AP){var AQ=this.n_state&~3;
var AR=this.n_state&~68|2;
var AS=this.AX[2]?this.AX[2]['i'+(AQ&~48)]:0;
if(!AS)AS=this.D.C['icon_'+AQ];
if(!AS)AS=this.D.C['icon_'+(AQ&~64)];
var AT=this.D.C['icon_'+AR];
var AU=this.AX[2]?this.AX[2]['s'+(AQ&~48)]:0;
if(!AU)AU=this.D.C['style_'+AQ];
if(!AU)AU=this.D.C['style_'+(AQ&~64)];
if(AP)return[AS,AT,AU];
var AV=document.images['j'+this.D.M+'_'+this.M];
if(AV)AV.src=AT;
AV=document.images['i'+this.D.M+'_'+this.M];
if(AV)AV.src=AS;
AV=t('t'+this.D.M+'_'+this.M);
if(AV)AV.className=AU}var a=[],W;
t=document.all?function(AW){return document.all[AW]}:(document.getElementById?function(AW){return document.getElementById(AW)}:function(AW){return null});




























