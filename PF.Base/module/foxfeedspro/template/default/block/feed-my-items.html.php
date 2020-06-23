<?php
/**
 * [PHPFOX_HEADER]
 */

defined('PHPFOX') or exit('NO DICE!');
?>
<!-- Feed Content Block Space -->
<div class="ynnews_block_news">
	<div class="foxfeedspro_block feed-item moderation_row" id ="js_feed_entry{$aFeed.feed_id}">
        <div class="yns feed_option table_row">
            <label class="item-checkbox">
                <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aFeed.feed_id}" id="check{$aFeed.feed_id}" />
                <i class="ico ico-square-o"></i>
            </label>

            <div class="row_edit_bar">
                <a href="#" role="button" class="row_edit_bar_action" data-toggle="dropdown">
                    <i class="fa fa-action"></i>
                    </a>
                <ul class="dropdown-menu dropdown-menu-right">
                {if $bCanEdit}
                    {if $aFeed.is_approved != 1}
                    <li>
                        <a href="{
                            if isset($sYnFfFrom) && ($sYnFfFrom == 'profile' || $sYnFfFrom == 'pages')}
                                {if $sYnFfFrom == 'profile'}
                                    {url link='profile.foxfeedspro.profileaddrssprovider.feed_'.$aFeed.feed_id}
                                {else}
                                    {$aParentModule.url}foxfeedspro/profileviewrss/go_profileaddrssprovider/feed_{$aFeed.feed_id}
                                {/if}
                            {else}
                                {url link='foxfeedspro.addfeed.feed_'.$aFeed.feed_id}
                            {/if}
                        " >{phrase var='foxfeedspro.edit'}</a>
                    </li>
                    {elseif $bCanGetFeedData }
                    {if isset($aParentModule) && $aParentModule.module_id == 'pages'}
                    <li id="feed_getdata_{$aFeed.feed_id}">
                            <a href="javascript:void(0);" onclick="foxfeedspro.getData({$aFeed.feed_id},{$bIsAdminPanel},'normal',{$aParentModule.item_id});">{phrase var='foxfeedspro.get_data'}</a>
                    </li>
                    {else}
                    <li id="feed_getdata_{$aFeed.feed_id}">
                            <a href="javascript:void(0);" onclick="foxfeedspro.getData({$aFeed.feed_id},{$bIsAdminPanel},'normal');">{phrase var='foxfeedspro.get_data'}</a>
                    </li>
                    {/if}
                    {/if}
                {/if}
                </ul>
            </div>
        </div>
		<div class="row_title">	
			<!-- Feed Logo -->
			<div class="yns feed_logo">
                {if $aFeed.feed_logo}
                    <a href="{permalink module='foxfeedspro.feeddetails' id='feed_'.$aFeed.feed_id}" style="background-image: url({$sFilePath}{$aFeed.feed_logo})"></a>
                {/if}
			</div>
			<!-- Feed Title -->
		
				<div class="yns feed_title">
					<a href="{permalink module='foxfeedspro.feeddetails' id='feed_'.$aFeed.feed_id}">{$aFeed.feed_name|shorten:100:'...'}</a>
				</div>
				
				<div class="yns feed_url">
					<span>{phrase var='foxfeedspro.headline_feed_url'}</span>
					<span class="value"><a href="{$aFeed.feed_url}" target="_blank" title="{$aFeed.feed_url}">{phrase var='foxfeedspro.rss_link'}</a></span>
				</div>
				
	            {if !empty($aFeed.category_name)}
				<div class="yns feed_category">
					<span>{phrase var='foxfeedspro.category'}</span>
					<span class="value">{$aFeed.category_name|shorten:20:'...'}</span>
				</div>
	            {/if}
				
				<div class="yns feed_last-update">
					<span>{phrase var='foxfeedspro.headline_last_update'}</span>
					<span class="value"><?php echo date('d F, Y',$this->_aVars["aFeed"]["time_update"]); ?></span>
				</div>
				
				<div class="yns feed_status">
					<span>{phrase var='foxfeedspro.headline_status'}</span>
					<span class="value">
						{if $aFeed.is_approved == 1}
							<span id="feed_update_status_{$aFeed.feed_id}">
								{if $aFeed.is_active eq 1}
									{phrase var='foxfeedspro.active'}
								{else}
									{phrase var='foxfeedspro.inactive'}
								{/if}
							</span>
						{elseif $aFeed.is_approved == 2}
							<span style="color:gray">{phrase var='foxfeedspro.declined'}</span>
						{else}
							<span style="color:gray">{phrase var='foxfeedspro.pending'}</span>	
						{/if}
					</span>
				</div>
	    </div>
	</div>
</div>