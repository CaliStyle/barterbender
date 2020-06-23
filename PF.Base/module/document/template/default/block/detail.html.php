<?php 
 /**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
/**
 * 
 * 
 * @copyright        [YOUNET_COPYRIGHT]
 * @author          YouNet Company
 * @package         YouNet_Document
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>

<div class="document_info_box">
    <div class="document_info_box_content">
        <div class="document_info_view">{if $aDocument.total_view == 0}1{else}{$aDocument.total_view|number_format}{/if}</div>    
        <ul class="document_info_box_list">
            <li class="full_name first" id="document_user_is_click">{$aDocument|user}</li>
            {foreach from=$aDocumentDetails key=sKey item=sValue}
            <li>{$sValue} ({$sKey})</li>
            {/foreach}
        </ul>

        <div class="document_info_box_text">
            <div class="document_text_shorten item_view_content">{$aDocument.text_shorten}</div>
            <div class="document_text_parsed item_view_content" style="display:none;">
                {if Phpfox::getParam('core.allow_html')}
                {$aDocument.text_parsed|parse}
                {else}
                {$aDocument.text|parse}
                {/if}
            </div>
        </div>

        <div class="document_info_box_extra">

            {if isset($aDocument.breadcrumb) && null != $aDocument.breadcrumb && count($aDocument.breadcrumb) > 0}
            <div class="ync-item-info-group">
                <div class="ync-item-info">
                    <span class="ync-item-label">{_p var='category'}:</span>
                    <div class="ync-item-content">{$aDocument.breadcrumb|category_display}</div>
                </div>
            </div>
            {/if}

            {if !empty($aDocument.tag_list)}
            <div class="table form-group">
                <div class="table_left">
                    {phrase var='tags'}:
                </div>
                <div class="table_right">
                {foreach from=$aDocument.tag_list name=tags item=aTag}
                    {if $phpfox.iteration.tags != 1}, {/if}<a onclick="window.location = this.href; return false;" href="{if isset($sGroup) && $sGroup !=''}{url link='group.'$sGroup'.document.tag.'$aTag.tag_url''}{else}{url link='document.tag.'$aTag.tag_url''}{/if}">{$aTag.tag_text}</a>
                {/foreach}
                </div>
            </div>
            {/if}    
        </div>
    </div>    
        <a href="#" onclick="toggleDocumentInfo();return false;" class="document_info_toggle">
        <span class="js_info_toggle_show_more">{phrase var='show_more'}&nbsp;<i class="fa fa-angle-double-down"></i></span>
        <span class="js_info_toggle_show_less">{phrase var='show_less'}&nbsp;<i class="fa fa-angle-double-up"></i></span>
    </a>    
</div>
