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

<!-- Insert layout from here -->
{if !count($aResumes) && $iPage <= 1}
		<div class="extra_info">
			{_p var='no_resumes_found'}
		</div>
{else}
	{foreach from=$aResumes key=iKey item=aResume}

	<div class="yns resume_item my-resume" id="js_item_m_resume_{$aResume.resume_id}">

		{if $bCanDelete && $aResume.status != 'approving'}
		<div class="_moderator">

			<!-- <a rel="resume"
			   class="moderate_link built" href="#{$aResume.resume_id}">
				<i class="fa"></i>
			</a> -->
			
			<div class="moderation_row">
		        <label class="item-checkbox">
		            <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aResume.resume_id}" id="check{$aResume.resume_id}" />
		            <i class="ico ico-square-o"></i>
		        </label>
		    </div>

			<div class="row_edit_bar_parent">				
			    <div class="row_edit_bar">
					<a role="button" class="row_edit_bar_action" data-toggle="dropdown" href="#"><i class="fa fa-action"></i></a>
					<ul class="dropdown-menu dropdown-menu-right">
						{if $bCanEdit }
    						{if $aResume.status != 'approving'}
    						<li>
    						    <a href="{url link='resume.add' id= $aResume.resume_id}">
    								{phrase var="resume.edit"}
    							</a>
    						</li>
    						{else}
    						  <li class="btn disabled">{phrase var="resume.edit"}</li>
    						{/if}
						{/if}
						<li>
                            {if $aResume.is_completed}
                            <!-- not published yet -->
                                {if !$aResume.is_published}
                                    <!-- has a resume is being approved or not -->
                                    {if !$bIsApproving and $aResume.status != 'denied'}
                                        <a href="{url link='resume.publish' id=$aResume.resume_id}" data-message="{_p var='are_you_sure_you_want_to_publish_this_resume'}" class="sJsConfirm">
                                            {phrase var="resume.publish"}
                                        </a>
                                    {/if}
                                <!-- published -->
                                {else}
                                    <!-- the current is being approved or not -->
                                    {if $aResume.status == 'approved'}
                                        <a href="{url link='resume.private' id=$aResume.resume_id}" data-message="{_p var='are_you_sure_you_want_to_set_private_this_resume'}" class="sJsConfirm">
                                            {phrase var="resume.private"}
                                        </a>
                                    {/if}
                                {/if}
                            {/if}
                        </li>
						{if $bCanDelete }
						{if $aResume.status != 'approving'}
    						<li class="item_delete">
    						    <a class="sJsConfirm" href="{url link='resume.delete' id=$aResume.resume_id}" class="sJsConfirm" data-message="{_p var='are_you_sure_you_want_to_delete_this_resume'}">
    								{phrase var="resume.delete"}
    							</a>
    						</li>
    						{else}
    							 <li class="btn disabled">{phrase var="resume.delete"}</li>
        					{/if}
    					{/if}
					</ul>
				</div>

			</div>
			
		</div>

		{/if}
		<!-- Resume thumbnail image -->
		<div class="resume_item_left">

			<a href="{permalink module='resume.view'
		id=$aResume.resume_id
		title=$aResume.headline}">
				{if $aResume.image_path!=""}
				{img
				server_id=$aResume.server_id
				path='core.url_pic'
				file='resume/'.$aResume.image_path suffix='_200'
				max_width='150'
				}
				{else}
				<img class="default_resume_image"
					 src="{$sCorePath}module/resume/static/image/profile.png" />
				{/if}
			</a>



		</div>

		<!-- Resume content summary -->
		<div class="resume_item_right">
			<!-- title -->
			<h4>
				<a href="{permalink module='resume.view' id=$aResume.resume_id title=$aResume.headline}">
					<strong>
						{if $aResume.headline}
							{$aResume.headline|shorten:50:"...":false}
						{else}
							{phrase var="resume.your_headline"}
						{/if}
					</strong>
				</a>
				<!-- status -->
				{if $aResume.is_completed}
					{if $aResume.is_published}
						{if $aResume.status == 'approved'}
						<a href="javascript:void(0);" class="yns-item yns-publish" title="{_p var='published'}">{phrase var="resume.published"}</a>
						{else}
							<i style="color: red">{phrase var="resume.".$aResume.status}</i>
						{/if}
					{else}
						{if $aResume.status == 'approved'}
							<i style="color: red">{phrase var="resume.private"}</i>
						{elseif $aResume.status == 'denied'}
							<i style="color: red">{phrase var="resume.".$aResume.status}</i>
						{else}
							<a href="javascript:void(0);" class="yns-item yns-complete" title="{_p var='complete'}">{phrase var="resume.complete"}</a>
						{/if}
					{/if}
				{else}
					<a href="#" class="yns-item yns-uncomplete" title="{_p var='incomplete'}">{_p var='incomplete'}</a>
				{/if}
			</h4>
			<!-- updated date  -->
			<div class="yns-res-info">
			<p>
				{phrase var ="resume.updated"}: {$aResume.time_update|date:'core.global_update_time'} - {$aResume.total_favorite} {phrase var="resume.favorites"}
			</p>
			</div>

			<!-- Categories -->
			<div class="yns-res-info">
				<p>{phrase var ="resume.categories"}:
					{if $aResume.categories}
						{$aResume.categories}
					{else}
						{phrase var="resume.not_selected"}
					{/if}
				</p>
			</div>
			<!-- Favorite -->
			<div class="yns-res-info">

			</div>
            {if $bCanViewInProfile}
            <div style="margin-left:-3px;margin-top:6px;">

                {if $aResume.status == 'approved' && $aResume.is_published == 1}
                <input id="resume_id_{$aResume.resume_id}" type="checkbox" name="resume_id[]" value="{$aResume.resume_id}" {if $aResume.is_show_in_profile} checked="checked" {/if} class="show_in_profile_info" onChange="showInProfileInfo(this)"/>
                <label for="resume_id_{$aResume.resume_id}">{phrase var="resume.show_in_profile_info"}</label>
                {/if}
            </div>
            {/if}
		</div>
		<div class="clear"></div>
	</div>
	{/foreach}
	<!-- pager -->
	<div style='clear: both'>
		{pager}
	</div>
	<!-- moderation -->
	{if $bCanDelete }
		<div style='clear: both'>
			{moderation}
		</div>

	{/if}
	<div class="clear"></div>
{/if}
