{literal}
<script language="JavaScript" type="text/javascript">
    $Behavior.ynfrInitializeStatisticJs = function(){
        $("#js_from_date_listing").datepicker({
            dateFormat: 'mm/dd/yy',
            onSelect: function(dateText, inst) {
                var $dateTo = $("#js_to_date_listing").datepicker("getDate");
                var $dateFrom = $("#js_from_date_listing").datepicker("getDate");
                if($dateTo)
                {
                    $dateTo.setHours(0);
                    $dateTo.setMilliseconds(0);
                    $dateTo.setMinutes(0);
                    $dateTo.setSeconds(0);
                }

                if($dateFrom)
                {
                    $dateFrom.setHours(0);
                    $dateFrom.setMilliseconds(0);
                    $dateFrom.setMinutes(0);
                    $dateFrom.setSeconds(0);
                }

                if($dateTo && $dateFrom && $dateTo < $dateFrom) {
                    tmp = $("#js_to_date_listing").val();
                    $("#js_to_date_listing").val($("#js_from_date_listing").val());
                    $("#js_from_date_listing").val(tmp);
                }
                return false;
            }
        });
        $("#js_to_date_listing").datepicker({
            dateFormat: 'mm/dd/yy',
            onSelect: function(dateText, inst) {
                var $dateTo = $("#js_to_date_listing").datepicker("getDate");
                var $dateFrom = $("#js_from_date_listing").datepicker("getDate");

                if($dateTo)
                {
                    $dateTo.setHours(0);
                    $dateTo.setMilliseconds(0);
                    $dateTo.setMinutes(0);
                    $dateTo.setSeconds(0);
                }

                if($dateFrom)
                {
                    $dateFrom.setHours(0);
                    $dateFrom.setMilliseconds(0);
                    $dateFrom.setMinutes(0);
                    $dateFrom.setSeconds(0);
                }

                if($dateTo && $dateFrom && $dateTo < $dateFrom) {
                    tmp = $("#js_to_date_listing").val();
                    $("#js_to_date_listing").val($("#js_from_date_listing").val());
                    $("#js_from_date_listing").val(tmp);
                }
                return false;
            }
        });

        $("#js_from_date_listing_anchor").click(function() {
            $("#js_from_date_listing").focus();
            return false;
        });

        $("#js_to_date_listing_anchor").click(function() {
            $("#js_to_date_listing").focus();
            return false;
        });
    };
</script>
{/literal}


<!-- Filter Search Form Layout -->
<form class="ynfr" method="GET">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var="directory.search_filter"}
            </div>
        </div>

        <div class="panel-body">
            <div class="form-group">
                <label for="title">{phrase var='business'}:</label>
                <input class="form-control" type="text" name="search[title]" value="{value type='input' id='title'}" id="title">
            </div>
            <div class="form-group">
                <label for="username">{phrase var='claimed_by'}:</label>
                <input class="form-control" type="text" name="search[username]" value="{value type='input' id='username'}" id="username">
            </div>

            <div class="row">
                <!-- From -->
                <div class="form-group col-md-6">
                    <label for="from_date">{phrase var='from_date'}:</label>
                    <div class="row">
                        <div class="col-md-12 js_from_select">
                            {select_date prefix='from_' id='_from' start_year='-10' end_year='+10' field_separator=' / '
                            field_order='MDY' default_all=true }
                        </div>
                    </div>
                </div>

                <!-- To -->
                <div class="form-group col-md-6">
                    <label for="todate">{phrase var='to_date'}:</label>
                    <div class="row">
                        <div class="col-md-12 js_to_select">
                            {select_date prefix='to_' id='_to' start_year='-10' end_year='+10' field_separator=' / '
                            field_order='MDY' default_all=true }
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel-footer">
            <input type="submit" id="filter_submit" name="search[submit]" value="{phrase var='search'}" class="btn btn-primary">
        </div>
    </div>
</form>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {phrase var='claim_request'}
        </div>
    </div>
    {if count($aList)}
    <div class="table-responsive flex-sortable">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="t_center w180">{phrase var='claimed_date'}</th>
                    <th class="t_center">{phrase var='business'}</th>
                    <th class="t_center">{phrase var='claimed_by'}</th>
                    <th class="t_center w180">{phrase var='option'}</th>
                </tr>
            </thead>
            <tbody>
            {foreach from = $aList item = aList_item}
                <tr>
                    <td class="t_center w180">{$aList_item.timestamp_claimrequest_convert}</td>
                    <td class="t_center">{$aList_item.name|shorten:30:'...'}</td>
                    <td class="t_center">{$aList_item|user}</td>
                    <td class="t_center w180">
                        <a href="{url link='admincp.directory.manageclaimrequest'}approve_{$aList_item.business_id}/">{phrase var='approve'}</a> | <a href="{url link='admincp.directory.manageclaimrequest'}deny_{$aList_item.business_id}/">{phrase var='deny'}</a>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    <?php if ($this->getLayout('pager')): ?>
        <div class="panel-footer">
            {pager}
        </div>
    <?php endif; ?>
    {else}
    <div class="alert alert-info">
        {phrase var='no_requests_found'}
    </div>
    {/if}
</div>

