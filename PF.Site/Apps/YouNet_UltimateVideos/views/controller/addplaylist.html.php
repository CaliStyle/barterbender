<?php
defined('PHPFOX') or exit('NO DICE!');

?>
{if isset($sError) && !empty($sError)}
<div>{$sError}</div>
{else}
{$sCreateJs}
<div id="js_ultimatevideo_playlist_block_detail" data-addjs="{$corePath}/assets/jscript/add_playlist.js"
     data-validjs="{$corePath}/assets/jscript/jquery.validate.js" class="js_photo_block page_section_menu_holder"
     {if !empty($sActiveTab) && $sActiveTab != 'detail'}style="display:none;"{/if}>
    <form method="post" enctype="multipart/form-data" action="{url link='current'}" id="ynuv_add_playlist_form"
          class="ultimatevideo-form-add">
        <div><input type="hidden" name="val[current_tab]" value="" id="current_tab"></div>
        <div id="js_custom_privacy_input_holder">
            {if $bIsEdit && (!isset($sModule) || empty($sModule))}
                {module name='privacy.build' privacy_item_id=$aForms.playlist_id privacy_module_id='ultimatevideo_playlist'}
            {/if}
        </div>
        {if $bIsEdit}<input type="hidden" id="ynuv_playlistid" name="val[playlist_id]"
                            value="{$aForms.playlist_id}">{/if}
        <div class="">
            <div class="p-flex-wrapper flex-wrap p-mx--1">
                <div class="form-group col-xs-12 col-sm-6 px-1 p-flex-item">
                    <label>{_p('name')} <span class="p-text-danger">{required}</span></label>
                    <div>
                        <input class="form-control" type="text" name="val[title]" id="ynuv_add_playlist_title"
                               value="{value type='input' id='title'}"/>
                    </div>
                </div>
                {if !empty($sCategories)}
                    <div class="form-group js_core_init_selectize_form_group col-xs-12 col-sm-6 px-1 p-flex-item">
                        <label for="category">{_p('Category')}</label>
                        <div>
                            {module name='ultimatevideo.add_category_list'}
                        </div>
                    </div>
                {/if}
            </div>
        </div>
        {if $bIsEdit}
            {if !empty($aForms.image_path)}
                {module name='core.upload-form' type='ultimatevideo' current_photo=$aForms.current_image id=$aForms.playlist_id}
                <input type="hidden" name="val[image_path]" value="{value type='input' id='image_path'}">
                <input type="hidden" name="val[image_server_id]" value="{value type='input' id='image_server_id'}">
            {else}
                {module name='core.upload-form' type='ultimatevideo' current_photo=''}
            {/if}
        {/if}
        <div class="form-group">
            <label>{_p('description')}</label>
            {editor id='description'}
        </div>
        {if empty($sModule) && Phpfox::isModule('privacy')}
            <div class="form-group">
                <label for="privacy">{_p('privacy')}</label>
                {module name='privacy.form' privacy_name='privacy' privacy_info='control_who_can_see_this_playlist' default_privacy='anyone'}
            </div>
        {/if}
        {if !$bIsEdit}
            <input type="submit" value="{_p('Submit')}" name="val[submit]" id="ynuv_add_submit"
                   class="button btn btn-primary">
        {else}
            <input type="submit" value="{_p('Update')}" name="val[submit]" id="ynuv_add_update"
                   class="button btn btn-primary">
        {/if}
    </form>
</div>
{if $bIsEdit}
    <div id="js_ultimatevideo_playlist_block_video" class="js_photo_block page_section_menu_holder"
         {if empty($sActiveTab) || $sActiveTab != 'video'}style="display:none;"{/if}>
        {template file="ultimatevideo.block.edit_playlist_video"}
    </div>
{/if}
{/if}
