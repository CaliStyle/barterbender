<?php 
/**
 * [PHPFOX_HEADER]
 */
 
defined('PHPFOX') or exit('NO DICE!'); 

?>
{literal}
<style type="text/css">
    table{margin-top: 10px;}
    .pet_help_tit a{font-size: 22px;color: #6f6f6f;}
    tr{border-bottom: 1px solid #dfdfdf;}
    td{padding:10px;}
    td.help_cont{vertical-align: top;}
    td.help_img{vertical-align: middle;}
    .pet_help_cont{white-space: pre-line;color: #565656;font-size: 12px;}
</style>
{/literal}
{if count($aHelps) > 0}    
<table>
{foreach from=$aHelps item=aHelp name=help}
    <tr>
        <td class="help_img">{img server_id=$aHelp.server_id path='core.url_pic' file=$aHelp.image_path suffix='_200' max_width='120' max_height='120' class='js_mp_fix_width'}</td>
        <td class="help_cont">
            <div class="pet_help_tit"><a href="{permalink module='fundraising.help' id=$aHelp.help_id title=$aHelp.title}" class="link">{$aHelp.title|clean|shorten:55:'...'|split:20}</a></div>
            <div class="pet_help_cont">{$aHelp.content_parsed|shorten:200:'...'|split:55}</div>
        </td>
    </tr>
{/foreach}
</table>
<div class="clear"></div>
{pager}
{else}
    {phrase var='no_helps_found'}
{/if}