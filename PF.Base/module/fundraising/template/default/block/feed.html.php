<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="fundraising-feed {if $sImageSrc}has-image{/if}">
    {if $sImageSrc}
    <div class="fundraising-feed-image">
        <span style="background-image: url({$sImageSrc})"></span>
    </div>
    {/if}
    <div class="fundraising-feed-info">
        <div class="fundraising-title"><a href="{$sLink}">{$aCampaign.title|clean}</a></div>
        <div class="fundraising-info-general">
            <span class="fundraising-datetime">{$aCampaign.time_stamp|convert_time:'core.global_update_time'}</span>
            {if !empty($aCategories)}
                <span class="fundraising-catgory">{_p var='category'}:
                {foreach from=$aCategories item=aCategory key=iKey}
                    {if $iKey != 0}&#8250; {/if}<a href="{$aCategory.1}">{$aCategory.0}</a>
                {/foreach}
            {/if}
        </div>
        <div class="fundraising-content item_content">{$aCampaign.description|stripbb|feed_strip|split:55|max_line}</div>
    </div>
</div>