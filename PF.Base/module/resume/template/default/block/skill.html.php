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
{if count($aResume.skills) > 0 or $aOptions.can_edit }
 <!-- Skill layout here -->
 <div class="yns contact-info">

 	<h3>
 		{phrase var="resume.skills_expertise"}
 		{if  $aOptions.can_edit }
			<a href="{url link='resume.skill'}id_{$aResume.resume_id}/" class="add-new">+ {_p var='add_a_skill'}</a>
		{/if}
	</h3>
	<div class="skill_education">
		{foreach from = $aResume.skills item = aSkill}
			{if !empty($aSkill)}<a>{$aSkill}</a>{/if}
		{/foreach}
	</div>

 </div>
{/if}