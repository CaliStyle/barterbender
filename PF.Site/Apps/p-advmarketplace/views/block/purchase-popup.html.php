<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if empty($warning)}
<div class="p-advancedmarketplace-payment_gateway-content" id="js_payment_gateway_content">
    {if !PHPFOX_IS_AJAX}
        {template file='error.controller.display'}
    {/if}
    {plugin call='api.template_block_gateway_form_start'}
    {if count($aGateways)}
        {foreach from=$aGateways name=gateways item=aGateway}
        <form id="js_advancedmarketplace_payment_gateway_{$aGateway.gateway_id}" class="form" method="post" action="{$aGateway.form.url}"{if $aGateway.gateway_id == 'activitypoints'} onsubmit="$(this).ajaxCall('api.processActivityPayment'); return false;"{/if} {if $bIsThickBox}style="max-height: 400px; overflow: auto;"{/if}>
            {foreach from=$aGateway.form.param key=sField item=sValue}
            <div><input type="hidden" name="{$sField}" value='{$sValue}'/></div>
            {/foreach}
        </form>
        {/foreach}
    {/if}
    {plugin call='api.template_block_gateway_form_end'}
</div>
<div class="p-advmarketplace-purchase-popup-container" id="js_advancedmarketplace_purchase_popup">
	<div class="p-advmarketplace-purchase-popup-outer">
		<div class="item-purchase-info">
			<div class="item-image">
				<span style="background-image: url(
					{if $aListing.image_path}
						{img server_id=$aListing.server_id title=$aListing.title path='advancedmarketplace.url_pic' file=$aListing.image_path suffix='_200' return_url=true}
					{else}
						{img path='core.path_actual' file='PF.Site/Apps/p-advmarketplace/assets/image/default/no-image.png' return_url=true}
					{/if}
					);" alt="thumb"></span>
			</div>
			<div class="item-inner">
				<div class="item-title">
					<a href="{url link='advancedmarketplace.detail.'.$aListing.listing_id.'.'.$aListing.title}">{$aListing.title}</a>
				</div>
				<div class="item-user">
                    <span>{_p var='by'}</span> {$aListing|user:'':'':50:'':'author'}
				</div>
				<div class="item-price">
					<span class="p-text-warning">{$listingPrice}</span>
				</div>
			</div>	
		</div>
		<div class="item-payment-method">
            {if !empty($availablePaymentGateways)}
			<div class="item-title">
				{_p var='advancedmarketplace_available_payment_gateways'}
			</div>
			<div class="item-method-listing">
                {foreach from=$availablePaymentGateways item=availablePaymentGateway}
				<div class="item-method js_advancedmarketplace_select_payment_gateway {if isset($availablePaymentGateway.is_default)}active{/if} {if isset($availablePaymentGateway.disabled)}disable{/if} {if isset($availablePaymentGateway.hide)}hide{/if} {if !$enoughActivityPoint && $availablePaymentGateway.gateway_id == 'activitypoints'}has-alert{/if}" data-id="{$availablePaymentGateway.gateway_id}">
					<div class="item-method-outer">
						{$availablePaymentGateway.title}
					</div>
				</div>
                {/foreach}
			</div>
			<div class="item-method-alert">
                {if !$enoughActivityPoint}
				<div class="item-alert-no-point">
					{_p var='advancedmarketplace_you_do_not_have_enough_activity_points_to_purchase_this_listing'}
				</div>
                {/if}
                {if $acceptOnlyPaypal}
				<div class="p-advmarketplace-alert p-advmarketplace-alert-info">
                    <div class="">{_p var='advancedmarketplace_this_listing_only_accept_payment_via_paypal'}</div>
                </div>
                {/if}
			</div>
            {else}
                <p class="help-block">
                    {_p var='opps_no_payment_gateways_have_been_set_up_yet'}
                </p>
            {/if}
		</div>
		<div class="p-advmarketplace-action-wrapper-popup">
			<button class="btn btn-default btn-sm" onclick="tb_remove(); return false;">{_p var='cancel}</button>
            {if count($aGateways)}
			<button class="btn btn-primary btn-sm" onclick="appAdvMarketplace.processPurchasement(this);" data-detail-payment="1" data-invoice="{$invoiceId}">{_p var='advancedmarketplace_proceed_to_checkout'}</button>
            {/if}
		</div>
	</div>
</div>
{/if}
