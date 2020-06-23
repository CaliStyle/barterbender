<div class="ultimatevideo_playlist_detail_item ms-slide">
    <img src="{$corePath}/assets/jscript/masterslider/blank.gif"
         data-src="{if $aItem.image_path}{img server_id=$aItem.image_server_id path='core.url_pic' file=$aItem.image_path suffix='_500' return_url=true}{else}{$corePath}/assets/image/noimg_video.jpg{/if}"
         alt="lorem ipsum dolor sit"/>
    {$aItem.embed_code}
    <div class="ultimatevideo_playlist_detail_infomation ms-thumb"
         onclick="setTimeout(function(){l}ynultimatevideoPlay();{r},1000)">
        <div class="ultimatevideo_video_thumb">
            <span class="item-media-src"
                  style="background-image: url({if $aItem.image_path}{if $aItem.image_server_id == -1}{$aItem.image_path}{else}{img server_id=$aItem.image_server_id path='core.url_pic' file=$aItem.image_path suffix='_500' return_url=true}{/if}{else}{$corePath}/assets/image/noimg_video.jpg{/if});"></span>
            <span class="item-number">
                {$phpfox.iteration.video}
            </span>
            <div class="p-item-flag-wrapper">
                <!-- Featured -->
                <div class="sticky-label-icon sticky-featured-icon"
                     {if $aItem.is_featured == 0}style="display:none"{/if}>
                    <span class="flag-style-arrow"></span>
                    <i class="ico ico-diamond"></i>
                </div>
            </div>
        </div>

        <div class="ultimatevideo_playlist_detail_infomation_detail ultimatevideo-video-entry">
            <div class="ultimatevideo-video-title">
                {$aItem.title|clean}
            </div>

            <div class="ultimatevideo-video-owner-duration">
                <div class="ultimatevideo-duration">
                    {$aItem.duration|ultimatevideo_duration}
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" class="video_id" value="{$aItem.video_id}"/>
    <input type="hidden" class="title" value="{$aItem.title}"/>
    <input type="hidden" class="href" value="{permalink module='ultimatevideo' id=$aItem.video_id title=$aItem.title}"/>
    <div class="item-statistic" style="display: none;">
        {if $aItem.total_view}
            <span>
                {$aItem.total_view} <span
                        class="p-text-uppercase">{if $aItem.total_view == 1}{_p('view')}{else}{_p('views')}{/if}</span>
            </span>
        {/if}
        {if $aItem.total_like}
            <span>
                {$aItem.total_like} <span
                        class="p-text-uppercase">{if $aItem.total_like == 1}{_p('like')}{else}{_p('likes')}{/if}</span>
            </span>
        {/if}
        {if $aItem.total_rating}
            <span>
                <div class="p-outer-rating p-outer-rating-row mini p-rating-sm">
                    <div class="p-outer-rating-row">
                        <div class="p-rating-count-star">{$aItem.total_rating}</div>
                         <div class="p-rating-star">
                             {$aItem.rating|ultimatevideo_rating}
                        </div>
                    </div>
                    <div class="p-rating-count-review-wrapper">
                        <span class="p-rating-count-review">
                            <span class="item-number">{$aItem.total_rating}</span>
                            <span class="item-text">{$aItem.total_rating|p_ultimatevideo_n:'review':'reviews'}</span>
                        </span>
                    </div>
                </div>
            </span>
        {/if}
    </div>
    <div class="item-addto" style="display: none;">
        {if Phpfox::getUserId() && isset($bIsPagesView) && !$bIsPagesView && $aItem.is_approved && $aItem.status}
            {template file='ultimatevideo.block.link_video_viewer'}
        {/if}
    </div>
</div>