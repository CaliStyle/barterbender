<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>
<div class="sub_section_menu">
	<ul class="action">
	{foreach from=$aCategories item=aCategory}
		<li class="category {if $iCategoryChannelView == $aCategory.category_id} active{/if}">
			<a href="{$aCategory.url}" class="ajax_link">
                {if Core\Lib::phrase()->isPhrase($this->_aVars['aCategory']['name'])}
                    {phrase var=$aCategory.name}
                {else}
                    {$aCategory.name|convert|clean}
                {/if}
            </a>
            {if isset($aCategory.sub) && count($aCategory.sub)}
                {if Phpfox::getParam('videochannel.subcategories_to_show_at_first') == 0}
                    <span onclick="$Core.toggleCategory('{if isset($sModule)}{$sModule}_{/if}subcategory_{$aCategory.category_id}',{$aCategory.category_id})">
                        <div id="show_more_{$aCategory.category_id}" style="padding-right:2%;text-align:right;vertical-align:middle;"><a href="#" onclick="return false;">{img theme='misc/plus.gif' class='v_middle'}</a></div>
                        <div id="show_less_{$aCategory.category_id}" style="padding-right:2%;display:none;text-align:right;vertical-align:middle;"><a href="#" onclick="return false;">{img theme='misc/minus.gif' class='v_middle'}</a></div>
                    </span>
                {/if}
            	<ul>
                  {foreach from=$aCategory.sub item=aSubCategory key=iKey}                    
                    <li class="{if $iCategoryChannelView == $aSubCategory.category_id} active{/if} {if $iKey >= $iLimitShowMore}{if isset($sModule)}{$sModule}_{/if}subcategory_{$aCategory.category_id}{/if} special_subcategory" {if $iKey >= Phpfox::getParam('videochannel.subcategories_to_show_at_first')}style="display:none;"{/if}>
                      <a href="{$aSubCategory.url}" id="{if isset($sModule)}{$sModule}_{/if}subcategory_{$aSubCategory.category_id}" class="ajax_link">
                          {if Core\Lib::phrase()->isPhrase($this->_aVars['aSubCategory']['name'])}
                            {phrase var=$aSubCategory.name}
                          {else}
                            {$aSubCategory.name|convert|clean}
                          {/if}
                      </a>
                    </li>
                  {/foreach}
      
                 {if $iKey >= Phpfox::getParam('videochannel.subcategories_to_show_at_first') && Phpfox::getParam('videochannel.subcategories_to_show_at_first') > 0}
                    <li onclick="$Core.toggleCategory('{if isset($sModule)}{$sModule}_{/if}subcategory_{$aCategory.category_id}',{$aCategory.category_id})">
                      <div id="show_more_{$aCategory.category_id}" style="padding-right:2%;text-align:right;vertical-align:middle;"><a href="#" onclick="return false;"><i class="ico ico-plus mr-1"></i>{phrase var='user.view_more'}</a></div>
                      <div id="show_less_{$aCategory.category_id}" style="padding-right:2%;display:none;text-align:right;vertical-align:middle;"><a href="#" onclick="return false;"><i class="ico ico-minus mr-1"></i>{phrase var='core.view_less'}</a></div>
                    </li>
                  {/if}
            	</ul>
            {/if}
        </li>
	{/foreach}
	</ul>
</div>
