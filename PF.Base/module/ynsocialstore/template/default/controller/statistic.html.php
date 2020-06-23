<?php

defined('PHPFOX') or exit('NO DICE!');

?>

<div class="ynstore-seller-statistic">
    <div class="ynstore-seller-statistic-item ynstore-seller-store">
        <span>{_p var='ynsocialstore_stores'}</span>
        <div class="ynstore-seller-statistic-img"></div>
        <span class="ynstore-count">{$iTotalStores}</span>
    </div>

    <div class="ynstore-seller-statistic-item ynstore-seller-product">
        <span>{_p var='ynsocialstore_products'}</span>
        <div class="ynstore-seller-statistic-img"></div>
        <span class="ynstore-count">{$iTotalProducts}</span>
    </div>

    <div class="ynstore-seller-statistic-item ynstore-seller-totalsale">
        <span>{_p('Total sales')}</span>
        <div class="ynstore-seller-statistic-img"></div>
        <span class="ynstore-count">{$fTotalSales|ynsocialstore_format_price:$sDefaultCurrency}</span>
    </div>

     <div class="ynstore-seller-statistic-item ynstore-seller-totalorder">
        <span>{_p('Total orders')}</span>
        <div class="ynstore-seller-statistic-img"></div>
        <span class="ynstore-count">{$iTotalOrders}</span>
    </div>

    <div class="ynstore-seller-statistic-item ynstore-seller-totalsold">
        <span>{_p('Total items sold')}</span>
        <div class="ynstore-seller-statistic-img"></div>
        <span class="ynstore-count">{$iTotalItemSold}</span>
    </div>
</div>

<div class="insight_store_search_message"></div>

<div class="ynstore-seller-statistic-search">
    <form method="post" action="#" onsubmit="return false;" id="js_statistic_search_form">

        <input type="hidden" name="sType" value="statistic">
        <div class="row">
            <div class="col-md-5 col-sm-12 ynstore-paddingright-5">
                <div class="form-group">
                    <div class="ynstore_start_time" class="form-control">
                        <div class="input-group">
                            <div class="btn input-group-addon">{_p('From')}</div>
                                <div class="js_from_select">
                                   {select_date prefix='from_' id='_from' start_year='-2' end_year='+2' field_separator=' / ' field_order='MDY' default_all=true }
                                </div>
                            <div class="btn input-group-addon js_datepicker_image"><i class="ico ico-calendar"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5 col-sm-12 ynstore-paddingleft-5 ynstore-paddingright-5">
                <div class="form-group">
                    <div class="ynstore_end_time">
                        <div class="input-group">
                            <div class="btn input-group-addon">{_p('To')}</div>
                            <div class="js_to_select">
                                {select_date prefix='to_' id='_to' start_year='-2' end_year='+2' field_separator=' / ' field_order='MDY' default_all=true }
                            </div>
                            <div class="btn input-group-addon js_datepicker_image"><i class="ico ico-calendar"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-2 col-sm-12 ynstore-paddingleft-5">
                <div class="ynstore-statistic_submit">
                    <input id="statistic_button" type="button" name="submit" value="{_p var='ecommerce.go_chart'}" class="btn btn-primary" onclick="searchStatistic();"/>
                </div>
            </div>
        </div>



    </form>
</div>


<div id="charts_loading" class="ynauction-clearfix" style="display: none;">{img theme='ajax/large.gif' class='v_middle'}</div>
<div id="charts_holder" class="ynauction-charts"></div>

{literal}
<script type="text/javascript">
    function searchStatistic(){
        $('#charts_loading').show();
        $('#charts_holder').hide();
        $('#statistic_button').prop("disabled", false);
        $('.statistic_search_message').html('');
        $('#js_statistic_search_form').ajaxCall('ynsocialstore.getCharts');
    }

    $Behavior.initCalendarButtons = function() {
        $('.js_datepicker_image').off('click').click(function (e) {
            $(this).closest('.input-group').find('.js_date_picker').datepicker('show');
        });
    }
</script>
{/literal}
