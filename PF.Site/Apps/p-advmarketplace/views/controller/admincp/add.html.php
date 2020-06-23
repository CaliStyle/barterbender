<?php

defined('PHPFOX') or exit('NO DICE!');

?>
{literal}
<script type="text/javascript" language="javascript">
    $Behavior.advmarket_deleteImage = function () {
        $(".delete-image").click(function (evt) {
            evt.preventDefault();
            var review = $(this);

            $Core.jsConfirm({}, function () {
                $.ajaxCall("advancedmarketplace.deleteCategoryImage", "category_id=" + review.data("category-id"));
            }, function () {
            });

            return false;
        });
        $(".parent-id").change(function () {
            var val = $(".parent-id").val();
            if(val != 0){
                $("#image_select").css("display","none");
            }
            else{
                $("#image_select").css("display","block");
            }
            return false;
        });
    }
</script>
{/literal}
<form method="post" action="{url link='admincp.advancedmarketplace.add'}" enctype="multipart/form-data">
    {if $bIsEdit}
    <div><input type="hidden" name="id" value="{$aForms.category_id}"/></div>
    <div><input type="hidden" name="val[name]" value="{$aForms.name}"/></div>
    {/if}

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='advancedmarketplace.advancedmarketplace_category_details'}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group js_core_init_selectize_form_group">
                <label for="">{phrase var='advancedmarketplace.parent_category'}:</label>
                <select name="val[parent_id]" class="form-control parent-id">
                    {if !$bSubCategory}
                    <option value="0">{phrase var='advancedmarketplace.select'}:</option>
                    {/if}
                    {$sOptions}
                </select>
            </div>

            {field_language phrase='name' label='name' field='name' format='val[name_' size=30 maxlength=100}

            {if !$bIsEdit || ($bIsEdit && !$bSubCategory)}
            <div class="form-group" id="image_select">
                <label for="image">{ _p var='Image' }</label>
                {if isset($aForms.image_path) && $aForms.image_path}
                <div class="category-image">
                    <a href="{img server_id=$aForms.server_id path='advancedmarketplace.url_pic' file=$aForms.image_path return_url=true}"
                       class="thickbox">
                        {if $aForms.server_id == '-1'}
                            {img path='core.path_actual' file='PF.Site/Apps/p-advmarketplace/assets/image/default/'.$aForms.image_path}
                        {else}
                            {img server_id=$aForms.server_id path='advancedmarketplace.url_pic' file=$aForms.image_path
                            suffix='_200' max_width='200' max_height='200'}
                        {/if}
                    </a>
                    <a class="btn btn-danger delete-image" data-category-id="{$aForms.category_id}">
                        <i class="fa fa-trash"></i>
                    </a>
                </div>

                <div style="clear: both;"></div>
                {/if}
                <input type="file" name="file" id="file" class="form-control">
                <p class="help-block">
                    {_p var='upload_image_for_category'}
                </p>
            </div>
            {/if}

        </div>

        <div class="panel-footer">
            <input type="submit" value="{phrase var='advancedmarketplace.submit'}" class="btn btn-primary"/>
        </div>
    </div>
</form>