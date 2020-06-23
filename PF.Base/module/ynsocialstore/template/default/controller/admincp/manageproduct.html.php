<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<form id="manage_product_form" method="GET" action="{url link='admincp.ynsocialstore.manageproduct'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var="ynsocialstore.search_filter"}
            </div>
        </div>

        <div class="panel-body">
            <div>
                <input type="hidden" class="form-control" name="search[sort_store_name]" id="sort_store_name"  value="{value type='input' id='sort_store_name'}">
                <input type="hidden" class="form-control" name="search[sort_name]" id="sort_name"  value="{value type='input' id='sort_name'}">
                <input type="hidden" class="form-control" name="search[sort_price]" id="sort_price"  value="{value type='input' id='sort_price'}">
                <input type="hidden" class="form-control" name="search[sort_creation_date]" id="sort_creation_date"  value="{value type='input' id='sort_creation_date'}">
            </div>
            <div class="form-group">
                <label>{_p var="ynsocialstore.store_title"}</label>
                <input class="form-control" type="text" name="search[title]" value="{value type='input' id='title'}" id="title">
            </div>

            <div class="form-group">
                <label>{_p var='ynsocialstore.store'}</label>
                <input class="form-control" type="text" name="search[store_name]" value="{value type='input' id='store_name'}" id="store_name">
            </div>

            <div class="form-group">
                <label>{_p var="ynsocialstore.featured"}</label>
                <select name="search[feature]" class="form-control">
                    <option value="0">{_p var='ynsocialstore.any'}</option>
                    <option value="featured"  {value type='select' id='feature' default = 'featured'}>{_p var='ynsocialstore.featured'}</option>
                    <option value="not_featured"  {value type='select' id='feature' default = 'not_featured'}>{_p var='ynsocialstore.not_featured'}</option>
                </select>
            </div>

            <div class="form-group">
                <label>{_p var="ynsocialstore.categories"}</label>
                <select name="search[category_id]" class="form-control">
                    <option value="0">{_p var='ynsocialstore.any'}</option>
                    {foreach from=$aCategories item=aCategory}
                        {if Phpfox::isPhrase($this->_aVars['aCategory']['title'])}
                            <?php $this->_aVars['value_name'] = _p($this->_aVars['aCategory']['title']) ?>
                        {else}
                            {assign var='value_name' value=$aCategory.title|convert}
                        {/if}
                        <option id="{$aCategory.category_id}" {if isset($aForms) && $aForms.category_id == $aCategory.category_id} selected="selected" {/if}  value="{$aCategory.category_id}">{$value_name}</option>
                        {if !empty($aCategory.sub_1)}
                            {foreach from=$aCategory.sub_1 item=aSubCategory}
                                {if Phpfox::isPhrase($this->_aVars['aSubCategory']['title'])}
                                    <?php $this->_aVars['value_name'] = _p($this->_aVars['aSubCategory']['title']) ?>
                                {else}
                                    {assign var='value_name' value=$aSubCategory.title|convert}
                                {/if}
                                <option id="{$aSubCategory.category_id}" {if isset($aForms) && $aForms.category_id == $aSubCategory.category_id} selected="selected" {/if} value="{$aSubCategory.category_id}">--{$value_name}</option>
                            {/foreach}
                        {/if}
                        {if !empty($aCategory.sub_2)}
                            {foreach from=$aCategory.sub_2 item=aSubCategory}
                                {if Phpfox::isPhrase($this->_aVars['aSubCategory']['title'])}
                                    <?php $this->_aVars['value_name'] = _p($this->_aVars['aSubCategory']['title']) ?>
                                {else}
                                    {assign var='value_name' value=$aSubCategory.title|convert}
                                {/if}
                                <option id="{$aSubCategory.category_id}" {if isset($aForms) && $aForms.category_id == $aSubCategory.category_id} selected="selected" {/if} value="{$aSubCategory.category_id}">----{$value_name}</option>
                            {/foreach}
                        {/if}
                    {/foreach}
                </select>
            </div>

            <div class="form-group">
                <label>{_p var="ynsocialstore.status"}</label>
                <select name="search[status]" class="form-control">
                    <option value="">{_p var='ynsocialstore.any'}</option>
                    <option value="draft"  {value type='select' id='status' default = 'draft'}>{_p var='ynsocialstore.draft'}</option>
                    <option value="pending"  {value type='select' id='status' default = 'pending'}>{_p var='ynsocialstore.pending'}</option>
                    <option value="public"  {value type='select' id='status' default = 'public'}>{_p var='ynsocialstore.public'}</option>
                    <option value="denied"  {value type='select' id='status' default = 'denied'}>{_p var='ynsocialstore.denied'}</option>
                    <option value="closed"  {value type='select' id='status' default = 'closed'}>{_p var='ynsocialstore.closed'}</option>
                </select>
            </div>
        </div>

        <div class="panel-footer">
            <button type="submit" id="filter_submit" name="search[submit]" class="btn btn-primary">{_p var='ynsocialstore.search'}</button>
        </div>
    </div>
</form>

{if $iCount > 0}
<form method="POST" id="stores_list" >
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var="ynsocialstore.products"}
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <!-- Table rows header -->
                <thead>
                    <tr>
                        <th class="t_center w40"><input type="checkbox" name="val[id]" value="" id="js_check_box_all" class="main_checkbox" /></th>
                        <th class="t_center w40"></th>
                        <th class="t_center clickable" data-value="#sort_name" onclick="ynsocialstoreSortBy(this)">{_p var='ynsocialstore.product_name'}</th>
                        <th class="t_center clickable" data-value="#sort_store_name" onclick="ynsocialstoreSortBy(this)">{_p var='ynsocialstore.store'}</th>
                        <th class="t_center w100">{_p var='ynsocialstore.category'}</th>
                        <th class="t_center clickable" data-value="#sort_price" onclick="ynsocialstoreSortBy(this)">{_p var='ynsocialstore.price'}</th>
                        <th class="t_center w160 clickable" data-value="#sort_creation_date" onclick="ynsocialstoreSortBy(this)">{_p var='ynsocialstore.created_on'}</th>
                        <th class="t_center w100">{_p var='ynsocialstore.status'}</th>
                        <th class="t_center w100">{_p var='ynsocialstore.store_status'}</th>
                        <th class="t_center w40">{_p var='ynsocialstore.featured'}</th>
                    </tr>
                </thead>

                <tbody>
                {foreach from=$aList key=iKey item=aItem}
                    <tr id="js_row{$aItem.product_id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                        <td class="t_center w40"><input type="checkbox" name="id[]" class="checkbox" value="{$aItem.product_id}" id="js_id_row{$aItem.product_id}"></td>
                        <!-- Options -->
                        <td class="t_center w40">
                            <a href="#" class="js_drop_down_link" title="Options"></a>
                            <div class="link_menu">
                                <ul>
                                    <li><a href="{url link='ynsocialstore.add' id=$aItem.product_id}">{_p var='ynsocialstore.edit'}</a></li>
                                    <li><a href="javascript:void(0)" onclick="manageproducts.confirmdeleteProduct({$aItem.product_id}); return false;">{_p var='ynsocialstore.delete'}</a></li>
                                    {if $aItem.product_status == 'pending'}
                                    <li><a href="javascript:void(0)" onclick="manageproducts.denyProduct({$aItem.product_id}, '{$aItem.product_status}'); return false;">{_p var='ynsocialstore.deny'}</a></li>
                                    <li><a href="javascript:void(0)" onclick="manageproducts.approveProduct({$aItem.product_id}, '{$aItem.product_status}'); return false;">{_p var='ynsocialstore.approve'}</a></li>
                                    {elseif $aItem.product_status == 'public'}
                                    <li><a href="javascript:void(0)" onclick="manageproducts.closeProduct({$aItem.product_id}, {$aItem.user_id}, '{$aItem.product_status}'); return false;">{_p var='ynsocialstore.close'}</a></li>
                                    {elseif $aItem.product_status == 'closed'}
                                    <li><a href="javascript:void(0)" onclick="manageproducts.reopenProduct({$aItem.product_id}, {$aItem.user_id}, '{$aItem.product_status}'); return false;">{_p var='ynsocialstore.reopen'}</a></li>
                                    {elseif $aItem.product_status == 'denied'}
                                    <li><a href="javascript:void(0)" onclick="manageproducts.approveProduct({$aItem.product_id}, '{$aItem.product_status}'); return false;">{_p var='ynsocialstore.approve'}</a></li>
                                    {/if}
                                </ul>
                            </div>
                        </td>

                        <td class="t_center">
                            <a href="{permalink module='ynsocialstore.product' id=$aItem.product_id title=$aItem.name}">
                                {$aItem.name|shorten:35:'...'}
                            </a>
                        </td>

                        <td class="t_center">
                            <a href="{permalink module='ynsocialstore.store' id=$aItem.store_id title=$aItem.store_name}">
                                {$aItem.store_name|shorten:35:'...'}
                            </a>
                        </td>

                        <td class="t_center w100">
                            {if Phpfox::isPhrase($this->_aVars['aItem']['category_name'])}
                                {_p var=$aItem.category_name}
                            {else}
                                {$aItem.category_name|convert}
                            {/if}
                        </td>

                        <td class="t_center">
                            {$aItem.product_price}
                        </td>

                        <td class="t_center w160">
                            {$aItem.time_stamp|date:'core.global_update_time'}
                        </td>

                        <td class="t_center w100">
                            {_p var=$aItem.product_status}
                        </td>

                        <td class="t_center w100">
                            {_p var=$aItem.store_status}
                        </td>

                        <td class="t_center w40" id="ynstore_feature_product_{$aItem.product_id}">
                            <div class="js_item_is_active" style="{if !$aItem.is_featured}display:none;{/if}">
                                <a href="javascript:void(0)" onclick="manageproducts.featureProduct({$aItem.product_id}, {$aItem.user_id}, '{$aItem.product_status}', {$aItem.is_featured}); return false;" class="js_item_active_link" title="{_p var='Deactivate'}"></a>
                            </div>
                            <div class="js_item_is_not_active" style="{if $aItem.is_featured}display:none;{/if}">
                                <a href="javascript:void(0)" onclick="manageproducts.featureProduct({$aItem.product_id}, {$aItem.user_id}, '{$aItem.product_status}', {$aItem.is_featured}); return false;" class="js_item_active_link" title="{_p var='Deactivate'}"></a>
                            </div>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>

        <div class="panel-footer t_right">
            <input type="button" id="delete_selected" name="delete[submit]" value="{_p var='ynsocialstore.delete_selected'}" class="delete btn btn-danger sJsCheckBoxButton disabled" disabled onclick="manageproducts.confirmDeleteProducts('stores_list');"/>
        </div>
    </div>
</form>
{pager}
{literal}
<style>
    .clickable {
        cursor: pointer;
    }
</style>
<script type="text/javascript">
    function ynsocialstoreSortBy(ele) {
        var id = ele.getAttribute('data-value');
        var sort_direction = $(id).val();
        $('#manage_product_form input[type=hidden]').val('');
        if (sort_direction == 'asc') {
            $(id).val('desc');
        } else {
            $(id).val('asc');
        }

        $('#manage_product_form').submit();
    };
</script>
{/literal}
{else}
<div class="alert alert-info">
    {_p var='ynsocialstore.no_results'}
</div>
{/if}