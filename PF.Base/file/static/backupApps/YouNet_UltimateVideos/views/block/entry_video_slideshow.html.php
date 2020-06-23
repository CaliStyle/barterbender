<article class="p-item ultimatevideo-item">
    <div class="item-outer">
        <!-- image -->
        <div class="p-item-media-wrapper ultimatevideo-item-media-wrapper ratio-wide">
            <a class="item-media-link"
               href="{if !empty($aItem.item_url)}{$aItem.item_url}{else}{permalink module='ultimatevideo' id=$aItem.video_id title=$aItem.title|clean}{/if}">
                <span class="item-media-src"
                      {if $aItem.image_path}style="background-image: url({if $aItem.image_server_id == -1}{$aItem.image_path}{else}{img server_id=$aItem.image_server_id path='core.url_pic' file=$aItem.image_path suffix='_500' return_url=true}{/if})"
                      {else}style="background-image: url({param var='core.path_actual'}PF.Site/Apps/YouNet_UltimateVideos/assets/image/noimg_video.jpg)"{/if}></span>
                <div class="p-item-flag-wrapper">
                    <!-- Sponsor -->
                    {if $aItem.is_sponsor}
                        <div class="sticky-label-icon sticky-sponsored-icon">
                            <span class="flag-style-arrow"></span>
                            <i class="ico ico-sponsor"></i>
                        </div>
                    {/if}
                    <!-- Featured -->
                    {if $aInfo.featured && $aItem.is_featured}
                        <div class="sticky-label-icon sticky-featured-icon">
                            <span class="flag-style-arrow"></span>
                            <i class="ico ico-diamond"></i>
                        </div>
                    {/if}
                </div>
                {if $aItem.duration}
                <div class="ultimatevideo-item-duration">
                    {$aItem.duration|ultimatevideo_duration}
                </div>
                {/if}
                {if $aInfo.display_ranking}
                    <div class="ultimatevideo-item-ranking">
                        <div class="ultimatevideo-item-ranking-flag ranking-{$aItem.ranking}">
                            {$aItem.ranking}
                        </div>
                    </div>
                {/if}
            </a>
        </div>
        <div class="item-inner">
            <div class="ultimatevideo-item-wrapper-left">
                <!-- title -->
                <div class="p-mb-line ultimatevideo-item-title-wrapper p-flex-wrapper">
                    <h2 class="p-item-title">
                        <a href="{if !empty($aItem.item_url)}{$aItem.item_url}{else}{permalink module='ultimatevideo' id=$aItem.video_id title=$aItem.title|clean}{/if}">{$aItem.title|clean}</a>
                    </h2>
                </div>

                <div class="p-item-statistic ultimatevideo-item-statistic">
                    {template file='ultimatevideo.block.entry_statistics'}
                    {if !empty($aInfo.rating) && $aItem.total_rating}
                        <span class="ultimatevideo-item-star">
                        <div class="p-outer-rating p-outer-rating-row mini p-rating-sm">
                            <div class="p-outer-rating-row">
                                <div class="p-rating-count-star">{$aItem.rating}</div>
                                 <div class="p-rating-star">
                                    {$aItem.rating|ultimatevideo_rating}
                                </div>
                            </div>
                            <div class="p-rating-count-review-wrapper">
                                <span class="p-rating-count-review">
                                    <span class="item-number">{$aItem.total_rating}</span>
                                    {if $aItem.total_rating > 1}
                                        <span class="item-text">{_p var='reviews'}</span>
                                    {else}
                                        <span class="item-text">{_p var='review'}</span>
                                    {/if}
                                </span>
                            </div>
                        </div>
                    </span>
                    {/if}
                </div>
                <div class="ultimatevideo-item-minor-info-container">
                    <!-- avatar -->
                    <div class="ultimatevideo-item-avatar">
                        {img user=$aItem suffix='_50_square' max_width=50 max_height=50}
                    </div>
                    <div class="ultimatevideo-item-minor-info-inner">
                        <!-- minor info -->
                        <div class="p-item-minor-info p-seperate-dot-wrapper ultimatevideo-item-minor-info p-seperate-dot-item">
                            <span class="p-seperate-dot-item item-author"><span
                                        class="p-text-capitalize">{_p var='by'}</span> <span class="user_profile_link_span"
                                                                                  id="js_user_name_link_admin">{$aItem|user:'':'':50:'':'author'}</span></span>
                            <span class="p-seperate-dot-item p-hidden-side-block">{$aItem.time_stamp|convert_time}</span>

                        </div>
                        <!-- category -->
                        {if !empty($aItem.sCategory)}
                            <div class="p-item-category">
                                <span>{_p var='category'}:</span>{$aItem.sCategory}
                            </div>
                        {/if}
                    </div>
                </div>
                <div class="ultimatevideo-item-description p-item-description item_content">
                    {$aItem.description|striptag|stripbb|clean|highlight:'search'|split:500}
                </div>
                <div class="ultimatevideo-item-slider-action">
                    <a href="{if !empty($aItem.item_url)}{$aItem.item_url}{else}{permalink module='ultimatevideo' id=$aItem.video_id title=$aItem.title|clean}{/if}"
                       class="btn btn-primary">{_p var='watch_now'}<i class="ico ico-play"></i></a>
                </div>
            </div>
            <div class="ultimatevideo-item-wrapper-right">
                <div class="p-item-statistic ultimatevideo-item-statistic">
                    {template file='ultimatevideo.block.entry_statistics'}
                    {if (empty($aInfo.view) || !$aItem.total_view) && (empty($aInfo.like) || !$aItem.total_like) && (empty($aInfo.comment) || !$aItem.total_comment) && (empty($aInfo.rating) || !$aItem.total_rating)}
                        <div class="ultimatevideo-item-slider-action">
                            <a href="{if !empty($aItem.item_url)}{$aItem.item_url}{else}{permalink module='ultimatevideo' id=$aItem.video_id title=$aItem.title|clean}{/if}"
                               class="btn btn-primary">{_p var='watch_now'}<i class="ico ico-play ml-1"></i></a>
                        </div>
                    {/if}
                </div>
                {if !empty($aInfo.rating) && $aItem.total_rating}
                    <span class="ultimatevideo-item-star">
                    <div class="p-outer-rating p-outer-rating-row mini p-rating-sm">
                        <div class="p-outer-rating-row">
                            <div class="p-rating-count-star">5</div>
                             <div class="p-rating-star">
                                {$aItem.rating|ultimatevideo_rating}
                            </div>
                        </div>
                        <div class="p-rating-count-review-wrapper">
                            <span class="p-rating-count-review">
                                <span class="item-number">{$aItem.total_rating}</span>
                                {if $aItem.total_rating > 1}
                                    <span class="item-text">{_p var='reviews'}</span>
                                {else}
                                    <span class="item-text">{_p var='review'}</span>
                                {/if}
                            </span>
                        </div>
                    </div>
                </span>
                {/if}
            </div>
        </div>
    </div>
</article>