<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         VuDP, TienNPL, TrucPTM
 * @package        Module_Resume
 * @version        3.01
 *
 */?>
{ if count($aExperience) > 0 or $aOptions.can_edit}
 <!-- Experience information layout here -->
 <div class="yns contact-info resume_experience">

 	<h3>{phrase var="resume.experience"}
 		{if $aOptions.can_edit}
			<a href="{url link='resume.experience.id_'$aResume.resume_id}" class="add-new">+ {_p var='add_experience'}</a>
		{/if}
	</h3>
 			{foreach from = $aExperience item = aExp}
	 			<div class="experience_content extra_info" id="experience_{$aExp.experience_id}" >
		 			<!-- Title -->
		 			<p class="f_14">
		 				<b>{$aExp.title}</b>
		 				{ if $aOptions.can_edit }
		 					<a  class="f_11" href="{url link='resume.experience.id_'$aExp.resume_id'.exp_'$aExp.experience_id}">{phrase var="resume.edit"}</a>
		 				{/if}
		 				{ if $aOptions.can_delete }
		 					{ if $aOptions.can_edit } | {/if}
		 					<a class="f_11" href="javascript.void(0);" onclick="if(confirm( '{_p var='are_you_sure'}'))$.ajaxCall('resume.delete_experience','exp_id={$aExp.experience_id}');return false;">{_p var='delete'}</a>
		 				{/if}
		 			</p>
		 			<!-- Company Name -->
		 			<p class="company_name">{$aExp.company_name}</p>
		 			<p class="">{$aExp.level_name}</p>
		 			<!-- Start time -->
		 			<p><?php echo date('F Y',mktime(0,0,0,$this->_aVars["aExp"]["start_month"],1,$this->_aVars["aExp"]["start_year"])); ?>
		 			   -
		 			   <!-- End Time -->
		 			   {if $aExp.is_working_here || !$aExp.end_month || !$aExp.end_year}
		 			   		{phrase var="resume.present"}
		 			   	 {else}
		 			   	 	<?php echo date('F Y',mktime(0,0,0,$this->_aVars["aExp"]["end_month"],1,$this->_aVars["aExp"]["end_year"])); ?>
		 			   	 {/if}
		 			   <!-- Working Period -->
		 			   	 ({$aExp.period})
		 			   <!-- Location -->
		 			   {if !empty($aExp.location)}
		 			   	| {$aExp.location}
		 			   {/if}
		 			</p>
		 			<!-- Description -->
		 			<p>{$aExp.description_parsed}</p>
 				</div>
 			{/foreach}

 </div>
{/if}
