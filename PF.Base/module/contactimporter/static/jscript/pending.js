$Behavior.loadJsPendingContactImporter = function(){
(function($,document,undefined){
    var pluses=/\+/g;
    function raw(s){
        return s;
    }
    function decoded(s){
        return decodeURIComponent(s.replace(pluses,' '));
    }
    var config=$.cookie=function(key,value,options){
        if(value!==undefined){
            options=$.extend({},config.defaults,options);
            if(value===null){
                options.expires=-1;
            }
            if(typeof options.expires==='number'){
                var days=options.expires,t=options.expires=new Date();
                t.setDate(t.getDate()+days);
            }
            value=config.json?JSON.stringify(value):String(value);
            return(document.cookie=[encodeURIComponent(key),'=',config.raw?value:encodeURIComponent(value),options.expires?'; expires='+options.expires.toUTCString():'',options.path?'; path='+options.path:'',options.domain?'; domain='+options.domain:'',options.secure?'; secure':''].join(''));
        }
        var decode=config.raw?raw:decoded;
        var cookies=document.cookie.split('; ');
        for(var i=0,parts;(parts=cookies[i]&&cookies[i].split('='));i++){
            if(decode(parts.shift())===key){
                var cookie=decode(parts.join('='));
                return config.json?JSON.parse(cookie):cookie;
            }
        }
        return null;
    };

    config.defaults={};

    $.removeCookie=function(key,options){
        if($.cookie(key)!==null){
            $.cookie(key,null,options);
            return true;
        }
        return false;
    };

})(jQuery,document);

var currentPage = 1;
$Core.inviteContactimpoter =
{
    iEnabled : 0,
    localSelector: function(sValue)
    {
        $('.checkbox').each(function(){
            if (sValue == "none")
            {
                $(this).prop('checked', false);
                $('#js_action_selector_1').attr('disabled', 'disabled');
                $('.moderation_drop').addClass("not_active");
                $('.moderation_drop').removeClass("is_clicked");
                $('.moderation_action_select').show();
            }
            if (sValue == "all")
            {

                $(this).prop('checked', true);
                $('#js_action_selector_1').removeAttr('disabled', '');
                $('.moderation_drop').removeClass("not_active");
                $('.moderation_action_unselect').show();
            }
        });
    /* saveSelectedPendingItems(currentPage); */
    },

    enableDelete: function(oObj)
    {
        if ($(oObj).attr('checked') == true || $(oObj).attr('checked')  == "checked" || $(oObj).is(':checked'))
        {

            $('#js_action_selector_1').removeAttr('disabled', '');
            $Core.inviteContactimpoter.iEnabled++;
            $('.moderation_action_unselect').show();
            $('.moderation_drop').removeClass('not_active');
            $('.moderation_action_select').hide();
        }
        else
        {
            $Core.inviteContactimpoter.iEnabled--;
            if ($Core.inviteContactimpoter.iEnabled < 1)
            {
                $('#js_action_selector_1').attr('disabled', 'disabled');
            }
        }

        if($('.checkbox:checked').size() == 0){
            $('.moderation_action_unselect').hide();
            $('.moderation_drop').addClass('not_active');
            $('.moderation_action_select').show();
        }
    },

    doAction: function(sAction)
    {
        if (sAction == "delete")
        {
            var selecteds = yn_GetSelectedPendingContacts();
            $('#js_form').prepend('<input name="selectedVals" type="hidden" value="'+selecteds.join(",")+'"/>');
            $('#js_form').submit();
        }
        if(sAction == "resendallselected")
        {

            var html = '<input type="hidden" value="resendallselected" name="resendallselected">';
            $('#js_form').append(html);
            $('#js_form').submit();
        }
        if(sAction == "resendall")
        {

            var html = '<input type="hidden" value="resendall" name="resendall">';
            $('#js_form').append(html);
            $('#js_form').submit();
        }
        return true;
    }
}

$Core.queueContactimpoter =
{
    iEnabled : 0,
    localSelector: function(sValue)
    {
        $('.checkbox').each(function(){
            if (sValue == "none")
            {
                $(this).prop('checked', false);
                $('#js_action_selector_1').attr('disabled', 'disabled');
                $('.moderation_drop').addClass("not_active");
                $('.moderation_drop').removeClass("is_clicked");
                $('.moderation_action_select').show();
            }
            if (sValue == "all")
            {

                $(this).prop('checked', true);
                $('#js_action_selector_1').removeAttr('disabled', '');
                $('.moderation_drop').removeClass("not_active");
                $('.moderation_action_unselect').show();
            }
        });
    /* saveSelectedPendingItems(currentPage); */
    },

    enableDelete: function(oObj)
    {
        if ($(oObj).attr('checked') == true || $(oObj).attr('checked')  == "checked" || $(oObj).is(':checked'))
        {

            $('#js_action_selector_1').removeAttr('disabled', '');
            $Core.inviteContactimpoter.iEnabled++;
            $('.moderation_action_unselect').show();
            $('.moderation_drop').removeClass('not_active');
            $('.moderation_action_select').hide();
        }
        else
        {
            $Core.inviteContactimpoter.iEnabled--;
            if ($Core.inviteContactimpoter.iEnabled < 1)
            {
                $('#js_action_selector_1').attr('disabled', 'disabled');
            }
        }

        if($('.checkbox:checked').size() == 0){
            $('.moderation_action_unselect').hide();
            $('.moderation_drop').addClass('not_active');
            $('.moderation_action_select').show();
        }
    },

    doAction: function(sAction)
    {
        if (sAction == "delete")
        {
            var selecteds = yn_GetSelectedPendingContacts();
            $('#js_form').prepend('<input name="selectedVals" type="hidden" value="'+selecteds.join(",")+'"/>');
            $('#js_form').submit();
        }
        if(sAction == "resendallselected")
        {

            var html = '<input type="hidden" value="resendallselected" name="resendallselected">';
            $('#js_form').append(html);
            $('#js_form').submit();
        }
        if(sAction == "resendall")
        {

            var html = '<input type="hidden" value="resendall" name="resendall">';
            $('#js_form').append(html);
            $('#js_form').submit();
        }
        return true;
    }
}


function getItemsChecked(strItemName, sep) {
    var x=document.getElementsByName(strItemName);
    var p="";
    for(var i=0; i<x.length; i++) {
        if(x[i].checked) {
            p += x[i].value + sep;
        }
    }
    var result = (p != '' ? p.substr(0, p.length - 1) : '');
    return result;
}

function saveSelectedPendingItems(page){
    var selected = getItemsChecked('val[]','|');

    if($.cookie("contactimporter.pendings") == null)
    {
        selectedContacts = Array();
        selectedContacts[page] = selected;
        $.cookie("contactimporter.pendings",selectedContacts.join());
    }
    else{
        selectedContacts = $.cookie("contactimporter.pendings").split(',');
        selectedContacts[page] = selected;
        $.cookie("contactimporter.pendings",selectedContacts.join());
    }
    return true;
}


$Behavior.loadJs = function() {
    var url = ynGetRequests(window.location.href,true);
    var param = $Core.getParams(url);
    if(typeof(param['page']) != "undefined"){
        currentPage = param['page'];
    }
    $('.pager_previous_link').attr('onclick','return saveSelectedPendingItems('+currentPage+');');
    $('.pager_next_link').attr('onclick','return saveSelectedPendingItems('+currentPage+');');
}

$Behavior.selectPendingContacts = function(){
    if($.cookie("contactimporter.pendings") != null)
    {
        var url = ynGetRequests(window.location.href,true);
        var param = $Core.getParams(url);
        if(typeof(param['page']) != "undefined"){
            currentPage = param['page'];
            $('#js_action_selector_1').removeAttr('disabled', '');
            var selectedContacts = $.cookie("contactimporter.pendings").split(',');
            if(typeof(selectedContacts[currentPage]) != "undefined"){
                var selected = selectedContacts[currentPage].split("|");
                $(".checkbox").each(function(){
                    var val = $(this).val();
                    if($.inArray(val,selected) >= 0){
                        $(this).attr('checked','checked');
                    }
                });
            }
        }
        else{
            $.cookie("contactimporter.pendings", "", {
                expires: -1
            });
        }

        if($('.checkbox:checked').size() > 0){
            $('.moderation_action_unselect').show();
            $('.moderation_drop').removeClass('not_active');
            $('.moderation_action_select').hide();
        }
    }
}

var ynGetRequests = function(sHref, bReturnPath)
{
    var sParams = '';
    var sUrlString = '';
    var sModuleName = oCore['core.section_module'];

    switch (oCore['core.url_rewrite'])
    {
        case '1':
            if (getParam('sHostedVersionId') == ''){
                var oReq = new RegExp("" + getParam('sJsHome') + "(.*?)$","i");
                var aMatches = oReq.exec(sHref + (getParam('sHostedVersionId') == '' ? '' : getParam('sHostedVersionId') + '/'));
                if (aMatches !== null && aMatches.length > 1)
                    sUrlString = '/' + aMatches[1];
            }
            else {
                sUrlString = sHref.pathname;
            }
            break;
        case '3':
            if (oCore['profile.is_user_profile'])
            {
                var aProfileMatches = sHref.match(/http:\/\/(.*?)\.(.*?)/i);
                sModuleName = aProfileMatches[1];
            }

            var oReq = new RegExp("" + oParams['sJsHome'] + "(.*?)$","i");
            var aMatches = oReq.exec(sHref);

            sUrlString = sModuleName + '/' + (aMatches != null && isset(aMatches[1]) ? aMatches[1] : '');
            break;
        default:
            var oReq = new RegExp("(.*?)=\/(.*?)$","i");
            var aMatches = oReq.exec(sHref);
            if (aMatches !== null && aMatches.length > 2)
            {
                sUrlString = aMatches[2];
            }
            break;
    }

    if (bReturnPath === true)
    {
        return '/' + ltrim(sUrlString, '/');
    }

    return $Core.parseUrlString(sUrlString);
};

function yn_GetSelectedPendingContacts(){
    var url = ynGetRequests(window.location.href,true);
    var param = $Core.getParams(url);
    var page = 1;
    if(typeof(param['page']) != "undefined"){
        page = param['page'];
    }
    saveSelectedPendingItems(page);
    var aSelecteds = Array();
    if($.cookie("contactimporter.pendings") != null)
    {
        var selectedContacts = $.cookie("contactimporter.pendings").split(',');
        for(var i =0; i < selectedContacts.length; i++)
        {
            if(selectedContacts[i] != ""){
                var select = selectedContacts[i].split("|");
                for(var j = 0; j < select.length; j++){
                    aSelecteds.push(select[j]);
                }
            }
        }
    }
    else{
        aSelecteds = getItemsChecked('val[]',',');
    }
    return aSelecteds;
}


    $('.moderation_drop').bind('click',function()
    {
        if ($(this).hasClass('not_active')){
            return false;
        }

        if ($(this).hasClass('is_clicked'))
        {
            $('.moderation_holder ul').hide();
            $(this).removeClass('is_clicked');
        }
        else
        {
            $('.moderation_holder ul').show();
            $('.moderation_holder ul').css({'margin-top': '-' + ($(this).height() + $('.moderation_holder ul').height() + 4) + 'px'});
            $(this).addClass('is_clicked');
        }

        return false;
    });

}