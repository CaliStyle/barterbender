<?php 
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
<form method="post" action="{url link='admincp.ecommerce.category.add'}" enctype="multipart/form-data">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='ecommerce_category_detail'}
            </div>
        </div>
        <div class="panel-body">
            {if $bIsEdit}
                <div><input type="hidden" name="id" value="{$aForms.category_id}" /></div>
                <div><input type="hidden" name="val[edit_id]" value="{$aForms.category_id}" /></div>
                <div><input type="hidden" name="val[name]" value="{$aForms.title}" /></div>
            {/if}

            <div class="form-group">
                <label for="">{phrase var='parent_category'}:</label>
                <select name="val[parent_id]" id="" class="form-control">
                    <option value="">{phrase var='parent_category'}</option>
                    {$sOptions}
                </select>
            </div>

            <div class="form-group">
                <label for="">{phrase var='icon'}</label>
                {if $bIsEdit && !empty($aForms.image_path)}
                    <div id="js_category_icon">
                        {img file=$aForms.image_path path='core.url_pic' server_id=$aForms.server_id suffix='_16'}
                        <div class="extra_info">
                            {phrase var='click_here_to_change_this_icon'}
                        </div>
                    </div>
                {/if}
                <div id="js_category_icon_upload" style="{if $bIsEdit && !empty($aForms.image_path)}display:none;{/if}">
                    <input type="file" name="image" size="30" />{if $bIsEdit && !empty($aForms.image_path)} - <a href="#" onclick="$('#js_category_icon_upload').hide(); $('#js_category_icon').show(); return false;">{phrase var='cancel'}</a>{/if}
                    <div class="extra_info">
                        {phrase var='you_can_upload_a_jpg_gif_or_png_file'}
                    </div>
                </div>
            </div>

            <div class="form-group">
                {field_language required=true phrase='title' label='title' field='name' format='val[name_' size=30 maxlength=100}
            </div>
        </div>

        <div class="panel-footer">
            <input type="submit" value="{phrase var='submit'}" class="btn btn-primary">
        </div>
    </div>
</form>