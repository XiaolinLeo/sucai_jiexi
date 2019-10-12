;!function(window, $, undefined) {
    "use strict";

    var win = $(window),
        ready = {
            config: {},
            end: {},
            minIndex: 0,
            minLeft: [],
            btn: ['确定', '取消'],
            type: ['dialog', 'page', 'iframe', 'loading', 'tips'],
        };

    //默认内置方法。
    var dialog = {
        ie: function() {
            var agent = navigator.userAgent.toLowerCase();
            return (!!window.ActiveXObject || "ActiveXObject"in window) ? ((agent.match(/msie\s(\d+)/) || [])[1] || '11'//由于ie11并没有msie的标识
            ) : false;
        }(),

        index: 100000,

        alert: function(content, options, yes) {
            var type = typeof options === 'function';
            if (type)
                yes = options;
            return dialog.open($.extend({
                content: content,
                yes: yes
            }, type ? {} : options));
        },

        confirm: function(content, options, yes, cancel) {
            var type = typeof options === 'function';
            if (type) {
                cancel = yes;
                yes = options;
            }
            return dialog.open($.extend({
                content: content,
                btn: ready.btn,
                yes: yes,
                btn2: cancel
            }, type ? {} : options));
        },

        msg: function(content, options, end) {
            var type = typeof options === 'function'
              , rskin = ready.config.skin;
            var skin = (rskin ? rskin + ' ' + rskin + '-msg' : '') || 'sucaiDialog-dialog-msg';
            var anim = doms.anim.length - 1,
              icon = '<i class="adt-icon mr-1 icon-show-info"></i>';
              
            if (type)
                end = options;
            if(typeof content == 'object'){
               var msg = content.msg; 
               switch (content.code) {
                    case -1:
                        skin += ' dialog-msg-error';
                        icon = '<i class="adt-icon mr-1 icon-show-error"></i>';
                        break;
                    case 0:
                        skin += ' dialog-msg-info';
                        break;
                    case 1:
                        skin += ' dialog-msg-success';
                        icon = '<i class="adt-icon mr-1 icon-show-right"></i>';
                        break;
                    case 2:
                        skin += ' dialog-msg-loading';
                        icon = '<i class="adt-icon mr-1 rotating icon-show-loading"></i>';
                        break;
               }
            }else{
                var msg = content;
                skin += ' dialog-msg-info'; 
            }
            return dialog.open($.extend({
                content: icon+msg,
                time: 3000,
                shade: false,
                skin: skin,
                title: false,
                closeBtn: false,
                btn: false,
                resize: false,
                end: end
            }, (type && !ready.config.skin) ? {
                skin: skin + ' sucaiDialog-dialog-hui',
                anim: anim
            } : function() {
                options = options || {};
                if (options.icon === -1 || options.icon === undefined && !ready.config.skin) {
                    options.skin = skin + ' ' + (options.skin || 'sucaiDialog-dialog-hui');
                }
                return options;
            }()));
        },

        load: function(icon, options) {
            return dialog.open($.extend({
                type: 3,
                icon: icon || 0,
                resize: false,
                shade: 0.01
            }, options));
        },

        tips: function(content, follow, options) {
            return dialog.open($.extend({
                type: 4,
                content: [content, follow],
                closeBtn: false,
                time: 3000,
                shade: false,
                resize: false,
                fixed: false,
                maxWidth: 210
            }, options));
        }
    };

    var Class = function(setings) {
        var that = this;
        that.index = ++dialog.index;
        that.config = $.extend({}, that.config, ready.config, setings);
        document.body ? that.creat() : setTimeout(function() {
            that.creat();
        }, 30);
    };

    Class.pt = Class.prototype;

    //缓存常用字符
    var doms = ['sucaiDialog-dialog', '.sucaiDialog-dialog-title', '.sucaiDialog-dialog-main', '.sucaiDialog-dialog-dialog', 'sucaiDialog-dialog-iframe', 'sucaiDialog-dialog-content', 'sucaiDialog-dialog-btn', 'sucaiDialog-dialog-close'];
    doms.anim = ['dialog-anim-00', 'dialog-anim-01', 'dialog-anim-02', 'dialog-anim-03', 'dialog-anim-04', 'dialog-anim-05', 'dialog-anim-06'];

    //默认配置
    Class.pt.config = {
        type: 0,
        shade: 0.3,
        fixed: true,
        move: doms[1],
        title: '提示',
        offset: 'auto',
        area: 'auto',
        closeBtn: 1,
        time: 0,
        //0表示不自动关闭
        zIndex: 19891014,
        maxWidth: 360,
        anim: 0,
        isOutAnim: true,
        icon: -1,
        moveType: 1,
        resize: true,
        scrollbar: true,
        //是否允许浏览器滚动条
        tips: 2
    };

    //容器
    Class.pt.vessel = function(conType, callback) {
        var that = this
          , times = that.index
          , config = that.config;
        var zIndex = config.zIndex + times
          , titype = typeof config.title === 'object';
        var ismax = config.maxmin && (config.type === 1 || config.type === 2);
        var titleHTML = (config.title ? '<div class="sucaiDialog-dialog-title" style="' + (titype ? config.title[1] : '') + '">' + (titype ? config.title[0] : config.title) + '</div>' : '');

        config.zIndex = zIndex;
        callback([//遮罩
        config.shade ? ('<div class="sucaiDialog-dialog-shade" id="sucaiDialog-dialog-shade' + times + '" times="' + times + '" style="' + ('z-index:' + (zIndex - 1) + '; ') + '"></div>') : '',
        //主体
        '<div class="' + doms[0] + (' sucaiDialog-dialog-' + ready.type[config.type]) + (((config.type == 0 || config.type == 2) && !config.shade) ? ' sucaiDialog-dialog-border' : '') + ' ' + (config.skin || '') + '" id="' + doms[0] + times + '" type="' + ready.type[config.type] + '" times="' + times + '" showtime="' + config.time + '" conType="' + (conType ? 'object' : 'string') + '" style="z-index: ' + zIndex + '; width:' + config.area[0] + ';height:' + config.area[1] + (config.fixed ? '' : ';position:absolute;') + '">' + (conType && config.type != 2 ? '' : titleHTML) + '<div id="' + (config.id || '') + '" class="sucaiDialog-dialog-content' + ((config.type == 0 && config.icon !== -1) ? ' sucaiDialog-dialog-padding' : '') + (config.type == 3 ? ' sucaiDialog-dialog-loading' + config.icon : '') + '">' + (config.type == 0 && config.icon !== -1 ? '<i class="sucaiDialog-dialog-ico sucaiDialog-dialog-ico' + config.icon + '"></i>' : '') + (config.type == 1 && conType ? '' : (config.content || '')) + '</div>' + '<span class="sucaiDialog-dialog-setwin">' + function() {
            var closebtn = ismax ? '<a class="sucaiDialog-dialog-min" href="javascript:;"><cite></cite></a><a class="sucaiDialog-dialog-ico sucaiDialog-dialog-max" href="javascript:;"></a>' : '';
            config.closeBtn && (closebtn += '<a class="sucaiDialog-dialog-ico ' + doms[7] + ' ' + doms[7] + (config.title ? config.closeBtn : (config.type == 4 ? '1' : '2')) + '" href="javascript:;"></a>');
            return closebtn;
        }() + '</span>' + (config.btn ? function() {
            var button = '';
            typeof config.btn === 'string' && (config.btn = [config.btn]);
            for (var i = 0, len = config.btn.length; i < len; i++) {
                button += '<a href="javascript:;" class="btn btn-success btn-sm ' + doms[6] + '' + i + '">' + config.btn[i] + '</a>'
            }
            return '<div class="' + doms[6] + ' sucaiDialog-dialog-btn-' + (config.btnAlign || '') + '">' + button + '</div>'
        }() : '') + (config.resize ? '<span class="sucaiDialog-dialog-resize"></span>' : '') + '</div>'], titleHTML, $('<div class="sucaiDialog-dialog-move"></div>'));
        return that;
    }
    ;

    //创建骨架
    Class.pt.creat = function() {
        var that = this, config = that.config, times = that.index, nodeIndex, content = config.content, conType = typeof content === 'object', body = $('body');

        if (config.id && $('#' + config.id)[0])
            return;

        if (typeof config.area === 'string') {
            config.area = config.area === 'auto' ? ['', ''] : [config.area, ''];
        }

        //anim兼容旧版shift
        if (config.shift) {
            config.anim = config.shift;
        }

        if (dialog.ie == 6) {
            config.fixed = false;
        }

        switch (config.type) {
        case 0:
            config.btn = ('btn'in config) ? config.btn : ready.btn[0];
            dialog.closeAll('dialog');
            break;
        case 2:
            var content = config.content = conType ? config.content : [config.content || 'https://www.sucaiDialog.com', 'auto'];
            config.content = '<iframe scrolling="' + (config.content[1] || 'auto') + '" allowtransparency="true" id="' + doms[4] + '' + times + '" name="' + doms[4] + '' + times + '" onload="this.className=\'\';" class="sucaiDialog-dialog-load" frameborder="0" src="' + config.content[0] + '"></iframe>';
            break;
        case 3:
            delete config.title;
            delete config.closeBtn;
            config.icon === -1 && (config.icon === 0);
            dialog.closeAll('loading');
            break;
        case 4:
            conType || (config.content = [config.content, 'body']);
            config.follow = config.content[1];
            config.content = config.content[0] + '<i class="sucaiDialog-dialog-TipsG"></i>';
            delete config.title;
            config.tips = typeof config.tips === 'object' ? config.tips : [config.tips, true];
            config.tipsMore || dialog.closeAll('tips');
            break;
        }

        //建立容器
        that.vessel(conType, function(html, titleHTML, moveElem) {
            body.append(html[0]);
            conType ? function() {
                (config.type == 2 || config.type == 4) ? function() {
                    $('body').append(html[1]);
                }() : function() {
                    if (!content.parents('.' + doms[0])[0]) {
                        content.data('display', content.css('display')).show().addClass('sucaiDialog-dialog-wrap').wrap(html[1]);
                        $('#' + doms[0] + times).find('.' + doms[5]).before(titleHTML);
                    }
                }();
            }() : body.append(html[1]);
            $('.sucaiDialog-dialog-move')[0] || body.append(ready.moveElem = moveElem);
            that.dialogo = $('#' + doms[0] + times);
            config.scrollbar || doms.html.css('overflow', 'hidden').attr('dialog-full', times);
        }).auto(times);

        //遮罩
        $('#sucaiDialog-dialog-shade' + that.index).css({
            'background-color': config.shade[1] || '#000',
            'opacity': config.shade[0] || config.shade
        });

        config.type == 2 && dialog.ie == 6 && that.dialogo.find('iframe').attr('src', content[0]);

        //坐标自适应浏览器窗口尺寸
        config.type == 4 ? that.tips() : that.offset();
        if (config.fixed) {
            win.on('resize', function() {
                that.offset();
                (/^\d+%$/.test(config.area[0]) || /^\d+%$/.test(config.area[1])) && that.auto(times);
                config.type == 4 && that.tips();
            });
        }

        config.time <= 0 || setTimeout(function() {
            dialog.close(that.index)
        }, config.time);
        that.move().callback();

        //为兼容jQuery3.0的css动画影响元素尺寸计算
        if (doms.anim[config.anim]) {
            var animClass = 'dialog-anim ' + doms.anim[config.anim];
            that.dialogo.addClass(animClass).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
                $(this).removeClass(animClass);
            });
        }
        ;
        //记录关闭动画
        if (config.isOutAnim) {
            that.dialogo.data('isOutAnim', true);
        }
    }
    ;

    //自适应
    Class.pt.auto = function(index) {
        var that = this
          , config = that.config
          , dialogo = $('#' + doms[0] + index);

        if (config.area[0] === '' && config.maxWidth > 0) {
            //为了修复IE7下一个让人难以理解的bug
            if (dialog.ie && dialog.ie < 8 && config.btn) {
                dialogo.width(dialogo.innerWidth());
            }
            dialogo.outerWidth() > config.maxWidth && dialogo.width(config.maxWidth);
        }

        var area = [dialogo.innerWidth(), dialogo.innerHeight()]
          , titHeight = dialogo.find(doms[1]).outerHeight() || 0
          , btnHeight = dialogo.find('.' + doms[6]).outerHeight() || 0
          , setHeight = function(elem) {
            elem = dialogo.find(elem);
            elem.height(area[1] - titHeight - btnHeight - 2 * (parseFloat(elem.css('padding-top')) | 0));
        };

        switch (config.type) {
        case 2:
            setHeight('iframe');
            break;
        default:
            if (config.area[1] === '') {
                if (config.maxHeight > 0 && dialogo.outerHeight() > config.maxHeight) {
                    area[1] = config.maxHeight;
                    setHeight('.' + doms[5]);
                } else if (config.fixed && area[1] >= win.height()) {
                    area[1] = win.height();
                    setHeight('.' + doms[5]);
                }
            } else {
                setHeight('.' + doms[5]);
            }
            break;
        }
        ;
        return that;
    }
    ;

    //计算坐标
    Class.pt.offset = function() {
        var that = this
          , config = that.config
          , dialogo = that.dialogo;
        var area = [dialogo.outerWidth(), dialogo.outerHeight()];
        var type = typeof config.offset === 'object';
        that.offsetTop = (win.height() - area[1]) / 2;
        that.offsetLeft = (win.width() - area[0]) / 2;

        if (type) {
            that.offsetTop = config.offset[0];
            that.offsetLeft = config.offset[1] || that.offsetLeft;
        } else if (config.offset !== 'auto') {

            if (config.offset === 't') {
                //上
                that.offsetTop = 0;
            } else if (config.offset === 'r') {
                //右
                that.offsetLeft = win.width() - area[0];
            } else if (config.offset === 'b') {
                //下
                that.offsetTop = win.height() - area[1];
            } else if (config.offset === 'l') {
                //左
                that.offsetLeft = 0;
            } else if (config.offset === 'lt') {
                //左上角
                that.offsetTop = 0;
                that.offsetLeft = 0;
            } else if (config.offset === 'lb') {
                //左下角
                that.offsetTop = win.height() - area[1];
                that.offsetLeft = 0;
            } else if (config.offset === 'rt') {
                //右上角
                that.offsetTop = 0;
                that.offsetLeft = win.width() - area[0];
            } else if (config.offset === 'rb') {
                //右下角
                that.offsetTop = win.height() - area[1];
                that.offsetLeft = win.width() - area[0];
            } else {
                that.offsetTop = config.offset;
            }

        }

        if (!config.fixed) {
            that.offsetTop = /%$/.test(that.offsetTop) ? win.height() * parseFloat(that.offsetTop) / 100 : parseFloat(that.offsetTop);
            that.offsetLeft = /%$/.test(that.offsetLeft) ? win.width() * parseFloat(that.offsetLeft) / 100 : parseFloat(that.offsetLeft);
            that.offsetTop += win.scrollTop();
            that.offsetLeft += win.scrollLeft();
        }

        if (dialogo.attr('minLeft')) {
            that.offsetTop = win.height() - (dialogo.find(doms[1]).outerHeight() || 0);
            that.offsetLeft = dialogo.css('left');
        }

        dialogo.css({
            top: that.offsetTop,
            left: that.offsetLeft
        });
    }
    ;

    //Tips
    Class.pt.tips = function() {
        var that = this
          , config = that.config
          , dialogo = that.dialogo;
        var layArea = [dialogo.outerWidth(), dialogo.outerHeight()]
          , follow = $(config.follow);
        if (!follow[0])
            follow = $('body');
        var goal = {
            width: follow.outerWidth(),
            height: follow.outerHeight(),
            top: follow.offset().top,
            left: follow.offset().left
        }
          , tipsG = dialogo.find('.sucaiDialog-dialog-TipsG');

        var guide = config.tips[0];
        config.tips[1] || tipsG.remove();

        goal.autoLeft = function() {
            if (goal.left + layArea[0] - win.width() > 0) {
                goal.tipLeft = goal.left + goal.width - layArea[0];
                tipsG.css({
                    right: 12,
                    left: 'auto'
                });
            } else {
                goal.tipLeft = goal.left;
            }
            ;
        }
        ;

        //辨别tips的方位
        goal.where = [function() {
            //上        
            goal.autoLeft();
            goal.tipTop = goal.top - layArea[1] - 10;
            tipsG.removeClass('sucaiDialog-dialog-TipsB').addClass('sucaiDialog-dialog-TipsT').css('border-right-color', config.tips[1]);
        }
        , function() {
            //右
            goal.tipLeft = goal.left + goal.width + 10;
            goal.tipTop = goal.top;
            tipsG.removeClass('sucaiDialog-dialog-TipsL').addClass('sucaiDialog-dialog-TipsR').css('border-bottom-color', config.tips[1]);
        }
        , function() {
            //下
            goal.autoLeft();
            goal.tipTop = goal.top + goal.height + 10;
            tipsG.removeClass('sucaiDialog-dialog-TipsT').addClass('sucaiDialog-dialog-TipsB').css('border-right-color', config.tips[1]);
        }
        , function() {
            //左
            goal.tipLeft = goal.left - layArea[0] - 10;
            goal.tipTop = goal.top;
            tipsG.removeClass('sucaiDialog-dialog-TipsR').addClass('sucaiDialog-dialog-TipsL').css('border-bottom-color', config.tips[1]);
        }
        ];
        goal.where[guide - 1]();

        /* 8*2为小三角形占据的空间 */
        if (guide === 1) {
            goal.top - (win.scrollTop() + layArea[1] + 8 * 2) < 0 && goal.where[2]();
        } else if (guide === 2) {
            win.width() - (goal.left + goal.width + layArea[0] + 8 * 2) > 0 || goal.where[3]()
        } else if (guide === 3) {
            (goal.top - win.scrollTop() + goal.height + layArea[1] + 8 * 2) - win.height() > 0 && goal.where[0]();
        } else if (guide === 4) {
            layArea[0] + 8 * 2 - goal.left > 0 && goal.where[1]()
        }

        dialogo.find('.' + doms[5]).css({
            'background-color': config.tips[1],
            'padding-right': (config.closeBtn ? '30px' : '')
        });
        dialogo.css({
            left: goal.tipLeft - (config.fixed ? win.scrollLeft() : 0),
            top: goal.tipTop - (config.fixed ? win.scrollTop() : 0)
        });
    }

    //拖拽层
    Class.pt.move = function() {
        var that = this
          , config = that.config
          , _DOC = $(document)
          , dialogo = that.dialogo
          , moveElem = dialogo.find(config.move)
          , resizeElem = dialogo.find('.sucaiDialog-dialog-resize')
          , dict = {};

        if (config.move) {
            moveElem.css('cursor', 'move');
        }

        moveElem.on('mousedown', function(e) {
            e.preventDefault();
            if (config.move) {
                dict.moveStart = true;
                dict.offset = [e.clientX - parseFloat(dialogo.css('left')), e.clientY - parseFloat(dialogo.css('top'))];
                ready.moveElem.css('cursor', 'move').show();
            }
        });

        resizeElem.on('mousedown', function(e) {
            e.preventDefault();
            dict.resizeStart = true;
            dict.offset = [e.clientX, e.clientY];
            dict.area = [dialogo.outerWidth(), dialogo.outerHeight()];
            ready.moveElem.css('cursor', 'se-resize').show();
        });

        _DOC.on('mousemove', function(e) {

            //拖拽移动
            if (dict.moveStart) {
                var X = e.clientX - dict.offset[0]
                  , Y = e.clientY - dict.offset[1]
                  , fixed = dialogo.css('position') === 'fixed';

                e.preventDefault();

                dict.stX = fixed ? 0 : win.scrollLeft();
                dict.stY = fixed ? 0 : win.scrollTop();

                //控制元素不被拖出窗口外
                if (!config.moveOut) {
                    var setRig = win.width() - dialogo.outerWidth() + dict.stX
                      , setBot = win.height() - dialogo.outerHeight() + dict.stY;
                    X < dict.stX && (X = dict.stX);
                    X > setRig && (X = setRig);
                    Y < dict.stY && (Y = dict.stY);
                    Y > setBot && (Y = setBot);
                }

                dialogo.css({
                    left: X,
                    top: Y
                });
            }

            //Resize
            if (config.resize && dict.resizeStart) {
                var X = e.clientX - dict.offset[0]
                  , Y = e.clientY - dict.offset[1];

                e.preventDefault();

                dialog.style(that.index, {
                    width: dict.area[0] + X,
                    height: dict.area[1] + Y
                })
                dict.isResize = true;
                config.resizing && config.resizing(dialogo);
            }
        }).on('mouseup', function(e) {
            if (dict.moveStart) {
                delete dict.moveStart;
                ready.moveElem.hide();
                config.moveEnd && config.moveEnd(dialogo);
            }
            if (dict.resizeStart) {
                delete dict.resizeStart;
                ready.moveElem.hide();
            }
        });

        return that;
    }
    ;

    Class.pt.callback = function() {
        var that = this
          , dialogo = that.dialogo
          , config = that.config;
        that.openDialog();
        if (config.success) {
            if (config.type == 2) {
                dialogo.find('iframe').on('load', function() {
                    config.success(dialogo, that.index);
                });
            } else {
                config.success(dialogo, that.index);
            }
        }
        dialog.ie == 6 && that.IE6(dialogo);

        //按钮
        dialogo.find('.' + doms[6]).children('a').on('click', function() {
            var index = $(this).index();
            if (index === 0) {
                if (config.yes) {
                    config.yes(that.index, dialogo)
                } else if (config['btn1']) {
                    config['btn1'](that.index, dialogo)
                } else {
                    dialog.close(that.index);
                }
            } else {
                var close = config['btn' + (index + 1)] && config['btn' + (index + 1)](that.index, dialogo);
                close === false || dialog.close(that.index);
            }
        });

        //取消
        function cancel() {
            var close = config.cancel && config.cancel(that.index, dialogo);
            close === false || dialog.close(that.index);
        }

        //右上角关闭回调
        dialogo.find('.' + doms[7]).on('click', cancel);

        //点遮罩关闭
        if (config.shadeClose) {
            $('#sucaiDialog-dialog-shade' + that.index).on('click', function() {
                dialog.close(that.index);
            });
        }

        //最小化
        dialogo.find('.sucaiDialog-dialog-min').on('click', function() {
            var min = config.min && config.min(dialogo);
            min === false || dialog.min(that.index, config);
        });

        //全屏/还原
        dialogo.find('.sucaiDialog-dialog-max').on('click', function() {
            if ($(this).hasClass('sucaiDialog-dialog-maxmin')) {
                dialog.restore(that.index);
                config.restore && config.restore(dialogo);
            } else {
                dialog.full(that.index, config);
                setTimeout(function() {
                    config.full && config.full(dialogo);
                }, 100);
            }
        });

        config.end && (ready.end[that.index] = config.end);
    }
    ;

    //for ie6 恢复select
    ready.reselect = function() {
        $.each($('select'), function(index, value) {
            var sthis = $(this);
            if (!sthis.parents('.' + doms[0])[0]) {
                (sthis.attr('dialog') == 1 && $('.' + doms[0]).length < 1) && sthis.removeAttr('dialog').show();
            }
            sthis = null;
        });
    }
    ;

    Class.pt.IE6 = function(dialogo) {
        //隐藏select
        $('select').each(function(index, value) {
            var sthis = $(this);
            if (!sthis.parents('.' + doms[0])[0]) {
                sthis.css('display') === 'none' || sthis.attr({
                    'dialog': '1'
                }).hide();
            }
            sthis = null;
        });
    }
    ;

    //需依赖原型的对外方法
    Class.pt.openDialog = function() {
        var that = this;

        //置顶当前窗口
        dialog.zIndex = that.config.zIndex;
        dialog.setTop = function(dialogo) {
            var setZindex = function() {
                dialog.zIndex++;
                dialogo.css('z-index', dialog.zIndex + 1);
            };
            dialog.zIndex = parseInt(dialogo[0].style.zIndex);
            dialogo.on('mousedown', setZindex);
            return dialog.zIndex;
        }
        ;
    }
    ;

    ready.record = function(dialogo) {
        var area = [dialogo.width(), dialogo.height(), dialogo.position().top, dialogo.position().left + parseFloat(dialogo.css('margin-left'))];
        dialogo.find('.sucaiDialog-dialog-max').addClass('sucaiDialog-dialog-maxmin');
        dialogo.attr({
            area: area
        });
    }
    ;

    ready.rescollbar = function(index) {
        if (doms.html.attr('dialog-full') == index) {
            if (doms.html[0].style.removeProperty) {
                doms.html[0].style.removeProperty('overflow');
            } else {
                doms.html[0].style.removeAttribute('overflow');
            }
            doms.html.removeAttr('dialog-full');
        }
    }
    ;

    /** 内置成员 */

    window.dialog = dialog;

    //获取子iframe的DOM
    dialog.getChildFrame = function(selector, index) {
        index = index || $('.' + doms[4]).attr('times');
        return $('#' + doms[0] + index).find('iframe').contents().find(selector);
    }
    ;

    //得到当前iframe层的索引，子iframe时使用
    dialog.getFrameIndex = function(name) {
        return $('#' + name).parents('.' + doms[4]).attr('times');
    }
    ;

    //iframe层自适应宽高
    dialog.iframeAuto = function(index) {
        if (!index)
            return;
        var heg = dialog.getChildFrame('html', index).outerHeight();
        var dialogo = $('#' + doms[0] + index);
        var titHeight = dialogo.find(doms[1]).outerHeight() || 0;
        var btnHeight = dialogo.find('.' + doms[6]).outerHeight() || 0;
        dialogo.css({
            height: heg + titHeight + btnHeight
        });
        dialogo.find('iframe').css({
            height: heg
        });
    }
    ;

    //重置iframe url
    dialog.iframeSrc = function(index, url) {
        $('#' + doms[0] + index).find('iframe').attr('src', url);
    }
    ;

    //设定层的样式
    dialog.style = function(index, options, limit) {
        var dialogo = $('#' + doms[0] + index)
          , contElem = dialogo.find('.sucaiDialog-dialog-content')
          , type = dialogo.attr('type')
          , titHeight = dialogo.find(doms[1]).outerHeight() || 0
          , btnHeight = dialogo.find('.' + doms[6]).outerHeight() || 0
          , minLeft = dialogo.attr('minLeft');

        if (type === ready.type[3] || type === ready.type[4]) {
            return;
        }

        if (!limit) {
            if (parseFloat(options.width) <= 260) {
                options.width = 260;
            }
            ;
            if (parseFloat(options.height) - titHeight - btnHeight <= 64) {
                options.height = 64 + titHeight + btnHeight;
            }
            ;
        }

        dialogo.css(options);
        btnHeight = dialogo.find('.' + doms[6]).outerHeight();

        if (type === ready.type[2]) {
            dialogo.find('iframe').css({
                height: parseFloat(options.height) - titHeight - btnHeight
            });
        } else {
            contElem.css({
                height: parseFloat(options.height) - titHeight - btnHeight - parseFloat(contElem.css('padding-top')) - parseFloat(contElem.css('padding-bottom'))
            })
        }
    }
    ;

    //最小化
    dialog.min = function(index, options) {
        var dialogo = $('#' + doms[0] + index)
          , titHeight = dialogo.find(doms[1]).outerHeight() || 0
          , left = dialogo.attr('minLeft') || (181 * ready.minIndex) + 'px'
          , position = dialogo.css('position');

        ready.record(dialogo);

        if (ready.minLeft[0]) {
            left = ready.minLeft[0];
            ready.minLeft.shift();
        }

        dialogo.attr('position', position);

        dialog.style(index, {
            width: 180,
            height: titHeight,
            left: left,
            top: win.height() - titHeight,
            position: 'fixed',
            overflow: 'hidden'
        }, true);

        dialogo.find('.sucaiDialog-dialog-min').hide();
        dialogo.attr('type') === 'page' && dialogo.find(doms[4]).hide();
        ready.rescollbar(index);

        if (!dialogo.attr('minLeft')) {
            ready.minIndex++;
        }
        dialogo.attr('minLeft', left);
    }
    ;

    //还原
    dialog.restore = function(index) {
        var dialogo = $('#' + doms[0] + index)
          , area = dialogo.attr('area').split(',');
        var type = dialogo.attr('type');
        dialog.style(index, {
            width: parseFloat(area[0]),
            height: parseFloat(area[1]),
            top: parseFloat(area[2]),
            left: parseFloat(area[3]),
            position: dialogo.attr('position'),
            overflow: 'visible'
        }, true);
        dialogo.find('.sucaiDialog-dialog-max').removeClass('sucaiDialog-dialog-maxmin');
        dialogo.find('.sucaiDialog-dialog-min').show();
        dialogo.attr('type') === 'page' && dialogo.find(doms[4]).show();
        ready.rescollbar(index);
    }
    ;

    //全屏
    dialog.full = function(index) {
        var dialogo = $('#' + doms[0] + index), timer;
        ready.record(dialogo);
        if (!doms.html.attr('dialog-full')) {
            doms.html.css('overflow', 'hidden').attr('dialog-full', index);
        }
        clearTimeout(timer);
        timer = setTimeout(function() {
            var isfix = dialogo.css('position') === 'fixed';
            dialog.style(index, {
                top: isfix ? 0 : win.scrollTop(),
                left: isfix ? 0 : win.scrollLeft(),
                width: win.width(),
                height: win.height()
            }, true);
            dialogo.find('.sucaiDialog-dialog-min').hide();
        }, 100);
    }
    ;

    //改变title
    dialog.title = function(name, index) {
        var title = $('#' + doms[0] + (index || dialog.index)).find(doms[1]);
        title.html(name);
    }
    ;

    //关闭dialog总方法
    dialog.close = function(index) {
        var dialogo = $('#' + doms[0] + index)
          , type = dialogo.attr('type')
          , closeAnim = 'dialog-anim-close';
        if (!dialogo[0])
            return;
        var WRAP = 'sucaiDialog-dialog-wrap'
          , remove = function() {
            if (type === ready.type[1] && dialogo.attr('conType') === 'object') {
                dialogo.children(':not(.' + doms[5] + ')').remove();
                var wrap = dialogo.find('.' + WRAP);
                for (var i = 0; i < 2; i++) {
                    wrap.unwrap();
                }
                wrap.css('display', wrap.data('display')).removeClass(WRAP);
            } else {
                //低版本IE 回收 iframe
                if (type === ready.type[2]) {
                    try {
                        var iframe = $('#' + doms[4] + index)[0];
                        iframe.contentWindow.document.write('');
                        iframe.contentWindow.close();
                        dialogo.find('.' + doms[5])[0].removeChild(iframe);
                    } catch (e) {}
                }
                dialogo[0].innerHTML = '';
                dialogo.remove();
            }
            typeof ready.end[index] === 'function' && ready.end[index]();
            delete ready.end[index];
        };

        if (dialogo.data('isOutAnim')) {
            dialogo.addClass('dialog-anim ' + closeAnim);
        }

        $('#sucaiDialog-dialog-moves, #sucaiDialog-dialog-shade' + index).remove();
        dialog.ie == 6 && ready.reselect();
        ready.rescollbar(index);
        if (dialogo.attr('minLeft')) {
            ready.minIndex--;
            ready.minLeft.push(dialogo.attr('minLeft'));
        }

        if ((dialog.ie && dialog.ie < 10) || !dialogo.data('isOutAnim')) {
            remove()
        } else {
            setTimeout(function() {
                remove();
            }, 200);
        }
    }
    ;

    //关闭所有层
    dialog.closeAll = function(type) {
        $.each($('.' + doms[0]), function() {
            var othis = $(this);
            var is = type ? (othis.attr('type') === type) : 1;
            is && dialog.close(othis.attr('times'));
            is = null;
        });
    }
    ;

    var cache = dialog.cache || {}
      , skin = function(type) {
        return (cache.skin ? (' ' + cache.skin + ' ' + cache.skin + '-' + type) : '');
    };

    //仿系统prompt
    dialog.prompt = function(options, yes) {
        var style = '';
        options = options || {};

        if (typeof options === 'function')
            yes = options;

        if (options.area) {
            var area = options.area;
            style = 'style="width: ' + area[0] + '; height: ' + area[1] + ';"';
            delete options.area;
        }
        var prompt, content = options.formType == 2 ? '<textarea class="sucaiDialog-dialog-input"' + style + '>' + (options.value || '') + '</textarea>' : function() {
            return '<input type="' + (options.formType == 1 ? 'password' : 'text') + '" class="sucaiDialog-dialog-input" value="' + (options.value || '') + '">';
        }();

        var success = options.success;
        delete options.success;

        return dialog.open($.extend({
            type: 1,
            btn: ['确定', '取消'],
            content: content,
            skin: 'sucaiDialog-dialog-prompt' + skin('prompt'),
            maxWidth: win.width(),
            success: function(dialogo) {
                prompt = dialogo.find('.sucaiDialog-dialog-input');
                prompt.focus();
                typeof success === 'function' && success(dialogo);
            },
            resize: false,
            yes: function(index) {
                var value = prompt.val();
                if (value === '') {
                    prompt.focus();
                } else if (value.length > (options.maxlength || 500)) {
                    dialog.tips('最多输入' + (options.maxlength || 500) + '个字数', prompt, {
                        tips: 1
                    });
                } else {
                    yes && yes(value, index, prompt);
                }
            }
        }, options));
    }
    ;

    //tab层
    dialog.tab = function(options) {
        options = options || {};

        var tab = options.tab || {}
          , THIS = 'sucaiDialog-this'
          , success = options.success;

        delete options.success;

        return dialog.open($.extend({
            type: 1,
            skin: 'sucaiDialog-dialog-tab' + skin('tab'),
            resize: false,
            title: function() {
                var len = tab.length
                  , ii = 1
                  , str = '';
                if (len > 0) {
                    str = '<span class="' + THIS + '">' + tab[0].title + '</span>';
                    for (; ii < len; ii++) {
                        str += '<span>' + tab[ii].title + '</span>';
                    }
                }
                return str;
            }(),
            content: '<ul class="sucaiDialog-dialog-tabmain">' + function() {
                var len = tab.length
                  , ii = 1
                  , str = '';
                if (len > 0) {
                    str = '<li class="sucaiDialog-dialog-tabli ' + THIS + '">' + (tab[0].content || 'no content') + '</li>';
                    for (; ii < len; ii++) {
                        str += '<li class="sucaiDialog-dialog-tabli">' + (tab[ii].content || 'no  content') + '</li>';
                    }
                }
                return str;
            }() + '</ul>',
            success: function(dialogo) {
                var btn = dialogo.find('.sucaiDialog-dialog-title').children();
                var main = dialogo.find('.sucaiDialog-dialog-tabmain').children();
                btn.on('mousedown', function(e) {
                    e.stopPropagation ? e.stopPropagation() : e.cancelBubble = true;
                    var othis = $(this)
                      , index = othis.index();
                    othis.addClass(THIS).siblings().removeClass(THIS);
                    main.eq(index).show().siblings().hide();
                    typeof options.change === 'function' && options.change(index);
                });
                typeof success === 'function' && success(dialogo);
            }
        }, options));
    }
    ;

    //相册层
    dialog.photos = function(options, loop, key) {
        var dict = {};
        options = options || {};
        if (!options.photos)
            return;
        var type = options.photos.constructor === Object;
        var photos = type ? options.photos : {}
          , data = photos.data || [];
        var start = photos.start || 0;
        dict.imgIndex = (start | 0) + 1;

        options.img = options.img || 'img';

        var success = options.success;
        delete options.success;

        if (!type) {
            //页面直接获取
            var parent = $(options.photos)
              , pushData = function() {
                data = [];
                parent.find(options.img).each(function(index) {
                    var othis = $(this);
                    othis.attr('dialog-index', index);
                    data.push({
                        alt: othis.attr('alt'),
                        pid: othis.attr('dialog-pid'),
                        src: othis.attr('dialog-src') || othis.attr('src'),
                        thumb: othis.attr('src')
                    });
                })
            };

            pushData();

            if (data.length === 0)
                return;

            loop || parent.on('click', options.img, function() {
                var othis = $(this)
                  , index = othis.attr('dialog-index');
                dialog.photos($.extend(options, {
                    photos: {
                        start: index,
                        data: data,
                        tab: options.tab
                    },
                    full: options.full
                }), true);
                pushData();
            })

            //不直接弹出
            if (!loop)
                return;

        } else if (data.length === 0) {
            return dialog.msg('没有图片');
        }

        //上一张
        dict.imgprev = function(key) {
            dict.imgIndex--;
            if (dict.imgIndex < 1) {
                dict.imgIndex = data.length;
            }
            dict.tabimg(key);
        }
        ;

        //下一张
        dict.imgnext = function(key, errorMsg) {
            dict.imgIndex++;
            if (dict.imgIndex > data.length) {
                dict.imgIndex = 1;
                if (errorMsg) {
                    return
                }
                ;
            }
            dict.tabimg(key)
        }
        ;

        //方向键
        dict.keyup = function(event) {
            if (!dict.end) {
                var code = event.keyCode;
                event.preventDefault();
                if (code === 37) {
                    dict.imgprev(true);
                } else if (code === 39) {
                    dict.imgnext(true);
                } else if (code === 27) {
                    dialog.close(dict.index);
                }
            }
        }

        //切换
        dict.tabimg = function(key) {
            if (data.length <= 1)
                return;
            photos.start = dict.imgIndex - 1;
            dialog.close(dict.index);
            return dialog.photos(options, true, key);
            setTimeout(function() {
                dialog.photos(options, true, key);
            }, 200);
        }

        //一些动作
        dict.event = function() {
            dict.bigimg.hover(function() {
                dict.imgsee.show();
            }, function() {
                dict.imgsee.hide();
            });

            dict.bigimg.find('.sucaiDialog-dialog-imgprev').on('click', function(event) {
                event.preventDefault();
                dict.imgprev();
            });

            dict.bigimg.find('.sucaiDialog-dialog-imgnext').on('click', function(event) {
                event.preventDefault();
                dict.imgnext();
            });

            $(document).on('keyup', dict.keyup);
        }
        ;

        //图片预加载
        function loadImage(url, callback, error) {
            var img = new Image();
            img.src = url;
            if (img.complete) {
                return callback(img);
            }
            img.onload = function() {
                img.onload = null;
                callback(img);
            }
            ;
            img.onerror = function(e) {
                img.onerror = null;
                error(e);
            }
            ;
        }
        ;
        dict.loadi = dialog.load(1, {
            shade: 'shade'in options ? false : 0.9,
            scrollbar: false
        });

        loadImage(data[start].src, function(img) {
            dialog.close(dict.loadi);
            dict.index = dialog.open($.extend({
                type: 1,
                id: 'sucaiDialog-dialog-photos',
                area: function() {
                    var imgarea = [img.width, img.height];
                    var winarea = [$(window).width() - 100, $(window).height() - 100];

                    //如果 实际图片的宽或者高比 屏幕大（那么进行缩放）
                    if (!options.full && (imgarea[0] > winarea[0] || imgarea[1] > winarea[1])) {
                        var wh = [imgarea[0] / winarea[0], imgarea[1] / winarea[1]];
                        //取宽度缩放比例、高度缩放比例
                        if (wh[0] > wh[1]) {
                            //取缩放比例最大的进行缩放
                            imgarea[0] = imgarea[0] / wh[0];
                            imgarea[1] = imgarea[1] / wh[0];
                        } else if (wh[0] < wh[1]) {
                            imgarea[0] = imgarea[0] / wh[1];
                            imgarea[1] = imgarea[1] / wh[1];
                        }
                    }

                    return [imgarea[0] + 'px', imgarea[1] + 'px'];
                }(),
                title: false,
                shade: 0.9,
                shadeClose: true,
                closeBtn: false,
                move: '.sucaiDialog-dialog-phimg img',
                moveType: 1,
                scrollbar: false,
                moveOut: true,
                //anim: Math.random()*5|0,
                isOutAnim: false,
                skin: 'sucaiDialog-dialog-photos' + skin('photos'),
                content: '<div class="sucaiDialog-dialog-phimg">' + '<img src="' + data[start].src + '" alt="' + (data[start].alt || '') + '" dialog-pid="' + data[start].pid + '">' + '<div class="sucaiDialog-dialog-imgsee">' + (data.length > 1 ? '<span class="sucaiDialog-dialog-imguide"><a href="javascript:;" class="sucaiDialog-dialog-iconext sucaiDialog-dialog-imgprev"></a><a href="javascript:;" class="sucaiDialog-dialog-iconext sucaiDialog-dialog-imgnext"></a></span>' : '') + '<div class="sucaiDialog-dialog-imgbar" style="display:' + (key ? 'block' : '') + '"><span class="sucaiDialog-dialog-imgtit"><a href="javascript:;">' + (data[start].alt || '') + '</a><em>' + dict.imgIndex + '/' + data.length + '</em></span></div>' + '</div>' + '</div>',
                success: function(dialogo, index) {
                    dict.bigimg = dialogo.find('.sucaiDialog-dialog-phimg');
                    dict.imgsee = dialogo.find('.sucaiDialog-dialog-imguide,.sucaiDialog-dialog-imgbar');
                    dict.event(dialogo);
                    options.tab && options.tab(data[start], dialogo);
                    typeof success === 'function' && success(dialogo);
                },
                end: function() {
                    dict.end = true;
                    $(document).off('keyup', dict.keyup);
                }
            }, options));
        }, function() {
            dialog.close(dict.loadi);
            dialog.msg('当前图片地址异常<br>是否继续查看下一张？', {
                time: 30000,
                btn: ['下一张', '不看了'],
                yes: function() {
                    data.length > 1 && dict.imgnext(true, true);
                }
            });
        });
    }

    doms.html = $('html');
    dialog.open = function(deliver) {
        var o = new Class(deliver);
        return o.index;
    }

}(window,jQuery);


$(function(){
    if($('.Clipboard').length > 0){
        $.getScript('static/js/clipboard.min.js',function(){
            var clipboard = new ClipboardJS('.Clipboard');
            clipboard.on('success', function(e) {
                dialog.msg({code:1,msg:'数据复制成功'});
            });
        })
    }
    $(document)
        .on('click','.custom-radio,.custom-checkbox',function(){
            var input = $(this).find('input.custom-control-input'),
                data = $(this).data();
            if(radio_checkbox_before(input,data) == false) return false;
            input.prop('checked',input.attr('type') == 'radio' ? true : (input.prop('checked') ? false : true));
            radio_checkbox_after(input,data);
            return false;
        })
        .on('mouseover','[data-toggle="tooltip"]',function(){
            $(this).tooltip('show');
        })
        .on('mouseout','[data-toggle="tooltip"]',function(){
            $(this).tooltip('hide');
        })
        .on('click','.btn-submit,button[type="submit"]',function(){
            var form = $(this).data('form') || $(this).parents('form');
            if(form.length == 0 || $(this).hasClass('btn-disabled')){
                return false;
            }
            $(this).prop('disabled',true);
            form.data('mode',$(this).data('mode') || '');

            var before = $(this).data('before') || '';
            if(before && typeof window[before] === 'function'){
                if(window[before](form,$(this)) === false){
                    $(this).prop('disabled',false);
                    return false;
                }
            }
            if($(this).hasClass('ajax-post') || form.hasClass('ajax-post')){
                ajax_post(form,$(this),false);
                return false;
            }
            return form.submit();
        })
        .on('click','.ajax-link',function(){
            ajax_link($(this),false);
            return false;
        })
        .on('change','.ajax-select select',function(){
            ajax_select($(this));
            return false;
        })
        .on('click','.ajax-page>.pagination a',function(){
            ajax_page($(this));
            return false;
        })
        .on('click','.btn-location',function(){
            var location = $(this).data('location');
            $('html,body').animate({scrollTop: $(location).offset().top}, 800);
        })
})

function ajax_post(form,btn,sure){
    if(form.hasClass('lock-form')){
        return dialog.msg({code:-1,msg:'数据提交中,请稍后...'});
    }
    var data = form.data();
    if(data['mode'] == 'confirm' && !sure){
        var post_i = dialog.confirm(data['notic'] || '当前操作不可逆，您确定要这么做吗？', {
            btn: ['确定','取消'],
            title:data['title'] || '操作提示',
        }, function(){
            dialog.close(post_i);
            ajax_post(form,btn,true);
        });
        return false;
    }
    var formData = new FormData(form[0]);
    if(data['before'] && typeof window[data['before']] === 'function'){
    	var call = window[data['before']](form,btn,formData);
        if(call === false){
            btn.prop('disabled',false);
            return false;
        }else if(typeof call === 'object'){
        	formData = call;
        }
    }
    form.addClass('lock-form');
    $.ajax({
        url:form.attr('action') || window.location.href,
        type:'POST',
        data:formData,
        dataType:'json',
        cache: false,
        timeout:60000,
        contentType: false,
        processData: false,
        success:function(s){
            if(data['after'] && typeof window[data['after']] === 'function'){
                if(window[data['after']](form,s,btn) === false){
                    return false;
                }
            }
            var jump = s.url || window.location.href;
            if(s.wait <= 0){
                window.location.href = jump;
                return false;
            }
            if(s.url && s.code != -1){
                setTimeout(function() {
                    window.location.href = jump;
                }, s.wait*1000);
            }
            if(s.msg){
                dialog.msg(s);
            }
        },
        complete:function(request, status){
            form.removeClass('lock-form');
            btn.prop('disabled',false);
            if(status == 'error'){
                dialog.msg({code:-1,msg:'页面错误，请联系管理员！'});
            }else if(status == 'timeout'){
                dialog.msg({code:-1,msg:'数据提交超时，请稍后再试！'});
            }
        }
    });
}

function ajax_link(obj,sure){
    if(obj.hasClass('ajax-disabled')){
        return dialog.msg({code:-1,msg:'请刷新页面后重试！'});
    }
    var data = obj.data();
    if(typeof data['param'] != 'object'){
        data['param'] = data['param'] ? eval('('+data['param']+')') : {};
    }
    if(data['mode'] == 'confirm' && !sure){
        var link_d = dialog.confirm(data['notic']  || '当前操作不可逆，您确定要这么做吗？', {
            btn: [data['yes'] || '确定',data['no'] || '取消'],
            title:data['title'] || obj.attr('title') || '操作提示',
        }, function(){
            dialog.close(link_d);
            ajax_link(obj,true);
        });
        return false;
    }
    obj.addClass('ajax-disabled');
    $.ajax({
        url:data['href'] || obj.attr('href'),
        type:'POST',
        data:data['param'],
        dataType:'json',
        cache: false,
        timeout:30000,
        success:function(s){
            if(data['after'] && typeof window[data['after']] === 'function'){
                if(window[data['after']](obj,s) === false){
                    return false;
                }
            }
            if(s.url){
                if(s.wait <= 0){
                    return window.location.href = s.url;
                }
                setTimeout(function() {
                    window.location.href = s.url;
                }, s.wait*1000);
            }
            if(s.msg){
                dialog.msg(s);
            }
        },
        complete:function(request, status){
            obj.removeClass('ajax-disabled');
            if(status == 'error'){
                dialog.msg({code:-1,msg:'页面错误，请联系管理员！'});
            }else if(status == 'timeout'){
                dialog.msg({code:-1,msg:'数据提交超时，请稍后再试！'});
            }
        }
    });
}

function ajax_select(obj){

}

function ajax_page(obj){
    var data = obj.parents('.ajax-page').data();
    $.ajax({
        url:obj.attr('href') || obj.data('href'),
        type:data['ajaxType'] || 'POST',
        cache: false,
        success:function(s){
            $s = $('<code>').append($(s));
            var html = $s.find(data['pageBox']).length <= 0 ? s : $s.find(data['pageBox']).html();
            obj.parents('.ajax-page').html($s.find('[data-page-box="'+data['pageBox']+'"]').html());
            if(data['pageType'] == 'append'){
                return $(data['pageBox']).append(html);
            }
            $(data['pageBox']).html(html);
            $('html,body').animate({scrollTop: $(data['pageBox']).offset().top}, 800);
        }
    })
}

function radio_checkbox_before(input,data){
    if(input.prop('disabled') || input.prop('readonly') || data['disabled'] == 'true' || data['readonly'] == 'true' ){
        return false;
    }
    if(data['before'] && typeof window[data['before']] === 'function'){
        return window[data['before']](input,data);
    }
    return true;
}

function radio_checkbox_after(input,data){
    if(data['after'] && typeof window[data['after']] === 'function'){
        window[data['after']](input,data);
    }
}