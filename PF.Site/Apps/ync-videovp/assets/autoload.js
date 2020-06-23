$Ready(function () {

});

var yncTimeOut, yncvideovpUpdatePlaylistListTimeOut, keyPressed = false;

var yncvideovp = {
    initialized: false,
    sBaseURL: getParam('sBaseURL'),
    sRegEx: /\/(video\/play|ultimatevideo|videochannel)\/(\d+)\/.*/,
    // sPopupId: 'yncvideovp_popup',
    sPopupId: 'yncvideovp_popup',
    init: function () {
        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
            return;
        }

        var $popup = this.getPopup(), $videoLinks, $ultimateVideoLinks, $videoChannelLinks;

        if (!getParam('bIsAdminCP')) {
            // $links = $('a[href^="' + yncvideovp.sBaseURL + 'photo/"]:not([onclick*="tb_show"],[href*="action=download"],[class^="addthis_button_"],[class^="stream_photo"],[class="ajax_link"],[id="next_photo"],[id="previous_photo"])');
            $videoLinks = $('a[href^="' + yncvideovp.sBaseURL + 'video/play"]');
            $ultimateVideoLinks = $('a[href^="' + yncvideovp.sBaseURL + 'ultimatevideo"]');
            if($('#page_ultimatevideo_view').length) {
                let ultimateVideoTitle = $('.p-detail-header-page-title a[href="#"]:first');
                ultimateVideoTitle.each(yncvideovp.bindVideoLinks);
            }
            $videoChannelLinks = $('a[href^="' + yncvideovp.sBaseURL + 'videochannel"]');
            $videoLinks.each(yncvideovp.bindVideoLinks);
            $ultimateVideoLinks.each(yncvideovp.bindVideoLinks);
            $videoChannelLinks.each(yncvideovp.bindVideoLinks);
        }

        if (!$popup.length) {
            yncvideovp.addJSApiParam();
            return;
        }

        window.bAddingFeed = true;

        $popup.find('.js_box_close a').removeAttr('onclick');
        $popup.find('.js_box_close a').off('click');

        this.initPhrases();

        $popup.off('click')
            .on('click', yncvideovp.closeOnClickOutside)
            .on('click', '.js_ync_videovp_view_detail_page', yncvideovp.viewDetailPage)
            .on('click', '.js_box_close a', yncvideovp.closePopup)
            .on('click', '.videovp-star-vote', yncvideovp.favoriteVideo);

        $(document).unbind('keydown').unbind('keyup').keydown(yncvideovp.keyboardNavigation).keyup(function () {
            keyPressed = false;
        });

        $(document).unbind('keydown').unbind('keyup').keydown(yncvideovp.closeOnEsc).keyup(function () {
            keyPressed = false;
        });

        $popup.find('#yncvideovp_embed_code').on('shown.bs.collapse', function () {
            $('#yncvideovp_embed_code_value').select();
        });

        $popup.find('.dropup').on('show.bs.dropdown', function () {
            $(this).closest('.ync-videovp-content-info').addClass('has-open-dropdown');
        }).on('hidden.bs.dropdown', function () {
            $(this).closest('.ync-videovp-content-info').removeClass('has-open-dropdown');
        });

        if (typeof yncvideovp.tb_remove === 'undefined') {
            yncvideovp.tb_remove = window.tb_remove;
        }
        window.tb_remove = function () {
            var $jsBoxes = $('.js_box_holder');
            if ($jsBoxes.length === 1 && $jsBoxes.attr('id') === yncvideovp.sPopupId) {
                return false;
            } else {
                yncvideovp.tb_remove();
            }
        };

        var $videoContainer = $popup.find('.ync-videovp-container'),
            $commentForm = $popup.find('.js_feed_comment_form, .ync-comment-box-container');

        if ($commentForm.length) {
            yncvideovp.addCommentFormObserver($commentForm);
        } else {
            $videoContainer.addClass('cannot_comment');
        }

        //add scroll
        $(".ync-videovp-block-info-inner").mCustomScrollbar({
            theme: "minimal-dark",
            callbacks: {
                onOverflowY: function () {
                    var $infoBottom = $popup.find('.ync-videovp-block-info-bottom');
                    $infoBottom.height($commentForm.outerHeight());
                }
            }
        }).addClass('dont-unbind-children');

        window.addEventListener("popstate", function () {
            $popup && $popup.find('.js_box_close a').trigger('click');
        });
    },
    initPhrases: function () {
        if (typeof window.oTranslations === 'undefined') {
            window.oTranslations = {};
        }
    },
    addCommentFormObserver: function ($commentForm) {
        var config = {attributes: true, childList: true, subtree: true};

        var callback = function (e) {
            yncvideovp.updateCommentListHeight($commentForm);
        };

        var observer = new MutationObserver(callback);

        observer.observe($commentForm.get(0), config);
    },
    addJSApiParam: function () {
        $('iframe[src*="youtube"]:not([src*="enablejsapi"])').each(function (index, el) {
            var $el = $(el), sUrl = $el.attr('src');
            if (sUrl.indexOf('?') === -1) {
                $el.attr('src', sUrl + '?enablejsapi=1');
            } else {
                $el.attr('src', sUrl + '&enablejsapi=1')
            }
        });
        $('iframe[src*="vimeo"]:not([src*="api"]),iframe[src*="dailymotion"]:not([src*="api"])').each(function (index, el) {
            var $el = $(el), sUrl = $el.attr('src');
            if (sUrl.indexOf('?') === -1) {
                $el.attr('src', sUrl + '?api=1');
            } else {
                $el.attr('src', sUrl + '&api=1')
            }
        });
    },
    pausePlayingVideos: function () {
        $('iframe[src*="youtube"],iframe[src*="vimeo"],iframe[src*="dailymotion"]').each(function (index, el) {
            var $el = $(el), sUrl = $el.attr('src');

            if (sUrl.indexOf('youtube') !== -1) {
                $el.get(0).contentWindow.postMessage(JSON.stringify({
                    event: 'command',
                    func: 'pauseVideo',
                    args: []
                }), '*');
            } else if (sUrl.indexOf('vimeo') !== -1) {
                $el.get(0).contentWindow.postMessage(JSON.stringify({
                    method: 'pause'
                }), '*');
            } else if (sUrl.indexOf('dailymotion') !== -1) {
                $el.get(0).contentWindow.postMessage('pause', '*');
            }
        });
        $('video').each(function (index, el) {
            el.pause();
        });
    },
    bindVideoLinks: function () {
        var $link = $(this),
            sLink = $link.attr('href');

        if(sLink == '#' && $('#page_ultimatevideo_view').length) {
            sLink = $('meta[property="og:url"]').attr('content');
        }
        if (!yncvideovp.sRegEx.test(sLink)) {
            return;
        }

        $link.unbind('click').click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            if (yncvideovp.isPopupOpen()) {
                yncvideovp.fetch(sLink);
            } else {
                yncvideovp.openPopup(sLink);
            }
        });
    },
    parseExtraParams: function (sLink) {
        var regex = /([^\/]+)_(\d+)/g;
        var matches = sLink.match(regex),
            paramsStr = '';

        if (Array.isArray(matches)) {
            var matchLength = matches.length;
            for (var i = 0; i < matchLength; i++) {
                matches[i] = matches[i].replace("_", "=");
            }
            paramsStr = '&' + matches.join('&');
        }

        return paramsStr;
    },
    openPopup: function (sLink) {
        yncvideovp.pausePlayingVideos();
        window.yncvideovpWindowScrollY = window.scrollY;
        tb_remove();
        tb_show('', yncvideovp.getAjaxBox(sLink), '', '', false, '', false, yncvideovp.sPopupId);
        $('body').css({
            top: 0
        });
    },
    getAjaxBox: function (sLink) {
        var parseVideoLink = sLink.match(yncvideovp.sRegEx),
            module = parseVideoLink[1],
            videoID = parseVideoLink[2],
            extraParams = yncvideovp.parseExtraParams(sLink);

        if (module === 'video/play') {
            module = 'video';
        }

        return $.ajaxBox('yncvideovp.view', 'slink=' + sLink + '&video_id=' + videoID + '&module=' + module + extraParams);
    },
    closeOnClickOutside: function (e) {
        if (e.target === e.currentTarget) {
            var $popup = yncvideovp.getPopup();
            $popup && $popup.find('.js_box_close a').trigger('click');
        }
    },
    closeOnEsc: function (e) {
        if (keyPressed) {
            return;
        }
        keyPressed = true;
        var $popup = yncvideovp.getPopup();
        switch (e.which) {
            case 27:
                // e.stopPropagation();
                $popup.find('.js_box_close a').trigger('click');
                break;
            default:
                return;
        }
        e.preventDefault();
    },
    closePopup: function () {
        $('.note, .notep').remove();
        $('#main_core_body_holder').show();

        var $aAllBoxIndex = [],
            $aAllBoxIndexHolder = [];

        $('.js_box').each(function () {
            $aAllBoxIndex[parseInt($(this).css('z-index'))] = $(this).attr('id');
            $aAllBoxIndexHolder.push(parseInt($(this).css('z-index')));
        });

        var $iFocusBox = parseInt(Math.max.apply(Math, $aAllBoxIndexHolder));

        if (isset($aAllBoxIndex[$iFocusBox])) {
            var $focusBox = $('#' + $aAllBoxIndex[$iFocusBox]);
            if ($focusBox.length > 0) {
                var $sLink = $focusBox.find('.js_box_history:first').html();
                var holder = yncvideovp.getPopup();
                $focusBox.remove();
                delete $aBoxHistory[$sLink];
                holder.remove();
                $('body').css({
                    overflow: '',
                    position: '',
                    width: '',
                    top: ''
                });
                $(window).scrollTop(window.yncvideovpWindowScrollY);
            }
        }

        $('#global_attachment_list_inline').hide();

        keyPressed = false;
        window.bAddingFeed = false;

        return false;
    },
    isPopupOpen: function () {
        return this.getPopup().length;
    },
    getPopup: function () {
        return $('#' + yncvideovp.sPopupId);
    },
    reload: function () {
        var currentLink = yncvideovp.getCurrentLink();
        if (currentLink) {
            yncvideovp.openPopup(currentLink);
        }
    },
    refresh: function () {
        var currentLink = yncvideovp.getCurrentLink();
        if (currentLink) {
            yncvideovp.fetch(currentLink);
        }
    },
    fetch: function (sLink) {
        var $popup = yncvideovp.getPopup();
        $('.note, .notep').remove();
        $popup.find('.js_box_content').html('<span class="js_box_loader"><i class="fa fa-spin fa-spinner"></i></span>');
        $popup.find('.js_box_content').load(yncvideovp.getAjaxBox(sLink), function () {
            $Core.loadInit();
        });
    },
    getCurrentLink: function () {
        var $popup = yncvideovp.getPopup();
        if (!$popup.length) {
            return '';
        }
        var $slink = $popup.find('#js_ync_videovp_slink');
        if (!$slink.length || !$slink.val()) {
            return '';
        }
        return $slink.val();
    },
    fetchLink: function (e) {
        var sLink = $(this).data('href');
        yncvideovp.fetch(sLink);
    },
    viewDetailPage: function () {
        var currentLink = yncvideovp.getCurrentLink();
        if (currentLink) {
            window.location.href = currentLink;
        }
    },
    updateCommentListHeight: function ($commentForm) {
        clearTimeout(yncTimeOut);
        yncTimeOut = setTimeout(function () {
            var $popup = yncvideovp.getPopup(),
                $infoBottom = $popup.find('.ync-videovp-block-info-bottom');
            $infoBottom.height($commentForm.outerHeight());
        }, 100);
    },
    favoriteVideo: function () {
        var videoId = $(this).data('video_id'),
            isFavourite = $(this).hasClass('voted'),
            type = isFavourite ? 0 : 1;
        $.ajaxCall('yncvideovp.favorite_videochannel', 'video_id=' + videoId + '&type=' + type);
    },
    updateFavoriteButton: function (type) {
        var $popup = yncvideovp.getPopup(),
            $favoriteButton = $popup.find('.videovp-star-vote')
        ;

        if (type) {
            $favoriteButton.addClass('voted');
        } else {
            $favoriteButton.removeClass('voted');
        }
    }
};

$Behavior.initVideoPopup = function () {
    yncvideovp.init();
};