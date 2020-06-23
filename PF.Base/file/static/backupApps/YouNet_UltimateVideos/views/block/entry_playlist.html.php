<article class="p-item ultimatevideo-item ultimatevideo-playlist-item {if !$aPitem.total_video}empty-playlist{/if}">
    <div class="item-outer">
        <!-- image -->
        <div class="p-item-media-wrapper ultimatevideo-item-media-wrapper ratio-wide p-margin-default">
            <a class="item-media-link"
               href="{permalink module='ultimatevideo.playlist' id=$aPitem.playlist_id title=$aPitem.title}">
                <span class="item-media-src" style="background-image: url(
                {if $aPitem.image_path}
                    {img server_id=$aPitem.image_server_id path='core.url_pic' file=$aPitem.image_path suffix='_500' return_url=true}
                {else}
                    {param var='core.path_actual'}PF.Site/Apps/YouNet_UltimateVideos/assets/image/noimg_playlist.jpg
                {/if}
                        )"></span>
                <div class="p-item-flag-wrapper">
                    <!-- Sponsor -->
                    {if $aPitem.is_sponsored}
                        <div class="sticky-label-icon sticky-sponsored-icon">
                            <span class="flag-style-arrow"></span>
                            <i class="ico ico-sponsor"></i>
                        </div>
                    {/if}
                    {if !$aPitem.is_approved}
                        <div class="sticky-label-icon sticky-pending-icon">
                            <span class="flag-style-arrow"></span>
                            <i class="ico ico-clock-o"></i>
                        </div>
                    {/if}
                    <!-- Featured -->
                    {if $aInfo.featured && $aPitem.is_featured}
                        <div class="sticky-label-icon sticky-featured-icon">
                            <span class="flag-style-arrow"></span>
                            <i class="ico ico-diamond"></i>
                        </div>
                    {/if}
                </div>
                <div class="ultimatevideo-item-total-video">
                    {$aPitem.total_video|short_number} <span
                            class="item-text-title">{if $aPitem.total_video == 1}{_p var='video'}{else}{_p var='videos'}{/if}</span>
                </div>
            </a>
        </div>
        <div class="item-inner">
            <div class="ultimatevideo-item-group-info-general">
                <!-- title -->
                <div class="p-mb-line ultimatevideo-item-title-wrapper p-flex-wrapper">
                    <h4 class="p-item-title">
                        <a href="{permalink module='ultimatevideo.playlist' id=$aPitem.playlist_id title=$aPitem.title}"
                           title="{$aPitem.title|clean}">{$aPitem.title|clean}</a>
                    </h4>
                    {if $bShowCommand}
                        <div class="p-ml-auto ultimatevideo-item-option-container">
                            <div class="ml-1 p-mr--1 item-option-list">
                                <div class="dropdown clearfix ">
                                    {template file='ultimatevideo.block.link_playlist'}
                                </div>
                            </div>
                        </div>
                    {/if}
                </div>
                <!-- minor info -->
                <div class="p-item-minor-info p-seperate-dot-wrapper ultimatevideo-item-minor-info p-seperate-dot-item">
                    <span class="p-seperate-dot-item item-author"><span
                                class="p-text-capitalize">{_p('by')}</span> {$aPitem|user}</span>
                    {if !$bIsSideLocation}
                        <span class="p-seperate-dot-item p-hidden-side-block item-time">{$aPitem.time_stamp|convert_time}</span>
                    {/if}
                    {template file='ultimatevideo.block.entry_playlist_statistics'}
                </div>

                <div class="p-item-statistic ultimatevideo-item-statistic show-on-featured">
                    {template file='ultimatevideo.block.entry_playlist_statistics'}
                </div>
                <div class="ultimatevideo-item-total-video-inline hidden">
                    {$aPitem.total_video|short_number} <span
                            class="item-text-title">{if $aPitem.total_video == 1}{_p var='video'}{else}{_p var='videos'}{/if}</span>
                </div>
            </div>
            <div class="ultimatevideo-item-group-info-playlist">
                {if $aPitem.description}
                    <div class="ultimatevideo-item-description p-item-description item_content truncate-1 mb-1">
                        {$aPitem.description|striptag|stripbb|clean|highlight:'search'|split:500}
                    </div>
                {/if}
                {if $aPitem.total_video}
                    <div class="ultimatevideo-item-video-list">
                        {foreach from=$aPitem.video_list name=video item=aVideo}
                            {if $phpfox.iteration.video < 3}
                                <div class="video-list-item">
                                    <a href="{permalink module='ultimatevideo.playlist' id=$aPitem.playlist_id title=$aPitem.title}?play={$aVideo.video_id}"
                                       class="video-list-item-link">
                                        <div class="video-list-item-title">
                                            <i class="ico ico-play"></i>{$aVideo.title|clean}</div>
                                        {if $aVideo.duration|ultimatevideo_duration}
                                            <span class="video-list-item-length">({$aVideo.duration|ultimatevideo_duration})</span>
                                        {/if}
                                    </a>
                                </div>
                            {/if}
                        {/foreach}
                    </div>
                    {if $aPitem.total_video > 1}
                        <div class="show-on-featured ultimatevideo-item-playall">
                            <a href="{permalink module='ultimatevideo.playlist' id=$aPitem.playlist_id title=$aPitem.title}">
                                {_p var='play_all_number_videos' number=$aPitem.total_video}
                            </a>
                        </div>
                    {/if}
                {/if}
            </div>
        </div>
        {if $bShowModeration}
            <div class="moderation_row" style="position: absolute;top: 0;opacity:0.8">
                <label class="item-checkbox">
                    <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aPitem.playlist_id}" id="check{$aPitem.playlist_id}" />
                    <i class="ico ico-square-o"></i>
                </label>
            </div>
        {/if}
    </div>
</article>
