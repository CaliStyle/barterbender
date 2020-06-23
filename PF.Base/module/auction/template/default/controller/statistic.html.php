<?php 

defined('PHPFOX') or exit('NO DICE!'); 

?>

<div class="ynauction_statistic ynauction-clearfix">
    <div class="stat-info total_auctions">
        <div class="stat-info-content">
            <div class="stat-label">{phrase var='ecommerce.auction_s'}</div>
            <div class="stat-value total_auctions_number">{$iTotalAuctions}</div>
            <div><span class="total_auctions_icon"></span></div>
        </div>
    </div>
    <div class="stat-info total_bids">
        <div class="stat-info-content">
            <div class="stat-label">{phrase var='ecommerce.bid_s'}</div>
            <div class="stat-value total_bids_number">{$iTotalBids}</div>
            <div><span class="total_bids_icon"></span></div>
        </div>
    </div>
    <div class="stat-info total_orders">
        <div class="stat-info-content">
            <div class="stat-label">{phrase var='ecommerce.order_s'}</div>
            <div class="stat-value total_orders_number">{$iTotalOrders}</div>
            <div><span class="total_orders_icon"></span></div>
        </div>
    </div>
    <div class="stat-info total_sales">
        <div class="stat-info-content">
            <div class="stat-label">{phrase var='ecommerce.total_sales'}</div>
            <div class="stat-value total_sales_number">{$fTotalSales|number_format:2}</div>
            <div><span class="total_sales_icon"></span></div>
        </div>
    </div>
    <div class="stat-info total_commissions">
        <div class="stat-info-content">
        <div class="stat-label">{phrase var='ecommerce.total_commissions'}</div>
        <div class="stat-value total_commissions_number">{$fTotalCommissions|number_format:2}</div>
        <div><span class="total_commissions_icon"></span></div>
        </div>
    </div>
    <div class="stat-info total_number_auctions_sold">
        <div class="stat-info-content">
            <div class="stat-label">{phrase var='ecommerce.number_of_auctions_sold'}</div>
            <div class="stat-value total_number_auctions_sold_number">{$iTotalSoldAuctions}</div>
            <div><span class="total_number_auctions_sold_icon"></span></div>
        </div>
    </div>
    <div class="stat-info total_liked">
        <div class="stat-info-content">
            <div class="stat-label">{phrase var='ecommerce.liked'}</div>
            <div class="stat-value total_liked_number">{$iTotalLikes}</div>
            <div><span class="total_liked_icon"></span></div>
        </div>
    </div>
    <div class="stat-info total_views">
        <div class="stat-info-content">
            <div class="stat-label">{phrase var='ecommerce.viewed'}</div>
            <div class="stat-value otal_views_number">{$iTotalViews}</div>
            <div><span class="total_views_icon"></span></div>
        </div>
    </div>
</div>

<div class="statistic_search_message ynauction-clearfix"></div>
<div class="ynauction_statistic_search ynauction-clearfix">
    <form method="post" action="#" onsubmit="return false;" id="js_statistic_search_form" >
        <div class="statistic_from">
            <div class="statistic_from_label">{phrase var='ecommerce.from'}</div>
            <div style="position: relative;" class="js_from_select">
                {select_date prefix='from_' id='_from' start_year='-2' end_year='+2' field_separator=' / ' field_order='MDY' default_all=true }
            </div>
        </div>
        <div class="statistic_to">
            <div class="statistic_to_label">{phrase var='ecommerce.to'}</div>
            <div style="position: relative;" class="js_to_select">
                {select_date prefix='to_' id='_to' start_year='-2' end_year='+2' field_separator=' / ' field_order='MDY' default_all=true }
            </div>
        </div>
        <div class="statistic_submit">
            <input id="statistic_button" type="button" name="submit" value="{phrase var='ecommerce.go_chart'}" class="btn btn-sm btn-primary" onclick="searchStatistic();"/>
        </div>
    </form>
</div>
<div id="charts_loading" class="ynauction-clearfix" style="display: none;">{img theme='ajax/large.gif' class='v_middle'}</div>
<div id="charts_holder" class="ynauction-charts">
    
</div>

{literal}
<script type="text/javascript">
    function searchStatistic()
    {
        $('#charts_loading').show();
        $('#charts_holder').hide();
        $('#statistic_button').prop("disabled", false);
        $('.statistic_search_message').html('');
        $('#js_statistic_search_form').ajaxCall('auction.getCharts');
    }
</script>
{/literal}