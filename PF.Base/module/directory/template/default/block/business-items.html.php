<?php
	defined('PHPFOX') or exit('NO DICE!');
?>
 
<article class="ync-item yndirectory-sideblock-item-mini">
	<div class="item-outer">
        <a href="{permalink module='directory.detail' id=$aBusiness.business_id title=$aBusiness.name}" title="{$aBusiness.name|clean}" class="item-media-src">
        	<span style="background-image: url(
        	{if $aBusiness.logo_path}
        	    {img server_id=$aBusiness.server_id path='core.url_pic' file=$aBusiness.logo_path suffix='_400' width=80 height=80 return_url = true}
        	{else}
        	    {$aBusiness.default_logo_path}
        	{/if}
        	)"></span>
        </a>

	    <div class="item-inner">
	    	<div class="item-title mb-h1" id="js_business_edit_title{$aBusiness.business_id}">
	    		<a href="{permalink module='directory.detail' id=$aBusiness.business_id title=$aBusiness.name}" id="js_business_edit_inner_title{$aBusiness.business_id}" class="link ajax_link">{$aBusiness.name|clean|shorten:25:'...'|split:10}</a>
	    	</div>
	    	<div class="item-category mb-h1 fz-12">
	    		{if Phpfox::isPhrase($this->_aVars['aBusiness']['category_title'])}
	                <?php $this->_aVars['value_name'] = _p($this->_aVars['aBusiness']['category_title']) ?>
	            {else}
	                {assign var='value_name' value=$aBusiness.category_title|convert}
	            {/if}
	    		<a href="{permalink module='directory.category' id=$aBusiness.category_id title=$value_name}">{$value_name}</a>
	    	</div>
	    	<div class="item-statistic">
				{if $aBusiness.block_stype=="most-rated" || $aBusiness.block_stype=="most-reviewed"}
					{$aBusiness.total_score_text}
				{else}
	                <div class="item-total-statistic text-gray fz-12 text-transition">{$aBusiness.statistics}</div>
				{/if}
			</div>
	    </div>
	</div>
</article>