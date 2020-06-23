<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 05/01/2017
 * Time: 17:21
 */
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="p-advblog-tag-container">        
    {foreach from=$aHotTags item=aHotTag}
	    <a href="{$aHotTag.tag_url}" class="p-advblog-tag-item">{$aHotTag.tag_text|clean|shorten:55:'...'|split:20}</a>
    {/foreach}
</div>