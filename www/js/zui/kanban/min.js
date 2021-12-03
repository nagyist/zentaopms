/*!
 * ZUI: ZUI Kanban View - v1.10.0 - 2021-12-02
 * http://openzui.com
 * GitHub: https://github.com/easysoft/zui.git 
 * Copyright (c) 2021 cnezsoft.com; Licensed MIT
 */
!function(){"use strict";function a(a,e){return n&&!e?requestAnimationFrame(a):setTimeout(a,e||0)}function e(a){return n?cancelAnimationFrame(a):void clearTimeout(a)}var n="function"==typeof window.requestAnimationFrame;$.zui({asap:a,clearAsap:e})}(),function(a){"use strict";function e(e,n){"string"==typeof e&&(e=a(e)),e instanceof a&&(e=e[0]);var t=e.getBoundingClientRect(),r=window.innerHeight||document.documentElement.clientHeight,d=window.innerWidth||document.documentElement.clientWidth;if(n)return t.left>=0&&t.top>=0&&t.left+t.width<=d&&t.top+t.height<=r;var i=t.top<=r&&t.top+t.height>=0,o=t.left<=d&&t.left+t.width>=0;return i&&o}var n="zui.virtualRender",t=function(e,r){"function"==typeof r&&(r={render:r});var d=this;d.name=n,d.$=a(e),d.options=r=a.extend({},t.DEFAULTS,this.$.data(),r),d.rendered=!1;var i=r.container;"function"==typeof i&&(i=i(d));var o=a(i?i:window);d.tryRender()||(d.$container=o,d.scrollListener=d.tryRender.bind(d),r.pendingClass&&d.$.addClass(r.pendingClass),o.on("scroll",d.scrollListener))};t.prototype.tryRender=function(){var n=this;return!(n.rendered||!e(n.$))&&(n.renderTaskID&&a.zui.clearAsap(n.renderTaskID),n.renderTaskID=a.zui.asap(function(){n.renderTaskID=null;var a=n.options.render(n.$);a!==!1&&(n.rendered=!0,n.destroy())},n.options.delay),!0)},t.prototype.destroy=function(){var e=this;e.renderTaskID&&a.zui.clearAsap(e.renderTaskID),e.scrollListener&&(e.$container.off("scroll",e.scrollListener),e.scrollListener=null);var t=e.options.pendingClass;t&&e.$.removeClass(t),e.$.removeData(n)},t.DEFAULTS={pendingClass:"virtual-pending"},a.fn.virtualRender=function(e){return this.each(function(){var r=a(this),d=r.data(n);if(d){if("string"==typeof e)return d[e]();d.destroy()}r.data(n,d=new t(this,e))})},a.zui.isElementInViewport=e}(jQuery),function(a){"use strict";var e="zui.kanban",n="object"==typeof CSS&&CSS.supports("display","flex"),t=function(n,r){var d=this;if(d.name=e,d.$=a(n).addClass("kanban"),r=d.setOptions(a.extend({},t.DEFAULTS,this.$.data(),r)),r.onAction){var i=function(e){var n=a(this);r.onAction(n.data("action"),n,e,d)};d.$.on("click",".action",i).on("dblclick",".action-dbc",i)}if("auto"===r.droppable&&(r.droppable=!r.readonly),r.droppable){var o=0,s={dropOnMouseleave:!0,selector:".kanban-item",target:'.kanban-lane-col:not([data-type="EMPTY"])',drop:function(a){"function"==typeof r.droppable?r.droppable(a):r.onAction&&r.onAction("dropItem",a.element,a,d)},start:function(e){d.$.addClass("kanban-dragging"),o&&clearTimeout(o),o=setTimeout(function(){a(e.shadowElement).addClass("in"),o=0},50)},always:function(){d.$.removeClass("kanban-dragging"),o&&(clearTimeout(o),o=0)}};"object"==typeof r.droppable&&a.extend(s,r.droppable),d.$.droppable(s)}if(!r.useFlex){var l=0;a(window).on("resize",function(){l&&a.zui.clearAsap(l),l=a.zui.asap(function(){l=0,d.adjustSize()})})}r.onCreate&&r.onCreate(d)};t.prototype.setOptions=function(e){var t=this,r=a.extend({},t.options,e);t.options=r,r.useFlex&&!n&&(r.useFlex=!1),t.$.toggleClass("no-flex",!r.useFlex).toggleClass("use-flex",!!r.useFlex);var d=!!a.fn.virtualRender&&r.virtualize;return d&&("object"!=typeof d&&(d={lane:!0}),t.virtualize=a.extend({},d)),t.data=r.data||[],t.render(t.data),r},t.prototype.render=function(a){var e=this;a&&(e.data=a),e.data&&!Array.isArray(e.data)&&(e.data=[e.data]);var t=e.data||[];e.options.beforeRender&&e.options.beforeRender(e,t),e.$.toggleClass("kanban-readonly",!!e.options.readonly).toggleClass("kanban-no-lane-name",!!e.options.noLaneName),e.$.children(".kanban-board").addClass("kanban-expired");for(var r=0;r<t.length;++r)e.renderKanban(r);e.$.children(".kanban-expired").remove(),n&&e.options.useFlex||setTimeout(e.adjustSize.bind(this),200),e.options.onRender&&e.options.onRender(e)},t.prototype.layoutKanban=function(a,e){for(var n=this,t=n.options,r=a.columns,d=t.noLaneName?0:t.laneNameWidth,i=0,o={},s=!1,l=0;l<r.length;++l){var c=r[l];if(c.$cardsCount=0,c.$kanbanData=a,c.$index=i,c.id||(c.id=c.type),c.asParent)s=!0,c.subs=[],o[c.type]=c;else if(i++,c.parentType){var p=o[c.parentType];c.$subIndex=p.subs.length,p.subs.push(c)}}var u=a.id,h=n.$,b=t.minColWidth*i+d;e=e||h.children('.kanban-board[data-id="'+u+'"]'),e.css(t.fluidBoardWidth?"min-width":"width",b),a.$layout={laneNameWidth:d,columnsCount:i,hasParentCol:s,columnsMap:o,columnWidth:100/i};for(var v=a.cardsPerRow||t.cardsPerRow,f=function(a,e,r){if(a.asParent)return 0;r=r||e.items||e.cards||{};var d=r[a.type]||[];if(a.$cardsCount+=d.length,a.parentType){var i=o[a.parentType];i.$cardsCount+=d.length}var s=Math.ceil(d.length/(a.cardsPerRow||e.cardsPerRow||v))*(t.cardHeight+t.cardSpace)+t.cardSpace,l=e.$parent?t.maxSubColHeight||t.maxColHeight:t.maxColHeight,c=e.$parent?t.minSubColHeight||t.minColHeight:t.minColHeight,p="auto"===l?s:Math.max(c,Math.min(l,s));return t.calcColHeight&&(p=t.calcColHeight(a,e,d,p,n)),p},m=a.lanes||[],l=0;l<m.length;++l){var k=m[l];k.kanban=u,k.$index=l,k.$kanbanData=a,k.$height=0;var C=k.subLanes;if(C){k.$height=0;for(var g=0;g<C.length;++g){var x=C[g];x.kanban=u,x.$parent=k,x.$index=g,x.$kanbanData=a,x.$height=0;for(var y=x.items||x.cards||{},$=0;$<r.length;++$)x.$height=Math.max(x.$height,f(r[$],x,y));k.$height+=x.$height,g>0&&t.subLaneSpace&&(k.$height+=t.subLaneSpace)}}else for(var y=k.items||k.cards||{},g=0;g<r.length;++g)k.$height=Math.max(k.$height,f(r[g],k,y))}},t.prototype.renderKanban=function(e){var n=this;if("number"==typeof e)e=n.data[e];else{var t=n.data.findIndex(function(a){return a.id===e.id});if(t>-1){var r=n.data[t];e=a.extend(r,e),n.data[t]=e}else n.data.push(e)}e.id||(e.id=a.zui.uuid());var d=e.id,i=n.options,o=n.$,s=o.children('.kanban-board[data-id="'+d+'"]');s.length?s.removeClass("kanban-expired"):s=a('<div class="kanban-board" data-id="'+d+'"></div>').appendTo(o),n.layoutKanban(e,s),n.renderKanbanHeader(e,s),s.children(".kanban-lane").addClass("kanban-expired");for(var l=e.lanes||[],c=0;c<l.length;++c)n.renderLane(l[c],e.columns,s,e);s.children(".kanban-expired").remove(),i.onRenderKanban&&i.onRenderKanban(s,e,n)},t.prototype.renderKanbanHeader=function(e,n){var t=this,r=t.options,d=e.$layout.hasParentCol;n=n||t.$.children('.kanban-board[data-id="'+e.id+'"]');var i=n.children(".kanban-header");i.length||(i=a('<div class="kanban-header"><div class="kanban-cols kanban-header-cols"></div></div>').prependTo(n),r.useFlex||i.addClass("clearfix")),i.css("height",(d?2:1)*r.headerHeight).toggleClass("kanban-header-has-parent",!!d);var o=i.children(".kanban-cols");o.css("left",e.$layout.laneNameWidth).children(".kanban-col").addClass("kanban-expired");for(var s=e.columns,l=e.$layout.columnsMap||{},c=null,p=null,u=0;u<s.length;++u){var h=s[u];if(h.asParent)t.renderHeaderParentCol(s[u],o,c,e),c=h,p=null;else if(h.parentType){var b=l[h.parentType];t.renderHeaderCol(s[u],o,b,p,e),p=h}else t.renderHeaderCol(s[u],o,null,c,e),c=h,p=null}o.find(".kanban-expired").remove(),r.onRenderHeader&&r.onRenderHeader(o,e)},t.prototype.renderHeaderParentCol=function(e,n,t,r){var d=this,i=d.options,o=n.children('.kanban-header-parent-col[data-id="'+e.id+'"]'),s=t?n.children('.kanban-header-col[data-id="'+t.id+'"]:not(.kanban-expired)'):null;o.length?o.removeClass("kanban-expired").find(".kanban-header-sub-cols>.kanban-col").addClass("kanban-expired"):o=a(['<div class="kanban-col kanban-header-col kanban-header-parent-col" data-id="'+e.id+'">','<div class="kanban-header-col">','<div class="title">','<i class="icon"></i>','<span class="text"></span>',i.showCount?'<span class="count"></span>':"","</div>","</div>",'<div class="kanban-header-sub-cols">',"</div>","</div>"].join("")),s&&s.length?s.after(o):n.prepend(o),o.data("col",e).attr("data-type",e.type);var l=r.$layout.columnWidth;i.useFlex?o.css("flex",e.subs.length+" "+e.subs.length+" "+l*e.subs.length+"%"):o.css({width:l*e.subs.length+"%",left:e.$index*l+"%"});var c=o.children(".kanban-header-col");c.find(".title>.icon").attr("class","icon icon-"+(e.icon||""));var p=c.find(".title>.text").text(e.name);if(e.color&&p.css("color",e.color),i.showCount){var u=void 0!==e.count?e.count:e.$cardsCount;i.showZeroCount||u||(u="");var h=c.find(".title>.count").text(u);i.onRenderCount&&i.onRenderCount(h,u,e,d)}i.onRenderHeaderCol&&i.onRenderHeaderCol(o,e,n,r)},t.prototype.renderHeaderCol=function(e,n,t,r,d){var i=this,o=i.options;if(e.parentType&&t){var s=n.children('.kanban-header-parent-col[data-id="'+t.id+'"]');n=s.children(".kanban-header-sub-cols")}var l=n.children('.kanban-header-col[data-id="'+e.id+'"]'),c=r?n.children('.kanban-header-col[data-id="'+r.id+'"]:not(.kanban-expired)'):null;l.length?l.removeClass("kanban-expired"):l=a(['<div class="kanban-col kanban-header-col" data-id="'+e.id+'">','<div class="title action-dbc" data-action="editCol">','<i class="icon"></i>','<span class="text"></span>',o.showCount?'<span class="count"></span>':"","</div>",'<div class="actions"></div>',"</div>"].join("")),c&&c.length?c.after(l):n.prepend(l),l.data("col",e).attr("data-type",e.type);var p=t?100/t.subs.length:d.$layout.columnWidth;o.useFlex?l.css("flex","1 1 "+p+"%"):l.css({left:(t?e.$subIndex:e.$index)*p+"%",width:p+"%"}),l.find(".title>.icon").attr("class","icon icon-"+(e.icon||""));var u=l.find(".title>.text").text(e.name);if(e.color&&u.css("color",e.color),o.showCount){var h=void 0!==e.count?e.count:e.$cardsCount;o.showZeroCount||h||(h="");var b=l.find(".title>.count").text(h);o.onRenderCount&&o.onRenderCount(b,h,e,i)}o.onRenderHeaderCol&&o.onRenderHeaderCol(l,e,n,d)},t.prototype.renderLane=function(e,t,r,d){var i=this,o=i.options;r=r||i.$.children('.kanban-board[data-id="'+e.kanban+'"]');var s=r.children('.kanban-lane[data-id="'+e.id+'"]');s.length?s.removeClass("kanban-expired"):(s=a('<div class="kanban-lane" data-id="'+e.id+'"></div>').appendTo(r),n||s.addClass("clearfix"));var l=e.subLanes?e.subLanes.length:0;s.attr("data-index",e.$index).data("lane",e).toggleClass("has-sub-lane",l>0).css({height:e.$height}),i.virtualizeRender(d,"lane",s,function(){if(!o.noLaneName){var n=s.children('.kanban-lane-name[data-id="'+e.id+'"]');n.length||(n=a('<div class="kanban-lane-name action-dbc" data-action="editLaneName" data-id="'+e.id+'"></div>').prependTo(s)),n.empty().css("width",o.laneNameWidth).attr("title",e.name).append(a('<span class="text" />').text(e.name)),e.color&&n.css("background-color",e.color),o.onRenderLaneName&&o.onRenderLaneName(n,e,r,t,d)}s.children(".kanban-cols,.kanban-sub-lanes").addClass("kanban-expired");var l;l=e.subLanes?i.renderSubLanes(e,t,s,d):i.renderLaneCols(t,e.items||e.cards||{},s,e,d),o.useFlex||l.css("left",d.$layout.laneNameWidth),s.children(".kanban-expired").remove()},{lane:e,columns:t,kanban:d})},t.prototype.virtualizeRender=function(e,n,t,r,d){var i=this,o=i.virtualize,s=o?o[n]:null;return s?("function"==typeof s&&(s=s(d,t)),"number"==typeof s&&t.height(s),void t.virtualRender(a.extend({render:r},i.options.virtualRenderOptions))):r()},t.prototype.renderSubLanes=function(e,n,t,r){var d=this,i=t.children(".kanban-sub-lanes");i.length?i.removeClass("kanban-expired"):i=a('<div class="kanban-sub-lanes"></div>').appendTo(t),i.children(".kanban-sub-lane").addClass("kanban-expired");for(var o=0;o<e.subLanes.length;++o)d.renderSubLane(e.subLanes[o],n,i,r,o);return i.children(".kanban-expired").remove(),i},t.prototype.renderSubLane=function(e,t,r,d,i){var o=r.children('.kanban-sub-lane[data-id="'+e.id+'"]');o.length?o.removeClass("kanban-expired"):(o=a('<div class="kanban-sub-lane" data-id="'+e.id+'"></div>').appendTo(r),n||o.addClass("clearfix")),o.attr("data-index",i).data("lane",e).css({height:e.$height}),o.children(".kanban-col").addClass("kanban-expired");var s=e.items||e.cards;s&&this.renderLaneCols(t,s,o,e,d),o.children(".kanban-expired").remove()},t.prototype.renderLaneCols=function(e,n,t,r,d){var i=this,o=t.children(".kanban-cols");o.length?o.removeClass("kanban-expired"):o=a('<div class="kanban-cols kanban-'+(r.$parent?"sub-":"")+'lane-cols"></div>').appendTo(t),o.children(".kanban-col").addClass("kanban-expired");for(var s=null,l=0;l<e.length;++l){var c=e[l];if(!c.asParent){var p=i.renderLaneCol(c,o,s),u=n[c.type]||[];i.renderColumnCards(c,u,p,r,d),s=c}}return o.children(".kanban-expired").remove(),o},t.prototype.renderColumnCards=function(a,e,n,t,r){var d=n.find(".kanban-lane-items");d.children(".kanban-item").addClass("kanban-expired");for(var i=0;i<e.length;++i){var o=e[i];o.$index=i,o.$col=a,o.$lane=t,this.renderCard(o,d,a,t,r)}var s=a.cardsPerRow||t.cardsPerRow||r.cardsPerRow||this.options.cardsPerRow;d.css("padding",this.options.cardSpace/2).toggleClass("kanban-items-grid",s>1).attr("data-cards-per-row",s),d.children(".kanban-expired").remove()},t.prototype.renderLaneCol=function(e,n,t){var r=this,d=r.options,i=n.children('.kanban-lane-col[data-id="'+e.id+'"]'),o=t?n.children('.kanban-lane-col[data-id="'+t.id+'"]:not(.kanban-expired)'):null;i.length?i.removeClass("kanban-expired"):(i=a(['<div class="kanban-col kanban-lane-col" data-id="'+e.id+'">','<div class="kanban-lane-items"></div>',"</div>"].join("")),r.options.readonly||i.append(['<div class="kanban-lane-actions">','<button class="btn btn-default btn-block action" type="button" data-action="addItem"><span class="text-muted"><i class="icon icon-plus"></i> '+r.options.addItemText+"</span></button>","</div>"].join("")),d.laneItemsClass&&i.find(".kanban-lane-items").addClass(d.laneItemsClass),d.laneColClass&&i.addClass(d.laneColClass)),o&&o.length?o.after(i):n.prepend(i),i.attr({"data-parent":e.parentType?e.parentType:null,"data-type":e.type}).data("col",e);var s=e.$kanbanData.$layout.columnWidth;return d.useFlex?i.css("flex","1 1 "+s+"%"):i.css({left:e.$index*s+"%",width:s+"%"}),i},t.prototype.renderCard=function(e,n,t,r,d){var i=this.options,o=n.children('.kanban-item[data-id="'+e.id+'"]');o.length?o.removeClass("kanban-expired"):(o=a('<div class="kanban-item" data-id="'+e.id+'"></div>').appendTo(n),i.wrapCard&&o.append('<div class="kanban-card"></div>'));var s=t.cardsPerRow||r.cardsPerRow||d.cardsPerRow||i.cardsPerRow;o.data("item",e).css({padding:i.cardSpace/2,width:s>1?100/s+"%":""});var l=i.wrapCard?o.children(".kanban-card"):o;l.css("height",i.cardHeight);var c=i.cardRender||i.itemRender;if(c)c(e,l,t,r,d);else{var p=l.find(".title");p.length||(p=a('<div class="title"></div>').appendTo(l)),p.text(e.name||e.title)}return l},t.prototype.tryAdjustKanbanSize=function(a,e,n){},t.prototype.adjustKanbanSize=function(a,e){},t.prototype.adjustLaneSize=function(a,e){},t.prototype.adjustSize=function(){},t.DEFAULTS={minColWidth:100,maxColHeight:400,minColHeight:90,minSubColHeight:40,subLaneSpace:2,laneNameWidth:20,headerHeight:32,cardHeight:40,cardSpace:10,cardsPerRow:1,wrapCard:!0,fluidBoardWidth:!0,addItemText:"添加条目",createColumnText:"添加看板列",useFlex:!0,cardRender:null,calcColHeight:null,droppable:"auto",readonly:!1,laneColClass:"",noLaneName:!1,showCount:!0,showZeroCount:!1,virtualize:!1},a.fn.kanban=function(n){return this.each(function(){var r=a(this),d=r.data(e),i="object"==typeof n&&n;d||r.data(e,d=new t(this,i)),"string"==typeof n&&d[n]()})},t.NAME=e,a.fn.kanban.Constructor=t}(jQuery);