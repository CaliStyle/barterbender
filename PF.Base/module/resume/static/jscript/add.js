/**
 * Created with JetBrains PhpStorm.
 * User: datlv
 * Date: 10/24/12
 * Time: 10:51 AM
 * To change this template use File | Settings | File Templates.
 */

    
var iMinPredefined = 2;
var iMaxPredefined = 5;
var iMinPredefined_imail = 2;
var iMaxPredefined_imail = 5;
var iMinPredefined_phone = 2;
var iMaxPredefined_phone = 5;


$Behavior.setMinPredefined = function() {};

function appendPredefined(sId,classname)
{
	if(classname=="emailaddress")
    { 
    	$('.js_prev_block .remove_icon').css('display','inline-block');   
    	iCnt = 0;
	    $('.js_predefined').each(function()
	    {
	        if ($(this).parents('.placeholder:visible').length)
	            iCnt++;
	    });
	
	    if (iCnt >= iMaxPredefined)
	    {
	        alert(oTranslations['resume.you_reach_the_maximum_of_total_predefined'].replace('{total}', iMaxPredefined));
	        return false;
	    }
	
	    //iCnt++;
	    var oCloned = $('.placeholder:first').clone();
		var value = 'val['+classname+'][]';
	    oCloned.find('.js_predefined').attr('class' , 'js_predefined form-control');
	    oCloned.find('.js_predefined').attr('name', value);
	    oCloned.find('.js_predefined').attr('value', '');
	    var oFirst = oCloned.clone();
	
	    var firstAnswer = oFirst.html();
	
	    $(sId).parents('.js_prev_block').parents('.placeholder').after('<div class="placeholder">' + firstAnswer + '</div>')
	    return false;
	}
	if(classname=="homepage")
	{
		$('.js_prev_block_image .remove_icon').css('display','inline-block'); 
		iCnt_imail = 0;
	    $('.js_predefined_imail').each(function()
	    {
	        if ($(this).parents('.placeholder_image:visible').length)
	            iCnt_imail++;
	    });
	
	    if (iCnt_imail >= iMaxPredefined_imail)
	    {
	        alert(oTranslations['resume.you_reach_the_maximum_of_total_predefined'].replace('{total}', iMaxPredefined_imail));
	        return false;
	    }
	
	    //iCnt++;
	    var oCloned = $('.placeholder_image:first').clone();
		var value = 'val['+classname+'][]';
	    oCloned.find('.js_predefined_imail').attr('class' , 'js_predefined_imail form-control');
	    oCloned.find('.js_predefined_imail').attr('name', value);
	    oCloned.find('.js_predefined_imail').attr('value', '');
	    var oFirst = oCloned.clone();
	
	    var firstAnswer = oFirst.html();
	
	    $(sId).parents('.js_prev_block_image').parents('.placeholder_image').after('<div class="placeholder_image">' + firstAnswer + '</div>')
	    return false;
	}
	if(classname=="phone")
	{
		$('.js_prev_block_phone .remove_icon').css('display','inline-block');
		iCnt_phone = 0;
	    $('.js_predefined_phone').each(function()
	    {
	        if ($(this).parents('.placeholder_phone:visible').length)
	            iCnt_phone++;
	    });
	
	    if (iCnt_phone >= iMaxPredefined_phone)
	    {
	        alert(oTranslations['resume.you_reach_the_maximum_of_total_predefined'].replace('{total}', iMaxPredefined_phone));
	        return false;
	    }
	
	    //iCnt++;
	    var oCloned = $('.placeholder_phone:first').clone();
		var value = 'val['+classname+'][]';
	    oCloned.find('.js_predefined_phone').attr('class' , 'js_predefined_phone form-control');
	    oCloned.find('.js_predefined_phone').attr('name', value);
	    oCloned.find('.js_predefined_phone').attr('value', '');
	    var oFirst = oCloned.clone();
	
	    var firstAnswer = oFirst.html();
	
	    $(sId).parents('.js_prev_block_phone').parents('.placeholder_phone').after('<div class="placeholder_phone">' + firstAnswer + '</div>')
	    return false;
	}
	if(classname=="website")
    { 
    	$('.js_prev_block .remove_icon').css('display','inline-block');   
    	iCnt = 0;
	    $('.js_predefined_websites').each(function()
	    {
	        if ($(this).parents('.placeholder:visible').length)
	            iCnt++;
	    });
	
	    if (iCnt >= iMaxPredefined)
	    {
	        alert(oTranslations['resume.you_reach_the_maximum_of_total_predefined'].replace('{total}', iMaxPredefined));
	        return false;
	    }
	
	    //iCnt++;
	    var oCloned = $('.placeholder:first').clone();
		var value = 'val['+classname+'][]';
	    oCloned.find('.js_predefined_websites').attr('class' , 'js_predefined_websites form-control');
	    oCloned.find('.js_predefined_websites').attr('name', value);
	    oCloned.find('.js_predefined_websites').attr('value', '');
	    var oFirst = oCloned.clone();
	
	    var firstAnswer = oFirst.html();
	
	    $(sId).parents('.js_prev_block').parents('.placeholder').after('<div class="placeholder">' + firstAnswer + '</div>')
	    return false;
	}
}

/**
 * Uses JQuery to count the answers and validate if user is allowed one less answer
 * Effect used fadeOut(1200)
 */
function removePredefined(sId,classname)
{
   

	if(classname=="emailaddress")
    {
    	iCnt = 0;
	    $('.js_predefined').each(function()
	    {
	        iCnt++;
	    });
    	if (iCnt <= 2)
    	{
    		$('.js_prev_block .remove_icon').css('display','none');   
        	//alert(oTranslations['resume.you_must_have_a_minimum_of_total_predefined'].replace('{total}', iMinPredefined));
        	//return false;
       	}
       	 $(sId).parents('.placeholder').remove();
    }
    if(classname=="homepage")
    {
    	iCnt_imail = 0;
	    $('.js_predefined_imail').each(function()
	    {
	        iCnt_imail++;
	    });
    	if (iCnt_imail <= 2)
    	{
    		$('.js_prev_block_image .remove_icon').css('display','none');
       	}
       	 $(sId).parents('.placeholder_image').remove();
    }
    if(classname=="phone")
    {
    	iCnt_phone = 0;
	    $('.js_predefined_phone').each(function()
	    {
	        iCnt_phone++;
	    });
    	if (iCnt_phone <= 2)
    	{
    		$('.js_prev_block_phone .remove_icon').css('display','none');
       	}
       	 $(sId).parents('.placeholder_phone').remove();
    }
	if(classname=="website")
    {
    	iCnt_websites = 0;
	    $('.js_predefined_websites').each(function()
	    {
	        iCnt_websites++;
	    });
	    console.log(iCnt_websites);
    	if (iCnt_websites <= 2)
    	{console.log(iCnt_websites);
    		$('.js_prev_block .remove_icon').css('display','none');
       	}
       	 $(sId).parents('.placeholder').remove();
    }
    return false;
}


function custom_js_event_form()
{

    $('#js_resume_add_form_msg').hide('');
    $('#js_resume_add_form_msg').html('');
    var bIsValid = true;
    var fields = eval($("#required_custom_fields").val());
 	
    if(fields!=null)
    for(var i=0; i<fields.length; i++)
    {
        var passed = true;
        switch(fields[i]['var_type'])
        {
            case "radio":
            case "checkbox":
                if($('input[id="cf_' + fields[i]['field_name']+'"]:checked').length==0)
                {
                    passed = false;
                }
                break;
            default:
            var value = $.trim($('#cf_' + fields[i]['field_name']).val());
            if(value == '' || value == null)
            {
                passed = false;
            }
        }
        if(!passed)
        {
            bIsValid = false; 
            $('#js_resume_add_form_msg').message(oTranslations['resume.the_field_field_name_is_required'].replace('{field_name}', fields[i]['phrase_name']), 'error');
            $('#cf_' + fields[i]['field_name']).addClass('alert_input');
        }
    }
    if (!bIsValid)
    {
        $('#js_resume_add_form_msg').show();
        window.location.hash = '#pem';
    }
    return bIsValid;
}

