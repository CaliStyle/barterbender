<?php
/**
 * @copyright		[YOUNETCO]
 * @author  		NghiDV
 * @package  		Module_Donation
 * @version 		$Id: ajax.class.php 1 2012-02-15 10:33:17Z YOUNETCO $
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<form id="postform_id"  method="post">
<div class="message" style="display:none;"></div>
<div class="error_message" style="display:none;"></div>
<div class="general">
	<div class="form-group">
        <label for="">{phrase var='donation.enable_donation_on_this_site'}</label>
        <div class="item_is_active_holder">
            <input type="hidden" name="iPageId" id="iPageId" value="{$iPageId}">
            {if $iActive}
                <span class="js_item_active item_is_active"><input type="radio" class="checkbox" checked="checked" name="donation" value="1"> {phrase var='donation.yes'}</span>
                <span class="js_item_active item_is_not_active"><input type="radio" class="checkbox" name="donation" value="0"> {phrase var='donation.no'}</span>
            {else}
                <span class="js_item_active item_is_active"><input type="radio" class="checkbox" name="donation" value="1"> {phrase var='donation.yes'}</span>
                <span class="js_item_active item_is_not_active"><input type="radio" class="checkbox" checked="checked" name="donation" value="0"> {phrase var='donation.no'}</span>
            {/if}
        </div>
	</div>
	<div class="form-group">
        <label for="">{phrase var='donation.input_your_paypal_email_account'}</label>
	    <input class="form-control" type="text" name="email" id="email" value="{$sEmail}"/>
	</div>
	<div class="form-group">
        <label for="">{phrase var='donation.purpose_of_donation'}</label>
		<textarea rows="6" class="form-control" name="content">{$content}</textarea>
	</div>
</div>
<div class="clear"> </div>
<div class="terms">
	<div class="form-group">
        <label for="">{phrase var='donation.terms_and_conditions'}</label>
		<textarea rows="6" class="form-control"  name="term_of_service">{$sTermOfService}</textarea>
	</div>
</div>
<div class="clear"> </div>
<div class="emails">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
	            {phrase var='donation.email_template'}
            </div>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="">{phrase var='donation.subject'}</label>
                <input class="form-control" type="text" id="email_subject"  name="email_subject" value="{$sSubject}" />
            </div>
            <div class="form-group">
                <label for="">{phrase var='donation.content'}</label>
                <textarea rows="15" class="form-control" type="text" id="email_content" name="val[email_content]">{value type='textarea' id='email_content'}</textarea>
            </div>

            <div class="form-group">
                <label for="">{phrase var='donation.keyword_substitutions'}:</label>
                <ul>
                    <li>{phrase var='donation.123_full_name_125_recipient_s_full_name'}</li>
                    <li>{phrase var='donation.123_user_name_125_recipient_s_user_name'}</li>
                    <li>{phrase var='donation.123_site_name_125_site_s_name'}</li>
                </ul>
            </div>
        </div>

        <div class="panel-footer">
            <input type="button" class="btn btn-primary" name="btnUpdate" id="btnUpdate" value="{phrase var='donation.update'}" />
        </div>
    </div>
</div>
</form>
{literal}
<script type="text/javascript">
    $Behavior.DonationConfig = function() {
        $(document).ready(function () {
            Editor.sEditorId= 'email_content';
        });

        $('#btnUpdate').click(function(){
            var iPageId = $('#iPageId').val();
            $("#postform_id").ajaxCall('donation.updateConfig');
        });

    }


    function showTerms()
    {
    	$('.general').css('display', 'none');
    	$('.emails').css('display', 'none');
    	$('.terms').css('display', '');
    }
    function showGenerals()
    {
    	$('.emails').css('display', 'none');
    	$('.terms').css('display', 'none');
    	$('.general').css('display', '');
    }
    function showEmails()
    {
		Editor.sEditorId= 'email_content';
		$Core.loadInit();
    	$('.terms').css('display', 'none');
    	$('.general').css('display', 'none');
    	$('.emails').css('display', '');
		 $('#btnUpdate').click(function(){
			var iPageId = $('#iPageId').val();
			$("#postform_id").ajaxCall('donation.updateConfig');
		});
    }
</script>
<style type="text/css">

.table_left
{
	width:35%;
	float:left;
}
#email_subject
{
	margin-bottom: 15px;
}
.table .table_right textarea{
	width: 100%;
	padding: 10px;
	text-indent: 0;
	box-sizing: border-box;
	margin: 0px;
}

</style>
{/literal}