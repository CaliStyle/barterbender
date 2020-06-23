<div class="ultimatevideo-app ultimatevideo-feed-playlist core-feed-item">
    <div class="item-outer">
        <a class="item-media" href="{permalink module='ultimatevideo.playlist' id=$aPitem.playlist_id title=$aPitem.title}">
            <span class="item-media-src" style="background-image: url(
            {if $aPitem.image_path}
                {img server_id=$aPitem.image_server_id path='core.url_pic' file=$aPitem.image_path suffix='_500' return_url=true}
            {else}
                {param var='core.path_actual'}PF.Site/Apps/YouNet_UltimateVideos/assets/image/noimg_playlist.jpg
            {/if}
                    )"></span>
            <div class="item-video-bg"><i class="ico ico-play-circle-o"></i></div>
            <div class="item-statistic-wrapper">
                {if $aPitem.total_view}
                    <div class="item-view">
                        {$aPitem.total_view|short_number} <span class="p-text-lowercase">{$aPitem.total_view|p_ultimatevideo_n:'view':'views'}</span>
                    </div>
                {/if}
            </div>
        </a>
        <div class="item-inner">
            <div class="item-title">
                <a class="core-feed-title line-2" href="{permalink module='ultimatevideo.playlist' id=$aPitem.playlist_id title=$aPitem.title}">{$aPitem.title|clean}</a>
            </div>
            {if $aPitem.sCategory}
            <div class="item-category">
                <div class="core-feed-minor">
                    <span class="category-title">{_p var='category'}:</span> {$aPitem.sCategory}
                </div>
            </div>
            {/if}
            {if $aPitem.total_video}
                <div class="item-playlist-listing">
                    {foreach from=$aPitem.video_list name=video item=aVideo}
                        {if $phpfox.iteration.video < 3}
                            <a href="{permalink module='ultimatevideo.playlist' id=$aPitem.playlist_id title=$aPitem.title}?play={$aVideo.video_id}" class="item-playlist">
                                <i class="ico ico-play"></i> {$aVideo.title|clean}
                            </a>
                        {/if}
                    {/foreach}
                </div>
                {if $aPitem.total_video > 1}
                    <div class="item-playall">
                        <a href="{$aFeed.feed_link}">{_p var='play_all_number_videos' number=$aPitem.total_video}</a>
                    </div>
                {/if}
            {/if}
        </div>
    </div>
</div>
