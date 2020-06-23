<?php
/**
 * @copyright		[YOUNETCO]
 * @author  		NghiDV
 * @package  		Module_Donationpages
 * @version 		$Id: ajax.class.php 1 2012-02-15 10:33:17Z YOUNETCO $
 */
defined('PHPFOX') or exit('NO DICE!');
?>
{literal}
<script type="text/javascript">
$(document).ready(function ()
	{
		Editor.sEditorId= 'contact_description';
		$Core.loadInit();
	});
	
    $('#btnContactUpdate').click(function(){
		$(this).addClass('disabled').attr('disabled','disabled');
        $("#core_js_pagecontacts_config").ajaxCall('pagecontacts.addConfig');
    });
</script>
<style>
	#core_js_pagecontacts_config{
		padding: 10px;
		background: #FFF;
	    box-sizing: border-box;
	    -webkit-box-sizing: border-box;
	    -moz-box-sizing: border-box;
	}

	input:not([type="button"]):focus,textarea:focus{
		text-indent: 0;
	}
	h3{
		margin-bottom: 15px;
	}

	#js_add_question{
	    float: right;
    	clear: both;
    	margin-bottom: 10px;
	}

	.yncontact_form_item{
		clear: both;
		margin-bottom: 15px;
	}
	.yncontact_form.full_question_holder{
		position: relative;
	}

	#removeQuestion{
		float: right;
		margin-bottom: 10px;
		top: 0px;
		width: 35px;
		height: 35px;
		line-height: 35px;
		text-align: center;
	}

	#removeQuestion i.fa{
		color: #E61010;
		font-size:20px;
	}
    .item_is_active, .item_is_not_active{
        width: 64px;
    }
</style>
{/literal}

<div style="display:none;" id="hiddenQuestion">
	<div id="js_quiz_layout_default">
		{template file="pagecontacts.block.topic"}
	</div>
</div>

<form id="core_js_pagecontacts_config" method="post">
	<div>
		<input type="hidden" name="val[page_id]" value="{$iPageId}" />
	</div>

	<div class="message" style="display:none;margin-bottom:5px;width:100%"></div>
	<div class="error_message" style="display:none;margin-bottom:5px"></div>

	<div class="table form-group">
		<div class="table_left" style="padding-top: 0px">
			{phrase var='pagecontacts.enable_contact_form'}:
		</div>
		<div class="table_right">
			<div class="item_is_active_holder">
				{if $bIsActive}
				<span class="js_item_active item_is_active"><input type="radio" class="checkbox" checked="checked" name="val[is_active]" value="1">{phrase var='pagecontacts.yes'}</span>
				<span class="js_item_active item_is_not_active"><input type="radio" class="checkbox" name="val[is_active]" value="0">{phrase var='pagecontacts.no'}</span>
				{else}
				<span class="js_item_active item_is_active"><input type="radio" class="checkbox" name="val[is_active]" value="1">{phrase var='pagecontacts.yes'}</span>
				<span class="js_item_active item_is_not_active"><input type="radio" class="checkbox" checked="checked" name="val[is_active]" value="0">{phrase var='pagecontacts.no'}</span>
				{/if}
			</div>
		</div>
	</div>

	<div class="table form-group">
		<div class="table_left">
			{required} {phrase var='pagecontacts.description'}:
		</div>

		<div class="table_right">
			<textarea class="form-control"  cols="80" rows="10" name="val[contact_description]">{if isset($aForms.contact_description)}{$aForms.contact_description}{/if}</textarea>
		</div>
		<div class="clear"></div>
	</div>

	<h3>{required} {phrase var='pagecontacts.topics'}:</h3>

	<div id="js_quiz_container">
		{if isset($aForms.topics)}
		  {foreach from=$aForms.topics item=Topic name=topic}
		      {template file="pagecontacts.block.topic"}
		  {/foreach}
		{else}
		  {template file="pagecontacts.block.topic"}
		{/if}
	</div>
	
	<div class="clearfix">
		<a href="#" id="js_add_question" class="button btn btn-success btn-sm">{phrase var='pagecontacts.add_another_topic'}</a>
	</div>

	<div class="table_clear" style="margin-top: 5px;">
		<button type="button" class="button btn btn-primary btn-sm" name="btnUpdate" id="btnContactUpdate">{phrase var='pagecontacts.submit'}</button>
	</div>
</form>