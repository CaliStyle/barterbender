<?php
/**
 * @copyright		[YOUNETCO]
 * @author  		NghiDV
 * @package  		Module_Donation
 * @version 		$Id: ajax.class.php 1 2012-02-15 10:33:17Z YOUNETCO $
 */
defined('PHPFOX') or exit('NO DICE!');
?>

{literal}
	<style>
		.donation{
			padding: 10px;
			box-sizing: border-box;
			background: #fff;
		}

		.donation h3{
			margin-top: 10px;
		}
		
		.donation input:not([type="button"]),textarea{
			background: #f4f4f4 !important;
			padding: 10px !important;
			box-sizing: border-box;
		}

		.donation .table_right{
			border: none;
			padding: 0px;
		}

		.donation .edit_menu_container{
			padding-bottom: 0;
		}
        .donation .item_is_active, .donation .item_is_not_active{
          width: 64px;
        } 

	</style>
{/literal}

<form id="postform_id"  method="post">
	<div class="message" style="display:none;"></div>
	<div class="error_message" style="display:none;"></div>
	<div class="general">
		<div class="table form-group">
			<div class="table_left"style="padding-top: 0px">
				{if Phpfox::isAdminPanel()}
				{phrase var='donation.enable_donation_on_this_site'}
				{else}
				{phrase var='donation.enable_donation_on_this_page'}
				{/if}
			</div>
			<div class="table_right">
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
		</div>
		<div class="table form-group">
			<div class="table_left">
				{phrase var='donation.input_your_paypal_email_account'}
			</div>
			<div class="table_right">
				<input type="text" class="form-control" name="email" id="email" value="{$sEmail}"/>
			</div>
		</div>
		<div class="table form-group">
			<div class="table_left">
				{phrase var='donation.purpose_of_donation'}
			</div>
			<div class="table_right">
				<textarea rows="6" class="form-control"  name="content">{$content}</textarea>
			</div>
		</div>
	</div>
	<div class="clear"> </div>
	<div class="terms">
		<div class="table form-group">
			<div class="table_left">
				{phrase var='donation.terms_and_conditions'}
			</div>
			<div class="table_right">
				<textarea rows="6" class="form-control"  name="term_of_service">{$sTermOfService}</textarea>
			</div>
		</div>
	</div>
	<div class="clear"> </div>
	<div class="emails">
		<div class="table form-group">
			<h3>{phrase var='donation.email_template'}</h3>
			<div class="extra_info">
				*{phrase var='donation.notice_about_sending_email'}
				
			</div>
			<div class='clear'> </div>
			<div class="table_left">
				{phrase var='donation.subject'}
			</div>
			<div class="table_right">
				<input type="text" class="form-control" id="email_subject"  name="email_subject" value="{$sSubject}" />
			</div>
			<div class='clear'> </div>
			<div class="table_left" >
				{phrase var='donation.content'}
			</div>
			<div class="table_right">
                <textarea rows="15" class="form-control" type="text" id="email_content" name="val[email_content]">{value type='textarea' id='email_content'}</textarea>
			</div>
			<div class="clear"></div>
			<div class="extra_info ">
				{phrase var='donation.keyword_substitutions'}:
				<ul>
					<li>{phrase var='donation.123_full_name_125_recipient_s_full_name'}</li>
					<li>{phrase var='donation.123_user_name_125_recipient_s_user_name'}</li>
					<li>{phrase var='donation.123_site_name_125_site_s_name'}</li>
				</ul>
				
				
			</div>
		</div>
		<div class="clear"> </div>
		<div class="table_clear">
			<button type="button" class="button btn btn-primary btn-sm" name="btnUpdate" id="btnUpdate">{phrase var='donation.update'}</button>
		</div>
	</div>
</form>
{literal}
<script type="text/javascript">
{/literal}

{if isset($bAjaxCall) && $bAjaxCall}

    {literal}
        $(document).ready(function (){
            Editor.sEditorId= 'email_content';
        });
		$Core.loadInit();
        $('#btnUpdate').click(function(){
            var iPageId = $('#iPageId').val();
            $("#postform_id").ajaxCall('donation.updateConfig');
        });        
        
    {/literal}

{else}

    {literal}
        $Behavior.DonationConfig = function() {
            $(document).ready(function (){
                Editor.sEditorId= 'email_content';
            });

            $('#btnUpdate').click(function(){
                var iPageId = $('#iPageId').val();
                $("#postform_id").ajaxCall('donation.updateConfig');
            });            
            $Core.loadInit();
        }
    {/literal}

{/if}

{literal}
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
{/literal}