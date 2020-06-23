<?php
/**
 * Created by PhpStorm.
 * User: minhhai
 * Date: 10/19/16
 * Time: 09:22
 */
?>
{if !empty($sError)}
    {$sError}
{else}
    <form id="ynstore_manage_product" method="post" action="{url link='ynsocialstore.store.manage-products'}id_{$iStoreId}">
        <!-- Form Header -->
        <div class="form-group">
            <label for="product_title">
                {_p var="ecommerce.product_title"}
            </label>
            <input class="form-control" type="text" name="search[title]" value="{value type='input' id='title'}" id="title" size="50" />
        </div>

        <div class="form-group">
            <label for="categories">
                {_p var="ynsocialstore.categories"}
            </label>
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
            <label for="status">
                {_p var="ynsocialstore.status"}
            </label>
            <select name="search[status]" class="form-control">
                <option value="">{_p var='ynsocialstore.any'}</option>
                <option value="draft"  {value type='select' id='status' default = 'draft'}>{_p var='ynsocialstore.draft'}</option>
                <option value="pending"  {value type='select' id='status' default = 'pending'}>{_p var='ynsocialstore.pending'}</option>
                <option value="running"  {value type='select' id='status' default = 'running'}>{_p var='ynsocialstore.public'}</option>
                <option value="denied"  {value type='select' id='status' default = 'denied'}>{_p var='ynsocialstore.denied'}</option>
                <option value="paused"  {value type='select' id='status' default = 'paused'}>{_p var='ynsocialstore.closed'}</option>
            </select>
        </div>
        <!-- Submit Buttons -->
        <div class="pull-right">
            <button type="submit" id="filter_submit" name="search[submit]" class="btn btn-primary">{_p var='ynsocialstore.search'}</button>
            <a href="{url link='ynsocialstore.add' store=$iStoreId}" class="btn btn-success"><i class="ico ico-plus mr-1"></i> {_p var='ynsocialstore.add_product'}</a>
        </div>
    </form>

    <div id="ynstore_store_manage_product">
    {if count($aProducts) > 0}
        {module name='ynsocialstore.store.list-products'}
    {else}
        {_p var='ynsocialstore.no_results'}
    {/if}
    </div>
{/if}