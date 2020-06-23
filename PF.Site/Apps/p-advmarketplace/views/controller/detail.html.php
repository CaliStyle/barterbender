<?php

defined('PHPFOX') or exit('NO DICE!');

?>

<div class="p-detail-container" id="js_advmarketplace_detail_container">
    {if (int)$aListing.view_id == 1}
        {template file='core.block.pending-item-action'}
    {/if}
    <div class="p-detail-main-content p-advmarketplace-detail-main-content">

        <div class="p-advmarketplace-detail-main-info-block">
            <h1 class="p-detail-header-page-title header-page-title item-title {if isset($aTitleLabel.total_label) && $aTitleLabel.total_label > 0}header-has-label-{$aTitleLabel.total_label}{/if}">
                <a href="javascript:void(0);" class="ajax_link">
                    {if $aListing.post_status == 2}<span class="p-advmarketplace-title-label p-label-status solid draft mr-1">{_p var='draft'}</span>{/if}
                    {$aListing.title|parse}
                </a>
                <div class="p-type-id-icon js_status_icon_item_{$aListing.listing_id}">
                    {template file='advancedmarketplace.block.status-icon-entry'}
                </div>
            </h1>
            <div class="p-detail-statistic-wrapper">
                <div class="">
                    <div class="p-outer-rating p-rating-sm p-outer-rating-row full">
                        <div class="p-outer-rating-row">
                             <div class="p-rating-star">
                                {$aListing.total_rating_star}
                            </div>
                        </div>
                        <div class="p-rating-count-review-wrapper js_total_review">
                            <span class="p-rating-count-review">
                                <span class="item-number">{if !empty($aListing.total_review)}{$aListing.total_review}{else}0{/if}</span>
                                <span class="item-text">{if empty($aListing.total_review) || (int)$aListing.total_review > 1}{_p var='advancedmarketplace_reviews'}{else}{_p var='advancedmarketplace_review'}{/if}</span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="p-detail-statistic-list">
                    {if $aListing.total_view > 1 || (int)$aListing.total_view == 0}
                        <span class="item-statistic">{$aListing.total_view} {_p var='views'}</span>
                    {else}
                        <span class="item-statistic">{_p var='one_view'}</span>
                    {/if}
                </div>
            </div>
            <div class="p-detail-action-wrapper">
                <div class="p-detail-action-list js_detail_action_list">
                   {if $aListing.view_id == 0 && $aListing.post_status != 2}
                   <div class="item-action">
                       <a href="javascript:void(0);" class="p-advmarketplace-item-wishlist-action btn btn-default btn-sm btn-icon js_wishlist_btn {if !empty($aListing.is_wishlist)}checked{/if}" data-id="{$aListing.listing_id}" data-wishlist="{if $aListing.is_wishlist}0{else}1{/if}" data-detail="1" onclick="appAdvMarketplace.processWishlist(this); return false;">
                           <i class="ico ico-heart-o"></i>
                           <span class="item-text fw-bold js_wishlist_text">{if !empty($aListing.is_wishlist)}{_p var='added_to_wish_list_replacement'}{else}{_p var='advancedmarketplace_add_to_wishlist'}{/if}</span>
                       </a>
                   </div>
                   {/if}
                   {if Phpfox::isAppActive('Core_Messages') && $aListing.user_id != Phpfox::getUserId()}
                   <div class="item-action">
                       <a href="javascript:void(0);" class="btn btn-default btn-sm btn-icon" onclick="appAdvMarketplace.contactSeller({l}id: {$aListing.user_id}, listing_id: {$aListing.listing_id}, module_id: 'advancedmarketplace'{r}); return false;">
                           <i class="ico ico-comment-o"></i>
                           <span class="item-text fw-bold">{_p var='contact_seller'}</span>
                       </a>
                   </div>
                   {/if}
                </div>
            </div>
            <div class="p-detail-author-wrapper">
                <div class="p-detail-author-info">
                    <span class="item-author"><span class="item-text-label">{_p var='by'}</span> {$aListing|user:'':'':50:'':'author'}</span>
                    {if Phpfox::getParam('advancedmarketplace.advmarketplace_display_update_date')}
                        <span class="item-time">
                            {if $aListing.update_timestamp}
                                {_p var='advancedmarketplace_updated_on'} {$aListing.update_timestamp|date:'advancedmarketplace.advancedmarketplace_view_time_stamp'} {_p var='in'} <a href="{$aListing.categories.0.1}">{$aListing.categories.0.0}</a>
                            {else}
                                {_p var='advancedmarketplace_updated_on'} {$aListing.time_stamp|date:'advancedmarketplace.advancedmarketplace_view_time_stamp'} {_p var='in'} <a href="{$aListing.categories.0.1}">{$aListing.categories.0.0}</a>
                            {/if}
                        </span>
                    {else}
                        <span class="item-time">{_p var='advancedmarketplace_published_on'} {$aListing.time_stamp|date:'advancedmarketplace.advancedmarketplace_view_time_stamp'} {_p var='in'} <a href="{$aListing.categories.0.1}">{$aListing.categories.0.0}</a></span>
                    {/if}
                </div>
                <div class="p-detail-option-manage">
                    {if ($aListing.user_id == Phpfox::getUserId() && Phpfox::getUserParam('advancedmarketplace.can_edit_own_listing')) || Phpfox::getUserParam('advancedmarketplace.can_edit_other_listing')
                    || ($aListing.user_id == Phpfox::getUserId() && Phpfox::getUserParam('advancedmarketplace.can_delete_own_listing')) || Phpfox::getUserParam('advancedmarketplace.can_delete_other_listings')
                    || (Phpfox::getUserParam('advancedmarketplace.can_feature_listings'))
                    }
                    <div class="dropdown">
                        <a data-toggle="dropdown" class="p-option-button"><i class="ico ico-gear-o"></i></a>
                        <ul class="dropdown-menu dropdown-menu-right" id="js_blog_entry_options_{$aListing.listing_id}">
                            {template file='advancedmarketplace.block.menu'}
                        </ul>
                    </div>
                    {/if}
                </div>
            </div>
        </div>
        <div class="p-advmarketplace-detail-photo-block">
            <div class="ms-advmarketplace-detail-showcase dont-unbind advmarket-app">
                <div class="ms-vertical-template ms-tabs-vertical-template dont-unbind" id="advmarketplace_slider-detail">
                    {if !empty($aListing.images)}
                        {foreach from=$aListing.images item=listing_image}
                        <div class="ms-slide ms-skin-default dont-unbind">
                            <img data-src="{img return_url=true title=$aListing.title server_id=$listing_image.server_id path='advancedmarketplace.url_pic' file=$listing_image.image_path suffix='_400'}"/>
                            <div class="ms-thumb">
                                <span class="dont-unbind" style="background-image: url({img return_url=true title=$aListing.title server_id=$listing_image.server_id path='advancedmarketplace.url_pic' file=$listing_image.image_path suffix='_120'});" alt="thumb"></span>
                            </div>
                        </div>
                        {/foreach}
                    {else}
                        <div class="ms-slide ms-skin-default dont-unbind">
                            <img data-src="{img return_url=true path='core.path_actual' file='PF.Site/Apps/p-advmarketplace/assets/image/default/no-image-big.png'}">
                            <div class="ms-thumb">
                                <span class="dont-unbind" style="background-image: url({img return_url=true path='core.path_actual' file='PF.Site/Apps/p-advmarketplace/assets/image/default/no-image.png'});" alt="thumb"></span>
                            </div>
                        </div>
                    {/if}
                </div>
            </div>
        </div>
        <div class="p-advmarketplace-detail-sub-info-block">
            <div class="item-product-price">
                {if $aListing.price > 0}
                    <span class="p-text-warning fw-bold">{$sListingPrice}</span>
                {elseif $aListing.price == '0.00'}
                    <span class="p-text-success fw-bold">{_p var='advancedmarketplace.free'}</span>
                {/if}
            </div>
            {if $aListing.user_id != Phpfox::getUserId()}
                {if $aListing.is_sell && $aListing.view_id != '2' && $aListing.price != '0.00'}
                    <div class="item-product-action">
                        {if Phpfox::isUser()}
                        <a class="btn btn-primary btn-icon" onclick="tb_show('{_p('advancedmarketplace_purchase_review_and_confirmation_replacement')}', $.ajaxBox('advancedmarketplace.confirmPurchase', 'height=300&width=600&listing_id={$aListing.listing_id}')); return false;"><i class="ico ico-cart-o"></i>{phrase var='advancedmarketplace.buy_now'}</a>
                        {else}
                        <a class="btn btn-primary btn-icon" onclick="window.location.href='{url link='user.login'}'">{phrase var='advancedmarketplace.login_to_buy'}</a>
                        {/if}
                    </div>
                {/if}
            {/if}

            {if $aListing.view_id == 2 || !empty($aListing.is_expired)}
            <div class="mb-3 mt-2">
                {if !empty($aListing.is_expired)}
                <div class="p-advmarketplace-alert p-advmarketplace-alert-success">
                    {_p var='advancedmarketplace_this_item_has_been_expired'}
                </div>
                {else}
                    {if $aListing.is_purchased}
                    <div class="p-advmarketplace-alert p-advmarketplace-alert-success">
                        <div class=""><i class="ico ico-check-circle"></i> {_p var='advancedmarketplace_you_already_purchased_this_product'}</div>
                    </div>
                    {else}
                    <div class="p-advmarketplace-alert p-advmarketplace-alert-info">
                        <div>{_p var='advancedmarketplace_this_item_has_been_sold'}</div>
                    </div>
                    {/if}
                {/if}
            </div>
            {/if}

            <div class="item-product-info">
                {if !empty($aListing.location_parsed)}
                <div class="item-product-location">
                    <span class="fw-bold">{_p var='location'}:</span> {$aListing.location_parsed}
                </div>
                <div class="p-mt-line">
                    <a class="item-map-link" href="{$findAddressUrl}" target="_blank">{_p var='advancedmarketplace_view_on_maps'}</a>
                </div>
                {/if}

                {if !empty($aListing.short_description_parsed)}
                <div class="item-product-short-description">
                    {$aListing.short_description_parsed|highlight:'search'|parse|shorten:200:'feed.view_more':true|split:55|max_line}
                </div>
                {/if}
            </div>
        </div>
        <div class="p-detail-content-wrapper pt-2">
            <div class="p-collapse-content js_p_collapse_content item_view_content" data-max-height="120">
                <div class="p-text-uppercase p-text-gray mb-1">
                    {_p var ='advancedmarketplace_product_information'}
                </div>
                {if !empty($aListing.description)}
                <div>
                    {$aListing.description|highlight:'search'|parse}
                </div>
                {/if}
                {if count($aCustomFields) > 0}
                    {module name="advancedmarketplace.frontend.viewcustomfield" aCustomFields=$aCustomFields cfInfors=$cfInfors}
                {/if}
                <div class="p-detail-type-info">
                    {if isset($aListing.categories) && null != $aListing.categories && count($aListing.categories) > 0}
                        <div class="p-type-info-item">
                            <div class="p-category">
                                <span class="p-item-label">{_p var='advancedmarketplace.category'}:</span>
                                <div class="p-item-content">{$aListing.categories|category_display}</div>
                            </div>
                        </div>
                    {/if}
                    {if isset($aListing.tag_list)}
                    <div class="p-type-info-item">
                        {module name='tag.item' sType=$sTagType sTags=$aListing.tag_list iItemId=$aListing.listing_id iUserId=$aListing.user_id sMicroKeywords='keywords'}
                    </div>
                    {/if}
                </div>
            </div>
        </div>
    </div>
    <div class="p-detail-bottom-content">
        <div class="p-detail-addthis-wrapper">
            <div class="p-detail-addthis">
            {addthis url=$aItem.bookmark_url title=$aItem.title}
            </div>
            <div class="p-detail-minor-action">
                <a data-caption="HTML Code" title="HTML Code" class="p-btn-minor-action" onclick="$(this).closest('.p-detail-bottom-content').find('.advanced_blog_html_code_block').toggleClass('hide');">
                    <i class="ico ico-code"></i>{_p('advancedmarketplace_embed_code')}
                </a>
                {if ($aListing.user_id == Phpfox::getUserId() && Phpfox::getUserParam('advancedmarketplace.can_edit_own_listing')) || Phpfox::getUserParam('advancedmarketplace.can_edit_other_listing')}
                <a href="{url link='advancedmarketplace.add.invite' id=$aListing.listing_id tab='invite'}" class="p-btn-minor-action"><i class="ico ico-user3-two2"></i>{_p var='invite_friends'}</a>
                {/if}
            </div>
        </div>
        <div class="advanced_blog_html_code_block hide mb-2">
            <textarea id="ynadvblog_html_code_value" readonly class="form-control disabled"><iframe width="500" height="550" src="{$sUrl}"></iframe></textarea>
        </div>
        <div class="item-detail-feedcomment p-detail-comment">
            {plugin call='advancedmarketplace.template_default_controller_detail_extra_info'}

            <div {if $aListing.view_id != 0}style="display:none;" class="js_moderation_on"{/if}>
                <div class="item-detail-feedcomment">{module name='feed.comment'}</div>
            </div>
        </div>
    </div>
    <div class="p-advancedmarketplace-rating mt-2" id="js_rating_section">
        {module name='advancedmarketplace.rating'}
    </div>
</div>

{literal}
<script language="javascript" type="text/javascript">
	var iCheck = 0;

	$Behavior.advancedmarketplaceRating = function(){
		$(".yn_reviewrating").children("button").click(function(evt){
			evt.preventDefault();
			var page = ($("#xf_page").size() > 0)?$("#xf_page").val():0;
			tb_show("{/literal}{phrase var="advancedmarketplace.rate_this_product" phpfox_squote=true}{literal}", $.ajaxBox('advancedmarketplace.ratePopup', 'height=300&page=' + page + '&width=550&id={/literal}{$aListing.listing_id}{literal}'));
			return false;
		});
		$('.js_total_review', $('#js_advmarketplace_detail_container')).on('click', function(){
            $('html, body').animate({
                scrollTop: ($('#js_rating_section', $('#js_advmarketplace_detail_container')).offset().top - 85)
            }, 1000);
        });
	};
	$Behavior.advancedmarketplaceViewDetail = function(){
		var fadeTTime = 100;
		$("#yn_show_yn_listingcontent").click(function(evt){
			evt.preventDefault();
			$("#yn_listingcontent").stop(false, false).fadeIn(fadeTTime, function(){
				$("#yn_listingrating").fadeOut(fadeTTime);
			});
			$("#yn_tab").find(".active").removeClass("active");
			$(this).parent().addClass("active");
			return false;
		});
		$("#yn_show_yn_listingrating").click(function(evt){
			evt.preventDefault();
			$("#yn_listingrating").stop(false, false).fadeIn(fadeTTime, function(){
				$("#yn_listingcontent").fadeOut(fadeTTime);
			});
			$("#yn_tab").find(".active").removeClass("active");
			$(this).parent().addClass("active");
			return false;
		});

		if(iCheck == 0)
		{
			$("#yn_listingrating").hide();
			iCheck++;
		}
		$(".remove_otheraction").unbind();
	};

</script>
{/literal}