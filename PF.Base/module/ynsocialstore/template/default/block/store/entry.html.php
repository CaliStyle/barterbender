<li class="ynstore-item moderation_row  {if Phpfox_Request::instance()->get('view') == 'friend'}ynstore-store-friend-item{/if}" data-store-id="{$aItem.store_id}" id="js_store_id_{$aItem.store_id}">
	<div class="ynstore-item-content ynstore-store-listing" >
		{if (Phpfox::isAdmin() || !empty($bShowModeration)) && empty($bIsNoModerate)}
            <div class="moderation_row">
                <label class="item-checkbox">
                    <input type="checkbox" class="js_global_item_moderate" name="item_moderate[]" value="{$aItem.store_id}" id="check{$aItem.store_id}" />
                    <i class="ico ico-square-o"></i>
                </label>
            </div>
		{/if}
		<div class="ynstore-bg"
			style="background-image: url(
				{if $aItem.logo_path}
			        {img server_id=$aItem.server_id path='core.url_pic' file='ynsocialstore/'.$aItem.logo_path suffix='_480_square' return_url='true'}
			    {else}
			    	{param var='core.path_actual'}PF.Base/module/ynsocialstore/static/image/store_default.png
			    {/if}
		    )">

		    <div class="ynstore-featured">
		    	<div  title="{_p var='ynsocialstore.featured'}" class="ynstore-featured-triangle ynstore_entry_feature_icon-{$aItem.store_id}" {if !$aItem.is_featured}style="visibility:hidden"{/if}>
		    		<i class="ico ico-diamond"></i>
		    	</div>
		    </div>

		    <a  href="{permalink module='ynsocialstore.store' id=$aItem.store_id title=$aItem.name}"></a>
		    <div title="{_p var='ynsocialstore.add_to_compare'}" class="ynstore-compare-btn {if isset($bIsNoCompare) && $bIsNoCompare}hide{/if}" onclick="ynsocialstore.addToCompare({$aItem.store_id},'store');return false;" data-comparestoreid="{$aItem.store_id}">
		    	<i class="ico ico-copy"></i>
		    </div>
			{if isset($aItem.user_id)}
			<div class="ynstore-actions-block">
				<div class="ynstore-cms">
					{template file='ynsocialstore.block.store.link' aItem=$aItem}
				</div>
			</div>
			{/if}
		</div>

		<div class="ynstore-info">
	    	<div class="ynstore-featured ynstore-4listmobile" style="display: none;">
		    	<div class="ynstore-featured-triangle ynstore_entry_feature_icon-{$aItem.store_id}" {if !$aItem.is_featured}style="visibility:hidden"{/if}>
		    		<i class="ico ico-diamond"></i>
		    	</div>
		    </div>

			<div class="ynstore-info-detail">
				<a href="{permalink module='ynsocialstore.store' id=$aItem.store_id title=$aItem.name}" class="ynstore-title">
					{$aItem.name|clean}
				</a>
				{if !empty($aItem.address)}
				<div title="{$aItem.address}" class="ynstore-address">
					<i class="ico ico-checkin"></i>
					{$aItem.address}
				</div>
				{/if}

				<div class="ynstore-categories {if $aItem.hiddencate > 0}ynstore-long{/if}">
					<div class="ynstore-categories-content">
						<i class="ico ico-folder-alt"></i>
						{if $aItem.hiddencate > 0}
                        {if Phpfox::isPhrase($this->_aVars['aItem']['categories'][0]['title'])}
                            <?php $this->_aVars['value_name'] = _p($this->_aVars['aItem']['categories'][0]['title']) ?>
                        {else}
                            {assign var='value_name' value=$aItem.categories.0.title|convert}
                        {/if}
						<a href="{permalink module='ynsocialstore.store.category' id=$aItem.categories.0.category_id title=$value_name}">{$value_name}</a>
						<div class="dropdown">
							{_p('and')}
							<a href="javascript:void(0)" data-toggle="dropdown">+{$aItem.hiddencate}</a>
							<ul class="dropdown-menu">
								{foreach from=$aItem.categories key=iKey item=aCategory}
								{if $iKey > 0}
                                    {if Phpfox::isPhrase($this->_aVars['aCategory']['title'])}
                                        <?php $this->_aVars['value_name'] = _p($this->_aVars['aCategory']['title']) ?>
                                    {else}
                                        {assign var='value_name' value=$aCategory.title|convert}
                                    {/if}
									<li><a href="{permalink module='ynsocialstore.store.category' id=$aCategory.category_id title=$value_name}">{$value_name}</a></li>
								{/if}
								{/foreach}
							</ul>
						</div>
						{else}
							{foreach from=$aItem.categories key=iKey item=aCategory}
                                {if Phpfox::isPhrase($this->_aVars['aCategory']['title'])}
                                    <?php $this->_aVars['value_name'] = _p($this->_aVars['aCategory']['title']) ?>
                                {else}
                                    {assign var='value_name' value=$aCategory.title|convert}
                                {/if}
								<a href="{permalink module='ynsocialstore.store.category' id=$aCategory.category_id title=$value_name}">{$value_name}</a>{if $iKey == 0 && count($aItem.categories) > 1}<i>,</i>{/if}
							{/foreach}
						{/if}
					</div>
				</div>

				{if Phpfox_Request::instance()->get('view') == 'friend'}
					<div class="ynstore-friend">
						{img user=$aItem suffix='_50_square'}
						<div class="ynstore-owner">
							{$aItem|user}
						</div>
					</div>
				{/if}
			</div>

			<div class="ynstore-description ynstore-4list" style="display: none;">
				{$aItem.short_description|clean}
			</div>

			{if Phpfox_Request::instance()->get('view') == 'friend'}
				<div class="ynstore-friend ynstore-friend-4mobile" style="display: none;">
					{img user=$aItem suffix='_50_square'}
					<div class="ynstore-owner">
						{$aItem|user}
					</div>
				</div>
			{/if}

			<div class="ynstore-statistic-block">
				<span class="ynstore-statistic ynstore-orders">
					{if $aItem.total_orders == 1}
						<b>{$aItem.total_orders}</b> <i>{_p('order')}</i>
					{else}
						<b>{$aItem.total_orders}</b> <i>{_p('orders')}</i>
					{/if}
				</span>
				<span class="ynstore-statistic ynstore-pipe"></span>
				<span class="ynstore-statistic ynstore-follows">
					{if $aItem.total_follow == 1}
						<b>{$aItem.total_follow}</b> <i>{_p('follower')}</i>
					{else}
						<b>{$aItem.total_follow}</b> <i>{_p('followers')}</i>
					{/if}
				</span>
			</div>

			<div class="ynstore-rating-compare-block ynstore-4list" style="display: none">
				<div class="ynstore-rating-block">
					<span class="ynstore-rating-number">{$aItem.rating}</span>
					<span class="ynstore-rating yn-rating-small">
					{for $i = 0; $i < 5; $i++}
						{if $i < (int)$aItem.rating}
							<i class="ico ico-star" aria-hidden="true"></i>
						{elseif (($aItem.rating - round($aItem.rating)) > 0) && ($aItem.rating - $i) > 0}
							<i class="ico ico-star-half-o" aria-hidden="true"></i>
						{else}
							<i class="ico ico-star yn-rating-disable" aria-hidden="true"></i>
						{/if}
					{/for}
					</span>

                    {if isset($aItem.total_review)}
					<a href="{permalink module='ynsocialstore.store' id=$aItem.store_id title=$aItem.name}reviews" class="ynstore-review-count">
						{$aItem.total_review}&nbsp;{if $aItem.total_review == 1}{_p var='ynsocialstore.review'}{else}{_p var='ynsocialstore.reviews'}{/if}
					</a>
                    {/if}
				</div>

				<div class="ynstore-compare-btn {if isset($bIsNoCompare) && $bIsNoCompare}hide{/if}" onclick="ynsocialstore.addToCompare({$aItem.store_id},'store');return false;" data-comparestoreid="{$aItem.store_id}">
					<i class="ico ico-copy"></i>
				</div>
			</div>
		</div>

	    <div class="ynstore-featured ynstore-4list" style="display: none;">
	    	<div class="ynstore-featured-triangle ynstore_entry_feature_icon-{$aItem.store_id}" {if !$aItem.is_featured}style="visibility:hidden"{/if}>
	    		<i class="ico ico-diamond"></i>
	    	</div>
	    </div>
	</div>
</li>
