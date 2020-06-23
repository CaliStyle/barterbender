$Behavior.loadJs = function(){

;(function($,document,undefined){var pluses=/\+/g;function raw(s){return s;}
function decoded(s){return decodeURIComponent(s.replace(pluses,' '));}
var config=$.cookie=function(key,value,options){if(value!==undefined){options=$.extend({},config.defaults,options);if(value===null){options.expires=-1;}
if(typeof options.expires==='number'){var days=options.expires,t=options.expires=new Date();t.setDate(t.getDate()+days);}
value=config.json?JSON.stringify(value):String(value);return(document.cookie=[encodeURIComponent(key),'=',config.raw?value:encodeURIComponent(value),options.expires?'; expires='+options.expires.toUTCString():'',options.path?'; path='+options.path:'',options.domain?'; domain='+options.domain:'',options.secure?'; secure':''].join(''));}
var decode=config.raw?raw:decoded;var cookies=document.cookie.split('; ');for(var i=0,parts;(parts=cookies[i]&&cookies[i].split('='));i++){if(decode(parts.shift())===key){var cookie=decode(parts.join('='));return config.json?JSON.parse(cookie):cookie;}}
return null;};config.defaults={};$.removeCookie=function(key,options){if($.cookie(key)!==null){$.cookie(key,null,options);return true;}
return false;};})(jQuery,document);
;
};

function checkAll(strItemName, value) {
    var x=document.getElementsByName(strItemName);
    for (var i=0; i<x.length; i++) {
        if (value == 1 && !x[i].disabled) {
            if( !x[i].checked ) {x[i].checked = 'checked'; $(x[i]).parents("tr").removeClass("thTableOddRow").addClass("thTableSelectRow"); };
        } else {
            if(x[i].checked) {x[i].checked = ''; $(x[i]).parents("tr").removeClass("thTableSelectRow").addClass("thTableOddRow");};
        }
    }
}

function ct_getParam(sHref){
	var aParams = new Array();
	var aUrlParts = explode('/', sHref);
	var iRequest = 0;
	for (i in aUrlParts)
	{
		if (empty(aUrlParts[i]))
		{
			continue;
		}
		if(typeof(aUrlParts[i])=="string"){
			aUrlParts[i] = aUrlParts[i].replace('#', '');
			if (aUrlParts[i].match(/_/i))
			{
				var aUrlParams = explode('_', aUrlParts[i]);

				aParams[aUrlParams[0]] = aUrlParams[1];
			}
			else
			{
				iRequest++;

				aParams['req' + iRequest] = aUrlParts[i];
			}
		}
	}

	return aParams;
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

function saveSelectedItems(page){
    var selected = getItemsChecked('items[]','|');

    if($.cookie("contactimporter.selected") == null)
    {
        selectedContacts = Array();
        selectedContacts[page] = selected;
        $.cookie("contactimporter.selected",selectedContacts.join());
    }
    else{
        selectedContacts = $.cookie("contactimporter.selected").split(',');
        selectedContacts[page] = selected;
        $.cookie("contactimporter.selected",selectedContacts.join());
    }
    return true;
}

$Behavior.comtactImporterLoadJs = function() {
    $('#send-button:not(.built)').off('click').on('click', function(evt) {
        var provider = $('#provider').val();
        var selecteds = yn_GetSelectedContacts();
        var total = 0;
        total = selecteds.length;
        if(provider == 'csv'){
            if (selecteds instanceof Array)
            {
                  total = selecteds.length;
            }
            else
            {
                  tmp = selecteds.split(',');
                  total = tmp.length;
            }
        }
	    var message = encodeURIComponent($('#message').val());
	    tb_show("", $.ajaxBox('contactimporter.sendPopup', "width=400&total="+total+'&provider='+provider+'&message='+message+'&selecteds='+selecteds));
    });
    $('#sendall-button').off('click').on('click', function(evt) {
        var provider = $('#provider').val();
        var friends_count = $('#friends_count').val();
        tb_show("", $.ajaxBox('contactimporter.sendallPopup', "width=400" + "&provider=" + provider + "&friends_count=" + friends_count));
    });
    var url = ynGetRequests(window.location.href,true);
    var param = ct_getParam(url);
    var page = 1;
    if(typeof(param['page']) != "undefined"){
        page = param['page'];
    }
    $('.pager_previous_link').attr('onclick','return saveSelectedItems('+page+');');
    $('.pager_next_link').attr('onclick','return saveSelectedItems('+page+');');
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

function yn_GetSelectedContacts(){
    var url = ynGetRequests(window.location.href,true);
    var param = ct_getParam(url);
    var page = 1;
    if(typeof(param['page']) != "undefined"){
        page = param['page'];
    }
    saveSelectedItems(page);
    var aSelecteds = Array();
	var aCurrentSelectedInPage =  getItemsChecked('items[]',',');
    if($.cookie("contactimporter.selected") != null && $.cookie("contactimporter.selected").length >= aCurrentSelectedInPage.length)
    {
        var selectedContacts = $.cookie("contactimporter.selected").split(',');
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
        aSelecteds = getItemsChecked('items[]',',');
    }

    return aSelecteds;
}

$Behavior.loadJs = function() {
    $('.letter').click(function(evt) {
        var id = $(this).attr('rel');
        if ($("#"+id).length) {
            var top = $("#"+id).offset().top;
            $('html,body').animate({
                scrollTop:top
            },'slow');
        }
    });

    $(".contact_item").bind("change",function(){
        if($(this).is(':checked') == true){
            $(this).parents("tr").removeClass("thTableOddRow").addClass("thTableSelectRow");
        }
        else{
            $(this).parents("tr").removeClass("thTableSelectRow").addClass("thTableOddRow");
        }
    });
}

function openWindowPP(type) {
    var url ="{url link='contactimporter'}" ;
    newwindow=window.open('https://openid.younetid.com/auth/'+type+'.php?callbackUrl='+url,'name','scrollbars=yes,height=400,width=550');
    if (window.focus) newwindow.focus();
}

function toggleCurrent(element) {
    var check = false;
    if(element.innerHTML == oTranslations['contactimporter.select_current_page'])
    {
        check = true;
        element.innerHTML = oTranslations['contactimporter.unselect_current_page'] ;
    }
    else
    {
        check = false;
        element.innerHTML = oTranslations['contactimporter.select_current_page'] ;
    }

    var form = document.forms.openinviterform, z = 0;
    var counter = $('input[name="items[]"]').size();
    for (id=1; id<=counter;id++)
    {
        if(document.getElementById('row_'+id).style.display == '' )
        {
            document.getElementById('check_'+id).checked = check;
            //id = form[z].name.substring(6);
            if(document.getElementById('row_'+id))
            {
                if(check ) {
                    document.getElementById('row_'+id).className='thTableSelectRow';
                } else {
                    if (z%2 ==1 ) {
                        document.getElementById('row_'+id).className='thTableOddRow';
                    } else {
                        document.getElementById('row_'+id).className='thTableEvenRow';
                    }
                }
            }
        }
    }
}


function checkRespones()
{
    var provider_box_social = document.getElementById('provider_box_social');
    if (provider_box_social.value == 'linkedin') {
        $('#linkedinA').trigger('click');
        return false;
    }
    if (provider_box_social.value == 'twitter') {
        $('#twitterA').trigger('click');
        return false;
    }

    if (provider_box_social.value == 'youtube') {
        openWindowPP('youtube');
        return false;
    }
    return true;
}



function other_input(obj) {
    var element = document.getElementById(obj);
    var newElement = document.getElementById('provider_box-element2');
    var provider_box_mail = document.getElementById('provider_box_mail');
    if (provider_box_mail.value == 'other') {
        element.style.display = 'none';
        newElement.style.display ='';
        var provider_box_mail2 = document.getElementById('provider_box_mail2');
        provider_box_mail2.value ='';
    }
}

function choose_provider(kind,provider)
{
    var provider_list = document.getElementById(kind);
    var flag = true;
    var i =0;
    if (provider_list.tagName == "INPUT") {
        for (i=0;i<count_specific_email;i++) {
            if (specific[i].search(provider_domain_mapping[provider])!=-1) {
                provider_list.value = specific[i];
                flag = false;
                break;
            }

        }
        if (flag) {
            provider_list.value = provider;
        }
    } else {
        for (i=0;i<provider_list.options.length;i++) {
            if (provider == provider_list.options[i].value) {
                provider_list.options[i].selected = true;
                flag = false;
                var provider_box_mail2 = document.getElementById('provider_box_mail2');
                provider_box_mail2.value =  provider_list.options[i].innerHTML;
                break;
            }
        }
        if (flag) {
            var provider_box_mail = document.getElementById('provider_box_mail');
            provider_box_mail.value = 'other';
            other_input('provider_box-element');
            provider_list.value = provider;
            return true;
        }
    }
}


function error_notify(objId,error) {
    document.getElementById(objId+"_content").innerHTML = error;
    document.getElementById(objId).style.display = '';
}


function check_domain(domain,providerObj,emailObj) {
    error_email_empty = oTranslations['contactimporter.your_email_is_empty'];
    errror_not_support_domain = oTranslations['contactimporter.this_mail_domain_is_not_supported'];
    if (emailObj.value == '') {
        error_notify(oTranslations['contactimporter.email_should_not_be_left_blank'],error_email_empty);
        return false;
    }
    if (!providerObj) {
        if (emailObj.value.search('@') == -1) {
            emailObj.value+="@"+document.getElementById('provider_box_mail').options[document.getElementById('provider_box_mail').selectedIndex].text;
        }
        sending_request();
        return true;
    }
    for (i=0;i<count;i++) {
        if (domain.search(mapKey[i]+".")!= -1) {
            providerObj.value = mapValue[i];
            emailObj.value += "@"+domain;
            sending_request();
            return true;
        }
    }
    error_notify("error_mail",errror_not_support_domain);
    return false;
}

function do_submit() {
    var provider_box_mail= document.getElementById('provider_box_mail');
    var provider_box_input= document.getElementById('provider_box_input');
    if (provider_box_mail.value == '' || provider_box_mail.value == 'other') {
        provider_box_mail =  document.getElementById('provider_box_mail2');
        provider_box_input= document.getElementById('provider_box_input2');
    }
    var email_box= document.getElementById('email_box');
    if (!check_domain(provider_box_mail.value,provider_box_input,email_box)) return false;
    return true;
}

function sending_request() {
    var import_form = document.getElementById('import_form');
    var loading = document.getElementById('loading');
    import_form.style.display = 'none';
    loading.style.display = 'block';
}

function toggleAll(element) {
    var form = document.forms.openinviterform, z = 0;
    var counter = $('input[name="items[]"]').size();
    for (id=1; id<=counter;id++)    {
        document.getElementById('check_'+id).checked = element.checked;
        if (document.getElementById('row_'+id)) {
            if(element.checked) {
                document.getElementById('row_'+id).className='thTableSelectRow';
            } else {
                if (z%2 == 1) {
                    document.getElementById('row_'+id).className='thTableOddRow';
                } else {
                    document.getElementById('row_'+id).className='thTableEvenRow';
                }
            }
        }
    }
}

function check_toggle(element_id,obj,isCheckBox) {
    var check_element = document.getElementById('check_'+element_id);
    if (isCheckBox) check_element.checked = !check_element.checked;
    if (check_element.checked) {
        obj.className='thTableSelectRow';
    } else {
        if (element_id%2 ==1) {
            obj.className='thTableOddRow';
        } else {
            obj.className='thTableEvenRow';
        }
    }
}

function check_select() {
    error_no_contact = oTranslations['contactimporter.no_contacts_were_selected'];
    var selecteds = yn_GetSelectedContacts();
	var total = selecteds.length;
	var limit_select = total;

    if (limit_select >0) {
        if (items) {
            var items = items.substr(0, items.length - 1);
            $('#contacts').val(items);
        }
        if (limit_select > total_allow_select) {
            alert(oTranslations['contactimporter.you_can_send']+" "+total_allow_select+" "+oTranslations['contactimporter.invitations_per_time']+"\n "+oTranslations['contactimporter.you_have_selected']+" "+limit_select+" "+oTranslations['contactimporter.contacts']+".");
            return false;
        } else {
//            sending_request();
            return true;
        }
    }
    alert(error_no_contact);
    return false;
}

function check_select_invite() {
    error_no_contact = oTranslations['contactimporter.no_contacts_were_selected'];
    var limit_select = 0;
    var sep = ',';
    var x = document.getElementsByName('items[]');
    var items = '';
    for (var i=0; i<x.length; i++) {
        if (x[i].checked) {
            limit_select++;
            items += x[i].value + sep;
        }
    }
    if (limit_select > 0) {
        if (items) {
            var items = items.substr(0, items.length - 1);
            $('#contacts').val(items);
        }
        if (limit_select > total_allow_select) {
            alert(oTranslations['contactimporter.you_can_send']+" "+total_allow_select+" "+oTranslations['contactimporter.invitations_per_time']+"\n "+oTranslations['contactimporter.you_have_selected']+" "+limit_select+" "+oTranslations['contactimporter.contacts']+".");
            return false;
        } else {
            sending_request();
            return true;
        }
    }
//    error_notify(error_no_contact);
    return false;
}

function setValue(obj) {
    var check = document.getElementsByName('is_selected');
    var count = check.length;
    var arr = "";
    for (var i = count-1 ; i >=0 ; i--) {
        if ( check[i].checked == true) {
            arr+=","+check[i].value;
        }
    }
    arr = trim(arr,',');
    document.getElementById('arr_selected').value = arr;

    if (arr.length > 0) {
        var conf = oTranslations['contactimporter.are_you_sure_you_want_to_delete'];
        if (is_category == true) {
            conf = oTranslations['contactimporter.are_you_sure_you_want_to_delete_this_action_will_delete_all_feeds_belong_to'];
            is_category = false;
        }
        var form = $(obj).closest('form');
        $Core.jsConfirm({message:conf}, function () {
            is_submit=true;
            form.prepend('<input type="hidden" name="delete_select" value="1">').submit();
        }, function () {
            document.getElementById('arr_selected').value="";
            is_submit = false;
        });
    } else  {
        document.getElementById('arr_selected').value ="";
        is_submit = false;
    }
    return false;
}

var is_submit=true;
var is_category = false;
function getsubmit() {
    return is_submit;
}
