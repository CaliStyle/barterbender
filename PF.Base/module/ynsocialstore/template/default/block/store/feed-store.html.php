<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 11/3/16
 * Time: 14:08
 */
?>
<div class="ynstore-store-feed"> 
    <div class="ynstore-store-most-items">
        {if isset($aParentFeed) && $aParentFeed.type_id == 'ynsocialstore_store' && ($aParentFeed.item_id == $aFeed.parent_feed_id)}
        <div class="ynstore-store-most-item">
            <div class="ynstore-store-cover" style="background-image: url({if $aParentFeed.cover_path}{img server_id=$aParentFeed.cover_server_id path='core.url_pic' file='ynsocialstore/'.$aParentFeed.cover_path suffix='_1024' return_url='true'}{else}{param var='core.path'}module/ynsocialstore/static/image/store_cover_default.jpg{/if});">
                <a href="{permalink module='ynsocialstore.store' id=$aParentFeed.item_id title=$aParentFeed.feed_title}" class="ynstore-store-gradient"></a>
                {if Phpfox::isUser() && $aParentFeed.user_id != Phpfox::getUserId()}
                <div class="ynstore-detail-btn js-ynstore-detail-follow-store-{$aParentFeed.item_id}">
                    {if !$aParentFeed.is_following}
                    <a title="{_p var='follow'}" class="btn btn-primary" onclick="ynstoreupdateFollow({$aParentFeed.item_id},1);return false">
                        <i class="ico ico-plus"></i> {_p var='ynsocialstore.follow'}
                    </a>
                    {else}
                    <a title="{_p('Following')}" class="btn btn-default" onclick="ynstoreupdateFollow({$aParentFeed.item_id},0);return false">
                        <i class="ico ico-check"></i> {_p var='ynsocialstore.following'}
                    </a>
                    {/if}
                </div>
                {/if}
            </div>
            <div class="ynstore-store-info">
                <a href="{permalink module='ynsocialstore.store' id=$aParentFeed.item_id title=$aParentFeed.feed_title}" class="ynstore-store-avatar">
                    {if $aParentFeed.logo_path}
                    {img server_id=$aParentFeed.server_id path='core.url_pic' file='ynsocialstore/'.$aParentFeed.logo_path suffix='_480_square'}
                    {else}
                    <img src="{param var='core.path'}module/ynsocialstore/static/image/store_default.png">
                    {/if}

                    <div class="ynstore-featured">
                        <div  title="{_p var='ynsocialstore.featured'}" class="ynstore-featured-triangle ynstore_entry_feature_icon-{$aParentFeed.item_id}" {if !$aParentFeed.is_featured}style="visibility:hidden"{/if}>
                        <i class="ico ico-diamond"></i>
                        </div>
                    </div>
                </a>
                <div class="ynstore-store-info-txt">
                    <div class="ynstore-store-title">
                        <a href="{permalink module='ynsocialstore.store' id=$aParentFeed.item_id title=$aParentFeed.feed_title}">{$aParentFeed.feed_title}</a>
                    </div>

                    {if isset($aParentFeed.address) && !empty($aParentFeed.address)}
                    <div class="ynstore-store-address" title="{$aParentFeed.address}">
                        {_p var='ynsocialstore.location'}:
                        <a href="//maps.google.com/maps?daddr={$aParentFeed.latitude},{$aParentFeed.longitude}" target="_blank">{$aParentFeed.address}</a>
                    </div>
                    {/if}

                    <div class="ynstore-categories">
                        <div class="ynstore-categories-content">
                            {_p var='ynsocialstore.category'}:
                            {if $aParentFeed.hiddencate > 0}
                                {if Phpfox::isPhrase($this->_aVars['aParentFeed']['feed_categories'][0]['title'])}
                                    <?php $this->_aVars['value_name'] = _p($this->_aVars['aParentFeed']['feed_categories'][0]['title']) ?>
                                {else}
                                    {assign var='value_name' value=$aParentFeed.feed_categories.0.title|convert}
                                {/if}
                                <a href="{permalink module='ynsocialstore.store.category' id=$aParentFeed.feed_categories.0.category_id title=$value_name}">{$value_name}</a>
                                <div class="dropdown">
                                    {_p('and')}
                                    <a href="javascript:void(0)" data-toggle="dropdown">+{$aParentFeed.hiddencate}</a>
                                    <ul class="dropdown-menu">
                                        {foreach from=$aParentFeed.feed_categories key=iKey item=aCategory}
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
                                {foreach from=$aParentFeed.feed_categories key=iKey item=aCategory}
                                    {if Phpfox::isPhrase($this->_aVars['aCategory']['title'])}
                                        <?php $this->_aVars['value_name'] = _p($this->_aVars['aCategory']['title']) ?>
                                    {else}
                                        {assign var='value_name' value=$aCategory.title|convert}
                                    {/if}
                                    <a href="{permalink module='ynsocialstore.store.category' id=$aCategory.category_id title=$value_name}">{$value_name}</a>{if $iKey == 0 && count($aParentFeed.feed_categories) > 1}<i>,</i>{/if}
                                {/foreach}
                            {/if}
                        </div>
                    </div>

                    <div class="ynstore-store-description">
                        <span>{$aParentFeed.feed_content|clean}</span>
                    </div>
                </div>
            </div>
        </div>
        {else}
        <div class="ynstore-store-most-item">
            <div class="ynstore-store-cover" style="background-image: url({if $aFeed.cover_path}{img server_id=$aFeed.cover_server_id path='core.url_pic' file='ynsocialstore/'.$aFeed.cover_path suffix='_1024' return_url='true'}{else}{param var='core.path'}module/ynsocialstore/static/image/store_cover_default.jpg{/if});">
                <a href="{permalink module='ynsocialstore.store' id=$aFeed.item_id title=$aFeed.feed_title}" class="ynstore-store-gradient"></a>
                
                {if Phpfox::isUser() && $aFeed.user_id != Phpfox::getUserId()}
                <div class="ynstore-detail-btn js-ynstore-detail-follow-store-{$aFeed.item_id}">
                    {if !$aFeed.is_following}
                    <a title="{_p var='follow'}" class="btn btn-primary" onclick="ynstoreupdateFollow({$aFeed.item_id},1);return false">
                        <i class="ico ico-plus"></i> {_p var='ynsocialstore.follow'}
                    </a>
                    {else}
                    <a title="{_p('Following')}" class="btn btn-default" onclick="ynstoreupdateFollow({$aFeed.item_id},0);return false">
                        <i class="ico ico-check"></i> {_p var='ynsocialstore.following'}
                    </a>
                    {/if}
                </div>
                {/if}
            </div>


            <div class="ynstore-store-info">
                <a href="{permalink module='ynsocialstore.store' id=$aFeed.item_id title=$aFeed.feed_title}" class="ynstore-store-avatar">
                    {if $aFeed.logo_path}
                    {img server_id=$aFeed.server_id path='core.url_pic' file='ynsocialstore/'.$aFeed.logo_path suffix='_480_square'}
                    {else}
                    <img src="{param var='core.path'}module/ynsocialstore/static/image/store_default.png">
                    {/if}
                    <div class="ynstore-featured">
                        <div  title="{_p var='ynsocialstore.featured'}" class="ynstore-featured-triangle ynstore_entry_feature_icon-{$aFeed.item_id}" {if !$aFeed.is_featured}style="visibility:hidden"{/if}>
                        <i class="ico ico-diamond"></i>
                        </div>
                    </div>
                </a>

                <div class="ynstore-store-info-txt">
                    <div class="ynstore-store-title">
                        <a href="{permalink module='ynsocialstore.store' id=$aFeed.item_id title=$aFeed.feed_title}">{$aFeed.feed_title}</a>
                    </div>

                    {if isset($aFeed.address) && !empty($aFeed.address)}
                    <div class="ynstore-store-address" title="{$aFeed.address}">
                        {_p var='ynsocialstore.location'}:
                        <a href="//maps.google.com/maps?daddr={$aFeed.latitude},{$aFeed.longitude}" target="_blank">{$aFeed.address}</a>
                    </div>
                    {/if}

                    <div class="ynstore-categories">
                        <div class="ynstore-categories-content">
                            {_p var='ynsocialstore.category'}:
                            {if $aFeed.hiddencate > 0}
                                {if Phpfox::isPhrase($this->_aVars['aFeed']['feed_categories'][0]['title'])}
                                    <?php $this->_aVars['value_name'] = _p($this->_aVars['aFeed']['feed_categories'][0]['title']) ?>
                                {else}
                                    {assign var='value_name' value=$aFeed.feed_categories.0.title|convert}
                                {/if}
                                <a href="{permalink module='ynsocialstore.store.category' id=$aFeed.feed_categories.0.category_id title=$value_name}">{$value_name}</a>
                                <div class="dropdown">
                                    {_p('and')}
                                    <a href="javascript:void(0)" data-toggle="dropdown">+{$aFeed.hiddencate}</a>
                                    <ul class="dropdown-menu">
                                        {foreach from=$aFeed.feed_categories key=iKey item=aCategory}
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
                                {foreach from=$aFeed.feed_categories key=iKey item=aCategory}
                                    {if Phpfox::isPhrase($this->_aVars['aCategory']['title'])}
                                        <?php $this->_aVars['value_name'] = _p($this->_aVars['aCategory']['title']) ?>
                                    {else}
                                        {assign var='value_name' value=$aCategory.title|convert}
                                    {/if}
                                    <a href="{permalink module='ynsocialstore.store.category' id=$aCategory.category_id title=$value_name}">{$value_name}</a>{if $iKey == 0 && count($aFeed.feed_categories) > 1}<i>,</i>{/if}
                                {/foreach}
                            {/if}
                        </div>
                    </div>

                    <div class="ynstore-store-description">
                        <span>{$aFeed.feed_content|clean}</span>
                    </div>
                </div>
            </div>
        </div>
        {/if}
    </div>
</div>
{literal}
<script type="text/javascript">
    function ynstoreupdateFollow(store_id,iType){
        $.ajaxCall('ynsocialstore.updateFollow', $.param({iStoreId: store_id, bFollowing: iType}));
        return false;
    }
</script>
{/literal}