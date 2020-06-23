<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/19/16
 * Time: 15:41
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="ynstore-compare-holder" id="ynstore-compare-dashboard">
    <div id="ynstore-compare-item-list" class="ynstore-compare-block {if $boxSize == 'min'}ynstore-hide{/if} {if count($aStores)}yes{/if}">
        <div class="">
            <ul id="ynsocialstore_tab" class="ynsocialstore_tab nav nav-tabs nav-justified">
                <li class="{if $tabSelected == 'product'}ynstore-active{/if}">
                    <a href="" data-typecompare="product" onclick="return ynsocialstore.switchCompareTab(this);"><span>{_p var='products'}{if $iTotalProduct > 0}&nbsp;({$iTotalProduct}){/if}</span></a>
                </li>
                <li class="{if $tabSelected == 'store'}ynstore-active{/if}">
                    <a href="" data-typecompare="store" onclick="return ynsocialstore.switchCompareTab(this);"><span>{_p var='stores'}{if $iTotalStore > 0}&nbsp;({$iTotalStore}){/if}</span></a>
                </li>
            </ul>
        </div>

        <div id="ynstore-compare-store-section" class="{if $tabSelected == 'product'}hide{/if}">
            {if count($aStores)}
            <div id="ynstore-store-tab" class="ynstore-compare-store-items">
                <div id="ynstore-confirm-detele-all" class="hide ynstore-confirm-btn-block">
                    {_p var='ynsocialstore.are_you_sure_to_remove_all_stores_from_compare_list'}

                    <div class="ynstore-confirm-btns">
                        <span class="ynstore-confirm-btn">
                            <i class="ico ico-ban" onclick="$('#ynstore-confirm-detele-all').addClass('hide'); return false;"></i>
                        </span>

                        <span class="ynstore-confirm-btn">
                            <i class="ico ico-check" onclick="return ynsocialstore.removeAllStoreFromCompare();"></i>
                        </span>
                    </div>
                </div>
                {foreach from=$aStores key=iKey item=aStore}
                    <div title="{$aStore.name}" class="ynstore-compare-store-item">
                        <div class="ynstore-compare-store-content" style="background-image: url(
                            {if $aStore.logo_path}
                                {img server_id=$aStore.server_id path='core.url_pic' file='ynsocialstore/'.$aStore.logo_path suffix='_480_square' return_url='true'}
                            {else}
                                {$sCorePath}module/ynsocialstore/static/image/store_default.png
                            {/if}
                        )">
                            <span title="{_p var='ynsocialstore.delete'}" class="ynstore-btn-delete" data-storeid="{$aStore.store_id}" onclick="return ynsocialstore.removeStoreFromCompare(this,{$aStore.store_id});">
                                <i class="ico ico-close"></i>
                            </span>
                        </div>
                    </div>
                {/foreach}
            </div>

            <div class="ynstore-compare-actions">
                <a class="btn btn-primary {if $iTotalStore < 2}disabled{/if}" href="{$compareStoreLink}" onclick="ynsocialstore.toggleCompareDasBoard(this);" data-type="hide">
                    {_p var='ynsocialstore.start_compare_store'}
                </a>

                <a class="btn ynstore-btn" onclick="$('#ynstore-confirm-detele-all').removeClass('hide'); return false;">
                    {_p var='ynsocialstore.remove_all_stores_from_compare'}
                </a>
            </div>

            {else}
                <div class="ynstore-compare-no-item">
                    <img src="{$sCorePath}module/ynsocialstore/static/image/no-compare-store.jpg" alt="">
                    {_p var='ynsocialstore.no_store_to_compare'}
                </div>
            {/if}
        </div>

        <div id="ynstore-compare-product-section" class="{if $tabSelected == 'store'}hide{/if}">

            {if isset($aCompareProductList) && count($aCompareProductList)}
            <div id="ynstore-product-tab" class="ynstore-compare-store-items" role="tablist" aria-multiselectable="true">
                <div id="ynstore-confirm-detele-category" class="hide ynstore-confirm-btn-block">
                    {_p var='ynsocialstore.are_you_sure_to_remove_this_group_and_its_items'}

                    <div class="ynstore-confirm-btns">
                        <span class="ynstore-confirm-btn" onclick="$('#ynstore-confirm-detele-category').addClass('hide');$('#js_ynstore_check_delete_product').unbind('click'); return false;">
                            <i class="ico ico-ban" ></i>
                        </span>

                        <span class="ynstore-confirm-btn" id="js_ynstore_check_delete_product">
                            <i class="ico ico-check"  ></i>
                        </span>
                    </div>
                </div>
                {foreach from=$aCompareProductList key=iKey item=aCategoryProduct}
                <div class="ynstore-product-compare-catename" id="ynstore-ptab-{$aCategoryProduct.category_id}">
                    <a id="ynstore-category-product-compare" class="collapsed" role="button" data-toggle="collapse" data-parent="#ynstore-product-tab"  href="#ynstore-category-product-tab-{$aCategoryProduct.category_id}"  aria-expanded="false" aria-controls="ynstore-category-product-tab-{$aCategoryProduct.category_id}" data-cateid="{$aCategoryProduct.category_id}">
                        <span class="ynstore-title">
                            {if Phpfox::isPhrase($this->_aVars['aCategoryProduct']['title'])}
                                {_p var=$aCategoryProduct.title}
                            {else}
                                {$aCategoryProduct.title|convert}
                            {/if}
                        </span>
                        ({$aCategoryProduct.total})

                        <span class="ynstore-actions">
                            <span title="{_p var='ynsocialstore.delete'}" class="ynstore-btn-delete" data-categoryid="{$aCategoryProduct.category_id}" onclick="ynsocialstore.showDeleteCategoryCompareConfirm(this)">
                                <i class="ico ico-trash-o"></i>
                            </span>
                            <span class="ynstore-arr"><i class="ico ico-angle-right"></i></span>
                        </span>
                    </a>
                </div>

                <div class="ynstore-product-compare-content panel-collapse collapse" id="ynstore-category-product-tab-{$aCategoryProduct.category_id}" role="tabpanel" aria-labelledby="ynstore-ptab-{$aCategoryProduct.category_id}">
                    {foreach from=$aCategoryProduct.products key=iKey2 item=aProduct}
                    <div id="js_ynstore_product_compare_list-{$aProduct.product_id}" class="ynstore-product-item">
                        <div title="{$aProduct.name}" data-productid="{$aProduct.product_id}" class="ynstore-product-bg js_ynstore_list_product_compare"
                             style="background-image: url(
                            {if $aProduct.logo_path}
                                {img server_id=$aProduct.server_id path='core.url_pic' file=$aProduct.logo_path suffix='_100' return_url='true'}
                            {else}
                                {$sCorePath}module/ynsocialstore/static/image/product_default.jpg
                            {/if}
                        )">
                            <span title="{_p var='ynsocialstore.delete'}" class="ynstore-btn-delete" data-productid="{$aProduct.product_id}" onclick="return ynsocialstore.removeProductFromCompare(this,{$aProduct.product_id});">
                                <i class="ico ico-close" data-productid="{$aProduct.product_id}" ></i>
                            </span>
                        </div>
                        <div class="ynstore-product-info">
                            <a title="{$aProduct.name}" class="ynstore-product-title" href="{permalink module='ynsocialstore.product' id=$aProduct.product_id}">
                                {$aProduct.name|clean}
                            </a>
                            <span class="ynstore-product-categories">
                                <u>{_p var='store'}:</u> {$aProduct.store_name|clean}
                            </span>
                        </div>
                    </div>
                    {/foreach}
                    <a class="btn btn-primary {if $aCategoryProduct.total < 2}disabled{/if}"  href="{$aCategoryProduct.compare_link}" onclick="ynsocialstore.toggleCompareDasBoard(this);" data-type="hide">{_p var='ynsocialstore.start_compare_product'}</a>
                </div>

                {/foreach}
            </div>

            {else}
                <div class="ynstore-compare-no-item">
                    <img src="{$sCorePath}module/ynsocialstore/static/image/no-compare-product.jpg" alt="">
                    {_p var='ynsocialstore.no_product_to_compare'}
                </div>
            {/if}
        </div>
    </div>

    <div class="ynstore-icon-compare">
        <div class="ynstore-toggle-compare-btn {if $boxSize == 'min'}ynstore-hide{/if}" id="ynstore_compare_btn" data-type="{if $boxSize == 'min'}show{else}hide{/if}" onclick="return ynsocialstore.toggleCompareDasBoard(this);">
            {if $iTotalCompare > 0}
            <span id="ynstore-total-compare-item">
                <b>{$iTotalCompare}</b>
            </span>
            {/if}
            <i class="ynstore-open ico ico-copy"></i>
            <i class="ynstore-close ico ico-close"></i>
        </div>
    </div>
</div>



