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

{literal}
<style>

	.content3{
		width: 100% !important;
	}
</style>
{/literal}

{module name='resume.advanced-search'}
<!-- Insert layout from here -->
{if $bIsSelfView or $sView == 'my'}
	<!-- my resume page layout -->
	{template file='resume.block.my-resume-item'}
{else}
	{if !count($aResumes)}
		{if !$bViewResumeRegistry and !$sView and !$bIsProfile}
			<div id="public_message" class="public_message" style="display:block;">
				{phrase var="resume.currently_you_can_only_view_your_friend_resumes"}
				<a href="javascript:void(0);" onclick="$Core.box('resume.registerViewResume',500,'');return false;">{_p var='click_here'}</a>
				{_p var='to_register_view_all_resume_service_to_view_full_list_of_resume'}
			</div>
		{/if}
        {if $current_page <=1 }
		<div class="extra_info">
			{_p var='no_resumes_found'}
		</div>
        {/if}
	{else}
	<!-- normal resume page layout -->
		<!-- Registration Button -->
		{if !$bViewResumeRegistry and !$sView and !$bIsProfile and $current_page <= 1}
            <div class="resume_upgrade_btn">
            	<a type="button"
            	   href="#" class="button btn btn-success" onclick="$Core.box('resume.registerViewResume',500,'');return false;">
            		{_p var='upgrade_your_account_to_see_full_list_of_resumes'}
            	</a>
            </div>
    		{foreach from=$aResumes key=iKey item=aResume}
    		  {if $iKey == 0 || $iKey == 1 || $iKey == 2}
                {template file='resume.block.resume-item'}
              {/if}
            {/foreach}
		{else}
    		<!-- Resume items -->
    		{foreach from=$aResumes key=iKey item=aResume}
    			{template file='resume.block.resume-item'}
    		{/foreach}
    
    		{pager}
		{/if}
	{/if}
{/if}