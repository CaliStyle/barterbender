<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/20/16
 * Time: 10:56
 */
?>
{if PHPFOX_IS_AJAX_PAGE}
<span id="ynstore_goback" class="_a_back mb-1">
		<a href="javascript:void(0)">
            <i class="ico ico-arrow-left" aria-hidden="true"></i>
            {_p('Go Back')}
        </a>
	</span>
{/if}
{if count($aStoreList)}
<!-- Don't change the class name, using in Behavior-->
<div class="ynstore_compare_store">
    <div class="ynstore-compare-content">
        <div class="ynstore-compare-header">
            <div>&nbsp;</div>
            {if $aFieldStatus.name}
            <div>{_p var='ynsocialstore.comparision_store_name'}</div>
            {/if}
            {if $aFieldStatus.rating}
            <div>{_p var='ynsocialstore.comparision_store_rating'}</div>
            {/if}
            {if $aFieldStatus.categories}
            <div>{_p var='ynsocialstore.comparision_store_main_categories'}</div>
            {/if}
            {if $aFieldStatus.total_products}
            <div>{_p var='ynsocialstore.comparision_store_total_products'}</div>
            {/if}
            {if $aFieldStatus.total_orders}
            <div>{_p var='ynsocialstore.comparision_store_total_orders'}</div>
            {/if}
            {if $aFieldStatus.total_views}
            <div>{_p var='ynsocialstore.comparision_store_total_views'}</div>
            {/if}
            {if $aFieldStatus.total_reviews}
            <div>{_p var='ynsocialstore.comparision_store_total_reviews'}</div>
            {/if}
            {if $aFieldStatus.payment_info}
            <div>{_p var='ynsocialstore.comparision_store_shiping_and_payment_info'}</div>
            {/if}
            {if $aFieldStatus.policy}
            <div>{_p var='ynsocialstore.comparision_store_policy'}</div>
            {/if}
            {if $aFieldStatus.buyer_protection}
            <div>{_p var='ynsocialstore.comparision_store_buyer_protection'}</div>
            {/if}
        </div>

        <div class="ynstore-compare-list-content">
            <ul class="ynstore-compare-list">
                {foreach from=$aStoreList key=id item=aStore}
                <li id="ynstore_compare_page_item_{$aStore.store_id}">
                    <!-- image -->
                    <div class="ynstore-compare-item-top-content">
                        <div class="ynstore-compare-item-image">
                            <a href="{permalink module='ynsocialstore.store' id=$aStore.store_id title=$aStore.name}" title="{$aStore.name|clean}">
                                <span class="ynstore-photo-span" style="background-image: url(
                                {if $aStore.logo_path}
                                    {img server_id=$aStore.server_id path='core.url_pic' file='ynsocialstore/'.$aStore.logo_path suffix='_480_square' return_url='true'}
                                {else}
                                    {$sCorePath}module/ynsocialstore/static/image/store_default.png
                                {/if}
                            )"/>
                            </a>
                        </div>

                        <div class="ynstore-compare-item-close" onclick=" ynsocialstore.removeStoreFromCompare(this,{$aStore.store_id});"><i class="ico ico-close"></i></div>
                    </div>

                    <!--name-->
                    {if $aFieldStatus.name}
                    <div>
                        <span class="ynstore-compare-item-stats">
                            <a href="{permalink module='ynsocialstore.store' id=$aStore.store_id title=$aStore.name}">{$aStore.name|clean|shorten:100:'...'|split:10}</a>
                        </span>
                    </div>
                    {/if}
                    <!-- ratings -->
                    {if $aFieldStatus.rating}
                    <div>
                        <div class="ynstore-rating yn-rating yn-rating-normal">
                            {for $i = 0; $i < 5; $i++}
                            {if $i < (int)$aStore.rating}
                            <i class="ico ico-star" aria-hidden="true"></i>
                            {elseif ((round($aStore.rating) - $aStore.rating) > 0) && ($aStore.rating - $i) > 0}
                            <i class="ico ico-star-half-o" aria-hidden="true"></i>
                            {else}
                            <i class="ico ico-star yn-rating-disable" aria-hidden="true"></i>
                            {/if}
                            {/for}
                        </div>
                    </div>
                    {/if}

                    <!-- categories -->
                    {if $aFieldStatus.categories}
                    <div class="ynstore-categories">
                        <span class="">
                            <div class="ynstore-categories-content">
                                {if $aStore.hiddencate > 0}
                                    {if Phpfox::isPhrase($this->_aVars['aStore']['categories'][0]['title'])}
                                        <?php $this->_aVars['value_name'] = _p($this->_aVars['aStore']['categories'][0]['title']) ?>
                                    {else}
                                        {assign var='value_name' value=$aStore.categories.0.title|convert}
                                    {/if}
                                    <a href="{permalink module='ynsocialstore.store.category' id=$aStore.categories.0.category_id title=$value_name}">{$value_name}</a>
                                    <div class="dropdown">
                                        {_p('and')}
                                        <a href="javascript:void(0)" data-toggle="dropdown">+{$aStore.hiddencate}</a>
                                        <ul class="dropdown-menu">
                                            {foreach from=$aStore.categories key=iKey item=aCategory}
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
                                    {foreach from=$aStore.categories key=iKey item=aCategory}
                                        {if Phpfox::isPhrase($this->_aVars['aCategory']['title'])}
                                            <?php $this->_aVars['value_name'] = _p($this->_aVars['aCategory']['title']) ?>
                                        {else}
                                            {assign var='value_name' value=$aCategory.title|convert}
                                        {/if}
                                        <a href="{permalink module='ynsocialstore.store.category' id=$aCategory.category_id title=$value_name}">{$value_name}</a>{if $iKey == 0 && count($aStore.categories) > 1}<i>,</i>{/if}
                                    {/foreach}
                                {/if}
                            </div>
                        </span>
                    </div>
                    {/if}

                    <!-- products -->
                    {if $aFieldStatus.total_products}
                    <div><span class="">{$aStore.total_products}</span> </div>
                    {/if}

                    <!-- orders -->
                    {if $aFieldStatus.total_orders}
                    <div><span class="">{$aStore.total_orders}</span> </div>
                    {/if}

                    <!-- views -->
                    {if $aFieldStatus.total_views}
                    <div><span class="">{$aStore.total_view}</span> </div>
                    {/if}

                    <!-- reviews -->
                    {if $aFieldStatus.total_reviews}
                    <div><span class="">{$aStore.total_review}</span> </div>
                    {/if}

                    <!-- payment info -->
                    {if $aFieldStatus.payment_info}
                    <div><span class="">{$aStore.ship_payment_info}</span> </div>
                    {/if}

                    <!-- policy -->
                    {if $aFieldStatus.policy}
                    <div><span class="">{$aStore.return_policy}</span> </div>
                    {/if}

                    <!-- protection -->
                    {if $aFieldStatus.buyer_protection}
                    <div><span class="">{$aStore.buyer_protection}</span> </div>
                    {/if}
                </li>
                {/foreach}
            </ul>
        </div>

    </div>
</div>
{else}
{_p var='ynsocialstore.no_store_to_compare'}
{/if}

{literal}
<script type="text/javascript">
    ;$Behavior.ynsocialstore_compareitem_more_script = function() {

        $('.ynstore-compare-list').css('width', 200*$('.ynstore-compare-list > li').length );
        $('.ynstore-compare-header > div').each(function()
        {
            var div_index = $(this).index();
            var	max_height = $(this).height();

            $('.ynstore-compare-list > li').each(function(){
                if ( max_height < $(this).children('div').eq(div_index).height() ) {
                    max_height = $(this).children('div').eq(div_index).height();
                }
            });

            $(this).css('height', max_height);
            $('.ynstore-compare-list > li').each(function(){
                $(this).children('div').eq(div_index).css('height', max_height);
            });
        });

    };
</script>
{/literal}
