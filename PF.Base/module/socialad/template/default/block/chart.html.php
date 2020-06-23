<form id="js_ynsa_chart_control_form" >
<input type="hidden" name="val[ad_id]" value="{$iChartAdId}" />
<div class="table form-group">
	<div class="table_right clearfix">
		<select class="form-control" name="val[period]" id="ynsa_period_history">
			{foreach from=$aSaPeriods item=iPeriod} 
				<option value="{$iPeriod.value}" > {$iPeriod.phrase}</option>
			{/foreach}
		</select>	

		<select class="form-control" name="val[data_type]" id="">
			<option value="click" > {phrase var='click'}</option>
			<option value="unique_click" > {phrase var='unique_click'}</option>
			<option value="reach" > {phrase var='reach'}</option>
			<option value="impression" > {phrase var='impression'}</option>
		</select>	
		<div id="js_ynsa_chart_waiting_symbol"> </div>
	</div>
	<div class="table form-group ynsaClearFix" style="margin-top: 20px; display: none;padding: 10px;" id="ynsa_range_of_dates_picker">
		<div class="ynsaAdTime ynsaClearFix" >
			<div class="ynsaAdTimeTitle ynsaLFloat" >{phrase var='start'} : </div>
			
			<div class="ynsaAdTimeContent ynsaLFloat report">
				 {select_date prefix='start_' id='_begin_time' start_year='2000' end_year='+10' field_separator=' / ' field_order='MDY' }
			</div>
		</div>
		<div class="clear"></div>
		<div class="ynsaAdTime mt-1" >
			<div class="ynsaAdTimeTitle ynsaLFloat" >{phrase var='end'} : </div>
			
			<div class="ynsaAdTimeContent ynsaLFloat report" style="">
				 {select_date prefix='end_' id='_begin_time' start_year='2000' start_day='1' start_month='1' end_year='+10' field_separator=' / ' field_order='MDY' }
			</div>
		</div>
		<div class="clear"></div>
		<div class="ynsaAdTime mt-1" style="">
			<div class="ynsaAdTimeTitle ynsaLFloat" ><a class="btn btn-primary " href="#" onclick="ynsocialad.chart.updateWithRangeOfDates(); return false;">Update</a></div>			
		</div>
	</div> <!-- end choose date time -->
</div>

</form>
<div class="clear"></div>
<div id="ynsa_chart_holder_{$iChartAdId}"></div>
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	<script type="text/javascript">
        if (typeof google != 'undefined' && typeof google.load != 'undefined') {l}
		    google.load('visualization', '1.0', {l}'packages':['corechart']{r});
        {r}
	</script>
    <script type="text/javascript">
    {literal}
        $Behavior.ynsaInitChart = function() { 
 	        ynsocialad.setParams('{/literal}{$aParams}{literal}');
         	ynsocialad.chart.init({/literal}{$iChartAdId}{literal});
         	ynsocialad.chart.getAdData();
        }
    {/literal}
    </script>



