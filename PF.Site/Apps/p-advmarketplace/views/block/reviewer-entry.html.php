<div class="item-review-listing-item p-item js_reviewer_item" id="js_reviewer_{$reviewer.rate_id}">
    <div class="item-outer">
        <div class="p-detail-author-wrapper">
            <div class="p-detail-author-image">
                {img user=$reviewer suffix='_50_square'}
            </div>
            <div class="p-detail-author-info">
                <span class="item-star-rating">
                    <div class="p-outer-rating p-outer-rating-row mini p-rating-sm">
                        <div class="p-outer-rating-row">
                            <div class="p-rating-count-star">{$reviewer.rating}</div>
                             <div class="p-rating-star">
                                {$reviewer.rating_star}
                            </div>
                        </div>
                    </div>
                </span>
                <span>
                    {_p var='by'} <span class="user_profile_link_span" id="js_user_name_link_{$reviewer.user_name}" itemprop="author"><a href="{$reviewer.author_url}" rel="author">{$reviewer.full_name}</a></span> {_p var='on'} {$reviewer.review_time}
                </span>
            </div>

        </div>
        <div class="item-review-text">
            {$reviewer.content|parse}
        </div>
        {if $reviewer.can_delete_own_review || $reviewer.can_delete_all_review}
        <div class="item-option">
            <a href="javascript:void(0);" class="p-option-button" onclick="appAdvMarketplace.deleteReview({$listing_id},{$reviewer.rate_id}); return false;"><i class="ico ico-close"></i></a>
        </div>
        {/if}
    </div>
</div>