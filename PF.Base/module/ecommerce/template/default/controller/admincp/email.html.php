<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{$sCreateJs}
<form method="post" id="frmEmailTemplate" action="{url link='admincp.ecommerce.email'}" name="js_form">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='email_templates'}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label>{phrase var='language_pack'}</label>
                {if count($aLanguages) > 1}
                    <p class="help-block">
                        {phrase var='your_community_has_more_than_one_language_pack_installed_please_select_the_language_pack_you_want_to_edit_right_now'}
                    </p>
                {/if}
                <select class="form-control" title="{phrase var='language_pack'}" name="val[language_id]" id="language_id" onchange="$.ajaxCall('ecommerce.fillEmailTemplate', 'email_template_id=' + $('#email_template_id').val()+'&language_id='+ $('#language_id').val());">
                    {foreach from=$aLanguages item=aLanguage}
                        <option value="{$aLanguage.language_code}">{$aLanguage.title}</option>
                    {/foreach}
                </select>
            </div>
            <div class="form-group">
                <label>{phrase var='choose_message'}</label>
                <div class="form-group">
                    <select class="form-control" title="{phrase var='choose_message'}" name="val[email_template_id]" id="email_template_id" onchange="$.ajaxCall('ecommerce.fillEmailTemplate', 'email_template_id=' + $(this).val()+'&language_id='+ $('#language_id').val());">
                        <option value="{$aTypes.congratulations_your_item_sold}">{phrase var='congratulations_your_item_sold'}</option>
                        <option value="{$aTypes.you_ve_bought_the_item}">{phrase var='you_ve_bought_the_item'}</option>
                        <option value="{$aTypes.order_updated}">{phrase var='order_updated'}</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>{phrase var='subject'}:</label>
                <input title="{phrase var='subject'}" class="form-control" type="text" name="val[email_subject]" value="{value type='input' id='email_subject'}" id="email_subject" size="40" maxlength="150">
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
$Behavior.emailEcommerce = function(){l}
	$('#email_template_id option').each(function() {l} 
		if($(this).val() == {$iCurrentTypeId})
		{l}
			$(this).attr('selected', 'selected');
            $('#email_template_id').trigger('change');
		{r}
	{r});
{r}	
</script>