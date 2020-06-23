{if isset($sError) && !empty($sError)}
    {$sError}
{else}
<div id='ynstore_manage_insight' class="ynstore-store-manage-block">
    <h2>{$aStore.name}</h2>

    <ul class="ynstore-store-insight-listing">
        {if isset($aPackageStore) && count($aPackageStore)}
        <li>
            <div class="ynstore-package-bg">
                <span class="ynstore-label">{_p var='ynsocialstore.package'}</span>
                <span class="ynstore-value">{$aPackageStore.name}</span>
            </div>
        </li>
        {/if}

        {if $aStore.expire_time == 4294967295}
        <li>
            <div class="ynstore-expired-bg">
                <span class="ynstore-label">{_p var='ynsocialstore.expired'}</span>
                <span class="ynstore-value">{_p var='ynsocialstore.never_expired'}</span>
            </div>
         </li>
         {elseif $aStore.expire_time > 0}
         <li>
            <div>
                <span class="ynstore-label">{_p var='ynsocialstore.expired'}</span>
                <span class="ynstore-value">{$aStore.expire_time|date}</span>
            </div>
        </li>
        {/if}

        <li>
            <div class="ynstore-products-bg">
                <span class="ynstore-label">{_p var='ynsocialstore.products'}</span>
                <span class="ynstore-value">{$aStore.total_products}</span>
            </div>
        </li>

        <li>
            <div class="ynstore-followers-bg">
                <span class="ynstore-label">{_p('Followers')}</span>
                <span class="ynstore-value">{$aStore.total_follow}</span>
            </div>
        </li>

        <li>
            <div class="ynstore-liked-bg">
                <span class="ynstore-label">{_p var='liked'}</span>
                <span class="ynstore-value">{if !empty($aStore.total_product_like)}{$aStore.total_product_like}{else}0{/if}</span>
            </div>
        </li>

        <li>
            <div class="ynstore-viewed-bg">
                <span class="ynstore-label">{_p var='ynsocialstore_viewed'}</span>
                <span class="ynstore-value">{$aStore.total_view}</span>
            </div>
        </li>

        <li>
            <div class="ynstore-charged-bg">
                <span class="ynstore-label">{_p('Total amount charged')}</span>
                <span class="ynstore-value">{$aStore.total_commission_price}</span>
            </div>
        </li>

        <li>
            <div class="ynstore-totalsale-bg">
                <span class="ynstore-label">{_p('Total sale')}</span>
                <span class="ynstore-value">{$aStore.total_product_price}</span>
            </div>
        </li>

        <li>
            <div class="ynstore-sold-bg">
                <span class="ynstore-label">{_p('Products sold')}</span>
                <span class="ynstore-value">{if !empty($aStore.product_sold)}{$aStore.product_sold}{else}0{/if}</span>
            </div>
        </li>

        <li>
            <div class="ynstore-rating-bg">
                <span class="ynstore-label">{_p('Rating')}</span>
                <span class="ynstore-value">{$aStore.rating}</span>
            </div>
        </li>
    </ul>

    <div class="insight_store_search_message"></div>

    <div class="ynstore-insight-store-search ynstore-seller-statistic-search clearfix">
        <form method="post" action="#" onsubmit="return false;" id="js_statistic_search_form">
            <input type="hidden" name="val[store_id]" id='store_id' value="{$iStoreId}" >
            <input type="hidden" name="sType" value="insight">
            <div class="row">
                <div class="col-md-5 col-sm-12 ynstore-paddingright-5">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon">{_p var='ecommerce.from'}</div>
                            <div class="js_from_select">
                                {select_date prefix='from_' id='_from' start_year='-2' end_year='+2' field_separator=' / ' field_order='MDY' default_all=true }
                            </div>
                            <div class="input-group-addon js_datepicker_image"><i class="ico ico-calendar"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-sm-12 ynstore-paddingleft-5 ynstore-paddingright-5">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon">{_p var='ecommerce.to'}</div>
                            <div class="js_to_select">
                                {select_date prefix='to_' id='_to' start_year='-2' end_year='+2' field_separator=' / ' field_order='MDY' default_all=true }
                            </div>
                            <div class="input-group-addon js_datepicker_image"><i class="ico ico-calendar"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-12 ynstore-paddingleft-5">
                    <div class="ynstore-statistic_submit">
                        <input id="statistic_button" type="button" name="submit" value="{_p var='ecommerce.go_chart'}" class="btn btn-primary" onclick="searchStoreInsight();return false;"/>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div id="charts_loading" class="clearfix" style="display: none;">{img theme='ajax/large.gif' class='v_middle'}</div>
    <div id="charts_holder" class="ynstore-charts">
    </div>
</div>

{/if}

{literal}
<script type="text/javascript">
    function searchStoreInsight()
    {
        $('#charts_loading').show();
        $('#charts_holder').hide();
        $('#statistic_button').prop("disabled", false);
        $('.insight_store_search_message').html('');
        $('#js_statistic_search_form').ajaxCall('ynsocialstore.getCharts');
    }

    $Behavior.initCalendarButtons = function() {
        $('.js_datepicker_image').off('click').click(function (e) {
            $(this).closest('.input-group').find('.js_date_picker').datepicker('show');
        });
    }
</script>
{/literal}
