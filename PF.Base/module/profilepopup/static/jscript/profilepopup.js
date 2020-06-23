/**
 * @package Ynfbpp
 * @category Extenstion
 * @author YouNet Company
 */

var ynfbpp = {
    iOpeningDelayTime: 300,
    iClosingDelayTime: 300,
    sEnableCache: true,
    pt: [],
    ele: 0,
    href: 0,
    timeoutId: 0,
    cached: {},
    dir: {cx: 0, cy: 0},
    isShowing: 0,
    rewriteData: null,
    ignoreClasses: ['sub_section_menu', 'item_bar_action_holder', 'js_hover_title', 'has_drop_down',
        'no_ajax_link', 'ajax_link', 'first', 'feed_permalink', 'p_bottom_5','ynmenu-listing', 'dropdown-panel-body'],
    notIgnoreClassesOfGrandElement: ['activity_feed_content_image', 'activity_feed_content_link'],
    ignoreClassesOfParentElement: ['item-button-step-container', 'public_message', 'pager_links', 'title',
        'comment_mini_link','comment_mini_link_like', 'profile_header', 'profile_header no_cover_photo',
        'profile_header_inner', 'page_section_menu', 'row_title_image', 'profile_image_holder',
        'pages_menu', 'profiles_menu', 'no-popup', 'js_box #gmap', 'profiles-menu', 'owl-carousel',
        'cancel-upload'],
    notIgnoreClassesOfParentElement: [],
    ignoreIDOfParentElement: ['header', 'welcome', 'footer', 'nb_name'],
    notIgnoreIDOfParentElement: ['js_block_content_profile_friend'],
    ignoreTagOfParentElement: ['H1'],
    notIgnoreTagOfParentElement: [],
    isNotIgnoreClassesOfGrandElement: false,
    enableThumb: true,
    enableCache: false,
    isMouseOver: 1,
    mouseOverTimeoutId: 0,
    box: 0,
    timeoutOpen: 300,
    timeoutClose: 300,
    enabledAdmin: false,
    data: {match_type: '', match_id: 0},
    boxContent: 0,
    maxHeightOfPopup: 150,
    listOfModules: [],
    listOfAdminUser: [],
    init: function () {
        ynfbpp.setTimeoutOpen(ynfbpp.iOpeningDelayTime);
        ynfbpp.setTimeoutClose(ynfbpp.iClosingDelayTime);
        ynfbpp.setEnableCache(ynfbpp.sEnableCache);
    },
    setTimeoutOpen: function (time) {
        var parseTime = parseInt(time, 10);
        if (parseTime >= 100 && parseTime <= 1000) {
            ynfbpp.timeoutOpen = parseTime;
        }

        return ynfbpp;
    },
    clearCached: function () {
        ynfbpp.cached = {};
    },
    openSmoothBox: function (href) {
        alert('openSmoothBox');
    },
    setTimeoutClose: function (time) {
        var parseTime = parseInt(time, 10);
        if (parseTime >= 100 && parseTime <= 1000) {
            ynfbpp.timeoutClose = parseTime;
        }
        return ynfbpp;
    },
    setEnabledAdmin: function (flag) {
        ynfbpp.enabledAdmin = flag;
        return ynfbpp;
    },
    setIgnoreClasses: function (s) {
        var ar = s.replace('.', ' ').replace(',', ' ').split(/\s+/);
        for (var i = 0; i < ar.length; ++i) {
            if (ar[i] != null && ar[i] != undefined && ar[i]) {
                ynfbpp.ignoreClasses.push(ar[i]);
            }
        }
        return ynfbpp;
    },
    setEnableThumb: function (flag) {
        ynfbpp.enableThumb = flag;
        return ynfbpp;
    },
    setEnableCache: function (flag) {
        ynfbpp.enableCache = flag;
        return ynfbpp;
    },
    setMaxHeightOfPopup: function (maxHeightOfPopup) {
        ynfbpp.maxHeightOfPopup = maxHeightOfPopup;
        return ynfbpp;
    },
    setListOfModules: function (listOfModules) {
        ynfbpp.listOfModules = $.parseJSON(listOfModules);
        return ynfbpp;
    },
    setListOfAdminUser: function (listOfAdminUser) {
        ynfbpp.listOfAdminUser = $.parseJSON(listOfAdminUser);
        return ynfbpp;
    },
    removeIgnoreClass: function (k) {
        for (var i = 0; i < ynfbpp.ignoreClasses.length; ++i) {
            if (ynfbpp.ignoreClasses[i] == k) {
                ynfbpp.ignoreClasses[i] = '';
            }
        }
        return ynfbpp;
    },
    boot: function () {
        if (window.screen && window.screen.width < 800) {
            return;
        }

        if (window.parent != window) {
            return;
        }

        if (document.location.href.search('admincp') > 0) {
            if (ynfbpp.enabledAdmin == false) {
                return;
            } else {
            }
        }

        $(document).on('mouseover', 'a', function (event) {
            ynfbpp.check(event);
        });
        $(document).mousemove(function(e) {
            window.ynpp_cursor_x = e.pageX;
            window.ynpp_cursor_y = e.pageY;
        });

        if (ynfbpp.enableThumb) {
            $('img').mouseover(function (event) {
                ynfbpp.check(event);
            });
        }
    },
    isShowSpecialCaseByID: function (obj) {
        var p = obj;
        while (p.parentNode != undefined && p.tagName != undefined && p.parentNode != null && p.tagName != null) {
            switch ($(p.parentNode).attr("id")) {
                case 'photo_view_theater_mode':
                    return true;
            }
            p = p.parentNode;
        }

        return false;
    },
    checkIngoreClasses: function (a, img) {
        ynfbpp.isNotIgnoreClassesOfGrandElement = false;
        var p = a;
        //      get child node of anchor if any
        var kid = null;
        if ($(p).children() && $(p).children().length > 0 && $(p).children()[0]) {
            kid = $(p).children()[0];
        }
        var len = ynfbpp.ignoreClasses.length;
        while (p.parentNode != null && p.parentNode != undefined && p.tagName != null && p.tagName != undefined) {
            for (var i = 0; i < len; ++i) {
                //      check parent node of anchor
                if (ynfbpp.ignoreClasses[i] && ($(p).hasClass(ynfbpp.ignoreClasses[i]) || $(p.parentNode).hasClass(ynfbpp.ignoreClasses[i]))) {
                    if (ynfbpp.isShowSpecialCaseByID(p) === true) {
                        return true;
                    } else {
                        return false;
                    }

                }

                //      check img and grand parent node of img
                if (img !== null && img !== undefined && ynfbpp.enableThumb && $(img).hasClass(ynfbpp.ignoreClasses[i])) {
                    if (img.parentNode.parentNode) {
                        var grandElement = img.parentNode.parentNode;
                        if ($(grandElement).hasClass()) {
                            ynfbpp.isNotIgnoreClassesOfGrandElement = false;
                            var length = ynfbpp.notIgnoreClassesOfGrandElement.length;
                            var isExist = false;
                            for (var j = 0; j < length; j++) {
                                if ($(grandElement).hasClass(ynfbpp.notIgnoreClassesOfGrandElement[j])) {
                                    isExist = true;
                                    ynfbpp.isNotIgnoreClassesOfGrandElement = true;

                                    //      prevent js hover title
                                    $('#js_global_tooltip').css('display', 'none');

                                    break;
                                }
                            }
                            if (isExist === false) {
                                return false;
                            }
                        } else {
                            //      prevent js hover title
                            $('#js_global_tooltip').css('display', 'none');
                            ynfbpp.isNotIgnoreClassesOfGrandElement = true;
                        }
                    } else {
                        return false;
                    }
                }

                //      check child not of anchor
                if (ynfbpp.isNotIgnoreClassesOfGrandElement === false && kid) {
                    if (ynfbpp.ignoreClasses[i] && ($(kid).hasClass(ynfbpp.ignoreClasses[i]) || $(kid.parentNode).hasClass(ynfbpp.ignoreClasses[i]))) {
                        return false;
                    }
                }
            }
            p = p.parentNode;
        }
        return true;
    },
    checkIgnoreClassesAndIDOfParentElement: function (a, img) {
        var p = a;
        var lenClasses = ynfbpp.ignoreClassesOfParentElement.length;
        var lenNotClasses = ynfbpp.notIgnoreClassesOfParentElement.length;

        var lenID = ynfbpp.ignoreIDOfParentElement.length;
        var lenNotID = ynfbpp.notIgnoreIDOfParentElement.length;

        var lenTag = ynfbpp.ignoreTagOfParentElement.length;
        var lenNotTag = ynfbpp.notIgnoreTagOfParentElement.length;

        var idx = 0;
        var idx2 = 0;

        var parentNode = null;
        if (p.parentNode != null && p.parentNode != undefined && p.parentNode.tagName != null && p.parentNode.tagName != undefined) {
            parentNode = p.parentNode;
        }

        for (idx = 0; idx < lenClasses; idx++) {
            if ($(p).parents('.' + ynfbpp.ignoreClassesOfParentElement[idx]).length > 0) {
                var shouldIgnore = true;
                for (idx2 = 0; idx2 < lenNotClasses; idx2++) {
                    if ($(p).parents('.' + ynfbpp.notIgnoreClassesOfParentElement[idx2]).length > 0) {
                        shouldIgnore = false;
                        break;
                    }
                }
                for (idx2 = 0; idx2 < lenNotID; idx2++) {
                    if ($(p).parents('#' + ynfbpp.notIgnoreIDOfParentElement[idx2]).length > 0) {
                        shouldIgnore = false;
                        break;
                    }
                }

                if (parentNode !== null && parentNode !== undefined) {
                    for (idx2 = 0; idx2 < lenNotTag; idx2++) {
                        if (parentNode.tagName.toUpperCase() == ynfbpp.notIgnoreTagOfParentElement[idx2]) {
                            shouldIgnore = false;
                            break;
                        }
                    }
                }

                if (shouldIgnore === true) {
                    //	check special case
                    if (ynfbpp.isShowSpecialCaseByID(p) === true) {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        }
        for (idx = 0; idx < lenID; idx++) {
            if ($(p).parents('#' + ynfbpp.ignoreIDOfParentElement[idx]).length > 0) {
                var shouldIgnore = true;
                for (idx2 = 0; idx2 < lenNotClasses; idx2++) {
                    if ($(p).parents('.' + ynfbpp.notIgnoreClassesOfParentElement[idx2]).length > 0) {
                        shouldIgnore = false;
                        break;
                    }
                }
                for (idx2 = 0; idx2 < lenNotID; idx2++) {
                    if ($(p).parents('#' + ynfbpp.notIgnoreIDOfParentElement[idx2]).length > 0) {
                        shouldIgnore = false;
                        break;
                    }
                }
                if (parentNode !== null && parentNode !== undefined) {
                    for (idx2 = 0; idx2 < lenNotTag; idx2++) {
                        if (parentNode.tagName.toUpperCase() == ynfbpp.notIgnoreTagOfParentElement[idx2]) {
                            shouldIgnore = false;
                            break;
                        }
                    }
                }

                if (shouldIgnore === true) {
                    return false;
                }
            }
        }

        if (parentNode !== null && parentNode !== undefined) {
            for (idx = 0; idx < lenTag; idx++) {
                if (parentNode.tagName.toUpperCase() == ynfbpp.ignoreTagOfParentElement[idx]) {
                    var shouldIgnore = true;
                    for (idx2 = 0; idx2 < lenNotClasses; idx2++) {
                        if ($(p).parents('.' + ynfbpp.notIgnoreClassesOfParentElement[idx2]).length > 0) {
                            shouldIgnore = false;
                            break;
                        }
                    }
                    for (idx2 = 0; idx2 < lenNotID; idx2++) {
                        if ($(p).parents('#' + ynfbpp.notIgnoreIDOfParentElement[idx2]).length > 0) {
                            shouldIgnore = false;
                            break;
                        }
                    }
                    if (parentNode !== null && parentNode !== undefined) {
                        for (idx2 = 0; idx2 < lenNotTag; idx2++) {
                            if (parentNode.tagName.toUpperCase() == ynfbpp.notIgnoreTagOfParentElement[idx2]) {
                                shouldIgnore = false;
                                break;
                            }
                        }
                    }

                    //	for: phpFox 3.6.x
                    if (undefined != $(parentNode).attr("itemprop") && null != $(parentNode).attr("itemprop")) {
                        shouldIgnore = false;
                    }

                    if (shouldIgnore === true) {
                        return false;
                    }
                }
            }
        }

        return true;
    },
    check: function (e) {
        if (e.target == null && e.target == undefined) {
            return;
        }
        var a = e.target;
        var ele = e.target;

        if (a.getAttribute == null || a.getAttribute == undefined) {
            return;
        }

        var img = null;
        if (a.tagName.toUpperCase() == 'IMG') {
            img = a;
        }

        if (a.tagName.toUpperCase() != 'A') {
            var closest = $(a).closest('a');

            if (!closest.length) {
                return;
            }

            a = closest[0];
        }
        if (a.tagName.toUpperCase() == 'A') {
            if (a.parentElement.nodeName == 'LI' && !$(a.parentElement).hasClass('item-event-title') && a.getAttribute('href') != window.location.href) {
                return;
            }
            if(a.parentElement.classList.contains('page_breadcrumbs_menu') && a.getAttribute('href') != window.location.href) {
                return;
            }
        }

        if ($(a).hasClass('buttonlink') || $(a).hasClass('menu_core_mini')) {
            return;
        }

        var href = a.getAttribute('href');
        if (href == null && href == undefined) {
            return;
        }

        var p = a;

        if (ynfbpp.checkIngoreClasses(p, img) == false) {
            return;
        }
        if (ynfbpp.checkIgnoreClassesAndIDOfParentElement(p, img) == false) {
            return;
        }
        for (var i = 0; i < ynfbpp.pt.length; ++i) {
            var data = ynfbpp.pt[i](href, a);
            if (data != null && data != undefined && data != false) {
                if ($(ele)[0]) {
                    ynfbpp.ele = $(ele)[0];
                }

                ynfbpp.href = href;
                ynfbpp.data = data;
                if (ynfbpp.timeoutId) {
                    try {
                        window.clearTimeout(ynfbpp.timeoutId);
                    } catch (e) {

                    }
                }
                $(a).mouseleave(function (event) {
                    ynfbpp.resetTimeout(0);
                });

                ynfbpp.timeoutId = 0;
                ynfbpp.isRunning = 0;
                ynfbpp.dir.cx = e.clientX;
                ynfbpp.dir.cy = e.clientY;

                ynfbpp.timeoutId = window.setTimeout('ynfbpp.requestPopup()', ynfbpp.timeoutOpen);
                return;
            }
        }

    },
    updateBoxContent: function (html) {
        ynfbpp.boxContent.innerHTML = html;
        return ynfbpp;
    },
    startSending: function (html) {
        ynfbpp.boxContent.innerHTML = '<div class="uiContextualDialogContent p_10"> \
                                      <div class="yn_profilepopup_hovercard_stage"> \
                                          <div class="yn_profilepopup_hovercard_content"> \
                                          ' + html + ' \
                                          </div> \
                                      </div> \
                                  </div> \
                                  ';
        return ynfbpp;
    },
    requestPopup: function () {
        ynfbpp.timeoutId = 0;
        var box = ynfbpp.getBox();
        box.style.display = 'none';

        if (!ynfbpp.data.match_type || !ynfbpp.data.match_id) {
            return;
        }

        var key = ynfbpp.data.match_type + '-' + ynfbpp.data.match_id + '-' + ynfbpp.data.match_name;
        if (ynfbpp.enableCache === true && ynfbpp.cached[key] != undefined) {
            ynfbpp.showPopup(ynfbpp.cached[key]);
            return;
        }

        $Core.ajax('profilepopup.loadProfilePopup',
            {
                type: 'POST',
                params: {
                    m: 'lite'
                    , module: 'ynfbpp'
                    , name: 'popup'
                    , match_type: ynfbpp.data.match_type
                    , match_id: ynfbpp.data.match_id
                    , match_name: ynfbpp.data.match_name
                },
                success: function (sOutput) {
                    var oOutput = $.parseJSON(sOutput);
                    if (oOutput.msg == 'success') {
                        if (ynfbpp.enableCache === true) {
                            ynfbpp.cached[key] = oOutput;
                        }

                        ynfbpp.showPopup(oOutput);
                    } else {
                        ynfbpp.startSending(oTranslations['profilepopup.loading_error']);
                        ynfbpp.resetPosition(1);
                    }
                }
            });

        ynfbpp.startSending(oTranslations['profilepopup.loading']);

        ynfbpp.resetPosition(1);

        return ynfbpp;

    },
    resetTimeout: function ($flag) {
        ynfbpp.isMouseOver = $flag;
        if (ynfbpp.mouseOverTimeoutId) {
            try {
                window.clearTimeout(ynfbpp.mouseOverTimeoutId);
                ynfbpp.mouseOverTimeoutId = 0;
                if (ynfbpp.timeoutId) {
                    try {
                        window.clearTimeout(ynfbpp.timeoutId);
                        ynfbpp.timeoutId = 0;
                    } catch (e) {
                    }
                }
            } catch (e) {
            }
        }
        if ($flag == 0) {
            ynfbpp.data.match_id = 0;
            ynfbpp.mouseOverTimeoutId = window.setTimeout('ynfbpp.closePopup()', ynfbpp.timeoutClose);
        }
        return ynfbpp;

    },
    closePopup: function () {
        box = ynfbpp.getBox();
        if (box && box.style) {
            box.style.display = 'none';
        }
        ynfbpp.isShowing = 0;

        return ynfbpp;
    },
    resetPosition: function (flag) {
        ynfbpp.isShowing = 1;
        var box = ynfbpp.getBox();
        var ele = ynfbpp.ele;
        var bodyCssTop = $('body').offset().top;
        if (!ele) {
            return;
        }

        var pos = {x: ynpp_cursor_x, y: ynpp_cursor_y};
        //findhe
        var size = {x: $(ele).width(), y: $(ele).height()};

        if (pos == null || pos == undefined) {
            return;
        }
        //plus bodycsstop fix case popup show in "core popup"
        if (ynfbpp.dir.cy > 500) {
            box.style.top = (pos.y - 10) + -bodyCssTop + 'px';
            $(box).removeClass('yn_profilepopup_dialog_dir_down').addClass('yn_profilepopup_dialog_dir_up');
        } else {
            box.style.top = (pos.y + 10) + -bodyCssTop + 'px';
            $(box).removeClass('yn_profilepopup_dialog_dir_up').addClass('yn_profilepopup_dialog_dir_down');
        }

        if ($('html').attr('dir') == 'ltr') {
            // check the position of the content
            if ($(window).width() - ynfbpp.dir.cx > 425) {
                $(box).removeClass('yn_profilepopup_dialog_dir_left').addClass('yn_profilepopup_dialog_dir_right');
                var px = size.x > 245 ? ynfbpp.dir.cx : pos.x;
                box.style.left = (px - 15) + 'px';
            } else {
                $(box).removeClass('yn_profilepopup_dialog_dir_right').addClass('yn_profilepopup_dialog_dir_left');
                var px = size.x > 245 ? ynfbpp.dir.cx : pos.x ;
                box.style.left = (px - 425) + 'px';
            }
        } else {
            // right to left
            if (ynfbpp.dir.cx < 376) {
                $(box).removeClass('yn_profilepopup_dialog_dir_left').addClass('yn_profilepopup_dialog_dir_right');
                var px = size.x > 245 ? ynfbpp.dir.cx : pos.x;
                box.style.left = px + 'px';
            } else {
                var px = size.x > 245 ? ynfbpp.dir.cx : (pos.x + size.x);
                box.style.left = px + 'px';
                $(box).removeClass('yn_profilepopup_dialog_dir_right').addClass('yn_profilepopup_dialog_dir_left');
            }
        }

        if (flag) {
            box.style.display = 'block';
        }
    },
    showPopup: function (json) {
        if (json == null || json == undefined) {
            return;
        }
        if (json.match_type != ynfbpp.data.match_type || json.match_id != ynfbpp.data.match_id) {
            ynfbpp.closePopup();
            return;
        }
        ynfbpp.resetPosition(1);
        var box = ynfbpp.getBox();
        ynfbpp.updateBoxContent(json.content);
        if (ynfbpp.isNotIgnoreClassesOfGrandElement === true) {
            $('#js_global_tooltip').css('display', 'none');
        }
        box.style.display = 'block';
        ynfbpp.loadImages();

        return ynfbpp;
    },
    loadImages: function () {
        $('.image_deferred:not(.built)').each(function () {
            var t = $(this),
                src = t.attr('src'),
                i = new Image();

            t.addClass('built');
            if (!src) {
                t.addClass('no_image');
                return;
            }

            t.addClass('has_image');
            i.onerror = function (e, u) {
                t.replaceWith('');
            };
            i.onload = function (e) {
                t.attr('src','').attr('src', src);
            };
            i.src = src;
        });
    },
    unfriend: function (sUserID) {
        $Core.jsConfirm({}, function() {
            $.ajaxCall('profilepopup.unfriend', 'id=' + sUserID);
        }, function(){});
        return false;
    },
    refreshPage: function (url) {
        if (url === null || url === undefined) {
            window.location.href = window.location.href;
        } else {
            window.location.href = url;
        }
    },
    leaveEvent: function (id, confirmText) {
        $Core.jsConfirm({message: confirmText}, function() {
            $.ajaxCall('profilepopup.leaveEvent', 'id=' + id);
        }, function(){});
    },
    leaveFEvent: function (id, confirmText) {
        $Core.jsConfirm({message: confirmText}, function() {
            $.ajaxCall('profilepopup.leaveFEvent', 'id=' + id);
        }, function(){});
    },
    getBox: function () {
        if (ynfbpp.box) {
            return ynfbpp.box;
        }
        var ct = document.createElement('DIV');
        ct.setAttribute('id', 'yn_profilepopup_dialog');
        var html = '<div class="yn_profilepopup_dialog_overlay" id="ynfbppUiOverlay" onmouseover="ynfbpp.resetTimeout(1)" onmouseout="ynfbpp.resetTimeout(0)">\
						<div class="yn_profilepopup_overlay_content" id="ynfbppUiOverlayContent">\
						</div> \
						<i class="yn_profilepopup_contextual_dialog_arrow"></i> \
					</div> \
		';
        ct.innerHTML = html;
        var body = document.getElementsByTagName('body')[0];
        body.appendChild(ct);
        $(ct).addClass('yn_profilepopup_dialog');
        if ($('#yn_profilepopup_dialog')[0]) {
            ynfbpp.box = $('#yn_profilepopup_dialog')[0];
        }
        if ($('#ynfbppUiOverlayContent')[0]) {
            ynfbpp.boxContent = $('#ynfbppUiOverlayContent')[0];
        }
        return ynfbpp.box;
    },
    triggerEventOfActionsNotCallCoreLoadInit: function () {

        //	shoutbox
        if ($('#js_shoutbox_input').length) {
            $('#js_shoutbox_input').focus(function () {
                ynfbpp.bootSpecificID('js_block_border_shoutbox_display');
            });
        }

    },
    bootSpecificID: function (id) {
        if (window.screen && window.screen.width < 800) {
            return;
        }

        if (window.parent != window) {
            return;
        }

        if (document.location.href.search('admincp') > 0) {
            if (ynfbpp.enabledAdmin == false) {
                return;
            } else {
            }
        }

        if ($('#' + id).length) {
            $('#' + id)
                .find('a')
                .mouseover(function (event) {
                    ynfbpp.check(event);
                });

            if (ynfbpp.enableThumb) {
                $('#' + id)
                    .find('img')
                    .mouseover(function (event) {
                        ynfbpp.check(event);
                    });
            }
        }
    },
    isRewriteData: function (name) {
        if (undefined != ynfbpp.rewriteData && null != ynfbpp.rewriteData && name.length > 0) {
            var len = ynfbpp.rewriteData.length;
            var i = 0;
            for (i = 0; i < len; i++) {
                if (name == ynfbpp.rewriteData[i].replacement) {
                    return true;
                }
            }
        }
        return false;
    }

};

ynfbpp.pt = [
    function (href, a) {
        //	user
        var sYNBaseUrl = getParam('sJsHome').replace('PF.Base/', '');

        var idx = 0;
        var match = null;
        var reg = null;
        var username = '';
        var iAdminUser = false;
        if (href.search(/index.php/i) > 0) {
            // with not friendly URL
            match = href.match(/\/index\.php\/(\w+\-\d+)(\/)?/i);
            if (match != null && match != undefined) {
                // check normal user
                username = trim(match[1], '/');
                reg = new RegExp("profile-([^\-]+)$", "i");
                regfb = new RegExp("fb-([^\-]+)$", "i");
                match = href.match(reg) || href.match(regfb);
                if (match != undefined && match != null) {
                    if (isNaN(trim(match[1], '/')) === false) {
                        if (ynfbpp.isRewriteData(username) == true) {
                            return false;
                        } else {
                            return {
                                match_id: decodeURIComponent(trim(match[1], '/')),
                                match_name: decodeURIComponent(username),
                                match_type: 'user'
                            };
                        }
                    }
                }
            } else {
                var tail = trim(sYNBaseUrl, '/') + '/index.php/';
                tail = href.substring(tail.length, href.length);

                username = tail.substring(0, tail.indexOf('/') == -1 ? tail.length : tail.indexOf('/'));
                username = trim(username, '/');

                tail = trim(tail, '/');
                if (username !== undefined && username !== null && username != '' && username.length > 0) {
                    if (tail.indexOf('/') > 0) {
                        iAdminUser = false;
                    } else {
                        iAdminUser = true;
                    }
                    if (isModule(username) === true) {
                        iAdminUser = false;
                    }
                    if (iAdminUser === true) {
                        if (ynfbpp.isRewriteData(username) == true) {
                            return false;
                        } else {
                            return {
                                match_id: 'true',
                                match_name: decodeURIComponent(username),
                                match_type: 'user'
                            };
                        }
                    }
                }
            }
        } else if (sYNBaseUrl.length > 0 && href.search(sYNBaseUrl) >= 0) {
            //      with friendly URL
            var tail = href.substring(sYNBaseUrl.length, href.length);
            reg = new RegExp("profile-([^\-]+)$", "i");
            var match = tail.match(reg);
            if (match != undefined && match != null) {
                //      match normal user
                if (isNaN(trim(match[1], '/')) === false) {
                    if (ynfbpp.isRewriteData(trim(tail, '/')) == true) {
                        return false;
                    } else {
                        return {
                            match_id: decodeURIComponent(trim(match[1], '/')),
                            match_name: decodeURIComponent(trim(tail, '/')),
                            match_type: 'user'
                        };
                    }
                }
            } else {
                username = tail.substring(0, tail.indexOf('/') == -1 ? tail.length : tail.indexOf('/'));
                username = trim(username, '/');
                tail = trim(tail, '/');
                if (username !== undefined && username !== null && username != '' && username.length > 0) {
                    if (tail.indexOf('/') > 0) {
                        iAdminUser = false;
                    } else {
                        iAdminUser = true;
                    }
                    if (isModule(username) === true) {
                        iAdminUser = false;
                    }

                    if (iAdminUser === true) {
                        if (ynfbpp.isRewriteData(username) == true) {
                            return false;
                        } else {
                            return {
                                match_id: 'true',
                                match_name: decodeURIComponent(username),
                                match_type: 'user'
                            };
                        }
                    }
                }
            }
        }

        return false;
    }
    , function (href, a) {
        //	pages
        var match = href.match(/\/pages\/(\d+)(\/)?/i);
        if (match != undefined && match != null) {
            var sYNBaseUrl = getParam('sJsHome').replace('PF.Base/', '');
            var tail = href.substring(sYNBaseUrl.length, href.length);
            if (tail !== undefined && tail !== null && trim(tail, '/').search('pages') >= 0 && trim(tail, '/').search('musicsharing/') == -1) {
                return {
                    match_id: decodeURIComponent(trim(match[1], '/')),
                    match_name: '',
                    match_type: 'page'
                };
            }
        }
        return false;
    }
    , function (href, a) {
        //	groups
        var match = href.match(/\/groups\/(\d+)(\/)?/i);
        if (match != undefined && match != null) {
            var sYNBaseUrl = getParam('sJsHome').replace('PF.Base/', '');
            var tail = href.substring(sYNBaseUrl.length, href.length);
            if (tail !== undefined && tail !== null && trim(tail, '/').search('groups') >= 0 && trim(tail, '/').search('musicsharing/') == -1) {
                return {
                    match_id: decodeURIComponent(trim(match[1], '/')),
                    match_name: '',
                    match_type: 'group'
                };
            }
        }
        return false;
    }
    , function (href, a) {
        //	event
        if (href.indexOf('file/pic') > 0) {
            return false;
        }

        var match = href.match(/\/event\/(\d+)(\/)?/i);
        if (match != undefined && match != null) {
            var sYNBaseUrl = getParam('sJsHome').replace('PF.Base/', '');
            var tail = href.substring(sYNBaseUrl.length, href.length);
            if (tail !== undefined && tail !== null && trim(tail, '/').search('event') >= 0) {
                return {
                    match_id: decodeURIComponent(trim(match[1], '/')),
                    match_name: '',
                    match_type: 'event'
                };
            }
        }
        return false;
    }
    , function (href, a) {
        // fevent
        if (href.indexOf('file/pic') > 0) {
            return false;
        }

        var match = href.match(/\/fevent\/(\d+)(\/)?/i);
        if (match != undefined && match != null) {
            var sYNBaseUrl = getParam('sJsHome').replace('PF.Base/', '');
            var tail = href.substring(sYNBaseUrl.length, href.length);
            if (tail !== undefined && tail !== null && trim(tail, '/').search('fevent') >= 0) {
                return {
                    match_id: decodeURIComponent(trim(match[1], '/')),
                    match_name: '',
                    match_type: 'fevent'
                };
            }
        }
        return false;
    }
];


$Behavior.readyBootPopup = function () {
    ynfbpp.iOpeningDelayTime = iOpeningDelayTime;
    ynfbpp.iClosingDelayTime = iClosingDelayTime;
    ynfbpp.sEnableCache = sEnableCache;
    ynfbpp.init();
    ynfbpp.boot();
    ynfbpp.clearCached();
    ynfbpp.triggerEventOfActionsNotCallCoreLoadInit();
};
