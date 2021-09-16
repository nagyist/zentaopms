/*!
 * ZUI: ZUI for Zentao - v1.9.2 - 2021-09-08
 * http://openzui.com
 * GitHub: https://github.com/easysoft/zui.git 
 * Copyright (c) 2021 cnezsoft.com; Licensed MIT
 */
!function (t, e, i) {
    "use strict";
    if ("undefined" == typeof t) throw new Error("ZUI requires jQuery");
    t.zui || (t.zui = function (e) {
        t.isPlainObject(e) && t.extend(t.zui, e)
    });
    var n = {all: -1, left: 0, middle: 1, right: 2}, o = 0;
    t.zui({
        uuid: function (t) {
            var e = 1e5 * (Date.now() - 1580890015292) + 10 * Math.floor(1e4 * Math.random()) + o++ % 10;
            return t ? e : e.toString(36)
        }, callEvent: function (t, e, n) {
            if ("function" == typeof t) {
                n !== i && (t = t.bind(n));
                var o = t(e);
                return e && (e.result = o), !(o !== i && !o)
            }
            return 1
        }, strCode: function (t) {
            var e = 0;
            if ("string" != typeof t && (t = String(t)), t && t.length) for (var i = 0; i < t.length; ++i) e += (i + 1) * t.charCodeAt(i);
            return e
        }, getMouseButtonCode: function (t) {
            return "number" != typeof t && (t = n[t]), t !== i && null !== t || (t = -1), t
        }, defaultLang: "en", clientLang: function () {
            var i, n = e.config;
            if ("undefined" != typeof n && n.clientLang && (i = n.clientLang), !i) {
                var o = t("html").attr("lang");
                i = o ? o : navigator.userLanguage || navigator.userLanguage || t.zui.defaultLang
            }
            return i.replace("-", "_").toLowerCase()
        }, langDataMap: {}, addLangData: function (e, i, n) {
            var o = {};
            n && i && e ? (o[i] = {}, o[i][e] = n) : e && i && !n ? (n = i, t.each(n, function (t) {
                o[t] = {}, o[t][e] = n[t]
            })) : !e || i || n || t.each(n, function (e) {
                var i = n[e];
                t.each(i, function (t) {
                    o[t] || (o[t] = {}), o[t][e] = i[t]
                })
            }), t.extend(!0, t.zui.langDataMap, o)
        }, getLangData: function (e, i, n) {
            if (!arguments.length) return t.extend({}, t.zui.langDataMap);
            if (1 === arguments.length) return t.extend({}, t.zui.langDataMap[e]);
            if (2 === arguments.length) {
                var o = t.zui.langDataMap[e];
                return o ? i ? o[i] : o : {}
            }
            if (3 === arguments.length) {
                i = i || t.zui.clientLang();
                var o = t.zui.langDataMap[e], a = o ? o[i] : {};
                return t.extend(!0, {}, n[i] || n.en || n.zh_cn, a)
            }
            return null
        }, lang: function () {
            return arguments.length && t.isPlainObject(arguments[arguments.length - 1]) ? t.zui.addLangData.apply(null, arguments) : t.zui.getLangData.apply(null, arguments)
        }, _scrollbarWidth: 0, checkBodyScrollbar: function () {
            if (document.body.clientWidth >= e.innerWidth) return 0;
            if (!t.zui._scrollbarWidth) {
                var i = document.createElement("div");
                i.className = "scrollbar-measure", document.body.appendChild(i), t.zui._scrollbarWidth = i.offsetWidth - i.clientWidth, document.body.removeChild(i)
            }
            return t.zui._scrollbarWidth
        }, fixBodyScrollbar: function () {
            if (t.zui.checkBodyScrollbar()) {
                var e = t("body"), i = parseInt(e.css("padding-right") || 0, 10);
                return t.zui._scrollbarWidth && e.css({
                    paddingRight: i + t.zui._scrollbarWidth,
                    overflowY: "hidden"
                }), !0
            }
        }, resetBodyScrollbar: function () {
            t("body").css({paddingRight: "", overflowY: ""})
        }
    }), t.fn.callEvent = function (e, n, o) {
        var a = t(this), s = e.indexOf(".zui."), r = s < 0 ? e : e.substring(0, s), l = t.Event(r, n);
        if (o === i && s > 0 && (o = a.data(e.substring(s + 1))), o && o.options) {
            var h = o.options[r];
            "function" == typeof h && (l.result = t.zui.callEvent(h, l, o))
        }
        return a.trigger(l), l
    }, t.fn.callComEvent = function (t, e, n) {
        n === i || Array.isArray(n) || (n = [n]);
        var o, a = this;
        a.trigger(e, n);
        var s = t.options[e];
        return s && (o = s.apply(t, n)), o
    }
}(jQuery, window, void 0), function (t) {
    "use strict";
    t.fn.fixOlPd = function (e) {
        return e = e || 10, this.each(function () {
            var i = t(this);
            i.css("paddingLeft", Math.ceil(Math.log10(i.children().length)) * e + 10)
        })
    }, t(function () {
        t(".ol-pd-fix,.article ol").fixOlPd()
    })
}(jQuery), +function (t) {
    "use strict";
    var e = '[data-dismiss="alert"]', i = "zui.alert", n = function (i) {
        t(i).on("click", e, this.close)
    };
    n.prototype.close = function (e) {
        function n() {
            s.trigger("closed." + i).remove()
        }

        var o = t(this), a = o.attr("data-target");
        a || (a = o.attr("href"), a = a && a.replace(/.*(?=#[^\s]*$)/, ""));
        var s = t(a);
        e && e.preventDefault(), s.length || (s = o.hasClass("alert") ? o : o.parent()), s.trigger(e = t.Event("close." + i)), e.isDefaultPrevented() || (s.removeClass("in"), t.support.transition && s.hasClass("fade") ? s.one(t.support.transition.end, n).emulateTransitionEnd(150) : n())
    };
    var o = t.fn.alert;
    t.fn.alert = function (e) {
        return this.each(function () {
            var o = t(this), a = o.data(i);
            a || o.data(i, a = new n(this)), "string" == typeof e && a[e].call(o)
        })
    }, t.fn.alert.Constructor = n, t.fn.alert.noConflict = function () {
        return t.fn.alert = o, this
    }, t(document).on("click." + i + ".data-api", e, n.prototype.close)
}(window.jQuery), function (t, e) {
    "use strict";
    var i = "zui.pager", n = {page: 1, recTotal: 0, recPerPage: 10}, o = {
        zh_cn: {
            pageOfText: "第 {0} 页",
            prev: "上一页",
            next: "下一页",
            first: "第一页",
            last: "最后一页",
            "goto": "跳转",
            pageOf: "第 <strong>{page}</strong> 页",
            totalPage: "共 <strong>{totalPage}</strong> 页",
            totalCount: "共 <strong>{recTotal}</strong> 项",
            pageSize: "每页 <strong>{recPerPage}</strong> 项",
            itemsRange: "第 <strong>{start}</strong> ~ <strong>{end}</strong> 项",
            pageOfTotal: "第 <strong>{page}</strong>/<strong>{totalPage}</strong> 页"
        },
        zh_tw: {
            pageOfText: "第 {0} 頁",
            prev: "上一頁",
            next: "下一頁",
            first: "第一頁",
            last: "最後一頁",
            "goto": "跳轉",
            pageOf: "第 <strong>{page}</strong> 頁",
            totalPage: "共 <strong>{totalPage}</strong> 頁",
            totalCount: "共 <strong>{recTotal}</strong> 項",
            pageSize: "每頁 <strong>{recPerPage}</strong> 項",
            itemsRange: "第 <strong>{start}</strong> ~ <strong>{end}</strong> 項",
            pageOfTotal: "第 <strong>{page}</strong>/<strong>{totalPage}</strong> 頁"
        },
        en: {
            pageOfText: "Page {0}",
            prev: "Prev",
            next: "Next",
            first: "First",
            last: "Last",
            "goto": "Goto",
            pageOf: "Page <strong>{page}</strong>",
            totalPage: "<strong>{totalPage}</strong> pages",
            totalCount: "Total: <strong>{recTotal}</strong> items",
            pageSize: "<strong>{recPerPage}</strong> per page",
            itemsRange: "From <strong>{start}</strong> to <strong>{end}</strong>",
            pageOfTotal: "Page <strong>{page}</strong> of <strong>{totalPage}</strong>"
        }
    }, a = function (e, n) {
        var s = this;
        s.name = i, s.$ = t(e), n = s.options = t.extend({}, a.DEFAULTS, this.$.data(), n), s.langName = n.lang || t.zui.clientLang(), s.lang = t.zui.getLangData(i, s.langName, o), s.state = {}, s.set(n.page, n.recTotal, n.recPerPage, !0), s.$.on("click", ".pager-goto-btn", function () {
            var e = t(this).closest(".pager-goto"), i = parseInt(e.find(".pager-goto-input").val());
            NaN !== i && s.set(i)
        }).on("click", ".pager-item", function () {
            var e = t(this).data("page");
            "number" == typeof e && e > 0 && s.set(e)
        }).on("click", ".pager-size-menu [data-size]", function () {
            var e = t(this).data("size");
            "number" == typeof e && e > 0 && s.set(-1, -1, e)
        })
    };
    a.prototype.set = function (e, i, o, a) {
        var s = this;
        "object" == typeof e && null !== e && (o = e.recPerPage, i = e.recTotal, e = e.page);
        var r = s.state;
        r || (r = t.extend({}, n));
        var l = t.extend({}, r);
        return "number" == typeof o && o > 0 && (r.recPerPage = o), "number" == typeof i && i >= 0 && (r.recTotal = i), "number" == typeof e && e >= 0 && (r.page = e), r.totalPage = r.recTotal && r.recPerPage ? Math.ceil(r.recTotal / r.recPerPage) : 1, r.page = Math.max(0, Math.min(r.page, r.totalPage)), r.pageRecCount = r.recTotal, r.page && r.recTotal && (r.page < r.totalPage ? r.pageRecCount = r.recPerPage : r.page > 1 && (r.pageRecCount = r.recTotal - r.recPerPage * (r.page - 1))), r.skip = r.page > 1 ? (r.page - 1) * r.recPerPage : 0, r.start = r.skip + 1, r.end = r.skip + r.pageRecCount, r.prev = r.page > 1 ? r.page - 1 : 0, r.next = r.page < r.totalPage ? r.page + 1 : 0, s.state = r, a || l.page === r.page && l.recTotal === r.recTotal && l.recPerPage === r.recPerPage || s.$.callComEvent(s, "onPageChange", [r, l]), s.render()
    }, a.prototype.createLinkItem = function (i, n, o) {
        var a = this;
        n === e && (n = i);
        var s = t('<a title="' + a.lang.pageOfText.format(i) + '" class="pager-item" data-page="' + i + '"/>').attr("href", i ? a.createLink(i, a.state) : "###").html(n);
        return o || (s = t("<li />").append(s).toggleClass("active", i === a.state.page).toggleClass("disabled", !i || i === a.state.page)), s
    }, a.prototype.createNavItems = function (t) {
        var i = this, n = i.$, o = i.state, a = o.totalPage, s = o.page, r = function (t, o) {
            if (t === !1) return void n.append(i.createLinkItem(0, o || i.options.navEllipsisItem));
            o === e && (o = t);
            for (var a = t; a <= o; ++a) n.append(i.createLinkItem(a))
        };
        t === e && (t = i.options.maxNavCount || 10), r(1), a > 1 && (a <= t ? r(2, a) : s < t - 2 ? (r(2, t - 2), r(!1), r(a)) : s > a - t + 2 ? (r(!1), r(a - t + 2, a)) : (r(!1), r(s - Math.ceil((t - 4) / 2), s + Math.floor((t - 4) / 2)), r(!1), r(a)))
    }, a.prototype.createGoto = function () {
        var e = this, i = this.state,
            n = t('<div class="input-group pager-goto" style="width: ' + (35 + 9 * (i.page + "").length + 25 + 12 * e.lang["goto"].length) + 'px"><input value="' + i.page + '" type="number" min="1" max="' + i.totalPage + '" placeholder="' + i.page + '" class="form-control pager-goto-input"><span class="input-group-btn"><button class="btn pager-goto-btn" type="button">' + e.lang["goto"] + "</button></span></div>");
        return n
    }, a.prototype.createSizeMenu = function () {
        var e = this, i = this.state, n = t('<ul class="dropdown-menu"></ul>'), o = e.options.pageSizeOptions;
        "string" == typeof o && (o = o.split(","));
        for (var a = 0; a < o.length; ++a) {
            var s = o[a];
            "string" == typeof s && (s = parseInt(s));
            var r = t('<li><a href="###" data-size="' + s + '">' + s + "</a></li>").toggleClass("active", s === i.recPerPage);
            n.append(r)
        }
        return t('<div class="btn-group pager-size-menu"><button type="button" class="btn dropdown-toggle" data-toggle="dropdown">' + e.lang.pageSize.format(i) + ' <span class="caret"></span></button></div>').addClass(e.options.menuDirection).append(n)
    }, a.prototype.createElement = function (e, i, n) {
        var o = this, a = o.createLinkItem.bind(o), s = o.lang;
        switch (e) {
            case"prev":
                return a(n.prev, s.prev);
            case"prev_icon":
                return a(n.prev, '<i class="icon ' + o.options.prevIcon + '"></i>');
            case"next":
                return a(n.next, s.next);
            case"next_icon":
                return a(n.next, '<i class="icon ' + o.options.nextIcon + '"></i>');
            case"first":
                return a(1, s.first);
            case"first_icon":
                return a(1, '<i class="icon ' + o.options.firstIcon + '"></i>');
            case"last":
                return a(n.totalPage, s.last);
            case"last_icon":
                return a(n.totalPage, '<i class="icon ' + o.options.lastIcon + '"></i>');
            case"space":
            case"|":
                return t('<li class="space" />');
            case"nav":
            case"pages":
                return void o.createNavItems();
            case"total_text":
                return t(('<div class="pager-label">' + s.totalCount + "</div>").format(n));
            case"page_text":
                return t(('<div class="pager-label">' + s.pageOf + "</div>").format(n));
            case"total_page_text":
                return t(('<div class="pager-label">' + s.totalPage + "</div>").format(n));
            case"page_of_total_text":
                return t(('<div class="pager-label">' + s.pageOfTotal + "</div>").format(n));
            case"page_size_text":
                return t(('<div class="pager-label">' + s.pageSize + "</div>").format(n));
            case"items_range_text":
                return t(('<div class="pager-label">' + s.itemsRange + "</div>").format(n));
            case"goto":
                return o.createGoto();
            case"size_menu":
                return o.createSizeMenu();
            default:
                return t("<li/>").html(e.format(n))
        }
    }, a.prototype.createLink = function (i, n) {
        i === e && (i = this.state.page), n === e && (n = this.state);
        var o = this.options.linkCreator;
        return "string" == typeof o ? o.format(t.extend({}, n, {page: i})) : "function" == typeof o ? o(i, n) : "#page=" + i
    }, a.prototype.render = function (e) {
        var i = this, n = i.state, o = i.options.elementCreator || i.createElement, a = t.isPlainObject(o);
        e = e || i.elements || i.options.elements, "string" == typeof e && (e = e.split(",")), i.elements = e, i.$.empty();
        for (var s = 0; s < e.length; ++s) {
            var r = t.trim(e[s]), l = a ? o[r] || o : o, h = l.call(i, r, i.$, n);
            h === !1 && (h = i.createElement(r, i.$, n)), h instanceof t && ("LI" !== h[0].tagName && (h = t("<li/>").append(h)), i.$.append(h))
        }
        var c = null;
        return i.$.children("li").each(function () {
            var e = t(this), i = !!e.children(".pager-item").length;
            c ? c.toggleClass("pager-item-right", !i) : i && e.addClass("pager-item-left"), c = i ? e : null
        }), c && c.addClass("pager-item-right"), i.$.callComEvent(i, "onRender", [n]), i
    }, a.DEFAULTS = t.extend({
        elements: ["first_icon", "prev_icon", "pages", "next_icon", "last_icon", "page_of_total_text", "items_range_text", "total_text"],
        prevIcon: "icon-double-angle-left",
        nextIcon: "icon-double-angle-right",
        firstIcon: "icon-step-backward",
        lastIcon: "icon-step-forward",
        navEllipsisItem: '<i class="icon icon-ellipsis-h"></i>',
        maxNavCount: 10,
        menuDirection: "dropdown",
        pageSizeOptions: [10, 20, 30, 50, 100]
    }, n), t.fn.pager = function (e) {
        return this.each(function () {
            var n = t(this), o = n.data(i), s = "object" == typeof e && e;
            o || n.data(i, o = new a(this, s)), "string" == typeof e && o[e]()
        })
    }, a.NAME = i, a.LANG = o, t.fn.pager.Constructor = a, t(function () {
        t('[data-ride="pager"]').pager()
    })
}(jQuery, void 0), +function (t) {
    "use strict";
    var e = "zui.tab", i = function (e) {
        this.element = t(e)
    };
    i.prototype.show = function () {
        var i = this.element, n = i.closest("ul:not(.dropdown-menu)"), o = i.attr("data-target") || i.attr("data-tab");
        if (o || (o = i.attr("href"), o = o && o.replace(/.*(?=#[^\s]*$)/, "")), !i.parent("li").hasClass("active")) {
            var a = n.find(".active:last a")[0], s = t.Event("show." + e, {relatedTarget: a});
            if (i.trigger(s), !s.isDefaultPrevented()) {
                var r = t(o);
                this.activate(i.parent("li"), n), this.activate(r, r.parent(), function () {
                    i.trigger({type: "shown." + e, relatedTarget: a})
                })
            }
        }
    }, i.prototype.activate = function (e, i, n) {
        function o() {
            a.removeClass("active").find("> .dropdown-menu > .active").removeClass("active"), e.addClass("active"), s ? (e[0].offsetWidth, e.addClass("in")) : e.removeClass("fade"), e.parent(".dropdown-menu") && e.closest("li.dropdown").addClass("active"), n && n()
        }

        var a = i.find("> .active"), s = n && t.support.transition && a.hasClass("fade");
        s ? a.one(t.support.transition.end, o).emulateTransitionEnd(150) : o(), a.removeClass("in")
    };
    var n = t.fn.tab;
    t.fn.tab = function (n) {
        return this.each(function () {
            var o = t(this), a = o.data(e);
            a || o.data(e, a = new i(this)), "string" == typeof n && a[n]()
        })
    }, t.fn.tab.Constructor = i, t.fn.tab.noConflict = function () {
        return t.fn.tab = n, this
    }, t(document).on("click.zui.tab.data-api", '[data-toggle="tab"], [data-tab]', function (e) {
        e.preventDefault(), t(this).tab("show")
    })
}(window.jQuery), +function (t) {
    "use strict";

    function e() {
        var t = document.createElement("bootstrap"), e = {
            WebkitTransition: "webkitTransitionEnd",
            MozTransition: "transitionend",
            OTransition: "oTransitionEnd otransitionend",
            transition: "transitionend"
        };
        for (var i in e) if (void 0 !== t.style[i]) return {end: e[i]};
        return !1
    }

    t.fn.emulateTransitionEnd = function (e) {
        var i = !1, n = this;
        t(this).one("bsTransitionEnd", function () {
            i = !0
        });
        var o = function () {
            i || t(n).trigger(t.support.transition.end)
        };
        return setTimeout(o, e), this
    }, t(function () {
        t.support.transition = e(), t.support.transition && (t.event.special.bsTransitionEnd = {
            bindType: t.support.transition.end,
            delegateType: t.support.transition.end,
            handle: function (e) {
                if (t(e.target).is(this)) return e.handleObj.handler.apply(this, arguments)
            }
        })
    })
}(jQuery), +function (t) {
    "use strict";
    var e = "zui.collapse", i = function (e, n) {
        this.$element = t(e), this.options = t.extend({}, i.DEFAULTS, n), this.transitioning = null, this.options.parent && (this.$parent = t(this.options.parent)), this.options.toggle && this.toggle()
    };
    i.DEFAULTS = {toggle: !0}, i.prototype.dimension = function () {
        var t = this.$element.hasClass("width");
        return t ? "width" : "height"
    }, i.prototype.show = function () {
        if (!this.transitioning && !this.$element.hasClass("in")) {
            var i = t.Event("show." + e);
            if (this.$element.trigger(i), !i.isDefaultPrevented()) {
                var n = this.$parent && this.$parent.find(".in");
                if (n && n.length) {
                    var o = n.data(e);
                    if (o && o.transitioning) return;
                    n.collapse("hide"), o || n.data(e, null)
                }
                var a = this.dimension();
                this.$element.removeClass("collapse").addClass("collapsing")[a](0), this.transitioning = 1;
                var s = function () {
                    this.$element.removeClass("collapsing").addClass("in")[a]("auto"), this.transitioning = 0, this.$element.trigger("shown." + e)
                };
                if (!t.support.transition) return s.call(this);
                var r = t.camelCase(["scroll", a].join("-"));
                this.$element.one(t.support.transition.end, s.bind(this)).emulateTransitionEnd(350)[a](this.$element[0][r])
            }
        }
    }, i.prototype.hide = function () {
        if (!this.transitioning && this.$element.hasClass("in")) {
            var i = t.Event("hide." + e);
            if (this.$element.trigger(i), !i.isDefaultPrevented()) {
                var n = this.dimension();
                this.$element[n](this.$element[n]())[0].offsetHeight, this.$element.addClass("collapsing").removeClass("collapse").removeClass("in"), this.transitioning = 1;
                var o = function () {
                    this.transitioning = 0, this.$element.trigger("hidden." + e).removeClass("collapsing").addClass("collapse")
                };
                return t.support.transition ? void this.$element[n](0).one(t.support.transition.end, o.bind(this)).emulateTransitionEnd(350) : o.call(this)
            }
        }
    }, i.prototype.toggle = function () {
        this[this.$element.hasClass("in") ? "hide" : "show"]()
    };
    var n = t.fn.collapse;
    t.fn.collapse = function (n) {
        return this.each(function () {
            var o = t(this), a = o.data(e), s = t.extend({}, i.DEFAULTS, o.data(), "object" == typeof n && n);
            a || o.data(e, a = new i(this, s)), "string" == typeof n && a[n]()
        })
    }, t.fn.collapse.Constructor = i, t.fn.collapse.noConflict = function () {
        return t.fn.collapse = n, this
    }, t(document).on("click." + e + ".data-api", "[data-toggle=collapse]", function (i) {
        var n, o = t(this),
            a = o.attr("data-target") || i.preventDefault() || (n = o.attr("href")) && n.replace(/.*(?=#[^\s]+$)/, ""),
            s = t(a), r = s.data(e), l = r ? "toggle" : o.data(), h = o.attr("data-parent"), c = h && t(h);
        r && r.transitioning || (c && c.find('[data-toggle=collapse][data-parent="' + h + '"]').not(o).addClass("collapsed"), o[s.hasClass("in") ? "addClass" : "removeClass"]("collapsed")), s.collapse(l)
    })
}(window.jQuery), function (t, e) {
    "use strict";
    var i = 1200, n = 992, o = 768, a = e(t), s = function () {
        var t = a.width();
        e("html").toggleClass("screen-desktop", t >= n && t < i).toggleClass("screen-desktop-wide", t >= i).toggleClass("screen-tablet", t >= o && t < n).toggleClass("screen-phone", t < o).toggleClass("device-mobile", t < n).toggleClass("device-desktop", t >= n)
    }, r = "", l = navigator.userAgent;
    l.match(/(iPad|iPhone|iPod)/i) ? r += " os-ios" : l.match(/android/i) ? r += " os-android" : l.match(/Win/i) ? r += " os-windows" : l.match(/Mac/i) ? r += " os-mac" : l.match(/Linux/i) ? r += " os-linux" : l.match(/X11/i) && (r += " os-unix"), "ontouchstart" in document.documentElement && (r += " is-touchable"), e("html").addClass(r), a.resize(s), s()
}(window, jQuery), function (t) {
    "use strict";
    var e = {
        zh_cn: '您的浏览器版本过低，无法体验所有功能，建议升级或者更换浏览器。 <a href="https://browsehappy.com/" target="_blank" class="alert-link">了解更多...</a>',
        zh_tw: '您的瀏覽器版本過低，無法體驗所有功能，建議升級或者更换瀏覽器。<a href="https://browsehappy.com/" target="_blank" class="alert-link">了解更多...</a>',
        en: 'Your browser is too old, it has been unable to experience the colorful internet. We strongly recommend that you upgrade a better one. <a href="https://browsehappy.com/" target="_blank" class="alert-link">Learn more...</a>'
    }, i = function () {
        for (var t = !1, e = 11; e > 5; e--) if (this.isIE(e)) {
            t = e;
            break
        }
        this.ie = t, this.cssHelper()
    };
    i.prototype.cssHelper = function () {
        var e = this.ie, i = t("html");
        i.toggleClass("ie", e).removeClass("ie-6 ie-7 ie-8 ie-9 ie-10"), e && i.addClass("ie-" + e).toggleClass("gt-ie-7 gte-ie-8 support-ie", e >= 8).toggleClass("lte-ie-7 lt-ie-8 outdated-ie", e < 8).toggleClass("gt-ie-8 gte-ie-9", e >= 9).toggleClass("lte-ie-8 lt-ie-9", e < 9).toggleClass("gt-ie-9 gte-ie-10", e >= 10).toggleClass("lte-ie-9 lt-ie-10", e < 10).toggleClass("gt-ie-10 gte-ie-11", e >= 11).toggleClass("lte-ie-10 lt-ie-11", e < 11)
    }, i.prototype.tip = function (i) {
        var n = t("#browseHappyTip");
        n.length || (n = t('<div id="browseHappyTip" class="alert alert-dismissable alert-danger-inverse alert-block" style="position: relative; z-index: 99999"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><div class="container"><div class="content text-center"></div></div></div>'), n.prependTo("body")), i || (i = t.zui.getLangData("zui.browser", t.zui.clientLang(), e), "object" == typeof i && (i = i.tip)), n.find(".content").html(i)
    }, i.prototype.isIE = function (t) {
        if (11 === t) return this.isIE11();
        if (10 === t) return this.isIE10();
        if (!t && (this.isIE11() || this.isIE10())) return !0;
        var e = document.createElement("b");
        return e.innerHTML = "<!--[if IE " + (t || "") + "]><i></i><![endif]-->", 1 === e.getElementsByTagName("i").length
    }, i.prototype.isIE10 = function () {
        return navigator.appVersion.indexOf("MSIE 10") !== -1
    }, i.prototype.isIE11 = function () {
        var t = navigator.userAgent;
        return t.indexOf("Trident") !== -1 && t.indexOf("rv:11") !== -1
    }, t.zui({browser: new i}), t(function () {
        t("body").hasClass("disabled-browser-tip") || t.zui.browser.ie && t.zui.browser.ie < 8 && t.zui.browser.tip()
    })
}(jQuery), function (t) {
    "use strict";
    var e = 864e5, i = function (t) {
        return t instanceof Date || ("number" == typeof t && t < 1e10 && (t *= 1e3), t = new Date(t)), t
    }, n = function (t) {
        return i(t).getTime()
    }, o = function (t, e) {
        t = i(t), void 0 === e && (e = "yyyy-MM-dd hh:mm:ss");
        var n = {
            "M+": t.getMonth() + 1,
            "d+": t.getDate(),
            "h+": t.getHours(),
            "m+": t.getMinutes(),
            "s+": t.getSeconds(),
            "q+": Math.floor((t.getMonth() + 3) / 3),
            "S+": t.getMilliseconds()
        };
        /(y+)/i.test(e) && (e = e.replace(RegExp.$1, (t.getFullYear() + "").substr(4 - RegExp.$1.length)));
        for (var o in n) new RegExp("(" + o + ")").test(e) && (e = e.replace(RegExp.$1, 1 == RegExp.$1.length ? n[o] : ("00" + n[o]).substr(("" + n[o]).length)));
        return e
    }, a = function (t, e) {
        return t.setTime(t.getTime() + e), t
    }, s = function (t, i) {
        return a(t, i * e)
    }, r = function (t) {
        return new Date(i(t).getTime())
    }, l = function (t) {
        return t % 4 === 0 && t % 100 !== 0 || t % 400 === 0
    }, h = function (t, e) {
        return [31, l(t) ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][e]
    }, c = function (t) {
        return h(t.getFullYear(), t.getMonth())
    }, d = function (t) {
        return t.setHours(0), t.setMinutes(0), t.setSeconds(0), t.setMilliseconds(0), t
    }, u = function (t, e) {
        var i = t.getDate();
        return t.setDate(1), t.setMonth(t.getMonth() + e), t.setDate(Math.min(i, c(t))), t
    }, f = function (t, e) {
        e = e || 1;
        for (var i = new Date(t.getTime()); i.getDay() != e;) i = s(i, -1);
        return d(i)
    }, p = function (t, e) {
        return t.toDateString() === e.toDateString()
    }, g = function (t, e) {
        var i = f(t), n = s(r(i), 7);
        return e >= i && e < n
    }, m = function (t, e) {
        return t.getFullYear() === e.getFullYear()
    }, v = {
        formatDate: o,
        createDate: i,
        date: {
            ONEDAY_TICKS: e,
            create: i,
            getTimestamp: n,
            format: o,
            addMilliseconds: a,
            addDays: s,
            cloneDate: r,
            isLeapYear: l,
            getDaysInMonth: h,
            getDaysOfThisMonth: c,
            clearTime: d,
            addMonths: u,
            getLastWeekday: f,
            isSameDay: p,
            isSameWeek: g,
            isSameYear: m
        }
    };
    t.$ && t.$.zui ? $.zui(v) : t.dateHelper = v.date, t.noDatePrototypeHelper || (Date.ONEDAY_TICKS = e, Date.prototype.format || (Date.prototype.format = function (t) {
        return o(this, t)
    }), Date.prototype.addMilliseconds || (Date.prototype.addMilliseconds = function (t) {
        return a(this, t)
    }), Date.prototype.addDays || (Date.prototype.addDays = function (t) {
        return s(this, t)
    }), Date.prototype.clone || (Date.prototype.clone = function () {
        return r(this)
    }), Date.isLeapYear || (Date.isLeapYear = function (t) {
        return l(t)
    }), Date.getDaysInMonth || (Date.getDaysInMonth = function (t, e) {
        return h(t, e)
    }), Date.prototype.isLeapYear || (Date.prototype.isLeapYear = function () {
        return l(this.getFullYear())
    }), Date.prototype.clearTime || (Date.prototype.clearTime = function () {
        return d(this)
    }), Date.prototype.getDaysInMonth || (Date.prototype.getDaysInMonth = function () {
        return c(this)
    }), Date.prototype.addMonths || (Date.prototype.addMonths = function (t) {
        return u(this, t)
    }), Date.prototype.getLastWeekday || (Date.prototype.getLastWeekday = function (t) {
        return f(this, t)
    }), Date.prototype.isSameDay || (Date.prototype.isSameDay = function (t) {
        return p(t, this)
    }), Date.prototype.isSameWeek || (Date.prototype.isSameWeek = function (t) {
        return g(t, this)
    }), Date.prototype.isSameYear || (Date.prototype.isSameYear = function (t) {
        return m(this, t)
    }), Date.create || (Date.create = function (t) {
        return i(t)
    }), Date.timestamp || (Date.timestamp = function (t) {
        return n(t)
    }))
}(window), function () {
    "use strict";
    var t = function (t, e) {
        if (arguments.length > 1) {
            var i;
            if (2 == arguments.length && "object" == typeof e) for (var n in e) void 0 !== e[n] && (i = new RegExp("({" + n + "})", "g"), t = t.replace(i, e[n])); else for (var o = 1; o < arguments.length; o++) void 0 !== arguments[o] && (i = new RegExp("({[" + (o - 1) + "]})", "g"), t = t.replace(i, arguments[o]))
        }
        return t
    }, e = function (t) {
        if (null !== t) {
            var e, i;
            return i = /\d*/i, e = t.match(i), e == t
        }
        return !1
    }, i = {formatString: t, string: {format: t, isNum: e}};
    window.$ && window.$.zui ? $.zui(i) : window.stringHelper = i.string, window.noStringPrototypeHelper || (String.prototype.format || (String.prototype.format = function () {
        var e = [].slice.call(arguments);
        return e.unshift(this), t.apply(this, e)
    }), String.prototype.isNum || (String.prototype.isNum = function () {
        return e(this)
    }), String.prototype.endsWith || (String.prototype.endsWith = function (t, e) {
        return (void 0 === e || e > this.length) && (e = this.length), this.substring(e - t.length, e) === t
    }), String.prototype.startsWith || Object.defineProperty(String.prototype, "startsWith", {
        value: function (t, e) {
            return e = !e || e < 0 ? 0 : +e, this.substring(e, e + t.length) === t
        }
    }), String.prototype.includes || (String.prototype.includes = function () {
        return String.prototype.indexOf.apply(this, arguments) !== -1
    }))
}(),/*!
 * jQuery resize event - v1.1
 * http://benalman.com/projects/jquery-resize-plugin/
 * Copyright (c) 2010 "Cowboy" Ben Alman
 * MIT & GPL http://benalman.com/about/license/
 */
    function (t, e, i) {
        "$:nomunge";

        function n() {
            o = e[r](function () {
                a.each(function () {
                    var e = t(this), i = e.width(), n = e.height(), o = t.data(this, h);
                    i === o.w && n === o.h || e.trigger(l, [o.w = i, o.h = n])
                }), n()
            }, s[c])
        }

        var o, a = t([]), s = t.resize = t.extend(t.resize, {}), r = "setTimeout", l = "resize",
            h = l + "-special-event", c = "delay", d = "throttleWindow";
        s[c] = 250, s[d] = !0, t.event.special[l] = {
            setup: function () {
                if (!s[d] && this[r]) return !1;
                var e = t(this);
                a = a.add(e), t.data(this, h, {w: e.width(), h: e.height()}), 1 === a.length && n()
            }, teardown: function () {
                if (!s[d] && this[r]) return !1;
                var e = t(this);
                a = a.not(e), e.removeData(h), a.length || clearTimeout(o)
            }, add: function (e) {
                function n(e, n, a) {
                    var s = t(this), r = t.data(this, h) || {};
                    r.w = n !== i ? n : s.width(), r.h = a !== i ? a : s.height(), o.apply(this, arguments)
                }

                if (!s[d] && this[r]) return !1;
                var o;
                return "function" == typeof e ? (o = e, n) : (o = e.handler, void (e.handler = n))
            }
        }
    }(jQuery, this),/*!
 * jQuery Cookie Plugin v1.4.1
 * https://github.com/carhartl/jquery-cookie
 * Copyright 2006, 2014 Klaus Hartl
 * Released under the MIT license
 */
    function (t) {
        "function" == typeof define && define.amd ? define(["jquery"], t) : t("object" == typeof exports ? require("jquery") : jQuery)
    }(function (t) {
        function e(t) {
            return r.raw ? t : encodeURIComponent(t)
        }

        function i(t) {
            return r.raw ? t : decodeURIComponent(t)
        }

        function n(t) {
            return e(r.json ? JSON.stringify(t) : String(t))
        }

        function o(t) {
            0 === t.indexOf('"') && (t = t.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, "\\"));
            try {
                return t = decodeURIComponent(t.replace(s, " ")), r.json ? JSON.parse(t) : t
            } catch (e) {
            }
        }

        function a(t, e) {
            var i = r.raw ? t : o(t);
            return "function" == typeof e ? e(i) : i
        }

        var s = /\+/g, r = t.cookie = function (o, s, l) {
            if (void 0 !== s && "function" != typeof s) {
                if (l = t.extend({}, r.defaults, l), "number" == typeof l.expires) {
                    var h = l.expires, c = l.expires = new Date;
                    c.setTime(+c + 864e5 * h)
                }
                return document.cookie = [e(o), "=", n(s), l.expires ? "; expires=" + l.expires.toUTCString() : "", l.path ? "; path=" + l.path : "", l.domain ? "; domain=" + l.domain : "", l.secure ? "; secure" : ""].join("")
            }
            for (var d = o ? void 0 : {}, u = document.cookie ? document.cookie.split("; ") : [], f = 0, p = u.length; f < p; f++) {
                var g = u[f].split("="), m = i(g.shift()), v = g.join("=");
                if (o && o === m) {
                    d = a(v, s);
                    break
                }
                o || void 0 === (v = a(v)) || (d[m] = v)
            }
            return d
        };
        r.defaults = {}, t.removeCookie = function (e, i) {
            return void 0 !== t.cookie(e) && (t.cookie(e, "", t.extend({}, i, {expires: -1})), !t.cookie(e))
        }
    }), function (t, e) {
    "use strict";
    var i, n, o = "localStorage", a = "page_" + t.location.pathname + t.location.search, s = function () {
        this.silence = !0;
        try {
            o in t && t[o] && t[o].setItem && (this.enable = !0, i = t[o])
        } catch (s) {
        }
        this.enable || (n = {}, i = {
            getLength: function () {
                var t = 0;
                return e.each(n, function () {
                    t++
                }), t
            }, key: function (t) {
                var i, o = 0;
                return e.each(n, function (e) {
                    return o === t ? (i = e, !1) : void o++
                }), i
            }, removeItem: function (t) {
                delete n[t]
            }, getItem: function (t) {
                return n[t]
            }, setItem: function (t, e) {
                n[t] = e
            }, clear: function () {
                n = {}
            }
        }), this.storage = i, this.page = this.get(a, {})
    };
    s.prototype.pageSave = function () {
        if (e.isEmptyObject(this.page)) this.remove(a); else {
            var t, i = [];
            for (t in this.page) {
                var n = this.page[t];
                null === n && i.push(t)
            }
            for (t = i.length - 1; t >= 0; t--) delete this.page[i[t]];
            this.set(a, this.page)
        }
    }, s.prototype.pageRemove = function (t) {
        "undefined" != typeof this.page[t] && (this.page[t] = null, this.pageSave())
    }, s.prototype.pageClear = function () {
        this.page = {}, this.pageSave()
    }, s.prototype.pageGet = function (t, e) {
        var i = this.page[t];
        return void 0 === e || null !== i && void 0 !== i ? i : e
    }, s.prototype.pageSet = function (t, i) {
        e.isPlainObject(t) ? e.extend(!0, this.page, t) : this.page[this.serialize(t)] = i, this.pageSave()
    }, s.prototype.check = function () {
        if (!this.enable && !this.silence) throw new Error("Browser not support localStorage or enable status been set true.");
        return this.enable
    }, s.prototype.length = function () {
        return this.check() ? i.getLength ? i.getLength() : i.length : 0
    }, s.prototype.removeItem = function (t) {
        return i.removeItem(t), this
    }, s.prototype.remove = function (t) {
        return this.removeItem(t)
    }, s.prototype.getItem = function (t) {
        return i.getItem(t)
    }, s.prototype.get = function (t, e) {
        var i = this.deserialize(this.getItem(t));
        return "undefined" != typeof i && null !== i || "undefined" == typeof e ? i : e
    }, s.prototype.key = function (t) {
        return i.key(t)
    }, s.prototype.setItem = function (t, e) {
        return i.setItem(t, e), this
    }, s.prototype.set = function (t, e) {
        return void 0 === e ? this.remove(t) : (this.setItem(t, this.serialize(e)), this)
    }, s.prototype.clear = function () {
        return i.clear(), this
    }, s.prototype.forEach = function (t) {
        for (var e = this.length(), n = e - 1; n >= 0; n--) {
            var o = i.key(n);
            t(o, this.get(o))
        }
        return this
    }, s.prototype.getAll = function () {
        var t = {};
        return this.forEach(function (e, i) {
            t[e] = i
        }), t
    }, s.prototype.serialize = function (t) {
        return "string" == typeof t ? t : JSON.stringify(t)
    }, s.prototype.deserialize = function (t) {
        if ("string" == typeof t) try {
            return JSON.parse(t)
        } catch (e) {
            return t || void 0
        }
    }, e.zui({store: new s})
}(window, jQuery), function (t) {
    "use strict";
    var e = "zui.searchBox", i = function (e, n) {
        var o = this;
        o.name = name, o.$ = t(e), o.options = n = t.extend({}, i.DEFAULTS, o.$.data(), n);
        var a = o.$.is(n.inputSelector) ? o.$ : o.$.find(n.inputSelector);
        if (a.length) {
            var s = function () {
                o.changeTimer && (clearTimeout(o.changeTimer), o.changeTimer = null)
            }, r = function () {
                s();
                var t = o.getSearch();
                if (t !== o.lastValue) {
                    var e = "" === t;
                    a.toggleClass("empty", e), o.$.callComEvent(o, "onSearchChange", [t, e]), o.lastValue = t
                }
            };
            o.$input = a = a.first(), a.on(n.listenEvent, function (t) {
                o.changeTimer = setTimeout(function () {
                    r()
                }, n.changeDelay)
            }).on("focus", function (t) {
                a.addClass("focus"), o.$.callComEvent(o, "onFocus", [t])
            }).on("blur", function (t) {
                a.removeClass("focus"), o.$.callComEvent(o, "onBlur", [t])
            }).on("keydown", function (t) {
                var e = 0, i = t.which;
                27 === i && n.escToClear ? (this.setSearch("", !0), r(), e = 1) : 13 === i && n.onPressEnter && (r(), o.$.callComEvent(o, "onPressEnter", [t]));
                var a = o.$.callComEvent(o, "onKeyDown", [t]);
                a === !1 && (e = 1), e && t.preventDefault()
            }), o.$.on("click", ".search-clear-btn", function (t) {
                o.setSearch("", !0), r(), o.focus(), t.preventDefault()
            }), r()
        } else console.error("ZUI: search box init error, cannot find search box input element.")
    };
    i.DEFAULTS = {
        inputSelector: 'input[type="search"],input[type="text"]',
        listenEvent: "change input paste",
        changeDelay: 500
    }, i.prototype.getSearch = function () {
        return this.$input && t.trim(this.$input.val())
    }, i.prototype.setSearch = function (t, e) {
        var i = this.$input;
        i && (i.val(t), e || i.trigger("change"))
    }, i.prototype.focus = function () {
        this.$input && this.$input.focus()
    }, t.fn.searchBox = function (n) {
        return this.each(function () {
            var o = t(this), a = o.data(e), s = "object" == typeof n && n;
            a || o.data(e, a = new i(this, s)), "string" == typeof n && a[n]()
        })
    }, i.NAME = e, t.fn.searchBox.Constructor = i
}(jQuery), function (t, e) {
    "use strict";
    var i = "zui.draggable", n = {container: "body", move: !0}, o = 0, a = function (e, i) {
        var a = this;
        a.$ = t(e), a.id = o++, a.options = t.extend({}, n, a.$.data(), i), a.init()
    };
    a.DEFAULTS = n, a.NAME = i, a.prototype.init = function () {
        var n, o, a, s, r, l = this, h = l.$, c = "before", d = "drag", u = "finish", f = "." + i + "." + l.id,
            p = "mousedown" + f, g = "mouseup" + f, m = "mousemove" + f, v = l.options, y = v.selector, b = v.handle,
            w = h, x = "function" == typeof v.move, C = function (t) {
                var e = t.pageX, i = t.pageY;
                r = !0;
                var o = {left: e - a.x, top: i - a.y};
                w.removeClass("drag-ready").addClass("dragging"), v.move && (x ? v.move(o, w) : w.css(o)), v[d] && v[d]({
                    event: t,
                    element: w,
                    startOffset: a,
                    pos: o,
                    offset: {x: e - n.x, y: i - n.y},
                    smallOffset: {x: e - s.x, y: i - s.y}
                }), s.x = e, s.y = i, v.stopPropagation && t.stopPropagation()
            }, _ = function (i) {
                if (t(e).off(f), !r) return void w.removeClass("drag-ready");
                var o = {left: i.pageX - a.x, top: i.pageY - a.y};
                w.removeClass("drag-ready dragging"), v.move && (x ? v.move(o, w) : w.css(o)), v[u] && v[u]({
                    event: i,
                    element: w,
                    startOffset: a,
                    pos: o,
                    offset: {x: i.pageX - n.x, y: i.pageY - n.y},
                    smallOffset: {x: i.pageX - s.x, y: i.pageY - s.y}
                }), i.preventDefault(), v.stopPropagation && i.stopPropagation()
            }, k = function (i) {
                var l = t.zui.getMouseButtonCode(v.mouseButton);
                if (!(l > -1 && i.button !== l)) {
                    var h = t(this);
                    if (y && (w = b ? h.closest(y) : h), v[c]) {
                        var d = v[c]({event: i, element: w});
                        if (d === !1) return
                    }
                    var u = t(v.container), f = w.offset();
                    o = u.offset(), n = {x: i.pageX, y: i.pageY}, a = {
                        x: i.pageX - f.left + o.left,
                        y: i.pageY - f.top + o.top
                    }, s = t.extend({}, n), r = !1, w.addClass("drag-ready"), i.preventDefault(), v.stopPropagation && i.stopPropagation(), t(e).on(m, C).on(g, _)
                }
            };
        b ? h.on(p, b, k) : y ? h.on(p, y, k) : h.on(p, k)
    }, a.prototype.destroy = function () {
        var n = "." + i + "." + this.id;
        this.$.off(n), t(e).off(n), this.$.data(i, null)
    }, t.fn.draggable = function (e) {
        return this.each(function () {
            var n = t(this), o = n.data(i), s = "object" == typeof e && e;
            o || n.data(i, o = new a(this, s)), "string" == typeof e && o[e]()
        })
    }, t.fn.draggable.Constructor = a
}(jQuery, document), function (t, e, i) {
    "use strict";
    var n = "zui.droppable",
        o = {target: ".droppable-target", deviation: 5, sensorOffsetX: 0, sensorOffsetY: 0, dropToClass: "drop-to"},
        a = 0, s = function (e, i) {
            var n = this;
            n.id = a++, n.$ = t(e), n.options = t.extend({}, o, n.$.data(), i), n.init()
        };
    s.DEFAULTS = o, s.NAME = n, s.prototype.trigger = function (e, i) {
        return t.zui.callEvent(this.options[e], i, this)
    }, s.prototype.init = function () {
        var o, a, s, r, l, h, c, d, u, f, p, g, m, v = this, y = v.$, b = v.options, w = b.deviation,
            x = "." + n + "." + v.id, C = "mousedown" + x, _ = "mouseup" + x, k = "mousemove" + x, T = b.selector,
            S = b.handle, D = b.flex, M = b.container, P = b.canMoveHere, z = b.dropToClass, L = y, F = !1,
            I = M ? t(b.container).first() : T ? y : t("body"), $ = function (e) {
                if (F && (p = {left: e.pageX, top: e.pageY}, !(i.abs(p.left - d.left) < w && i.abs(p.top - d.top) < w))) {
                    if (null === s) {
                        var n = I.css("position");
                        "absolute" != n && "relative" != n && "fixed" != n && (h = n, I.css("position", "relative")), s = L.clone().removeClass("drag-from").addClass("drag-shadow").css({
                            position: "absolute",
                            width: L.outerWidth(),
                            transition: "none"
                        }).appendTo(I), L.addClass("dragging"), v.trigger("start", {
                            event: e,
                            element: L,
                            shadowElement: s,
                            targets: o
                        })
                    }
                    var c = {left: p.left - f.left, top: p.top - f.top}, m = {left: c.left - u.left, top: c.top - u.top};
                    s.css(m), t.extend(g, p);
                    var y = !1;
                    r = !1, D || o.removeClass(z);
                    var x = null;
                    if (o.each(function () {
                        var e = t(this), i = e.offset(), n = e.outerWidth(), o = e.outerHeight(),
                            a = i.left + b.sensorOffsetX, s = i.top + b.sensorOffsetY;
                        if (p.left > a && p.top > s && p.left < a + n && p.top < s + o && (x && x.removeClass(z), x = e, !b.nested)) return !1
                    }), x) {
                        r = !0;
                        var C = x.data("id");
                        L.data("id") != C && (l = !1), (null === a || a.data("id") !== C && !l) && (y = !0), a = x, D && o.removeClass(z), a.addClass(z)
                    }
                    D ? null !== a && a.length && (r = !0) : (L.toggleClass("drop-in", r), s.toggleClass("drop-in", r)), P && P(L, a) === !1 || v.trigger("drag", {
                        event: e,
                        isIn: r,
                        target: a,
                        element: L,
                        isNew: y,
                        selfTarget: l,
                        clickOffset: f,
                        offset: c,
                        position: {left: c.left - u.left, top: c.top - u.top},
                        mouseOffset: p
                    }), e.preventDefault()
                }
            }, A = function (i) {
                if (t(e).off(x), clearTimeout(m), F) {
                    if (F = !1, h && I.css("position", h), null === s) return L.removeClass("drag-from"), void v.trigger("always", {
                        event: i,
                        cancel: !0
                    });
                    r || (a = null);
                    var n = !0;
                    p = i ? {left: i.pageX, top: i.pageY} : g;
                    var c = {left: p.left - f.left, top: p.top - f.top}, d = {left: p.left - g.left, top: p.top - g.top};
                    g.left = p.left, g.top = p.top;
                    var y = {
                        event: i,
                        isIn: r,
                        target: a,
                        element: L,
                        isNew: !l && null !== a,
                        selfTarget: l,
                        offset: c,
                        mouseOffset: p,
                        position: {left: c.left - u.left, top: c.top - u.top},
                        lastMouseOffset: g,
                        moveOffset: d
                    };
                    n = v.trigger("beforeDrop", y), n && r && v.trigger("drop", y), o.removeClass(z), L.removeClass("dragging").removeClass("drag-from"), s.remove(), s = null, v.trigger("finish", y), v.trigger("always", y), i && i.preventDefault()
                }
            }, E = function (i) {
                var n = t.zui.getMouseButtonCode(b.mouseButton);
                if (!(n > -1 && i.button !== n)) {
                    var p = t(this);
                    T && (L = S ? p.closest(T) : p), L.hasClass("drag-shadow") || b.before && b.before({
                        event: i,
                        element: L
                    }) === !1 || (F = !0, o = "function" == typeof b.target ? b.target(L, y) : I.find(b.target), a = null, s = null, r = !1, l = !0, h = null, c = L.offset(), u = I.offset(), u.top = u.top - I.scrollTop(), u.left = u.left - I.scrollLeft(), d = {
                        left: i.pageX,
                        top: i.pageY
                    }, g = t.extend({}, d), f = {
                        left: d.left - c.left,
                        top: d.top - c.top
                    }, L.addClass("drag-from"), t(e).on(k, $).on(_, A), m = setTimeout(function () {
                        t(e).on(C, A)
                    }, 10), i.preventDefault(), b.stopPropagation && i.stopPropagation())
                }
            };
        S ? y.on(C, S, E) : T ? y.on(C, T, E) : y.on(C, E)
    }, s.prototype.destroy = function () {
        var i = "." + n + "." + this.id;
        this.$.off(i), t(e).off(i), this.$.data(n, null)
    }, s.prototype.reset = function () {
        this.destroy(), this.init()
    }, t.fn.droppable = function (e) {
        return this.each(function () {
            var i = t(this), o = i.data(n), a = "object" == typeof e && e;
            o || i.data(n, o = new s(this, a)), "string" == typeof e && o[e]()
        })
    }, t.fn.droppable.Constructor = s
}(jQuery, document, Math), +function (t, e) {
    "use strict";

    function i(e, i, a) {
        return this.each(function () {
            var s = t(this), r = s.data(n), l = t.extend({}, o.DEFAULTS, s.data(), "object" == typeof e && e);
            r || s.data(n, r = new o(this, l)), "string" == typeof e ? r[e](i, a) : l.show && r.show(i, a)
        })
    }

    var n = "zui.modal", o = function (i, o) {
        var a = this;
        a.options = o, a.$body = t(document.body), a.$element = t(i), a.$backdrop = a.isShown = null, a.scrollbarWidth = 0, o.moveable === e && (a.options.moveable = a.$element.hasClass("modal-moveable")), o.remote && a.$element.find(".modal-content").load(o.remote, function () {
            a.$element.trigger("loaded." + n)
        }), o.scrollInside && t(window).on("resize." + n, function () {
            a.isShown && a.adjustPosition(e, 100)
        })
    };
    o.VERSION = "3.2.0", o.TRANSITION_DURATION = 300, o.BACKDROP_TRANSITION_DURATION = 150, o.DEFAULTS = {
        backdrop: !0,
        keyboard: !0,
        show: !0,
        position: "fit"
    };
    var a = function (e, i) {
        var n = t(window);
        i.left = Math.max(0, Math.min(i.left, n.width() - e.outerWidth())), i.top = Math.max(0, Math.min(i.top, n.height() - e.outerHeight())), e.css(i)
    };
    o.prototype.toggle = function (t, e) {
        return this.isShown ? this.hide() : this.show(t, e)
    }, o.prototype.adjustPosition = function (i, o) {
        var s = this;
        if (clearTimeout(s.reposTask), o) return void (s.reposTask = setTimeout(s.adjustPosition.bind(s, i, 0), o));
        var r = s.options;
        if (i === e && (i = r.position), i !== e && null !== i) {
            "function" == typeof i && (i = i(s));
            var l = s.$element.find(".modal-dialog"), h = t(window).height(),
                c = {maxHeight: "initial", overflow: "visible"}, d = l.find(".modal-body").css(c);
            if (r.scrollInside && d.length) {
                var u = r.headerHeight, f = r.footerHeight, p = l.find(".modal-header"), g = l.find(".modal-footer");
                "number" != typeof u && (u = p.length ? p.outerHeight() : "function" == typeof u ? u(p) : 0), "number" != typeof f && (f = g.length ? g.outerHeight() : "function" == typeof f ? f(g) : 0), c.maxHeight = h - u - f, c.overflow = d[0].scrollHeight > c.maxHeight ? "auto" : "visible", d.css(c)
            }
            var m = Math.max(0, (h - l.outerHeight()) / 2);
            if ("fit" === i ? i = {top: m > 50 ? Math.floor(2 * m / 3) : m} : "center" === i ? i = {top: m} : t.isPlainObject(i) || (i = {top: i}), l.hasClass("modal-moveable")) {
                var v = null, y = r.rememberPos;
                y && (y === !0 ? v = s.$element.data("modal-pos") : t.zui.store && (v = t.zui.store.pageGet(n + ".rememberPos." + y))), i = t.extend(i, {left: Math.max(0, (t(window).width() - l.outerWidth()) / 2)}, v), "inside" === r.moveable ? a(l, i) : l.css(i)
            } else l.css(i)
        }
    }, o.prototype.setMoveable = function () {
        t.fn.draggable || console.error("Moveable modal requires draggable.js.");
        var e = this, i = e.options, o = e.$element.find(".modal-dialog").removeClass("modal-dragged");
        o.toggleClass("modal-moveable", !!i.moveable), e.$element.data("modal-moveable-setup") || o.draggable({
            container: e.$element,
            handle: ".modal-header",
            before: function () {
                var t = o.css("margin-top");
                t && "0px" !== t && o.css("top", t).css("margin-top", "").addClass("modal-dragged")
            },
            finish: function (o) {
                var a = i.rememberPos;
                a && (e.$element.data("modal-pos", o.pos), t.zui.store && a !== !0 && t.zui.store.pageSet(n + ".rememberPos." + a, o.pos))
            },
            move: "inside" !== i.moveable || function (t) {
                a(o, t)
            }
        })
    }, o.prototype.show = function (e, i) {
        var a = this, s = t.Event("show." + n, {relatedTarget: e});
        a.$element.trigger(s), a.$element.toggleClass("modal-scroll-inside", !!a.options.scrollInside), a.isShown || s.isDefaultPrevented() || (a.isShown = !0, a.options.moveable && a.setMoveable(), a.options.backdrop !== !1 && (a.setScrollbar(), a.$body.addClass("modal-open")), a.escape(), a.$element.on("click.dismiss." + n, '[data-dismiss="modal"]', function (t) {
            a.hide(), t.stopPropagation()
        }), a.backdrop(function () {
            var s = t.support.transition && a.$element.hasClass("fade");
            a.$element.parent().length || a.$element.appendTo(a.$body), a.$element.show().scrollTop(0), s && a.$element[0].offsetWidth, a.$element.addClass("in").attr("aria-hidden", !1), a.adjustPosition(i), a.enforceFocus();
            var r = t.Event("shown." + n, {relatedTarget: e});
            s ? a.$element.find(".modal-dialog").one("bsTransitionEnd", function () {
                a.$element.trigger("focus").trigger(r)
            }).emulateTransitionEnd(o.TRANSITION_DURATION) : a.$element.trigger("focus").trigger(r)
        }))
    }, o.prototype.hide = function (e) {
        e && e.preventDefault && e.preventDefault();
        var i = this;
        e = t.Event("hide." + n), i.$element.trigger(e), i.isShown && !e.isDefaultPrevented() && (i.isShown = !1, i.options.backdrop !== !1 && (i.$body.removeClass("modal-open"), i.resetScrollbar()), i.escape(), t(document).off("focusin." + n), i.$element.removeClass("in").attr("aria-hidden", !0).off("click.dismiss." + n), t.support.transition && i.$element.hasClass("fade") ? i.$element.one("bsTransitionEnd", i.hideModal.bind(i)).emulateTransitionEnd(o.TRANSITION_DURATION) : i.hideModal())
    }, o.prototype.enforceFocus = function () {
        t(document).off("focusin." + n).on("focusin." + n, function (t) {
            this.$element[0] === t.target || this.$element.has(t.target).length || this.$element.trigger("focus")
        }.bind(this))
    }, o.prototype.escape = function () {
        this.isShown && this.options.keyboard ? t(document).on("keydown.dismiss." + n, function (i) {
            if (27 == i.which) {
                var o = t.Event("escaping." + n), a = this.$element.triggerHandler(o, "esc");
                if (a != e && !a) return;
                this.hide()
            }
        }.bind(this)) : this.isShown || t(document).off("keydown.dismiss." + n)
    }, o.prototype.hideModal = function () {
        var t = this;
        this.$element.hide(), this.backdrop(function () {
            t.$element.trigger("hidden." + n)
        })
    }, o.prototype.removeBackdrop = function () {
        this.$backdrop && this.$backdrop.remove(), this.$backdrop = null
    }, o.prototype.backdrop = function (e) {
        var i = this, a = this.$element.hasClass("fade") ? "fade" : "";
        if (this.isShown && this.options.backdrop) {
            var s = t.support.transition && a;
            if (this.$backdrop = t('<div class="modal-backdrop ' + a + '" />').appendTo(this.$body), this.$element.on("mousedown.dismiss." + n, function (t) {
                t.target === t.currentTarget && ("static" == this.options.backdrop ? this.$element[0].focus.call(this.$element[0]) : this.hide.call(this))
            }.bind(this)), s && this.$backdrop[0].offsetWidth, this.$backdrop.addClass("in"), !e) return;
            s ? this.$backdrop.one("bsTransitionEnd", e).emulateTransitionEnd(o.BACKDROP_TRANSITION_DURATION) : e()
        } else if (!this.isShown && this.$backdrop) {
            this.$backdrop.removeClass("in");
            var r = function () {
                i.removeBackdrop(), e && e()
            };
            t.support.transition && this.$element.hasClass("fade") ? this.$backdrop.one("bsTransitionEnd", r).emulateTransitionEnd(o.BACKDROP_TRANSITION_DURATION) : r()
        } else e && e()
    }, o.prototype.setScrollbar = function () {
        t.zui.fixBodyScrollbar() && this.options.onSetScrollbar && this.options.onSetScrollbar()
    }, o.prototype.resetScrollbar = function () {
        t.zui.resetBodyScrollbar(), this.options.onSetScrollbar && this.options.onSetScrollbar("")
    }, o.prototype.measureScrollbar = function () {
        var t = document.createElement("div");
        t.className = "modal-scrollbar-measure", this.$body.append(t);
        var e = t.offsetWidth - t.clientWidth;
        return this.$body[0].removeChild(t), e
    };
    var s = t.fn.modal;
    t.fn.modal = i, t.fn.modal.Constructor = o, t.fn.modal.noConflict = function () {
        return t.fn.modal = s, this
    }, t(document).on("click." + n + ".data-api", '[data-toggle="modal"]', function (e) {
        var o = t(this), a = o.attr("href"), s = null;
        try {
            s = t(o.attr("data-target") || a && a.replace(/.*(?=#[^\s]+$)/, ""))
        } catch (r) {
            return
        }
        if (s.length) {
            var l = s.data(n) ? "toggle" : t.extend({remote: !/#/.test(a) && a}, s.data(), o.data());
            o.is("a") && e.preventDefault(), s.one("show." + n, function (t) {
                t.isDefaultPrevented() || s.one("hidden." + n, function () {
                    o.is(":visible") && o.trigger("focus")
                })
            }), i.call(s, l, this, o.data("position"))
        }
    })
}(jQuery, void 0), function (t, e, i) {
    "use strict";
    if (!t.fn.modal) throw new Error("Modal trigger requires modal.js");
    var n = "zui.modaltrigger", o = "ajax", a = ".zui.modal", s = "string", r = function (e, i) {
        e = t.extend({}, r.DEFAULTS, t.ModalTriggerDefaults, i ? i.data() : null, e), this.isShown, this.$trigger = i, this.options = e, this.id = t.zui.uuid(), e.show && this.show()
    };
    r.DEFAULTS = {
        type: "custom",
        height: "auto",
        name: "triggerModal",
        fade: !0,
        position: "fit",
        showHeader: !0,
        delay: 0,
        backdrop: !0,
        keyboard: !0,
        waittime: 0,
        loadingIcon: "icon-spinner-indicator",
        scrollInside: !1
    }, r.prototype.initOptions = function (i) {
        if (i.url && (!i.type || i.type != o && "iframe" != i.type) && (i.type = o), i.remote) i.type = o, typeof i.remote === s && (i.url = i.remote); else if (i.iframe) i.type = "iframe", typeof i.iframe === s && (i.url = i.iframe); else if (i.custom && (i.type = "custom", typeof i.custom === s)) {
            var n;
            try {
                n = t(i.custom)
            } catch (a) {
            }
            n && n.length ? i.custom = n : "function" == typeof e[i.custom] && (i.custom = e[i.custom])
        }
        return i
    }, r.prototype.init = function (e) {
        var i = this, o = t("#" + e.name);
        o.length && (i.isShown || o.off(a), o.remove()), o = t('<div id="' + e.name + '" class="modal modal-trigger ' + (e.className || "") + '">' + ("string" == typeof e.loadingIcon && 0 === e.loadingIcon.indexOf("icon-") ? '<div class="icon icon-spin loader ' + e.loadingIcon + '"></div>' : e.loadingIcon) + '<div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button class="close" data-dismiss="modal">×</button><h4 class="modal-title"><i class="modal-icon"></i> <span class="modal-title-name"></span></h4></div><div class="modal-body"></div></div></div></div>').appendTo("body").data(n, i);
        var s = function (t, i, n) {
            n = n || e[t], "function" == typeof n && o.on(i + a, n)
        };
        s("onShow", "show"), s("shown", "shown"), s("onHide", "hide", function (t) {
            if ("iframe" === e.type && i.$iframeBody) {
                var n = i.$iframeBody.triggerHandler("modalhide" + a, [i]);
                n === !1 && t.preventDefault()
            }
            var o = e.onHide;
            if (o) return o(t)
        }), s("hidden", "hidden"), s("loaded", "loaded"), o.on("shown" + a, function () {
            i.isShown = !0
        }).on("hidden" + a, function () {
            i.isShown = !1
        }), this.$modal = o, this.$dialog = o.find(".modal-dialog"), e.mergeOptions && (this.options = e)
    }, r.prototype.show = function (i) {
        var a = this,
            l = t.extend({}, r.DEFAULTS, a.options, {url: a.$trigger ? a.$trigger.attr("href") || a.$trigger.attr("data-url") || a.$trigger.data("url") : a.options.url}, i),
            h = a.isShown;
        l = a.initOptions(l), h || a.init(l);
        var c = a.$modal, d = c.find(".modal-dialog"), u = l.custom,
            f = d.find(".modal-body").css("padding", "").toggleClass("load-indicator loading", !!h),
            p = d.find(".modal-header"), g = d.find(".modal-content");
        c.toggleClass("fade", l.fade).addClass(l.className).toggleClass("modal-loading", !h).toggleClass("modal-scroll-inside", !!l.scrollInside), d.toggleClass("modal-md", "md" === l.size).toggleClass("modal-sm", "sm" === l.size).toggleClass("modal-lg", "lg" === l.size).toggleClass("modal-fullscreen", "fullscreen" === l.size), p.toggle(l.showHeader), p.find(".modal-icon").attr("class", "modal-icon icon-" + l.icon), p.find(".modal-title-name").text(l.title || ""), l.size && "fullscreen" === l.size && (l.width = "", l.height = "");
        var m = function () {
            clearTimeout(this.resizeTask), this.resizeTask = setTimeout(function () {
                a.adjustPosition(l.position)
            }, 100)
        }, v = function (t, e) {
            return "undefined" == typeof t && (t = l.delay), setTimeout(function () {
                d = c.find(".modal-dialog"), l.width && "auto" != l.width && d.css("width", l.width), l.height && "auto" != l.height && (d.css("height", l.height), "iframe" === l.type && f.css("height", d.height() - p.outerHeight())), a.adjustPosition(l.position), c.removeClass("modal-loading").removeClass("modal-updating"), h && f.removeClass("loading"), "iframe" != l.type && (f = d.off("resize." + n).find(".modal-body").off("resize." + n), l.scrollInside && (f = f.children().off("resize." + n)), (f.length ? f : d).on("resize." + n, m)), e && e()
            }, t)
        };
        if ("custom" === l.type && u) if ("function" == typeof u) {
            var y = u({modal: c, options: l, modalTrigger: a, ready: v});
            typeof y === s && (f.html(y), v())
        } else u instanceof t ? (f.html(t("<div>").append(u.clone()).html()), v()) : (f.html(u), v()); else if (l.url) {
            var b = function () {
                var t = c.callComEvent(a, "broken");
                "string" == typeof t && f.html(t), v()
            };
            if (c.attr("ref", l.url), "iframe" === l.type) {
                c.addClass("modal-iframe"), this.firstLoad = !0;
                var w = "iframe-" + l.name;
                p.detach(), f.detach(), g.empty().append(p).append(f), f.css("padding", 0).html('<iframe id="' + w + '" name="' + w + '" src="' + l.url + '" frameborder="no"  allowfullscreen="true" mozallowfullscreen="true" webkitallowfullscreen="true"  allowtransparency="true" scrolling="auto" style="width: 100%; height: 100%; left: 0px;"></iframe>'), l.waittime > 0 && (a.waitTimeout = v(l.waittime, b));
                var x = document.getElementById(w);
                x.onload = x.onreadystatechange = function (i) {
                    var o = !!l.scrollInside;
                    if (a.firstLoad && c.addClass("modal-loading"), !this.readyState || "complete" == this.readyState) {
                        a.firstLoad = !1, l.waittime > 0 && clearTimeout(a.waitTimeout);
                        try {
                            c.attr("ref", x.contentWindow.location.href);
                            var s = e.frames[w].$;
                            if (s && "auto" === l.height && "fullscreen" != l.size) {
                                var r = s("body").addClass("body-modal").toggleClass("body-modal-scroll-inside", o);
                                a.$iframeBody = r, l.iframeBodyClass && r.addClass(l.iframeBodyClass);
                                var h = [], d = function (i) {
                                    c.removeClass("fade");
                                    var n = r.outerHeight();
                                    if (i === !0 && l.onlyIncreaseHeight && (n = Math.max(n, f.data("minModalHeight") || 0), f.data("minModalHeight", n)), o) {
                                        var a = l.headerHeight;
                                        "number" != typeof a ? a = p.outerHeight() : "function" == typeof a && (a = a(p));
                                        var s = t(e).height();
                                        n = Math.min(n, s - a)
                                    }
                                    for (h.length > 1 && n === h[0] && (n = Math.max(n, h[1])), h.push(n); h.length > 2;) h.shift();
                                    f.css("height", n), l.fade && c.addClass("fade"), v()
                                };
                                c.callComEvent(a, "loaded", {
                                    modalType: "iframe",
                                    jQuery: s
                                }), setTimeout(d, 100), r.off("resize." + n).on("resize." + n, d), o && t(e).off("resize." + n).on("resize." + n, d)
                            } else v();
                            var u = l.handleLinkInIframe;
                            u && s("body").on("click", "string" == typeof u ? u : "a[href]", function () {
                                t(this).is('[data-toggle="modal"]') || c.addClass("modal-updating")
                            }), l.iframeStyle && s("head").append("<style>" + l.iframeStyle + "</style>")
                        } catch (i) {
                            v()
                        }
                    }
                }
            } else t.ajax(t.extend({
                url: l.url, success: function (i) {
                    try {
                        var s = t(i);
                        s.filter(".modal-dialog").length ? d.parent().empty().append(s) : s.filter(".modal-content").length ? d.find(".modal-content").replaceWith(s) : f.wrapInner(s)
                    } catch (r) {
                        e.console && e.console.warn && console.warn("ZUI: Cannot recogernize remote content.", {
                            error: r,
                            data: i
                        }), c.html(i)
                    }
                    c.callComEvent(a, "loaded", {modalType: o}), v(), l.scrollInside && t(e).off("resize." + n).on("resize." + n, m)
                }, error: b
            }, l.ajaxOptions))
        }
        h || c.modal({
            show: "show",
            backdrop: l.backdrop,
            moveable: l.moveable,
            rememberPos: l.rememberPos,
            keyboard: l.keyboard,
            scrollInside: l.scrollInside
        })
    }, r.prototype.close = function (t, i) {
        var n = this;
        (t || i) && n.$modal.on("hidden" + a, function () {
            "function" == typeof t && t(), typeof i === s && i.length && !n.$modal.data("cancel-reload") && ("this" === i ? e.location.reload() : e.location = i)
        }), n.$modal.modal("hide")
    }, r.prototype.toggle = function (t) {
        this.isShown ? this.close() : this.show(t)
    }, r.prototype.adjustPosition = function (t) {
        t = t === i ? this.options.position : t, "function" == typeof t && (t = t(this)), this.$modal.modal("adjustPosition", t)
    }, t.zui({ModalTrigger: r, modalTrigger: new r}), t.fn.modalTrigger = function (e, i) {
        return t(this).each(function () {
            var o = t(this), a = o.data(n), l = t.extend({
                title: o.attr("title") || o.text(),
                url: o.attr("href"),
                type: o.hasClass("iframe") ? "iframe" : ""
            }, o.data(), t.isPlainObject(e) && e);
            return a ? void (typeof e == s ? a[e](i) : l.show && a.show(i)) : (o.data(n, a = new r(l, o)), void o.on((l.trigger || "click") + ".toggle." + n, function (e) {
                l = t.extend(l, {url: o.attr("href") || o.attr("data-url") || o.data("url") || l.url}), a.toggle(l), o.is("a") && e.preventDefault()
            }))
        })
    };
    var l = t.fn.modal;
    t.fn.modal = function (e, i) {
        return t(this).each(function () {
            var n = t(this);
            n.hasClass("modal") ? l.call(n, e, i) : n.modalTrigger(e, i)
        })
    }, t.fn.modal.bs = l;
    var h = function (e) {
        return e ? e = t(e) : (e = t(".modal.modal-trigger"), !e.length), e && e instanceof t ? e : null
    }, c = function (i, o, a) {
        var s = i;
        if ("function" == typeof i) {
            var r = a;
            a = o, o = i, i = r
        }
        i = h(i), i && i.length ? i.each(function () {
            t(this).data(n).close(o, a)
        }) : t("body").hasClass("modal-open") || t(".modal.in").length || t("body").hasClass("body-modal") && e.parent.$.zui.closeModal(s, o, a)
    }, d = function (t, e) {
        e = h(e), e && e.length && e.modal("adjustPosition", t)
    }, u = function (e, i) {
        "string" == typeof e && (e = {url: e});
        var o = h(i);
        o && o.length && o.each(function () {
            t(this).data(n).show(e)
        })
    };
    t.zui({
        reloadModal: u,
        closeModal: c,
        ajustModalPosition: d,
        adjustModalPosition: d
    }), t(document).on("click." + n + ".data-api", '[data-toggle="modal"]', function (e) {
        var i = t(this), o = i.attr("href"), a = null;
        try {
            a = t(i.attr("data-target") || o && o.replace(/.*(?=#[^\s]+$)/, ""))
        } catch (s) {
        }
        a && a.length || (i.data(n) ? i.trigger(".toggle." + n) : i.modalTrigger({show: !0})), i.is("a") && e.preventDefault()
    }).on("click." + n + ".data-api", '[data-dismiss="modal"]', function () {
        t.zui.closeModal()
    })
}(window.jQuery, window, void 0), +function (t) {
    "use strict";
    var e = function (t, e) {
        this.type = null, this.options = null, this.enabled = null, this.timeout = null, this.hoverState = null, this.$element = null, this.init("tooltip", t, e)
    };
    e.DEFAULTS = {
        animation: !0,
        placement: "top",
        selector: !1,
        template: '<div class="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
        trigger: "hover focus",
        title: "",
        delay: 0,
        html: !1,
        container: !1
    }, e.prototype.init = function (e, i, n) {
        this.enabled = !0, this.type = e, this.$element = t(i), this.options = this.getOptions(n);
        for (var o = this.options.trigger.split(" "), a = o.length; a--;) {
            var s = o[a];
            if ("click" == s) this.$element.on("click." + this.type, this.options.selector, this.toggle.bind(this)); else if ("manual" != s) {
                var r = "hover" == s ? "mouseenter" : "focus", l = "hover" == s ? "mouseleave" : "blur";
                this.$element.on(r + "." + this.type, this.options.selector, this.enter.bind(this)), this.$element.on(l + "." + this.type, this.options.selector, this.leave.bind(this))
            }
        }
        this.options.selector ? this._options = t.extend({}, this.options, {
            trigger: "manual",
            selector: ""
        }) : this.fixTitle()
    }, e.prototype.getDefaults = function () {
        return e.DEFAULTS
    }, e.prototype.getOptions = function (e) {
        return e = t.extend({}, this.getDefaults(), this.$element.data(), e), e.delay && "number" == typeof e.delay && (e.delay = {
            show: e.delay,
            hide: e.delay
        }), e
    }, e.prototype.getDelegateOptions = function () {
        var e = {}, i = this.getDefaults();
        return this._options && t.each(this._options, function (t, n) {
            i[t] != n && (e[t] = n)
        }), e
    }, e.prototype.enter = function (e) {
        var i = e instanceof this.constructor ? e : t(e.currentTarget)[this.type](this.getDelegateOptions()).data("zui." + this.type);
        return clearTimeout(i.timeout), i.hoverState = "in", i.options.delay && i.options.delay.show ? void (i.timeout = setTimeout(function () {
            "in" == i.hoverState && i.show()
        }, i.options.delay.show)) : i.show()
    }, e.prototype.leave = function (e) {
        var i = e instanceof this.constructor ? e : t(e.currentTarget)[this.type](this.getDelegateOptions()).data("zui." + this.type);
        return clearTimeout(i.timeout), i.hoverState = "out", i.options.delay && i.options.delay.hide ? void (i.timeout = setTimeout(function () {
            "out" == i.hoverState && i.hide()
        }, i.options.delay.hide)) : i.hide()
    }, e.prototype.show = function (e) {
        var i = t.Event("show.zui." + this.type);
        if ((e || this.hasContent()) && this.enabled) {
            var n = this;
            if (n.$element.trigger(i), i.isDefaultPrevented()) return;
            var o = n.tip();
            n.setContent(e), n.options.animation && o.addClass("fade");
            var a = "function" == typeof n.options.placement ? n.options.placement.call(n, o[0], n.$element[0]) : n.options.placement,
                s = /\s?auto?\s?/i, r = s.test(a);
            r && (a = a.replace(s, "") || "top"), o.detach().css({
                top: 0,
                left: 0,
                display: "block"
            }).addClass(a), n.options.container ? o.appendTo(n.options.container) : o.insertAfter(n.$element);
            var l = n.getPosition(), h = o[0].offsetWidth, c = o[0].offsetHeight;
            if (r) {
                var d = n.$element.parent(), u = a, f = document.documentElement.scrollTop || document.body.scrollTop,
                    p = "body" == n.options.container ? window.innerWidth : d.outerWidth(),
                    g = "body" == n.options.container ? window.innerHeight : d.outerHeight(),
                    m = "body" == n.options.container ? 0 : d.offset().left;
                a = "bottom" == a && l.top + l.height + c - f > g ? "top" : "top" == a && l.top - f - c < 0 ? "bottom" : "right" == a && l.right + h > p ? "left" : "left" == a && l.left - h < m ? "right" : a, o.removeClass(u).addClass(a)
            }
            var v = n.getCalculatedOffset(a, l, h, c);
            n.applyPlacement(v, a);
            var y = function () {
                var t = n.hoverState;
                n.$element.trigger("shown.zui." + n.type), n.hoverState = null, "out" == t && n.leave(n)
            };
            t.support.transition && n.$tip.hasClass("fade") ? o.one("bsTransitionEnd", y).emulateTransitionEnd(150) : y()
        }
    }, e.prototype.applyPlacement = function (t, e) {
        var i, n = this.tip(), o = n[0].offsetWidth, a = n[0].offsetHeight, s = parseInt(n.css("margin-top"), 10),
            r = parseInt(n.css("margin-left"), 10);
        isNaN(s) && (s = 0), isNaN(r) && (r = 0), t.top = t.top + s, t.left = t.left + r, n.offset(t).addClass("in");
        var l = n[0].offsetWidth, h = n[0].offsetHeight;
        if ("top" == e && h != a && (i = !0, t.top = t.top + a - h), /bottom|top/.test(e)) {
            var c = 0;
            t.left < 0 && (c = t.left * -2, t.left = 0, n.offset(t), l = n[0].offsetWidth, h = n[0].offsetHeight), this.replaceArrow(c - o + l, l, "left")
        } else this.replaceArrow(h - a, h, "top");
        i && n.offset(t)
    }, e.prototype.replaceArrow = function (t, e, i) {
        this.arrow().css(i, t ? 50 * (1 - t / e) + "%" : "")
    }, e.prototype.setContent = function (t) {
        var e = this.tip(), i = t || this.getTitle();
        this.options.tipId && e.attr("id", this.options.tipId), this.options.tipClass && e.addClass(this.options.tipClass), e.find(".tooltip-inner")[this.options.html ? "html" : "text"](i), e.removeClass("fade in top bottom left right")
    }, e.prototype.hide = function () {
        function e() {
            "in" != i.hoverState && n.detach()
        }

        var i = this, n = this.tip(), o = t.Event("hide.zui." + this.type);
        if (this.$element.trigger(o), !o.isDefaultPrevented()) return n.removeClass("in"), t.support.transition && this.$tip.hasClass("fade") ? n.one(t.support.transition.end, e).emulateTransitionEnd(150) : e(), this.$element.trigger("hidden.zui." + this.type), this
    }, e.prototype.fixTitle = function () {
        var t = this.$element;
        (t.attr("title") || "string" != typeof t.attr("data-original-title")) && t.attr("data-original-title", t.attr("title") || "").attr("title", "")
    }, e.prototype.hasContent = function () {
        return this.getTitle()
    }, e.prototype.getPosition = function () {
        var e = this.$element[0];
        return t.extend({}, "function" == typeof e.getBoundingClientRect ? e.getBoundingClientRect() : {
            width: e.offsetWidth,
            height: e.offsetHeight
        }, this.$element.offset())
    }, e.prototype.getCalculatedOffset = function (t, e, i, n) {
        return "bottom" == t ? {
            top: e.top + e.height,
            left: e.left + e.width / 2 - i / 2
        } : "top" == t ? {
            top: e.top - n,
            left: e.left + e.width / 2 - i / 2
        } : "left" == t ? {top: e.top + e.height / 2 - n / 2, left: e.left - i} : {
            top: e.top + e.height / 2 - n / 2,
            left: e.left + e.width
        }
    }, e.prototype.getTitle = function () {
        var t, e = this.$element, i = this.options;
        return t = e.attr("data-original-title") || ("function" == typeof i.title ? i.title.call(e[0]) : i.title);
    }, e.prototype.tip = function () {
        return this.$tip = this.$tip || t(this.options.template)
    }, e.prototype.arrow = function () {
        return this.$arrow = this.$arrow || this.tip().find(".tooltip-arrow")
    }, e.prototype.validate = function () {
        this.$element[0].parentNode || (this.hide(), this.$element = null, this.options = null)
    }, e.prototype.enable = function () {
        this.enabled = !0
    }, e.prototype.disable = function () {
        this.enabled = !1
    }, e.prototype.toggleEnabled = function () {
        this.enabled = !this.enabled
    }, e.prototype.toggle = function (e) {
        var i = e ? t(e.currentTarget)[this.type](this.getDelegateOptions()).data("zui." + this.type) : this;
        i.tip().hasClass("in") ? i.leave(i) : i.enter(i)
    }, e.prototype.destroy = function () {
        this.hide().$element.off("." + this.type).removeData("zui." + this.type)
    };
    var i = t.fn.tooltip;
    t.fn.tooltip = function (i, n) {
        return this.each(function () {
            var o = t(this), a = o.data("zui.tooltip"), s = "object" == typeof i && i;
            a || o.data("zui.tooltip", a = new e(this, s)), "string" == typeof i && a[i](n)
        })
    }, t.fn.tooltip.Constructor = e, t.fn.tooltip.noConflict = function () {
        return t.fn.tooltip = i, this
    }
}(window.jQuery), +function (t) {
    "use strict";
    var e = function (t, e) {
        this.init("popover", t, e)
    };
    if (!t.fn.tooltip) throw new Error("Popover requires tooltip.js");
    e.DEFAULTS = t.extend({}, t.fn.tooltip.Constructor.DEFAULTS, {
        placement: "right",
        trigger: "click",
        content: "",
        template: '<div class="popover"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
    }), e.prototype = t.extend({}, t.fn.tooltip.Constructor.prototype), e.prototype.constructor = e, e.prototype.getDefaults = function () {
        return e.DEFAULTS
    }, e.prototype.setContent = function () {
        var t = this.tip(), e = this.getTarget();
        if (e) return e.find(".arrow").length < 1 && t.addClass("no-arrow"), void t.html(e.html());
        var i = this.getTitle(), n = this.getContent();
        t.find(".popover-title")[this.options.html ? "html" : "text"](i), t.find(".popover-content")[this.options.html ? "html" : "text"](n), t.removeClass("fade top bottom left right in"), this.options.tipId && t.attr("id", this.options.tipId), this.options.tipClass && t.addClass(this.options.tipClass), t.find(".popover-title").html() || t.find(".popover-title").hide()
    }, e.prototype.hasContent = function () {
        return this.getTarget() || this.getTitle() || this.getContent()
    }, e.prototype.getContent = function () {
        var t = this.$element, e = this.options;
        return t.attr("data-content") || ("function" == typeof e.content ? e.content.call(t[0]) : e.content)
    }, e.prototype.getTarget = function () {
        var e = this.$element, i = this.options,
            n = e.attr("data-target") || ("function" == typeof i.target ? i.target.call(e[0]) : i.target);
        return !!n && ("$next" == n ? e.next(".popover") : t(n))
    }, e.prototype.arrow = function () {
        return this.$arrow = this.$arrow || this.tip().find(".arrow")
    }, e.prototype.tip = function () {
        return this.$tip || (this.$tip = t(this.options.template)), this.$tip
    };
    var i = t.fn.popover;
    t.fn.popover = function (i) {
        return this.each(function () {
            var n = t(this), o = n.data("zui.popover"), a = "object" == typeof i && i;
            o || n.data("zui.popover", o = new e(this, a)), "string" == typeof i && o[i]()
        })
    }, t.fn.popover.Constructor = e, t.fn.popover.noConflict = function () {
        return t.fn.popover = i, this
    }
}(window.jQuery), +function (t) {
    "use strict";

    function e(e) {
        t(o).remove(), t(a).each(function (e) {
            var o = i(t(this));
            o.hasClass("open") && (o.trigger(e = t.Event("hide." + n)), e.isDefaultPrevented() || o.removeClass("open").trigger("hidden." + n))
        })
    }

    function i(e) {
        var i = e.attr("data-target");
        i || (i = e.attr("href"), i = i && /#/.test(i) && i.replace(/.*(?=#[^\s]*$)/, ""));
        var n;
        try {
            n = i && t(i)
        } catch (o) {
        }
        return n && n.length ? n : e.parent()
    }

    var n = "zui.dropdown", o = ".dropdown-backdrop", a = "[data-toggle=dropdown]", s = function (e) {
        t(e).on("click." + n, this.toggle)
    };
    s.prototype.toggle = function (o) {
        var a = t(this);
        if (!a.is(".disabled, :disabled")) {
            var s = i(a), r = s.hasClass("open");
            if (e(), !r) {
                if ("ontouchstart" in document.documentElement && !s.closest(".navbar-nav").length && t('<div class="dropdown-backdrop"/>').insertAfter(t(this)).on("click", e), s.trigger(o = t.Event("show." + n)), o.isDefaultPrevented()) return;
                s.toggleClass("open").trigger("shown." + n), a.focus()
            }
            return !1
        }
    }, s.prototype.keydown = function (e) {
        if (/(38|40|27)/.test(e.keyCode)) {
            var n = t(this);
            if (e.preventDefault(), e.stopPropagation(), !n.is(".disabled, :disabled")) {
                var o = i(n), s = o.hasClass("open");
                if (!s || s && 27 == e.keyCode) return 27 == e.which && o.find(a).focus(), n.click();
                var r = t("[role=menu] li:not(.divider):visible a", o);
                if (r.length) {
                    var l = r.index(r.filter(":focus"));
                    38 == e.keyCode && l > 0 && l--, 40 == e.keyCode && l < r.length - 1 && l++, ~l || (l = 0), r.eq(l).focus()
                }
            }
        }
    };
    var r = t.fn.dropdown;
    t.fn.dropdown = function (e) {
        return this.each(function () {
            var i = t(this), n = i.data("dropdown");
            n || i.data("dropdown", n = new s(this)), "string" == typeof e && n[e].call(i)
        })
    }, t.fn.dropdown.Constructor = s, t.fn.dropdown.noConflict = function () {
        return t.fn.dropdown = r, this
    };
    var l = n + ".data-api";
    t(document).on("click." + l, e).on("click." + l, ".dropdown form,.not-clear-menu", function (t) {
        t.stopPropagation()
    }).on("click." + l, a, s.prototype.toggle).on("keydown." + l, a + ", [role=menu]", s.prototype.keydown)
}(window.jQuery), function (t, e, i) {
    "use strict";
    var n = 0,
        o = '<div class="messager messager-{type} {placement}" style="display: none"><div class="messager-content"></div><div class="messager-actions"></div></div>',
        a = {icons: {}, type: "default", placement: "top", time: 4e3, parent: "body", close: !0, fade: !0, scale: !0},
        s = {}, r = function (e, r) {
            t.isPlainObject(e) ? r = t.extend({}, r, e) : e && (r ? r.content = e : r = {content: e});
            var l = this;
            r = l.options = t.extend({}, a, r), l.id = r.id || n++;
            var h = s[l.id];
            h && h.destroy(), s[l.id] = l, l.$ = t(o.format(r)).toggleClass("fade", r.fade).toggleClass("scale", r.scale).attr("id", "messager-" + l.id), r.cssClass && l.$.addClass(r.cssClass);
            var c = !1, d = l.$.find(".messager-actions"), u = function (e) {
                var n = t('<button type="button" class="action action-' + e.name + '"/>');
                "close" === e.name && n.addClass("close"), e.html !== i && n.html(e.html), e.icon !== i && n.append('<i class="action-icon icon-' + e.icon + '"/>'), e.text !== i && n.append('<span class="action-text">' + e.text + "</span>"), e.tooltip !== i && n.attr("title", e.tooltip).tooltip(), n.data("action", e), d.append(n)
            };
            r.actions && t.each(r.actions, function (t, e) {
                e.name === i && (e.name = t), "close" == e.name && (c = !0), u(e)
            }), !c && r.close && u({name: "close", html: "&times;"}), l.$.on("click", ".action", function (e) {
                var i, n = t(this).data("action");
                r.onAction && (i = r.onAction.call(this, n.name, n, l), i === !1) || "function" == typeof n.action && (i = n.action.call(this, l), i === !1) || (l.hide(), e.stopPropagation())
            }), l.$.on("click", function (t) {
                if (r.onAction) {
                    var e = r.onAction.call(this, "content", null, l);
                    e === !0 && l.hide()
                }
            }), l.$.data("zui.messager", l), r.show && l.message !== i && l.show()
        };
    r.prototype.update = function (e, i) {
        t.isPlainObject(e) ? i = e : e && (i ? i.content = e : i = {content: e});
        var n = this, o = n.options;
        n.$.removeClass("messager-" + o.type);
        var a = n.$.find(".messager-content");
        o.contentClass && a.removeClass(o.contentClass), i && (o = t.extend(o, i)), n.$.addClass("messager-" + o.type).toggleClass("messager-notification", !!o.notification), o.contentClass && a.addClass(o.contentClass);
        var s = o.title, r = o.icon;
        if (e = o.content, a.empty(), s) {
            var l = t('<div class="messager-title"></div>');
            l[o.html ? "html" : "text"](s), a.append(l)
        }
        if (e) {
            var h = t('<div class="messager-text"></div>');
            h[o.html ? "html" : "text"](e), a.append(h)
        }
        var c = n.$.find(".messager-icon");
        if (r) {
            var d = t.isPlainObject(r) ? r.html : '<i class="icon-' + r + ' icon"></i>';
            c.length ? c.html(d) : a.before('<div class="messager-icon">' + d + "<div>")
        } else c.remove();
        n.$.toggleClass("messager-has-icon", !!r), n.updateTime || o.onUpdate && o.onUpdate.call(n, o), n.updateTime = Date.now()
    }, r.prototype.show = function (n, o) {
        var a = this, s = this.options;
        if ("function" == typeof n) {
            var r = o;
            o = n, r !== i && (n = r)
        }
        if (a.isShow) return void a.hide(function () {
            a.show(n, o)
        });
        a.hiding && (clearTimeout(a.hiding), a.hiding = null), a.update(n);
        var l = s.placement, h = t(s.parent), c = h.children(".messagers-holder." + l);
        if (c.length || (c = t("<div/>").attr("class", "messagers-holder " + l).appendTo(h)), c.append(a.$), "center" === l) {
            var d = t(e).height() - c.height();
            c.css("top", Math.max(-d, d / 2))
        }
        return a.$.show().addClass("in"), s.time && (a.hiding = setTimeout(function () {
            a.hide()
        }, s.time)), a.isShow = !0, o && o(), s.onShow && s.onShow.call(a, s), a
    }, r.prototype.hide = function (t, e) {
        t === !0 && (e = !0, t = null);
        var i = this, n = i.options;
        if (i.$.hasClass("in")) {
            i.$.removeClass("in");
            var o = function () {
                var o = i.$.parent();
                i.$.detach(), o.children().length || o.remove(), t && t(!0), n.onHide && n.onHide.call(i, e)
            };
            e ? o() : setTimeout(o, 200)
        } else t && t(!1), n.onHide && n.onHide.call(i, e);
        i.isShow = !1
    }, r.prototype.destroy = function () {
        var t = this;
        t.hide(function () {
            t.$.remove(), t.$ = null
        }, !0), delete s[t.id]
    };
    var l = function (e) {
        if (e === i) t(".messager").each(function () {
            var e = t(this).data("zui.messager");
            e && e.hide && e.hide(!0)
        }); else {
            var n = t("#messager-" + e).data("zui.messager");
            n && n.hide && n.hide()
        }
    }, h = function (e, n) {
        "string" == typeof n && (n = {type: n}), t.isPlainObject(e) && (n = t.extend({}, n, e), e = null), n = t.extend({}, n), n.id === i && l();
        var o = s[n.id] || new r(e, n);
        return o.show(), o
    }, c = {notification: !0, placement: "bottom-right", time: 0, icon: "bell icon-2x"}, d = function (e, i, n) {
        var o = t.extend({id: t.zui.uuid()}, c), a = "string" == typeof e, s = "string" == typeof i;
        return a && s ? n = t.extend(o, n, {
            title: e,
            content: i
        }) : a && t.isPlainObject(i) ? n = t.extend(o, n, i, {title: e}) : t.isPlainObject(e) ? n = t.extend(o, n, i, e) : a && (n = t.extend(o, n, {title: e})), h(n)
    }, u = function (t) {
        return "string" == typeof t ? {placement: t} : t
    }, f = {show: h, hide: l};
    r.all = s, r.DEFAULTS = a, r.NOTIFICATION_DEFAULTS = c, t.each({
        primary: 0,
        success: "ok-sign",
        info: "info-sign",
        warning: "warning-sign",
        danger: "exclamation-sign",
        important: 0,
        special: 0
    }, function (e, i) {
        f[e] = function (n, o) {
            return h(n, t.extend({type: e, icon: r.DEFAULTS.icons[e] || i || null}, u(o)))
        }
    }), t.zui({Messager: r, showMessager: h, showNotification: d, messager: f})
}(jQuery, window, void 0), function (t, e, i, n) {
    "use strict";

    function o(t) {
        if (t = t.toLowerCase(), t && c.test(t)) {
            var e;
            if (4 === t.length) {
                var i = "#";
                for (e = 1; e < 4; e += 1) i += t.slice(e, e + 1).concat(t.slice(e, e + 1));
                t = i
            }
            var n = [];
            for (e = 1; e < 7; e += 2) n.push(b("0x" + t.slice(e, e + 2)));
            return {r: n[0], g: n[1], b: n[2], a: 1}
        }
        throw new Error("Wrong hex string! (hex: " + t + ")")
    }

    function a(e) {
        return typeof e === p && ("transparent" === e.toLowerCase() || m[e.toLowerCase()] || c.test(t.trim(e.toLowerCase())))
    }

    function s(t) {
        function e(t) {
            return t = t < 0 ? t + 1 : t > 1 ? t - 1 : t, 6 * t < 1 ? r + (s - r) * t * 6 : 2 * t < 1 ? s : 3 * t < 2 ? r + (s - r) * (2 / 3 - t) * 6 : r
        }

        var i = t.h, n = t.s, o = t.l, a = t.a;
        i = h(i) % u / u, n = l(h(n)), o = l(h(o)), a = l(h(a));
        var s = o <= .5 ? o * (n + 1) : o + n - o * n, r = 2 * o - s,
            c = {r: e(i + 1 / 3) * d, g: e(i) * d, b: e(i - 1 / 3) * d, a: a};
        return c
    }

    function r(t, i, n) {
        return v(n) && (n = 0), v(i) && (i = d), e.min(e.max(t, n), i)
    }

    function l(t, e) {
        return r(t, e)
    }

    function h(t) {
        return "number" == typeof t ? t : parseFloat(t)
    }

    var c = /^#([0-9a-fA-f]{3}|[0-9a-fA-f]{6})$/, d = 255, u = 360, f = 100, p = "string", g = "object", m = {
        aliceblue: "#f0f8ff",
        antiquewhite: "#faebd7",
        aqua: "#00ffff",
        aquamarine: "#7fffd4",
        azure: "#f0ffff",
        beige: "#f5f5dc",
        bisque: "#ffe4c4",
        black: "#000000",
        blanchedalmond: "#ffebcd",
        blue: "#0000ff",
        blueviolet: "#8a2be2",
        brown: "#a52a2a",
        burlywood: "#deb887",
        cadetblue: "#5f9ea0",
        chartreuse: "#7fff00",
        chocolate: "#d2691e",
        coral: "#ff7f50",
        cornflowerblue: "#6495ed",
        cornsilk: "#fff8dc",
        crimson: "#dc143c",
        cyan: "#00ffff",
        darkblue: "#00008b",
        darkcyan: "#008b8b",
        darkgoldenrod: "#b8860b",
        darkgray: "#a9a9a9",
        darkgreen: "#006400",
        darkkhaki: "#bdb76b",
        darkmagenta: "#8b008b",
        darkolivegreen: "#556b2f",
        darkorange: "#ff8c00",
        darkorchid: "#9932cc",
        darkred: "#8b0000",
        darksalmon: "#e9967a",
        darkseagreen: "#8fbc8f",
        darkslateblue: "#483d8b",
        darkslategray: "#2f4f4f",
        darkturquoise: "#00ced1",
        darkviolet: "#9400d3",
        deeppink: "#ff1493",
        deepskyblue: "#00bfff",
        dimgray: "#696969",
        dodgerblue: "#1e90ff",
        firebrick: "#b22222",
        floralwhite: "#fffaf0",
        forestgreen: "#228b22",
        fuchsia: "#ff00ff",
        gainsboro: "#dcdcdc",
        ghostwhite: "#f8f8ff",
        gold: "#ffd700",
        goldenrod: "#daa520",
        gray: "#808080",
        green: "#008000",
        greenyellow: "#adff2f",
        honeydew: "#f0fff0",
        hotpink: "#ff69b4",
        indianred: "#cd5c5c",
        indigo: "#4b0082",
        ivory: "#fffff0",
        khaki: "#f0e68c",
        lavender: "#e6e6fa",
        lavenderblush: "#fff0f5",
        lawngreen: "#7cfc00",
        lemonchiffon: "#fffacd",
        lightblue: "#add8e6",
        lightcoral: "#f08080",
        lightcyan: "#e0ffff",
        lightgoldenrodyellow: "#fafad2",
        lightgray: "#d3d3d3",
        lightgreen: "#90ee90",
        lightpink: "#ffb6c1",
        lightsalmon: "#ffa07a",
        lightseagreen: "#20b2aa",
        lightskyblue: "#87cefa",
        lightslategray: "#778899",
        lightsteelblue: "#b0c4de",
        lightyellow: "#ffffe0",
        lime: "#00ff00",
        limegreen: "#32cd32",
        linen: "#faf0e6",
        magenta: "#ff00ff",
        maroon: "#800000",
        mediumaquamarine: "#66cdaa",
        mediumblue: "#0000cd",
        mediumorchid: "#ba55d3",
        mediumpurple: "#9370db",
        mediumseagreen: "#3cb371",
        mediumslateblue: "#7b68ee",
        mediumspringgreen: "#00fa9a",
        mediumturquoise: "#48d1cc",
        mediumvioletred: "#c71585",
        midnightblue: "#191970",
        mintcream: "#f5fffa",
        mistyrose: "#ffe4e1",
        moccasin: "#ffe4b5",
        navajowhite: "#ffdead",
        navy: "#000080",
        oldlace: "#fdf5e6",
        olive: "#808000",
        olivedrab: "#6b8e23",
        orange: "#ffa500",
        orangered: "#ff4500",
        orchid: "#da70d6",
        palegoldenrod: "#eee8aa",
        palegreen: "#98fb98",
        paleturquoise: "#afeeee",
        palevioletred: "#db7093",
        papayawhip: "#ffefd5",
        peachpuff: "#ffdab9",
        peru: "#cd853f",
        pink: "#ffc0cb",
        plum: "#dda0dd",
        powderblue: "#b0e0e6",
        purple: "#800080",
        red: "#ff0000",
        rosybrown: "#bc8f8f",
        royalblue: "#4169e1",
        saddlebrown: "#8b4513",
        salmon: "#fa8072",
        sandybrown: "#f4a460",
        seagreen: "#2e8b57",
        seashell: "#fff5ee",
        sienna: "#a0522d",
        silver: "#c0c0c0",
        skyblue: "#87ceeb",
        slateblue: "#6a5acd",
        slategray: "#708090",
        snow: "#fffafa",
        springgreen: "#00ff7f",
        steelblue: "#4682b4",
        tan: "#d2b48c",
        teal: "#008080",
        thistle: "#d8bfd8",
        tomato: "#ff6347",
        turquoise: "#40e0d0",
        violet: "#ee82ee",
        wheat: "#f5deb3",
        white: "#ffffff",
        whitesmoke: "#f5f5f5",
        yellow: "#ffff00",
        yellowgreen: "#9acd32"
    }, v = function (t) {
        return t === n
    }, y = function (t) {
        return !v(t)
    }, b = function (t) {
        return parseInt(t)
    }, w = function (t) {
        return b(l(h(t), d))
    }, x = function (t, e, i, n) {
        var a = this;
        if (a.r = a.g = a.b = 0, a.a = 1, y(n) && (a.a = l(h(n), 1)), y(t) && y(e) && y(i)) a.r = w(t), a.g = w(e), a.b = w(i); else if (y(t)) {
            var r = typeof t;
            if (r == p) if (t = t.toLowerCase(), "transparent" === t) a.a = 0; else if (m[t]) a.rgb(o(m[t])); else if (0 === t.indexOf("rgb")) {
                var c = t.substring(t.indexOf("(") + 1, t.lastIndexOf(")")).split(",", 4);
                a.rgb({r: c[0], g: c[1], b: c[2], a: c[3]})
            } else a.rgb(o(t)); else if ("number" == r && v(e)) a.r = a.g = a.b = w(t); else if (r == g && y(t.r)) a.r = w(t.r), y(t.g) && (a.g = w(t.g)), y(t.b) && (a.b = w(t.b)), y(t.a) && (a.a = l(h(t.a), 1)); else if (r == g && y(t.h)) {
                var d = {h: l(h(t.h), u), s: 1, l: 1, a: 1};
                y(t.s) && (d.s = l(h(t.s), 1)), y(t.l) && (d.l = l(h(t.l), 1)), y(t.a) && (d.a = l(h(t.a), 1)), a.rgb(s(d))
            }
        }
    };
    x.prototype.rgb = function (t) {
        var e = this;
        if (y(t)) {
            if (typeof t == g) y(t.r) && (e.r = w(t.r)), y(t.g) && (e.g = w(t.g)), y(t.b) && (e.b = w(t.b)), y(t.a) && (e.a = l(h(t.a), 1)); else {
                var i = b(h(t));
                e.r = i, e.g = i, e.b = i
            }
            return e
        }
        return {r: e.r, g: e.g, b: e.b, a: e.a}
    }, x.prototype.hue = function (t) {
        var e = this, i = e.toHsl();
        return v(t) ? i.h : (i.h = l(h(t), u), e.rgb(s(i)), e)
    }, x.prototype.darken = function (t) {
        var e = this, i = e.toHsl();
        return i.l -= t / f, i.l = l(i.l, 1), e.rgb(s(i)), e
    }, x.prototype.clone = function () {
        var t = this;
        return new x(t.r, t.g, t.b, t.a)
    }, x.prototype.lighten = function (t) {
        return this.darken(-t)
    }, x.prototype.fade = function (t) {
        return this.a = l(t / f, 1), this
    }, x.prototype.spin = function (t) {
        var e = this.toHsl(), i = (e.h + t) % u;
        return e.h = i < 0 ? u + i : i, this.rgb(s(e))
    }, x.prototype.toHsl = function () {
        var t, i, n = this, o = n.r / d, a = n.g / d, s = n.b / d, r = n.a, l = e.max(o, a, s), h = e.min(o, a, s),
            c = (l + h) / 2, f = l - h;
        if (l === h) t = i = 0; else {
            switch (i = c > .5 ? f / (2 - l - h) : f / (l + h), l) {
                case o:
                    t = (a - s) / f + (a < s ? 6 : 0);
                    break;
                case a:
                    t = (s - o) / f + 2;
                    break;
                case s:
                    t = (o - a) / f + 4
            }
            t /= 6
        }
        return {h: t * u, s: i, l: c, a: r}
    }, x.prototype.luma = function () {
        var t = this.r / d, i = this.g / d, n = this.b / d;
        return t = t <= .03928 ? t / 12.92 : e.pow((t + .055) / 1.055, 2.4), i = i <= .03928 ? i / 12.92 : e.pow((i + .055) / 1.055, 2.4), n = n <= .03928 ? n / 12.92 : e.pow((n + .055) / 1.055, 2.4), .2126 * t + .7152 * i + .0722 * n
    }, x.prototype.saturate = function (t) {
        var e = this.toHsl();
        return e.s += t / f, e.s = l(e.s), this.rgb(s(e))
    }, x.prototype.desaturate = function (t) {
        return this.saturate(-t)
    }, x.prototype.contrast = function (t, e, i) {
        if (e = v(e) ? new x(d, d, d, 1) : new x(e), t = v(t) ? new x(0, 0, 0, 1) : new x(t), t.luma() > e.luma()) {
            var n = e;
            e = t, t = n
        }
        return this.a < .5 ? t : (i = v(i) ? .43 : h(i), this.luma() < i ? e : t)
    }, x.prototype.hexStr = function () {
        var t = this.r.toString(16), e = this.g.toString(16), i = this.b.toString(16);
        return 1 == t.length && (t = "0" + t), 1 == e.length && (e = "0" + e), 1 == i.length && (i = "0" + i), "#" + t + e + i
    }, x.prototype.toCssStr = function () {
        var t = this;
        return t.a > 0 ? t.a < 1 ? "rgba(" + t.r + "," + t.g + "," + t.b + "," + t.a + ")" : t.hexStr() : "transparent"
    }, x.isColor = a, x.names = m, x.get = function (t) {
        return new x(t)
    }, t.zui({Color: x})
}(jQuery, Math, window, void 0),/*!
 * Chart.js 1.0.2
 * Copyright 2015 Nick Downie
 * Released under the MIT license
 * http://chartjs.org/
 */
    function (t) {
        "use strict";
        var e = t && t.zui ? t.zui : this, i = (e.Chart, function (t) {
            this.canvas = t.canvas, this.ctx = t;
            var e = function (t, e) {
                return t["offset" + e] ? t["offset" + e] : document.defaultView.getComputedStyle(t).getPropertyValue(e)
            }, i = this.width = e(t.canvas, "Width"), o = this.height = e(t.canvas, "Height");
            t.canvas.width = i, t.canvas.height = o;
            var i = this.width = t.canvas.width, o = this.height = t.canvas.height;
            return this.aspectRatio = this.width / this.height, n.retinaScale(this), this
        });
        i.defaults = {
            global: {
                animation: !0,
                animationSteps: 60,
                animationEasing: "easeOutQuart",
                showScale: !0,
                scaleOverride: !1,
                scaleSteps: null,
                scaleStepWidth: null,
                scaleStartValue: null,
                scaleLineColor: "rgba(0,0,0,.1)",
                scaleLineWidth: 1,
                scaleShowLabels: !0,
                scaleLabel: "<%=value%>",
                scaleIntegersOnly: !0,
                scaleBeginAtZero: !1,
                scaleFontFamily: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
                scaleFontSize: 12,
                scaleFontStyle: "normal",
                scaleFontColor: "#666",
                responsive: !1,
                maintainAspectRatio: !0,
                showTooltips: !0,
                customTooltips: !1,
                tooltipEvents: ["mousemove", "touchstart", "touchmove", "mouseout"],
                tooltipFillColor: "rgba(0,0,0,0.8)",
                tooltipFontFamily: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
                tooltipFontSize: 14,
                tooltipFontStyle: "normal",
                tooltipFontColor: "#fff",
                tooltipTitleFontFamily: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
                tooltipTitleFontSize: 14,
                tooltipTitleFontStyle: "bold",
                tooltipTitleFontColor: "#fff",
                tooltipYPadding: 6,
                tooltipXPadding: 6,
                tooltipCaretSize: 8,
                tooltipCornerRadius: 6,
                tooltipXOffset: 10,
                tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %>",
                multiTooltipTemplate: "<%if (datasetLabel){%><%=datasetLabel%>: <%}%><%= value %>",
                multiTooltipTitleTemplate: "<%= label %>",
                multiTooltipKeyBackground: "#fff",
                onAnimationProgress: function () {
                },
                onAnimationComplete: function () {
                }
            }
        }, i.types = {};
        var n = i.helpers = {}, o = n.each = function (t, e, i) {
            var n = Array.prototype.slice.call(arguments, 3);
            if (t) if (t.length === +t.length) {
                var o;
                for (o = 0; o < t.length; o++) e.apply(i, [t[o], o].concat(n))
            } else for (var a in t) e.apply(i, [t[a], a].concat(n))
        }, a = n.clone = function (t) {
            var e = {};
            return o(t, function (i, n) {
                t.hasOwnProperty(n) && (e[n] = i)
            }), e
        }, s = n.extend = function (t) {
            return o(Array.prototype.slice.call(arguments, 1), function (e) {
                o(e, function (i, n) {
                    e.hasOwnProperty(n) && (t[n] = i)
                })
            }), t
        }, r = n.merge = function (t, e) {
            var i = Array.prototype.slice.call(arguments, 0);
            return i.unshift({}), s.apply(null, i)
        }, l = n.indexOf = function (t, e) {
            if (Array.prototype.indexOf) return t.indexOf(e);
            for (var i = 0; i < t.length; i++) if (t[i] === e) return i;
            return -1
        }, h = (n.where = function (t, e) {
            var i = [];
            return n.each(t, function (t) {
                e(t) && i.push(t)
            }), i
        }, n.findNextWhere = function (t, e, i) {
            i || (i = -1);
            for (var n = i + 1; n < t.length; n++) {
                var o = t[n];
                if (e(o)) return o
            }
        }, n.findPreviousWhere = function (t, e, i) {
            i || (i = t.length);
            for (var n = i - 1; n >= 0; n--) {
                var o = t[n];
                if (e(o)) return o
            }
        }, n.inherits = function (t) {
            var e = this, i = t && t.hasOwnProperty("constructor") ? t.constructor : function () {
                return e.apply(this, arguments)
            }, n = function () {
                this.constructor = i
            };
            return n.prototype = e.prototype, i.prototype = new n, i.extend = h, t && s(i.prototype, t), i.__super__ = e.prototype, i
        }), c = n.noop = function () {
        }, d = n.uid = function () {
            var t = 0;
            return function () {
                return "chart-" + t++
            }
        }(), u = n.warn = function (t) {
            window.console && "function" == typeof window.console.warn && console.warn(t)
        }, f = n.amd = "function" == typeof define && define.amd, p = n.isNumber = function (t) {
            return !isNaN(parseFloat(t)) && isFinite(t)
        }, g = n.max = function (t) {
            return Math.max.apply(Math, t)
        }, m = n.min = function (t) {
            return Math.min.apply(Math, t)
        }, v = (n.cap = function (t, e, i) {
            if (p(e)) {
                if (t > e) return e
            } else if (p(i) && t < i) return i;
            return t
        }, n.getDecimalPlaces = function (t) {
            return t % 1 !== 0 && p(t) ? t.toString().split(".")[1].length : 0
        }), y = n.radians = function (t) {
            return t * (Math.PI / 180)
        }, b = (n.getAngleFromPoint = function (t, e) {
            var i = e.x - t.x, n = e.y - t.y, o = Math.sqrt(i * i + n * n), a = 2 * Math.PI + Math.atan2(n, i);
            return i < 0 && n < 0 && (a += 2 * Math.PI), {angle: a, distance: o}
        }, n.aliasPixel = function (t) {
            return t % 2 === 0 ? 0 : .5
        }), w = (n.splineCurve = function (t, e, i, n) {
            var o = Math.sqrt(Math.pow(e.x - t.x, 2) + Math.pow(e.y - t.y, 2)),
                a = Math.sqrt(Math.pow(i.x - e.x, 2) + Math.pow(i.y - e.y, 2)), s = n * o / (o + a),
                r = n * a / (o + a);
            return {
                inner: {x: e.x - s * (i.x - t.x), y: e.y - s * (i.y - t.y)},
                outer: {x: e.x + r * (i.x - t.x), y: e.y + r * (i.y - t.y)}
            }
        }, n.calculateOrderOfMagnitude = function (t) {
            return Math.floor(Math.log(t) / Math.LN10)
        }), x = (n.calculateScaleRange = function (t, e, i, n, o) {
            var a = 2, s = Math.floor(e / (1.5 * i)), r = a >= s, l = g(t), h = m(t);
            l === h && (l += .5, h >= .5 && !n ? h -= .5 : l += .5);
            for (var c = Math.abs(l - h), d = w(c), u = Math.ceil(l / (1 * Math.pow(10, d))) * Math.pow(10, d), f = n ? 0 : Math.floor(h / (1 * Math.pow(10, d))) * Math.pow(10, d), p = u - f, v = Math.pow(10, d), y = Math.round(p / v); (y > s || 2 * y < s) && !r;) if (y > s) v *= 2, y = Math.round(p / v), y % 1 !== 0 && (r = !0); else if (o && d >= 0) {
                if (v / 2 % 1 !== 0) break;
                v /= 2, y = Math.round(p / v)
            } else v /= 2, y = Math.round(p / v);
            return r && (y = a, v = p / y), {steps: y, stepValue: v, min: f, max: f + y * v}
        }, n.template = function (t, e) {
            function i(t, e) {
                var i = /\W/.test(t) ? new Function("obj", "var p=[],print=function(){p.push.apply(p,arguments);};with(obj){p.push('" + t.replace(/[\r\t\n]/g, " ").split("<%").join("\t").replace(/((^|%>)[^\t]*)'/g, "$1\r").replace(/\t=(.*?)%>/g, "',$1,'").split("\t").join("');").split("%>").join("p.push('").split("\r").join("\\'") + "');}return p.join('');") : n[t] = n[t];
                return e ? i(e) : i
            }

            if (t instanceof Function) return t(e);
            var n = {};
            return i(t, e)
        }), C = (n.generateLabels = function (t, e, i, n) {
            var a = new Array(e);
            return labelTemplateString && o(a, function (e, o) {
                a[o] = x(t, {value: i + n * (o + 1)})
            }), a
        }, n.easingEffects = {
            linear: function (t) {
                return t
            }, easeInQuad: function (t) {
                return t * t
            }, easeOutQuad: function (t) {
                return -1 * t * (t - 2)
            }, easeInOutQuad: function (t) {
                return (t /= .5) < 1 ? .5 * t * t : -.5 * (--t * (t - 2) - 1)
            }, easeInCubic: function (t) {
                return t * t * t
            }, easeOutCubic: function (t) {
                return 1 * ((t = t / 1 - 1) * t * t + 1)
            }, easeInOutCubic: function (t) {
                return (t /= .5) < 1 ? .5 * t * t * t : .5 * ((t -= 2) * t * t + 2)
            }, easeInQuart: function (t) {
                return t * t * t * t
            }, easeOutQuart: function (t) {
                return -1 * ((t = t / 1 - 1) * t * t * t - 1)
            }, easeInOutQuart: function (t) {
                return (t /= .5) < 1 ? .5 * t * t * t * t : -.5 * ((t -= 2) * t * t * t - 2)
            }, easeInQuint: function (t) {
                return 1 * (t /= 1) * t * t * t * t
            }, easeOutQuint: function (t) {
                return 1 * ((t = t / 1 - 1) * t * t * t * t + 1)
            }, easeInOutQuint: function (t) {
                return (t /= .5) < 1 ? .5 * t * t * t * t * t : .5 * ((t -= 2) * t * t * t * t + 2)
            }, easeInSine: function (t) {
                return -1 * Math.cos(t / 1 * (Math.PI / 2)) + 1
            }, easeOutSine: function (t) {
                return 1 * Math.sin(t / 1 * (Math.PI / 2))
            }, easeInOutSine: function (t) {
                return -.5 * (Math.cos(Math.PI * t / 1) - 1)
            }, easeInExpo: function (t) {
                return 0 === t ? 1 : 1 * Math.pow(2, 10 * (t / 1 - 1))
            }, easeOutExpo: function (t) {
                return 1 === t ? 1 : 1 * (-Math.pow(2, -10 * t / 1) + 1)
            }, easeInOutExpo: function (t) {
                return 0 === t ? 0 : 1 === t ? 1 : (t /= .5) < 1 ? .5 * Math.pow(2, 10 * (t - 1)) : .5 * (-Math.pow(2, -10 * --t) + 2)
            }, easeInCirc: function (t) {
                return t >= 1 ? t : -1 * (Math.sqrt(1 - (t /= 1) * t) - 1)
            }, easeOutCirc: function (t) {
                return 1 * Math.sqrt(1 - (t = t / 1 - 1) * t)
            }, easeInOutCirc: function (t) {
                return (t /= .5) < 1 ? -.5 * (Math.sqrt(1 - t * t) - 1) : .5 * (Math.sqrt(1 - (t -= 2) * t) + 1)
            }, easeInElastic: function (t) {
                var e = 1.70158, i = 0, n = 1;
                return 0 === t ? 0 : 1 == (t /= 1) ? 1 : (i || (i = .3), n < Math.abs(1) ? (n = 1, e = i / 4) : e = i / (2 * Math.PI) * Math.asin(1 / n), -(n * Math.pow(2, 10 * (t -= 1)) * Math.sin((1 * t - e) * (2 * Math.PI) / i)))
            }, easeOutElastic: function (t) {
                var e = 1.70158, i = 0, n = 1;
                return 0 === t ? 0 : 1 == (t /= 1) ? 1 : (i || (i = .3), n < Math.abs(1) ? (n = 1, e = i / 4) : e = i / (2 * Math.PI) * Math.asin(1 / n), n * Math.pow(2, -10 * t) * Math.sin((1 * t - e) * (2 * Math.PI) / i) + 1)
            }, easeInOutElastic: function (t) {
                var e = 1.70158, i = 0, n = 1;
                return 0 === t ? 0 : 2 == (t /= .5) ? 1 : (i || (i = 1 * (.3 * 1.5)), n < Math.abs(1) ? (n = 1, e = i / 4) : e = i / (2 * Math.PI) * Math.asin(1 / n), t < 1 ? -.5 * (n * Math.pow(2, 10 * (t -= 1)) * Math.sin((1 * t - e) * (2 * Math.PI) / i)) : n * Math.pow(2, -10 * (t -= 1)) * Math.sin((1 * t - e) * (2 * Math.PI) / i) * .5 + 1)
            }, easeInBack: function (t) {
                var e = 1.70158;
                return 1 * (t /= 1) * t * ((e + 1) * t - e)
            }, easeOutBack: function (t) {
                var e = 1.70158;
                return 1 * ((t = t / 1 - 1) * t * ((e + 1) * t + e) + 1)
            }, easeInOutBack: function (t) {
                var e = 1.70158;
                return (t /= .5) < 1 ? .5 * (t * t * (((e *= 1.525) + 1) * t - e)) : .5 * ((t -= 2) * t * (((e *= 1.525) + 1) * t + e) + 2)
            }, easeInBounce: function (t) {
                return 1 - C.easeOutBounce(1 - t)
            }, easeOutBounce: function (t) {
                return (t /= 1) < 1 / 2.75 ? 1 * (7.5625 * t * t) : t < 2 / 2.75 ? 1 * (7.5625 * (t -= 1.5 / 2.75) * t + .75) : t < 2.5 / 2.75 ? 1 * (7.5625 * (t -= 2.25 / 2.75) * t + .9375) : 1 * (7.5625 * (t -= 2.625 / 2.75) * t + .984375)
            }, easeInOutBounce: function (t) {
                return t < .5 ? .5 * C.easeInBounce(2 * t) : .5 * C.easeOutBounce(2 * t - 1) + .5
            }
        }), _ = n.requestAnimFrame = function () {
            return window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.oRequestAnimationFrame || window.msRequestAnimationFrame || function (t) {
                return window.setTimeout(t, 1e3 / 60)
            }
        }(), k = n.cancelAnimFrame = function () {
            return window.cancelAnimationFrame || window.webkitCancelAnimationFrame || window.mozCancelAnimationFrame || window.oCancelAnimationFrame || window.msCancelAnimationFrame || function (t) {
                return window.clearTimeout(t, 1e3 / 60)
            }
        }(), T = (n.animationLoop = function (t, e, i, n, o, a) {
            var s = 0, r = C[i] || C.linear, l = function () {
                s++;
                var i = s / e, h = r(i);
                t.call(a, h, i, s), n.call(a, h, i), s < e ? a.animationFrame = _(l) : o.apply(a)
            };
            _(l)
        }, n.getRelativePosition = function (t) {
            var e, i, n = t.originalEvent || t, o = t.currentTarget || t.srcElement, a = o.getBoundingClientRect();
            return n.touches ? (e = n.touches[0].clientX - a.left, i = n.touches[0].clientY - a.top) : (e = n.clientX - a.left, i = n.clientY - a.top), {
                x: e,
                y: i
            }
        }, n.addEvent = function (t, e, i) {
            t.addEventListener ? t.addEventListener(e, i) : t.attachEvent ? t.attachEvent("on" + e, i) : t["on" + e] = i
        }), S = n.removeEvent = function (t, e, i) {
            t.removeEventListener ? t.removeEventListener(e, i, !1) : t.detachEvent ? t.detachEvent("on" + e, i) : t["on" + e] = c
        }, D = (n.bindEvents = function (t, e, i) {
            t.events || (t.events = {}), o(e, function (e) {
                t.events[e] = function () {
                    i.apply(t, arguments)
                }, T(t.chart.canvas, e, t.events[e])
            })
        }, n.unbindEvents = function (t, e) {
            o(e, function (e, i) {
                S(t.chart.canvas, i, e)
            })
        }), M = n.getMaximumWidth = function (t) {
            var e = t.parentNode;
            return e.clientWidth
        }, P = n.getMaximumHeight = function (t) {
            var e = t.parentNode;
            return e.clientHeight
        }, z = (n.getMaximumSize = n.getMaximumWidth, n.retinaScale = function (t) {
            var e = t.ctx, i = t.canvas.width, n = t.canvas.height;
            window.devicePixelRatio && (e.canvas.style.width = i + "px", e.canvas.style.height = n + "px", e.canvas.height = n * window.devicePixelRatio, e.canvas.width = i * window.devicePixelRatio, e.scale(window.devicePixelRatio, window.devicePixelRatio))
        }), L = n.clear = function (t) {
            t.ctx.clearRect(0, 0, t.width, t.height)
        }, F = n.fontString = function (t, e, i) {
            return e + " " + t + "px " + i
        }, I = n.longestText = function (t, e, i) {
            t.font = e;
            var n = 0;
            return o(i, function (e) {
                var i = t.measureText(e).width;
                n = i > n ? i : n
            }), n
        }, $ = n.drawRoundedRectangle = function (t, e, i, n, o, a) {
            t.beginPath(), t.moveTo(e + a, i), t.lineTo(e + n - a, i), t.quadraticCurveTo(e + n, i, e + n, i + a), t.lineTo(e + n, i + o - a), t.quadraticCurveTo(e + n, i + o, e + n - a, i + o), t.lineTo(e + a, i + o), t.quadraticCurveTo(e, i + o, e, i + o - a), t.lineTo(e, i + a), t.quadraticCurveTo(e, i, e + a, i), t.closePath()
        };
        i.instances = {}, i.Type = function (t, e, n) {
            this.options = e, this.chart = n, this.id = d(), i.instances[this.id] = this, e.responsive && this.resize(), this.initialize.call(this, t)
        }, s(i.Type.prototype, {
            initialize: function () {
                return this
            }, clear: function () {
                return L(this.chart), this
            }, stop: function () {
                return k(this.animationFrame), this
            }, resize: function (t) {
                this.stop();
                var e = this.chart.canvas, i = M(this.chart.canvas),
                    n = this.options.maintainAspectRatio ? i / this.chart.aspectRatio : P(this.chart.canvas);
                return e.width = this.chart.width = i, e.height = this.chart.height = n, z(this.chart), "function" == typeof t && t.apply(this, Array.prototype.slice.call(arguments, 1)), this
            }, reflow: c, render: function (t) {
                return t && this.reflow(), this.options.animation && !t ? n.animationLoop(this.draw, this.options.animationSteps, this.options.animationEasing, this.options.onAnimationProgress, this.options.onAnimationComplete, this) : (this.draw(), this.options.onAnimationComplete.call(this)), this
            }, generateLegend: function () {
                return x(this.options.legendTemplate, this)
            }, destroy: function () {
                this.clear(), D(this, this.events);
                var t = this.chart.canvas;
                t.width = this.chart.width, t.height = this.chart.height, t.style.removeProperty ? (t.style.removeProperty("width"), t.style.removeProperty("height")) : (t.style.removeAttribute("width"), t.style.removeAttribute("height")), delete i.instances[this.id]
            }, showTooltip: function (t, e) {
                "undefined" == typeof this.activeElements && (this.activeElements = []);
                var a = function (t) {
                    var e = !1;
                    return t.length !== this.activeElements.length ? e = !0 : (o(t, function (t, i) {
                        t !== this.activeElements[i] && (e = !0)
                    }, this), e)
                }.call(this, t);
                if (a || e) {
                    if (this.activeElements = t, this.draw(), this.options.customTooltips && this.options.customTooltips(!1), t.length > 0) if (this.datasets && this.datasets.length > 1) {
                        for (var s, r, h = this.datasets.length - 1; h >= 0 && (s = this.datasets[h].points || this.datasets[h].bars || this.datasets[h].segments, r = l(s, t[0]), r === -1); h--) ;
                        var c = [], d = [], u = function (t) {
                            var e, i, o, a, s, l = [], h = [], u = [];
                            return n.each(this.datasets, function (t) {
                                t.showTooltips !== !1 && (e = t.points || t.bars || t.segments, e[r] && e[r].hasValue() && l.push(e[r]))
                            }), n.each(l, function (t) {
                                h.push(t.x), u.push(t.y), c.push(n.template(this.options.multiTooltipTemplate, t)), d.push({
                                    fill: t._saved.fillColor || t.fillColor,
                                    stroke: t._saved.strokeColor || t.strokeColor
                                })
                            }, this), s = m(u), o = g(u), a = m(h), i = g(h), {
                                x: a > this.chart.width / 2 ? a : i,
                                y: (s + o) / 2
                            }
                        }.call(this, r);
                        new i.MultiTooltip({
                            x: u.x,
                            y: u.y,
                            xPadding: this.options.tooltipXPadding,
                            yPadding: this.options.tooltipYPadding,
                            xOffset: this.options.tooltipXOffset,
                            fillColor: this.options.tooltipFillColor,
                            textColor: this.options.tooltipFontColor,
                            fontFamily: this.options.tooltipFontFamily,
                            fontStyle: this.options.tooltipFontStyle,
                            fontSize: this.options.tooltipFontSize,
                            titleTextColor: this.options.tooltipTitleFontColor,
                            titleFontFamily: this.options.tooltipTitleFontFamily,
                            titleFontStyle: this.options.tooltipTitleFontStyle,
                            titleFontSize: this.options.tooltipTitleFontSize,
                            cornerRadius: this.options.tooltipCornerRadius,
                            labels: c,
                            legendColors: d,
                            legendColorBackground: this.options.multiTooltipKeyBackground,
                            title: x(this.options.multiTooltipTitleTemplate, t[0]),
                            chart: this.chart,
                            ctx: this.chart.ctx,
                            custom: this.options.customTooltips
                        }).draw()
                    } else o(t, function (t) {
                        var e = t.tooltipPosition();
                        new i.Tooltip({
                            x: Math.round(e.x),
                            y: Math.round(e.y),
                            xPadding: this.options.tooltipXPadding,
                            yPadding: this.options.tooltipYPadding,
                            fillColor: this.options.tooltipFillColor,
                            textColor: this.options.tooltipFontColor,
                            fontFamily: this.options.tooltipFontFamily,
                            fontStyle: this.options.tooltipFontStyle,
                            fontSize: this.options.tooltipFontSize,
                            caretHeight: this.options.tooltipCaretSize,
                            cornerRadius: this.options.tooltipCornerRadius,
                            text: x(this.options.tooltipTemplate, t),
                            chart: this.chart,
                            custom: this.options.customTooltips
                        }).draw()
                    }, this);
                    return this
                }
            }, toBase64Image: function () {
                return this.chart.canvas.toDataURL.apply(this.chart.canvas, arguments)
            }
        }), i.Type.extend = function (t) {
            var e = this, n = function () {
                return e.apply(this, arguments)
            };
            if (n.prototype = a(e.prototype), s(n.prototype, t), n.extend = i.Type.extend, t.name || e.prototype.name) {
                var o = t.name || e.prototype.name,
                    l = i.defaults[e.prototype.name] ? a(i.defaults[e.prototype.name]) : {};
                i.defaults[o] = s(l, t.defaults), i.types[o] = n, i.prototype[o] = function (t, e) {
                    var a = r(i.defaults.global, i.defaults[o], e || {});
                    return new n(t, a, this)
                }
            } else u("Name not provided for this chart, so it hasn't been registered");
            return e
        }, i.Element = function (t) {
            s(this, t), this.initialize.apply(this, arguments), this.save()
        }, s(i.Element.prototype, {
            initialize: function () {
            }, restore: function (t) {
                return t ? o(t, function (t) {
                    this[t] = this._saved[t]
                }, this) : s(this, this._saved), this
            }, save: function () {
                return this._saved = a(this), delete this._saved._saved, this
            }, update: function (t) {
                return o(t, function (t, e) {
                    this._saved[e] = this[e], this[e] = t
                }, this), this
            }, transition: function (t, e) {
                return o(t, function (t, i) {
                    this[i] = (t - this._saved[i]) * e + this._saved[i]
                }, this), this
            }, tooltipPosition: function () {
                return {x: this.x, y: this.y}
            }, hasValue: function () {
                return p(this.value)
            }
        }), i.Element.extend = h, i.Point = i.Element.extend({
            display: !0, inRange: function (t, e) {
                var i = this.hitDetectionRadius + this.radius;
                return Math.pow(t - this.x, 2) + Math.pow(e - this.y, 2) < Math.pow(i, 2)
            }, draw: function () {
                if (this.display) {
                    var t = this.ctx;
                    t.beginPath(), t.arc(this.x, this.y, this.radius, 0, 2 * Math.PI), t.closePath(), t.strokeStyle = this.strokeColor, t.lineWidth = this.strokeWidth, t.fillStyle = this.fillColor, t.fill(), t.stroke()
                }
            }
        }), i.Arc = i.Element.extend({
            inRange: function (t, e) {
                var i = n.getAngleFromPoint(this, {x: t, y: e}),
                    o = i.angle >= this.startAngle && i.angle <= this.endAngle,
                    a = i.distance >= this.innerRadius && i.distance <= this.outerRadius;
                return o && a
            }, tooltipPosition: function () {
                var t = this.startAngle + (this.endAngle - this.startAngle) / 2,
                    e = (this.outerRadius - this.innerRadius) / 2 + this.innerRadius;
                return {x: this.x + Math.cos(t) * e, y: this.y + Math.sin(t) * e}
            }, draw: function (t) {
                var e = this.ctx;
                if (e.beginPath(), e.arc(this.x, this.y, this.outerRadius, this.startAngle, this.endAngle), e.arc(this.x, this.y, this.innerRadius, this.endAngle, this.startAngle, !0), e.closePath(), e.strokeStyle = this.strokeColor, e.lineWidth = this.strokeWidth, e.fillStyle = this.fillColor, e.fill(), e.lineJoin = "bevel", this.showStroke && e.stroke(), this.circleBeginEnd) {
                    var i = (this.outerRadius + this.innerRadius) / 2, n = (this.outerRadius - this.innerRadius) / 2;
                    e.beginPath(), e.arc(this.x + Math.cos(this.startAngle) * i, this.y + Math.sin(this.startAngle) * i, n, 0, 2 * Math.PI), e.closePath(), e.fill(), e.beginPath(), e.arc(this.x + Math.cos(this.endAngle) * i, this.y + Math.sin(this.endAngle) * i, n, 0, 2 * Math.PI), e.closePath(), e.fill()
                }
            }
        }), i.Rectangle = i.Element.extend({
            draw: function () {
                var t = this.ctx, e = this.width / 2, i = this.x - e, n = this.x + e,
                    o = this.base - (this.base - this.y), a = this.strokeWidth / 2;
                this.showStroke && (i += a, n -= a, o += a), t.beginPath(), t.fillStyle = this.fillColor, t.strokeStyle = this.strokeColor, t.lineWidth = this.strokeWidth, t.moveTo(i, this.base), t.lineTo(i, o), t.lineTo(n, o), t.lineTo(n, this.base), t.fill(), this.showStroke && t.stroke()
            }, height: function () {
                return this.base - this.y
            }, inRange: function (t, e) {
                return t >= this.x - this.width / 2 && t <= this.x + this.width / 2 && e >= this.y && e <= this.base
            }
        }), i.Tooltip = i.Element.extend({
            draw: function () {
                var t = this.chart.ctx;
                t.font = F(this.fontSize, this.fontStyle, this.fontFamily), this.xAlign = "center", this.yAlign = "above";
                var e = this.caretPadding = 2, i = t.measureText(this.text).width + 2 * this.xPadding,
                    n = this.fontSize + 2 * this.yPadding, o = n + this.caretHeight + e;
                this.x + i / 2 > this.chart.width ? this.xAlign = "left" : this.x - i / 2 < 0 && (this.xAlign = "right"), this.y - o < 0 && (this.yAlign = "below");
                var a = this.x - i / 2, s = this.y - o;
                if (t.fillStyle = this.fillColor, this.custom) this.custom(this); else {
                    switch (this.yAlign) {
                        case"above":
                            t.beginPath(), t.moveTo(this.x, this.y - e), t.lineTo(this.x + this.caretHeight, this.y - (e + this.caretHeight)), t.lineTo(this.x - this.caretHeight, this.y - (e + this.caretHeight)), t.closePath(), t.fill();
                            break;
                        case"below":
                            s = this.y + e + this.caretHeight, t.beginPath(), t.moveTo(this.x, this.y + e), t.lineTo(this.x + this.caretHeight, this.y + e + this.caretHeight), t.lineTo(this.x - this.caretHeight, this.y + e + this.caretHeight), t.closePath(), t.fill()
                    }
                    switch (this.xAlign) {
                        case"left":
                            a = this.x - i + (this.cornerRadius + this.caretHeight);
                            break;
                        case"right":
                            a = this.x - (this.cornerRadius + this.caretHeight)
                    }
                    $(t, a, s, i, n, this.cornerRadius), t.fill(), t.fillStyle = this.textColor, t.textAlign = "center", t.textBaseline = "middle", t.fillText(this.text, a + i / 2, s + n / 2)
                }
            }
        }), i.MultiTooltip = i.Element.extend({
            initialize: function () {
                this.font = F(this.fontSize, this.fontStyle, this.fontFamily), this.titleFont = F(this.titleFontSize, this.titleFontStyle, this.titleFontFamily), this.height = this.labels.length * this.fontSize + (this.labels.length - 1) * (this.fontSize / 2) + 2 * this.yPadding + 1.5 * this.titleFontSize, this.ctx.font = this.titleFont;
                var t = this.ctx.measureText(this.title).width,
                    e = I(this.ctx, this.font, this.labels) + this.fontSize + 3, i = g([e, t]);
                this.width = i + 2 * this.xPadding;
                var n = this.height / 2;
                this.y - n < 0 ? this.y = n : this.y + n > this.chart.height && (this.y = this.chart.height - n), this.x > this.chart.width / 2 ? this.x -= this.xOffset + this.width : this.x += this.xOffset
            }, getLineHeight: function (t) {
                var e = this.y - this.height / 2 + this.yPadding, i = t - 1;
                return 0 === t ? e + this.titleFontSize / 2 : e + (1.5 * this.fontSize * i + this.fontSize / 2) + 1.5 * this.titleFontSize
            }, draw: function () {
                if (this.custom) this.custom(this); else {
                    $(this.ctx, this.x, this.y - this.height / 2, this.width, this.height, this.cornerRadius);
                    var t = this.ctx;
                    t.fillStyle = this.fillColor, t.fill(), t.closePath(), t.textAlign = "left", t.textBaseline = "middle", t.fillStyle = this.titleTextColor, t.font = this.titleFont, t.fillText(this.title, this.x + this.xPadding, this.getLineHeight(0)), t.font = this.font, n.each(this.labels, function (e, i) {
                        t.fillStyle = this.textColor, t.fillText(e, this.x + this.xPadding + this.fontSize + 3, this.getLineHeight(i + 1)), t.fillStyle = this.legendColorBackground, t.fillRect(this.x + this.xPadding, this.getLineHeight(i + 1) - this.fontSize / 2, this.fontSize, this.fontSize), t.fillStyle = this.legendColors[i].fill, t.fillRect(this.x + this.xPadding, this.getLineHeight(i + 1) - this.fontSize / 2, this.fontSize, this.fontSize)
                    }, this)
                }
            }
        }), i.Scale = i.Element.extend({
            initialize: function () {
                this.fit()
            }, buildYLabels: function () {
                this.yLabels = [];
                for (var t = v(this.stepValue), e = 0; e <= this.steps; e++) this.yLabels.push(x(this.templateString, {value: (this.min + e * this.stepValue).toFixed(t)}));
                this.yLabelWidth = this.display && this.showLabels ? I(this.ctx, this.font, this.yLabels) : 0
            }, addXLabel: function (t) {
                this.xLabels.push(t), this.valuesCount++, this.fit()
            }, removeXLabel: function () {
                this.xLabels.shift(), this.valuesCount--, this.fit()
            }, fit: function () {
                this.startPoint = this.display ? this.fontSize : 0, this.endPoint = this.display ? this.height - 1.5 * this.fontSize - 5 : this.height, this.startPoint += this.padding, this.endPoint -= this.padding;
                var t, e = this.endPoint - this.startPoint;
                for (this.calculateYRange(e), this.buildYLabels(), this.calculateXLabelRotation(); e > this.endPoint - this.startPoint;) e = this.endPoint - this.startPoint, t = this.yLabelWidth, this.calculateYRange(e), this.buildYLabels(), t < this.yLabelWidth && this.calculateXLabelRotation()
            }, calculateXLabelRotation: function () {
                this.ctx.font = this.font;
                var t, e, i = this.ctx.measureText(this.xLabels[0]).width,
                    n = this.ctx.measureText(this.xLabels[this.xLabels.length - 1]).width;
                if (this.xScalePaddingRight = n / 2 + 3, this.xScalePaddingLeft = i / 2 > this.yLabelWidth + 10 ? i / 2 : this.yLabelWidth + 10, this.xLabelRotation = 0, this.display) {
                    var o, a = I(this.ctx, this.font, this.xLabels);
                    this.xLabelWidth = a;
                    for (var s = Math.floor(this.calculateX(1) - this.calculateX(0)) - 6; this.xLabelWidth > s && 0 === this.xLabelRotation || this.xLabelWidth > s && this.xLabelRotation <= 90 && this.xLabelRotation > 0;) o = Math.cos(y(this.xLabelRotation)), t = o * i, e = o * n, t + this.fontSize / 2 > this.yLabelWidth + 8 && (this.xScalePaddingLeft = t + this.fontSize / 2), this.xScalePaddingRight = this.fontSize / 2, this.xLabelRotation++, this.xLabelWidth = o * a;
                    this.xLabelRotation > 0 && (this.endPoint -= Math.sin(y(this.xLabelRotation)) * a + 3)
                } else this.xLabelWidth = 0, this.xScalePaddingRight = this.padding, this.xScalePaddingLeft = this.padding
            }, calculateYRange: c, drawingArea: function () {
                return this.startPoint - this.endPoint
            }, calculateY: function (t) {
                var e = this.drawingArea() / (this.min - this.max);
                return this.endPoint - e * (t - this.min)
            }, calculateX: function (t) {
                var e = (this.xLabelRotation > 0, this.width - (this.xScalePaddingLeft + this.xScalePaddingRight)),
                    i = e / Math.max(this.valuesCount - (this.offsetGridLines ? 0 : 1), 1),
                    n = i * t + this.xScalePaddingLeft;
                return this.offsetGridLines && (n += i / 2), Math.round(n)
            }, update: function (t) {
                n.extend(this, t), this.fit()
            }, draw: function () {
                var t = this.ctx, e = (this.endPoint - this.startPoint) / this.steps,
                    i = Math.round(this.xScalePaddingLeft);
                if (this.display) {
                    t.fillStyle = this.textColor, t.font = this.font;
                    var a = this.showBeyondLine ? 5 : 0;
                    o(this.yLabels, function (o, s) {
                        var r = this.endPoint - e * s, l = Math.round(r), h = this.showHorizontalLines;
                        t.textAlign = "right", t.textBaseline = "middle", this.showLabels && t.fillText(o, i - 10, r), 0 !== s || h || (h = !0), h && t.beginPath(), s > 0 ? (t.lineWidth = this.gridLineWidth, t.strokeStyle = this.gridLineColor) : (t.lineWidth = this.lineWidth, t.strokeStyle = this.lineColor), l += n.aliasPixel(t.lineWidth), h && (t.moveTo(i, l), t.lineTo(this.width, l), t.stroke(), t.closePath()), t.lineWidth = this.lineWidth, t.strokeStyle = this.lineColor, t.beginPath(), t.moveTo(i - a, l), t.lineTo(i, l), t.stroke(), t.closePath()
                    }, this), o(this.xLabels, function (e, i) {
                        var n = this.calculateX(i) + b(this.lineWidth),
                            o = this.calculateX(i - (this.offsetGridLines ? .5 : 0)) + b(this.lineWidth),
                            s = this.xLabelRotation > 0, r = this.showVerticalLines;
                        0 !== i || r || (r = !0), r && t.beginPath(), i > 0 ? (t.lineWidth = this.gridLineWidth, t.strokeStyle = this.gridLineColor) : (t.lineWidth = this.lineWidth, t.strokeStyle = this.lineColor), r && (t.moveTo(o, this.endPoint), t.lineTo(o, this.startPoint - 3), t.stroke(), t.closePath()), t.lineWidth = this.lineWidth, t.strokeStyle = this.lineColor, t.beginPath(), t.moveTo(o, this.endPoint), t.lineTo(o, this.endPoint + a), t.stroke(), t.closePath(), t.save(), t.translate(n, s ? this.endPoint + 12 : this.endPoint + 8), t.rotate(y(this.xLabelRotation) * -1), t.font = this.font, t.textAlign = s ? "right" : "center", t.textBaseline = s ? "middle" : "top", t.fillText(e, 0, 0), t.restore()
                    }, this)
                }
            }
        }), i.RadialScale = i.Element.extend({
            initialize: function () {
                this.size = m([this.height, this.width]), this.drawingArea = this.display ? this.size / 2 - (this.fontSize / 2 + this.backdropPaddingY) : this.size / 2
            }, calculateCenterOffset: function (t) {
                var e = this.drawingArea / (this.max - this.min);
                return (t - this.min) * e
            }, update: function () {
                this.lineArc ? this.drawingArea = this.display ? this.size / 2 - (this.fontSize / 2 + this.backdropPaddingY) : this.size / 2 : this.setScaleSize(), this.buildYLabels()
            }, buildYLabels: function () {
                this.yLabels = [];
                for (var t = v(this.stepValue), e = 0; e <= this.steps; e++) this.yLabels.push(x(this.templateString, {value: (this.min + e * this.stepValue).toFixed(t)}))
            }, getCircumference: function () {
                return 2 * Math.PI / this.valuesCount
            }, setScaleSize: function () {
                var t, e, i, n, o, a, s, r, l, h, c, d,
                    u = m([this.height / 2 - this.pointLabelFontSize - 5, this.width / 2]), f = this.width, g = 0;
                for (this.ctx.font = F(this.pointLabelFontSize, this.pointLabelFontStyle, this.pointLabelFontFamily), e = 0; e < this.valuesCount; e++) t = this.getPointPosition(e, u), i = this.ctx.measureText(x(this.templateString, {value: this.labels[e]})).width + 5, 0 === e || e === this.valuesCount / 2 ? (n = i / 2, t.x + n > f && (f = t.x + n, o = e), t.x - n < g && (g = t.x - n, s = e)) : e < this.valuesCount / 2 ? t.x + i > f && (f = t.x + i, o = e) : e > this.valuesCount / 2 && t.x - i < g && (g = t.x - i, s = e);
                l = g, h = Math.ceil(f - this.width), a = this.getIndexAngle(o), r = this.getIndexAngle(s), c = h / Math.sin(a + Math.PI / 2), d = l / Math.sin(r + Math.PI / 2), c = p(c) ? c : 0, d = p(d) ? d : 0, this.drawingArea = u - (d + c) / 2, this.setCenterPoint(d, c)
            }, setCenterPoint: function (t, e) {
                var i = this.width - e - this.drawingArea, n = t + this.drawingArea;
                this.xCenter = (n + i) / 2, this.yCenter = this.height / 2
            }, getIndexAngle: function (t) {
                var e = 2 * Math.PI / this.valuesCount;
                return t * e - Math.PI / 2
            }, getPointPosition: function (t, e) {
                var i = this.getIndexAngle(t);
                return {x: Math.cos(i) * e + this.xCenter, y: Math.sin(i) * e + this.yCenter}
            }, draw: function () {
                if (this.display) {
                    var t = this.ctx;
                    if (o(this.yLabels, function (e, i) {
                        if (i > 0) {
                            var n, o = i * (this.drawingArea / this.steps), a = this.yCenter - o;
                            if (this.lineWidth > 0) if (t.strokeStyle = this.lineColor, t.lineWidth = this.lineWidth, this.lineArc) t.beginPath(), t.arc(this.xCenter, this.yCenter, o, 0, 2 * Math.PI), t.closePath(), t.stroke(); else {
                                t.beginPath();
                                for (var s = 0; s < this.valuesCount; s++) n = this.getPointPosition(s, this.calculateCenterOffset(this.min + i * this.stepValue)), 0 === s ? t.moveTo(n.x, n.y) : t.lineTo(n.x, n.y);
                                t.closePath(), t.stroke()
                            }
                            if (this.showLabels) {
                                if (t.font = F(this.fontSize, this.fontStyle, this.fontFamily), this.showLabelBackdrop) {
                                    var r = t.measureText(e).width;
                                    t.fillStyle = this.backdropColor, t.fillRect(this.xCenter - r / 2 - this.backdropPaddingX, a - this.fontSize / 2 - this.backdropPaddingY, r + 2 * this.backdropPaddingX, this.fontSize + 2 * this.backdropPaddingY)
                                }
                                t.textAlign = "center", t.textBaseline = "middle", t.fillStyle = this.fontColor, t.fillText(e, this.xCenter, a)
                            }
                        }
                    }, this), !this.lineArc) {
                        t.lineWidth = this.angleLineWidth, t.strokeStyle = this.angleLineColor;
                        for (var e = this.valuesCount - 1; e >= 0; e--) {
                            if (this.angleLineWidth > 0) {
                                var i = this.getPointPosition(e, this.calculateCenterOffset(this.max));
                                t.beginPath(), t.moveTo(this.xCenter, this.yCenter), t.lineTo(i.x, i.y), t.stroke(), t.closePath()
                            }
                            var n = this.getPointPosition(e, this.calculateCenterOffset(this.max) + 5);
                            t.font = F(this.pointLabelFontSize, this.pointLabelFontStyle, this.pointLabelFontFamily), t.fillStyle = this.pointLabelFontColor;
                            var a = this.labels.length, s = this.labels.length / 2, r = s / 2, l = e < r || e > a - r,
                                h = e === r || e === a - r;
                            0 === e ? t.textAlign = "center" : e === s ? t.textAlign = "center" : e < s ? t.textAlign = "left" : t.textAlign = "right", h ? t.textBaseline = "middle" : l ? t.textBaseline = "bottom" : t.textBaseline = "top", t.fillText(this.labels[e], n.x, n.y)
                        }
                    }
                }
            }
        }), n.addEvent(window, "resize", function () {
            var t;
            return function () {
                clearTimeout(t), t = setTimeout(function () {
                    o(i.instances, function (t) {
                        t.options.responsive && t.resize(t.render, !0)
                    })
                }, 50)
            }
        }()), f ? define(function () {
            return i
        }) : "object" == typeof module && module.exports && (module.exports = i), e.Chart = i, t.fn.chart = function () {
            var t = [];
            return this.each(function () {
                t.push(new i(this.getContext("2d")))
            }), 1 === t.length ? t[0] : t
        }
    }.call(this, jQuery), function (t) {
    "use strict";
    var e = t && t.zui ? t.zui : this, i = e.Chart, n = i.helpers, o = {
        scaleShowGridLines: !0,
        scaleGridLineColor: "rgba(0,0,0,.05)",
        scaleGridLineWidth: 1,
        scaleShowHorizontalLines: !0,
        scaleShowBeyondLine: !0,
        scaleShowVerticalLines: !0,
        bezierCurve: !0,
        bezierCurveTension: .4,
        pointDot: !0,
        pointDotRadius: 4,
        pointDotStrokeWidth: 1,
        pointHitDetectionRadius: 20,
        datasetStroke: !0,
        datasetStrokeWidth: 2,
        datasetFill: !0,
        legendTemplate: '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].strokeColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>'
    };
    i.Type.extend({
        name: "Line", defaults: o, initialize: function (e) {
            this.PointClass = i.Point.extend({
                strokeWidth: this.options.pointDotStrokeWidth,
                radius: this.options.pointDotRadius,
                display: this.options.pointDot,
                hitDetectionRadius: Math.min(this.options.pointHitDetectionRadius, Math.max(2, Math.floor(300 / (e.labels.length - 1) / 2))),
                ctx: this.chart.ctx,
                inRange: function (t) {
                    return Math.pow(t - this.x, 2) < Math.pow(this.radius + this.hitDetectionRadius, 2)
                }
            }), this.datasets = [], this.options.showTooltips && n.bindEvents(this, this.options.tooltipEvents, function (t) {
                var e = "mouseout" !== t.type ? this.getPointsAtEvent(t) : [];
                this.eachPoints(function (t) {
                    t.restore(["fillColor", "strokeColor"])
                }), n.each(e, function (t) {
                    t.fillColor = t.highlightFill, t.strokeColor = t.highlightStroke
                }), this.showTooltip(e)
            }), n.each(e.datasets, function (i) {
                if (t.zui && t.zui.Color && t.zui.Color.get) {
                    var o = t.zui.Color.get(i.color), a = o.toCssStr();
                    i.fillColor || (i.fillColor = o.clone().fade(20).toCssStr()), i.strokeColor || (i.strokeColor = a), i.pointColor || (i.pointColor = a), i.pointStrokeColor || (i.pointStrokeColor = "#fff"), i.pointHighlightFill || (i.pointHighlightFill = "#fff"), i.pointHighlightStroke || (i.pointHighlightStroke = a)
                }
                var s = {
                    label: i.label || null,
                    fillColor: i.fillColor,
                    strokeColor: i.strokeColor,
                    pointColor: i.pointColor,
                    pointStrokeColor: i.pointStrokeColor,
                    showTooltips: i.showTooltips !== !1,
                    points: []
                };
                this.datasets.push(s), n.each(i.data, function (t, n) {
                    s.points.push(new this.PointClass({
                        value: t,
                        label: e.labels[n],
                        datasetLabel: i.label,
                        strokeColor: i.pointStrokeColor,
                        fillColor: i.pointColor,
                        highlightFill: i.pointHighlightFill || i.pointColor,
                        highlightStroke: i.pointHighlightStroke || i.pointStrokeColor
                    }))
                }, this), this.buildScale(e.labels), this.eachPoints(function (t, e) {
                    n.extend(t, {x: this.scale.calculateX(e), y: this.scale.endPoint}), t.save()
                }, this)
            }, this), this.render()
        }, update: function () {
            this.scale.update(), n.each(this.activeElements, function (t) {
                t.restore(["fillColor", "strokeColor"])
            }), this.eachPoints(function (t) {
                t.save()
            }), this.render()
        }, eachPoints: function (t) {
            n.each(this.datasets, function (e) {
                n.each(e.points, t, this)
            }, this)
        }, getPointsAtEvent: function (t) {
            var e = [], i = n.getRelativePosition(t);
            return n.each(this.datasets, function (t) {
                n.each(t.points, function (t) {
                    t.inRange(i.x, i.y) && e.push(t)
                })
            }, this), e
        }, buildScale: function (t) {
            var e = this, o = function () {
                var t = [];
                return e.eachPoints(function (e) {
                    t.push(e.value)
                }), t
            }, a = {
                templateString: this.options.scaleLabel,
                height: this.chart.height,
                width: this.chart.width,
                ctx: this.chart.ctx,
                textColor: this.options.scaleFontColor,
                fontSize: this.options.scaleFontSize,
                fontStyle: this.options.scaleFontStyle,
                fontFamily: this.options.scaleFontFamily,
                valuesCount: t.length,
                beginAtZero: this.options.scaleBeginAtZero,
                integersOnly: this.options.scaleIntegersOnly,
                calculateYRange: function (t) {
                    var e = n.calculateScaleRange(o(), t, this.fontSize, this.beginAtZero, this.integersOnly);
                    n.extend(this, e)
                },
                xLabels: t,
                font: n.fontString(this.options.scaleFontSize, this.options.scaleFontStyle, this.options.scaleFontFamily),
                lineWidth: this.options.scaleLineWidth,
                lineColor: this.options.scaleLineColor,
                showHorizontalLines: this.options.scaleShowHorizontalLines,
                showVerticalLines: this.options.scaleShowVerticalLines,
                showBeyondLine: this.options.scaleShowBeyondLine,
                gridLineWidth: this.options.scaleShowGridLines ? this.options.scaleGridLineWidth : 0,
                gridLineColor: this.options.scaleShowGridLines ? this.options.scaleGridLineColor : "rgba(0,0,0,0)",
                padding: this.options.showScale ? 0 : this.options.pointDotRadius + this.options.pointDotStrokeWidth,
                showLabels: this.options.scaleShowLabels,
                display: this.options.showScale
            };
            this.options.scaleOverride && n.extend(a, {
                calculateYRange: n.noop,
                steps: this.options.scaleSteps,
                stepValue: this.options.scaleStepWidth,
                min: this.options.scaleStartValue,
                max: this.options.scaleStartValue + this.options.scaleSteps * this.options.scaleStepWidth
            }), this.scale = new i.Scale(a)
        }, addData: function (t, e) {
            n.each(t, function (t, i) {
                this.datasets[i].points.push(new this.PointClass({
                    value: t,
                    label: e,
                    datasetLabel: this.datasets[i].label,
                    x: this.scale.calculateX(this.scale.valuesCount + 1),
                    y: this.scale.endPoint,
                    strokeColor: this.datasets[i].pointStrokeColor,
                    fillColor: this.datasets[i].pointColor
                }))
            }, this), this.scale.addXLabel(e), this.update()
        }, removeData: function () {
            this.scale.removeXLabel(), n.each(this.datasets, function (t) {
                t.points.shift()
            }, this), this.update()
        }, reflow: function () {
            var t = n.extend({height: this.chart.height, width: this.chart.width});
            this.scale.update(t)
        }, draw: function (t) {
            var e = t || 1;
            this.clear();
            var i = this.chart.ctx, o = function (t) {
                return null !== t.value
            }, a = function (t, e, i) {
                return n.findNextWhere(e, o, i) || t
            }, s = function (t, e, i) {
                return n.findPreviousWhere(e, o, i) || t
            };
            this.scale.draw(e), n.each(this.datasets, function (t) {
                var r = n.where(t.points, o);
                n.each(t.points, function (t, i) {
                    t.hasValue() && t.transition({y: this.scale.calculateY(t.value), x: this.scale.calculateX(i)}, e)
                }, this), this.options.bezierCurve && n.each(r, function (t, e) {
                    var i = e > 0 && e < r.length - 1 ? this.options.bezierCurveTension : 0;
                    t.controlPoints = n.splineCurve(s(t, r, e), t, a(t, r, e), i), t.controlPoints.outer.y > this.scale.endPoint ? t.controlPoints.outer.y = this.scale.endPoint : t.controlPoints.outer.y < this.scale.startPoint && (t.controlPoints.outer.y = this.scale.startPoint), t.controlPoints.inner.y > this.scale.endPoint ? t.controlPoints.inner.y = this.scale.endPoint : t.controlPoints.inner.y < this.scale.startPoint && (t.controlPoints.inner.y = this.scale.startPoint)
                }, this), i.lineWidth = this.options.datasetStrokeWidth, i.strokeStyle = t.strokeColor, i.beginPath(), n.each(r, function (t, e) {
                    if (0 === e) i.moveTo(t.x, t.y); else if (this.options.bezierCurve) {
                        var n = s(t, r, e);
                        i.bezierCurveTo(n.controlPoints.outer.x, n.controlPoints.outer.y, t.controlPoints.inner.x, t.controlPoints.inner.y, t.x, t.y)
                    } else i.lineTo(t.x, t.y)
                }, this), i.stroke(), this.options.datasetFill && r.length > 0 && (i.lineTo(r[r.length - 1].x, this.scale.endPoint), i.lineTo(r[0].x, this.scale.endPoint), i.fillStyle = t.fillColor, i.closePath(), i.fill()), n.each(r, function (t) {
                    t.draw()
                })
            }, this)
        }
    }), t.fn.lineChart = function (e, n) {
        var o = [];
        return this.each(function () {
            var a = t(this);
            o.push(new i(this.getContext("2d")).Line(e, t.extend(a.data(), n)))
        }), 1 === o.length ? o[0] : o
    }
}.call(this, jQuery), function (t) {
    "use strict";
    var e = t && t.zui ? t.zui : this, i = e.Chart, n = i.helpers, o = {
        segmentShowStroke: !0,
        segmentStrokeColor: "#fff",
        segmentStrokeWidth: 1,
        percentageInnerCutout: 50,
        scaleShowLabels: !1,
        scaleLabel: "<%=value%>",
        scaleLabelPlacement: "auto",
        animationSteps: 60,
        animationEasing: "easeOutBounce",
        animateRotate: !0,
        animateScale: !1,
        legendTemplate: '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<segments.length; i++){%><li><span style="background-color:<%=segments[i].fillColor%>"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>'
    };
    i.Type.extend({
        name: "Doughnut", defaults: o, initialize: function (t) {
            this.segments = [], this.outerRadius = (n.min([this.chart.width, this.chart.height]) - this.options.segmentStrokeWidth / 2) / 2, this.SegmentArc = i.Arc.extend({
                ctx: this.chart.ctx,
                x: this.chart.width / 2,
                y: this.chart.height / 2
            }), this.options.showTooltips && n.bindEvents(this, this.options.tooltipEvents, function (t) {
                var e = "mouseout" !== t.type ? this.getSegmentsAtEvent(t) : [];
                n.each(this.segments, function (t) {
                    t.restore(["fillColor"])
                }), n.each(e, function (t) {
                    t.fillColor = t.highlightColor
                }), this.showTooltip(e)
            }), this.calculateTotal(t), n.each(t, function (t, e) {
                this.addData(t, e, !0)
            }, this), this.render()
        }, getSegmentsAtEvent: function (t) {
            var e = [], i = n.getRelativePosition(t);
            return n.each(this.segments, function (t) {
                t.inRange(i.x, i.y) && e.push(t)
            }, this), e
        }, addData: function (e, i, n) {
            if (t.zui && t.zui.Color && t.zui.Color.get) {
                var o = new t.zui.Color.get(e.color);
                e.color = o.toCssStr(), e.highlight || (e.highlight = o.lighten(5).toCssStr())
            }
            var a = i || this.segments.length;
            this.segments.splice(a, 0, new this.SegmentArc({
                id: "undefined" == typeof e.id ? a : e.id,
                value: e.value,
                outerRadius: this.options.animateScale ? 0 : this.outerRadius,
                innerRadius: this.options.animateScale ? 0 : this.outerRadius / 100 * this.options.percentageInnerCutout,
                fillColor: e.color,
                highlightColor: e.highlight || e.color,
                showStroke: this.options.segmentShowStroke,
                strokeWidth: this.options.segmentStrokeWidth,
                strokeColor: this.options.segmentStrokeColor,
                startAngle: 1.5 * Math.PI,
                circumference: this.options.animateRotate ? 0 : this.calculateCircumference(e.value),
                showLabel: e.showLabel !== !1,
                circleBeginEnd: e.circleBeginEnd,
                label: e.label
            })), n || (this.reflow(), this.update())
        }, calculateCircumference: function (t) {
            return 2 * Math.PI * (Math.abs(t) / this.total)
        }, calculateTotal: function (t) {
            this.total = 0, n.each(t, function (t) {
                this.total += Math.abs(t.value)
            }, this)
        }, update: function () {
            this.calculateTotal(this.segments), n.each(this.activeElements, function (t) {
                t.restore(["fillColor"])
            }), n.each(this.segments, function (t) {
                t.save()
            }), this.render()
        }, removeData: function (t) {
            var e = n.isNumber(t) ? t : this.segments.length - 1;
            this.segments.splice(e, 1), this.reflow(), this.update()
        }, reflow: function () {
            n.extend(this.SegmentArc.prototype, {
                x: this.chart.width / 2,
                y: this.chart.height / 2
            }), this.outerRadius = (n.min([this.chart.width, this.chart.height]) - this.options.segmentStrokeWidth / 2) / 2, n.each(this.segments, function (t) {
                t.update({
                    outerRadius: this.outerRadius,
                    innerRadius: this.outerRadius / 100 * this.options.percentageInnerCutout
                })
            }, this)
        }, drawLabel: function (e, i, o) {
            var a = this.options, s = (e.endAngle + e.startAngle) / 2, r = a.scaleLabelPlacement;
            "inside" !== r && "outside" !== r && this.chart.width - this.chart.height > 50 && e.circumference < Math.PI / 18 && (r = "outside");
            var l = Math.cos(s) * e.outerRadius, h = Math.sin(s) * e.outerRadius, c = n.template(a.scaleLabel, {
                value: "undefined" == typeof i ? e.value : Math.round(i * e.value),
                label: e.label
            }), d = this.chart.ctx;
            d.font = n.fontString(a.scaleFontSize, a.scaleFontStyle, a.scaleFontFamily), d.textBaseline = "middle", d.textAlign = "center";
            var u = (d.measureText(c).width, this.chart.width / 2), f = this.chart.height / 2;
            if ("outside" === r) {
                var p = l >= 0, g = l + u, m = h + f;
                d.textAlign = p ? "left" : "right", d.measureText(c).width, l = p ? Math.max(u + e.outerRadius + 10, l + 30 + u) : Math.min(u - e.outerRadius - 10, l - 30 + u);
                var v = a.scaleFontSize * (a.scaleLineHeight || 1), y = Math.round((.8 * h + f) / v) + 1,
                    b = (Math.floor(this.chart.width / v) + 1, p ? 1 : -1);
                if (o[y * b] && (y > 1 ? y-- : y++), o[y * b]) return;
                h = (y - 1) * v + a.scaleFontSize / 2, o[y * b] = !0, d.beginPath(), d.moveTo(g, m), d.lineTo(l, h), l = p ? l + 5 : l - 5, d.lineTo(l, h), d.strokeStyle = t.zui && t.zui.Color ? new t.zui.Color(e.fillColor).fade(40).toCssStr() : e.fillColor, d.strokeWidth = a.scaleLineWidth, d.stroke(), d.fillStyle = e.fillColor
            } else l = .7 * l + u, h = .7 * h + f, d.fillStyle = t.zui && t.zui.Color ? new t.zui.Color(e.fillColor).contrast().toCssStr() : "#fff";
            d.fillText(c, l, h)
        }, draw: function (t) {
            var e = t ? t : 1;
            this.clear();
            var i;
            if (n.each(this.segments, function (t, i) {
                t.transition({
                    circumference: this.calculateCircumference(t.value),
                    outerRadius: this.outerRadius,
                    innerRadius: this.outerRadius / 100 * this.options.percentageInnerCutout
                }, e), t.endAngle = t.startAngle + t.circumference, this.options.reverseDrawOrder || t.draw(), 0 === i && (t.startAngle = 1.5 * Math.PI), i < this.segments.length - 1 && (this.segments[i + 1].startAngle = t.endAngle)
            }, this), this.options.reverseDrawOrder && n.each(this.segments.slice().reverse(), function (t, e) {
                t.draw()
            }, this), this.options.scaleShowLabels) {
                var o = this.segments.slice().sort(function (t, e) {
                    return e.value - t.value
                }), i = {};
                n.each(o, function (e, n) {
                    e.showLabel && this.drawLabel(e, t, i)
                }, this)
            }
        }
    }), i.types.Doughnut.extend({
        name: "Pie",
        defaults: n.merge(o, {percentageInnerCutout: 0})
    }), t.fn.pieChart = function (e, n) {
        var o = [];
        return this.each(function () {
            var a = t(this);
            o.push(new i(this.getContext("2d")).Pie(e, t.extend(a.data(), n)))
        }), 1 === o.length ? o[0] : o
    }, t.fn.doughnutChart = function (e, n) {
        var o = [];
        return this.each(function () {
            var a = t(this);
            o.push(new i(this.getContext("2d")).Doughnut(e, t.extend(a.data(), n)))
        }), 1 === o.length ? o[0] : o
    }
}.call(this, jQuery), function (t) {
    "use strict";
    var e = t && t.zui ? t.zui : this, i = e.Chart, n = i.helpers, o = {
        scaleBeginAtZero: !0,
        scaleShowGridLines: !0,
        scaleGridLineColor: "rgba(0,0,0,.05)",
        scaleGridLineWidth: 1,
        scaleShowHorizontalLines: !0,
        scaleShowVerticalLines: !0,
        scaleShowBeyondLine: !0,
        barShowStroke: !0,
        barStrokeWidth: 1,
        scaleValuePlacement: "auto",
        barValueSpacing: 5,
        barDatasetSpacing: 1,
        legendTemplate: '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].fillColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>'
    };
    i.Type.extend({
        name: "Bar", defaults: o, initialize: function (e) {
            var o = this.options;
            this.ScaleClass = i.Scale.extend({
                offsetGridLines: !0, calculateBarX: function (t, e, i) {
                    var n = this.calculateBaseWidth(), a = this.calculateX(i) - n / 2, s = this.calculateBarWidth(t);
                    return a + s * e + e * o.barDatasetSpacing + s / 2
                }, calculateBaseWidth: function () {
                    return this.calculateX(1) - this.calculateX(0) - 2 * o.barValueSpacing
                }, calculateBarWidth: function (t) {
                    var e = this.calculateBaseWidth() - (t - 1) * o.barDatasetSpacing;
                    return e / t
                }
            }), this.datasets = [], this.options.showTooltips && n.bindEvents(this, this.options.tooltipEvents, function (t) {
                var e = "mouseout" !== t.type ? this.getBarsAtEvent(t) : [];
                this.eachBars(function (t) {
                    t.restore(["fillColor", "strokeColor"])
                }), n.each(e, function (t) {
                    t.fillColor = t.highlightFill, t.strokeColor = t.highlightStroke
                }), this.showTooltip(e)
            }), this.BarClass = i.Rectangle.extend({
                strokeWidth: this.options.barStrokeWidth,
                showStroke: this.options.barShowStroke,
                ctx: this.chart.ctx
            }), n.each(e.datasets, function (i, o) {
                if (t.zui && t.zui.Color && t.zui.Color.get) {
                    var a = t.zui.Color.get(i.color), s = a.toCssStr();
                    i.fillColor || (i.fillColor = a.clone().fade(50).toCssStr()), i.strokeColor || (i.strokeColor = s)
                }
                var r = {label: i.label || null, fillColor: i.fillColor, strokeColor: i.strokeColor, bars: []};
                this.datasets.push(r), n.each(i.data, function (t, n) {
                    r.bars.push(new this.BarClass({
                        value: t,
                        label: e.labels[n],
                        datasetLabel: i.label,
                        strokeColor: i.strokeColor,
                        fillColor: i.fillColor,
                        highlightFill: i.highlightFill || i.fillColor,
                        highlightStroke: i.highlightStroke || i.strokeColor
                    }))
                }, this)
            }, this), this.buildScale(e.labels), this.BarClass.prototype.base = this.scale.endPoint, this.eachBars(function (t, e, i) {
                n.extend(t, {
                    width: this.scale.calculateBarWidth(this.datasets.length),
                    x: this.scale.calculateBarX(this.datasets.length, i, e),
                    y: this.scale.endPoint
                }), t.save()
            }, this), this.render()
        }, update: function () {
            this.scale.update(), n.each(this.activeElements, function (t) {
                t.restore(["fillColor", "strokeColor"])
            }), this.eachBars(function (t) {
                t.save()
            }), this.render()
        }, eachBars: function (t) {
            n.each(this.datasets, function (e, i) {
                n.each(e.bars, t, this, i)
            }, this)
        }, getBarsAtEvent: function (t) {
            for (var e, i = [], o = n.getRelativePosition(t), a = function (t) {
                i.push(t.bars[e])
            }, s = 0; s < this.datasets.length; s++) for (e = 0; e < this.datasets[s].bars.length; e++) if (this.datasets[s].bars[e].inRange(o.x, o.y)) return n.each(this.datasets, a), i;
            return i
        }, buildScale: function (t) {
            var e = this, i = function () {
                var t = [];
                return e.eachBars(function (e) {
                    t.push(e.value)
                }), t
            }, o = {
                templateString: this.options.scaleLabel,
                height: this.chart.height,
                width: this.chart.width,
                ctx: this.chart.ctx,
                textColor: this.options.scaleFontColor,
                fontSize: this.options.scaleFontSize,
                fontStyle: this.options.scaleFontStyle,
                fontFamily: this.options.scaleFontFamily,
                valuesCount: t.length,
                beginAtZero: this.options.scaleBeginAtZero,
                integersOnly: this.options.scaleIntegersOnly,
                calculateYRange: function (t) {
                    var e = n.calculateScaleRange(i(), t, this.fontSize, this.beginAtZero, this.integersOnly);
                    n.extend(this, e)
                },
                xLabels: t,
                font: n.fontString(this.options.scaleFontSize, this.options.scaleFontStyle, this.options.scaleFontFamily),
                lineWidth: this.options.scaleLineWidth,
                lineColor: this.options.scaleLineColor,
                showHorizontalLines: this.options.scaleShowHorizontalLines,
                showVerticalLines: this.options.scaleShowVerticalLines,
                showBeyondLine: this.options.scaleShowBeyondLine,
                gridLineWidth: this.options.scaleShowGridLines ? this.options.scaleGridLineWidth : 0,
                gridLineColor: this.options.scaleShowGridLines ? this.options.scaleGridLineColor : "rgba(0,0,0,0)",
                padding: this.options.showScale ? 0 : this.options.barShowStroke ? this.options.barStrokeWidth : 0,
                showLabels: this.options.scaleShowLabels,
                display: this.options.showScale
            };
            this.options.scaleOverride && n.extend(o, {
                calculateYRange: n.noop,
                steps: this.options.scaleSteps,
                stepValue: this.options.scaleStepWidth,
                min: this.options.scaleStartValue,
                max: this.options.scaleStartValue + this.options.scaleSteps * this.options.scaleStepWidth
            }), this.scale = new this.ScaleClass(o)
        }, addData: function (t, e) {
            n.each(t, function (t, i) {
                this.datasets[i].bars.push(new this.BarClass({
                    value: t,
                    label: e,
                    x: this.scale.calculateBarX(this.datasets.length, i, this.scale.valuesCount + 1),
                    y: this.scale.endPoint,
                    width: this.scale.calculateBarWidth(this.datasets.length),
                    base: this.scale.endPoint,
                    strokeColor: this.datasets[i].strokeColor,
                    fillColor: this.datasets[i].fillColor
                }))
            }, this), this.scale.addXLabel(e), this.update()
        }, removeData: function () {
            this.scale.removeXLabel(), n.each(this.datasets, function (t) {
                t.bars.shift()
            }, this), this.update()
        }, reflow: function () {
            n.extend(this.BarClass.prototype, {y: this.scale.endPoint, base: this.scale.endPoint});
            var t = n.extend({height: this.chart.height, width: this.chart.width});
            this.scale.update(t)
        }, drawLabel: function (t, e) {
            var i = this.options;
            e = e || i.scaleValuePlacement, e = e ? e.toLowerCase() : "auto", "auto" === e && (e = t.y < 15 ? "insdie" : "outside");
            var o = "insdie" === e ? t.y + 10 : t.y - 10, a = this.chart.ctx;
            a.font = n.fontString(i.scaleFontSize, i.scaleFontStyle, i.scaleFontFamily), a.textBaseline = "middle", a.textAlign = "center", a.fillStyle = i.scaleFontColor, a.fillText(t.value, t.x, o)
        }, draw: function (t) {
            var e = t || 1;
            this.clear();
            this.chart.ctx;
            this.scale.draw(e);
            var i = this.options.scaleShowLabels && this.options.scaleValuePlacement;
            n.each(this.datasets, function (t, o) {
                n.each(t.bars, function (t, n) {
                    t.hasValue() && (t.base = this.scale.endPoint, t.transition({
                        x: this.scale.calculateBarX(this.datasets.length, o, n),
                        y: this.scale.calculateY(t.value),
                        width: this.scale.calculateBarWidth(this.datasets.length)
                    }, e).draw()), i && this.drawLabel(t)
                }, this)
            }, this)
        }
    }), t.fn.barChart = function (e, n) {
        var o = [];
        return this.each(function () {
            var a = t(this);
            o.push(new i(this.getContext("2d")).Bar(e, t.extend(a.data(), n)))
        }), 1 === o.length ? o[0] : o
    }
}.call(this, jQuery),/*!
 * Datetimepicker for Bootstrap
 * Copyright 2012 Stefan Petre
 * Licensed under the Apache License v2.0
 */
    !function (t) {
        function e() {
            return new Date(Date.UTC.apply(Date, arguments))
        }

        var i = function (e, i) {
            var o = this;
            this.element = t(e), this.language = (i.language || this.element.data("date-language") || (t.zui && t.zui.clientLang ? t.zui.clientLang().replace("_", "-") : "zh-cn")).toLowerCase(), this.lang = t.zui && t.zui.getLangData ? t.zui.getLangData("datetimepicker", this.language, n) : n[this.language], this.isRTL = this.lang.rtl || !1, this.formatType = i.formatType || this.element.data("format-type") || "standard", this.format = a.parseFormat(i.format || this.element.data("date-format") || this.lang.format || a.getDefaultFormat(this.formatType, "input"), this.formatType), this.isInline = !1, this.isVisible = !1, this.isInput = this.element.is("input"), this.component = !!this.element.is(".date") && this.element.find(".input-group-addon .icon-th, .input-group-addon .icon-time, .input-group-addon .icon-calendar").parent(), this.componentReset = !!this.element.is(".date") && this.element.find(".input-group-addon .icon-remove").parent(), this.hasInput = this.component && this.element.find("input").length, this.component && 0 === this.component.length && (this.component = !1), this.linkField = i.linkField || this.element.data("link-field") || !1, this.linkFormat = a.parseFormat(i.linkFormat || this.element.data("link-format") || a.getDefaultFormat(this.formatType, "link"), this.formatType), this.minuteStep = i.minuteStep || this.element.data("minute-step") || 5, this.pickerPosition = i.pickerPosition || this.element.data("picker-position") || "bottom-right", this.showMeridian = i.showMeridian || this.element.data("show-meridian") || !1, this.initialDate = i.initialDate || new Date, this.pickerClass = i.eleClass, this.onlyPickTime = i.maxView <= 1, this.pickerId = i.eleId, this._attachEvents(), this.formatViewType = "datetime", "formatViewType" in i ? this.formatViewType = i.formatViewType : "formatViewType" in this.element.data() && (this.formatViewType = this.element.data("formatViewType")), this.minView = 0, "minView" in i ? this.minView = i.minView : "minView" in this.element.data() && (this.minView = this.element.data("min-view")), this.minView = a.convertViewMode(this.minView), this.maxView = a.modes.length - 1, "maxView" in i ? this.maxView = i.maxView : "maxView" in this.element.data() && (this.maxView = this.element.data("max-view")), this.maxView = a.convertViewMode(this.maxView), this.wheelViewModeNavigation = !1, "wheelViewModeNavigation" in i ? this.wheelViewModeNavigation = i.wheelViewModeNavigation : "wheelViewModeNavigation" in this.element.data() && (this.wheelViewModeNavigation = this.element.data("view-mode-wheel-navigation")), this.wheelViewModeNavigationInverseDirection = !1, "wheelViewModeNavigationInverseDirection" in i ? this.wheelViewModeNavigationInverseDirection = i.wheelViewModeNavigationInverseDirection : "wheelViewModeNavigationInverseDirection" in this.element.data() && (this.wheelViewModeNavigationInverseDirection = this.element.data("view-mode-wheel-navigation-inverse-dir")), this.wheelViewModeNavigationDelay = 100, "wheelViewModeNavigationDelay" in i ? this.wheelViewModeNavigationDelay = i.wheelViewModeNavigationDelay : "wheelViewModeNavigationDelay" in this.element.data() && (this.wheelViewModeNavigationDelay = this.element.data("view-mode-wheel-navigation-delay")), this.startViewMode = 2, "startView" in i ? this.startViewMode = i.startView : "startView" in this.element.data() && (this.startViewMode = this.element.data("start-view")), this.startViewMode = a.convertViewMode(this.startViewMode), this.viewMode = this.startViewMode, this.viewSelect = this.minView, "viewSelect" in i ? this.viewSelect = i.viewSelect : "viewSelect" in this.element.data() && (this.viewSelect = this.element.data("view-select")), this.viewSelect = a.convertViewMode(this.viewSelect), this.forceParse = !0, "forceParse" in i ? this.forceParse = i.forceParse : "dateForceParse" in this.element.data() && (this.forceParse = this.element.data("date-force-parse")), this.picker = t(a.template).appendTo(this.isInline ? this.element : "body").on({click: this.click.bind(this)}), this.wheelViewModeNavigation && (t.fn.mousewheel ? this.picker.on({mousewheel: this.mousewheel.bind(this)}) : console.log("Mouse Wheel event is not supported. Please include the jQuery Mouse Wheel plugin before enabling this option")), this.isInline ? this.picker.addClass("datetimepicker-inline") : this.picker.addClass("datetimepicker-dropdown-" + this.pickerPosition + " dropdown-menu"), this.isRTL && (this.picker.addClass("datetimepicker-rtl"), this.picker.find(".prev span, .next span").toggleClass("icon-arrow-left icon-arrow-right")), t(document).on("mousedown", function (e) {
                0 === t(e.target).closest(".datetimepicker").length && o.hide()
            }), this.autoclose = !1, "autoclose" in i ? this.autoclose = i.autoclose : "dateAutoclose" in this.element.data() && (this.autoclose = this.element.data("date-autoclose")), this.keyboardNavigation = !0, "keyboardNavigation" in i ? this.keyboardNavigation = i.keyboardNavigation : "dateKeyboardNavigation" in this.element.data() && (this.keyboardNavigation = this.element.data("date-keyboard-navigation")), this.todayBtn = i.todayBtn || this.element.data("date-today-btn") || !1, this.todayHighlight = i.todayHighlight || this.element.data("date-today-highlight") || !1, this.weekStart = (i.weekStart || this.element.data("date-weekstart") || this.lang.weekStart || 0) % 7, this.weekEnd = (this.weekStart + 6) % 7, this.startDate = -(1 / 0), this.endDate = 1 / 0, this.daysOfWeekDisabled = [], this.setStartDate(i.startDate || this.element.data("date-startdate")), this.setEndDate(i.endDate || this.element.data("date-enddate")), this.setDaysOfWeekDisabled(i.daysOfWeekDisabled || this.element.data("date-days-of-week-disabled")), this.fillDow(), this.fillMonths(), this.update(), this.showMode(), this.isInline && this.show()
        };
        i.prototype = {
            constructor: i, _events: [], _attachEvents: function () {
                this._detachEvents(), this.isInput ? this._events = [[this.element, {
                    focus: this.show.bind(this),
                    keyup: this.update.bind(this),
                    keydown: this.keydown.bind(this)
                }]] : this.component && this.hasInput ? (this._events = [[this.element.find("input"), {
                    focus: this.show.bind(this),
                    keyup: this.update.bind(this),
                    keydown: this.keydown.bind(this)
                }], [this.component, {click: this.show.bind(this)}]], this.componentReset && this._events.push([this.componentReset, {click: this.reset.bind(this)}])) : this.element.is("div") ? this.isInline = !0 : this._events = [[this.element, {click: this.show.bind(this)}]];
                for (var t, e, i = 0; i < this._events.length; i++) t = this._events[i][0], e = this._events[i][1], t.on(e)
            }, _detachEvents: function () {
                for (var t, e, i = 0; i < this._events.length; i++) t = this._events[i][0], e = this._events[i][1], t.off(e);
                this._events = []
            }, show: function (e) {
                this.picker.show(), this.height = this.component ? this.component.outerHeight() : this.element.outerHeight(), this.forceParse && this.update(), this.place(), t(window).on("resize", this.place.bind(this)), e && (e.stopPropagation(), e.preventDefault()), this.isVisible = !0, this.element.trigger({
                    type: "show",
                    date: this.date
                })
            }, hide: function (e) {
                this.isVisible && (this.isInline || (this.picker.hide(), t(window).off("resize", this.place), this.viewMode = this.startViewMode, this.showMode(), this.isInput || t(document).off("mousedown", this.hide), this.forceParse && (this.isInput && this.element.val() || this.hasInput && this.element.find("input").val()) && this.setValue(), this.isVisible = !1, this.element.trigger({
                    type: "hide",
                    date: this.date
                })))
            }, remove: function () {
                this._detachEvents(), this.picker.remove(), delete this.picker, delete this.element.data().datetimepicker
            }, getDate: function () {
                var t = this.getUTCDate();
                return new Date(t.getTime() + 6e4 * t.getTimezoneOffset())
            }, getUTCDate: function () {
                return this.date
            }, setDate: function (t) {
                this.setUTCDate(new Date(t.getTime() - 6e4 * t.getTimezoneOffset()))
            }, setUTCDate: function (t) {
                t >= this.startDate && t <= this.endDate ? (this.date = t, this.setValue(), this.viewDate = this.date, this.fill()) : this.element.trigger({
                    type: "outOfRange",
                    date: t,
                    startDate: this.startDate,
                    endDate: this.endDate
                })
            }, setFormat: function (t) {
                this.format = a.parseFormat(t, this.formatType);
                var e;
                this.isInput ? e = this.element : this.component && (e = this.element.find("input")), e && e.val() && this.setValue()
            }, setValue: function () {
                var e = this.getFormattedDate();
                this.isInput ? this.element.val(e) : (this.component && this.element.find("input").val(e), this.element.data("date", e)), this.linkField && t("#" + this.linkField).val(this.getFormattedDate(this.linkFormat))
            }, getFormattedDate: function (t) {
                return void 0 == t && (t = this.format), a.formatDate(this.date, t, this.language, this.formatType)
            }, setStartDate: function (t) {
                this.startDate = t || -(1 / 0), this.startDate !== -(1 / 0) && (this.startDate = a.parseDate(this.startDate, this.format, this.language, this.formatType)), this.update(), this.updateNavArrows()
            }, setEndDate: function (t) {
                this.endDate = t || 1 / 0, this.endDate !== 1 / 0 && (this.endDate = a.parseDate(this.endDate, this.format, this.language, this.formatType)), this.update(), this.updateNavArrows()
            }, setDaysOfWeekDisabled: function (e) {
                this.daysOfWeekDisabled = e || [], Array.isArray(this.daysOfWeekDisabled) || (this.daysOfWeekDisabled = this.daysOfWeekDisabled.split(/,\s*/)), this.daysOfWeekDisabled = t.map(this.daysOfWeekDisabled, function (t) {
                    return parseInt(t, 10)
                }), this.update(), this.updateNavArrows()
            }, place: function () {
                if (!this.isInline) {
                    var e = 0;
                    t("div").each(function () {
                        var i = parseInt(t(this).css("zIndex"), 10);
                        i > e && (e = i)
                    });
                    var i, n, o, a = e + 10;
                    this.component ? (i = this.component.offset(), o = i.left, "bottom-left" !== this.pickerPosition && "top-left" !== this.pickerPosition && "auto-left" !== this.pickerPosition || (o += this.component.outerWidth() - this.picker.outerWidth())) : (i = this.element.offset(), o = i.left);
                    var s = 0 === this.pickerPosition.indexOf("auto-"),
                        r = s ? (i.top + this.picker.outerHeight() > t(window).height() + t(window).scrollTop() ? "top" : "bottom") + (0 === this.pickerPosition.lastIndexOf("-left") ? "-left" : "-right") : this.pickerPosition;
                    n = "top-left" === r || "top-right" === r ? i.top - this.picker.outerHeight() : i.top + this.height, this.picker.css({
                        top: n,
                        left: o,
                        zIndex: a
                    }).attr("class", "datetimepicker dropdown-menu datetimepicker-dropdown-" + r), this.pickerClass && this.picker.addClass(this.pickerClass), this.pickerId && this.picker.attr("id", this.pickerId), this.onlyPickTime && this.picker.addClass("datetimepicker-only-time")
                }
            }, update: function () {
                var t, e = !1;
                arguments && arguments.length && ("string" == typeof arguments[0] || arguments[0] instanceof Date) ? (t = arguments[0], e = !0) : (t = this.element.data("date") || (this.isInput ? this.element.val() : this.element.find("input").val()) || this.initialDate, ("string" == typeof t || t instanceof String) && (t = t.replace(/^\s+|\s+$/g, ""))), t || (t = new Date, e = !1), this.date = a.parseDate(t, this.format, this.language, this.formatType), e && this.setValue(), this.date < this.startDate ? this.viewDate = new Date(this.startDate) : this.date > this.endDate ? this.viewDate = new Date(this.endDate) : this.viewDate = new Date(this.date), this.fill()
            }, fillDow: function () {
                for (var t = this.weekStart, e = "<tr>"; t < this.weekStart + 7;) e += '<th class="dow">' + this.lang.daysMin[t++ % 7] + "</th>";
                e += "</tr>", this.picker.find(".datetimepicker-days thead").append(e)
            }, fillMonths: function () {
                for (var t = "", e = 0; e < 12;) t += '<span class="month">' + this.lang.monthsShort[e++] + "</span>";
                this.picker.find(".datetimepicker-months td").html(t)
            }, fill: function () {
                if (null != this.date && null != this.viewDate) {
                    var i = new Date(this.viewDate), n = i.getUTCFullYear(), o = i.getUTCMonth(), s = i.getUTCDate(),
                        r = i.getUTCHours(), l = i.getUTCMinutes(),
                        h = this.startDate !== -(1 / 0) ? this.startDate.getUTCFullYear() : -(1 / 0),
                        c = this.startDate !== -(1 / 0) ? this.startDate.getUTCMonth() : -(1 / 0),
                        d = this.endDate !== 1 / 0 ? this.endDate.getUTCFullYear() : 1 / 0,
                        u = this.endDate !== 1 / 0 ? this.endDate.getUTCMonth() : 1 / 0,
                        f = new e(this.date.getUTCFullYear(), this.date.getUTCMonth(), this.date.getUTCDate()).valueOf(),
                        p = new Date;
                    if (this.picker.find(".datetimepicker-days thead th:eq(1)").text(this.lang.months[o] + " " + n), "time" == this.formatViewType) {
                        var g = r % 12 ? r % 12 : 12, m = (g < 10 ? "0" : "") + g, v = (l < 10 ? "0" : "") + l,
                            y = this.lang.meridiem[r < 12 ? 0 : 1];
                        this.picker.find(".datetimepicker-hours thead th:eq(1)").text(m + ":" + v + " " + y.toUpperCase()), this.picker.find(".datetimepicker-minutes thead th:eq(1)").text(m + ":" + v + " " + y.toUpperCase())
                    } else this.picker.find(".datetimepicker-hours thead th:eq(1)").text(s + " " + this.lang.months[o] + " " + n), this.picker.find(".datetimepicker-minutes thead th:eq(1)").text(s + " " + this.lang.months[o] + " " + n);
                    this.picker.find("tfoot th.today").text(this.lang.today).toggle(this.todayBtn !== !1), this.updateNavArrows(), this.fillMonths();
                    var b = e(n, o - 1, 28, 0, 0, 0, 0), w = a.getDaysInMonth(b.getUTCFullYear(), b.getUTCMonth());
                    b.setUTCDate(w), b.setUTCDate(w - (b.getUTCDay() - this.weekStart + 7) % 7);
                    var x = new Date(b);
                    x.setUTCDate(x.getUTCDate() + 42), x = x.valueOf();
                    for (var C, _ = []; b.valueOf() < x;) b.getUTCDay() == this.weekStart && _.push("<tr>"), C = "", b.getUTCFullYear() < n || b.getUTCFullYear() == n && b.getUTCMonth() < o ? C += " old" : (b.getUTCFullYear() > n || b.getUTCFullYear() == n && b.getUTCMonth() > o) && (C += " new"), this.todayHighlight && b.getUTCFullYear() == p.getFullYear() && b.getUTCMonth() == p.getMonth() && b.getUTCDate() == p.getDate() && (C += " today"), b.valueOf() == f && (C += " active"), (b.valueOf() + 864e5 <= this.startDate || b.valueOf() > this.endDate || t.inArray(b.getUTCDay(), this.daysOfWeekDisabled) !== -1) && (C += " disabled"), _.push('<td class="day' + C + '">' + b.getUTCDate() + "</td>"), b.getUTCDay() == this.weekEnd && _.push("</tr>"), b.setUTCDate(b.getUTCDate() + 1);
                    this.picker.find(".datetimepicker-days tbody").empty().append(_.join("")), _ = [];
                    for (var k = "", T = "", S = "", D = 0; D < 24; D++) {
                        var M = e(n, o, s, D);
                        C = "", M.valueOf() + 36e5 <= this.startDate || M.valueOf() > this.endDate ? C += " disabled" : r == D && (C += " active"), this.showMeridian && 2 == this.lang.meridiem.length ? (T = D < 12 ? this.lang.meridiem[0] : this.lang.meridiem[1], T != S && ("" != S && _.push("</fieldset>"), _.push('<fieldset class="hour"><legend>' + T.toUpperCase() + "</legend>")), S = T, k = D % 12 ? D % 12 : 12, _.push('<span class="hour' + C + " hour_" + (D < 12 ? "am" : "pm") + '">' + k + "</span>"), 23 == D && _.push("</fieldset>")) : (k = D + ":00", _.push('<span class="hour' + C + '">' + k + "</span>"))
                    }
                    this.picker.find(".datetimepicker-hours td").html(_.join("")), _ = [], k = "", T = "", S = "";
                    for (var D = 0; D < 60; D += this.minuteStep) {
                        var M = e(n, o, s, r, D, 0);
                        C = "", M.valueOf() < this.startDate || M.valueOf() > this.endDate ? C += " disabled" : Math.floor(l / this.minuteStep) == Math.floor(D / this.minuteStep) && (C += " active"), this.showMeridian && 2 == this.lang.meridiem.length ? (T = r < 12 ? this.lang.meridiem[0] : this.lang.meridiem[1], T != S && ("" != S && _.push("</fieldset>"), _.push('<fieldset class="minute"><legend>' + T.toUpperCase() + "</legend>")), S = T, k = r % 12 ? r % 12 : 12, _.push('<span class="minute' + C + '">' + k + ":" + (D < 10 ? "0" + D : D) + "</span>"), 59 == D && _.push("</fieldset>")) : (k = D + ":00", _.push('<span class="minute' + C + '">' + r + ":" + (D < 10 ? "0" + D : D) + "</span>"))
                    }
                    this.picker.find(".datetimepicker-minutes td").html(_.join(""));
                    var P = this.date.getUTCFullYear(),
                        z = this.picker.find(".datetimepicker-months").find("th:eq(1)").text(n).end().find("span").removeClass("active");
                    P == n && z.eq(this.date.getUTCMonth()).addClass("active"), (n < h || n > d) && z.addClass("disabled"), n == h && z.slice(0, c).addClass("disabled"), n == d && z.slice(u + 1).addClass("disabled"), _ = "", n = 10 * parseInt(n / 10, 10);
                    var L = this.picker.find(".datetimepicker-years").find("th:eq(1)").text(n + "-" + (n + 9)).end().find("td");
                    n -= 1;
                    for (var D = -1; D < 11; D++) _ += '<span class="year' + (D == -1 || 10 == D ? " old" : "") + (P == n ? " active" : "") + (n < h || n > d ? " disabled" : "") + '">' + n + "</span>", n += 1;
                    L.html(_), this.place()
                }
            }, updateNavArrows: function () {
                var t = new Date(this.viewDate), e = t.getUTCFullYear(), i = t.getUTCMonth(), n = t.getUTCDate(),
                    o = t.getUTCHours();
                switch (this.viewMode) {
                    case 0:
                        this.startDate !== -(1 / 0) && e <= this.startDate.getUTCFullYear() && i <= this.startDate.getUTCMonth() && n <= this.startDate.getUTCDate() && o <= this.startDate.getUTCHours() ? this.picker.find(".prev").css({visibility: "hidden"}) : this.picker.find(".prev").css({visibility: "visible"}), this.endDate !== 1 / 0 && e >= this.endDate.getUTCFullYear() && i >= this.endDate.getUTCMonth() && n >= this.endDate.getUTCDate() && o >= this.endDate.getUTCHours() ? this.picker.find(".next").css({visibility: "hidden"}) : this.picker.find(".next").css({visibility: "visible"});
                        break;
                    case 1:
                        this.startDate !== -(1 / 0) && e <= this.startDate.getUTCFullYear() && i <= this.startDate.getUTCMonth() && n <= this.startDate.getUTCDate() ? this.picker.find(".prev").css({visibility: "hidden"}) : this.picker.find(".prev").css({visibility: "visible"}), this.endDate !== 1 / 0 && e >= this.endDate.getUTCFullYear() && i >= this.endDate.getUTCMonth() && n >= this.endDate.getUTCDate() ? this.picker.find(".next").css({visibility: "hidden"}) : this.picker.find(".next").css({visibility: "visible"});
                        break;
                    case 2:
                        this.startDate !== -(1 / 0) && e <= this.startDate.getUTCFullYear() && i <= this.startDate.getUTCMonth() ? this.picker.find(".prev").css({visibility: "hidden"}) : this.picker.find(".prev").css({visibility: "visible"}), this.endDate !== 1 / 0 && e >= this.endDate.getUTCFullYear() && i >= this.endDate.getUTCMonth() ? this.picker.find(".next").css({visibility: "hidden"}) : this.picker.find(".next").css({visibility: "visible"});
                        break;
                    case 3:
                    case 4:
                        this.startDate !== -(1 / 0) && e <= this.startDate.getUTCFullYear() ? this.picker.find(".prev").css({visibility: "hidden"}) : this.picker.find(".prev").css({visibility: "visible"}), this.endDate !== 1 / 0 && e >= this.endDate.getUTCFullYear() ? this.picker.find(".next").css({visibility: "hidden"}) : this.picker.find(".next").css({visibility: "visible"})
                }
            }, mousewheel: function (t) {
                if (t.preventDefault(), t.stopPropagation(), !this.wheelPause) {
                    this.wheelPause = !0;
                    var e = t.originalEvent, i = e.wheelDelta, n = i > 0 ? 1 : 0 === i ? 0 : -1;
                    this.wheelViewModeNavigationInverseDirection && (n = -n), this.showMode(n), setTimeout(function () {
                        this.wheelPause = !1
                    }.bind(this), this.wheelViewModeNavigationDelay)
                }
            }, click: function (i) {
                i.stopPropagation(), i.preventDefault();
                var n = t(i.target).closest("span, td, th, legend");
                if (1 == n.length) {
                    if (n.is(".disabled")) return void this.element.trigger({
                        type: "outOfRange",
                        date: this.viewDate,
                        startDate: this.startDate,
                        endDate: this.endDate
                    });
                    switch (n[0].nodeName.toLowerCase()) {
                        case"th":
                            switch (n[0].className) {
                                case"switch":
                                    this.showMode(1);
                                    break;
                                case"prev":
                                case"next":
                                    var o = a.modes[this.viewMode].navStep * ("prev" == n[0].className ? -1 : 1);
                                    switch (this.viewMode) {
                                        case 0:
                                            this.viewDate = this.moveHour(this.viewDate, o);
                                            break;
                                        case 1:
                                            this.viewDate = this.moveDate(this.viewDate, o);
                                            break;
                                        case 2:
                                            this.viewDate = this.moveMonth(this.viewDate, o);
                                            break;
                                        case 3:
                                        case 4:
                                            this.viewDate = this.moveYear(this.viewDate, o)
                                    }
                                    this.fill();
                                    break;
                                case"today":
                                    var s = new Date;
                                    s = e(s.getFullYear(), s.getMonth(), s.getDate(), s.getHours(), s.getMinutes(), s.getSeconds(), 0), s < this.startDate ? s = this.startDate : s > this.endDate && (s = this.endDate), this.viewMode = this.startViewMode, this.showMode(0), this._setDate(s), this.fill(), this.autoclose && this.hide()
                            }
                            break;
                        case"span":
                            if (!n.is(".disabled")) {
                                var r = this.viewDate.getUTCFullYear(), l = this.viewDate.getUTCMonth(),
                                    h = this.viewDate.getUTCDate(), c = this.viewDate.getUTCHours(),
                                    d = this.viewDate.getUTCMinutes(), u = this.viewDate.getUTCSeconds();
                                if (n.is(".month") ? (this.viewDate.setUTCDate(1), l = n.parent().find("span").index(n), h = this.viewDate.getUTCDate(), this.viewDate.setUTCMonth(l), this.element.trigger({
                                    type: "changeMonth",
                                    date: this.viewDate
                                }), this.viewSelect >= 3 && this._setDate(e(r, l, h, c, d, u, 0))) : n.is(".year") ? (this.viewDate.setUTCDate(1), r = parseInt(n.text(), 10) || 0, this.viewDate.setUTCFullYear(r), this.element.trigger({
                                    type: "changeYear",
                                    date: this.viewDate
                                }), this.viewSelect >= 4 && this._setDate(e(r, l, h, c, d, u, 0))) : n.is(".hour") ? (c = parseInt(n.text(), 10) || 0, (n.hasClass("hour_am") || n.hasClass("hour_pm")) && (12 == c && n.hasClass("hour_am") ? c = 0 : 12 != c && n.hasClass("hour_pm") && (c += 12)), this.viewDate.setUTCHours(c), this.element.trigger({
                                    type: "changeHour",
                                    date: this.viewDate
                                }), this.viewSelect >= 1 && this._setDate(e(r, l, h, c, d, u, 0))) : n.is(".minute") && (d = parseInt(n.text().substr(n.text().indexOf(":") + 1), 10) || 0, this.viewDate.setUTCMinutes(d), this.element.trigger({
                                    type: "changeMinute",
                                    date: this.viewDate
                                }), this.viewSelect >= 0 && this._setDate(e(r, l, h, c, d, u, 0))), 0 != this.viewMode) {
                                    var f = this.viewMode;
                                    this.showMode(-1), this.fill(), f == this.viewMode && this.autoclose && this.hide()
                                } else this.fill(), this.autoclose && this.hide()
                            }
                            break;
                        case"td":
                            if (n.is(".day") && !n.is(".disabled")) {
                                var h = parseInt(n.text(), 10) || 1, r = this.viewDate.getUTCFullYear(),
                                    l = this.viewDate.getUTCMonth(), c = this.viewDate.getUTCHours(),
                                    d = this.viewDate.getUTCMinutes(), u = this.viewDate.getUTCSeconds();
                                n.is(".old") ? 0 === l ? (l = 11, r -= 1) : l -= 1 : n.is(".new") && (11 == l ? (l = 0, r += 1) : l += 1), this.viewDate.setUTCFullYear(r), this.viewDate.setUTCMonth(l, h), this.element.trigger({
                                    type: "changeDay",
                                    date: this.viewDate
                                }), this.viewSelect >= 2 && this._setDate(e(r, l, h, c, d, u, 0));
                                var f = this.viewMode;
                                this.showMode(-1), this.fill(), f == this.viewMode && this.autoclose && this.hide()
                            }
                    }
                }
            }, _setDate: function (t, e) {
                e && "date" != e || (this.date = t), e && "view" != e || (this.viewDate = t), this.fill(), this.setValue();
                var i;
                this.isInput ? i = this.element : this.component && (i = this.element.find("input")), i && (i.change(), this.autoclose && (!e || "date" == e)), this.element.trigger({
                    type: "changeDate",
                    date: this.date
                }), null === t && (this.date = this.viewDate)
            }, moveMinute: function (t, e) {
                if (!e) return t;
                var i = new Date(t.valueOf());
                return i.setUTCMinutes(i.getUTCMinutes() + e * this.minuteStep), i
            }, moveHour: function (t, e) {
                if (!e) return t;
                var i = new Date(t.valueOf());
                return i.setUTCHours(i.getUTCHours() + e), i
            }, moveDate: function (t, e) {
                if (!e) return t;
                var i = new Date(t.valueOf());
                return i.setUTCDate(i.getUTCDate() + e), i
            }, moveMonth: function (t, e) {
                if (!e) return t;
                var i, n, o = new Date(t.valueOf()), a = o.getUTCDate(), s = o.getUTCMonth(), r = Math.abs(e);
                if (e = e > 0 ? 1 : -1, 1 == r) n = e == -1 ? function () {
                    return o.getUTCMonth() == s
                } : function () {
                    return o.getUTCMonth() != i
                }, i = s + e, o.setUTCMonth(i), (i < 0 || i > 11) && (i = (i + 12) % 12); else {
                    for (var l = 0; l < r; l++) o = this.moveMonth(o, e);
                    i = o.getUTCMonth(), o.setUTCDate(a), n = function () {
                        return i != o.getUTCMonth()
                    }
                }
                for (; n();) o.setUTCDate(--a), o.setUTCMonth(i);
                return o
            }, moveYear: function (t, e) {
                return this.moveMonth(t, 12 * e)
            }, dateWithinRange: function (t) {
                return t >= this.startDate && t <= this.endDate
            }, keydown: function (t) {
                if (this.picker.is(":not(:visible)")) return void (27 == t.keyCode && this.show());
                var e, i, n, o = !1;
                switch (t.keyCode) {
                    case 27:
                        this.hide(), t.preventDefault();
                        break;
                    case 37:
                    case 39:
                        if (!this.keyboardNavigation) break;
                        e = 37 == t.keyCode ? -1 : 1, viewMode = this.viewMode, t.ctrlKey ? viewMode += 2 : t.shiftKey && (viewMode += 1), 4 == viewMode ? (i = this.moveYear(this.date, e), n = this.moveYear(this.viewDate, e)) : 3 == viewMode ? (i = this.moveMonth(this.date, e), n = this.moveMonth(this.viewDate, e)) : 2 == viewMode ? (i = this.moveDate(this.date, e), n = this.moveDate(this.viewDate, e)) : 1 == viewMode ? (i = this.moveHour(this.date, e), n = this.moveHour(this.viewDate, e)) : 0 == viewMode && (i = this.moveMinute(this.date, e), n = this.moveMinute(this.viewDate, e)), this.dateWithinRange(i) && (this.date = i, this.viewDate = n, this.setValue(), this.update(), t.preventDefault(), o = !0);
                        break;
                    case 38:
                    case 40:
                        if (!this.keyboardNavigation) break;
                        e = 38 == t.keyCode ? -1 : 1, viewMode = this.viewMode, t.ctrlKey ? viewMode += 2 : t.shiftKey && (viewMode += 1), 4 == viewMode ? (i = this.moveYear(this.date, e), n = this.moveYear(this.viewDate, e)) : 3 == viewMode ? (i = this.moveMonth(this.date, e), n = this.moveMonth(this.viewDate, e)) : 2 == viewMode ? (i = this.moveDate(this.date, 7 * e), n = this.moveDate(this.viewDate, 7 * e)) : 1 == viewMode ? this.showMeridian ? (i = this.moveHour(this.date, 6 * e), n = this.moveHour(this.viewDate, 6 * e)) : (i = this.moveHour(this.date, 4 * e), n = this.moveHour(this.viewDate, 4 * e)) : 0 == viewMode && (i = this.moveMinute(this.date, 4 * e), n = this.moveMinute(this.viewDate, 4 * e)), this.dateWithinRange(i) && (this.date = i, this.viewDate = n, this.setValue(), this.update(), t.preventDefault(), o = !0);
                        break;
                    case 13:
                        if (0 != this.viewMode) {
                            var a = this.viewMode;
                            this.showMode(-1), this.fill(), a == this.viewMode && this.autoclose && this.hide()
                        } else this.fill(), this.autoclose && this.hide();
                        t.preventDefault();
                        break;
                    case 9:
                        this.hide()
                }
                if (o) {
                    var s;
                    this.isInput ? s = this.element : this.component && (s = this.element.find("input")), s && s.change(), this.element.trigger({
                        type: "changeDate",
                        date: this.date
                    })
                }
            }, showMode: function (t) {
                if (t) {
                    var e = Math.max(0, Math.min(a.modes.length - 1, this.viewMode + t));
                    e >= this.minView && e <= this.maxView && (this.element.trigger({
                        type: "changeMode",
                        date: this.viewDate,
                        oldViewMode: this.viewMode,
                        newViewMode: e
                    }), this.viewMode = e)
                }
                this.picker.find(">div").hide().filter(".datetimepicker-" + a.modes[this.viewMode].clsName).css("display", "block"), this.updateNavArrows()
            }, reset: function (t) {
                this._setDate(null, "date")
            }
        }, t.fn.datetimepicker = function (e) {
            var n = Array.apply(null, arguments);
            return n.shift(), this.each(function () {
                var o = t(this), a = o.data("datetimepicker"), s = "object" == typeof e && e;
                a || o.data("datetimepicker", a = new i(this, t.extend({}, t.fn.datetimepicker.defaults, o.data(), s))), "string" == typeof e && "function" == typeof a[e] && a[e].apply(a, n)
            })
        }, t.fn.datetimepicker.defaults = {pickerPosition: "auto-right"}, t.fn.datetimepicker.Constructor = i;
        var n = t.fn.datetimepicker.dates = {
            en: {
                days: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
                daysShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
                daysMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa", "Su"],
                months: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
                monthsShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                meridiem: ["am", "pm"],
                suffix: ["st", "nd", "rd", "th"],
                today: "Today"
            },
            "zh-cn": {
                days: ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六", "星期日"],
                daysShort: ["周日", "周一", "周二", "周三", "周四", "周五", "周六", "周日"],
                daysMin: ["日", "一", "二", "三", "四", "五", "六", "日"],
                months: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
                monthsShort: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
                today: "今日",
                suffix: [],
                meridiem: []
            },
            "zh-tw": {
                days: ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六", "星期日"],
                daysShort: ["周日", "周一", "周二", "周三", "周四", "周五", "周六", "周日"],
                daysMin: ["日", "一", "二", "三", "四", "五", "六", "日"],
                months: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
                monthsShort: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
                today: "今天",
                suffix: [],
                meridiem: ["上午", "下午"]
            }
        }, o = function (e) {
            var i = n[e];
            return i || (i = t.zui && t.zui.getLangData ? n[e] = t.zui.getLangData("datetimepicker", this.language, n) : n.en), i
        }, a = {
            modes: [{clsName: "minutes", navFnc: "Hours", navStep: 1}, {
                clsName: "hours",
                navFnc: "Date",
                navStep: 1
            }, {clsName: "days", navFnc: "Month", navStep: 1}, {
                clsName: "months",
                navFnc: "FullYear",
                navStep: 1
            }, {clsName: "years", navFnc: "FullYear", navStep: 10}],
            isLeapYear: function (t) {
                return t % 4 === 0 && t % 100 !== 0 || t % 400 === 0
            },
            getDaysInMonth: function (t, e) {
                return [31, a.isLeapYear(t) ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][e]
            },
            getDefaultFormat: function (t, e) {
                if ("standard" == t) return "input" == e ? "yyyy-mm-dd hh:ii" : "yyyy-mm-dd hh:ii:ss";
                if ("php" == t) return "input" == e ? "Y-m-d H:i" : "Y-m-d H:i:s";
                throw new Error("Invalid format type.")
            },
            validParts: function (t) {
                if ("standard" == t) return /hh?|HH?|p|P|ii?|ss?|dd?|DD?|mm?|MM?|yy(?:yy)?/g;
                if ("php" == t) return /[dDjlNwzFmMnStyYaABgGhHis]/g;
                throw new Error("Invalid format type.")
            },
            nonpunctuation: /[^ -\/:-@\[-`{-~\t\n\rTZ]+/g,
            parseFormat: function (t, e) {
                var i = t.replace(this.validParts(e), "\0").split("\0"), n = t.match(this.validParts(e));
                if (!i || !i.length || !n || 0 == n.length) throw new Error("Invalid date format.");
                return {separators: i, parts: n}
            },
            parseDate: function (n, a, s, r) {
                if (n instanceof Date) {
                    var l = new Date(n.valueOf() - 6e4 * n.getTimezoneOffset());
                    return l.setMilliseconds(0), l
                }
                if (/^\d{4}\-\d{1,2}\-\d{1,2}$/.test(n) && (a = this.parseFormat("yyyy-mm-dd", r)), /^\d{4}\-\d{1,2}\-\d{1,2}[T ]\d{1,2}\:\d{1,2}$/.test(n) && (a = this.parseFormat("yyyy-mm-dd hh:ii", r)), /^\d{4}\-\d{1,2}\-\d{1,2}[T ]\d{1,2}\:\d{1,2}\:\d{1,2}[Z]{0,1}$/.test(n) && (a = this.parseFormat("yyyy-mm-dd hh:ii:ss", r)), /^[-+]\d+[dmwy]([\s,]+[-+]\d+[dmwy])*$/.test(n)) {
                    var h, c, d = /([-+]\d+)([dmwy])/, u = n.match(/([-+]\d+)([dmwy])/g);
                    n = new Date;
                    for (var f = 0; f < u.length; f++) switch (h = d.exec(u[f]), c = parseInt(h[1]), h[2]) {
                        case"d":
                            n.setUTCDate(n.getUTCDate() + c);
                            break;
                        case"m":
                            n = i.prototype.moveMonth.call(i.prototype, n, c);
                            break;
                        case"w":
                            n.setUTCDate(n.getUTCDate() + 7 * c);
                            break;
                        case"y":
                            n = i.prototype.moveYear.call(i.prototype, n, c)
                    }
                    return e(n.getUTCFullYear(), n.getUTCMonth(), n.getUTCDate(), n.getUTCHours(), n.getUTCMinutes(), n.getUTCSeconds(), 0)
                }
                var p, g, h, u = n && n.match(this.nonpunctuation) || [], n = new Date(0, 0, 0, 0, 0, 0, 0), m = {},
                    v = ["hh", "h", "ii", "i", "ss", "s", "yyyy", "yy", "M", "MM", "m", "mm", "D", "DD", "d", "dd", "H", "HH", "p", "P"],
                    y = {
                        hh: function (t, e) {
                            return t.setUTCHours(e)
                        }, h: function (t, e) {
                            return t.setUTCHours(e)
                        }, HH: function (t, e) {
                            return t.setUTCHours(12 == e ? 0 : e)
                        }, H: function (t, e) {
                            return t.setUTCHours(12 == e ? 0 : e)
                        }, ii: function (t, e) {
                            return t.setUTCMinutes(e)
                        }, i: function (t, e) {
                            return t.setUTCMinutes(e)
                        }, ss: function (t, e) {
                            return t.setUTCSeconds(e)
                        }, s: function (t, e) {
                            return t.setUTCSeconds(e)
                        }, yyyy: function (t, e) {
                            return t.setUTCFullYear(e)
                        }, yy: function (t, e) {
                            return t.setUTCFullYear(2e3 + e)
                        }, m: function (t, e) {
                            for (e -= 1; e < 0;) e += 12;
                            for (e %= 12, t.setUTCMonth(e); t.getUTCMonth() != e;) t.setUTCDate(t.getUTCDate() - 1);
                            return t
                        }, d: function (t, e) {
                            return t.setUTCDate(e)
                        }, p: function (t, e) {
                            return t.setUTCHours(1 == e ? t.getUTCHours() + 12 : t.getUTCHours())
                        }
                    };
                if (y.M = y.MM = y.mm = y.m, y.dd = y.d, y.P = y.p, n = e(n.getFullYear(), n.getMonth(), n.getDate(), n.getHours(), n.getMinutes(), n.getSeconds()), u.length == a.parts.length) {
                    for (var f = 0, b = a.parts.length; f < b; f++) {
                        if (p = parseInt(u[f], 10), h = a.parts[f], isNaN(p)) switch (h) {
                            case"MM":
                                g = t(o(s).months).filter(function () {
                                    var t = this.slice(0, u[f].length), e = u[f].slice(0, t.length);
                                    return t == e
                                }), p = t.inArray(g[0], o(s).months) + 1;
                                break;
                            case"M":
                                g = t(o(s).monthsShort).filter(function () {
                                    var t = this.slice(0, u[f].length), e = u[f].slice(0, t.length);
                                    return t == e
                                }), p = t.inArray(g[0], o(s).monthsShort) + 1;
                                break;
                            case"p":
                            case"P":
                                p = t.inArray(u[f].toLowerCase(), o(s).meridiem)
                        }
                        m[h] = p
                    }
                    for (var w, f = 0; f < v.length; f++) w = v[f], w in m && !isNaN(m[w]) && y[w](n, m[w])
                }
                return n
            },
            formatDate: function (e, i, n, s) {
                if (null == e) return "";
                var r;
                if ("standard" == s) r = {
                    yy: e.getUTCFullYear().toString().substring(2),
                    yyyy: e.getUTCFullYear(),
                    m: e.getUTCMonth() + 1,
                    M: o(n).monthsShort[e.getUTCMonth()],
                    MM: o(n).months[e.getUTCMonth()],
                    d: e.getUTCDate(),
                    D: o(n).daysShort[e.getUTCDay()],
                    DD: o(n).days[e.getUTCDay()],
                    p: 2 == o(n).meridiem.length ? o(n).meridiem[e.getUTCHours() < 12 ? 0 : 1] : "",
                    h: e.getUTCHours(),
                    i: e.getUTCMinutes(),
                    s: e.getUTCSeconds()
                }, 2 == o(n).meridiem.length ? r.H = r.h % 12 == 0 ? 12 : r.h % 12 : r.H = r.h, r.HH = (r.H < 10 ? "0" : "") + r.H, r.P = r.p.toUpperCase(), r.hh = (r.h < 10 ? "0" : "") + r.h, r.ii = (r.i < 10 ? "0" : "") + r.i, r.ss = (r.s < 10 ? "0" : "") + r.s, r.dd = (r.d < 10 ? "0" : "") + r.d, r.mm = (r.m < 10 ? "0" : "") + r.m; else {
                    if ("php" != s) throw new Error("Invalid format type.");
                    r = {
                        y: e.getUTCFullYear().toString().substring(2),
                        Y: e.getUTCFullYear(),
                        F: o(n).months[e.getUTCMonth()],
                        M: o(n).monthsShort[e.getUTCMonth()],
                        n: e.getUTCMonth() + 1,
                        t: a.getDaysInMonth(e.getUTCFullYear(), e.getUTCMonth()),
                        j: e.getUTCDate(),
                        l: o(n).days[e.getUTCDay()],
                        D: o(n).daysShort[e.getUTCDay()],
                        w: e.getUTCDay(),
                        N: 0 == e.getUTCDay() ? 7 : e.getUTCDay(),
                        S: e.getUTCDate() % 10 <= o(n).suffix.length ? o(n).suffix[e.getUTCDate() % 10 - 1] : "",
                        a: 2 == o(n).meridiem.length ? o(n).meridiem[e.getUTCHours() < 12 ? 0 : 1] : "",
                        g: e.getUTCHours() % 12 == 0 ? 12 : e.getUTCHours() % 12,
                        G: e.getUTCHours(),
                        i: e.getUTCMinutes(),
                        s: e.getUTCSeconds()
                    }, r.m = (r.n < 10 ? "0" : "") + r.n, r.d = (r.j < 10 ? "0" : "") + r.j, r.A = r.a.toString().toUpperCase(), r.h = (r.g < 10 ? "0" : "") + r.g, r.H = (r.G < 10 ? "0" : "") + r.G, r.i = (r.i < 10 ? "0" : "") + r.i, r.s = (r.s < 10 ? "0" : "") + r.s
                }
                for (var e = [], l = t.extend([], i.separators), h = 0, c = i.parts.length; h < c; h++) l.length && e.push(l.shift()), e.push(r[i.parts[h]]);
                return l.length && e.push(l.shift()), e.join("")
            },
            convertViewMode: function (t) {
                switch (t) {
                    case 4:
                    case"decade":
                        t = 4;
                        break;
                    case 3:
                    case"year":
                        t = 3;
                        break;
                    case 2:
                    case"month":
                        t = 2;
                        break;
                    case 1:
                    case"day":
                        t = 1;
                        break;
                    case 0:
                    case"hour":
                        t = 0
                }
                return t
            },
            headTemplate: '<thead><tr><th class="prev"><i class="icon-arrow-left"/></th><th colspan="5" class="switch"></th><th class="next"><i class="icon-arrow-right"/></th></tr></thead>',
            contTemplate: '<tbody><tr><td colspan="7"></td></tr></tbody>',
            footTemplate: '<tfoot><tr><th colspan="7" class="today"></th></tr></tfoot>'
        };
        a.template = '<div class="datetimepicker"><div class="datetimepicker-minutes"><table class=" table-condensed">' + a.headTemplate + a.contTemplate + a.footTemplate + '</table></div><div class="datetimepicker-hours"><table class=" table-condensed">' + a.headTemplate + a.contTemplate + a.footTemplate + '</table></div><div class="datetimepicker-days"><table class=" table-condensed">' + a.headTemplate + "<tbody></tbody>" + a.footTemplate + '</table></div><div class="datetimepicker-months"><table class="table-condensed">' + a.headTemplate + a.contTemplate + a.footTemplate + '</table></div><div class="datetimepicker-years"><table class="table-condensed">' + a.headTemplate + a.contTemplate + a.footTemplate + "</table></div></div>", t.fn.datetimepicker.DPGlobal = a, t.fn.datetimepicker.noConflict = function () {
            return t.fn.datetimepicker = old, this
        }, t(document).on("focus.datetimepicker.data-api click.datetimepicker.data-api", '[data-provide="datetimepicker"]', function (e) {
            var i = t(this);
            i.data("datetimepicker") || (e.preventDefault(), i.datetimepicker("show"))
        }), t(function () {
            t('[data-provide="datetimepicker-inline"]').datetimepicker()
        })
    }(window.jQuery),/*! bootbox.js v4.4.0 http://bootboxjs.com/license.txt */
    function (t, e) {
        "use strict";
        "function" == typeof define && define.amd ? define(["jquery"], e) : "object" == typeof exports ? module.exports = e(require("jquery")) : t.bootbox = e(t.jQuery)
    }(this, function t(e, i) {
        "use strict";

        function n(t) {
            var i = e.zui && e.zui.getLangData ? e.zui.getLangData("bootbox", p.locale, m) : m[p.locale];
            return i ? i[t] : m.en[t]
        }

        function o(t, e, i) {
            t.stopPropagation(), t.preventDefault();
            var n = "function" == typeof i && i.call(e, t) === !1;
            n || e.modal("hide")
        }

        function a(t) {
            var e, i = 0;
            for (e in t) i++;
            return i
        }

        function s(t, i) {
            var n = 0;
            e.each(t, function (t, e) {
                i(t, e, n++)
            })
        }

        function r(t) {
            var i, n;
            if ("object" != typeof t) throw new Error("Please supply an object of options");
            if (!t.message) throw new Error("Please specify a message");
            return t = e.extend({}, p, t), t.buttons || (t.buttons = {}), i = t.buttons, n = a(i), s(i, function (t, o, a) {
                if ("function" == typeof o && (o = i[t] = {callback: o}), "object" !== e.type(o)) throw new Error("button with key " + t + " must be an object");
                o.label || (o.label = t), o.className || (2 === n && ("ok" === t || "confirm" === t) || 1 === n ? o.className = "btn-primary" : o.className = "btn-default")
            }), t
        }

        function l(t, e) {
            var i = t.length, n = {};
            if (i < 1 || i > 2) throw new Error("Invalid argument length");
            return 2 === i || "string" == typeof t[0] ? (n[e[0]] = t[0], n[e[1]] = t[1]) : n = t[0], n
        }

        function h(t, i, n) {
            return e.extend(!0, {}, t, l(i, n))
        }

        function c(t, e, i, n) {
            var o = {className: "bootbox-" + t, buttons: d.apply(null, e)};
            return u(h(o, n, i), e)
        }

        function d() {
            for (var t = {}, e = 0, i = arguments.length; e < i; e++) {
                var o = arguments[e], a = o.toLowerCase(), s = o.toUpperCase();
                t[a] = {label: n(s)}
            }
            return t
        }

        function u(t, e) {
            var n = {};
            return s(e, function (t, e) {
                n[e] = !0
            }), s(t.buttons, function (t) {
                if (n[t] === i) throw new Error("button key " + t + " is not allowed (options are " + e.join("\n") + ")")
            }), t
        }

        var f = {
            dialog: "<div class='bootbox modal' tabindex='-1' role='dialog'><div class='modal-dialog'><div class='modal-content'><div class='modal-body'><div class='bootbox-body'></div></div></div></div></div>",
            header: "<div class='modal-header'><h4 class='modal-title'></h4></div>",
            footer: "<div class='modal-footer'></div>",
            closeButton: "<button type='button' class='bootbox-close-button close' data-dismiss='modal' aria-hidden='true'>&times;</button>",
            form: "<form class='bootbox-form'></form>",
            inputs: {
                text: "<input class='bootbox-input bootbox-input-text form-control' autocomplete=off type=text />",
                textarea: "<textarea class='bootbox-input bootbox-input-textarea form-control'></textarea>",
                email: "<input class='bootbox-input bootbox-input-email form-control' autocomplete='off' type='email' />",
                select: "<select class='bootbox-input bootbox-input-select form-control'></select>",
                checkbox: "<div class='checkbox'><label><input class='bootbox-input bootbox-input-checkbox' type='checkbox' /></label></div>",
                date: "<input class='bootbox-input bootbox-input-date form-control' autocomplete=off type='date' />",
                time: "<input class='bootbox-input bootbox-input-time form-control' autocomplete=off type='time' />",
                number: "<input class='bootbox-input bootbox-input-number form-control' autocomplete=off type='number' />",
                password: "<input class='bootbox-input bootbox-input-password form-control' autocomplete='off' type='password' />"
            }
        }, p = {
            locale: e.zui && e.zui.clientLang ? e.zui.clientLang() : "en",
            backdrop: "static",
            animate: !0,
            className: null,
            closeButton: !0,
            show: !0,
            container: "body"
        }, g = {};
        g.alert = function () {
            var t;
            if (t = c("alert", ["ok"], ["message", "callback"], arguments), t.callback && "function" != typeof t.callback) throw new Error("alert requires callback property to be a function when provided");
            return t.buttons.ok.callback = t.onEscape = function () {
                return "function" != typeof t.callback || t.callback.call(this)
            }, g.dialog(t)
        }, g.confirm = function () {
            var t;
            if (t = c("confirm", ["confirm", "cancel"], ["message", "callback"], arguments), t.buttons.cancel.callback = t.onEscape = function () {
                return t.callback.call(this, !1)
            }, t.buttons.confirm.callback = function () {
                return t.callback.call(this, !0)
            }, "function" != typeof t.callback) throw new Error("confirm requires a callback");
            return g.dialog(t)
        }, g.prompt = function () {
            var t, n, o, a, r, l, c;
            if (a = e(f.form), n = {
                className: "bootbox-prompt",
                buttons: d("cancel", "confirm"),
                value: "",
                inputType: "text"
            }, t = u(h(n, arguments, ["title", "callback"]), ["confirm", "cancel"]), l = t.show === i || t.show, t.message = a, t.buttons.cancel.callback = t.onEscape = function () {
                return t.callback.call(this, null)
            }, t.buttons.confirm.callback = function () {
                var i;
                switch (t.inputType) {
                    case"text":
                    case"textarea":
                    case"email":
                    case"select":
                    case"date":
                    case"time":
                    case"number":
                    case"password":
                        i = r.val();
                        break;
                    case"checkbox":
                        var n = r.find("input:checked");
                        i = [], s(n, function (t, n) {
                            i.push(e(n).val())
                        })
                }
                return t.callback.call(this, i)
            }, t.show = !1, !t.title) throw new Error("prompt requires a title");
            if ("function" != typeof t.callback) throw new Error("prompt requires a callback");
            if (!f.inputs[t.inputType]) throw new Error("invalid prompt type");
            switch (r = e(f.inputs[t.inputType]), t.inputType) {
                case"text":
                case"textarea":
                case"email":
                case"date":
                case"time":
                case"number":
                case"password":
                    r.val(t.value);
                    break;
                case"select":
                    var p = {};
                    if (c = t.inputOptions || [], !Array.isArray(c)) throw new Error("Please pass an array of input options");
                    if (!c.length) throw new Error("prompt with select requires options");
                    s(c, function (t, n) {
                        var o = r;
                        if (n.value === i || n.text === i) throw new Error("given options in wrong format");
                        n.group && (p[n.group] || (p[n.group] = e("<optgroup/>").attr("label", n.group)), o = p[n.group]), o.append("<option value='" + n.value + "'>" + n.text + "</option>")
                    }), s(p, function (t, e) {
                        r.append(e)
                    }), r.val(t.value);
                    break;
                case"checkbox":
                    var m = Array.isArray(t.value) ? t.value : [t.value];
                    if (c = t.inputOptions || [], !c.length) throw new Error("prompt with checkbox requires options");
                    if (!c[0].value || !c[0].text) throw new Error("given options in wrong format");
                    r = e("<div/>"), s(c, function (i, n) {
                        var o = e(f.inputs[t.inputType]);
                        o.find("input").attr("value", n.value), o.find("label").append(n.text), s(m, function (t, e) {
                            e === n.value && o.find("input").prop("checked", !0)
                        }), r.append(o)
                    })
            }
            return t.placeholder && r.attr("placeholder", t.placeholder), t.pattern && r.attr("pattern", t.pattern), t.maxlength && r.attr("maxlength", t.maxlength), a.append(r), a.on("submit", function (t) {
                t.preventDefault(), t.stopPropagation(), o.find(".btn-primary").click()
            }), o = g.dialog(t), o.off("shown.zui.modal"), o.on("shown.zui.modal", function () {
                r.focus()
            }), l === !0 && o.modal("show"), o
        }, g.dialog = function (t) {
            t = r(t);
            var n = e(f.dialog), a = n.find(".modal-dialog"), l = n.find(".modal-body"), h = t.buttons, c = "",
                d = {onEscape: t.onEscape};
            if (e.fn.modal === i) throw new Error("$.fn.modal is not defined; please double check you have included the Bootstrap JavaScript library. See http://getbootstrap.com/javascript/ for more details.");
            if (s(h, function (t, e) {
                c += "<button data-bb-handler='" + t + "' type='button' class='btn " + e.className + "'>" + e.label + "</button>", d[t] = e.callback
            }), l.find(".bootbox-body").html(t.message), t.animate === !0 && n.addClass("fade"), t.className && n.addClass(t.className), "large" === t.size ? a.addClass("modal-lg") : "small" === t.size && a.addClass("modal-sm"), t.title && l.before(f.header), t.closeButton) {
                var u = e(f.closeButton);
                t.title ? n.find(".modal-header").prepend(u) : u.css("margin-top", "-10px").prependTo(l)
            }
            return t.title && n.find(".modal-title").html(t.title), c.length && (l.after(f.footer), n.find(".modal-footer").html(c)), n.on("hidden.zui.modal", function (t) {
                t.target === this && n.remove()
            }), n.on("shown.zui.modal", function () {
                n.find(".btn-primary:first").focus()
            }), "static" !== t.backdrop && n.on("click.dismiss.zui.modal", function (t) {
                n.children(".modal-backdrop").length && (t.currentTarget = n.children(".modal-backdrop").get(0)), t.target === t.currentTarget && n.trigger("escape.close.bb")
            }), n.on("escape.close.bb", function (t) {
                d.onEscape && o(t, n, d.onEscape)
            }), n.on("click", ".modal-footer button", function (t) {
                var i = e(this).data("bb-handler");
                o(t, n, d[i])
            }), n.on("click", ".bootbox-close-button", function (t) {
                o(t, n, d.onEscape)
            }), n.on("keyup", function (t) {
                27 === t.which && n.trigger("escape.close.bb")
            }), e(t.container).append(n), n.modal({
                backdrop: !!t.backdrop && "static",
                keyboard: !1,
                show: !1
            }), t.show && n.modal("show"), n
        }, g.setDefaults = function () {
            var t = {};
            2 === arguments.length ? t[arguments[0]] = arguments[1] : t = arguments[0], e.extend(p, t)
        }, g.hideAll = function () {
            return e(".bootbox").modal("hide"), g
        };
        var m = {
            en: {OK: "OK", CANCEL: "Cancel", CONFIRM: "Confirm"},
            zh_cn: {OK: "确认", CANCEL: "取消", CONFIRM: "确认"},
            zh_tw: {OK: "確認", CANCEL: "取消", CONFIRM: "確認"}
        };
        return g.addLocale = function (t, i) {
            return e.each(["OK", "CANCEL", "CONFIRM"], function (t, e) {
                if (!i[e]) throw new Error("Please supply a translation for '" + e + "'")
            }), m[t] = {OK: i.OK, CANCEL: i.CANCEL, CONFIRM: i.CONFIRM}, g
        }, g.removeLocale = function (t) {
            return delete m[t], g
        }, g.setLocale = function (t) {
            return g.setDefaults("locale", t)
        }, g.init = function (i) {
            return t(i || e)
        }, g
    }),/*!
Chosen, a Select Box Enhancer for jQuery and Prototype
by Patrick Filler for Harvest, http://getharvest.com

Version 1.1.0
Full source at https://github.com/harvesthq/chosen
Copyright (c) 2011 Harvest http://getharvest.com

MIT License, https://github.com/harvesthq/chosen/blob/master/LICENSE.md
*/
    function () {
        var t, e, i, n, o, a = {}.hasOwnProperty, s = function (t, e) {
            function i() {
                this.constructor = t
            }

            for (var n in e) a.call(e, n) && (t[n] = e[n]);
            return i.prototype = e.prototype, t.prototype = new i, t.__super__ = e.prototype, t
        }, r = {
            zh_cn: {no_results_text: "没有找到"},
            zh_tw: {no_results_text: "沒有找到"},
            en: {no_results_text: "No results match"}
        }, l = {};
        n = function () {
            function e() {
                this.options_index = 0, this.parsed = []
            }

            return e.prototype.add_node = function (t) {
                return "OPTGROUP" === t.nodeName.toUpperCase() ? this.add_group(t) : this.add_option(t)
            }, e.prototype.add_group = function (e) {
                var i, n, o, a, s, r;
                for (i = this.parsed.length, this.parsed.push({
                    array_index: i,
                    group: !0,
                    label: this.escapeExpression(e.label),
                    children: 0,
                    disabled: e.disabled,
                    title: e.title,
                    search_keys: t.trim(e.getAttribute("data-keys") || "").replace(/,/g, " ")
                }), s = e.childNodes, r = [], o = 0, a = s.length; o < a; o++) n = s[o], r.push(this.add_option(n, i, e.disabled));
                return r
            }, e.prototype.add_option = function (e, i, n) {
                if ("OPTION" === e.nodeName.toUpperCase()) return "" !== e.text ? (null != i && (this.parsed[i].children += 1), this.parsed.push({
                    array_index: this.parsed.length,
                    options_index: this.options_index,
                    value: e.value,
                    text: e.text,
                    title: e.title,
                    html: e.innerHTML,
                    selected: e.selected,
                    disabled: n === !0 ? n : e.disabled,
                    group_array_index: i,
                    classes: e.className,
                    style: e.style.cssText,
                    data: e.getAttribute("data-data"),
                    search_keys: (t.trim(e.getAttribute("data-keys") || "") + e.value).replace(/,/, " ")
                })) : this.parsed.push({
                    array_index: this.parsed.length,
                    options_index: this.options_index,
                    empty: !0
                }), this.options_index += 1
            }, e.prototype.escapeExpression = function (t) {
                var e, i;
                return null == t || t === !1 ? "" : /[\&\<\>\"\'\`]/.test(t) ? (e = {
                    "<": "&lt;",
                    ">": "&gt;",
                    '"': "&quot;",
                    "'": "&#x27;",
                    "`": "&#x60;"
                }, i = /&(?!\w+;)|[\<\>\"\'\`]/g, t.replace(i, function (t) {
                    return e[t] || "&amp;"
                })) : t
            }, e
        }(), n.select_to_array = function (t) {
            var e, i, o, a, s;
            for (i = new n, s = t.childNodes, o = 0, a = s.length; o < a; o++) e = s[o], i.add_node(e);
            return i.parsed
        }, e = function () {
            function e(i, n) {
                if (this.form_field = i, this.options = t.extend({}, l, null != n ? n : {}), e.browser_is_supported()) {
                    var o = this.options.lang || t.zui.clientLang ? t.zui.clientLang() : "en",
                        a = t.zui.clientLang ? t.zui.clientLang() : "en";
                    t.isPlainObject(o) ? this.lang = t.zui.getLangData ? t.zui.getLangData("chosen", a, r) : t.extend(o, r.en, r[a]) : this.lang = t.zui.getLangData ? t.zui.getLangData("chosen", o, r) : r[o || a] || r.en, this.is_multiple = this.form_field.multiple, this.set_default_text(), this.set_default_values(), this.setup(), this.set_up_html(), this.register_observers()
                }
            }

            return e.prototype.set_default_values = function () {
                var t = this, e = t.options;
                t.click_test_action = function (e) {
                    return t.test_active_click(e)
                }, t.activate_action = function (e) {
                    return t.activate_field(e)
                }, t.active_field = !1, t.mouse_on_container = !1, t.results_showing = !1, t.result_highlighted = null, t.allow_single_deselect = null != e.allow_single_deselect && null != this.form_field.options[0] && "" === t.form_field.options[0].text && e.allow_single_deselect, t.disable_search_threshold = e.disable_search_threshold || 0, t.disable_search = e.disable_search || !1, t.enable_split_word_search = null == e.enable_split_word_search || e.enable_split_word_search, t.group_search = null == e.group_search || e.group_search, t.search_contains = e.search_contains || !1, t.single_backstroke_delete = null == e.single_backstroke_delete || e.single_backstroke_delete, t.max_selected_options = e.max_selected_options || 1 / 0, t.drop_direction = e.drop_direction || "auto", t.drop_item_height = void 0 !== e.drop_item_height ? e.drop_item_height : 25, t.max_drop_height = void 0 !== e.max_drop_height ? e.max_drop_height : 240, t.middle_highlight = e.middle_highlight, t.compact_search = e.compact_search || !1, t.inherit_select_classes = e.inherit_select_classes || !1, t.display_selected_options = null == e.display_selected_options || e.display_selected_options, t.sort_value_splitter = e.sort_value_spliter || e.sort_value_splitter || ",", t.sort_field = e.sort_field;
                var i = e.max_drop_width;
                return "string" == typeof i && i.indexOf("px") === i.length - 2 && (i = parseInt(i.substring(0, i.length - 2))), t.max_drop_width = i, t.display_disabled_options = null == e.display_disabled_options || e.display_disabled_options
            }, e.prototype.set_default_text = function () {
                return this.form_field.getAttribute("data-placeholder") ? this.default_text = this.form_field.getAttribute("data-placeholder") : this.is_multiple ? this.default_text = this.options.placeholder_text_multiple || this.options.placeholder_text || e.default_multiple_text : this.default_text = this.options.placeholder_text_single || this.options.placeholder_text || e.default_single_text, this.results_none_found = this.form_field.getAttribute("data-no_results_text") || this.options.no_results_text || this.lang.no_results_text || e.default_no_result_text
            }, e.prototype.mouse_enter = function () {
                return this.mouse_on_container = !0
            }, e.prototype.mouse_leave = function () {
                return this.mouse_on_container = !1
            }, e.prototype.input_focus = function (t) {
                var e = this;
                if (this.is_multiple) {
                    if (!this.active_field) return setTimeout(function () {
                        return e.container_mousedown()
                    }, 50)
                } else if (!this.active_field) return this.activate_field()
            }, e.prototype.input_blur = function (t) {
                var e = this;
                if (!this.mouse_on_container) return this.active_field = !1, setTimeout(function () {
                    return e.blur_test()
                }, 100)
            }, e.prototype.results_option_build = function (e) {
                var i, n, o, a, s;
                i = "", s = this.results_data;
                var r = e && e.first ? [] : null;
                for (o = 0, a = s.length; o < a; o++) n = s[o], i += n.group ? this.result_add_group(n) : this.result_add_option(n), r && n.selected && r.push(n);
                if (r) {
                    var l, h;
                    if (this.sort_field && this.is_multiple) {
                        l = t(this.sort_field);
                        var c = l.val();
                        if (h = "string" == typeof c && c.length ? c.split(this.sort_value_splitter) : [], h.length) {
                            var d = {};
                            for (o = 0; o < h.length; ++o) d[h[o]] = o;
                            r.sort(function (t, e) {
                                var i = d[t.value], n = d[e.value];
                                return void 0 === i && (i = 0), void 0 === n && (n = 0), i - n
                            })
                        }
                    }
                    for (h = [], o = 0; o < r.length; ++o) n = r[o], this.is_multiple ? (this.choice_build(n), h.push(n.value)) : this.single_set_selected_text(n.text);
                    l && l.length && l.val(h.join(this.sort_value_splitter))
                }
                return i
            }, e.prototype.result_add_option = function (t) {
                var e, i;
                return t.search_match && this.include_option_in_results(t) ? (e = [], t.disabled || t.selected && this.is_multiple || e.push("active-result"), !t.disabled || t.selected && this.is_multiple || e.push("disabled-result"), t.selected && e.push("result-selected"), null != t.group_array_index && e.push("group-option"), "" !== t.classes && e.push(t.classes), i = document.createElement("li"), i.className = e.join(" "), i.style.cssText = t.style, i.title = t.title, i.setAttribute("data-option-array-index", t.array_index), i.setAttribute("data-data", t.data), i.innerHTML = t.search_text, this.outerHTML(i)) : ""
            }, e.prototype.result_add_group = function (t) {
                var e;
                return (t.search_match || t.group_match) && t.active_options > 0 ? (e = document.createElement("li"), e.className = "group-result", e.title = t.title, e.innerHTML = t.search_text, this.outerHTML(e)) : ""
            }, e.prototype.results_update_field = function () {
                this.set_default_text(), this.is_multiple || this.results_reset_cleanup(), this.result_clear_highlight(), this.results_build(), this.results_showing && (this.winnow_results(), this.autoResizeDrop())
            }, e.prototype.reset_single_select_options = function () {
                var t, e, i, n, o;
                for (n = this.results_data, o = [], e = 0, i = n.length; e < i; e++) t = n[e], t.selected ? o.push(t.selected = !1) : o.push(void 0);
                return o
            }, e.prototype.results_toggle = function () {
                return this.results_showing ? this.results_hide() : this.results_show()
            }, e.prototype.results_search = function (t) {
                return this.results_showing ? this.winnow_results(1) : this.results_show()
            }, e.prototype.winnow_results = function (t) {
                var e, i, n, o, a, s, r, l, h, c, d, u, f;
                for (this.no_results_clear(), a = 0, r = this.get_search_text(), e = r.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&"), o = this.search_contains ? "" : "^", n = new RegExp(o + e, "i"), c = new RegExp(e, "i"), f = this.results_data, d = 0, u = f.length; d < u; d++) i = f[d], i.search_match = !1, s = null, this.include_option_in_results(i) && (i.group && (i.group_match = !1, i.active_options = 0), null != i.group_array_index && this.results_data[i.group_array_index] && (s = this.results_data[i.group_array_index], 0 === s.active_options && s.search_match && (a += 1), s.active_options += 1), i.group && !this.group_search || (i.search_text = i.group ? i.label : i.html, i.search_keys_match = this.search_string_match(i.search_keys, n), i.search_text_match = this.search_string_match(i.search_text, n), i.search_match = i.search_text_match || i.search_keys_match, i.search_match && !i.group && (a += 1), i.search_match ? (i.search_text_match && i.search_text.length ? (l = i.search_text.search(c), h = i.search_text.substr(0, l + r.length) + "</em>" + i.search_text.substr(l + r.length), i.search_text = h.substr(0, l) + "<em>" + h.substr(l)) : i.search_keys_match && i.search_keys.length && (l = i.search_keys.search(c), h = i.search_keys.substr(0, l + r.length) + "</em>" + i.search_keys.substr(l + r.length), i.search_text += '&nbsp; <small style="opacity: 0.7">' + h.substr(0, l) + "<em>" + h.substr(l) + "</small>"), null != s && (s.group_match = !0)) : null != i.group_array_index && this.results_data[i.group_array_index].search_match && (i.search_match = !0)));
                return this.result_clear_highlight(), a < 1 && r.length ? (this.update_results_content(""), this.no_results(r)) : (this.update_results_content(this.results_option_build()), this.winnow_results_set_highlight(t))
            }, e.prototype.search_string_match = function (t, e) {
                var i, n, o, a;
                if (e.test(t)) return !0;
                if (this.enable_split_word_search && (t.indexOf(" ") >= 0 || 0 === t.indexOf("[")) && (n = t.replace(/\[|\]/g, "").split(" "), n.length)) for (o = 0, a = n.length; o < a; o++) if (i = n[o], e.test(i)) return !0
            }, e.prototype.choices_count = function () {
                var t, e, i, n;
                if (null != this.selected_option_count) return this.selected_option_count;
                for (this.selected_option_count = 0, n = this.form_field.options, e = 0, i = n.length; e < i; e++) t = n[e], t.selected && "" != t.value && (this.selected_option_count += 1);
                return this.selected_option_count
            }, e.prototype.choices_click = function (t) {
                if (t.preventDefault(), !this.results_showing && !this.is_disabled) return this.results_show()
            }, e.prototype.keyup_checker = function (t) {
                var e, i;
                switch (e = null != (i = t.which) ? i : t.keyCode, this.search_field_scale(), e) {
                    case 8:
                        if (this.is_multiple && this.backstroke_length < 1 && this.choices_count() > 0) return this.keydown_backstroke();
                        if (!this.pending_backstroke) return this.result_clear_highlight(), this.results_search();
                        break;
                    case 13:
                        if (t.preventDefault(), this.results_showing) return this.result_select(t);
                        break;
                    case 27:
                        return this.results_showing && this.results_hide(), !0;
                    case 9:
                    case 38:
                    case 40:
                    case 16:
                    case 91:
                    case 17:
                        break;
                    default:
                        return this.results_search()
                }
            }, e.prototype.clipboard_event_checker = function (t) {
                var e = this;
                return setTimeout(function () {
                    return e.results_search()
                }, 50)
            }, e.prototype.container_width = function () {
                return null != this.options.width ? this.options.width : this.form_field && this.form_field.classList && this.form_field.classList.contains("form-control") ? "100%" : "" + this.form_field.offsetWidth + "px"
            }, e.prototype.include_option_in_results = function (t) {
                return !(this.is_multiple && !this.display_selected_options && t.selected) && (!(!this.display_disabled_options && t.disabled) && !t.empty)
            }, e.prototype.search_results_touchstart = function (t) {
                return this.touch_started = !0, this.search_results_mouseover(t)
            }, e.prototype.search_results_touchmove = function (t) {
                return this.touch_started = !1, this.search_results_mouseout(t)
            }, e.prototype.search_results_touchend = function (t) {
                if (this.touch_started) return this.search_results_mouseup(t)
            }, e.prototype.outerHTML = function (t) {
                var e;
                return t.outerHTML ? t.outerHTML : (e = document.createElement("div"), e.appendChild(t), e.innerHTML)
            }, e.browser_is_supported = function () {
                return "Microsoft Internet Explorer" === window.navigator.appName ? document.documentMode >= 8 : !/iP(od|hone)/i.test(window.navigator.userAgent) && (!/Android/i.test(window.navigator.userAgent) || !/Mobile/i.test(window.navigator.userAgent))
            }, e.default_multiple_text = "", e.default_single_text = "", e.default_no_result_text = "No results match", e
        }(), t = jQuery, t.fn.extend({
            chosen: function (n) {
                return e.browser_is_supported() ? this.each(function (e) {
                    var o = t(this), a = o.data("chosen");
                    "destroy" === n && a ? a.destroy() : a || o.data("chosen", new i(this, t.extend({}, o.data(), n)))
                }) : this
            }
        }), i = function (e) {
            function i() {
                return o = i.__super__.constructor.apply(this, arguments)
            }

            return s(i, e), i.prototype.setup = function () {
                return this.form_field_jq = t(this.form_field), this.current_selectedIndex = this.form_field.selectedIndex, this.is_rtl = this.form_field_jq.hasClass("chosen-rtl")
            }, i.prototype.set_up_html = function () {
                var e, i;
                e = ["chosen-container"], e.push("chosen-container-" + (this.is_multiple ? "multi" : "single")), this.inherit_select_classes && this.form_field.className && e.push(this.form_field.className), this.is_rtl && e.push("chosen-rtl");
                var n = this.form_field.getAttribute("data-css-class");
                return n && e.push(n), i = {
                    "class": e.join(" "),
                    style: "width: " + this.container_width() + ";",
                    title: this.form_field.title
                }, this.form_field.id.length && (i.id = this.form_field.id.replace(/[^\w]/g, "_") + "_chosen"), this.container = t("<div />", i), this.is_multiple ? this.container.html('<ul class="chosen-choices"><li class="search-field"><input type="text" value="' + this.default_text + '" class="default" autocomplete="off" style="width:25px;" /></li></ul><div class="chosen-drop"><ul class="chosen-results"></ul></div>') : (this.container.html('<a class="chosen-single chosen-default" tabindex="-1"><span>' + this.default_text + '</span><div><b></b></div><div class="chosen-search"><input type="text" autocomplete="off" /></div></a><div class="chosen-drop"><ul class="chosen-results"></ul></div>'), this.compact_search ? this.container.addClass("chosen-compact").find(".chosen-search").appendTo(this.container.find(".chosen-single")) : this.container.find(".chosen-search").prependTo(this.container.find(".chosen-drop")), this.options.highlight_selected !== !1 && this.container.addClass("chosen-highlight-selected")), this.form_field_jq.hide().after(this.container), this.dropdown = this.container.find("div.chosen-drop").first(), this.search_field = this.container.find("input").first(), this.search_results = this.container.find("ul.chosen-results").first(), this.search_field_scale(), this.search_no_results = this.container.find("li.no-results").first(), this.is_multiple ? (this.search_choices = this.container.find("ul.chosen-choices").first(), this.search_container = this.container.find("li.search-field").first()) : (this.search_container = this.container.find("div.chosen-search").first(), this.selected_item = this.container.find(".chosen-single").first()), this.options.drop_width && this.dropdown.css("width", this.options.drop_width).addClass("chosen-drop-size-limited"), this.max_drop_width && this.dropdown.addClass("chosen-auto-max-width"), this.options.no_wrap && this.dropdown.addClass("chosen-no-wrap"), this.results_build(), this.set_tab_index(), this.set_label_behavior(), this.form_field_jq.trigger("chosen:ready", {chosen: this})
            }, i.prototype.register_observers = function () {
                var t = this;
                return this.container.bind("mousedown.chosen", function (e) {
                    t.container_mousedown(e)
                }), this.container.bind("mouseup.chosen", function (e) {
                    t.container_mouseup(e)
                }), this.container.bind("mouseenter.chosen", function (e) {
                    t.mouse_enter(e)
                }), this.container.bind("mouseleave.chosen", function (e) {
                    t.mouse_leave(e)
                }), this.search_results.bind("mouseup.chosen", function (e) {
                    t.search_results_mouseup(e)
                }), this.search_results.bind("mouseover.chosen", function (e) {
                    t.search_results_mouseover(e)
                }), this.search_results.bind("mouseout.chosen", function (e) {
                    t.search_results_mouseout(e)
                }), this.search_results.bind("mousewheel.chosen DOMMouseScroll.chosen", function (e) {
                    t.search_results_mousewheel(e)
                }), this.search_results.bind("touchstart.chosen", function (e) {
                    t.search_results_touchstart(e)
                }), this.search_results.bind("touchmove.chosen", function (e) {
                    t.search_results_touchmove(e)
                }), this.search_results.bind("touchend.chosen", function (e) {
                    t.search_results_touchend(e)
                }), this.form_field_jq.bind("chosen:updated.chosen", function (e) {
                    t.results_update_field(e)
                }), this.form_field_jq.bind("chosen:activate.chosen", function (e) {
                    t.activate_field(e)
                }), this.form_field_jq.bind("chosen:open.chosen", function (e) {
                    t.container_mousedown(e)
                }), this.form_field_jq.bind("chosen:close.chosen", function (e) {
                    t.input_blur(e)
                }), this.search_field.bind("blur.chosen", function (e) {
                    t.input_blur(e)
                }), this.search_field.bind("keyup.chosen", function (e) {
                    t.keyup_checker(e)
                }), this.search_field.bind("keydown.chosen", function (e) {
                    t.keydown_checker(e)
                }), this.search_field.bind("focus.chosen", function (e) {
                    t.input_focus(e)
                }), this.search_field.bind("cut.chosen", function (e) {
                    t.clipboard_event_checker(e)
                }), this.search_field.bind("paste.chosen", function (e) {
                    t.clipboard_event_checker(e)
                }), this.is_multiple ? this.search_choices.bind("click.chosen", function (e) {
                    t.choices_click(e)
                }) : this.container.bind("click.chosen", function (t) {
                    t.preventDefault()
                })
            }, i.prototype.destroy = function () {
                return t(this.container[0].ownerDocument).unbind("click.chosen", this.click_test_action), this.search_field[0].tabIndex && (this.form_field_jq[0].tabIndex = this.search_field[0].tabIndex), this.container.remove(), this.form_field_jq.removeData("chosen"), this.form_field_jq.show()
            }, i.prototype.search_field_disabled = function () {
                return this.is_disabled = this.form_field_jq[0].disabled, this.is_disabled ? (this.container.addClass("chosen-disabled"), this.search_field[0].disabled = !0, this.is_multiple || this.selected_item.unbind("focus.chosen", this.activate_action), this.close_field()) : (this.container.removeClass("chosen-disabled"), this.search_field[0].disabled = !1, this.is_multiple ? void 0 : this.selected_item.bind("focus.chosen", this.activate_action))
            }, i.prototype.container_mousedown = function (e) {
                if (!this.is_disabled && (e && "mousedown" === e.type && !this.results_showing && e.preventDefault(), null == e || !t(e.target).hasClass("search-choice-close"))) return this.active_field ? this.is_multiple || !e || t(e.target)[0] !== this.selected_item[0] && !t(e.target).parents("a.chosen-single").length || (e.preventDefault(), this.results_toggle()) : (this.is_multiple && this.search_field.val(""), t(this.container[0].ownerDocument).bind("click.chosen", this.click_test_action), this.results_show()), this.activate_field()
            }, i.prototype.container_mouseup = function (t) {
                if ("ABBR" === t.target.nodeName && !this.is_disabled) return this.results_reset(t)
            }, i.prototype.search_results_mousewheel = function (t) {
                var e;
                if (t.originalEvent && (e = -t.originalEvent.wheelDelta || t.originalEvent.detail), null != e) return t.preventDefault(), "DOMMouseScroll" === t.type && (e = 40 * e), this.search_results.scrollTop(e + this.search_results.scrollTop())
            }, i.prototype.blur_test = function (t) {
                if (!this.active_field && this.container.hasClass("chosen-container-active")) return this.close_field()
            }, i.prototype.close_field = function () {
                return t(this.container[0].ownerDocument).unbind("click.chosen", this.click_test_action), this.active_field = !1, this.results_hide(), this.container.removeClass("chosen-container-active"), this.clear_backstroke(), this.show_search_field_default(), this.search_field_scale()
            }, i.prototype.activate_field = function () {
                return this.container.addClass("chosen-container-active"), this.active_field = !0, this.search_field.val(this.search_field.val()), this.search_field.focus()
            }, i.prototype.test_active_click = function (e) {
                var i;
                return i = t(e.target).closest(".chosen-container"), i.length && this.container[0] === i[0] ? this.active_field = !0 : this.close_field()
            }, i.prototype.results_build = function () {
                return this.parsing = !0, this.selected_option_count = null, this.results_data = n.select_to_array(this.form_field), this.is_multiple ? this.search_choices.find("li.search-choice").remove() : this.is_multiple || (this.single_set_selected_text(), this.disable_search || this.form_field.options.length <= this.disable_search_threshold ? (this.search_field[0].readOnly = !0, this.container.addClass("chosen-container-single-nosearch"), this.container.removeClass("chosen-with-search")) : (this.search_field[0].readOnly = !1, this.container.removeClass("chosen-container-single-nosearch"), this.container.addClass("chosen-with-search"))), this.update_results_content(this.results_option_build({first: !0})), this.search_field_disabled(), this.show_search_field_default(), this.search_field_scale(), this.parsing = !1
            }, i.prototype.result_do_highlight = function (t, e) {
                if (t.length) {
                    var i, n, o, a, s, r, l = -1;
                    this.result_clear_highlight(), this.result_highlight = t, this.result_highlight.addClass("highlighted"), o = parseInt(this.search_results.css("maxHeight"), 10), r = this.result_highlight.outerHeight(), s = this.search_results.scrollTop(), a = o + s, n = this.result_highlight.position().top + this.search_results.scrollTop(), i = n + r, this.middle_highlight && (e || "always" === this.middle_highlight) ? l = Math.min(n - r, Math.max(0, n - (o - r) / 2)) : i >= a ? l = i - o > 0 ? i - o : 0 : n < s && (l = n), l > -1 ? this.search_results.scrollTop(l) : this.result_highlight.scrollIntoView && this.result_highlight.scrollIntoView()
                }
            }, i.prototype.result_clear_highlight = function () {
                return this.result_highlight && this.result_highlight.removeClass("highlighted"), this.result_highlight = null
            }, i.prototype.results_show = function () {
                var e = this;
                if (e.is_multiple && e.max_selected_options <= e.choices_count()) return e.form_field_jq.trigger("chosen:maxselected", {chosen: this}), !1;
                e.results_showing = !0, e.search_field.focus(), e.search_field.val(e.search_field.val()), e.container.addClass("chosen-with-drop"), e.winnow_results(1);
                var i = e.drop_direction;
                if ("function" == typeof i && (i = i.call(this)), "auto" === i) if (e.drop_directionFixed) i = e.drop_directionFixed; else {
                    var n = e.container.find(".chosen-drop"), o = n.outerHeight();
                    e.drop_item_height && o < e.max_drop_height && (o = Math.min(e.max_drop_height, n.find(".chosen-results>.active-result").length * e.drop_item_height));
                    var a = e.container.offset();
                    a.top + o + 30 > t(window).height() + t(window).scrollTop() && (i = "up"), e.drop_directionFixed = i
                }
                return e.container.toggleClass("chosen-up", "up" === i), e.autoResizeDrop(), e.form_field_jq.trigger("chosen:showing_dropdown", {chosen: e})
            }, i.prototype.autoResizeDrop = function () {
                var e = this, i = e.max_drop_width;
                if (i) {
                    var n = e.container.find(".chosen-drop");
                    n.removeClass("in");
                    var o = 0, a = n.find(".chosen-results"), s = a.children("li"),
                        r = parseFloat(a.css("padding-left").replace("px", "")),
                        l = parseFloat(a.css("padding-right").replace("px", "")),
                        h = (isNaN(r) ? 0 : r) + (isNaN(l) ? 0 : l);
                    s.each(function () {
                        o = Math.max(o, t(this).outerWidth())
                    }), n.css("width", Math.min(o + h + 20, i)), e.fixDropWidthTimer = setTimeout(function () {
                        e.fixDropWidthTimer = null, n.addClass("in"), e.winnow_results_set_highlight(1)
                    }, 50)
                }
            }, i.prototype.update_results_content = function (t) {
                return this.search_results.html(t)
            }, i.prototype.results_hide = function () {
                var t = this;
                return t.fixDropWidthTimer && (clearTimeout(t.fixDropWidthTimer), t.fixDropWidthTimer = null), t.results_showing && (t.result_clear_highlight(), t.container.removeClass("chosen-with-drop"), t.form_field_jq.trigger("chosen:hiding_dropdown", {chosen: t}), t.drop_directionFixed = 0), t.results_showing = !1
            }, i.prototype.set_tab_index = function (t) {
                var e;
                if (this.form_field.tabIndex) return e = this.form_field.tabIndex, this.form_field.tabIndex = -1, this.search_field[0].tabIndex = e
            }, i.prototype.set_label_behavior = function () {
                var e = this;
                if (this.form_field_label = this.form_field_jq.parents("label"), !this.form_field_label.length && this.form_field.id.length && (this.form_field_label = t("label[for='" + this.form_field.id + "']")), this.form_field_label.length > 0) return this.form_field_label.bind("click.chosen", function (t) {
                    return e.is_multiple ? e.container_mousedown(t) : e.activate_field()
                })
            }, i.prototype.show_search_field_default = function () {
                return this.is_multiple && this.choices_count() < 1 && !this.active_field ? (this.search_field.val(this.default_text), this.search_field.addClass("default")) : (this.search_field.val(""), this.search_field.removeClass("default"))
            }, i.prototype.search_results_mouseup = function (e) {
                var i;
                if (i = t(e.target).hasClass("active-result") ? t(e.target) : t(e.target).parents(".active-result").first(), i.length) return this.result_highlight = i, this.result_select(e), this.search_field.focus()
            }, i.prototype.search_results_mouseover = function (e) {
                var i;
                if (i = t(e.target).hasClass("active-result") ? t(e.target) : t(e.target).parents(".active-result").first()) return this.result_do_highlight(i)
            }, i.prototype.search_results_mouseout = function (e) {
                if (t(e.target).hasClass("active-result")) return this.result_clear_highlight()
            }, i.prototype.choice_build = function (e) {
                var i, n, o = this;
                return i = t("<li />", {"class": "search-choice"}).html("<span title='" + e.html + "'>" + e.html + "</span>"), e.disabled ? i.addClass("search-choice-disabled") : (n = t("<a />", {
                    "class": "search-choice-close",
                    "data-option-array-index": e.array_index
                }), n.bind("click.chosen", function (t) {
                    return o.choice_destroy_link_click(t)
                }), i.append(n)), this.search_container.before(i)
            }, i.prototype.choice_destroy_link_click = function (e) {
                if (e.preventDefault(), e.stopPropagation(), !this.is_disabled) return this.choice_destroy(t(e.target))
            }, i.prototype.choice_destroy = function (t) {
                if (this.result_deselect(t[0].getAttribute("data-option-array-index"))) return this.show_search_field_default(), this.is_multiple && this.choices_count() > 0 && this.search_field.val().length < 1 && this.results_hide(), t.parents("li").first().remove(), this.search_field_scale()
            }, i.prototype.results_reset = function () {
                var t = this.form_field_jq.val();
                this.reset_single_select_options(), this.form_field.options[0].selected = !0, this.single_set_selected_text(), this.show_search_field_default(), this.results_reset_cleanup();
                var e = this.form_field_jq.val(), i = {selected: e};
                if (t === e || e.length || (i.deselected = t), this.form_field_jq.trigger("change", i), this.sync_sort_field(), this.active_field) return this.results_hide()
            }, i.prototype.results_reset_cleanup = function () {
                return this.current_selectedIndex = this.form_field.selectedIndex, this.selected_item.find("abbr").remove()
            }, i.prototype.result_select = function (t) {
                var e, i;
                if (this.result_highlight) return e = this.result_highlight, this.result_clear_highlight(), this.is_multiple && this.max_selected_options <= this.choices_count() ? (this.form_field_jq.trigger("chosen:maxselected", {chosen: this}), !1) : (this.is_multiple ? e.removeClass("active-result") : this.reset_single_select_options(), i = this.results_data[e[0].getAttribute("data-option-array-index")], i.selected = !0, this.form_field.options[i.options_index].selected = !0, this.selected_option_count = null, this.is_multiple ? this.choice_build(i) : this.single_set_selected_text(i.text), (t.metaKey || t.ctrlKey) && this.is_multiple || this.results_hide(), this.search_field.val(""), (this.is_multiple || this.form_field.selectedIndex !== this.current_selectedIndex) && (this.form_field_jq.trigger("change", {selected: this.form_field.options[i.options_index].value}), this.sync_sort_field()), this.current_selectedIndex = this.form_field.selectedIndex, this.search_field_scale())
            }, i.prototype.single_set_selected_text = function (t) {
                return null == t && (t = this.default_text), t === this.default_text ? this.selected_item.addClass("chosen-default") : (this.single_deselect_control_build(), this.selected_item.removeClass("chosen-default")), this.compact_search && this.search_field.attr("placeholder", t), this.selected_item.find("span").attr("title", t).text(t)
            }, i.prototype.sync_sort_field = function () {
                var e = this;
                if (e.is_multiple && e.sort_field) {
                    var i = t(e.sort_field);
                    if (!i.length) return;
                    var n = [];
                    e.search_choices.find("li.search-choice").each(function () {
                        var i = t(this), o = i.children(".search-choice-close").first().data("optionArrayIndex"),
                            a = e.results_data[o];
                        a && a.selected && n.push(a.value)
                    }), i.val(n.join(e.sort_value_splitter)).trigger("change")
                }
            }, i.prototype.result_deselect = function (t) {
                var e;
                return e = this.results_data[t], !this.form_field.options[e.options_index].disabled && (e.selected = !1, this.form_field.options[e.options_index].selected = !1, this.selected_option_count = null, this.result_clear_highlight(), this.results_showing && this.winnow_results(), this.form_field_jq.trigger("change", {deselected: this.form_field.options[e.options_index].value}), this.sync_sort_field(), this.search_field_scale(), !0)
            }, i.prototype.single_deselect_control_build = function () {
                if (this.allow_single_deselect) return this.selected_item.find("abbr").length || this.selected_item.find("span").first().after('<abbr class="search-choice-close"></abbr>'), this.selected_item.addClass("chosen-single-with-deselect")
            }, i.prototype.get_search_text = function () {
                return this.search_field.val() === this.default_text ? "" : t("<div/>").text(t.trim(this.search_field.val())).html()
            }, i.prototype.winnow_results_set_highlight = function (t) {
                var e, i;
                if (i = this.is_multiple ? [] : this.search_results.find(".result-selected.active-result"), e = i.length ? i.first() : this.search_results.find(".active-result").first(), null != e) return this.result_do_highlight(e, t)
            }, i.prototype.no_results = function (e) {
                var i;
                return i = t('<li class="no-results">' + this.results_none_found + ' "<span></span>"</li>'), i.find("span").first().html(e), this.search_results.append(i), this.form_field_jq.trigger("chosen:no_results", {chosen: this})
            }, i.prototype.no_results_clear = function () {
                return this.search_results.find(".no-results").remove()
            }, i.prototype.keydown_arrow = function () {
                var t;
                return this.results_showing && this.result_highlight ? (t = this.result_highlight.nextAll("li.active-result").first()) ? this.result_do_highlight(t) : void 0 : this.results_show()
            }, i.prototype.keyup_arrow = function () {
                var t;
                return this.results_showing || this.is_multiple ? this.result_highlight ? (t = this.result_highlight.prevAll("li.active-result"), t.length ? this.result_do_highlight(t.first()) : (this.choices_count() > 0 && this.results_hide(), this.result_clear_highlight())) : void 0 : this.results_show()
            }, i.prototype.keydown_backstroke = function () {
                var t;
                return this.pending_backstroke ? (this.choice_destroy(this.pending_backstroke.find("a").first()), this.clear_backstroke()) : (t = this.search_container.siblings("li.search-choice").last(), t.length && !t.hasClass("search-choice-disabled") ? (this.pending_backstroke = t, this.single_backstroke_delete ? this.keydown_backstroke() : this.pending_backstroke.addClass("search-choice-focus")) : void 0)
            }, i.prototype.clear_backstroke = function () {
                return this.pending_backstroke && this.pending_backstroke.removeClass("search-choice-focus"), this.pending_backstroke = null
            }, i.prototype.keydown_checker = function (t) {
                var e, i;
                switch (e = null != (i = t.which) ? i : t.keyCode, this.search_field_scale(), 8 !== e && this.pending_backstroke && this.clear_backstroke(), e) {
                    case 8:
                        this.backstroke_length = this.search_field.val().length;
                        break;
                    case 9:
                        this.results_showing && !this.is_multiple && this.result_select(t), this.mouse_on_container = !1;
                        break;
                    case 13:
                        t.preventDefault();
                        break;
                    case 38:
                        t.preventDefault(), this.keyup_arrow();
                        break;
                    case 40:
                        t.preventDefault(), this.keydown_arrow()
                }
            }, i.prototype.search_field_scale = function () {
                var e, i, n, o, a, s, r, l, h;
                if (this.is_multiple) {
                    for (n = 0, r = 0, a = "position:absolute; left: -1000px; top: -1000px; display:none;", s = ["font-size", "font-style", "font-weight", "font-family", "line-height", "text-transform", "letter-spacing"], l = 0, h = s.length; l < h; l++) o = s[l], a += o + ":" + this.search_field.css(o) + ";";
                    return e = t("<div />", {style: a}), e.text(this.search_field.val()), t("body").append(e), r = e.width() + 25, e.remove(), i = this.container.outerWidth(), r > i - 10 && (r = i - 10), this.search_field.css({width: r + "px"})
                }
            }, i
        }(e), i.DEFAULTS = l, i.LANGUAGES = r, t.fn.chosen.Constructor = i
    }.call(this), function (t) {
    "use strict";
    var e = "zui.selectable", i = function (i, n) {
        this.name = e, this.$ = t(i), this.id = t.zui.uuid(), this.selectOrder = 1, this.selections = {}, this.getOptions(n), this._init()
    }, n = function (t, e, i) {
        return t >= i.left && t <= i.left + i.width && e >= i.top && e <= i.top + i.height
    }, o = function (t, e) {
        var i = Math.max(t.left, e.left), o = Math.max(t.top, e.top), a = Math.min(t.left + t.width, e.left + e.width),
            s = Math.min(t.top + t.height, e.top + e.height);
        return n(i, o, t) && n(a, s, t) && n(i, o, e) && n(a, s, e)
    };
    i.DEFAULTS = {
        selector: "li,tr,div",
        trigger: "",
        selectClass: "active",
        rangeStyle: {
            border: "1px solid " + (t.zui.colorset ? t.zui.colorset.primary : "#3280fc"),
            backgroundColor: t.zui.colorset ? new t.zui.Color(t.zui.colorset.primary).fade(20).toCssStr() : "rgba(50, 128, 252, 0.2)"
        },
        clickBehavior: "toggle",
        ignoreVal: 3,
        listenClick: !0
    }, i.prototype.getOptions = function (e) {
        this.options = t.extend({}, i.DEFAULTS, this.$.data(), e)
    }, i.prototype.select = function (t) {
        this.toggle(t, !0)
    }, i.prototype.unselect = function (t) {
        this.toggle(t, !1)
    }, i.prototype.toggle = function (e, i, n) {
        var o, a, s = this.options.selector, r = this;
        if (void 0 === e) return void this.$.find(s).each(function () {
            r.toggle(this, i)
        });
        if ("object" == typeof e ? (o = t(e).closest(s), a = o.data("id")) : (a = e, o = r.$.find('.slectable-item[data-id="' + a + '"]')), o && o.length) {
            if (a || (a = t.zui.uuid(), o.attr("data-id", a)), void 0 !== i && null !== i || (i = !r.selections[a]), !!i != !!r.selections[a]) {
                var l;
                "function" == typeof n && (l = n(i)), l !== !0 && (r.selections[a] = !!i && r.selectOrder++, r.callEvent(i ? "select" : "unselect", {
                    id: a, selections: r.selections, target: o,
                    selected: r.getSelectedArray()
                }, r))
            }
            r.options.selectClass && o.toggleClass(r.options.selectClass, i)
        }
    }, i.prototype.getSelectedArray = function () {
        var e = [];
        return t.each(this.selections, function (t, i) {
            i && e.push(t)
        }), e
    }, i.prototype.syncSelectionsFromClass = function () {
        var e = this, i = e.$children = e.$.find(e.options.selector);
        e.selections = {}, i.each(function () {
            var i = t(this);
            e.selections[i.data("id")] = i.hasClass(e.options.selectClass)
        })
    }, i.prototype._init = function () {
        var e, i, n, a, s, r, l, h = this.options, c = this, d = h.ignoreVal, u = !0,
            f = "." + this.name + "." + this.id, p = "function" == typeof h.checkFunc ? h.checkFunc : null,
            g = "function" == typeof h.rangeFunc ? h.rangeFunc : null, m = !1, v = null, y = "mousedown" + f,
            b = function () {
                a && c.$children.each(function () {
                    var e = t(this), i = e.offset();
                    i.width = e.outerWidth(), i.height = e.outerHeight();
                    var n = g ? g.call(this, a, i) : o(a, i);
                    if (p) {
                        var s = p.call(c, {intersect: n, target: e, range: a, targetRange: i});
                        s === !0 ? c.select(e) : s === !1 && c.unselect(e)
                    } else n ? c.select(e) : c.multiKey || c.unselect(e)
                })
            }, w = function (o) {
                m && (s = o.pageX, r = o.pageY, a = {
                    width: Math.abs(s - e),
                    height: Math.abs(r - i),
                    left: s > e ? e : s,
                    top: r > i ? i : r
                }, u && a.width < d && a.height < d || (n || (n = t('.selectable-range[data-id="' + c.id + '"]'), n.length || (n = t('<div class="selectable-range" data-id="' + c.id + '"></div>').css(t.extend({
                    zIndex: 1060,
                    position: "absolute",
                    top: e,
                    left: i,
                    pointerEvents: "none"
                }, c.options.rangeStyle)).appendTo(t("body")))), n.css(a), clearTimeout(l), l = setTimeout(b, 10), u = !1))
            }, x = function (e) {
                t(document).off(f), clearTimeout(v), m && (m = !1, n && n.remove(), u || a && (clearTimeout(l), b(), a = null), c.callEvent("finish", {
                    selections: c.selections,
                    selected: c.getSelectedArray()
                }), e.preventDefault())
            }, C = function (o) {
                if (m) return x(o);
                var a = t.zui.getMouseButtonCode(h.mouseButton);
                if (!(a > -1 && o.button !== a || c.altKey || 3 === o.which || c.callEvent("start", o) === !1)) {
                    var s = c.$children = c.$.find(h.selector);
                    s.addClass("slectable-item");
                    var r = c.multiKey ? "multi" : h.clickBehavior;
                    if ("single" === r && c.unselect(), h.listenClick && ("multi" === r ? c.toggle(o.target) : "single" === r ? c.select(o.target) : "toggle" === r && c.toggle(o.target, null, function (t) {
                        c.unselect()
                    })), c.callEvent("startDrag", o) === !1) return void c.callEvent("finish", {
                        selections: c.selections,
                        selected: c.getSelectedArray()
                    });
                    e = o.pageX, i = o.pageY, n = null, u = !0, m = !0, t(document).on("mousemove" + f, w).on("mouseup" + f, x), v = setTimeout(function () {
                        t(document).on(y, x)
                    }, 10), o.preventDefault()
                }
            }, _ = h.container && "default" !== h.container ? t(h.container) : this.$;
        h.trigger ? _.on(y, h.trigger, C) : _.on(y, C), t(document).on("keydown", function (t) {
            var e = t.keyCode;
            17 === e || 91 == e ? c.multiKey = e : 18 === e && (c.altKey = !0)
        }).on("keyup", function (t) {
            c.multiKey = !1, c.altKey = !1
        })
    }, i.prototype.callEvent = function (e, i) {
        var n = t.Event(e + "." + this.name);
        this.$.trigger(n, i);
        var o = n.result, a = this.options[e];
        return "function" == typeof a && (o = a.apply(this, Array.isArray(i) ? i : [i])), o
    }, t.fn.selectable = function (n) {
        return this.each(function () {
            var o = t(this), a = o.data(e), s = "object" == typeof n && n;
            a || o.data(e, a = new i(this, s)), "string" == typeof n && a[n]()
        })
    }, t.fn.selectable.Constructor = i, t(function () {
        t('[data-ride="selectable"]').selectable()
    })
}(jQuery), +function (t, e, i) {
    "use strict";
    if (!t.fn.droppable) return void console.error("Sortable requires droppable.js");
    var n = "zui.sortable", o = {selector: "li,div", dragCssClass: "invisible", sortingClass: "sortable-sorting"},
        a = "order", s = function (e, i) {
            var n = this;
            n.$ = t(e), n.options = t.extend({}, o, n.$.data(), i), n.init()
        };
    s.DEFAULTS = o, s.NAME = n, s.prototype.init = function () {
        var e, i = this, n = i.$, o = i.options, s = o.selector, r = o.containerSelector, l = o.sortingClass,
            h = o.dragCssClass, c = o.targetSelector, d = o.reverse, u = function (e) {
                e = e || i.getItems(1);
                var n = e.length;
                n && e.each(function (e) {
                    var i = d ? n - e : e;
                    t(this).attr("data-" + a, i).data(a, i)
                })
            };
        u(), n.droppable({
            handle: o.trigger,
            target: c ? c : r ? s + "," + r : s,
            selector: s,
            container: n,
            always: o.always,
            flex: !0,
            lazy: o.lazy,
            canMoveHere: o.canMoveHere,
            dropToClass: o.dropToClass,
            before: o.before,
            nested: !!r,
            mouseButton: o.mouseButton,
            stopPropagation: o.stopPropagation,
            start: function (t) {
                h && t.element.addClass(h), e = !1, i.trigger("start", t)
            },
            drag: function (t) {
                if (n.addClass(l), t.isIn) {
                    var o = t.element, h = t.target, c = r && h.is(r);
                    if (c) {
                        if (!h.children(s).filter(".dragging").length) {
                            h.append(o);
                            var f = i.getItems(1);
                            u(f), i.trigger(a, {list: f, element: o})
                        }
                        return
                    }
                    var p = o.data(a), g = h.data(a);
                    if (p === g) return u(f);
                    p > g ? h[d ? "after" : "before"](o) : h[d ? "before" : "after"](o), e = !0;
                    var f = i.getItems(1);
                    u(f), i.trigger(a, {list: f, element: o})
                }
            },
            finish: function (t) {
                h && t.element && t.element.removeClass(h), n.removeClass(l), i.trigger("finish", {
                    list: i.getItems(),
                    element: t.element,
                    changed: e
                })
            }
        })
    }, s.prototype.destroy = function () {
        this.$.droppable("destroy"), this.$.data(n, null)
    }, s.prototype.reset = function () {
        this.destroy(), this.init()
    }, s.prototype.getItems = function (e) {
        var i = this.$.find(this.options.selector).not(".drag-shadow");
        return e ? i : i.map(function () {
            var e = t(this);
            return {item: e, order: e.data("order")}
        })
    }, s.prototype.trigger = function (e, i) {
        return t.zui.callEvent(this.options[e], i, this)
    }, t.fn.sortable = function (e) {
        return this.each(function () {
            var i = t(this), o = i.data(n), a = "object" == typeof e && e;
            o ? "object" == typeof e && o.reset() : i.data(n, o = new s(this, a)), "string" == typeof e && o[e]()
        })
    }, t.fn.sortable.Constructor = s
}(jQuery, window, document), function (t, e) {
    "use strict";
    var i = "zui.contextmenu", n = {
        animation: "fade",
        menuTemplate: '<ul class="dropdown-menu"></ul>',
        toggleTrigger: !1,
        duration: 200,
        limitInsideWindow: !0
    }, o = !1, a = {}, s = "zui-contextmenu-" + t.zui.uuid(), r = 0, l = 0, h = function () {
        return t(document).off("mousemove." + i).on("mousemove." + i, function (t) {
            r = t.clientX, l = t.clientY
        }), a
    }, c = function (e, i) {
        if ("string" == typeof e && (e = "seperator" === e || "divider" === e || "-" === e || "|" === e ? {type: "seperator"} : {
            label: e,
            id: i
        }), "seperator" === e.type || "divider" === e.type) return t('<li class="divider"></li>');
        var n = t("<a/>").attr({href: e.url || "###", "class": e.className, style: e.style}).data("item", e);
        return e.html ? e.html === !0 ? n.html(e.label || e.text) : n = t(e.html) : n.text(e.label || e.text), e.onClick && n.on("click", e.onClick), t("<li />").toggleClass("disabled", e.disabled === !0).append(n)
    }, d = function (e) {
        var i = t("#" + s);
        return i.length && i.hasClass("contextmenu-show") && (!e || (i.data("options") || {}).id === e)
    }, u = null, f = function (e, i) {
        "function" == typeof e && (i = e, e = null), u && (clearTimeout(u), u = null);
        var n = t("#" + s);
        if (n.length) {
            var o = n.removeClass("contextmenu-show").data("options");
            if (!e || o.id === e) {
                var r = function () {
                    n.find(".contextmenu-menu").removeClass("open"), o.onHidden && o.onHidden(), i && i()
                };
                o.onHide && o.onHide();
                var l = o.animation;
                n.find(".contextmenu-menu").removeClass("in"), l ? u = setTimeout(r, o.duration) : r()
            }
        }
        return a
    }, p = function (h, d, p) {
        t.isPlainObject(h) && (p = d, d = h, h = d.items), o = !0, d = t.extend({}, n, d);
        var g = t("#" + s);
        g.length || (g = t('<div style="position: fixed; z-index: 2000;" class="contextmenu" id="' + s + '"><div class="contextmenu-menu"></div></div>').appendTo("body"));
        var m = g.find(".contextmenu-menu").off("click." + i).on("click." + i, "a,.contextmenu-item", function (e) {
            var i = t(this), n = d.onClickItem && d.onClickItem(i.data("item"), i, e, d);
            n !== !1 && f()
        }).empty();
        m.attr("class", "contextmenu-menu" + (d.className ? " " + d.className : "")), g.attr("class", "contextmenu contextmenu-show");
        var v = d.menuCreator;
        if (v) m.append(v(h, d)); else {
            m.append(d.menuTemplate);
            var y = m.children().first(), b = d.itemCreator || c, w = typeof h;
            if ("string" === w ? h = h.split(",") : "function" === w && (h = h(d)), !h) return !1;
            t.each(h, function (t, e) {
                y.append(b(e, t, d))
            })
        }
        var x = d.animation, C = d.duration;
        x === !0 && (d.animation = x = "fade"), u && (clearTimeout(u), u = null);
        var _ = function () {
            m.addClass("in"), d.onShown && d.onShown(), p && p()
        };
        d.onShow && d.onShow(), g.data("options", {
            animation: x,
            onHide: d.onHide,
            onHidden: d.onHidden,
            id: d.id,
            duration: C
        });
        var k = d.x, T = d.y;
        k === e && (k = (d.event || d).clientX), k === e && (k = r), T === e && (T = (d.event || d).clientY), T === e && (T = l);
        var y = m.children().first(), S = y.outerWidth(), D = y.outerHeight();
        if (d.position) {
            var M = d.position({x: k, y: T, width: S, height: D}, d, m);
            M && (k = M.x, T = M.y)
        }
        if (d.limitInsideWindow) {
            var P = t(window);
            k = Math.max(0, Math.min(k, P.width() - S)), T = Math.max(0, Math.min(T, P.height() - D))
        }
        return g.css({left: k, top: T}).show(), m.addClass("open"), x ? (m.addClass(x), u = setTimeout(function () {
            _(), o = !1
        }, 10)) : (_(), o = !1), a
    };
    t.extend(a, {NAME: i, DEFAULTS: n, show: p, hide: f, listenMouse: h, isShow: d}), t.zui({ContextMenu: a});
    var g = function (e, n) {
        var o = this;
        o.name = i, o.$ = t(e), o.id = t.zui.uuid(), n = o.options = t.extend({trigger: "contextmenu"}, a.DEFAULTS, this.$.data(), n);
        var s = function (t) {
            if ("mousedown" !== t.type || 2 === t.button) {
                if (n.toggleTrigger && o.isShow()) o.hide(); else {
                    var e = {x: t.clientX, y: t.clientY, event: t};
                    if (o.show(e) === !1) return
                }
                return t.preventDefault(), t.returnValue = !1, !1
            }
        }, r = n.trigger, l = r + "." + i;
        n.selector ? o.$.on(l, n.selector, s) : o.$.on(l, s), n.show && o.show("object" == typeof n.show ? n.show : null)
    };
    g.prototype.destory = function () {
        that.$.off("." + i)
    }, g.prototype.hide = function (t) {
        return a.hide(this.id, t)
    }, g.prototype.show = function (e, i) {
        return e = t.extend({id: this.id, $toggle: this.$}, this.options, e), a.show(e, i)
    }, g.prototype.isShow = function () {
        return d(this.id)
    }, t.fn.contextmenu = function (e) {
        return this.each(function () {
            var n = t(this), o = n.data(i), a = "object" == typeof e && e;
            o || n.data(i, o = new g(this, a)), "string" == typeof e && o[e]()
        })
    }, t.fn.contextmenu.Constructor = g, t.fn.contextDropdown = function (e) {
        t(this).contextmenu(t.extend({
            trigger: "click", animation: "fade", toggleTrigger: !0, menuCreator: function (e, i) {
                var n = i.$toggle, o = n.attr("data-target");
                o || (o = n.attr("href"), o = o && /#/.test(o) && o.replace(/.*(?=#[^\s]*$)/, ""));
                var a = o ? t(o) : n.next(".dropdown-menu"), s = i.transferEvent;
                if (s !== !1) {
                    var r = "data-contextmenu-index";
                    a.find("a,.contextmenu-item").each(function (e) {
                        t(this).attr(r, e)
                    });
                    var l = a.clone();
                    return l.on("string" == typeof s ? s : "click", "a,.contextmenu-item", function (e) {
                        var i = a.find("[" + r + '="' + t(this).attr(r) + '"]'), n = i[0];
                        if (n) return n[e.type] ? n[e.type]() : i.trigger(e.type), e.preventDefault(), e.stopPropagation(), !1
                    }), l
                }
                return a.clone()
            }, position: function (t, e, i) {
                var n = e.placement, o = e.$toggle;
                if (!n) {
                    var a = i.find(".dropdown-menu"), s = a.hasClass("pull-right"), r = o.parent().hasClass("dropup");
                    n = s ? r ? "top-right" : "bottom-right" : r ? "top-left" : "bottom-left", s && a.removeClass("pull-right")
                }
                var l = o[0].getBoundingClientRect();
                switch (n) {
                    case"top-left":
                        return {x: l.left, y: Math.floor(l.top - t.height)};
                    case"top-right":
                        return {x: Math.floor(l.right - t.width), y: Math.floor(l.top - t.height)};
                    case"bottom-left":
                        return {x: l.left, y: l.bottom};
                    case"bottom-right":
                        return {x: Math.floor(l.right - t.width), y: l.bottom}
                }
                return t
            }
        }, e))
    }, t(document).on("click", function (e) {
        var n = t(e.target), a = n.closest('[data-toggle="context-dropdown"]');
        if (a.length) {
            var s = a.data(i);
            s || a.contextDropdown({show: !0})
        } else o || n.closest(".contextmenu").length || f()
    })
}(jQuery, void 0),/*!
 * jQuery Form Plugin
 * version: 4.2.2
 * Requires jQuery v1.7.2 or later
 * Project repository: https://github.com/jquery-form/form

 * Copyright 2017 Kevin Morris
 * Copyright 2006 M. Alsup

 * Dual licensed under the LGPL-2.1+ or MIT licenses
 * https://github.com/jquery-form/form#license

 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 */
    function (t) {
        "function" == typeof define && define.amd ? define(["jquery"], t) : "object" == typeof module && module.exports ? module.exports = function (e, i) {
            return "undefined" == typeof i && (i = "undefined" != typeof window ? require("jquery") : require("jquery")(e)), t(i), i
        } : t(jQuery)
    }(function (t) {
        "use strict";

        function e(e) {
            var i = e.data;
            e.isDefaultPrevented() || (e.preventDefault(), t(e.target).closest("form").ajaxSubmit(i))
        }

        function i(e) {
            var i = e.target, n = t(i);
            if (!n.is("[type=submit],[type=image]")) {
                var o = n.closest("[type=submit]");
                if (0 === o.length) return;
                i = o[0]
            }
            var a = i.form;
            if (a.clk = i, "image" === i.type) if ("undefined" != typeof e.offsetX) a.clk_x = e.offsetX, a.clk_y = e.offsetY; else if ("function" == typeof t.fn.offset) {
                var s = n.offset();
                a.clk_x = e.pageX - s.left, a.clk_y = e.pageY - s.top
            } else a.clk_x = e.pageX - i.offsetLeft, a.clk_y = e.pageY - i.offsetTop;
            setTimeout(function () {
                a.clk = a.clk_x = a.clk_y = null
            }, 100)
        }

        function n() {
            if (t.fn.ajaxSubmit.debug) {
                var e = "[jquery.form] " + Array.prototype.join.call(arguments, "");
                window.console && window.console.log ? window.console.log(e) : window.opera && window.opera.postError && window.opera.postError(e)
            }
        }

        var o = /\r?\n/g, a = {};
        a.fileapi = void 0 !== t('<input type="file">').get(0).files, a.formdata = "undefined" != typeof window.FormData;
        var s = !!t.fn.prop;
        t.fn.attr2 = function () {
            if (!s) return this.attr.apply(this, arguments);
            var t = this.prop.apply(this, arguments);
            return t && t.jquery || "string" == typeof t ? t : this.attr.apply(this, arguments)
        }, t.fn.ajaxSubmit = function (e, i, o, r) {
            function l(i) {
                var n, o, a = t.param(i, e.traditional).split("&"), s = a.length, r = [];
                for (n = 0; n < s; n++) a[n] = a[n].replace(/\+/g, " "), o = a[n].split("="), r.push([decodeURIComponent(o[0]), decodeURIComponent(o[1])]);
                return r
            }

            function h(i) {
                for (var n = new FormData, o = 0; o < i.length; o++) n.append(i[o].name, i[o].value);
                if (e.extraData) {
                    var a = l(e.extraData);
                    for (o = 0; o < a.length; o++) a[o] && n.append(a[o][0], a[o][1])
                }
                e.data = null;
                var s = t.extend(!0, {}, t.ajaxSettings, e, {
                    contentType: !1,
                    processData: !1,
                    cache: !1,
                    type: d || "POST"
                });
                e.uploadProgress && (s.xhr = function () {
                    var i = t.ajaxSettings.xhr();
                    return i.upload && i.upload.addEventListener("progress", function (t) {
                        var i = 0, n = t.loaded || t.position, o = t.total;
                        t.lengthComputable && (i = Math.ceil(n / o * 100)), e.uploadProgress(t, n, o, i)
                    }, !1), i
                }), s.data = null;
                var r = s.beforeSend;
                return s.beforeSend = function (t, i) {
                    e.formData ? i.data = e.formData : i.data = n, r && r.call(this, t, i)
                }, t.ajax(s)
            }

            function c(i) {
                function o(t) {
                    var e = null;
                    try {
                        t.contentWindow && (e = t.contentWindow.document)
                    } catch (i) {
                        n("cannot get iframe.contentWindow document: " + i)
                    }
                    if (e) return e;
                    try {
                        e = t.contentDocument ? t.contentDocument : t.document
                    } catch (i) {
                        n("cannot get iframe.contentDocument: " + i), e = t.document
                    }
                    return e
                }

                function a() {
                    function e() {
                        try {
                            var t = o(m).readyState;
                            n("state = " + t), t && "uninitialized" === t.toLowerCase() && setTimeout(e, 50)
                        } catch (i) {
                            n("Server abort: ", i, " (", i.name, ")"), r(M), C && clearTimeout(C), C = void 0
                        }
                    }

                    var i = p.attr2("target"), a = p.attr2("action"), s = "multipart/form-data",
                        l = p.attr("enctype") || p.attr("encoding") || s;
                    _.setAttribute("target", f), d && !/post/i.test(d) || _.setAttribute("method", "POST"), a !== c.url && _.setAttribute("action", c.url), c.skipEncodingOverride || d && !/post/i.test(d) || p.attr({
                        encoding: "multipart/form-data",
                        enctype: "multipart/form-data"
                    }), c.timeout && (C = setTimeout(function () {
                        x = !0, r(D)
                    }, c.timeout));
                    var h = [];
                    try {
                        if (c.extraData) for (var u in c.extraData) c.extraData.hasOwnProperty(u) && (t.isPlainObject(c.extraData[u]) && c.extraData[u].hasOwnProperty("name") && c.extraData[u].hasOwnProperty("value") ? h.push(t('<input type="hidden" name="' + c.extraData[u].name + '">', T).val(c.extraData[u].value).appendTo(_)[0]) : h.push(t('<input type="hidden" name="' + u + '">', T).val(c.extraData[u]).appendTo(_)[0]));
                        c.iframeTarget || g.appendTo(S), m.attachEvent ? m.attachEvent("onload", r) : m.addEventListener("load", r, !1), setTimeout(e, 15);
                        try {
                            _.submit()
                        } catch (v) {
                            var y = document.createElement("form").submit;
                            y.apply(_)
                        }
                    } finally {
                        _.setAttribute("action", a), _.setAttribute("enctype", l), i ? _.setAttribute("target", i) : p.removeAttr("target"), t.each(h, function () {
                            this.remove()
                        })
                    }
                }

                function r(e) {
                    if (!v.aborted && !I) {
                        if (F = o(m), F || (n("cannot access response document"), e = M), e === D && v) return v.abort("timeout"), void k.reject(v, "timeout");
                        if (e === M && v) return v.abort("server abort"), void k.reject(v, "error", "server abort");
                        if (F && F.location.href !== c.iframeSrc || x) {
                            m.detachEvent ? m.detachEvent("onload", r) : m.removeEventListener("load", r, !1);
                            var i, a = "success";
                            try {
                                if (x) throw"timeout";
                                var s = "xml" === c.dataType || F.XMLDocument || t.isXMLDoc(F);
                                if (n("isXml=" + s), !s && window.opera && (null === F.body || !F.body.innerHTML) && --$) return n("requeing onLoad callback, DOM not available"), void setTimeout(r, 250);
                                var l = F.body ? F.body : F.documentElement;
                                v.responseText = l ? l.innerHTML : null, v.responseXML = F.XMLDocument ? F.XMLDocument : F, s && (c.dataType = "xml"), v.getResponseHeader = function (t) {
                                    var e = {"content-type": c.dataType};
                                    return e[t.toLowerCase()]
                                }, l && (v.status = Number(l.getAttribute("status")) || v.status, v.statusText = l.getAttribute("statusText") || v.statusText);
                                var h = (c.dataType || "").toLowerCase(), d = /(json|script|text)/.test(h);
                                if (d || c.textarea) {
                                    var f = F.getElementsByTagName("textarea")[0];
                                    if (f) v.responseText = f.value, v.status = Number(f.getAttribute("status")) || v.status, v.statusText = f.getAttribute("statusText") || v.statusText; else if (d) {
                                        var p = F.getElementsByTagName("pre")[0], y = F.getElementsByTagName("body")[0];
                                        p ? v.responseText = p.textContent ? p.textContent : p.innerText : y && (v.responseText = y.textContent ? y.textContent : y.innerText)
                                    }
                                } else "xml" === h && !v.responseXML && v.responseText && (v.responseXML = A(v.responseText));
                                try {
                                    L = O(v, h, c)
                                } catch (b) {
                                    a = "parsererror", v.error = i = b || a
                                }
                            } catch (b) {
                                n("error caught: ", b), a = "error", v.error = i = b || a
                            }
                            v.aborted && (n("upload aborted"), a = null), v.status && (a = v.status >= 200 && v.status < 300 || 304 === v.status ? "success" : "error"), "success" === a ? (c.success && c.success.call(c.context, L, "success", v), k.resolve(v.responseText, "success", v), u && t.event.trigger("ajaxSuccess", [v, c])) : a && ("undefined" == typeof i && (i = v.statusText), c.error && c.error.call(c.context, v, a, i), k.reject(v, "error", i), u && t.event.trigger("ajaxError", [v, c, i])), u && t.event.trigger("ajaxComplete", [v, c]), u && !--t.active && t.event.trigger("ajaxStop"), c.complete && c.complete.call(c.context, v, a), I = !0, c.timeout && clearTimeout(C), setTimeout(function () {
                                c.iframeTarget ? g.attr("src", c.iframeSrc) : g.remove(), v.responseXML = null
                            }, 100)
                        }
                    }
                }

                var l, h, c, u, f, g, m, v, b, w, x, C, _ = p[0], k = t.Deferred();
                if (k.abort = function (t) {
                    v.abort(t)
                }, i) for (h = 0; h < y.length; h++) l = t(y[h]), s ? l.prop("disabled", !1) : l.removeAttr("disabled");
                c = t.extend(!0, {}, t.ajaxSettings, e), c.context = c.context || c, f = "jqFormIO" + (new Date).getTime();
                var T = _.ownerDocument, S = p.closest("body");
                if (c.iframeTarget ? (g = t(c.iframeTarget, T), w = g.attr2("name"), w ? f = w : g.attr2("name", f)) : (g = t('<iframe name="' + f + '" src="' + c.iframeSrc + '" />', T), g.css({
                    position: "absolute",
                    top: "-1000px",
                    left: "-1000px"
                })), m = g[0], v = {
                    aborted: 0,
                    responseText: null,
                    responseXML: null,
                    status: 0,
                    statusText: "n/a",
                    getAllResponseHeaders: function () {
                    },
                    getResponseHeader: function () {
                    },
                    setRequestHeader: function () {
                    },
                    abort: function (e) {
                        var i = "timeout" === e ? "timeout" : "aborted";
                        n("aborting upload... " + i), this.aborted = 1;
                        try {
                            m.contentWindow.document.execCommand && m.contentWindow.document.execCommand("Stop")
                        } catch (o) {
                        }
                        g.attr("src", c.iframeSrc), v.error = i, c.error && c.error.call(c.context, v, i, e), u && t.event.trigger("ajaxError", [v, c, i]), c.complete && c.complete.call(c.context, v, i)
                    }
                }, u = c.global, u && 0 === t.active++ && t.event.trigger("ajaxStart"), u && t.event.trigger("ajaxSend", [v, c]), c.beforeSend && c.beforeSend.call(c.context, v, c) === !1) return c.global && t.active--, k.reject(), k;
                if (v.aborted) return k.reject(), k;
                b = _.clk, b && (w = b.name, w && !b.disabled && (c.extraData = c.extraData || {}, c.extraData[w] = b.value, "image" === b.type && (c.extraData[w + ".x"] = _.clk_x, c.extraData[w + ".y"] = _.clk_y)));
                var D = 1, M = 2, P = t("meta[name=csrf-token]").attr("content"),
                    z = t("meta[name=csrf-param]").attr("content");
                z && P && (c.extraData = c.extraData || {}, c.extraData[z] = P), c.forceSync ? a() : setTimeout(a, 10);
                var L, F, I, $ = 50, A = t.parseXML || function (t, e) {
                    return window.ActiveXObject ? (e = new ActiveXObject("Microsoft.XMLDOM"), e.async = "false", e.loadXML(t)) : e = (new DOMParser).parseFromString(t, "text/xml"), e && e.documentElement && "parsererror" !== e.documentElement.nodeName ? e : null
                }, E = t.parseJSON || function (t) {
                    return window.eval("(" + t + ")")
                }, O = function (e, i, n) {
                    var o = e.getResponseHeader("content-type") || "", a = ("xml" === i || !i) && o.indexOf("xml") >= 0,
                        s = a ? e.responseXML : e.responseText;
                    return a && "parsererror" === s.documentElement.nodeName && t.error && t.error("parsererror"), n && n.dataFilter && (s = n.dataFilter(s, i)), "string" == typeof s && (("json" === i || !i) && o.indexOf("json") >= 0 ? s = E(s) : ("script" === i || !i) && o.indexOf("javascript") >= 0 && t.globalEval(s)), s
                };
                return k
            }

            if (!this.length) return n("ajaxSubmit: skipping submit process - no element selected"), this;
            var d, u, f, p = this;
            "function" == typeof e ? e = {success: e} : "string" == typeof e || e === !1 && arguments.length > 0 ? (e = {
                url: e,
                data: i,
                dataType: o
            }, "function" == typeof r && (e.success = r)) : "undefined" == typeof e && (e = {}), d = e.method || e.type || this.attr2("method"), u = e.url || this.attr2("action"), f = "string" == typeof u ? t.trim(u) : "", f = f || window.location.href || "", f && (f = (f.match(/^([^#]+)/) || [])[1]), e = t.extend(!0, {
                url: f,
                success: t.ajaxSettings.success,
                type: d || t.ajaxSettings.type,
                iframeSrc: /^https/i.test(window.location.href || "") ? "javascript:false" : "about:blank"
            }, e);
            var g = {};
            if (this.trigger("form-pre-serialize", [this, e, g]), g.veto) return n("ajaxSubmit: submit vetoed via form-pre-serialize trigger"), this;
            if (e.beforeSerialize && e.beforeSerialize(this, e) === !1) return n("ajaxSubmit: submit aborted via beforeSerialize callback"), this;
            var m = e.traditional;
            "undefined" == typeof m && (m = t.ajaxSettings.traditional);
            var v, y = [], b = this.formToArray(e.semantic, y, e.filtering);
            if (e.data) {
                var w = "function" == typeof e.data ? e.data(b) : e.data;
                e.extraData = w, v = t.param(w, m)
            }
            if (e.beforeSubmit && e.beforeSubmit(b, this, e) === !1) return n("ajaxSubmit: submit aborted via beforeSubmit callback"), this;
            if (this.trigger("form-submit-validate", [b, this, e, g]), g.veto) return n("ajaxSubmit: submit vetoed via form-submit-validate trigger"), this;
            var x = t.param(b, m);
            v && (x = x ? x + "&" + v : v), "GET" === e.type.toUpperCase() ? (e.url += (e.url.indexOf("?") >= 0 ? "&" : "?") + x, e.data = null) : e.data = x;
            var C = [];
            if (e.resetForm && C.push(function () {
                p.resetForm()
            }), e.clearForm && C.push(function () {
                p.clearForm(e.includeHidden)
            }), !e.dataType && e.target) {
                var _ = e.success || function () {
                };
                C.push(function (i, n, o) {
                    var a = arguments, s = e.replaceTarget ? "replaceWith" : "html";
                    t(e.target)[s](i).each(function () {
                        _.apply(this, a)
                    })
                })
            } else e.success && (Array.isArray(e.success) ? t.merge(C, e.success) : C.push(e.success));
            if (e.success = function (t, i, n) {
                for (var o = e.context || this, a = 0, s = C.length; a < s; a++) C[a].apply(o, [t, i, n || p, p])
            }, e.error) {
                var k = e.error;
                e.error = function (t, i, n) {
                    var o = e.context || this;
                    k.apply(o, [t, i, n, p])
                }
            }
            if (e.complete) {
                var T = e.complete;
                e.complete = function (t, i) {
                    var n = e.context || this;
                    T.apply(n, [t, i, p])
                }
            }
            var S = t("input[type=file]:enabled", this).filter(function () {
                    return "" !== t(this).val()
                }), D = S.length > 0, M = "multipart/form-data", P = p.attr("enctype") === M || p.attr("encoding") === M,
                z = a.fileapi && a.formdata;
            n("fileAPI :" + z);
            var L, F = (D || P) && !z;
            e.iframe !== !1 && (e.iframe || F) ? e.closeKeepAlive ? t.get(e.closeKeepAlive, function () {
                L = c(b)
            }) : L = c(b) : L = (D || P) && z ? h(b) : t.ajax(e), p.removeData("jqxhr").data("jqxhr", L);
            for (var I = 0; I < y.length; I++) y[I] = null;
            return this.trigger("form-submit-notify", [this, e]), this
        }, t.fn.ajaxForm = function (o, a, s, r) {
            if (("string" == typeof o || o === !1 && arguments.length > 0) && (o = {
                url: o,
                data: a,
                dataType: s
            }, "function" == typeof r && (o.success = r)), o = o || {}, o.delegation = o.delegation && "function" == typeof t.fn.on, !o.delegation && 0 === this.length) {
                var l = {s: this.selector, c: this.context};
                return !t.isReady && l.s ? (n("DOM not ready, queuing ajaxForm"), t(function () {
                    t(l.s, l.c).ajaxForm(o)
                }), this) : (n("terminating; zero elements found by selector" + (t.isReady ? "" : " (DOM not ready)")), this)
            }
            return o.delegation ? (t(document).off("submit.form-plugin", this.selector, e).off("click.form-plugin", this.selector, i).on("submit.form-plugin", this.selector, o, e).on("click.form-plugin", this.selector, o, i), this) : this.ajaxFormUnbind().on("submit.form-plugin", o, e).on("click.form-plugin", o, i)
        }, t.fn.ajaxFormUnbind = function () {
            return this.off("submit.form-plugin click.form-plugin")
        }, t.fn.formToArray = function (e, i, n) {
            var o = [];
            if (0 === this.length) return o;
            var s, r = this[0], l = this.attr("id"),
                h = e || "undefined" == typeof r.elements ? r.getElementsByTagName("*") : r.elements;
            if (h && (h = t.makeArray(h)), l && (e || /(Edge|Trident)\//.test(navigator.userAgent)) && (s = t(':input[form="' + l + '"]').get(), s.length && (h = (h || []).concat(s))), !h || !h.length) return o;
            "function" == typeof n && (h = t.map(h, n));
            var c, d, u, f, p, g, m;
            for (c = 0, g = h.length; c < g; c++) if (p = h[c], u = p.name, u && !p.disabled) if (e && r.clk && "image" === p.type) r.clk === p && (o.push({
                name: u,
                value: t(p).val(),
                type: p.type
            }), o.push({name: u + ".x", value: r.clk_x}, {
                name: u + ".y",
                value: r.clk_y
            })); else if (f = t.fieldValue(p, !0), f && f.constructor === Array) for (i && i.push(p), d = 0, m = f.length; d < m; d++) o.push({
                name: u,
                value: f[d]
            }); else if (a.fileapi && "file" === p.type) {
                i && i.push(p);
                var v = p.files;
                if (v.length) for (d = 0; d < v.length; d++) o.push({
                    name: u,
                    value: v[d],
                    type: p.type
                }); else o.push({name: u, value: "", type: p.type})
            } else null !== f && "undefined" != typeof f && (i && i.push(p), o.push({
                name: u,
                value: f,
                type: p.type,
                required: p.required
            }));
            if (!e && r.clk) {
                var y = t(r.clk), b = y[0];
                u = b.name, u && !b.disabled && "image" === b.type && (o.push({
                    name: u,
                    value: y.val()
                }), o.push({name: u + ".x", value: r.clk_x}, {name: u + ".y", value: r.clk_y}))
            }
            return o
        }, t.fn.formSerialize = function (e) {
            return t.param(this.formToArray(e))
        }, t.fn.fieldSerialize = function (e) {
            var i = [];
            return this.each(function () {
                var n = this.name;
                if (n) {
                    var o = t.fieldValue(this, e);
                    if (o && o.constructor === Array) for (var a = 0, s = o.length; a < s; a++) i.push({
                        name: n,
                        value: o[a]
                    }); else null !== o && "undefined" != typeof o && i.push({name: this.name, value: o})
                }
            }), t.param(i)
        }, t.fn.fieldValue = function (e) {
            for (var i = [], n = 0, o = this.length; n < o; n++) {
                var a = this[n], s = t.fieldValue(a, e);
                null === s || "undefined" == typeof s || s.constructor === Array && !s.length || (s.constructor === Array ? t.merge(i, s) : i.push(s))
            }
            return i
        }, t.fieldValue = function (e, i) {
            var n = e.name, a = e.type, s = e.tagName.toLowerCase();
            if ("undefined" == typeof i && (i = !0), i && (!n || e.disabled || "reset" === a || "button" === a || ("checkbox" === a || "radio" === a) && !e.checked || ("submit" === a || "image" === a) && e.form && e.form.clk !== e || "select" === s && e.selectedIndex === -1)) return null;
            if ("select" === s) {
                var r = e.selectedIndex;
                if (r < 0) return null;
                for (var l = [], h = e.options, c = "select-one" === a, d = c ? r + 1 : h.length, u = c ? r : 0; u < d; u++) {
                    var f = h[u];
                    if (f.selected && !f.disabled) {
                        var p = f.value;
                        if (p || (p = f.attributes && f.attributes.value && !f.attributes.value.specified ? f.text : f.value), c) return p;
                        l.push(p)
                    }
                }
                return l
            }
            return t(e).val().replace(o, "\r\n")
        }, t.fn.clearForm = function (e) {
            return this.each(function () {
                t("input,select,textarea", this).clearFields(e)
            })
        }, t.fn.clearFields = t.fn.clearInputs = function (e) {
            var i = /^(?:color|date|datetime|email|month|number|password|range|search|tel|text|time|url|week)$/i;
            return this.each(function () {
                var n = this.type, o = this.tagName.toLowerCase();
                i.test(n) || "textarea" === o ? this.value = "" : "checkbox" === n || "radio" === n ? this.checked = !1 : "select" === o ? this.selectedIndex = -1 : "file" === n ? /MSIE/.test(navigator.userAgent) ? t(this).replaceWith(t(this).clone(!0)) : t(this).val("") : e && (e === !0 && /hidden/.test(n) || "string" == typeof e && t(this).is(e)) && (this.value = "")
            })
        }, t.fn.resetForm = function () {
            return this.each(function () {
                var e = t(this), i = this.tagName.toLowerCase();
                switch (i) {
                    case"input":
                        this.checked = this.defaultChecked;
                    case"textarea":
                        return this.value = this.defaultValue, !0;
                    case"option":
                    case"optgroup":
                        var n = e.parents("select");
                        return n.length && n[0].multiple ? "option" === i ? this.selected = this.defaultSelected : e.find("option").resetForm() : n.resetForm(), !0;
                    case"select":
                        return e.find("option").each(function (t) {
                            if (this.selected = this.defaultSelected, this.defaultSelected && !e[0].multiple) return e[0].selectedIndex = t, !1
                        }), !0;
                    case"label":
                        var o = t(e.attr("for")), a = e.find("input,select,textarea");
                        return o[0] && a.unshift(o[0]), a.resetForm(), !0;
                    case"form":
                        return ("function" == typeof this.reset || "object" == typeof this.reset && !this.reset.nodeType) && this.reset(), !0;
                    default:
                        return e.find("form,input,label,select,textarea").resetForm(), !0
                }
            })
        }, t.fn.enable = function (t) {
            return "undefined" == typeof t && (t = !0), this.each(function () {
                this.disabled = !t
            })
        }, t.fn.selected = function (e) {
            return "undefined" == typeof e && (e = !0), this.each(function () {
                var i = this.type;
                if ("checkbox" === i || "radio" === i) this.checked = e; else if ("option" === this.tagName.toLowerCase()) {
                    var n = t(this).parent("select");
                    e && n[0] && "select-one" === n[0].type && n.find("option").selected(!1), this.selected = e
                }
            })
        }, t.fn.ajaxSubmit.debug = !1
    }),/*!
 * jQuery Hotkeys Plugin
 * Copyright 2010, John Resig
 * Dual licensed under the MIT or GPL Version 2 licenses.
 *
 * Based upon the plugin by Tzury Bar Yochay:
 * http://github.com/tzuryby/hotkeys
 *
 * Original idea by:
 * Binny V A, http://www.openjs.com/scripts/events/keyboard_shortcuts/
*/
    function (t) {
        function e(e) {
            if ("string" == typeof e.data) {
                var i = e.handler, n = e.data.toLowerCase().split(" ");
                e.handler = function (e) {
                    if (this === e.target || !/textarea|select/i.test(e.target.nodeName) && "text" !== e.target.type) {
                        var o = "keypress" !== e.type && t.hotkeys.specialKeys[e.which],
                            a = String.fromCharCode(e.which).toLowerCase(), s = "", r = {};
                        e.altKey && "alt" !== o && (s += "alt+"), e.ctrlKey && "ctrl" !== o && (s += "ctrl+"), e.metaKey && !e.ctrlKey && "meta" !== o && (s += "meta+"), e.shiftKey && "shift" !== o && (s += "shift+"), o ? r[s + o] = !0 : (r[s + a] = !0, r[s + t.hotkeys.shiftNums[a]] = !0, "shift+" === s && (r[t.hotkeys.shiftNums[a]] = !0));
                        for (var l = 0, h = n.length; l < h; l++) if (r[n[l]]) return i.apply(this, arguments)
                    }
                }
            }
        }

        t.hotkeys = {
            version: "0.8",
            specialKeys: {
                8: "backspace",
                9: "tab",
                13: "return",
                16: "shift",
                17: "ctrl",
                18: "alt",
                19: "pause",
                20: "capslock",
                27: "esc",
                32: "space",
                33: "pageup",
                34: "pagedown",
                35: "end",
                36: "home",
                37: "left",
                38: "up",
                39: "right",
                40: "down",
                45: "insert",
                46: "del",
                96: "0",
                97: "1",
                98: "2",
                99: "3",
                100: "4",
                101: "5",
                102: "6",
                103: "7",
                104: "8",
                105: "9",
                106: "*",
                107: "+",
                109: "-",
                110: ".",
                111: "/",
                112: "f1",
                113: "f2",
                114: "f3",
                115: "f4",
                116: "f5",
                117: "f6",
                118: "f7",
                119: "f8",
                120: "f9",
                121: "f10",
                122: "f11",
                123: "f12",
                144: "numlock",
                145: "scroll",
                191: "/",
                224: "meta"
            },
            shiftNums: {
                "`": "~",
                1: "!",
                2: "@",
                3: "#",
                4: "$",
                5: "%",
                6: "^",
                7: "&",
                8: "*",
                9: "(",
                0: ")",
                "-": "_",
                "=": "+",
                ";": ": ",
                "'": '"',
                ",": "<",
                ".": ">",
                "/": "?",
                "\\": "|"
            }
        }, t.each(["keydown", "keyup", "keypress"], function () {
            t.event.special[this] = {add: e}
        })
    }(jQuery), function (t) {
    "use strict";

    function e(e, i) {
        if (e === !1) return e;
        if (!e) return i;
        e === !0 ? e = {add: !0, "delete": !0, edit: !0, sort: !0} : "string" == typeof e && (e = e.split(","));
        var n;
        return Array.isArray(e) && (n = {}, t.each(e, function (e, i) {
            t.isPlainObject(i) ? n[i.action] = i : n[i] = !0
        }), e = n), t.isPlainObject(e) && (n = {}, t.each(e, function (e, i) {
            i ? n[e] = t.extend({type: e}, s[e], t.isPlainObject(i) ? i : null) : n[e] = !1
        }), e = n), i ? t.extend(!0, {}, i, e) : e
    }

    function i(e, i, n) {
        return i = i || e.type, t(n || e.template).addClass("tree-action").attr(t.extend({
            "data-type": i,
            title: e.title || ""
        }, e.attr)).data("action", e)
    }

    var n = "zui.tree", o = 0, a = function (e, i) {
        this.name = n, this.$ = t(e), this.getOptions(i), this._init()
    }, s = {
        sort: {template: '<a class="sort-handler" href="javascript:;"><i class="icon icon-move"></i></a>'},
        add: {template: '<a href="javascript:;"><i class="icon icon-plus"></i></a>'},
        edit: {template: '<a href="javascript:;"><i class="icon icon-pencil"></i></a>'},
        "delete": {template: '<a href="javascript:;"><i class="icon icon-trash"></i></a>'}
    };
    a.DEFAULTS = {
        animate: null,
        initialState: "normal",
        toggleTemplate: '<i class="list-toggle icon"></i>'
    }, a.prototype.add = function (e, i, n, o, a) {
        var s, r = t(e), l = this.options;
        if (r.is("li") ? (s = r.children("ul"), s.length || (s = t("<ul/>"), r.append(s), this._initList(s, r))) : s = r, s) {
            var h = this;
            Array.isArray(i) || (i = [i]), t.each(i, function (e, i) {
                var n = t("<li/>").data(i).appendTo(s);
                void 0 !== i.id && n.attr("data-id", i.id);
                var o = l.itemWrapper ? t(l.itemWrapper === !0 ? '<div class="tree-item-wrapper"/>' : l.itemWrapper).appendTo(n) : n;
                if (i.html) o.html(i.html); else if ("function" == typeof h.options.itemCreator) {
                    var a = h.options.itemCreator(n, i);
                    a !== !0 && a !== !1 && o.html(a)
                } else i.url ? o.append(t("<a/>", {href: i.url}).text(i.title || i.name)) : o.append(t("<span/>").text(i.title || i.name));
                h._initItem(n, i.idx || e, s, i), i.children && i.children.length && h.add(n, i.children)
            }), this._initList(s), n && !s.hasClass("tree") && h.expand(s.parent("li"), o, a)
        }
    }, a.prototype.reload = function (e) {
        var i = this;
        e && (i.$.empty(), i.add(i.$, e)), i.isPreserve && i.store.time && i.$.find("li:not(.tree-action-item)").each(function () {
            var e = t(this);
            i[i.store[e.data("id")] ? "expand" : "collapse"](e, !0, !0)
        })
    }, a.prototype._initList = function (n, o, a, s) {
        var r = this;
        n.hasClass("tree") ? (a = 0, o = null) : (o = (o || n.closest("li")).addClass("has-list"), o.find(".list-toggle").length || o.prepend(this.options.toggleTemplate), a = a || o.data("idx")), n.removeClass("has-active-item");
        var l = n.attr("data-idx", a || 0).children("li:not(.tree-action-item)").each(function (e) {
            r._initItem(t(this), e + 1, n)
        });
        1 !== l.length || l.find("ul").length || l.addClass("tree-single-item"), s = s || (o ? o.data() : null);
        var h = e(s ? s.actions : null, this.actions);
        if (h) {
            if (h.add && h.add.templateInList !== !1) {
                var c = n.children("li.tree-action-item");
                c.length ? c.detach().appendTo(n) : t('<li class="tree-action-item"/>').append(i(h.add, "add", h.add.templateInList)).appendTo(n)
            }
            h.sort && n.sortable(t.extend({
                dragCssClass: "tree-drag-holder",
                trigger: ".sort-handler",
                selector: "li:not(.tree-action-item)",
                finish: function (t) {
                    r.callEvent("action", {action: h.sort, $list: n, target: t.target, item: s})
                }
            }, h.sort.options, t.isPlainObject(this.options.sortable) ? this.options.sortable : null))
        }
        o && (o.hasClass("open") || s && s.open) && o.addClass("open in")
    }, a.prototype._initItem = function (n, o, a, s) {
        if (void 0 === o) {
            var r = n.prev("li");
            o = r.length ? r.data("idx") + 1 : 1
        }
        if (a = a || n.closest("ul"), n.attr("data-idx", o).removeClass("tree-single-item"), !n.data("id")) {
            var l = o;
            a.hasClass("tree") || (l = a.parent("li").data("id") + "-" + l), n.attr("data-id", l)
        }
        n.hasClass("active") && a.parent("li").addClass("has-active-item"), s = s || n.data();
        var h = e(s.actions, this.actions);
        if (h) {
            var c = n.find(".tree-actions");
            c.length || (c = t('<div class="tree-actions"/>').appendTo(this.options.itemWrapper ? n.find(".tree-item-wrapper") : n), t.each(h, function (t, e) {
                e && c.append(i(e, t))
            }))
        }
        var d = n.children("ul");
        d.length && this._initList(d, n, o, s)
    }, a.prototype._init = function () {
        var i = this.options, a = this;
        this.actions = e(i.actions), this.$.addClass("tree"), i.animate && this.$.addClass("tree-animate"), this._initList(this.$);
        var s = i.initialState, r = t.zui && t.zui.store && t.zui.store.enable;
        r && (this.selector = n + "::" + (i.name || "") + "#" + (this.$.attr("id") || o++), this.store = t.zui.store[i.name ? "get" : "pageGet"](this.selector, {})), "preserve" === s && (r ? this.isPreserve = !0 : this.options.initialState = s = "normal"), this.reload(i.data), r && (this.isPreserve = !0), "expand" === s ? this.expand() : "collapse" === s ? this.collapse() : "active" === s && this.expandSelect(".active"), this.$.on("click", '.list-toggle,a[href="#"],.tree-toggle', function (e) {
            var i = t(this), n = i.parent("li");
            a.callEvent("hit", {target: n, item: n.data()}), a.toggle(n), i.is("a") && e.preventDefault()
        }).on("click", ".tree-action", function () {
            var e = t(this), i = e.data();
            if (i.action && (i = i.action), "sort" !== i.type) {
                var n = e.closest("li:not(.tree-action-item)");
                a.callEvent("action", {action: i, target: this, $item: n, item: n.data()})
            }
        })
    }, a.prototype.preserve = function (e, i, n) {
        if (this.isPreserve) if (e) i = i || e.data("id"), n = void 0 === n && e.hasClass("open"), n ? this.store[i] = n : delete this.store[i], this.store.time = (new Date).getTime(), t.zui.store[this.options.name ? "set" : "pageSet"](this.selector, this.store); else {
            var o = this;
            this.store = {}, this.$.find("li").each(function () {
                o.preserve(t(this))
            })
        }
    }, a.prototype.expandSelect = function (t) {
        this.show(t, !0)
    }, a.prototype.expand = function (t, e, i) {
        t ? (t.addClass("open"), !e && this.options.animate ? setTimeout(function () {
            t.addClass("in")
        }, 10) : t.addClass("in")) : t = this.$.find("li.has-list").addClass("open in"), i || this.preserve(t), this.callEvent("expand", t, this)
    }, a.prototype.show = function (e, i, n) {
        var o = this;
        e instanceof t || (e = o.$.find("li").filter(e)), e.each(function () {
            var e = t(this);
            if (o.expand(e, i, n), e) for (var a = e.parent("ul"); a && a.length && !a.hasClass("tree");) {
                var s = a.parent("li");
                s.length ? (o.expand(s, i, n), a = s.parent("ul")) : a = !1
            }
        })
    }, a.prototype.collapse = function (t, e, i) {
        t ? !e && this.options.animate ? (t.removeClass("in"), setTimeout(function () {
            t.removeClass("open")
        }, 300)) : t.removeClass("open in") : t = this.$.find("li.has-list").removeClass("open in"), i || this.preserve(t), this.callEvent("collapse", t, this)
    }, a.prototype.toggle = function (t) {
        var e = t && t.hasClass("open") || t === !1 || void 0 === t && this.$.find("li.has-list.open").length;
        this[e ? "collapse" : "expand"](t)
    }, a.prototype.getOptions = function (e) {
        this.options = t.extend({}, a.DEFAULTS, this.$.data(), e), null === this.options.animate && this.$.hasClass("tree-animate") && (this.options.animate = !0)
    }, a.prototype.toData = function (e, i) {
        "function" == typeof e && (i = e, e = null), e = e || this.$;
        var n = this;
        return e.children("li:not(.tree-action-item)").map(function () {
            var e = t(this), o = e.data();
            delete o["zui.droppable"];
            var a = e.children("ul");
            return a.length && (o.children = n.toData(a)), "function" == typeof i ? i(o, e) : o
        }).get()
    }, a.prototype.callEvent = function (e, i) {
        var n;
        return "function" == typeof this.options[e] && (n = this.options[e](i, this)), this.$.trigger(t.Event(e + "." + this.name, i)), n
    }, t.fn.tree = function (e, i) {
        return this.each(function () {
            var o = t(this), s = o.data(n), r = "object" == typeof e && e;
            s || o.data(n, s = new a(this, r)), "string" == typeof e && s[e](i)
        })
    }, t.fn.tree.Constructor = a, t(function () {
        t('[data-ride="tree"]').tree()
    })
}(jQuery), function (t) {
    "use strict";
    var e = "zui.colorPicker",
        i = '<div class="colorpicker"><button type="button" class="btn dropdown-toggle" data-toggle="dropdown"><span class="cp-title"></span><i class="ic"></i></button><ul class="dropdown-menu clearfix"></ul></div>',
        n = {zh_cn: {errorTip: "不是有效的颜色值"}, zh_tw: {errorTip: "不是有效的顏色值"}, en: {errorTip: "Not a valid color value"}},
        o = function (i, n) {
            this.name = e, this.$ = t(i), this.getOptions(n), this.init()
        };
    o.prototype.init = function () {
        var e = this, n = e.options, o = e.$, a = o.parent(), s = !1;
        a.hasClass("colorpicker") ? e.$picker = a : (e.$picker = t(n.template || i), s = !0), e.$picker.addClass(n.wrapper).find(".cp-title").toggle(void 0 !== n.title).text(n.title), e.$menu = e.$picker.find(".dropdown-menu").toggleClass("pull-right", n.pullMenuRight), e.$btn = e.$picker.find(".btn.dropdown-toggle"), e.$btn.find(".ic").addClass("icon-" + n.icon), n.btnTip && e.$picker.attr("data-toggle", "tooltip").tooltip({
            title: n.btnTip,
            placement: n.tooltip,
            container: "body"
        }), o.attr("data-provide", null), s && o.after(e.$picker), e.colors = {}, t.each(n.colors, function (i, n) {
            if (t.zui.Color.isColor(n)) {
                var o = new t.zui.Color(n);
                e.colors[o.toCssStr()] = o
            }
        }), e.updateColors(), e.$picker.on("click", ".cp-tile", function () {
            e.setValue(t(this).data("color"))
        });
        var r = function () {
            var i = o.val(), a = t.zui.Color.isColor(i);
            o.parent().toggleClass("has-error", !(a || n.optional && "" === i)), a ? e.setValue(i, !0) : n.optional && "" === i ? o.tooltip("hide") : o.is(":focus") || o.tooltip("show", n.errorTip)
        };
        o.is("input:not([type=hidden])") ? (n.tooltip && o.attr("data-toggle", "tooltip").tooltip({
            trigger: "manual",
            placement: n.tooltip,
            tipClass: "tooltip-danger",
            container: "body"
        }), o.on("keyup paste input change", r)) : o.appendTo(e.$picker), r()
    }, o.prototype.addColor = function (e) {
        e instanceof t.zui.Color || (e = new t.zui.Color(e));
        var i = e.toCssStr(), n = this.options;
        this.colors[i] || (this.colors[i] = e);
        var o = t('<a href="###" class="cp-tile"></a>', {titile: e}).data("color", e).css({
            color: e.contrast().toCssStr(),
            background: i,
            "border-color": e.luma() > .43 ? "#ccc" : "transparent"
        }).attr("data-color", i);
        this.$menu.append(t("<li/>").css({
            width: n.tileSize,
            height: n.tileSize
        }).append(o)), n.optional && this.$menu.find(".cp-tile.empty").parent().detach().appendTo(this.$menu)
    }, o.prototype.updateColors = function (e) {
        var i = this.$menu, n = this.options, e = e || this.colors, o = this, a = 0;
        if (i.children("li:not(.heading)").remove(), t.each(e, function (t, e) {
            o.addColor(e), a++
        }), n.optional) {
            var s = t('<li><a class="cp-tile empty" href="###"></a></li>').css({width: n.tileSize, height: n.tileSize});
            this.$menu.append(s), a++
        }
        i.css("width", Math.min(a, n.lineCount) * n.tileSize + 6)
    }, o.prototype.setValue = function (e, i) {
        var n = this, o = n.options, a = n.$btn, s = "";
        n.$menu.find(".cp-tile.active").removeClass("active");
        var r = o.updateBtn;
        if ("auto" === r) {
            var l = a.find(".color-bar");
            r = !l.length || function (t) {
                l.css("background", t || "")
            }
        }
        if (e) {
            var h = new t.zui.Color(e);
            s = h.toCssStr().toLowerCase(), r && ("function" == typeof r ? r(s, a, n) : a.css({
                background: s,
                color: h.contrast().toCssStr(),
                borderColor: h.luma() > .43 ? "#ccc" : s
            })), n.colors[s] || n.addColor(h), i || n.$.val().toLowerCase() === s || n.$.val(s).trigger("change"), n.$menu.find('.cp-tile[data-color="' + s + '"]').addClass("active"), n.$.tooltip("hide"), n.$.trigger("colorchange", h)
        } else r && ("function" == typeof r ? r(null, a, n) : a.attr("style", null)), i || "" === n.$.val() || n.$.val(s).trigger("change"), o.optional && n.$.tooltip("hide"), n.$menu.find(".cp-tile.empty").addClass("active"), n.$.trigger("colorchange", null);
        o.updateBorder && t(o.updateBorder).css("border-color", s), o.updateBackground && t(o.updateBackground).css("background-color", s), o.updateColor && t(o.updateColor).css("color", s), o.updateText && t(o.updateText).text(s)
    }, o.prototype.getOptions = function (i) {
        var a = t.extend({}, o.DEFAULTS, this.$.data(), i);
        "string" == typeof a.colors && (a.colors = a.colors.split(","));
        var s = a.lang || t.zui.clientLang(),
            r = this.lang = t.zui.getLangData ? t.zui.getLangData(e, s, n) : n[s] || n.en;
        a.errorTip || (a.errorTip = r.errorTip), t.fn.tooltip || (a.btnTip = !1), this.options = a
    }, o.DEFAULTS = {
        colors: ["#00BCD4", "#388E3C", "#3280fc", "#3F51B5", "#9C27B0", "#795548", "#F57C00", "#F44336", "#E91E63"],
        pullMenuRight: !0,
        wrapper: "btn-wrapper",
        tileSize: 30,
        lineCount: 5,
        optional: !0,
        tooltip: "top",
        icon: "caret-down",
        updateBtn: "auto"
    }, o.LANG = n, t.fn.colorPicker = function (e) {
        return this.each(function () {
            var i = t(this), n = i.data(name), a = "object" == typeof e && e;
            n || i.data(name, n = new o(this, a)), "string" == typeof e && n[e]()
        })
    }, t.fn.colorPicker.Constructor = o, t(function () {
        t('[data-provide="colorpicker"]').colorPicker()
    })
}(jQuery), function (t, e) {
    function i(t) {
        return t === e && (t = o += 1), a[t % a.length]
    }

    function n(e, i) {
        var n = t(e);
        i = t.extend({
            percent: 0,
            size: 20,
            backColor: "#eee",
            color: "#00da88",
            borderColor: "#ccc",
            borderSize: 1,
            rotate: -90,
            doughnut: 8
        }, n.data(), i);
        var o = i.percent, a = i.size;
        "string" == typeof o && (o = Number.parseFloat(o, 10)), "string" == typeof a && (a = Number.parseFloat(a, 10)), a = Math.floor(a);
        var s = a / 2, r = 3.14 * s, l = "http://www.w3.org/2000/svg", h = document.createElementNS(l, "svg"),
            c = document.createElementNS(l, "circle"), d = document.createElementNS(l, "circle"),
            u = document.createElementNS(l, "circle");
        d.setAttribute("r", s), d.setAttribute("cx", s), d.setAttribute("cy", s), d.setAttribute("fill", i.backColor), d.setAttribute("stroke", i.borderColor), d.setAttribute("stroke-width", i.borderSize), u.setAttribute("r", i.doughnut), u.setAttribute("cx", s), u.setAttribute("cy", s), u.setAttribute("fill", i.backColor), c.setAttribute("r", s / 2), c.setAttribute("cx", s), c.setAttribute("cy", s), c.setAttribute("fill", "transparent"), c.setAttribute("stroke-dasharray", (o * r / 100).toFixed(1) + " " + r), c.setAttribute("stroke-width", s), c.setAttribute("stroke", i.color), c.setAttribute("transform", "rotate(-90) translate(-" + a + ")"), h.setAttribute("viewBox", "0 0 " + a + " " + a), h.setAttribute("width", a), h.setAttribute("height", a), h.setAttribute("transform", "rotate(180)"), h.appendChild(d), h.appendChild(c), h.appendChild(u), n[0].appendChild(h);
        var f = {width: a, height: a};
        "inline" === n.css("display") && (f.display = "inline-block", f.verticalAlign = "middle"), n.css(f)
    }

    var o = 0,
        a = ["#00a9fc", "#ff5d5d", "#fdc137", "#00da88", "#7ec5ff", "#8666b8", "#bd7b46", "#ff9100", "#ff3d00", "#f57f17", "#00e5ff", "#00b0ff", "#2979ff", "#3d5afe", "#651fff", "#d500f9", "#f50057", "#ff1744"];
    jQuery.fn.tableChart = function () {
        t(this).each(function () {
            var e = t(this), n = e.data(), o = n.chart || "pie", a = t(n.target);
            if (a.length) {
                var s = null;
                if ("pie" === o) {
                    n = t.extend({scaleShowLabels: !0, scaleLabel: "<%=label%>: <%=value%>"}, n);
                    var r = [], l = e.find("tbody > tr").each(function (e) {
                        var n = t(this), o = i();
                        n.attr("data-id", e).find(".chart-color-dot").css("background", o), r.push({
                            label: n.find(".chart-label").text(),
                            value: parseFloat(n.data("value") || n.find(".chart-value").text()),
                            color: o,
                            id: e
                        })
                    });
                    r.length > 1 ? n.scaleLabelPlacement = "outside" : 1 === r.length && (n.scaleLabelPlacement = "inside", r.push({
                        label: "",
                        value: r[0].value / 2e3,
                        color: "#fff",
                        showLabel: !1
                    })), s = a.pieChart(r, n), a.on("mousemove", function (t) {
                        var e = s.getSegmentsAtEvent(t);
                        l.removeClass("active"), e.length && l.filter('[data-id="' + e[0].id + '"]').addClass("active")
                    })
                } else if ("bar" === o) {
                    var h = i(), c = [], d = {label: e.find("thead .chart-label").text(), color: h, data: []},
                        l = e.find("tbody > tr").each(function (e) {
                            var i = t(this);
                            c.push(i.find(".chart-label").text()), d.data.push(i.data("value") || parseFloat(i.find(".chart-value").text())), i.find(".chart-color-dot").css("background", h)
                        }), r = {labels: c, datasets: [d]};
                    c.length && (n.barValueSpacing = 5), s = a.barChart(r, n)
                } else if ("line" === o) {
                    var h = i(), c = [], d = {label: e.find("thead .chart-label").text(), color: h, data: []},
                        l = e.find("tbody > tr").each(function (e) {
                            var i = t(this);
                            c.push(i.find(".chart-label").text()), d.data.push(parseInt(i.find(".chart-value").text())), i.find(".chart-color-dot").css("background", h)
                        }), r = {labels: c, datasets: [d]};
                    c.length && (n.barValueSpacing = 5), s = a.lineChart(r, n)
                }
                null !== s && e.data("zui.chart", s)
            }
        })
    }, t(".table-chart").tableChart();
    var s = function (i, n) {
        var o = t(i);
        if (!o.data("pieChart")) {
            var a = o.is("canvas") ? o : o.find("canvas"), s = t.extend({
                value: 0,
                color: t.getThemeColor("primary") || "#006af1",
                backColor: t.getThemeColor("pale") || "#E9F2FB",
                doughnut: !0,
                doughnutSize: 85,
                width: 20,
                height: 20,
                showTip: !1,
                name: "",
                tipTemplate: "<%=value%>%",
                animation: "auto",
                realValue: parseFloat(o.find(".progress-value").text())
            }, n, o.data()), r = a.length;
            r || (a = t("<canvas>").appendTo(o)), a.attr("width") !== e ? s.width = a.width() : a.attr("width", s.width), a.attr("height") !== e ? s.height = a.height() : a.attr("height", s.height), r || 8 != t.zui.browser.ie || G_vmlCanvasManager.initElement(a[0]), "auto" === s.animation && (s.animation = s.width > 30), o.addClass("progress-pie-" + s.width).css({
                width: s.width,
                height: s.height
            }), s.value = Math.max(0, Math.min(100, s.value));
            var l = [{value: s.value, label: s.name, color: s.color, circleBeginEnd: !0}, {
                value: 100 - s.value,
                label: "",
                color: s.backColor
            }], h = a[s.doughnut ? "doughnutChart" : "pieChart"](l, t.extend({
                segmentShowStroke: !1,
                animation: s.animation,
                showTooltips: s.showTip,
                tooltipTemplate: s.tipTemplate,
                percentageInnerCutout: s.doughnutSize,
                reverseDrawOrder: !0,
                animationEasing: "easeInOutQuart",
                onAnimationProgress: s.realValue ? function (t) {
                    o.find(".progress-value").text(Math.floor(s.realValue * t))
                } : e,
                onAnimationComplete: s.realValue ? function (t) {
                    o.find(".progress-value").text(s.realValue)
                } : e
            }, s.chartOptions));
            o.data("pieChart", h)
        }
    };
    jQuery.fn.progressPie = function (e) {
        t(this).each(function () {
            var i = t(this);
            if (!i.closest(".hidden").length) {
                var n = i.closest(".tab-pane");
                n.length && !n.hasClass("active") ? t('[data-toggle="tab"][data-target="#' + n.attr("id") + '"]').one("shown.zui.tab", function () {
                    s(i, e)
                }) : s(this, e)
            }
        })
    }, t.fn.pieIcon = function (e) {
        t(this).each(function () {
            n(this, e)
        })
    }, t(function () {
        t(".table-chart").tableChart();
        var e = t(".progress-pie:visible");
        e.length < 100 && t(".progress-pie:visible").progressPie(), setTimeout(function () {
            t(".progress-pie:visible").progressPie()
        }, e.length > 100 ? 1e3 : 50), t(".pie-icon:visible").pieIcon()
    })
}(jQuery, void 0), function (t) {
    jQuery.fn.sparkline = function (e) {
        t(this).each(function () {
            var i = t(this),
                n = t.extend({values: i.attr("values"), width: i.width() - 4, height: i.height() - 4}, i.data(), e),
                o = n.height, a = [], s = n.width, r = n.values.split(","), l = 0;
            for (var h in r) {
                var c = parseFloat(r[h]);
                NaN != c && (a.push(c), l = Math.max(c, l))
            }
            var d = (Math.min(l, 30), Math.min(s, Math.max(10, a.length * s / 30))), u = i.children("canvas");
            u.length || (i.append('<canvas class="projectline-canvas"></canvas>'), u = i.children("canvas")), u.attr("width", d).attr("height", o);
            var f = {
                labels: a,
                datasets: [{
                    fillColor: t.getThemeColor("pale") || "rgba(0,0,255,0.05)",
                    strokeColor: t.getThemeColor("primary") || "#0054EC",
                    pointColor: t.getThemeColor("secondary") || "rgba(255,136,0,1)",
                    pointStrokeColor: "#fff",
                    data: a
                }]
            }, p = {
                animation: !0,
                scaleOverride: !0,
                scaleStepWidth: Math.ceil(l / 10),
                scaleSteps: 10,
                scaleStartValue: 0,
                showScale: !1,
                showTooltips: !1,
                pointDot: !1,
                scaleShowGridLines: !1,
                datasetStrokeWidth: 1
            }, g = t(u).lineChart(f, p);
            i.data("sparklineChart", g)
        })
    }, t(function () {
        t(".sparkline").sparkline()
    })
}(jQuery), function (t) {
    t.fn.fixedDate = function () {
        return t(this).each(function () {
            var e = t(this).attr("autocomplete", "off");
            "0000-00-00" == e.val() && e.focus(function () {
                "0000-00-00" == e.val() && e.val("").datetimepicker("update")
            }).blur(function () {
                "" == e.val() && e.val("0000-00-00")
            })
        })
    }, window.datepickerOptions = {
        language: t("html").attr("lang"),
        weekStart: 1,
        todayBtn: 1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        forceParse: 0,
        showMeridian: 1,
        format: "yyyy-mm-dd hh:ii",
        startDate: "1970-1-1"
    }, t.extend(t.fn.datetimepicker.defaults, window.datepickerOptions), t(function () {
        var e = {minView: 2, format: "yyyy-mm-dd"}, i = {startView: 1, minView: 0, maxView: 1, format: "hh:ii"},
            n = {minView: 3, startView: 3, format: "yyyy-mm"};
        t(".datepicker-wrapper").click(function () {
            t(this).find(".form-date, .form-datetime, .form-time, .form-month").datetimepicker("show").focus()
        }), t.fn.datepicker = function (i) {
            return this.datetimepicker(t.extend({}, e, i))
        }, t.fn.timepicker = function (e) {
            return this.datetimepicker(t.extend({}, i, e))
        }, t.fn.monthpicker = function (e) {
            return this.datetimepicker(t.extend({}, n, e))
        }, t.fn.datepickerAll = function () {
            return this.find(".form-datetime").fixedDate().datetimepicker(), this.find(".form-date").fixedDate().datepicker(), this.find(".form-time").fixedDate().timepicker(), this.find(".form-month").fixedDate().monthpicker(), this
        }, t("body").datepickerAll()
    })
}(jQuery), function (t) {
    var e = function (e, i) {
        i = t.extend({idStart: 0, idEnd: 9, chosen: !0, datetimepicker: !0, colorPicker: !0, hotkeys: !0}, i, e.data());
        var n = e.find(".template");
        !n.length && i.template && (n = t(i.template));
        var o = 0, a = 0, s = function (t) {
            t.is("select.chosen") ? t.next(".chosen-container").find("input").focus() : t.focus()
        }, r = function (t) {
            var i = e.find("[data-ctrl-index]:focus,.chosen-container-active").first();
            if (i.length) {
                if (i.is(".chosen-container-active")) {
                    if (i.hasClass("chosen-with-drop") && ("down" === t || "up" === t)) return;
                    i = i.prev("select.chosen")
                }
                var n = i.data("ctrlIndex"), r = i.closest("tr").data("row");
                "down" === t ? r < a - 1 ? r += 1 : r = 0 : "up" === t ? r > 0 ? r -= 1 : r = a - 1 : "left" === t ? n > 0 ? n -= 1 : n = o - 1 : "right" === t && (n < o - 1 ? n += 1 : n = 0), s(e.find('tr[data-row="' + r + '"]').find('[data-ctrl-index="' + n + '"]'))
            }
        }, l = {options: i, focusNext: r, focusControl: s}, h = e.find("tbody,.batch-rows"), c = function (e) {
            t.fn.chosen && i.chosen && e.find(".chosen").chosen(t.isPlainObject(i.chosen) ? i.chosen : null), t.fn.datetimepicker && i.datetimepicker && e.datepickerAll(t.isPlainObject(i.datetimepicker) ? i.datetimepicker : null), t.fn.colorPicker && i.colorPicker && e.find("input.colorpicker").colorPicker(t.isPlainObject(i.colorPicker) ? i.colorPicker : null);
            var n = 0;
            e.find('input[type!="hidden"],textarea,select').each(function () {
                var e = t(this);
                e.parent().hasClass("chosen-search") || e.attr("data-ctrl-index", n++)
            }), o = Math.max(o, n)
        };
        if (n.length) {
            var d = n.remove().html(), u = function (e, o) {
                var s = d;
                "number" != typeof e && (e = a), a = Math.max(e + 1, a), s = s.replace(/\$idPlus/g, e + 1).replace(/\$id/g, e);
                var r = t("<" + n[0].tagName.toLowerCase() + " />").html(s);
                return r.attr("data-row", e).addClass(n.attr("class")).removeClass("template"), i.rowCreator && i.rowCreator(r, e, i), o ? o.after(r) : h.append(r), c(r), r
            };
            t.extend(l, {createRow: u, template: d});
            for (var f = i.idStart; f <= i.idEnd; ++f) u(f)
        } else c(e);
        e.on("click", ".btn-copy", function () {
            var e = t(this), i = t(e.data("copyFrom")).val(), n = t(e.data("copyTo")).val(i).addClass("highlight");
            setTimeout(function () {
                n.removeClass("highlight")
            }, 2e3)
        }), i.hotkeys && t(document).on("keydown", function (t) {
            var e = {
                "Ctrl+#37": "left",
                "Ctrl+#39": "right",
                "#38": "up",
                "#40": "down",
                "Ctrl+#38": "up",
                "Ctrl+#40": "down"
            }, i = [];
            t.ctrlKey && i.push("Ctrl"), i.push("#" + t.keyCode);
            var n = e[i.join("+")];
            n && (r(n), t.ctrlKey && (t.stopPropagation(), t.preventDefault()))
        }), e.data("zui.batchActionForm", l), setTimeout(t.fixTableResponsive, 0)
    };
    t.fn.batchActionForm = function (i) {
        return this.each(function () {
            e(t(this), i)
        })
    }
}(jQuery), function (t, e) {
    "use strict";
    var i = "zui.table", n = {
            zh_cn: {selectedItems: "已选择 <strong>{0}</strong> 项", attrTotal: "{0}总计 <strong>{1}</strong>"},
            zh_tw: {selectedItems: "已选择 <strong>{0}</strong> 项", attrTotal: "{0}总计 <strong>{1}</strong>"},
            en: {selectedItems: "Seleted <strong>{0}</strong> items", attrTotal: "{0} total <strong>{1}</strong>"},
            de: {selectedItems: "<strong>{0}</strong> ausgewählt", attrTotal: "{0} insgesamt <strong>{1}</strong>"},
            fr: {selectedItems: "<strong>{0}</strong> sélectionnés", attrTotal: "{0} total <strong>{1}</strong>"}
        }, o = /^((?!chrome|android).)*safari/i.test(navigator.userAgent), a = t.zui.browser.isIE(), s = a ? 200 : 100,
        r = function (e, o) {
            var a = this;
            a.name = i;
            var s = a.$ = t(e);
            o = a.options = t.extend({}, r.DEFAULTS, this.$.data(), o), a.langName = o.lang || t.zui.clientLang(), a.lang = t.zui.getLangData(i, a.langName, n), a.id = s.attr("id"), a.id || (a.id = o.id || "table-" + t.zui.uuid(), a.noID = !0, s.attr("id", a.id), o.hot && console.warn("ZUI: table hot replace id not defined, the element id attribute should be set.")), s.attr("data-ride") || s.attr("data-ride", "table");
            var l = a.getTable();
            if (l.length) {
                l.find("thead>tr>th").each(function () {
                    var e = t(this);
                    if (!e.attr("title")) {
                        var i = t.trim(e.find("a:first").text() || e.text() || "");
                        i.length && e.attr("title", i)
                    }
                }), o.nested && a.initNestedList(), o.checkable && (s.on("click", ".check-all", function () {
                    a.checkAll(!t(this).hasClass("checked"))
                }), o.checkOnClickRow && s.on("click", "tbody>tr", function (e) {
                    t(e.target).closest('.btn,a,.not-check,.form-control,input[type="text"],.chosen-container').length || a.checkRow(t(this))
                }), s.on("click", 'tbody input[type="checkbox"],tbody label[for]', function (e) {
                    e.stopPropagation();
                    var i = t(this);
                    i.is("label") && (i = i.closest(".checkbox-primary").find('input[type="checkbox"]')), a.checkRow(i.closest("tr"), i.is(":checked"))
                }), o.selectable && s.selectable(t.extend({}, {
                    selector: a.isDataTable ? ".fixed-left tbody>tr" : "tbody>tr",
                    selectClass: "",
                    trigger: ".c-id",
                    clickBehavior: "multi",
                    listenClick: !1,
                    start: function () {
                        this.syncSelectionsFromClass()
                    },
                    select: function (e) {
                        a.checkRow(e.target, !0), t.cookie("ajax_dragSelected") || (t.cookie("ajax_dragSelected", "on", {
                            expires: config.cookieLife,
                            path: config.webRoot
                        }), t.ajaxSendScore("dragSelected"))
                    },
                    unselect: function (t) {
                        a.checkRow(t.target, !1)
                    },
                    rangeStyle: {
                        border: "1px solid #006af1",
                        backgroundColor: "rgba(50,128,252,0.2)",
                        borderRadius: "2px"
                    }
                }, t.isPlainObject(o.selectable) ? o.selectable : null)));
                var h = a.$form = s.is("form") ? s : s.find("form");
                h.length && (o.ajaxForm ? h.ajaxForm(t.isPlainObject(o.ajaxForm) ? o.ajaxForm : null) : h.on("click", "[data-form-action]", function () {
                    h.attr("action", t(this).data("formAction")).submit()
                })), (o.fixFooter || o.fixHeader) && (a.pageFooterHeight = t("#footer").outerHeight(), a.updateFixUI(!0), t(window).on("scroll resize", function () {
                    a.updateFixUI()
                }).on("sidebar.toggle", function () {
                    setTimeout(function () {
                        a.updateFixUI()
                    }, 200)
                })), o.group && (s.on("click", ".group-toggle", function () {
                    a.toggleRowGroup(t(this).closest("tr").data("id"))
                }), t(document).on("click", ".group-collapse-all", function () {
                    a.toggleGroups(!1)
                }).on("click", ".group-expand-all", function () {
                    a.toggleGroups(!0)
                })), a.defaultStatistic = s.find(".table-statistic").html(), a.updateStatistic(), a.initModals(), a.checkItems = {}, a.updateCheckUI()
            }
        };
    r.prototype.initNestedList = function () {
        var e = this, i = e.options, n = e.getTable().addClass("table-nested"), o = n.find("tbody"),
            a = o.children("tr[data-id]"), s = {}, r = [], l = i.preserveNested, h = i.enableEmptyNestedRow;
        n.toggleClass("disable-empty-nest-row", !h), a.each(function (e) {
            var i = t(this), n = i.data();
            n.realOrder = e, n.$row = i, n.nestPath ? (n.nestPathList = n.nestPath.split(","), n.nestPathList[0] || n.nestPathList.shift(), n.nestPathList[n.nestPathList.length - 1] || n.nestPathList.pop(), n.nestPathLevel = n.nestPathList.length, i.attr("data-level", n.nestPathLevel)) : (n.nestPath = "," + n.id + ",", n.nestPathList = [n.id], n.nestPathLevel = 1), s[n.id] = n, delete n.children, r.push(n)
        });
        var c = [];
        t.each(r, function (t, n) {
            return n.nestParent && (n.parent = s[n.nestParent], n.parent) ? (n.parent.children ? n.parent.children.push(n) : n.parent.children = [n], void (i.expandNestChild || e._initedNestedList || (n.$row.addClass("table-nest-hide"), n.parent.$row.addClass("table-nest-child-hide")))) : (n.$row.removeClass("table-nest-hide"), void c.push(n))
        }), e.nestRowsMap = s, e.$nestBody = o;
        var d = function (e) {
            for (var n = 0; n < e.length; ++n) {
                var a = e[n];
                o.append(a.$row), a.children ? (a.$row.addClass("has-nest-child"), d(a.children), a.nestPathLevel < 2 && a.$row.addClass("is-top-level")) : a.parent ? a.$row.addClass("is-nest-child") : h || a.$row.addClass("no-nest");
                var s = a.$row.find(".table-nest-title");
                s.length || (s = a.$row.find("td:first"));
                var r = s.find(".table-nest-icon");
                r.length || (r = t('<span class="table-nest-icon icon"></span>').prependTo(s)), r.toggleClass("table-nest-toggle", !!a.children || h && a.nested).css("marginLeft", (a.nestPathLevel - 1) * i.nestLevelIndent), delete a.$row
            }
        };
        if (d(c), e.nestConfigStoreName = "/" + window.config.currentModule + "/" + window.config.currentMethod + "/table." + e.id + ".nestConfig", e.nestConfig = l ? t.zui.store.get(e.nestConfigStoreName, {}) : {}, l) for (var u = 0; u < r.length; ++u) {
            var f = r[u];
            if (f.children) {
                var p = !!e.nestConfig[f.id];
                e.toggleNestedRows(f.id, p, !1, null, !0, !0)
            }
        }
        e.updateNestState(), e._initedNestedList || (o.on("click", ".table-nest-toggle", function () {
            e.toggleNestedRows(t(this).closest("tr").data("id"))
        }), o.on("mouseenter", "tr.has-nest-child", function () {
            o.find(".table-nest-hover").removeClass("table-nest-hover"), o.find(".table-nest-child-hover").removeClass("table-nest-child-hover");
            var e = t(this).addClass("table-nest-hover"), i = e.closest("tr").data("id");
            o.find('[data-nest-path*=",' + i + ',"]').addClass("table-nest-child-hover")
        }).on("mouseleave", "tr.table-nest-hover", function () {
            o.find(".table-nest-hover").removeClass("table-nest-hover"), o.find(".table-nest-child-hover").removeClass("table-nest-child-hover")
        }), e.$.on("click", ".table-nest-toggle-global", function () {
            for (var t = e.$.hasClass("table-nest-collapsed"), n = 0; n < c.length; ++n) {
                var o = c[n];
                (o.children || o.nested && i.enableEmptyNestedRow) && e.toggleNestedRows(o.id, t, !0, null, !1, !0)
            }
            e.updateNestState()
        }), e._initedNestedList = !0)
    }, r.prototype.toggleNestedRows = function (i, n, o, a, s, r) {
        var l = this, h = l.nestRowsMap[i], c = l.$nestBody.find('[data-id="' + i + '"]'),
            d = c.hasClass("table-nest-child-hide");
        n === e && (n = d);
        var u = "boolean" == typeof a;
        u && !o || c.toggleClass("table-nest-child-hide", !n), u ? (c.toggleClass("table-nest-hide", !n || !a), a = !c.hasClass("table-nest-child-hide")) : a = n, n ? l.nestConfig[i] = !0 : l.nestConfig[i] && delete l.nestConfig[i];
        var f = h.children;
        if (f) for (var p = 0; p < f.length; ++p) {
            var g = f[p];
            l.toggleNestedRows(g.id, n, o, a, !0, !0)
        }
        !l.options.preserveNested || s || u || t.zui.store.set(l.nestConfigStoreName, l.nestConfig), r || l.updateNestState()
    }, r.prototype.updateNestState = function () {
        var e = this, i = !e.$nestBody.find("tr.is-top-level.has-nest-child:not(.table-nest-child-hide)").length;
        e.$.toggleClass("table-nest-collapsed", i), e.$.find(".table-nest-toggle-global").each(function () {
            var e = t(this);
            e.attr("title", e.data(i ? "expandText" : "collapseText"))
        }), e.$.trigger("tableNestStateChanged")
    }, r.prototype.reload = function (e) {
        var i = this, n = i.options, o = n.replaceId;
        if (!o) return e && e();
        var a = i.id;
        if ("self" === o) {
            if (i.noID) return;
            o = a
        }
        var s = t("<div></div>");
        i.$.addClass("load-indicator loading"), s.load(window.location.href + " #" + o, function (r) {
            if (a === o) i.$.empty().html(s.children().html()), i.$.find('[data-ride="pager"]').pager(); else {
                i.$.find("#" + o).empty().html(s.children().html());
                try {
                    var l = t(r), h = l.find("#" + o).closest('[data-ride="table"],#' + a);
                    if (h.length) {
                        var c = h.find(".table-statistic");
                        c.length && (i.defaultStatistic = c.html());
                        var d = i.$.find('[data-ride="pager"]').data("zui.pager"), u = h.find('[data-ride="pager"]');
                        d && u.length && d.set(u.data())
                    }
                } catch (f) {
                    console.error(f)
                }
            }
            i.$.removeClass("load-indicator loading").trigger("beforeTableReload"), delete i.defaultStatistic, i.updateStatistic(), i.initModals(), i.$.datepickerAll();
            var p = i.$.find("tbody>tr"), g = !1;
            t.each(i.checkItems, function (t, e) {
                e && (i.checkRow(p.filter('[data-id="' + t + '"]'), !0, !0), g = !0)
            }), g && i.updateCheckUI(), i.$.trigger("tableReload");
            var m = t("#mainMenu>.btn-toolbar>.btn-active-text>.label");
            if (m.length) {
                var u = i.$.find(".pager[data-rec-total]"),
                    v = u.length ? u.attr("data-rec-total") : i.getTable().find("tbody:first>tr:not(.table-children)").length;
                m.text(v)
            }
            e && e(), n.afterReload && n.afterReload()
        })
    }, r.prototype.initModals = function () {
        var e = this, i = e.options, n = e.$.find(i.iframeModalTrigger);
        if (n.length) {
            var o = {
                type: "iframe", onHide: i.replaceId ? function () {
                    var n = t.cookie("selfClose");
                    (1 == n || i.hot) && (t("#triggerModal").data("cancel-reload", 1), e.reload(function () {
                        t.cookie("selfClose", 0)
                    }))
                } : null
            };
            n.modalTrigger(o)
        }
    }, r.prototype.getTable = function () {
        var t = this.$;
        if (this.isDataTable) return t.find("div.datatable");
        var e = t.is("table") ? t : t.find("table:not(.fixed-header-copy)").first();
        return e.is(".datatable") && (this.isDataTable = !0, e.data("zui.datatable") || window.initDatatable(e), e = t.find("div.datatable")), e
    }, r.prototype.toggleGroups = function (e) {
        var i = this, n = {};
        i.$.find("tbody>tr").each(function () {
            var o = t(this).closest("tr").data("id");
            n[o] || i.toggleRowGroup(o, e)
        })
    }, r.prototype.toggleRowGroup = function (i, n) {
        var o = this.$.find('tbody>tr[data-id="' + i + '"]'), a = o.filter(".group-summary"),
            s = n === e ? !a.hasClass("hidden") : !!n;
        o.not(".group-summary").toggleClass("hidden", !s), a.toggleClass("hidden", s), t("body").toggleClass("table-group-collapsed", !this.$.find("tbody>tr.group-summary.hidden").length)
    }, r.prototype.updateStatistic = function () {
        var i = this, n = i.$.find(".table-statistic");
        if (n.length) {
            if (i.defaultStatistic === e && (i.defaultStatistic = n.html()), i.options.statisticCreator) return void n.html(i.options.statisticCreator(i) || i.defaultStatistic);
            var o = i.statisticCols;
            if (!o && o !== !1) {
                o = {};
                var a = !1;
                i.getTable().find("thead th").each(function (e) {
                    var i = t(this), n = i.data("statistic");
                    n && (a = !0, o[e] = {format: n, name: i.text()})
                }), i.statisticCols = !!a && o
            }
            var s = 0;
            o && t.each(o, function (t) {
                o[t].total = 0, o[t].checkedTotal = 0
            }), i.$.find(i.isDataTable ? ".fixed-left tbody>tr" : "tbody>tr").each(function () {
                var e = t(this), i = e.hasClass("checked"), n = e.children("td");
                i && s++, o && t.each(o, function (t) {
                    var e = parseFloat(n.eq(t).text());
                    isNaN(e) && (e = 0), o[t].total += e, i && (o[t].checkedTotal += e)
                })
            });
            var r = [];
            if (s) r.push(i.lang.selectedItems.format(s)); else if (i.defaultStatistic) return void n.html(i.defaultStatistic);
            o && t.each(o, function (t) {
                var e = o[t], n = e[s ? "checkedTotal" : "total"];
                e.format && (n = e.format.format(n)), r.push(i.lang.attrTotal.format(e.name, n))
            }), n.html(r.join(", "))
        }
    }, r.prototype.updateFixUI = function (e) {
        var i = this, n = (new Date).getTime();
        if (!e && (i.lastUpdateCall && clearTimeout(i.lastUpdateCall), !i.lastUpdateTime || n - i.lastUpdateTime < s)) return void (i.lastUpdateCall = setTimeout(function () {
            i.updateFixUI(!0)
        }, s / 2));
        if (i.lastUpdateTime = n, i.lastUpdateCall && (clearTimeout(i.lastUpdateCall), i.lastUpdateCall = null), o) {
            var a = i.getTable();
            if (a.parent().is(".table-responsive")) {
                var r = a.find("thead"), l = 0;
                r.find("th").each(function () {
                    l += t(this).outerWidth()
                }), a.css("min-width", l)
            }
        }
        i.options.fixHeader && !i.isDataTable && i.fixHeader(), i.options.fixFooter && i.fixFooter()
    }, r.prototype.fixHeader = function () {
        var e = this, i = e.getTable(), n = i.find("thead"), o = n[0].getBoundingClientRect(), s = e.options.fixFooter,
            r = t.isFunction(s) ? s(o, n) : o.top < ("number" == typeof s ? s : -5),
            l = e.$.find(".fix-table-copy-wrapper"), h = i.parent(), c = h.is(".table-responsive");
        if (r) {
            if (l.length || (l = t('<div class="fix-table-copy-wrapper" style="position:fixed; z-index: 3; top: 0;"></div>').append(t('<table class="fixed-header-copy"></table>').addClass(i.attr("class")).append(n.clone())).insertAfter(i)), c) {
                var d = h[0].getBoundingClientRect();
                l.css({
                    left: d.left,
                    width: h.width(),
                    overflow: "hidden"
                }), l.find(".fixed-header-copy").css({
                    left: o.left - d.left,
                    position: "relative",
                    minWidth: i.width()
                }), a || h.data("fixHeaderScroll") || (h.data("fixHeaderScroll", 1), i.width() > h.width() && h.on("scroll", function () {
                    e.fixHeader()
                }))
            } else l.css({left: o.left, width: o.width});
            var u = l.find("th");
            n.find("th").each(function (e) {
                u.eq(e).css("width", t(this).outerWidth())
            })
        } else l.remove()
    }, r.prototype.fixFooter = function () {
        var e, i = this, n = i.getTable(), o = i.$.find(".table-footer");
        if (i.isDataTable) e = n[0].getBoundingClientRect(); else {
            var a = n.find("tbody");
            if (!a.length) return;
            e = a[0].getBoundingClientRect()
        }
        var s = i.options.fixFooter;
        o.toggleClass("fixed-footer", !!r);
        var r = "function" == typeof s ? s(e, o) : e.bottom > window.innerHeight - 50 - ("number" == typeof s ? s : i.pageFooterHeight || 5);
        o.toggleClass("fixed-footer", !!r), n.toggleClass("with-footer-fixed", !!r), n.trigger("fixFooter", r);
        var l = t("body"), h = l.hasClass("body-modal");
        if (r) {
            var c = n.parent(), d = c.is(".table-responsive");
            o.css({
                bottom: i.pageFooterHeight || 0,
                left: d ? c[0].getBoundingClientRect().left : e.left,
                width: d ? c.width() : e.width
            }), h && l.css("padding-bottom", 40)
        } else o.css({width: "", left: 0, bottom: 0}), h && l.css("padding-bottom", 0)
    }, r.prototype.checkAll = function (e) {
        var i = this, n = i.$.find(i.isDataTable ? ".fixed-left tbody>tr" : "tbody>tr");
        n.each(function () {
            i.checkRow(t(this), e, !0)
        }), i.updateCheckUI()
    }, r.prototype.checkRow = function (t, i, n) {
        var o = this;
        o.isDataTable && !t.is(".datatable-row-left") && (t = o.getTable().find('.datatable-row-left[data-index="' + t.data("index") + '"]'));
        var a = t.find('input[type="checkbox"]');
        a.length && !a.is(":disabled") && (i === e && (i = !a.is(":checked")), o.isDataTable ? o.getTable().find('.datatable-row[data-index="' + t.data("index") + '"]').toggleClass("checked", i) : t.toggleClass("checked", i), this.checkItems[t.data("id")] = i, a.prop("checked", i).trigger("change"), n || o.updateCheckUI())
    }, r.prototype.updateCheckUI = function () {
        var e = this, i = e.getTable(),
            n = i.find(e.isDataTable ? ".fixed-left tbody>tr" : "tbody>tr").not(".group-summary"), o = !1, a = null,
            s = 0, r = !1, l = n.length;
        n.each(function (n) {
            var h = t(this), c = h.find('input[type="checkbox"]');
            if (!c.length) return void l--;
            r = c.is(":checked");
            var d = e.isDataTable ? i.find('.datatable-row[data-index="' + h.data("index") + '"]') : h;
            d.toggleClass("checked", r), d.toggleClass("row-check-begin", r && !o), a && a.toggleClass("row-check-end", !r && o), r && (s += 1), a = d, o = r, l === n + 1 && d.toggleClass("row-check-end", r)
        }), e.$.toggleClass("has-row-checked", s > 0).find(".check-all").toggleClass("checked", !(!l || s !== l)), e.updateStatistic(), e.options.onCheckChange && e.options.onCheckChange(), i.trigger("checkChange")
    }, r.DEFAULTS = {
        checkable: !0,
        checkOnClickRow: !0,
        ajaxForm: !1,
        selectable: !0,
        fixHeader: !a,
        fixFooter: !a,
        iframeWidth: 900,
        replaceId: "self",
        nestLevelIndent: 18,
        nested: !1,
        preserveNested: !0,
        hot: !1,
        iframeModalTrigger: ".iframe"
    }, t.fn.table = function (e) {
        return this.each(function () {
            var n = t(this), o = n.data(i), a = "object" == typeof e && e;
            o || n.data(i, o = new r(this, a)), "string" == typeof e && o[e]()
        })
    }, r.NAME = i, t.fn.table.Constructor = r, t(function () {
        t('[data-ride="table"]').table()
    })
}(jQuery, void 0), function (t, e, i) {
    t.fn._ajaxForm = t.fn.ajaxForm;
    var n = {timeout: e.config ? e.config.timeout : 0, dataType: "json", method: "post"}, o = "";
    t.fn.enableForm = function (e, n, o) {
        return e === i && (e = !0), this.each(function () {
            var i = t(this);
            n || i.find('[type="submit"]').attr("disabled", e ? null : "disabled"), !o && i.hasClass("load-indicator") && i.toggleClass("loading", !e), i.toggleClass("form-disabled", !e)
        })
    }, t.enableForm = function (e, i, n, o) {
        "string" == typeof e || e instanceof t ? e = t(e) : (o = n, n = i, i = e, e = t("form")), e.enableForm(i !== !1, n, o)
    }, t.disableForm = function (e, i, n) {
        t.enableForm(e, !1, i, n)
    };
    var a = function (e, i, n) {
        "string" == typeof i && (n = i, i = null), n = n || "show", t.zui.messager ? t.zui.messager[n](e, i) : alert(e)
    };
    t.ajaxForm = function (s, r) {
        var l = t(s);
        if (l.length > 1) return l.each(function () {
            t.ajaxForm(this, r)
        });
        "function" == typeof r && (r = {complete: r}), r = t.extend({}, n, l.data(), r);
        var h = r.beforeSubmit, c = r.error, d = r.success, u = r.finish;
        delete r.finish, delete r.success, delete r.onError, delete r.beforeSubmit, r = t.extend({
            beforeSubmit: function (n, a, s) {
                if (l.removeClass("form-watched").enableForm(!1), (h && h(n, a, s)) !== !1) {
                    var r = {}, c = a.find('[type="file"]');
                    r.fileapi = c.length && c[0].files !== i, r.formdata = e.FormData !== i;
                    var d = r.fileapi && a.find('input[type="file"]:enabled').filter(function () {
                            return "" !== t(this).val()
                        }), u = d.length, f = "multipart/form-data", p = a.attr("enctype") == f || a.attr("encoding") == f,
                        g = r.fileapi && r.formdata, m = u && !g || p && !r.formdata;
                    m && ("" == o && (o = s.url), s.url != o && (s.url = o), s.url = s.url.indexOf("&") >= 0 ? s.url + "&HTTP_X_REQUESTED_WITH=XMLHttpRequest" : s.url + "?HTTP_X_REQUESTED_WITH=XMLHttpRequest")
                }
            }, success: function (i, n, o) {
                if ((d && d(i, n, o, l)) !== !1) {
                    try {
                        "string" == typeof i && (i = JSON.parse(i))
                    } catch (s) {
                    }
                    if (null === i || "object" != typeof i) return i ? alert(i) : a("No response.", "danger");
                    var h = r.responser ? t(r.responser) : l.find(".form-responser");
                    h.length || (h = t("#responser"));
                    var c = i.message, f = function () {
                        var n = i.callback;
                        if (n) {
                            var o = n.indexOf("("), a = (o > 0 ? n.substr(0, o) : n).split("."), s = e, r = a[0];
                            a.length > 1 && (r = a[1], "top" === a[0] ? s = e.top : "parent" === a[0] && (s = e.parent));
                            var h = s[r];
                            if ("function" == typeof h) {
                                var c = [];
                                return o > 0 && ")" == n[n.length - 1] && (c = t.parseJSON("[" + n.substring(o + 1, n.length - 1) + "]")), c.push(i), h.apply(l, c)
                            }
                        }
                    };
                    if ("success" === i.result) {
                        var p = r.locate || i.locate;
                        if (l.enableForm(!0, !!p), c) {
                            var g = l.find('[type="submit"]').first(), m = !1;
                            g.length && (g.popover({
                                container: "body",
                                trigger: "manual",
                                content: c,
                                tipClass: "popover-in-modal popover-success popover-form-result",
                                placement: i.placement || g.data("placement") || r.popoverPlacement || "right"
                            }).popover("show"), setTimeout(function () {
                                g.popover("destroy")
                            }, r.popoverTime || 2e3), m = !0), h.length && (h.html('<span class="small text-success">' + c + "</span>").show().delay(3e3).fadeOut(100), m = !0), m || a(c, "success")
                        }
                        if (u) return u(i, !0, l);
                        if ((r.closeModal || i.closeModal) && setTimeout(t.zui.closeModal, r.closeModalTime || 2e3), f() === !1) return;
                        if (p) if ("loadInModal" == p) {
                            var v = t(".modal");
                            setTimeout(function () {
                                v.load(v.attr("ref"), function () {
                                    t(this).find(".modal-dialog").css("width", t(this).data("width")), t.zui.ajustModalPosition()
                                })
                            }, 1e3)
                        } else "parent" === p || "top" === p ? e[p] && setTimeout(function () {
                            e[p].location.reload()
                        }, 1200) : "reload" === p ? setTimeout(function () {
                            e.location.href = e.location.href
                        }, 1200) : setTimeout(function () {
                            t.tabs ? t.tabs.open(p) : e.location.href = p
                        }, 1200);
                        var y = r.ajaxReload || i.ajaxReload;
                        if (y) {
                            var b = t(y);
                            b.length && b.load(e.location.href + " " + y, function () {
                                b.find('[data-toggle="modal"]').modalTrigger()
                            })
                        }
                    } else {
                        if (l.enableForm(), "string" == typeof c) h.length ? h.html('<span class="text-small text-red">' + c + "</span>").show().delay(3e3).fadeOut(100) : a(c, "danger"); else if ("object" == typeof c) {
                            var w = !1, x = [];
                            t.each(c, function (e, i) {
                                var n = t.isArray(i) ? i.join("") : i, o = t("#" + e);
                                if (!o.length) return void x.push(n);
                                var a = e + "Label", s = t("#" + a);
                                if (!s.length) {
                                    var r = o.closest(".input-group").length, l = o.closest("td").length;
                                    s = t('<div id="' + a + '" class="text-danger help-text" />').appendTo(l ? o.closest("td") : r ? o.closest(".input-group").parent() : o.parent())
                                }
                                s.empty().append(n), o.addClass("has-error");
                                var h = function () {
                                    var e = t("#" + a);
                                    if (e.length) return e.remove(), o.removeClass("has-error"), !0
                                };
                                o.on("change input mousedown", h);
                                var c = t("#" + e + "_chosen");
                                if (c.length && c.find(".chosen-single,.chosen-choices").addClass("has-error").on("mousedown", function () {
                                    h() === !0 && t(this).removeClass("has-error")
                                }), !w) {
                                    if (o.hasClass("chosen")) o.trigger("chosen:activate"); else if (o.is("textarea") && o.data("keditor")) {
                                        var d = o.data("keditor");
                                        d.focus(), d.edit.doc.body.focus()
                                    } else o.focus();
                                    w = !0
                                }
                            }), x.length && a(x.join(";"), "danger")
                        }
                        if (u) return u(i, !1, l);
                        if (f() === !1) return
                    }
                }
            }, error: function (t, i, n) {
                if ((c && c(t, i, n, l)) !== !1) {
                    l.enableForm();
                    var o = "timeout" == i || "error" == i ? e.lang ? e.lang.timeout : i : t.responseText + i + n;
                    a(o, "danger")
                }
            }
        }, r), l._ajaxForm(r).data("zui.ajaxform", !0), l.on("click", "[data-form-action]", function () {
            l.attr("action", t(this).data("formAction")).submit()
        })
    }, t.setAjaxForm = function (e, i, n) {
        t.ajaxForm(e, t.isPlainObject(i) ? i : {onFinish: i, beforeSubmit: n})
    }, t.fn.ajaxForm = function (e) {
        return this.each(function () {
            t.ajaxForm(this, e)
        })
    }, t.fn.setInputRequired = function () {
        return this.each(function () {
            var e = t(this), i = e.parent();
            i.is(".input-control,td") ? i.addClass("required") : e.is(".chosen") ? e.attr("required", null).next(".chosen-container").addClass("required") : i.addClass("required"), e.attr("required", null);
            var n = i.closest(".input-group");
            n.length && 1 === n.find(".required,input[required],select[required]").length && n.addClass("required")
        })
    }, t(function () {
        t('.form-ajax,form[data-type="ajax"]').ajaxForm(), setTimeout(function () {
            var i = e.config.requiredFields, n = t("form");
            i && (i = i.split(",")), i && i.length && t.each(i, function (t, e) {
                n.find("#" + e).attr("required", "required")
            }), n.find("input[required],select[required],textarea[required]").setInputRequired()
        }, 400), t('form[target="hiddenwin"]').on("submit", function () {
            var e = t(this);
            e.data("zui.ajaxform") || e.enableForm(!1).data("disabledTime", (new Date).getTime())
        }).on("click", function () {
            var e = t(this), i = e.data("disabledTime");
            i && (new Date).getTime() - i > 1e4 && e.enableForm(!0).data("disabledTime", null)
        })
    })
}(jQuery, window, void 0), function (t) {
    "use strict";
    var e = "zui.searchList", i = function (t, e) {
        if (t && t.length) for (var i = 0; i < t.length; ++i) if (e.indexOf(t[i]) < 0) return !1;
        return !0
    }, n = function (i, o) {
        var a = this;
        a.name = e;
        var s = a.$ = t(i);
        o = a.options = t.extend({}, n.DEFAULTS, this.$.data(), o);
        var r = s.find(o.searchBox);
        r.length && (r.searchBox({
            onSearchChange: function (t) {
                a.search(t)
            }, onKeyDown: function (t) {
                var e = t.which;
                if (13 === e) {
                    var i = a.getActiveItem();
                    i.length && (o.onSelectItem ? o.onSelectItem(i) : window.location.href = i.attr("href")), t.preventDefault()
                } else if (38 === e) {
                    var i = a.getActiveItem();
                    i.removeClass("active");
                    for (var n = i.prev(); n.length && !n.is(".search-list-item:not(.hidden)");) n = n.prev();
                    n.length || (n = a.getItems().not(".hidden").last()), a.scrollTo(n.addClass("active")), t.preventDefault()
                } else if (40 === e) {
                    var i = a.getActiveItem();
                    i.removeClass("active");
                    for (var s = i.next(); s.length && !s.is(".search-list-item:not(.hidden)");) s = s.next();
                    s.length || (s = a.getItems().not(".hidden").first()), a.scrollTo(s.addClass("active")), t.preventDefault()
                }
            }, onFocus: function () {
                s.addClass("searchbox-focus")
            }, onBlur: function () {
                s.removeClass("searchbox-focus")
            }
        }), a.searchBox = r.data("zui.searchBox"), a.search(a.searchBox.getSearch()));
        var l = a.$menu = s.closest(".dropdown-menu");
        if (l.length) {
            a.isDropdown = !0, s.on("click", function (e) {
                t(e.target).closest(o.selector + ",[data-toggle]").length || e.stopPropagation()
            });
            var h = l.parent();
            h.on(h.hasClass("dropdown-hover") ? "mouseenter" : "shown.zui.dropdown", function () {
                a.tryLoadRemote(function () {
                    if (h.hasClass("dropup")) {
                        var t = null, e = h[0].getBoundingClientRect();
                        e.top < 220 && (t = Math.floor(e.top) - 57), l.find(".list-group").css("max-height", t)
                    }
                    setTimeout(function () {
                        a.searchBox && a.searchBox.focus()
                    }, 50)
                })
            })
        }
        s.on("mouseenter", o.selector, function () {
            s.find(a.options.selector).not(".hidden").removeClass("active"), t(this).addClass("active")
        })
    };
    n.prototype.tryLoadRemote = function (t) {
        var e = this, i = e.options;
        i.url || i.ajax ? e.isLoaded ? t() : e.loadRemote(t) : t()
    }, n.prototype.loadRemote = function (e) {
        var i = this, n = i.options;
        i.$menu.addClass("load-indicator loading").find(".list-group").remove(), i.isLoaded = !1, t.ajax(t.extend({
            url: n.url,
            type: "GET",
            dataType: "html",
            success: function (n, o, a) {
                var s = t(n);
                s.hasClass("list-group") || (s = t('<div class="list-group"></div>').append(s)), i.$menu.append(s), i.$menu.removeClass("loading"), i.isLoaded = !0, e && e(!0)
            },
            error: function () {
                i.$menu.removeClass("loading").append('<div class="list-group"><div class="text-error has-padding">' + (n.errorText || window.lang && window.lang.timeout) + "</div></div>"), e && e(!1)
            }
        }, n.ajax))
    }, n.prototype.scrollTo = function (t) {
        t.length && t[0].scrollIntoViewIfNeeded && t[0].scrollIntoViewIfNeeded({behavior: "smooth"})
    }, n.prototype.getItems = function () {
        return this.$.find(this.options.selector).addClass("search-list-item")
    }, n.prototype.getActiveItem = function () {
        return this.getItems().filter(".active:first")
    }, n.prototype.search = function (e) {
        var n = this, o = void 0 === e || null === e || "" === e;
        n.$.toggleClass("has-search-text", !o);
        var a = n.getItems().removeClass("active");
        if (o) a.removeClass("hidden"); else {
            var s = t.trim(e).split(" ");
            a.each(function () {
                var e = t(this), n = e.text() + " " + (e.data("key") || e.data("filter"));
                e.toggleClass("hidden", !i(s, n))
            })
        }
        n.scrollTo(a.not(".hidden").first().addClass("active"))
    }, n.DEFAULTS = {
        selector: ".list-group a:not(.not-list-item)",
        searchBox: ".search-box",
        onSelectItem: null
    }, t.fn.searchList = function (i) {
        return this.each(function () {
            var o = t(this), a = o.data(e), s = "object" == typeof i && i;
            a || o.data(e, a = new n(this, s)), "string" == typeof i && a[i]()
        })
    }, n.NAME = e, t.fn.searchList.Constructor = n, t(function () {
        t('[data-ride="searchList"]').searchList()
    })
}(jQuery), function (t) {
    "use strict";
    var e = "zui.labelSelector", i = function (n, o) {
        var a = this;
        a.name = e, a.$ = t(n), o = a.options = t.extend({}, i.DEFAULTS, this.$.data(), o), a.$.hide(), a.update()
    };
    i.prototype.select = function (t) {
        t += "", this.$wrapper.find(".label.active").removeClass("active"), this.$wrapper.find('.label[data-value="' + t + '"]').addClass("active"), this.$.val(t).trigger("change")
    }, i.prototype.update = function () {
        var e = this, i = e.options, n = e.$wrapper;
        if (!n) {
            if (i.wrapper) n = t(i.wrapper); else {
                var o = e.$.next();
                n = o.hasClass(".label-selector") ? o : t('<div class="label-selector"></div>')
            }
            n.parent().length || e.$.after(n), e.$wrapper = n, n.on("click", ".label", function (i) {
                var n = e.$.val(), o = t(this).data("value");
                e.hasEmptyValue !== !1 && o == n && (o = e.hasEmptyValue), e.select(o), i.preventDefault()
            })
        }
        n.empty();
        var a = e.$.val();
        e.hasEmptyValue = !1, e.$.children("option").each(function () {
            var e = t(this), o = {label: e.text(), value: e.val()}, s = "" === o.value || "0" === o.value,
                r = t(i.labelTemplate || '<span class="label"></span>');
            i.labelClass && !s && r.addClass(i.labelClass), i.labelCreator ? r = i.labelCreator(r) : (r.data("option", o).attr("data-value", o.value), s && !o.label ? r.addClass("empty").append('<i class="icon icon-close"></i>') : r.text(o.label).toggleClass("active", a === o.value)), n.append(r)
        })
    }, i.DEFAULTS = {}, t.fn.labelSelector = function (n) {
        return this.each(function () {
            var o = t(this), a = o.data(e), s = "object" == typeof n && n;
            a || o.data(e, a = new i(this, s)), "string" == typeof n && a[n]()
        })
    }, i.NAME = e, t.fn.labelSelector.Constructor = i, t(function () {
        t('[data-provide="labelSelector"]').labelSelector()
    })
}(jQuery), function (t) {
    "use strict";
    var e = "zui.fileInput", i = t.BYTE_UNITS = {B: 1, KB: 1024, MB: 1048576, GB: 1073741824, TB: 1099511627776},
        n = t.formatBytes = function (t, e, n) {
            return void 0 === e && (e = 2), n || (n = t < i.KB ? "B" : t < i.MB ? "KB" : t < i.GB ? "MB" : t < i.TB ? "GB" : "TB"), (t / i[n]).toFixed(e) + n
        }, o = function (t) {
            if ("string" == typeof t) {
                t = t.toUpperCase();
                var e = t.replace(/\d+/, "");
                t = parseFloat(t.replace(e, "")), t *= i[e] || i[e + "B"], t = Math.floor(t)
            }
            return t
        }, a = function (i, s) {
            var r = this;
            r.name = e;
            var l = r.$ = t(i);
            s = r.options = t.extend({}, a.DEFAULTS, this.$.data(), s), s.fileMaxSize && "string" == typeof s.fileMaxSize && (s.fileMaxSize = o(s.fileMaxSize));
            var h = r.$input = l.find('input[type="file"]');
            l.on("click", ".file-input-btn", function () {
                h.trigger("click")
            }).on("click", ".file-input-rename", function () {
                r.oldName = l.addClass("edit").find(".file-editbox").focus().val(), t.fn.fixInputGroup && l.find(".input-group,.btn-group").fixInputGroup()
            }).on("click", ".file-input-delete", function () {
                h.val(""), r.update(), s.onDelete && s.onDelete(r)
            }).on("click", ".file-name-cancel", function () {
                l.removeClass("edit").find(".file-editbox").focus().val(r.oldName)
            }).on("click", ".file-name-confirm", function () {
                var e = l.find(".file-editbox"), i = t.trim(e.val());
                i.length ? l.removeClass("edit").find(".file-title").text(i).attr("title", i) : e.focus()
            }).on("change input paste", ".file-editbox", function () {
                var e = t(this);
                e.attr("size", Math.max(5, e.val().length))
            }), h.on("change", function () {
                var t = r.getFile();
                t && s.fileMaxSize && t.size > s.fileMaxSize && (h.val(""), (window.bootbox || window).alert(s.fileSizeError.format(n(s.fileMaxSize)))), r.update()
            }), r.update()
        };
    a.prototype.getFile = function () {
        var t = this.$input.prop("files");
        return t && t[0]
    }, a.prototype.update = function () {
        var t = this, e = t.$, i = t.getFile(), o = !i;
        e.toggleClass("normal", !o).toggleClass("empty", o), i ? (t.oldName = i.name, e.find(".file-title").text(i.name).attr("title", i.name), e.find(".file-size").text(n(i.size)), e.find(".file-editbox").val(i.name).attr("size", i.name.length), t.options.onSelect && t.options.onSelect(i, t)) : e.find(".file-editbox").val("")
    }, a.DEFAULTS = {fileMaxSize: 0, fileSizeError: "无法上传大于 {0} 的文件。"}, t.fn.fileInput = function (i) {
        return this.each(function () {
            var n = t(this), o = n.data(e), s = "object" == typeof i && i;
            o || n.data(e, o = new a(this, s)), "string" == typeof i && o[i]()
        })
    }, a.NAME = e, t.fn.fileInput.Constructor = a, t(function () {
        t('[data-provide="fileInput"]').fileInput()
    });
    var s = "zui.fileInputList", r = function (e, i) {
        var n = this;
        n.name = s;
        var o = n.$ = t(e);
        i = n.options = t.extend({}, r.DEFAULTS, this.$.data(), i), n.$template = o.find(".file-input").detach(), n.add()
    };
    r.prototype.add = function () {
        var t = this, e = t.options, i = t.$template.clone();
        "before" === e.appendWay ? t.$.prepend(i) : t.$.append(i), i.fileInput({
            fileMaxSize: e.eachFileMaxSize,
            fileSizeError: e.fileSizeError,
            onDelete: function (e) {
                e.$.remove(), t.options.onDelete && t.options.onDelete(e, t)
            },
            onSelect: function (e, i) {
                t.add(), t.options.onSelect && t.options.onSelect(e, i, t)
            }
        })
    }, r.DEFAULTS = {
        fileMaxSize: 0,
        eachFileMaxSize: 0,
        appendWay: "after",
        fileSizeError: "无法上传大于 {0} 的文件。"
    }, t.fn.fileInputList = function (e) {
        return this.each(function () {
            var i = t(this), n = i.data(s), o = "object" == typeof e && e;
            n || i.data(s, n = new r(this, o)), "string" == typeof e && n[e]()
        })
    }, r.NAME = s, t.fn.fileInputList.Constructor = r, t(function () {
        t('[data-provide="fileInputList"]').fileInputList()
    })
}(jQuery), function (t) {
    window.config || (window.config = {}), t.createLink = window.createLink = function (e, n, o, a, s, r, l) {
        if ("object" == typeof e) return t.createLink(e.moduleName, e.methodName, e.vars, e.viewType, e.isOnlyBody, e.hash, e.tid);
        if (t.tabSession && !l && (l = t.tabSession.getTid()), a || (a = config.defaultView), s || (s = !1), o) for ("string" == typeof o && (o = o.split("&")), i = 0; i < o.length; i++) if ("string" == typeof o[i]) {
            var h = o[i].split("=");
            o[i] = [h.shift(), h.join("=")]
        }
        var c, d = "GET" === config.requestType;
        if (d) {
            if (c = config.router + "?" + config.moduleVar + "=" + e + "&" + config.methodVar + "=" + n, o) for (i = 0; i < o.length; i++) c += "&" + o[i][0] + "=" + o[i][1];
            c += "&" + config.viewVar + "=" + a
        } else {
            if ("PATH_INFO" == config.requestType && (c = config.webRoot + e + config.requestFix + n), "PATH_INFO2" == config.requestType && (c = config.webRoot + "index.php/" + e + config.requestFix + n), o) for (i = 0; i < o.length; i++) c += config.requestFix + o[i][1];
            c += "." + a
        }
        if (void 0 !== config.onlybody && "yes" == config.onlybody || s) {
            var u = (d ? "&" : "?") + "onlybody=yes";
            c += u
        }
        return l && config.tabSession && (c = c + (!d && c.indexOf("?") < 0 ? "?" : "&") + "tid=" + l), c + (r ? ("#" === r[0] ? "" : "#") + r : "")
    }, t(function () {
        var e = t("#main,#mainContent,#mainRow,.auto-fade-in");
        e.length && e.hasClass("fade") && setTimeout(function () {
            e.addClass("in")
        }, e.data("fadeTime") || 200)
    }), t.ajaxSendScore = function (e) {
        t.get(t.createLink("score", "ajax", "method=" + e))
    };
    var e = function (t) {
        var e = 0;
        if (t) {
            var i = t.split(":");
            e += 60 * parseInt(i[0]), e += parseInt(i[1])
        }
        return e
    }, n = function (t) {
        t %= 1440;
        var e = Math.floor(t / 60), i = t % 60;
        return e < 10 && (e = "0" + e), i < 10 && (i = "0" + i), e + ":" + i
    }, o = function (t) {
        if ("string" == typeof t && (t = e(t)), "number" == typeof t) if (t < 1e5) {
            var i = new Date;
            i.setHours(Math.floor(t / 60) % 24), i.setMinutes(t % 60), t = i
        } else t = new Date(t);
        return t
    }, a = function (t, i) {
        for (var a = i ? o(i) : new Date, s = a.getHours(), r = 10 * Math.floor(a.getMinutes() / 10) + 10, l = 0; l < 24; ++l) {
            var h = (l + s) % 24;
            if (!(h < 5)) for (var c = 0; c < 6; ++c) {
                var d = n(60 * h + 10 * c + r);
                t.append('<option value="' + d + '">' + d + "</option>")
            }
        }
        t.val() || (time = e(a.format("hh:mm")), time = time - time % 10 + 10, t.val(n(time)))
    };
    t.fn.timeSpanControl = function (i) {
        return this.each(function () {
            var s = t(this), r = t.extend({}, i, s.data()), l = s.find('[name="begin"],.control-time-begin'),
                h = s.find('[name="end"],.control-time-end'), c = function () {
                    var t = l.val();
                    if (s.find(".hide-empty-begin").toggleClass("hide", !t), t) {
                        var i = n(e(t) + 30);
                        h.find('option[value="' + i + '"]').length && h.val(i), r.onChange && r.onChange(h, i)
                    }
                };
            if (s.data("timeSpanControlInit")) {
                if (r.begin) {
                    var d = o(r.begin).format("hh:mm");
                    l.find('option[value="' + d + '"]').length && l.val(d), r.onChange && r.onChange(l, d)
                }
                if (r.end) {
                    var u = o(r.end).format("hh:mm");
                    h.find('option[value="' + u + '"]').length && h.val(u), r.onChange && r.onChange(h, u)
                }
            } else l.on("change", c), a(l, r.begin), a(h, r.end), s.data("timeSpanControlInit", !0);
            r.end || c()
        })
    }, t.timeSpanControl = {convertTimeToNum: e, convertNumToTime: n, initTimeSelect: a, createTime: o};
    var s = t.setSearchType = function (e, i) {
        var n = t("#searchType");
        e || (e = n.val()), e = e || "bug", n.val(e);
        var o = t("#searchTypeMenu");
        o.find("li.selected").removeClass("selected");
        var a = o.find('a[data-value="' + e + '"]'), s = a.text();
        a.parent().addClass("selected"), t("#searchTypeName").text(s), i || t("#searchInput").focus()
    };
    t.gotoObject = function (e, i) {
        if (e || (e = t("#searchType").val()), i || (i = t("#searchInput").val()), i && e) if (i = i.replace(/[^\d]/g, "")) {
            var n = e.split("-");
            e = n[0];
            var o = n.length > 1 ? n[1] : "testsuite" === e ? "library" : "view", a = t.createLink(e, o, "id=" + i);
            t.apps ? t.apps.open(a) : window.location.href = a
        } else {
            var s = {zh_cn: "请输入数字ID进行搜索", zh_tw: "請輸入數值ID行搜索"};
            alert(lang.searchTip || s[t.zui.clientLang()] || "Please enter a numberic id to search")
        }
        t("#searchInput").val(i).focus()
    }, t(function () {
        s(null, !0), t(document).on("keydown", function (e) {
            e.ctrlKey && 71 === e.keyCode && (t("#searchInput").val("").focus(), e.stopPropagation(), e.preventDefault())
        })
    }), t.removeAnchor = window.removeAnchor = function (t) {
        var e = t.lastIndexOf("#");
        return e > -1 ? t.substr(0, e) : t
    }, t.refreshPage = function (t) {
        t ? window.parent.location.reload() : window.location.reload()
    }, t.selectLang = window.selectLang = function (e) {
        t.cookie("lang", e, {
            expires: config.cookieLife,
            path: config.webRoot
        }), t.ajaxSendScore("selectLang"), t.refreshPage(1)
    }, t.selectTheme = window.selectTheme = function (e) {
        t.cookie("theme", e, {
            expires: config.cookieLife,
            path: config.webRoot
        }), t.ajaxSendScore("selectTheme"), t.refreshPage(1)
    }, t.zui.Picker && t.zui.Picker.enableChosen(), t.chosenDefaultOptions = {
        middle_highlight: !0,
        disable_search_threshold: 1,
        compact_search: !0,
        allow_single_deselect: !0,
        placeholder_text_single: " ",
        placeholder_text_multiple: " ",
        search_contains: !0,
        max_drop_width: 500,
        max_drop_height: 245,
        no_wrap: !0,
        drop_direction: function () {
            var e = t(this.container).closest(".table-responsive:not(.scroll-none)");
            if (e.length) {
                if (this.drop_directionFixed) return this.drop_directionFixed;
                e.css("position", "relative");
                var i = "down", n = this.container.find(".chosen-drop"), o = this.container.position(),
                    a = n.outerHeight();
                return o.top >= a && o.top + 31 + a > e.outerHeight() && (i = "up"), this.drop_directionFixed = i, i
            }
            return "auto"
        }
    }, t.chosenSimpleOptions = t.extend({}, t.chosenDefaultOptions, {disable_search_threshold: 6}), t.fn._chosen = t.fn.chosen, t.fn.chosen = function (e) {
        return "string" == typeof e ? this._chosen(e) : this.each(function () {
            var i = t(this).addClass("chosen-controled");
            return i._chosen(t.extend({}, i.hasClass("chosen-simple") ? t.chosenSimpleOptions : t.chosenDefaultOptions, i.data(), e))
        })
    }, t.fn.chosen.Constructor = t.fn._chosen.Constructor, t(function () {
        t(".chosen,.chosen-simple").each(function () {
            var e = t(this);
            e.closest(".template").length || e.chosen()
        })
    }), t.extend(t.fn.pager.Constructor.DEFAULTS, {
        maxNavCount: 8,
        prevIcon: "icon-angle-left",
        nextIcon: "icon-angle-right",
        firstIcon: "icon-first-page",
        lastIcon: "icon-last-page",
        navEllipsisItem: "…",
        menuDirection: "dropup",
        pageSizeOptions: [5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 100, 200, 500, 1e3, 2e3],
        elements: ["total_text", "size_menu", "first_icon", "prev_icon", '<div class="pager-label"><strong>{page}</strong>/<strong>{totalPage}</strong></div>', "next_icon", "last_icon"],
        onPageChange: function (e, i) {
            e.recPerPage !== i.recPerPage && t.cookie(this.options.pageCookie, e.recPerPage, {
                expires: config.cookieLife,
                path: config.webRoot
            }), e.recPerPage !== i.recPerPage && (window.location.href = this.createLink())
        }
    }), t.extend(!0, t.zui.Messager.DEFAULTS, {
        cssClass: "messagger-zt",
        icons: {success: "check-circle", info: "chat-line", warning: "exclamation-sign", danger: "exclamation-sign"}
    }), t.fn.reverseOrder = function () {
        return this.each(function () {
            var e = t(this);
            e.prependTo(e.parent())
        })
    };
    var r = function (e, i) {
        var n = t(e);
        if (!n.data("historiesInited")) {
            n.data("historiesInited", 1), i = t.extend({}, n.data(), i);
            var o = n.find(".histories-list"), a = !0, s = !1;
            n.on("click", ".btn-reverse", function () {
                o.children("li").reverseOrder(), a = !a, t(this).find(".icon").toggleClass("icon-arrow-up", a).toggleClass("icon-arrow-down", !a);
                var e = "#lastComment", i = t(e);
                i.length && window.KindEditor && (window.KindEditor.remove(e), i.kindeditor())
            }).on("click", ".btn-expand-all", function () {
                var e = t(this).find(".icon");
                s = !s, e.toggleClass("icon-plus", !s).toggleClass("icon-minus", s), o.children("li").toggleClass("show-changes", s)
            }).on("click", ".btn-expand", function () {
                t(this).closest("li").toggleClass("show-changes")
            }).on("click", ".btn-strip", function () {
                var e = t(this), n = e.find(".icon"), o = n.hasClass("icon-code");
                n.toggleClass("icon-code", !o).toggleClass("icon-text", o), e.attr("title", o ? i.original : i.textdiff), e.closest("li").toggleClass("show-original", o)
            }), o.find(".btn-strip").attr("title", i.original);
            var r = n.find(".modal-comment").modal({show: !1}).on("shown.zui.modal", function () {
                var t = r.find("#comment");
                t.length && (t.focus(), window.editor && window.editor.comment && window.editor.comment.focus())
            }).on("show.zui.modal", function () {
                var e = r.find("#comment");
                e.length && !e.data("keditor") && t.fn.kindeditor && e.kindeditor()
            });
            n.on("click", ".btn-comment", function (t) {
                r.modal("toggle"), t.preventDefault()
            }).on("click", ".btn-edit-comment,.btn-hide-form", function () {
                t(this).closest("li").toggleClass("show-form")
            });
            var l = n.find(".comment-edit-form");
            l.ajaxForm({
                success: function (t, e, i, n) {
                    setTimeout(function () {
                        l.closest("li").removeClass("show-form")
                    }, 2e3)
                }
            })
        }
    };
    t.fn.histories = function (t) {
        return this.each(function () {
            r(this, t)
        })
    }, t(function () {
        t(".histories").histories()
    });
    var l = 0, h = 0;
    t.toggleSidebar = function (e) {
        var i = t("#sidebar");
        if (i.length) {
            var n = t("main");
            if (void 0 === e) e = n.hasClass("hide-sidebar"); else if (e && !n.hasClass("hide-sidebar")) return;
            n.toggleClass("hide-sidebar", !e), clearTimeout(l), t.zui.store.set(h, e);
            var o = i.children(".cell"), a = {overflow: "visible", maxHeight: "initial"};
            e ? (i.addClass("showing"), l = setTimeout(function () {
                i.removeClass("showing"), i.trigger("sidebar.toggle", e)
            }, 210)) : (i.trigger("sidebar.toggle", e), t(window).width() < 1900 && (a = {
                overflow: "hidden",
                maxHeight: t(window).height() - 45
            })), o.css(a)
        }
    };
    var c = t.initSidebar = function () {
        var e = t("#sidebar");
        if (e.length) {
            if (e.data("init")) return !0;
            h = "sidebar:" + (e.data("id") || config.currentModule + "/" + config.currentMethod);
            var i = t("main");
            if (i.length) {
                i.on("click", ".sidebar-toggle", function () {
                    t.toggleSidebar(i.hasClass("hide-sidebar"))
                });
                var n = t.zui.store.get(h, e.data("hide") !== !1);
                n === !1 && e.addClass("no-animate"), t.toggleSidebar(n), n === !1 && setTimeout(function () {
                    e.removeClass("no-animate")
                }, 500);
                var o = e.find(".sidebar-toggle");
                if (o.length) {
                    var a = function () {
                        var e = o[0].getBoundingClientRect(), i = t(window).height(),
                            n = Math.max(0, Math.floor(Math.min(i - 40, e.top + e.height) - Math.max(e.top, 0)) / 2) + (e.top < 0 ? 0 - e.top : 0);
                        o.removeClass("fade").find(".icon").css("top", n + (t.zui.browser.isIE() ? (i - 80) / 2 : 0))
                    };
                    a(), e.data("init", 1).on("sidebar.toggle", a);
                    var s = t.zui.browser.isIE() ? 1500 : 0, r = 0, l = null, c = function () {
                        var t = Date.now();
                        return l && (clearTimeout(l), l = null), t - r < s ? (l = setTimeout(c, s / 2), void o.addClass("fade")) : (r = t, void a())
                    };
                    return t(window).on("resize scroll", s ? c : a), !0
                }
            }
        }
    };
    c() || t(c), t.toggleQueryBox = function (e, i) {
        var n = t(i || "#queryBox");
        if (n.length) {
            if (void 0 === e && (e = !n.hasClass("show")), n.toggleClass("show", !!e), !n.data("init")) {
                n.addClass("load-indicator loading").data("init", 1);
                var o = n.data("url") || t.createLink("search", "buildForm", n.data("module") ? "module=" + n.data("module") : "");
                t.get(o, function (t) {
                    n.html(t).removeClass("loading")
                })
            }
            t(".querybox-toggle").toggleClass("querybox-opened", e)
        }
    }, t(function () {
        var e = t("#queryBox");
        e.length && (t(document).on("click", ".querybox-toggle", function () {
            t.toggleQueryBox()
        }), e.hasClass("show") && t.toggleQueryBox(!0))
    }), t.extend(t.fn.colorPicker.Constructor.DEFAULTS, {colors: ["#3DA7F5", "#75C941", "#2DBDB2", "#797EC9", "#FFAF38", "#FF4E3E"]}), window.setCheckedCookie = function () {
        var e = [], i = t('#mainContent .main-table tbody>tr input[type="checkbox"]:checked');
        i.each(function () {
            var i = parseInt(t(this).val(), 10);
            NaN !== i && e.push(i)
        }), t.cookie("checkedItem", e.join(","), {expires: config.cookieLife, path: config.webRoot})
    }, t.extend(t.fn.modal.bs.Constructor.DEFAULTS, {
        scrollInside: !0,
        backdrop: "static",
        headerHeight: 100
    }), t.extend(t.zui.ModalTrigger.DEFAULTS, {
        scrollInside: !0,
        backdrop: "static",
        headerHeight: 40
    }), t.fn.initIframeModal = function () {
        return this.each(function () {
            var e = t(this);
            if (!e.parents('[data-ride="table"],.skip-iframe-modal').length) {
                var i = {type: "iframe"};
                e.hasClass("export") && t.extend(i, {width: 800, shown: setCheckedCookie}, e.data()), e.modalTrigger(i)
            }
        })
    }, t(function () {
        t("a.iframe,.export").initIframeModal()
    });
    var d = function () {
        var e, i, n = t(this), o = t.extend({limitSize: 40, suffix: "…"}, n.data()), a = n.text();
        if (a.length > o.limitSize) {
            e = a, i = a.substr(0, o.limitSize) + o.suffix, n.text(i).addClass("limit-text-on");
            var s = o.toggleBtn ? t(o.toggleBtn) : n.next(".text-limit-toggle");
            s.text(s.data("textExpand")), s.on("click", function () {
                var t = n.toggleClass("limit-text-on").hasClass("limit-text-on");
                n.text(t ? i : e), s.text(s.data(t ? "textExpand" : "textCollapse"))
            })
        } else (o.toggleBtn ? t(o.toggleBtn) : n.next(".text-limit-toggle")).hide()
    };
    t.fn.textLimit = function () {
        return this.each(d)
    }, t(function () {
        t(".text-limit").textLimit()
    }), t.fixedTableHead = window.fixedTableHead = function (e, i) {
        var n = t(e);
        if (n.is("table") || (n = n.find("table")), n.length) {
            var o = t(i || window), a = null, s = function () {
                var e = n.children("thead"), i = e[0].getBoundingClientRect(), o = n.next(".fixed-head-table");
                if (i.top < 0) {
                    var s = e.width();
                    if (o.length) {
                        if (a !== s) {
                            a = s;
                            var r = o.find("th");
                            e.find("th").each(function (e) {
                                r.eq(e).width(t(this).width())
                            })
                        }
                    } else {
                        var o = t("<table class='table fixed-head-table' style='position:fixed; top: 0;'></table>").addClass(n.attr("class")),
                            l = e.clone(), r = l.find("th");
                        e.find("th").each(function (e) {
                            r.eq(e).width(t(this).width())
                        }), o.append(l).insertAfter(n)
                    }
                    o.css({left: i.left, width: i.width}).show()
                } else o.hide()
            };
            o.on("scroll", s).on("resize", s), s()
        }
    }, t(document).on("click", "tr[data-url]", function () {
        var e = t(this), i = e.data("href") || e.data("url");
        i && (window.location.href = i)
    }), "yes" === config.onlybody && self === parent && (window.location.href = window.location.href.replace("?onlybody=yes", "").replace("&onlybody=yes", "")), t(function () {
        t("body").addClass("m-{currentModule}-{currentMethod}".format(config))
    });
    var u, f, p, g, m, v = function () {
        u || (u = t("#subNavbar"), f = t("#pageNav"), p = t("#pageActions"), g = u.children(".nav"), m = g.outerWidth());
        var e = u.outerWidth(), i = f.outerWidth() || 0, n = p.outerWidth() || 0;
        if (i = i ? i + 15 : 0, n = n ? n + 15 : 0, !i && !n) return void g.css({
            maxWidth: null,
            left: null,
            position: "static"
        });
        var o = Math.max(300, e - i - n), a = Math.min(o, m), s = (e - a) / 2,
            r = i && s < i ? i : n && s < n ? e - a - n : 0;
        g.css({maxWidth: o, left: r ? r - s : 0, position: "relative"})
    }, y = function () {
        t.cookie("windowWidth", window.innerWidth), t.cookie("windowHeight", window.innerHeight), v()
    };
    t(y), t(window).on("resize", y);
    var b = function () {
        var e = t("#back").attr("href");
        e && (window.location.href = e)
    }, w = function () {
        t.cookie("ajax_lastNext") || (t.cookie("ajax_lastNext", "on", {
            expires: config.cookieLife,
            path: config.webRoot
        }), t.ajaxSendScore("lastNext"))
    }, x = function () {
        var e = t("#prevPage").attr("href");
        e && (window.location.href = e), w()
    }, C = function () {
        var e = t("#nextPage").attr("href");
        e && (window.location.href = e), w()
    };
    t(document).on("keydown", function (e) {
        t("body").hasClass("modal-open") || (e.altKey && 38 === e.keyCode ? b() : 37 === e.keyCode ? x() : 39 === e.keyCode && C())
    }), t.fn.tree.Constructor.DEFAULTS.initialState = "preserve", t.closeModal = function (e, i, n) {
        t.zui.closeModal(n, e, i)
    }, t.getThemeColor = function (e) {
        if (!t.themeColor) {
            var i = t("#mainHeader");
            i.length && (t.themeColor = {
                primary: i.css("border-top-color"),
                pale: i.css("border-bottom-color"),
                secondary: i.css("background-color")
            })
        }
        return e ? t.themeColor && t.themeColor[e] : t.themeColor
    };
    var _ = function (e) {
        var i = t(e);
        if (!i.hasClass("header-angle-btn")) {
            var n, o,
                a = i.children(".input-group-addon,.form-control:not(.chosen-controled),.chosen-container,.btn,.input-control,.input-group-btn,.datepicker-wrapper").not(".hidden"),
                s = 1 === a.length;
            a.each(function (e) {
                var i = t(this),
                    r = i.is(".input-group-addon") ? "addon" : i.is(".chosen-container") ? "chosen" : i.is(".btn") ? "btn" : i.is(".input-control,.datepicker-wrapper") ? "insideInput" : i.is(".input-group-btn") ? "insideBtn" : "input",
                    l = {};
                if (s) l.borderTopLeftRadius = 4, l.borderBottomLeftRadius = 4, l.borderTopRightRadius = 4, l.borderBottomRightRadius = 4; else {
                    var h = !n, c = e === a.length - 1, d = "btn" === r ? 4 : 2;
                    l.borderTopLeftRadius = 0, l.borderBottomLeftRadius = 0, l.borderTopRightRadius = 0, l.borderBottomRightRadius = 0, h && ("addon" === r && (l.borderLeftWidth = 1), l.borderTopLeftRadius = d, l.borderBottomLeftRadius = d), c && ("addon" === r && (l.borderRightWidth = 1), l.borderTopRightRadius = d, l.borderBottomRightRadius = d), o && ("chosen" !== o && "input" !== o && "btn" !== o && "insideInput" !== o && "insideBtn" !== o || "chosen" !== r && "input" !== r && "btn" !== r && "insideInput" !== r && "insideBtn" !== r ? "addon" === o && "addon" === r && (l.borderLeftWidth = 1) : l.borderLeftColor = "transparent")
                }
                ("insideBtn" === r ? i.find(".btn") : "insideInput" === r ? i.find(".form-control") : "chosen" === r ? i.find(".chosen-single,.chosen-choices") : i).css(l), n = i, o = r
            })
        }
    };
    t.fn.fixInputGroup = function () {
        return this.each(function () {
            _(this)
        })
    };
    var k = function () {
        var e = t(".main-actions>.btn-toolbar");
        if (e.length) {
            var i, n, o = e.children(), a = o.length, s = !1, r = null;
            if (a) for (o.each(function (e) {
                i = t(this), n = i.is(".divider"), n && !r && i.hide(), s || n || (s = !0), r = n ? null : i, !n || e !== a - 1 && 0 !== e || i.hide()
            }); i.length && i.is(".divider");) i = i.hide().prev();
            s || e.hide()
        }
    };
    t(function () {
        t(".input-group,.btn-group").fixInputGroup(), k()
    }), window.holders && t.each(window.holders, function (e) {
        var i = t("#" + e);
        i.length && i.is("input") && i.attr("placeholder", window.holders[e])
    }), t(function () {
        var e = t(".table-responsive"), i = t.fixTableResponsive = function () {
            e.each(function () {
                this.scrollHeight - 3 <= this.clientHeight && this.scrollWidth - 3 <= this.clientWidth ? t(this).addClass("scroll-none").css("overflow", "visible") : t(this).removeClass("scroll-none").css("overflow", "auto")
            })
        };
        e.length && (t(window).on("resize", i), setTimeout(i, 100))
    });
    var T = function () {
        var e = this, i = t(e), n = i.closest("tr").find("textarea");
        if (n.length) {
            var o = 32;
            n.each(function () {
                var e = t(this).closest("td"), i = e.css("height");
                e.css("height", this.style.height), this.style.height = "auto";
                var n = this.value ? this.scrollHeight + 2 : 32;
                o = Math.max(o, n), e.css("height", i)
            }), n.css("height", o)
        } else {
            e.style.height = "auto";
            var a = e.value ? e.scrollHeight + 2 : 32;
            e.style.height = a + "px"
        }
    };
    t.autoResizeTextarea = function (e) {
        t(e).each(T)
    }, t(function () {
        t("textarea.autosize").each(T), t(document).on("input paste change", "textarea.autosize", T)
    }), t(function () {
        var e = t("#dropMenu,.drop-menu");
        e.length && e.on("click", ".toggle-right-col", function (e) {
            t(this).closest("#dropMenu,.drop-menu").toggleClass("show-right-col"), e.stopPropagation(), e.preventDefault()
        })
    });
    var S = "undefined" != typeof InstallTrigger;
    t.zui.browser.firefox = S, t("html").toggleClass("is-firefox", S).toggleClass("not-firefox", !S), t(function () {
        var e = t("#mainContent>.main-col"), i = e.children(".main-actions");
        if (i.length) {
            var n = i.prev();
            if (i.length && n.length) {
                t('<div class="main-actions-holder"></div>').css("height", i.outerHeight()).insertAfter(i);
                var o = 0, a = function () {
                    var e = n[0].getBoundingClientRect(), s = e.top + e.height + 120 > t(window).height();
                    if (t("body").toggleClass("main-actions-fixed", s), s) {
                        var r = n.width();
                        r ? i.width(r) : o < 10 && setTimeout(a, 1e3)
                    }
                    o++
                };
                t.resetToolbarPosition = a, a(), t(window).on("resize scroll", a)
            }
        }
    }), t(document).on("show.zui.modal", function (e) {
        t("body.body-modal").length && window.parent && window.parent !== window && t(e.target).is(".modal") && window.parent.$("body").addClass("hide-modal-close")
    }).on("hidden.zui.modal", function () {
        t("body.body-modal").length && window.parent && window.parent !== window && window.parent.$("body").removeClass("hide-modal-close")
    }), t(function () {
        var e = t(".dropdown-menu.with-search");
        e.length && (e.find(".menu-search").on("click", function (t) {
            return t.stopPropagation(), !1
        }), e.on("keyup change paste", "input", function () {
            var e = t(this), i = e.closest(".dropdown-menu.with-search"), n = e.val().toLowerCase(),
                o = i.find(".option");
            "" == n ? o.removeClass("hide") : o.each(function () {
                var e = t(this);
                e.toggleClass("hide", e.text().toString().toLowerCase().indexOf(n) < 0 && e.data("key").toString().toLowerCase().indexOf(n) < 0)
            })
        }), e.parents(".dropdown-submenu").one("mouseenter", function () {
            var e = t(this).find(".dropdown-list")[0];
            e && e.getBoundingClientRect && setTimeout(function () {
                var t = 270, i = e.getBoundingClientRect();
                i.top < 0 && (t = Math.min(270, i.height) + i.top), e.style.maxHeight = Math.min(270, t) + "px"
            }, 50)
        })), t(".dropdown-menu.with-search .menu-search").on("click", function (t) {
            return t.stopPropagation(), !1
        })
    })
}(jQuery), function (t) {
    function e() {
        var e = window.parent, i = config.currentModule, n = config.currentMethod, o = "index" === i && "index" === n,
            a = "#_single" === location.hash || o || !t("#mainHeader,#editorNav").length || "tutorial" === i || "install" === i || "upgrade" === i || "user" === i && ("login" === n || "deny" === n) || "my" === i && "changepassword" === n || t("body").hasClass("allow-self-open"),
            s = location.href;
        if (e === window && !a) {
            var r = location.pathname + location.search + location.hash, l = "";
            return location.href = t.createLink("index", "index", l) + "#app=" + encodeURIComponent(r)
        }
        if (e !== window && e.$.apps) {
            o && e.location.reload();
            var h = window.name;
            if (0 === h.indexOf("app-")) {
                t.apps = window.apps = e.$.apps;
                var c = h.substr(4);
                t.appCode = c, t(document).on("click", function (t) {
                    var i = e.document.getElementById(window.name);
                    if (i) {
                        var n = e.document.getElementById(i.name) || i;
                        n && n.dispatchEvent(new Event(t.type, {bubbles: !0}))
                    }
                }).on("click", "a,.open-in-app,.show-in-app", function (e) {
                    var i = t(this);
                    if (!i.is("[data-modal],[data-toggle],[data-ride],[data-tab],.iframe,.not-in-app,[target]") && !i.data("zui.modaltrigger")) {
                        var n = i.hasClass("show-in-app") ? "" : i.attr("href") || (i.is("a") ? "" : i.data("url")),
                            o = i.data("app") || i.data("group");
                        if (n) {
                            if (0 === n.indexOf("javascript:") || "#" === n[0]) return;
                            var a = t.parseLink(n);
                            if ("index" === a.moduleName && "index" === a.methodName) return window.location.reload(), void e.preventDefault()
                        } else if (!o) return;
                        o || (o = t.apps.getAppCode(n)), o && ("help" === o && (t.apps.appsMap.help.text = i.text(), t.apps.appsMap.help.url || (t.apps.appsMap.help.url = n)), t.apps.open(n, o) && e.preventDefault())
                    }
                }), t.apps.updateUrl(c, s, document.title)
            }
        }
    }

    function i() {
        var e = t("#navbar>.nav");
        if (e.length) {
            var i = t("#heading"), n = +i.css("left").replace("px", ""), o = i.outerWidth(), a = e.width(),
                s = t("#mainHeader>.container").width() - 2 * n, r = Math.floor((s - a) / 2);
            e.css("marginLeft", r < o ? 2 * (o - r) : "")
        }
    }

    function n() {
        var e = function (e, i) {
            var n = {
                zh_cn: {modal: "对话框中有未提交的表单，是否关闭？", app: "应用“{0}”中有未提交的表单，是否继续？"},
                zh_tw: {modal: "對話框中有未提交的表單，是否關閉？ ", app: "應用“{0}”中有未提交的表單，是否繼續？ "},
                en: {
                    modal: "There is an uncommitted form in the dialog box. Do you want to close?",
                    app: "There are uncommitted forms in the application '{0}'. Do you want to continue?"
                }
            };
            return t.zui.formatString((n[t.zui.clientLang()] || n.en)[e || "page"], i)
        };
        if (parent === window) return void (t.apps && t(window).on("beforeunload", function (i) {
            var n, o;
            if (t.each(t.apps.openedApps, function (t, e) {
                if (o = e.$iframe && e.$iframe[0].contentWindow.hasFormChanged()) return n = t, !1
            }), n) return t.apps.open(n), o.addClass("form-unsaved"), setTimeout(function () {
                o.removeClass("form-unsaved")
            }, 5e3), i.preventDefault(), e("app", t.apps.appsMap[n].text)
        }));
        var i = t("#main form:not(.not-watch)");
        if (i.length) {
            i.each(function () {
                var e = t(this);
                e.hasClass("search-form") || e.closest('[data-ride="table"]').length || e.data("zui.table") || e.find('[data-ride="table"]').length || e.addClass("form-watched").data("originalFormData", e.serialize())
            });
            var n = function () {
                var i = hasFormChanged();
                if (i) {
                    i.addClass("form-unsaved");
                    var n = !i.closest("body.body-modal").length;
                    if (!confirm(i.data("unsavedTip") || e(n ? "app" : "modal", n ? t.apps.appsMap[t.appCode].text : ""))) return setTimeout(function () {
                        i.removeClass("form-unsaved")
                    }, 5e3), !1;
                    i.removeClass("form-unsaved")
                }
            };
            t(document.body).hasClass("body-modal") ? t("body").on("modalhide.zui.modal", n) : parent.$.apps && t(document).on("openapp.apps closeapp.apps", function (t, e, i) {
                if ("closeapp" === t.type || i) return n()
            })
        }
    }

    Array.prototype.includes || Object.defineProperty(Array.prototype, "includes", {
        value: function (t, e) {
            function i(t, e) {
                return t === e || "number" == typeof t && "number" == typeof e && isNaN(t) && isNaN(e)
            }

            if (null == this) throw new TypeError('"this" is null or not defined');
            var n = Object(this), o = n.length >>> 0;
            if (0 === o) return !1;
            for (var a = 0 | e, s = Math.max(a >= 0 ? a : o - Math.abs(a), 0); s < o;) {
                if (i(n[s], t)) return !0;
                s++
            }
            return !1
        }
    }), t.getSearchParam = function (t, e) {
        e = void 0 === e ? window.location.search : e;
        var i = {};
        return e.length > 1 && ("?" === e[0] && (e = e.substr(1)), e.split("&").forEach(function (t) {
            var e = t.split("=", 2);
            if (e.length > 1) try {
                i[e[0]] = decodeURIComponent(e[1])
            } catch (n) {
                i[e[0]] = ""
            } else i[e[0]] = ""
        })), t ? i[t] : i
    }, t.parseLink = function (e) {
        if (!e) return {};
        if ((0 === e.indexOf("http:") || 0 === e.indexOf("https:")) && e.indexOf(window.location.origin) < 0) return {};
        var i = e.split("#"), n = i[0].split("?"), o = n[1], a = o ? t.getSearchParam("", o) : {}, s = n[0],
            r = {isOnlyBody: "yes" === a.onlybody, vars: [], hash: i[1] || "", params: a, tid: a.tid || ""};
        if ("GET" === config.requestType) {
            r.moduleName = a[config.moduleVar] || "index", r.methodName = a[config.methodVar] || "index", r.viewType = a[config.viewVar] || config.defaultView;
            for (var l in a) l !== config.moduleVar && l !== config.methodVar && l !== config.viewVar && "onlybody" !== l && "tid" !== l && r.vars.push([l, a[l]])
        } else {
            var h = s.lastIndexOf("/");
            h === s.length - 1 && (s = s.substr(0, h), h = s.lastIndexOf("/")), h >= 0 && (s = s.substr(h + 1));
            var c = s.lastIndexOf(".");
            c >= 0 ? (r.viewType = s.substr(c + 1), s = s.substr(0, c)) : r.viewType = config.defaultView;
            var d = s.split(config.requestFix);
            if (r.moduleName = d[0] || "index", r.methodName = d[1] || "index", d.length > 2) for (var u = 2; u < d.length; u++) r.vars.push(["", d[u]]), a["$" + (u - 1)] = d[u]
        }
        return r
    }, window.hasFormChanged = function (e) {
        var i = t(".modal.in iframe");
        if (i.length) for (var n = 0; n < i.length; ++n) {
            var o = i[n], a = o.contentWindow.hasFormChanged();
            if (a) return a
        }
        e || (e = ".form-watched");
        var s = t(e);
        if (!s.length) return !1;
        for (var n = 0; n < s.length; ++n) {
            var r = s.eq(n), l = r.data("originalFormData");
            if (null !== l && r.hasClass("form-watched") && !r.hasClass("form-disabled") && l !== r.serialize()) return r
        }
        return !1
    }, t(function () {
        e(), t("#navbar>.nav>li").length > 10 && (i(), t(window).on("resize", i)), setTimeout(n, 1e3)
    })
}(jQuery), $.zui.lang("de", {
    "zui.pager": {
        pageOfText: "Seite {0}",
        prev: "Zurück",
        next: "Nächste Seite",
        first: "Erste Seite",
        last: "Letzte Seite",
        "goto": "Goto",
        pageOf: "Seite <strong>{page}</strong>",
        totalPage: "<strong>{totalPage}</strong> Seiten",
        totalCount: "Total: <strong>{recTotal}</strong> Artikel",
        pageSize: "<strong>{recPerPage}</strong> Artikel pro Seite",
        itemsRange: "Seiten <strong>{start}</strong> bis <strong>{end}</strong>",
        pageOfTotal: "Seite <strong>{page}</strong>/<strong>{totalPage}</strong>"
    },
    "zui.boards": {append2end: "Gehen Sie zum Ende"},
    "zui.browser": {tip: "Online. Sorgenfrei. Aktualisiere deinen Browser noch heute!"},
    "zui.calendar": {
        weekNames: ["Son", "Mon", "Die", "Mit", "Don", "Fri", "Sam"],
        monthNames: ["Jan", "Feb", "Mär", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dez"],
        today: "Heute",
        year: "{0}Jahr",
        month: "{0}Monat",
        yearMonth: "{0}-{1}"
    },
    "zui.chosenIcons": {
        emptyIcon: "[Kein Icon]",
        commonIcons: "Gemeinsame Symbole",
        webIcons: "Web-Symbol",
        editorIcons: "Editor-Symbol",
        directionalIcons: "Pfeil Zusammenfluss",
        otherIcons: "Andere Symbole"
    },
    "zui.colorPicker": {errorTip: "Kein gültiger Farbwert"},
    "zui.datagrid": {
        errorCannotGetDataFromRemote: "Daten vom Remote-Server ({0}) können nicht abgerufen werden.",
        errorCannotHandleRemoteData: "Die vom Remote-Server zurückgegebenen Daten können nicht verarbeitet werden."
    },
    "zui.guideViewer": {prevStep: "Vorheriger Schritt", nextStep: "Nächster Schritt"},
    "zui.tabs": {
        reload: "Neu laden",
        close: "Schliessen",
        closeOthers: "Schließen Sie andere Registerkarten",
        closeRight: "Schließen Sie die rechte Registerkarte",
        reopenLast: "Letzten geschlossenen Tab wiederherstellen",
        errorCannotFetchFromRemote: "Inhalt kann nicht vom Remote-Server abgerufen werden ({0})."
    },
    "zui.uploader": {},
    datetimepicker: {
        days: ["Sonntag", "Montag", "Diensteg", "Mittwoch", "Donnerstag", "Freitag", "Samstag"],
        daysShort: ["Son", "Mon", "Die", "Mit", "Don", "Fri", "Sam"],
        daysMin: ["Son", "Mon", "Die", "Mit", "Don", "Fri", "Sam"],
        months: ["Jan", "Feb", "Mär", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dez"],
        monthsShort: ["Jan", "Feb", "Mär", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dez"],
        today: "Heute",
        suffix: [],
        meridiem: []
    },
    chosen: {no_results_text: "Nicht gefunden"},
    bootbox: {OK: "OK", CANCEL: "Stornieren", CONFIRM: "Bestätigen"}
}), $.zui.lang("fr", {
    "zui.pager": {
        pageOfText: "Page {0}",
        prev: "Prev",
        next: "Suivant",
        first: "First",
        last: "Last",
        "goto": "Goto",
        pageOf: "Page <strong>{page}</strong>",
        totalPage: "<strong>{totalPage}</strong> pages",
        totalCount: "Total: <strong>{recTotal}</strong> items",
        pageSize: "<strong>{recPerPage}</strong> per page",
        itemsRange: "De <strong>{start}</strong> à <strong>{end}</strong>",
        pageOfTotal: "Page <strong>{page}</strong> de <strong>{totalPage}</strong>"
    },
    "zui.boards": {append2end: "Aller jusqu'au bout"},
    "zui.browser": {tip: "Naviguez sans crainte sur Internet. Mettez votre navigateur à jour dès aujourd'hui!"},
    "zui.calendar": {
        weekNames: ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam"],
        monthNames: ["Jan", "Fév", "Mar", "Avr", "Mai", "Juin", "Juil", "Août", "Sep", "Oct", "Nov", "Déc"],
        today: "Aujourd'hui",
        year: "{0} Année",
        month: "{0} Mois",
        yearMonth: "{0}-{1}"
    },
    "zui.chosenIcons": {
        emptyIcon: "[Aucune icône]",
        commonIcons: "Icônes communes",
        webIcons: "Icône Web",
        editorIcons: "Icône de l'éditeur",
        directionalIcons: "Flèche confluence",
        otherIcons: "Autres icônes"
    },
    "zui.colorPicker": {errorTip: "Pas une valeur de couleur valide"},
    "zui.datagrid": {
        errorCannotGetDataFromRemote: "Impossible d'obtenir les données du serveur distant ({0}).",
        errorCannotHandleRemoteData: "Impossible de traiter les données renvoyées par le serveur distant."
    },
    "zui.guideViewer": {prevStep: "Étape précédente", nextStep: "Prochaine étape"},
    "zui.tabs": {
        reload: "Recharger",
        close: "Fermer",
        closeOthers: "Fermez les autres onglets",
        closeRight: "Fermer l'onglet de droite",
        reopenLast: "Restaurer le dernier onglet fermé",
        errorCannotFetchFromRemote: "Impossible d'obtenir le contenu du serveur distant ({0})."
    },
    "zui.uploader": {},
    datetimepicker: {
        days: ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"],
        daysShort: ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam"],
        daysMin: ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam"],
        months: ["Jan", "Fév", "Mar", "Avr", "Mai", "Juin", "Juil", "Août", "Sep", "Oct", "Nov", "Déc"],
        monthsShort: ["Jan", "Fév", "Mar", "Avr", "Mai", "Juin", "Juil", "Août", "Sep", "Oct", "Nov", "Déc"],
        today: "Aujourd'hui",
        suffix: [],
        meridiem: []
    },
    chosen: {no_results_text: "Pas trouvé"},
    bootbox: {OK: "D'accord", CANCEL: "Annuler", CONFIRM: "Confirmer"}
});