<?php

defined('PHPFOX') or exit('NO DICE!');

 ?>
<div class="business-item">
	<div>
	        <strong><a href="{permalink module='business.detail' id=$aBusiness.business_id title=$aBusiness.name}" id="js_business_edit_inner_title{$aBusiness.business_id}" class="link ajax_link">{$aBusiness.name|clean|shorten:75:'...'|split:10}</a></strong>
	        <p>{$aBusiness.location_title}</p>
	        <p>{if Phpfox::isPhrase($this->_aVars['aBusiness']['category_title'])}
                {phrase var=$aBusiness.category_title}
                {else}
                {$aBusiness.category_title|convert|clean|shorten:25:'...'}
                {/if}</p>
	        <p>{phrase var='more_info'}</p>
	</div>
	<div >
		<div>
	        <a href="{permalink module='business.detail' id=$aBusiness.business_id title=$aBusiness.name}" title="{$aBusiness.name|clean}">
	            {img yndirectory_overridenoimage=true  server_id=$aBusiness.server_id path='core.url_pic' file=$aBusiness.logo_path suffix='_400_square' width=80 height=80}
	        </a>
        </div>
        <div>
        	<div>
        			{if $aBusiness.total_reviews == 0}
        				{phrase var='no_reviews_be_the_first'}
        			{else}
						<div>{$aBusiness.total_rating}</div>
						<div>{$aBusiness.total_reviews}</div>
						{phrase var='write_a_review'}
        			{/if}
        	</div>
        	<div>{$aBusiness.short_description}</div>
        	<div class="yndirectory-extra-info">
        		<span>{$aBusiness.phone_number}</span>
        		<span>
					<a href="mailto:{$aBusiness.email}">{phrase var='email'}</a>
        		</span>
        		<span>
        			<a href="{$aBusiness.website_text}" target="_blank">{phrase var='website'}
        			</a>
        		</span>
        		<span>{phrase var='get_directions'}</span>
        		{if isset($aBusiness.featured) && $aBusiness.featured }
        			{phrase var='featured'}
        		{/if}
        	</div>
        </div>
    </div>
	<div >
		<input type="checkbox" id="compare"> {phrase var='add_to_compare'}
	</div>
<div class="clear"></div>
</div>