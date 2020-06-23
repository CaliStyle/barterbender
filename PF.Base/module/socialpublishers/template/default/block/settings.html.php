<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{literal}
<script type="text/javascript">
    function updateSocialPublishersSetting(oObj)
    {		
        $(oObj).ajaxCall('socialpublishers.updateModulesSettings');
        return false;
    }
</script>
{/literal}
<div align="left" class="page_section_menu_holder" id="js_setting_block_socialpublishers" style="display:none">  
    {if count($aModules)}
    <div class="table_left">
        {_p var='socialpublishers.activity_management'}
    </div>
    <div class="table_right">
        <form method="post" action="#" onsubmit="return updateSocialPublishersSetting(this);">
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
                <button type="submit" value="{_p var='core.update'}" class="btn btn-sm btn-primary">{_p var='core.update'}</button>
            </div>
        </form>
    </div>
    {/if}
</div>