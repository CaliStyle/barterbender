<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/18/16
 * Time: 6:13 PM
 */
?>
<div class="ynstore-featured-block">
    <input type="hidden" id="ynsocialstore_corepath" value="{param var='core.path_file'}">
    <ul class="ynstore-featured-items owl-carousel" id="ynstore-featured-block" style="display: none">
    {foreach from=$aItems name=store item=aItem}
    <li class="item ynstore-featured-item">
        <a href="{permalink module='ynsocialstore.store' id=$aItem.store_id title=$aItem.name}"
            class="ynstore-store-cover"
            style="background-image: url({if $aItem.cover_path}{img server_id=$aItem.cover_server_id path='core.url_pic' file='ynsocialstore/'.$aItem.cover_path suffix='_1024' return_url='true'}{else}{param var='core.path'}module/ynsocialstore/static/image/store_cover_default.jpg{/if});">
        </a>

        <div class="ynstore-store-info clearfix">
            <a href="{permalink module='ynsocialstore.store' id=$aItem.store_id title=$aItem.name}" class="ynstore-store-avatar profile_image">
                {if $aItem.logo_path}
                    <img src="{img server_id=$aItem.server_id path='core.url_pic' file='ynsocialstore/'.$aItem.logo_path suffix='_90' return_url='true'}" alt="{$aItem.name}">
                {else}
                    <img src="{param var='core.path'}module/ynsocialstore/static/image/store_default.png" alt="{$aItem.name}">
                {/if}
            </a>
            <div class="ynstore-store-info-txt">

                <div class="ynstore-store-title">
                    <a href="{permalink module='ynsocialstore.store' id=$aItem.store_id title=$aItem.name}">{$aItem.name}</a>
                </div>
                {if isset($aItem.address) && !empty($aItem.address)}
                <div class="ynstore-store-address" title="{$aItem.address}">
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
                            &nbsp;{_p('and')}
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
                            <a href="{permalink module='ynsocialstore.store.category' id=$aCategory.category_id title=$value_name}">{$value_name}</a>{if $iKey == 0 && count($aItem.categories) > 1}<i>,&nbsp;</i>{/if}
                        {/foreach}
                        {/if}
                    </div>
                </div>

                <div class="ynstore-store-count">
                    <span>
                        {$aItem.total_orders} {if $aItem.total_orders == 1}{_p var='ynsocialstore.order'} {else} {_p('orders')}{/if}
                    </span>
                    <span>
                        {$aItem.total_follow} {if $aItem.total_follow == 1}{_p var='ynsocialstore.follower'} {else} {_p var='ynsocialstore.followers'}{/if}
                    </span>
                    <span class="ynstore-rating yn-rating yn-rating-normal">
                        {for $i = 0; $i < 5; $i++}
						{if $i < (int)$aItem.rating}
							<i class="ico ico-star" aria-hidden="true"></i>
						{elseif (($aItem.rating - round($aItem.rating)) > 0) && ($aItem.rating - $i) > 0}
							<i class="ico ico-star-half-o" aria-hidden="true"></i>
						{else}
							<i class="ico ico-star yn-rating-disable" aria-hidden="true"></i>
						{/if}
					{/for}
                    </span>
                </div>
            </div>
        </div>
    </li>
    {/foreach}
    </ul>
</div>
