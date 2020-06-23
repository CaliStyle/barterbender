<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>

{if $is_import || $bIsEdit}


<div>
<h3  class="yns add-res">
<ul class="yns menu-add">
	<li>{required}{_p var='basic_info'}</li>
</ul>
</h3>
</div>
{$sCreateJs}
<form method="post" class="yresume_add_form" action="{url link='resume.add'}{if $id!=0}id_{$id}/{/if}" onsubmit="return startProcess(custom_js_event_form(), false);" name='js_resume_add_form' id="js_resume_add_form" enctype="multipart/form-data">
<input type="hidden" id="required_custom_fields" value='{$sOutJs}'/>
<div>
    {if $aPers.get_basic_information}
    <input type="hidden" value="1" name="val[is_synchronize]" >
    {else}
    <div class="summary_label">
		<input type="checkbox" class="checkbox"
			   id="syn_profile"
			   value="1"
			   {if isset($aForms.is_synchronize) && $aForms.is_synchronize}
			   checked="checked" {/if} id="is_synchronize" name="val[is_synchronize]" >
		<label for="syn_profile">
        <strong>{_p var='synchronize_with_basic_information_in_profile'}</strong>
		</label>
			<br/>
        {_p var='your_basic_information_will_be_changed_if_you_change_the_below'}
	</div>
    {/if}
    {if $aPers.display_date_of_birth || $aPers.display_gender || $aPers.display_relation_status}
       
    {/if}
    <div class="ynr-add-sumary summary_content">
		<div class="table form-group">
			<div class="table_left">
				<label for="full_name">{required}{_p var='full_name'}:</label>
			</div>
			<div class="table_right" {if $aPers.get_basic_information} style="padding: 2px 0 6px;" {/if}>
                {if $aPers.get_basic_information}
                    <label class="default_profile_info">{if $aPers.get_basic_information} {value type='input' id='full_name'} {/if}</label>
                    <input type="hidden" value="{value type='input' id='full_name'}" name="val[full_name]" >
                {else}
                    <input class="form-control" type="text" name="val[full_name]"  value="{value type='input' id='full_name'}" id="full_name" size="30" maxlength="200" />
                {/if}
			</div>
		</div>

        <div class="table form-group-follow">
			<div class="table_left">
				<label for="full_name">{required}{_p var='date_of_birth'}:</label>
				<input type="checkbox" class="checkbox"
					   {if isset($aForms.display_date_of_birth) && $aForms.display_date_of_birth} checked="checked" {/if} value="1" id="display_date_of_birth" name="val[display_date_of_birth]">
				</input>
				<label for="display_date_of_birth">{_p var='display'}</label>
			</div>
			<div class="table_right" {if $aPers.get_basic_information} style="padding: 2px 0 6px;" {/if}>
                {if $aPers.get_basic_information}
                    <label class="default_profile_info">{if $aPers.get_basic_information} {value type='input' id='birth_day_full'} {/if}</label>
                    <input type="hidden" value="{value type='input' id='month'}" name="val[month]" />
                    <input type="hidden" value="{value type='input' id='day'}" name="val[day]" />
                    <input type="hidden" value="{value type='input' id='year'}" name="val[year]" />
                {else}
                    {select_date start_year=$sDobStart end_year=$sDobEnd field_separator=""  field_order='MDY' bUseDatepicker=false sort_years='DESC'}
                {/if}
			</div>
            <div class="ynr-display ynr-align-right">


            </div>
		</div>

        <div class="table form-group-follow" >
			<div class="table_left">
				<label for="full_name">{required}{_p var='gender'}:</label>
				<input type="checkbox" class="checkbox" {if isset($aForms.display_gender) && $aForms.display_gender} checked="checked" {/if} value="1" id="display_gender" name="val[display_gender]" >
				</input>
				<label for="display_gender">{_p var='display'}</label>
			</div>
			<div class="table_right" {if $aPers.get_basic_information} style="padding: 2px 0 6px;" {/if}>
                {if $aPers.get_basic_information}
                    <label class="default_profile_info">{if $aPers.get_basic_information} {value type='input' id='gender_phrase'} {/if}</label>
                    <input type="hidden" value="{value type='input' id='gender'}" name="val[gender]" />
                {else}
                    {select_gender}
                {/if}
			</div>
            <div class="ynr-display ynr-align-right">

            </div>
		</div>

        <div class="table form-group-follow" >
			<div class="table_left">
				<label for="full_name">{_p var='marital_status'}:</label>
				<input type="checkbox" class="checkbox" {if isset($aForms.display_marital_status) && $aForms.display_marital_status} checked="checked" {/if} value="1" id="display_marital_status" name="val[display_marital_status]" >
				<label for="display_marital_status">{_p var='display'}</label>
			</div>
			<div class="table_right" {if $aPers.get_basic_information} style="padding: 2px 0 6px;" {/if}>
                {if $aPers.get_basic_information}
                    <label class="default_profile_info">{if $aPers.get_basic_information} {value type='input' id='marital_status_phrase'} {/if}</label>
                    <input type="hidden" value="{value type='input' id='marital_status'}" name="val[marital_status]" />
                {else}
                    <select class="form-control" name="val[marital_status]">
                        <option value='single' {if !empty($aForms) and $aForms.marital_status=='single'}selected{/if}>{_p var='single'}</option>
                        <option value='married' {if !empty($aForms) and $aForms.marital_status=='married'}selected{/if}>{_p var='married'}</option>
                        <option value='other' {if !empty($aForms) and $aForms.marital_status=='others'}selected{/if}>{_p var='others'}</option>
                    </select>
                {/if}
			</div>
            <div class="ynr-display ynr-align-right">
                {if $aPers.display_relation_status}

                {/if}
            </div>
		</div>

        <div class="table form-group">
			<div class="table_left">
				<label for="city">{_p var='city'}:</label>
			</div>
			<div class="table_right" {if $aPers.get_basic_information} style="padding: 2px 0 6px;" {/if}>
                {if $aPers.get_basic_information}
                    <label class="default_profile_info">{if $aPers.get_basic_information} {value type='input' id='city'} {/if}</label>
                    <input type="hidden" value="{value type='input' id='city'}" name="val[city]" />
                {else}
                    <input class="form-control" type="text" name="val[city]" id="city" value="{value type='input' id='city'}" size="30" />
                {/if}
			</div>
			<div class="clear"></div>
		</div>

		<div class="table form-group">
			<div class="table_left">
				<label for="zip_code">{_p var='zip_postal_code'}:</label>
			</div>
			<div class="table_right" {if $aPers.get_basic_information} style="padding: 2px 0 6px;" {/if}>
                {if $aPers.get_basic_information}
                    <label class="default_profile_info">{if $aPers.get_basic_information} {value type='input' id='zip_code'} {/if}</label>
                    <input type="hidden" value="{value type='input' id='zip_code'}" name="val[zip_code]" />
                {else}
                    <input class="form-control" type="text" name="val[zip_code]" id="zip_code" value="{value type='input' id='zip_code'}" size="10" />
                {/if}
			</div>
			<div class="clear"></div>
		</div>
    </div>
</div>

<div>
	<div class="summary_content">
		<div class="table form-group-follow">
			<div class="table_left table_left_add">
				<label for="phonenumber">{_p var='phone_number'}:</label>
			</div>
			<div class="table_right">
				{if !empty($aForms.phone) and count($aForms.phone)>0}
				{foreach from=$aForms.phone item=aPhone name=iphone}
				<div class="placeholder_phone">
	                <div class="js_prev_block_phone">
	                	<span class="class_answer" >
	                    	<input type="text" name="val[phone][]" value="{$aPhone.text}" size="30" class="js_predefined_phone form-control" />
	                    </span>
	                    	<select class="form-control" name="val[phonestyle][]">
								<option value='home' {if $aPhone.type=='home'}selected{/if}>{_p var='home'}</option>
								<option value='work' {if $aPhone.type=='work'}selected{/if}>{_p var='work'}</option>
								<option value='mobile' {if $aPhone.type=='mobile'}selected{/if}>{_p var='mobile'}</option>
							</select>
	                        <a href="#" class="add_icon" onclick="return appendPredefined(this,'phone');">
	                        	{img theme='misc/add.png' class='v_middle'}
	                        </a>
	                        <a href="#" class="remove_icon" onclick="return removePredefined(this,'phone');">
	                        	{img theme='misc/delete.png' class='v_middle'}
	                       </a>
	             	 </div>
	             </div>
	             {/foreach}
	             {else}
	             <div class="placeholder_phone">
	                <div class="js_prev_block_phone">
	                	<span class="class_answer">
	                    	<input type="text" name="val[phone][]" value="" size="30" class="js_predefined_phone form-control" />
	                    </span>
	                    	<select class="form-control" name="val[phonestyle][]">
								<option value='home'>{_p var='home'}</option>
								<option value='work'>{_p var='work'}</option>
								<option value='mobile'>{_p var='mobile'}</option>
							</select>
	                        <a href="#" class="add_icon" onclick="return appendPredefined(this,'phone');">
	                        	{img theme='misc/add.png' class='v_middle'}
	                        </a>
	                        <a href="#" class="remove_icon" onclick="return removePredefined(this,'phone');">
	                        	{img theme='misc/delete.png' class='v_middle'}
	                       </a>
	             	 </div>
	             </div>
	             {/if}
			</div>
		</div>

		<div class="table form-group-follow">
			<div class="table_left table_left_add">
				<label for="im">{_p var='im'}:</label>
			</div>
			<div class="table_right">
				{if !empty($aForms.imessage) and count($aForms.imessage)>0}
				{foreach from=$aForms.imessage item=aimessage name=imessage}
				<div class="placeholder_image">
	            	<div class="js_prev_block_image">
	                	<span class="class_answer">
	                    	<input type="text" name="val[homepage][]" value="{$aimessage.text}" size="30" class="js_predefined_imail form-control" />
	                   	</span>
	                    <select class="form-control" name="val[homepagestyle][]">
							<option value='aim' {if $aimessage.type=='aim'}selected{/if}>{_p var='aim'}</option>
							<option value='skype' {if $aimessage.type=='skype'}selected{/if}>{_p var='skype'}</option>
							<option value='windows_live_messenger' {if $aimessage.type=='windows_live_messenger'}selected{/if}>{_p var='windows_live_messenger'}</option>
							<option value='yahoo_messenger' {if $aimessage.type=='yahoo_messenger'}selected{/if}>{_p var='yahoo_messenger'}</option>
							<option value='icq' {if $aimessage.type=='icq'}selected{/if}>{_p var='icq'}</option>
							<option value='gtalk' {if $aimessage.type=='gtalk'}selected{/if}>{_p var='gtalk'}</option>
						</select>
	                    <a href="#" class="add_icon" onclick="return appendPredefined(this,'homepage');">
	                    	{img theme='misc/add.png' class='v_middle'}
	                    </a>
	                    <a href="#" class="remove_icon" onclick="return removePredefined(this,'homepage');">
	                    	{img theme='misc/delete.png' class='v_middle'}
	                    </a>
	                    </div>
	             </div>
				 {/foreach}
				 {else}
				 <div class="placeholder_image">
	            	<div class="js_prev_block_image">
	                	<span class="class_answer">
	                    	<input type="text" name="val[homepage][]" value="" size="30" class="js_predefined_imail form-control" />
	                   	</span>
	                    <select class="form-control" name="val[homepagestyle][]">
							<option value='aim'>{_p var='aim'}</option>
							<option value='skype'>{_p var='skype'}</option>
							<option value='windows_live_messenger'>{_p var='windows_live_messenger'}</option>
							<option value='yahoo_messenger'>{_p var='yahoo_messenger'}</option>
							<option value='icq'>{_p var='icq'}</option>
							<option value='gtalk'>{_p var='gtalk'}</option>
						</select>
	                    <a href="#" class="add_icon" onclick="return appendPredefined(this,'homepage');">
	                    	{img theme='misc/add.png' class='v_middle'}
	                    </a>
	                    <a href="#" class="remove_icon" onclick="return removePredefined(this,'homepage');">
	                    	{img theme='misc/delete.png' class='v_middle'}
	                    </a>
	                    </div>
	             </div>
				 {/if}
			</div>
		</div>

		<div class="table form-group-follow">
			<div class="table_left table_left_add">
				<label for="emailaddress">{_p var='email_address'}:</label>
			</div>
			<div class="table_right">
				{if !empty($aForms) and count($aForms.email)>0}
				{foreach from=$aForms.email item=aemail name=iemail}
				<div class="placeholder">
	            	<div class="js_prev_block">
	                	<span class="class_answer">
	                    	<input type="text" name="val[emailaddress][]" value="{$aemail}" size="30" class="js_predefined v_middle form-control" />
	                    </span>
	                    <a href="#" class="add_icon" onclick="return appendPredefined(this,'emailaddress');">
	                    	{img theme='misc/add.png' class='v_middle'}
	                    </a>
	                    <a href="#" class="remove_icon" onclick="return removePredefined(this,'emailaddress');">
	                    	{img theme='misc/delete.png' class='v_middle'}
	                    </a>
	                    </div>
	               </div>
				{/foreach}
				{else}
				<div class="placeholder">
	            	<div class="js_prev_block">
	                	<span class="class_answer">
	                    	<input type="text" name="val[emailaddress][]" value="" size="30" class="js_predefined v_middle form-control" />
	                    </span>
	                    <a href="#" class="add_icon" onclick="return appendPredefined(this,'emailaddress');">
	                    	{img theme='misc/add.png' class='v_middle'}
	                    </a>
	                    <a href="#" class="remove_icon" onclick="return removePredefined(this,'emailaddress');">
	                    	{img theme='misc/delete.png' class='v_middle'}
	                    </a>
	                    </div>
	               </div>
				{/if}
			</div>
		</div>

		 {module name='resume.custom'}

        {if !empty($aForms.current_image) && !empty($aForms.resume_id)}
            {module name='core.upload-form' type='resume' current_photo=$aForms.current_image id=$aForms.resume_id}
        {else}
            {module name='core.upload-form' type='resume' }
        {/if}

        {if Phpfox::isModule('privacy')}
		<div class="table form-group-follow">
			<div class="table_left">
				{_p var='privacy'}:
			</div>
			<div class="table_right">
				{module name='resume.privacy.form' privacy_name='privacy' privacy_info='resume.control_who_can_see_this_resume' default_privacy='blog.default_privacy_setting' privacy_no_custom=true}
			</div>
		</div>
		{/if}

		<div class="table_clear">

				<input type="submit" class="button btn btn-primary btn-sm" name="addresume" value = "{_p var='update'}"/>

		</div>
	</div>
</div>
</form>
{else}
<div class="error_message">
{_p var='each_users_only_can_create_maximum_limit_resume' limit=$total_allowed}
</div>
{/if}
{literal}
<script type="text/javascript">
    $Behavior.onAddResumeCheckDefine = function(){
        iCnt1 = 0; iCnt2 = 0; iCnt3 = 0;      
        $('.js_predefined').each(function()
        {
            iCnt1++;
        });
        if (iCnt1 <= 1)
        {
            $('.js_prev_block .remove_icon').css('display','none');            
        }
        $('.js_predefined_phone').each(function()
        {
            iCnt2++;
        });
        if (iCnt2 <= 1)
        {
            $('.js_prev_block_phone .remove_icon').css('display','none');  
        }        
        $('.js_predefined_imail').each(function()
        {
            iCnt3++;
        });
        if (iCnt3 <= 1)
        {
            $('.js_prev_block_image .remove_icon').css('display','none');  
        }        

    }
</script>
{/literal}
