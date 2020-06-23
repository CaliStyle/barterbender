{if isset($sError) && !empty($sError)}
    <div>{$sError}</div>
{else}
    <script type="text/javascript">
        oTranslations['view_more'] = "{_p var='view_more'}";
        oTranslations['like'] = "{_p var='like'}";
        oTranslations['liked'] = "{_p var='liked'}";
        oTranslations['rated_successfully'] = "{_p var='rated_successfully'}";
    </script>
    <div class="p-detail-container ultimatevideo_video_detail">
        <div class="p-detail-top-content">
            <div class="ultimatevideo-detail-embed-media">
                {$aItem.embed_code}
            </div>
        </div>
        <div class="p-detail-main-content">
            <h1 class="p-detail-header-page-title header-page-title item-title header-has-label-2">
                <a href="{permalink module='ultimatevideo' id=$aItem.video_id title=$aItem.title|clean}" class="ajax_link">{$aItem.title|clean}</a>
                <div class="p-type-id-icon">
                    {if !$aItem.is_approved}
                        <div class="sticky-label-icon sticky-pending-icon">
                            <span class="flag-style-arrow"></span>
                            <i class="ico ico-clock-o"></i>
                        </div>
                    {/if}
                    {if $aItem.is_sponsor}
                        <div class="sticky-label-icon sticky-sponsored-icon">
                            <span class="flag-style-arrow"></span>
                            <i class="ico ico-sponsor"></i>
                        </div>
                    {/if}
                    {if $aItem.is_featured}
                        <div class="sticky-label-icon sticky-featured-icon">
                            <span class="flag-style-arrow"></span>
                            <i class="ico ico-diamond"></i>
                        </div>
                    {/if}
                </div>
            </h1>
            <div class="p-detail-statistic-wrapper ">
                <div class="p-detail-statistic-list">
                    {if $aItem.total_view}
                        <span class="item-statistic"><span>{$aItem.total_view}</span>
                        <span class="p-text-lowercase">{if $aItem.total_view == 1}{_p('view')}{else}{_p('views')}{/if}</span></span>
                    {/if}
                    {if $aItem.total_like}
                        <span class="item-statistic"><span>{$aItem.total_like}</span>
                        <span class="p-text-lowercase">{if $aItem.total_like == 1}{_p('like')}{else}{_p('likes')}{/if}</span></span>
                    {/if}
                    {if $aItem.total_favorite}
                        <span class="item-statistic"><span>{$aItem.total_favorite}</span>
                        <span class="p-text-lowercase">{if $aItem.total_favorite == 1}{_p('favorite')}{else}{_p('favorites')}{/if}</span></span>
                    {/if}
                    {if $aItem.total_comment}
                        <span class="item-statistic"><span>{$aItem.total_comment}</span> <span
                                    class="p-text-lowercase">{if $aItem.total_comment == 1}{_p('comment')}{else}{_p('comments')}{/if}</span></span>
                    {/if}
                </div>

                <div class="p-detail-statistic-sub js-p-ultimatevideo-rating">
                    <span class="p-text-gray p-no-rate-text" {if $aItem.total_rating}style="display:none;"{/if}>{_p var='no_one_rates_this'}</span>
                    <div class="p-outer-rating p-rating-md p-outer-rating-row full" {if !$aItem.total_rating}style="display:none;"{/if}>
                        <div class="p-outer-rating-row">
                            <div class="p-rating-count-star">{$aItem.rating}</div>
                            <div class="p-rating-star one-star">
                                <i class="ico ico-star"></i>
                                <i class="ico ico-star"></i>
                                <i class="ico ico-star"></i>
                                <i class="ico ico-star"></i>
                                <i class="ico ico-star disable"></i>
                            </div>
                        </div>
                        <div class="p-rating-count-review-wrapper">
                            <a href="#"
                               onclick="return $Core.box('ultimatevideo.rate_list', 400, 'video_id={$aItem.video_id}')">
                        <span class="p-rating-count-review">
                            <span class="item-number">{$aItem.total_rating}</span>
                            <span class="item-text">{if $aItem.total_rating == 1}{_p var='rate'}{else}{_p var='rates'}{/if}</span>
                        </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-detail-action-wrapper">
                <div class="p-detail-action-list">
                    {if $aItem.is_approved}
                    <div class="item-action">
                        <a href="javascript:void(0);"
                           class="fw-bold btn btn-default btn-sm btn-icon"
                           id="ultiamtevideo_like_btn">
                            <i class="ico {if $aItem.is_liked}ico-thumbup{else}ico-thumbup-o{/if}"></i><span
                                    class="item-text">{if $aItem.is_liked}{_p var='liked'}{else}{_p var='like'}{/if}</span></a>
                    </div>
                    {/if}
                    {if $aItem.is_approved && $aItem.status}
                    {template file='ultimatevideo.block.link_video_viewer_detail'}
                    {/if}
                </div>
                {if Phpfox::isUser()}
                    <div class="p-detail-action-sub">
                        <span class="p-text-uppercase p-text-gray fw-bold">{_p var='your_rate'}</span>
                        <div class="p-outer-rating p-outer-rating-row mini p-rating-lg">
                            <div class="p-outer-rating-row">
                                <div class="p-rating-star p-can-rate dont-unbind-children" data-rating="{$aItem.viewer_rating}">
                                    {$aItem.viewer_rating|ultimatevideo_rating:$aItem.video_id}
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}
            </div>
            <div class="p-detail-author-wrapper">
                <div class="p-detail-author-image">{img user=$aItem suffix='_50_square'}</div>
                <div class="p-detail-author-info">
                    <span class="item-author"><span class="item-text-label">{_p var='by'}</span> {$aItem|user}</span>
                    <span class="item-time">{_p var='published_on'} {$aItem.time_stamp|date}</span>
                </div>
                <div class="p-detail-option-manage">
                    {if $bShowEditMenu}
                        {template file='ultimatevideo.block.link_video_edit'}
                    {/if}
                </div>
            </div>
            <div class="p-detail-content-wrapper">
                <div class="item_view_content p-collapse-content js_p_collapse_content">
                    <div>
                        {$aItem.description|parse}
                    </div>
                    <div class="ultimatevideo-custom-fields">
                        {module name='ultimatevideo.custom.view' video_id=$aItem.video_id}
                    </div>
                    <div class="p-detail-type-info">
                        {if !empty($sCategories)}
                        <div class="p-type-info-item">
                            <div class="p-category">
                                <span class="p-item-label">{_p var='categories'}:</span>
                                <div class="p-item-content">
                                    {($sCategories)}
                                </div>
                            </div>
                        </div>
                        {/if}
                        {if $aItem.sTags}
                        <div class="p-type-info-item">
                            <div class="p-category">
                                <span class="p-item-label">{_p var='tags'}:</span>
                                <div class="p-item-content">{$aItem.sTags}</div>
                            </div>
                        </div>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
        <div class="p-detail-bottom-content">
            <div class="p-detail-addthis-wrapper">
                <div class="p-detail-addthis">
                    {addthis url=$aItem.bookmark_url title=$aItem.title|parse|clean description=$sShareDescription}
                </div>
                <div class="p-detail-minor-action">
                    <a data-caption="{_p var='html_code'}" title="{_p var='html_code'}" class="p-btn-minor-action" data-toggle="ultimatevideo"
                       data-cmd="show_embed_code"><i class="ico ico-code"></i>{_p('embed_code')}</a>
                    {if Phpfox::isUser() && ($iProfilePageId == 0)}
                        <a title="{_p var='invite_friends'}" class="p-btn-minor-action popup"
                           href="{permalink module='ultimatevideo.invite' id=$aItem.video_id type=1}"><i
                                    class="ico ico-user3-two2"></i>{_p('invite_friends')}</a>
                    {/if}
                </div>
            </div>
        </div>
        <div class="ultimate_video_html_code_block hide">
            <textarea id="ultimatevideo_html_code_value" readony class="form-control disabled" rows="2"><iframe src="{$sUrl}" width="100%" height="100%" style="overflow:hidden;"></iframe></textarea>
            <div class="text-right" style="padding-top: 10px">
                <button type="button" class="btn btn-sm btn-default" data-toggle="ultimatevideo"
                        data-cmd="show_embed_code">
                    {_p('close')}
                </button>
                <button type="button" class="btn btn-sm btn-primary ultimatevideo_copy_btn" data-cmd="copy_embed_code"
                        data-clipboard-target="#ultimatevideo_html_code_value">
                    {_p('copy_code')}
                </button>
            </div>
        </div>
    </div>
    <!-- end -->

    {if Phpfox::getParam('feed.enable_check_in') && Phpfox::getParam('core.google_api_key') != '' && isset($aItem.location_name)}
        <div class="activity_feed_location">
            <span class="activity_feed_location_at">{_p('at')} </span>
            <span class="js_location_name_hover activity_feed_location_name"
                  {if isset($aItem.location_latlng)}onmouseover="$Core.Feed.showHoverMap('{$latitude}','{$longitude}', this);"{/if}>
    <span class="ico ico-checkin"></span>
    <a href="{if Phpfox::getParam('core.force_https_secure_pages')}https://{else}http://{/if}maps.google.com/maps?daddr={$latitude},{$longitude}"
       target="_blank">{$aItem.location_name}</a>
    </span>
        </div>
    {/if}

    {if $aItem.is_approved}
        <div class="ultimatevideo_video_detail-comment">
            {module name='feed.comment'}
        </div>
    {/if}
{literal}
    <script type="text/javascript">
        $Behavior.loadClipboardJs = function () {
            var eles = $('.ultimatevideo_copy_btn[data-cmd="copy_embed_code"]');
            if (eles.length) {
                if (typeof Clipboard == 'undefined') {
                    $Core.loadStaticFile('{/literal}{$corePath}{literal}/assets/jscript/clipboard.min.js');
                    window.setTimeout(function () {
                        new Clipboard('.ultimatevideo_copy_btn');
                    }, 2000);
                } else {
                    new Clipboard('.ultimatevideo_copy_btn');
                }
            }

        };
        $Behavior.checkDescription = function () {
            var desc_h = $('.ultimatevideo_video_detail-descriptions').height();

            if (desc_h > 54) {
                $('.ultimatevideo_show_less_more').show();
            }

            $('.ultimatevideo_show_less_more a').bind('click', function () {
                if ($(this).hasClass('ultimatevideo_link_more')) {
                    $(this).html({/literal}'{_p('view_less')}'{literal});
                    $(this).removeClass('ultimatevideo_link_more');
                    $(this).addClass('ultimatevideo_link_less');
                    $('#ultimatevideo_video_detail_moreless').removeClass('ultimatevideo_video_show_less');
                } else {
                    $(this).html('View more');
                    $(this).addClass('ultimatevideo_link_more');
                    $(this).removeClass('ultimatevideo_link_less');
                    $('#ultimatevideo_video_detail_moreless').addClass('ultimatevideo_video_show_less');
                }
            });
        }
    </script>
{/literal}
{/if}
{if $bLoadCheckin}
    <script type="text/javascript">
        var bCheckinInit = false;
        $Behavior.prepareInit = function ()
        {l}
        $Core.Feed.sIPInfoDbKey = '';
        $Core.Feed.sGoogleKey = '{param var="core.google_api_key"}';

        {if isset($aVisitorLocation)}
        $Core.Feed.setVisitorLocation({$aVisitorLocation.latitude}, {$aVisitorLocation.longitude} );
        {else}

        {/if}

        $Core.Feed.googleReady('{param var="core.google_api_key"}');
        {r}
    </script>
{/if}