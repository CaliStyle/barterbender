<li class="item js_listing_item_{$aListing.listing_id}">
    <div class="p-item p-advmarketplace-item">
        <div class="item-outer">
            <div class="p-item-media-wrapper p-margin-default p-advmarketplace-item-media js_p_advmarketplace_photo_main">
                <a href="{$aListing.url}" class="item-media-link">
                    {if !empty($aListing.image_path)}
                        <img class="focus js_p_advmarketplace_slider_photo_main main_photo" data-photo-main="1" src="{img return_url=true ref=$aListing.url title=$aListing.title server_id=$aListing.server_id path='advancedmarketplace.url_pic' file=$aListing.image_path suffix='_400'}" alt="">
                    {else}
                        <img class="focus js_p_advmarketplace_slider_photo_main main_photo" data-photo-main="1" src="{img return_url=true path='core.path_actual' file='PF.Site/Apps/p-advmarketplace/assets/image/default/no-image-big.png'}" alt="">
                    {/if}
                    {if !empty($aListing.selected_images)}
                        {foreach from=$aListing.selected_images item=image}
                            <img class="js_p_advmarketplace_slider_photo_main" data-photo-main="{$image.position}" src="{img return_url=true ref=$image.image_path title=$image.title server_id=$image.server_id path='advancedmarketplace.url_pic' file=$image.image_path suffix='_400'}">
                        {/foreach}
                    {/if}
                </a>
            </div>
            <div class="item-inner">
                <h4 class="p-item-title ">
                    <a href="{$aListing.url}" class="" >
                        <span>{$aListing.title}</span>
                    </a>
                </h4>
                <div class="p-item-minor-info p-seperate-dot-wrapper p-seperate-dot-item">
                    <span class="p-seperate-dot-item item-author"><span class="p-text-capitalize">{_p var='by'}</span> {$aListing|user:'':'':50:'':'author'}</span>
                    {if !empty($aListing.country_iso|location)}
                    <span class="p-seperate-dot-item item-location"> {if !empty($aListing.country_child_id)}{$aListing.country_child_id|location_child}, {/if}{$aListing.country_iso|location}</span>
                    {/if}
                </div>
                <div class="p-advmarketplace-item-rating">
                    {if !empty($aListing.total_rate)}
                    <div class="p-outer-rating p-outer-rating-row mini p-rating-sm">
                        <div class="p-outer-rating-row">
                            <div class="p-rating-count-star"></div>
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
                <div class="p-advmarketplace-item-price">
                    {if $aListing.price == '0.00'}
                        <span class="p-text-success">
                            {phrase var='advancedmarketplace.free'}
                        </span>
                    {else}
                        {$aListing.listing_price}
                    {/if}
                </div>
                {if !empty($aListing.selected_images)}
                <div class="p-advmarketplace-item-thumb-feature">
                        {foreach from=$aListing.selected_images item=image}
                            <div class="item-thumb js_p_advmarketplace_slider_photo_thumb" data-photo-thumb="{$image.position}">
                                <div class="item-thumb-media-wrapper">
                                    <a href="{$aListing.url}" class="item-media-link">
                                        <span class="item-media-src" style="background-image: url({img return_url=true ref=$image.image_path title=$image.title server_id=$image.server_id path='advancedmarketplace.url_pic' file=$image.image_path suffix='_120'})"></span>
                                    </a>
                                </div>
                            </div>
                        {/foreach}
                </div>
                {/if}
                <div class="p-advmarketplace-item-description">
                    {$aListing.short_description_parsed|parse|strip_tags|clean}
                </div>
                <div class="p-advmarketplace-item-action-container">
                    <div class="p-form-group-btn-container">
                        <a href="{$aListing.url}" class="btn btn-primary">{_p var='shop_now'}</a>
                        <a href="javascript:void(0);" class="btn btn-default p-advmarketplace-item-wishlist-action {if $aListing.is_wishlist}checked{/if} js_wishlist_btn" title="{_p var='advancedmarketplace_wish_list_upper_case_first_letter_replacement'}" data-id="{$aListing.listing_id}" data-wishlist="{if $aListing.is_wishlist}0{else}1{/if}" onclick="appAdvMarketplace.processWishlist(this); return false;"><i class="ico ico-heart-o"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</li>