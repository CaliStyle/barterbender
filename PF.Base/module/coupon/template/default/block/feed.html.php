<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="coupon-feed {if !empty($aItem.image_path)}has-image{/if}">

    <div class="coupon-feed-image">
        <a href="{$sLink}">
             <span style="background-image:url({if $aItem.image_path}{img return_url=true server_id=$aItem.server_id path='core.url_pic' file=$aItem.image_path }{else}{$sDefaultLink}{/if})"></span>
        </a>
    </div>
   
    <div class="coupon-feed-info">
        <div class="coupon-title"><a href="{$sLink}">{$aItem.title}</a></div>
        <div class="coupon-content item_content">{$aItem.description|stripbb|feed_strip|split:55|max_line}</div>
    </div>
</div>