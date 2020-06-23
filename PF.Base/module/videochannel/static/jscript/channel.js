//id of current active video list
var activeId = 0;

function editChannel(ele,id,isAddMore, sModule, iItem)
{
    $('#TB_ajaxContent').empty();
    var parent =  $('#js_channel_entry_'+id);   
    if(!parent)
        return false;
   
    var url = parent.find('div.en_url').html();
    var title = parent.find('div.en_title').html();
    var description = parent.find('div.en_summary').html();
    var img = parent.find('div.en_img').html();
   
    $Core.box('videochannel.channel.addChannel',600,'title='+title+'&url='+url+'&des='+description+'&img='+img+'&iChannelId='+id+'&act='+isAddMore +'&module=' + sModule + '&item=' + iItem);
}

function addChannel(ele,id, sModule, iItem)
{
    $('#TB_ajaxContent').empty();
    var parent =  $('#js_channel_entry_'+id);   
    if(!parent)
        return false;
   
    var url = parent.find('div.en_url').html();
    var title = parent.find('div.en_title').html();
    var title_not_encode = parent.find('div.title_not_encode').html();
    var description = parent.find('div.en_summary').html();
    var description_not_encode = parent.find('div.summary_not_encode').html();
    var img = parent.find('div.en_img').html();

    title_not_encode = title_not_encode.replace('#', 'number_sign');
    description_not_encode = description_not_encode.replace('#', 'number_sign');

    $Core.box('videochannel.channel.addChannel',600,'title='+title+'&title_not_encode='+title_not_encode+'&url='+url+'&des='+description+ '&description_not_encode='+description_not_encode+'&img='+img+'&id='+id+'&module=' + sModule + '&item=' + iItem);
}

function autoUpdate(id, sModule, iItem)
{
    $('#TB_ajaxContent').empty();
    $('#js_channel_processing_' + id).show('fast',function(){
        $.ajaxCall('videochannel.channel.autoupdate','id='+id +'&module=' + sModule + '&item=' + iItem);
    });
}

function deleteChannel(id, sModule, iItem)
{
    $('#TB_ajaxContent').empty();
    $('#js_channel_processing_' + id).show('fast',function(){
        $.ajaxCall("videochannel.channel.deleteChannel",'id='+id +'&module=' + sModule + '&item=' + iItem);
    });
}

function loadVideoList(l)
{
    $('#TB_ajaxContent').empty();
    $.ajaxCall("videochannel.channel.loadVideoList",'url='+l);
    $("div#channel_video_list").show();
    $("div#video_list_action").show();   
    activeId = 0;
}

function findChannels()
{
    $('#TB_ajaxContent').empty();
    var s = $("#search_channel");
    var l = $("#search_channel_loading");
    var kw = $("#keyword");
    var r = $('#search_channel_results');
        
    if($.trim(kw.val()).length == 0)
    {
        kw.val('');
        $("#channel_error").show();
        return false;
    }        
    else
    {       
        s.hide();		
        l.show();
        r.slideUp();
    }
}

function nextVideoList(ele, limit)
{
    //Stop current animation
    $(".active").stop(true,true);
    var p = $("#channel_video_list");
   
    var h = $(".active"); 
    activeId += limit;
    var s = p.find('ul#js_channel_video_list_'+activeId);
      
    h.fadeOut('slow', function(){
        var ch = s.find("a.moderate_link").length;
        var nch = s.find("a.moderate_link_active").length;
        if(ch == nch)
        {
            p.parent().find('.selectall').hide();
            p.parent().find('.unselectall').show();
        }
        else
        {         
            p.parent().find('.selectall').show();
            p.parent().find('.unselectall').hide();
        }
        s.fadeIn('slow');
        h.removeClass('active').hide();
        s.addClass('active');
    });
   
   
    var c = activeId + limit;
   
   
    if(!(p.find('ul#js_channel_video_list_' + c).length))
    {
        p.find('li#next').hide();
    }
  
    p.find('li#prev').show();
   
}

function prevVideoList(ele, limit)
{
    $(".active").stop(true,true);
    var p = $(ele).parent().parent().parent();
   
    var h = $(".active");
    activeId -= limit;
    var s = p.find('ul#js_channel_video_list_'+activeId);
      
    h.fadeOut('slow', function(){
        var ch = s.find("a.moderate_link").length;
        var nch = s.find("a.moderate_link_active").length;
        if(ch == nch)
        {
            p.parent().find('.selectall').hide();
            p.parent().find('.unselectall').show();
        }
        else
        {         
            p.parent().find('.selectall').show();
            p.parent().find('.unselectall').hide();
        }
        s.fadeIn('slow');      
        h.removeClass('active').hide();
        s.addClass('active');
    });
   
    var c = activeId - limit;
    if(!(p.find('ul#js_channel_video_list_' + c).length))   
        p.find('li#prev').hide();
  
    p.find('li#next').show();
   
}

function selectVideo(ele, id)
{
    var p = $("select#video_select_box");
    var v = p.find('option#video_'+id);
    var action = $(ele).attr("data-selected");
	

    if(action == 'Un-Select')
    {
        v.removeAttr('selected');
        $(ele).removeClass("moderate_link_active");
        $(ele).attr("data-selected","Select");
        /*
        $(ele).html('Select');
        $(ele).removeClass('moderate_link_active');
             */
    }
    else
    {
        v.attr("selected","selected");

        $(ele).removeClass("moderate_link_active");
        $(ele).addClass("moderate_link_active");
        $(ele).attr("data-selected","Un-Select");
        /*
        $(ele).html('Un-Select')

        $(ele).addClass('moderate_link_active');
              */
    }
}

function selectAllVideo(ele)
{
    var p1 = $(ele).parent();
    var p2 = p1.parent().find("div#channel_video_list");   
    var ul = p2.find('ul.active');
    var cb = ul.find("a.moderate_link");
   
    var action ='Select';
   
    if($(ele).hasClass('unselectall'))
        action ='Un-Select';
      
    cb.each(function(){
        //$(this).html(action);
        $(this).click();
    });
   
    if($(ele).hasClass('selectall'))
        p1.find('.unselectall').show();   
    else
        p1.find('.selectall').show();
    $(ele).hide();   
}

function deleteAllVideos(id)
{
    if(id <= 0)
        return false;
    else
    {
        $('#img_action').show();
        $('.btn_submit').hide();
        $.ajaxCall("videochannel.channel.deleteAllVideos",'id='+id);      
    }
}

function addChannelUrl(sModule, iItem)
{
    var sUrl = $('#channel_url').val();
    if($.trim(sUrl).length == 0)
    {
        //sUrl.val('');
        $("#channel_url_error").show();
        return false;
    }   
    $Core.box('videochannel.channel.addChannelUrl',600,'url=' + ynvc_base64_encode(sUrl) +'&module=' + sModule + '&item=' + iItem);
}

function ynvc_base64_encode (data) {
  // http://kevin.vanzonneveld.net
  // +   original by: Tyler Akins (http://rumkin.com)
  // +   improved by: Bayron Guevara
  // +   improved by: Thunder.m
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   bugfixed by: Pellentesque Malesuada
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +   improved by: Rafal Kukawski (http://kukawski.pl)
  // *     example 1: base64_encode('Kevin van Zonneveld');
  // *     returns 1: 'S2V2aW4gdmFuIFpvbm5ldmVsZA=='
  // mozilla has this native
  // - but breaks in 2.0.0.12!
  //if (typeof this.window['btoa'] === 'function') {
  //    return btoa(data);
  //}
  var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
  var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
    ac = 0,
    enc = "",
    tmp_arr = [];

  if (!data) {
    return data;
  }

  do { // pack three octets into four hexets
    o1 = data.charCodeAt(i++);
    o2 = data.charCodeAt(i++);
    o3 = data.charCodeAt(i++);

    bits = o1 << 16 | o2 << 8 | o3;

    h1 = bits >> 18 & 0x3f;
    h2 = bits >> 12 & 0x3f;
    h3 = bits >> 6 & 0x3f;
    h4 = bits & 0x3f;

    // use hexets to index into b64, and append result to encoded string
    tmp_arr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
  } while (i < data.length);

  enc = tmp_arr.join('');

  var r = data.length % 3;

  return (r ? enc.slice(0, r - 3) : enc) + '==='.slice(r || 3);

}

$Behavior.ynvcfixsearch = function(){

    //console.log("test channel.js");
    if (window.jQuery)
    {
        if ($('#page_videochannel_index').length)
        {
            $( ".action_drop a" ).each(function( index ) {
                //console.log( index + ": " + $( this ).text() );
                var href =  $( this ).attr("href");
                //console.log(href);
                href = href.replace("view=channels/?","view=channels&");
                href = href.replace("view=my/?","view=my&");
                href = href.replace("view=favorite/?","view=favorite&");
                href = href.replace("view=featured/?","view=featured&");
                href = href.replace("view=friend/?","view=friend&");
                href = href.replace("view=pending/?","view=pending&");
                href = href.replace("view=all_channels/?","view=all_channels&");


                $( this ).attr("href",href);
            });
        }
    }


}