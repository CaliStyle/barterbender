<script type="text/javascript" src="{$sCorePath}module/socialad/static/jscript/ynsocialad.js"></script>
<form id='ynsa_js_add_ad_form' action="{url link='socialad.ad.add' package=$iSaPackageId}" enctype="multipart/form-data" method="post">

	<input type="hidden" name="val[image_path]" value="{value type='input' id='image_path'}" id="js_ynsa_ad_image_path" />
	<input type="hidden" value="{$sSocialadImageRoot}" id="js_ynsa_socialad_image_root" />
	<input type="hidden" name="val[ad_package_id]" value="{$iSaPackageId}" />
	<input id="js_ynsa_ad_id" type="hidden" name="val[ad_id]" value="{value type='input' id='ad_id'}" />
	<input type="hidden" id="js_ynsa_is_in_edit" value="{if isset($aForms)}1{else}0{/if}"/>


{module name='socialad.sub-menu'}
{module name='socialad.ad.addedit.basic-info'}
{module name='socialad.ad.addedit.placement'}
{module name='socialad.ad.addedit.pricing'}
{module name='socialad.ad.addedit.audience'}
	<div class="clear"></div>
	{template file='socialad.block.termcondition' sSaSubmitPhrase=$sSaSubmitPhrase}
	<div class="clear"></div>

<div class="ynsaAddButtonGroup" >
{if $sSaSubmitPhrase}
	<input type="submit" value="{$sSaSubmitPhrase}" onclick="
		if($('#ynsa_term_condition_field').length > 0){l}
			if($('#ynsa_term_condition_field').is(':checked')){l}
				return true;
			{r}else{l}
				alert('{phrase var='you_must_accept_term_and_condition_before_finishing_creating_the_ads'}');
				return false;
			{r}
		{r}else{l}
			return true;
		{r}
		"
		name="val[action_placeorder]" class="btn btn-primary btn-sm" style="margin-right:3px;" />
 {/if}
	<input type="submit" value="{phrase var='save'}" name="val[action_save]" class="btn btn-success btn-sm" button_off" />
</div>

</form>

{template file='socialad.block.ad.addedit.upload-image'}

<div id="js_ynsa_loading_large_image" style="display:none;">{img theme='ajax/large.gif'}</div>
<div id="js_ynsa_loading_fb_small_image" style="display:none;">{img theme='ajax/add.gif'}</div>
<script type="text/javascript">
	$Behavior.ynsaInitAddForm = function() {l}
		if ($('#ynsa_js_add_ad_form').length) {l}
			ynsocialad.addForm.init();
		{r}
	{r}
</script>
