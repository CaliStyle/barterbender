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
<input type="hidden" name="category_id" id="ynstore_category_id" value="{$iCategoryId}">
<div id="js_ynstore_categories_compare_select" class="mb-2">
    {module name='ynsocialstore.product.categories-compare'}
</div>
{if count($aProductCompare)}
<!-- Don't change the class name, using in Behavior-->
<div class="ynstore_compare_store">
    <div class="ynstore-compare-content">
        <div class="ynstore-compare-header">
            <div>&nbsp;</div>
            {if $aFieldStatus.name}
            <div>{_p var='ynsocialstore.comparision_product_name'}</div>
            {/if}
            {if $aFieldStatus.rating}
            <div>{_p var='ynsocialstore.comparision_product_rating'}</div>
            {/if}
            {if $aFieldStatus.price}
            <div>{_p var='ynsocialstore.comparision_product_price'}</div>
            {/if}
            {if $aFieldStatus.total_orders}
            <div>{_p var='ynsocialstore.comparision_product_total_orders'}</div>
            {/if}
            {if $aFieldStatus.total_views}
            <div>{_p var='ynsocialstore.comparision_product_total_views'}</div>
            {/if}
            {if $aFieldStatus.total_reviews}
            <div>{_p var='ynsocialstore.comparision_product_total_reviews'}</div>
            {/if}
            {if $aFieldStatus.seller}
            <div>{_p var='ynsocialstore.comparision_product_seller'}</div>
            {/if}
            {if $aFieldStatus.description}
            <div>{_p var='ynsocialstore.comparision_product_description'}</div>
            {/if}
            {if $aFieldStatus.custom_fields}
                {foreach from=$aCustomFields key=id item=aCustomFieldItem}
                    <div>{_p var=$aCustomFieldItem.phrase_var_name}</div>
                {/foreach}
            {/if}
        </div>

        <div class="ynstore-compare-list-content">
            <ul class="ynstore-compare-list">
                {foreach from=$aProductCompare key=id item=aProduct}
                <li id="ynstore_compare_page_item_{$aProduct.product_id}">
                    <!-- image -->
                    <div class="ynstore-compare-item-top-content">
                        <div class="ynstore-compare-item-image">
                            <a href="{permalink module='ynsocialstore.product' id=$aProduct.product_id title=$aProduct.name}" title="{$aProduct.name|clean}">
                                <span class="ynstore-photo-span" style="background-image: url(
                                {if $aProduct.logo_path}
                                    {img server_id=$aProduct.server_id path='core.url_pic' file=$aProduct.logo_path suffix='_400' return_url='true'}
                                {else}
                                    {$sCorePath}module/ynsocialstore/static/image/product_default.jpg
                                {/if}
                            )"/>
                            </a>
                        </div>

                        <div class="ynstore-compare-item-close" onclick=" ynsocialstore.removeProductFromCompare(this,{$aProduct.product_id});"><i class="ico ico-close"></i></div>
                    </div>

                    <!--name-->
                    {if $aFieldStatus.name}
                    <div>
                        <span class="ynstore-compare-item-stats">
                            <a href="{permalink module='ynsocialstore.product' id=$aProduct.product_id title=$aProduct.name}">{$aProduct.name|clean|shorten:100:'...'|split:10}</a>
                        </span>
                    </div>
                    {/if}
                    <!-- ratings -->
                    {if $aFieldStatus.rating}
                    <div>
                        <div class="ynstore-rating yn-rating yn-rating-normal">
                            {for $i = 0; $i < 5; $i++}
                            {if $i < (int)$aProduct.rating}
                            <i class="ico ico-star" aria-hidden="true"></i>
                            {elseif ((round($aProduct.rating) - $aProduct.rating) > 0) && ($aProduct.rating - $i) > 0}
                            <i class="ico ico-star-half-o" aria-hidden="true"></i>
                            {else}
                            <i class="ico ico-star yn-rating-disable" aria-hidden="true"></i>
                            {/if}
                            {/for}
                        </div>
                    </div>
                    {/if}
                    <!-- price -->
                    {if $aFieldStatus.price}
                    <div>
                        {if isset($aProduct.has_attribute) && $aProduct.has_attribute}<span>{_p var='from_l'}</span> {/if}
                        {if $aProduct.discount_percentage && ($aProduct.discount_timeless || ($aProduct.discount_start_date <= PHPFOX_TIME && $aProduct.discount_end_date >= PHPFOX_TIME))}
                            <span class="">{$aProduct.currency_symbol}{$aProduct.discount_display|number_format:2}</span>{if $aProduct.product_type =='physical' && !empty($aProduct.uom_title)} / {$aProduct.uom_title|convert}{/if}
                            <br/>
                            <s>{$aProduct.currency_symbol}{$aProduct.product_price}</s>
                        {else}
                            <span class="">{$aProduct.product_price|currency:$aProduct.creating_item_currency}</span>{if $aProduct.product_type =='physical' && !empty($aProduct.uom_title)} / {$aProduct.uom_title|convert}{/if}
                        {/if}

                    </div>
                    {/if}

                    <!-- orders -->
                    {if $aFieldStatus.total_orders}
                    <div><span class="">{$aProduct.total_orders}</span> </div>
                    {/if}

                    <!-- views -->
                    {if $aFieldStatus.total_views}
                    <div><span class="">{$aProduct.total_view}</span> </div>
                    {/if}

                    <!-- reviews -->
                    {if $aFieldStatus.total_reviews}
                    <div><span class="">{$aProduct.total_review}</span> </div>
                    {/if}

                    <!-- seller info -->
                    {if $aFieldStatus.seller}
                    <div><span class=""><a href="{permalink module='ynsocialstore.store' id=$aProduct.store_id title=$aProduct.store_name}" title="{$aProduct.store_name|clean}">{$aProduct.store_name|clean|shorten:100:'...'|split:10}</a></span> </div>
                    {/if}
                    
                    <!-- description -->
                    {if $aFieldStatus.description}
                    <div><span class="">{$aProduct.description|striptag|clean}</span></div>
                    {/if}

                    <!-- custom field -->
                    {if $aFieldStatus.custom_fields}
                    {foreach from=$aProduct.custom_field_list key=list_customdata_id item=list_customdata_item}
                    <div>
                        <span class="">
                        {if $list_customdata_item.var_type=='text'}
                            {if isset($list_customdata_item.value) }{$list_customdata_item.value}&nbsp;{/if}
                        {elseif $list_customdata_item.var_type=='textarea'}
                            {if isset($list_customdata_item.value) }{$list_customdata_item.value}&nbsp;{/if}
                        {elseif $list_customdata_item.var_type=='select'}
                            {if isset($list_customdata_item.value) }
                                {foreach from=$list_customdata_item.value key=value_id item=value_item}
                                    {_p var=$value_item} <br/>
                                {/foreach}
                            {/if}
                        {elseif $list_customdata_item.var_type=='multiselect'}
                            {if isset($list_customdata_item.value) }
                                {foreach from=$list_customdata_item.value key=value_id item=value_item}
                                    {_p var=$value_item} <br/>
                                {/foreach}
                            {/if}
                        {elseif $list_customdata_item.var_type=='checkbox'}
                            {if isset($list_customdata_item.value) }
                                {foreach from=$list_customdata_item.value key=value_id item=value_item}
                                    {_p var=$value_item} <br/>
                                {/foreach}
                            {/if}
                        {elseif $list_customdata_item.var_type=='radio'}
                            {if isset($list_customdata_item.value) }
                                {foreach from=$list_customdata_item.value key=value_id item=value_item}
                                    {_p var=$value_item} <br/>
                                {/foreach}
                            {/if}
                        {/if}
                            </span>
                    </div>
                    {/foreach}
                    {/if}


                </li>
                {/foreach}
            </ul>
        </div>

    </div>
</div>
{else}
{_p var='ynsocialstore.no_product_to_compare'}
{/if}

{literal}
<script type="text/javascript">
    ;$Behavior.ynsocialstore_compareitem_more_script = function() {

        $('.ynstore-compare-list').css('width', 200*$('.ynstore-compare-list > li').length );
        $('.ynstore-compare-header > div').each(function()
        {
            var div_index = $(this).index();
            var max_height = $(this).height();

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
        $('#ynstore_detail_compare_product_category').on('change',function()
        {
            window.location.href = $('#ynstore_detail_compare_product_category option:selected').data('link');
        })
    };
</script>
{/literal}
