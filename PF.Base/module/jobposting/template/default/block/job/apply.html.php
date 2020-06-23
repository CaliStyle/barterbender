
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
		{img server_id=$aCompany.server_id path='core.url_pic' file="jobposting/".$aCompany.image_path suffix='_50' max_width='50' max_height='50' class='js_mp_fix_width'} 
	</a>
	<div class="ynjp_applyJobform_Info">
		<div class="ynjp_applyJobTitle"> {$aJob.title} </div>
		<p class="ynjp_applyformInfo"> <strong>{$aCompany.name}</strong> - {$aCompany.industrial_phrase} </p>
	</div>
</div>
<div class="clear"> </div>

<form method="post" enctype="multipart/form-data" action="{permalink module='jobposting' id=$aJob.job_id title=$aJob.title}" id="form_apply_job" class="ynjp_apply_Job_form">
<div class="table form-group">
	<div class="table_left">
		{phrase var='your_name'}
	</div>
	<div class="table_right">
		<input type="text" name="val[name]" value="{if isset($aForms.name)}{$aForms.name}{/if}"/>
	</div>
</div>

<div class="table form-group">
	<div class="table_left">
		{phrase var='your_photo'}
	</div>
	<div class="table_right">
		<input id="image" type="file" name="image">
	</div>
</div>

<div class="table form-group">
	<div class="table_left">
		{phrase var='your_email'}
	</div>
	<div class="table_right">
		<input type="text" name="val[email]" value="{if isset($aForms.name)}{$aForms.name}{/if}"/>
	</div>
</div>

<div class="table form-group">
	<div class="table_left">
		{phrase var='your_telephone'}
	</div>
	<div class="table_right">
		<input type="text" name="val[telephone]" value="{if isset($aForms.telephone)}{$aForms.telephone}{/if}"/>
	</div>
</div>

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
				Upload File
			</label>
		</div>
		{if $module_resume && $aCompany.resume_enable}
			<div class="radio">
				<label for="">
					<input onclick="radioresume(1);" value="1" type="radio" name="val[resume_type]"/> 
					{phrase var='use_my_resume'}
				</label>
			</div>
		{/if}
		<div id="div_photo_resume">
			<input id="resume" type="file" name="resume">
			<div>{phrase var='format_ms_word_pdf_zip_500kb_maximum'}</div>
		</div>
		
		<div id="div_list_resume" style="display:none">
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

<div class="table form-group ynjp_applyForm_submit">
	<div class="table_left">
		&nbsp;
	</div>
	<div class="table_right">
		<input type="submit" class="btn btn-sm btn-primary" value="{phrase var='apply'}"/>
		<input type="button" class="btn btn-sm btn-warning" onclick="return js_box_remove(this);"value="Cancel"/>
	</div>
</div>
</form>


