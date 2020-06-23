{foreach from=$aJobsSearch item=JobsSearch}
<div class="row_title clearfix">
	<div class="row_title_image">
		<a href="{permalink module='jobposting' id=$JobsSearch.job_id title=$JobsSearch.title}" title="{$JobsSearch.title}">
			{img server_id=$JobsSearch.image_server_id path='core.url_pic' file="jobposting/".$JobsSearch.image_path suffix='_50' max_width='50' max_height='50' class='js_mp_fix_width'}
		</a>
	</div>		
					
    {if $JobsSearch.permission.canEditJob || $JobsSearch.permission.canDeleteJob}
		<div class="jobposting-mini-action dropdown">				
	    	<a role="button"  data-toggle="dropdown">
	            <i class="ico ico-gear-o"></i>
	        </a>						
			<ul class="dropdown-menu dropdown-menu-right">
				{if $JobsSearch.permission.canEditJob}
				<li><a href="{url link='jobposting.add'}{$JobsSearch.job_id}/">{phrase var='edit_job'}</a></li>
				<li><a href="{url link='jobposting.company.manage' job=$JobsSearch.job_id}">{phrase var='view_application'}</a></li>
				{/if}
				{if $JobsSearch.permission.canDeleteJob}
				<li class="item_delete"><a href="javascript:void(0);" onclick="$.ajaxCall('jobposting.deleteJob_View', 'job_id={$JobsSearch.job_id}&page_view=2&company_id={$aCompany.company_id}', 'GET'); return false;" class="no_ajax_link" onclick="return confirm('Are you sure you want to delete this job?');" title="{phrase var='delete'}">{phrase var='delete_job'}</a></li>
				{/if}
			</ul>			
		</div>
    {/if}

	<div class="row_title_info">
		<span id="">
			<a href="{permalink module='jobposting' id=$JobsSearch.job_id title=$JobsSearch.title}" id="" class="link ajax_link">{$JobsSearch.title|clean|shorten:55:'...'|split:20}</a>
		</span>							
		<div class="extra_info">
			<span>{phrase var='posted_date'}: {$JobsSearch.time_stamp_phrase}</span> - <span> {phrase var='expired_date'}: {$JobsSearch.time_expire_phrase} </span>
		</div>							
	</div>					
</div>
{/foreach}