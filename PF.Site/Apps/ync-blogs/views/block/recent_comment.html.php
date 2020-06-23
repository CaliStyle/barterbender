<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 06/01/2017
 * Time: 18:40
 */
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="p-listing-container p-advblog-comment-container {if !$bIsSideLocation}col-3{/if}" data-mode-view="grid">
    {foreach from=$aItems item=aItem}
        {template file='ynblog.block.entry_comment_block'}
    {/foreach}
</div>
