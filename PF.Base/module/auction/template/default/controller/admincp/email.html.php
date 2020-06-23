<?php

defined('PHPFOX') or exit('NO DICE!');

?>
{$sCreateJs}
<form method="post" id="frmEmailTemplate" action="{url link='admincp.auction.email'}" name="js_form">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='email_templates'}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="">{phrase var='language_pack'}</label>
                {if count($aLanguages) > 1}
                <div>
                    {phrase var='your_community_has_more_than_one_language_pack_installed_please_select_the_language_pack_you_want_to_edit_right_now'}
                </div>
                {/if}
                <select class="form-control" name="val[language_id]" id="language_id" onchange="$.ajaxCall('auction.fillEmailTemplate', 'email_template_id=' + $('#email_template_id').val()+'&language_id='+ $('#language_id').val());">
                    {foreach from=$aLanguages item=aLanguage}
                    <option value="{$aLanguage.language_code}">{$aLanguage.title}</option>
                    {/foreach}
                </select>
            </div>
            <div class="form-group">
                <label for="">{phrase var='choose_message'}</label>
                <select class="form-control" name="val[email_template_id]" id="email_template_id" onchange="$.ajaxCall('auction.fillEmailTemplate', 'email_template_id=' + $(this).val()+'&language_id='+ $('#language_id').val());">
                    <option value="{$aTypes.someone_start_bidding_on_your_auction}">{phrase var='someone_start_bidding_on_your_auction'}</option>
                    <option value="{$aTypes.you_have_been_outbid_bid_again_now}">{phrase var='you_have_been_outbid_bid_again_now'}</option>
                    <option value="{$aTypes.your_auction_has_ended}">{phrase var='your_auction_has_ended'}</option>
                    <option value="{$aTypes.congratulations_you_won}">{phrase var='congratulations_you_won'}</option>
                    <option value="{$aTypes.bidding_has_ended}">{phrase var='bidding_has_ended'}</option>
                    <option value="{$aTypes.auction_have_been_transferred_old_winner}">{phrase var='auction_have_been_transferred'} ({phrase var='old_winner'})</option>
                    <option value="{$aTypes.auction_have_been_transferred_seller}">{phrase var='auction_have_been_transferred'} ({phrase var='seller'})</option>
                    <option value="{$aTypes.offer_received}">{phrase var='offer_received'}</option>
                    <option value="{$aTypes.offer_approved}">{phrase var='you_ve_made_a_best_offer'}</option>
                    <option value="{$aTypes.offer_denied}">{phrase var='offer_denied'}</option>
                    <option value="{$aTypes.auction_has_been_approved}">{phrase var='your_auction_has_been_approved'}</option>
                </select>
            </div>

            <div class="form-group">
                <label for="">{phrase var='subject'}:</label>
                <input class="form-control" type="text" name="val[email_subject]" value="{value type='input' id='email_subject'}" id="email_subject" size="40" maxlength="150" />
            </div>

            <div class="form-group">
                <div id="lbl_html_text">
                    <label for="">{phrase var='message_body'}:</label>
                </div>
                {editor id='email_template' rows='15'}
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" value="{phrase var='save_changes'}" class="btn btn-primary" />
        </div>
    </div>
</form>

<script type="text/javascript">
$Behavior.emailAuction = function(){l}
	$('#email_template_id option').each(function() {l}
		if($(this).val() == {$iCurrentTypeId})
		{l}
			$(this).attr('selected', 'selected');
            $('#email_template_id').trigger('change');
		{r}
	{r});
{r}
</script>