<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if count($aProviders)}
<div id="privacy_holder_table">
    <div align="center" class="page_section_menu_holder" id="js_setting_block_connections">
        {foreach from=$aProviders index=iKey name=Provider item=aProvider}
        {if isset($aProvider)}
        <div class="socialbridge_provider">
            <a href="{if isset($aProvider.Agent)}{url link='socialbridge.setting'}{else}javascript:void(openauthsocialbridge('{url link='socialbridge.sync' service=$aProvider.name status='connect' redirect=1}'));{/if}">
                <img src="{$sCoreUrl}module/socialbridge/static/image/{$aProvider.service}.jpg" alt="{$aProvider.name}" class="socialbridge_provider_img"/>
            </a>
            <div class="text">
                {if isset($aProvider.connected) and $aProvider.connected }
                <div class="socialbridge_connect_link" id="socialbridge_connect_link_{$aProvider.name}">
                    <div>
                        {if isset($aProvider.profile.img_url)}<img src="{$aProvider.profile.img_url}" alt="{$aProvider.profile.full_name}" align="left" height="32"/>{/if}
                    </div>
                    <div class="ml-5">
                        {_p var='socialbridge.connected_as' full_name=''} {$aProvider.profile.full_name|clean|shorten:18...}<br/>
                        <a href="{url link='socialbridge.setting' disconnect=$aProvider.service}" data-message="{_p var='socialbridge.are_you_sure_you_want_to_disconnect_this_account'}" class="sJsConfirm">{_p var='socialbridge.click_here'}</a> {_p var='socialbridge.to'} {_p var='socialbridge.disconnect'}.
                    </div>
                </div>
                {else}
                <div class="socialbridge_connect_link" id="socialbridge_connect_link_{$aProvider.name}">
                    <a href="javascript:void(openauthsocialbridge('{url link='socialbridge.sync' service=$aProvider.service status='connect' redirect=1}'));">{_p var='socialbridge.click_here'}</a> {_p var='socialbridge.to'} {_p var='socialbridge.connect'}.
                </div>
                {/if}
            </div>
        </div>
        {if is_int($phpfox.iteration.Provider/3)}
        <div class="clear"></div>
        {/if}
        {/if}
        {/foreach}
    </div>
    {plugin call='socialbridge.template_controller_setting'}
</div>
{if !empty($sTab)}
{literal}
<script type="text/javascript">
    $Behavior.pageSectionMenuRequest = function() {
        $Core.pageSectionMenuShow('#js_setting_block_{/literal}{$sTab}{literal}');
    }
</script>
{/literal}
{/if}
{else}
<div class="pulic_message">{_p var='socialbridge.there_are_no_social_providers_were_enable_please_contact_to_admin_site_to_get_more_information'}</div>
{/if}