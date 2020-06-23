<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
{$sCreateJs}
<form method="post" id="frmEmailTemplate" action="{url link='admincp.directory.email'}" name="js_form">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='email_templates'}
            </div>
        </div>

        <div class="panel-body">
            <div class="form-group">
                <label for="language_id">{phrase var='language_pack'}</label>
                {if count($aLanguages) > 1}
                <p class="help-block">
                    {phrase var='your_community_has_more_than_one_language_pack_installed_please_select_the_language_pack_you_want_to_edit_right_now'}
                </p>
                {/if}
                <select class="form-control" name="val[language_id]" id="language_id" onchange="$.ajaxCall('directory.fillEmailTemplate', 'email_template_id=' + $('#email_template_id').val()+'&language_id='+ $('#language_id').val());">
                    {foreach from=$aLanguages item=aLanguage}
                        <option value="{$aLanguage.language_code}">{$aLanguage.title}</option>
                    {/foreach}
                </select>
            </div>
            
            <div class="form-group">
                <label for="email_template_id">{phrase var='choose_message'}</label>
                <select class="form-control" name="val[email_template_id]" id="email_template_id" onchange="$.ajaxCall('directory.fillEmailTemplate', 'email_template_id=' + $(this).val()+'&language_id='+ $('#language_id').val());">
                    <option value="{$aTypes.claim_business_successfully}">{phrase var='claim_business_successfully'}</option>
                    <option value="{$aTypes.business_approved}">{phrase var='business_approved'}</option>
                    <option value="{$aTypes.claim_request_approved}">{phrase var='claim_request_approved'}</option>
                    <option value="{$aTypes.create_business_successfully}" selected="selected">{phrase var='create_business_successfully'}</option>
                </select>
            </div>

            <div class="form-group">
                <label for="email_subject">{phrase var='subject'}:</label>
                <input class="form-control" type="text" name="val[email_subject]" value="{value type='input' id='email_subject'}" id="email_subject" size="40" maxlength="150" />
            </div>

            <div class="form-group">
                <label>{phrase var='message_body'}:</label>
                {editor id='email_template' rows='15'}
            </div>
        </div>

        <div class="panel-footer">
            <input type="submit" value="{phrase var='save_changes'}" class="btn btn-primary">
        </div>
    </div>
</form>

<script type="text/javascript">
$Behavior.emailDirectory = function(){l}
	$('#email_template_id option').each(function() {l} 
		if($(this).val() == {$iCurrentTypeId})
		{l}
			$(this).attr('selected', 'selected');
            $('#email_template_id').trigger('change');
		{r}
	{r});
{r}

{literal}
    function fillEmailTemplate(content) {
        if ((typeof CKEDITOR !== 'undefined') && (typeof CKEDITOR.instances['email_template'] !== 'undefined')) {
            CKEDITOR.instances['email_template'].setData(content);
        }
    }
{/literal}
</script>