<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/14/16
 * Time: 17:03
 */
?>

<div class="ynstore-store-most-block">
	<div class="ynstore-store-most-items">
	{foreach from=$aItems name=store item=aItem}
		<div class="ynstore-store-most-item">
			<div class="ynstore-store-cover"
				 {if $aItem.cover_path}
				 style="background-image: url({img server_id=$aItem.cover_server_id path='core.url_pic' file='ynsocialstore/'.$aItem.cover_path suffix='_480' return_url=true})"
				 {else}
				 style="background-image:url({param var='core.path'}module/ynsocialstore/static/image/store_cover_default.jpg)"
				 data-bg="no"
				 {/if}
			>
				<a href="{permalink module='ynsocialstore.store' id=$aItem.store_id title=$aItem.name}" class="ynstore-store-gradient"></a>
			</div>

			<div class="ynstore-store-info">
				<a href="{permalink module='ynsocialstore.store' id=$aItem.store_id title=$aItem.name}" class="ynstore-store-avatar profile_image">
					{if $aItem.logo_path}
					{img server_id=$aItem.server_id path='core.url_pic' file='ynsocialstore/'.$aItem.logo_path suffix='_480_square'}
					{else}
					<img src="{param var='core.path'}module/ynsocialstore/static/image/store_default.png" alt="{_p('Store Logo')}">
					{/if}
				</a>

				<div class="ynstore-store-info-txt">
					<div class="ynstore-store-title">
						<a href="{permalink module='ynsocialstore.store' id=$aItem.store_id title=$aItem.name}">{$aItem.name}</a>
					</div>
                    {if !empty($aItem.address)}
					<div class="ynstore-store-address" title="{$aItem.address}">
						{_p var='ynsocialstore.at'} {$aItem.address}
					</div>
                    {/if}
					<div class="ynstore-store-count">
						{$aItem.total_follow} {if $aItem.total_follow == 1}{_p var='ynsocialstore.follower'} {else} {_p var='ynsocialstore.followers'}{/if}
					</div>
				</div>
			</div>
			<div title="{_p var='ynsocialstore.add_to_compare'}" class="ynstore-compare-btn" onclick="ynsocialstore.addToCompare({$aItem.store_id},'store');return false;" data-comparestoreid="{$aItem.store_id}">
				<i class="ico ico-copy"></i>
			</div>
		</div>
	{/foreach}
	</div>
</div>
