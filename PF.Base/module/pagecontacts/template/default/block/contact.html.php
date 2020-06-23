<?php
/**
 * @copyright		YouNet Company
 * @author  		MinhNTK
 * @package  		Module_Pagecontacts
 * @version 		3.01
 */
defined('PHPFOX') or exit('NO DICE!');
?>
<form id="core_js_pagecontact" method="post">
	<div class="error_message" id="pagecontact_error_message" style="display:none;"></div>
    <div class="description_2">
	   <h3>
		  {$aContact.contact_description|parse}
	   </h3>
	</div>

	<div class="table form-group">
		<div class="table_left">
			{required}{phrase var='pagecontacts.full_name'}: 
		</div>

		<div class="table_right">
			<input type="text" class="form-control" name="val[full_name]" {if isset($sFullName)}value="{$sFullName}"{/if} size="50" maxlength="250" />
		</div>
		<div class="clear"></div>
	</div>
	<div class="table form-group">
		<div class="table_left">
			{required}{phrase var='pagecontacts.email'}: 
		</div>
		<div class="table_right">
			<input type="text" name="val[email]" class="form-control" {if isset($sEmail)}value="{$sEmail}"{/if} size="50" maxlength="250" />
		</div>
		<div class="clear"></div>
	</div>
	<div class="table form-group">
		<div class="table_left">
			{required}{phrase var='pagecontacts.topics'}:
		</div>
		<div class="table_right">
			<select class="form-control" name="val[topic]">
				{foreach from=$aTopics	item=aTopic}
					<option value="{$aTopic.topic_id}">{$aTopic.topic}</option>
				{/foreach}
			</select>
		</div>
	</div>
	<div class="table form-group">
		<div class="table_left">
			{required}{phrase var='pagecontacts.subject'}: 
		</div>
		<div class="table_right">
			<input type="text" class="form-control" name="val[subject]" size="50" maxlength="250" />
		</div>
		<div class="clear"></div>
	</div>
	<div class="table form-group">
		<div class="table_left">
			{required}{phrase var='pagecontacts.message'}: 
		</div>
		<div class="table_right">
			<textarea class="form-control" cols="50" name="val[message]" rows="10"></textarea>
		</div>
		<div class="clear"></div>
	</div>
	<button type="button" id="btnContactSend" class="button btn btn-primary btn-sm">{phrase var='pagecontacts.submit'}</button>
	<div class="message" id="pagecontact_message" style="display:none;margin-top:5px;width:100%;"></div>
</form>

{literal}
<script type="text/javascript">
	$Behavior.onLoadContactPopUp = function()
	{
		$('#btnContactSend').click(function(){
			$(this).addClass('disabled').attr('disabled','disabled');
			$("#core_js_pagecontact").ajaxCall('pagecontacts.sendMail');
		});
	}
	$Core.loadInit();
</script>
<style>
	.table_right {
		padding: 0 !important;
		border: 0 !important;
	}

	input:not([type="button"]),textarea{
		background: #f4f4f4 !important;

	}
    .description_2{
        overflow: auto;
        max-height:150px;
    }
</style>
{/literal}