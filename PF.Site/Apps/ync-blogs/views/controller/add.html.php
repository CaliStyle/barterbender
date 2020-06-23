<?php
defined('PHPFOX') or exit('NO DICE!');
?>

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
    <div class="js_ynblog_add_error_message hide"></div>
    {$sCreateJs}
    <form method="post" action="{url link='ynblog.add'}" id="advancedblog_js_blog_form" onsubmit="{$sGetJsForm}" enctype="multipart/form-data">
        {if isset($iItem) && isset($sModule)}
        <div><input type="hidden" name="val[module_id]" value="{$sModule|htmlspecialchars}" /></div>
        <div><input type="hidden" name="val[item_id]" value="{$iItem|htmlspecialchars}" /></div>
        {/if}
        <div id="js_custom_privacy_input_holder">
            {if $bIsEdit && (!isset($sModule) || empty($sModule))}
            {module name='privacy.build' privacy_item_id=$aForms.blog_id privacy_module_id='ynblog'}
            {/if}
        </div>

        {if $bIsEdit}
        <div><input type="hidden" name="id" value="{$aForms.blog_id}" /></div>
        {/if}

        {plugin call='ynblog.template_controller_add_hidden_form'}

        <div class="form-group">
            <label for="title">{_p var='Blog Title'} <span class="p-text-danger">{required}</span></label>
            <input required maxlength="255" class="form-control close_warning" type="text" name="val[title]" value="{value type='input' id='title'}" id="title" size="40" />
        </div>

        {if Phpfox::isApps('phpFox_CKEditor')}
        <div>
            <input type="hidden" id="advancedblog_has_ckeditor" value="1">
            <input type="hidden" name="val[attachment]" class="js_attachment" value="{value type='input' id='attachment'}" />
        </div>
        <div class="form-group">
            <label for="text">{_p var='Blog Content'} <span class="p-text-danger">{required}</span></label>
            {editor id='text'}
        </div>
        {else}
        <div class="form-group">
            <label for="text">{required}{_p var='Blog Content'}:</label>
            <textarea id="text" name="val[text]">{value type='textarea' id='text'}</textarea>
        </div>
        {/if}

        {if !empty($aForms.current_image) && !empty($aForms.blog_id)}
            {module name='core.upload-form' type='ynblog' current_photo=$aForms.current_image id=$aForms.blog_id}
        {else}
            {module name='core.upload-form' type='ynblog' current_photo=''}
        {/if}

        <div class="form-group">
            <label>
                <input value="1" type="checkbox" name="val[is_hidden]" {value type='checkbox' id='is_hidden' default='1'}>
                {_p var='hide_featured_image'}
            </label>
            <div class="help-block">
                {_p var='check_hide_image'}
            </div>
        </div>

        {if !empty($sCategories)}
        <div class="form-group js_core_init_selectize_form_group">
            <label>{_p var='categories'} <span class="p-text-danger">{required}</span></label>
            <div>
                {module name='ynblog.add_category_list'}
            </div>
        </div>
        {/if}

        {if Phpfox::isModule('tag')}{module name='tag.add' sType=ynblog}{/if}

        {if !isset($sModule) || empty($sModule)}
        <div class="form-group">
            <label>
                {_p var='privacy'}
            </label>
            {if Phpfox::isModule('privacy')}
            {module name='privacy.form' privacy_name='privacy' privacy_info='ynblog.control_who_can_see_this_blog' default_privacy='ynblog.default_privacy_setting'}
            {/if}
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

        <div class="p-form-group-btn-container">
            {plugin call='ynblog.template_controller_add_submit_buttons'}
            {if $bIsEdit && $aForms.post_status == 'draft'}
            <input type="submit" name="val[draft_update]" value="{_p var='update'}" class="btn btn-primary" />
            <input type="submit" name="val[draft_publish]" value="{_p var='publish'}" class="btn btn-default button_off" />
            {else}
            <input type="submit" name="val[{if $bIsEdit}update{else}publish{/if}]" value="{if $bIsEdit}{_p var='update'}{else}{_p var='publish'}{/if}" class="btn btn-primary" />
            {/if}
            {if !$bIsEdit}<input type="submit" name="val[draft]" value="{_p var='save_as_draft'}" class="btn btn-default button_off" />{/if}
        </div>
    </form>

    <div class="table_clear">
        {required} {_p var='required_fields'}
    </div>
</div>

{literal}
<script type="text/javascript">
    function Validation_ynblog_js_blog_form(form) {
        let formObject = $(form);
        let errorObject = formObject.parent().find('.js_ynblog_add_error_message');
        function ynblog_validation_scroll() {
            $('body, html').animate({scrollTop: 0}, 'slow');
        }
        function strip_tags(str, allowed_tags) {
            var key = '', allowed = false;
            var matches = [];
            var allowed_array = [];
            var allowed_tag = '';
            var i = 0;
            var k = '';
            var html = '';

            var replacer = function(search, replace, str) {
                return str.split(search).join(replace);
            };
            // Build allowes tags associative array
            if (allowed_tags) {
                allowed_array = allowed_tags.match(/([a-zA-Z0-9]+)/gi);
            }

            str += '';

            // Match tags
            matches = str.match(/(<\/?[\S][^>]*>)/gi);

            // Go through all HTML tags
            for (key in matches) {
                if (isNaN(key)) {
                    // IE7 Hack
                    continue;
                }

                // Save HTML tag
                html = matches[key].toString();

                // Is tag not in allowed list ? Remove from str !
                allowed = false;

                // Go through all allowed tags
                for (k in allowed_array) {
                    // Init
                    allowed_tag = allowed_array[k];
                    i = -1;

                    if (i != 0) {
                        i = html.toLowerCase().indexOf('<' + allowed_tag + '>');
                    }
                    if (i != 0) {
                        i = html.toLowerCase().indexOf('<' + allowed_tag + ' ');
                    }
                    if (i != 0) {
                        i = html.toLowerCase().indexOf('</' + allowed_tag);
                    }

                    // Determine
                    if (i == 0) {
                        allowed = true;
                        break;
                    }
                }

                if (!allowed) {
                    str = replacer(html, "", str);
                    // Custom replace. No regexing
                }
            }

            return str;
        }

        if($('#advancedblog_js_blog_form .mce-tinymce').length && typeof tinyMCE !== 'undefined') {
            let editorContent = tinyMCE.editors['text'].getContent();
            var textValue = strip_tags(editorContent);
            textValue = trim(textValue.replace( /&nbsp;/g,''));
        }
        else {

            let editor = Editor.setId('text');
            Editor.getEditors();
            let editorContent = editor.getContent();
            var textValue = strip_tags(editorContent);
            textValue = trim(textValue.replace( /&nbsp;/g,''));
        }

        if(empty(textValue)) {
            errorObject.removeClass('hide').html('<div class="error_message">' + oTranslations['add_content_to_blog'] + '</div>');
            ynblog_validation_scroll();
            return false;
        }

        let categoryValue = (formObject.find('#video_categories').selectize())[0].selectize.getValue();
        if(empty(categoryValue)) {
            errorObject.removeClass('hide').html('<div class="error_message">' + oTranslations['provide_a_category_this_item_will_belong_to'] + '</div>');
            ynblog_validation_scroll();
            return false;
        }

        errorObject.addClass('hide').html('');
        return;
    }
</script>
{/literal}
