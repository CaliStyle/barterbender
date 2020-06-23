<?php 
/**
 * [PHPFOX_HEADER]
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{$sCreateJs2}

<script type="text/javascript">
{literal}

function Validation_js_form_sign_customize()
{
	$('#js_form_sign_msg').hide('');
	$('#js_form_sign_msg').html('');
	var bIsValid = true;
	$(".error_message").each(function(){$(this).remove();});
    if ($('#location').val() == '')
	{
		bIsValid = false; 
		$('#js_form_sign_msg').message('{/literal}{phrase var='petition.fill_in_a_location'}{literal}', 'error');
		$('#location').addClass('alert_input');
	}

	if ($('#signature').val() == '')
	{
		bIsValid = false; 
		$('#js_form_sign_msg').message('{/literal}{phrase var='petition.please_add_a_reason'}{literal}', 'error');
		$('#signature').addClass('alert_input');
	}
    if ( bIsValid ) {
	 	var $sLink = $('.js_box_history:first').html();
        if (isset($aBoxHistory[$sLink]))
        {
            delete $aBoxHistory[$sLink];
        }
	 	return true;
    }
    $('#js_form_sign_msg').show();
    return false;
}

  function Validation_form_sign()
  {
      if (Validation_js_form_sign_customize())
      {        
        $('#js_form_sign').ajaxCall('petition.signPetition');
        $('#petition_btn_sign').html('{/literal}{img theme="ajax/add.gif" alt="" class="v_middle"}{literal}');
        $('.js_box_close').remove();
      }
	  $('#divmessage2').html($('#js_form_sign_msg').html());
	  $('#js_form_sign_msg').hide();
      return false;
  }
{/literal}
</script>
<div id="divmessage2"></div>
 
<form id="js_form_sign" method="post" action="#" onsubmit="return Validation_form_sign();">
    <input type="hidden" name="val[petition_id]" value="{$aPetition.petition_id}"/>
    <div class="table form-group">
        <div class="table_left">
            {phrase var='petition.your_location'}
        </div>
        <div class="table_right">            
            <input type="text" class="form-control" name="val[location]" id="location" style="width: 98%" maxlength="150" value="">
        </div>
    </div>
    <div class="table form-group">
        <div class="table_left">
                {phrase var='petition.add_a_reason'}                
        </div>
        <div class="table_right">
            <div class="extra_info">
                    {phrase var='petition.why_are_you_signing'}
            </div>
            <textarea class="form-control" cols="40" rows="8" name="val[signature]" id="signature" onkeyup="limitChars('signature', 255, 'js_limit_info');" style="width:98%; height:100px;"></textarea>
            <div id="js_limit_info" class="extra_info">{_p var='255_character_limit'}</div>
        </div>
    </div>
    
    <div class="table_clear">
        <ul class="table_clear_button">
            <li id="petition_btn_sign"><input 
            	 
            	type="submit" 
			
			name="val[sign]" value="{phrase var='petition.sign_petition'}" class="btn btn-success btn-sm"/></li>        
			
        </ul>
        <div class="clear"></div>
    </div>
</form>

<div id="js_thank_message" style="display:none;">
    <p align="center">{phrase var='petition.thank_you_for_signing_the_petition'}</p>
    <p align="center"><strong>{$aPetition.title}</strong></p>
    <br/>
    <p align="center">
        <button type="button" class="button btn btn-primary btn-sm" value="" onclick="return js_box_remove(this);">{phrase var='petition.ok'}</button>
        <button type="button" class="button btn btn-success btn-sm" value="" onclick="js_box_remove(this); $Core.box('petition.inviteBlock',800,'&id={$aPetition.petition_id}'); return false;">{phrase var='petition.invite_friends'}</button>
    </p>
</div>