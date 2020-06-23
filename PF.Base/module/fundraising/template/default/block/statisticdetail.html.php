<?php
defined('PHPFOX') or exit('NO DICE!');
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {phrase var='transaction_detail'}
        </div>
    </div>
    <div class="panel-body">
        <h3>{phrase var='order_detail'}</h3>
        <div class="table-responsive">
            <table class="table table-bordered">
                <tbody>
                <tr>
                    <td class="w220">{phrase var='member'}:</td>
                    <td>{$aTransaction.donor_name}</td>
                </tr>
                <tr>
                    <td class="w220">{phrase var='campaign_name'}:</td>
                    <td>{$aTransaction.campaign_name}</td>
                </tr>

                <tr>
                    <td class="w220">{phrase var='donate_date'}:</td>
                    <td>{$aTransaction.donate_date|date}</td>
                </tr>
                <tr>
                    <td class="w220">{phrase var='description'}:</td>
                    <td>{$aTransaction.description}</td>
                </tr>
                </tbody>
            </table>
        </div>

        <h3>{phrase var='payment_detail'}</h3>

        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <td class="w220">{phrase var='donation_amount'}:</td>
                    <td>{$aTransaction.donation_amount}</td>
                </tr>
                <tr>
                    <td class="w220">{phrase var='gateway_transaction_id'}:</td>
                    <td>{$aTransaction.transaction_id}</td>
                </tr>
            </table>
        </div>

        <div style="width: 200px;" class="mt-1">
            <a href="#" class="button btn btn-default" onclick="history.back(); return false;">{phrase var='back'}</a>
        </div>
    </div>
</div>
