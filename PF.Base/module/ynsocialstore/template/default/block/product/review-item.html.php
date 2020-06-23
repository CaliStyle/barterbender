<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 11/1/16
 * Time: 17:28
 */
?>
<div class="ynstore-recent-reviews-item">
    <div class="ynstore-avatar">
        {img user=$aReview suffix='_50_square'}
    </div>

    <div class="ynstore-info">

        <div class="ynstore-action">
            {if $canEditReview && ($aReview.user_id == Phpfox::getUserId())}
            <a href="" onclick="ynsocialstore.showReviewForm();return false;">
                <i class="ico ico-pencil"></i>
            </a>
            {/if}
            {if ($canDeleteOwnReview && ($aReview.user_id == Phpfox::getUserId())) || ($canDeleteOtherReview && ($aProduct.user_id == Phpfox::getUserId())) }
            <a href="" class="btn-danger" onclick="ynsocialstore.confirmDeleteReviewProduct({$aReview.review_id},{$aProduct.product_id});return false;">
                <i class="ico ico-trash"></i>
            </a>
            {/if}
        </div>


        <div class="ynstore-username">
            {$aReview|user}
        </div>

        <div class=ynstore-rating-time>
            <span class="ynstore-rating yn-rating yn-rating-small">
                <span>{$aReview.rating}/5</span>
                {$aReview.total_score_text}
            </span>

            <span class="ynstore-date-time">
                <span>{$aReview.time}</span>&nbsp;&nbsp;.&nbsp;&nbsp;<span>{$aReview.date}</span>
            </span>
        </div>

        {if $aReview.showmore}
            <span id="ynstore_less_{$aReview.review_id}">
                <span class="ynstore-recent-reviews-item-content">{$aReview.less|parse}</span>
            </span>
            <span id="ynstore_showmore_{$aReview.review_id}" class="ynstore-review-showmore" reviewid="{$aReview.review_id}" >+ {_p('Show more')}</span>
            <span id="ynstore_full_{$aReview.review_id}" style="display:none;">
                <span class="ynstore-recent-reviews-item-content">{$aReview.content|parse}</span>
            </span>
            <span style="display:none;" id="ynstore_showless_{$aReview.review_id}" class="ynstore-review-showless" reviewid="{$aReview.review_id}" >- {_p('View less')}</span>
        {else}
            <div class="">{$aReview.content}</div>
        {/if}
    </div>

</div>
