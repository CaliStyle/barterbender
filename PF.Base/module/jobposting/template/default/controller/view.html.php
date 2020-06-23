{if $bIsApplied}
<div class="message">{phrase var='you_have_already_applied_this_job'}</div>
{/if}

<h1 class="ynjp_jobDetail_title"><a href="{permalink module='jobposting' id=$aJob.job_id title=$aJob.title}">{$aJob.title}</a></h1>



<div class="item_view ynjp_jobDetail_container">
	<div class="item_info">
		{phrase var='expire_on'}: {$aJob.time_expire_phrase}
	</div>
	{if $aJob.action}
		<div class="item_bar">
			<div class="item_bar_action_holder">
	            <a class="btn dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
	                <i class="ico ico-gear-o"></i>
	            </a>
				<ul class="dropdown-menu dropdown-menu-right">
				   {template file='jobposting.block.job.action-link'}
			   </ul>
			</div>
		</div>
	{/if}

    <!-- AddThis Button BEGIN -->
    <div class="addthis_block mt-1">
	    {addthis url=$aJob.bookmark_url title=$aJob.title}
    </div>

	<div class="yns job_detail_information">
		<h4><span> {phrase var='job_description'} </span></h4>
		<span>
			{$aJob.description_parsed|parse}
		</span>
		
		{if $aJob.total_attachment}
			{module name='attachment.list' sType=jobposting iItemId=$aJob.job_id}
		{/if}
			
		<h4><span> {phrase var='desired_skills_experience'} </span></h4>
		<span>
			{$aJob.skills_parsed}
		</span>
		<h4><span> {phrase var='addition_information'} </span></h4>
		<span>
			- {phrase var='language_preference'}: {$aJob.language_prefer}<br/>
			- {phrase var='education_preference'}: {$aJob.education_prefer}<br/>
			- {phrase var='working_place'}: {$aJob.working_place}<br/>
			- {phrase var='time'}: {$aJob.working_time}
			{if !empty($aJob.category_phrase)}
				<br/>- {phrase var='catjob_cat'}: {$aJob.category_phrase}
			{/if}
			
		</span>
		{if count($aFields)}
        {foreach from=$aFields item=aField}
            {template file='jobposting.block.custom.view'}
        {/foreach}
    	{/if}
		
		{if $aJob.location_city_country_phrase != ''}
			<h4 style="margin-bottom:20px;" class="ynjp_h4_location"><span>Location: <b> {$aJob.location_city_country_phrase}</b></span></h4>
		<iframe width="510" height="430" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="//maps.google.com/maps?f=q&source=s_q&geocode=&q={$aJob.encode_location_city_country_phrase}+&ll={$aJob.latitude},{$aJob.longitude}8&spn=0,0&t=m&z=12&output=embed"></iframe>
		{/if}
	</div>
	
	<div>
		{module name='feed.comment'}
	</div>
</div>

<!-- Temp - Left Column Content -->
