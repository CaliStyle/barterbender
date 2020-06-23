<?php
/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');
?>

<ul class="action">
    {foreach from=$article_block item=article}
    <li >
        <a href="{url link='gettingstarted.article'}article_{$article.article_id}">{$article.title}</a>
    </li>
    {/foreach}
</ul>
