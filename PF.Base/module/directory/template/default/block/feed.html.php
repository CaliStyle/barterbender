<?php
defined('PHPFOX') or exit('NO DICE!');

?>
<div class="directory-feed {if $sImageSrc}has-image{/if}">
    {if $sImageSrc}
    <div class="directory-feed-image">
        <span style="background-image: url({$sImageSrc})"></span>
    </div>
    {/if}
    <div class="directory-feed-info">
        <div class="directory-title"><a href="{$sLink}">{$aItem.name|clean}</a></div>
        <div class="directory-info-general">
            <span class="directory-datetime">{$aItem.time_stamp|convert_time:'core.global_update_time'}</span>
            {if !empty($aCategory)}
            <span class="directory-catgory">{_p var='category'}: <a href="{permalink module='directory.category' id=$aCategory.category_id title=$aCategory.title}">{softPhrase var=$aCategory.title}</a></span>
            {/if}
        </div>
        <div class="directory-content item_content">{$aItem.description_parsed|stripbb|feed_strip|split:55|max_line}</div>
    </div>
</div>