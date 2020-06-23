<?php

defined('PHPFOX') or exit('NO DICE!');

?>
{literal}
<style>
ul.opensocialconnect_holder_header
{
    position: relative;
    left: {/literal}{$iMarginLeft}{literal}px;
    top: {/literal}{$iMarginTop}{literal}px;
    width: auto;
    display: none;
} 
ul#opensocialconnect_holder_header li
{
    padding:3px;
}  
ul#opensocialconnect_holder_header li img
{
    width:{/literal}{$iIconSize}{literal}px;
    height:{/literal}{$iIconSize}{literal}px;
}
</style>
<script>
function opensopopup(pageURL)
{
    tb_remove();
    var w = 990;
    var h = 560;
    var title ="socialconnectwindow";
    var left = (screen.width/2)-(w/2);
    var top = (screen.height/2)-(h/2);
    var newwindow = window.open (pageURL, title, 'toolbar=no,location=no,directories=no,status=no,menubar=no, scrollbars=yes,resizable=yes,copyhistory=no,width='+w+',height='+h+',top='+top+',left='+left);
    if (window.focus) {newwindow.focus();}
    return newwindow;
};
</script>
{/literal}
{if count($aOpenProviders)}
<ul id="opensocialconnect_holder_header" class="opensocialconnect_holder_header clearfix" {if Phpfox::getLib('module')->getFullControllerName()=='user.login'}style="position: inherit; width:{$iWidth}px"{/if}>
    {foreach from=$aOpenProviders key=index item=aOpenProvider name=opr}
        {if $phpfox.iteration.opr <= $iLimitView }
            <li class="providers" {if Phpfox::getLib('module')->getFullControllerName()=='user.login'}style="float:left"{/if}> <a href="javascript: void(opensopopup('{url link='opensocialconnect' service=$aOpenProvider.name}'));" title="{$aOpenProvider.title}"><img src="{$sCoreUrl}module/opensocialconnect/static/image/{$aOpenProvider.name}.png" alt="{$aOpenProvider.title}" /></a> </li>
            {if $phpfox.iteration.opr ==  $iLimitView}
            <li><a href="#?call=opensocialconnect.viewMore&amp;width=410" title="{_p('module_opensocialconnect')}" class="inlinePopup usingapi"><img src="{$sCoreUrl}module/opensocialconnect/static/image/more.png" alt="" /></a>
            {/if}
        {/if}
    {/foreach}
</ul>
{if Phpfox::getLib('module')->getFullControllerName()=='user.login'}
<div class="clear"></div>
{/if}
{/if}
