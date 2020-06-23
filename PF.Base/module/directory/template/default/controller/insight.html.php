<div id='yndirectory_manage_insight'>		
<div class="clear"></div> 
	<form method="post" action="{url link='directory.manage_insight.id_'.$iBusinessid}" id="js_manage_insight" onsubmit="" enctype="multipart/form-data">

			<input type="hidden" name="val[business_id]" id='business_id' value="{$iBusinessid}" >

			<h4 class="yndirectory-doashboard-title">
				{$aBusiness.name}
			</h4>

			{if isset($aPackageBusiness)}
			<div class="yndirectory-doashboard-statics">
				<span>{phrase var='package'}</span> : {$aPackageBusiness.name}
			</div>
			{/if}

			<div class="yndirectory-doashboard-statics">
				<span>{phrase var='expire'}</span> : {$aBusiness.expire_date}
			</div>

			{if isset($aBusiness.featured) && $aBusiness.featured}
				<div class="yndirectory-doashboard-statics">
					<span>{phrase var='feature_expiration_date'}</span> : {if isset($aBusiness.is_unlimited) && $aBusiness.is_unlimited}{phrase var='unlimited'}{else}{$aBusiness.feature_expired_date}{/if}
				</div>
			{/if}

			{if isset($aPackageBusiness)}
				<div class="yndirectory-doashboard-statics">
					<span>{phrase var='price'}</span> : {$aPackageBusiness.fee}
				</div>
			{/if}

			<div class="yndirectory-doashboard-statics">
				<span>{phrase var='latest_payment'}</span> :
				{if isset($aBusiness.time_paid)}{$aBusiness.time_paid}{else}N/A{/if}
			</div>

			<div class="yndirectory-doashboard-statics">
				<span>{phrase var='approved_at'}</span> : {if $aBusiness.time_approved != ''}{$aBusiness.time_approved}{else}N/A{/if}
			</div>

			<div class="yndirectory-doashboard-statics">
				<span>{phrase var='reviews'}</span> : {$aBusiness.total_reviews}
			</div>

			<div class="yndirectory-doashboard-statics">
				<span>{phrase var='rating'}</span> : {$aBusiness.total_score}
			</div>

			<div class="yndirectory-doashboard-statics">
				<span>{phrase var='members_up'}</span> : {$aBusiness.count_member}
			</div>

			<div class="yndirectory-doashboard-statics">
				<span>{phrase var='followers_up'}</span> : {$aBusiness.count_follower}
			</div>

			<div class="yndirectory-doashboard-statics">
				<span>{phrase var='liked'}</span> : {$aBusiness.total_like}
			</div>

			<div class="yndirectory-doashboard-statics">
				<span>{phrase var='commented'}</span> : {$aBusiness.total_comment}
			</div>

			<div class="yndirectory-doashboard-statics">
				<span>{phrase var='viewed'}</span> : {$aBusiness.total_view}
			</div>

			<div class="yndirectory-doashboard-statics">
				<span>{phrase var='pages'}</span> : {$aBusiness.count_pages}
			</div>

		{if Phpfox::getService('directory.helper')->isMusic() && isset($aModuleView.musics) && $aModuleView.musics.is_show}
			<div class="yndirectory-doashboard-statics">
				<span>{$aModuleView.musics.module_phrase|convert}</span> : {$aNumberOfItem.musics} 
			</div>
		{/if}

		{if Phpfox::getService('directory.helper')->isBlog() && isset($aModuleView.blogs) && $aModuleView.blogs.is_show}
			<div class="yndirectory-doashboard-statics">
				<span>{$aModuleView.blogs.module_phrase|convert}</span> : {$aNumberOfItem.blogs} 
			</div>
		{/if}

		{if Phpfox::getService('directory.helper')->isPhoto() && isset($aModuleView.photos) && $aModuleView.photos.is_show}
			<div class="yndirectory-doashboard-statics">
				<span>{$aModuleView.photos.module_phrase|convert}</span> : {$aNumberOfItem.photos} 
			</div>
		{/if}


		{if Phpfox::getService('directory.helper')->isVideoChannel() && isset($aModuleView.videos) && $aModuleView.videos.is_show}
			<div class="yndirectory-doashboard-statics">
				<span>{$aModuleView.videos.module_phrase|convert}</span> : {$aNumberOfItem.videos} 
			</div>
		{/if}

        {if Phpfox::getService('directory.helper')->isVideo() && isset($aModuleView.v) && $aModuleView.v.is_show}
			<div class="yndirectory-doashboard-statics">
				<span>{$aModuleView.v.module_phrase|convert}</span> : {$aNumberOfItem.videos}
			</div>
		{/if}

		{if Phpfox::getService('directory.helper')->isPoll() && isset($aModuleView.polls) && $aModuleView.polls.is_show}
			<div class="yndirectory-doashboard-statics">
				<span>{$aModuleView.polls.module_phrase|convert}</span> : {$aNumberOfItem.polls} 
			</div>
		{/if}

		{if Phpfox::getService('directory.helper')->isCoupon() && isset($aModuleView.coupons) && $aModuleView.coupons.is_show}
			<div class="yndirectory-doashboard-statics">
				<span>{$aModuleView.coupons.module_phrase|convert}</span> : {$aNumberOfItem.coupons} 
			</div>
		{/if}

		{if Phpfox::getService('directory.helper')->isEvent() && isset($aModuleView.events) && $aModuleView.events.is_show}
			<div class="yndirectory-doashboard-statics">
				<span>{$aModuleView.events.module_phrase|convert}</span> : {$aNumberOfItem.events} 
			</div>
		{/if}

		{if Phpfox::getService('directory.helper')->isJob() && isset($aModuleView.jobs) && $aModuleView.jobs.is_show}
			<div class="yndirectory-doashboard-statics">
				<span>{$aModuleView.jobs.module_phrase|convert}</span> : {$aNumberOfItem.jobs} 
			</div>
		{/if}

		{if Phpfox::getService('directory.helper')->isMarketplace() && isset($aModuleView.marketplace) && $aModuleView.marketplace.is_show}
			<div class="yndirectory-doashboard-statics">
				<span>{$aModuleView.marketplace.module_phrase|convert}</span> : {$aNumberOfItem.marketplace} 
			</div>
		{/if}

		<div id="statics">
			
			<div class="help-block">{phrase var='use_the_below_filter_to_observe_various_metrics_of_your_page_over_different_time_periods'}</div>

			<div class="yndirectory-doashboard-insight-form-item form-group">
				<label>{phrase var='type'}</label>
				<select class="form-control" id='yndirectory_chart_type' name="yndirectory_chart_type">
					<option value="normal">{phrase var='all'}</option>
					<option value="cumulative">{phrase var='cumulative'}</option>
					<option value="changein">{phrase var='change_in'}</option>
				</select>
			</div>

			<div class="yndirectory-doashboard-insight-form-item form-group">
				<label>{phrase var='metric'}</label>
				<select class="form-control" id='yndirectory_chart_metric'>
					<option value="reviews">{phrase var='reviews'}</option>
					<option value="members">{phrase var='members_up'}</option>
					<option value="followers">{phrase var='followers_up'}</option>
					<option value="comments">{phrase var='comment'}</option>
					<option value="likes">{phrase var='like'}</option>
				</select>
			</div>

			<div class="yndirectory-doashboard-insight-form-item form-group">
				<label>{phrase var='duration'}</label>
				<select class="form-control" id='yndirectory_chart_duration'>
					<option value="today">{phrase var='today'}</option>
					<option value="yesterday">{phrase var='yesterday'}</option>
					<option value="last_week">{phrase var='last_week'}</option>
					<option value="range_of_dates">{phrase var='range_of_dates'}</option>
				</select>
			</div>

			<div style="display: none;" id="ynd_range_of_dates_picker" class="form-group">
				<div class="yndirectory-doashboard-insight-form-item">
					<span>{phrase var='start'}: </span>
					<div style="position:relative;">
						{select_date prefix='start_' id='_begin_time' start_year='2000' end_year='+10' field_separator=' / ' field_order='MDY' }
					</div>
				</div>

				<div class="yndirectory-doashboard-insight-form-item mt-h1">
					<span>{phrase var='end'}: </span>
					<div style="position:relative;">					
						{select_date prefix='end_' id='_begin_time' start_year='2000' start_day='1' start_month='1' end_year='+10' field_separator=' / ' field_order='MDY' }
					</div>
				</div>
			</div>

			<div class="yndirectory-doashboard-insight-form-item-button yndirectory-button form-group">
				<button type="button" id='filter_chart' class="btn btn-sm btn-primary" value='{phrase var='filter'}'>{phrase var='filter'}</button>
			</div>

			<div class="yndirectory-demo-container">
				<div id="placeholder" class="demo-placeholder" style="width:600px;height:350px;"></div>
			</div>

		</div>
	</form>
</div>
{literal}
<script type="text/javascript">
;
$Behavior.changeRangeDate = function(){

	$('#yndirectory_chart_duration').change(function(){

		if($('#yndirectory_chart_duration').val() == 'range_of_dates'){
			// Range of dates
			$('#ynd_range_of_dates_picker').show();
		} else {
			$('#ynd_range_of_dates_picker').hide();
		}

	});
		
}

$Behavior.yndirectory_load_chart = function() {

	var data = [];

	var iBusinessid = $('#yndirectory_manage_insight #business_id').val();

	var iTypeId = 0;


		var iTypeId = $('#yndirectory_manage_insight #yndirectory_chart_type').val();
		var iMetricId = $('#yndirectory_manage_insight #yndirectory_chart_metric').val();
		var iDuration = $('#yndirectory_manage_insight #yndirectory_chart_duration').val();
		var js_start__datepicker = $('#yndirectory_manage_insight input[name="js_start__datepicker"]').val();
		var js_end__datepicker = $('#yndirectory_manage_insight input[name="js_end__datepicker"]').val();

		$Core.ajax('directory.getChartData',
        {
            type: 'POST',
            params:
            {	
                 iBusinessid: iBusinessid,
                 iTypeId: 'normal',
                 iMetricId: 'reviews',
                 iDuration: 'today',
				 js_start__datepicker: js_start__datepicker,
                 js_end__datepicker: js_end__datepicker,
            },
            success: function(sOutput)
            {
            	var aChartData = $.parseJSON(sOutput);
            	//data.push(aReceive);

            	var d = [];
                var ticks = [];
                var count = 0;
                
                aReceive = aChartData.data;
                title = aChartData.title;
                for(var i in aReceive)
                {
                    d.push([count,aReceive[i]]);
                    ticks.push([count,i]);
                    count = count +1;
                }

               var data = [{
		                    data: d,
		                    label: oTranslations['directory.'+title]
		                }];

				$.plot("#placeholder", data, {
                   
                    legend: {
                        labelFormatter: function(label, series) {
                            return  label;
                        }
                    },
                    series: {
                        lines: {
                            show: true
                        },
                        points: {
                            show: true
                        }
                    },
                    grid: {
                        hoverable: true,
                        clickable: true
                    },
                    xaxis: { 
                        show: true,
                        ticks: ticks
                    }
                } );
            }
        });

	$('#filter_chart').click(function(){

		var iTypeId = $('#yndirectory_manage_insight #yndirectory_chart_type').val();
		var iMetricId = $('#yndirectory_manage_insight #yndirectory_chart_metric').val();
		var iDuration = $('#yndirectory_manage_insight #yndirectory_chart_duration').val();
		var js_start__datepicker = $('#yndirectory_manage_insight input[name="js_start__datepicker"]').val();
		var js_end__datepicker = $('#yndirectory_manage_insight input[name="js_end__datepicker"]').val();

		$Core.ajax('directory.getChartData',
        {
            type: 'POST',
            params:
            {	
                 iBusinessid: iBusinessid,
                 iTypeId: iTypeId,
                 iMetricId: iMetricId,
                 iDuration : iDuration,
				 js_start__datepicker: js_start__datepicker,
                 js_end__datepicker: js_end__datepicker,
            },
            success: function(sOutput)
            {
            	var aChartData = $.parseJSON(sOutput);
            	//data.push(aReceive);

            	var d = [];
                var ticks = [];
                var count = 0;
                
                aReceive = aChartData.data;
                title = aChartData.title;
                for(var i in aReceive)
                {
                    d.push([count,aReceive[i]]);
                    ticks.push([count,i]);
                    count = count +1;
                }

               var data = [{
		                    data: d,
		                    label: oTranslations['directory.'+title]
		                }];

				$.plot("#placeholder", data, {
                   
                    legend: {
                        labelFormatter: function(label, series) {
                            return  label;
                        }
                    },
                    series: {
                        lines: {
                            show: true
                        },
                        points: {
                            show: true
                        }
                    },
                    grid: {
                        hoverable: true,
                        clickable: true
                    },
                    xaxis: { 
                        show: true,
                        ticks: ticks
                    }
                } );
            }
        });

	});

}
;

</script>
{/literal}
