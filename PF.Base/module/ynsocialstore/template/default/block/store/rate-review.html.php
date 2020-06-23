<?php
?>
<div id="ynstore-rating-holder">
	{if count($aReview)}
		<div id="ynstore-rating-result">
			<div class="ynstore-title">{_p var='ynsocialstore.thank_you_for_your_review'}</div>
			<div class="ynstore-rating yn-rating yn-rating-large">
				{$sResult}
				<span>{$aReview.rating|number_format:1}</span>
			</div>
		</div>
	{/if}

	<div class="{if count($aReview)}hide{/if}" id="ynstore-review-store-form">
		{if count($aReview)}
			<div class="ynstore-title">{_p var='ynsocialstore.edit_your_review'}</div>
		{else}
			<div class="ynstore-title">{_p var='ynsocialstore.post_a_review'}</div>
		{/if}

		<div id="ynstore-rating-section" class="ynstore-rating yn-rating yn-rating-large dont-unbind-children">
			{$sResult}
			<br/><span class="text-danger"></span>
		</div>

		<form id="form-rating" method="post" action="{permalink module='ynsocialstore.store' id=$item_id title=$aStore.name}reviews" onsubmit="return ynsocialstore.validReviewForm();">
			<input type="hidden" name="rating[store_id]" value="{$item_id}" />
			<input type="hidden" name="rating[rating]" value="{if isset($aReview.rating)}{$aReview.rating}{else}0{/if}" id="ynstore-current-rating">
			<span class="ynstore-tips">{_p var='ynsocialstore.click_on_stars_for_rating'}</span>
			<div class="ynstore-box-addtxt">
				<textarea class="form-control" cols="20" rows="5" placeholder="{_p var='ynsocialstore.write_your_message'}"  name="rating[content]" id="review-content" value="">{if isset($aReview.content)}{$aReview.content}{/if}</textarea>
			</div>

			<span class="ynstore-remaining-block">
				{_p var='ynsocialstore.remaining_characters'}&nbsp;<span id="ynstore-review-countdown"></span>
			</span>

			<div class="ynstore-button-reviews">
				<button type="submit" id="rating" value="review" name="rating[review]" onclick="" class="btn btn-primary">{_p var='ynsocialstore.submit_review'}</button>
			</div>
		</form>
	</div>
</div>

 
 