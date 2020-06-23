<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<div class="ynfr-shareinfo-block">
    <div id="ynfr-shareinfo">
        <input type='hidden' value='{$aCampaign.campaign_id}' id='campaign_id' />
        <input type='hidden' value='{$sToken}' id='token' />
    </div>
</div>

{if Phpfox::getParam('core.show_addthis_section')}
<!-- AddThis Button BEGIN -->
<div class="addthis_share pf_video_addthis mb-7" style="margin-left: 10px;">
    <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid={$sAddThisPubId}" data-title="{$aItem.title|clean}"></script>
    {plugin call='video.template_controller_play_addthis_start'}
    {if $sAddThisShareButton}
    {$sAddThisShareButton}
    {else}
    <div class="addthis_toolbox addthis_32x32_style">
        <a class="addthis_button_preferred_1"></a>
        <a class="addthis_button_preferred_2"></a>
        <a class="addthis_button_preferred_3"></a>
        <a class="addthis_button_preferred_4"></a>
        <a class="addthis_button_compact"></a>
    </div>
    {/if}
    {literal}
    <script language="javascript" type="text/javascript">
        $Behavior.videoInitAddthis = function(){
            $('.addthis_toolbox').attr('addthis:url', window.location.href);
            $('.addthis_toolbox').attr('addthis:title', "{/literal}{_p('share_this_page_now')}{literal}");
            $('.addthis_toolbox').attr('addthis:description', "{/literal}{_p('share_this_page_now')}{literal}");
            addthis.toolbox('.addthis_toolbox');
        };
    </script>
    {/literal}
</div>
<!-- AddThis Button END -->
{/if}