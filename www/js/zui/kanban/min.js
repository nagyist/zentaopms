/*!
 * ZUI: ZUI Kanban View - v1.10.0 - 2022-06-02
 * http://openzui.com
 * GitHub: https://github.com/easysoft/zui.git 
 * Copyright (c) 2022 cnezsoft.com; Licensed MIT
 */
!function(){"use strict";function a(a,e){return n&&!e?requestAnimationFrame(a):setTimeout(a,e||0)}function e(a){return n?cancelAnimationFrame(a):void clearTimeout(a)}var n="function"==typeof window.requestAnimationFrame;$.zui({asap:a,clearAsap:e})}(),function(a){"use strict";function e(e,n){"string"==typeof e&&(e=a(e)),e instanceof a&&(e=e[0]);var t=e.getBoundingClientRect(),r=window.innerHeight||document.documentElement.clientHeight,i=window.innerWidth||document.documentElement.clientWidth;if(n)return t.left>=0&&t.top>=0&&t.left+t.width<=i&&t.top+t.height<=r;var d=t.top<=r&&t.top+t.height>=0,o=t.left<=i&&t.left+t.width>=0;return d&&o}var n="zui.virtualRender",t=function(e,r){"function"==typeof r&&(r={render:r});var i=this;i.name=n,i.$=a(e),i.options=r=a.extend({},t.DEFAULTS,this.$.data(),r),i.rendered=!1;var d=r.container;"function"==typeof d&&(d=d(i));var o=a(d?d:window);i.tryRender()||(i.$container=o,i.scrollListener=i.tryRender.bind(i),r.pendingClass&&i.$.addClass(r.pendingClass),o.on("scroll",i.scrollListener))};t.prototype.tryRender=function(){var n=this;return!(n.rendered||!e(n.$))&&(n.renderTaskID&&a.zui.clearAsap(n.renderTaskID),n.renderTaskID=a.zui.asap(function(){n.renderTaskID=null;var a=n.options.render(n.$);a!==!1&&(n.rendered=!0,n.destroy())},n.options.delay),!0)},t.prototype.destroy=function(){var e=this;e.renderTaskID&&a.zui.clearAsap(e.renderTaskID),e.scrollListener&&(e.$container.off("scroll",e.scrollListener),e.scrollListener=null);var t=e.options.pendingClass;t&&e.$.removeClass(t),e.$.removeData(n)},t.DEFAULTS={pendingClass:"virtual-pending"},a.fn.virtualRender=function(e){return this.each(function(){var r=a(this),i=r.data(n);if(i){if("string"==typeof e)return i[e]();i.destroy()}r.data(n,i=new t(this,e))})},a.zui.isElementInViewport=e}(jQuery),function(a){"use strict";var e="zui.virtuallist",n=function(t,r){"function"==typeof r&&(r={render:r});var i=this;i.name=e,i.$=a(t),i.options=r=a.extend({},n.DEFAULTS,this.$.data(),r),i.render(r.list,r.scrollTop);var d=0;i.$.on("scroll."+e,function(){var e=i.$.scrollTop();e!==i.scrollTop&&(i.scrollTop=e,d&&a.zui.clearAsap(d),d=a.zui.asap(function(){d=0,i.render()}))}),i.$.on("resize."+e,function(){d&&a.zui.clearAsap(d),d=a.zui.asap(function(){d=0,i.render()})})};n.prototype.render=function(e,n){var t=this;e?t.list=e:e=t.list,n&&a.extend(this.options,n);var r=t.scrollTop=t.$.scrollTop(),i=t.$,d=t.options.itemClassName,o=t.options.itemHeight,s=t.options.countPerRow,l=Number.parseFloat(i.css("padding-top"),10),c=Array.isArray(e),u="number"==typeof e?e:e.length,h=i.height(),p="virtual-list-item-expired",b=i.children(".virtual-list-holder-top"),v=i.children(".virtual-list-holder-bottom");b.length||(b=a('<div class="virtual-list-holder-top" style="width:100%">').prependTo(i)),v.length||(v=a('<div class="virtual-list-holder-bottom" style="width:100%">').appendTo(i)),i.children("."+d).addClass(p);var f=Math.max(0,s*Math.floor((r-l)/o)),m=Math.min(u,s*Math.ceil((r-l+h)/o));b.height(f*o/s);for(var g=f;g<m;g++){var k=t.options.render(g,e,c?e[g]:null);k.addClass(d).removeClass(p)}return v.height((u-m)*o/s),i.children("."+p).remove(),b.prependTo(i),v.appendTo(i),!0},n.prototype.destroy=function(){that.$.off("."+e)},n.DEFAULTS={itemClassName:"virtual-list-item",countPerRow:1},a.fn.virtualList=function(t){return this.each(function(){var r=a(this),i=r.data(e);if(i){if("string"==typeof t)return i[t]();i.destroy()}r.data(e,i=new n(this,t))})}}(jQuery),function(a){"use strict";var e="zui.kanban",n="object"==typeof CSS&&CSS.supports("display","flex"),t=function(n,r){var i=this;if(i.name=e,i.$=a(n).addClass("kanban"),r=i.setOptions(a.extend({},t.DEFAULTS,this.$.data(),r)),r.onAction){var d=function(e){var n=a(this);r.onAction(n.data("action"),n,e,i)};i.$.on("click",".action",d).on("dblclick",".action-dbc",d)}var o=r.droppable;if("auto"===o&&(o=!r.readonly),o){var s=r.sortable;"function"==typeof s?s={finish:s}:s&&"object"!=typeof s&&(s={});var l=0;"function"==typeof o?o={drop:o}:"object"!=typeof o&&(o={});var c=a.extend({dropOnMouseleave:!0,selector:".kanban-item",target:".kanban-lane-col:not(.kanban-col-sorting)",mouseButton:"left"},o,{before:function(e){if(o.before){var n=o.before(e);if(n===!1)return n}if(s){var t=e.element.closest(".kanban-lane-items");t.closest(".kanban-col").addClass("kanban-col-sorting"),i._sortResult=null,i._$sortItems=t;var r=t.data("zui.sortable");r||t.sortable(a.extend({},s,{selector:".kanban-item",trigger:".kanban-card",dragCssClass:"kanban-item-sorting",noShadow:!0,finish:function(a){a.changed&&a.list.length>1&&(i._sortResult=a)}})).triggerHandler(e.event)}},drop:function(a){o.drop&&o.drop(a),r.onAction&&r.onAction("dropItem",a.element,a,i)},start:function(e){i.$.addClass("kanban-dragging"),l&&clearTimeout(l),l=setTimeout(function(){a(e.shadowElement).addClass("in"),l=0},50),o.start&&o.start(e)},always:function(a){i.$.removeClass("kanban-dragging"),l&&(clearTimeout(l),l=0),s&&(i._$sortItems.sortable("destroy").closest(".kanban-col").removeClass("kanban-col-sorting"),a.target&&a.target[0]===a.element.closest(".kanban-lane-col")[0]&&i._sortResult&&s.finish&&s.finish(i._sortResult)),o.always&&o.always(a)}});i.$.droppable(c)}r.onCreate&&r.onCreate(i)};t.prototype.setOptions=function(e){var t=this,r=a.extend({},t.options,{data:t.data},e);t.options=r,r.useFlex&&!n&&(r.useFlex=!1),t.$.toggleClass("no-flex",!r.useFlex).toggleClass("use-flex",!!r.useFlex);var i=!!a.fn.virtualRender&&r.virtualize;return i&&("object"!=typeof i&&(i={lane:!0}),t.virtualize=a.extend({},i)),t.data=r.data||[],t.render(t.data),r},t.prototype.render=function(a){var e=this;a&&(e.data=a),e.data&&!Array.isArray(e.data)&&(e.data=[e.data]);var n=e.options,t=e.data||[];n.beforeRender&&n.beforeRender(e,t),e.$.toggleClass("kanban-readonly",!!n.readonly).toggleClass("kanban-no-lane-name",!!n.noLaneName),e.$.children(".kanban-board").addClass("kanban-expired"),e.maxKanbanBoardWidth=0;for(var r=0;r<t.length;++r)e.renderKanban(r);e.$.children(".kanban-expired").remove(),n.fluidBoardWidth&&t.length>1&&e.$.children(".kanban-board").css("min-width",e.maxKanbanBoardWidth),n.onRender&&n.onRender(e)},t.prototype.layoutKanban=function(a,e){for(var n=this,t=n.options,r=t.noLaneName?0:t.laneNameWidth,i=0,d={},o=!1,s=[],l=0;l<a.columns.length;++l){var c=a.columns[l];if(d[c.type])console.error('ZUI kanban error: duplicated column type "'+c.type+'" definition.');else{if(d[c.type]=c,c.$cardsCount=0,c.$kanbanData=a,c.$index=i,c.id||(c.id=c.type),c.asParent)o=!0,c.subs=[];else if(i++,c.parentType){var u=d[c.parentType];c.$subIndex=u.subs.length,u.subs.push(c)}s.push(c)}}a.columns=s;var h=a.id,p=n.$,b=t.minColWidth*i+r;e=e||p.children('.kanban-board[data-id="'+h+'"]'),e.css(t.fluidBoardWidth?"min-width":"width",b),n.maxKanbanBoardWidth=Math.max(n.maxKanbanBoardWidth,b),a.$layout={minWidth:b,laneNameWidth:r,columnsCount:i,hasParentCol:o,columnsMap:d,columnWidth:100/i};for(var v=a.cardsPerRow||t.cardsPerRow,f=function(a,e,r){if(a.asParent)return 0;r=r||e.items||e.cards||{};var i=r[a.type]||[];if(a.$cardsCount+=i.length,a.parentType){var o=d[a.parentType];o.$cardsCount+=i.length}var s=Math.ceil(i.length/(a.cardsPerRow||e.cardsPerRow||v))*(t.cardHeight+t.cardSpace)+t.cardSpace,l=e.$parent?t.maxSubColHeight||t.maxColHeight:t.maxColHeight,c=e.$parent?t.minSubColHeight||t.minColHeight:t.minColHeight,u="auto"===l?s:Math.max(c,Math.min(l,s));return t.calcColHeight&&(u=t.calcColHeight(a,e,i,u,n)),u},m=a.lanes||[],l=0;l<m.length;++l){var g=m[l];g.kanban=h,g.$index=l,g.$kanbanData=a,g.$height=0;var k=g.subLanes;if(k){g.$height=0;for(var C=0;C<k.length;++C){var x=k[C];x.kanban=h,x.$parent=g,x.$index=C,x.$kanbanData=a,x.$height=0;for(var y=x.items||x.cards||{},$=0;$<s.length;++$)x.$height=Math.max(x.$height,f(s[$],x,y));g.$height+=x.$height,C>0&&t.subLaneSpace&&(g.$height+=t.subLaneSpace)}}else for(var y=g.items||g.cards||{},C=0;C<s.length;++C)g.$height=Math.max(g.$height,f(s[C],g,y))}},t.prototype.renderKanban=function(e){var n=this;if("number"==typeof e)e=n.data[e];else{var t=n.data.findIndex(function(a){return a.id===e.id});if(t>-1){var r=n.data[t];e=a.extend(r,e),n.data[t]=e}else n.data.push(e)}e.id||(e.id=a.zui.uuid());var i=e.id,d=n.options,o=n.$,s=o.children('.kanban-board[data-id="'+i+'"]');s.length?s.removeClass("kanban-expired"):s=a('<div class="kanban-board" data-id="'+i+'"></div>').appendTo(o),n.layoutKanban(e,s),n.renderKanbanHeader(e,s),s.children(".kanban-lane").addClass("kanban-expired");for(var l=e.lanes||[],c=null,u=0;u<l.length;++u){var h=l[u];n.renderLane(h,c,e.columns,s,e),c=h}s.children(".kanban-expired").remove(),d.onRenderKanban&&d.onRenderKanban(s,e,n)},t.prototype.renderKanbanHeader=function(e,n){var t=this,r=t.options,i=e.$layout.hasParentCol;n=n||t.$.children('.kanban-board[data-id="'+e.id+'"]');var d=n.children(".kanban-header");d.length||(d=a('<div class="kanban-header"><div class="kanban-cols kanban-header-cols"></div></div>').prependTo(n),r.useFlex||d.addClass("clearfix")),d.css("height",(i?2:1)*r.headerHeight).toggleClass("kanban-header-has-parent",!!i);var o=d.children(".kanban-cols");o.css("left",e.$layout.laneNameWidth).children(".kanban-col").addClass("kanban-expired");for(var s=e.columns,l=e.$layout.columnsMap||{},c=null,u=null,h=0;h<s.length;++h){var p=s[h];if(p.asParent)t.renderHeaderParentCol(s[h],o,c,e),c=p,u=null;else if(p.parentType){var b=l[p.parentType];t.renderHeaderCol(s[h],o,b,u,e),u=p}else t.renderHeaderCol(s[h],o,null,c,e),c=p,u=null}o.find(".kanban-expired").remove(),r.onRenderHeader&&r.onRenderHeader(o,e)},t.prototype.renderHeaderParentCol=function(e,n,t,r){var i=this,d=i.options,o=n.children('.kanban-header-parent-col[data-id="'+e.id+'"]'),s=t?n.children('.kanban-header-col[data-id="'+t.id+'"]:not(.kanban-expired)'):null;o.length?o.removeClass("kanban-expired").find(".kanban-header-sub-cols>.kanban-col").addClass("kanban-expired"):o=a(['<div class="kanban-col kanban-header-col kanban-header-parent-col" data-id="'+e.id+'">','<div class="kanban-header-col">','<div class="title">','<i class="icon"></i>','<span class="text"></span>',d.showCount?'<span class="count"></span>':"","</div>","</div>",'<div class="kanban-header-sub-cols">',"</div>","</div>"].join("")),s&&s.length?s.after(o):n.prepend(o),o.data("col",e).attr("data-type",e.type);var l=r.$layout.columnWidth;d.useFlex?o.css("flex",e.subs.length+" "+e.subs.length+" "+l*e.subs.length+"%"):o.css({width:l*e.subs.length+"%",left:e.$index*l+"%"});var c=o.children(".kanban-header-col");c.find(".title>.icon").attr("class","icon icon-"+(e.icon||""));var u=c.find(".title>.text").text(e.name).attr("title",e.name);if(e.color&&u.css("color",e.color),d.showCount){var h=void 0!==e.count?e.count:e.$cardsCount;d.showZeroCount||h||(h="");var p=c.find(".title>.count").text(h);d.onRenderCount&&d.onRenderCount(p,h,e,i)}d.onRenderHeaderCol&&d.onRenderHeaderCol(o,e,n,r)},t.prototype.renderHeaderCol=function(e,n,t,r,i){var d=this,o=d.options;if(e.parentType&&t){var s=n.children('.kanban-header-parent-col[data-id="'+t.id+'"]');n=s.children(".kanban-header-sub-cols")}var l=n.children('.kanban-header-col[data-id="'+e.id+'"]'),c=r?n.children('.kanban-header-col[data-id="'+r.id+'"]:not(.kanban-expired)'):null;l.length?l.removeClass("kanban-expired"):l=a(['<div class="kanban-col kanban-header-col" data-id="'+e.id+'">','<div class="title action-dbc" data-action="editCol">','<i class="icon"></i>','<span class="text"></span>',o.showCount?'<span class="count"></span>':"","</div>",'<div class="actions"></div>',"</div>"].join("")),c&&c.length?c.after(l):n.prepend(l),l.data("col",e).attr("data-type",e.type);var u=t?100/t.subs.length:i.$layout.columnWidth;o.useFlex?l.css("flex","1 1 "+u+"%"):l.css({left:(t?e.$subIndex:e.$index)*u+"%",width:u+"%"}),l.find(".title>.icon").attr("class","icon icon-"+(e.icon||""));var h=l.find(".title>.text").text(e.name).attr("title",e.name);if(e.color&&h.css("color",e.color),o.showCount){var p=void 0!==e.count?e.count:e.$cardsCount;o.showZeroCount||p||(p="");var b=l.find(".title>.count").text(p);o.onRenderCount&&o.onRenderCount(b,p,e,d)}o.onRenderHeaderCol&&o.onRenderHeaderCol(l,e,n,i)},t.prototype.renderLane=function(e,t,r,i,d){var o=this,s=o.options;i=i||o.$.children('.kanban-board[data-id="'+e.kanban+'"]');var l=i.children('.kanban-lane[data-id="'+e.id+'"]'),c=t?i.children('.kanban-lane[data-id="'+t.id+'"]:not(.kanban-expired)'):null;l.length?l.removeClass("kanban-expired"):(l=a('<div class="kanban-lane" data-id="'+e.id+'"></div>'),n||l.addClass("clearfix")),c&&c.length?c.after(l):i.children(".kanban-header").after(l);var u=e.subLanes?e.subLanes.length:0;l.attr("data-index",e.$index).data("lane",e).toggleClass("has-sub-lane",u>0).css({height:e.$height||"auto"}),o.virtualizeRender(d,"lane",l,function(){if(!s.noLaneName){var n=l.children('.kanban-lane-name[data-id="'+e.id+'"]');n.length||(n=a('<div class="kanban-lane-name action-dbc" data-action="editLaneName" data-id="'+e.id+'"></div>').prependTo(l)),n.empty().css("width",s.laneNameWidth).attr("title",e.name).append(a('<span class="text" />').text(e.name)),e.color&&n.css("background-color",e.color),s.onRenderLaneName&&s.onRenderLaneName(n,e,i,r,d)}l.children(".kanban-cols,.kanban-sub-lanes").addClass("kanban-expired");var t;t=e.subLanes?o.renderSubLanes(e,r,l,d):o.renderLaneCols(r,e.items||e.cards||{},l,e,d),s.useFlex||t.css("left",d.$layout.laneNameWidth),l.children(".kanban-expired").remove()},{lane:e,columns:r,kanban:d})},t.prototype.virtualizeRender=function(e,n,t,r,i){var d=this,o=d.virtualize,s=o?o[n]:null;return s?("function"==typeof s&&(s=s(i,t)),"number"==typeof s&&t.height(s),void t.virtualRender(a.extend({render:r},d.options.virtualRenderOptions))):r()},t.prototype.renderSubLanes=function(e,n,t,r){var i=this,d=t.children(".kanban-sub-lanes");d.length?d.removeClass("kanban-expired"):d=a('<div class="kanban-sub-lanes"></div>').appendTo(t),d.children(".kanban-sub-lane").addClass("kanban-expired");for(var o=0;o<e.subLanes.length;++o)i.renderSubLane(e.subLanes[o],n,d,r,o);return d.children(".kanban-expired").remove(),d},t.prototype.renderSubLane=function(e,t,r,i,d){var o=r.children('.kanban-sub-lane[data-id="'+e.id+'"]');o.length?o.removeClass("kanban-expired"):(o=a('<div class="kanban-sub-lane" data-id="'+e.id+'"></div>').appendTo(r),n||o.addClass("clearfix")),o.attr("data-index",d).data("lane",e).css({height:e.$height||"auto"}),o.children(".kanban-col").addClass("kanban-expired");var s=e.items||e.cards;s&&this.renderLaneCols(t,s,o,e,i),o.children(".kanban-expired").remove()},t.prototype.renderLaneCols=function(e,n,t,r,i){var d=this,o=t.children(".kanban-cols");o.length?o.removeClass("kanban-expired"):o=a('<div class="kanban-cols kanban-'+(r.$parent?"sub-":"")+'lane-cols"></div>').appendTo(t),o.children(".kanban-col").addClass("kanban-expired");for(var s=null,l=0;l<e.length;++l){var c=e[l];if(!c.asParent){for(var u=d.renderLaneCol(c,o,s),h=n[c.type]||[],p=0;p<h.length;++p){var b=h[p];b.$index=p,b.order=+b.order,Number.isNaN(b.order)&&(b.order=p)}h.sort(function(a,e){var n=a.order-e.order;return 0!==n?n:a.$index-e.$index}),d.renderColumnCards(c,h,u,r,i),s=c}}return o.children(".kanban-expired").remove(),o},t.prototype.renderColumnCards=function(a,e,n,t,r){var i=this,d=i.options,o=n.find(".kanban-lane-items");o.children(".kanban-item").addClass("kanban-expired");var s=a.cardsPerRow||t.cardsPerRow||r.cardsPerRow||d.cardsPerRow;if(d.virtualCardList){var l=o.data("zui.virtuallist");l?l.render(e,{countPerRow:s,itemHeight:d.cardHeight+d.cardSpace}):o.virtualList({countPerRow:s,itemHeight:d.cardHeight+d.cardSpace,list:e,itemClassName:"kanban-item",render:function(e,n,d){var d=n[e],s=e>0?n[e-1]:null;return d.$index=e,d.$col=a,d.$lane=t,i.renderCard(d,o,s,a,t,r)}})}else for(var c=0;c<e.length;++c){var u=e[c],h=c>0?e[c-1]:null;u.$index=c,u.$col=a,u.$lane=t,i.renderCard(u,o,h,a,t,r)}o.css("padding",d.cardSpace/2).toggleClass("kanban-items-grid",s>1).attr("data-cards-per-row",s).data("cards",e),o.children(".kanban-expired").remove()},t.prototype.renderLaneCol=function(e,n,t){var r=this,i=r.options,d=n.children('.kanban-lane-col[data-id="'+e.id+'"]'),o=t?n.children('.kanban-lane-col[data-id="'+t.id+'"]:not(.kanban-expired)'):null;d.length?d.removeClass("kanban-expired"):(d=a(['<div class="kanban-col kanban-lane-col" data-id="'+e.id+'">','<div class="kanban-lane-items scrollbar-hover"></div>',"</div>"].join("")),r.options.readonly||d.append(['<div class="kanban-lane-actions">','<button class="btn btn-default btn-block action" type="button" data-action="addItem"><span class="text-muted"><i class="icon icon-plus"></i> '+r.options.addItemText+"</span></button>","</div>"].join("")),i.laneItemsClass&&d.find(".kanban-lane-items").addClass(i.laneItemsClass),i.laneColClass&&d.addClass(i.laneColClass)),o&&o.length?o.after(d):n.prepend(d),d.attr({"data-parent":e.parentType?e.parentType:null,"data-type":e.type}).data("col",e);var s=e.$kanbanData.$layout.columnWidth;return i.useFlex?d.css("flex","1 1 "+s+"%"):d.css({left:e.$index*s+"%",width:s+"%"}),d},t.prototype.renderCard=function(e,n,t,r,i,d){var o=this.options,s=n.children('.kanban-item[data-id="'+e.id+'"]'),l=t?n.children('.kanban-item[data-id="'+t.id+'"]:not(.kanban-expired)'):null;s.length?s.removeClass("kanban-expired"):(s=a('<div class="kanban-item" data-id="'+e.id+'"></div>'),o.wrapCard&&s.append('<div class="kanban-card"></div>')),l&&l.length?l.after(s):n.prepend(s);var c=r.cardsPerRow||i.cardsPerRow||d.cardsPerRow||o.cardsPerRow;s.data("item",e).css({padding:o.cardSpace/2,width:c>1?100/c+"%":""});var u=o.wrapCard?s.children(".kanban-card"):s;u.css("height",o.cardHeight);var h=o.cardRender||o.itemRender;if(h)h(e,u,r,i,d);else{var p=u.find(".title");p.length||(p=a('<div class="title"></div>').appendTo(u)),p.text(e.name||e.title)}return s},t.DEFAULTS={minColWidth:100,maxColHeight:400,minColHeight:90,minSubColHeight:40,subLaneSpace:2,laneNameWidth:20,headerHeight:32,cardHeight:40,cardSpace:10,cardsPerRow:1,wrapCard:!0,fluidBoardWidth:!0,addItemText:"添加条目",useFlex:!0,droppable:"auto",laneColClass:"",showCount:!0},a.fn.kanban=function(n){return this.each(function(){var r=a(this),i=r.data(e),d="object"==typeof n&&n;i||r.data(e,i=new t(this,d)),"string"==typeof n&&i[n]()})},t.NAME=e,a.fn.kanban.Constructor=t}(jQuery);