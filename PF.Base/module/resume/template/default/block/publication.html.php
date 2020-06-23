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
{if count($aPublications) > 0 or $aOptions.can_edit }
 <!-- Education information layout here -->
 <div class="yns contact-info resume_experience">

 	<h3>
 		{phrase var="resume.publications"}
 		{ if $aOptions.can_edit }
			<a href="{url link='resume.publication.id_'$aResume.resume_id}" class="add-new">+ {_p var='add_a_publication'}</a>
		{/if}
 	</h3>
	 		{foreach from = $aPublications item = aPub}
	 		<div class="experience_content extra_info" id="publication_{$aPub.publication_id}">
	 				<!-- Publication Type -->
					<span class="f_14">
						<strong>
							{if $aPub.type_id == 1}
								{phrase var="resume.book"}
							{elseif $aPub.type_id == 2}
								{phrase var="resume.magazine"}
							{else}
								{$aPub.other_type}
							{/if}
						</strong>
					</span>
					{if $aPub.publisher}
						 -
						 <span class="f_14">
						 	<strong>{$aPub.publisher}</strong>
						 </span>
					{/if}
	 			{if $aPub.published_month and $aPub.published_year}
	 			, <?php echo date('d F Y',mktime(0,0,0,$this->_aVars["aPub"]["published_month"], $this->_aVars["aPub"]["published_day"],$this->_aVars["aPub"]["published_year"])); ?>
	 			{/if}
	 			&nbsp;
	 			{ if $aOptions.can_edit }
		 				<a  class="f_11" href="{url link='resume.publication.id_'$aPub.resume_id'.exp_'$aPub.publication_id}">{phrase var="resume.edit"}</a>
	 			{/if}
	 			{ if $aOptions.can_delete }
	 				|
	 				<a class="f_11" href="javascript:void(0);" onclick="if(confirm('{_p var='are_you_sure'}'))$.ajaxCall('resume.delete_publication','exp_id={$aPub.publication_id}');return false;">{_p var='delete'}</a>
	 			{/if}
	 			<!-- Publication Title and Url -->
	 			<div class="publication_title_url">
					<strong>{$aPub.title}</strong>
					{if $aPub.publication_url}
						-
						<a href ="{$aPub.publication_url}" target="_blank" title="{$aPub.publication_url}">{$aPub.publication_url|shorten:150:'...'}</a>
					{/if}
				</div>
				<!-- Publication Authors -->
				{if isset($aPub.author_list)}
					<div class="publication_info">
						<i>{phrase var="resume.author"}:</i>
						{$aPub.author_list}
					</div>
				{/if}
				<!-- Publication Summary -->
				{if $aPub.note_parsed}
					<div class ="publication_summary">
						<i>{phrase var="resume.summary"}:</i>
						<div>
							{$aPub.note_parsed}
						</div>
					</div>
				{/if}
	 		</div>
	 		{/foreach}

 </div>
{/if}