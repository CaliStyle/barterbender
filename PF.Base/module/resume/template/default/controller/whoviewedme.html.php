
{literal}
<style>

	.content3{
		width: 100% !important;
	}
	#content_holder{
		overflow:hidden;
	}
</style>
{/literal}
{if $iPage <= 1}
<div class="whoviewed_text">
	{_p var='your_profile_has_been_viewed_by_total_view_people_in_this_time_period' total_view=$iCnt}
</div>
{/if}
{foreach from=$aResumes key=iKey item=aResume}
	{template file='resume.block.who-view-resume-item'}
{/foreach}
{if $bWhoViewRegistration}
	{pager}
{else}

<div class="resume_upgrade_btn">
	<a type="button"
	   class="button btn btn-success btn-sm"
	   href="#"
	   onclick="$Core.box('resume.register',500,'');return false;">
		{_p var='upgrade_you_account_to_see_the_full_list_of_who_s_viewed_you_resume'}
	</a>
</div>
{/if}