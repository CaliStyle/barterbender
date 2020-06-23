<?php
	defined('PHPFOX') or exit('NO DICE!');
?>

<div class="ynadvancedblog_user_tags">
	<ul class="clearfix">
	    {foreach from=$aTagAuthor item=aItem}
		    <li class="ynadvancedblog_user_tags_item">
			    <div class="ynadvancedblog_user_tags_item_inner">
			        <a href="{$aItem.tag_url}">{$aItem.tag_text|clean|shorten:55:'...'|split:20}</a>
		        </div>
		    </li>
	    {/foreach}
    </ul>
</div>