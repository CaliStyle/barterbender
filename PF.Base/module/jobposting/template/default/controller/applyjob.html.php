

{literal}
<script type="text/javascript">
	function radioresume(itype){
		if(itype==0){
			$('#div_photo_resume').show();
			$('#div_list_resume').hide();
		}
		else
		{
			$('#div_photo_resume').hide();
			$('#div_list_resume').show();
		}
	}
</script>{/literal}

<div class="ynjp_apply_header_holder">
	<div class="ynjp_applyformTitle"> {$aCompany.form_title} </div>
	<p class="ynjp_applyformDesc"> {$aCompany.form_description} </p>
</div>
<div class="clear"> </div>
<div class="ynjp_apply_Jobheader_holder">
	<a title="Postcards" href="{permalink module='jobposting' id=$aJob.job_id title=$aJob.title}" class="ynjp_applyform_infoThumb">
		{if $aCompany.logo_path==""}
			{img server_id=$aCompany.server_id path='core.url_pic' file="jobposting/".$aCompany.image_path suffix='_50' max_width='50' max_height='50' class='js_mp_fix_width'}
		{else}
			{img server_id=$aCompany.server_id path='core.url_pic' file="jobposting/".$aCompany.logo_path suffix='_50' max_width='50' max_height='50' class='js_mp_fix_width'}
		{/if}
	</a>
	<div class="ynjp_applyJobform_Info">
        {if !empty($aCompany.job_title_enable)}
		<div class="ynjp_applyJobTitle"> {$aJob.title} </div>
        {/if}
		<p class="ynjp_applyformInfo"> <strong>{$aCompany.name}</strong> - {$aCompany.industrial_phrase} </p>
	</div>
</div>
<div class="clear"> </div>

<form method="post" enctype="multipart/form-data" action="{if isset($fromComponent)}{permalink module='jobposting.applyjob' id=null}{else}{permalink module='jobposting.applyjob' id=$iTransactionId}{/if}" id="form_apply_job" class="ynjp_apply_Job_form">
	<div>
		{if isset($fromComponent)}
			<input type="hidden" name="jobID" value="{$jobID}">
			<input type="hidden" name="fromComponent" id="fromComponent" value="{$fromComponent}">
		{/if}
		{if isset($iTransactionId)}
			<input type="hidden" name="iTransactionId" id="iTransactionId" value="{$iTransactionId}">
		{/if}
	</div>

	{if isset($aCompany.candidate_name_enable) && $aCompany.candidate_name_enable==1}
<div class="table form-group">
	<div class="table_left">
		<label for="">
			{if isset($aCompany.candidate_name_require) && $aCompany.candidate_name_require==1}{required}{/if}{phrase var='your_name'}
		</label>
	</div>
	<div class="table_right">
		<input type="text" name="val[name]" class="form-control" value="{if isset($aForms.name)}{$aForms.name}{else}{$defaultFullName}{/if}"/>
	</div>
</div>
{/if}

{if isset($aCompany.candidate_photo_enable) && $aCompany.candidate_photo_enable==1}
<div class="table form-group">
	<div class="table_left">
		<label for="">
			{if isset($aCompany.candidate_photo_require) && $aCompany.candidate_photo_require==1}{required}{/if}{phrase var='your_photo'}
		</label>
	</div>
	<div class="table_right">
		<input id="image" type="file" name="image">
	</div>
</div>
{/if}

{if isset($aCompany.candidate_email_enable) && $aCompany.candidate_email_enable==1}
<div class="table form-group">
	<div class="table_left">
		<label for="">
		{if isset($aCompany.candidate_email_require) && $aCompany.candidate_email_require==1}{required}{/if}{phrase var='your_email'}
		</label>
	</div>
	<div class="table_right">
		<input type="text" name="val[email]" class="form-control" value="{if isset($aForms.email)}{$aForms.email}{/if}"/>
	</div>
</div>
{/if}

{if isset($aCompany.candidate_telephone_enable) && $aCompany.candidate_telephone_enable==1}
<div class="table form-group">
	<div class="table_left">
		<label for="">
		{if isset($aCompany.candidate_telephone_require) && $aCompany.candidate_telephone_require==1}{required}{/if}{phrase var='your_telephone'}
		</label>
	</div>
	<div class="table_right">
		<input type="text" name="val[telephone]" class="form-control" value="{if isset($aForms.telephone)}{$aForms.telephone}{/if}"/>
	</div>
</div>
{/if}

{if count($aFields)}
    {foreach from=$aFields item=aField}
        {template file='jobposting.block.custom.form'}
    {/foreach}
{/if}

<div class="table form-group">
	<label for="">
		{phrase var='resume'}
	</label>

        <div class="radio" {if !$module_resume || !$aCompany.resume_enable}style="display:none"{/if}>
            <label for="">
                <input onclick="radioresume(0);" value="0" type="radio" name="val[resume_type]" checked="true"/>
                {phrase var='upload_file'}
            </label>
        </div>
		{if $module_resume && $aCompany.resume_enable}
			<div class="radio">
				<label for="">
					<input onclick="radioresume(1);" value="1" type="radio" name="val[resume_type]" {if isset($aForms.resume_type) && $aForms.resume_type==1}checked="true"{/if}/>
					{phrase var='use_my_resume'}
				</label>
			</div>
		{/if}

		<div id="div_photo_resume" {if isset($aForms.resume_type) && $aForms.resume_type==1}style="display:none"{/if}>
			<input id="resume" type="file" name="resume">
			<div>{phrase var='format_ms_word_pdf_zip_size_kb_maximum' size=$jobposting_maximum_upload_size_resume}</div>
		</div>

		<div id="div_list_resume" {if !isset($aForms.resume_type) || $aForms.resume_type==0}style="display:none"{/if}>
			{if $module_resume && $aCompany.resume_enable}
				{if count($aResumes)>0}
					<select name="val[list_resume]" class="form-control">
						{foreach from = $aResumes item=aResume}
							<option value="{$aResume.resume_id}">{$aResume.headline}</option>
						{/foreach}
					</select>
				{else}
					{phrase var='sorry_you_don_t_have_any_resume_click' link=$resumeaddlink}
				{/if}
			{/if}
		</div>
	</div>

<div class="table ynjp_applyForm_submit clearfix">
	<input type="submit" class="btn btn-primary btn-sm" value="{phrase var='apply'}"/>
</div>

</form>



