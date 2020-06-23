{if $aAuction.shipping  != ''}
<div class="ynauction-detail-shipping">
    <div class="ynauction_trix_header">
        <span class="section_title"> <i class="fa fa-info-circle"></i> {phrase var='shipping_and_payment'}</span>
    </div>
    <div class="ynauction-detail-shipping-item item_view_content">
        {$aAuction.shipping|parse}
    </div>
</div>
{/if}