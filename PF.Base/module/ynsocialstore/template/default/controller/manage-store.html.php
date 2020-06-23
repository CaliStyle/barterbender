<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/20/16
 * Time: 11:05 AM
 */
?>
{if count($aItems) > 0}
    {if !PHPFOX_IS_AJAX}
<div class="ynstore-my-store-page" id="js_block_border_ynsocialstore_store_managestore">
    <ul class="ynstore-items">
        {/if}
        {foreach from=$aItems name=store item=aItem}
        <li class="ynstore-item">
            <div class="ynstore-item-content">
                <div class="ynstore-actions-block ynstore-putleft">
                    {if !empty($bShowModeration)}
                    <div class="moderation_row">
                        <label class="item-checkbox">
                            <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aItem.store_id}" id="check{$aItem.store_id}" />
                            <i class="ico ico-square-o"></i>
                        </label>
                    </div>
                    {/if}
                    <div class="ynstore-cms">
                        {template file='ynsocialstore.block.store.link' aItem=$aItem is_manage=$is_manage}
                    </div>
                </div>
                <div class="ynstore-bg"
                     style="background-image: url(
                        {if $aItem.logo_path}
                            {img server_id=$aItem.server_id path='core.url_pic' file='ynsocialstore/'.$aItem.logo_path suffix='_480_square' return_url='true'}
                        {else}
                            {$sCorePath}module/ynsocialstore/static/image/store_default.png
                        {/if}
                     )">
    
                    <div id="ynstore_status_{$aItem.store_id}" class="ynstore-status-block">
                        <div class="ynstore-status ynstatus_{$aItem.status}">
                            {_p var='ynsocialstore.'.$aItem.status}
                        </div>
                    </div>
                </div>



                <div class="ynstore-featured">
                    <div title="{_p('Featured')}" class="ynstore-featured-triangle ynstore_entry_feature_icon-{$aItem.store_id}" {if !$aItem.is_featured}style="visibility:hidden"{/if}>
                    <i class="ico ico-diamond"></i>
                    </div>
                </div>
                
                <div class="ynstore-info">
                    <a href="{permalink module='ynsocialstore.store' id=$aItem.store_id title=$aItem.name}" class="ynstore-title">
                        {$aItem.name}
                    </a>
                    <div class="ynstore-info-detail">
                        <div class="ynstore-date">
                            {_p var='ynsocialstore.created_on'} {$aItem.time_stamp|date:'core.global_update_time'}
                        </div>

                        <div class="ynstore-categories">
                            <div class="ynstore-categories-content">
                                {_p var='category'}:
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
                    
                    <div class="ynstore-package-product">
                        <span>
                            <label>{_p var="ynsocialstore.package"}</label>
                            {$aItem.package_name}
                        </span>

                        <span>
                            <label>{_p var="ynsocialstore.products"}</label>
                            {$aItem.total_products}
                        </span>
                    </div>
                </div>

                <div class="ynstore-statistic-block">
                    <span class="ynstore-statistic ynstore-follows">
                        {if $aItem.total_follow == 1}
                            <b>{$aItem.total_follow}</b> {_p('follower')}
                        {else}
                            <b>{$aItem.total_follow}</b> {_p('followers')}
                        {/if}
                    </span>
                    
                    <span class="ynstore-statistic ynstore-orders">
                        {if $aItem.total_orders == 1}
                            <b>{$aItem.total_orders}</b> {_p('order')}
                        {else}
                            <b>{$aItem.total_orders}</b> {_p('orders')}
                        {/if}
                    </span>
                    
                    <span class="ynstore-statistic ynstore-favorites ynstore-flag">
                        {if $aItem.total_favorite == 1}
                            <b>{$aItem.total_favorite}</b> {_p('favorite')}
                        {else}
                            <b>{$aItem.total_favorite}</b> {_p('favorites')}
                        {/if}
                    </span>
                    
                    <span class="ynstore-statistic ynstore-views ynstore-flag">
                        {if $aItem.total_view == 1}
                            <b>{$aItem.total_view}</b> {_p('view')}
                        {else}
                            <b>{$aItem.total_view}</b> {_p('views')}
                        {/if}
                    </span>

                    <div class="ynstore-statistic-dropdown dropdown">
                        <span class="ynstore-statistic-dropdown-btn" data-toggle="dropdown"></span>
                        <div class="dropdown-menu">
                            <span class="ynstore-statistic ynstore-favorites ynstore-flag">
                                {if $aItem.total_favorite == 1}
                                    <b>{$aItem.total_favorite}</b> {_p('favorite')}
                                {else}
                                    <b>{$aItem.total_favorite}</b> {_p('favorites')}
                                {/if}
                            </span>
                            
                            <span class="ynstore-statistic ynstore-views ynstore-flag">
                                {if $aItem.total_view == 1}
                                    <b>{$aItem.total_view}</b> {_p('view')}
                                {else}
                                    <b>{$aItem.total_view}</b> {_p('views')}
                                {/if}
                            </span>
                            
                            <span class="ynstore-statistic ynstore-shares">
                                {if $aItem.total_share == 1}
                                    <b>{$aItem.total_share}</b> {_p('share')}
                                {else}
                                    <b>{$aItem.total_share}</b> {_p('shares')}
                                {/if}
                            </span>                    

                            <span class="ynstore-statistic ynstore-likes">
                                {if $aItem.total_like == 1}
                                    <b>{$aItem.total_like}</b> {_p('like')}
                                {else}
                                    <b>{$aItem.total_like}</b> {_p('likes')}
                                {/if}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </li>
        {/foreach}
        {pager}
        {if !PHPFOX_IS_AJAX}
        {if $bShowModeration}{moderation}{/if}
    </ul>
</div>
{/if}
{else}
    {if !PHPFOX_IS_AJAX}
    <div class="extra_info">
        {_p var='ynsocialstore.no_stores_found'}
    </div>
    {/if}
{/if}

{literal}
<script type="text/javascript">
    $Behavior.initViewMode = function(){
        ynsocialstore.initViewMode('js_block_border_ynsocialstore_store_managestore');
    }
</script>
{/literal}
