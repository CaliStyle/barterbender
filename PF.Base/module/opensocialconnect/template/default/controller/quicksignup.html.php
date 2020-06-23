<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
{literal}
<style>
div#header_user_register_holder {
	display:none;
}
</style>
<script type="text/javascript">
$Behavior.termsAndPrivacy = function()
{
	$('#js_terms_of_use').click(function()
	{
		{/literal}
		tb_show('{phrase var='user.terms_of_use' phpfox_squote=true phpfox_squote=true phpfox_squote=true phpfox_squote=true phpfox_squote=true phpfox_squote=true}', $.ajaxBox('page.view', 'height=410&width=600&title=terms')); 
		{literal}
		return false;
	});
	
	$('#js_privacy_policy').click(function()
	{
		{/literal}
		tb_show('{phrase var='user.privacy_policy' phpfox_squote=true phpfox_squote=true phpfox_squote=true phpfox_squote=true phpfox_squote=true phpfox_squote=true}', $.ajaxBox('page.view', 'height=410&width=600&title=policy')); 
		{literal}
		return false;
	});
	$('#fwpasswordcheckbox').click(function(){
		if($(this).is(":checked"))
		{
			$('#manual_password').slideUp();
		}
		else
		{
			$('#manual_password').slideDown();
		}
	});
}
function Validation_js_form()
{ return true; }
</script>
{/literal}
{if isset($step) && $step =='syncuser'}
   <form method="post" action="{url link='opensocialconnect.syncuser'}" id="js_form">
    <div>
        <h1>{phrase var='user.sign_up_for_ssitetitle' sSiteTitle=$sSiteTitle} {phrase var='opensocialconnect.by_using'} {$aService.title}</h1>
        <div class="extra_info">
            {phrase var='user.join_ssitetitle_to_connect_with_friends_share_photos_and_create_your_own_profile' sSiteTitle=$sSiteTitle}
        </div>
        <div>
            <div class="main_break" style="margin-bottom:5px;">
                 {phrase var='opensocialconnect.this_email_already_exists_do_you_want_to_synchronize_with_this_account' email=$sEmail}
            </div>
            <div class="table_clear">
            	<input type="hidden" value="{if isset($aData.gender)}{$aData.gender}{else}0{/if}" name="val[gender]"/>
            	{if isset($aData.birthday)}
			    	 <input type="hidden" value="{$aData.birthday}" name="val[birthday]"/>
			    {/if}
			    {if isset($aData.birthday_search)}
			    	 <input type="hidden" value="{$aData.birthday_search}" name="val[birthday_search]"/>
			    {/if}
                <input type="hidden" value="{$aData.identity}" name="val[identity]"/>
                <input type="hidden" value="{$aData.service}" name="val[service]"/>    
                <input type="hidden" value="{$iSyncUserId}" name="val[user_id]"/>    
                <input type="hidden" value="{$sEmail}" name="val[email]"/>    
                <button type="submit" class="btn btn-sm btn-danger" id="js_registration_submit" value="{phrase var='opensocialconnect.synchronize'}" name="synchronize">{phrase var='opensocialconnect.synchronize'}</button>
                <button type="submit" class="btn btn-sm btn-default disable" id="" value="{phrase var='core.no'}" name="cancel">{phrase var='core.no'}</button>
            </div>
        </div>
    </div>
    </form> 
{else}
    <form method="post" action="{url link='opensocialconnect.quicksignup'}" id="js_form"{if isset($sGetJsForm)} onsubmit="{$sGetJsForm}"{/if} enctype="multipart/form-data">
    <div>
	    <h1>{phrase var='user.sign_up_for_ssitetitle' sSiteTitle=$sSiteTitle} {phrase var='opensocialconnect.by_using'} {$aService.title}</h1>
	    <div class="extra_info">
		    {phrase var='user.join_ssitetitle_to_connect_with_friends_share_photos_and_create_your_own_profile' sSiteTitle=$sSiteTitle}
	    </div>
	    <div>
		    <div class="main_break">
			    {template file='opensocialconnect.block.registerform'}
		    </div>
		    <div class="table_clear">
			    <input type="hidden" value="{if isset($aData.gender)}{$aData.gender}{else}0{/if}" name="val[gender]"/>
			    
			    {if isset($aData.birthday)}
			    	 <input type="hidden" value="{$aData.birthday}" name="val[birthday]"/>
			    {/if}
			    {if isset($aData.birthday_search)}
			    	 <input type="hidden" value="{$aData.birthday_search}" name="val[birthday_search]"/>
			    {/if}
			    
			    <input type="hidden" value="{$aData.identity}" name="val[identity]"/>
			    <input type="hidden" value="{$aData.service}" name="val[service]"/>	
			    <button type="submit" class="btn btn-sm btn-danger" id="js_registration_submit">{phrase var='user.sign_up'}</button>
		    </div>
	    </div>
    </div>
    </form>
{/if}
