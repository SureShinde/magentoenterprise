/*
 * Ext JS Library 1.0.1
 * Customized by mahmood rehman for copy category extension
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 *
 * http://www.extjs.com/license
 */
 Ext.dd.Registry = function () {
    var _1 = {};
    var _2 = {};
    var _3 = 0;
    var _4 = function (el, _6) {
            if (typeof el == "string") {
                return el;
            }
			
            var id = el.id;
            if (!id && _6 !== false) {
                id = "extdd-" + (++_3);
                el.id = id;
            }
            return id;
        };
    return {
        register: function (el, _9) {
            _9 = _9 || {};
            if (typeof el == "string") {
                el = document.getElementById(el);
            }
            _9.ddel = el;
            _1[_4(el)] = _9;
            if (_9.isHandle !== false) {
                _2[_9.ddel.id] = _9;
            }
            if (_9.handles) {
                var hs = _9.handles;
                for (var i = 0, _c = hs.length; i < _c; i++) {
                    _2[_4(hs[i])] = _9;
                }
            }
        },
        unregister: function (el) {
            var id = _4(el, false);
            var _f = _1[id];
            if (_f) {
                delete _1[id];
                if (_f.handles) {
                    var hs = _f.handles;
                    for (var i = 0, len = hs.length; i < len; i++) {
                        delete _2[_4(hs[i], false)];
                    }
                }
            }
        },
        getHandle: function (id) {
            if (typeof id != "string") {
                id = id.id;
            }
            return _2[id];
        },
        getHandleFromEvent: function (e) {
            var t = Ext.lib.Event.getTarget(e);
            return t ? _2[t.id] : null;
        },
        getTarget: function (id) {
            if (typeof id != "string") {
                id = id.id;
            }
            return _1[id];
        },
        getTargetFromEvent: function (e) {
            var t = Ext.lib.Event.getTarget(e);
            return t ? _1[t.id] || _2[t.id] : null;
        }
    };
}();
Ext.tree.TreeNodeUI = function (_1) {
    this.node = _1;
    this.rendered = false;
    this.animating = false;
    this.emptyIcon = Ext.BLANK_IMAGE_URL;
};
Ext.tree.TreeNodeUI.prototype = {
    removeChild: function (_2) {
        if (this.rendered) {
            this.ctNode.removeChild(_2.ui.getEl());
        }
    },
    beforeLoad: function () {
        this.addClass("x-tree-node-loading");
    },
    afterLoad: function () {
        this.removeClass("x-tree-node-loading");
    },
    onTextChange: function (_3, _4, _5) {
        if (this.rendered) {
            this.textNode.innerHTML = _4;
        }
    },
    onDisableChange: function (_6, _7) {
        this.disabled = _7;
        if (_7) {
            this.addClass("x-tree-node-disabled");
        } else {
            this.removeClass("x-tree-node-disabled");
        }
    },
    onSelectedChange: function (_8) {
        if (_8) {
            this.focus();
            this.addClass("x-tree-selected");
        } else {
            this.removeClass("x-tree-selected");
        }
    },
    onMove: function (_9, _a, _b, _c, _d, _e) {
        this.childIndent = null;
        if (this.rendered) {
            var _f = _c.ui.getContainer();
            if (!_f) {
                this.holder = document.createElement("div");
                this.holder.appendChild(this.wrap);
                return;
            }
            var _10 = _e ? _e.ui.getEl() : null;
            if (_10) {
                _f.insertBefore(this.wrap, _10);
            } else {
                _f.appendChild(this.wrap);
            }
            this.node.renderIndent(true);
        }
    },
    addClass: function (cls) {
        if (this.elNode) {
            Ext.fly(this.elNode).addClass(cls);
        }
    },
    removeClass: function (cls) {
        if (this.elNode) {
            Ext.fly(this.elNode).removeClass(cls);
        }
    },
    remove: function () {
        if (this.rendered) {
            this.holder = document.createElement("div");
            this.holder.appendChild(this.wrap);
        }
    },
    fireEvent: function () {
        return this.node.fireEvent.apply(this.node, arguments);
    },
    initEvents: function () {
        this.node.on("move", this.onMove, this);
        var E = Ext.EventManager;
        var a = this.anchor;
        var el = Ext.fly(a);
        if (Ext.isOpera) {
            el.setStyle("text-decoration", "none");
        }
        el.on("click", this.onClick, this);
        el.on("dblclick", this.onDblClick, this);
        el.on("contextmenu", this.onContextMenu, this);
        var _16 = Ext.fly(this.iconNode);
        _16.on("click", this.onClick, this);
        _16.on("dblclick", this.onDblClick, this);
        _16.on("contextmenu", this.onContextMenu, this);
        E.on(this.ecNode, "click", this.ecClick, this, true);
        if (this.node.disabled) {
            this.addClass("x-tree-node-disabled");
        }
        if (this.node.hidden) {
            this.addClass("x-tree-node-disabled");
        }
        var ot = this.node.getOwnerTree();
        var dd = ot.enableDD || ot.enableDrag || ot.enableDrop;
        if (dd && (!this.node.isRoot || ot.rootVisible)) {
            Ext.dd.Registry.register(this.elNode, {
                node: this.node,
                handles: [this.iconNode, this.textNode],
                isHandle: false
            });
        }
    },
    hide: function () {
        if (this.rendered) {
            this.wrap.style.display = "none";
        }
    },
    show: function () {
        if (this.rendered) {
            this.wrap.style.display = "";
        }
    },
    onContextMenu: function (e) {
        e.preventDefault();
        this.focus();
        this.fireEvent("contextmenu", this.node, e);
    },
    onClick: function (e) {
        if (this.dropping) {
            return;
        }
        if (this.fireEvent("beforeclick", this.node, e) !== false) {
            if (!this.disabled && this.node.attributes.href) {
                this.fireEvent("click", this.node, e);
                return;
            }
            e.preventDefault();
            if (this.disabled) {
                return;
            }
            if (this.node.attributes.singleClickExpand && !this.animating && this.node.hasChildNodes()) {
                this.node.toggle();
            }
            this.fireEvent("click", this.node, e);
        } else {
            e.stopEvent();
        }
    },
    onDblClick: function (e) {
        e.preventDefault();
        if (this.disabled) {
            return;
        }
        if (!this.animating && this.node.hasChildNodes()) {
            this.node.toggle();
        }
        this.fireEvent("dblclick", this.node, e);
    },
    ecClick: function (e) {
        if (!this.animating && this.node.hasChildNodes()) {
            this.node.toggle();
        }
    },
    startDrop: function () {
        this.dropping = true;
    },
    endDrop: function () {
        setTimeout(function () {
            this.dropping = false;
        }.createDelegate(this), 50);
    },
    expand: function () {
        this.updateExpandIcon();
        this.ctNode.style.display = "";
    },
    focus: function () {
        if (!this.node.preventHScroll) {
            try {
                this.anchor.focus();
            } catch (e) {}
        } else {
            if (!Ext.isIE) {
                try {
                    var _1d = this.node.getOwnerTree().el.dom;
                    var l = _1d.scrollLeft;
                    this.anchor.focus();
                    _1d.scrollLeft = l;
                } catch (e) {}
            }
        }
    },
    blur: function () {
        try {
            this.anchor.blur();
        } catch (e) {}
    },
    animExpand: function (_1f) {
        var ct = Ext.get(this.ctNode);
        ct.stopFx();
        if (!this.node.hasChildNodes()) {
            this.updateExpandIcon();
            this.ctNode.style.display = "";
            Ext.callback(_1f);
            return;
        }
        this.animating = true;
        this.updateExpandIcon();
        ct.slideIn("t", {
            callback: function () {
                this.animating = false;
                Ext.callback(_1f);
            },
            scope: this,
            duration: this.node.ownerTree.duration || 0.25
        });
    },
    highlight: function () {
        var _21 = this.node.getOwnerTree();
        Ext.fly(this.wrap).highlight(_21.hlColor || "C3DAF9", {
            endColor: _21.hlBaseColor
        });
    },
    collapse: function () {
        this.updateExpandIcon();
        this.ctNode.style.display = "none";
    },
    animCollapse: function (_22) {
        var ct = Ext.get(this.ctNode);
        ct.enableDisplayMode("block");
        ct.stopFx();
        this.animating = true;
        this.updateExpandIcon();
        ct.slideOut("t", {
            callback: function () {
                this.animating = false;
                Ext.callback(_22);
            },
            scope: this,
            duration: this.node.ownerTree.duration || 0.25
        });
    },
    getContainer: function () {
        return this.ctNode;
    },
    getEl: function () {
        return this.wrap;
    },
    appendDDGhost: function (_24) {
        _24.appendChild(this.elNode.cloneNode(true));
    },
    getDDRepairXY: function () {
        return Ext.lib.Dom.getXY(this.iconNode);
    },
    onRender: function () {
        this.render();
    },
    render: function (_25) {
        var n = this.node;
        var _27 = n.parentNode ? n.parentNode.ui.getContainer() : n.ownerTree.container.dom;
        if (!this.rendered) {
            this.rendered = true;
            var a = n.attributes;
            this.indentMarkup = "";
            if (n.parentNode) {
                this.indentMarkup = n.parentNode.ui.getChildIndent();
            }
            var buf = ["<li class=\"x-tree-node\"><div class=\"x-tree-node-el ", n.attributes.cls, "\">", "<span class=\"x-tree-node-indent\">", this.indentMarkup, "</span>", "<img src=\"", this.emptyIcon, "\" class=\"x-tree-ec-icon\">", "<img src=\"", a.icon || this.emptyIcon, "\" class=\"x-tree-node-icon", (a.icon ? " x-tree-node-inline-icon" : ""), (a.iconCls ? " " + a.iconCls : ""), "\" unselectable=\"on\">", "<input type='checkbox' name=\"tree['" + n.id + "']\" value='" + n.id + "' id='" + n.id + "' class='copycate'/><a hidefocus=\"on\" href=\"", a.href ? a.href : "#", "\" tabIndex=\"1\" ", a.hrefTarget ? " target=\"" + a.hrefTarget + "\"" : "", " class='movea'><span unselectable=\"on\">", n.text, "</span></a></div>", "<ul class=\"x-tree-node-ct\" style=\"display:none;\"></ul>", "</li>"];
            if (_25 !== true && n.nextSibling && n.nextSibling.ui.getEl()) {
                this.wrap = Ext.DomHelper.insertHtml("beforeBegin", n.nextSibling.ui.getEl(), buf.join(""));
            } else {
                this.wrap = Ext.DomHelper.insertHtml("beforeEnd", _27, buf.join(""));
            }
            this.elNode = this.wrap.childNodes[0];
            this.ctNode = this.wrap.childNodes[1];
            var cs = this.elNode.childNodes;
            this.indentNode = cs[0];
            this.ecNode = cs[1];          
			 this.iconNode = cs[2];
			this.inputNode = cs[3];
            this.anchor = cs[4];
            this.textNode = cs[4].firstChild;
            if (a.qtip) {
                if (this.textNode.setAttributeNS) {
                    this.textNode.setAttributeNS("ext", "qtip", a.qtip);
                    if (a.qtipTitle) {
                        this.textNode.setAttributeNS("ext", "qtitle", a.qtipTitle);
                    }
                } else {
                    this.textNode.setAttribute("ext:qtip", a.qtip);
                    if (a.qtipTitle) {
                        this.textNode.setAttribute("ext:qtitle", a.qtipTitle);
                    }
                }
            }
            this.initEvents();
            if (!this.node.expanded) {
                this.updateExpandIcon();
            }
        } else {
            if (_25 === true) {
                _27.appendChild(this.wrap);
            }
        }
    },
    getAnchor: function () {
        return this.anchor;
    },
    getTextEl: function () {
        return this.textNode;
    },
    getIconEl: function () {
        return this.iconNode;
    },
    updateExpandIcon: function () {
        if (this.rendered) {
            var n = this.node,
                c1, c2;
            var cls = n.isLast() ? "x-tree-elbow-end" : "x-tree-elbow";
            var _2f = n.hasChildNodes();
            if (_2f) {
                if (n.expanded) {
                    cls += "-minus";
                    c1 = "x-tree-node-collapsed";
                    c2 = "x-tree-node-expanded";
                } else {
                    cls += "-plus";
                    c1 = "x-tree-node-expanded";
                    c2 = "x-tree-node-collapsed";
                }
                if (this.wasLeaf) {
                    this.removeClass("x-tree-node-leaf");
                    this.wasLeaf = false;
                }
                if (this.c1 != c1 || this.c2 != c2) {
                    Ext.fly(this.elNode).replaceClass(c1, c2);
                    this.c1 = c1;
                    this.c2 = c2;
                }
            } else {
                if (!this.wasLeaf) {
                    Ext.fly(this.elNode).replaceClass("x-tree-node-expanded", "x-tree-node-leaf");
                    this.wasLeaf = true;
                }
            }
            var ecc = "x-tree-ec-icon " + cls;
            if (this.ecc != ecc) {
                this.ecNode.className = ecc;
                this.ecc = ecc;
            }
        }
    },
    getChildIndent: function () {
        if (!this.childIndent) {
            var buf = [];
            var p = this.node;
            while (p) {
                if (!p.isRoot || (p.isRoot && p.ownerTree.rootVisible)) {
                    if (!p.isLast()) {
                        buf.unshift("<img src=\"" + this.emptyIcon + "\" class=\"x-tree-elbow-line\">");
                    } else {
                        buf.unshift("<img src=\"" + this.emptyIcon + "\" class=\"x-tree-icon\">");
                    }
                }
                p = p.parentNode;
            }
            this.childIndent = buf.join("");
        }
        return this.childIndent;
    },
    renderIndent: function () {
        if (this.rendered) {
            var _33 = "";
            var p = this.node.parentNode;
            if (p) {
                _33 = p.ui.getChildIndent();
            }
            if (this.indentMarkup != _33) {
                this.indentNode.innerHTML = _33;
                this.indentMarkup = _33;
            }
            this.updateExpandIcon();
        }
    }
};
Ext.tree.RootTreeNodeUI = function () {
    Ext.tree.RootTreeNodeUI.superclass.constructor.apply(this, arguments);
};
Ext.extend(Ext.tree.RootTreeNodeUI, Ext.tree.TreeNodeUI, {
    render: function () {
        if (!this.rendered) {
            var _35 = this.node.ownerTree.container.dom;
            this.node.expanded = true;
            _35.innerHTML = "<div class=\"x-tree-root-node\"></div>";
            this.wrap = this.ctNode = _35.firstChild;
        }
    },
    collapse: function () {},
    expand: function () {}
});





Ext.tree.TreeLoader = function (_1) {
    this.baseParams = {};
    this.requestMethod = "POST";
    Ext.apply(this, _1);
    this.addEvents({
        "beforeload": true,
        "load": true,
        "loadexception": true
    });
};
Ext.extend(Ext.tree.TreeLoader, Ext.util.Observable, {
    uiProviders: {},
    clearOnLoad: true,
    load: function (_2, _3) {
        if (this.clearOnLoad) {
            while (_2.firstChild) {
                _2.removeChild(_2.firstChild);
            }
        }
        if (_2.attributes.children) {
            var cs = _2.attributes.children;
            for (var i = 0, _6 = cs.length; i < _6; i++) {
                _2.appendChild(this.createNode(cs[i]));
            }
            if (typeof _3 == "function") {
                _3();
            }
        } else {
            if (this.dataUrl) {
                this.requestData(_2, _3);
            }
        }
    },
    getParams: function (_7) {
        var _8 = [],
            bp = this.baseParams;
        for (var _a in bp) {
            if (typeof bp[_a] != "function") {
                _8.push(encodeURIComponent(_a), "=", encodeURIComponent(bp[_a]), "&");
            }
        }
        _8.push("node=", encodeURIComponent(_7.id));
        return _8.join("");
    },
    requestData: function (_b, _c) {
        if (this.fireEvent("beforeload", this, _b, _c) !== false) {
            var _d = this.getParams(_b);
            var cb = {
                success: this.handleResponse,
                failure: this.handleFailure,
                scope: this,
                argument: {
                    callback: _c,
                    node: _b
                }
            };
            this.transId = Ext.lib.Ajax.request(this.requestMethod, this.dataUrl, cb, _d);
        } else {
            if (typeof _c == "function") {
                _c();
            }
        }
    },
    isLoading: function () {
        return this.transId ? true : false;
    },
    abort: function () {
        if (this.isLoading()) {
            Ext.lib.Ajax.abort(this.transId);
        }
    },
    createNode: function (_f) {
        if (this.applyLoader !== false) {
            _f.loader = this;
        }
        if (typeof _f.uiProvider == "string") {
            _f.uiProvider = this.uiProviders[_f.uiProvider] || eval(_f.uiProvider);
        }
        return (_f.leaf ? new Ext.tree.TreeNode(_f) : new Ext.tree.AsyncTreeNode(_f));
    },
    processResponse: function (_10, _11, _12) {
        var _13 = _10.responseText;
        try {
            var o = eval("(" + _13 + ")");
            for (var i = 0, len = o.length; i < len; i++) {
                var n = this.createNode(o[i]);
                if (n) {
                    _11.appendChild(n);
                }
            }
            if (typeof _12 == "function") {
                _12(this, _11);
            }
        } catch (e) {
            this.handleFailure(_10);
        }
    },
    handleResponse: function (_18) {
        this.transId = false;
        var a = _18.argument;
        this.processResponse(_18, a.node, a.callback);
        this.fireEvent("load", this, a.node, _18);
    },
    handleFailure: function (_1a) {
        this.transId = false;
        var a = _1a.argument;
        this.fireEvent("loadexception", this, a.node, _1a);
        if (typeof a.callback == "function") {
            a.callback(this, a.node);
        }
    }
});