<?php 
/**
 * [PHPFOX_HEADER]
 */
 
defined('PHPFOX') or exit('NO DICE!'); 
?>
{if isset($aItem.user_id )}
	{if $aItem.canDoAction || (isset($is_manage) && $is_manage) || (isset($sView) && ($sView == 'favorite' || $sView == 'follow'))}
		<div class="dropdown">
		<a role="button" data-toggle="dropdown" class="btn">
			<i class="ico ico-compose"></i>
		</a>
		<ul class="dropdown-menu dropdown-menu-right">
			{if $aItem.canApprove}
			<li class="item ynstore-close-open-product-{$aItem.product_id}">
				<a href="javascript:void(0)" onclick="ynsocialstore.approveProduct({$aItem.product_id},'{$aItem.product_status}'); return false;"><i class="fa fa-eye"></i> {_p var='ynsocialstore.approve'} </a>
			</li>
			{/if}

			{if $aItem.canDeny}
			<li class="item ynstore-close-open-product-{$aItem.product_id}">
				<a href="javascript:void(0)" onclick="ynsocialstore.denyProduct({$aItem.product_id},'{$aItem.product_status}'); return false;"><i class="fa fa-eye-slash"></i> {_p var='ynsocialstore.deny'} </a>
			</li>
			{/if}

			{if $aItem.canPublish}
			<li class="item ynstore-close-open-product-{$aItem.product_id}">
				<a href="{url link='ynsocialstore.add.id_'.$aItem.product_id}publish" ><i class="fa fa-globe"></i> {_p var='ynsocialstore.publish'} </a>
			</li>
			{/if}

			{if $aItem.canFeature == 1}
			<li class="item">
				<a href="javascript:void(0)" onclick="ynsocialstore.featureProductInBox(this, {$aItem.product_id}); return false;"><i class="fa fa-diamond"></i> {_p var='ynsocialstore.featured'} </a>
			</li>
			{elseif $aItem.canFeature == 2}
				{if !$aItem.is_featured}
					<li class="item ynstore-feature-product-{$aItem.product_id}">
						<a href="javascript:void(0)" onclick="ynsocialstore.updateFeatureProduct(this, {$aItem.product_id},0); return false;"><i class="fa fa-diamond"></i> {_p var='ynsocialstore.featured'} </a>
					</li>
				{else}
					<li class="item ynstore-feature-product-{$aItem.product_id}">
						<a href="javascript:void(0)" onclick="ynsocialstore.updateFeatureProduct(this, {$aItem.product_id},1); return false;"><i class="fa fa-diamond"></i> {_p var='ynsocialstore.un_featured'} </a>
					</li>
				{/if}
			{/if}

			{if $aItem.canEdit}
			<li class="item">
				<a href="{url link='ynsocialstore.add.id_'.$aItem.product_id}"><i class="fa fa-cog"></i> {_p var='ynsocialstore.dashboard'} </a>
			</li>
			{/if}

			{if $aItem.canClose}
			<li class="item ynstore-close-open-product-{$aItem.product_id}">
				<a href="javascript:void(0)" onclick="ynsocialstore.closeProduct({$aItem.product_id},{$aItem.user_id},'{$aItem.product_status}'); return false;"><i class="fa fa-times"></i> {_p var='ynsocialstore.close'} </a>
			</li>
			{/if}

			{if $aItem.canReopen}
			<li class="item ynstore-close-open-product-{$aItem.product_id}">
				<a href="javascript:void(0)" onclick="ynsocialstore.openProduct({$aItem.product_id},{$aItem.user_id},'{$aItem.product_status}'); return false;"><i class="fa fa-check"></i> {_p var='ynsocialstore.open'} </a>
			</li>
			{/if}

			{if isset($aItem.canCreateProduct) && $aItem.canCreateProduct == 2}
			<li class="item">
				<a href="{url link='ynsocialstore.add' product=$aItem.product_id}"><i class="fa fa-plus"></i> {_p var='ynsocialstore.add_product'} </a>
			</li>
			{/if}

			{if $aItem.canDelete}
			<li class="item_delete">
				<a href="javascript:void(0)" onclick="ynsocialstore.confirmDeleteProduct({$aItem.product_id},{$aItem.user_id},{if isset($bIsDetail)}1{else}0{/if});"><i class="fa fa-trash"></i> {_p var='ynsocialstore.delete'} </a>
			</li>
			{/if}
		</ul>
	</div>
	{/if}
{/if}