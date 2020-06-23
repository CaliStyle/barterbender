<script>
    if (typeof showNotice == 'undefined') {l}
        function showNotice(title, message) {l}
            window.parent.sCustomMessageString = message;
            tb_show(title, $.ajaxBox('core.message', 'height=150&width=300'));
        {r}
    {r}
</script>
{if empty($iJob)}
{literal}
<script type="text/javascript">
	function publishJob() {
		console.log(1);
		var packages = $('[name=radio_package]:checked').val();
		if(packages > 0) {
			$('#popup_packages').val(packages);
        } else {
            showNotice(oTranslations['notice'], oTranslations['please_select_a_package_to_publish_this_job']);
            return false;
        }
		var rel = $('[name=radio_package]:checked').attr('rel');
		$('#popup_paypal').val(rel);
		$('#popup_publish').val(1);
		var feature = $('[name=feature]').is(':checked');
		if(feature) {
			$('#popup_feature').val(1);	
		}
		//popup_feature
		$('#ync_add_edit_form').submit();
	}
</script>
{/literal}
{else}
<script type="text/javascript">
	function publishJob() {l}
		console.log(2);
        var param = 'id={$iJob}';
        var packages = $('[name=radio_package]:checked').val();
		if (packages > 0) {l}
			param += '&package=' + packages;
        {r} else {l}
            showNotice(oTranslations['notice'], oTranslations['please_select_a_package_to_publish_this_job']);
            return false;
        {r}
		param += '&paypal=' + $('[name=radio_package]:checked').attr('rel');
		var feature = $('[name=feature]').is(':checked');
		if(feature) {l}
			param += '&feature=1';
		{r} else {l}
            param += '&feature=0';
        {r}
        $('#js_job_publish_btn').attr('disabled', true);
        $('#js_job_publish_loading').html($.ajaxProcess(oTranslations['jobposting.processing'])).show();
        $.ajaxCall('jobposting.publishJob', param);
        return false;
    {r}
</script>
{/if}

<div class="table form-group">
	<div class="table_left">
		{phrase var='select_your_existing_packages'}
	</div>
	<div class="table_right" style="margin-left: 20px;">
		{foreach from=$aPackages name=package item=aPackage}
		<label>
			<input rel="0" value="{$aPackage.data_id}" type="radio" name="radio_package" {if $phpfox.iteration.package==1}checked="true" {/if}/>
			{$aPackage.name} - {$aPackage.fee_text} - {if $aPackage.post_number==0}{phrase var='unlimited'}{else}{phrase var='remaining'} {$aPackage.remaining_post} {phrase var='job_posts'}{/if} - {$aPackage.expire_text_2}
		</label><br />
		{foreachelse}
			{phrase var='no_package_found'}
		{/foreach}
	</div>
</div>
<div class="table form-group">
	<div class="table_left">
		{phrase var='or_select_the_one_of_following_packages'}
	</div>
	<div class="table_right" style="margin-left: 20px;">
		{foreach from=$aTobuyPackages name=tbpackage item=aTBPackage}
		<label>
			<input rel="1" value="{$aTBPackage.package_id}" type="radio" name="radio_package"/>
			{$aTBPackage.name} - {$aTBPackage.fee_text} - {if $aTBPackage.post_number==0}{phrase var='unlimited'}{else}{phrase var='remaining'} {$aTBPackage.post_number} {phrase var='job_posts'}{/if} - {$aTBPackage.expire_text}
		</label><br />
		{foreachelse}
			{phrase var='no_package_found'}
		{/foreach}
	</div>
</div>

{if $bCanFeature}
<div class="table_right">
	<label><input type="checkbox" name="feature" value="1"/> {phrase var='feature_this_job_with_featurefee' featurefee=$featurefee}</label>
</div>
{/if}

<div class="text-right" style="padding-top: 20px;">
	<input type="button" class="btn btn-primary" value="Publish" onclick="publishJob();" id="js_job_publish_btn" /><span id="js_job_publish_loading"></span>
</div>
