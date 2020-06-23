<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		YouNet Company
 * @author  		MinhNTK
 * @package  		Module_PageContacts
 * @version 		3.01
 */
defined('PHPFOX') or exit('NO DICE!');

?>
{literal}
	<style type="text/css">
    
	</style>
{/literal}

<div class="yncontact_form full_question_holder yn_pagecontact_table">

	<div class="yncontact_form_item">
		<div class="table_left">
				{if isset($phpfox.iteration.topic) && $phpfox.iteration.topic <= 1}
					{*{required}*}
				{/if}
				{phrase var='pagecontacts.topic_name'}:
		</div>

		<div class="table_right">	
				<input type="text" class="topic_title form-control" name="val[q][{if isset($Topic.topic_id)}{$Topic.topic_id}{elseif isset($phpfox.iteration.topic)}{$phpfox.iteration.topic}{else}0{/if}][question]" value="{if isset($Topic.topic)}{$Topic.topic}{/if}" maxlength="255" size="30" />	
		</div>

		<div class="clear"></div>
	</div>

	<div class="yncontact_form_item">
		<div class="table_left">
			{phrase var='pagecontacts.email'}:
		</div>

		<div class="table_right" >
			<input type="text" class="email form-control" name="val[q][{if isset($Topic.topic_id)}{$Topic.topic_id}{elseif isset($phpfox.iteration.topic)}{$phpfox.iteration.topic}{else}0{/if}][email]" value="{if isset($Topic.email)}{$Topic.email}{/if}" maxlength="255" size="30" />
		</div>	
		<div class="clear"></div>
	</div>

	{if $aForms.totalTopic == 1 || $aForms.totalTopic == 0}
		<div id="removeQuestion" style="display:none;">
	{else}
		<div id="removeQuestion" >
	{/if}
		<a href="#" onclick="return $Core.pagecontacts.removeQuestion(this);"><i class="fa fa-minus-square fa-lg"></i></a>			
		
	</div>
	<div class="clear"></div>
</div>