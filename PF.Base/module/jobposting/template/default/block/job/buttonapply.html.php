<div class="block ynjp_jobDetailApply">
	<div class="title">
		<a href="{permalink module='jobposting.company' id=$aCompany.company_id title=$aCompany.name}">{$aCompany.name}</a>
	</div>
	<div class="content">
		<div class="extra_info">
			{$aCompany.location}
		</div>
		<div class="website_link">
			{phrase var='website'}: <a href="{$aCompany.website}" target="_blank"> {$aCompany.website} </a>
		</div>

		<br/>

	    {if !$bIsApplied}
			{if $canApplyJob}
				<div class="ynjp_applyJob_btn_holder">
					<div class="ynjp_applyJob_btn" onclick="" href="{permalink module='jobposting.applyjob' id=$aJob.job_id title=$aJob.title}"> 
						<a class="btn btn-sm btn-success" href="javascript:void(0);" onclick="{if $canApplyJobWithoutFee}$.ajaxCall('jobposting.applyJobWithoutFee','id={$aJob.job_id}');{else}$Core.box('jobposting.popupApplyJobPackage', '500', 'id={$aJob.job_id}');{/if} return false;"> {phrase var='apply_job'} </a>
					</div>
				</div>
			{/if}
	    {/if}
    </div>
</div>


