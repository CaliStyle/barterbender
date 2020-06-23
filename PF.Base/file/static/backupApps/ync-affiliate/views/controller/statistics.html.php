<div class="yncaffiliate_statistics clearfix">
	<div class="col-md-12 clearfix statictis_header">
		<div class="col-md-4 col-sm-12 nopadding text-left">
			<span>{_p var='total_number_of_payment'}:</span>
			<b>{$iTotalPayment}</b>
		</div>
		<div class="col-md-4 col-sm-12 nopadding text-center">
			<span>{_p var='total_of_commission_point'}:</span>
			<b>{$iTotalCommissionPoint|number_format:2}</b>
		</div>
		<div class="col-md-4 col-sm-12 nopadding text-right">
			<span>{_p var='total_available_point'}:</span>
			<b>{$iTotalAvailablePoint|number_format:2}</b>
		</div>
	</div>
	<ul class="statistics_status clearfix">
		<li>
			<div class="statistics_status_inner">
				<div>
					<i class="fa fa-check-circle" aria-hidden="true"></i>
					<strong class="text-success">{_p var='Approved'}</strong>
				</div>
				<ul>
					<li class="clearfix">
						<span class="text-left capitalize">{_p var='total_number'}</span>
						<span class="text-right">{$iTotalApproved}</span>
					</li>
					<li class="clearfix">
						<span class="text-left capitalize">{_p var='total_point'}</span>
						<span class="text-right">{$iComApprovedPoint|number_format:2}</span>
					</li>
				</ul>
			</div>
		</li>
		<li>
			<div class="statistics_status_inner">
				<div>
					<i class="fa fa-history" aria-hidden="true"></i>
					<strong class="text-warning">{_p var='Delaying'}</strong>
				</div>
				<ul>
					<li class="clearfix">
						<span class="text-left capitalize">{_p var='total_number'}</span>
						<span class="text-right">{$iTotalDelaying}</span>
					</li>
					<li class="clearfix">
						<span class="text-left capitalize">{_p var='total_point'}</span>
						<span class="text-right">{$iComDelayingPoint|number_format:2}</span>
					</li>
				</ul>
			</div>
		</li>
		<li>
			<div class="statistics_status_inner">
				<div>
					<i class="fa fa-times-circle" aria-hidden="true"></i>
					<strong class="text-danger">{_p var='Denied'}</strong>
				</div>
				<ul>
					<li class="clearfix">
						<span class="text-left capitalize">{_p var='total_number'}</span>
						<span class="text-right">{$iTotalDenied}</span>
					</li>
					<li class="clearfix">
						<span class="text-left capitalize">{_p var='total_point'}</span>
						<span class="text-right">{$iComDeniedPoint|number_format:2}</span>
					</li>
				</ul>
			</div>
		</li>
		<li>
			<div class="statistics_status_inner">
				<div>
					<i class="fa fa-hourglass-end" aria-hidden="true"></i>
					<strong class="text-info">{_p var='Waiting'}</strong>
				</div>
				<ul>
					<li class="clearfix">
						<span class="text-left capitalize">{_p var='total_number'}</span>
						<span class="text-right">{$iTotalWaiting}</span>
					</li>
					<li class="clearfix">
						<span class="text-left capitalize">{_p var='total_point'}</span>
						<span class="text-right">{$iComWaitingPoint|number_format:2}</span>
					</li>
				</ul>
			</div>
		</li>
	</ul>
    <form action="" id="yncaffiliate-statistic-filter">
        <div class="yncaffiliate_labeling col-md-6 col-xs-6 nopadding statistics_select">
            <input type="hidden" name="val[user_id]" value="{$iUserId}">
            <div class="form-group nomargin">
                <label class="label_title nomargin">{_p var='labeling'}</label>
                <ul class="clearfix">
                    <li class="col-md-6 nopadding">
                        <label class="fw-400 nomargin">
                            <input type="radio" name="val[labeling]" value="rules" checked>{_p var='commission_rules'}
                        </label>
                    </li>
                    <li class="col-md-6 nopadding">
                        <label class="fw-400 nomargin">
                            <input type="radio" name="val[labeling]" value="levels">{_p var='client_levels'}
                        </label>
                    </li>
                </ul>
            </div>
        </div>
        <div class="yncaffiliate_status col-md-6 col-xs-6 nopadding statistics_select">
            <div class="form-group nomargin">
                <label class="label_title nomargin">{_p var='status'}</label>
                <ul class="clearfix">
                    <li class="col-md-3 col-xs-6 nopadding">
                        <label class="fw-400 nomargin">
                            <input type="radio" name="val[status]" value="all" checked>{_p var='all'}
                        </label>
                    </li>
                    <li class="col-md-3 col-xs-6 nopadding">
                        <label class="fw-400 nomargin">
                            <input type="radio" name="val[status]" value="waiting">{_p var='Waiting'}
                        </label>
                    </li>
                    <li class="col-md-3 col-xs-6 nopadding">
                        <label class="fw-400 nomargin">
                            <input type="radio" name="val[status]" value="delaying">{_p var='Delaying'}
                        </label>
                    </li>
                    <li class="col-md-3 col-xs-6 nopadding">
                        <label class="fw-400 nomargin">
                            <input type="radio" name="val[status]" value="approved">{_p var='Approved'}
                        </label>
                    </li>
                </ul>
            </div>
        </div>
        <div class="yncaffiliate_data col-md-6 col-xs-6 nopadding statistics_select">
            <div class="form-group nomargin">
                <label class="label_title nomargin">{_p var='Data'}</label>
                <ul class="clearfix">
                    <li class="col-md-6 nopadding">
                        <label class="fw-400 nomargin">
                            <input type="radio" name="val[data]" value="number_transaction" checked>{_p var='number_of_transaction'}
                        </label>
                    </li>
                    <li class="col-md-6 nopadding">
                        <label class="fw-400 nomargin">
                            <input type="radio" name="val[data]" value="earning">{_p var='earning_l'}
                        </label>
                    </li>
                </ul>
            </div>
        </div>
        <div class="yncaffiliate_groupby col-md-6 col-xs-6 nopadding statistics_select">
            <div class="form-group nomargin">
                <label class="label_title nomargin">{_p var='group_by'}</label>
                <ul class="clearfix">
                    <li class="col-md-3 col-xs-6 nopadding">
                        <label class="fw-400 nomargin">
                            <input type="radio" name="val[groupby]" value="day" checked>{_p var='day_l'}
                        </label>
                    </li>
                    <li class="col-md-3 col-xs-6 nopadding">
                        <label class="fw-400 nomargin">
                            <input type="radio" name="val[groupby]" value="week">{_p var='week_l'}
                        </label>
                    </li>
                    <li class="col-md-3 col-xs-6 nopadding">
                        <label class="fw-400 nomargin">
                            <input type="radio" name="val[groupby]" value="month">{_p var='month_l'}
                        </label>
                    </li>
                    <li class="col-md-3 col-xs-6 nopadding">
                        <label class="fw-400 nomargin">
                            <input type="radio" name="val[groupby]" value="year">{_p var='year_l'}
                        </label>
                    </li>
                </ul>
            </div>
        </div>
        <div class="yncaffiliate_search_form form-inline">
            <div class="yncaffiliate_search_form_inner clearfix">
                <div class="form-group padding">
                    <label>{_p var='Time'}</label>
                    <div class="form-inline clearfix md_padding_parent">
                        <div class="form-group yncaffiliate_datetime_picker_parent md_padding">
                            <div class="form-group js_from_select">
                                {select_date prefix='from_' id='_from' start_year='-10' end_year='+10' field_separator=' / '
                                field_order='MDY' default_all=true }
                            </div>
                        </div>
                        <div class="form-group yncaffiliate_datetime_picker_parent md_padding">
                            <div class="form-group js_to_select">
                                {select_date prefix='to_' id='_to' start_year='-10' end_year='+10' field_separator=' / '
                                field_order='MDY' default_all=true }
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group padding text-left">
                    <label class="notext"></label>
                    <button class="btn btn-primary" type="button" onclick="return getCharts();">{_p var='refresh'}</button>
                </div>
            </div>
        </div>
    </form>
    <div id="ynaff_loading" style="display: none;al" class="t_center"><i class="fa fa-spin fa-circle-o-notch"></i></div>
	<div id="yncaffiliate-chart-holder" style="display: none">
    </div>
</div>
{literal}
<script type="text/javascript">
    $Behavior.onInitStatistic = function(){
        if($('.yncaffiliate_statistics').length == 0) return;
        if($('.yncaffiliate_statistics').prop('init')) return;
        $('.yncaffiliate_statistics').prop('init',true);
        $('#ynaff_loading').show();
        $("#tooltip").remove();
        $('#yncaffiliate-statistic-filter').ajaxCall('yncaffiliate.getCharts');

    }
    function getCharts()
    {
        $('#yncaffiliate-chart-holder').html('');
        $('#ynaff_loading').show();
        $("#tooltip").remove();
        $('#yncaffiliate-statistic-filter').ajaxCall('yncaffiliate.getCharts');
        return false;
    }
</script>
{/literal}