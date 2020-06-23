<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div id="privacy_holder_table" class="p_4">
    <div align="center" class="socialpublishers_provider_content js_privacy_block page_section_menu_holder" id="js_setting_block_connections" style="display:none">
        {if count($aPublisherProviders)}
        {foreach from=$aPublisherProviders index=iKey name=apu item=aPublisherProvider}
        <div class="socialpublishers_provider">
            <a href="{if isset($aPublisherProvider.Agent)}{url link='socialpublishers.setting'}{else}javascript:void(openauthpublishers('{url link='socialpublishers.sync' service=$aPublisherProvider.name status='connect' redirect=1}'));{/if}">
                <img src="{$sCoreUrl}module/socialpublishers/static/image/{$aPublisherProvider.name}.jpg" alt="{$aPublisherProvider.title}" class="socialpublishers_provider_img"/>
            </a>
            <div class="text">
                {if isset($aPublisherProvider.Agent)}
                <img src="{$aPublisherProvider.Agent.img_url}" alt="{$aPublisherProvider.Agent.full_name}" align="left"/>
                {_p var='socialpublishers.connected_as' full_name=''} {$aPublisherProvider.Agent.full_name|clean|shorten:18...}<br/>
                <a href="{url link='socialpublishers.setting' disconnect=$aPublisherProvider.name}">{_p var='socialpublishers.click_here'}</a> {_p var='socialpublishers.to'} {_p var='socialpublishers.disconnect'}.
                {else}    
                <a href="javascript:void(openauthpublishers('{url link='socialpublishers.sync' service=$aPublisherProvider.name status='connect' redirect=1}'));">{_p var='socialpublishers.click_here'}</a> {_p var='socialpublishers.to'} {_p var='socialpublishers.connect'}.
                {/if}
            </div>
        </div>
        {/foreach}

        {else}
        <div class="pulic_message">{_p var='socialpublishers.there_are_no_publisher_providers_were_enable_please_contact_to_admin_site_to_get_more_information'}</div>
        {/if}
        {*
        <a href="javascript:void(0);" onclick="$Core.box('socialpublishers.share', 450, 'fid=1&no_remove_box=true');">test</a>
        <div class="clear"></div>
        *}

        <div class="clear"></div>
    </div>
    <div class="js_setting_block page_section_menu_holder" id="js_setting_block_modules" style="display:none">
        {if count($aModules)}
        <form action="{url link='socialpublishers.setting'}" method="post">
            {foreach from=$aModules item=aModule}
            <div class="table form-group">
                <div class="table_left">
                    {_p var=$aModule.title}
                </div>
                <div class="table_right">
                    {if $aModule.facebook == 1}
                    <label><input type="checkbox" value="1" name="val[{$aModule.module_id}][facebook]" {if !isset($aModule.user_setting.facebook) || $aModule.user_setting.facebook == 1  }checked{/if}/>{_p var='socialpublishers.facebook'}</label>
                    {/if}
                    {if $aModule.twitter == 1}
                    <label><input type="checkbox" value="1" name="val[{$aModule.module_id}][twitter]" {if  !isset($aModule.user_setting.twitter) || $aModule.user_setting.twitter == 1}checked{/if}/>{_p var='socialpublishers.twitter'}</label>
                    {/if}
                    {if $aModule.linkedin == 1}
                    <label><input type="checkbox" value="1" name="val[{$aModule.module_id}][linkedin]" {if !isset($aModule.user_setting.linkedin)|| $aModule.user_setting.linkedin == 1}checked{/if}/>{_p var='socialpublishers.linkedin'}</label>
                    {/if}
                    <label><input type="checkbox" value="1" name="val[{$aModule.module_id}][no_ask]" {if !isset($aModule.user_setting.no_ask) || $aModule.user_setting.no_ask == 1 }checked{/if}/>{_p var='socialpublishers.don_t_ask_me_again'}</label>
                    <input type="hidden" value="{$aModule.is_insert}" name="val[{$aModule.module_id}][is_insert]"/>
                </div>            
                <div class="clear"></div>
            </div>     
            {/foreach}   
            <div class="table_clear">
                <button type="submit" value="{_p var='user.save_changes'}" class="btn btn-sm btn-primary">{_p var='user.save_changes'}</button>
            </div>            
    </div>
</form>
{/if}
</div>

