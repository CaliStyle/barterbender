<div class="p-advmarketplace-review-container">
    <div class="p-advmarketplace-review-container-outer">
        <div class="p-advmarketplace-review-side side-left">
            <div class="item-review-rating-general {if empty($aListing.average_score)}no-review{/if}">
                <div class="p-outer-rating p-rating-lg p-outer-rating-column full">
                    <div class="p-outer-rating-column">
                        <div class="p-rating-count-star">{if !empty($aListing.average_score)}{$aListing.average_score}{else}{_p var='no_ratings'}{/if}</div>
                        <div class="p-rating-star">
                            {if !empty($aListing.average_score)}
                                {$aListing.total_rating_star}
                            {else}
                            <i class="ico ico-star disable"></i>
                            <i class="ico ico-star disable"></i>
                            <i class="ico ico-star disable"></i>
                            <i class="ico ico-star disable"></i>
                            <i class="ico ico-star disable"></i>
                            {/if}

                        </div>
                    </div>
                    <div class="p-rating-count-review-wrapper">
                        <span class="p-rating-count-review">
                            <span class="item-number">{if !empty($aListing.total_review)}{$aListing.total_review}{else}0{/if}</span>
                            <span class="item-text">{_p var='review_s'}</span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="item-review-rating-statistic">
                <div class="p-advmarketplace-review-statistic-listing">
                    {foreach from=$aListing.review_chart item=review_chart}
                    <div class="p-advmarketplace-review-statistic-item">
                        <div class="item-outer">
                            <div class="item-title">
                                <div class="p-outer-rating p-outer-rating-row mini p-rating-sm">
                                    <div class="p-outer-rating-row">
                                        <div class="p-rating-star">
                                            {$review_chart.rating_star}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="item-statistic">
                                <div class="item-chart">
                                    <div class="chart-processor">
                                        <div class="chart-success js_advmarketplace-chart-success" data-chart-success="{$review_chart.total_review}" data-chart-total="{$aListing.total_rate}">
                                        </div>
                                    </div>
                                </div>
                                <div class="item-number">
                                    {$review_chart.total_review}
                                </div>
                            </div>
                        </div>
                    </div>
                    {/foreach}
                </div>
            </div>
        </div>
        {if (Phpfox::getUserParam('advancedmarketplace.can_post_a_review') && empty($aListing.rate_id)) || (!empty($aListing.rate_id) && $aListing.review_user_id == Phpfox::getUserId())}
        <div class="p-advmarketplace-review-side side-right">
            <form id="js_advancedmarketplace_form_rating" onsubmit="return appAdvMarketplace.submitReview(this);">
                <input type="hidden" name="val[listing_id]" value="{$aListing.listing_id}">
                <div class="p-advmarketplace-review-my-container">
                    {if !empty($aListing.rating)}
                    <div class="item-my-review">
                        <div class="item-my-review-content">
                            <div class="item-info">
                                {_p var='advancedmarketplace_your_review_on' time=$aListing.review_time}
                            </div>
                            <div class="item-review-text">
                                {$aListing.review_content}
                            </div>
                        </div>
                        {if $aListing.can_do_action_review}
                        <div class="item-my-review-action">
                            <div class="dropdown">
                            <span class="p-option-button dropdown-toggle" data-toggle="dropdown">
                                <i class="ico ico-dottedmore-vertical"></i>
                            </span>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    {if $aListing.can_delete_own_review || $aListing.can_delete_all_review}
                                    <li>
                                        <a href="javascript:void(0);" onclick="appAdvMarketplace.deleteReview({$aListing.listing_id},{$aListing.rate_id}); return false;">{_p var='delete'}</a>
                                    </li>
                                    {/if}
                                </ul>
                            </div>
                        </div>
                        {/if}
                    </div>
                    {else}
                        <textarea class="form-control" name="val[text]" id="" cols="30" rows="10" placeholder="{_p var='advancedmarketplace_write_your_review_here'}"></textarea>
                    {/if}
                    <div class="item-bottom">
                        <div class="p-advmarketplace-rating-action-vote js_rating_action_vote">
                            <input type="hidden" name="val[rating]" value="{if !empty($aListing.rating)}{$aListing.rating}{else}0{/if}" id="js_total_rating">
                            <span class="p-text-uppercase p-text-gray fw-bold">{_p var='advancedmarketplace_your_rate'}:</span>
                            <div class="p-outer-rating p-outer-rating-row mini p-rating-lg">
                                <div class="p-outer-rating-row">
                                    <div class="p-rating-star {if !empty($aListing.rate_id)}reviewed{/if}">
                                        {if !empty($aListing.rating)}
                                            {$aListing.rating_star}
                                        {else}
                                        <i class="ico ico-star disable" data-value="1"></i>
                                        <i class="ico ico-star disable" data-value="2"></i>
                                        <i class="ico ico-star disable" data-value="3"></i>
                                        <i class="ico ico-star disable" data-value="4"></i>
                                        <i class="ico ico-star disable" data-value="5"></i>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                        </div>
                        {if Phpfox::getUserParam('advancedmarketplace.can_post_a_review') && empty($aListing.rating)}
                        <div class="item-action">
                            <button class="btn btn-sm btn-primary js_submit_review_btn">{_p var='advancedmarketplace_submit_review'}</button>
                        </div>
                        {/if}
                    </div>
                </div>
            </form>
        </div>
        {/if}
    </div>
</div>