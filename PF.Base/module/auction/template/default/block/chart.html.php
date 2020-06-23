<div class="ynauction-detail-charforbidding">
    <div class="ynauction_trix_header">
        <div class="section_title">
            <span class="section_title"><i class="fa fa-area-chart"></i> {phrase var='chart_for_bidding'}</span>
        </div>
    </div>

    <input type="hidden" id='ynauction_product_id' name='ynauction_product_id' value="{$aAuction.product_id}" />

    <div class="chart_for_bidding_fields">
        <div class="table form-group">
            <label>{phrase var='start'} : </label>
             <div class="table_right">
                {select_date prefix='start_' id='_begin_time' start_year='2000' end_year='+10' field_separator=' / ' field_order='MDY' }
             </div>
        </div>

        <div class="table form-group">
            <label>
                {phrase var='end'} :
            </label>
            <div class="table_right">
                 {select_date prefix='end_' id='_begin_time' start_year='2000' start_day='1' start_month='1' end_year='+10' field_separator=' / ' field_order='MDY' }
            </div>
        </div>
        <div class="submit_group">
            <button type="submit" name="submit" class="btn btn-sm btn-success" id='filter_chart'>{phrase var='go_to_chart'}</button>
        </div>
    </div>

    <div class="ynauction-demo-container">
        <div id="placeholder" class="demo-placeholder" style="width:600px;height:350px;"></div>
    </div>


</div>


{literal}
<script type="text/javascript">
;
$Behavior.ynauction_load_chart = function() {
	ynauction.initDetailChart();
}
;

</script>
{/literal}

