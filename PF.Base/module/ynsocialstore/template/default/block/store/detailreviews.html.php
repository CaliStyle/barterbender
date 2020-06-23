<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/18/16
 * Time: 11:07
 */
?>
{if $iPage <= 1}
<div class="ynstore-store-review-block">
{/if}
    {if Phpfox::isUser() && (Phpfox::getUserId() != $aStore.user_id) && $iPage <= 1}
    <div id="ynstore-store-add-review-{$aStore.store_id}" class="ynstore-store-add-review">
        {module name="ynsocialstore.store.rate-review"}
    </div>
    {/if}

    <div id="ynstore_detail_reviews" class="ynstore-store-review-list">
    {if count($aReviews)}
            <div class="ynstore-title">
                {_p var='ynsocialstore_customer_reviews'}
            </div>

            {foreach from=$aReviews name=iReview item=aReview}

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
                        {if ($canDeleteOwnReview && ($aReview.user_id == Phpfox::getUserId())) || ($canDeleteOtherReview && ($aStore.user_id == Phpfox::getUserId())) }
                        <a href="" class="btn-danger" onclick="ynsocialstore.confirmDeleteReview({$aReview.review_id},{$aStore.store_id});return false;">
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
            {/foreach}
        {pager}

        {elseif $iPage <= 1}
            {_p('There are no reviews yet')}
    {/if}
    </div>
{if $iPage <= 1}
</div>
{/if}

{if $iPage <= 1}
    {literal}
    <script textype="text/javascript">
        $Behavior.loadReviewDetailStore = function(){
            $('.ynstore-review-showmore').click(function(){
                iReviewId = $(this).attr('reviewid');
                $('#ynstore_full_'+iReviewId).show();
                $('#ynstore_less_'+iReviewId).hide();
                $('#ynstore_showmore_'+iReviewId).hide();
                $('#ynstore_showless_'+iReviewId).show();
            });

            $('.ynstore-review-showless').click(function(){
                iReviewId = $(this).attr('reviewid');
                $('#ynstore_full_'+iReviewId).hide();
                $('#ynstore_less_'+iReviewId).show();
                $('#ynstore_showless_'+iReviewId).hide();
                $('#ynstore_showmore_'+iReviewId).show();
            });
        }
    </script>
    {/literal}
{/if}