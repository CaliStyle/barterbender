<?php

defined('PHPFOX') or exit('NO DICE!');

?>
{literal}
<script>
    function openauthsocialbridge(url,ele)
    {
        return openauthpublishers(url);
    }
</script>
{/literal}
<script type="text/javascript">$Core.loadStaticFile("{jscript file='socialbridge.js' module='socialbridge'}");</script>
<script>$Core.loadStaticFile(oParams['sJsHome']+'module/socialpublishers/static/jscript/socialpublishers.js');</script>
<form id="socialpublishers_form_share" name="socialpublishers_form_share">
<div class="socialpublishers_status">
    {if isset($aUser.user_image) && $aUser.user_image !=''}
     <a href="{url link='profile'}" title="{$aUser.full_name}">
        <img data-src="{$aUser.link_profile}" src="{$aUser.link_profile}" alt="{$aUser.full_name}" align="left" class=" _image_50 image_deferred ">
    </a>
    {else}
        {img user=$aUser suffix='_50' align="left" }
    {/if}
    <div>
        <textarea cols="41" rows="2" name="val[status]" class="socialpublishers_status_text" placeholder="{_p var='socialpublishers.what_s_on_your_mind'}" ></textarea>
    </div>
</div>
<div class="clear"></div>
<input type="hidden" name='val[item_id]' id='item_id' value="{if isset($aParams.item_id)}{$aParams.item_id}{/if}">
<fieldset class="socialpublishers_content">
    <legend>{_p var='socialpublishers.share_content'}</legend>
    <div class="socialpublishers_content">
    {if isset($aParams.img)}
    <img src="{$aParams.img}" alt="" align="left" class="avatar" width="50px" heigth="50px"/>
    {/if}
    <span>
       {if isset($aParams.url)}<a href="{$aParams.url}">{$aParams.url|shorten:60:'...'}</a><br/>{/if}
       {if isset($aParams.content)}{$aParams.content}{/if}
    </span>
    <input type="hidden" value="{if isset($aParams.img)}{$aParams.img}{/if}" name="val[img]" id="socialpublishers_form_share_img"/>
    <input type="hidden" value="{if isset($aParams.content)}{$aParams.content|clean|shorten:150}{/if}" name="val[content]" id="socialpublishers_form_share_content"/>
    <input type="hidden" value="{if isset($aParams.url)}{$aParams.url}{/if}" name="val[url]" id="socialpublishers_form_share_url"/>
    <input type="hidden" value="{if isset($aParams.type)}{$aParams.type}{/if}" name="val[type]" id="socialpublishers_form_share_type"/>
</div>
</fieldset>
<div class="clear"></div>

{if count($aPublisherProviders)}
<ul class="socialpublishers_share_providers">
    {foreach from=$aPublisherProviders index=iKey name=apu item=aPublisherProvider}
        <li class="socialpublishers_provider_popup" style="background:url({$sCoreUrl}module/socialpublishers/static/image/{$aPublisherProvider.name}.png) no-repeat scroll left center;height:40px;">
                {if $aPublisherProvider.connected }
                   <div class="socialpublishers_share_providers_checkbox">
                   <input class="check_option" type="checkbox" {if (isset($aPublisherProvider.is_checked) && $aPublisherProvider.is_checked == 1)|| !isset($aPublisherProvider.is_checked)} checked="checked"{/if} value="{$aPublisherProvider.name}" name="val[provider][{$aPublisherProvider.name}]"/>

                   <span id="showpopup_span_connected_{$aPublisherProvider.name}">{_p var='socialpublishers.connected_as' full_name=''} {$aPublisherProvider.profile.full_name|clean|shorten:18...}</span>
                   </div>
                {else}
                   <div class="socialpublishers_share_providers_checkbox">
                   <input type="checkbox" value="{$aPublisherProvider.name}" name="val[provider][{$aPublisherProvider.name}]" onclick="openauthsocialbridge('{url link='socialpublishers.sync' service=$aPublisherProvider.name redirect=0}',this);" id="showpopup_checkbox_connected_{$aPublisherProvider.name}"/>
                   <span id="showpopup_span_connected_{$aPublisherProvider.name}">{_p var='socialpublishers.not_connected'} (<a href="javascript:void(0);" onclick="openauthsocialbridge('{url link='socialpublishers.sync' service=$aPublisherProvider.name redirect=0}',this);">{_p var='socialpublishers.connect'}</a>)</span>
                   </div>
                {/if}

        </li>
    {/foreach}
</ul>
{/if}
    <div class="clear"></div>
    <div class="socialpublishers_button_control_optional"  style="padding-top:4px;">
        <input style="margin-right: 5px" class="check_option" type="checkbox" name="val[no_ask]" value="1">{_p var='socialpublishers.don_t_ask_me_again'}</div>

<div class="clear"></div>
<div class="socialpublishers_button_control">
    <span id="socialpublishers_button_control_span">
    <button type="button" class="btn btn-sm btn-primary" name="socialpublishersshare" value="{_p var='socialpublishers.publish'}" onclick="return submitf(this);">{_p var='socialpublishers.publish'}</button>
    </span>
</div>
<div class="clear"></div>
</form>
{literal}
<script type="text/javascript">
    function submitf(f) {
        $('#socialpublishers_button_control_span').html($.ajaxProcess('no_message'));
        $('#socialpublishers_button_control_span').css("float", "left");
        $('#socialpublishers_button_control_span').css("margin-right", "5px");
        $('#socialpublishers_form_share').ajaxCall('socialpublishers.publish');
        show_suggestion();
        return true;
    }

    function cancelf(f) {
        $('#socialpublishers_button_control_span').html($.ajaxProcess('no_message'));
        $('#socialpublishers_button_control_span').css("float", "left");
        $('#socialpublishers_button_control_span').css("margin-right", "5px");
        $('#socialpublishers_form_share').ajaxCall('socialpublishers.cancelpublish');
        js_box_remove($(f).parent());
        return true;
    }

    // close suggest if have
    var mLoadclear = setInterval(function () {
        if ($('.suggestion_and_recommendation_js_box_close').length) {
            close_suggestion();
            clearInterval(mLoadclear);
        }
    }, 500);


    function close_suggestion() {
        $('.suggestion_and_recommendation_js_box_close').parent().hide();
    }

    function show_suggestion() {
        $('.suggestion_and_recommendation_js_box_close').parent().show();
    }
    $Behavior.initPublisherPopup = function() {
        $('.js_box_close').addClass('social_publisher_cancel').find('a').attr('onclick', '');
        $('.social_publisher_cancel').click(function (evt) {
            var e = $(evt.currentTarget);
            cancelf(e);
            show_suggestion();
        });
    };
</script>
{/literal}
{unset var1=$aUser var2=$aPublisherProviders var3=$sParams}