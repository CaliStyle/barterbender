<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 11/1/16
 * Time: 09:21
 */
?>
{if $iPage <= 1}
<div class="ynstore-product-review-block">
    {/if}
    {if Phpfox::isUser() && (Phpfox::getUserId() != $aProduct.user_id) && $iPage <= 1}
    <div id="ynstore-product-add-review-{$aProduct.product_id}" class="ynstore-product-add-review">
        {module name="ynsocialstore.product.rate-review"}
    </div>
    {/if}

    <div id="ynstore_detail_reviews" class="ynstore-product-review-list">
        {if count($aReviews)}
            <input type="hidden" id="ynstore_current_page" value="{$iPage}">
            <input type="hidden" id="product_id" value="{$aProduct.product_id}">
            {foreach from=$aReviews name=iReview item=aReview}
                {template file='ynsocialstore.block.product.review-item'}
            {/foreach}
        {if count($aReviews) >= $iSize}
            <a role="button" class="ynstore-loadmore" id="js_load_more_review" onclick="onLoadMoreReview()">
                {_p var='ynsocialstore.load_more'}
            </a>
        {/if}
        {elseif $iPage <= 1}
            <p class="ynstore-tips bg-info">
                {_p('There are no reviews yet')}
            </p>
        {/if}
    </div>
    {if $iPage <= 1}
</div>
{/if}

{if $iPage <= 1}
{literal}
<script textype="text/javascript">
    $Behavior.loadReviewDetailProductReview = function(){
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
    function onLoadMoreReview()
    {
        if($('#ynstore_current_page').length != 0)
        {
            $('#js_load_more_review').remove();
            var iPage = $('#ynstore_current_page').val(),
                iProductId = $('#product_id').val();
            $('#ynstore_current_page').remove();
            $.ajaxCall('ynsocialstore.loadmoreProductReview', $.param({iProductId: iProductId,iPage:iPage}));
        }
    }
</script>
{/literal}
{/if}
