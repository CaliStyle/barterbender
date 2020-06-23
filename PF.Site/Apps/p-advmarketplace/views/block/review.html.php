<?php

?>
{literal}
<script type="text/javascript" language="javascript">
    $Behavior.advmarket_ratingJS = function(){
		$(".rwdelete").click(function(evt){
			evt.preventDefault();
			var review = $(this);

			$Core.jsConfirm({}, function() {
              $.ajaxCall("advancedmarketplace.deleteReview", "rid=" + review.attr("ref"));
            }, function() {});

			return false;
		});
	}
</script>
{/literal}
{if ($iCurrentUserId !== ((int)$aListing.user_id)) && $page == 0}
	{if !isset($aListing.isReviewed) || !$aListing.isReviewed }
	<div class="ync-review-header mb-2" id="yn_advmarketplace_rating">
	    <span class="ync-review-header__label text-gray">
            {if count($aRating)}
                {phrase var='advancedmarketplace.you_havent_reviewed_this_product_yet_do_it_now'}
            {else}
                {phrase var='advancedmarketplace.no_reviews_for_this_product_yet_be_the_first_to_review'}
            {/if}
        </span>
        <span class="yn_reviewrating">
		    <button class="button ssbt  btn-primary btn-sm" type="button">{phrase var='advancedmarketplace.write_your_review'}</button>
        </span>
    </div>
	{/if}
{/if}
{if count($aRating)}
	<ul class="ync-review-list mb--2">
		{foreach from=$aRating key=iKey item=aRate}
			<li class="ync-review-list__item" id="rw_ref_{$aRate.rate_id}">
				<div class="ync-review-list__item__inner">
					<div class="ync-review-list__info">
						<div class="ync-review-list__media">{img user=$aRate suffix='_50_square' max_width='50' max_height='50'}</div>
						<div class="ync-review-list__body">
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
	                        <time>{phrase var="advancedmarketplace.by"} {$aRate|user} {phrase var="advancedmarketplace.on"} {$aRate.timestamp|date:'advancedmarketplace.advancedmarketplace_view_time_stamp'}
	                        </time>
						</div>
					</div>
					{if $aRate.content}
						<div class="ync-review-list__content">{$aRate.content}</div>
					{/if}
					{if phpfox::getUserParam('advancedmarketplace.delete_other_reviews') || (phpfox::getUserParam('advancedmarketplace.can_delete_own_review') && $aRate.user_id == phpfox::getUserId())}
						<a href="javascript:void()" ref="{$aRate.rate_id}" class="rwdelete ync-review-list__close"><i class="ico ico-close"></i></a>
					{/if}
				</div>
			</li>
		{/foreach}
	</ul>
{/if}
<input type="hidden" id="xf_page" value="{$page}" />