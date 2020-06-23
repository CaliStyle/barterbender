<?php 
/**
 * [PHPFOX_HEADER]
 */
 
defined('PHPFOX') or exit('NO DICE!'); 
?>
{if $aItem.canDoAction || (isset($is_manage) && $is_manage) || (isset($sView) && ($sView == 'favorite' || $sView == 'follow'))}
	<div class="dropdown">
    <a role="button" data-toggle="dropdown" class="btn">
		<i class="ico ico-pencil"></i>
	</a>
    <ul class="dropdown-menu dropdown-menu-right">
		{if $aItem.canApprove}
		<li class="item ynstore-close-open-store-{$aItem.store_id}">
			<a href="javascript:void(0)" onclick="ynsocialstore.approveStore({$aItem.store_id},'{$aItem.status}'); return false;"><i class="fa fa-eye"></i> {_p var='ynsocialstore.approve'} </a>
		</li>
		{/if}
		{if $aItem.canDeny}
		<li class="item ynstore-close-open-store-{$aItem.store_id}">
			<a href="javascript:void(0)" onclick="ynsocialstore.denyStore({$aItem.store_id},'{$aItem.status}'); return false;"><i class="fa fa-eye-slash"></i> {_p var='ynsocialstore.deny'} </a>
		</li>
		{/if}
		{if $aItem.canPublish}
		<li class="item ynstore-close-open-store-{$aItem.store_id}">
			<a href="{url link='ynsocialstore.store.manage-packages.id_'.$aItem.store_id}" ><i class="fa fa-globe"></i> {_p var='ynsocialstore.publish'} </a>
		</li>
		{/if}
		{if $aItem.module_id === 'ynsocialstore'}
			{if $aItem.canFeature == 1}
			<li class="item">
				<a href="javascript:void(0)" onclick="ynsocialstore.featureStoreInBox(this, {$aItem.store_id}); return false;"><i class="fa fa-diamond"></i> {_p var='ynsocialstore.featured'} </a>
			</li>
			{elseif $aItem.canFeature == 2}
			{if !$aItem.is_featured}
				<li class="item ynstore-feature-store-{$aItem.store_id}">
					<a href="javascript:void(0)" onclick="ynsocialstore.updateFeatureStore(this, {$aItem.store_id},0); return false;"><i class="ico ico-diamond-o"></i> {_p var='ynsocialstore.featured'} </a>
				</li>
			{else}
				<li class="item ynstore-feature-store-{$aItem.store_id}">
					<a href="javascript:void(0)" onclick="ynsocialstore.updateFeatureStore(this, {$aItem.store_id},1); return false;"><i class="ico ico-diamond-o"></i> {_p var='ynsocialstore.un_featured'} </a>
				</li>
			{/if}
			{/if}
		{/if}
		{if $aItem.canEdit}
	    <li class="item">
	        <a href="{url link='ynsocialstore.store.insight.id_'.$aItem.store_id}"><i class="fa fa-cog"></i> {_p var='ynsocialstore.dashboard'} </a>
	    </li>
		{/if}
		{if isset($aItem.canCreateProduct) && $aItem.canCreateProduct == 2}
		<li class="item">
			<a href="{url link='ynsocialstore.add' store=$aItem.store_id}"><i class="fa fa-plus"></i> {_p var='ynsocialstore.add_product'} </a>
		</li>
		{/if}
	    {if $aItem.canClose}
	    <li class="item ynstore-close-open-store-{$aItem.store_id}">
	        <a href="javascript:void(0)" onclick="ynsocialstore.closeStore({$aItem.store_id},{$aItem.user_id},'{$aItem.status}'); return false;">
	        <i class="ico ico-close-circle"></i> {_p var='ynsocialstore.close'} </a>
	    </li>
	    {/if}
	    {if $aItem.canReopen}
	    <li class="item ynstore-close-open-store-{$aItem.store_id}">
	        <a href="javascript:void(0)" onclick="ynsocialstore.openStore({$aItem.store_id},{$aItem.user_id},'{$aItem.status}'); return false;"><i class="fa fa-check"></i> {_p var='ynsocialstore.open'} </a>
	    </li>
	    {/if}
		{if $aItem.canDelete}
		<li class="item_delete">
			<a href="javascript:void(0)" onclick="ynsocialstore.confirmDeleteStore({$aItem.store_id},{$aItem.user_id},{if isset($bIsDetail)}1{else}0{/if});"><i class="fa fa-trash"></i> {_p var='ynsocialstore.delete'} </a>
		</li>
		{/if}
    </ul>
</div>
{/if}