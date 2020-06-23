<?php 
/**
 * [PHPFOX_HEADER]
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{$sCreateJs}
<form method="post" id="frmEmailTemplate" action="{url link='admincp.fundraising.email'}" name="js_form">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='email_templates'}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="type_id">{required}{phrase var='email_templates_types'}:</label>
                <select class="form-control" name="val[type_id]" id="type_id" onchange="$.ajaxCall('fundraising.fillEmailTemplate', 'type_id=' + $(this).val());">
                    <option value="">{phrase var='select'}:</option>
                    <option value="{$aTypes.createcampaignsuccessful_owner}">{phrase var='create_campaign_successfull_owner'}</option>
                    <option value="{$aTypes.thankdonor_donor}">{phrase var='thank_donor'}</option>
                    <option value="{$aTypes.updatedonor_owner}">{phrase var='update_donor_owner'}</option>
                    <option value="{$aTypes.campaignexpired_owner}">{phrase var='campaign_expired_owner'}</option>
                    <option value="{$aTypes.campaignexpired_donor}">{phrase var='campaign_expired'}</option>
                    <option value="{$aTypes.campaigncloseduetoreach_owner}">{phrase var='campaign_closed_due_to_reach_owner'}</option>
                    <option value="{$aTypes.campaigncloseduetoreach_donor}">{phrase var='campaign_closed_due_to_reach'}</option>
                    <option value="{$aTypes.campaignclose_owner}">{phrase var='campaign_closed_owner'}</option>
                    <option value="{$aTypes.invitefriendletter_template}">{phrase var='invite_friend_letter_template'}</option>
                </select>
            </div>
            <div class="form-group">
                <label for="email_subject">{phrase var='subject'}:</label>
                <input class="form-control" type="text" name="val[email_subject]" value="{value type='input' id='email_subject'}" id="email_subject" size="40" maxlength="150">
            </div>
            <div class="form-group">
                <label>{phrase var='content'}:</label>
                {editor id='email_template' rows='15'}
            </div>
            {module name='fundraising.keyword-placeholder'}
        </div>
        <div class="panel-footer">
            <button type="submit" class="btn btn-primary">{phrase var='submit'}</button>
        </div>
    </div>
</form>

<script type="text/javascript">
$Behavior.ffundAdmincpEmail = function() {l}
	$('#type_id option').each(function() {l} 
		if($(this).val() == {$iCurrentTypeId})
		{l}
			$(this).attr('selected', 'selected');
		{r}
	{r});
{r}
</script>