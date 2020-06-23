<div id="yndirectory_comparebusiness">
	<div class="yndirectory-hiddenblock">
		<input type="hidden" value="comparebusiness" id="yndirectory_pagename" name="yndirectory_pagename">
	</div>
	<div class="yndirectory-comparebusiness-title">
		{phrase var='compare'}
	</div>

	<div class="yndirectory-comparebusiness-choose">
		<div class="yndirectory-comparebusiness-choose-content">
			<select class="form-control" id="yndirectory_comparebusiness_detail_category" data-comparelink="{$sCompareLink}">
        		{foreach from=$aCategory key=Id item=aCategoryItem}
                    {if Phpfox::isPhrase($this->_aVars['aCategoryItem']['data']['title'])}
                        <?php $this->_aVars['value_name'] = _p($this->_aVars['aCategoryItem']['data']['title']) ?>
                    {else}
                        {assign var='value_name' value=$aCategoryItem.data.title|convert}
                    {/if}
        			<option 
        				id="yndirectory_comparebusiness_detail_option_{$aCategoryItem.data.category_id}" 
        				data-comparedetailtotalitem="{$aCategoryItem.total_business}"
        				{if $category_id == $aCategoryItem.data.category_id}
        					selected="selected"
        				{/if} 
        				value="{$aCategoryItem.data.category_id}">{$value_name} ({$aCategoryItem.total_business})</option>
    	    	{/foreach}
			</select>			
		</div>		
	</div>

	<div class="yndirectory-compare-content">
		<div class="yndirectory-compare-header">
			<div>&nbsp;{*image*}</div>
			{if $aFieldStatus.ratings}
				<div>{phrase var='ratings'}</div>
			{/if}
			{if $aFieldStatus.members}
				<div>{phrase var='members_up'}</div>
			{/if}
			{if $aFieldStatus.follower}
				<div>{phrase var='followers_up'}</div>
			{/if}
			{if $aFieldStatus.reviews}
				<div>{phrase var='reviews'}</div>
			{/if}
			<div class="yndirectory-compare-item-row-contact">{phrase var='contact_detail'}</div>
			{if $aFieldStatus.address}
				<div>{phrase var='address'}</div>
			{/if}
			{if $aFieldStatus.operating_hours}
				<div>{phrase var='operating_hours'}</div>
			{/if}
			{if $aFieldStatus.custom_field}
				{foreach from=$aCustomFields key=id item=aCustomFieldItem}
					<div>{phrase var=$aCustomFieldItem.phrase_var_name}</div>
				{/foreach}
			{/if}
			{if $aFieldStatus.short_description}
				<div>{phrase var='short_description'}</div>
			{/if}
		</div>

		<div class="yndirectory-compare-list-content">
		<ul class="yndirectory-compare-list">
		{foreach from=$aBusinessCompare key=id item=aBusiness}
			<li id="yndirectory_compare_page_item_{$aBusiness.business_id}">
				<!-- image -->
				<div class="yndirectory-compare-item-top-content">
					<div class="yndirectory-compare-item-image">
				        <a href="{permalink module='directory.detail' id=$aBusiness.business_id title=$aBusiness.name}" title="{$aBusiness.name|clean}">
				            <span class="yndirectory-photo-span">
                                {if isset($aBusiness.logo_path)}
                                    {img server_id=$aBusiness.server_id path='core.url_pic' file=$aBusiness.logo_path suffix='_400'}
                                {else}
                                    <img title="{$aBusiness.name}" src="{$aBusiness.default_logo_path}"/>
                                {/if}
                            </span>
				        </a>					
					</div>
					<div class="yndirectory-compare-item-title">
						<a href="{permalink module='directory.detail' id=$aBusiness.business_id title=$aBusiness.name}" id="js_business_edit_inner_title{$aBusiness.business_id}" class="link ajax_link">{$aBusiness.name|clean|shorten:25:'...'|split:10}</a>
					</div>
					<div class="yndirectory-compare-item-close" onclick="yndirectory.removeItemOutCompareDashboardOnComparePage({$aBusiness.business_id});"><i class="fa fa-times"></i></div>
					<div style="display: none;">
	                    <input type="checkbox" 
	                        data-compareitembusinessid="{$aBusiness.business_id}"
	                        data-compareitemname="{$aBusiness.name}"
	                        data-compareitemlink="{permalink module='directory.detail' id=$aBusiness.business_id title=$aBusiness.name}"
	                        data-compareitemlogopath="{if isset($aBusiness.logo_path)}{img server_id=$aBusiness.server_id path='core.url_pic' file=$aBusiness.logo_path suffix='_100' return_url=true}{else}
                                    {img server_id=$aBusiness.server_id path='' file=$aBusiness.default_logo_path suffix='' return_url=true}{/if}"
	                        onclick="yndirectory.clickCompareCheckbox(this);" 
	                        class="yndirectory-compare-checkbox"> {phrase var='add_to_compare'}
					</div>
				</div>
				
				<!-- ratings -->
				{if $aFieldStatus.ratings}
				<div>
						<div>
                            {$aBusiness.total_score_text}  
                        </div>
				</div>
				{/if}

				<!-- members -->
				{if $aFieldStatus.members}
				<div><span class="yndirectory-compare-item-stats">{$aBusiness.members}</span> {phrase var='member_s'}</div>
				{/if}

				<!-- followers -->
				{if $aFieldStatus.follower}
				<div><span class="yndirectory-compare-item-stats">{$aBusiness.total_follow}</span> {phrase var='follower_s'}</div>
				{/if}

				<!-- reviews -->
				{if $aFieldStatus.reviews}
				<div><span class="yndirectory-compare-item-stats">{$aBusiness.total_reviews}</span> {phrase var='review_s'}</div>
				{/if}

				<!-- contact_detail -->
				<div class="yndirectory-compare-item-row-contact">
					{if $aFieldStatus.phone}
					<div>
		                <a href="#" onclick="tb_show('{phrase var='phone'}',$.ajaxBox('directory.getExtraInfo', 'iBusinessId={$aBusiness.business_id}&sType=phone&height=300&width=330')); return false;">
		                    <i class="fa fa-phone"></i>
		                    <span>{phrase var='phone_number'}</span>
		                </a>
					</div>
					{/if}
					<div>
		                <a href="mailto:{$aBusiness.email}">
		                    <i class="fa fa-envelope"></i>
		                    <span>{phrase var='email'}</span>
		                </a>
					</div>
					{if $aFieldStatus.website}
					<div>
		                <a href="#" onclick="tb_show('{phrase var='website'}',$.ajaxBox('directory.getExtraInfo', 'iBusinessId={$aBusiness.business_id}&sType=website&height=300&width=330')); return false;">
		                    <i class="fa fa-globe"></i> 
		                    <span>{phrase var='website'}</span>
		                </a>						
					</div>
					{/if}
				</div>
				<!-- address -->
				{if $aFieldStatus.address}
				<div>{$aBusiness.location_title} - {$aBusiness.location_address}</div>
				{/if}
				<!-- operating_hours -->
				{if $aFieldStatus.operating_hours}
				<div>
					<ul>
					{foreach from=$aBusiness.list_visitinghour key=list_visitinghour_id item=list_visitinghour_item}
						<li>
							{$list_visitinghour_item.phrase} {$list_visitinghour_item.vistinghour_starttime} - {$list_visitinghour_item.vistinghour_endtime}
						</li>
					{/foreach}
					</ul>
				</div>
				{/if}
				<!-- custom field -->
				{if $aFieldStatus.custom_field}
				{foreach from=$aBusiness.list_customdata key=list_customdata_id item=list_customdata_item}
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
				{if $aFieldStatus.short_description}
				<div class="wrap-longtext">{$aBusiness.short_description|parse}</div>
				{/if}
			</li>
		{/foreach}
		</ul>
		</div>

	</div>
</div>

{literal}
<script type="text/javascript">
    $Behavior.yndirectory_compareitem_more_script = function() {
    	
   		$('.yndirectory-compare-list').css('width', 200*$('.yndirectory-compare-list > li').length );	
    	$('.yndirectory-compare-header > div').each(function()
    	{
    		var div_index = $(this).index();
    		var	max_height = $(this).outerHeight();
    		
    		$('.yndirectory-compare-list > li').each(function(){
    			if ( max_height < $(this).children('div').eq(div_index).outerHeight() ) {
    				max_height = $(this).children('div').eq(div_index).outerHeight();
    			}    			
    		});

    		$(this).css('height', max_height);
    		$('.yndirectory-compare-list > li').each(function(){
    			$(this).children('div').eq(div_index).css('height', max_height);

    		});
    	});
	    
    };        
</script>
{/literal}



