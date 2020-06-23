<?php
defined('PHPFOX') or exit('NO DICE!');
?>
{if count($aListReacted)}
<div class="ync-reaction-popup-user" id="js_ync_reaction_popup_user">
    <div class="page_section_menu page_section_menu_header ync-reaction-popup-header">
        <ul class="nav nav-tabs navpage_section_menu-justified nav-justified js_ync_reaction_popup_nav">
            <li class="item-li-all {if empty($iReactId)}active{/if}">
                <a data-toggle="tab" href="#ync_reaction_all" rel="ync_reaction_all" onclick="yncreaction.setTabColor(this);" {if empty($iReactId)}style="border-bottom: 3px solid #555555 !important;"{/if} data-color="555555">
                    <span class="item-all">{_p var='all'}</span>
                    <span class="item-number" {if empty($iReactId)}style="color:#555555 !important;"{/if}>{$iTotalReacted|short_number}</span>
                </a>
            </li>
            {foreach from=$aListReacted item=aReaction}
                <li class="{if !empty($iReactId) && $iReactId == $aReaction.id}active{/if}">
                    <a data-toggle="tab" href="#ync_reaction_{$aReaction.id}"  onclick="yncreaction.setTabColor(this);" rel="ync_reaction_{$aReaction.id}" {if !empty($iReactId) && $iReactId == $aReaction.id}style="border-bottom: 3px solid #{$aReaction.color} !important;"{/if} data-color="{$aReaction.color}">
                        <img src="{$aReaction.full_path}" alt="" class="ync-reacted-icon">
                        <span class="item-number" {if !empty($iReactId) && $iReactId == $aReaction.id}style="color:#{$aReaction.color} !important;"{/if}>{$aReaction.total_reacted|short_number}</span>
                    </a>
                </li>
            {/foreach}
        </ul>
    </div>

    <div class="tab-content">
        <div id="ync_reaction_all" class="page_section_menu_holder ync_reaction_all ync-reaction-popup-user-total-container" {if !empty($iReactId)}style="display: none;"{/if}>
            <div class="ync-reaction-popup-user-total-outer">
                {module name='yncreaction.detail-react' react_id=0 item_id=$iItemId feed_type=$sType table_prefix=$sPrefix}
            </div>
        </div>
        {foreach from=$aListReacted item=aReaction}
            <div id="ync_reaction_{$aReaction.id}" class="page_section_menu_holder ync_reaction_{$aReaction.id} ync-reaction-popup-user-total-container" {if $iReactId != $aReaction.id}style="display: none;"{/if}>
                <div class="ync-reaction-popup-user-total-outer">
                    {module name='yncreaction.detail-react' react_id=$aReaction.id item_id=$iItemId feed_type=$sType table_prefix=$sPrefix}
                </div>
            </div>
        {/foreach}
    </div>
</div>
{/if}
{literal}
<script type="text/javascript">
    $('#js_ync_reaction_popup_user').closest('.js_box').addClass('ync-reaction-popup-box');
</script>
{/literal}