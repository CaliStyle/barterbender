<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

/**

 * @copyright      YouNet Company
 * @author         VuDP, TienNPL, TrucPTM
 * @package        Module_Resume
 * @version        3.01
 *
 */?>
{if !empty($aAddition.website) or !empty($aAddition.sport) or !empty($aAddition.movies) or !empty($aAddition.interests) or !empty($aAddition.music) or $aOptions.can_edit }
 <!-- Experience information layout here -->
 <div class="yns contact-info additional_info">

 	<h3>
 		{phrase var="resume.additional_information"}
 		{ if $aOptions.can_edit }
			<a href="{url link='resume.addition.id_'$aResume.resume_id}" class="add-new">{_p var='edit'}</a>
		{/if}
 	</h3>
 		<div class ="experience_content">
 		<!-- Web Site -->
			{if !empty($aAddition.website)}
			<div class="info ">
				<div class="info_left">{phrase var="resume.websites"}:</div>
				<div class="info_right">
					{foreach from=$aAddition.website item=aWebsite}
                    <p><a href="{$aWebsite}">{$aWebsite}</a></p>
					{/foreach}
				</div>
			</div>
			{/if}
	 		<!-- Sports -->
	 		{if !empty($aAddition.sport) }
			<div class="info ">
				<div class="info_left">{phrase var="resume.sport"}: </div>
				<div class="info_right">{$aAddition.sport_parsed}</div>
			</div>
			{/if}
	 		<!-- Movies -->
	 		{if !empty($aAddition.movies) }
			<div class="info ">
				<div class="info_left">{phrase var="resume.movies"}: </div>
				<div class="info_right">{$aAddition.movies_parsed}</div>
			</div>
			{/if}
	 		<!-- Interests -->
	 		{if !empty($aAddition.interests) }
			<div class="info ">
				<div class="info_left">{phrase var="resume.interests"}:</div>
				<div class="info_right">{$aAddition.interestes_parsed}</div>
			</div>
			{/if}
	 		<!-- Music -->
	 		{if !empty($aAddition.music) }
			<div class="info ">
				<div class="info_left">{phrase var="resume.music"}:</div>
				<div class="info_right">{$aAddition.music_parsed}</div>
			</div>
			{/if}
 		</div>

 </div>
{/if}
