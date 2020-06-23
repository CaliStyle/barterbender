<?php
/**
 * Created by PhpStorm.
 * User: thanhnc
 * Date: 10/27/16
 * Time: 4:07 PM
 */
?>
{if !PHPFOX_IS_AJAX}
<div class="header_bar_search">
    <form action="{$link}" method="GET">
        <div class="hidden">
            <input type="hidden" name="s" value="1">
            <input type="hidden" name="type" value="{$aForms.sType}">
            <input type="hidden" name="sort" value="{$aForms.sSort}">
        </div>
        <?php if (isset($this->_aVars['aSearchTool']['search']['actual_value'])) $this->_aVars['sSearch'] = Phpfox_Parse_Output::instance()->clean($this->_aVars['aSearchTool']['search']['actual_value']); else $this->_aVars['aSearch'] = ''; ?>
        <div class="header_bar_search_holder">
            <div class="header_bar_search_inner">
                <div class="input-group">
                    <input type="text" placeholder="{_p('Search...')}" class="form-control" name="search[search]" id="name"
                           value="{if isset($sSearch)}{$sSearch}{/if}">
                    <a class="form-control-feedback" onclick="$(this).closest('form').submit()">
                        <i class="ico ico-search-o"></i>
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="header-filter-holder hidden-xs pull-left">
    <div class="filter-options">
        <a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
            <span>{_p var='Sort'}: {_p var='ynsocialstore.'.$sActivePhrase}</span>
            <span class="ico ico-caret-down"></span>
        </a>
        <ul class="dropdown-menu  dropdown-menu-limit">
            <li>
                <a href="{$link}?rewrite=2&amp;s=1&amp;type={$aForms.sType}&amp;sort=latest&amp;search%5Bsearch%5D={if isset($sSearch)}{$sSearch}{/if}"
                   class="ajax_link ">
                    {_p var='ynsocialstore.latest'} </a>
            </li>
            <li>
                <a href="{$link}?rewrite=2&amp;s=1&amp;type={$aForms.sType}&amp;sort=a-z&amp;search%5Bsearch%5D={if isset($sSearch)}{$sSearch}{/if}"
                   class="ajax_link ">
                    {_p var='ynsocialstore.a_z'} </a>
            </li>
            <li>
                <a href="{$link}?rewrite=2&amp;s=1&amp;type={$aForms.sType}&amp;sort=z-a&amp;search%5Bsearch%5D={if isset($sSearch)}{$sSearch}{/if}"
                   class="ajax_link ">
                    {_p var='ynsocialstore.z_a'} </a>
            </li>
            <li>
                <a href="{$link}?rewrite=2&amp;s=1&amp;type={$aForms.sType}&amp;sort=most-liked&amp;search%5Bsearch%5D={if isset($sSearch)}{$sSearch}{/if}"
                   class="ajax_link ">
                    {_p var='ynsocialstore.most_liked'} </a>
            </li>
            <li>
                <a href="{$link}?rewrite=2&amp;s=1&amp;type={$aForms.sType}&amp;sort=most-viewed&amp;search%5Bsearch%5D={if isset($sSearch)}{$sSearch}{/if}"
                   class="ajax_link ">
                    {_p var='ynsocialstore.most_viewed'} </a>
            </li>
            <li>
                <a href="{$link}?rewrite=2&amp;s=1&amp;type={$aForms.sType}&amp;sort=most-purchased&amp;search%5Bsearch%5D={if isset($sSearch)}{$sSearch}{/if}"
                   class="ajax_link ">
                    {_p var='ynsocialstore.most_purchased'} </a>
            </li>
            <li>
                <a href="{$link}?rewrite=2&amp;s=1&amp;type={$aForms.sType}&amp;sort=super-deal&amp;search%5Bsearch%5D={if isset($sSearch)}{$sSearch}{/if}"
                   class="ajax_link ">
                    {_p var='ynsocialstore.super_deal'} </a>
            </li>
            <li>
                <a href="{$link}?rewrite=2&amp;s=1&amp;type={$aForms.sType}&amp;sort=price-increase&amp;search%5Bsearch%5D={if isset($sSearch)}{$sSearch}{/if}"
                   class="ajax_link ">
                    {_p var='ynsocialstore.price_increase'} </a>
            </li>
            <li>
                <a href="{$link}?rewrite=2&amp;s=1&amp;type={$aForms.sType}&amp;sort=price-decrease&amp;search%5Bsearch%5D={if isset($sSearch)}{$sSearch}{/if}"
                   class="ajax_link ">
                    {_p var='ynsocialstore.price_decrease'} </a>
            </li>
        </ul>
    </div>

    <div class="filter-options">
        <a class="dropdown-toggle" data-toggle="dropdown">
            <span>{_p var='Type'}: {_p var='ynsocialstore.'.$aForms.sType.'_products'}</span>
            <span class="ico ico-caret-down"></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-left dropdown-menu-limit">
            <li>
                <a href="{$link}?rewrite=2&amp;s=1&amp;type=all&amp;sort={$aForms.sSort}&amp;search%5Bsearch%5D={if isset($sSearch)}{$sSearch}{/if}"
                   class="ajax_link " rel="nofollow">
                    {_p('all_products')} </a>
            </li>
            <li>
                <a href="{$link}?rewrite=2&amp;s=1&amp;type=physical&amp;sort={$aForms.sSort}&amp;search%5Bsearch%5D={if isset($sSearch)}{$sSearch}{/if}"
                   class="ajax_link " rel="nofollow">
                    {_p('physical_products')} </a>
            </li>
            <li>
                <a href="{$link}?rewrite=2&amp;s=1&amp;type=digital&amp;sort={$aForms.sSort}&amp;search%5Bsearch%5D={if isset($sSearch)}{$sSearch}{/if}"
                   class="ajax_link " rel="nofollow">
                    {_p('digital_products')} </a>
            </li>
        </ul>
    </div>

</div>
{/if}
{if count($aItems)}
    {if !PHPFOX_IS_AJAX}
    <div class="ynstore-view-modes-block yn-viewmode-grid" id="js_block_border_ynsocialstore_store_detail_listingproduct">
        <div class="yn-view-modes yn-nomargin">
            <span data-mode="grid" class="yn-view-mode"><i class="ico ico-th"></i></span>
            <span data-mode="list" class="yn-view-mode"><i class="ico ico-list"></i></span>
        </div>
        <ul class="ynstore-items">
            {/if}
            {foreach from=$aItems name=product item=aItem}
                {template file='ynsocialstore.block.product.entry'}
            {/foreach}
            {pager}
            {moderation}
            {if !PHPFOX_IS_AJAX}
        </ul>
    </div>
    {/if}
    {literal}
    <script type="text/javascript">
        $Behavior.initViewMode = function(){
            ynsocialstore.initViewMode('js_block_border_ynsocialstore_store_detail_listingproduct');
        }
    </script>
    {/literal}
    {elseif !PHPFOX_IS_AJAX}
    <div class="extra_info">
        {_p var='ynsocialstore.no_products_found'}
    </div>
{/if}
