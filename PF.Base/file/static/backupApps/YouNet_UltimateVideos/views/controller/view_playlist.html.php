{if isset($sError) && !empty($sError)}
    <div>{$sError}</div>
{else}
    <script type="text/javascript">
        oTranslations['view_more'] = "{_p var='view_more'}";
        oTranslations['like'] = "{_p var='like'}";
        oTranslations['liked'] = "{_p var='liked'}";
    </script>
    <div class="ultimatevideo_video_detail ultimatevideo_playlist_detail">
        <div class="p-detail-container">
            <div class="p-detail-main-content">
                <h1 class="p-detail-header-page-title header-page-title item-title header-has-label-2"
                    id="ultimatevideo_playlist_title">
                    <a href="{permalink module='ultimatevideo.playlist' id=$aPitem.playlist_id}"
                       class="ajax_link">{$aPitem.title|clean}</a>
                    <div class="p-type-id-icon">
                        {if !$aPitem.is_approved}
                            <div class="sticky-label-icon sticky-pending-icon">
                                <span class="flag-style-arrow"></span>
                                <i class="ico ico-clock-o"></i>
                            </div>
                        {/if}
                        {if $aPitem.is_sponsored}
                            <div class="sticky-label-icon sticky-sponsored-icon">
                                <span class="flag-style-arrow"></span>
                                <i class="ico ico-sponsor"></i>
                            </div>
                        {/if}
                        {if $aPitem.is_featured}
                            <div class="sticky-label-icon sticky-featured-icon">
                                <span class="flag-style-arrow"></span>
                                <i class="ico ico-diamond"></i>
                            </div>
                        {/if}
                    </div>
                </h1>
                <div class="p-detail-statistic-wrapper mb-1">
                    <div class="p-detail-statistic-list">
                        {if $aPitem.total_view}
                            <span class="item-statistic">
                                {$aPitem.total_view} <span class="p-text-lowercase">{if $aPitem.total_view == 1}{_p('view')}{else}{_p('views')}{/if}</span>
                        </span>
                        {/if}
                        {if $aPitem.total_like}
                            <span class="item-statistic">
                                {$aPitem.total_like} <span class="p-text-lowercase">{if $aPitem.total_like == 1}{_p('like')}{else}{_p('likes')}{/if}</span>
                        </span>
                        {/if}
                        {if $aPitem.total_comment}
                            <span class="item-statistic">
                                {$aPitem.total_comment} <span class="p-text-lowercase">{if $aPitem.total_comment == 1}{_p('comment')}{else}{_p('comments')}{/if}</span>
                        </span>
                        {/if}
                    </div>
                </div>
                <div class="p-detail-author-wrapper">
                    <div class="p-detail-author-image">
                        {img user=$aPitem suffix='_50_square' max_width=50 max_height=50}
                    </div>
                    <div class="p-detail-author-info">
                        <span class="item-author"><span class="item-text-label">{_p var='by'}</span>
                            <span class="user_profile_link_span" id="js_user_name_link_admin" itemprop="author">
                                {$aPitem|user}
                            </span>
                        </span>
                        <span class="item-time">
                            {_p var='published'}
                            <span class="p-text-lowercase">{_p var='on'}</span> {$aPitem.time_stamp|convert_time}
                        </span>
                    </div>
                    <div class="p-detail-option-manage">
                        {if $bShowEditMenu}
                            {template file='ultimatevideo.block.link_playlist'}
                        {/if}
                    </div>
                </div>
                <div class="p-detail-content-wrapper">
                    <div class="item_view_content p-collapse-content js_p_collapse_content">
                        <div>
                            {$aPitem.description|parse}
                        </div>
                        {if $aCategory}
                            <div class="p-detail-type-info">
                                <div class="p-type-info-item">
                                    <div class="p-category">
                                        <span class="p-item-label">{_p var='categories'}:</span>
                                        <div class="p-item-content">
                                            <a href="{permalink module='ultimatevideo.playlist.category' id=$aCategory.category_id title=$aCategory.title}">
                                                {_p var=$aCategory.title}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {/if}
                    </div>
                </div>
            </div>
            <div class="p-detail-bottom-content">
                <div class="p-detail-addthis-wrapper">
                    <div class="p-detail-addthis">
                        {addthis url=$aPitem.bookmark_url title=$aPitem.title description=$sShareDescription}
                    </div>
                    <div class="p-detail-minor-action">
                        <a title="{_p('invite_friends')}" class="p-btn-minor-action popup"
                           href="{permalink module='ultimatevideo.invite' id=$aPitem.playlist_id type=2}"><i
                                    class="ico ico-user3-two2"></i>{_p('invite_friends')}</a>
                    </div>
                </div>
                {if $aPitem.is_approved == 1}
                    <div class="item-detail-feedcomment p-detail-feedcomment">
                        {module name='feed.comment'}
                    </div>
                {/if}
            </div>
        </div>
    </div>
{/if}