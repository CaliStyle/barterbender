<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
?>
<!-- Feed Content Block Space -->
<div class="ynnews_block_news">
	<div class="foxfeedspro_block new-item moderation_row" id ="js_new_entry{$aNews.item_id}">
        <div class="yns feed_option table_row">
            <label class="item-checkbox">
                <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aNews.item_id}" id="check{$aNews.item_id}" />
                <i class="ico ico-square-o"></i>
            </label>

            {if $aNews.is_approved != 1}
            <div class="row_edit_bar_parent">
                <div class="row_edit_bar">
                    <a role="button" class="row_edit_bar_action" data-toggle="dropdown">
                        <i class="fa fa-action"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{url link= 'foxfeedspro.addnews.item_'.$aNews.item_id}">{phrase var='foxfeedspro.edit'}<a/>
                        </li>
                    </ul>
                </div>
                <div class="row_edit_bar">
                    <a href="#" class="row_edit_bar_action" data-toggle="dropdown"><span>{phrase var='foxfeedspro.actions'}</span></a>
                </div>
            </div>
            {/if}
        </div>
		<div class="row_title">	
			<!-- New Title -->
			<div class="yns new_title">
				<a href="{permalink module='foxfeedspro.newsdetails' id ='item_'.$aNews.item_id title=$aNews.item_title}" 
				title="{$aNews.item_title}">{$aNews.item_title}</a>
			</div>
			
			<div class="yns new_provider">
				<span>{phrase var='foxfeedspro.rss_provider_name'}</span>
				<span class="value">{$aNews.feed_name|shorten:35:'...'}</span>
			</div>
			
			<div class="yns new_posted-date">
				<span>{phrase var='foxfeedspro.headline_posted_date'}</span>
				<span class="value">
					{if $aNews.item_pubDate_parse}
		 		 		{$aNews.item_pubDate_parse}
		 		 	{else}
		 		 		<?php echo date("D, d M Y h:i:s e", $this->_aVars['aNews']['added_time']);?>
		 		 	{/if}
				</span>
			</div>
			
			<div class="yns new_status">
				<span>{phrase var='foxfeedspro.headline_status'}</span>
				<span class="value">
					{ if $aNews.is_approved == 1}
						{if $aNews.is_active }
							{phrase var='foxfeedspro.active'}
						{else}
							{phrase var='foxfeedspro.inactive'}
						{/if}
					{elseif $aNews.is_approved == 2}
						{phrase var='foxfeedspro.declined'}
					{else}
						{phrase var='foxfeedspro.pending'}
					{/if}
				</span>
			</div>
	    </div>
	</div>
</div>