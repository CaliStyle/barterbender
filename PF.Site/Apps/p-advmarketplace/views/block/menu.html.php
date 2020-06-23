<?php
	defined('PHPFOX') or exit('NO DICE!');
?>
	{if $aListing.canEdit}
		<li><a href="{url link='advancedmarketplace.add' id=$aListing.listing_id}" title="{phrase var='advancedmarketplace.edit_listing'}"><i class="ico ico-pencilline-o mr-1"></i>{phrase var='advancedmarketplace.edit_listing'}</a></li>
		<li role="separator" class="divider"></li>
		<li><a href="{url link='advancedmarketplace.add.customize' id=$aListing.listing_id tab='customize'}" title="{phrase var='advancedmarketplace.manage_photos'}"><i class="ico ico-photo-o mr-1"></i>{phrase var='advancedmarketplace.manage_photos'}</a></li>
		<li><a href="{url link='advancedmarketplace.add.invite' id=$aListing.listing_id tab='invite'}" title="{phrase var='advancedmarketplace.send_invitations'}"><i class="ico ico-envelope mr-1"></i>{phrase var='advancedmarketplace.send_invitations'}</a></li>
	{/if}

    {if $aListing.canApprove}
        <li>
            <a href="javascript:void(0);" onclick="$.ajaxCall('advancedmarketplace.approve','listing_id={$aListing.listing_id}&no_reload=1&no_delete={if !empty($sView) && $sView == 'pending'}0{else}1{/if}'); return false;" id="js_approved_{$aListing.listing_id}">
                <span class="ico ico-check-square-alt mr-1"></span>{_p var='approve'}
            </a>
        </li>
    {/if}

	{if $aListing.canFeature}
		<li class="js_advancedmarketplace_is_feature" {if $aListing.is_featured} style="display:none;"{/if}><a href="#" onclick="$('#js_featured_phrase_{$aListing.listing_id}').show(); $.ajaxCall('advancedmarketplace.feature', 'listing_id={$aListing.listing_id}&amp;type=1', 'GET'); $(this).parent().hide(); $(this).parents('ul:first').find('.js_advancedmarketplace_is_un_feature').show(); return false;"><i class="ico ico-diamond-o mr-1"></i>{phrase var='advancedmarketplace.feature'}</a></li>
		<li class="js_advancedmarketplace_is_un_feature" {if !$aListing.is_featured} style="display:none;"{/if}><a href="#" onclick="$('#js_featured_phrase_{$aListing.listing_id}').hide(); $.ajaxCall('advancedmarketplace.feature', 'listing_id={$aListing.listing_id}&amp;type=0', 'GET'); $(this).parent().hide(); $(this).parents('ul:first').find('.js_advancedmarketplace_is_feature').show(); return false;"><i class="ico ico-diamond-o mr-1"></i>{phrase var='advancedmarketplace.un_feature'}</a></li>
	{/if}

	{if $aListing.canSponsorAll}
	<li>
        {if $aListing.is_sponsor}
            <a href="javascript:void(0);" onclick="$('#js_sponsor_phrase_{$aListing.listing_id}').hide(); $.ajaxCall('advancedmarketplace.sponsor','listing_id={$aListing.listing_id}&type=0', 'GET'); return false;"><i class="ico ico-sponsor mr-1"></i>{phrase var='advancedmarketplace_un_sponsor'}</a>
        {else}
            <a href="javascript:void(0);" onclick="$('#js_sponsor_phrase_{$aListing.listing_id}').show(); $.ajaxCall('advancedmarketplace.sponsor','listing_id={$aListing.listing_id}&type=1', 'GET'); return false;"><i class="ico ico-sponsor mr-1"></i>{phrase var='advancedmarketplace.sponsor_this_listing'}</a>
        {/if}
	</li>
	{elseif $aListing.canSponsorOwn}
	<li>
        {if $aListing.is_sponsor}
        <a href="javascript:void(0);" onclick="$('#js_sponsor_phrase_{$aListing.listing_id}').hide(); $.ajaxCall('advancedmarketplace.sponsor','listing_id={$aListing.listing_id}&type=0', 'GET'); return false;"><i class="ico ico-sponsor mr-1"></i>{phrase var='advancedmarketplace_un_sponsor'}</a>
        {else}
        <a href="{permalink module='ad.sponsor' id=$aListing.listing_id}section_advancedmarketplace/"><i class="ico ico-sponsor mr-1"></i>{phrase var='advancedmarketplace.sponsor_this_listing'}</a>
        {/if}
	</li>
	{/if}

    {if $aListing.canSponsorInFeed}
    <li>
        {if $aListing.sponsorInFeedId}
        <a id="sp_in_feed" title="{_p var='sponsor_in_feed'}" href="{url link='ad.sponsor' where='feed' section='advancedmarketplace' item=$aListing.listing_id}">
            <i class="ico ico-sponsor mr-1"></i>{_p var='sponsor_in_feed'}
        </a>
        {else}
        <a id="unsp_in_feed" title="{_p var='unsponsor_in_feed'}" role="button" onclick="$.ajaxCall('ad.removeSponsor', 'type_id=advancedmarketplace&item_id={$aListing.listing_id}', 'GET'); return false;">
            <i class="ico ico-sponsor mr-1"></i>{_p var="unsponsor_in_feed"}
        </a>
        {/if}
    </li>
    {/if}

	{if $aListing.canDelete}
		<li class="item_delete"><a href="{url link='advancedmarketplace' delete=$aListing.listing_id}" class="sJsConfirm"><i class="ico ico-trash-o mr-1"></i>{phrase var='advancedmarketplace.delete_listing'}</a></li>
	{/if}