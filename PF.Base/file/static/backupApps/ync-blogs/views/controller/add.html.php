<?php
defined('PHPFOX') or exit('NO DICE!');
?>

{if isset($aForms.blog_id)}
<div>
    <a class="page_section_menu_link" href="{permalink module='ynblog' id=$aForms.blog_id title=$aForms.title}" title="{_p var='view_blog'}"></a>
</div>
{/if}

<script src="<?php echo Phpfox::getParam('core.path_actual').'PF.Site/Apps/ync-blogs/assets/jscript/tinymce/tinymce.min.js'; ?>"></script>

{literal}
<script type="text/javascript">
    function plugin_addFriendToSelectList()
    {
        $('#js_allow_list_input').show();
    }
</script>
{/literal}

<div class="main_break">
    {$sCreateJs}
    <form method="post" action="{url link='ynblog.add'}" id="advancedblog_js_blog_form" onsubmit="{$sGetJsForm}" enctype="multipart/form-data">
        {if isset($iItem) && isset($sModule)}
            <div><input type="hidden" name="val[module_id]" value="{$sModule|htmlspecialchars}" /></div>
            <div><input type="hidden" name="val[item_id]" value="{$iItem|htmlspecialchars}" /></div>
        {/if}
        <div id="js_custom_privacy_input_holder">
            {if $bIsEdit && (!isset($sModule) || empty($sModule))}
                {module name='privacy.build' privacy_item_id=$aForms.blog_id privacy_module_id='blog'}
            {/if}
        </div>

        {if $bIsEdit}
            <div><input type="hidden" name="id" value="{$aForms.blog_id}" /></div>
        {/if}

        {plugin call='ynblog.template_controller_add_hidden_form'}

        <div class="form-group">
            <label>{required}{_p var='categories'}:</label>
            {$sCategories}
        </div>

        <div class="form-group">
            <label for="title">{required}{_p var='Blog Title'}:</label>
            <input required maxlength="255" class="form-control close_warning" type="text" name="val[title]" value="{value type='input' id='title'}" id="title" size="40" />
        </div>

        {if Phpfox::isModule('tag')}{module name='tag.add' sType=ynblog}{/if}

        {if !isset($sModule) || empty($sModule)}
        <div class="form-group">
            <label>
                {_p var='privacy'}:
            </label>
            {if Phpfox::isModule('privacy')}
                {module name='privacy.form' privacy_name='privacy' privacy_info='ynblog.control_who_can_see_this_blog' default_privacy='ynblog.default_privacy_setting'}
            {/if}
        </div>
        {/if}

        {if !empty($aForms.current_image) && !empty($aForms.blog_id)}
            {module name='core.upload-form' type='ynblog' current_photo=$aForms.current_image id=$aForms.blog_id}
            <input type="hidden" name="val[image_path]" value="{value type='input' id='image_path'}">
            <input type="hidden" name="val[server_id]" value="{value type='input' id='server_id'}">
        {else}
            {module name='core.upload-form' type='ynblog' current_photo=''}
        {/if}

        {if Phpfox::isApps('phpFox_CKEditor')}
        <div>
            <input type="hidden" id="advancedblog_has_ckeditor" value="1">
            <input type="hidden" name="val[attachment]" class="js_attachment" value="{value type='input' id='attachment'}" />
        </div>
        <div class="form-group">
            <label for="text">{required}{_p var='Blog Content'}:</label>
            {editor id='text'}
        </div>
        {else}
        <div class="form-group">
            <label for="text">{required}{_p var='Blog Content'}:</label>
            <textarea id="text" name="val[text]">{value type='textarea' id='text'}</textarea>
        </div>
        {/if}

        {if Phpfox::isModule('captcha') && Phpfox::getUserParam('captcha.captcha_on_blog_add')}{module name='captcha.form' sType=blog}{/if}

        <div class="form-group" style="display:none;">
            <label>{_p var='post_status'}:</label>
            <div>
                <label><input value="1" type="radio" name="val[post_status]" id="js_post_status1" class="checkbox" {value type='checkbox' id='post_status' default='1'}/> {_p var='published'}</label>
                <label><input value="2" type="radio" name="val[post_status]" id="js_post_status2" class="checkbox" {value type='checkbox' id='post_status' default='2'}/> {_p var='draft'}</label>
            </div>
        </div>

        <div class="form-group">
            <ul class="table_clear_button clearfix">
                {plugin call='ynblog.template_controller_add_submit_buttons'}
                {if $bIsEdit && $aForms.post_status == 'draft'}
                    <li><input type="submit" name="val[draft_update]" value="{_p var='update'}" class="button btn-primary" /></li>
                    <li><input type="submit" name="val[draft_publish]" value="{_p var='publish'}" class="button button_off" /></li>
                {else}
                    <li><input type="submit" name="val[{if $bIsEdit}update{else}publish{/if}]" value="{if $bIsEdit}{_p var='update'}{else}{_p var='publish'}{/if}" class="button btn-primary" /></li>
                {/if}
                {if !$bIsEdit}<li><input type="submit" name="val[draft]" value="{_p var='save_as_draft'}" class="btn btn-default button_off" /></li>{/if}
            </ul>
        </div>

    </form>

    {if Phpfox::getParam('core.display_required')}
    <div class="table_clear">
        {required} {_p var='required_fields'}
    </div>
    {/if}
</div>
