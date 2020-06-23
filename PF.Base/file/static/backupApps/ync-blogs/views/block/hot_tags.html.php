<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 05/01/2017
 * Time: 17:21
 */
defined('PHPFOX') or exit('NO DICE!');
?>

<div class="ynadvblog_hot_tags">
	<ul class="clearfix">
	    {foreach from=$aHotTags item=aHotTag}
		    <li>
		        <a href="{$aHotTag.tag_url}">{$aHotTag.tag_text|clean|shorten:55:'...'|split:20}</a>
		    </li>
	    {/foreach}
    </ul>
</div>
