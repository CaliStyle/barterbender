<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {_p var='statistics'}
        </div>
    </div>
    <div class="panel-body">
        <div class="extra_info">
            {_p var='module_statistics_page_information'}
        </div>
        <div class="table-responsive">
            <table class="table table-admin">
                <tr >
                    <td>{_p var='total_affiliates'}</td>
                    <td class="w220">{$iTotalAffiliate}</td>
                </tr>
                <tr class="tr">
                    <td>{_p var='total_clients'}</td>
                    <td class="w220">{$iTotalClients}</td>
                </tr>
                {if count($aRuleComDetail)}
                    {foreach from=$aRuleComDetail item=aDetail key=iKey}
                        <tr class="{if !is_int($iKey/2)}tr{/if}">
                            <td>{_p var=$aDetail.rule_title}</td>
                            <td>{$aDetail.total_points}</td>
                        </tr>
                    {/foreach}
                    <tr class="{if is_int($iKey/2)}tr{/if}">
                        <td>{_p var='total_commisison_points'}</td>
                        <td class="w220">{$iTotalComPoint}</td>
                    </tr>
                    <tr class="{if !is_int($iKey/2)}tr{/if}">
                        <td>{_p var='total_requested_points'}</td>
                        <td class="w220">{$iTotalRequested}</td>
                    </tr>
                {else}
                    <tr>
                        <td>{_p var='total_commisison_points'}</td>
                        <td class="w220">{$iTotalComPoint}</td>
                    </tr>
                    <tr class="tr">
                        <td>{_p var='total_requested_points'}</td>
                        <td class="w220">{$iTotalRequested}</td>
                    </tr>
                {/if}
            </table>
        </div>
    </div>
    <div class="yncaffiliate_statistics clearfix panel-body">
        <form action="" id="yncaffiliate-statistic-filter">
           <input type="hidden" name="val[labeling]" value="rules"/>
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
                    <label class="label_title nomargin">{_p('Data')}</label>
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
                        <label>{_p('Time')}</label>
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
                        <button class="btn btn-primary" onclick="return getCharts();">{_p var='refresh'}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="panel panel-default">
<div id="ynaff_loading" style="display: none;" class="t_center"><i class="fa fa-spin fa-circle-o-notch"></i></div>
<div id="yncaffiliate-chart-holder" style="display: none"></div>
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

    };
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
<link rel="stylesheet" href="{param var='core.path_actual'}PF.Site/Apps/ync-affiliate/assets/css/default/default/backend.css">