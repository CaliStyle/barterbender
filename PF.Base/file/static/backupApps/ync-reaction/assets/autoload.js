var yncreaction = {
    isClicked: false,
    isHover: false,
    openTimer: null,
    closeTimer: null,
    shownReact: false,
    isMobile: false,
    updateMostReactionOnComment: function (obj, iItemId, sType, sPrefix) {
        if (sType != 'feed_mini') {
            return false;
        }
        obj.ajaxCall('yncreaction.updateMostReactionOnComment', $.param({
            'type': sType,
            'item_id': iItemId,
            'table_prefix': sPrefix
        }), 'post', null, function (e, self) {
            var oHtml = JSON.parse(e);
            var oReactCont = self.closest('.comment_mini_action').find('.ync-reaction-container-js');
            oReactCont.siblings('.ync-reaction-list-mini').remove();
            if (oHtml.length) {
                oReactCont.after(oHtml);
            }
        });
        return true;
    },
    checkScrollPopup: function () {
        var width_header = $('.ync-reaction-popup-header').width(),
            number_item = $('.js_ync_reaction_popup_nav li').length,
            width_item = $('.js_ync_reaction_popup_nav li').width(),
            width_all_item = number_item * width_item,
            position_header = $('.ync-reaction-popup-header').offset(),
            position_first_item = $('.js_ync_reaction_popup_nav li:first-child').offset(),
            position_last_item = $('.js_ync_reaction_popup_nav li:last-child').offset();

        if ($("html").attr("dir") == "rtl") {
            if (width_all_item > width_header) {
                if ((position_last_item.left) < (position_header.left)) {
                    $('.ync-reaction-popup-header').addClass('overlay-end');
                } else {
                    $('.ync-reaction-popup-header').removeClass('overlay-end');
                }
                if ((position_first_item.left + width_item) > (position_header.left + width_header)) {
                    $('.ync-reaction-popup-header').addClass('overlay-start');
                } else {
                    $('.ync-reaction-popup-header').removeClass('overlay-start');
                }
            }
        } else {
            if (width_all_item > width_header) {
                if ((position_last_item.left + width_item) > (position_header.left + width_header)) {
                    $('.ync-reaction-popup-header').addClass('overlay-end');
                } else {
                    $('.ync-reaction-popup-header').removeClass('overlay-end');
                }
                if (position_first_item.left < position_header.left) {
                    $('.ync-reaction-popup-header').addClass('overlay-start');
                } else {
                    $('.ync-reaction-popup-header').removeClass('overlay-start');
                }
            }
        }
    },
    setTabColor: function(ele) {
        $('.js_ync_reaction_popup_nav').find('a[data-toggle="tab"]').removeAttr('style');
        $('.js_ync_reaction_popup_nav').find('.item-number').removeAttr('style');
        $(ele).parent().addClass('active');
        $(ele).attr('style','border-bottom: 3px solid #' + $(ele).data('color') + ' !important;');
        $(ele).find('.item-number').attr('style','color:#' + $(ele).data('color') + ' !important;');
    }
};
$Ready(function () {
    //auto position tooltip
    $('.comment_mini_action .ync-reaction-list-mini').off('mouseover').on('mouseover', function () {
           var pos = $(this).offset().top + $(this).outerHeight();
           var window_top = $(window).scrollTop();
           var window_bottom = window_top + $(window).height();
           var height = $(this).find('.ync-reaction-tooltip-total').height();
           if ((window_bottom - pos) < (height + 10)) {
               $(this).find('.ync-reaction-tooltip-total').addClass('reverse');
           } else {
               $(this).find('.ync-reaction-tooltip-total').removeClass('reverse');
           }
       
   });

    $('.comment_mini_action .ync-reaction-container-js').parent().addClass('ync-reaction-container-outer');
    //popup check scroll

    if ($('.ync-reaction-popup-box').is(':visible')) {
        yncreaction.checkScrollPopup();
    }
    //end
    if ((/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent))) {
        $('.js_ync_reaction_tooltip,.ync-reaction-list').addClass('is-mobile');
        $('.ync-reaction-item').removeAttr('data-toggle');
        //check scroll
        yncreaction.isMobile = true;
        $('.js_ync_reaction_popup_nav').scroll(function () {
            yncreaction.checkScrollPopup();
        });
        $('.ync-reaction-container-js').off('touchstart').on('touchstart',function () {
            var _this = $(this);
            clearTimeout(yncreaction.closeTimer);
            yncreaction.openTimer = setTimeout(function () {
                if (_this.hasClass('open')) return false;
                if (_this.hasClass('is_clicked')) {
                    _this.removeClass('is_clicked');
                    return false;
                }
                $('.ync-reaction-container-js').not([_this]).removeClass('open').find('.ync-reaction-list .ync-reaction-item').removeClass('animate');
                _this.addClass('open');
                _this.find('.ync-reaction-list .ync-reaction-item').each(function (index, element) {
                    setTimeout(function () {
                        $(element).addClass('animate');
                    }, index * 30);
                });
                yncreaction.shownReact = true;
            }, 500);
        }).on('oncontextmenu',function(){
            return false;
        });
        $(document).on('touchstart', function(event) {
            var oObj = $(event.target);
            if (!oObj.hasClass('ync-reaction-container-js') && !oObj.closest('.ync-reaction-container-js').length) {
                $('.ync-reaction-container-js').removeClass('open');
                $('.ync-reaction-container-js').find('.ync-reaction-list .ync-reaction-item').removeClass('animate');
                yncreaction.shownReact = false;
            }
        })
    } else {
        
        $(".ync-reaction-popup-header").mCustomScrollbar({
            theme: "minimal-dark",
            axis: "x",
            callbacks: {
                onScroll: function () {
                    yncreaction.checkScrollPopup();
                }
            }
        }).addClass('dont-unbind-children');

        $('.ync-reaction-container-js').hover(function () {
            var _this = $(this);
            clearTimeout(yncreaction.closeTimer);
            yncreaction.openTimer = setTimeout(function () {
                if (_this.hasClass('is_clicked') || _this.hasClass('open')) {
                    return false;
                }
                $('.ync-reaction-container-js').not([_this]).removeClass('open').find('.ync-reaction-list .ync-reaction-item').removeClass('animate');
                _this.addClass('open');
                _this.find('.ync-reaction-list .ync-reaction-item').each(function (index, element) {
                    setTimeout(function () {
                        $(element).addClass('animate');
                    }, index * 30);
                });
            }, 500);
        }, function () {
            var _this = $(this);
            clearTimeout(yncreaction.openTimer);
            yncreaction.closeTimer = setTimeout(function () {
                _this.removeClass('is_clicked');
                _this.removeClass('open');
                _this.find('.ync-reaction-list .ync-reaction-item').removeClass('animate');
            }, 500);
        });
    }
    //init tooltip
    $(document).tooltip({
        selector: '[data-toggle="tooltip"]'
    });

    /**
     * click on like toggle
     */
    $(document).on('click', '[data-toggle="ync_reaction_toggle_cmd"]', function (event) {
        if (yncreaction.isClicked) {
            return false;
        }
        yncreaction.isClicked = true;
        var element = $(this),
            obj = element.data(),
            react_icon = obj.full_path,
            liked = !!obj.liked,
            extras = '',
            re_react = !element.hasClass('js_like_link_toggle') && liked,
            _liked = !liked || re_react,
            method = (_liked) ? 'yncreaction.add' : 'yncreaction.delete';
        if ($(event.target).hasClass('ync-reaction-title')) {
            clearTimeout(yncreaction.openTimer);
        } else {
            element.closest('.ync-reaction-container-js').addClass('is_clicked');
        }
        if (yncreaction.shownReact && yncreaction.isMobile && (!_liked || $(event.target).hasClass('js_like_link_toggle') || $(event.target).closest('.ync-reacted-icon-outer').length)) {
            yncreaction.isClicked = false;
            return false;
        }

        if (!$('body').hasClass('_is_guest_user')) {
            if (element.parents('.comment-mini-content-commands').length) {
                var allElement = $('.comment-mini-content-commands').find('[data-toggle="ync_reaction_toggle_cmd"][data-feed_id="' + obj.feed_id + '"][data-type_id="' + obj.type_id + '"]'),
                    oDisplayLink = $('.comment-mini-content-commands').find('[data-toggle="ync_reaction_toggle_cmd"][data-feed_id="' + obj.feed_id + '"][data-type_id="' + obj.type_id + '"].js_like_link_toggle');
                allElement.data('liked', _liked ? true : false);
                allElement.removeClass('unlike liked').addClass(!_liked ? 'unlike' : 'liked');
                allElement.find('span').text(_liked ? obj.label2 : obj.label1);
                oDisplayLink.html(_liked ? '<div class="ync-reacted-icon-outer"><img src="' + react_icon + '" class="ync-reacted-icon" oncontextmenu="return false;"/> </div><strong class=ync-reaction-title style="color:\#' + obj.reaction_color + '">' + obj.reaction_title + '</strong>' : '');
            }
            else {
                element = element.hasClass('js_like_link_toggle') ? element : element.closest('.ync-reaction-container-js').find('.js_like_link_toggle');
                element.data('liked', _liked ? true : false);
                element.removeClass('unlike liked').addClass(!_liked ? 'unlike' : 'liked');
                element.find('span').text(!liked ? obj.label2 : obj.label1);
                element.html(_liked ? '<div class="ync-reacted-icon-outer"><img src="' + react_icon + '" class="ync-reacted-icon" oncontextmenu="return false;"/> </div><strong class="ync-reaction-title" style="color:\#' + obj.reaction_color + '">' + obj.reaction_title + '</strong>' : '');
            }
        }

        var i = element.parents('.comment_mini_content_holder:first');
        if (i.hasClass('_is_app')) {
            extras += 'custom_app_id=' + i.data('app-id') + '&';
        }
        element.closest('.ync-reaction-container-js').removeClass('open');
        element.closest('.ync-reaction-container-js').find('.ync-reaction-list .ync-reaction-item').removeClass('animate');

        element.ajaxCall(method, extras
            + 'type_id=' + obj.type_id
            + '&item_id=' + obj.item_id
            + '&parent_id=' + obj.feed_id
            + '&custom_inline=' + obj.is_custom
            + '&table_prefix=' + obj.table_prefix
            + '&reaction_id=' + obj.reaction_id
            + '&is_re_react=' + re_react,
            'GET', null, function (e, self) {
                yncreaction.isClicked = false;
                yncreaction.shownReact = false;
                if (obj.type_id == 'feed_mini') {
                    yncreaction.updateMostReactionOnComment(self, obj.item_id, obj.type_id, obj.table_prefix);
                }
            }
        );
    });
    $(document).on('click', '[data-action="ync_reaction_show_list_user_react_cmd"]', function () {
        if (yncreaction.isClicked) {
            return false;
        }
        yncreaction.isClicked = true;
        clearTimeout(yncreaction.openTimer);
        var element = $(this),
            obj = element.data();

        tb_show('', $.ajaxBox('yncreaction.showListReactOnItem', $.param({
            'type': obj.type_id,
            'item_id': obj.item_id,
            'react_id': obj.react_id,
            'table_prefix': obj.table_prefix
        })));
        yncreaction.isClicked = false;
    });
    $('[data-toggle="ync_reaction_toggle_user_reacted_cmd"]').on('mouseover', function () {
        if (yncreaction.isHover) {
            return false;
        }
        yncreaction.isHover = true;
        var element = $(this),
            obj = element.data();
        if (element.closest('.js_reaction_item').find('.js_ync_reaction_preview_reacted').prop('built_list')) {
            return false;
        }
        element.ajaxCall('yncreaction.showReactedUser', $.param({
            'type': obj.type_id,
            'item_id': obj.item_id,
            'table_prefix': obj.table_prefix,
            'total_reacted': obj.total_reacted,
            'react_id': obj.react_id
        }), 'POST', null, function (e, self) {
            var oHtml = JSON.parse(e);
            if (oHtml.length) {
                self.closest('.js_reaction_item').find('.js_ync_reaction_preview_reacted').html(oHtml).prop('built_list', true);
            }
            yncreaction.isHover = false;
        });
    }).on('mouseout', function () {
        yncreaction.isHover = false;
    });
    if ($('.js_ync_reaction_display_in_detail').length) {
        $('.js_feed_comment_border').find('.js_comment_like_holder:not(.js_ync_reaction_display_in_detail)').remove();
        $('.js_comment_like_holder.js_ync_reaction_display_in_detail').show();
    }
});