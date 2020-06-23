<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright        [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Document
 */
defined('PHPFOX') or exit('NO DICE!');
?>

{if empty($scribd_error_message)}
<script>
    var no_upload_file_message = "{phrase var='select_a_document_file_to_upload'}";
</script>
<div class="main_break">
    {$sCreateJs}
    {if !empty($file_error_message)}
    <div id="jp_document_file_message" class="error_message" style="display:block">{$file_error_message}</div>
    {/if}
    <form method="post" enctype="multipart/form-data" action="{url link='current'}" id="core_js_document_form" onsubmit="{$sGetJsForm}">
        {if $bIsEdit}
        <div><input type="hidden" name="id" value="{$aForms.document_id}"/></div>
        {/if}
        <div id="js_custom_privacy_input_holder">
            {if $bIsEdit}
            {module name='privacy.build' privacy_item_id=$aForms.document_id privacy_module_id='document'}
            {/if}
        </div>
        {plugin call='document.template_controller_add_hidden_form'}

        <div class="form-group">
            <label for="title">{required}{phrase var='title'}:</label>
            <input type="text" class="form-control" name="val[title]" value="{value type='input' id='title'}" id="title" size="40" maxlength="128"/>
        </div>

        {plugin call='document.template_controller_add_textarea_start'}

        <div class="form-group">
            <label for="text">{required}{phrase var='description'}:</label>
            {editor id='text'}
        </div>

        <div class="form-group">
            <label>{required}{phrase var='categories'}:</label>
            {$sCategories}
        </div>

        <div class="form-group-follow">
            <label>{required}{phrase var='document_file'}:</label>
            {if $bIsEdit}
            <label> {$aForms.document_file_name} </label>
            {else}
                {if $max_file_size}
                    <input type="hidden" id="max_file_size" name="MAX_FILE_SIZE" value="{$max_file_size}"/>
                {/if}
                <input class="form-control" name="uploadedfile" accept=".doc, .docx, .ppt, .pptx, .pps, .xls, .xlsx, .pdf, .ps, .odt, .odp, .sxw, .sxi, .txt, .rtf" id="uploadedfile" type="file"/><br/>
            {/if}
            <p class="help-block">
                {phrase var='document_file_format_support'}<br/>
                {phrase var='max_file_sie_maxsize_mb' maxsize=$max_file_size_mb}
            </p>

        </div>
        <div class="form-group">
            {if $bIsEdit}
                {module name='core.upload-form' type='document' current_photo=$aForms.current_image id=$aForms.document_id}
            {else}
                {module name='core.upload-form' type='document'}
            {/if}
        </div>
        {if Phpfox::isModule('tag') && Phpfox::getUserParam('document.can_add_tags_on_documents')}
            {module name='tag.add' sType=document}
        {/if}

        {if Phpfox::getUserParam('document.can_set_allow_download')}
        <div class="form-group form-group-follow document-toggle-add">
            <div class="privacy-block-content">
                <div class="item_is_active_holder">
                <span class="js_item_active item_is_active">
                    <input type="radio" name="val[allow_download]" value="1" {value type='radio' id='allow_download' default='1'}> {phrase var='core.yes'}
                </span>
                <span class="js_item_active item_is_not_active">
                    <input type="radio" name="val[allow_download]" value="0" {value type='radio' id='allow_download' default='0' selected='true' }> {phrase var='core.no'}</span>
                </div>
                <div class="inner">
                    <label>{phrase var='download_enabled'}:</label>
                    <div class="extra_info">
                        {phrase var='enabling_this_option_will_allow_others_the_rights_to_download_this_document'}
                    </div>
                </div>
            </div>
        </div>
        {/if}

        <div class="form-group form-group-follow document-toggle-add attach-email">
            <div class="privacy-block-content">
                <div class="item_is_active_holder">
                <span class="js_item_active item_is_active">
                    <input type="radio" name="val[allow_attach]" value="1" {value type='radio' id='allow_attach' default='1' }>
                    {phrase var='core.yes'}
                </span>
                <span class="js_item_active item_is_not_active">
                    <input type="radio" name="val[allow_attach]" value="0" {value type='radio' id='allow_attach' default='0' selected='true'}>
                    {phrase var='core.no'}
                </span>
                </div>
                <div class="inner">
                    <label>{phrase var='allow_email_attachment'}:</label>
                    <div class="extra_info">
                        {phrase var='enabling_this_option_will_allow_others_the_rights_to_attach_this_document'}
                    </div>
                </div>
            </div>

        </div>

        <div class="form-group">
            <label for="document_license">{phrase var='license_associated'}:</label>
            <select name="val[document_license]" class="form-control" id="document_license">
                <option value="0" {value type='select' id='document_license' default='0' }>
                    {phrase var='unspecified'}
                </option>
                {foreach from=$license_list item=license}
                    <option value="{$license.license_id}" {value type='select' id='document_license' default=$license.license_id }>{$license.license_name}</option>
                {/foreach}
            </select>
        </div>

        {if $document_access_show && $bUseScribdViewer}
        <div class="form-group">
            <label for="visibility">{phrase var='visibility'}:</label>
            <select name="val[visibility]" class="form-control" id="visibility">
                <option value="0" {value type='select' id='visibility' default='0' }>{phrase
                    var='private_on_this_site'}
                </option>
                <option value="1" {value type='select' id='visibility' default='1' }>{phrase var='public_on_scribd'}
                </option>
            </select>
        </div>
        {/if}

        {if $sModule == 'document'}
            {if Phpfox::isModule('privacy')}
            <div class="form-group">
                <label for="">{phrase var='privacy'}:</label>
                {module name='privacy.form' privacy_name='privacy' privacy_info='document.control_who_can_see_this_document' default_privacy='document.default_privacy_setting'}
            </div>
            {/if}
        {/if}

        {plugin call='document.template_controller_add_textarea_end'}

        <div class="form-group">
        {plugin call='document.template_controller_add_submit_buttons'}
            <button type="submit" id="submit_document" class="btn btn-primary" name="val[{if $bIsEdit}update{else}publish{/if}]">{if $bIsEdit}{phrase var='update'}{else}{phrase var='publish'}{/if}</button>
            <button type="button" id="cancel_document"
                    class="button button_off btn btn-default" onclick="javascript:history.back()">{phrase var='cancel'}
            </button>
        </div>
    </form>

    {if Phpfox::getParam('core.display_required')}
    <div class="help-block">
        {required} {phrase var='core.required_fields'}
    </div>
    {/if}
</div>
{else}
<div id="jp_document_file_message" class="error_message" style="display:block">{$scribd_error_message}</div>
{/if}
{literal}
<script type="text/javascript">
    if (window.jQuery) {
        $(document).ready(function () {
            $('.global_attachment_list li').eq(2).hide();
            $('.global_attachment_list li').eq(3).hide();
        });
    }

</script>
{/literal}