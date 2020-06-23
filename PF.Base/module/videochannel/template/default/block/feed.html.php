<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if isset($aVideos.is_one_video)}
    <div class="js_outer_video_div js_mp_fix_holder image_hover_holder">
        <iframe width="100" class="videochannel_youtube_iframe_big" title="{$aVideos.title}" src="//www.youtube.com/embed/{$aVideos.video_code}" autoplay="0" frameborder="0" allowfullscreen="" scrolling="no"></iframe>
        <a href="{$aVideos.link}" class="activity_feed_content_link_title">{$aVideos.title}</a>
        <div class="item_view_content">
            {if strpos($aVideos.text_parsed, '<br />') >= 200}
                {$aVideos.text_parsed|feed_strip|shorten:200:'feed.view_more':true|split:55|max_line}
            {else}
                {$aVideos.text_parsed|feed_strip|split:55|max_line|shorten:200:'feed.view_more':true}
            {/if}
        </div>
    </div>
{else}
<div id="js_video_outer_body" class="activity_feed_multiple_image">
    {foreach from=$aVideos key=iId name=aVideo item=aVideo}
    {if $iId == 0}
        <iframe class="videochannel_youtube_iframe_big" title="{$aVideo.title}" src="//www.youtube.com/embed/{$aVideo.video_code}" autoplay="0" frameborder="0" allowfullscreen="" scrolling="no"></iframe>
    {elseif $iId <= 4}
        <div class="js_video_parent main_videochannel_div_container">
                <div class="js_outer_video_div js_mp_fix_holder image_hover_holder">
                    <div class="video_duration">
                        {$aVideo.duration}
                    </div>
                    <a href="{$aVideo.link}" class="ynvideochanel_avatar js_video_title_{$aVideo.video_id}">
                        <i class="fa fa-play"></i>
                        {if !empty($aVideo.image_path)}
                            <span style="background-image: url({img title=$aVideo.title path='core.url_pic' server_id=$aVideo.image_server_id file=$aVideo.image_path suffix='_120' return_url=true});"></span>
                        {else}
                            <span style="background-image: url('{param var='core.path_file'}module/videochannel/static/image/noimg_video.jpg');"></span>
                        {/if}
                    </a>
                </div>
        </div>
    {/if}
    {/foreach}
</div>
{/if}
{unset var=$aVideos}
{literal}
<script type="text/javascript">
    $Ready(function(){
        var videoAspec =  16/9;

        (function(eles){
            eles.each(function(index, ele){
                var $ele =  $(ele),
                    parent = $ele.parent();

                $ele.data('built', true);
                $ele.css("width", parent.width());
                $ele.css("height", parent.width()/videoAspec);
            });
        })($('.videochannel_youtube_iframe_big').not('.built'));
    });
</script>
{/literal}