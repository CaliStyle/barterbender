<script type="text/javascript">
	function applyJob() {l}
		var packages = $('[name=radio_package]:checked').val();
		if(packages > 0) {l}
			$('#popup_packages').val(packages);
        {r} else {l}
            alert('Please select a package to apply this job.');
            return false;
        {r}
		var rel = $('[name=radio_package]:checked').attr('rel');
		$('#popup_paypal').val(rel);

		$('#ync_applyfee_jobposting_form').submit();
    {r}
</script>

<form method="post" action="{url link='jobposting.applyfee'}" id="ync_applyfee_jobposting_form" name='ync_applyfee_jobposting_form' enctype="multipart/form-data">
	<div>
		<input type="hidden" name="jobID" value="{$iJob}">
		<input type="hidden" name="popup_packages" id="popup_packages" value="">
		<input type="hidden" name="popup_paypal" id="popup_paypal" value="">
	</div>	
	<div class="form-group">
        <label>{phrase var='select_your_existing_packages'}</label>
        {foreach from=$aPackages name=package item=aPackage}
        <div class="radio">
            <label>
                <input rel="0" value="{$aPackage.data_id}" type="radio" name="radio_package" {if $phpfox.iteration.package==1}checked="true" {/if}/>
                {$aPackage.name} - {$aPackage.fee_text} - {if $aPackage.apply_number==0}{phrase var='unlimited'}{else}{phrase var='remaining'} {$aPackage.remaining_apply} {phrase var='job_posts'}{/if} - {$aPackage.expire_text_2}
            </label><br />
        </div>
        {foreachelse}
            <div class="alert alert-info">
                {phrase var='no_package_found'}
            </div>
        {/foreach}
	</div>
	<div class="form-group">
        <label>{phrase var='or_select_the_one_of_following_packages'}</label>
        {foreach from=$aTobuyPackages name=tbpackage item=aTBPackage}
        <div class="radio">
            <label>
                <input rel="1" value="{$aTBPackage.package_id}" type="radio" name="radio_package"/>
                {$aTBPackage.name} - {$aTBPackage.fee_text} - {if $aTBPackage.apply_number==0}{phrase var='unlimited'}{else}{phrase var='remaining'} {$aTBPackage.apply_number} {phrase var='job_posts'}{/if} - {$aTBPackage.expire_text}
            </label>
        </div>
        {foreachelse}
            <div class="alert alert-info">
                {phrase var='no_package_found'}
            </div>
        {/foreach}
	</div>

	<div class="table_clear">
		<input type="button" class="btn btn-primary" value="{phrase var='btn_apply'}" onclick="applyJob();" id="js_job_publish_btn" /><span id="js_job_publish_loading"></span>
	</div>
</form>