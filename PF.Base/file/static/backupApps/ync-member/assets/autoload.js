var ynmember = {};
// member slider
(function(ynmember, $){
    var wid;
    var hei;
    var par = $('.ynmember_feature_item').parents('#main');
    var windowsize = $(window).width();
    if(par.hasClass('empty-right')){
        wid = 143;
        hei = 143;
    } else{
        wid = 231;
        hei = 231;
    }
    if(windowsize = 768){
        wid = 180;
        hei = 180;
    }
    if(windowsize = 320){
        wid = 240;
        hei = 240;
    }

    ynmember.member_slidershow =  function(ele){
        if(!ele.length) return;
        var slider = new MasterSlider();
        slider.control('arrows' , {autohide:false});
        slider.setup('ynmember_feature_slider' , {
            loop:true,
            width: wid,
            height: hei,
            speed: 15,
            view:'focus',
            mouse: false,
            wheel: false,
            space: 20,
            autoplay: true
        });

        if (slider.api.view) {
            var $current = slider.api.view.currentSlide.$element.addClass('ynmember_selected');
        } else {
            var $current = $();
        }

        slider.api.addEventListener(MSSliderEvent.CHANGE_START , function(){
            $current.removeClass('ynmember_selected');
            $current = slider.api.view.currentSlide.$element.addClass('ynmember_selected');
        });
        $('.ms-nav-next').addClass('dont-unbind');
        $('.ms-nav-prev').addClass('dont-unbind');
    };

    ynmember.advSearchDisplay = function (title_search) {
        var $form = $('#ynmember_adv_search');
        var $flag = $('#form_flag');
        if ($flag.val() == 1) {
            $form.slideUp(200);
            $flag.val(0);
        }
        else {
            $form.slideDown(200);
            $flag.val(1);
        }

        return false;
    };

    ynmember.initModeView = function (block_id) {
        var yn_viewmodes_block = $('#' + block_id + ' .ynmember-view-modes-block');

        var yn_cookie_viewmodes = getCookie(block_id + 'ynviewmodes');

        //Check if have cookie
        if (!yn_cookie_viewmodes) {
            yn_cookie_viewmodes = 'grid';
        }

        yn_viewmodes_block.attr('class', 'ynmember-view-modes-block');
        yn_viewmodes_block.addClass('yn-viewmode-' + yn_cookie_viewmodes);

        $('#' + block_id + ' .ynmember-view-modes-block span[data-mode=' + yn_cookie_viewmodes + ']').addClass('active');

        $('#' + block_id + ' .yn-view-mode').click(function () {
            //Get data-mode
            var yn_viewmode_data = $(this).attr('data-mode');

            //Remove class active
            $(this).parent('.yn-view-modes').find('.yn-view-mode').removeClass('active');

            //Add class active
            $(this).addClass('active');

            //Set view mode
            yn_viewmodes_block.attr('class', 'ynmember-view-modes-block');
            yn_viewmodes_block.addClass('yn-viewmode-' + yn_viewmode_data);
            setCookie(block_id + 'ynviewmodes', yn_viewmode_data);
        });
    };

    ynmember.show_suggestfriend = function(e)
    {
        // e.preventDefault();
        _iFriendId = $(e).attr('rel');

        if (parseInt($('#bIsAllowSuggestion').html()) == 1)
            suggestion_and_recommendation_tb_show($('#sTitle').html(),$.ajaxBox('suggestion.friends','iFriendId='+_iFriendId));
        else
            suggestion_and_recommendation_tb_show($('#sTitle').html(),$.ajaxBox('suggestion.friends','iFriendId='+_iFriendId+'&sSuggestionType=recommendation'));

        return false;
    };

    ynmember.updateFriendship = function(user_id, id, action)
    {
        var container = $('#ynmember_link_friendship_' + user_id);
        if (container) {
            $.ajaxCall('ynmember.updateFriendship', 'user_id='+user_id+'&id='+id+'&action='+action);
        }
        return false;
    };

    ynmember.shareMember = function(user_id)
    {
        $.ajaxCall('ynmember.shareMember', 'user_id='+user_id);
    };

    ynmember.sendMessage = function(user_id)
    {
        $Core.composeMessage({user_id: user_id});

    };

    ynmember.poke = function(user_id)
    {
        $Core.box('poke.poke', 400, 'user_id=' + user_id);
    };

    ynmember.block = function(user_id)
    {
        // $Core.box('user.block', 420, 'user_id=' + user_id + '&height=100');
        tb_show('Block this User', $.ajaxBox('user.block', 'width=400&height=120&user_id=' + user_id));
    };

    ynmember.giftPoints = function(user_id)
    {
        // $Core.box('core.showGiftPoints', 420, 'user_id=' + user_id + '&height=100');
        tb_show('Gift Points', $.ajaxBox('core.showGiftPoints', 'width=400&height=120&user_id=' + user_id));
    };

    ynmember.followMember = function(user_id)
    {
        $.ajaxCall('ynmember.followMember', 'user_id=' + user_id);
    };

    ynmember.followMemberOnProfile = function(user_id)
    {
        $.ajaxCallwBack('ynmember.followMember', 'user_id=' + user_id, function(){
            setTimeout(function(){
                $Core.reloadPage();
            }, 800);
        });
    };

    ynmember.writeReviewPrevent = function(ele, user_id)
    {
        $(ele).attr('href', 'javascript:void(0)');
        $Core.box('ynmember.writeReview', 500, 'user_id=' + user_id);
    };

    ynmember.rate_member = function(el)
    {
    };

    ynmember.sendBirthdayWish = function(user_id)
    {
        var $form = $('#ynmember_birthday_wish_form_' + user_id);
        if ($form.length && trim($form.find('.js_ynmember_birthday_wish').val())) {
            $form.ajaxCall('ynmember.sendBirthdayWish');
            $('#ynmember_send_bw_btn_' + user_id).remove();
            $form.find('.js_ynmember_birthday_wish').prop('disabled', true);
        }
        return false;
    };

    ynmember.setFeatured = function(user_id, current_featured)
    {
        $.ajaxCall('ynmember.featureMember', 'user_id=' + user_id + '&featured=' + current_featured);
    };

    ynmember.deleteReview = function(review_id)
    {
        $Core.jsConfirm({}, function(){
            $.ajaxCall('ynmember.deleteReview','review_id=' + review_id);
        }, function(){});
        return false;
    };

    ynmember.deletePlace = function(place_id)
    {
        $Core.jsConfirm({}, function(){
            $.ajaxCall('ynmember.deletePlace','place_id=' + place_id);
        }, function(){});
        return false;
    };

    ynmember.voteReview = function(review_id, positive)
    {
        $.ajaxCall('ynmember.voteReview', 'review_id=' + review_id + '&positive=' + positive);
    };

    ynmember.initEditPlace = function(type)
    {
        $('.pac-container').remove();
        if (type == 'addplace') {
            var input = $("#page_ynmember_profile_places #location_address").get(0);
            if (!input) return;
        }
        else if (type == 'search') {
            var input = $('#ynmember_search_form input[name="search[location]"]').get(0);
            if (!input) return;
            $(input).keyup(function() {
               if (this.value == '') {
                   $('input[name="search[location_latitude]"]').val('');
                   $('input[name="search[location_longitude]"]').val('');
               }
            });
        }

        if (window.google){
            // do nothing
            var autocomplete = new google.maps.places.Autocomplete(input);

            $(input).focus(function () {
                if($('.pac-container').length && $('body').css('top')) {
                    var pac_container = $($('.pac-container').get(0));
                    pac_container.css('transform', 'translateY(' + Math.abs(parseInt($('body').css('top'))) + 'px)');
                }
            });

            google.maps.event.addListener(autocomplete, 'place_changed', function() {
                var place = autocomplete.getPlace();
                if (!place.geometry) {
                    return;
                }
                if (type == 'addplace') {
                    $('#location_latitude').val(place.geometry.location.lat());
                    $('#location_longitude').val(place.geometry.location.lng());
                } else if (type == 'search') {
                    $('input[name="search[location_latitude]"]').val(place.geometry.location.lat());
                    $('input[name="search[location_longitude]"]').val(place.geometry.location.lng());
                }
            });
        }
    };

    ynmember.getCurrentPosition = function(type) {
        var result = null;
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                if (position.coords.latitude) {
                    var latLng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                    geocoder = new google.maps.Geocoder();
                    geocoder.geocode({
                            latLng: latLng
                        },
                        function (responses) {
                            if (responses && responses.length > 0) {
                                if (type == 'search') {
                                    $('#ynmember_search_form input[name="search[location]"]').val(responses[0].formatted_address);
                                    $('#ynmember_search_form input[name="search[location_latitude]"]').val(position.coords.latitude);
                                    $('#ynmember_search_form input[name="search[location_longitude]"]').val(position.coords.longitude);

                                }
                                else if (type == 'addplace') {
                                    $("#ynmember_js_place_form #location_address").val(responses[0].formatted_address);
                                    $("#ynmember_js_place_form #location_latitude").val(position.coords.latitude);
                                    $("#ynmember_js_place_form #location_longitude").val(position.coords.longitude);

                                }
                            }

                        }
                    );
                }
            });
        }
        else {
            result = {latitude: -33.8688, longitude: 151.2195};
            // showMapByLatLong('', -33.8688, 151.2195);
        }
    };

    ynmember.toggleReviewComment = function(review_id) {
        var el = $('#ynmember_review_comment_' + review_id);
        if (el.is(':visible')) {
            el.parent('.ynmember_review_block_inner').removeClass('active_comment');
        } else {
            el.parent('.ynmember_review_block_inner').addClass('active_comment');
        }
        el.toggle();
    };

    ynmember.selectYnmemberMenu = function() {
        if(window.location.href.indexOf('members') != -1)
            $('.navbar-nav a[href$="members/"]').addClass('menu_is_selected');
    };

    ynmember.toggleViewmore = function(el) {
        $(el).parents('.js_view_more_parent:first').toggleClass('ynmember_viewmore');
    };

    $(document).on('mouseenter','.ynmember_rate', function(){
        var ele  = $(this);
        ele.prevAll().removeClass('disable');
        ele.nextAll().addClass('disable');
        ele.removeClass('disable');
    }).on('mouseout', '.ynmember_rate', function(){
        var ele  = $(this),
            rating = $('.ynmember_rating_stars').data('rating');
        var i = 0;
        $('.ynmember_rating_stars').find('.ynmember_rate').each(function () {
            if (i < rating) {
                i++;
                $(this).removeClass('disable');
            } else {
                $(this).addClass('disable');
            }
        });
    }).on('click','.ynmember_rate', function () {
        var ele  = $(this);
        $('.ynmember_rating_stars').data('rating', ele.data('value'));
        $('#rating').val(ele.data('value'));
    });

})(ynmember, jQuery);

$Ready(function () {

    (function (ele) {
        if (!ele.length) return;
        if (ele.prop('built')) return;
        ele.prop('built', true);
        var ynmemberSlideshowIntervalId,
            jsFiles = ele.data('js').split(',');

        $Core.loadStaticFiles(jsFiles);

        if (typeof window.MSLayerEffects === 'undefined') {
            // check interval timeout
            ynmemberSlideshowIntervalId = window.setInterval(function () {
                if (typeof window.MSLayerEffects === 'undefined') {
                } else {
                    ynmember.member_slidershow(ele);
                    window.clearInterval(ynmemberSlideshowIntervalId);
                }
            }, 250);
        } else {
            ynmember.member_slidershow(ele);
        }
    })($('#ynmember_feature_slider'));

    if ($('.ynmember_toggle_status').length) {
        $('.ynmember_toggle_status').click(function () {
            $(this).toggleClass('active');
            $(this).siblings('.ynmember_info').find('.ynmember_status').toggleClass('active');
        });
    }
});

$.ajaxCallwBack = function (sCall, sExtra, callback) {
    if ($('body').hasClass('page-loading')) return false;
    return $.fn.ajaxCall(sCall, sExtra, true, 'POST', callback);
};

function closeEditPlace(link) {
    $(link).closest('.dropdown').removeClass('open');
}



