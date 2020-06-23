<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
{literal}
<style>
ul#opensocialconnect_holder_header_view_more li
{
    padding: 3px;
    float: left;
}  
ul#opensocialconnect_holder_header_view_more li img
{
    width: 32px;
    height: 32px;
} 
#header_menu_holder ul#opensocialconnect_holder_header_view_more li a, 
#header_menu_holder ul#opensocialconnect_holder_header li a:hover
{
    line-height: 10px;
    padding: 0px;
}
</style>
{/literal}
{if count($aOpenProviders)}
<ul id="opensocialconnect_holder_header_view_more">
    {foreach from=$aOpenProviders key=index item=aOpenProvider name=opr}
        <li class="providers"> <a href="javascript: void(opensopopup('{url link='opensocialconnect' service=$aOpenProvider.name}'));" title="{$aOpenProvider.title}"><img src="{$sCoreUrl}module/opensocialconnect/static/image/{$aOpenProvider.name}.png" alt="{$aOpenProvider.title}" /></a> </li>
    {/foreach}
</ul>
 <div class="clear"></div>
{/if}
