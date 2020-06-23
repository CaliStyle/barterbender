<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<form id="manage_store_form" method="get" action="{url link='admincp.ynsocialstore.managestore'}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {_p var="ynsocialstore.search_filter"}
            </div>
        </div>

        <div class="panel-body">
            <div>
                <input type="hidden" class="form-control" name="search[sort_owner]" id="sort_owner"  value="{value type='input' id='sort_owner'}">
                <input type="hidden" class="form-control" name="search[sort_name]" id="sort_name"  value="{value type='input' id='sort_name'}">
            </div>
            <div class="form-group">
                <label>{_p var="ynsocialstore.store_title"}</label>
                <input class="form-control" type="text" name="search[title]" value="{value type='input' id='title'}" id="title">
            </div>

            <div class="form-group">
                <label>{_p var='ynsocialstore.store_owner'}</label>
                <input class="form-control" type="text" name="search[owner]" value="{value type='input' id='owner'}" id="owner">
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
                        <option id="{$aCategory.category_id}" {if isset($aForms.category_id) && $aForms.category_id == $aCategory.category_id} selected="selected" {/if} value="{$aCategory.category_id}">{$value_name}</option>
                        {if !empty($aCategory.sub_1)}
                            {foreach from=$aCategory.sub_1 item=aSubCategory}
                                {if Phpfox::isPhrase($this->_aVars['aSubCategory']['title'])}
                                    <?php $this->_aVars['value_name'] = _p($this->_aVars['aSubCategory']['title']) ?>
                                {else}
                                    {assign var='value_name' value=$aSubCategory.title|convert}
                                {/if}
                                <option id="{$aSubCategory.category_id}" {if isset($aForms.category_id) && $aForms.category_id == $aSubCategory.category_id} selected="selected" {/if} value="{$aSubCategory.category_id}">--{$value_name}</option>
                                {if !empty($aSubCategory.sub_1)}
                                    {foreach from=$aSubCategory.sub_1 item=aSubSubCategory}
                                        {if Phpfox::isPhrase($this->_aVars['aSubSubCategory']['title'])}
                                            <?php $this->_aVars['value_name'] = _p($this->_aVars['aSubSubCategory']['title']) ?>
                                        {else}
                                            {assign var='value_name' value=$aSubSubCategory.title|convert}
                                        {/if}
                                        <option id="{$aSubSubCategory.category_id}" {if isset($aForms.category_id) && $aForms.category_id == $aSubSubCategory.category_id} selected="selected" {/if} value="{$aSubSubCategory.category_id}">----{$value_name}</option>
                                    {/foreach}
                                {/if}
                            {/foreach}
                        {/if}
                    {/foreach}
                </select>
            </div>

            <div class="form-group">
                <label>{_p var="ynsocialstore.package"}</label>
                <select name="search[package_id]" class="form-control">
                    <option value="0">{_p var='ynsocialstore.any'}</option>
                    {foreach from = $aPackages item = aPackageItem}
                    <option value="{$aPackageItem.package_id}"
                            {if isset($aForms) && $aForms.package_id == $aPackageItem.package_id}
                            selected="selected"
                            {/if}
                    >
                    {$aPackageItem.name}
                    </option>
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
                    <option value="expired"  {value type='select' id='status' default = 'expired'}>{_p var='ynsocialstore.expired'}</option>
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
                {_p var="ynsocialstore.stores"}
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <!-- Table rows header -->
                <thead>
                    <tr>
                        <th class="t_center w40"><input type="checkbox" name="val[id]" value="" id="js_check_box_all" class="main_checkbox" /></th>
                        <th class="t_center w40"></th>
                        <th class="t_center clickable" data-value="#sort_name" onclick="ynsocialstoreSortBy(this)">{_p var='ynsocialstore.name'}</th>
                        <th class="t_center w220">{_p var='ynsocialstore.main_categories'}</th>
                        <th class="t_center w100">{_p var='ynsocialstore.status'}</th>
                        <th class="t_center w100 clickable" data-value="#sort_owner" onclick="ynsocialstoreSortBy(this)">{_p var='ynsocialstore.store_owner'}</th>
                        <th class="t_center w100">{_p var='ynsocialstore.featured'}</th>
                        <th class="t_center w180">{_p var='ynsocialstore.package'}</th>
                    </tr>
                </thead>

                <tbody>
                {foreach from=$aList key=iKey item=aItem}
                    <tr id="js_row{$aItem.store_id}" class="checkRow{if is_int($iKey/2)} tr{else}{/if}">
                        <td class="t_center w40"><input type="checkbox" name="id[]" class="checkbox" value="{$aItem.store_id}" id="js_id_row{$aItem.store_id}" /></td>
                        <!-- Options -->
                        <td class="t_center w40">
                            <a href="#" class="js_drop_down_link" title="Options"></a>
                            <div class="link_menu">
                                <ul>
                                    <li><a href="{url link='ynsocialstore.store.add' id=$aItem.store_id}">{_p var='ynsocialstore.edit'}</a></li>
                                    <li><a href="javascript:void(0)" onclick="managestores.confirmdeleteStore({$aItem.store_id}); return false;">{_p var='ynsocialstore.delete'}</a></li>
                                    {if $aItem.status == 'pending'}
                                        <li><a href="javascript:void(0)" onclick="managestores.denyStore({$aItem.store_id}, '{$aItem.status}'); return false;">{_p var='ynsocialstore.deny'}</a></li>
                                        <li><a href="javascript:void(0)" onclick="managestores.approveStore({$aItem.store_id}, '{$aItem.status}'); return false;">{_p var='ynsocialstore.approve'}</a></li>
                                    {elseif $aItem.status == 'public'}
                                        <li><a href="javascript:void(0)" onclick="managestores.closeStore({$aItem.store_id}, {$aItem.user_id}, '{$aItem.status}'); return false;">{_p var='ynsocialstore.close'}</a></li>
                                    {elseif $aItem.status == 'closed'}
                                        <li><a href="javascript:void(0)" onclick="managestores.reopenStore({$aItem.store_id}, {$aItem.user_id}, '{$aItem.status}'); return false;">{_p var='ynsocialstore.reopen'}</a></li>
                                    {elseif $aItem.status == 'denied'}
                                        <li><a href="javascript:void(0)" onclick="managestores.approveStore({$aItem.store_id}, '{$aItem.status}'); return false;">{_p var='ynsocialstore.approve'}</a></li>
                                    {/if}
                                </ul>
                            </div>
                        </td>

                        <td class="t_center">
                            <a href="{permalink module='ynsocialstore.store' id=$aItem.store_id title=$aItem.name}">
                                {$aItem.name|shorten:35:'...'}
                            </a>
                        </td>

                        <td class="t_center w220">
                            {foreach from=$aItem.categories key=iKey item=aCatItem}
                                {if Phpfox::isPhrase($this->_aVars['aCatItem']['title'])}
                                    <?php $this->_aVars['value_name'] = _p($this->_aVars['aCatItem']['title']) ?>
                                {else}
                                    {assign var='value_name' value=$aCatItem.title|convert}
                                {/if}
                                {if $iKey != 0}, {/if}{$value_name}
                            {/foreach}
                        </td>

                        <td class="t_center w100">
                            {_p var='ynsocialstore.'.$aItem.status}
                        </td>

                        <td class="t_center w100">
                            {$aItem|user}
                        </td>
                        <td class="t_center w100" id="ynstore_feature_store_{$aItem.store_id}">
                            {if $aItem.module_id != 'pages'}
                                <div class="js_item_is_active" style="{if !$aItem.is_featured}display:none;{/if}">
                                    <a href="javascript:void(0)" onclick="managestores.featureStore({$aItem.store_id}, {$aItem.user_id}, '{$aItem.status}', {$aItem.is_featured}); return false;" class="js_item_active_link" title="{_p var='Deactivate'}"></a>
                                </div>
                                <div class="js_item_is_not_active" style="{if $aItem.is_featured}display:none;{/if}">
                                    <a href="javascript:void(0)" onclick="managestores.featureStore({$aItem.store_id}, {$aItem.user_id}, '{$aItem.status}', {$aItem.is_featured}); return false;" class="js_item_active_link" title="{_p var='Deactivate'}"></a>
                                </div>
                            {/if}
                        </td>
                        <td class="t_center w180">
                            {$aItem.package}
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
        <div class="panel-footer t_right">
            <input type="button" id="delete_selected" name="delete[submit]" value="{_p var='ynsocialstore.delete_selected'}" class="delete btn btn-danger sJsCheckBoxButton disabled" disabled="true" onclick="managestores.confirmDeleteStores('stores_list');"/>
        </div>
    </div>
</form>
{pager}
<span id="ynsocialstore_loading" style="display: none;">{img theme='ajax/add.gif'}</span>
{literal}
<style>
    .clickable {
        cursor: pointer;
    }
</style>
<script type="text/javascript">
    function ynsocialstoreSortBy(ele) {
        var id = ele.getAttribute('data-value');
        var sort_name = $(id).val();
        if (sort_name == 'name_asc') {
            $(id).val('name_desc');
        } else {
            $(id).val('name_asc');
        }

        $('#manage_store_form').submit();
    };
</script>
{/literal}
{else}
<div class="alert alert-info">
    {_p var='ynsocialstore.no_results'}
</div>
{/if}



