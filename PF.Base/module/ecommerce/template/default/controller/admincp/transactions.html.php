<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<form id="ynecommerce_transactions_search" action="{url link=$sModule}" method="GET">
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
                <label>{phrase var='item_name'}:</label>
                <input title="{phrase var='item_name'}" id="item_name" value="{value type='input' id='item_name'}" type="text" name="search[item_name]" class="form-control item_name">
            </div>
            <div class="form-group">
                <label>{phrase var='seller_name'}:</label>
                <input title="{phrase var='seller_name'}" id="seller_name" value="{value type='input' id='seller_name'}" type="text" name="search[seller_name]" class="form-control seller_name">
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label>{phrase var='from_date'}:</label>
                    {select_date prefix='from_' id='_start' start_year='current_year' end_year='+1' field_separator=' / ' default_all=true}
                </div>
                <div class="form-group col-md-6">
                    <label>{phrase var='to_date'}:</label>
                    {select_date prefix='to_' id='_start' start_year='current_year' end_year='+1' field_separator=' / ' default_all=true}
                </div>
                <div class="form-group col-md-6">
                    <label>{phrase var='payment_status'}:</label>
                    <select title="{phrase var='payment_status'}" class="form-control" id="invoice_status" name="search[invoice_status]">
                        <option value="all" {value type='select' id='invoice_status' default='all' }>{phrase var='all'}</option>
                        <option value="pending" {value type='select' id='invoice_status' default='pending' }>{phrase var='ecommerce.pending'}</option>
                        <option value="completed" {value type='select' id='invoice_status' default='completed' }>{phrase var='ecommerce.completed'}</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label>{phrase var='item_type'}:</label>
                    <select title="{phrase var='item_type'}" class="form-control" id="item_type" name="search[item_type]">
                        <option value="all" {value type='select' id='item_type' default='all' }>{phrase var='all'}</option>
                        {if Phpfox::isModule('auction')}
                            <option value="auction" {value type='select' id='item_type' default='auction'}>{phrase var='auction'}</option>
                        {/if}
                        {if Phpfox::isModule('ynsocialstore')}
                        <option value="store" {value type='select' id='item_type' default='store' }>{phrase var='store'}</option>
                        <option value="product" {value type='select' id='item_type' default='product' }>{phrase var='product'}</option>
                        {/if}
                    </select>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" id="btn_search" name="search[submit]" value="{phrase var='search'}" class="btn btn-primary">
            <input type="submit" id="btn_reset" name="reset" value="{phrase var='reset'}" class="btn btn-danger">
        </div>
    </div>
</form>

{if count($aTransactionRows)}
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {phrase var='manage_transactions'}
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>
                        {phrase var='transaction_id'}
                        <span class="go_right">
                            <a href="{$sCustomBaseLink}?sortfield=transaction_id&sorttype={if $sSortType == 'asc'}desc{else}asc{/if}"><i class="fa fa-sort" aria-hidden="true"></i></a>
                        </span>
                    </th>
                    <th>
                        {phrase var='item_name'}
                        <span class="go_right">
                            <a href="{$sCustomBaseLink}?sortfield=item_name&sorttype={if $sSortType == 'asc'}desc{else}asc{/if}"><i class="fa fa-sort" aria-hidden="true"></i></a>
                        </span>
                    </th>
                     <th>
                         {phrase var='item_type'}
                         <span class="go_right">
                            <a href="{$sCustomBaseLink}?sortfield=item_type&sorttype={if $sSortType == 'asc'}desc{else}asc{/if}"><i class="fa fa-sort" aria-hidden="true"></i></a>
                         </span>
                    </th>
                     <th>
                         {phrase var='seller_name'}
                         <span class="go_right">
                            <a href="{$sCustomBaseLink}?sortfield=seller_name&sorttype={if $sSortType == 'asc'}desc{else}asc{/if}"><i class="fa fa-sort" aria-hidden="true"></i></a>
                         </span>
                    </th>
                    <th>
                        {phrase var='purchase_date'}
                        <span class="go_right">
                            <a href="{$sCustomBaseLink}?sortfield=purchase_date&sorttype={if $sSortType == 'asc'}desc{else}asc{/if}"><i class="fa fa-sort" aria-hidden="true"></i></a>
                        </span>
                    </th>
                    <th>
                        {phrase var='fee'}
                        <span class="go_right">
                            <a href="{$sCustomBaseLink}?sortfield=fee&sorttype={if $sSortType == 'asc'}desc{else}asc{/if}"><i class="fa fa-sort" aria-hidden="true"></i></a>
                        </span>
                    </th>
                    <th class="payment_method">{phrase var='payment_method'}</th>
                    <th class="payment_status">{phrase var='payment_status'}</th>
                    <th class="options">{phrase var='description'}</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$aTransactionRows item=aTransactionRow}
                <?php
                    $aTransactionRow = $this->_aVars['aTransactionRow'];
                    $aItem = Phpfox::getService('ecommerce')->getTransactionItem($aTransactionRow['item_id'], $aTransactionRow['item_type']);
                    $this->_aVars['aItem'] = $aItem;
                ?>
                <tr>
                    <td>{$aTransactionRow.invoice_id}</td>
                    <td>
                        {if !empty($aItem)}
                            <a href="{$aItem.permalink}">{$aItem.name}</a>
                        {/if}
                    </td>
                    <td><?php echo ($aItem) ? ucfirst($aTransactionRow['item_type']) : ""; ?></td>
                    <td>
                        {if !empty($aItem)}
                            <?php
                                $aSeller = Phpfox::getService('user')->get($aItem['user_id']);
                                if($aSeller):
                                $aSellerLink = Phpfox::getService('user')->getLink($aSeller['user_id'], $aSeller['user_name']);
                            ?>
                            <a style="text-decoration: none;" href="<?php echo $aSellerLink;?>"><?php echo $aSeller['full_name'];?></a>
                            <?php endif;?>
                        {/if}
                    </td>
                    <td><?php echo Phpfox::getTime('d/m/Y',$aTransactionRow['time_stamp_paid']) ;?></td>
                    <td>{$aTransactionRow.price|number_format:2} {$aTransactionRow.currency_id}</td>
                    <td><?php echo ucfirst($aTransactionRow['payment_method']);?></td>
                    <td><?php echo _p(''.$aTransactionRow['status']);?></td>
                    <td><?php $aPayType = explode('|', $aTransactionRow['pay_type']);
                        foreach ($aPayType as $iKey => $aValue)
                            $aPayType[$iKey] = implode(' ', explode('_', $aValue));
                            echo ucwords(implode(' , ', $aPayType));
                        ?>
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
    {phrase var='no_transactions_found'}
</div>
{/if}