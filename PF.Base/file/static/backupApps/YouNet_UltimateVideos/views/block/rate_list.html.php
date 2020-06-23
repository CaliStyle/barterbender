<?php
?>

<?php

defined('PHPFOX') or exit('NO DICE!');

?>
{if $aViewerRate}
<div class="ultimatevideo-popup-user-container my-rating">
    <div class="item-user-row">
        <div class="item-outer">
            <div class="item-media">
                {img user=$aViewer suffix='_50_square' max_width=50 max_height=50}
            </div>
            <div class="item-inner">
                <div class="item-inner-wrapper">
                    <div class="item-wrapper-left">
                        <div class="p-text-uppercase fw-bold p-text-gray p-mb-line">
                            {_p var='your_rate'}
                        </div>
                        <div class="p-outer-rating p-outer-rating-row p-rating-lg">
                            <div class="p-rating-star p-can-rate" data-rating="{$aViewerRate.rating}">
                                {$aViewerRate.rating|ultimatevideo_rating:$iVideoId}
                            </div>
                        </div>
                        <span class="p-text-gray ml-1 p-text-sm">({_p var='you_can_rate_again'})</span>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
{/if}
{if count($aRates)}
    {if !$bIsPaging}
        <div class="ultimatevideo-popup-user-container">
    {/if}
    {foreach from=$aRates name=like item=aRate}
        <div id="js_row_like_{$aRate.user_id}" class="like-browse-item popup-user-item">
            {module name='ultimatevideo.rating_item' user_id=$aRate.user_id rating=$aRate.rating}
        </div>
    {/foreach}
    {if $hasPagingNext}
        {pager}
    {/if}
    {if !$bIsPaging}
        </div>
    {/if}
{else}
    {if !$bIsPaging}
        <div class="extra_info">
            {$sErrorMessage}
        </div>
    {/if}
{/if}
