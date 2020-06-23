<div id="ynauction_compareauction">
	<div class="ynauction-hiddenblock">
		<input type="hidden" value="compareauction" id="ynauction_pagename" name="ynauction_pagename">
	</div>
	<div class="ynauction-compareauction-title">
		{phrase var='compare'}
	</div>

	<div class="ynauction-compareauction-choose">
		<div class="ynauction-compareauction-choose-content">
			<select id="ynauction_compareauction_detail_category" data-comparelink="{$sCompareLink}">
        		{foreach from=$aCategory key=Id item=aCategoryItem}
        			<option 
        				id="ynauction_compareauction_detail_option_{$aCategoryItem.data.category_id}" 
        				data-comparedetailtotalitem="{$aCategoryItem.total_auction}"
        				{if $category_id == $aCategoryItem.data.category_id}
        					selected="selected"
        				{/if} 
        				value="{$aCategoryItem.data.category_id}">{$aCategoryItem.data.title} ({$aCategoryItem.total_auction})</option>
    	    	{/foreach}
			</select>			
		</div>		
	</div>

	<div class="ynauction-compare-content clearfix">
		<div class="ynauction-compare-header">
			<div style="height:206px">&nbsp;{*image*}</div>
			{if $aFieldStatus.reserve_price}
				<div>{phrase var='reserve_price'}</div>
			{/if}
			{if $aFieldStatus.total_bids}
				<div>{phrase var='number_of_bids'}</div>
			{/if}
			{if $aFieldStatus.total_orders}
				<div>{phrase var='number_of_orders'}</div>
			{/if}
			{if $aFieldStatus.total_view}
				<div>{phrase var='number_of_views'}</div>
			{/if}
			{if $aFieldStatus.seller}
				<div>{phrase var='seller'}</div>
			{/if}
			{if $aFieldStatus.custom_field}
				{foreach from=$aCustomFields key=id item=aCustomFieldItem}
					<div>{phrase var=$aCustomFieldItem.phrase_var_name}</div>
				{/foreach}
			{/if}
			{if $aFieldStatus.description}
				<div>{phrase var='description'}</div>
			{/if}
		</div>

		<div class="ynauction-compare-list-content">
		<ul class="ynauction-compare-list">
		{foreach from=$aAuctionCompare key=id item=aAuction}
			<li id="ynauction_compare_page_item_{$aAuction.product_id}">
				<!-- image -->
				<div class="ynauction-compare-item-top-content">
					<div class="ynauction-compare-item-image">
				        <a href="{permalink module='auction.detail' id=$aAuction.product_id title=$aAuction.name}" title="{$aAuction.name|clean}">
				            <span class="ynauction-photo-span" style="background-image: url(
				            {if isset($aAuction.logo_path)}
                                {img server_id=$aAuction.server_id path='core.url_pic' file=$aAuction.logo_path suffix='_400' return_url=true}
                            {else}
                                {$aAuction.default_logo_path}
                            {/if}
				            );"></span>
				        </a>					
					</div>
					<div class="ynauction-compare-item-title">
						<a href="{permalink module='auction.detail' id=$aAuction.product_id title=$aAuction.name}" id="js_auction_edit_inner_title{$aAuction.product_id}" class="link ajax_link">{$aAuction.name|clean|shorten:25:'...'|split:25}</a>
					</div>
					<div class="ynauction-compare-item-close" onclick="ynauction.removeItemOutCompareDashboardOnComparePage({$aAuction.product_id});"><i class="fa fa-times"></i></div>
					<div style="display: none;">
	                    <input type="checkbox" 
	                        data-compareitemauctionid="{$aAuction.product_id}"
	                        data-compareitemname="{$aAuction.name}"
	                        data-compareitemlink="{permalink module='auction.detail' id=$aAuction.product_id title=$aAuction.name}"
	                        data-compareitemlogopath="{if isset($aAuction.logo_path)}
                                {img server_id=$aAuction.server_id path='core.url_pic' file=$aAuction.logo_path suffix='_400' return_url=true}
                            {else}
                                {$aAuction.default_logo_path}
                            {/if}"
	                        onclick="ynauction.clickCompareCheckbox(this);" 
	                        class="ynauction-compare-checkbox"> {phrase var='add_to_compare'}						
					</div>
				</div>
			
				{if $aFieldStatus.reserve_price}
				<div><span class="ynauction-compare-item-stats">{$aAuction.sSymbolCurrency}{$aAuction.auction_item_reserve_price}</span></div>
				{/if}

				{if $aFieldStatus.total_bids}
				<div><span class="ynauction-compare-item-stats">{$aAuction.auction_total_bid}</span></div>
				{/if}

				{if $aFieldStatus.total_orders}
				<div><span class="ynauction-compare-item-stats">{$aAuction.total_orders}</span></div>
				{/if}

				{if $aFieldStatus.total_view}
				<div><span class="ynauction-compare-item-stats">{$aAuction.total_view}</span></div>
				{/if}

				{if $aFieldStatus.seller}
				<div><span class="ynauction-compare-item-stats">{$aAuction|user}</span> </div>
				{/if}

				
				<!-- custom field -->
				{if $aFieldStatus.custom_field}
				{foreach from=$aAuction.list_customdata key=list_customdata_id item=list_customdata_item}
					<div>
						{if $list_customdata_item.var_type=='text'}
							{$list_customdata_item.value}&nbsp;
						{elseif $list_customdata_item.var_type=='textarea'}
							{$list_customdata_item.value}&nbsp;
						{elseif $list_customdata_item.var_type=='select'}
							{foreach from=$list_customdata_item.value key=value_id item=value_item}
								{phrase var=$value_item} <br/>
							{/foreach}
						{elseif $list_customdata_item.var_type=='multiselect'}
							{foreach from=$list_customdata_item.value key=value_id item=value_item}
								{phrase var=$value_item} <br/>
							{/foreach}
						{elseif $list_customdata_item.var_type=='checkbox'}
							{foreach from=$list_customdata_item.value key=value_id item=value_item}
								{phrase var=$value_item} <br/>
							{/foreach}
						{elseif $list_customdata_item.var_type=='radio'}
							{foreach from=$list_customdata_item.value key=value_id item=value_item}
								{phrase var=$value_item} <br/>
							{/foreach}
						{/if}
					</div>
				{/foreach}				
				{/if}
				<!-- short_description -->
				{if $aFieldStatus.description}
					<div class="item_view_content">{$aAuction.description}</div>
				{/if}
			</li>
		{/foreach}
		</ul>
		</div>

	</div>
</div>

{literal}
<script type="text/javascript">
    ;$Behavior.ynauction_compareitem_more_script = function() {
    	
	$('.ynauction-compare-list').css('width', 200*$('.ynauction-compare-list > li').length );	

    	$('.ynauction-compare-header > div').each(function(){
	    		var div_index = $(this).index();
	    		var	max_height = $(this).height();
	    		
	    		$('.ynauction-compare-list > li').each(function(){
	    			if ( max_height < $(this).children('div').eq(div_index).height() ) {
	    				max_height = $(this).children('div').eq(div_index).height();
	    			}    			
	    		});

	    		$(this).css('height', max_height);
	    		$('.ynauction-compare-list > li').each(function(){
	    			$(this).children('div').eq(div_index).css('height', max_height);
	    		});
	    	});
    };        
</script>
{/literal}


