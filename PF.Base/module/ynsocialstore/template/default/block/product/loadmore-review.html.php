<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 11/1/16
 * Time: 17:41
 */
?>
{if count($aReviews)}
    <input type="hidden" id="ynstore_current_page" value="{$iPage}">
    {foreach from=$aReviews name=iReview item=aReview}
        {template file='ynsocialstore.block.product.review-item'}
    {/foreach}
    {if count($aReviews) >= $iSize}
        <a role="button" class="ynstore-loadmore" id="js_load_more_review" onclick="onLoadMoreReview()">
            {_p var='ynsocialstore.load_more'}
        </a>
    {/if}
{/if}
