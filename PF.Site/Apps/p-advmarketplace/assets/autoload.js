$Behavior.ynadvmarketplace_init_home_sliders = function () {
    setTimeout(function(){
        var owl_array = $('.p-advmarketplace-slider-container');
        owl_array.each(function(){
            var owl=$(this);
            var owl_dot_container = $(this).closest('.p-advmarketplace-feature-container').find('.advmarketplace_carousel_custom_dots');
            if (!owl.length || owl.prop('built')) {
                return false;
            }
            owl.prop('built', true);
            owl.addClass('dont-unbind-children');
            var rtl = false;
            if ($("html").attr("dir") == "rtl") {
                rtl = true;
            }
            var item_amount = parseInt(owl.find('.item').length);
            var more_than_one_item = item_amount > 1;
            var dotseach = 1;
            var stagepadding = 0;
            if(item_amount > 10){
                dotseach = Math.ceil(item_amount/10);
            }
            if(more_than_one_item){
                if (window.matchMedia('(min-width: 1200px)').matches) {
                    if($('#main.empty-right.empty-left').length > 0){
                        stagepadding = 130;
                    }
                }
            }
            owl.owlCarousel({
                rtl: rtl,
                items: 1,
                dotsEach : dotseach,
                loop: more_than_one_item,
                mouseDrag: more_than_one_item,
                margin: 16,
                autoplay: false,
                autoplayTimeout: 5500,
                autoplayHoverPause: true,
                smartSpeed: 800,
                dots:true,
                stagePadding: stagepadding,
                dotsContainer: owl_dot_container,
                onInitialized:callback
            });
            function callback(event){
                if(owl.closest('.p-advmarketplace-feature-container').find('.advmarketplace_carousel_custom_dots').hasClass('disabled')){
                    owl.closest('.p-advmarketplace-feature-container').find('.p-advmarketplace-slider-bottom').hide();
                }
                $Behavior.ynadvmarketplace_init_syns_thumb();
            }
            $('.advmarketplace_next_slide').off('click').click(function(){
                $(this).closest('.p-advmarketplace-feature-container').find('.p-advmarketplace-slider-container').trigger('next.owl.carousel');
                console.log('asd');
            });
            $('.advmarketplace_prev_slide').off('click').click(function(){
                $(this).closest('.p-advmarketplace-feature-container').find('.p-advmarketplace-slider-container').trigger('prev.owl.carousel');
            });

            $('.owl-dot').click(function () {
              $(this).closest('.p-advmarketplace-feature-container').find('.p-advmarketplace-slider-container').trigger('to.owl.carousel', [$(this).index(), 300]);
            });
        });
    }, 300);
};
PF.event.on('p_update_main_layout', function(){
    if($('.p-advmarketplace-slider-container').length > 0){
        $('.p-advmarketplace-slider-container').trigger('destroy.owl.carousel').prop('built', false);
        $Behavior.ynadvmarketplace_init_home_sliders();
    }

});
$Behavior.ynadvmarketplace_init_syns_thumb = function () {
    $('.js_p_advmarketplace_slider_photo_thumb').mouseenter(function(){
        console.log('ga');
        $(this).addClass('focus');
        var id_photo_thumb = $(this).data('photo-thumb'),
            parent = $(this).closest('.p-advmarketplace-item');
        parent.find('.js_p_advmarketplace_slider_photo_main[data-photo-main='+ id_photo_thumb +']').addClass('focus');
        parent.find('.js_p_advmarketplace_photo_main .main_photo').removeClass('focus');
    });
    $('.js_p_advmarketplace_slider_photo_thumb').mouseleave(function(){
        var id_photo_thumb = $(this).data('photo-thumb'),
            parent = $(this).closest('.p-advmarketplace-item');
        $(this).removeClass('focus');
        parent.find('.js_p_advmarketplace_slider_photo_main[data-photo-main='+ id_photo_thumb +']').removeClass('focus');
        parent.find('.js_p_advmarketplace_photo_main .main_photo').addClass('focus');
    });
};

$Behavior.ynadvmarketplace_chart_statistic = function () {
    if($('.js_advmarketplace-chart-success').length > 0){
        $('.js_advmarketplace-chart-success').each(function(){
            var chartTotal = $(this).data('chart-total'),
                chartSuccess = $(this).data('chart-success');
            var chartPercent =  (chartSuccess/chartTotal)*100;
            $(this).css('width',chartPercent + '%');
        });
    }
};

$Behavior.ynadvmarketplace_initDetailSlide = function() {
    var ele = $('#advmarketplace_slider-detail');
    if (ele.prop('built') || !ele.length) return false;
    ele.prop('built', true).addClass('dont-unbind-children');
        var slider = new MasterSlider();

    slider.setup('advmarketplace_slider-detail' , {
        width: ele.width(),
        height: ele.width(),
        space:5,
        view:'basic',
        dir: 'h',
    });

    slider.control('arrows');
    slider.control('thumblist' , {autohide:false ,dir:'h'});

    slider.api.addEventListener(MSSliderEvent.CHANGE_END , function(){
        if ($('.ms-thumbs-cont .ms-thumb-frame').length < 2){
            $('.ms-thumbs-cont').closest('.p-advmarketplace-detail-photo-block').addClass('one-slide');
        }
        var width_list_thumb = $('.ms-thumb-list').outerWidth(),
            width_item_thumb = $('.ms-thumb-frame').outerWidth() + 5,
            max_item_thumb = parseInt(width_list_thumb/width_item_thumb),
            count_item_thumb = $('.ms-thumbs-cont .ms-thumb-frame').length ;
        if(count_item_thumb <= max_item_thumb){
            $('.ms-thumb-list').addClass('not-nav-btn');
        }
        /*if (slider.api.count() < 5){
            $('#advmarketplace_slider-detail').addClass('less-4-slide');
        }*/
    });
};

var appAdvMarketplace = {
    processWishlist: function(obj) {
        var addWishlist = $(obj).data('wishlist');
        var listingId = $(obj).data('id');
        var detail = $(obj).data('detail');
        var wishlist_page = $(obj).data('wishlist-page');
        var feed  = $(obj).data('feed');
        $.ajaxCall('advancedmarketplace.addWishlist','listing_id=' + listingId + '&wishlist=' + addWishlist + (!empty(detail) ? '&detail=1' : '') + (!empty(wishlist_page) ? '&wishlist_page=1' : '') + (!empty(feed) ? '&feed=1' : ''));
    },
    processAfterAddWishlist: function(params) {
        var parent = $(params['parent']);
        if(parent.length) {
            var wishlist = params['wishlist'];
            parent.each(function () {
                var wishlistBtn = $(this).find('.js_wishlist_btn:first');
                if(wishlist) {
                    wishlistBtn.removeClass('checked');
                }else {
                    wishlistBtn.addClass('checked');
                }
                wishlistBtn.data('wishlist', wishlist);
                if(!empty(params['change_text'])) {
                    wishlistBtn.find('.js_wishlist_text').html(wishlist ? params['advancedmarketplace_add_to_wishlist'] : params['advancedmarketplace_remove_from_wishlist']);
                }
            });

            let wishlistCountMenu = $('.js_wishlist_count_menu');
            if(wishlistCountMenu.length) {
                let currentCount = parseInt(wishlistCountMenu.html());
                currentCount = currentCount > 0 ? currentCount : 0;
                currentCount = (wishlist ? (currentCount < 1 ? 0 : currentCount - 1) : (currentCount + 1));
                wishlistCountMenu.html(currentCount);
                (currentCount > 0 ? wishlistCountMenu.removeClass("hide") : wishlistCountMenu.addClass("hide"));
            }
        }
    },
    updateCurrentRaring: function(rating_container, current_rating) {
        rating_container.find('i').removeClass('hover');
        if (current_rating > 0) {
            var i = 0;
            rating_container.find('i').each(function () {
                if (i < current_rating) {
                    i++;
                    $(this).removeClass('disable');
                }
            });
        }
    },
    submitReview: function(obj) {
        var form = $(obj);
        form.find('.js_submit_review_btn:first').prop('disabled', true);
        form.ajaxCall('advancedmarketplace.submitReview');
        return false;
    },
    contactSeller: function($aParams) {
        tb_show('', $.ajaxBox('mail.compose', 'height=300&width=500&is_advancedmarketpalce_contact_seller=true&no_remove_box=true&' + $.param($aParams)));
    },
    deleteReview: function(listing_id, rate_id) {
        var params = 'rate_id=' + rate_id + '&listing_id=' + listing_id;
        $Core.jsConfirm({message: oTranslations['are_you_sure']}, function () {
            $.ajaxCall('advancedmarketplace.deleteReview', params);
        });
    },
    prepareComposeMessage: function(listingId) {
        var intervalId = setInterval(function(){
            if($('form.js_ajax_compose_message').length) {
                var form = $('form.js_ajax_compose_message');
                if(!form.find('input[type="hidden"][name="is_advancedmarketpalce_contact_seller"]').length) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'is_advancedmarketpalce_contact_seller',
                        value: true
                    }).prependTo(form);
                }
                if(!form.find('input[type="hidden"][name="type"]').length) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'type',
                        value: 'claim-page'
                    }).prependTo(form);
                }
                if(!form.find('input[type="hidden"][name="page_id"]').length) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'val[page_id]',
                        value: listingId
                    }).prependTo(form);
                }
                clearInterval(intervalId);
            }
        },1000);
    },
    processPurchasement: function(obj) {
        var gatewayId = $('.js_advancedmarketplace_select_payment_gateway.active',$('#js_advancedmarketplace_purchase_popup')).data('id');
        var form = $('#js_advancedmarketplace_payment_gateway_' + gatewayId);
        if(!form.length) {
            return tb_remove();
        }
        if(gatewayId == 'activitypoints' && $(obj).data('detail-payment')) {
            tb_show('',$.ajaxBox('advancedmarketplace.detailPayment','width=400&invoice_id='+ $(obj).data('invoice')));
        }
        else {
            if($(obj).data('no-confirm')) {
                form.submit();
            }
            else {
                $Core.jsConfirm({message: oTranslations['are_you_sure']}, function(){
                    form.submit();
                });
            }

        }

    },
    switchStep: function(step) {
        var $stepLink = $('.p-step-link[rel="' + step + '"]');

        $('.p-step-item').removeClass('active');
        $stepLink.closest('.p-step-item').addClass('active');

        $('.page_section_menu_holder').hide();
        $('#' + step).show();

        if ($('#page_section_menu_form').length > 0) {
            $('#page_section_menu_form').val(step);
        }
        // set current tab
        $('#current_tab').val($stepLink.attr('href').replace('#', ''));
    }
};

$Behavior.rating = function() {
    if($('.js_rating_action_vote').length) {
        var parent = $('.js_rating_action_vote');
        parent.on('mouseenter','.p-rating-star:not(".reviewed") i', function(){
            var ele = $(this);
            ele.siblings('i').addClass('disable').removeClass('hover');
            ele.addClass('hover');
            ele.prevAll().addClass('hover');

        }).on('mouseout', '.p-rating-star', function(){
            var rating = parent.find('#js_total_rating').val();
            console.log(rating);
            appAdvMarketplace.updateCurrentRaring($(this), rating);
        });

        parent.on('click', '.p-rating-star:not(".reviewed") .ico.ico-star', function(){
            var value = $(this).data('value');
            parent.find('.p-rating-star .ico.ico-star').addClass('disable');
            var count = 0;
            parent.find('.p-rating-star .ico.ico-star').each(function(){
                count++;
               if(count <= value) {
                   $(this).removeClass('disable');
               }
            });
            parent.find('#js_total_rating').val(value);
        });
    }
};

$Behavior.payment_methods = function (){
    $(document).on('click', '.js_advancedmarketplace_select_payment_gateway:not(".disable")' ,function(){
        $('.js_advancedmarketplace_select_payment_gateway').removeClass('active');
        $(this).addClass('active');
    });
};

$Behavior.advancedmarketplace_create = function() {
    var $instant_payment = $('#advancedmarketplace_is_sell'),
        $expiry_date = $('#advancedmarketplace_is_expiry_date');
    if ($instant_payment.length) {
        $instant_payment.change(function(){
            $('#advancedmarketplace_payment_methods').toggle($(this).prop('checked'));
        });
        $instant_payment.change();
        window.onbeforeunload = null;
    }
    if ($expiry_date.length) {
        $expiry_date.change(function(){
            $('#advancedmarketplace_expiry_date').toggle($(this).prop('checked'));
        });
        $expiry_date.change();
        window.onbeforeunload = null;
    }
    $('.p-step-nav-container a.p-step-link').click(function () {
        // remove error message when click another tab
        var sRel = $(this).attr('rel');
        if (empty(sRel)) {
            return true;
        }

        $('#core_js_messages').remove();

        appAdvMarketplace.switchStep(sRel);

        return false;
    });
    var $cancelBtn = $('#js_p_advmarketplace_cancel');
    if ($cancelBtn.length){
        window.setTimeout(function(){
            $('form').off('click');
        }, 1000);
    }
};

var admincpManageListings = {
    resetForm: function(obj){
        let objThis = $(obj);
        let form = objThis.closest('form');
        form.find('.form-control').each(function(){
           let ele = $(this).get(0);
           let type = ele.type || ele.tagName.toLowerCase();
           if(type == 'text' || type == 'select-one') {
               $(this).val('');
           }
        });
        form.submit();
        return false;
    }
}