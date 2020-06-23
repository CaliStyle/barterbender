<div id="yndirectory_business_detail_module_contactus" class="yndirectory_business_detail_module_contactus">	
	{if isset($sInform)}
		<div style="margin-bottom: 10px; display: block;" class="public_message" id="js_custom_public_message">
			{$sInform}
		</div>
	{/if}
	<form enctype="multipart/form-data" action="{$sFormUrl}" method="post">
		
		<!-- hidden field -->
		<div>
			<input type="hidden" id="yndirectory_detail_submit_sign" name="val[yndirectory_detail_submit_sign]" value="contactus">
			<input type="hidden" id="yndirectory_contactus_businessid" name="val[yndirectory_contactus_businessid]" value="{$aYnDirectoryDetail.aBusiness.business_id}">
			<input type="hidden" id="yndirectory_contactus_contactusid" name="val[yndirectory_contactus_contactusid]" value="{$aContactUs.contactus_id}">
		</div>
		<div class="form-group contact-info-block">{_p var='if_you_want_to_ask_us_a_question_directly_please_submit_your_message_with_the_following_form_how_can_i_help_you'}</div>

        <div class="yndirectory-contactform-item form-group">
            <label for="yndirectory_contactus_full_name">{_p var='full_name'}</label>
            <input type="text"
                value="{if isset($aContactUsForm)}
                        {$aContactUsForm.yndirectory_contactus_full_name}
                    {else}
                        {$sDefaultFullname}
                    {/if}"
                id="yndirectory_contactus_full_name" name="val[yndirectory_contactus_full_name]" class="form-control" />
        </div>
        <div class="yndirectory-contactform-item form-group">
            <label for="yndirectory_contactus_email">{_p var='email'}</label>
            <input type="text"
                value="{if isset($aContactUsForm)}
                        {$aContactUsForm.yndirectory_contactus_email}
                    {else}
                        {$sDefaultEmail}
                    {/if}"
                id="yndirectory_contactus_email" name="val[yndirectory_contactus_email]" class="form-control"/>
        </div>
        <div class="yndirectory-contactform-item form-group">
            <label for="yndirectory_contactus_department">{_p var='department'}</label>
            <select class="form-control" id="yndirectory_contactus_department" name="val[yndirectory_contactus_department]">
                <option
                    {if isset($aContactUsForm) && $aContactUsForm.yndirectory_contactus_department == $aYnDirectoryDetail.aBusiness.email}
                        checked="checked"
                    {/if}
                    value="{$aYnDirectoryDetail.aBusiness.email}">{$aYnDirectoryDetail.aBusiness.name}</option>
                {if count($aContactUs.receiver_data)}
                    {foreach from=$aContactUs.receiver_data key=iKeyReceiver item=aDataReceiver }
                        <option
                            {if isset($aContactUsForm) && $aContactUsForm.yndirectory_contactus_department == $aDataReceiver.email}
                                checked="checked"
                            {/if}
                            value="{$aDataReceiver.email}">{$aDataReceiver.department}</option>
                    {/foreach}
                {/if}
            </select>
        </div>
        <div class="yndirectory-contactform-item form-group">
            <label for="yndirectory_contactus_subject">{_p var='subject'}</label>
            <input type="text"
                value="{if isset($aContactUsForm)}
                        {$aContactUsForm.yndirectory_contactus_subject}
                    {/if}"
                id="yndirectory_contactus_subject" name="val[yndirectory_contactus_subject]" placeholder="{_p var = 'enter_subject'}" class="form-control" />
        </div>
        <div class="yndirectory-contactform-item form-group">
            <label for="yndirectory_contactus_message">{_p var='message'}</label>
            <textarea class="form-control" cols="59" rows="10" id="yndirectory_contactus_message" name="val[yndirectory_contactus_message]" placeholder="{_p var = 'enter_message'}">
                {if isset($aContactUsForm)}
                    {$aContactUsForm.yndirectory_contactus_message}
                {/if}</textarea>
        </div>

        <!-- custom field -->
        {if count($aContactUsCustomfield)}
            {foreach from=$aContactUsCustomfield item=aField}

                {template file='directory.block.contactuscustomfieldform'}

            {/foreach}
        {/if}

        <div class="yndirectory-contactform-input form-group yndirectory-contactform-btngroup">
            <button type="submit" value="{phrase var='send'}" class="btn btn-primary" id="yndirectory_contactus_send">{_p var='send'}</button>
            <a class="btn btn-default" id="yndirectory_contactus_cancel" href="#">{_p var='cancel'}</a>
        </div>
	</form>
</div>

{literal}
<script type="text/javascript">
    $Behavior.yndirectory_contactus_init = function() {
    	if($('#yndirectory_business_detail_module_contactus').length > 0){
    		yndirectory.initBusinessDetailContactUs();
    	} 
    }        
</script>
{/literal}
