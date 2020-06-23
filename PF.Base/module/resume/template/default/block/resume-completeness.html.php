<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**
 *
 * @copyright      YouNet Company
 * @author         VuDP, TienNPL
 * @package        Module_Resume
 * @version        3.01
 *
 */
 ?>

<link rel="stylesheet" type="text/css" href="{$core_path}module/resume/static/css/default/default/resume-completeness.css">
{if !$bNoResume }
<ul class="yns-profile-complete">
	{foreach from = $aResumes item = aResume}
	<li>
		<strong>{$aResume.headline|shorten:30:'...'}</strong>
		{if $aResume.is_completed and $aResume.is_published and $aResume.status == 'approved'}
			<sup>{phrase var ="resume.published" }</sup>
			<a href="{permalink module='resume.add' id='id_'$aResume.resume_id}">{phrase var="resume.update"}</a>
		{else}
			<a href="{permalink module='resume.add' id='id_'$aResume.resume_id}">{phrase var="resume.update"}</a>
			<div class="clear"></div>
			<span class="progress-meter" title="{$aResume.completed_percent}% {_p var='complete'}">
				<span class="has-progress" style="width:{$aResume.completed_percent}%"></span>
			</span>
			{if $aResume.next_suggestion}
				{$aResume.next_suggestion}
			{/if}
		{/if}
		<div class="clear"></div>
	</li>
	{/foreach}
</ul>
<p class="yns-viewall"><a class="btn btn-success btn-sm" href="{url link='resume.view_my'}">{_p var='view_all'}</a></p>
{else}
	{phrase var="resume.no_created_resume" link=$sCreateLink}
{/if}
