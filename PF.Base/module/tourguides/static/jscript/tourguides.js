var bInitYnTGuide = false;
var bStartTour = false;
var bCreateYnTGuide = false;
var bClickSelecteYnTGuide = true;
var sHTMLTags = "&ltp&gt,&lti&gt,&ltb&gt";
var aPreventElements = ['activatetour','canceltour','endtour','restarttour','nextstep','prevstep','createtour','cancelcreatetour','tourcontrols','activatetourtitle','completecreatetour','yn_jtour_tour_name','tour_name','is_auto','yn_jtour_button_save_continue_create_tour','yn_jtour_button_cancel_create_tour','loginbacktour','canceladdsteps','closeaddsteps'];
var aPreventClass = ['yntour_already_created_step','yntour_step_number','yntour_controll_number','yn_tour_create_new'];
var aColorList = ['white','black','yellow','silver','red','purple','olive','navy','maroon','lime','green','gray','fuchsia','blue','aqua'];
var aPositionElement = ['TL','TR','BL','BR','LT','LB','RT','RB','T','R','B','L','Top Left','Top Right','Bottom Left','Bottom Right','Left Top','Left Bottom','Right Top','Right Bottom','Top','Right','Bottom','Left'];
var iCreatedStep = 0;
var config = aTourSteps;
var autoplay    = false;
var showtime = 1000;
var step     = 0;
var total_steps    = config.length;
var bCreateTourStep = false;
var bNoAsk = false;
var bMultiLang = false;
var aTopPositions = new Array();

CorePageAjaxBrowsingStart = function(){
    bInitYnTGuide = false;
};
$Behavior.initYnTGuide = function(){
$.fn.extend({
    getTourElementPath: function( path ) {
        if (this.length != 1) return '';
        var path, node = this;
        node.removeClass('yntour_mouse_active');
        var needParents = "";
        if(node.parents('.block') && typeof(node.parents('.block')[0]) !== "undefined"){
            if(node.parents('.block')[0].id != "")
                needParents = node.parents('.block')[0].id;

        }
        while (node.length) {
            var realNode = node[0];
            var name = (
                /* IE9 and non-IE */
                realNode.localName ||
                /* IE <= 8 */
                realNode.tagName ||
                realNode.nodeName
                );
            /* on IE8, nodeName is '#document' at the top level, but we don't need that */
            var needParent = "";
            if(realNode.tagName == "A"){
                var nodeParent = node.parent();
                if(nodeParent[0].classList[0]){
                    needParent = "."+nodeParent[0].classList[0]+">";
                }
            }
            if (!name || name == '#document') break;
            name = name.toLowerCase();
            if (realNode.getAttribute('id') && realNode.id) {
                /* As soon as an id is found, there's no need to specify more. */
                var pathx = name + '#' + realNode.id + (path ? '>' + path : '');
                pathx = pathx.replace('.image_hover_holder_hover','')
                if(pathx.charAt(0) == "a"){
                    pathx = needParent + pathx;
                }
                if(pathx.indexOf(needParents) != -1){
                    return pathx;
                }
                else{
                    return "#" + needParents + " " + pathx;
                }
            } else if (realNode.className) {
                name += '.' + realNode.className.split(/\s+/).join('.');
                if(name.slice(-1) == '.') {
                    name = name.slice(0, -1);
                }
            }

            var parent = node.parent(), siblings = parent.children(name);
            if (siblings.length > 1) name += ':eq(' + siblings.index(node) + ')';
            path = name + (path ? ' > ' + path : '');
            node = parent;
        }
        path = path.replace('.image_hover_holder_hover','')
        if(path.indexOf(needParents) != -1)
            return path;
        else
            return "#" + needParents + " " + path;
    }
});
};
var curentYNTLink = '';
$Behavior.initYnTGuides = function() {
	init();
	curentYNTLink = window.location.href;
    if($("#completecreatetour").is(":visible")) {
        bindActions();
        ynTourBindAll();
    }

    if($("#tour_tooltip").is(":visible")) {
        douqleStep();
    }

    $("body").css({
        "cursor": "default"
    });
};
function init()
{
    if((aYnTourSession.sessionid && aYnTourSession.call_from == "admincp" && window.location.href == aYnTourSession.url) || (curentYNTLink == window.location.href))
    {

        if(bCreateTourStep == false && $('#tourcontrols').length <=0 )
        {
            var ua = window.navigator.userAgent;
            var msie = ua.indexOf("MSIE ");
            if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))
            {
            }
            else {
                bCreateTourStep = true;
                iCreatedStep = aYnTourSession.total_steps;
                if (bInitYnTGuide == false || $('#tourcontrols').length == 0) {
                    bInitYnTGuide = true;
                    showControls();
                }
            }

        }
        else if (curentYNTLink != window.location.href)
        {
            bindActions();
        }
        ynTourBindAll();
        return false;
    }
    curentYNTLink = window.location.href;
    $Core.ajax('tourguides.getObject',
    {
        params:
        {
            url : window.location.href
            , sTourController: sTourController
        },
        type: 'POST',
        success: function(sData)
        {

            if(sData)
            {
                sData = $.parseJSON(sData);
            }
            if(sData.success == true)
            {
                if((aYnTour.url == window.location.href || aYnTour.url =='undefined' || aYnTour.url == null && sCurrentURL) && $('#tourcontrols').length > 0 && sCurrentURL==window.location.href)
                {
                    ynTourBindAll();
                    sCurrentURL = window.location.href;
                    return false;
                }
                sCurrentURL = window.location.href;
                aYnTour = sData.aYnTour;
                bNoAsk = sData.bNoAsk;
                aYnTour = $.parseJSON(aYnTour);
                bCreateTourStep = sData.bCanCreate;
                $('#tourcontrols').remove();
                bStartTour = false;
                bCreateYnTGuide = false;
                bClickSelecteYnTGuide = true;
                iCreatedStep = 0;
                config = sData.aTourSteps;

                config = $.parseJSON(config);
                aTourSteps = config;
                autoplay    = false;
                showtime = 1000;
                step     = 0;
                total_steps    = config.length;
                bCreateTourStep = false;
                aYnTourSession ={};
                if(aYnTour.id && aYnTour.is_auto)
                {
                    autoplay = (aYnTour.is_auto==1)?true:false;
                }
                if(bCreateTourStep == true)
                {
                    bindActions();
                }
                if(bInitYnTGuide == false || $('#tourcontrols').length == 0)
                {
                    bInitYnTGuide = true;
                    showControls();

                }
                if(step >0)
                {
                    showOverlay();
                }
                ynTourBindAll();

            }
        }
    });
}
function ynTourBindAll()
{
    $('#activatetour').off();
    $('#activatetour').on('click',startTour);
    $('#canceltour').off();
    $('#canceltour').on('click',endTour);
    $('#nextstep').off();
    $('#nextstep').on('click',clickNextStep);
    $('#prevstep').off();
    $('#prevstep').off();
    $('#prevstep').on('click',clickPrevStep);
    $('#loginbacktour').off();
    $('#loginbacktour').on('click',loginbackAdmin);

    $('#createtour').off();
    $('#createtour').on('click',createTour);

    $('#cancelcreatetour').off();
    $('#cancelcreatetour').on('click',cancelCreateTour);
    $('#completecreatetour').off();
    $('#completecreatetour').on('click',CompleteCreateTour);

    $('#canceladdsteps').off();
    $('#canceladdsteps').on('click',cancelAddSteps);

    /* create tour */
    $('#yn_jtour_button_save_continue_create_tour').off();
    $('#yn_jtour_button_save_continue_create_tour').on('click', saveTour);
    $('#yn_jtour_button_cancel_create_tour').off();
    $('#yn_jtour_button_cancel_create_tour').on('click', closeCreateTour);
    $('#yn_jtour_button_close_2').off();
    $('#yn_jtour_button_close_2').on('click', closeCreateTour);

    $('input#yn_jtour_button_cancel').off();
    $('input#yn_jtour_button_cancel').on('click', resumeFindElement);
    $('span#yn_jtour_button_close').off();
    $('span#yn_jtour_button_close').on('click', resumeFindElement);
    $('#yn_jtour_button_save_continue').off();
    $('#yn_jtour_button_save_continue').on('click',saveCountinue);

    $('#yntour_img_next').off('click',clickNextStep);
    $('#yntour_img_prev').off('click',clickPrevStep);
    $('#yntour_img_next').on('click',clickNextStep);
    $('#yntour_img_prev').on('click',clickPrevStep);
    $('#endtour').off('click',endTourComplete);
    $('#restarttour').off('click',restartTour);
    $('#endtour').on('click',endTourComplete);
    $('#restarttour').on('click',restartTour);

    $('input#yn_chk_multi_lang_yes').off();
    $('input#yn_chk_multi_lang_yes').on('click',function(){
        $('#yntour_lang_select_value').show();
        $('#yn_current_lang').hide();
        var sId = $('#yntour_lang_select_value').val();
        $('.js_tour_lang_area.active').removeClass('active').fadeOut(100,function(){
            $('#js_tour_lang_area_'+sId).fadeIn(300).addClass('active');
        });
    });

    $('input#yn_chk_multi_lang_no').off();
    $('input#yn_chk_multi_lang_no').on('click',function(){
        $('#yntour_lang_select_value').hide();
        $('#yn_current_lang').show();
        var sId = $('#yntour_lang_default_value').val();
        $('.js_tour_lang_area.active').removeClass('active').fadeOut(100,function(){
            $('#js_tour_lang_area_'+sId).fadeIn(300).addClass('active');
        });
    });

    $('div.yntour_bgcolor').off();
    $('div.yntour_bgcolor').on('click',function(){

        var $p = $(this).parent().parent();
        if($p.attr('id'))
        {
            if($p.attr('id') == 'yntour_bg_color')
            {
                $('#yn_tour_bgcolor').val($(this).attr('rel'));
                $('div#yntour_bg_color div.yntour_bgcolor').removeClass('yntour_color_selected');
                $(this).addClass('yntour_color_selected');
            }
            if($p.attr('id') == 'yntour_font_color')
            {
                $('#yn_tour_fcolor').val($(this).attr('rel'));
                $('div#yntour_font_color div.yntour_bgcolor').removeClass('yntour_color_selected');
                $(this).addClass('yntour_color_selected');
            }
        }
    });
    $('select#yntour_select_value').off();
    $('select#yntour_select_value').on('change',function(){
        $('#yn_tour_position').val($(this).val());
    });
    $('select#yntour_lang_select_value').off();
    $('select#yntour_lang_select_value').on('change',function(){
        var sId = $(this).val();
        $('.js_tour_lang_area.active').removeClass('active').fadeOut(100,function(){
            $('#js_tour_lang_area_'+sId).fadeIn(300).addClass('active');
        });
    });
    if(aYnTourSession.sessionid && aYnTourSession.call_from == "admincp" && window.location.href == aYnTourSession.url)
    {
        bindMaskNumber(config,config.length,0);
    }
}
function loginbackAdmin()
{
    if(!aYnTourSession.sessionid || !aYnTourSession.id)
    {
        return false;
    }
    $Core.ajax('tourguides.loginbackAdmin',
    {
        params:
        {
            sessionid: aYnTourSession.sessionid,
            email: aYnTourSession.current_user,
            admincp_url_return: aYnTourSession.admincp_url_return
        },
        type: 'POST',
        success: function(sData)
        {
            if(sData)
            {
                sData = $.parseJSON(sData);
            }
            if(sData.success == true)
            {
                window.location.href =  sData.admincp_url_return;
            }
        }
    });
}
function bindActions()
{
    $('html>body').off('mousedown click', _getClickElement);
    $('html>body').on('mousedown click', _getClickElement);
    $('html>body').off('mouseover', _getActiveElement);
    $('html>body').on('mouseover', _getActiveElement);
    $('html>body').off('mouseout', _getNoActiveElement);
    $('html>body').on('mouseout', _getNoActiveElement);
    $('html>body>div.js_box>*').off('mousedown click', _getClickElement);
    $('html>body>div.js_box>*').off('mouseover', _getActiveElement);
    $('html>body>div.js_box>*').off('mouseout', _getNoActiveElement);
}
var _getClickElement = function(event){
    if(bClickSelecteYnTGuide  && event && event.target.offsetParent)
    {

        var eleClick = event.target;
        if($.inArray($(eleClick).attr('id'),aPreventElements) >=0)
        {
            return true;
        }
        if($.inArray($(eleClick).attr('yntour'),aPreventClass) >=0)
        {
            return true;
        }
        var offsetY = parseInt(!event.offsetY ? event.pageY - $(eleClick.offsetParent).offset().top : event.offsetY);
        var offsetX = parseInt(!event.offsetX ? event.pageX - $(eleClick.offsetParent).offset().left : event.offsetX);
        $('#yn_tour_position_body').val('Top: ' + offsetY + ' Left: ' + offsetX);
        //alert($(eleClick).getTourElementPath());
        $('#yn_tour_element').val($(eleClick).getTourElementPath());
        document.getElementById('yn_jtour_form_add_slide').style.top = event.pageY + 'px';
        if($(window).width() > (event.pageX + $('#yn_jtour_form_add_slide').width()))
        {
        	document.getElementById('yn_jtour_form_add_slide').style.left = event.pageX + 'px';
        }
        else
        {
        	document.getElementById('yn_jtour_form_add_slide').style.left = ($(window).width() - $('#yn_jtour_form_add_slide').width() - 30) + 'px';
        }
        document.getElementById('yn_jtour_form_add_slide').style.display = 'inline';
        if(!$(eleClick.offsetParent).hasClass('yntour_border_active'))
        {
            $(eleClick.offsetParent).addClass('yntour_border_active');
        }
        bClickSelecteYnTGuide = false;
        $('#yn_jtour_button_save_continue').off();
        $('#yn_jtour_button_save_continue').on('click',saveCountinue);
        event.stopPropagation();
        event.preventDefault();
        bindWriteGuideFrom();
        pauseFindElement();
    }


    return false;
};
var _getActiveElement = function(event)
{
    var eleClick = event.target;

    if($.inArray($(eleClick).attr('id'),aPreventElements) >=0)
    {
        return true;
    }
    if($.inArray($(eleClick).attr('yntour'),aPreventClass) >=0)
    {
        return true;
    }
    if(bClickSelecteYnTGuide)
    {
        if(!$(event.target).hasClass('yntour_mouse_active'))
        {
            $(event.target).addClass('yntour_mouse_active');
        }
    }

}

var _getNoActiveElement = function(event)
{
    var eleClick = event.target;

    if($.inArray($(eleClick).attr('id'),aPreventElements)  >= 0)
    {
        return true;
    }
    if($.inArray($(eleClick).attr('yntour'),aPreventClass) >=0)
    {
        return true;
    }
    if(bClickSelecteYnTGuide)
    {
        if($(event.target).hasClass('yntour_mouse_active'))
            $(event.target).removeClass('yntour_mouse_active');
    }

}
var stopFindElement = function(event)
{
    bClickSelecteYnTGuide = true;

    $('#yn_jtour_form_add_slide').hide();
    $('.yntour_mouse_active').removeClass('yntour_mouse_active');
    $('.yntour_border_active').removeClass('yntour_border_active');

    if(event)
    {
        event.preventDefault();
        event.stopPropagation();
    }
    hideOverlay();
    return false;
}

var bindWriteGuideFrom = function()
{
    $('#yn_jtour_form_add_slide textarea').focus();

}
var pauseFindElement = function(event)
{
    $('html>body').off('mousedown click', _getClickElement);
    $('html>body').off('mouseover', _getActiveElement);
    $('html>body').off('mouseout', _getNoActiveElement);
    showOverlay();
}
var resumeFindElement = function(event)
{
    $('html>body').on('mousedown click', _getClickElement);
    $('html>body').on('mouseover', _getActiveElement);
    $('html>body').on('mouseout', _getNoActiveElement);
    bClickSelecteYnTGuide = true;
    $('#yn_jtour_form_add_slide').hide();
    $('.yntour_mouse_active').removeClass('yntour_mouse_active');
    $('.yntour_border_active').removeClass('yntour_border_active');
    if(event)
    {
        event.preventDefault();
        event.stopPropagation();
    }
    hideOverlay();
    return false;
}
function saveCountinue()
{
    $Core.ajax('tourguides.addStep',
    {
        params:
        {
            data: $("#savecountinue_tourguides").serialize(),
            url: sTourCurrentUrl,
            controller: sTourController
        },
        type: 'POST',
        success: function(sData)
        {
            if(sData)
            {
                sData = $.parseJSON(sData);
            }
            resumeFindElement();
            setElementValue(sData);
        }
    });
    $("#yntour_lang_select_value").val(jsCurrentLangId);
    $('.js_tour_lang_area.active').removeClass('active').hide(0,function(){
        $('#js_tour_lang_area_'+jsCurrentLangId).show().addClass('active');
    });

}
function setElementValue(sData)
{
    $('#tour_tourguide_id').val(sData.tour_tourguide_id);
    if(sData.step && sData.step.id)
    {
        iCreatedStep++;
        var ele = $(sData.step.step_element);
        if(ele)
        {
            if(ele.length > 1)
                ele = ele.eq(0);

            if(ele.prop("tagName") == "IMG" || ele.prop("tagName") == "TEXTAREA" || ele.prop("tagName") == "INPUT" || ele.prop("tagName") == "BUTTON" || ele.prop("tagName") == "SELECT"  || ele.prop("tagName") == "I")
			{
				ele = ele.parent().parent();
                if(ele.prop("tagName") == "IMG" || ele.prop("tagName") == "TEXTAREA" || ele.prop("tagName") == "INPUT" || ele.prop("tagName") == "BUTTON" || ele.prop("tagName") == "SELECT"  || ele.prop("tagName") == "I")
                {
                    ele = ele.children();
                }
			}
            ele.addClass("yntour_already_created_step");
            if(ele.children().prop("tagName") == "IMG"){
                ele.children().attr("yntour","yntour_already_created_step");
            }
            ele.attr("href",'javascript:void(0)');
            ele.attr("yntour","yntour_already_created_step");
            maskNumber(ele,iCreatedStep,0);
        }

    }
    $('#yn_jtour_form_add_slide textarea').val("");
}
function maskNumber(ele,number,isStart) {
    ele.addClass('yntg_number');
    ele.addClass('yntg_number_' + number);
    $mask = $('<span class="yntour_controll_number"></span>s');
    $mask.css("left", ele.offset().left - 1);
    $mask.css("top", ele.offset().top + 12);
    aTopPositions[number] = parseInt(ele.offset().top);
    $("BODY").prepend($mask);
    //
    var padding_top = ele.css('padding-top');
    var padding_left = ele.css('padding-left');
    if (isStart == 1) {
        $('body').append($('<style>.yntour_already_created_step.yntg_number.yntg_number_' + number + ':before { content: "' + number + '" !important; margin-top: -' + padding_top + ';margin-left: -' + padding_left + '; }</style>'));

    }
}
function unmaskNumber(ele,number)
{
    $('.yntour_controll_number').remove();
    $("div").removeClass("yntour_already_created_step");
}
function initSelecter()
{

}
function createTour(){
    $('#yn_jtour_tour_name').show();
    $('#yn_jtour_button_save_continue_create_tour').off();
    $('#yn_jtour_button_save_continue_create_tour').on('click', saveTour);
    $('#yn_jtour_button_cancel_create_tour').off();
    $('#yn_jtour_button_cancel_create_tour').on('click', closeCreateTour);
    $('#yn_jtour_button_close_2').off();
    $('#yn_jtour_button_close_2').on('click', closeCreateTour);
    $('#tour_name').focus();
    showOverlay();
    bCreateTourStep = true;
}
function saveTour()
{
	if($("#tour_name").val() == "")
	{
		$("#yn_tour_error").css("display","block");
		setTimeout(function(){
			$("#yn_tour_error").css("display","none");
		},3500);

		return false;
	}
    $Core.ajax('tourguides.createNewTour',
    {
        params:
        {
            data: $("#createnew_tourguides").serialize(),
            url: window.location.href,
            controller: sTourController
        },
        type: 'POST',
        success: function(sData)
        {
            if(sData)
            {
                sData = $.parseJSON(sData);
                if(sData.success == true)
                {
                    closeCreateTour();
                    createSteps();
                    bCreateTourStep = true;
                    bindActions();
                    $('#tour_tourguide_id').val(sData.tour_tourguide_id);
                    $('#activatetourtitle').html(sData.name);
                }
                else
                {
                    alert(sData.message);
                }
            }
            else
            {

            }


        }
    });
}
function closeCreateTour()
{
    $('#yn_jtour_tour_name').hide();
    reset();
    hideOverlay();
    bCreateTourStep = false;
}
function reset()
{
    $('html>body').off('mousedown click', _getClickElement);
    $('html>body').off('mouseover', _getActiveElement);
    $('html>body').off('mouseout', _getNoActiveElement);

}
function createSteps()
{
    if(bCreateYnTGuide == false)
    {
        bCreateYnTGuide = true;
        $('input#yn_jtour_button_cancel').on('click', resumeFindElement);
        $('span#yn_jtour_button_close').on('click', resumeFindElement);
        $('#yn_jtour_button_save_continue').on('click',saveCountinue);
        $('html>body').on('mousedown click', _getClickElement);
        $('html>body').on('mouseover', _getActiveElement);
        $('html>body').on('mouseout', _getNoActiveElement);
        unbindControllers();
        $('#tourcontrols').unbind();
        $('#createtour').hide();
        $('#cancelcreatetour').show();
        $('#activatetour').hide();
        $('#completecreatetour').show();
    }
}
function unbindControllers()
{
    $('#tourcontrols').unbind('mousedown click', _getClickElement);
    $('#tourcontrols').unbind('mouseover', _getActiveElement);
    $('#tourcontrols').unbind('mouseout', _getNoActiveElement);
}

function cancelCreateTour()
{
    $Core.ajax('tourguides.cancelNewTour',
    {
        params:
        {
            id:$('#tour_tourguide_id').val()
        },
        type: 'POST',
        success: function(sData)
        {
            if(sData.trim().length > 0)
            {
                window.location.reload();
            }
        }
    });
}

function CompleteCreateTour()
{
    $Core.ajax('tourguides.completeCreateTour',
    {
        params:
        {
            id:$('#tour_tourguide_id').val()
        },
        type: 'POST',
        success: function(sData)
        {
            if(sData)
            {
                sData = $.parseJSON(sData);
            }
            url = sData.url;
            if(url == 'undefined' || !url)
            {
                url = window.location.href
            }
            window.location.href =url;
        }
    });
}

function cancelAddSteps()
{
    $Core.ajax('tourguides.cancelAddSteps',
    {
        params:
        {
            id: aYnTourSession.id,
            step: aYnTourSession.total_steps
        },
        type: 'POST',
        success: function(sData)
        {
            if(sData)
            {
                sData = $.parseJSON(sData);
            }
            url = sData.url;
            if(url == 'undefined' || !url)
            {
                url = window.location.href;
            }
            window.location.href = url;
        }
    });
}

function bindMaskNumber(colection,total, isStart)
{
    for(i = 0 ; i < total; i++)
    {

        ele = $(colection[i].name);
        if(ele.size() > 0)
        {
        	if(ele.prop("tagName") == "IMG" || ele.prop("tagName") == "TEXTAREA" || ele.prop("tagName") == "INPUT" || ele.prop("tagName") == "BUTTON" || ele.prop("tagName") == "SELECT"  || ele.prop("tagName") == "I")
			{
                ele = ele.parent().parent();
                if(ele.prop("tagName") == "IMG" || ele.prop("tagName") == "TEXTAREA" || ele.prop("tagName") == "INPUT" || ele.prop("tagName") == "BUTTON" || ele.prop("tagName") == "SELECT"  || ele.prop("tagName") == "I")
                {
                    ele = ele.children();
                }
			}
            ele.addClass("yntour_already_created_step");
            if(ele.children().prop("tagName") == "IMG"){
                ele.children().attr("yntour","yntour_already_created_step");
            }
            ele.attr("href",'javascript:void(0)');
            ele.attr("yntour","yntour_already_created_step");
            maskNumber(ele,i+1,isStart);
        }

    }
}
function startTour(){
    $('body').addClass('yntour_disable_scroll');
    $('html,body').animate({
        scrollTop:top
    },'slow',function(){
        bindMaskNumber(config,total_steps,1);
        bCreateYnTGuide = true;
        bClickSelecteYnTGuide = false;
        bStartTour = true;
        $('#activatetour').remove();
        $('#createtour').hide();
        $('#activatetourtitle').hide();
        var bg_img = oParams['sJsHome']+'module/tourguides/static/image/stop.png';
        $('#canceltour').attr("style",'background:url("'+bg_img+'") no-repeat center center;width:24px;height:24px;');
        $('#cancelcreatetour').hide();
        $('#tourcontrols').css('z-index','9999');
        $('#tourcontrols').css('background','none');
        $('#endtour,#restarttour').show();
        showOverlay();
        step = 0;
        nextStep();
    });
}
function clickNextStep()
{
    if(autoplay){
        clearTimeout(showtime);
    }
}
function clickPrevStep()
{
    if(autoplay){
        clearTimeout(showtime);
    }
    prevStep();
}
function nextStep(){
    if(!autoplay){
        if(step > 0)
            $('#prevstep').show();
        else
            $('#prevstep').hide();
        if(step == total_steps-1)
            $('#nextstep').hide();
        else
            $('#nextstep').show();
    }
    if(step >= total_steps){
        /* if last step then end tour */
        endTour();
        return false;
    }
    ++step;
    var name=  config[step-1].name;
    if(name.indexOf("timeline_main_menu") >= 0)
    {
        $('html,body').animate({
            scrollTop:top
        },'slow',showTooltip(1));
    }
    else{
        showTooltip(1);
    }
}
function douqleStep(){
    if(!autoplay){
        if(step > 0)
            $('#prevstep').show();
        else
            $('#prevstep').hide();
        if(step == total_steps-1)
            $('#nextstep').hide();
        else
            $('#nextstep').show();
    }
    showTooltip(1);
}

function prevStep(){
    if(!autoplay){
        if(step > 2)
            $('#prevstep').show();
        else
            $('#prevstep').hide();
        if(step == total_steps)
            $('#nextstep').show();
    }
    if(step <= 1)
        return false;
    --step;
    showTooltip(0);
}
function endTourComplete()
{
    $('body').removeClass('yntour_disable_scroll');
    $('body').find('.yntg_number').removeClass('yntg_number');
   	if(iUserId != 0)
	{
        $Core.jsConfirm({message:oTranslations['don_t_show_it_again_for_this_page']}, function() {
            $.ajaxCall("tourguides.completetour",'id='+aYnTour.id);
        },function(){});
   	}
    hideOverlay();
    for(i = 0 ; i < total_steps; i++)
    {
        ele = $(config[i].name);
        if(ele.size() > 0)
        {
        	if(ele.prop("tagName") == "IMG" || ele.prop("tagName") == "TEXTAREA" || ele.prop("tagName") == "INPUT" || ele.prop("tagName") == "BUTTON" || ele.prop("tagName") == "SELECT"  || ele.prop("tagName") == "I")
			{
                ele = ele.parent().parent();
                if(ele.prop("tagName") == "IMG" || ele.prop("tagName") == "TEXTAREA" || ele.prop("tagName") == "INPUT" || ele.prop("tagName") == "BUTTON" || ele.prop("tagName") == "SELECT"  || ele.prop("tagName") == "I")
                {
                    ele = ele.children();
                }
			}
            unmaskNumber(ele,i+1);
            ele.removeClass("yntour_already_created_step");
            ele.removeAttr("yntour");
        }

    }
    step = 0;
    if(autoplay) clearTimeout(showtime);
    removeTooltip();
    hideControls();
    hideOverlay();
    $('.yntour_already_created_step').removeClass('yntour_already_created_step');
    bStartTour = false;
}
function endTour(){
    $('body').removeClass('yntour_disable_scroll');
    $('body').find('.yntg_number').removeClass('yntg_number');
    if(aYnTour.id > 0 && iUserId != 0)
    {
        $Core.jsConfirm({message:oTranslations['don_t_show_it_again_for_this_page']}, function() {
            $.ajaxCall("tourguides.completetour",'id='+aYnTour.id);
        },function(){});
    }

    hideOverlay();
    for(i = 0 ; i < total_steps; i++)
    {
        ele = $(config[i].name);
        if(ele.size() > 0)
        {
        	if(ele.prop("tagName") == "IMG" || ele.prop("tagName") == "TEXTAREA" || ele.prop("tagName") == "INPUT" || ele.prop("tagName") == "BUTTON" || ele.prop("tagName") == "SELECT"  || ele.prop("tagName") == "I")
			{
                ele = ele.parent().parent();
                if(ele.prop("tagName") == "IMG" || ele.prop("tagName") == "TEXTAREA" || ele.prop("tagName") == "INPUT" || ele.prop("tagName") == "BUTTON" || ele.prop("tagName") == "SELECT"  || ele.prop("tagName") == "I")
                {
                    ele = ele.children();
                }
			}
            unmaskNumber(ele,i+1);
            ele.removeClass("yntour_already_created_step");
            ele.removeAttr("yntour");
        }

    }
    step = 0;
    if(autoplay) clearTimeout(showtime);
    removeTooltip();
    hideControls();
    hideOverlay();
    bStartTour = false;
}

function restartTour(){
	$('html,body').animate({
        scrollTop:top
  	},'slow');
    step = 0;
    if(autoplay) clearTimeout(showtime);
    nextStep();
}
function replaceString(str)
{
    str = str.replace(/\r\n/g,"<br/>");
    return str;
}
function showTooltip(bNext){
    removeTooltip();
    var step_config        = config[step-1];
    var $elem = $(step_config.name);
    if($elem.size() == 0)
    {
        if(step >= total_steps){
            endTour();
            return false;
        }
        else
        {
            if(bNext == 1)
                nextStep();
            else
                prevStep();
            return false;
        }
    }
    if(showtime)
    {
        clearTimeout(showtime);
    }
    if(autoplay)
        showtime    = setTimeout(nextStep,step_config.time);

    var bgcolor         = step_config.bgcolor;
    var color             = step_config.color;
    var img_prev = oParams['sJsHome']+'module/tourguides/static/image/prev.png';
    var img_next = oParams['sJsHome']+'module/tourguides/static/image/next.png';
    var img_store = oParams['sJsHome']+'module/tourguides/static/image/reload.png';
    var img_end = oParams['sJsHome']+'module/tourguides/static/image/finish.png';
    step_config.text = replaceString(step_config.text);
    var $tooltip        = $('<div>',{
        id            : 'tour_tooltip',
        html        : '<div class="yntour_description_tip">'+step_config.text+'</div><div class="clear"><div class="yntour_control_end"><span class=""><a id="restarttour" ><img src="'+img_store+'" title="'+oTranslations['restart_tour_guide']+'"/></a><a id="endtour"><img src="'+img_end+'" title="'+oTranslations['end_tour_guide']+'"/></a></span></div><div class="yntour_control_bars"><span class="yntour_preview yntour_icon"><img id="yntour_img_prev" src="'+img_prev+'" alt="Prev" title="'+oTranslations['previous']+'"/></span><span class="yntour_next yntour_icon"><img id="yntour_img_next" src="'+img_next+'" alt="Next" title="'+oTranslations['next']+'"/></span></div><div class="clear"></div><span class="yntourguidetooltip_arrow"></span>'
    }).css({
        'display'            : 'none',
        'background-color'    : bgcolor,
        'color'                : color
    });
    $tooltip.addClass('yntourguidetooltip');
    /* position the tooltip correctly: */

    /* the css properties the tooltip should have */
    var properties        = {};
    var tip_position     = step_config.position;
    var e_w                = $elem.outerWidth();
    var e_h                = $elem.outerHeight();
    var e_l                = $elem.offset().left;
    var e_t                = aTopPositions[step]; // $elem.offset().top;
    /* append the tooltip but hide it */
    $('BODY').prepend($tooltip);
    $('div.yntour_control_end a').css("color",color);
    $('#yntour_img_next').off();
    $('#yntour_img_prev').off();
    $('#yntour_img_next').on('click',nextStep);
    $('#yntour_img_prev').on('click',prevStep);
    $('#endtour').off('click',endTourComplete);
    $('#restarttour').off('click',restartTour);
    $('#endtour').on('click',endTourComplete);
    $('#restarttour').on('click',restartTour);

    /* get some info of the element */

    switch(tip_position){
        case 'BL'    :
            properties = {
                'left'    : e_l + 'px',
                'top'    : e_t + e_h + 'px'
            };
            $tooltip.find('span.yntourguidetooltip_arrow').addClass('yntourguidetooltip_arrow_TL');

            break;
        case 'BR'    :
            properties = {
                'left'    : e_l + e_w - $tooltip.width() + 'px',
                'top'    : e_t + e_h + 'px'
            };
            $tooltip.find('span.yntourguidetooltip_arrow').addClass('yntourguidetooltip_arrow_TR');
            break;
        case 'TL'    :
            properties = {
                'left'    : e_l + 'px',
                'top'    : e_t - $tooltip.height() - 12 + 'px'
            };
            $tooltip.find('span.yntourguidetooltip_arrow').addClass('yntourguidetooltip_arrow_BL');
            break;
        case 'TR'    :
            properties = {
                'left'    : e_l + e_w - $tooltip.width() + 'px',
                'top'    : e_t - $tooltip.height() + 'px'
            };
            $tooltip.find('span.yntourguidetooltip_arrow').addClass('yntourguidetooltip_arrow_BR');
            break;
        case 'RT'    :
            properties = {
                'left'    : e_l + e_w + 'px',
                'top'    : e_t + 'px'
            };
            $tooltip.find('span.yntourguidetooltip_arrow').addClass('yntourguidetooltip_arrow_LT');
            break;
        case 'RB'    :
            properties = {
                'left'    : e_l + e_w + 'px',
                'top'    : e_t + e_h - $tooltip.height() + 'px'
            };
            $tooltip.find('span.yntourguidetooltip_arrow').addClass('yntourguidetooltip_arrow_LB');
            break;
        case 'LT'    :
            properties = {
                'left'    : e_l - $tooltip.width() + 'px',
                'top'    : e_t + 'px'
            };
            $tooltip.find('span.yntourguidetooltip_arrow').addClass('yntourguidetooltip_arrow_RT');
            break;
        case 'LB'    :
            properties = {
                'left'    : e_l - $tooltip.width() + 'px',
                'top'    : e_t + e_h - $tooltip.height() + 'px'
            };
            $tooltip.find('span.yntourguidetooltip_arrow').addClass('yntourguidetooltip_arrow_RB');
            break;
        case 'B'    :
            properties = {
                'left'    : e_l + e_w/2 - $tooltip.width()/2 + 'px',
                'top'    : e_t + e_h + 'px'
            };
            $tooltip.find('span.yntourguidetooltip_arrow').addClass('yntourguidetooltip_arrow_T');
            break;
        case 'L'    :
            properties = {
                'left'    : e_l - $tooltip.width() + 'px',
                'top'    : e_t + e_h/2 - $tooltip.height()/2 + 'px'
            };
            $tooltip.find('span.yntourguidetooltip_arrow').addClass('yntourguidetooltip_arrow_R');
            break;
        case 'T'    :
            properties = {
                'left'    : e_l + e_w/2 - $tooltip.width()/2 + 'px',
                'top'    : e_t - $tooltip.height() + 'px'
            };
            $tooltip.find('span.yntourguidetooltip_arrow').addClass('yntourguidetooltip_arrow_B');
            break;
        case 'R'    :
            properties = {
                'left'    : e_l + e_w  + 'px',
                'top'    : e_t + e_h/2 - $tooltip.height()/2 + 'px'
            };
            $tooltip.find('span.yntourguidetooltip_arrow').addClass('yntourguidetooltip_arrow_L');
            break;
    }
    /*
        if the element is not in the viewport
        we scroll to it before displaying the tooltip
    */
    var w_t    = $(window).scrollTop();
    var w_b = $(window).scrollTop() + $(window).height();
    /* get the boundaries of the element + tooltip */
    var b_t = parseFloat(properties.top,10);

    if(e_t < b_t)
        //b_t = e_t;

    var b_b = parseFloat(properties.top,10) + $tooltip.height();
    if((e_t + e_h) > b_b)
        b_b = e_t + e_h;

    if((b_t < w_t || b_t > w_b) || (b_b < w_t || b_b > w_b)){
        $('html, body').stop()
        .animate({
            scrollTop: b_t-100
        }, 500, 'easeInOutExpo', function(){
            setTimeout(function(){
                if ($(window).scrollTop() > 200 && $('.navbar').length) {
                    $('.navbar').removeClass('ynresbusiness-fixed');
                    $('.navbar').removeClass('ynresphoenix-fixed');
                }
            },10);
            /* need to reset the timeout because of the animation delay */
            if(autoplay){
                clearTimeout(showtime);
                showtime = setTimeout(nextStep,step_config.time);
            }
            /* show the new tooltip */
            $tooltip.css(properties).show();
        });
    }
    else{
        /* show the new tooltip */
        $tooltip.css(properties).show();
    }

}

function removeTooltip(){
    $('#tour_tooltip').remove();
}

function showControls(){
    /*
    we can restart or stop the tour,
    and also navigate through the steps
    */
    if(bNoAsk == true || $('#tourcontrols').length)
    {
        return false;
    }
    if(aTourSteps.length <= 0 && bCanCreate == 0 && !aYnTourSession.sessionid )
    {
        return false;
    }

    var $tourcontrols  = '<div id="tourcontrols" class="yntourguidetourcontrols" style="z-index:10000;margin-top:25px">';
    $tourcontrols += '<p id="activatetourtitle">'+oTranslations['first_time_here']+'</p>';
    if(aTourSteps.length > 0 && !aYnTourSession.sessionid)
    {
        $tourcontrols += '<span class="yntourguidebutton" id="activatetour">'+oTranslations['start_the_tour']+'</span>';
    }
    if((bCanCreate == 1 && aTourSteps.length <=0) && !aYnTourSession.sessionid )
    {
        $tourcontrols += '<span class="yntourguidebutton" id="createtour">'+oTranslations['create_a_tour']+'</span>';
        $tourcontrols += '<span class="yntourguidebutton" id="completecreatetour" style="display:none">'+oTranslations['complete']+'</span>';
        $tourcontrols += '<span class="yntourguidebutton" id="cancelcreatetour" style="display:none">'+oTranslations['cancel']+'</span>';
    }
    tour_id = 0;
    if(aYnTourSession.sessionid)
    {
        $tourcontrols += '<span class="yntourguidebutton" id="completecreatetour">'+oTranslations['complete']+'</span>';
        $tourcontrols += '<span class="yntourguidebutton" id="canceladdsteps">'+oTranslations['cancel']+'</span>';

        tour_id = aYnTourSession.id;
    }
    if(aYnTourSession.is_member == 0)
    {
    }
    if(!autoplay){

    }
    if(!aYnTourSession.sessionid)
    {
        $tourcontrols += '<span class="yntourguideclose" id="canceltour" onclick="cancelCreateTour(); return false;"></span>';
    }
    else
    {
        $tourcontrols += '<span class="yntourguideclose" id="closeaddsteps" onclick="cancelAddSteps(); return false;"></span>';
    }
    $tourcontrols += '</div>';
    sSupportColor = "<div>";
    for (i=0;i<aColorList.length; i++ )
    {
        if(i == 0)
        {
            sSupportColor += '<div rel="'+aColorList[i]+'" class="yntour_bgcolor yntour_color_selected" style="background-color:'+aColorList[i]+'"></div>';
        }
        else
        {
            sSupportColor += '<div rel="'+aColorList[i]+'"class="yntour_bgcolor" style="background-color:'+aColorList[i]+'"></div>';
        }
    }
    sSupportColor += "</div>";
    sSupportBGColor = "<div>";
    for (i=0;i<aColorList.length; i++ )
    {
        if(i == 1)
        {
            sSupportBGColor += '<div rel="'+aColorList[i]+'" class="yntour_bgcolor yntour_color_selected" style="background-color:'+aColorList[i]+'"></div>';
        }
        else
        {
            sSupportBGColor += '<div rel="'+aColorList[i]+'"class="yntour_bgcolor" style="background-color:'+aColorList[i]+'"></div>';
        }
    }
    sSupportBGColor += "</div>";

    sSupportPosition = '<select style="padding:2px;" id="yntour_select_value">';
    for (i=0;i<aPositionElement.length-12; i++ )
    {
        sSupportPosition += '<option value="'+aPositionElement[i]+'">'+aPositionElement[i+12]+'</option>';
    }
    sSupportPosition += '</select>';


    sTextArea = '<div style="position: relative; height: 160px; overflow: hidden">';
    sSupportLanguages = '<input type="hidden" id="yntour_lang_default_value" value="'+jsCurrentLangId+'"/>';
    sSupportLanguages += '<select style="padding:2px; display: none;" id="yntour_lang_select_value">';
    for (i=0;i< jsLangTitle.length; i++ )
    {
        if(jsCurrentLangId == jsLangId[i])
        {
            sSupportLanguages += '<option value="'+jsLangId[i]+'" selected="selected">'+jsLangTitle[i]+'</option>';
            sTextArea += '<textarea  id="js_tour_lang_area_'+jsLangId[i]+'" class="js_tour_lang_area active" name="tour_description['+jsLangId[i]+']" rows="8" cols="48"></textarea>';
        }
        else
        {
            sSupportLanguages += '<option value="'+jsLangId[i]+'">'+jsLangTitle[i]+'</option>';
            sTextArea += '<textarea  id="js_tour_lang_area_'+jsLangId[i]+'" class="js_tour_lang_area" style="display: none" name="tour_description['+jsLangId[i]+']" rows="8" cols="48"></textarea>';
        }
    }
    sTextArea += '</div>';
    sSupportLanguages += '</select>';


    $('BODY').prepend($tourcontrols);

    var windowSize = {
        width: $(window).width(),
        height: $(window).height()
    };
    var buttonSize = {
        width: $('#tourcontrols').outerWidth(),
        height: $('#tourcontrols').outerHeight()
    };
    if(isNaN(yntourPositionRight)) {
        $('#tourcontrols').animate({
            'right':'30px'
        },500);
    } else {
        var r = parseFloat(yntourPositionRight)*(windowSize.width - buttonSize.width);
        var t = parseFloat(yntourPositionTop)*(windowSize.height - buttonSize.height);
        $('#tourcontrols').css('margin-top', 0 + 'px').css('top', t + 'px');
        if(yntourPositionRight < 0.5) {
            $('#tourcontrols').animate({
                'right': r + 'px'
            },500);
        } else{
            $('#tourcontrols').css('left', '-300px').css('right', 'auto').animate({
                'left': windowSize.width - r - buttonSize.width + 'px'
            },500);
        }
    }

    $('#yn_jtour_form_add_slide').remove();
    var  $yn_jtour_form_add_slide = '<div id="yn_jtour_form_add_slide" class="yntourguidetourcontrols" style="width:400px;padding:10px;display:none; position:absolute; top:1px; left:1px;">';
    $yn_jtour_form_add_slide +='<form name="savecountinue_tourguides" id="savecountinue_tourguides"  onsubmit="$Core.ajaxMessage();return false;">';
    $yn_jtour_form_add_slide +='<div class="title">'+oTranslations['write_description_for_this_step']+'<span id="yn_jtour_button_close" class="yntourguideclose" style="top:9px;"></span></div>';
    $yn_jtour_form_add_slide +=sTextArea;
    /* $yn_jtour_form_add_slide +='<div class="extra_info"><span>Accept HTML Tag:</span> <span class="tags">'+sHTMLTags+'</span></div>'; */
    if(jsLangTitle.length > 1)
    {
        $yn_jtour_form_add_slide +='<div class="extra_info"><div class="yntour_span_color" style=" margin-top: 4px;">'+oTranslations['yn_multiple_languages']+':</div> <div class="tags"><input id="yn_chk_multi_lang_no" type="radio" value="'+jsCurrentLangId+'" name="is_multi_lang" checked="checked"/>'+oTranslations['no']+'&nbsp;&nbsp;<input id="yn_chk_multi_lang_yes" type="radio" value="" name="is_multi_lang"/>'+oTranslations['yes']+'</div></div>';
        $yn_jtour_form_add_slide +='<div class="extra_info"><div class="yntour_span_color" style=" margin-top: 4px;">'+oTranslations['yn_tour_language']+':</div> <div class="tags"><span id="yn_current_lang" style="line-height: 24px">'+jsCurrentLangTitle+'</span>'+sSupportLanguages+'</div></div>';
    }
    else
    {
        $yn_jtour_form_add_slide +='<div class="extra_info"><div class="yntour_span_color" style=" margin-top: 4px;">'+oTranslations['yn_tour_language']+':</div> <div class="tags"><span id="yn_current_lang" style="line-height: 24px">'+jsCurrentLangTitle+'</span></div></div>';
    }
    $yn_jtour_form_add_slide +='<div class="extra_info"><div class="yntour_span_color">'+oTranslations['background_color']+':</div> <div class="tags" id="yntour_bg_color">'+sSupportBGColor+'</div></div><div class="clear"></div>';
    $yn_jtour_form_add_slide +='<div class="extra_info"><div class="yntour_span_color">'+oTranslations['font_color']+':</div> <div class="tags" id="yntour_font_color">'+sSupportColor+'</div></div><div class="clear"></div>';
    $yn_jtour_form_add_slide +='<div class="extra_info"><div class="yntour_span_color" style=" margin-top: 4px;">'+oTranslations['position_display']+':</div> <div class="tags">'+sSupportPosition+'</div></div>';
    $yn_jtour_form_add_slide +='<div class="extra_info"><div class="yntour_span_color">'+oTranslations['time_display']+':</div> <div class="tags" style="margin-top: -5px;float:left;"><input type="text" name="tour_delay" value="5" id="yntour_delay" size="11" style="margin:0;"/> '+oTranslations['second_s']+'</div></div>';
    $yn_jtour_form_add_slide +='<div class="extra_info" style="padding-top: 10px;"><input id="yn_jtour_button_save_continue" type="button" value="'+oTranslations['save_countinue']+'"  class="yntourguidebutton"/>';
    $yn_jtour_form_add_slide +='<input id="yn_jtour_button_cancel" type="button" value="'+oTranslations['cancel']+'"  class="yntourguidebutton"/></div>';
    $yn_jtour_form_add_slide +='<input type="hidden" value="" name="tour_element" id="yn_tour_element"/>';
    $yn_jtour_form_add_slide +='<input type="hidden" value="Left:0;Top:0;" name="tour_position" id="yn_tour_position_body"/>';
    $yn_jtour_form_add_slide +='<input type="hidden" value="white" name="tour_fcolor" id="yn_tour_fcolor"/>';
    $yn_jtour_form_add_slide +='<input type="hidden" value="black" name="tour_bgcolor" id="yn_tour_bgcolor"/>';
    $yn_jtour_form_add_slide +='<input type="hidden" value="'+tour_id+'" name="tour_tourguide_id" id="tour_tourguide_id"/>';
    $yn_jtour_form_add_slide +='<input type="hidden" value="TL" name="position" id="yn_tour_position"/>';
    $yn_jtour_form_add_slide += '</form>';
    $yn_jtour_form_add_slide += '</div>';


    $('BODY').prepend($yn_jtour_form_add_slide);
    $('input#yn_chk_multi_lang_yes').off();
    $('input#yn_chk_multi_lang_yes').on('click',function(){
        $('#yntour_lang_select_value').show();
        $('#yn_current_lang').hide();
        var sId = $('#yntour_lang_select_value').val();
        $('.js_tour_lang_area.active').removeClass('active').fadeOut(100,function(){
            $('#js_tour_lang_area_'+sId).fadeIn(300).addClass('active');
        });
    });

    $('input#yn_chk_multi_lang_no').off();
    $('input#yn_chk_multi_lang_no').on('click',function(){
        $('#yntour_lang_select_value').hide();
        $('#yn_current_lang').show();
        var sId = $('#yntour_lang_default_value').val();
        $('.js_tour_lang_area.active').removeClass('active').fadeOut(100,function(){
            $('#js_tour_lang_area_'+sId).fadeIn(300).addClass('active');
        });
    });

    $('div.yntour_bgcolor').off();
    $('div.yntour_bgcolor').on('click',function(){

        var $p = $(this).parent().parent();
        if($p.attr('id'))
        {
            if($p.attr('id') == 'yntour_bg_color')
            {
                $('#yn_tour_bgcolor').val($(this).attr('rel'));
                $('div#yntour_bg_color div.yntour_bgcolor').removeClass('yntour_color_selected');
                $(this).addClass('yntour_color_selected');
            }
            if($p.attr('id') == 'yntour_font_color')
            {
                $('#yn_tour_fcolor').val($(this).attr('rel'));
                $('div#yntour_font_color div.yntour_bgcolor').removeClass('yntour_color_selected');
                $(this).addClass('yntour_color_selected');
            }
        }
    });
    $('select#yntour_select_value').off();
    $('select#yntour_select_value').on('change',function(){
        $('#yn_tour_position').val($(this).val());
    });

    $('select#yntour_lang_select_value').off();
    $('select#yntour_lang_select_value').on('change',function(){
        var sId = $(this).val();
        $('.js_tour_lang_area.active').removeClass('active').fadeOut(100,function(){
            $('#js_tour_lang_area_'+sId).fadeIn(300).addClass('active');
        });
    });
    /* new tour */
    $('#yn_jtour_tour_name').remove();
    var $yn_jtour_name = '<div id="yn_jtour_tour_name" class="yntourguidetourcontrols" style="width:380px;padding:10px;display:none; position:absolute; left:450px; top:200px;text-shadow:none;font-size:11px;">';
    $yn_jtour_name +='<form name="createnew_tourguides" id="createnew_tourguides"  onsubmit="return false;">';
    $yn_jtour_name +='<div class="title" yntour="yn_tour_create_new">'+oTranslations['tour_guide_name']+'<span id="yn_jtour_button_close_2" class="yntourguideclose" style="top:9px;"></span></div>';
    $yn_jtour_name +='<div class="yn_tour_create_new" yntour="yn_tour_create_new"><input name="tour_name" id="tour_name" type="text" value="" maxlength="255" style="width:100%;margin: 10px 0px;border:none"/></div>';
    $yn_jtour_name +='<div class="yn_tour_create_new" yntour="yn_tour_create_new"><input name="is_auto" type="checkbox" value="1" style="margin:1px 4px 0px 0px;" id="is_auto"/>'+oTranslations['autorun_this_tour']+'</div>';
    $yn_jtour_name +='<span id="yn_tour_error" hidden style="color:red;">*'+oTranslations['tourguides.provide_tour_guide_name']+'</span>';
    $yn_jtour_name +='<input id="yn_jtour_button_save_continue_create_tour" type="button" value="'+oTranslations['save_countinue']+'"  class="yntourguidebutton" style="margin-left:0px;"/>';
    $yn_jtour_name +='<input id="yn_jtour_button_cancel_create_tour" type="button" value="'+oTranslations['cancel']+'"  class="yntourguidebutton"/>';
    $yn_jtour_name += '</form>';
    $('BODY').prepend($yn_jtour_name);
/* end */
}

function hideControls(){
    $('#tourcontrols').remove();
}

function showOverlay(){
    var $overlay    = '<div id="tour_overlay" class="yntourguideoverlay"></div>';
    $('BODY').prepend($overlay);
}

function hideOverlay(){
    $('#tour_overlay').remove();
}
