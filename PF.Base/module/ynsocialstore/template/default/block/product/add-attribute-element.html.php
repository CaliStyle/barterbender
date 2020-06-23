<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 11/3/16
 * Time: 3:42 PM
 */
?>
<form method="post" action="{url link='ynsocialstore.manage-attributes'}?id={$iProductId}" enctype="multipart/form-data" id="js_add_element_page" class="ajax_upload ajax_form">
    <input type="hidden" name="val[attribute_id]" id="attribute_id" value="{$iElementId}">
    <input type="hidden" name="val[product_id]" id="product_id" value="{$iProductId}">

    <div class="form-group">
        <label>{_p('Name')}</label>({_p var='ynsocialstore.maximum_limit_characters' limit=16})
        <input type="text" required="required" maxlength="16" class="form-control" name="val[name]" id="name" value="{if isset($aElement.title)}{$aElement.title}{/if}">
    </div>

     <div class="form-group">
        <div class="radio-inline">
            <label>
                <input type="radio" onclick="$('#js_ynstore_type_image').show(); $('#js_ynstore_type_color').hide();" name="val[type]" {if empty($aElement.color)}checked{/if} value="0" {value type='radio' id='type' default='0'}/>
                {_p('Upload an image')}
            </label>
        </div>

        <div class="radio-inline">
            <label>
                <input type="radio" onclick="$('#js_ynstore_type_image').hide(); $('#js_ynstore_type_color').show();" name="val[type]" {if !empty($aElement.color)}checked{/if} value="1" {value type='radio' id='type' default='1'}/>
                {_p('Color')}
            </label>
        </div>
    </div>

    <div class="form-group ynstore-type" id="js_ynstore_type_image" {if !empty($aElement.color)}style="display: none"{/if}>
        <input type="file" name="image" id="image" accept=".jpg,.png,.gif">
    </div>

    <div class="form-group ynstore-type ynstore-color-picker dont-unbind-children" id="js_ynstore_type_color" {if empty($aElement.color)}style="display: none"{/if}>
        <label>{_p('Color')}</label>
        <input readonly class="_colorpicker form-control" data-old="{if isset($aElement.color)}{$aElement.color}{/if}" autocomplete="off" type="text" name="val[color]" value="{if isset($aElement.color)}{$aElement.color}{/if}" id="color">
        <div class="_colorpicker_holder"></div>
    </div>

    <div class="form-group">
        <label>{_p('Quantity')}</label> ({_p('Number of item with this attribute that you want to sell')})
        {if $iAvailable <= 0}
        <select id="quantity" name="val[quantity]" class="form-control">
            <option value="0" {if empty($aElement.quantity)}selected{/if} {value type='select' id='quantity' default = '0'}>{_p('Unlimited')}</option>
            <option value="1" {if !empty($aElement.quantity)}selected{/if} {value type='select' id='quantity' default = '1'}>{_p('Amount number')}</option>
        </select>
        {else}
        <input type="hidden" name="val[quantity]" value="1">
        {/if}

        <input type="text" class="form-control" name="val[amount]" id="amount" {if !empty($aElement.remain)}value="{$aElement.remain}" {else} value="0"{/if} {if $iAvailable <= 0 && empty($aElement.quantity)}disabled{/if}>
    </div>

    <div class="form-group">
        <label>{_p('Price Affect')}</label>
        <input type="text" class="form-control" name="val[price]" id="price" value="{if isset($aElement.price)}{$aElement.price}{else}0{/if}">
    </div>

    <div class="ynstore-button">
        <button type="submit" class="btn btn-primary" name="update_element" id="update_element">{_p('Save')}</button>
        <button type="cancel" onclick="js_box_remove(this); return false;" class="btn btn-default" name="cancel" id="cancel">{_p('Cancel')}</button>
    </div>
</form>
{literal}
<script type="text/javascript">
    ynsocialstore.initAddAttributeElement({/literal}{$iAvailable}{literal});
</script>
{/literal}
