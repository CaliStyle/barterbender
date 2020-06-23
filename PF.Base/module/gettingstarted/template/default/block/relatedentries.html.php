<?php
/**
 * [PHPFOX_HEADER]
 */
defined('PHPFOX') or exit('NO DICE!');
?>

<section>
    <ul >
        {foreach from=$article_block item=article}
        <li>
            <a href="{url link='gettingstarted.article'}article_{$article.article_id}">{$article.title}</a> 
            <span> ({if $article.total_view == 0}1 {phrase var='gettingstarted.view'}{else} {$article.total_view}  {phrase var='gettingstarted.views'}{/if}) </span>
        </li>
        {/foreach}
    </ul>
</section>
