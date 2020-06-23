<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<ul class="action">
    {foreach from=$article_block item=article}
    <li >
        <a href="{url link='gettingstarted.article'}article_{$article.article_id}">{$article.title}</a>
    </li>
    {/foreach}
</ul>
