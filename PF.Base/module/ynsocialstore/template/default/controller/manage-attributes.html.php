<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/31/16
 * Time: 9:09 AM
 */
?>
{if !empty($sError)}
    {$sError}
{else}
<div class="ynstore-manage-attributes">
    <div class="yn-alert-warning" role="alert">
        <i>{_p var='ynsocialstore.attribute_plan_might_override_your_stock_inventory_that_you_provided_before_at_create_section'}</i>
    </div>

    <form method="post" action="{url link='current'}" id="js_add_attribute_form" name="js_add_attribute_form">
        <input type="hidden" name="valAttr[product_id]" value="{$iEditId}">
        <div class="form-group">
            <label>
                {_p var='ynsocialstore.attribute_title'}{required}
                <span>({_p var='ynsocialstore.maximum_limit_characters' limit=16})</span>
            </label>
            <input type="text" maxlength="16" required name="valAttr[title]" placeholder="{_p var='ynsocialstore.color_size_options'}" id="title" value="{if isset ( $aForms.title )}{$aForms.title}{/if}" />
        </div>

        <div><b>{_p var='ynsocialstore.style_to_show'}</b></div>

        <div class="radio">
            <label>
                <input type="radio" name="valAttr[style]" checked value="1" {value type='radio' id='style' default='1'}/>
                {_p var='ynsocialstore.text_only'}&nbsp;
                <span>({_p var='ynsocialstore.show_attributes_as_text_ex_red_green'})</span>
            </label>
        </div>

        <div class="radio">
            <label>
                <input type="radio" name="valAttr[style]" value="2" {value type='radio' id='style' default='2'}/>
                {_p var='ynsocialstore.image_only'}&nbsp;
                <span>({_p var='ynsocialstore.show_each_option_for_an_image_if_the_image_is_not_available_it_s_be_shown_with_default_image'})</span>
            </label>
        </div>

        <div class="radio">
            <label>
                <input type="radio" name="valAttr[style]" value="3" {value type='radio' id='style' default='3'}/>
                {_p var='ynsocialstore.image_with_text'}
           </label>
        </div>

        <div class="yn-btn-group">
            <input class="btn btn-primary" type="submit" id="submit" name="valAttr[submit]" value="{_p var='ynsocialstore.create_attribute'}" />
            <input class="btn btn-default" type="reset" value="{_p var='ynsocialstore.reset'}" />
         </div>
    </form>

    <div class="yn-help-block">
        <p><label>{_p('Name/Text/Description')}</label> : {_p('Something you want to tell about this element. Example: Red, Long, Big,... or a code')}</p>
        <p><label>{_p var='image'}</label> : {_p('If you want to show image for attribute, then upload an image then check the box "Show image"')}</p>
        <p><label>{_p('Quantity')}</label> : {_p('Number of item with this certain attribute that you want to sell')}</p>
        <p><label>{_p('Affect Price')}</label> : {_p('Offer a different price for items with this certain attribute')}</p>
    </div>

    {if !empty($aForms.title) && $iAvailable >= 0}
    <div class="ynstore-add-btn clearfix">
        <a href="#" class="btn btn-link pull-right" onclick="tb_show('{_p var='ynsocialstore.add_an_element'}', $.ajaxBox('ynsocialstore.addAttributeElement', 'height=300&width=500&action=add&product_id={$iEditId}')); return false;">
            <i class="ico ico-plus"></i>
            {_p var='ynsocialstore.add_an_element'}
        </a>
    </div>
    {/if}

    {if count($aAttributeElements)}
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>{_p('Id')}</th>
                    <th>{_p('Name / Description')}</th>
                    <th>{_p('Image / Color')}</th>
                    <th>{_p('Remaining Qty / Total Qty')}</th>
                    <th>{_p('Affect Price')}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$aAttributeElements key=iKey item=aElement}
            <tr>
                <input type="hidden" name="val[attribute][]" value="{$aElement.attribute_id}">
                <td class="id">{$aElement.attribute_id}</td>
                <td>{$aElement.title}</td>
                <td>
                    {if {$aElement.color}
                    <div class="ynstore-img" style="background:{$aElement.color}; width:65px;height: 50px"></div>
                    {else}
                    <div class="ynstore-img">
                        {img server_id=$aElement.server_id path='core.url_pic' file=$aElement.image_path suffix='_90_square' title=$aElement.title}
                    </div>
                    {/if}
                </td>
                <td>
                    {if $aElement.quantity}
                        {$aElement.remain}/{$aElement.quantity}
                    {else}
                        {_p var='ynsocialstore.unlimited'}
                    {/if}
                </td>
                <td>
                    {$aElement.price}
                </td>
                <td align="right">
                    <a href="javascript:void(0);" class="ynstore-action-btn ynstore-edit" onclick="tb_show('{_p(\'Edit \')}{$aElement.title}', $.ajaxBox('ynsocialstore.addAttributeElement', 'height=300&width=500&element_id={$aElement.attribute_id}&product_id={$iEditId}')); return false;">
                        <i class="ico ico-pencil"></i>
                        {_p('Edit')}
                    </a>
                    <a href="javascript:void(0);" class="ynstore-action-btn ynstore-delete" onclick="ynsocialstore.confirmDeleteElementAttr({$aElement.attribute_id},{$iEditId})">
                        <i class="ico ico-close"></i>
                        {_p var='ynsocialstore.delete'}
                    </a>
                </td>
            </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    {else}
    {/if}
</div>
{/if}
