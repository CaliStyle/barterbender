<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 11/7/16
 * Time: 09:04
 */
?>
{if count($aProducts) > 0}
<form action="{url link='ynsocialstore.store.manage-products' id=$iStoreId}" method="post" id="ynstore_product_list" class="ynstore-store-product-manage">
    <div class="table-responsive">
        <table class="table">
            <!-- Table rows header -->
            <thead>
                <th><input type="checkbox" onclick="ynsocialstore.checkAllProducts();" name="val[product_id]" value="" id="js_check_box_all" class="main_checkbox" /></th>
                <th></th>
                <th data-value="#sort_name" onclick="ynsocialstoreSortBy(this)">{_p var='ynsocialstore.product_name'}</th>
                <th>{_p var='ynsocialstore.category'}</th>
                <th>{_p var='ynsocialstore.remaining'}</th>
                <th>{_p var='ynsocialstore.sold'}</th>
                <th>{_p var='ynsocialstore.status'}</th>
                <th>{_p var='ynsocialstore.featured'}</th>
            </thead>

            <tbody>
                {foreach from=$aProducts key=iKey item=aItem}
                <tr id="js_row{$aItem.product_id}">
                    <td><input type="checkbox" name="product_id[]" class="js_row_checkbox"" value="{$aItem.product_id}" id="js_ynstore_product_{$aItem.product_id}" onclick="ynsocialstore.checkDisableStatus();"/></td>
                    <!-- Options -->
                    <td class="dropdown">
                        <a href="#" title="Options" role="button" data-toggle="dropdown">
                            <i class="ico ico-caret-down"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-left">
                            <li class="item"><a href="{url link='ynsocialstore.add' id=$aItem.product_id}">{_p var='ynsocialstore.edit'}</a></li>
                            <li class="item"><a href="{url link='ynsocialstore.manage-photos' id=$aItem.product_id}">{_p var='ynsocialstore.manage_photos'}</a></li>
                            {if $aItem.product_type != 'digital' && $aPackageStore.enable_attribute}
                            <li class="item"><a href="{url link='ynsocialstore.manage-attributes' id=$aItem.product_id}">{_p var='ynsocialstore.manage_attributes'}</a></li>
                            {/if}
                            <li class="item"><a href="{url link='ynsocialstore.product-sales' id=$aItem.product_id}">{_p var='ynsocialstore.sales'}</a></li>
                            {if $aItem.product_status == 'public'}
                            <li class="item ynstore-close-open-product-{$aItem.product_id}">
                                <a href="javascript:void(0)" onclick="ynsocialstore.closeProduct({$aItem.product_id},{$aItem.user_id},'{$aItem.product_status}','manage'); return false;">{_p var='ynsocialstore.close'} </a>
                            </li>
                            {/if}
                            {if $aItem.product_status == 'closed'}
                            <li class="item ynstore-close-open-product-{$aItem.product_id}">
                                <a href="javascript:void(0)" onclick="ynsocialstore.openProduct({$aItem.product_id},{$aItem.user_id},'{$aItem.product_status}','manage'); return false;">{_p var='ynsocialstore.open'} </a>
                            </li>
                            {/if}
                            <li class="item"><a href="{permalink module='ynsocialstore.product' id=$aItem.product_id}">{_p var='ynsocialstore.view_this_product'}</a></li>
                        </ul>
                    </td>

                    <td style="min-width: 300px;">
                        <a title="{$aItem.name}" href="{permalink module='ynsocialstore.product' id=$aItem.product_id title=$aItem.name}">
                            {$aItem.name|shorten:100:'...'}
                        </a>
                        <!-- <span>{$aItem.time_stamp|date:'core.global_update_time'}</span> -->
                    </td>

                    <td class="">
                        {if Phpfox::isPhrase($this->_aVars['aItem']['category_name'])}
                            {_p var=$aItem.category_name}
                        {else}
                            {$aItem.category_name|convert}
                        {/if}
                    </td>

                    <td class="">
                        {if (int)$aItem.product_quantity > 0}
                            {$aItem.remaining}
                        {else}
                            {_p var='ynsocialstore.unlimited'}
                        {/if}
                    </td>

                    <td class="t_center">
                        {$aItem.total_orders}
                    </td>

                    <td class="t_center" id="js_product_status_{$aItem.product_id}">
                        {_p var='ynsocialstore.'.$aItem.product_status}
                    </td>
                    <td class="t_center">
                        {if $aItem.is_featured}
                        <i class="ico ico-check"></i>
                        {else}
                        <i class="ico ico-ban"></i>
                        {/if}
                    </td>

                </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
    <div class="table_bottom">
        <input type="button" id="delete_selected" name="delete[submit]" value="{_p var='ynsocialstore.delete_selected'}" class="btn btn-danger disabled" onclick="ynsocialstore.confirmDeleteProducts('ynstore_product_list');"/>
    </div>
</form>
{template file='ynsocialstore.block.store.pager'}
{else}
{_p var='ynsocialstore.no_results'}
{/if}