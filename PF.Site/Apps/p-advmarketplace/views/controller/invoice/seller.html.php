<?php
/**
 * [PHPFOX_HEADER]
 *
 * @copyright		[PHPFOX_COPYRIGHT]
 * @author  		Raymond_Benc
 * @package 		Phpfox
 * @version 		$Id: index.html.php 1594 2010-05-22 22:49:41Z Raymond_Benc $
 */

defined('PHPFOX') or exit('NO DICE!');

?>
<div class="p-advmarketplace-invoice-search-wrapper">
    <div class="p-advmarketplace-invoice-search-content">
        <form  method="GET" action="{url link='advancedmarketplace.invoice.seller'}">
            <div class="search-item-wrapper">
                <div class="search-item form-group">
                    <div class="item-page-search-general input-group">
                        <input class="form-control" placeholder="{_p var='advancedmarketplace_search_listing_by_name_dot'}" name="val[name]" value="{value type='input' id='name'}">
                        <span class="input-group-btn" aria-hidden="true">
                            <button class="btn " type="submit">
                                 <i class="ico ico-search-o"></i>
                            </button>
                        </span>
                    </div>
                </div>

                <div class="search-item form-group">
                    <div  class="item-date-inputgroup-wrapper">
                        <div class="item-title form-control">{_p var='from'}</div>
                        {select_date prefix='from_' id='_start' start_year='current_year' end_year='+1' field_separator='/' default_all=true field_order='MDY'}
                    </div>
                </div>
                <div class="search-item form-group">
                    <div  class="item-date-inputgroup-wrapper">
                        <div class="item-title form-control">{_p var='to'}</div>
                        {select_date prefix='to_' id='_start' start_year='current_year' end_year='+1' field_separator='/' default_all=true field_order='MDY'}
                    </div>
                </div>

                <div class="item-btn-search form-group">
                    <button class="btn btn-primary">{_p var='search'}</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- new html for invoice -->
{if !count($aInvoices)}
<div class="extra_info">
    {phrase var='advancedmarketplace.you_do_not_have_any_invoices'}
</div>
{else}
<div class="p-advmarketplace-invoice-container">
    <div class="p-advmarketplace-invoice-content-wrapper">
        <div class="p-advmarketplace-table-title">
            <div class="p-advmarketplace-table-row">
                <div class="item-id">
                    {_p var='id'}
                </div>
                <div class="item-title">
                    {_p var='listing'}
                </div>
                <div class="item-date">
                    {_p var='advancedmarketplace_purchase_date'}
                </div>
                <div class="item-seller">
                    {_p var='advancedmarketplace_purchaser'}
                </div>
                <div class="item-price">
                    {_p var='price'}
                </div>
                <div class="item-status">
                    {_p var='status'}
                </div>
            </div>
        </div>
        <div class="p-advmarketplace-table-content">
            {foreach from=$aInvoices item=aInvoice}
            <div class="p-advmarketplace-table-row">
                <div class="item-id">
                    <div class="item-text-title">
                        {_p var='id'}:
                    </div>
                    <div class="item-text-info">
                        {$aInvoice.invoice_id}
                    </div>
                </div>
                <div class="item-title">
                    <div class="item-text-info">
                        <a href="{$aInvoice.link}">
                            {$aInvoice.title}
                        </a>
                    </div>
                </div>
                <div class="item-date">
                    <div class="item-text-title">
                        {_p var='advancedmarketplace_purchase_date'}:
                    </div>
                    <div class="item-text-info">
                        {$aInvoice.time_stamp|convert_time}
                    </div>
                </div>
                <div class="item-seller">
                    <div class="item-text-title">
                        {_p var='advancedmarketplace_purchaser'}:
                    </div>
                    <div class="item-text-info">
                        {$aInvoice|user}
                    </div>
                </div>
                <div class="item-price">
                    <div class="item-text-title">
                        {_p var='price'}:
                    </div>
                    <div class="item-text-info">
                        {$aInvoice.listing_price}
                    </div>
                </div>
                <div class="item-status">
                    <div class="item-text-title">
                        {_p var='status'}:
                    </div>
                    <div class="item-text-info">
                        <span class="item-status-info {if $aInvoice.status == 'pending'}fw-bold{/if}">{$aInvoice.status_phrase}</span>
                    </div>
                </div>
            </div>
            {/foreach}
        </div>
    </div>
    {pager}
</div>
{/if}

