import{ah as M,ai as B,aj as t,a1 as a,v as U,ak as C,al as V,am as F,j as N,d as G,r as E,m as $,z as k,e as u,w as m,f as l,F as j,o as z,an as d}from"./index.js";import{_ as K}from"./index-370ba2b7.js";import{u as I,a as W}from"./chartEditStore-02533e8b.js";import{l as Y}from"./plugin-1ac3c7cf.js";import{i as g}from"./icon-75f043e1.js";const Ie=(n,o)=>{!window.$vue.component(n)&&o&&window.$vue.component(n,o)},_=n=>M({loader:n,loadingComponent:B,delay:20}),{CopyIcon:X,CutIcon:q,ClipboardOutlineIcon:J,TrashIcon:Q,ChevronDownIcon:Z,ChevronUpIcon:ee,LockOpenOutlineIcon:ne,LockClosedOutlineIcon:te,EyeOutlineIcon:oe,EyeOffOutlineIcon:ae}=g.ionicons5,{UpToTopIcon:le,DownToBottomIcon:ie,PaintBrushIcon:se,Carbon3DSoftwareIcon:re,Carbon3DCursorIcon:ue}=g.carbon,e=I(),O=(n=3)=>({type:"divider",key:`d${n}`}),f=[{label:"\u9501\u5B9A",key:t.LOCK,icon:a(te),fnHandle:e.setLock},{label:"\u89E3\u9501",key:t.UNLOCK,icon:a(ne),fnHandle:e.setUnLock},{label:"\u9690\u85CF",key:t.HIDE,icon:a(ae),fnHandle:e.setHide},{label:"\u663E\u793A",key:t.SHOW,icon:a(oe),fnHandle:e.setShow},{type:"divider",key:"d0"},{label:"\u590D\u5236",key:t.COPY,icon:a(X),fnHandle:e.setCopy},{label:"\u526A\u5207",key:t.CUT,icon:a(q),fnHandle:e.setCut},{label:"\u7C98\u8D34",key:t.PARSE,icon:a(J),fnHandle:e.setParse},{type:"divider",key:"d1"},{label:"\u7F6E\u9876",key:t.TOP,icon:a(le),fnHandle:e.setTop},{label:"\u7F6E\u5E95",key:t.BOTTOM,icon:a(ie),fnHandle:e.setBottom},{label:"\u4E0A\u79FB",key:t.UP,icon:a(ee),fnHandle:e.setUp},{label:"\u4E0B\u79FB",key:t.DOWN,icon:a(Z),fnHandle:e.setDown},{type:"divider",key:"d2"},{label:"\u6E05\u7A7A\u526A\u8D34\u677F",key:t.CLEAR,icon:a(se),fnHandle:e.setRecordChart},{label:"\u5220\u9664",key:t.DELETE,icon:a(Q),fnHandle:e.removeComponentList}],y=[{label:"\u521B\u5EFA\u5206\u7EC4",key:t.GROUP,icon:a(re),fnHandle:e.setGroup},{label:"\u89E3\u9664\u5206\u7EC4",key:t.UN_GROUP,icon:a(ue),fnHandle:e.setUnGroup}],ce=[t.PARSE,t.CLEAR],S=(n,o)=>{if(!o)return n;const i=[];return o.forEach(c=>{i.push(...n.filter(s=>s.key===c))}),i},de=(n,o)=>o?n.filter(i=>o.findIndex(c=>c!==i.key)!==-1):n,r=U([]),_e=(n,o,i,c,s)=>{n.stopPropagation(),n.preventDefault();let p=n.target;for(;p instanceof SVGElement;)p=p.parentNode;e.setTargetSelectChart(o&&o.id),e.setRightMenuShow(!1),e.getTargetChart.selectId.length>1?r.value=y:r.value=f,o||(r.value=S(C(r.value),ce)),c&&(r.value=de([...y,O(),...f],c)),s&&(r.value=S([...y,O(),...f],s)),i&&(r.value=i(V(C(r.value)),[...y,...f],o)),F().then(()=>{e.setMousePosition(n.clientX,n.clientY),e.setRightMenuShow(!0)})},fe=()=>(r.value=f,{menuOptions:r,defaultOptions:f,defaultMultiSelectOptions:y,handleContextMenu:_e,onClickOutSide:()=>{e.setRightMenuShow(!1)},handleMenuSelect:i=>{e.setRightMenuShow(!1);const c=r.value.filter(s=>s.key===i);r.value.forEach(s=>{if(s.key===i){if(s.fnHandle){s.fnHandle();return}c||Y()}})},mousePosition:e.getMousePosition});const pe={class:"go-chart"},me={style:{overflow:"hidden",display:"flex"}},ye=G({__name:"index",setup(n){const o=W(),i=I();o.canvasInit(i.getEditCanvas);const c=_(()=>d(()=>import("./index-a754038e.js"),["static/js/index-a754038e.js","static/js/index.js","static/css/index-a1e82c00.css"])),s=_(()=>d(()=>import("./index-806cc133.js"),["static/js/index-806cc133.js","static/css/index-89aaed03.css","static/js/index.js","static/css/index-a1e82c00.css","static/js/chartEditStore-02533e8b.js","static/js/plugin-1ac3c7cf.js","static/js/icon-75f043e1.js"])),p=_(()=>d(()=>import("./index-6bc4ca6a.js"),["static/js/index-6bc4ca6a.js","static/css/index-13c8c126.css","static/js/index.js","static/css/index-a1e82c00.css","static/js/icon-75f043e1.js","static/js/chartEditStore-02533e8b.js","static/js/plugin-1ac3c7cf.js","static/js/chartLayoutStore-68fe6628.js","static/js/index-e8934610.js","static/css/index-0b9d86bb.css"])),b=_(()=>d(()=>import("./index-40dea43f.js"),["static/js/index-40dea43f.js","static/css/index-9214308d.css","static/css/SizeSetting.vue_vue_type_style_index_0_scoped_true_lang-976f12dd.css","static/js/index.js","static/css/index-a1e82c00.css","static/js/index-1c23f6b2.js","static/css/index-3e214e52.css","static/js/chartLayoutStore-68fe6628.js","static/js/chartEditStore-02533e8b.js","static/js/plugin-1ac3c7cf.js","static/js/icon-75f043e1.js","static/js/index-e01ecf60.js","static/css/index-fcd7c9c0.css","static/js/tables_list-16bd57ab.js","static/js/SettingItemBox-31d7c018.js","static/js/CollapseItem-eece6de1.js","static/js/useTargetData.hook-e164791d.js","static/js/index-370ba2b7.js","static/js/index-05711e10.js","static/css/index-24d4cc74.css"])),w=_(()=>d(()=>import("./index-d40e40cd.js").then(function(v){return v.i}),["static/js/index-d40e40cd.js","static/css/index-fc68a65b.css","static/js/index.js","static/css/index-a1e82c00.css","static/js/index-1c23f6b2.js","static/css/index-3e214e52.css","static/js/index-e01ecf60.js","static/css/index-fcd7c9c0.css","static/css/SizeSetting.vue_vue_type_style_index_0_scoped_true_lang-976f12dd.css","static/js/chartEditStore-02533e8b.js","static/js/plugin-1ac3c7cf.js","static/js/icon-75f043e1.js","static/js/chartLayoutStore-68fe6628.js","static/js/tables_list-16bd57ab.js","static/js/SettingItemBox-31d7c018.js","static/js/CollapseItem-eece6de1.js","static/js/useTargetData.hook-e164791d.js"])),H=_(()=>d(()=>import("./index-121b19ec.js"),["static/js/index-121b19ec.js","static/css/index-418685ba.css","static/js/index.js","static/css/index-a1e82c00.css","static/js/index-1c23f6b2.js","static/css/index-3e214e52.css","static/js/chartLayoutStore-68fe6628.js","static/js/chartEditStore-02533e8b.js","static/js/plugin-1ac3c7cf.js","static/js/icon-75f043e1.js","static/js/index-370ba2b7.js","static/js/index-05711e10.js","static/css/index-24d4cc74.css"])),P=_(()=>d(()=>import("./index-8eba3805.js"),["static/js/index-8eba3805.js","static/js/chartLayoutStore-68fe6628.js","static/js/index.js","static/css/index-a1e82c00.css","static/js/chartEditStore-02533e8b.js","static/js/plugin-1ac3c7cf.js","static/js/icon-75f043e1.js"])),{menuOptions:A,onClickOutSide:T,mousePosition:h,handleMenuSelect:R}=fe();return(v,he)=>{const D=E("n-layout-content"),x=E("n-layout"),L=E("n-dropdown");return z(),$(j,null,[k("div",pe,[u(x,null,{default:m(()=>[u(l(K),null,{left:m(()=>[u(l(c))]),center:m(()=>[u(l(p))]),"ri-left":m(()=>[u(l(s))]),_:1}),u(D,{"content-style":"overflow:hidden; display: flex"},{default:m(()=>[k("div",me,[u(l(w)),u(l(b))]),u(l(H))]),_:1})]),_:1})]),u(L,{placement:"bottom-start",trigger:"manual",size:"small",x:l(h).x,y:l(h).y,options:l(A),show:l(i).getRightMenuShow,"on-clickoutside":l(T),onSelect:l(R)},null,8,["x","y","options","show","on-clickoutside","onSelect"]),u(l(P))],64)}}});var Ee=N(ye,[["__scopeId","data-v-63c190b0"]]),ge=Object.freeze(Object.defineProperty({__proto__:null,default:Ee},Symbol.toStringTag,{value:"Module"}));export{Ie as c,O as d,ge as i,_ as l,fe as u};
