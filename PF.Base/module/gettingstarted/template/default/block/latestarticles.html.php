<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
  <a href="{url link='gettingstarted.article'}article_{$article.article_id}">{$article.title}</a>
 */

?>

<ul class="action">
    {foreach from=$article_block item=article}
    <li >
        <a href="{url link='gettingstarted.article'}article_{$article.article_id}">{$article.title|clean}</a>
    </li>
    {/foreach}
</ul>

