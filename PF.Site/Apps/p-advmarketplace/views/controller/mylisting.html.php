<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="ynmarketplace-listing-wappers">
    {foreach from=$aListings key=iKey item=aListing}
    <div class="ynmarketplace-listing-wapper">
        <div class="ynmarketplace_listing-thumb-rating-review">
            <div class="ynmarketplace-item-flag">
            {if isset($sView) && $sView == 'featured'}
            {else}
            <div id="js_featured_phrase_{$aListing.listing_id}" class="row_featured_link"{if !$aListing.is_featured} style="display:none;"{/if}>
                {phrase var='advancedmarketplace.featured'}
            </div>
            {/if}
            <div id="js_sponsor_phrase_{$aListing.listing_id}" class="js_sponsor_event row_sponsored_link"{if !$aListing.is_sponsor} style="display:none;"{/if}>
                {phrase var='advancedmarketplace.sponsored'}
            </div>
            </div>
            <a href="{$aListing.url}" class="ynmarketplace_listing-thumb" style="background-image: url({if $aListing.image_path != NULL}
                    {img server_id=$aListing.server_id path='advancedmarketplace.url_pic' return_url=true file=$aListing.image_path suffix='_120_square' max_width=120 max_height=120 title=$aListing.title }
                {else}
                   {$corepath}module/advancedmarketplace/static/image/default/noimage.png
                {/if})" >
            </a>

            <div class="listing_rate" style="background: none;width: 140px;">
                <div>
                    <?php for($i = 1; $i <= floor($this->_aVars["aListing"]["rating"] / 2); $i++) {ldelim} ?>
                    <img src="{$corepath}module/advancedmarketplace/static/image/default/staronsm.png" />
                    <?php {rdelim} ?>
                    <?php for($i = 1; $i <= ceil(5 - $this->_aVars["aListing"]["rating"] / 2); $i++) {ldelim} ?>
                    <img src="{$corepath}module/advancedmarketplace/static/image/default/staroffsm.png" />
                    <?php {rdelim} ?>
                </div>
                <div>
                    {phrase var="advancedmarketplace.nreview_review_s" nreview=$aListing.rating_count}
                </div>
            </div>

        </div>

        <div class="row_title_image_header_body">
            <div class="row_title">
                <div class="row_title_image">
                    {img user=$aListing suffix='_50_square' max_width='50' max_height='50'}
                    {if ($aListing.user_id == Phpfox::getUserId() && Phpfox::getUserParam('advancedmarketplace.can_edit_own_listing')) || Phpfox::getUserParam('advancedmarketplace.can_edit_other_listing')
                    || ($aListing.user_id == Phpfox::getUserId() && Phpfox::getUserParam('advancedmarketplace.can_delete_own_listing')) || Phpfox::getUserParam('advancedmarketplace.can_delete_other_listings')
                    || (Phpfox::getUserParam('advancedmarketplace.can_feature_listings'))
                    }
                    <div class="row_edit_bar_parent">
                        <div class="row_edit_bar">
                            <a role="button" class="row_edit_bar_action" data-toggle="dropdown">
                                <i class="fa fa-action"></i>
                            </a>
                            <ul class="dropdown-menu">
                                {template file='advancedmarketplace.block.menu'}
                            </ul>
                        </div>
                    </div>
                    {/if}
                    {if Phpfox::getUserParam('advancedmarketplace.can_approve_listings') || Phpfox::getUserParam('advancedmarketplace.can_delete_other_listings')}
                    <div class="moderation_row">
                        <label class="item-checkbox">
                            <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aListing.listing_id}" id="check{$aListing.listing_id}" />
                            <i class="ico ico-square-o"></i>
                        </label>
                    </div>
                    {/if}
                </div>

                <div class="row_title_info">
                    <a class="advlink" href="{$aListing.url}">{$aListing.title}</a>
                    <div class="advancedmarketplace_price_tag">
                        {if $aListing.price == '0.00'}
                        {phrase var='advancedmarketplace.free'}
                        {else}
                        {$aListing.currency_id|currency_symbol}{$aListing.price}
                        {/if}
                    </div>
                    <div class="extra_info"> {$aListing.time_stamp|convert_time} <span>&middot;</span> {$aListing|user} <span>&middot;</span> <a class="js_hover_title" href="{url link='advancedmarketplace' location=$aListing.country_iso}">{$aListing.country_iso|location}<span class="js_hover_info">{if !empty($aListing.city)} {$aListing.city|clean} &raquo; {/if}{if !empty($aListing.country_child_id)} {$aListing.country_child_id|location_child} &raquo; {/if} {$aListing.country_iso|location}</span></a></div>
                    <div class="item_content">
                        {$aListing.description|parse|highlight:'search'|split:25|shorten:200:'...'}
                    </div>
                </div>

            </div>
        </div>

    </div>
    {/foreach}
    {if Phpfox::getUserParam('advancedmarketplace.can_delete_other_listings') || Phpfox::getUserParam('advancedmarketplace.can_feature_listings') || Phpfox::getUserParam('advancedmarketplace.can_approve_listings')}
    {moderation}
    {/if}
    {pager}
</div>