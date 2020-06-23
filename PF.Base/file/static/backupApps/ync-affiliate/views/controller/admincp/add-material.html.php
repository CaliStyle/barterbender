<?php
/**
 * Created by PhpStorm.
 * User: dai
 * Date: 20/01/2017
 * Time: 18:44
 */

defined('PHPFOX') or exit('NO DICE!');
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {if $bIsEdit}
                {_p var='edit_code'}:
            {else}
                {_p var='add_code'}:
            {/if}
        </div>
    </div>
    <form method="post" enctype="multipart/form-data" id="yna_form_add_material" action="{url link='current'}">
        <div class="panel-body">
            {if $bIsEdit}
            <div><input type="hidden" name="idMaterial" value="{$iEditId}" /></div>
            {/if}
            <div class="form-group">
                <lable for="ynaf_material_size">
                    {_p var='size_l'}
                </lable>
                <select name="" class="form-control" id="ynaf_material_size">
                    <option value="{_p var='full_banner_468_60'}" data-width="468" data-height="60" {if isset($aForms) && $aForms.material_width == '468' && $aForms.material_height == '60'}selected{/if}>{_p var='full_banner_468_60'}</option>
                    <option value="{_p var='half_banner_234_60'}" data-width="234" data-height="60" {if isset($aForms) && $aForms.material_width == '234' && $aForms.material_height == '60'}selected{/if}>{_p var='half_banner_234_60'}</option>
                    <option value="{_p var='leader_board_728_90'}" data-width="728" data-height="90" {if isset($aForms) && $aForms.material_width == '728' && $aForms.material_height == '90'}selected{/if}>{_p var='leader_board_728_90'}</option>
                    <option value="{_p var='vertical_banner_120_240'}" data-width="120" data-height="240" {if isset($aForms) && $aForms.material_width == '120' && $aForms.material_height == '240'}selected{/if}>{_p var='vertical_banner_120_240'}</option>
                    <option value="{_p var='skyscraper_120_600'}" data-width="120" data-height="600" {if isset($aForms) && $aForms.material_width == '120' && $aForms.material_height == '600'}selected{/if}>{_p var='skyscraper_120_600'}</option>
                    <option value="{_p var='button_120_90'}" data-width="120" data-height="90" {if isset($aForms) && $aForms.material_width == '120' && $aForms.material_height == '90'}selected{/if}>{_p var='button_120_90'}</option>
                    <option value="custom" data-width="" data-height="">{_p var='Custom'}</option>
                </select>
            </div>
            <div class="form-group">
                <label for="image">
                    {required}{_p var='image'}
                </label>
                {if isset($aForms.image_path)}
                    <div>
                        {img path='core.url_pic' file="yncaffiliate/"$aForms.image_path server_id=$aForms.server_id suffix="_"$aForms.material_width"_"$aForms.material_height style="max-width:100%"}
                        <div class="p_4"></div>
                    </div>
                {/if}
                <input type="file" class="form-control" name="image" id="image">
                <div class="extra_info">
                    {_p var='image_will_be_crop_with_size_as_width_and_height_in_below_fields'}
                </div>
            </div>
            <div class="form-group">
                <label for="material_width">
                    {required}{_p var='width_px'}
                </label>
                    <input type="text" class="form-control" name='val[material_width]' maxlength="10" id="material_width" value="{value type='input' id='material_width' default='468'}"/>
            </div>
            <div class="form-group">
                <label for="material_height">
                    {required}{_p var='height_px'}
                </label>
                <input type="text" class="form-control" name='val[material_height]' maxlength="10" id="material_height" value="{value type='input' id='material_height' default='60'}"/>
            </div>
            <div class="form-group">
                <label for="material_name">
                    {required}{_p var='title'}
                </label>
                <input type="text" class="form-control" name='val[material_name]' maxlength="150" id="material_name" value="{value type='input' id='material_name' default='Full Banner(468x60)'}"/>
            </div>
            <div class="form-group">
                <label for="link">
                    {required}{_p var='link'}
                </label>
                <input type="text" class="form-control" name="val[link]" id="link" value="{value type='input' id='link'}">
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" value="{_p var='Save'}" class="btn btn-primary" />
            <input type="button" value="{_p var='Cancel'}" class="btn btn-default" onclick="if($('.js_box_content').length){l}js_box_remove(this);{r}else{l}window.location='{url link='admincp.yncaffiliate.affiliate-materials'}'{r}"/>
        </div>
    </form>
</div>
{literal}
<script type="text/javascript">
    $Behavior.onChangeCodeSizes = function(){
        $('#ynaf_material_size').on('change',function(){
            if($(this).val() != "custom"){
                var data = $(this).val(),
                    width = $(this).find(':selected').data('width'),
                    height = $(this).find(':selected').data('height');
                $('#material_width').val(width);
                $('#material_height').val(height);
                $('#material_name').val(data);

            }
            else{
                $('#material_width').val('');
                $('#material_height').val('');
                $('#material_name').val('');
            }
        });
    }
</script>
{/literal}