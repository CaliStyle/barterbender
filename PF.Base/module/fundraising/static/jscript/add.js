/**
 * Created with JetBrains PhpStorm.
 * User: datlv
 * Date: 10/24/12
 * Time: 10:51 AM
 * To change this template use File | Settings | File Templates.
 */

var iMinPredefined = 2;
var iMaxPredefined = 5;

/*
 * set max min predefined
 */

/*
 * add predefined textbox
 */

function appendPredefined(sId)
{
    iCnt = 0;
    $('.js_predefined').each(function()
    {
        if ($(this).parents('.placeholder:visible').length)
            iCnt++;
    });

    if (iCnt >= iMaxPredefined)
    {
        alert(oTranslations['you_reach_the_maximum_of_total_predefined'].replace('{total}', iMaxPredefined));
        return false;
    }

    //iCnt++;
    var oCloned = $('.placeholder:first').clone();

    oCloned.find('.js_predefined').attr('class' , 'form-control js_predefined number greater_than_minimum');
    oCloned.find('.js_predefined').attr('name' , 'val[predefined][' + iCnt + ']');
    oCloned.find('.js_predefined').attr('value' , '');
    var oFirst = oCloned.clone();

    var firstAnswer = oFirst.html();

    $(sId).parents('.ynf_js_prev_block').parents('.placeholder').after('<div class="placeholder">' + firstAnswer + '</div>')
    return false;
}

/**
 * remove predefined textbox
 */
function removePredefined(sId)
{
    iCnt = 0;

    $('.js_predefined').each(function()
    {
        iCnt++;
    });

    if (iCnt <= iMinPredefined)
    {
        alert(oTranslations['you_must_have_a_minimum_of_total_predefined'].replace('{total}', iMinPredefined));
        return false;
    }

    /*
     $(sId).parents('.placeholder').fadeOut(900, function(){
     $(this).remove();
     });
     */

    $(sId).parents('.placeholder').remove();

    return false;
}

/**
 * check paypal account
 *
 */

function checkPaypalAccount()
{
    var sEmails = $('#paypal_account').val();
    if(sEmails.length == 0)
        return false;
    var aEmails = sEmails.split(',');

    if(aEmails.length == 0)
    {
        $('html, body').animate({scrollTop:0}, 0);
        return false;
    }
    for (var i = 0; i < aEmails.length; i++)
    {
        if ($.trim(aEmails[i]).search(/^[0-9a-zA-Z]([\-.\w]*[0-9a-zA-Z]?)*@([0-9a-zA-Z][\-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,}$/) == -1)
        {
            bIsValid = false;
            $('#ynfr_edit_campaign_form_msg').message(oTranslations['provide_a_valid_email_address'], 'error');
            $('#paypal_account').addClass('alert_input');
            $('html, body').animate({scrollTop:0},0);
            return false;
        }

    }

    return true;
}

/*
 * add sponsor level
 */

function ynfr_addMoreLevels()
{
    iCnt = 0;
    $('div #ynfr_sholder').each(function()
    {
        iCnt++;
    });

    var oCloned = $('.ynfr_sample_holder').clone();

    oCloned.find('#amount').attr('class' , 'ynfr_amount ynfr required number ynfr_sponsor_level_amount');
    oCloned.find('#amount').attr('name', 'val[sponsor_level][' + iCnt + '][amount]');
    oCloned.find('#amount').val('');
    oCloned.find('#level_name').attr('class' , 'ynfr_level_name ynfr required');
    oCloned.find('#level_name').attr('name', 'val[sponsor_level][' + iCnt + '][level_name]');
    oCloned.find('#level_name').val('');
    oCloned.removeClass('ynfr_sample_holder');
    oCloned.show();
    var oFirst = oCloned.clone();

    var firstAnswer = oFirst.html();

    $('#ynfr_sponsor_holder').find('#ynfr_sholder:last').after('<div id="ynfr_sholder">' + firstAnswer + '</div>')

    return false;
}

/*
 * remove sponsor level
 */

function ynfr_removeLevels(sId)
{
    $(sId).parents('#ynfr_sholder').remove();

    return false;
}

/*
 check email on client side
 */

function ynfr_checkEmails(aName)
{
    var sEmails = $(aName).val();
    if(sEmails.length == 0)
    {
        $('#ynfr_edit_campaign_form_msg').message(oTranslations['provide_a_valid_email_address'], 'error');
        $(aName).addClass('alert_input');
        $('html, body').animate({scrollTop:0},0);
        return false;
    }
    var aEmails = sEmails.split(',');

    if(aEmails.length == 0)
    {
        $('html, body').animate({scrollTop:0}, 0);
        return false;
    }
    for (var i = 0; i < aEmails.length; i++)
    {
        if ($.trim(aEmails[i]).search(/^[0-9a-zA-Z]([\-.\w]*[0-9a-zA-Z]?)*@([0-9a-zA-Z][\-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,}$/) == -1)
        {
            bIsValid = false;
            $('#ynfr_edit_campaign_form_msg').message(oTranslations['provide_a_valid_email_address'], 'error');
            $(aName).addClass('alert_input');
            $('html, body').animate({scrollTop:0},0);
            return false;
        }

    }

    return true;
}

$Core.fundraising =
{
    sUrl: '',

    url: function (sUrl) {
        this.sUrl = sUrl;
    },

    action: function (oObj, sAction) {
        aParams = $.getParams(oObj.href);

        $('.dropContent').hide();

        switch (sAction) {
            case 'edit':
                window.location.href = this.sUrl + 'add/id_' + aParams['id'] + '/';
                break;
            case 'delete':
                var url = this.sUrl;
                $Core.jsConfirm({}, function () {
                    window.location.href = url + 'delete_' + aParams['id'] + '/';
                }, function () {
                });
                break;
            default:

                break;
        }

        return false;
    },

    dropzoneOnSending: function (data, xhr, formData) {
        $('#js_fundraising_form_upload_images').find('input[type="hidden"]').each(function () {
            formData.append($(this).prop('name'), $(this).val());
        });
    },

    dropzoneOnSuccess: function (ele, file, response) {
        $Core.fundraising.processResponse(ele, file, response);
    },

    dropzoneOnError: function (ele, file) {

    },
    dropzoneQueueComplete: function () {
        $('#js_listing_done_upload').show();
    },
    processResponse: function (t, file, response) {
        response = JSON.parse(response);
        if (typeof response.id !== 'undefined') {
            file.item_id = response.id;
            if (typeof t.data('submit-button') !== 'undefined') {
                var ids = '';
                if (typeof $(t.data('submit-button')).data('ids') !== 'undefined') {
                    ids = $(t.data('submit-button')).data('ids');
                }
                $(t.data('submit-button')).data('ids', ids + ',' + response.id);
            }
        }
        // show error message
        if (typeof response.errors != 'undefined') {
            for (var i in response.errors) {
                if (response.errors[i]) {
                    $Core.dropzone.setFileError('fundraising', file, response.errors[i]);
                    return;
                }
            }
        }
        return file.previewElement.classList.add('dz-success');
    }
};