<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 11/17/16
 * Time: 4:09 PM
 */
?>
<div class="ynstore-hot-sellers yn-viewmode-grid">
    <input type="hidden" id="ynsocialstore_corepath" value="{param var='core.path_file'}">
    <ul class="ynstore-items ynstore-store-listing-block ynstore-hot-seller-slideshow">
        {foreach from=$aItems name=store item=aItem}
            <li class="ynstore-item item" data-store-id="{$aItem.store_id}" id="js_store_id_{$aItem.store_id}">
            	<div class="ynstore-item-content ynstore-store-listing" >
            		<div class="ynstore-bg"
            			style="background-image: url(
            				{if $aItem.logo_path}
            			        {img server_id=$aItem.server_id path='core.url_pic' file='ynsocialstore/'.$aItem.logo_path suffix='_480_square' return_url='true'}
            			    {else}
            			    	{param var='core.path_actual'}PF.Base/module/ynsocialstore/static/image/store_default.png
            			    {/if}
            		    )">

            		    <div class="ynstore-featured">
            		    	<div  title="{_p var='ynsocialstore.featured'}" class="ynstore-featured-triangle ynstore_entry_feature_icon-{$aItem.store_id}" {if !$aItem.is_featured}style="visibility:hidden"{/if}>
            		    		<i class="ico ico-diamond"></i>
            		    	</div>
            		    </div>

            		    <a  href="{permalink module='ynsocialstore.store' id=$aItem.store_id title=$aItem.name}"></a>
            		    <div title="{_p var='ynsocialstore.add_to_compare'}" class="ynstore-compare-btn {if isset($bIsNoCompare) && $bIsNoCompare}hide{/if}" onclick="ynsocialstore.addToCompare({$aItem.store_id},'store');return false;" data-comparestoreid="{$aItem.store_id}">
            		    	<i class="ico ico-copy"></i>
            		    </div>
            			{if isset($aItem.user_id)}
            			<div class="ynstore-actions-block">
            				<div class="ynstore-cms">
            					{template file='ynsocialstore.block.store.link' aItem=$aItem}
            				</div>
            			</div>
            			{/if}
            		</div>

            		<div class="ynstore-info">
            			<div class="ynstore-info-detail">
            				<a href="{permalink module='ynsocialstore.store' id=$aItem.store_id title=$aItem.name}" class="ynstore-title">
            					{$aItem.name|clean}
            				</a>
            				{if !empty($aItem.address)}
            				<div title="{$aItem.address}" class="ynstore-address">
            					<i class="ico ico-checkin"></i>
            					{$aItem.address}
            				</div>
            				{/if}

            				<div class="ynstore-categories">
            					<div class="ynstore-categories-content">
            						<i class="ico ico-folder-alt"></i>
            						{if $aItem.hiddencate > 0}
                                    {if Phpfox::isPhrase($this->_aVars['aItem']['categories'][0]['title'])}
                                        <?php $this->_aVars['value_name'] = _p($this->_aVars['aItem']['categories'][0]['title']) ?>
                                    {else}
                                        {assign var='value_name' value=$aItem.categories.0.title|convert}
                                    {/if}
            						<a href="{permalink module='ynsocialstore.store.category' id=$aItem.categories.0.category_id title=$value_name}">{$value_name}</a>
            						<div class="dropdown">
            							{_p('and')}
            							<a href="javascript:void(0)" data-toggle="dropdown">+{$aItem.hiddencate}</a>
            							<ul class="dropdown-menu">
            								{foreach from=$aItem.categories key=iKey item=aCategory}
            								{if $iKey > 0}
                                                {if Phpfox::isPhrase($this->_aVars['aCategory']['title'])}
                                                    <?php $this->_aVars['value_name'] = _p($this->_aVars['aCategory']['title']) ?>
                                                {else}
                                                    {assign var='value_name' value=$aCategory.title|convert}
                                                {/if}
            									<li><a href="{permalink module='ynsocialstore.store.category' id=$aCategory.category_id title=$value_name}">{$value_name}</a></li>
            								{/if}
            								{/foreach}
            							</ul>
            						</div>
            						{else}
            							{foreach from=$aItem.categories key=iKey item=aCategory}
                                            {if Phpfox::isPhrase($this->_aVars['aCategory']['title'])}
                                                <?php $this->_aVars['value_name'] = _p($this->_aVars['aCategory']['title']) ?>
                                            {else}
                                                {assign var='value_name' value=$aCategory.title|convert}
                                            {/if}
            								<a href="{permalink module='ynsocialstore.store.category' id=$aCategory.category_id title=$value_name}">{$value_name}</a>{if $iKey == 0 && count($aItem.categories) > 1}<i>,</i>{/if}
            							{/foreach}
            						{/if}
            					</div>
            				</div>
            			</div>

            			<div class="ynstore-statistic-block">
            				<span class="ynstore-statistic ynstore-orders">
            					{if $aItem.total_orders == 1}
            						<b>{$aItem.total_orders}</b> <i>{_p('order')}</i>
            					{else}
            						<b>{$aItem.total_orders}</b> <i>{_p('orders')}</i>
            					{/if}
            				</span>
            				<span class="ynstore-statistic ynstore-pipe"></span>
            				<span class="ynstore-statistic ynstore-follows">
            					{if $aItem.total_follow == 1}
            						<b>{$aItem.total_follow}</b> <i>{_p('follower')}</i>
            					{else}
            						<b>{$aItem.total_follow}</b> <i>{_p('followers')}</i>
            					{/if}
            				</span>
            			</div>
            		</div>
            	</div>
            </li>
        {/foreach}
    </ul>
</div>
