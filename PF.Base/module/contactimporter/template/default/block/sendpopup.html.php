<form id="submit-form" action="" method="post" class="yncontact_form_send">    
	{if $sNoticeQuota}
	<div class="extra_info" >
		{$sNoticeQuota}
	</div>
	{/if}
    <div style="padding:10px;" id="sending">
        <ul>
            <li style="font-weight:bold;">{_p var='sending'} (<span id="totalsent">0</span>/<span id="total">{$iTotal}</span>)
            	<img class="yncontact_sending" src="{$sCorePath}module/contactimporter/static/image/add.gif"/>
            </li>
			 <li style="font-weight:bold;">{_p var='successed'}  <span id="successed">0</span></li>
			 <li style="font-weight:bold;">{_p var='failed'}  <span id="failed">0</span></li>
            <li>
                <div id="progressbar">
                    <div id="percent"></div>
                </div>
            </li>
        </ul>
    </div>
    <div class="yncontact_close_sending">
        <button type="button" class="btn btn-sm btn-default" value="{_p var='close'}" id="close-button">{_p var='close'}</button>
    </div>
    <div class="clear"></div>
</form>
{literal}<script language="javascript" type="text/javascript">
    var url = "{/literal}{$sMainUrl}{literal}",
        total = "{/literal}{$iTotal}{literal}",
        provider = "{/literal}{$sProvider}{literal}",
        message = $('#message').val(),
        fail = 0,
        isSent = false;
    if($.trim(message).length > 0)
    {
        $Behavior.initJs = function() {
            var popup = $('#submit-form').closest(".js_box");
            var btDiv = popup.find(".js_box_close");	
            var message = $('#message').val();
            
            var contacts = '{/literal}{$yncontacts}{literal}';
            contacts = jQuery.parseJSON(contacts);
            if(provider == "csv"){
                if (contacts instanceof Array){
                    
                }
                else
                {
                    contacts = contacts.split(',');
                }
            }
            
			
            if (contacts instanceof Array){
            	contacts = contacts.slice(0, total);
            }
          
            $('#close-button').click(function(evt) {   
                tb_remove();
                location.href = url;
                return false;
            });
            setTimeout(function(){btDiv.hide();}, 1);
            if(total == 0)
            {
                setTimeout(function(){location.href = url ;}, 3000);
            }
            else if (!isSent)
            {
                sendInvite(contacts, fail);
            }
        };
        sendInvite = function(contacts, fail) {
            isSent = true;
            $Core.ajax('contactimporter.sendInvite',
            {
                params:
                    {            
                    provider: provider,                
                    message: message,                
                    total: total,                
                    contacts: contacts,
                    fail: fail
                },
                type: 'POST',
                success: function(response)
                {
                    response = $.parseJSON(response);
                    var percent = response['percent'];
                    var success = response['success'];
                    var contacts = response['contacts'];
                    var totalsent = response['totalsent'];
                    var fail = response['fail'];
                    var error = response['error'];
                    $("#totalsent").html(totalsent);
                    $("#successed").html(success);
                    $("#failed").html(fail);
                    $("#percent").css('width', percent);
                    if (success + fail < total) {                   	
                        sendInvite(contacts, fail);
                    } else {
                        setTimeout(function(){location.href = url + 'success_' + success + '/fail_' + fail ;}, 3000);    
                    }            
                }
            });
        }
    }
    else{
        $('#close-button').remove();
        $("#sending").html('<div class="error_message">{/literal}{$sEmptyMsg}{literal}</div>');        
    }
</script>{/literal}