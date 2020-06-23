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
{if count($aCertificates) > 0 or $aOptions.can_edit }
 <!-- Education information layout here -->
 <div class="yns contact-info resume_experience">

 	<h3>
 		{phrase var="resume.certifications"}
 		{ if $aOptions.can_edit }
			<a href="{url link='resume.certification.id_'$aResume.resume_id}" class="add-new">+ {_p var='add_a_certification'}</a>
		{/if}
 	</h3>
 		{foreach from = $aCertificates item = aCertificate}
 			<div class="experience_content extra_info" id="certification_{$aCertificate.certification_id}">
 				<!-- Certificate Name -->
	 			<p class="f_14">
	 				<strong>{$aCertificate.certification_name}</strong>
	 				{ if $aOptions.can_edit }
		 				<a  class="f_11" href="{url link='resume.certification.id_'$aCertificate.resume_id'.exp_'$aCertificate.certification_id}">{phrase var="resume.edit"}</a>
		 			{/if}
		 			{ if $aOptions.can_delete }
		 				{ if $aOptions.can_edit } | {/if}
		 				<a class="f_11" href="javascript:void(0);" onclick="if(confirm('{_p var='are_you_sure'}'))$.ajaxCall('resume.delete_certification','exp_id={$aCertificate.certification_id}');return false;">{_p var='delete'}</a>
		 			{/if}
	 			</p>
	 			<!-- Course Name -->
	 			<p>
	 				{if $aCertificate.course_name}
	 					{phrase var="resume.course_s_name"}: {$aCertificate.course_name}
	 				{/if}
	 			</p>
	 			<!-- Time Period and Training Place -->
	 			<p>
	 			{if $aCertificate.start_month and $aCertificate.start_year and $aCertificate.end_month and $aCertificate.end_year}
		 			{phrase var="resume.attended"}:
		 			<?php echo date('F Y',mktime(0,0,0,$this->_aVars["aCertificate"]["start_month"],1,$this->_aVars["aCertificate"]["start_year"]));?>
		 			-
		 			<?php echo date('F Y',mktime(0,0,0,$this->_aVars["aCertificate"]["end_month"],1,$this->_aVars["aCertificate"]["end_year"])); ?>
				{/if}

	 			{if $aCertificate.training_place}
					{phrase var="resume.at"} {$aCertificate.training_place}
	 			{/if}
	 			</p>
		 		<!-- Note -->
	 			{if $aCertificate.note_parsed }
		 			<p>{phrase var="resume.additional_notes"}:</p>
		 			<p>{$aCertificate.note_parsed}</p>
	 			{/if}
			</div>
		{/foreach}

 </div>
{/if}