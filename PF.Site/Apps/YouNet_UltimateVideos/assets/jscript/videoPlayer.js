function ynultimatevideoPlayer(slider) {
    // @TODO consider using adapter for each type
    var currentVideoTitle = '';
    var currentVideoHref = '';
    var previousVideoId = 0;
    var currentVideoId = 0;
    var isPlaying = false;

    function switchPlayState(state) {
        isPlaying = state;
    }

    function getPlayingStatus() {
        var status = {
            continue: 0,
            repeat: 0,
            shuffle: 0
        };
        $(".ynultimatevideo_status_button").each(function(){
            status[$(this).data('status')] = $(this).hasClass('active') ? 1 : 0;
        });
        return status;
    }

    function fixMEJSCss() {
        $('#player_' + currentVideoId).css('margin', '0').find('iframe').css('height', '100%');
        $('.mejs__overlay').css('width','100%').css('height','100%');
        $('.mejs-container').each(function(){
            $(this).css('width','100%').css('height','100%');
        });
        $('.mejs-layer').each(function(){
            $(this).css('width','100%').css('height','100%');
        });
        $('.mejs-controls').each(function(){
            $(this).css('width','100%').css('bottom','0');
        });
    }

    this.updateCurrentSlideInfo = function() {
        var $slideItemEle = slider.api.view.currentSlide.$element;
        previousVideoId = currentVideoId;
        currentVideoId = $slideItemEle.find('.video_id').val();
        // update title
        currentVideoTitle = $slideItemEle.find('.title').val();
        currentVideoHref = $slideItemEle.find('.href').val();
        $("#ynuv_current_video_info-title").show();
        $("#ynuv_current_video_title").text(currentVideoTitle);
        $("#ynuv_current_video_statistic").html($slideItemEle.find('.item-statistic').html());
        $("#ynuv_current_video_addto").html($slideItemEle.find('.item-addto').html());
        UltimateVideo.bind_privacy_on_add_form();
        // update href
        $("#current_video_href").attr('href',currentVideoHref);
    };

    this.init = function(startPlaying) {
         //init playing or continue playing
        previousVideoId = currentVideoId;
        if(currentVideoId == 0){
            this.updateCurrentSlideInfo();
        }
        if (startPlaying) {
            this.play();
        } else {
            $('.ultimatevideo_btn_playlist_play').show();
        }
    };

    this.removeAllPlayers = function() {
        // remove previous player
        $('.ynultimatevideo-player').each(function() {
            switch($(this).data('type')) {
                case 1:
                case 6:
                case 7:
                    if ($(this)[0] && $(this)[0].player) {
                        $(this)[0].player.remove();
                    }
                    break;
                case 2:
                    var data = {method:'unload'};
                    var message = JSON.stringify(data);
                    $(this)[0].contentWindow.postMessage(message,'*');
                    $(this).hide();
                    break;
                case 4:
                    $(this).hide();
                    $(this).html('');
                    break;
                case 3:
                case 5:

                    break;
            }
        });
    };

    /**
     * 1 youtube
     * 2 vimeo
     * 3 uploaded
     * 4 dailymotion
     * 5 url
     * 6 embed
     * 7 facebook
     **/
    this.play = function() {
        if( typeof(jQuery) == 'undefined'){
            $Core.loadStaticFile(document.getElementById('ultimatevideo_playlist_slideshow_masterslider').data('jquery'));
        }
        var player = $('#player_' + currentVideoId),
            videoType = player.data('type');

        $('.ultimatevideo_btn_playlist_play').hide();
        switch (videoType) {
            case 1:
            case 7:

                player.mediaelementplayer({
                    startVolume: 1,
                    success: function(mediaElement, domObject) {
                        mediaElement.play();
                        // set play state for slide change
                        mediaElement.addEventListener('canplay', function() {
                            mediaElement.play();
                        });
                        mediaElement.addEventListener('play', function() {
                            fixMEJSCss();
                            switchPlayState(true);
                        });
                        mediaElement.addEventListener('ended', function() {
                            // set this to overide last second pause action of player
                            switchPlayState(true);
                            ynultimatevideoAutoNext();
                        });
                        mediaElement.addEventListener('pause', function() {
                            switchPlayState(false);
                        });
                    }
                });
                break;
            case 4:
                player.show();
                var video_code = player.data('code'),
                dailymotion_iframe = '<div id="player_' + currentVideoId + '_iframe"></div>';
                player.html(dailymotion_iframe);
                var DMplayer = DM.player(document.getElementById('player_' + currentVideoId + '_iframe'), {
                    video: video_code,
                    width: '100%',
                    height: '100%',
                    params: {
                        autoplay: true
                    }
                });
                DMplayer.addEventListener('play', function(event){
                    switchPlayState(true);
                });
                DMplayer.addEventListener('pause', function(event){
                    switchPlayState(false);
                });
                DMplayer.addEventListener('ended', function(event){
                    switchPlayState(true);
                    ynultimatevideoAutoNext();
                });
                break;
            case 3:
            case 5:
                player.show();
                player.mediaelementplayer({
                    success: function(mediaElement, domObject) {
                        // set play state for slide change
                        switchPlayState(true);
                        fixMEJSCss();
                        mediaElement.play();
                        mediaElement.setMuted(false);
                        mediaElement.addEventListener('play', function() {
                            fixMEJSCss();
                            // when go back to mp4 playerplay evnet is trigger, check this to skip this event
                            if (mediaElement.currentTime > 0.5) {
                                switchPlayState(true);
                            }
                        });
                        mediaElement.addEventListener('ended', function() {
                            switchPlayState(true);
                            ynultimatevideoAutoNext();
                        });
                        mediaElement.addEventListener('pause', function() {
                            fixMEJSCss();
                            // when changing slide, pause event for url is triggered, use this check to skip at ending
                            if (mediaElement.currentTime > 0.5) {
                                switchPlayState(false);
                            }
                        });
                        mediaElement.addEventListener('timeupdate', function() {
                            fixMEJSCss();
                        });
                    }
                });
                break;
            case 2:
                var iframe = player[0];
                $(iframe).show();
                var VimeoPlayer = $f(iframe);

                // When the player is ready, add listeners for pause, finish, and playProgress
                VimeoPlayer.addEvent('ready', function() {

                    var data = {method:'play'};
                    var message = JSON.stringify(data);
                    iframe.contentWindow.postMessage(message,'*');
                    switchPlayState(true);

                    VimeoPlayer.addEvent('pause', function(){
                        //switchPlayState(false);
                    });
                    VimeoPlayer.addEvent('finish', function(){
                        switchPlayState(true);
                        ynultimatevideoAutoNext();
                    });
                });
                break;
            case 6:
                player.show();
        }
    };
}