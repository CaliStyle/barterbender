{if count($aRecentReviews)}
    {foreach from=$aRecentReviews item=aReview name=review}
    	<div class="yndirectory-recent-reviews-item">
    		<div class="yndirectory-recent-reviews-item-review-star">{$aReview.total_score_text}</div>
    		<div class="yndirectory-recent-reviews-item-author">{phrase var='for'} <a href="{$aReview.business_link}">{$aReview.name}</a></div>
    		<div class="yndirectory-recent-reviews-item-content">{$aReview.content|clean|shorten:100:'...'|split:10}</div>
    		<div class="yndirectory-recent-reviews-item-review">{phrase var='by'} {$aReview|user}</div>
		</div>
    {/foreach}
{/if}