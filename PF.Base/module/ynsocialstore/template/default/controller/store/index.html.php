<?php

?>
{if !PHPFOX_IS_AJAX}
<div id="ynsocialstore_indexstore">
    <div class="ynsocialstore-hiddenblock">
        <input type="hidden" value="indexstore" id="ynsocialstore_pagename" name="ynsocialstore_pagename">
    </div>
</div>
{module name='ynsocialstore.store.search'}
{/if}
{if !$bIsHomepage}
<!-- Because design for 2 page 'favorite' and 'follow' too different so we render another entry-->
    {if in_array($sView, array('favorite', 'follow'))}
    {if count($aItems)}
    {if !PHPFOX_IS_AJAX}
    <div class="yn-viewmode-grid ynstore-store-follow-favorite">
        <ul class="ynstore-items ynstore-store-listing-block">
            {/if}
            {foreach from=$aItems name=store item=aItem}
            {template file='ynsocialstore.block.store.entry-favorite-follow'}
            {/foreach}
            {pager}
            {if $bShowModeration && !PHPFOX_IS_AJAX}{moderation}{/if}
            {if !PHPFOX_IS_AJAX}
        </ul>
        {/if}
    </div>
    {else}
    {if !PHPFOX_IS_AJAX}
    <div class="extra_info">
        {_p var='ynsocialstore.no_stores_found'}
    </div>
    {/if}
    {/if}

    {literal}
    <script type="text/javascript">
        $Behavior.onLoadAllCategories = function(){
            {/literal}
                {if isset($bIsCategoryHandle) && $bIsCategoryHandle == 1}
                {literal}
                if($('#page_ynsocialstore_store_index').length >0){
                    $('.sub_section_menu ul > li.active').removeClass("active");
                    $('.header_display ul > li.active').removeClass("active");
                    $('.sub_section_menu ul > li:eq(1)').addClass("active");
                    $('.header_display ul > li:eq(1)').addClass("active");
                }
                {/literal}
                    {/if}
                        {literal}
                    }
    </script>
    {/literal}
    {else}
        {if count($aItems)}
        {if !PHPFOX_IS_AJAX}
        <div class="ynstore-view-modes-block yn-viewmode-grid" id="js_block_border_ynsocialstore_store_listingstore">
            <div class="yn-view-modes yn-nomargin">
                <span data-mode="grid" class="yn-view-mode"><i class="ico ico-th"></i></span>
                <span data-mode="list" class="yn-view-mode"><i class="ico ico-list"></i></span>
            </div>
            <ul class="ynstore-items ynstore-store-listing-block">
                {/if}
                {foreach from=$aItems name=store item=aItem}
                {template file='ynsocialstore.block.store.entry'}
                {/foreach}
                {pager}
                {if $bShowModeration && !PHPFOX_IS_AJAX}{moderation}{/if}
                {if !PHPFOX_IS_AJAX}
            </ul>
        </div>
        {/if}
        {literal}
        <script type="text/javascript">
            $Behavior.initViewMode = function(){
                ynsocialstore.initViewMode('js_block_border_ynsocialstore_store_listingstore');
            }
        </script>
        {/literal}
        {elseif $iPage < 1}
        <div class="extra_info">
            {_p var='ynsocialstore.no_stores_found'}
        </div>
        {/if}

        {literal}
        <script type="text/javascript">
            $Behavior.onLoadAllCategories = function(){
                {/literal}
                    {if isset($bIsCategoryHandle) && $bIsCategoryHandle == 1}
                    {literal}
                    if($('#page_ynsocialstore_store_index').length >0){
                        $('.sub_section_menu ul > li.active').removeClass("active");
                        $('.header_display ul > li.active').removeClass("active");
                        $('.sub_section_menu ul > li:eq(1)').addClass("active");
                        $('.header_display ul > li:eq(1)').addClass("active");
                    }
                    {/literal}
                        {/if}
                            {literal}
                        }
        </script>
        {/literal}
    {/if}
{/if}
<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?key={$apiKey}&v=3.exp&libraries=places"></script>
