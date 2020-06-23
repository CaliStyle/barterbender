<script type="text/javascript">
	{literal}
	$Behavior.yncontestInitializeCategoryJs = function() {
		yncontest.addContest.addCategoryJsEventListener();
	}

	{/literal}
</script>

<div id="js_contest_block_main" class="js_contest_block page_section_menu_holder">

<form method="post" class="yncontest_add_edit_form" action="{url link='current'}" id="yncontest_main_info_form"  enctype="multipart/form-data" data-validate="datepicker">
	<input type='hidden' id='yncontest_is_should_disable' value='{if $bShouldDisable}1{else}0{/if}'/>
	<input type='hidden' name='val[yncontest_is_edit]' value='{if $bIsEdit}1{else}0{/if}'/>
	<input type='hidden' name='val[publish_contest]' id="yncontest_publish_contest" value='0'/>
	<input type='hidden' name='val[contest_id]' value='{if $bIsEdit}{$aForms.contest_id}{else}0{/if}'/>
	<div id="js_custom_privacy_input_holder">
		{if $bIsEdit && empty($sModule)}
			{module name='privacy.build' privacy_item_id=$aForms.contest_id privacy_module_id='contest'}
		{/if}
		</div>

	<div><input type="hidden" name="val[attachment]" class="js_attachment" value="{value type='input' id='attachment'}" /></div>
    {if !empty($sCategories)}
    <div class="table frist_table form-group">
        <div class="table_left">
            <label for="category">{required}{phrase var='contest.category'}:</label>
        </div>
        <div class="table_right">
            {$sCategories}
        </div>
    </div>
    {/if}

	<div class="table form-group">
		<div class="table_left">
			<label for="title">{required}{phrase var='contest.contest_name'}: </label>
		</div>
		<div class="table_right">
			<input type="text" class="contest_add required yn_contest_title_max_length form-control" name="val[contest_name]" value="{value type='input' id='contest_name'}" id="yncontest_add_contest_name" size="60" />
		</div>
		<div class="extra_info">
			{phrase var='contest.you_can_enter_maximum_number_characters', number=255}
		</div>
	</div>

	<div class="table form-group">
		<div class="table_left">
			<label for="title">{required}{phrase var='contest.contest_type'}: </label>
		</div>

		<div class="table_right">

			<ul class="checklist_grp">
                {if Phpfox::isModule('blog') || Phpfox::isModule('ynblog')}
				<li class="radio-inline">
					<label>
						<input type="radio" class='contest_add contest_type_radio' name="val[contest_type]" value="blog"
						{if isset($aForms.type) && $aForms.type == $aContestTypes.blog.id} checked='yes' {/if}/>
						{phrase var='contest.blog'}
					</label>
				</li>
				{/if}
				{if Phpfox::isModule('photo') || Phpfox::isModule('advancedphoto')}
				<li class="radio-inline">
					<label>
						<input type="radio" class='contest_add contest_type_radio' name="val[contest_type]" value="photo"
						{if !$bIsEdit} checked='yes' {/if}{if isset($aForms.type) && $aForms.type == $aContestTypes.photo.id} checked='yes' {/if}/>
						{phrase var='contest.photo'}
					</label>
				</li>
				{/if}
				{if Phpfox::isModule('v') || Phpfox::isModule('videochannel') || Phpfox::isModule('ultimatevideo')}
				<li class="radio-inline">
					<label>
						<input type="radio" class='contest_add contest_type_radio' name="val[contest_type]" value="video"
						{if isset($aForms.type) && $aForms.type == $aContestTypes.video.id} checked='yes' {/if}/>
						{phrase var='contest.video'}
					</label>
				</li>
				{/if}
				{if Phpfox::isModule('music') || Phpfox::isModule('musicsharing')}
				<li class="radio-inline">
					<label>
						<input type="radio" class='contest_add contest_type_radio' name="val[contest_type]" value="music"
						{if isset($aForms.type) && $aForms.type == $aContestTypes.music.id} checked='yes' {/if}/>
						{phrase var='contest.music'}
					</label>
				</li>
                {/if}
         	</ul>
		</div>
	</div>

	<div class="table form-group">
		<div class="table_left">
			<label for="text">{required}{phrase var='contest.main_description'}:</label>
		</div>
		<div class="table_right">
			{editor id='yn_contest_add_description'}
		</div>
	</div>

	<div class="table form-group">
		<div class="table_left">
			<label for="short_description">{required}{phrase var='contest.short_description'}:</label>
		</div>
		<div class="table_right">
			<textarea cols="59" rows="10" name="val[short_description]" class="js_edit_contest_form contest_add required yn_contest_short_description_max_length form-control" id="short_description" style="height:70px;">{value id='short_description' type='textarea'}</textarea>
		</div>
		<div class="extra_info ynfr_extra_info">
			{phrase var='contest.you_can_enter_maximum_number_characters', number=160}
		</div>
	</div>

	<div class="table form-group">
		<div class="table_left">
			<label for="award">{required}{phrase var='contest.award'}:</label>
		</div>

		<div class="table_right">
			<textarea cols="59" rows="10" name="val[award]" class='contest_add required form-control' id="award" style="height:70px;">{value id='award' type='textarea'}</textarea>
		</div>
	</div>

    <div class="table form-group">
        <div class="table_left">
            <label for="term_condition">{required}{phrase var='contest.terms_and_conditions'}</label>
        </div>
        <div class="table_right">
            <textarea id="term_condition" name="val[term_condition]" class="contest_add required form-control" cols="59" rows="10" style="height: 70px;">{value type='textarea' id='term_condition'}</textarea>
        </div>
    </div>

	<div class="table form-group-follow">
		<div class="table_left">
			<label for="award">{required}{phrase var='contest.photo'}:</label>
		</div>

		<div class="table_right">
			<input type="file" class='contest_add {if !$bIsEdit} required yn_validation_file_type{/if}' name="image" id="image" >
		</div>
		<div class="extra_info">
			{phrase var='contest.you_can_upload_a_jpg_gif_or_png_file'}
			{if $sMaxFileSize !== null}
				<br />
				{phrase var='contest.the_file_size_limit_is_filesize_if_your_upload_does_not_work_try_uploading_a_smaller_picture' filesize=$sMaxFileSize}
			{/if}
		</div>
	</div>

    <!--Contest Times-->
    <div class="table form-group">
		<div class="table_left">
			<label for="">
				{phrase var='contest.contest_duration'}:
			</label>
		</div>
		<div class="table_right">
			<div class="yncontest_add_time_start ynsui_timepicker_withlabel">
				<label for="">
					{phrase var='contest.start'} 
				</label>

				{select_date prefix='begin_time_' id='_begin_time' start_year='current_year' end_year='+10' field_separator=' / ' field_order='MDY' default_all=true add_time=true time_separator='contest.time_separator'}
			</div>
            <div class="yncontest_add_time_end ynsui_timepicker_withlabel">
				<label for="">
					{phrase var='contest.end'}
				</label>
				
				{select_date prefix='end_time_' id='_end_time' start_year='current_year' end_year='+10' start_hour='+12' field_separator=' / ' field_order='MDY' default_all=true add_time=true time_separator='contest.time_separator'}
			</div>
		</div>
	</div>

    <div class="table form-group">
		<div class="table_left">
			<label for="">
				{phrase var='contest.submit_entries_duration'}:
			</label>
		</div>
		<div class="table_right">
			<div class="yncontest_add_time_start ynsui_timepicker_withlabel">
				<label for="">
					{phrase var='contest.start'} 
				</label>

				{select_date prefix='start_time_' id='_start_time' start_year='current_year' end_year='+10' field_separator=' / ' field_order='MDY' default_all=true add_time=true time_separator='contest.time_separator'}
			</div>
            <div class="yncontest_add_time_end ynsui_timepicker_withlabel">
				<label for="">
					{phrase var='contest.end'} 
				</label>

				{select_date prefix='stop_time_' id='_stop_time' start_year='current_year' end_year='+10' start_hour='+4' field_separator=' / ' field_order='MDY' default_all=true add_time=true time_separator='contest.time_separator'}
			</div>
		</div>
	</div>

    <div class="table form-group">
		<div class="table_left">
			<label for="">
				{phrase var='contest.voting_duration'}:
			</label>
		</div>
		<div class="table_right">
			<div class="yncontest_add_time_start ynsui_timepicker_withlabel">
				<label for="">
					{phrase var='contest.start'}
				</label>

				{select_date prefix='start_vote_' id='_start_vote' start_year='current_year' end_year='+10' start_hour='+4' field_separator=' / ' field_order='MDY' default_all=true add_time=true time_separator='contest.time_separator'}
			</div>
            <div class="yncontest_add_time_end ynsui_timepicker_withlabel">
				<label for="">
					{phrase var='contest.end'} 
				</label>
				
				{select_date prefix='stop_vote_' id='_stop_vote' start_year='current_year' end_year='+10' start_hour='+8' field_separator=' / ' field_order='MDY' default_all=true add_time=true time_separator='contest.time_separator'}
			</div>
		</div>
		<label>
        <input type="checkbox" name="val[vote_without_join]"{if isset($aForms.vote_without_join) && $aForms.vote_without_join==-1}{else} checked="checked"{/if} /> {phrase var='contest.allow_other_members_to_vote_for_an_entry_without_joining_the_contest'}
    </label>
	</div>
    <!--//Contest Times-->


	<div class="table form-group">
		<div class="table_left">
			<label >{required}{phrase var='contest.maxium_entries_a_participant_can_submit'} : </label>
		</div>
		<div class="table_right">
			<input type="text" class="contest_add required yn_positive_number form-control" name="val[maximum_entry]" value="{value type='input' id='maximum_entry'}" id="maximum_entry" size="60" />
		</div>
		<div class="extra_info">
			{phrase var='contest.set_0_for_unlimited_entries'}
		</div>
	</div>

	<div class="table form-group">
        <div class="table_left">
            {required}{phrase var='contest.number_of_winning_entries'}
        </div>
        <div class="table_right">
            <input type="text" name="val[num_winning_entry]" value="{value type='input' id='num_winning_entry'}" id="num_winning_entry" size="60" class='contest_add required yn_positive_number_greater_than_0 form-control' />
        </div>
        <div class="extra_info">
			{phrase var='contest.must_be_greater_or_equal_1'}
		</div>
		<label>
	        <input type="checkbox" {if isset($aForms.is_auto_approve) && $aForms.is_auto_approve} checked="true" {/if} name="val[automatic_approve]" /> {phrase var='contest.set_entries_automatically_approved'}
	    </label>
    </div>


	{if empty($sModule) && Phpfox::isModule('privacy') }
	<div class="table form-group-follow">
		<div class="table_left">
			{phrase var='contest.privacy'}:
		</div>
		<div class="table_right">
			{module name='privacy.form' privacy_name='privacy' privacy_info='contest.control_who_can_see_this_contest'  default_privacy='contest.default_privacy_setting'}
		</div>
	</div>
	{/if}

	<div class="table_clear">
		{if !$bIsEdit || !$bShouldDisable}
			<input type='submit' class="button add_contest btn btn-primary btn-sm" name='val[save_as_draft]' value="{phrase var='contest.save_as_draft'}" onclick="$('#yncontest_publish_contest').val(0);"></input>
			<input type='submit' class="button add_contest btn btn-success btn-sm" name='val[publish_contest]' value="{phrase var='contest.publish'}" onclick="$('#yncontest_publish_contest').val(1);yncontest.confirmOnAddConstest('{url link='current'}'); return false;"></input>
		{else}
			<input type='submit' class="button add_contest btn btn-primary btn-sm" name='val[save]' value="{phrase var='contest.save'}" onclick="$('#yncontest_publish_contest').val(0);"></input>
		{/if}
		<span type="submit" class="button btn btn-default btn-sm"  onclick="location.href='{url link='contest'}'"  value="{phrase var='contest.cancel'}">{phrase var='contest.cancel'}</span>
	</div>
</form>

<script type="text/javascript">

{if $bShouldDisable}
	$Behavior.yncontestDisableFields = function() {l}
		yncontest.addContest.disableFields();
	{r}
{/if}
$Behavior.initializeValidateCustomClassYncontest = function() {l}

$('#yn_contest_add_description').addClass('contest_add required');

jQuery.validator.addMethod('greater_than_minimum', function(value, element) {l}
	if(value < parseInt($('#minimum_amount').val()) && value != '')
		{l}

	return false;
	{r}

	return true;
	{r}, '{phrase var='contest.must_greater_than_minimum'}'
	);


jQuery.validator.addClassRules("greater_than_minimum", {l}
	greater_than_minimum: {l}greater_than_minimum: true{r}
	{r});


jQuery.validator.messages.maxlength = "{phrase var='contest.maximum_number_of_characters_for_this_field_is'}" + ' {l}0{r} ';
{r}
</script>


</div>