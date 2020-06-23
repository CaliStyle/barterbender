<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 11/01/2017
 * Time: 16:07
 */
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="item_image{if empty($aBlog.text)} full{/if} pull-left"
     style="background-image: url(
         {if $aBlog.image_path}
            {img server_id=$aBlog.server_id path='core.url_pic' file='ynadvancedblog/'.$aBlog.image_path suffix='_grid' return_url=true}
         {else}
            {$appPath}/assets/image/blog_photo_default.png
         {/if}
     )">
</div>
{if !empty($aBlog.text)}
<div class="extra_info">
    {$aBlog.text|striptag|stripbb|highlight:'search'|split:500|shorten:200:'...'}
</div>
{/if}
