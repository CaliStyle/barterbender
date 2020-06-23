{if (Phpfox::getUserId() != $aBusiness.user_id) && $iPage <= 1}
    {if !count($aReviewedByUser)}
    <div class="yndirectory-review-header mb-2" id="yndirectory_add_review">
            <span class="yndirectory-review-header__label text-gray">
                {if count($aBusiness.total_review)}
                    {phrase var='directory.you_havent_reviewed_this_product_yet_do_it_now'}

                {else}
                    {phrase var='directory.no_reviews_for_this_product_yet_be_the_first_to_review'}
                {/if}
            </span>
        <button class="button ssbt btn-primary btn-sm" type="button" onclick="tb_show(oTranslations['directory.rate_this_business'], $.ajaxBox('directory.ratePopup', 'height=300&width=550&business_id={$aBusiness.business_id}')); return false;">{phrase var='directory.write_your_review'}</button>
    </div>
    {/if}
{/if}

{if count($aBusiness.total_review)}
<ul class="yndirectory-review-list ">

    {foreach from=$aReviews key=iKey item=aRate}
    <li class="yndirectory-review-list__item" id="rw_ref_{$aRate.rate_id}">
        <div class="yndirectory-review-list__item__inner">
            <div class="yndirectory-review-list__info">
                <div class="yndirectory-review-list__media">{img user=$aRate suffix='_50_square' max_width='50' max_height='50'}</div>
                <div class="yndirectory-review-list__body">
                    <div class="ync-outer-rating ync-outer-rating-row mini ync-rating-sm">
                        <div class="ync-outer-rating-row">
                            <div class="ync-rating-star">
                                {for $i = 0; $i < 10; $i+=2}
                                {if $i < (int)$aRate.rating}
                                <i class="ico ico-star" aria-hidden="true"></i>
                                {elseif ((round($aRate.rating) - $aRate.rating) > 0) && ($aRate.rating - $i) > 0}
                                <i class="ico ico-star half-star" aria-hidden="true"></i>
                                {else}
                                <i class="ico ico-star disable" aria-hidden="true"></i>
                                {/if}
                                {/for}
                            </div>
                        </div>
                    </div>
                    <time>{phrase var="directory.by"} {$aRate|user} {phrase var="directory.on"} {$aRate.timestamp'}
                    </time>
                </div>
            </div>
            {if $aRate.title}
            <div class="yndirectory-review-list__content"><strong>{$aRate.title}</strong></div>
            {/if}
            {if $aRate.content}
            <div class="yndirectory-review-list__content">{$aRate.content}</div>
            {/if}
            {if Phpfox::getUserParam('directory.can_edit_own_review') && (Phpfox::getUserId() == $aRate.user_id)}
                <a  onclick="tb_show(oTranslations['directory.rate_this_business'], $.ajaxBox('directory.editRatePopup', 'height=300&width=550&business_id={$aBusiness.business_id}&review_id={$aReviewedByUser.review_id}')); return false;" href="javascript:void(0)" class="yndirectory-review-list__close"><i class="ico ico-pencil"></i></a>
            {/if}
        </div>
    </li>
    {/foreach}
</ul>
{/if}
<input type="hidden" id="xf_page" value="{$page}" />