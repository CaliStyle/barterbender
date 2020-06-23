<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 11/3/16
 * Time: 11:04 AM
 */
?>

<li class="ynstore-item" data-store-id="{$aItem.store_id}" id="js_store_id_{$aItem.store_id}">
    <div class="ynstore-item-content ynstore-store-listing" >
        {if Phpfox::isAdmin() && !isset($bIsNoModerate) || !empty($bShowModeration)}
        <div class="moderation_row">
            <label class="item-checkbox">
                <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aItem.store_id}" id="check{$aItem.store_id}" />
                <i class="ico ico-square-o"></i>
            </label>
        </div>
        {/if}
        <div class="ynstore-bg"
             style="background-image: url(
				{if $aItem.logo_path}
			        {img server_id=$aItem.server_id path='core.url_pic' file='ynsocialstore/'.$aItem.logo_path suffix='_480_square' return_url='true'}
			    {else}
			    	{param var='core.path_actual'}PF.Base/module/ynsocialstore/static/image/store_default.png
			    {/if}
		    )">

            <div class="ynstore-featured">
                <div  title="{_p('Featured')}" class="ynstore-featured-triangle ynstore_entry_feature_icon-{$aItem.store_id}" {if !$aItem.is_featured}style="visibility:hidden"{/if}>
                <i class="ico ico-diamond"></i>
            </div>
        </div>

        <a  href="{permalink module='ynsocialstore.store' id=$aItem.store_id title=$aItem.name}"></a>

        <div class="ynstore-store-action">
            {if isset($aItem.user_id)}
                {if Phpfox::isUser() && $aItem.user_id != Phpfox::getUserId()}
                    {if isset($sView) && $sView == 'favorite'}
                        <div id="ynstore-detail-favorite-store-{$aItem.store_id}">
                            <a class="" href="javascript:void(0)" onclick="ynsocialstore.updateFavorite({$aItem.store_id},0);return false;"><i class="ico ico-star"></i> {_p var='ynsocialstore.favorited'} </a>
                        </div>
                    {/if}

                    {if isset($sView) && $sView == 'follow'}
                        <div id="ynstore-detail-follow-store-{$aItem.store_id}">
                            <a class="" href="javascript:void(0)" onclick="ynsocialstore.updateFollow({$aItem.store_id},0);return false">
                                {_p var='ynsocialstore_unfollow'}
                            </a>
                        </div>
                    {/if}
                {/if}
            {/if}
        </div>
        <div title="{_p('Add to compare')}" class="ynstore-compare-btn {if isset($bIsNoCompare) && $bIsNoCompare}hide{/if}" onclick="ynsocialstore.addToCompare({$aItem.store_id},'store');return false;" data-comparestoreid="{$aItem.store_id}">
            <i class="ico ico-copy"></i>
        </div>
    </div>

    <div class="ynstore-info">
        <div class="ynstore-info-detail">
            <a href="{permalink module='ynsocialstore.store' id=$aItem.store_id title=$aItem.name}" class="ynstore-title">
                {$aItem.name|clean}
            </a>
            {if !empty($aItem.address)}
            <div class="ynstore-address">
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
    
            <div class="ynstore-store-action">
                {if isset($aItem.user_id)}
                    {if Phpfox::isUser() && $aItem.user_id != Phpfox::getUserId()}
                        {if isset($sView) && $sView == 'favorite'}
                            <div id="ynstore-detail-favorite-store-{$aItem.store_id}">
                                <a class="" href="javascript:void(0)" onclick="ynsocialstore.updateFavorite({$aItem.store_id},0);return false;"><i class="ico ico-star"></i> {_p var='ynsocialstore.favorited'} </a>
                            </div>
                        {/if}

                        {if isset($sView) && $sView == 'follow'}
                            <div id="ynstore-detail-follow-store-{$aItem.store_id}">
                                <a class="" href="javascript:void(0)" onclick="ynsocialstore.updateFollow({$aItem.store_id},0);return false">
                                    {_p var='ynsocialstore_unfollow'}
                                </a>
                            </div>
                        {/if}
                    {/if}
                {/if}
            </div>
        </div>
    </div>
</li>