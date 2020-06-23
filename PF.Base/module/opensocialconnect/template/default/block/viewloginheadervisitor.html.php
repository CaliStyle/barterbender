
{literal}
<style>
div#opensocialconnect_holder_header
{
    margin-left: {/literal}{$iMarginLeft}{literal}px;
    margin-top: {/literal}{$iMarginTop}{literal}px;
    z-index: 9990;
    padding: 3px;
    display: inline-block;
} 

div#opensocialconnect_holder_header img
{
    width:{/literal}{$iIconSize}{literal}px;
    height:{/literal}{$iIconSize}{literal}px;
} 

div#opensocialconnect_holder_header .providers
{
   float:left;
   padding-left: 7px;
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
<div id="opensocialconnect_holder_header">
    {foreach from=$aOpenProviders key=index item=aOpenProvider name=opr}
        {if $phpfox.iteration.opr <= $iLimitView }
            <div class="providers" > <a href="javascript: void(opensopopup('{url link='opensocialconnect' service=$aOpenProvider.name}'));" title="{$aOpenProvider.title}"><img src="{$sCoreUrl}module/opensocialconnect/static/image/{$aOpenProvider.name}.png" alt="{$aOpenProvider.title}" /></a> </div>
            {if $phpfox.iteration.opr ==  $iLimitView}
            <div class="providers"><a class="inlinePopup usingapi" title="{_p('module_opensocialconnect')}" href="#?call=opensocialconnect.viewMore&amp;width=410"><img style="padding-top: 0px;" src="{$sCoreUrl}module/opensocialconnect/static/image/more.png" alt="" /></a></div>
            {/if}
        {/if}
    {/foreach}
</div>
{/if}
