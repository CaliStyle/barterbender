{literal}
<script language="javascript" type="text/javascript">
	$Behavior.advmarket_indexaction = function(){
		if($("#jhslider").size() > 0) {
			$($(".header_bar_float").get(2)).hide();
			$($(".header_bar_float").get(1)).hide();
		}
	};
</script>
{/literal}
{if !PHPFOX_IS_AJAX}
    {template file='advancedmarketplace.block.adv_search'}
{/if}
{if !$bIsInHomePage}
    {if isset($aListings) && count($aListings)}
        <div class="ync-block">
        <div class="ynmarketplace-listing-wapper item-container ync-listing-container ync-list-layout">
            {foreach from=$aListings name=listings item=aListing}
                <article id="js_mp_item_holder_{$aListing.listing_id}" class="ynmarketplace-listing-inner ync-item">
                    <div class="item-outer my-listing">
                        <div class="ynmarketplace-listing-wappe-inner">
                            <div class="ynmarketplace_listing-photo">
                                <a href="{$aListing.url}" class="ynmarketplace_listing-thumb" style="background-image: url({if $aListing.image_path != NULL}
                                        {img server_id=$aListing.server_id path='advancedmarketplace.url_pic' return_url=true file=$aListing.image_path suffix='_400_square' title=$aListing.title }
                                    {else}
                                       {$corepath}module/advancedmarketplace/static/image/default/noimage.png
                                    {/if})" >
                                </a>
                                {if Phpfox::getUserParam('advancedmarketplace.can_approve_listings') || Phpfox::getUserParam('advancedmarketplace.can_delete_other_listings')}
                                    <div class="moderation_row">
                                        <label class="item-checkbox">
                                            <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aListing.listing_id}" id="check{$aListing.listing_id}" />
                                            <i class="ico ico-square-o"></i>
                                        </label>
                                    </div>
                                {/if}
                                {if isset($aListing.is_expired)}
                                    <div class="ynmarketplace-item-flag">
                                        <div class="sticky-label-icon sticky-closed-icon" {if !$aListing.is_featured} style="display:none;"{/if}>
                                            <span class="flag-style-arrow"></span>
                                            <i class="ico ico-warning"></i>
                                        </div>
                                    </div>
                                {else}
                                    <div class="ynmarketplace-item-flag js_status_icon_item_{$aListing.listing_id}">
                                        {template file='advancedmarketplace.block.status-icon-entry'}
                                    </div>
                                {/if}
                            </div>
                            <div class="ynmarketplace_listing-infomation">
                                <a href="{$aListing.url}" class="advynmarketplace_listing-title pr-4" title="{$aListing.title|clean}">{$aListing.title}</a>
                                {if $aListing.view_id == '2'}<span class="advancedmarketplace_item_sold">({phrase var='advancedmarketplace.sold'})</span>{/if}

                                {if $aListing.post_status == 2}
                                    <div>{phrase var='advancedmarketplace.draft_info'}</div>
                                {/if}

                                <div class="item-author mt-1">
                                    <span class="item-author-info">{_p var='by_full_name' full_name=$aListing|user:'':'':50:'':'author'}</span>
                                    <span>{_p var='on'} {$aListing.time_stamp|convert_time}</span>
                                </div>

                                <div class="ynmarketplace_listing-description">{$aListing.short_description|parse|highlight:'search'|split:25|shorten:200:'...'}</div>

                                <a class="ynmarketplace_listing-location fz-12 text-gray-dark space-left error-icon d-block mt-1" href="{url link='advancedmarketplace' val[country_iso]=$aListing.country_iso}"><i class="ico ico-checkin"></i>{$aListing.country_iso|location}</a>

                                <div class="ynmarketplace_listing-review mt-1">
                                    <div class="ynmarketplace_listing-price {if $aListing.price == '0.00'}text-free{else}text-warning{/if}">
                                        {if $aListing.price == '0.00'}
                                        {phrase var='advancedmarketplace.free'}
                                        {else}
                                        {$aListing.listing_price}
                                        {/if}
                                    </div>
                                    <div class="ync-outer-rating ync-outer-rating-row mini ync-rating-sm">
                                        <div class="ync-outer-rating-row">
                                             <div class="ync-rating-star">
                                                {for $i = 0; $i < 5; $i++}
                                                    {if $i < (int)$aListing.rating}
                                                        <i class="ico ico-star" aria-hidden="true"></i>
                                                    {elseif ((round($aListing.rating) - $aListing.rating) > 0) && ($aListing.rating - $i) > 0}
                                                        <i class="ico ico-star half-star" aria-hidden="true"></i>
                                                    {else}
                                                        <i class="ico ico-star disable" aria-hidden="true"></i>
                                                    {/if}
                                                {/for}
                                            </div>
                                        </div>
                                        <div class="ync-rating-count-review-wrapper">
                                            <span class="ync-rating-count-review">
                                                <span class="item-number">{$aListing.rating_count}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                {if ($aListing.user_id == Phpfox::getUserId() && Phpfox::getUserParam('advancedmarketplace.can_edit_own_listing')) || Phpfox::getUserParam('advancedmarketplace.can_edit_other_listing')
                                || ($aListing.user_id == Phpfox::getUserId() && Phpfox::getUserParam('advancedmarketplace.can_delete_own_listing')) || Phpfox::getUserParam('advancedmarketplace.can_delete_other_listings')
                                || (Phpfox::getUserParam('advancedmarketplace.can_feature_listings'))}
                                    <div class="dropdown ynmarketplace-actions">
                                        <span role="button" class="s-4 fevent-actions-toggle text-gray-dark center" data-toggle="dropdown">
                                            <i class="ico ico-gear-o"></i>
                                        </span>
                                        <ul class="dropdown-menu dropdown-menu-right">{template file='advancedmarketplace.block.menu'}</ul>
                                    </div>
                                {/if}
                            </div>
                        </div>

                        <div class="ynmarketplace_listing-comment">
                            {module name='feed.comment' aFeed=$aListing.aFeed}
                        </div>
                    </div>
                </article>
            {/foreach}
        </div>
    </div>

    {if Phpfox::getUserParam('advancedmarketplace.can_delete_other_listings') || Phpfox::getUserParam('advancedmarketplace.can_feature_listings') || (Phpfox::getUserParam('advancedmarketplace.can_approve_listings') && $sListingView == 'pending')}
        {moderation}
    {/if}
    {pager}
    {elseif !PHPFOX_IS_AJAX}
        <div class="extra_info">
            {phrase var='advancedmarketplace.no_advancedmarketplace_listings_found'}
        </div>
    {/if}
{elseif $bIsInPage}
    {if isset($aListings) && count($aListings)}
        <div class="ync-block">
        <div class="ynmarketplace-listing-wapper item-container ync-listing-container ync-list-layout">
            {foreach from=$aListings name=listings item=aListing}
                <article id="js_mp_item_holder_{$aListing.listing_id}" class="ynmarketplace-listing-inner ync-item">
                    <div class="item-outer my-listing">
                        <div class="ynmarketplace-listing-wappe-inner">
                            <div class="ynmarketplace_listing-photo">
                                <a href="{$aListing.url}" class="ynmarketplace_listing-thumb" style="background-image: url({if $aListing.image_path != NULL}
                                        {img server_id=$aListing.server_id path='advancedmarketplace.url_pic' return_url=true file=$aListing.image_path suffix='_400_square' title=$aListing.title }
                                    {else}
                                       {$corepath}module/advancedmarketplace/static/image/default/noimage.png
                                    {/if})" >
                                </a>
                                {if Phpfox::getUserParam('advancedmarketplace.can_approve_listings') || Phpfox::getUserParam('advancedmarketplace.can_delete_other_listings')}
                                    <div class="moderation_row">
                                        <label class="item-checkbox">
                                            <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aListing.listing_id}" id="check{$aListing.listing_id}" />
                                            <i class="ico ico-square-o"></i>
                                        </label>
                                    </div>
                                {/if}
                                {if isset($aListing.is_expired)}
                                    <div class="ynmarketplace-item-flag">
                                        <div class="sticky-label-icon sticky-closed-icon" {if !$aListing.is_featured} style="display:none;"{/if}>
                                            <span class="flag-style-arrow"></span>
                                            <i class="ico ico-warning"></i>
                                        </div>
                                    </div>
                                {else}
                                    <div class="ynmarketplace-item-flag js_status_icon_item_{$aListing.listing_id}">
                                        {template file='advancedmarketplace.block.status-icon-entry'}
                                    </div>
                                {/if}
                            </div>
                            <div class="ynmarketplace_listing-infomation">
                                <a href="{$aListing.url}" class="advynmarketplace_listing-title pr-4" title="{$aListing.title|clean}">{$aListing.title}</a>
                                {if $aListing.view_id == '2'}<span class="advancedmarketplace_item_sold">({phrase var='advancedmarketplace.sold'})</span>{/if}

                                {if $aListing.post_status == 2}
                                    <div>{phrase var='advancedmarketplace.draft_info'}</div>
                                {/if}

                                <div class="item-author mt-1">
                                    <span class="item-author-info">{_p var='by_full_name' full_name=$aListing|user:'':'':50:'':'author'}</span>
                                    <span>{_p var='on'} {$aListing.time_stamp|convert_time}</span>
                                </div>

                                <div class="ynmarketplace_listing-description">{$aListing.short_description|parse|highlight:'search'|split:25|shorten:200:'...'}</div>

                                <a class="ynmarketplace_listing-location fz-12 text-gray-dark space-left error-icon d-block mt-1" href="{url link='advancedmarketplace' val[country_iso]=$aListing.country_iso}"><i class="ico ico-checkin"></i>{$aListing.country_iso|location}</a>

                                <div class="ynmarketplace_listing-review mt-1">
                                    <div class="ynmarketplace_listing-price">
                                        {if $aListing.price == '0.00'}
                                        {phrase var='advancedmarketplace.free'}
                                        {else}
                                        {$aListing.listing_price}
                                        {/if}
                                    </div>
                                    <div class="ync-outer-rating ync-outer-rating-row mini ync-rating-sm">
                                        <div class="ync-outer-rating-row">
                                             <div class="ync-rating-star">
                                                {for $i = 0; $i < 5; $i++}
                                                    {if $i < (int)$aListing.rating}
                                                        <i class="ico ico-star" aria-hidden="true"></i>
                                                    {elseif ((round($aListing.rating) - $aListing.rating) > 0) && ($aListing.rating - $i) > 0}
                                                        <i class="ico ico-star half-star" aria-hidden="true"></i>
                                                    {else}
                                                        <i class="ico ico-star disable" aria-hidden="true"></i>
                                                    {/if}
                                                {/for}
                                            </div>
                                        </div>
                                        <div class="ync-rating-count-review-wrapper">
                                            <span class="ync-rating-count-review">
                                                <span class="item-number">{$aListing.rating_count}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                {if ($aListing.user_id == Phpfox::getUserId() && Phpfox::getUserParam('advancedmarketplace.can_edit_own_listing')) || Phpfox::getUserParam('advancedmarketplace.can_edit_other_listing')
                                || ($aListing.user_id == Phpfox::getUserId() && Phpfox::getUserParam('advancedmarketplace.can_delete_own_listing')) || Phpfox::getUserParam('advancedmarketplace.can_delete_other_listings')
                                || (Phpfox::getUserParam('advancedmarketplace.can_feature_listings'))}
                                    <div class="dropdown ynmarketplace-actions">
                                        <span role="button" class="s-4 fevent-actions-toggle text-gray-dark center" data-toggle="dropdown">
                                            <i class="ico ico-gear-o"></i>
                                        </span>
                                        <ul class="dropdown-menu dropdown-menu-right">{template file='advancedmarketplace.block.menu'}</ul>
                                    </div>
                                {/if}
                            </div>
                        </div>

                        <div class="ynmarketplace_listing-comment">
                            {module name='feed.comment' aFeed=$aListing.aFeed}
                        </div>
                    </div>
                </article>
            {/foreach}
        </div>
    </div>

    {if Phpfox::getUserParam('advancedmarketplace.can_delete_other_listings') || Phpfox::getUserParam('advancedmarketplace.can_feature_listings') || (Phpfox::getUserParam('advancedmarketplace.can_approve_listings') && $sListingView == 'pending')}
        {moderation}
    {/if}
    {pager}
    {elseif !PHPFOX_IS_AJAX}
        <div class="extra_info">
            {phrase var='advancedmarketplace.no_advancedmarketplace_listings_found'}
        </div>
    {/if}
{/if}