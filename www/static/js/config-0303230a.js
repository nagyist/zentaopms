var l=Object.defineProperty,m=Object.defineProperties;var n=Object.getOwnPropertyDescriptors;var t=Object.getOwnPropertySymbols;var h=Object.prototype.hasOwnProperty,d=Object.prototype.propertyIsEnumerable;var o=(i,p,r)=>p in i?l(i,p,{enumerable:!0,configurable:!0,writable:!0,value:r}):i[p]=r,e=(i,p)=>{for(var r in p||(p={}))h.call(p,r)&&o(i,r,p[r]);if(t)for(var r of t(p))d.call(p,r)&&o(i,r,p[r]);return i},s=(i,p)=>m(i,n(p));import{ax as g,al as a}from"./index.js";import{e as c}from"./chartEditStore-02533e8b.js";import{d as f}from"./index-e01ecf60.js";import"./plugin-1ac3c7cf.js";import"./icon-75f043e1.js";import"./chartLayoutStore-68fe6628.js";import"./tables_list-16bd57ab.js";/* empty css                                                                */import"./SettingItemBox-31d7c018.js";import"./CollapseItem-eece6de1.js";import"./useTargetData.hook-e164791d.js";const C={dataset:3234,flipperLength:6,flipperBgColor:"#16293E",flipperTextColor:"#4A9EF8FF",flipperWidth:30,flipperHeight:50,flipperRadius:5,flipperGap:10,flipperType:"down",flipperSpeed:450};class B extends c{constructor(){super(...arguments),this.key=f.key,this.attr=s(e({},g),{w:300,h:100,zIndex:-1}),this.chartConfig=a(f),this.option=a(C)}}export{B as default,C as option};
