var UltimateVideo = {
    processUploadSuccess: function(ele, file, response) {
        // append to form
        $("#ynuv_add_submit_upload").removeAttr('disabled');
        if(response.encode_id) {
            $('#ynuv_add_video_form').
            append('<div><input id="ynuv_encoding_id" type="hidden" name="val[encoding_id]" value="' +
                response.encode_id + '"></div>');
        }
        else {
            $('#ynuv_add_video_form').
            append('<div><input id="video_path" type="hidden" name="val[video_path]" value="' +
                response.video_path + '"></div>');
        }
        // remove error message
        $('[data-dz-errormessage]').html('');
    },

    processError: function(ele, file, response) {
    },

    processAddedFile: function(ele, file, response) {
    },

    processRemoveButton: function() {
        $("#ynuv_add_submit_upload").attr('disabled', true);
        $('#video_path').remove();
    },

    update_video_rating: function(current_rating, rating, total_rating, text) {
        var $statistic_wrapper = $('.js-p-ultimatevideo-rating');


        if ($statistic_wrapper.length) {
            $statistic_wrapper.find('.p-no-rate-text').hide();
            $statistic_wrapper.find('.p-outer-rating').show();
            $statistic_wrapper.find('.p-rating-count-star').html(rating);
            $statistic_wrapper.find('.p-rating-count-review .item-number').html(total_rating);
            $statistic_wrapper.find('.p-rating-count-review .item-text').html(text);
        }

        $('.p-can-rate').each(function(){
            UltimateVideo.update_current_rating($(this), current_rating);
        });

        $('#public_message').html(oTranslations['rated_successfully']);
        $Behavior.addModerationListener();
    },

    update_current_rating: function(rating_container, current_rating) {
        rating_container.data('rating', current_rating);
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
    bind_privacy_on_add_form: function() {
        $('.ultimatevideo-quick-add-form .privacy_setting_active').off('click').on('click', function(e) {
            $(this).closest('.privacy_setting_div').toggleClass('open');
            e.stopPropagation();
        });

        $('.ultimatevideo-quick-add-form a[data-toggle="privacy_item"]').off('click').on('click', function(e) {
            var element = $(this),
                container = element.closest('.privacy_setting_div'),
                input = container.find('input:first'),
                button = container.find('[data-toggle="dropdown"]'),
                rel = element.attr('rel');

            input.val(rel);

            container.find('.is_active_image').removeClass('is_active_image');
            element.addClass('is_active_image');

            var $sContent = element.html();

            if ($sContent.toLowerCase().indexOf('<span>') > -1) {
                var $aParts = explode('<span>', $sContent);
                if (!isset($aParts[1])) {
                    $aParts = explode('<SPAN>', $sContent);
                }

                $sContent = $aParts[0];
            }

            button.find('span.txt-label').text($sContent);

            container.find('.fa.fa-privacy').replaceWith($('<i/>', {class: 'fa fa-privacy fa-privacy-' + rel}));
            container.removeClass('open');

            e.stopPropagation();
        });
    }
};

(function(videos, $){
    var supportCommands  = ['approve_video','featured_video','unfeatured_video',
        'favorite_video','unfavorite_video','watchlater_video',
        'unwatchlater_video','delete_video_history',
        'featured_playlist','unfeatured_playlist','approve_playlist','delete_playlist_history','sponsor_video'
    ];

    function makeVideoFunction(cmd){
        return function(ele){
            $.ajaxCall('ultimatevideo.'+ cmd, $.param({iVideoId: ele.data('id'), iValue: ele.data('value')||0}));
            return false;
        };
    }

    videos.makeId =  function() {
        var text = "",
            possible = "abcdefghijklmnopqrstuvwxyz01234567890",
            i = 0;

        for(; i < 10; i++ ) {
            text += possible.charAt(Math.floor(Math.random() * possible.length));
        }

        return '_'+text;
    };

    for(var i in supportCommands){
        videos[supportCommands[i]] = makeVideoFunction(supportCommands[i]);
    }

    videos.delete_video = function (ele) {
        var message = ($(ele).data('confirm')) ? $(ele).data('confirm') : oTranslations['are_you_sure'];
        $Core.jsConfirm({message: message}, function () {
            $.ajaxCall('ultimatevideo.delete_video', $.param({iVideoId: ele.data('id'),isDetail: ele.data('detail')}));
        }, function () {
        });
        return false;
    };

    videos.delete_playlist = function (ele) {
        var message = ($(ele).data('confirm')) ? $(ele).data('confirm') : oTranslations['are_you_sure'];
        $Core.jsConfirm({message: message}, function () {
            $.ajaxCall('ultimatevideo.delete_playlist', $.param({iPlaylistId: ele.data('id'),isDetail: ele.data('detail')}));
        }, function () {
        });
        return false;
    };

    videos.show_embed_code = function(ele){
        var block = $('.ultimate_video_html_code_block',ele.closest('.ultimatevideo_video_detail'));
        block.toggleClass('hide');
        if(!block.hasClass('hide')){
            $('textarea', block).get(0).select();
        }
    };

    videos.copy_embed_code =function(ele){
        var block  = $('.ultimate_video_html_code_block',ele.closest('.ultimatevideo_video_detail'));
        $('textarea', block).get(0).select();
    };

    videos.video_clear_all = function(ele){
        $Core.jsConfirm(
            {
                message: oTranslations['are_you_sure_you_want_to_clear_all_videos_from_this_section']
            }, function () {
                    $.ajaxCall('ultimatevideo.video_clear_all',$.param({sView: $(ele).data('view')}));
            }, function () {
            }
        );
    };

    videos.playlist_clear_all = function(ele){
        $Core.jsConfirm(
            {
                message: oTranslations['are_you_sure_you_want_to_clear_all_playlists_from_this_section']
            }, function () {
                $.ajaxCall('ultimatevideo.playlist_clear_all');
            }, function () {
            }
        );
    };
    $(document).on('click','[data-toggle="ultimatevideo"]',function(evt){
        var cmd =  $(this).data('cmd');
        if(!videos.hasOwnProperty(cmd)) return;
        evt.preventDefault();
        return videos[cmd]($(this), evt);
    });

    function countSelectedVideo() {
        var total_count = $('#js_ultimatevideo_playlist_block_video .ultimatevideo_remove').length,
            selected_count = $('#js_ultimatevideo_playlist_block_video .ultimatevideo_remove:checked').length;
        if (selected_count == 0) {
            $('#ultimatevideo_select_all').attr('class', 'ultimatevideo-select-none');
        } else if (selected_count < total_count) {
            $('#ultimatevideo_select_all').attr('class', 'ultimatevideo-select-half');
        } else {
            $('#ultimatevideo_select_all').attr('class', 'ultimatevideo-select-full');
        }

        if (selected_count == 0) {
            $('#ultimatevideo_remove_selected').hide();
        } else {
            $('#ultimatevideo_remove_selected .ultimatevideo_count').text(selected_count);
            $('#ultimatevideo_remove_selected .ultimatevideo_count_label').text(selected_count == 1 ? oTranslations.video : oTranslations.videos);
            $('#ultimatevideo_remove_selected').show();
        }
    }

    $(document).on('click','#ultimatevideo_select_all', function(){
        if ($(this).hasClass('ultimatevideo-select-none')) {
            $('#js_ultimatevideo_playlist_block_video .ultimatevideo_remove').prop('checked', 1);
        } else {
            $('#js_ultimatevideo_playlist_block_video .ultimatevideo_remove').prop('checked', 0);
        }

        countSelectedVideo();
    });

    $(document).on('click','#ultimatevideo_remove_selected', function(){
        $('#js_ultimatevideo_playlist_block_video .ultimatevideo_remove:checked').each(function() {

            var obj = $(this).closest('.ultimatevideo-manage-video-item').find('.ultimatevideo-dragdrop-remove')[0];
            videos.remove_video_from_playlist(obj);
        });

        countSelectedVideo();
    });

    $(document).on('change','.ultimatevideo_remove', countSelectedVideo);

    $(document).on('mouseenter','.p-can-rate i', function(){
        var ele = $(this);
        ele.siblings('i').addClass('disable').removeClass('hover');
        ele.addClass('hover');
        ele.prevAll().addClass('hover');

    }).on('mouseout', '.p-can-rate', function(){
        var ele = $(this),
        rating_container = ele.closest('.p-can-rate'),
            rating = rating_container.data('rating');

        UltimateVideo.update_current_rating(rating_container, rating);
    });

    videos.currentModeViewInPlaylistDetail = function(){
        var ele = $('#ultimatevideo-modeviews-playlist-detail'),
            grid_mode = ele.find('.ultimatevideo-grid.show_grid_view'),
            casual_mode = ele.find('.ultimatevideo-grid.show_casual_view');
        if(grid_mode.length)
            return 1;
        if(casual_mode.length)
            return 2;
    };

    videos.showFormAddPlaylist = function(ele){
        if(!ele.prop('id'))
            ele.prop('id', videos.makeId());
        $(ele).find('.ico').toggleClass('expand');
        $(".ultimatevideo-quick-add-form", $(ele).closest('ul')).toggle();
        return false;
    };

    videos.add_to_playlist = function(ele){
        var input =$('input',ele).get(0),
            checked  = input.checked,
            data ={
                isChecked: checked?0:1,
                iVideoId: ele.data('id'),
                iPlaylistId: ele.data('playlist'),
                iContainerId : ele.closest('ul').find('.ynuv_quick_list_playlist').prop('id'),
            };

        input.checked =  !checked;
        $.ajaxCall('ultimatevideo.updateQuickAddVideoToPlaylist',$.param(data));
        return false;
    };

    videos.slideshows =  function(ele){
        if (!ele.length) return;
        ele.addClass('dont-unbind-children');
        ele.owlCarousel_ynuv({
            navigation: true, // Show next and prev buttons
            slideSpeed: 300,
            paginationSpeed: 400,
            autoPlay: true,
            singleItem: true,
            navigationText: ["<i class='ynicon yn-arr-left'></i>", "<i class='ynicon yn-arr-right'></i>"],
        });
    };

    videos.close_add_form = function(ele){
        $(ele).closest('.ultimatevideo-quick-add-form').css('display','none');
        $(ele).closest('ul').find('.ico-angle-down').toggle();
        $(ele).closest('ul').find('.ico-angle-up').toggle();
        return false;
    };
    videos.add_new_playlist = function(ele){
        var ele = $(ele),
            parent = ele.closest('.ultimatevideo-quick-add-form'),
            sInput = parent.find('input[name="title"]').val(),
            iContainerId = ele.closest('ul').find('.ynuv_quick_list_playlist').prop('id'),
            iPrivacy = parent.find('input#privacy').val();
        if(trim(sInput) == ""){
            parent.find('.ultimatevideo-error').css('display','block');
            return false;
        }
        parent.find('input').val('');
        $(ele).closest('ul').find('.ico-angle-down').toggle();
        $(ele).closest('ul').find('.ico-angle-up').toggle();
        $(".ultimatevideo-quick-add-form", $(ele).closest('ul')).toggle();
        $.ajaxCall('ultimatevideo.addPlaylistOnAction',$.param({id: ele.data('id'), sTitle: sInput,iVideoId: ele.data('id'),iContainerId : iContainerId,iPrivacy: iPrivacy}));
        return false;
    };
    videos.get_embed_code = function(ele){
        $('.ultimatevideo_video_detail-embed_code').toggleClass('hide');
    };
    videos.invite_friend = function(ele){
        alert('this feature is coming soon!');
    };

    videos.remove_video_from_playlist = function(obj){
        var ele = $(obj),
            parent = ele.closest('li.ui-sortable-handle'),
            removed = $('#ynuv_removed_video'),
            removedArr = removed.val();
        if(removedArr === "")
            removed.val(ele.data('video'));
        else
            removed.val(removedArr+','+ele.data('video'));
        parent.remove();
        countSelectedVideo();
    };

    videos.playlist_slideshow =  function(ele){
        if(!ele.length) return;
        var slider = new MasterSlider(),
            previousIndex = [0];

        slider.setup('ultimatevideo_playlist_slideshow_masterslider', {
            width : 1136,
            height : 639,
            space : 0,
            loop : false,
            view : 'basic',
            swipe : false,
            mouse : false,
            speed : 100,
            autoplay : false
        });

        slider.control('arrows');
        if (window.matchMedia('(min-width: 1200px)').matches) {
            if(ele.closest('.location_11').length > 0){
                slider.control('thumblist', {autohide : false,  dir : 'v',width:300,height:80,});
            }
            else{
                if ($('#main.empty-left.empty-right').length > 0){
                    slider.control('thumblist', {autohide : false,  dir : 'v',width:300,height:80,});
                }else{
                    slider.control('thumblist', {autohide : false,  dir : 'h',width:300,height:80,});
                }
            }
        }else{
            slider.control('thumblist', {autohide : false,  dir : 'h',width:300,height:80,});
        }
        slider.api.addEventListener(MSSliderEvent.INIT , function(){
            // dispatches when the slider's current slide change starts.
            $('#ultimatevideo_playlist_slideshow_masterslider').find('.ms-thumb-list').unbind( "mousewheel" );
            $('#ultimatevideo_playlist_slideshow_masterslider').find('.ms-thumb-list.ms-dir-v .ms-thumbs-cont').mCustomScrollbar({
              theme: "minimal-dark",
              mouseWheel: {preventDefault: true}
            }).addClass('dont-unbind-children');
            $('#ultimatevideo_playlist_slideshow_masterslider').find('.ms-thumb-list.ms-dir-h').mCustomScrollbar({
              theme: "minimal-dark",
              mouseWheel: {preventDefault: true}
            }).addClass('dont-unbind-children');
        });

        slider.api.addEventListener(MSSliderEvent.INIT , function(){
            var ynultimatevideo_contain_btn = $('.ultimatevide_playlist_mode_slide .ms-container');
            var ynultimatevideo_btn_action = $('.ultimatevideo_playbutton-block');

            if(ynultimatevideo_btn_action.length){
                ynultimatevideo_btn_action.appendTo(ynultimatevideo_contain_btn);
            }
        });
        window.ulvideoplaylist_slider_clone_reinit = slider;

        var player = new ynultimatevideoPlayer(slider);
        window.currentynultimatevideoPlayer = player;

        slider.api.addEventListener(MSSliderEvent.CHANGE_START , function(){
            var current = slider.api.index();
            previousIndex.push(current);
            player.removeAllPlayers();
        });

        slider.api.addEventListener(MSSliderEvent.CHANGE_END , function(){
            player.updateCurrentSlideInfo();
            player.init(true);
        });

        window.ynultimatevideoPlay = function() {
            player.init(true);
        };

        window.ynultimatevideoNext = function() {
            var min = 0;
            var total = slider.api.count();
            var current = slider.api.index();
            var status = getPlayingStatus();
            if (status.shuffle) {
                slider.api.gotoSlide(generateRandom(current, min, total));
            } else if (status.repeat && current == total - 1) {
                slider.api.gotoSlide(0);
            } else if (current < total - 1) {
                slider.api.next();
            }
        };

        window.ynultimatevideoAutoNext = function() {
            var status = getPlayingStatus();
            if (status.auto_play) {
                ynultimatevideoNext();
            }
        };

        window.ynultimatevideoPrev = function() {
            var pos = previousIndex.pop();
            pos = previousIndex.pop();
            if (pos != null){
                slider.api.gotoSlide(pos);
            } else {
                slider.api.gotoSlide(0);
            }
        };
    };
    videos.playlist_slideshow_landingpage = function(ele){
        var sync1 = $("#ultimatevideo_slider_featured-1");
        var sync2 = $("#ultimatevideo_slider_featured-2");
        sync1.addClass('dont-unbind-children');
        sync2.addClass('dont-unbind-children');
        sync1.owlCarousel_ynuv({
            singleItem: true,
            slideSpeed: 1000,
            navigation: false,
            autoPlay: true,
            pagination: false,
            // navigationText: ["<i class='ynicon yn-arr-left'></i>", "<i class='ynicon yn-arr-right'></i>"],
            afterAction: syncPosition,
            responsiveRefreshRate: 200,
        });

        sync2.owlCarousel_ynuv({
            items: 6,
            singleItem: false,
            pagination: false,
            navigation: true,
            navigationText: ["<i class='ynicon yn-arr-left'></i>", "<i class='ynicon yn-arr-right'></i>"],
            responsiveRefreshRate: 100,
            itemsCustom : [
                [0, 2],
                [450, 4],
                [600, 6],
                [700, 6],
                [1000, 6],
                [1200, 6],
            ],
            afterInit: function(el) {
                el.find(".owl-item").eq(0).addClass("synced");
            },
            beforeInit : beforeInit,

        });

        function beforeInit(){
           var item_length = $("#ultimatevideo_slider_featured-2 .ultimatevideo-video-entry").length;

           if(item_length <= 6){
                $('#ultimatevideo_slider_featured-2').addClass('uv-nopadding');
           }
        }

        function syncPosition(el) {
            var current = this.currentItem;
            $("#ultimatevideo_slider_featured-2")
                .find(".owl-item")
                .removeClass("synced")
                .eq(current)
                .addClass("synced")
            if ($("#ultimatevideo_slider_featured-2").data("owlCarousel_ynuv") !== undefined) {
                center(current)
            }
        }

        $("#ultimatevideo_slider_featured-2").on("click", ".owl-item", function(e) {
            e.preventDefault();
            var number = $(this).data("owlItem");
            sync1.trigger("owl.goTo", number);
        });

        function center(number) {
            var sync2visible = sync2.data("owlCarousel_ynuv").owl.visibleItems;
            var num = number;
            var found = false;
            for (var i in sync2visible) {
                if (num === sync2visible[i]) {
                    var found = true;
                }
            }

            if (found === false) {
                if (num > sync2visible[sync2visible.length - 1]) {
                    sync2.trigger("owl.goTo", num - sync2visible.length + 2)
                } else {
                    if (num - 1 === -1) {
                        num = 0;
                    }
                    sync2.trigger("owl.goTo", num);
                }
            } else if (num === sync2visible[sync2visible.length - 1]) {
                sync2.trigger("owl.goTo", sync2visible[1])
            } else if (num === sync2visible[0]) {
                sync2.trigger("owl.goTo", num - 1)
            }
        }
    }
})(UltimateVideo, jQuery);

$Ready(function() {
    var videoFrameClass = [
        '.youtube_iframe_big',
        '.youtube_iframe_small',
        '.vimeo_iframe_small',
        '.vimeo_iframe_big',
        '.facebook_iframe',
        '.dailymotions_iframe_small',
        '.dailymotions_iframe_big'
    ], videoAspec =  16/9;

    (function(eles){
        setTimeout(function(){
            eles.each(function(index, ele){
                var $ele =  $(ele),
                    parent = $ele.parent();
                $ele.data('built', true);
                $ele.css("width", parent.width());
                $ele.css("height", parent.width()/videoAspec);
            });
        },300);
    })($(videoFrameClass.join(', ')).not('.built'));

    (function(ele){
        if(!ele.length) return;
        if(ele.prop('built')) return;
        ele.prop('built',true);
        var ultimatevideoSlideshowIntervalId;

        if(typeof $.fn.owlCarousel_ynuv == 'undefined'){
            $Core.loadStaticFiles(ele.data('js'));
            // check interval timeout
            ultimatevideoSlideshowIntervalId = window.setInterval(function(){
                if(typeof $.fn.owlCarousel_ynuv == 'undefined'){
                }else{
                    UltimateVideo.slideshows(ele);
                    window.clearInterval(ultimatevideoSlideshowIntervalId);
                }
            },250);
        }else{
            UltimateVideo.slideshows(ele);
        }
    })($('#ultimatevideo_slider_featured'));

    (function(ele){
        if(!ele.length) return;
        $Core.loadStaticFile([ele.data('validjs'), ele.data('addjs')]);

    })($('#js_ultimatevideo_block_detail'));

    (function(ele){

        if(!ele.length) return;
        $Core.loadStaticFile([ele.data('validjs'), ele.data('addjs')]);

    })($('#js_ultimatevideo_playlist_block_detail'));

    (function(ele){
        if(!ele.length) return;
        if(ele.prop('built')) return;
        ele.prop('built',true);
        var ultimatevideoPlaylistSlideshowIntervalId;

        if(typeof window.MSLayerEffects_ynuv == 'undefined'){
            // check interval timeout
            ultimatevideoPlaylistSlideshowIntervalId = window.setInterval(function(){
                if(typeof window.MSLayerEffects_ynuv == 'undefined'){
                }else{
                    UltimateVideo.playlist_slideshow(ele);
                    window.clearInterval(ultimatevideoPlaylistSlideshowIntervalId);
                }
            },250);
        }else{
            UltimateVideo.playlist_slideshow(ele);
        }
    })($('#ultimatevideo_playlist_slideshow_masterslider'));

    (function(ele){
        if(!ele.length) return;
        if(ele.prop('built')) return;
        ele.prop('built',true);
        var ultimatevideoSlideshowPlaylistLandingIntervalId;

        if(typeof $.fn.owlCarousel_ynuv == 'undefined'){
            $Core.loadStaticFiles(ele.data('js'));
            // check interval timeout
            ultimatevideoSlideshowPlaylistLandingIntervalId = window.setInterval(function(){
                if(typeof $.fn.owlCarousel_ynuv == 'undefined'){
                }else{
                    UltimateVideo.playlist_slideshow_landingpage(ele);
                    window.clearInterval(ultimatevideoSlideshowPlaylistLandingIntervalId);
                }
            },250);
        }else{
            UltimateVideo.playlist_slideshow_landingpage(ele);
        }
    })($('#ultimatevideo_slider_featured-1'));

    $(document).on('click', '.js-ultimatevideo-addto', function(e) {
        console.log('click');
        e.stopPropagation();
    });

    $('.ultimatevideo_playlist_detail_actions-toggle').click(function(){
        $('.ms-thumb-list').toggle();
        $(this).toggleClass('active');
        $('.ultimatevide_playlist_mode_slide').toggleClass('uv-close');
        /*$(window).trigger('resize');*/
        ulvideoplaylist_slider_clone_reinit.api.__resize();
    });

    //Support add video from feed
    if (ynuv_app_enabled != '1') {
        return;
    }
    if( $('.select-video-upload').length){
        //$('.select-video-upload').parent().remove();
    }

    // Upload routine for videos
    var m = $('#page_core_index-member .activity_feed_form_attach, #panel .activity_feed_form_attach'), p = $('#page_pages_view .activity_feed_form_attach'), g = $('#page_groups_view .activity_feed_form_attach'), v = $('.select-ult-video-upload'), b = $('#ynuv_upload_form_input');
    if (m.length && !v.length) {
        var html = '<li><a href="#" class="select-ult-video-upload" rel="custom"><span class="activity-feed-form-tab">' + uv_phrases.video + '</span></a></li>';

        m.append(html);
    }
    if (p.length && !v.length && can_post_ult_video_on_page == 1) {
        var html = '<li><a href="#" class="select-ult-video-upload" rel="custom"><span class="activity-feed-form-tab">' + uv_phrases.video + '</span></a></li>';
        p.append(html);
    }

    if (g.length && !v.length && can_post_ult_video_on_group == 1) {
        var html = '<li><a href="#" class="select-ult-video-upload" rel="custom"><span class="activity-feed-form-tab">' + uv_phrases.video + '</span></a></li>';
        g.append(html);
    }

    $('.activity_feed_form_attach a:not(.select-ult-video-upload)').click(function() {
        $('.process-ult-video-upload').remove();
        $('.activity_feed_form .error_message').remove();
    });

    $('.select-ult-video-upload').click(function() {
        $('.pf_v_url_cancel').remove();
        $('.activity_feed_form_attach a.active').removeClass('active');
        $(this).addClass('active');
        $('.global_attachment_holder_section').hide().removeClass('active');
        $('.activity_feed_form_button').show();
        $('.ynuv_upload_form .pf_select_video').show();
       /* $('#activity_feed_submit').hide();*/
       //fix material because flex !important
        setTimeout(function(){
            $('.process-video-upload').remove();
        }, 500);
        $('#activity_feed_submit').attr('style','display:none !important');
        //end
        $('#activity_feed_textarea_status_info').attr('placeholder', $('<div />').html(uv_phrases.say).text()).show();

        add_uv_video_button();
        var l = $('#global_attachment_ult_videos');
        if (l.length == 0) {
            var m = $('<div id="global_attachment_ult_videos" class="global_attachment_holder_section" style="display:block;"><div style="text-align:center;"><i class="fa fa-spin fa-circle-o-notch"></i></div></div>');
            $('.activity_feed_form_holder').prepend(m);

            $Core.ajax('ultimatevideo.loadFormAddOnFeed',
                {
                    type: "POST",
                    success: function(e){
                        m.html(e);
                        $('.activity_feed_form_button_status_info').show();
                        $('.activity_feed_form_holder .feed-location-info.active').removeClass('active');
                        $('.activity_feed_form_holder .feed-location-info').addClass('hide');
                        $Core.loadInit();
                    }
                }
            );
        }
        else {
            $('#ultvideoUpload').val('');
            $('#ynuv_add_video_code').val('');
            $('#ynuv_add_video_source').val('');
            l.show();
            $('.activity_feed_form_button_status_info').show();
            $('.activity_feed_form_holder .feed-location-info.active').removeClass('active');
            $('.activity_feed_form_holder .feed-location-info').addClass('hide');
            $Core.loadInit();
        }

        return false;
    });

    $('.process-ult-video-upload').click(function() {
        var t = $(this);

        // t.hide();
        // t.before('<span class="form-spin-it"><i class="fa fa-spin fa-circle-o-notch"></i></span>');
        var f = $(this).parents('form:first');
        f.find('.error_message').remove();

        if( ynultimatevideo_extract_code_on_feed($('#ynuv_add_video_input_link').val()) == false ){

            $('.ynuv_upload_form').prepend('<div class="error_message">'+ uv_phrases.not_valid_video + '</div>');
            t.show();
            return false;
        }
        t.addClass('in_process');

        f.attr('action',PF.url.make('/ultimatevideo/upload'));
        f.append('<input type="hidden" name="val[prev_url]" value="' +  window.location.href  + '">');
        f[0].submit();
        return false;
    });


    var ynuv_url_changed = function() {
        $('.yn_uv_video_info').show();
        $('.yn_uv_video_url .extra_info').removeClass('hide_it');
        $('.uv_select_video').slideUp();
        $('#activity_feed_submit').removeClass('button_not_active'); // .attr('disabled', false);
        $('#__form_caption').parent().parent().hide();
        $('.activity_feed_form_button_status_info').show();
    };

    $('#__form_url').focus(ynuv_url_changed);

    $('.yn_uv_url_cancel').click(function() {
        $(this).parent().addClass('hide_it');
        $('.uv_select_video').slideDown();
        $('.yn_uv_video_url #__form_url').val('');
        var f = $(this).parents('form:first');
        f.find('.error_message').remove();
        $('.process-ult-video-upload').hide();
        $('.activity_feed_form_button_status_info').hide();
        // $('.yn_uv_video_info').hide();

        return false;
    });

    $('.yn_uv_upload_cancel').click(function() {
        $(this).parent().addClass('hide_it');
        $('.yn_uv_video_url').slideDown();
        var f = $(this).parents('form:first');
        f.find('.error_message').remove();
        $('.activity_feed_form_button_status_info').hide();

        return false;
    });

    $('.yn_uv_message_cancel').click(function() {
        $(this).parent().addClass('hide_it');
        $('.yn_uv_video_url').show();
        $('.uv_select_video').show();
        $('.uv_upload_form').slideDown();
        $('.uv_video_message').hide();
        $('.uv_upload_form .message').remove();
        $('.process-ult-video-upload').remove();
        $('#uv_video_id_temp').remove();
        $('.uv_video_caption input').val('');

        return false;
    });

    $('#js_activity_feed_form, .uv_is_popup').submit(function() {
        // console.log('form submit...');
        if ($('.select-ult-video-upload').hasClass('active')) {
            console.log('form submit...');
            // $(this).find('.btn-primary').attr('disabled', true);
        }
    });

    UltimateVideo.bind_privacy_on_add_form();

    $('#page_ultimatevideo_add .p-tab-link').click(function(){
        var video_type = $(this).attr('aria-controls');
        $('#ynuv_add_video_type').length && $('#ynuv_add_video_type').val(video_type);
        $('.js_ultimatevideo_btn').hide();
        $('.js_ultimatevideo_btn_' + video_type).show();
    });

    $('.ultimatevideo_more_info').click(function() {
        $([document.documentElement, document.body]).animate({
            scrollTop: $("#ultimatevideo_playlist_title").offset().top - 50
        }, 500);
        return false;
    });

    $('.ultimatevideo-item-option-container .dropdown').on('show.bs.dropdown', function(){
       
        var ele = $(this);
        if(ele.find('.ynuv_quick_list_playlist').length > 0){
            if(ele.find('.ynuv_quick_list_playlist_wrapper').length > 0){
                ynuv_dropdown_scrollto(ele);
            }
        }else{
            ynuv_dropdown_scrollto(ele);
        }
        $(this).closest('.ultimatevideo-item').addClass('has-open-dropdown');

    }).on('hidden.bs.dropdown', function(){
        var ele = $(this);
        setTimeout(function(){ 
            ele.removeClass('dropup');
        }, 500);
        
        $(this).closest('.ultimatevideo-item').removeClass('has-open-dropdown');
    });
});
var ynuv_dropdown_scrollto = function(ele){
    var eleOffset = ele.offset(),
        scrollBottom = $(window).scrollTop() + $(window).height(),
        eleHeight = ele.outerHeight(),
        menuHeight = ele.find('.dropdown-menu').outerHeight() + 30,
        compareRange = scrollBottom - eleOffset.top - eleHeight;
    if (window.matchMedia('(min-width: 768px)').matches){
        if(ele.closest('.layout-right').length >0 || ele.closest('.layout-left').length >0){
            if(compareRange > 0 && compareRange < menuHeight ){
                ele.addClass('dropup');
            }    
        }else{
            if(compareRange > 0 && compareRange < menuHeight ){
                //window.scrollBy(0, 300);
                $('html, body').animate({
                  scrollTop: $(window).scrollTop() + menuHeight - compareRange
                }, 300);
            }
        }
    }else{
        if(compareRange > 0 && compareRange < menuHeight ){
            //window.scrollBy(0, 300);
            $('html, body').animate({
              scrollTop: $(window).scrollTop() + menuHeight - compareRange
            }, 300);
        }
    }
};
var ynuv_videoUpload = function(e) {
    var t = $('.process-ult-video-upload');
        t.hide();
        t.before('<span class="form-spin-it"><i class="fa fa-spin fa-circle-o-notch"></i></span>');

    var files = e.target.files || e.dataTransfer.files;
    for (var i = 0, f; f = files[i]; i++) {
        $.ajax({
            type: "POST",
            url: PF.url.make('/ultimatevideo/upload'),
            data: new FormData(),
            contentType: false,       // The content type used when sending data to the server.
            cache: false,             // To unable request pages to be cached
            processData: false,
            success: function (e) {
            }
        });
    }
};
var add_uv_video_button = function() {
    if($('.process-ult-video-upload').length) return;
    $('#activity_feed_submit').before('<a href="#" class="button btn btn-gradient mr-1 btn-primary process-ult-video-upload">'+uv_phrases.save+'</a>');
    $Core.loadInit();
};
var ynultimatevideo_extract_code_on_feed = function(url) {

    if(($('#video_path').length && $('#video_path').val() != '') || ($('#ynuv_encoding_id').length && $('#ynuv_encoding_id').val() != ''))
    {
        $('#ynuv_add_video_source').val('Uploaded');
        return true;
    }

    var videoid = url.match(/(?:https?:\/{2})?(?:w{3}\.)?youtu(?:be)?\.(?:com|be)(?:\/watch\?v=|\/)([^\s&]+)/);
    if(videoid){
        $('#ynuv_add_video_source').val('Youtube');
        $('#ynuv_add_video_code').val(videoid[1]);
        return true;
    }

    videoid = url.match(/(?:https?:\/{2})?(?:w{3}\.)?vimeo.com\/(\d+)($|\/)/);
    if (videoid){
        $('#ynuv_add_video_source').val('Vimeo');
        $('#ynuv_add_video_code').val(videoid[1]);
        return true;
    }

    videoid = url.match(/^.+dailymotion.com\/(video|hub)\/([^_\/\?]+)[^#]*(#video=([^_&]+))?/);
    if (videoid){
        $('#ynuv_add_video_source').val('Dailymotion');
        $('#ynuv_add_video_code').val(videoid[2]);
        return true;
    }

    videoid = url.match(/http(?:s?):\/\/(?:www\.|web\.|m\.)?facebook\.com\/([A-z0-9\.]+)\/videos(?:\/[0-9A-z].+)?\/(\d+)(?:.+)?$/);
    if (videoid){
        $('#ynuv_add_video_source').val('Facebook');
        $('#ynuv_add_video_code').val(videoid[2]);
        return true;
    }

    var ext = url.substr(url.lastIndexOf('.') + 1);
    if (ext.toUpperCase() == 'MP4'){
        $('#ynuv_add_video_source').val('VideoURL');
        return true;
    }
    var code = url.match(/(<iframe.*? src=(\"|\'))(.*?)((\"|\').*)/);
    if(code && code.length > 2)
    {
        $('#ynuv_add_video_source').val('Embed');
        $("#ynuv_add_video_code").val(code[3]);
        return true;
    }
    return false;
}

function getPlaylistToQuickAddVideo(obj)
{
    var ele  = $(obj),
        videoId = ele.data('id'),
        imgPath = ele.data('imgpath'),
        isExpand = ele.attr('aria-expanded'),
        container = $('.ynuv_quick_list_playlist', ele.closest('div'));

    if(!container.prop('id')){
        container.prop('id', UltimateVideo.makeId());
    }

    if(isExpand == 'false'){
        $(".ynuv_quick_list_playlist_" + videoId).html('<div class="text-center"><img src="'+imgPath+'"/></div>');
    }

    $.ajaxCall('ultimatevideo.getAllPlaylistOfUser',$.param({id: videoId, eleId: container.prop('id')}));
}

$Behavior.ultimatevideo_init_video_slider = function () {
    var owl = $('.ultimatevideo-slider-video-container-js');
    /*if (!owl.length || owl.prop('built')) {
        return false;
    }
    owl.prop('built', true);*/
    var rtl = false;
    if ($("html").attr("dir") == "rtl") {
        rtl = true;
    }
    var item_amount = parseInt(owl.find('.item').length);
    var more_than_one_item = item_amount > 1;
    var layout_col = 1;
    if($('#main:not(.empty-right):not(.empty-left)').length > 0){
        layout_col = 3;
    }else if( ($('#main.empty-right:not(.empty-left)').length > 0) || ($('#main.empty-left:not(.empty-right)').length > 0)){
        layout_col = 2;
    }
    var stagepadding = false;
    var item_nav = false;
    if (window.matchMedia('(min-width: 1200px)').matches) {
        if ( (layout_col == 1) || (layout_col == 2) ){
                stagepadding = 120;
            item_nav = true;
        }
    } else if( window.matchMedia('(min-width: 992px)').matches){
        if(layout_col == 1){
                stagepadding = 120;
            item_nav = true;
        }
    }
    owl.owlCarousel({
        rtl: rtl,
        items: 1,
        nav: item_nav,
        dots:true,
        smartSpeed: 800,
        navText: ["<i class='ico ico-angle-left'></i>", "<i class='ico ico-angle-right'></i>"],
        margin: 16,
        autoplay: false,
        autoplayTimeout: 5500,
        stagePadding:stagepadding,
        loop:more_than_one_item
    });
};

$Behavior.ultimatevideo_init_category_slider = function () {
    var owl = $('.ultimatevideo-slider-category-container-js');
    /*if (!owl.length || owl.prop('built')) {
        return false;
    }
    owl.prop('built', true);*/
    var rtl = false;
    if ($("html").attr("dir") == "rtl") {
        rtl = true;
    }
    var item_amount = parseInt(owl.find('.item').length);

    var layout_col = 1;
    if($('#main:not(.empty-right):not(.empty-left)').length > 0){
        layout_col = 3;
    }else if( ($('#main.empty-right:not(.empty-left)').length > 0) || ($('#main.empty-left:not(.empty-right)').length > 0)){
        layout_col = 2;
    }
    var item_show = 2;
    var item_margin = 16;
    if (window.matchMedia('(min-width: 1200px)').matches) {
        if ( (layout_col == 1) || (layout_col == 2) ){
            item_show = 3;
            item_margin = 24;
        }
    } else if( window.matchMedia('(min-width: 992px)').matches){
        if(layout_col == 1){
            item_show = 3;
            item_margin = 24;
        }
    }

    owl.owlCarousel({
        rtl: rtl,
        items: item_show,
        nav: true,
        dots:true,
        smartSpeed: 800,
        navText: ["<i class='ico ico-angle-left'></i>", "<i class='ico ico-angle-right'></i>"],
        margin: item_margin,
        autoplay: false,
        autoplayTimeout: 5500,
        loop:false,
        responsive:{
            0:{
                items: 1
            },
            481:{
                items: item_show
            }
        }
    });

    var $core_like_btn = $('.ultimatevideo_video_detail-comment a.js_like_link_toggle').filter(':first');
    var $likeBtn = $('#ultiamtevideo_like_btn');
    $likeBtn.click(function(){
        $core_like_btn.trigger('click');
    });

    var updateLikeBtn = function() {
        if ($core_like_btn.hasClass('liked')) {
            $likeBtn.find('.ico').removeClass('ico-thumbup-o').addClass('ico-thumbup');
            $likeBtn.find('.item-text').html(oTranslations.liked);
            $likeBtn.blur();
        } else {
            $likeBtn.find('.ico').removeClass('ico-thumbup').addClass('ico-thumbup-o');
            $likeBtn.find('.item-text').html(oTranslations.like);
            $likeBtn.blur();
        }
    };

    if ($core_like_btn.length) {
        var config = {attributes: true};

        var callback = function (e) {
            updateLikeBtn();
        };

        var observer = new MutationObserver(callback);

        observer.observe($core_like_btn.get(0), config);
    }
};

$Behavior.ultimatevideo_init_ating = function() {
    if (!$('.p-can-rate').length) {
        return;
    }

    $('.p-can-rate .ico.ico-star').click(function(){
        var ele = $(this);
        $.ajaxCall('ultimatevideo.rate_video', $.param({iVideoId: ele.data('id'), iValue: ele.data('value') || 0}));
        return false;
    });
};
