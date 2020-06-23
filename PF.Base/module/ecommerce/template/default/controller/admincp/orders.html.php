<?php

defined('PHPFOX') or exit('NO DICE!');

?>

<form id="ynecommerce_orders_search" action="{url link=$sModule}" method="post">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='search_filter'}
            </div>
        </div>

        <div class="panel-body">
            <div>
                <input type="hidden" name="sortfield" value="{$sSortField}">
                <input type="hidden" name="sorttype" value="{$sSortType}">
            </div>
            <div class="form-group">
                <label>{phrase var='product'}:</label>
                <input title="{phrase var='product'}" id="product_title" value="{value type='input' id='product_title'}"
                       type="text" name="search[product_title]" class="form-control product_title">
            </div>
            <div class="form-group">
                <label>{phrase var='item_type'}:</label>
                <select title="{phrase var='item_type'}" class="form-control" id="item_type" name="search[item_type]">
                    <option value="all" {value type='select' id='item_type' default='all' }>{phrase var='all'}</option>
                    <?php if (Phpfox::isModule('auction')): ?>
                        <option value="auction" {value type='select' id='item_type' default='auction' }>{phrase var='auction'}</option>
                    <?php endif; ?>
                    <?php if (Phpfox::isModule('ynsocialstore')): ?>
                        <option value="ynsocialstore_product" {value type='select' id='item_type' default='ynsocialstore_product' }> {phrase var='ynsocialstore'}</option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label>{phrase var='order_id'}:</label>
                    <input title="{phrase var='order_id'}" id="order_id" value="{value type='input' id='order_id'}"
                           type="text" name="search[order_id]" class="form-control order_id">
                </div>
                <div class="form-group col-md-6">
                    <label>{phrase var='seller'}:</label>
                    <input title="{phrase var='seller'}" id="seller_name" value="{value type='input' id='seller_name'}"
                           type="text" name="search[seller_name]" class="form-control seller_name">
                </div>
                <div class="form-group col-md-6">
                    <label>{phrase var='order_from'}:</label>
                    {select_date prefix='from_' id='_start' start_year='current_year' end_year='+1' field_separator=' / ' default_all=true}
                </div>
                <div class="form-group col-md-6">
                    <label>{phrase var='order_to'}:</label>
                    {select_date prefix='to_' id='_start' start_year='current_year' end_year='+1' field_separator=' / ' default_all=true}
                </div>
                <div class="form-group col-md-6">
                    <label>{phrase var='status'}:</label>
                    <select title="{phrase var='status'}" class="form-control" id="order_status"
                            name="search[order_status]">
                        <option value="new" {value type='select' id='order_status' default='new' }>{phrase var='new'}</option>
                        <option value="shipped" {value type='select' id='order_status' default='shipped' }>{phrase var='ecommerce.shipped'}</option>
                        <option value="cancel" {value type='select' id='order_status' default='cancel' }>{phrase var='ecommerce.cancel'}</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label>{phrase var='payment_status'}:</label>
                    <select title="{phrase var='payment_status'}" class="form-control" id="payment_status"
                            name="search[payment_status]">
                        <option value="">{phrase var='all'}</option>
                        <option value="initialized" {value type='select' id='payment_status' default='initialized' }>{phrase var='initialized'}</option>
                        <option value="pending" {value type='select' id='payment_status' default='pending' }>{phrase var='pending'}</option>
                        <option value="completed" {value type='select' id='payment_status' default='completed' }>{phrase var='completed'}</option>
                        <option value="expired" {value type='select' id='payment_status' default='expired' }>{phrase var='expired'}</option>
                        <option value="canceled" {value type='select' id='payment_status' default='canceled' }>{phrase var='canceled'}</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="panel-footer">
            <input type="submit" id="btn_search" name="search[submit]" value="{phrase var='search'}"
                   class="btn btn-primary">
            <input type="submit" id="btn_reset" name="search[reset]" value="{phrase var='reset'}"
                   class="btn btn-danger">
        </div>
    </div>
</form>

{if count($aOrderRows)}
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='manage_orders'}
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered">
                <!-- Table rows header -->
                <thead>
                <tr>
                    <th>
                        {phrase var='order_id'}
                        <span class="go_right">
                            <a href="{$sCustomBaseLink}?sortfield=order_id&sorttype={if $sSortType == 'asc'}desc{else}asc{/if}"><i
                                        class="fa fa-sort" aria-hidden="true"></i></a>
                        </span>
                    </th>
                    <th>
                        {phrase var='products'}
                    </th>
                    <th>
                        {phrase var='buyer'}
                        <span class="go_right">
                            <a href="{$sCustomBaseLink}?sortfield=order_buyer&sorttype={if $sSortType == 'asc'}desc{else}asc{/if}"><i
                                        class="fa fa-sort" aria-hidden="true"></i></a>
                        </span>
                    </th>
                    <th>
                        {phrase var='seller'}
                        <span class="go_right">
                            <a href="{$sCustomBaseLink}?sortfield=order_seller&sorttype={if $sSortType == 'asc'}desc{else}asc{/if}"><i
                                        class="fa fa-sort" aria-hidden="true"></i></a>
                        </span>
                    </th>
                    <th>
                        {phrase var='order_date'}
                        <span class="go_right">
                            <a href="{$sCustomBaseLink}?sortfield=order_date&sorttype={if $sSortType == 'asc'}desc{else}asc{/if}"><i
                                        class="fa fa-sort" aria-hidden="true"></i></a>
                        </span>
                    </th>
                    <th>
                        {phrase var='order_total'}
                        <span class="go_right">
                            <a href="{$sCustomBaseLink}?sortfield=order_total&sorttype={if $sSortType == 'asc'}desc{else}asc{/if}"><i
                                        class="fa fa-sort" aria-hidden="true"></i></a>
                        </span>
                    </th>
                    <th>
                        {phrase var='commission'}
                        <span class="go_right">
                            <a href="{$sCustomBaseLink}?sortfield=commission_value&sorttype={if $sSortType == 'asc'}desc{else}asc{/if}"><i
                                        class="fa fa-sort" aria-hidden="true"></i></a>
                        </span>
                    </th>
                    <th class="status">{phrase var='status'}</th>
                    <th class="payment_status">{phrase var='payment_status'}</th>
                    <th class="options">{phrase var='options'}</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$aOrderRows item=aOrderRow}
                    {php}
                        $aOrderRow = $this->_aVars['aOrderRow'];
                    {/php}
                    <tr>
                        <td class="order_id"><a
                                    href="{permalink module=$aOrderRow.module_id.'.order-detail' id=$aOrderRow.order_id}">{$aOrderRow.order_code}</a>
                        </td>
                        <td class="products">
                            <?php $lastKey = end($aOrderRow['products']); ?>
                            {foreach from = $aOrderRow.products key = iKey item = aOrderItem}
                                <a href="{permalink module=$aOrderItem.orderproduct_module.'.detail' id=$aOrderItem.orderproduct_id title=$aOrderItem.orderproduct_product_name}">{$aOrderItem.orderproduct_product_name}{if isset($aOrderItem.attribute_name)} ({$aOrderItem.attribute_name}){/if}</a>
                                <?php if ($lastKey != $this->_aVars['aOrderItem']) echo ',' ?>
                            {/foreach}
                        </td>
                        <td class="buyer">
                            <?php
                                $aBuyer = Phpfox::getService('user')->get($aOrderRow['user_id']);
                            if($aBuyer):
                            ?>
                            <?php
                                $aBuyerLink = Phpfox::getService('user')->getLink($aBuyer['user_id'],
                            $aBuyer['user_name']);
                            ;?>
                            <a style="text-decoration: none;"
                               href="<?php echo $aBuyerLink;?>"><?php echo $aBuyer['full_name'];?></a>
                            <?php endif;?>
                        </td>
                        <td class="seller">
                            <?php
                                $aSeller = Phpfox::getService('user')->get($aOrderRow['seller_id']);
                            if($aSeller):
                            ?>
                            <?php
                                $aSellerLink = Phpfox::getService('user')->getLink($aSeller['user_id'],
                            $aSeller['user_name']);
                            ;?>
                            <a style="text-decoration: none;"
                               href="<?php echo $aSellerLink;?>"><?php echo $aSeller['full_name'];?></a>
                            <?php endif;?>
                        </td>
                        <td class="order_date">{$aOrderRow.order_creation_datetime}</td>
                        <td class="order_total price">{$aOrderRow.order_total_price|number_format:2} {$aOrderRow.order_currency}</td>
                        <td class="order_commission_value price">{$aOrderRow.order_commission_value|number_format:2} {$aOrderRow.order_currency}</td>
                        <td class="status">
                            {if $aOrderRow.order_status == 'new'}{phrase var='new'}{/if}
                            {if $aOrderRow.order_status == 'shipped'}{phrase var='shipped'}{/if}
                            {if $aOrderRow.order_status == 'cancel'}{phrase var='cancel'}{/if}
                        </td>
                        <td class="payment_status">{$aOrderRow.order_payment_status}</td>
                        <td class="options">
                            <a href="{permalink module=$aOrderRow.module_id.'.order-detail' id=$aOrderRow.order_id}"><span
                                        class="view_icon"></span>{phrase var='view'}</a>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </div>
    {pager}
{else}
    <div class="alert alert-info">
        {phrase var='no_orders_found'}
    </div>
{/if}