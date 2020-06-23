<div class="yndirectory-job-list">
	{if count($aJobs) < 1}
		<div class="help-block">
			{phrase var='no_item_s_found'}.
		</div>
	{/if}

	<div class="yndirectory-content-column3">
	{foreach from=$aJobs item=aJob}
		<div class="yndirectory-job-item">
			<div class="yndirectory-item-photo">
				<a href="{permalink module='jobposting' id=$aJob.job_id title=$aJob.title}">
					{if $aJob.image_path}
						{img server_id=$aJob.image_server_id path='core.url_pic' file="jobposting/".$aJob.image_path suffix='_150' max_width='120' max_height='115'}
					{else}
					  <img src="{$coreUrlModule}jobposting/static/image/default/default_ava.png">
					{/if}
				</a>
			</div>
			<div class="yndirectory-item-info">
				<div class="yndirectory-item-title">
					<a class="link ajax_link" href="{permalink module='jobposting' id=$aJob.job_id title=$aJob.title}">
						{$aJob.title|shorten:20:'...'}
					</a>					
				</div>
				<div>{phrase var='posted_date'}: {$aJob.post_date_phrase}</div>
			</div>
		</div>
	{/foreach}
	</div>

	<div class="clear"></div>
	{module name='directory.paging'}	
</div>

{if PHPFOX_IS_AJAX}
{literal}
<script type="text/javascript">
	$Core.loadInit();
</script>
{/literal}
{/if}