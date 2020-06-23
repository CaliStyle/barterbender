<?php
defined('PHPFOX') or exit('NO DICE!');

?>
<div class="jobposting-company-feed {if $sImageSrc}has-image{/if}">
    {if $sImageSrc}
    <div class="jobposting-company-feed-image">
        <span style="background-image: url({$sImageSrc})"></span>
    </div>
    {/if}
    <div class="jobposting-company-feed-info">
        <div class="jobposting-company-title"><a href="{$sLink}">{$aCompany.name|clean}</a></div>
        <div class="jobposting-company-info-general">
            <span class="jobposting-company-datetime">{$aCompany.time_stamp|convert_time:'core.global_update_time'}</span>
            {if !empty($sCategories)}
            <span class="jobposting-company-catgory">{_p var='industry'}: {$sCategories}
            {/if}
        </div>
        <div class="jobposting-company-content item_content">{$aCompany.description_parsed|stripbb|feed_strip|split:55|max_line}</div>
    </div>
</div>