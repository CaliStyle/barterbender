<!-- Base MasterSlider style sheet -->
<link href="{$corePath}/assets/jscript/masterslider/style/masterslider.css" rel='stylesheet' type='text/css'>
<!-- Master Slider Skin -->
<link href="{$corePath}/assets/jscript/masterslider/skins/default/style.css" rel='stylesheet' type='text/css'>
<!-- MasterSlider Template Style -->
<link href='{$corePath}/assets/jscript/masterslider/style/ms-videogallery.css' rel='stylesheet' type='text/css'>

<link href='{$corePath}/assets/jscript/mediaelementplayer/mediaelementplayer.css' rel='stylesheet' type='text/css'>

{if !empty($aItems)}
    <div class="p-detail-top-content">
        <div class="ultimatevide_playlist_mode_slide" id="ultimatevide_playlist_mode_slide">
            <div id="ultimatevideo-playlist-detail-slide"
                 class="ms-videogallery-template  ms-videogallery-vertical-template ">
                <div class="master-slider " id="ultimatevideo_playlist_slideshow_masterslider"
                     data-jquery="{$corePath}/assets/jscript/masterslider/jquery.easing.min.js">
                    <div class="ultimatevideo_playbutton-block">
                        <a class="ultimatevideo_playbutton ultimatevideo_btn_playlist_pre" href="javascript:void(0)"
                           onclick="ynultimatevideoPrev();"><i class="ico ico-angle-left"></i></a>
                        <a class="ultimatevideo_playbutton ultimatevideo_btn_playlist_play" href="javascript:void(0)"
                           onclick="ynultimatevideoPlay();"><i class="ico ico-play-circle-o"></i></a>
                        <a class="ultimatevideo_playbutton ultimatevideo_btn_playlist_next" href="javascript:void(0)"
                           onclick="ynultimatevideoNext();"><i class="ico ico-angle-right"></i></a>
                    </div>
                    {foreach from=$aItems name=video item=aItem}
                        {template file="ultimatevideo.block.entry_video_on_playlist"}
                    {/foreach}

                    <div class="ultimatevideo-playlist-detail-main-info">
                        <div class="item-wrapper-info" id="ynuv_current_video_info-title">
                            <span class="item-title" id="ynuv_current_video_title"></span>
                            <div class="item-statistic" id="ynuv_current_video_statistic"></div>
                        </div>
                        <div class="item-wrapper-action">
                            <div id="ynuv_current_video_addto">
                            </div>
                            <div class="item-action">
                                <a href="#" class="item-action-btn no_ajax" id="current_video_href" target="_blank"
                                   title="{_p var='watch_this_video_in_new_window'}">
                                    <i class="ico ico-external-link"></i>
                                </a>
                            </div>
                            <div class="item-action">
                                <div class="item-action-btn ultimatevideo_playlist_detail_actions-toggle"
                                     title="{_p var='theater_mode'}">
                                    <i class="icon-custom"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ultimatevideo-playlist-detail-thumb-action-container">
                        <div class="item-wrapper-info">
                            <div class="item-total-video">
                                {$iTotalVideo} {if $iTotalVideo == 1} {_p('Video')} {else} {_p('Videos')} {/if}
                            </div>
                            <a href="#" rel="ultimatevideo_playlist_title"
                               class="ultimatevideo_more_info">{_p var='more_info'}</a>
                        </div>
                        <div class="item-wrapper-action">
                            <div class="item-icon-action">
                                <a id="ultimatevideo_repeat_button" data-status="repeat" href="javascript:void(0);"
                                   title="{_p('Repeat Playlist')}" onclick="ynultimatevideoSwitch(this);"
                                   class="ultimatevideo_status_button">
                                    <i class="ico ico-play-repeat-o"></i>
                                </a>
                                <a id="ultimatevideo_shuffle_button" data-status="shuffle" href="javascript:void(0);"
                                   title="{_p('Shuffle Playlist')}" onclick="ynultimatevideoSwitch(this);"
                                   class="ultimatevideo_status_button">
                                    <i class="ico ico-shuffle"></i>
                                </a>
                            </div>
                            <div class="item-auto-play">
                                <div class="checkbox p-checkbox-custom">
                                    <label>
                                        <input type="checkbox" data-status="auto_play" name="auto_play"
                                               id="ultimatevideo_continue_button" value="{_p('autoplay_next')}"
                                               onclick="ynultimatevideoSwitch(this);"
                                               class="ultimatevideo_status_button"/><i
                                                class="ico ico-square-o mr-1"></i> <span
                                                class="item-text">{_p('autoplay_next')}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{else}
{/if}
<script type="text/javascript" src="{$corePath}/assets/jscript/froogaloop2.min.js"></script>
<script src="https://api.dmcdn.net/all.js"></script>
<script type="text/javascript" src="{$corePath}/assets/jscript/videoPlayer.js"></script>
{literal}
<script type="text/javascript">
    $Behavior.onLoadSlideShowPlaylistDetail = function () {
        var playerCookie = getCookie('ultimatevideo_player_status');
        if (playerCookie) {
            var playerStatus = JSON.parse(playerCookie);
            $(".ultimatevideo_status_button").each(function () {
                if (playerStatus[$(this).data('status')]) {
                    $(this).addClass('active').prop('checked', true);
                }
            });
        }
        if (typeof window.MSLayerEffects_ynuv == 'undefined') {
            var script1 = document.createElement('script');
            script1.src = '{/literal}{$corePath}/assets/jscript/masterslider.min.js{literal}';
            document.getElementsByTagName("head")[0].appendChild(script1);

            var script2 = document.createElement('script');
            script2.src = '{/literal}{$corePath}/assets/jscript/mediaelementplayer/mediaelement-and-player.min.js{literal}';
            document.getElementsByTagName("head")[0].appendChild(script2);
        }
    };

    function ynultimatevideoSwitch(ele) {
        if ($(ele).hasClass('active')) {
            $(ele).removeClass('active');
        } else {
            $(ele).addClass('active');
        }
        var status = getPlayingStatus();
        setCookie('ultimatevideo_player_status', JSON.stringify(status));
    }

    function getPlayingStatus() {
        var status = {
            auto_play: 0,
            repeat: 0,
            shuffle: 0
        };
        $(".ultimatevideo_status_button").each(function () {
            status[$(this).data('status')] = $(this).hasClass('active') ? 1 : 0;
        });
        return status;
    }

    function generateRandom(current, min, max) {
        // playlist contain only 1 video
        if (max == 1) {
            return 0;
        }
        var gen = Math.floor(Math.random() * (max - min + 1)) + min;
        while (gen == current) {
            gen = Math.floor(Math.random() * (max - min + 1)) + min;
        }
        return gen;
    }

    $Behavior.jumpToVideo = function () {
        var iStartSlide = {/literal}{$iStartSlide}{literal};
        var iPlayId = {/literal}{$iPlayId}{literal};

        function playCurrent() {
            setTimeout(function () {
                ynultimatevideoPlay();
            }, 500);
        }

        if (iPlayId > 0) {
            setTimeout(function () {
                ulvideoplaylist_slider_clone_reinit.api.gotoSlide(iStartSlide);
            }, 4000);
        }
    }

</script>
{/literal}