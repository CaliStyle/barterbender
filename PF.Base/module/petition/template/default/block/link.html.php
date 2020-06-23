<?php 
/**
 * [PHPFOX_HEADER]
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{if (Phpfox::getUserParam('petition.edit_own_petition') && Phpfox::getUserId() == $aItem.user_id) || Phpfox::getUserParam('petition.edit_user_petition') ||
($aItem.module_id == 'pages' && Phpfox::getService('pages')->isAdmin('' . $aItem.item_id . ''))
}
   {if $aItem.module_id == 'pages'}
      <li><a href="{url link="petition.add" id=""$aItem.petition_id"" module="pages" item=""$aItem.item_id""}">{phrase var='petition.edit'}</a></li>
   {else}
      <li><a href="{url link="petition.add" id=""$aItem.petition_id""}">{phrase var='petition.edit'}</a></li>
   {/if}
{/if}
{if Phpfox::getUserParam('petition.can_feature_petition') && $aItem.is_approved == 1 && !Phpfox::isMobile() && Phpfox_Module::instance()->getModuleName() && $isPages != 'pages'}
        <li id="js_petition_feature_{$aItem.petition_id}">
        {if $aItem.is_featured}
                <a href="#" title="{phrase var='petition.un_feature_this_petition'}" onclick="$.ajaxCall('petition.feature', 'petition_id={$aItem.petition_id}&amp;type=0', 'GET'); return false;">{phrase var='petition.un_feature'}</a>
        {else}
                <a href="#" title="{phrase var='petition.feature_this_petition'}" onclick="$.ajaxCall('petition.feature', 'petition_id={$aItem.petition_id}&amp;type=1', 'GET'); return false;">{phrase var='petition.feature'}</a>
        {/if}
        </li>
{/if}
{if Phpfox::isAdmin() && $aItem.module_id != 'pages' && $aItem.is_approved == '1' && $aItem.petition_status == '2'  && !Phpfox::isMobile()}
   <li id="js_petition_directsign_{$aItem.petition_id}">
      {if $aItem.is_directsign }
         <a href="#" title="{phrase var='petition.unset_direct_sign'}" onclick="$.ajaxCall('petition.directsign', 'petition_id={$aItem.petition_id}&amp;active=0&amp;inline=true', 'GET'); return false;">{phrase var='petition.unset_direct_sign'}</a>
      {else}
         <a href="#" title="{phrase var='petition.set_direct_sign'}" onclick="$.ajaxCall('petition.directsign', 'petition_id={$aItem.petition_id}&amp;active=1&amp;inline=true', 'GET'); return false;">{phrase var='petition.set_direct_sign'}</a>         
      {/if}
   </li>
{/if}
{if (Phpfox::getUserParam('petition.delete_own_petition') && Phpfox::getUserId() == $aItem.user_id)
   || Phpfox::getUserParam('petition.delete_user_petition')
   ||($aItem.module_id == 'pages' && Phpfox::getService('pages')->isAdmin('' . $aItem.item_id . ''))
   }
   {if isset($bPetitionView) && $bPetitionView == true}
      <li class="item_delete"><a href="{url link='petition' delete=$aItem.petition_id}" class="sJsConfirm">{phrase var='petition.delete'}</a></li>      
   {else}
      <li class="item_delete"><a href="#" title="{phrase var='petition.delete'}" onclick="if (confirm('{phrase var='petition.are_you_sure_you_want_to_delete_this_petition' phpfox_squote=true}')) $.ajaxCall('petition.inlineDelete', 'item_id={$aItem.petition_id}'); return false;">{phrase var='petition.delete'}</a></li>
   {/if}
{/if}
{plugin call='petition.template_block_entry_links_main'}
