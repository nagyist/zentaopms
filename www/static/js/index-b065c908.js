import{u as H}from"./chartEditStore-1f8105d2.js";import{j as N,d as R,v as Y,aP as h,R as M,r as u,o as m,m as S,e as a,w as o,f as t,c as P,ab as W,F as j,A as X,p as r,t as q}from"./index.js";import{i as F}from"./icon-0617f5fe.js";import{a as d,S as x}from"./SettingItemBox-aedd53df.js";/* empty css                                                                */import"./plugin-9e9b27b9.js";const G={class:"go-canvas-setting"},J=r("\u5BBD"),K=r("\u9AD8"),Q=r("\u9002\u914D\u65B9\u5F0F"),Z=R({__name:"index",setup($){const i=H(),e=i.getEditCanvasConfig,v=i.getEditCanvas,E=Y(0),{ScaleIcon:k,FitToScreenIcon:C,FitToWidthIcon:w}=F.carbon,{LockOpenOutlineIcon:A,LockClosedOutlineIcon:y}=F.ionicons5,B=[{label:"1366 \xD7 768",value:1366},{label:"1600 x 900",value:1600},{label:"1920 x 1080",value:1920},{label:"2560 x 1440",value:2560},{label:"3840 x 2160",value:3840},{label:"\u81EA\u5B9A\u4E49",value:0}],b=()=>{e.size===1366?(e.width=1366,e.height=768):e.size===1600?(e.width=1600,e.height=900):e.size===1920?(e.width=1920,e.height=1080):e.size===2560?(e.width=2560,e.height=1440):e.size===3840&&(e.width=3840,e.height=2160),i.computedScale()},z=[{key:h.FIT,title:"\u81EA\u9002\u5E94",icon:k,desc:"\u6309\u5C4F\u5E55\u6BD4\u4F8B\u81EA\u9002\u5E94 (\u7559\u767D\u53EF\u80FD\u53D8\u591A)"},{key:h.FULL,title:"\u94FA\u6EE1",icon:C,desc:"\u5F3A\u5236\u94FA\u6EE1 (\u5143\u7D20\u53EF\u80FD\u6324\u538B\u6216\u62C9\u4F38\u53D8\u5F62)"},{key:h.SCROLL_Y,title:"Y\u8F74\u6EDA\u52A8",icon:w,desc:"X\u8F74\u56FA\u5B9A\uFF0CY\u8F74\u81EA\u9002\u5E94\u6EDA\u52A8"}];M(()=>e.selectColor,c=>{E.value=c?0:1},{immediate:!0});const f=c=>c>50;let s=1;const D=()=>{e.lockScale&&(e.height=Math.round(e.width/s)),i.computedScale()},I=()=>{e.lockScale&&(e.width=Math.round(e.height*s)),i.computedScale()},L=()=>{e.lockScale=!e.lockScale,s=1,e.lockScale&&(s=e.width/e.height)};return(c,l)=>{const T=u("n-select"),p=u("n-text"),g=u("n-input"),U=u("n-icon"),O=u("n-radio"),_=u("n-space"),V=u("n-radio-group");return m(),S("div",G,[a(t(x),{name:"\u753B\u5E03\u5C3A\u5BF8",alone:!0,itemBoxStyle:{margin:"0px 0px 20px 0px"}},{default:o(()=>[a(t(d),null,{default:o(()=>[a(T,{class:"scale-btn",value:t(e).size,"onUpdate:value":[l[0]||(l[0]=n=>t(e).size=n),b],options:B},null,8,["value"])]),_:1})]),_:1}),t(e).size===0?(m(),P(t(x),{key:0,itemRightStyle:{gridTemplateColumns:"1fr 1fr 1fr"}},{default:o(()=>[a(t(d),{width:80},{default:o(()=>[a(g,{size:"small",value:t(e).width,"onUpdate:value":[l[1]||(l[1]=n=>t(e).width=n),D],disabled:t(v).lockScale,validator:f},{prefix:o(()=>[a(p,{depth:"3"},{default:o(()=>[J]),_:1})]),_:1},8,["value","disabled"])]),_:1}),a(t(d),{width:80},{default:o(()=>[a(g,{size:"small",value:t(e).height,"onUpdate:value":[l[2]||(l[2]=n=>t(e).height=n),I],disabled:t(v).lockScale,validator:f},{prefix:o(()=>[a(p,{depth:"3"},{default:o(()=>[K]),_:1})]),_:1},8,["value","disabled"])]),_:1}),a(t(d),{width:20},{default:o(()=>[a(U,{size:"16",style:{"margin-top":"4px"},component:t(e).lockScale?t(y):t(A),onClick:L},null,8,["component"])]),_:1})]),_:1})):W("",!0),a(_,{class:"detail",vertical:"",size:12},{default:o(()=>[a(_,null,{default:o(()=>[a(p,null,{default:o(()=>[Q]),_:1}),a(V,{value:t(e).previewScaleType,"onUpdate:value":l[3]||(l[3]=n=>t(e).previewScaleType=n),name:"radiogroup"},{default:o(()=>[a(_,null,{default:o(()=>[(m(),S(j,null,X(z,n=>a(O,{key:n.key,value:n.key},{default:o(()=>[r(q(n.desc),1)]),_:2},1032,["value"])),64))]),_:1})]),_:1},8,["value"])]),_:1})]),_:1})])}}});var ue=N(Z,[["__scopeId","data-v-ee7c18b4"]]);export{ue as default};
