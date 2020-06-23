<article class="p-advmarketplace-item p-item js_listing_item_{$aListing.listing_id}">
    <div class="item-outer">
        <div class="p-item-media-wrapper p-margin-default p-advmarketplace-item-media js_p_advmarketplace_photo_main">
            {if $bShowModerator}
            <div class="moderation_row">
                <label class="item-checkbox">
                    <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aListing.listing_id}" id="check{$aListing.listing_id}" />
                    <i class="ico ico-square-o"></i>
                </label>
            </div>
            {/if}

            <a href="{$aListing.url}" class="item-media-link">
                {if $aListing.image_path}
                    {img ref=$aListing.url title=$aListing.title server_id=$aListing.server_id path='advancedmarketplace.url_pic' file=$aListing.image_path suffix=''}
                {else}
                    {img path='core.path_actual' file='PF.Site/Apps/p-advmarketplace/assets/image/default/no-image.png'}
                {/if}
            </a>
            {if !$isSideLocation}
                <div class="p-item-flag-wrapper js_status_icon_item_{$aListing.listing_id}">
                    {template file='advancedmarketplace.block.status-icon-entry'}
                </div>
            {/if}
        </div>
        <div class="item-inner">
            <div class="p-advmarketplace-item-title-wrapper">
                <h4 class="p-item-title truncate-2">
                    <a href="{$aListing.url}" class="advynmarketplace_listing-title" title="{$aListing.title|clean}">
                        {if $aListing.post_status == 2}
                        <span class="p-advmarketplace-title-label p-label-status solid draft">{_p var='draft'}</span>
                        {elseif !empty($aListing.is_expired)}
                        <span class="p-advmarketplace-title-label p-label-status solid danger">{_p var='expired'}</span>
                        {elseif $aListing.view_id == 2}
                        <span class="p-advmarketplace-title-label p-label-status solid info">{_p var='sold'}</span>
                        {/if}
                        <span>{$aListing.title}</span>
                    </a>
                </h4>
                {if $showConfigBtn && $aListing.canDoPermission}
                <div class="p-advmarketplace-action-container">
                    <div class="dropdown">
                        <span class="p-option-button dropdown-toggle" data-toggle="dropdown">
                            <i class="ico ico-gear-o"></i>
                        </span>
                        <ul class="dropdown-menu dropdown-menu-right">
                            {template file='advancedmarketplace.block.menu'}
                        </ul>
                    </div>
                </div>
                {/if}
            </div>
            <div class="p-item-minor-info p-advmarketplace-item-author p-hidden-side-block">
                <span class="item-author"><span class="p-text-capitalize">{_p var='by'}</span> {$aListing|user:'':'':50:'':'author'}</span>
            </div>

            {if $showStatistic == 'rating' || !$isSideLocation}
            <div class="p-advmarketplace-item-rating">
            {if !empty($aListing.average_score)}
                <div class="p-outer-rating p-outer-rating-row mini p-rating-sm">
                    <div class="p-outer-rating-row">
                        <div class="p-rating-count-star">{if !empty($aListing.average_score)}{$aListing.average_score}{else}0{/if}</div>
                        <div class="p-rating-star">
                            {$aListing.rating_star}
                        </div>
                    </div>
                    <div class="p-rating-count-review-wrapper">
                        <span class="p-rating-count-review">
                            <span class="item-number">{if !empty($aListing.total_rate)}{$aListing.total_rate}{else}0{/if}</span>
                        </span>
                    </div>
                </div>
            {else}
            <div class="p-advmarketplace-no-review-text">{_p var='be_the_first_to_review'}</div>
            {/if}
            </div>
            <div class="p-item-minor-info p-hidden-side-block">
                <span class="item-location">{$aListing.country_iso|location}</span>
            </div>
            {else}
            <div class="p-item-minor-info p-hidden-middle-block">
                {if $showStatistic == 'view'}
                    {$aListing.total_view} <span class="p-text-lowercase">{if $aListing.total_view == 1}{_p var='advancedmarketplace_view_lowercase'}{else}{_p var='advancedmarketplace_views_lowercase'}{/if}</span>
                {elseif $showStatistic == 'like'}
                    {$aListing.total_like} <span class="p-text-lowercase">{if $aListing.total_like == 1}{_p var='advancedmarketplace_like_lowercase'}{else}{_p var='advancedmarketplace_likes_lowercase'}{/if}</span>
                {elseif $showStatistic == 'comment'}
                    {$aListing.total_comment} <span class="p-text-lowercase">{if $aListing.total_comment == 1}{_p var='advancedmarketplace_comment_lowercase'}{else}{_p var='advancedmarketplace_comments_lowercase'}{/if}</span>
                {/if}
            </div>
            {/if}

            {if !empty($showDescription) || empty($dataSource)}
            <div class="p-item-description truncate-2 p-advmarketplace-item-description p-hidden-middle-block">
                {$aListing.description_parsed|highlight:'search'|parse|shorten:50|split:55|max_line}
            </div>
            {/if}

            <div class="p-advmarketplace-item-price-action-wrapper">
                <div class="p-advmarketplace-item-price">
                    {if $aListing.price == '0.00'}
                        <span class="p-text-success">
                            {phrase var='advancedmarketplace.free'}
                        </span>
                    {else}
                        {$aListing.listing_price}
                    {/if}

                </div>
                {if !$isSideLocation}
                <div class="p-advmarketplace-item-action-container">
                    <div class="p-form-group-btn-container">
                        {if ($aListing.post_status != 2) && ($aListing.view_id == 0)}
                        <a href="javascript:void(0);" class=" p-advmarketplace-item-wishlist-action js_wishlist_btn {if $aListing.is_wishlist}checked{/if}" title="{_p var='advancedmarketplace_wish_list_upper_case_first_letter_replacement'}" data-id="{$aListing.listing_id}" data-wishlist="{if $aListing.is_wishlist}0{else}1{/if}" data-wishlist-page="{$isWishlistPage}" onclick="appAdvMarketplace.processWishlist(this); return false;"><i class="ico ico-heart-o"></i></a>
                        {/if}
                    </div>
                </div>
                {/if}
            </div>
        </div>
    </div>
</article>