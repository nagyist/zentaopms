import{j as V,d as w,a3 as u,v as i,y as D,a1 as H,a$ as $,r as o,o as a,m as p,e as l,w as c,F as d,A as z,f as _,c as v,L as N,z as R,t as j}from"./index.js";import{i as K}from"./icon-3165ab42.js";import{u as O}from"./chartEditStore-4e92d8bb.js";import{u as f,C as m}from"./chartLayoutStore-c2304649.js";import{E as T}from"./index-3b00be53.js";import"./plugin-93d20baa.js";const U=w({__name:"index",setup(Y){const s=O(),{lockScale:y,scale:b}=u(s.getEditCanvas),{setItem:E}=f(),{getLayers:k}=u(f()),{LayersIcon:C}=K.ionicons5,S=i(100),L=e=>{E(e.key,!e.select)},x=e=>e.key===m.DETAILS?e.select?"":"primary":e.select?"primary":"",h=D([{key:m.LAYERS,select:k,title:"\u56FE\u5C42",icon:H(C)}]);let g=[{label:"200%",value:200},{label:"150%",value:150},{label:"100%",value:100},{label:"50%",value:50},{label:"\u81EA\u9002\u5E94",value:0}];const n=i(""),I=e=>{if(e===0){s.computedScale();return}s.setScale(e/100)};return $(()=>{const e=(b.value*100).toFixed(0);n.value=`${e}%`,S.value=parseInt(e)}),(e,r)=>{const A=o("n-button"),B=o("n-space"),F=o("n-select");return a(),p(d,null,[l(B,{class:"btnList"},{default:c(()=>[(a(!0),p(d,null,z(h,t=>(a(),v(A,{class:"btn",type:x(t),key:t.key,ghost:"",onClick:q=>L(t)},{icon:c(()=>[(a(),v(N(t.icon)))]),default:c(()=>[R("span",null,j(t.title),1)]),_:2},1032,["type","onClick"]))),128))]),_:1}),l(T),l(F,{disabled:_(y),class:"scale-btn",value:n.value,"onUpdate:value":[r[0]||(r[0]=t=>n.value=t),I],size:"mini",options:_(g)},null,8,["disabled","value","options"])],64)}}});var X=V(U,[["__scopeId","data-v-0fd5e37c"]]);export{X as default};
